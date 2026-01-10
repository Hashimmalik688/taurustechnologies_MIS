<?php

// Simple test script to check API endpoints
$endpoints = [
    'http://localhost:8000/zoom/test-api',
    'http://localhost:8000/zoom/test-phone-auth'
];

foreach ($endpoints as $endpoint) {
    echo "Testing: $endpoint\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    if ($error) {
        echo "Error: $error\n";
    } else {
        echo "Response: " . substr($response, 0, 200) . "\n";
    }
    echo "---\n";
}