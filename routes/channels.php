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

/**
 * Conversation channel authorization.
 *
 * Users can only access their own conversation channels for security.
 * This ensures that messages are only broadcasted to the intended recipient.
 *
 * @param \App\Models\User $user The authenticated user
 * @param int $userId The user ID from the channel name
 * @return bool Whether the user can access this channel
 */
Broadcast::channel('conversation.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
