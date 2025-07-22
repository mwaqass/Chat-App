<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * DashboardController
 *
 * Handles dashboard-related actions including
 * user listing and conversation partner management.
 */
class DashboardController extends Controller
{
    /**
     * Display the main dashboard with available conversation partners.
     */
    public function index()
    {
        $conversationPartners = User::whereNot('id', auth()->id())
            ->select('id', 'name', 'email')
            ->get();

        return view('dashboard', [
            'conversationPartners' => $conversationPartners
        ]);
    }

    /**
     * Get the authenticated user's profile information.
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
     */
    public function searchPartners(Request $request)
    {
        $query = $request->input('query');

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
