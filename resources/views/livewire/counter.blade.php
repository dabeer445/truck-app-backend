<div>
    <div 
        x-data="{ debugClick() { console.log('Alpine click handler triggered') } }"
        class="p-4 bg-white rounded shadow"
    >
        <h3 class="text-lg font-semibold mb-4">Test Counter (ID: {{ $this->getId() }})</h3>
        
        <div class="text-3xl mb-4">
            Current Count: {{ $count }}
        </div>

        <!-- Try different event binding approaches -->
        <div class="space-y-4">
            <button
                wire:click="increment"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            >
                Method 1: wire:click
            </button>

            <button
                wire:click.prevent="increment"
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600"
            >
                Method 2: wire:click.prevent
            </button>

            <button
                @click="$wire.increment()"
                class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600"
            >
                Method 3: $wire
            </button>

            <button
                x-on:click="$wire.increment()"
                class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600"
            >
                Method 4: x-on:click
            </button>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            <pre class="text-xs">{{ json_encode(['id' => $this->getId(), 'count' => $count], JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>