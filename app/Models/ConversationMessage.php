<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ConversationMessage Model
 *
 * Represents individual messages in the real-time conversation system.
 * Each message is associated with a sender and recipient user.
 *
 * This model handles:
 * - Message relationships with users
 * - Conversation querying between users
 * - Message formatting and display
 */
class ConversationMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'content'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who sent the message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received the message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Scope to get messages between two users.
     *
     * Retrieves all messages exchanged between two specific users,
     * regardless of who sent or received each message.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $user1Id
     * @param int $user2Id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)
              ->where('recipient_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)
              ->where('recipient_id', $user1Id);
        });
    }

    /**
     * Get the conversation partner for a given user.
     *
     * Returns the other user in the conversation (not the current user).
     *
     * @param int $currentUserId
     * @return \App\Models\User|null
     */
    public function getConversationPartner($currentUserId)
    {
        return $this->sender_id === $currentUserId
            ? $this->recipient
            : $this->sender;
    }

    /**
     * Check if the message was sent by a specific user.
     *
     * @param int $userId
     * @return bool
     */
    public function isSentBy($userId)
    {
        return $this->sender_id === $userId;
    }

    /**
     * Get a formatted timestamp for display.
     *
     * @return string
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Get a formatted date for display.
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M j, Y');
    }
}
