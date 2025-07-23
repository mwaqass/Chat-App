<?php

namespace App\Events;

use App\Models\ConversationMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ConversationMessageSent Event
 *
 * Broadcasts when a new conversation message is sent.
 * This event is used for real-time message delivery to ensure
 * instant communication between users in the chat application.
 *
 * Implements ShouldBroadcastNow for immediate broadcasting
 * without queuing to provide real-time experience.
 */
class ConversationMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\ConversationMessage $message The message that was sent
     */
    public function __construct(public ConversationMessage $message)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * Broadcasts to a private channel specific to the message recipient
     * to ensure secure and targeted message delivery.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("conversation.{$this->message->recipient_id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * Includes the message with its sender and recipient relationships
     * loaded for complete message context on the frontend.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->load(['sender', 'recipient']),
        ];
    }

    /**
     * Get the broadcast event name.
     *
     * Provides a consistent event name for frontend listeners
     * to identify and handle this specific event type.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'ConversationMessageSent';
    }
}
