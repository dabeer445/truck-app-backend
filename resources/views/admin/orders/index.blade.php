<x-admin-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Orders Management</h1>
            </div>
            {{-- <livewire:counter /> --}}
            <!-- Livewire Component -->
            <livewire:admin.order-list />
        </div>
    </div>

    <!-- Notification -->
    <div x-data="{
        show: false,
        message: '',
        type: 'success',
        timer: null
    }"
        @shownotification.window="
            show = true;
            message = $event.detail.message;
            type = $event.detail.type;
            if (timer) clearTimeout(timer);
            timer = setTimeout(() => { show = false }, 3000);
        "
        x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2" class="fixed bottom-4 right-4 z-50"
        @click="show = false">
        <div class="rounded-lg p-4 shadow-lg flex items-center space-x-3"
            :class="{
                'bg-green-500': type === 'success',
                'bg-red-500': type === 'error'
            }">
            <div class="flex-shrink-0">
                <template x-if="type === 'success'">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </template>
            </div>
            <p class="text-white" x-text="message"></p>
        </div>
    </div>
</x-admin-layout>
