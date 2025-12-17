<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomPhoneLogService
{
    private $baseUrl = 'https://api.zoom.us/v2';

    private $cacheTimeout = 300; // 5 minutes cache

    /**
     * Get Server-to-Server OAuth token with caching
     */
    private function getAccessToken()
    {
        return Cache::remember('zoom_oauth_token', 3500, function () {
            try {
                $clientId = config('zoom.client_id');
                $clientSecret = config('zoom.client_secret');
                $accountId = config('zoom.account_id');

                if (empty($clientId) || empty($clientSecret) || empty($accountId)) {
                    throw new \Exception('Missing Zoom OAuth credentials in config');
                }

                // Use form parameters instead of JSON
                $response = Http::withHeaders([
                    'Authorization' => 'Basic '.base64_encode($clientId.':'.$clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->asForm()->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);

                Log::info('Zoom OAuth Response Status', ['status' => $response->status()]);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Zoom OAuth Success', ['token_received' => ! empty($data['access_token'])]);

                    return $data['access_token'];
                }

                Log::error('Zoom OAuth Error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'headers' => $response->headers(),
                ]);

                throw new \Exception('Failed to get OAuth token: '.$response->body());
            } catch (\Exception $e) {
                Log::error('Zoom OAuth Exception', ['error' => $e->getMessage()]);
                throw $e;
            }
        });
    }

    /**
     * Make authenticated API request
     */
    private function makeRequest($endpoint, $params = [])
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
            ])->timeout(30)->get($this->baseUrl.$endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            // Handle specific error cases
            if ($response->status() === 401) {
                // Token might be expired, clear cache and retry once
                Cache::forget('zoom_oauth_token');

                $newToken = $this->getAccessToken();
                $retryResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.$newToken,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->get($this->baseUrl.$endpoint, $params);

                if ($retryResponse->successful()) {
                    return $retryResponse->json();
                }
            }

            Log::error('Zoom API Error', [
                'endpoint' => $endpoint,
                'params' => $params,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('API request failed: HTTP '.$response->status().' - '.$response->body());
        } catch (\Exception $e) {
            Log::error('Zoom API Exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get call logs for a specific user with caching
     */
    public function getUserCallLogs($userId, $options = [])
    {
        $params = $this->buildCallLogParams($options);
        $cacheKey = "zoom_user_calls_{$userId}_".md5(serialize($params));

        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($userId, $params) {
            return $this->makeRequest("/phone/users/{$userId}/call_logs", $params);
        });
    }

    /**
     * Get call logs for entire account with caching
     */
    public function getAccountCallLogs($options = [])
    {
        $params = $this->buildCallLogParams($options);
        $cacheKey = 'zoom_account_calls_'.md5(serialize($params));

        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($params) {
            return $this->makeRequest('/phone/call_logs', $params);
        });
    }

    /**
     * Get all call logs with pagination handling
     */
    public function getAllCallLogs($userId = null, $options = [])
    {
        $allLogs = [];
        $pageNumber = 1;
        $pageSize = $options['page_size'] ?? 100;
        $maxPages = 10; // Prevent infinite loops

        do {
            $params = array_merge($options, [
                'page_number' => $pageNumber,
                'page_size' => $pageSize,
            ]);

            try {
                if ($userId) {
                    $response = $this->getUserCallLogs($userId, $params);
                } else {
                    $response = $this->getAccountCallLogs($params);
                }

                $currentLogs = $response['call_logs'] ?? [];
                $allLogs = array_merge($allLogs, $currentLogs);
                $pageNumber++;

            } catch (\Exception $e) {
                Log::error('Error fetching page '.$pageNumber, ['error' => $e->getMessage()]);
                break;
            }

        } while (! empty($currentLogs) && count($currentLogs) === $pageSize && $pageNumber <= $maxPages);

        return [
            'call_logs' => $allLogs,
            'total_records' => count($allLogs),
            'page_count' => $pageNumber - 1,
        ];
    }

    /**
     * Get call log details by ID
     */
    public function getCallLogDetail($callLogId)
    {
        $cacheKey = "zoom_call_detail_{$callLogId}";

        return Cache::remember($cacheKey, 1800, function () use ($callLogId) {
            $response = $this->makeRequest("/phone/call_logs/{$callLogId}");

            return $this->formatSingleCallLog($response);
        });
    }

    /**
     * Get call recordings
     */
    public function getCallRecordings($callLogId)
    {
        $cacheKey = "zoom_call_recordings_{$callLogId}";

        return Cache::remember($cacheKey, 3600, function () use ($callLogId) {
            return $this->makeRequest("/phone/call_logs/{$callLogId}/recordings");
        });
    }

    /**
     * Get filtered and paginated call logs with advanced options
     */
    public function getFilteredCallLogs($filters = [])
    {
        $options = [
            'from' => $filters['start_date'] ?? Carbon::now()->subDays(30)->toDateString(),
            'to' => $filters['end_date'] ?? Carbon::now()->toDateString(),
            'page_size' => min($filters['per_page'] ?? 50, 300), // Zoom API limit
            'page_number' => $filters['page'] ?? 1,
        ];

        // Add optional filters
        if (! empty($filters['call_type'])) {
            $options['call_type'] = $filters['call_type'];
        }

        if (! empty($filters['path'])) {
            $options['path'] = $filters['path']; // inbound, outbound
        }

        $userId = $filters['user_id'] ?? null;

        try {
            if ($userId) {
                $response = $this->getUserCallLogs($userId, $options);
            } else {
                $response = $this->getAccountCallLogs($options);
            }

            return $this->formatCallLogsResponse($response, $filters);
        } catch (\Exception $e) {
            Log::error('Error in getFilteredCallLogs', [
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            return [
                'call_logs' => [],
                'total_records' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Search call logs by phone number
     */
    public function searchByPhoneNumber($phoneNumber, $options = [])
    {
        try {
            // Clean phone number for search
            $cleanNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

            // Get call logs and filter by phone number
            $callLogs = $this->getAllCallLogs(null, $options);

            $filtered = array_filter($callLogs['call_logs'], function ($log) use ($cleanNumber, $phoneNumber) {
                $callerNumber = $log['caller']['phone_number'] ?? '';
                $calleeNumber = $log['callee']['phone_number'] ?? '';

                // Clean numbers for comparison
                $cleanCaller = preg_replace('/[^\d+]/', '', $callerNumber);
                $cleanCallee = preg_replace('/[^\d+]/', '', $calleeNumber);

                return str_contains($cleanCaller, $cleanNumber) ||
                       str_contains($cleanCallee, $cleanNumber) ||
                       str_contains($callerNumber, $phoneNumber) ||
                       str_contains($calleeNumber, $phoneNumber);
            });

            return [
                'call_logs' => array_values($filtered),
                'total_records' => count($filtered),
            ];
        } catch (\Exception $e) {
            Log::error('Error in searchByPhoneNumber', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'call_logs' => [],
                'total_records' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get call statistics directly from API
     */
    public function getCallStatistics($userId = null, $startDate = null, $endDate = null)
    {
        $options = [
            'from' => $startDate ?? Carbon::now()->subDays(30)->toDateString(),
            'to' => $endDate ?? Carbon::now()->toDateString(),
        ];

        try {
            $callLogs = $this->getAllCallLogs($userId, $options);

            return $this->calculateStatistics($callLogs['call_logs']);
        } catch (\Exception $e) {
            Log::error('Error in getCallStatistics', ['error' => $e->getMessage()]);

            return $this->getEmptyStatistics();
        }
    }

    /**
     * Build call log parameters
     */
    private function buildCallLogParams($options = [])
    {
        $params = [
            'page_size' => min($options['page_size'] ?? 50, 300),
            'page_number' => $options['page_number'] ?? 1,
            'from' => $options['from'] ?? Carbon::now()->subDays(30)->toDateString(),
            'to' => $options['to'] ?? Carbon::now()->toDateString(),
        ];

        // Add optional parameters only if they have values
        if (! empty($options['call_type'])) {
            $params['call_type'] = $options['call_type'];
        }

        if (! empty($options['path'])) {
            $params['path'] = $options['path'];
        }

        return $params;
    }

    /**
     * Format call logs response with additional data
     */
    private function formatCallLogsResponse($response, $filters = [])
    {
        if (empty($response['call_logs'])) {
            return $response;
        }

        $response['call_logs'] = array_map(function ($log) {
            // Transform flat structure to expected nested structure
            $transformedLog = array_merge($log, [
                'caller' => [
                    'name' => $log['caller_name'] ?? '',
                    'phone_number' => $log['caller_number'] ?? '',
                    'caller_did_number' => $log['caller_did_number'] ?? '',
                ],
                'callee' => [
                    'name' => $log['callee_name'] ?? '',
                    'phone_number' => $log['callee_number'] ?? '',
                ],
                // Normalize result field
                'result' => $this->normalizeResult($log['result'] ?? ''),
                'formatted_duration' => $this->formatDuration($log['duration'] ?? 0),
                'formatted_date' => Carbon::parse($log['date_time'])->format('Y-m-d H:i:s'),
                'direction_badge' => $this->getDirectionBadge($log['direction'] ?? ''),
                'result_badge' => $this->getResultBadge($this->normalizeResult($log['result'] ?? '')),
            ]);

            // Now format display names using the nested structure
            $transformedLog['caller_display'] = $this->formatPhoneDisplay($transformedLog['caller']);
            $transformedLog['callee_display'] = $this->formatPhoneDisplay($transformedLog['callee']);

            return $transformedLog;
        }, $response['call_logs']);

        return $response;
    }

    // do the above for single record
    private function formatSingleCallLog($log)
    {
        // Transform flat structure to expected nested structure
        $transformedLog = array_merge($log, [
            'caller' => [
                'name' => $log['caller_name'] ?? '',
                'phone_number' => $log['caller_number'] ?? '',
                'caller_did_number' => $log['caller_did_number'] ?? '',
            ],
            'callee' => [
                'name' => $log['callee_name'] ?? '',
                'phone_number' => $log['callee_number'] ?? '',
            ],
            // Normalize result field
            'result' => $this->normalizeResult($log['result'] ?? ''),
            'formatted_duration' => $this->formatDuration($log['duration'] ?? 0),
            'formatted_date' => Carbon::parse($log['date_time'])->format('Y-m-d H:i:s'),
            'direction_badge' => $this->getDirectionBadge($log['direction'] ?? ''),
            'result_badge' => $this->getResultBadge($this->normalizeResult($log['result'] ?? '')),
        ]);

        // Now format display names using the nested structure
        $transformedLog['caller_display'] = $this->formatPhoneDisplay($transformedLog['caller']);
        $transformedLog['callee_display'] = $this->formatPhoneDisplay($transformedLog['callee']);

        return $transformedLog;
    }

    private function normalizeResult($result)
    {
        return match (strtolower($result)) {
            'call connected' => 'answered',
            'call declined', 'declined' => 'missed',
            'voicemail' => 'voicemail',
            'busy' => 'busy',
            default => strtolower($result)
        };
    }

    /**
     * Calculate statistics from call logs
     */
    private function calculateStatistics($callLogs)
    {
        $stats = [
            'total_calls' => count($callLogs),
            'inbound_calls' => 0,
            'outbound_calls' => 0,
            'answered_calls' => 0,
            'missed_calls' => 0,
            'total_duration' => 0,
            'voicemail_calls' => 0,
        ];

        foreach ($callLogs as $log) {
            $direction = $log['direction'] ?? '';
            $result = $log['result'] ?? '';
            $duration = $log['duration'] ?? 0;

            // Count by direction
            if ($direction === 'inbound') {
                $stats['inbound_calls']++;
            }
            if ($direction === 'outbound') {
                $stats['outbound_calls']++;
            }

            // Count by result
            if ($result === 'answered') {
                $stats['answered_calls']++;
            }
            if ($result === 'missed') {
                $stats['missed_calls']++;
            }
            if ($result === 'voicemail') {
                $stats['voicemail_calls']++;
            }

            $stats['total_duration'] += $duration;
        }

        $stats['average_duration'] = $stats['answered_calls'] > 0
            ? round($stats['total_duration'] / $stats['answered_calls'], 2)
            : 0;

        $stats['answer_rate'] = $stats['total_calls'] > 0
            ? round(($stats['answered_calls'] / $stats['total_calls']) * 100, 2)
            : 0;

        $stats['formatted_total_duration'] = $this->formatDuration($stats['total_duration']);
        $stats['formatted_average_duration'] = $this->formatDuration($stats['average_duration']);

        return $stats;
    }

    /**
     * Get empty statistics structure
     */
    private function getEmptyStatistics()
    {
        return [
            'total_calls' => 0,
            'inbound_calls' => 0,
            'outbound_calls' => 0,
            'answered_calls' => 0,
            'missed_calls' => 0,
            'total_duration' => 0,
            'voicemail_calls' => 0,
            'average_duration' => 0,
            'answer_rate' => 0,
            'formatted_total_duration' => '00:00',
            'formatted_average_duration' => '00:00',
        ];
    }

    /**
     * Format duration in human readable format
     */
    private function formatDuration($seconds)
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get direction badge class
     */
    private function getDirectionBadge($direction)
    {
        return match ($direction) {
            'inbound' => 'bg-success',
            'outbound' => 'bg-primary',
            default => 'bg-secondary'
        };
    }

    /**
     * Get result badge class
     */
    private function getResultBadge($result)
    {
        return match ($result) {
            'answered' => 'bg-success',
            'missed' => 'bg-danger',
            'voicemail' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Format phone display (name and number)
     */
    private function formatPhoneDisplay($contact)
    {
        // Handle both nested and flat structures
        if (is_array($contact)) {
            $name = $contact['name'] ?? '';
            $number = $contact['phone_number'] ?? '';
        } else {
            // For flat structure, this won't work - need to pass both name and number
            $name = '';
            $number = '';
        }

        if ($name && $number) {
            return "{$name} ({$number})";
        }

        return $name ?: $number ?: 'Unknown';
    }

    /**
     * Clear cache for specific user or all
     */
    public function clearCache($userId = null)
    {
        if ($userId) {
            // Clear user-specific cache patterns
            $patterns = [
                "zoom_user_calls_{$userId}_*",
                'zoom_call_detail_*',
                'zoom_call_recordings_*',
            ];

            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }
        } else {
            // Clear access token to force refresh
            Cache::forget('zoom_oauth_token');
        }
    }

    /**
     * Export call logs to CSV format
     */
    public function exportToCsv($filters = [])
    {
        try {
            $callLogs = $this->getFilteredCallLogs(array_merge($filters, ['page_size' => 1000]));

            $csvData = [];
            $csvData[] = [
                'Date/Time',
                'Direction',
                'Caller',
                'Callee',
                'Duration',
                'Result',
                'Call Type',
            ];

            foreach ($callLogs['call_logs'] as $log) {
                $csvData[] = [
                    $log['formatted_date'] ?? '',
                    ucfirst($log['direction'] ?? ''),
                    $log['caller_display'] ?? '',
                    $log['callee_display'] ?? '',
                    $log['formatted_duration'] ?? '',
                    ucfirst($log['result'] ?? ''),
                    $log['call_type'] ?? '',
                ];
            }

            return $csvData;
        } catch (\Exception $e) {
            Log::error('Error in exportToCsv', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            $response = $this->makeRequest('/phone/call_logs', [
                'page_size' => 1,
                'from' => Carbon::now()->subDays(1)->toDateString(),
                'to' => Carbon::now()->toDateString(),
            ]);

            return [
                'success' => true,
                'message' => 'Connection successful',
                'data' => $response,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
