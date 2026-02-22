<?php

namespace App\Notifications;

use App\Models\CommunityAnnouncement;
use App\Models\Community;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MentionedInAnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $announcement;
    protected $community;

    public function __construct(CommunityAnnouncement $announcement, Community $community)
    {
        $this->announcement = $announcement;
        $this->community = $community;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $sender = $this->announcement->creator;

        return [
            'type' => 'announcement_mention',
            'title' => 'Mentioned in ' . $this->community->name,
            'message' => ($sender->name ?? 'Someone') . ' mentioned you in an announcement: ' . substr($this->announcement->message, 0, 80) . (strlen($this->announcement->message) > 80 ? '...' : ''),
            'community_id' => $this->community->id,
            'community_name' => $this->community->name,
            'announcement_id' => $this->announcement->id,
            'sender_name' => $sender->name ?? 'Unknown',
            'sender_avatar' => $sender->avatar ?? null,
        ];
    }
}
