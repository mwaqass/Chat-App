<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * Root route - redirects authenticated users to dashboard,
 * unauthenticated users to login page
 */
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/**
 * Authenticated user routes
 * All routes in this group require user authentication
 */
Route::middleware(['auth'])->group(function () {

    // Dashboard and user management routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'getProfile'])->name('profile');
    Route::get('/search-partners', [DashboardController::class, 'searchPartners'])->name('search.partners');

    // Real-time conversation routes
    Route::get('/conversation/{conversationPartner}', [ConversationController::class, 'show'])->name('conversation.show');
    Route::get('/conversation/{conversationPartner}/messages', [ConversationController::class, 'getMessages'])->name('conversation.messages');
    Route::post('/conversation/{conversationPartner}/send', [ConversationController::class, 'sendMessage'])->name('conversation.send');
    Route::get('/conversation-partners', [ConversationController::class, 'getConversationPartners'])->name('conversation.partners');

    // Enhanced conversation features
    Route::get('/conversation-stats', [ConversationController::class, 'getStats'])->name('conversation.stats');
    Route::post('/conversation/{conversationPartner}/mark-read', [ConversationController::class, 'markAsRead'])->name('conversation.mark-read');
});

// Include authentication routes (login, register, password reset, etc.)
require __DIR__ . '/auth.php';
