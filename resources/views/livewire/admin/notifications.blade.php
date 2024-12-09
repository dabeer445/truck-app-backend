<div class="py-6 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-semibold">Notifications</h2>
            @if($notifications->where('read_at', null)->count() > 0)
                <button wire:click="markAllAsRead" class="text-sm text-blue-600 hover:text-blue-800">
                    Mark all as read
                </button>
            @endif
        </div>
 
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <div class="p-4 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            @if($notification->data['type'] === 'new_order')
                                <p class="text-sm">
                                    New order #{{ $notification->data['order_id'] }} received
                                </p>
                                <p class="mt-1 text-sm text-gray-600">
                                    Pickup: {{ $notification->data['extra_data']['pickup_location'] }}<br>
                                    Delivery: {{ $notification->data['extra_data']['delivery_location'] }}
                                </p>
                            @else
                                <p class="text-sm">
                                    Message from {{ \App\Models\User::find($notification->data['sender_id'])->name ?? 'User' }}
                                    for Order #{{ $notification->data['order_id'] }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600">{{ $notification->data['message'] }}</p>
                            @endif
                            <p class="mt-1 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        
                        @unless($notification->read_at)
                            <button 
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="ml-4 text-sm text-blue-600 hover:text-blue-800"
                            >
                                Mark as read
                            </button>
                        @endunless
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    No notifications found
                </div>
            @endforelse
        </div>
 
        @if($notifications->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
 </div>