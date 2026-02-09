<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommunityAnnouncementPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $announcement;
    public int $communityId;
    public string $communityName;

    /**
     * Create a new event instance.
     */
    public function __construct(array $announcement, int $communityId, string $communityName)
    {
        $this->announcement = $announcement;
        $this->communityId = $communityId;
        $this->communityName = $communityName;
    }

    /**
     * Get the channels the event should broadcast on.
     * Broadcast to all members of the community
     */
    public function broadcastOn()
    {
        return new PrivateChannel('community.' . $this->communityId);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'announcement.posted';
    }

    /**
     * Data to broadcast with the event.
     */
    public function broadcastWith()
    {
        return [
            'announcement' => $this->announcement,
            'community_id' => $this->communityId,
            'community_name' => $this->communityName,
        ];
    }
}

