<div class="grid grid-cols-1 md:grid-cols-5 bg-gray-100 h-[calc(100vh-12rem)]">
    <div class="md:col-span-1 bg-white border-r border-gray-200 overflow-y-auto">
        <div class="sticky top-0 bg-white p-4 border-b">
            <input wire:model.live="searchOrder" type="text" placeholder="Search orders..."
                class="w-full px-3 py-2 border rounded-lg">
        </div>

        <div class="divide-y divide-gray-200">
            @foreach ($orders as $order)
                <button wire:click="selectOrder({{ $order->id }})"
                    class="w-full p-4 text-left hover:bg-gray-50 {{ $selectedOrder?->id === $order->id ? 'bg-gray-50' : '' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Order #{{ $order->id }}</span>
                        @if ($order->messages->where('receiver_id', auth('sanctum')->id())->where('is_read', false)->count() > 0)
                            <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs">
                                {{ $order->messages->where('receiver_id', auth('sanctum')->id())->where('is_read', false)->count() }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 truncate">{{ $order->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $order->messages->first()?->created_at->diffForHumans() }}</p>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Chat Area -->
    <div class="md:col-span-4 flex flex-col h-[calc(100vh-12rem)] border-r">
        @if ($selectedOrder)
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4">
                <h2 class="text-lg font-semibold">Order #{{ $selectedOrder->id }}</h2>
                <p class="text-sm text-gray-500">Customer: {{ $selectedOrder->user->name }}</p>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                @foreach ($selectedOrder->messages->sortBy('created_at') as $message)
                    <div
                        class="flex {{ $message->sender_id === auth('sanctum')->id() ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="{{ $message->sender_id === auth('sanctum')->id() ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded-lg px-4 py-2 max-w-sm">
                            <p class="text-sm">{{ $message->content }}</p>
                            <span
                                class="text-xs {{ $message->sender_id === auth('sanctum')->id() ? 'text-blue-100' : 'text-gray-500' }}">
                                {{ $message->created_at->format('h:i A') }}
                                @if (!$message->is_read && $message->sender_id === auth('sanctum')->id())
                                    • Unread
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="sticky  bg-white border-t border-gray-200 p-4">
                <form wire:submit="sendMessage" class="flex gap-2">
                    <input wire:model="message" type="text" placeholder="Type your message..."
                        class="flex-1 border rounded-lg px-3 py-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Send
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center">
                <p class="text-gray-500">Select an order to view the conversation</p>
            </div>
        @endif
    </div>
</div>
