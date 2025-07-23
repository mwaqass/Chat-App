<template>
    <div class="conversation-container bg-white rounded-lg shadow-lg border border-gray-200">
        <!-- Conversation Header -->
        <div class="conversation-header bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-lg font-semibold">{{ conversationPartner.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">{{ conversationPartner.name }}</h3>
                        <p class="text-blue-100 text-sm">{{ conversationPartner.email }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-blue-100">Online</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="messages-container h-96 overflow-y-auto p-4 bg-gray-50" ref="messagesContainer">
            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center h-full">
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-600">Loading messages...</span>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="conversationMessages.length === 0" class="flex items-center justify-center h-full">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Start a conversation</h3>
                    <p class="text-gray-600">Send a message to begin chatting with {{ conversationPartner.name }}</p>
                </div>
            </div>

            <!-- Messages -->
            <div v-else class="space-y-4">
                <div
                    v-for="(message, index) in conversationMessages"
                    :key="message.id"
                    class="flex"
                    :class="message.sender_id === currentUser.id ? 'justify-end' : 'justify-start'"
                >
                    <div
                        class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg shadow-sm"
                        :class="message.sender_id === currentUser.id
                            ? 'bg-blue-600 text-white'
                            : 'bg-white text-gray-900 border border-gray-200'"
                    >
                        <div class="flex items-end space-x-2">
                            <div class="flex-1">
                                <p class="text-sm break-words">{{ message.content }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span
                                class="text-xs"
                                :class="message.sender_id === currentUser.id
                                    ? 'text-blue-100'
                                    : 'text-gray-500'"
                            >
                                {{ formatMessageTime(message.created_at) }}
                            </span>
                            <div v-if="message.sender_id === currentUser.id" class="flex items-center space-x-1">
                                <svg v-if="message.sent" class="w-3 h-3 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Typing Indicator -->
            <div v-if="isPartnerTyping" class="flex justify-start mt-4">
                <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm">
                    <div class="flex items-center space-x-1">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                        <span class="text-xs text-gray-600 ml-2">{{ conversationPartner.name }} is typing...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="message-input-container p-4 border-t border-gray-200 bg-white rounded-b-lg">
            <div class="flex items-end space-x-3">
                <div class="flex-1">
                    <textarea
                        v-model="newMessageContent"
                        @keydown="handleKeyDown"
                        @input="handleTypingIndicator"
                        placeholder="Type your message..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        rows="1"
                        ref="messageInput"
                        :disabled="sending"
                    ></textarea>
                </div>
                <button
                    @click="sendConversationMessage"
                    :disabled="!newMessageContent.trim() || sending"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                >
                    <svg v-if="sending" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>{{ sending ? 'Sending...' : 'Send' }}</span>
                </button>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm text-red-700">{{ errorMessage }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { nextTick, onMounted, ref, watch } from "vue";

const props = defineProps({
    conversationPartner: {
        type: Object,
        required: true,
    },
    currentUser: {
        type: Object,
        required: true,
    },
});

const conversationMessages = ref([]);
const newMessageContent = ref("");
const messagesContainer = ref(null);
const messageInput = ref(null);
const isPartnerTyping = ref(false);
const typingIndicatorTimer = ref(null);
const loading = ref(true);
const sending = ref(false);
const errorMessage = ref("");

// Watch for new messages and scroll to bottom
watch(
    conversationMessages,
    () => {
        nextTick(() => {
            scrollToBottom();
        });
    },
    { deep: true }
);

// Auto-resize textarea
watch(newMessageContent, () => {
    nextTick(() => {
        if (messageInput.value) {
            messageInput.value.style.height = 'auto';
            messageInput.value.style.height = messageInput.value.scrollHeight + 'px';
        }
    });
});

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: "smooth",
        });
    }
};

const formatMessageTime = (timestamp) => {
    const date = new Date(timestamp);
    const now = new Date();
    const diffInHours = (now - date) / (1000 * 60 * 60);

    if (diffInHours < 24) {
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    } else {
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }
};

const handleKeyDown = (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendConversationMessage();
    }
};

const sendConversationMessage = async () => {
    if (newMessageContent.value.trim() === "" || sending.value) return;

    const messageContent = newMessageContent.value.trim();
    newMessageContent.value = "";
    sending.value = true;
    errorMessage.value = "";

    // Reset textarea height
    if (messageInput.value) {
        messageInput.value.style.height = 'auto';
    }

    try {
        const response = await axios.post(
            `/conversation/${props.conversationPartner.id}/send`,
            {
                content: messageContent,
            }
        );

        // Add sent status to the message
        const messageWithStatus = { ...response.data, sent: true };
        conversationMessages.value.push(messageWithStatus);

    } catch (error) {
        console.error('Error sending message:', error);
        errorMessage.value = "Failed to send message. Please try again.";
        // Restore the message content
        newMessageContent.value = messageContent;
    } finally {
        sending.value = false;
    }
};

const handleTypingIndicator = () => {
    if (typeof window.Echo !== 'undefined') {
        Echo.private(`conversation.${props.conversationPartner.id}`).whisper("typing", {
            userId: props.currentUser.id,
        });
    }
};

const loadConversationHistory = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            `/conversation/${props.conversationPartner.id}/messages`
        );
        conversationMessages.value = response.data.map(msg => ({ ...msg, sent: true }));
    } catch (error) {
        console.error('Error loading conversation history:', error);
        errorMessage.value = "Failed to load conversation history.";
    } finally {
        loading.value = false;
    }
};

const setupRealTimeListeners = () => {
    if (typeof window.Echo === 'undefined') {
        console.error('Echo is not defined! Check if Laravel Echo is properly loaded.');
        return;
    }

    const channel = Echo.private(`conversation.${props.currentUser.id}`);

    channel.subscribed(() => {
        console.log('Successfully subscribed to channel: conversation.' + props.currentUser.id);
    });

    channel.listen(".ConversationMessageSent", (response) => {
        if (response.message.sender_id === props.conversationPartner.id) {
            conversationMessages.value.push({ ...response.message, sent: true });
        }
    });

    channel.listenForWhisper("typing", (response) => {
        isPartnerTyping.value = response.userId === props.conversationPartner.id;

        if (typingIndicatorTimer.value) {
            clearTimeout(typingIndicatorTimer.value);
        }

        typingIndicatorTimer.value = setTimeout(() => {
            isPartnerTyping.value = false;
        }, 2000);
    });

    channel.error((error) => {
        console.error('Echo connection error:', error);
        errorMessage.value = "Real-time connection lost. Messages may be delayed.";
    });
};

onMounted(() => {
    loadConversationHistory();
    setupRealTimeListeners();

    // Focus on input after mount
    nextTick(() => {
        if (messageInput.value) {
            messageInput.value.focus();
        }
    });
});
</script>

<style scoped>
.conversation-container {
    @apply bg-white rounded-lg shadow-lg border border-gray-200;
}

.messages-container {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 #F7FAFC;
}

.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: #F7FAFC;
}

.messages-container::-webkit-scrollbar-thumb {
    background-color: #CBD5E0;
    border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background-color: #A0AEC0;
}
</style>
