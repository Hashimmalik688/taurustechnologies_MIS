<?php

namespace App\Services\QA;

/**
 * Parses Zoom phone-recording transcripts (copy-pasted from Zoom portal)
 * into a clean AGENT:/CUSTOMER: diarized format for QA scoring.
 *
 * Zoom transcript format:
 *
 *   HH:MM Speaker Name        ← new utterance, named → AGENT
 *   Text content here
 *
 *   HH:MM 13368519588         ← new utterance, phone number → CUSTOMER
 *   Text content
 *
 *   HH:MM                     ← timestamp only → continue previous speaker
 *   More text
 */
class ZoomTranscriptParser
{
    /**
     * Parse a raw Zoom transcript into a diarized string for AI scoring.
     *
     * @param  string $rawTranscript  Raw copied text from Zoom recording page
     * @return array{diarized: string, duration_seconds: int, agent_name: string|null, lines: array}
     */
    public static function parse(string $rawTranscript): array
    {
        $lines        = preg_split('/\r\n|\r|\n/', trim($rawTranscript));
        $utterances   = [];
        $currentSpeaker  = null;
        $currentRole     = null;
        $currentText     = [];
        $agentName       = null;
        $lastTimestampSec = 0;

        // Regex: optional HH: hour, then MM:SS  (or H:MM for short calls)
        $tsRegex = '/^(\d{1,2}:\d{2})(?:\s+(.+))?$/';

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            if (preg_match($tsRegex, $line, $m)) {
                // Save previous utterance
                if ($currentRole !== null && count($currentText)) {
                    $utterances[] = [
                        'role'    => $currentRole,
                        'speaker' => $currentSpeaker,
                        'text'    => implode(' ', $currentText),
                    ];
                    $currentText = [];
                }

                // Parse timestamp → seconds
                $parts       = explode(':', $m[1]);
                $tsSec       = (int)$parts[0] * 60 + (int)$parts[1];
                $lastTimestampSec = $tsSec;

                $speakerFragment = trim($m[2] ?? '');

                if ($speakerFragment === '') {
                    // Timestamp only → keep current speaker
                } elseif (self::isPhone($speakerFragment)) {
                    $currentRole    = 'CUSTOMER';
                    $currentSpeaker = $speakerFragment;
                } else {
                    $currentRole    = 'AGENT';
                    $currentSpeaker = $speakerFragment;
                    if ($agentName === null) {
                        $agentName = $speakerFragment;
                    }
                }
            } else {
                // Plain text → append to current utterance
                if ($currentRole !== null) {
                    $currentText[] = $line;
                }
            }
        }

        // Flush last utterance
        if ($currentRole !== null && count($currentText)) {
            $utterances[] = [
                'role'    => $currentRole,
                'speaker' => $currentSpeaker,
                'text'    => implode(' ', $currentText),
            ];
        }

        // Build diarized string
        $diarized = implode("\n", array_map(
            fn($u) => $u['role'] . ': ' . $u['text'],
            $utterances
        ));

        return [
            'diarized'         => $diarized,
            'duration_seconds' => $lastTimestampSec,
            'agent_name'       => $agentName,
            'lines'            => $utterances,
        ];
    }

    /**
     * Returns true if the string looks like a phone number (10–15 digits).
     */
    private static function isPhone(string $s): bool
    {
        return (bool) preg_match('/^\+?[\d\s\-().]{10,15}$/', $s)
            && preg_match('/^\d/', $s);  // must start with digit
    }
}
