<div>
    @if($enabled)
        <div class="fixed bottom-6 right-6 z-50">
            <button type="button" wire:click="toggle" class="bg-primary text-white rounded-full shadow-lg px-4 py-3 text-sm font-semibold">
                {{ $open ? 'Close Chat' : 'Live Chat' }}
            </button>
        </div>

        @if($open)
            <div class="fixed bottom-20 right-6 w-80 max-w-full bg-white border border-gray-200 rounded-lg shadow-xl z-50">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Live Chat</p>
                        <p class="text-xs text-gray-500">
                            {{ $nameCaptured ? 'You: ' . $customerName : 'We typically reply within a few minutes.' }}
                        </p>
                    </div>
                    <button type="button" wire:click="toggle" class="text-gray-500 hover:text-gray-700">×</button>
                </div>

                <div id="live-chat-messages" class="p-4 space-y-2 max-h-72 overflow-y-auto" wire:poll.6s="pollMessages">
                    @if(!$nameCaptured)
                        <div class="space-y-3">
                            <p class="text-xs text-gray-500">Please enter your name to start the chat.</p>
                            <div class="flex gap-2">
                                <input type="text" wire:model.live="customerName" class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-xs focus:border-primary focus:ring-primary" placeholder="Your name">
                                <button type="button" wire:click="saveName" class="bg-primary text-white px-3 py-2 rounded-md text-xs font-semibold">Start</button>
                            </div>
                        </div>
                    @else
                        @forelse($messages as $msg)
                            <div class="flex {{ $msg['sender'] === 'customer' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[75%] rounded-lg px-3 py-2 text-xs {{ $msg['sender'] === 'customer' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700' }}">
                                    <p>{{ $msg['message'] }}</p>
                                    <p class="mt-1 text-[10px] opacity-70">{{ $msg['created_at'] }}</p>
                                    @if(!empty($msg['meta']['product']))
                                        <div class="mt-1 text-[10px] opacity-90">
                                            Product: {{ $msg['meta']['product']['name'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500">Ask us anything.</p>
                        @endforelse
                        <div id="live-chat-bottom"></div>
                    @endif
                </div>

                @if($currentProduct && $nameCaptured)
                    <div class="px-4 py-2 border-t border-gray-100">
                        <button type="button" wire:click="shareProduct" class="text-xs text-primary font-semibold hover:underline">
                            Share this product
                        </button>
                    </div>
                @endif

                <div class="px-4 py-3 border-t border-gray-100">
                    <div class="flex gap-2">
                        <input type="text" wire:model.live="message" class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-xs focus:border-primary focus:ring-primary" placeholder="Type a message..." {{ $nameCaptured ? '' : 'disabled' }}>
                        <button type="button" wire:click="sendMessage" wire:loading.attr="disabled" wire:target="sendMessage" class="bg-primary text-white px-3 py-2 rounded-md text-xs font-semibold flex items-center justify-center" {{ $nameCaptured ? '' : 'disabled' }}>
                            <span wire:loading.remove wire:target="sendMessage">Send</span>
                            <span wire:loading wire:target="sendMessage">
                                <svg class="w-3 h-3 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

<script>
    function scrollLiveChatToBottom() {
        const anchor = document.getElementById('live-chat-bottom');
        if (anchor) {
            anchor.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }
    }

    function initLiveChatObserver() {
        const container = document.getElementById('live-chat-messages');
        if (!container || container.dataset.observing === 'true') {
            return;
        }
        container.dataset.observing = 'true';
        const observer = new MutationObserver(() => {
            scrollLiveChatToBottom();
        });
        observer.observe(container, { childList: true, subtree: true });
        scrollLiveChatToBottom();
    }

    document.addEventListener('livewire:initialized', initLiveChatObserver);
    document.addEventListener('livewire:navigated', initLiveChatObserver);
    window.addEventListener('load', initLiveChatObserver);
    const bodyObserver = new MutationObserver(() => {
        initLiveChatObserver();
    });
    bodyObserver.observe(document.body, { childList: true, subtree: true });
</script>
