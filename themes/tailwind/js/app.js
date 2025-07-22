/**
 * Main JavaScript entry point for the Laravel Reverb Chat application.
 * This file loads all dependencies and initializes the Vue application.
 */

import "./bootstrap";

/**
 * Create a fresh Vue application instance.
 */
import { createApp } from "vue/dist/vue.esm-bundler.js";

const app = createApp({});

/**
 * Register Vue components.
 * Components are automatically registered with their "basename".
 */

import ConversationInterface from "./components/ConversationInterface.vue";

app.component("conversation-interface", ConversationInterface);

/**
 * Mount the Vue application to the page.
 * You may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
app.mount("#app");
