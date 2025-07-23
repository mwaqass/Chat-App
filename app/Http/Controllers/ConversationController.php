<?php

namespace App\Http\Controllers;

use App\Services\ConversationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

/**
 * ConversationController
 *
 * Handles all conversation-related actions including
 * message retrieval, sending, and real-time broadcasting.
 * Uses ConversationService for business logic separation.
 */
class ConversationController extends Controller
{
    protected ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * Display the conversation interface with a specific user.
     *
     * @param User $conversationPartner
     * @return View
     */
    public function show(User $conversationPartner): View
    {
        // Mark messages as read when opening conversation
        $this->conversationService->markMessagesAsRead(
            auth()->id(),
            $conversationPartner->id
        );

        return view('conversation', [
            'conversationPartner' => $conversationPartner
        ]);
    }

    /**
     * Retrieve conversation messages between the authenticated user and a partner.
     *
     * @param User $conversationPartner
     * @return JsonResponse
     */
    public function getMessages(User $conversationPartner): JsonResponse
    {
        try {
            $messages = $this->conversationService->getConversationHistory(
                auth()->id(),
                $conversationPartner->id,
                100
            );

            return response()->json($messages);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve conversation messages', [
                'user_id' => auth()->id(),
                'partner_id' => $conversationPartner->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to load conversation history'
            ], 500);
        }
    }

    /**
     * Send a new message to a conversation partner.
     *
     * @param Request $request
     * @param User $conversationPartner
     * @return JsonResponse
     */
    public function sendMessage(Request $request, User $conversationPartner): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000|min:1',
        ], [
            'content.required' => 'Message content is required.',
            'content.max' => 'Message cannot exceed 1000 characters.',
            'content.min' => 'Message cannot be empty.'
        ]);

        try {
            $message = $this->conversationService->sendMessage(
                auth()->id(),
                $conversationPartner->id,
                $request->input('content')
            );

            return response()->json($message, 201);
        } catch (\Exception $e) {
            Log::error('Failed to send message', [
                'user_id' => auth()->id(),
                'partner_id' => $conversationPartner->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }

    /**
     * Get all available conversation partners for the authenticated user.
     *
     * @return JsonResponse
     */
    public function getConversationPartners(): JsonResponse
    {
        try {
            $partners = $this->conversationService->getOnlineUsers();

            return response()->json($partners);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve conversation partners', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to load conversation partners'
            ], 500);
        }
    }

    /**
     * Get conversation statistics for the authenticated user.
     *
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->conversationService->getConversationStats(auth()->id());

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve conversation stats', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to load conversation statistics'
            ], 500);
        }
    }

    /**
     * Mark messages as read for a specific conversation.
     *
     * @param User $conversationPartner
     * @return JsonResponse
     */
    public function markAsRead(User $conversationPartner): JsonResponse
    {
        try {
            $updatedCount = $this->conversationService->markMessagesAsRead(
                auth()->id(),
                $conversationPartner->id
            );

            return response()->json([
                'message' => 'Messages marked as read',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark messages as read', [
                'user_id' => auth()->id(),
                'partner_id' => $conversationPartner->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to mark messages as read'
            ], 500);
        }
    }
}
