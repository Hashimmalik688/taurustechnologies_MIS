#!/usr/bin/env python3
"""
WhisperX Transcription for QA System.
Uses faster-whisper for fast, accurate speech-to-text.

Model: distil-large-v3  (6x faster than large-v2, ~97% accuracy — English-optimised)
Alignment: SKIPPED — we only need the plain text, not word-level timestamps
Diarization: handled downstream by Gemini AI in the PHP layer

Optimized for 12-core / 48GB RAM CPU server:
  - cpu_threads=12   — use all cores (one call processed at a time)
  - batch_size=32    — large batches saturate 48GB RAM for speed
  - compute_type=int8 — faster-whisper int8 quantisation is accurate and fast on CPU

Usage:
    python3 whisper_transcribe.py <audio_file_path> [--model distil-large-v3] [--language en]

Output: JSON to stdout with keys: plain, diarized (diarized is always empty — filled by Gemini AI)
"""

import os
import sys
import json
import gc
import argparse
import warnings
import logging

# Suppress non-critical warnings to keep stdout clean for JSON output
warnings.filterwarnings("ignore")

# Force ALL Python loggers to write to stderr ONLY (never stdout)
_stderr_handler = logging.StreamHandler(sys.stderr)
_stderr_handler.setLevel(logging.WARNING)
logging.basicConfig(level=logging.WARNING, handlers=[_stderr_handler], force=True)

for _logger_name in ["whisperx", "whisperx.vads", "faster_whisper",
                     "pytorch_lightning", "lightning", "torch"]:
    _logger = logging.getLogger(_logger_name)
    _logger.handlers.clear()
    _logger.addHandler(_stderr_handler)
    _logger.setLevel(logging.WARNING)
    _logger.propagate = False

import whisperx


def transcribe(
    file_path: str,
    model_size: str = "distil-large-v3",
    language: str = "en",
    batch_size: int = 32,
    cpu_threads: int = 12,
) -> dict:
    """
    Transcribe with faster-whisper only — no alignment, no diarization.

    Alignment (wav2vec2) is skipped because:
    - We only need the transcript text, not word-level timestamps
    - Alignment loads a second model and adds significant processing time
    - Gemini AI handles speaker labeling in the PHP layer

    Model choice: distil-large-v3
    - 6x faster than large-v2 on CPU
    - ~97% of large-v2 accuracy (imperceptible difference for insurance calls)
    - English-optimised (all our calls are in English)

    compute_type=int8:
    - faster-whisper's int8 quantisation is accurate and significantly faster on CPU
    - No GPU needed, no float16 issues
    """

    device = "cpu"
    compute_type = "int8"  # int8 is faster than float32 on CPU with negligible accuracy loss

    try:
        import torch
        torch.set_num_threads(cpu_threads)
    except Exception:
        pass

    # ── Transcribe with faster-whisper ───────────────────────────────
    sys.stderr.write(f"[WhisperX] Loading model: {model_size} (int8, {cpu_threads} threads, batch={batch_size})...\n")

    model = whisperx.load_model(
        model_size,
        device,
        compute_type=compute_type,
        language=language,
        threads=cpu_threads,
    )

    sys.stderr.write(f"[WhisperX] Transcribing: {os.path.basename(file_path)}...\n")
    audio = whisperx.load_audio(file_path)
    result = model.transcribe(audio, batch_size=batch_size, language=language)

    n_segments = len(result.get('segments', []))
    sys.stderr.write(f"[WhisperX] Done. {n_segments} segments. No alignment step (text-only mode).\n")
    del model
    gc.collect()

    # ── Build plain transcript ────────────────────────────────────────
    # Collapse all segments into a single string.
    # Gemini AI will handle speaker attribution in the PHP layer.
    plain_text = " ".join(
        seg["text"].strip()
        for seg in result.get("segments", [])
        if seg.get("text", "").strip()
    )

    return {
        "plain": plain_text,
        "diarized": "",  # intentionally empty — filled by Gemini in PHP layer
    }


def main():
    parser = argparse.ArgumentParser(description="WhisperX transcription for QA")
    parser.add_argument("file", help="Path to audio file")
    parser.add_argument("--model", default=None, help="Whisper model size (default: from WHISPER_MODEL env or large-v2)")
    parser.add_argument("--language", default="en", help="Language code")
    args = parser.parse_args()

    model_size = args.model or os.environ.get("WHISPER_MODEL", "distil-large-v3")
    cpu_threads = int(os.environ.get("WHISPER_CPU_THREADS", "12"))
    batch_size = int(os.environ.get("WHISPER_BATCH_SIZE", "32"))

    try:
        result = transcribe(
            file_path=args.file,
            model_size=model_size,
            language=args.language,
            batch_size=batch_size,
            cpu_threads=cpu_threads,
        )
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"error": str(e)}), file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()
