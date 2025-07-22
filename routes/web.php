<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Dashboard routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'getProfile'])->name('profile');
    Route::get('/search-partners', [DashboardController::class, 'searchPartners'])->name('search.partners');

    // Conversation routes
    Route::get('/conversation/{conversationPartner}', [ConversationController::class, 'show'])->name('conversation.show');
    Route::get('/conversation/{conversationPartner}/messages', [ConversationController::class, 'getMessages'])->name('conversation.messages');
    Route::post('/conversation/{conversationPartner}/send', [ConversationController::class, 'sendMessage'])->name('conversation.send');
    Route::get('/conversation-partners', [ConversationController::class, 'getConversationPartners'])->name('conversation.partners');
});

require __DIR__ . '/auth.php';
