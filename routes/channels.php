<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * Conversation channel authorization.
 * Users can only access their own conversation channels.
 */
Broadcast::channel('conversation.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
