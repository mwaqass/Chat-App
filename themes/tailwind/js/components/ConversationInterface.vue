<template>
    <div class="conversation-container">
        <div class="flex flex-col justify-end h-80">
            <div ref="messagesContainer" class="p-4 overflow-y-auto max-h-fit">
                <div
                    v-for="message in conversationMessages"
                    :key="message.id"
                    class="flex items-center mb-2"
                >
                    <div
                        v-if="message.sender_id === currentUser.id"
                        class="p-2 ml-auto text-white bg-blue-500 rounded-lg"
                    >
                        {{ message.content }}
                    </div>
                    <div v-else class="p-2 mr-auto bg-gray-200 rounded-lg">
                        {{ message.content }}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center">
            <input
                type="text"
                v-model="newMessageContent"
                @keydown="handleTypingIndicator"
                @keyup.enter="sendConversationMessage"
                placeholder="Type your message..."
                class="flex-1 px-2 py-1 border rounded-lg"
            />
            <button
                @click="sendConversationMessage"
                class="px-4 py-1 ml-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600"
            >
                Send
            </button>
        </div>
        <small v-if="isPartnerTyping" class="text-gray-700">
            {{ conversationPartner.name }} is typing...
        </small>
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
const isPartnerTyping = ref(false);
const typingIndicatorTimer = ref(null);

watch(
    conversationMessages,
    () => {
        nextTick(() => {
            if (messagesContainer.value) {
                messagesContainer.value.scrollTo({
                    top: messagesContainer.value.scrollHeight,
                    behavior: "smooth",
                });
            }
        });
    },
    { deep: true }
);

const sendConversationMessage = async () => {
    if (newMessageContent.value.trim() !== "") {
        try {
            const response = await axios.post(
                `/conversation/${props.conversationPartner.id}/send`,
                {
                    content: newMessageContent.value,
                }
            );
            conversationMessages.value.push(response.data);
            newMessageContent.value = "";
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }
};

const handleTypingIndicator = () => {
    Echo.private(`conversation.${props.conversationPartner.id}`).whisper("typing", {
        userId: props.currentUser.id,
    });
};

const loadConversationHistory = async () => {
    try {
        const response = await axios.get(
            `/conversation/${props.conversationPartner.id}/messages`
        );
        console.log('Loaded conversation messages:', response.data);
        conversationMessages.value = response.data;
    } catch (error) {
        console.error('Error loading conversation history:', error);
    }
};

const setupRealTimeListeners = () => {
    console.log('Setting up real-time listeners for user:', props.currentUser.id);

    // Check if Echo is available
    if (typeof window.Echo === 'undefined') {
        console.error('Echo is not defined! Check if Laravel Echo is properly loaded.');
        return;
    }

    const channel = Echo.private(`conversation.${props.currentUser.id}`);

    // Test channel connection
    channel.subscribed(() => {
        console.log('Successfully subscribed to channel: conversation.' + props.currentUser.id);
    });

    channel.listen(".ConversationMessageSent", (response) => {
        console.log('Received ConversationMessageSent event:', response);
        console.log('Message sender:', response.message.sender_id, 'Partner ID:', props.conversationPartner.id);

        // Only add the message if it's from the current conversation partner
        if (response.message.sender_id === props.conversationPartner.id) {
            console.log('Adding message to conversation:', response.message);
            conversationMessages.value.push(response.message);
        } else {
            console.log('Message not from current partner, ignoring');
        }
    });

    channel.listenForWhisper("typing", (response) => {
        console.log('Received typing whisper:', response);
        isPartnerTyping.value = response.userId === props.conversationPartner.id;

        if (typingIndicatorTimer.value) {
            clearTimeout(typingIndicatorTimer.value);
        }

        typingIndicatorTimer.value = setTimeout(() => {
            isPartnerTyping.value = false;
        }, 1000);
    });

    // Add error handling for Echo
    channel.error((error) => {
        console.error('Echo connection error:', error);
    });

    console.log('Echo listening on channel: conversation.' + props.currentUser.id);
};

onMounted(() => {
    console.log('ConversationInterface mounted for user:', props.currentUser.id, 'with partner:', props.conversationPartner.id);

    loadConversationHistory();
    setupRealTimeListeners();
});
</script>

<style scoped>
.conversation-container {
    @apply bg-white rounded-lg shadow-md p-4;
}
</style>
