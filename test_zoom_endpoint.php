<?php
require_once __DIR__.'/vendor/autoload.php';

// Simple test for the Zoom API endpoint
$url = 'http://localhost:8000/zoom/dial/9693';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Testing: $url\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "CURL Error: $error\n";
} else {
    echo "Response: $response\n";
}