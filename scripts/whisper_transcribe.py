#!/usr/bin/env python3
"""
WhisperX Transcription + Diarization for QA System.
Uses WhisperX (faster-whisper + wav2vec2 alignment + pyannote diarization)
for accurate speech-to-text with real speaker labels.

Optimized for 12-core / 48GB RAM CPU server.

Pipeline:
  1. Transcribe with faster-whisper (batched inference)
  2. Align words with wav2vec2 for precise timestamps
  3. Diarize speakers with pyannote-audio
  4. Assign speaker labels to each word/segment

Usage:
    python3 whisper_transcribe.py <audio_file_path> [--model large-v2] [--language en]

Output: JSON to stdout with keys: plain, diarized
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
# This is critical because WhisperX's internal loggers (whisperx.vads, whisperx.diarize)
# would otherwise pollute stdout and corrupt our JSON output
_stderr_handler = logging.StreamHandler(sys.stderr)
_stderr_handler.setLevel(logging.WARNING)
logging.basicConfig(level=logging.WARNING, handlers=[_stderr_handler], force=True)

# Also suppress known noisy loggers from whisperx internals
for _logger_name in ["whisperx", "whisperx.vads", "whisperx.vads.pyannote",
                     "whisperx.diarize", "faster_whisper", "pyannote",
                     "pytorch_lightning", "lightning", "torch"]:
    _logger = logging.getLogger(_logger_name)
    _logger.handlers.clear()
    _logger.addHandler(_stderr_handler)
    _logger.setLevel(logging.WARNING)
    _logger.propagate = False

import whisperx


def transcribe(
    file_path: str,
    model_size: str = "large-v2",
    language: str = "en",
    hf_token: str | None = None,
    batch_size: int = 16,
    cpu_threads: int = 10,
) -> dict:
    """
    Full WhisperX pipeline: Transcribe -> Align -> Diarize.

    Optimized for CPU with 12 cores / 48GB RAM:
    - compute_type=float32 (required for CPU, float16 not supported)
    - batch_size=16 (larger batches, 48GB RAM can handle it)
    - cpu_threads=10 (leaves 2 cores for web server + system)
    """

    device = "cpu"
    compute_type = "float32"  # WhisperX requires float32 on CPU

    # Set torch thread count for optimal CPU utilization
    try:
        import torch
        torch.set_num_threads(cpu_threads)
    except Exception:
        pass

    # ── Step 1: Transcribe with faster-whisper (batched) ─────────────
    sys.stderr.write(f"[WhisperX] Loading model: {model_size} ({compute_type})...\n")

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

    sys.stderr.write(f"[WhisperX] Transcription done. {len(result.get('segments', []))} segments.\n")

    # Free transcription model memory before loading alignment model
    del model
    gc.collect()

    # ── Step 2: Align with wav2vec2 for precise word timestamps ──────
    sys.stderr.write("[WhisperX] Aligning words with wav2vec2...\n")

    try:
        model_a, metadata = whisperx.load_align_model(
            language_code=language,
            device=device,
        )
        result = whisperx.align(
            result["segments"],
            model_a,
            metadata,
            audio,
            device,
            return_char_alignments=False,
        )
        sys.stderr.write("[WhisperX] Alignment done.\n")

        # Free alignment model
        del model_a
        gc.collect()
    except Exception as e:
        sys.stderr.write(f"[WhisperX] Alignment failed (continuing without): {e}\n")

    # ── Step 3: Diarize speakers with pyannote-audio ─────────────────
    diarized_text = ""

    if hf_token:
        sys.stderr.write("[WhisperX] Running speaker diarization (pyannote)...\n")
        try:
            from whisperx.diarize import DiarizationPipeline

            diarize_model = DiarizationPipeline(
                token=hf_token,
                device=device,
            )

            # For phone calls: exactly 2 speakers (agent + customer)
            diarize_segments = diarize_model(
                audio,
                min_speakers=2,
                max_speakers=2,
            )

            # Assign speaker labels to words/segments
            result = whisperx.assign_word_speakers(diarize_segments, result)

            sys.stderr.write("[WhisperX] Diarization complete.\n")

            # Free diarization model
            del diarize_model
            gc.collect()

            # Build diarized transcript with real speaker labels
            diarized_text = build_diarized_from_whisperx(result["segments"])

        except Exception as e:
            sys.stderr.write(f"[WhisperX] Diarization failed: {e}\n")
            # Fall back to pause-based heuristic
            diarized_text = build_diarized_heuristic(result["segments"])
    else:
        sys.stderr.write("[WhisperX] No HF token — using pause-based diarization heuristic.\n")
        diarized_text = build_diarized_heuristic(result["segments"])

    # ── Build plain transcript ───────────────────────────────────────
    plain_text = " ".join(
        seg["text"].strip()
        for seg in result.get("segments", [])
        if seg.get("text", "").strip()
    )

    return {
        "plain": plain_text,
        "diarized": diarized_text,
    }


def build_diarized_from_whisperx(segments: list) -> str:
    """
    Build AGENT:/CUSTOMER: labeled transcript from WhisperX diarized segments.

    WhisperX assigns speaker labels like SPEAKER_00, SPEAKER_01, etc.
    For outbound Final Expense calls:
    - The first speaker detected is AGENT (they initiate the call)
    - The second speaker is CUSTOMER
    """
    if not segments:
        return ""

    # Map WhisperX speaker IDs to AGENT/CUSTOMER
    speaker_map = {}
    speaker_order = []

    for seg in segments:
        spk = seg.get("speaker", "UNKNOWN")
        if spk not in speaker_map and spk != "UNKNOWN":
            speaker_order.append(spk)
            # First speaker = AGENT (outbound call), second = CUSTOMER
            if len(speaker_order) == 1:
                speaker_map[spk] = "AGENT"
            elif len(speaker_order) == 2:
                speaker_map[spk] = "CUSTOMER"
            else:
                speaker_map[spk] = f"SPEAKER_{len(speaker_order)}"

    # Group consecutive segments by the same speaker
    merged = []
    for seg in segments:
        text = seg.get("text", "").strip()
        if not text:
            continue

        spk_raw = seg.get("speaker", "UNKNOWN")
        speaker = speaker_map.get(spk_raw, "UNKNOWN")

        if merged and merged[-1]["speaker"] == speaker:
            merged[-1]["text"] += " " + text
        else:
            merged.append({"speaker": speaker, "text": text})

    # Filter out noise segments (< 2 words and < 10 chars)
    final = []
    for seg in merged:
        word_count = len(seg["text"].split())
        if word_count >= 2 or len(seg["text"]) >= 10:
            final.append(seg)
        elif final:
            final[-1]["text"] += " " + seg["text"]

    lines = [f"{seg['speaker']}: {seg['text']}" for seg in final]
    return "\n".join(lines)


def build_diarized_heuristic(segments: list) -> str:
    """
    Fallback: Build AGENT:/CUSTOMER: transcript using pause-based heuristic.
    Used when diarization model is unavailable.
    """
    if not segments:
        return ""

    # Collect all words with timestamps
    all_words = []
    for seg in segments:
        words = seg.get("words", [])
        for w in words:
            text = w.get("word", "").strip()
            if text:
                all_words.append({
                    "text": text,
                    "start": w.get("start", 0),
                    "end": w.get("end", 0),
                })

    if not all_words:
        # No word-level timestamps, use segment text
        text = " ".join(s.get("text", "").strip() for s in segments if s.get("text", "").strip())
        return f"AGENT: {text}" if text else ""

    # Calculate adaptive pause threshold
    gaps = []
    for i in range(1, len(all_words)):
        gap = all_words[i]["start"] - all_words[i - 1]["end"]
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

    for w in all_words:
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

    # Merge consecutive same-speaker segments
    merged = []
    for seg in raw_segments:
        if merged and merged[-1]["speaker"] == seg["speaker"]:
            merged[-1]["text"] += " " + seg["text"]
        else:
            merged.append(dict(seg))

    # Filter noise
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
    parser = argparse.ArgumentParser(description="WhisperX transcription + diarization for QA")
    parser.add_argument("file", help="Path to audio file")
    parser.add_argument("--model", default=None, help="Whisper model size (default: from WHISPER_MODEL env or large-v2)")
    parser.add_argument("--language", default="en", help="Language code")
    args = parser.parse_args()

    # Configuration from environment (set by systemd service / .env)
    model_size = args.model or os.environ.get("WHISPER_MODEL", "large-v2")
    hf_token = os.environ.get("HF_TOKEN", None)
    cpu_threads = int(os.environ.get("WHISPER_CPU_THREADS", "8"))
    batch_size = int(os.environ.get("WHISPER_BATCH_SIZE", "8"))

    try:
        result = transcribe(
            file_path=args.file,
            model_size=model_size,
            language=args.language,
            hf_token=hf_token,
            batch_size=batch_size,
            cpu_threads=cpu_threads,
        )
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"error": str(e)}), file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()
