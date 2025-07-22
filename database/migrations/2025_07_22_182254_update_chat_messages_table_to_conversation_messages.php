<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Rename columns to be more descriptive
            $table->renameColumn('receiver_id', 'recipient_id');
            $table->renameColumn('text', 'content');
        });

        // Rename the table
        Schema::rename('chat_messages', 'conversation_messages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('conversation_messages', 'chat_messages');

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->renameColumn('recipient_id', 'receiver_id');
            $table->renameColumn('content', 'text');
        });
    }
};
