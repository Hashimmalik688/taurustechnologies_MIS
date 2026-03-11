<?php

namespace App\Console\Commands;

use App\Models\ZoomToken;
use App\Services\ZoomPhoneApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Probe the Zoom API to discover which phone-report-style endpoints
 * actually exist and what scopes / data they return.
 *
 * Usage:
 *   php artisan zoom:probe-phone-reports
 */
class ZoomPhoneReportProbe extends Command
{
    protected $signature   = 'zoom:probe-phone-reports
                                {--from=2026-03-10 : Start date (YYYY-MM-DD)}
                                {--to=2026-03-11   : End date   (YYYY-MM-DD)}';

    protected $description = 'Probe Zoom API for phone usage/report endpoints and dump their responses';

    // Every candidate endpoint to test
    private array $endpoints = [
        // ── Zoom Phone "reports" prefix ──────────────────────────
        'phone/reports/call_logs'           => ['from' => true, 'type' => 2],  // by_users
        'phone/reports/users'               => ['from' => true],
        'phone/reports/usage'               => ['from' => true, 'type' => 1],
        'phone/reports/summary'             => ['from' => true],
        'phone/reports/metrics'             => ['from' => true],

        // ── Zoom Phone "dashboard" prefix ────────────────────────
        'phone/dashboards/call_logs'        => ['from' => true, 'type' => 2],
        'phone/dashboards/users'            => ['from' => true],

        // ── Top-level "report" (Meetings-style, phone variant) ───
        'report/phone/users'                => ['from' => true],
        'report/phone/call_logs'            => ['from' => true],
        'report/phone/usage'                => ['from' => true],

        // ── Phone stats/metrics ──────────────────────────────────
        'phone/stats'                       => ['from' => true],
        'phone/metrics'                     => ['from' => true],
        'phone/metrics/call_logs'           => ['from' => true],
        'phone/metrics/users'               => ['from' => true],

        // ── Account-level call quality / health ──────────────────
        'phone/quality_metrics'             => ['from' => true],
        'phone/call_metrics'                => ['from' => true],

        // ── Already-known working endpoints (baseline) ───────────
        'phone/call_logs'                   => ['from' => true, 'page_size' => 5],
    ];

    public function handle(): int
    {
        /** @var ZoomPhoneApiService $apiService */
        $apiService = app(ZoomPhoneApiService::class);

        // Prefer admin token
        $adminToken = ZoomToken::adminApp()->active()->orderByDesc('expires_at')->first()
                   ?? ZoomToken::adminApp()->orderByDesc('expires_at')->first();

        $token = null;

        if ($adminToken) {
            $token = $apiService->getAccessTokenForRecord($adminToken);
            $this->info('Using admin-app token (app_type=admin)');
        }

        if (! $token) {
            // Fallback: any available token
            $anyToken = ZoomToken::active()->orderByDesc('expires_at')->first();
            if ($anyToken) {
                $token = $apiService->getAccessTokenForRecord($anyToken);
                $this->warn('Admin token unavailable — using user token as fallback');
            }
        }

        if (! $token) {
            $this->error('No valid Zoom token found. Authorize at /zoom/admin-authorize first.');
            return 1;
        }

        $from = $this->option('from');
        $to   = $this->option('to');

        $this->newLine();
        $this->line("Date range: {$from} → {$to}");
        $this->newLine();

        $results = [];

        foreach ($this->endpoints as $path => $extraParams) {
            $params = array_merge([
                'page_size' => 10,
                'to'        => $to,
            ], $extraParams);

            // Add from only when the endpoint supports it
            if (! empty($params['from']) && $params['from'] === true) {
                $params['from'] = $from;
            }
            unset($params['from']); // handled above — reinject cleanly
            $params['from'] = $from;

            $url = "https://api.zoom.us/v2/{$path}";

            try {
                $response = Http::withToken($token)
                    ->timeout(10)
                    ->get($url, $params);

                $status = $response->status();
                $body   = $response->json() ?? [];

                $statusLabel = match (true) {
                    $status === 200                  => "<fg=green>200 OK</>",
                    $status === 204                  => "<fg=green>204 No Content</>",
                    $status === 400                  => "<fg=yellow>400 Bad Request</>",
                    $status === 401                  => "<fg=red>401 Unauthorized</>",
                    $status === 403                  => "<fg=red>403 Forbidden</>",
                    $status === 404                  => "<fg=gray>404 Not Found</>",
                    $status === 429                  => "<fg=yellow>429 Rate Limited</>",
                    default                          => "<fg=white>{$status}</>",
                };

                $errorCode = $body['code']    ?? null;
                $errorMsg  = $body['message'] ?? null;

                $this->line("  {$statusLabel}  GET /{$path}");

                if ($status === 200 || $status === 204) {
                    // Show top-level keys returned
                    $keys = array_keys($body);
                    $this->line("        Keys: " . implode(', ', $keys));
                    if (! empty($body['total_records'])) {
                        $this->line("        total_records: {$body['total_records']}");
                    }
                    // Show first record sample
                    $listKey = collect($keys)
                        ->first(fn($k) => is_array($body[$k] ?? null) && ! empty($body[$k]));
                    if ($listKey) {
                        $first = $body[$listKey][0] ?? [];
                        $this->line("        First [{$listKey}] sample keys: " . implode(', ', array_keys($first)));
                    }
                } elseif ($errorCode || $errorMsg) {
                    $this->line("        Error {$errorCode}: {$errorMsg}");
                }

                $results[$path] = [
                    'status' => $status,
                    'error'  => $errorCode,
                    'msg'    => $errorMsg,
                    'keys'   => array_keys($body),
                ];

            } catch (\Exception $e) {
                $this->line("  <fg=red>EXCEPTION</>  GET /{$path}: {$e->getMessage()}");
                $results[$path] = ['status' => 'exception', 'msg' => $e->getMessage()];
            }
        }

        // ── Summary ─────────────────────────────────────────────────────────
        $this->newLine();
        $this->info('══ SUMMARY ══');
        foreach ($results as $path => $r) {
            if (in_array($r['status'], [200, 204])) {
                $this->line("  <fg=green>✓ WORKS</>  /{$path}  →  keys: " . implode(', ', $r['keys']));
            }
        }

        $this->newLine();
        return 0;
    }
}
