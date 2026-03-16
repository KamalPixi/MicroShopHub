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
                        <p class="text-xs text-gray-500">We reply via Telegram</p>
                    </div>
                    <button type="button" wire:click="toggle" class="text-gray-500 hover:text-gray-700">×</button>
                </div>

                <div class="p-4 space-y-2 max-h-72 overflow-y-auto" wire:poll.6s="pollMessages">
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
                </div>

                @if($currentProduct)
                    <div class="px-4 py-2 border-t border-gray-100">
                        <button type="button" wire:click="shareProduct" class="text-xs text-primary font-semibold hover:underline">
                            Share this product
                        </button>
                    </div>
                @endif

                <div class="px-4 py-3 border-t border-gray-100">
                    <div class="flex gap-2">
                        <input type="text" wire:model.live="message" class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-xs focus:border-primary focus:ring-primary" placeholder="Type a message...">
                        <button type="button" wire:click="sendMessage" class="bg-primary text-white px-3 py-2 rounded-md text-xs font-semibold">Send</button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
