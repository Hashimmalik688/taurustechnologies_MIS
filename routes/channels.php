<?php

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('calls.{callerNumber}', function ($user, $callerNumber) {
    return (int) $user->zoom_number === (int) $callerNumber;
});

Broadcast::channel('call', function ($user) {
    return true; // Allow all authenticated users to listen
});

// Authorize private chat conversation channels
Broadcast::channel('chat.conversation.{conversationId}', function ($user, $conversationId) {
    return \App\Models\ChatConversation::where('id', $conversationId)
        ->whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->exists();
});

// Authorize community announcement channels - only members can listen
Broadcast::channel('community.{communityId}', function ($user, $communityId) {
    return \DB::table('community_members')
        ->where('community_id', $communityId)
        ->where('user_id', $user->id)
        ->exists();
});
