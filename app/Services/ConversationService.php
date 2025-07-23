<?php

namespace App\Services;

use App\Events\ConversationMessageSent;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * ConversationService
 *
 * Handles business logic for conversation operations including
 * message management, user interactions, and real-time features.
 */
class ConversationService
{
    /**
     * Send a message between two users.
     *
     * @param int $senderId
     * @param int $recipientId
     * @param string $content
     * @return ConversationMessage
     * @throws \Exception
     */
    public function sendMessage(int $senderId, int $recipientId, string $content): ConversationMessage
    {
        return DB::transaction(function () use ($senderId, $recipientId, $content) {
            // Validate users exist
            $sender = User::findOrFail($senderId);
            $recipient = User::findOrFail($recipientId);

            // Create the message
            $message = ConversationMessage::create([
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'content' => $this->sanitizeMessage($content)
            ]);

            // Load relationships for broadcasting
            $message->load(['sender', 'recipient']);

            // Log the message for audit purposes
            $this->logMessageActivity($message);

            // Broadcast the message
            $this->broadcastMessage($message);

            return $message;
        });
    }

    /**
     * Get conversation history between two users.
     *
     * @param int $user1Id
     * @param int $user2Id
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConversationHistory(int $user1Id, int $user2Id, int $limit = 50)
    {
        return ConversationMessage::betweenUsers($user1Id, $user2Id)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();
    }

    /**
     * Get recent conversations for a user.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentConversations(int $userId, int $limit = 10)
    {
        return ConversationMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_id === $userId
                    ? $message->recipient_id
                    : $message->sender_id;
            });
    }

    /**
     * Get conversation statistics for a user.
     *
     * @param int $userId
     * @return array
     */
    public function getConversationStats(int $userId): array
    {
        $totalMessages = ConversationMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->count();

        $messagesToday = ConversationMessage::where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->orWhere('recipient_id', $userId);
        })
        ->whereDate('created_at', today())
        ->count();

        $uniqueConversations = ConversationMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->selectRaw('CASE
                WHEN sender_id = ? THEN recipient_id
                ELSE sender_id
                END as partner_id', [$userId])
            ->distinct()
            ->count();

        return [
            'total_messages' => $totalMessages,
            'messages_today' => $messagesToday,
            'unique_conversations' => $uniqueConversations,
            'average_messages_per_day' => $this->calculateAverageMessagesPerDay($userId)
        ];
    }

    /**
     * Sanitize message content.
     *
     * @param string $content
     * @return string
     */
    private function sanitizeMessage(string $content): string
    {
        // Remove excessive whitespace
        $content = preg_replace('/\s+/', ' ', trim($content));

        // Basic XSS protection
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        return $content;
    }

    /**
     * Log message activity for audit purposes.
     *
     * @param ConversationMessage $message
     * @return void
     */
    private function logMessageActivity(ConversationMessage $message): void
    {
        Log::info('Message sent', [
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'recipient_id' => $message->recipient_id,
            'content_length' => strlen($message->content),
            'timestamp' => $message->created_at
        ]);
    }

    /**
     * Broadcast message to recipient.
     *
     * @param ConversationMessage $message
     * @return void
     */
    private function broadcastMessage(ConversationMessage $message): void
    {
        try {
            broadcast(new ConversationMessageSent($message));
            Log::info('Message broadcasted successfully', [
                'message_id' => $message->id,
                'channel' => "conversation.{$message->recipient_id}"
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast message', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate average messages per day for a user.
     *
     * @param int $userId
     * @return float
     */
    private function calculateAverageMessagesPerDay(int $userId): float
    {
        $firstMessage = ConversationMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$firstMessage) {
            return 0.0;
        }

        $totalDays = max(1, $firstMessage->created_at->diffInDays(now()));
        $totalMessages = ConversationMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->count();

        return round($totalMessages / $totalDays, 2);
    }

    /**
     * Get online users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOnlineUsers()
    {
        // This is a placeholder - in a real application, you would
        // track user online status in Redis or a similar system
        return User::where('id', '!=', auth()->id())->get();
    }

    /**
     * Mark messages as read for a conversation.
     *
     * @param int $userId
     * @param int $partnerId
     * @return int
     */
    public function markMessagesAsRead(int $userId, int $partnerId): int
    {
        return ConversationMessage::where('sender_id', $partnerId)
            ->where('recipient_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
