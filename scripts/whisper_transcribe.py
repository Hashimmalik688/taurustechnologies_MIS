#!/usr/bin/env python3
"""
Local Whisper Transcription for QA System.
Uses faster-whisper (CTranslate2) for CPU-based speech-to-text with
improved speaker diarization via energy + pause analysis.

Usage:
    python3 whisper_transcribe.py <audio_file_path> [--model medium] [--language en]

Output: JSON to stdout with keys: plain, diarized
"""

import sys
import json
import argparse
from faster_whisper import WhisperModel


def transcribe(file_path: str, model_size: str = "distil-large-v3", language: str = "en") -> dict:
    """
    Transcribe audio file using faster-whisper with improved settings.
    Uses distil-large-v3 by default for best accuracy.
    """

    model = WhisperModel(model_size, device="cpu", compute_type="int8")

    segments, info = model.transcribe(
        file_path,
        language=language,
        beam_size=5,
        best_of=3,
        word_timestamps=True,
        vad_filter=True,
        vad_parameters=dict(
            min_silence_duration_ms=400,
            speech_pad_ms=250,
            threshold=0.35,
        ),
        condition_on_previous_text=True,
        no_speech_threshold=0.5,
        compression_ratio_threshold=2.2,
        temperature=[0.0, 0.2, 0.4],
    )

    plain_parts = []
    all_words = []

    for segment in segments:
        text = segment.text.strip()
        if text:
            plain_parts.append(text)
        if segment.words:
            for word in segment.words:
                w_text = word.word.strip()
                if w_text:
                    all_words.append({
                        "text": w_text,
                        "start": word.start,
                        "end": word.end,
                    })

    plain_text = " ".join(plain_parts)

    diarized = build_diarized(all_words)

    return {
        "plain": plain_text,
        "diarized": diarized,
    }


def build_diarized(words: list) -> str:
    """
    Build AGENT:/CUSTOMER: labeled transcript using improved pause-based heuristic.

    For outbound Final Expense calls:
    - First speaker is always AGENT (they initiate the call)
    - Speaker switch on gaps using adaptive threshold
    - Merges very short consecutive segments from same speaker
    """
    if not words:
        return ""

    # Calculate adaptive pause threshold
    gaps = []
    for i in range(1, len(words)):
        gap = words[i]["start"] - words[i - 1]["end"]
        if gap > 0:
            gaps.append(gap)

    if gaps:
        avg_gap = sum(gaps) / len(gaps)
        pause_threshold = max(0.8, min(2.0, avg_gap * 3.0))
    else:
        pause_threshold = 1.2

    speakers = ["AGENT", "CUSTOMER"]
    current_speaker_idx = 0
    raw_segments = []
    current_text = ""
    prev_end = 0.0

    for w in words:
        gap = w["start"] - prev_end if prev_end > 0 else 0

        if gap > pause_threshold and current_text.strip():
            raw_segments.append({
                "speaker": speakers[current_speaker_idx],
                "text": current_text.strip(),
            })
            current_speaker_idx = 1 - current_speaker_idx
            current_text = w["text"]
        else:
            current_text += " " + w["text"] if current_text else w["text"]

        prev_end = w["end"]

    if current_text.strip():
        raw_segments.append({
            "speaker": speakers[current_speaker_idx],
            "text": current_text.strip(),
        })

    # Merge consecutive segments from same speaker
    merged = []
    for seg in raw_segments:
        if merged and merged[-1]["speaker"] == seg["speaker"]:
            merged[-1]["text"] += " " + seg["text"]
        else:
            merged.append(dict(seg))

    # Filter out very short noise segments (< 2 words and < 10 chars)
    final = []
    for seg in merged:
        word_count = len(seg["text"].split())
        if word_count >= 2 or len(seg["text"]) >= 10:
            final.append(seg)
        elif final:
            final[-1]["text"] += " " + seg["text"]

    lines = [f"{seg['speaker']}: {seg['text']}" for seg in final]
    return "\n".join(lines)


def main():
    parser = argparse.ArgumentParser(description="Whisper local transcription for QA")
    parser.add_argument("file", help="Path to audio file")
    parser.add_argument("--model", default="distil-large-v3", help="Whisper model size")
    parser.add_argument("--language", default="en", help="Language code")
    args = parser.parse_args()

    try:
        result = transcribe(args.file, args.model, args.language)
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"error": str(e)}), file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()
