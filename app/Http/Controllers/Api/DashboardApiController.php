<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Dashboard Metrics API Controller
 * Fetches data from the boss's dashboard endpoint and serves it to your CRM
 * 
 * Place this file at: app/Http/Controllers/Api/DashboardApiController.php
 * 
 * Add route in routes/api.php:
 * Route::get('/dashboard/metrics', [DashboardApiController::class, 'getMetrics'])->middleware('auth:sanctum');
 */
class DashboardApiController extends Controller
{
    /**
     * The boss's dashboard endpoint
     * Update this to the actual endpoint URL
     */
    private $bossEndpoint = 'https://your-boss-dashboard.com/dashboard-metrics.php';
    
    /**
     * Cache duration in seconds (5 minutes)
     */
    private $cacheDuration = 300;
    
    /**
     * Get all dashboard metrics
     * This method fetches from the boss's dashboard and transforms it for your CRM
     */
    public function getMetrics()
    {
        try {
            // Try to get from cache first
            $data = Cache::remember('dashboard_metrics', $this->cacheDuration, function () {
                return $this->fetchFromBossEndpoint();
            });
            
            return response()->json([
                'success' => true,
                'cached' => Cache::has('dashboard_metrics'),
                'timestamp' => now()->toIso8601String(),
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch dashboard metrics',
                'message' => config('app.debug') ? $e->getMessage() : 'Please contact support'
            ], 500);
        }
    }
    
    /**
     * Fetch data from boss's dashboard endpoint
     */
    private function fetchFromBossEndpoint()
    {
        // Make HTTP request to boss's dashboard
        $response = Http::timeout(30)->get($this->bossEndpoint);
        
        if (!$response->successful()) {
            throw new \Exception('Failed to fetch from boss endpoint');
        }
        
        $rawData = $response->json();
        
        // Transform boss's data structure to match your CRM format
        return $this->transformBossData($rawData);
    }
    
    /**
     * Transform boss's dashboard data format to your CRM format
     * Adjust this based on the actual structure of boss's data
     */
    private function transformBossData($rawData)
    {
        // Extract data from boss's format
        return [
            // Basic metrics
            'total_sales_today' => $rawData['totalSalesToday'] ?? 0,
            'total_monthly_sales' => $rawData['done'] ?? $rawData['TOTAL'] ?? 0,
            'total_revenue' => $rawData['totalRevenue'] ?? $rawData['total_revenue'] ?? 0,
            
            // Sales status
            'done_count' => $rawData['done'] ?? $rawData['TOTAL'] ?? 0,
            'approved_count' => $rawData['approved'] ?? $rawData['APPROVED'] ?? 0,
            'pending_count' => $rawData['pending'] ?? $rawData['PENDING'] ?? 0,
            'cancelled_count' => $rawData['cancelled'] ?? $rawData['CANCELLED'] ?? 0,

            // Agent performance
            'total_agents' => $rawData['totalAgents'] ?? 0,
            'active_agents' => $rawData['activeAgents'] ?? 0,

            // Keep original data for reference
            'raw_data' => $rawData
        ];
    }

    /**
     * Refresh dashboard cache
     */
    public function refresh()
    {
        try {
            Cache::forget('dashboard_metrics');
            $data = $this->fetchFromBossEndpoint();
            Cache::put('dashboard_metrics', $data, $this->cacheDuration);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache refreshed',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to refresh dashboard metrics',
                'message' => config('app.debug') ? $e->getMessage() : 'Please contact support'
            ], 500);
        }
    }
}