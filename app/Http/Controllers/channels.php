<?php

use App\Models\ChatConversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Verify that the user is a participant of the conversation
    return ChatConversation::where('id', $conversationId)
        ->whereHas('participants', fn($query) => $query->where('user_id', $user->id))
        ->exists();
});
