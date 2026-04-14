<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * GoogleSheetsService
 *
 * Appends sale rows to a Google Sheet using a Service Account.
 * No extra Composer packages required — uses Guzzle (already in project) for
 * all HTTP calls and generates the JWT/OAuth2 token in-house.
 *
 * Setup steps:
 *  1. Create a Google Cloud project & enable "Google Sheets API".
 *  2. Create a Service Account → generate a JSON key file.
 *  3. Place the JSON key at storage/app/google-service-account.json
 *     (path configurable via GOOGLE_SERVICE_ACCOUNT_JSON env var).
 *  4. Share the target Google Sheet with the service account email
 *     (found in the JSON as "client_email") — give it "Editor" access.
 *  5. Set GOOGLE_SHEETS_SPREADSHEET_ID in .env to the sheet's ID
 *     (the long string in the URL: .../spreadsheets/d/{ID}/edit).
 *  6. Optionally set GOOGLE_SHEETS_TAB_NAME to target a specific tab.
 */
class GoogleSheetsService
{
    private Client $http;

    // Google OAuth2 token endpoint
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    // Google Sheets API base URL
    private const SHEETS_BASE = 'https://sheets.googleapis.com/v4/spreadsheets';

    // Column headers — must match the first row of your Google Sheet exactly.
    // Add/remove/reorder as needed; the Lead data array keys below must match.
    public const HEADERS = [
        'Submission Date',
        'Closer Name',
        'Customer Name',
        'Phone Number',
        'Secondary Phone',
        'Date of Birth',
        'SSN',
        'Gender',
        'State',
        'Zip Code',
        'Address',
        'Policy Type',
        'Policy Number',
        'Carrier',
        'Coverage Amount',
        'Monthly Premium',
        'Initial Draft Date',
        'Bank Name',
        'Account Title',
        'Account Type',
        'Routing Number',
        'Account Number',
        'Account Verified By',
        'Bank Balance',
        'Source',
        'Height',
        'Weight',
        'Smoker',
        'Medical Issue',
        'Medications',
        'Doctor Name',
        'Doctor Number',
        'Emergency Contact',
        'MIS Lead ID',
    ];

    public function __construct()
    {
        $this->http = new Client(['timeout' => 10]);
    }

    /**
     * Append a single sale row to the configured Google Sheet.
     *
     * @param  \App\Models\Lead  $lead  The freshly saved Lead model.
     */
    public function appendSale(\App\Models\Lead $lead): void
    {
        $spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $tabName       = config('services.google_sheets.tab_name', 'Ravens Sales');

        if (empty($spreadsheetId)) {
            Log::warning('GoogleSheetsService: GOOGLE_SHEETS_SPREADSHEET_ID is not set — skipping.');
            return;
        }

        $token = $this->getAccessToken();
        if (!$token) {
            Log::error('GoogleSheetsService: Could not obtain access token — row not appended.');
            return;
        }

        $row = $this->buildRow($lead);

        $range      = urlencode($tabName) . '!A1';
        $url        = self::SHEETS_BASE . "/{$spreadsheetId}/values/{$range}:append";

        try {
            $this->http->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'query' => [
                    'valueInputOption'          => 'USER_ENTERED',
                    'insertDataOption'          => 'INSERT_ROWS',
                    'includeValuesInResponse'   => 'false',
                ],
                'json' => [
                    'values' => [$row],
                ],
            ]);

            Log::info("GoogleSheetsService: Lead #{$lead->id} ({$lead->cn_name}) appended to sheet.");
        } catch (RequestException $e) {
            $body = $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'no response';
            Log::error("GoogleSheetsService: Failed to append row — {$e->getMessage()} | {$body}");
            // Do NOT rethrow — a Sheets failure must never break the sale submission.
        }
    }

    /**
     * Write the header row to the sheet (call once during setup via Artisan
     * command or manually — it will NOT overwrite existing data if the first
     * row already has content).
     */
    public function writeHeaders(): bool
    {
        $spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $tabName       = config('services.google_sheets.tab_name', 'Ravens Sales');

        if (empty($spreadsheetId)) {
            return false;
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        $range = urlencode($tabName) . '!A1';
        $url   = self::SHEETS_BASE . "/{$spreadsheetId}/values/{$range}";

        try {
            // Only write if A1 is empty (don't stomp existing headers)
            $response  = $this->http->get($url, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
            ]);
            $existing  = json_decode((string) $response->getBody(), true);
            if (!empty($existing['values'])) {
                Log::info('GoogleSheetsService: Headers already present — skipping writeHeaders.');
                return true;
            }
        } catch (\Throwable $e) {
            // If range is empty Google returns 200 with no values — any real
            // error here is caught below.
        }

        try {
            $this->http->put(self::SHEETS_BASE . "/{$spreadsheetId}/values/{$range}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'query' => ['valueInputOption' => 'RAW'],
                'json'  => ['values' => [self::HEADERS]],
            ]);
            return true;
        } catch (RequestException $e) {
            Log::error('GoogleSheetsService: writeHeaders failed — ' . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Build the ordered row array matching self::HEADERS.
     */
    private function buildRow(\App\Models\Lead $lead): array
    {
        return [
            now()->format('Y-m-d H:i:s'),              // Submission Date
            $lead->closer_name ?? '',                    // Closer Name
            $lead->cn_name ?? '',                        // Customer Name
            $lead->phone_number ?? '',                   // Phone Number
            $lead->secondary_phone_number ?? '',         // Secondary Phone
            $lead->date_of_birth ?? '',                  // Date of Birth
            $lead->ssn ?? '',                            // SSN
            $lead->gender ?? '',                         // Gender
            $lead->state ?? '',                          // State
            $lead->zip_code ?? '',                       // Zip Code
            $lead->address ?? '',                        // Address
            $lead->policy_type ?? '',                    // Policy Type
            $lead->policy_number ?? '',                  // Policy Number
            $lead->carrier_name ?? '',                   // Carrier
            $lead->coverage_amount ?? '',                // Coverage Amount
            $lead->monthly_premium ?? '',                // Monthly Premium
            $lead->initial_draft_date ?? '',             // Initial Draft Date
            $lead->bank_name ?? '',                      // Bank Name
            $lead->account_title ?? '',                  // Account Title
            $lead->account_type ?? '',                   // Account Type
            $lead->routing_number ?? '',                 // Routing Number
            $lead->acc_number ?? '',                     // Account Number
            $lead->account_verified_by ?? '',            // Account Verified By
            $lead->bank_balance ?? '',                   // Bank Balance
            $lead->source ?? '',                         // Source
            $lead->height ?? '',                         // Height
            $lead->weight ?? '',                         // Weight
            $lead->smoker ?? '',                         // Smoker
            $lead->medical_issue ?? '',                  // Medical Issue
            $lead->medications ?? '',                    // Medications
            $lead->doctor_name ?? '',                    // Doctor Name
            $lead->doctor_number ?? '',                  // Doctor Number
            $lead->emergency_contact ?? '',              // Emergency Contact
            (string) $lead->id,                          // MIS Lead ID
        ];
    }

    /**
     * Retrieve a cached OAuth2 access token for the service account.
     * Tokens are cached for 55 minutes (Google issues 60-minute tokens).
     */
    private function getAccessToken(): ?string
    {
        return Cache::remember('google_sa_token', 55 * 60, function () {
            return $this->fetchAccessToken();
        });
    }

    /**
     * Exchange the service account private key for an OAuth2 Bearer token.
     */
    private function fetchAccessToken(): ?string
    {
        $credentialsPath = config('services.google_sheets.service_account_json');

        if (!$credentialsPath || !file_exists($credentialsPath)) {
            Log::error("GoogleSheetsService: Credentials file not found at [{$credentialsPath}]. "
                . "Set GOOGLE_SERVICE_ACCOUNT_JSON in .env.");
            return null;
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);

        if (empty($credentials['private_key']) || empty($credentials['client_email'])) {
            Log::error('GoogleSheetsService: Service account JSON is missing private_key or client_email.');
            return null;
        }

        $now   = time();
        $claim = [
            'iss'   => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud'   => self::TOKEN_URL,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $jwt = $this->buildJwt($claim, $credentials['private_key']);
        if (!$jwt) {
            return null;
        }

        try {
            $response = $this->http->post(self::TOKEN_URL, [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return $data['access_token'] ?? null;
        } catch (RequestException $e) {
            $body = $e->hasResponse() ? (string) $e->getResponse()->getBody() : '';
            Log::error("GoogleSheetsService: Token exchange failed — {$e->getMessage()} | {$body}");
            return null;
        }
    }

    /**
     * Build a signed RS256 JWT from a claim set and PEM private key.
     * No external JWT library required.
     */
    private function buildJwt(array $claims, string $privateKey): ?string
    {
        $header  = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = $this->base64UrlEncode(json_encode($claims));
        $data    = "{$header}.{$payload}";

        $signature = '';
        $key       = openssl_pkey_get_private($privateKey);

        if (!$key) {
            Log::error('GoogleSheetsService: Failed to load private key from service account JSON.');
            return null;
        }

        if (!openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256)) {
            Log::error('GoogleSheetsService: openssl_sign failed.');
            return null;
        }

        return $data . '.' . $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
