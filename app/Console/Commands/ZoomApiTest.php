<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ZoomController;

class ZoomApiTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:test-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Zoom API capabilities professionally';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ZOOM API PROFESSIONAL TESTING ===');
        $this->info('Testing Zoom integration capabilities...');
        $this->newLine();

        try {
            $controller = new ZoomController();
            
            $this->info('1. Testing API Capabilities...');
            $response = $controller->testApiCapabilities();
            $data = $response->getData(true);
            
            if ($response->getStatusCode() === 200) {
                $this->info('✓ API Test successful');
                if (isset($data['capabilities'])) {
                    $caps = $data['capabilities'];
                    $this->line('  - User Info: ' . ($caps['user_info']['success'] ? '✓ Available' : '✗ Failed'));
                    $this->line('  - Phone Info: ' . (isset($caps['phone_info']) && $caps['phone_info']['success'] ? '✓ Available' : '✗ Failed'));
                    $this->line('  - Call History: ' . ($caps['call_history']['success'] ? '✓ Available' : '✗ Failed'));
                }
                
                // Show detailed API responses for debugging
                if (isset($data['capabilities']['user_info']['data'])) {
                    $this->newLine();
                    $this->line('User Info Details:');
                    $userInfo = $data['capabilities']['user_info']['data'];
                    $this->line('  - Email: ' . ($userInfo['email'] ?? 'N/A'));
                    $this->line('  - Account ID: ' . ($userInfo['account_id'] ?? 'N/A'));
                    $this->line('  - Type: ' . ($userInfo['type'] ?? 'N/A'));
                }
                
            } else {
                $this->error('✗ API Test failed: ' . $response->getStatusCode());
                $this->line('  Error: ' . ($data['error'] ?? 'Unknown error'));
                if (isset($data['message'])) {
                    $this->line('  Details: ' . $data['message']);
                }
            }
            
            $this->newLine();
            $this->info('2. Testing Phone Authorization...');
            $phoneResponse = $controller->testPhoneAuth();
            $phoneData = $phoneResponse->getData(true);
            
            if ($phoneResponse->getStatusCode() === 200) {
                $this->info('✓ Phone authorization successful');
                if (isset($phoneData['phone_number'])) {
                    $this->line('  - Phone Number: ' . $phoneData['phone_number']);
                    $this->line('  - Extension: ' . ($phoneData['extension_number'] ?? 'N/A'));
                    $this->line('  - Phone User ID: ' . ($phoneData['phone_user_id'] ?? 'N/A'));
                }
            } else {
                $this->error('✗ Phone authorization failed: ' . $phoneResponse->getStatusCode());
                $this->line('  Error: ' . ($phoneData['error'] ?? 'Unknown error'));
                if (isset($phoneData['response'])) {
                    $this->line('  Response: ' . substr($phoneData['response'], 0, 200) . '...');
                }
            }
            
            $this->newLine();
            $this->info('=== PROFESSIONAL CALL DETECTION CAPABILITIES ===');
            
            // Determine implementation strategy based on test results
            if ($response->getStatusCode() === 200 && isset($data['summary'])) {
                $summary = $data['summary'];
                
                if ($summary['can_get_call_logs']) {
                    $this->info('✓ Can track call history and status');
                    $this->line('  → We can detect completed calls, failed calls, and call duration');
                } else {
                    $this->warn('✗ Cannot access call logs');
                }
                
                if ($summary['has_phone_access']) {
                    $this->info('✓ Has phone API access');
                    $this->line('  → We can make calls programmatically');
                } else {
                    $this->warn('✗ No phone API access - limited to protocol launching');
                }
                
                if ($summary['can_get_user_info']) {
                    $this->info('✓ Can access user information');
                    $this->line('  → We can verify authorization status');
                }
                
                $this->newLine();
                $this->info('=== RECOMMENDED IMPLEMENTATION ===');
                
                if ($summary['can_get_call_logs'] && $summary['has_phone_access']) {
                    $this->info('PROFESSIONAL API INTEGRATION POSSIBLE:');
                    $this->line('1. Use Zoom Phone API to initiate calls programmatically');
                    $this->line('2. Poll call logs API every 5-10 seconds to detect status changes');
                    $this->line('3. Show Ravens form when call status changes to "completed"');
                    $this->line('4. Track failed calls and provide retry options');
                    $this->line('5. Store call duration and outcome in database');
                    $this->line('6. Real-time call status updates via API polling');
                    
                } else if ($summary['can_get_call_logs']) {
                    $this->info('HYBRID INTEGRATION RECOMMENDED:');
                    $this->line('1. Use zoomphonecall:// protocol to initiate calls');
                    $this->line('2. Poll call logs API to detect when calls complete');
                    $this->line('3. Show Ravens form based on API-detected completion');
                    $this->line('4. More reliable than protocol-only approach');
                    
                } else {
                    $this->warn('BASIC PROTOCOL INTEGRATION ONLY:');
                    $this->line('1. Use zoomphonecall:// protocol to initiate calls');
                    $this->line('2. Rely on user confirmation for call completion');
                    $this->line('3. Limited automatic detection capabilities');
                }
            } else {
                $this->error('API access failed - falling back to basic protocol-only integration');
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Test failed with exception: ' . $e->getMessage());
            $this->line('File: ' . $e->getFile() . ':' . $e->getLine());
        }
        
        $this->newLine();
        $this->info('=== TEST COMPLETE ===');
        
        return Command::SUCCESS;
    }
}
