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
    // Transcription: AssemblyAI (upload-based, via QA scoring page)
    // Scoring: Claude (primary), Gemini (fallback)

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
        // Apps Script Web App deployment URL — set GOOGLE_SHEETS_SCRIPT_URL in .env
        'script_url'           => env('GOOGLE_SHEETS_SCRIPT_URL'),
        // Separate Apps Script URL for the MIS Peregrines Leads sheet
        'peregrine_script_url' => env('GOOGLE_SHEETS_PEREGRINE_SCRIPT_URL'),
    ],

];
