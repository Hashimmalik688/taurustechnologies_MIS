#!/usr/bin/env php
<?php

// Simulate a Zoom webhook call to test if the webhook processing works

$webhookUrl = 'http://localhost:8000/zoom/webhook';

// Sample webhook payload from Zoom when call is answered
$webhookPayload = [
    'event' => 'phone.callee_answered',
    'payload' => [
        'object' => [
            'id' => 'test_call_' . time(),
            'call_logs' => [
                [
                    'caller_did_number' => '7867077990',  // Your number
                    'callee_did_number' => '2393871921',  // Lead's number
                    'direction' => 'outbound',
                    'result' => 'Call answered'
                ]
            ]
        ]
    ],
    'event_ts' => time() * 1000
];

echo "🔵 Sending test webhook to: $webhookUrl\n";
echo "📦 Payload:\n";
echo json_encode($webhookPayload, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: Zoom-Webhook/1.0'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📊 HTTP Status: $httpCode\n";
echo "📄 Response: $response\n\n";

if ($httpCode === 200) {
    echo "✅ Webhook test successful!\n";
} else {
    echo "❌ Webhook test failed!\n";
}

echo "\n💡 Now check storage/logs/laravel.log for WEBHOOK entries\n";
