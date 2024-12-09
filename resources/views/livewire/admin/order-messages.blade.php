<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Order Messages</h3>
        <span class="text-sm text-gray-500">
            {{ $messages->count() }} {{ Str::plural('message', $messages->count()) }}
        </span>
    </div>
    
    <!-- Messages List -->
    <div class="border-t border-gray-200">
        <div id="messages-container" class="h-[32rem] overflow-y-auto p-4 space-y-6 flex flex-col-reverse">
            @forelse($messages as $message)
                <div @class([
                    'flex group',
                    'justify-end' => $message->sender_id === auth()->id(),
                    'justify-start' => $message->sender_id !== auth()->id(),
                ])>
                    <div class="max-w-[70%]">
                        <div @class([
                            'rounded-lg px-4 py-2',
                            'bg-indigo-100' => $message->sender_id === auth()->id(),
                            'bg-gray-100' => $message->sender_id !== auth()->id(),
                        ])>
                            <div class="text-sm flex items-center justify-between">
                                <span class="font-medium">
                                    {{ $message->sender_id === auth()->id() ? 'You' : $message->sender->name }}
                                </span>
                                <span class="text-xs text-gray-500 ml-2">
                                    {{ $message->created_at->format('M d, h:i A') }}
                                </span>
                            </div>
                            <div class="mt-1">
                                {{ $message->content }}
                            </div>
                        </div>
                        
                        <!-- Read Status - Now positioned below with proper spacing -->
                        @if($message->sender_id === auth()->id())
                            <div class="text-right mt-1">
                                <span class="text-xs text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    @if($message->is_read)
                                        Read {{ $message->read_at->format('M d, h:i A') }}
                                    @else
                                        Delivered
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    No messages yet
                </div>
            @endforelse
        </div>

        <!-- Message Input -->
        <div class="border-t border-gray-200 p-4 bg-gray-50">
            <form wire:submit="sendMessage" class="flex space-x-3">
                <div class="flex-grow">
                    <textarea
                        wire:model="newMessage"
                        rows="1"
                        class="shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md"
                        placeholder="Type your message..."
                    ></textarea>
                    @error('newMessage') 
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                    @enderror
                </div>
                <div class="flex items-center">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Send</span>
                        <span wire:loading>
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Auto-scroll script -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const container = document.getElementById('messages-container');
            container.scrollTop = container.scrollHeight;

            Livewire.on('refresh-messages', () => {
                container.scrollTop = container.scrollHeight;
            });
        });
    </script>
</div>