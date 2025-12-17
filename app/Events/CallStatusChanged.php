<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leadId;

    public $status;

    public $callerNumber;

    public $leadData;

    public $rawWebhookData;

    public function __construct($leadId, $status, $callerNumber = null, $leadData = null, $rawWebhookData = null)
    {
        $this->leadId = $leadId;
        $this->status = $status;
        $this->callerNumber = $callerNumber;
        $this->leadData = $leadData;
        $this->rawWebhookData = $rawWebhookData;
    }

    public function broadcastOn()
    {
        // Sanitize phone number for channel name
        $sanitizedNumber = $this->sanitizePhoneNumber($this->callerNumber);

        \Log::info('Broadcasting on channel', [
            'leadId' => $this->leadId,
            'status' => $this->status,
            'originalCallerNumber' => $this->callerNumber,
            'sanitizedNumber' => $sanitizedNumber,
            'channelName' => 'calls.'.$sanitizedNumber,
        ]);

        return new PrivateChannel('calls.'.$sanitizedNumber);
    }

    public function broadcastWith()
    {
        return [
            'leadId' => $this->leadId,
            'status' => $this->status,
            'callerNumber' => $this->callerNumber,
            'leadData' => $this->leadData,
            'rawWebhookData' => $this->rawWebhookData,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Remove invalid characters from phone number for channel name
     */
    private function sanitizePhoneNumber($phoneNumber)
    {
        if (! $phoneNumber) {
            return 'unknown';
        }

        // Remove +, spaces, dashes, parentheses, and other non-alphanumeric characters
        return preg_replace('/[^a-zA-Z0-9]/', '', $phoneNumber);
    }
}
