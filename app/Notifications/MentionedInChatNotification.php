<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionedInChatNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('You have been mentioned in a chat message.')
            ->action('View Message', route('chat.index'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $conversation = $this->message->conversation;
        $sender = $this->message->user;
        
        return [
            'type' => 'mention',
            'title' => 'Mentioned in ' . $conversation->name,
            'message' => $sender->name . ' mentioned you in: ' . substr($this->message->message, 0, 50) . '...',
            'conversation_id' => $conversation->id,
            'message_id' => $this->message->id,
            'sender_name' => $sender->name,
            'sender_avatar' => $sender->avatar,
        ];
    }
}
