<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.orders') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Orders
                </a>
            </div>

            <!-- Order Header -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Order #{{ $order->id }}</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Created {{ $order->created_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        <livewire:admin.order-status :order="$order" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Order Details -->
                <div class="md:col-span-2">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Order Details</h3>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Pickup Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $order->pickup_location }}</dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Delivery Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $order->delivery_location }}</dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Pickup Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ Carbon\Carbon::parse($order->pickup_time)->format('M d, Y h:i A') }}
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Delivery Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ Carbon\Carbon::parse($order->delivery_time)->format('M d, Y h:i A') }}
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Cargo Details</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <div>Weight: {{ $order->cargo_details['weight'] ?? 'N/A' }}</div>
                                        @if (isset($order->cargo_details['dimensions']))
                                            <div>Dimensions:
                                                {{ $order->cargo_details['dimensions']['length'] ?? 0 }} ×
                                                {{ $order->cargo_details['dimensions']['width'] ?? 0 }} ×
                                                {{ $order->cargo_details['dimensions']['height'] ?? 0 }}
                                            </div>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    <!-- Messages Section -->
                    <div class="mt-6">
                        <livewire:admin.order-messages :order="$order" />
                    </div>
                </div>

                <!-- Customer Details & Communication -->
                <div class="md:col-span-1">
                    <!-- Customer Info -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Customer Information</h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5">
                            <div class="mt-1 text-sm text-gray-900">
                                <div class="mb-3">
                                    <div class="font-medium">Name</div>
                                    <div>{{ $order->user->name }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="font-medium">Email</div>
                                    <div>{{ $order->user->email }}</div>
                                </div>
                                <div>
                                    <div class="font-medium">Phone</div>
                                    <div>{{ $order->user->phone }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Communication Quick Actions -->
                    <div class="mt-6">
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Quick Communication
                                </h3>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- SMS Template Button -->
                                    <button type="button" wire:click="sendSmsTemplate"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-3 3-3-3z" />
                                        </svg>
                                        Send Status Update (SMS)
                                    </button>

                                    <!-- Email Template Button -->
                                    <button type="button" wire:click="sendEmailTemplate"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Send Status Update (Email)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</x-admin-layout>
