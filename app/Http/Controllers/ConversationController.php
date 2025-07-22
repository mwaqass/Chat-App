<?php

namespace App\Http\Controllers;

use App\Events\ConversationMessageSent;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ConversationController
 *
 * Handles all conversation-related actions including
 * message retrieval, sending, and real-time broadcasting.
 */
class ConversationController extends Controller
{
    /**
     * Display the conversation interface with a specific user.
     */
    public function show(User $conversationPartner)
    {
        return view('conversation', [
            'conversationPartner' => $conversationPartner
        ]);
    }

    /**
     * Retrieve conversation messages between the authenticated user and a partner.
     */
    public function getMessages(User $conversationPartner)
    {
        $messages = ConversationMessage::betweenUsers(
            auth()->id(),
            $conversationPartner->id
        )
        ->with(['sender', 'recipient'])
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json($messages);
    }

    /**
     * Send a new message to a conversation partner.
     */
    public function sendMessage(Request $request, User $conversationPartner)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = ConversationMessage::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $conversationPartner->id,
            'content' => $request->input('content')
        ]);

        // Load relationships for broadcasting
        $message->load(['sender', 'recipient']);

        Log::info('Broadcasting conversation message', [
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'recipient_id' => $message->recipient_id,
            'channel' => "conversation.{$message->recipient_id}",
            'event' => 'ConversationMessageSent'
        ]);

        try {
            // Broadcast the message for real-time delivery
            broadcast(new ConversationMessageSent($message));
            Log::info('Message broadcasted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to broadcast message: ' . $e->getMessage());
        }

        return response()->json($message);
    }

    /**
     * Get all available conversation partners for the authenticated user.
     */
    public function getConversationPartners()
    {
        $partners = User::whereNot('id', auth()->id())
            ->select('id', 'name', 'email')
            ->get();

        return response()->json($partners);
    }
}
