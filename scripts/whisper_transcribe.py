#!/usr/bin/env python3
"""
Local Whisper Transcription for QA System.
Uses faster-whisper (CTranslate2) for fast CPU-based speech-to-text with
speaker diarization via VAD-based segmentation.

Usage:
    python3 whisper_transcribe.py <audio_file_path> [--model small] [--language en]

Output: JSON to stdout with keys: plain, diarized
"""

import sys
import json
import argparse
from faster_whisper import WhisperModel


def transcribe(file_path: str, model_size: str = "small", language: str = "en") -> dict:
    """
    Transcribe audio file using faster-whisper.
    
    Models (size / VRAM / relative speed):
      tiny   - 39M  params - fastest, least accurate
      base   - 74M  params - fast, okay accuracy
      small  - 244M params - good balance (recommended for CPU)
      medium - 769M params - better accuracy, slower
      large-v3 - 1.5B params - best accuracy, requires GPU
    
    For a 3-core 8GB RAM Contabo VPS, 'small' or 'base' is recommended.
    """
    
    # Use CPU with int8 quantization for speed on Contabo VPS
    model = WhisperModel(model_size, device="cpu", compute_type="int8")
    
    segments, info = model.transcribe(
        file_path,
        language=language,
        beam_size=5,
        word_timestamps=True,
        vad_filter=True,           # Filter out non-speech
        vad_parameters=dict(
            min_silence_duration_ms=500,
            speech_pad_ms=200,
        ),
    )
    
    plain_parts = []
    all_words = []
    
    for segment in segments:
        plain_parts.append(segment.text.strip())
        if segment.words:
            for word in segment.words:
                all_words.append({
                    "text": word.word.strip(),
                    "start": word.start,
                    "end": word.end,
                })
    
    plain_text = " ".join(plain_parts)
    
    # Build diarized output using timing-based heuristic.
    # Since faster-whisper doesn't do true speaker diarization,
    # we use a pause-based approach: first speaker is AGENT (outbound call),
    # switches on significant pauses (>1.5s gap between words).
    diarized = build_diarized(all_words)
    
    return {
        "plain": plain_text,
        "diarized": diarized,
    }


def build_diarized(words: list) -> str:
    """
    Build AGENT:/CUSTOMER: labeled transcript using pause-based heuristic.
    
    For outbound Final Expense calls:
    - First speaker is always AGENT
    - Speaker switch detected on gaps > 1.5 seconds
    - This is a basic heuristic; for production accuracy consider
      adding pyannote-audio for true speaker diarization.
    """
    if not words:
        return ""
    
    PAUSE_THRESHOLD = 1.5  # seconds
    
    speakers = ["AGENT", "CUSTOMER"]
    current_speaker_idx = 0
    lines = []
    current_text = ""
    prev_end = 0.0
    
    for w in words:
        gap = w["start"] - prev_end if prev_end > 0 else 0
        
        if gap > PAUSE_THRESHOLD and current_text.strip():
            # Speaker switch
            lines.append(f"{speakers[current_speaker_idx]}: {current_text.strip()}")
            current_speaker_idx = 1 - current_speaker_idx  # Toggle
            current_text = w["text"]
        else:
            current_text += " " + w["text"] if current_text else w["text"]
        
        prev_end = w["end"]
    
    # Append last segment
    if current_text.strip():
        lines.append(f"{speakers[current_speaker_idx]}: {current_text.strip()}")
    
    return "\n".join(lines)


def main():
    parser = argparse.ArgumentParser(description="Whisper local transcription for QA")
    parser.add_argument("file", help="Path to audio file")
    parser.add_argument("--model", default="small", help="Whisper model size (tiny/base/small/medium/large-v3)")
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
