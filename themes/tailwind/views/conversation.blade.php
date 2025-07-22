<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Conversation with') }} {{ $conversationPartner->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $conversationPartner->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $conversationPartner->email }}</p>
                        </div>
                        <a
                            href="{{ route('dashboard') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors"
                        >
                            Back to Dashboard
                        </a>
                    </div>

                    <conversation-interface
                        :conversation-partner="{{ $conversationPartner }}"
                        :current-user="{{ auth()->user() }}"
                    ></conversation-interface>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
