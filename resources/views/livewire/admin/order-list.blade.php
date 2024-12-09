<div>
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4" x-data="{
        counts: {
            pending: 0,
            in_progress: 0,
            completed: 0,
            cancelled: 0
        },
        stats: @entangle('stats'),
        icons: {
            pending: '<svg class=`h-6 w-6 text-yellow-600` fill=`none` viewBox=`0 0 24 24` stroke-width=`1.5` stroke=`currentColor`><path stroke-linecap=`round` stroke-linejoin=`round` d=`M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z` /></svg>',
            in_progress: '<svg class=`h-6 w-6 text-blue-600` fill=`none` viewBox=`0 0 24 24` stroke-width=`1.5` stroke=`currentColor`><path stroke-linecap=`round` stroke-linejoin=`round` d=`M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z` /></svg>',
            completed: '<svg class=`h-6 w-6 text-green-600` fill=`none` viewBox=`0 0 24 24` stroke-width=`1.5` stroke=`currentColor`><path stroke-linecap=`round` stroke-linejoin=`round` d=`M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z` /></svg>',
            cancelled: '<svg class=`h-6 w-6 text-red-600` fill=`none` viewBox=`0 0 24 24` stroke-width=`1.5` stroke=`currentColor`><path stroke-linecap=`round` stroke-linejoin=`round` d=`M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z` /></svg>'
        }
    }" x-init="$wire.on('statusCountsUpdated', (counts) => {
        console.log('Received counts:', counts);
        $data.counts = counts;
    });">
        <template x-for="status in ['pending', 'in_progress', 'completed', 'cancelled']" :key="status">
            <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
                <dt>
                    <div class="absolute rounded-md bg-opacity-10 p-3"
                        :class="{
                            'bg-yellow-100': status === 'pending',
                            'bg-blue-100': status === 'in_progress',
                            'bg-green-100': status === 'completed',
                            'bg-red-100': status === 'cancelled'
                        }">
                        <div x-html="icons[status]"></div>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500"
                        x-text="status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')">

                    </p>
                </dt>
                <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                    <p class="text-2xl font-semibold text-gray-900">
                        <span x-text="stats[status]"></span> {{-- Use x-text and the Alpine.js stats variable --}}
                    </p>
                    <p class="ml-2 flex items-baseline text-sm font-semibold"
                        :class="{
                            'text-yellow-600': status === 'pending',
                            'text-blue-600': status === 'in_progress',
                            'text-green-600': status === 'completed',
                            'text-red-600': status === 'cancelled'
                        }">
                        Orders
                    </p>
                </dd>
            </div>
        </template>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Date Range Filters -->
            <div>
                <label for="fromDate" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" wire:model.live="fromDate" id="fromDate"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="toDate" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" wire:model.live="toDate" id="toDate"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Items Per Page -->
            <div>
                <label for="perPage" class="block text-sm font-medium text-gray-700">Items Per Page</label>
                <select wire:model.live="perPage" id="perPage"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Locations
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                        At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>From: {{ $order->pickup_location }}</div>
                            <div>To: {{ $order->delivery_location }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <select wire:change="updateOrderStatus({{ $order->id }}, $event.target.value)"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                wire:loading.attr="disabled">
                                <option value="pending" @selected($order->status === 'pending')>Pending</option>
                                <option value="in_progress" @selected($order->status === 'in_progress')>In Progress</option>
                                <option value="completed" @selected($order->status === 'completed')>Completed</option>
                                <option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
                            </select>
                            <span
                                class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('admin.orders.show', $order) }}"
                                class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>
    <!-- Loading States -->
    <div wire:loading.delay class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50">
        <div class="bg-white p-4 rounded-lg shadow-lg">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-2 text-gray-600">Loading...</p>
        </div>
    </div>
</div>
{{-- <div>
    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model.live="statusFilter" id="statusFilter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Date Range Filters -->
            <div>
                <label for="fromDate" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" wire:model.live="fromDate" id="fromDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="toDate" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" wire:model.live="toDate" id="toDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" wire:model.live="search" id="search" placeholder="Search orders..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Debug Info - Remove in production -->
        <div class="p-4 bg-gray-100">
            <p>Status Filter: {{ $statusFilter }}</p>
            <p>From Date: {{ $fromDate }}</p>
            <p>To Date: {{ $toDate }}</p>
            <p>Search: {{ $search }}</p>
        </div> 

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Locations</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-6 py-4">{{ $order->id }}</td>
                        <td class="px-6 py-4">
                            <div>From: {{ $order->pickup_location }}</div>
                            <div>To: {{ $order->delivery_location }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">No orders found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $orders->links() }}
        </div>
    </div>
</div> --}}
