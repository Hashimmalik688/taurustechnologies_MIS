<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * GoogleSheetsService — Apps Script Edition
 *
 * Sends sale data to a Google Apps Script Web App, which appends a row to
 * the target sheet. No service account, no OAuth2, no key files needed.
 *
 * Setup:
 *  1. Open your Google Sheet → Extensions → Apps Script
 *  2. Paste the doPost() script provided below
 *  3. Deploy → New deployment → Web app
 *       Execute as: Me
 *       Who has access: Anyone
 *  4. Copy the deployment URL and set GOOGLE_SHEETS_SCRIPT_URL in .env
 */
class GoogleSheetsService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'timeout'         => 10,
            'connect_timeout' => 5,
            'allow_redirects' => true,
        ]);
    }

    public function appendSale(\App\Models\Lead $lead): void
    {
        $url = config('services.google_sheets.script_url');

        if (empty($url)) {
            Log::warning('GoogleSheetsService: GOOGLE_SHEETS_SCRIPT_URL is not set — skipping.');
            return;
        }

        try {
            $response = $this->http->post($url, [
                'json' => $this->buildPayload($lead),
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (isset($data['status']) && $data['status'] === 'error') {
                Log::error("GoogleSheetsService: Apps Script error for Lead #{$lead->id}: " . ($data['message'] ?? ''));
            } else {
                Log::info("GoogleSheetsService: Lead #{$lead->id} ({$lead->cn_name}) appended to sheet.");
            }
        } catch (RequestException $e) {
            $body = $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'no response';
            Log::error("GoogleSheetsService: HTTP error for Lead #{$lead->id} — {$e->getMessage()} | {$body}");
        } catch (\Throwable $e) {
            Log::error("GoogleSheetsService: Unexpected error for Lead #{$lead->id} — {$e->getMessage()}");
        }
    }

    private function buildPayload(\App\Models\Lead $lead): array
    {
        return [
            'submission_date'     => $lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            'closer_name'         => $lead->closer_name ?? '',
            'customer_name'       => $lead->cn_name ?? '',
            'phone_number'        => $lead->phone_number ?? '',
            'secondary_phone'     => $lead->secondary_phone_number ?? '',
            'date_of_birth'       => $lead->date_of_birth ?? '',
            'ssn'                 => $lead->ssn ?? '',
            'gender'              => $lead->gender ?? '',
            'state'               => $lead->state ?? '',
            'zip_code'            => $lead->zip_code ?? '',
            'address'             => $lead->address ?? '',
            'policy_type'         => $lead->policy_type ?? '',
            'policy_number'       => $lead->policy_number ?? '',
            'carrier_name'        => $lead->carrier_name ?? '',
            'coverage_amount'     => $lead->coverage_amount ?? '',
            'monthly_premium'     => $lead->monthly_premium ?? '',
            'initial_draft_date'  => $lead->initial_draft_date ?? '',
            'bank_name'           => $lead->bank_name ?? '',
            'account_title'       => $lead->account_title ?? '',
            'account_type'        => $lead->account_type ?? '',
            'routing_number'      => $lead->routing_number ?? '',
            'account_number'      => $lead->acc_number ?? '',
            'account_verified_by' => $lead->account_verified_by ?? '',
            'bank_balance'        => $lead->bank_balance ?? '',
            'source'              => $lead->source ?? '',
            'height'              => $lead->height ?? '',
            'weight'              => $lead->weight ?? '',
            'smoker'              => $lead->smoker ?? '',
            'medical_issue'       => $lead->medical_issue ?? '',
            'medications'         => $lead->medications ?? '',
            'doctor_name'         => $lead->doctor_name ?? '',
            'doctor_number'       => $lead->doctor_number ?? '',
            'emergency_contact'   => $lead->emergency_contact ?? '',
            'mis_lead_id'         => (string) $lead->id,
        ];
    }
}
