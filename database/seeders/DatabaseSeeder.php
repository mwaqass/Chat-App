<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ConversationMessage;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create multiple test users for conversation testing
        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => '12345678',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => '12345678',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => '12345678',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => '12345678',
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@example.com',
                'password' => '12345678',
            ],
        ];

        foreach ($users as $userData) {
            User::factory()->create($userData);
        }

        // Create some sample conversation messages
        $this->createSampleConversations();
    }

    /**
     * Create sample conversation messages between users.
     */
    private function createSampleConversations(): void
    {
        $users = User::all();

        // Create conversations between different user pairs
        $conversationPairs = [
            [1, 2], // Test User <-> John Doe
            [1, 3], // Test User <-> Jane Smith
            [2, 3], // John Doe <-> Jane Smith
            [4, 5], // Mike Johnson <-> Sarah Wilson
        ];

        foreach ($conversationPairs as [$user1Id, $user2Id]) {
            // Create messages from user1 to user2
            ConversationMessage::create([
                'sender_id' => $user1Id,
                'recipient_id' => $user2Id,
                'content' => "Hello! How are you doing today?",
            ]);

            // Create messages from user2 to user1
            ConversationMessage::create([
                'sender_id' => $user2Id,
                'recipient_id' => $user1Id,
                'content' => "Hi there! I'm doing great, thanks for asking!",
            ]);
        }
    }
}
