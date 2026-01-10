<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate a console command environment
$app->instance('request', new Illuminate\Http\Request);
$app->make('config')->set('app.env', 'production'); // Avoid debug output

echo "=== ZOOM API PROFESSIONAL TESTING ===\n";
echo "Testing Zoom integration capabilities...\n\n";

try {
    // Create ZoomController instance
    $controller = new \App\Http\Controllers\ZoomController();
    
    echo "1. Testing API Capabilities...\n";
    $response = $controller->testApiCapabilities();
    $data = $response->getData(true);
    
    if ($response->getStatusCode() === 200) {
        echo "✓ API Test successful\n";
        if (isset($data['capabilities'])) {
            $caps = $data['capabilities'];
            echo "  - User Info: " . ($caps['user_info']['success'] ? "✓ Available" : "✗ Failed") . "\n";
            echo "  - Phone Info: " . (isset($caps['phone_info']) && $caps['phone_info']['success'] ? "✓ Available" : "✗ Failed") . "\n";
            echo "  - Call History: " . ($caps['call_history']['success'] ? "✓ Available" : "✗ Failed") . "\n";
        }
    } else {
        echo "✗ API Test failed: " . $response->getStatusCode() . "\n";
        echo "  Error: " . ($data['error'] ?? 'Unknown error') . "\n";
    }
    
    echo "\n2. Testing Phone Authorization...\n";
    $phoneResponse = $controller->testPhoneAuth();
    $phoneData = $phoneResponse->getData(true);
    
    if ($phoneResponse->getStatusCode() === 200) {
        echo "✓ Phone authorization successful\n";
        if (isset($phoneData['phone_number'])) {
            echo "  - Phone Number: " . $phoneData['phone_number'] . "\n";
            echo "  - Extension: " . ($phoneData['extension_number'] ?? 'N/A') . "\n";
            echo "  - Phone User ID: " . ($phoneData['phone_user_id'] ?? 'N/A') . "\n";
        }
    } else {
        echo "✗ Phone authorization failed: " . $phoneResponse->getStatusCode() . "\n";
        echo "  Error: " . ($phoneData['error'] ?? 'Unknown error') . "\n";
    }
    
    echo "\n=== PROFESSIONAL CALL DETECTION CAPABILITIES ===\n";
    
    // Based on test results, determine what we can do
    if ($response->getStatusCode() === 200 && isset($data['summary'])) {
        $summary = $data['summary'];
        
        if ($summary['can_get_call_logs']) {
            echo "✓ Can track call history and status\n";
            echo "  → We can detect completed calls, failed calls, and call duration\n";
        } else {
            echo "✗ Cannot access call logs\n";
        }
        
        if ($summary['has_phone_access']) {
            echo "✓ Has phone API access\n";
            echo "  → We can make calls programmatically\n";
        } else {
            echo "✗ No phone API access - limited to protocol launching\n";
        }
        
        if ($summary['can_get_user_info']) {
            echo "✓ Can access user information\n";
            echo "  → We can verify authorization status\n";
        }
    }
    
    echo "\n=== RECOMMENDED IMPLEMENTATION ===\n";
    
    if ($response->getStatusCode() === 200) {
        echo "Based on API capabilities, we recommend:\n";
        echo "1. Use zoomphonecall:// protocol to initiate calls\n";
        echo "2. Poll call logs API every 5-10 seconds to detect status changes\n";
        echo "3. Show Ravens form when call status changes to 'completed'\n";
        echo "4. Track failed calls and provide retry options\n";
        echo "5. Store call duration and outcome in database\n";
    } else {
        echo "API access failed - falling back to basic protocol-only integration\n";
    }
    
} catch (Exception $e) {
    echo "✗ Test failed with exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";