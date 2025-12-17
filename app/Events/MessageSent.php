<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var ChatMessage
     */
    public ChatMessage $message;

    /**
     * Create a new event instance.
     *
     * @param ChatMessage $message
     */
    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.conversation.' . $this->message->conversation_id);
    }

    /**
     * Data to broadcast with the event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        // Ensure relationships are available
        $this->message->loadMissing(['user', 'attachments']);

        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'user' => [
                'id' => $this->message->user->id ?? null,
                'name' => $this->message->user->name ?? null,
                'avatar' => $this->message->user->avatar ?? null,
            ],
            'message' => $this->message->message,
            'type' => $this->message->type,
            'attachments' => $this->message->attachments->map(function ($att) {
                return [
                    'id' => $att->id,
                    'file_name' => $att->file_name,
                    'url' => $att->url,
                    'mime_type' => $att->mime_type,
                ];
            })->toArray(),
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }

    /**
     * Custom event name for the client side.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }
}
