<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],

    // ── QA Scoring AI Services ─────────────────────────────────────────

    'whisper' => [
        'enabled' => env('WHISPER_ENABLED', true),
        'python_bin' => env('WHISPER_PYTHON_BIN', '/opt/whisperx-env/bin/python'),
        'model' => env('WHISPER_MODEL', 'distil-large-v3'),  // distil-large-v3: 6x faster than large-v2, ~97% accuracy, English-optimised
        'hf_token' => env('HF_TOKEN'),                   // HuggingFace token for pyannote diarization
        'cpu_threads' => env('WHISPER_CPU_THREADS', 12), // CPU threads — use all 12 cores (1 call at a time)
        'batch_size' => env('WHISPER_BATCH_SIZE', 32),   // batch_size=32 saturates 48GB RAM for maximum throughput
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],

    'assemblyai' => [
        'api_key' => env('ASSEMBLYAI_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Sheets — Ravens Sales Integration
    |--------------------------------------------------------------------------
    | Set these three values in .env to enable automatic row-appending every
    | time a Ravens Closer submits a sale.
    |
    |  GOOGLE_SERVICE_ACCOUNT_JSON  – absolute path to the service account
    |                                  JSON key file downloaded from Google Cloud
    |                                  Console. Store it outside the web root,
    |                                  e.g. storage/app/google-service-account.json
    |
    |  GOOGLE_SHEETS_SPREADSHEET_ID – the long ID in the sheet URL:
    |                                  .../spreadsheets/d/{ID}/edit
    |
    |  GOOGLE_SHEETS_TAB_NAME       – the tab/worksheet name (default: Ravens Sales)
    */
    'google_sheets' => [
        'service_account_json' => env('GOOGLE_SERVICE_ACCOUNT_JSON', storage_path('app/google-service-account.json')),
        'spreadsheet_id'       => env('GOOGLE_SHEETS_SPREADSHEET_ID'),
        'tab_name'             => env('GOOGLE_SHEETS_TAB_NAME', 'Ravens Sales'),
    ],

];
