<div class="relative inline-block text-left">
    <div>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColors[$currentStatus] }}">
            {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
        </span>
        <button type="button" 
                class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                x-data=""
                x-on:click="$dispatch('open-status-modal')">
            Change Status
        </button>
    </div>

    <!-- Status Modal -->
    <div x-data="{ open: false }"
         x-show="open"
         x-on:open-status-modal.window="open = true"
         x-on:keydown.escape.window="open = false"
         @click.away="open = false"
         class="fixed z-10 inset-0 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                 x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Modal content -->
                <div>
                    <!-- Close button -->
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" 
                                @click="open = false"
                                class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Update Order Status
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Select a new status for order #{{ $order->id }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status options -->
                    <div class="mt-5 sm:mt-4">
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($statuses as $status => $label)
                                <button type="button"
                                        wire:click="updateStatus('{{ $status }}')"
                                        x-on:click="open = false"
                                        @class([
                                            'inline-flex justify-between items-center px-4 py-3 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                                            'border-green-500 bg-green-50 text-green-800' => $status === 'completed',
                                            'border-yellow-500 bg-yellow-50 text-yellow-800' => $status === 'pending',
                                            'border-blue-500 bg-blue-50 text-blue-800' => $status === 'in_progress',
                                            'border-red-500 bg-red-50 text-red-800' => $status === 'cancelled',
                                            'opacity-50 cursor-not-allowed' => $status === $currentStatus,
                                        ])
                                        @if($status === $currentStatus) disabled @endif>
                                    <span>{{ $label }}</span>
                                    @if($status === $currentStatus)
                                        <span class="ml-2">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="mt-5 sm:mt-6">
                        <button type="button"
                                class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
                                @click="open = false">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div wire:loading wire:target="updateStatus" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        <div class="relative bg-white rounded-lg px-4 py-3 flex items-center shadow-xl">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Updating status...</span>
        </div>
    </div>
</div>