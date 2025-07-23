<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * DashboardController
 *
 * Handles dashboard-related actions including
 * user listing and conversation partner management.
 *
 * This controller provides the main interface for users to:
 * - View available conversation partners
 * - Access their profile information
 * - Search for specific users to start conversations with
 */
class DashboardController extends Controller
{
    /**
     * Display the main dashboard with available conversation partners.
     *
     * Retrieves all users except the authenticated user and
     * passes them to the dashboard view for conversation partner selection.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all users except the current authenticated user
        $conversationPartners = User::whereNot('id', auth()->id())
            ->select('id', 'name', 'email')
            ->get();

        return view('dashboard', [
            'conversationPartners' => $conversationPartners
        ]);
    }

    /**
     * Get the authenticated user's profile information.
     *
     * Returns a JSON response containing the current user's
     * basic profile data (id, name, email).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        $user = auth()->user();
        $profile = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ];

        return response()->json($profile);
    }

    /**
     * Search for conversation partners by name or email.
     *
     * Performs a case-insensitive search on user names and emails
     * to help users find specific conversation partners quickly.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPartners(Request $request)
    {
        $query = $request->input('query');

        // Search for users by name or email, excluding the current user
        $partners = User::whereNot('id', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'email')
            ->get();

        return response()->json($partners);
    }
}
