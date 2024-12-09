<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Remove Alpine CDN since we're importing it via ESM -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>

<body class="font-sans antialiased">
    
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div x-data="{ open: false }" @keydown.window.escape="open = false">
            <!-- Off-canvas menu for mobile -->
            <div x-show="open" class="relative z-40 md:hidden"
                x-description="Off-canvas menu for mobile, show/hide based on off-canvas menu state." x-ref="dialog"
                aria-modal="true">
                <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-linear duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>

                <div class="fixed inset-0 z-40 flex">
                    <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform"
                        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in-out duration-300 transform"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                        class="relative flex w-full max-w-xs flex-1 flex-col bg-white">
                        <!-- Close button -->
                        <div class="absolute top-0 right-0 -mr-12 pt-2">
                            <button type="button"
                                class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                @click="open = false">
                                <span class="sr-only">Close sidebar</span>
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Sidebar content -->
                        <div class="h-0 flex-1 overflow-y-auto pt-5 pb-4">
                            <div class="flex flex-shrink-0 items-center px-4">
                                <span class="text-xl font-bold">{{ config('app.name') }} Admin</span>
                            </div>
                            <nav class="mt-5 space-y-1 px-2">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="@if (request()->routeIs('admin.dashboard')) bg-gray-100 text-gray-900 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                    <svg class="mr-4 h-6 w-6 @if (request()->routeIs('admin.dashboard')) text-gray-500 @else text-gray-400 group-hover:text-gray-500 @endif"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                    Dashboard
                                </a>

                                <a href="{{ route('admin.orders') }}"
                                    class="@if (request()->routeIs('admin.orders*')) bg-gray-100 text-gray-900 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                    <svg class="mr-4 h-6 w-6 @if (request()->routeIs('admin.orders*')) text-gray-500 @else text-gray-400 group-hover:text-gray-500 @endif"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                    </svg>
                                    Orders
                                </a>

                                <a href="{{ route('admin.chat') }}"
                                    class="@if (request()->routeIs('admin.chat')) bg-gray-100 text-gray-900 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                    <svg class="mr-4 h-6 w-6 @if (request()->routeIs('admin.chat')) text-gray-500 @else text-gray-400 group-hover:text-gray-500 @endif"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                    </svg>
                                    Chat
                                </a>

                                <a href="{{ route('admin.notifications') }}"
                                    class="@if (request()->routeIs('admin.notifications')) bg-gray-100 text-gray-900 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                    <svg class="mr-4 h-6 w-6 @if (request()->routeIs('admin.notifications')) text-gray-500 @else text-gray-400 group-hover:text-gray-500 @endif"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                    Notifications
                                </a>
                            </nav>
                        </div>

                        <!-- User profile -->
                        <div class="flex flex-shrink-0 border-t border-gray-200 p-4">
                            <div class="group block w-full flex-shrink-0">
                                <div class="flex items-center">
                                    <div>
                                        <img class="inline-block h-9 w-9 rounded-full"
                                            src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}"
                                            alt="">
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                            {{ auth()->user()->name }}</p>
                                        <form method="POST" action="{{ route('logout') }}" x-data>
                                            @csrf
                                            <button type="submit" @click.prevent="$root.submit();"
                                                class="text-xs font-medium text-gray-500 group-hover:text-gray-700">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Static sidebar for desktop -->
            <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
                <div class="flex min-h-0 flex-1 flex-col border-r border-gray-200 bg-white">
                    <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
                        <div class="flex flex-shrink-0 items-center px-4">
                            <span class="text-xl font-bold">{{ config('app.name') }} Admin</span>
                        </div>
                        <nav class="mt-5 flex-1 space-y-1 bg-white px-2">
                            <!-- Same navigation items as mobile, just with different styling -->
                            <a href="{{ route('admin.dashboard') }}"
                                class="@if (request()->routeIs('admin.dashboard')) bg-gray-100 text-gray-900 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <svg class="mr-4 h-6 w-6 @if (request()->routeIs('admin.dashboard')) text-gray-500 @else text-gray-400 group-hover:text-gray-500 @endif"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                                Dashboard
                            </a>

                            <a href="{{ route('admin.orders') }}"
                                class="@if (request()->routeIs('admin.orders*')) bg-gray-100 text-gray-900 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <svg class="mr-4 h-6 w-6 @if (request()->routeIs('admin.orders*')) text-gray-500 @else text-gray-400 group-hover:text-gray-500 @endif"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                </svg>
                                Orders
                            </a>

                            <a href="{{ route('admin.chat') }}"
                                class="@if (request()->routeIs('admin.chat')) bg-gray-100 text-gray-900 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <svg class="mr-4 h-6 w-6 @if (request()->routeIs('admin.chat')) text-gray-500 @else text-gray-400 group-hover:text-gray-500 @endif"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                                Chat
                            </a>

                            <a href="{{ route('admin.notifications') }}"
                                class="@if (request()->routeIs('admin.notifications')) bg-gray-100 text-gray-900 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <svg class="mr-4 h-6 w-6 @if (request()->routeIs('admin.notifications')) text-gray-500 @else text-gray-400 group-hover:text-gray-500 @endif"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                Notifications
                            </a>
                        </nav>
                    </div>
                    <div class="flex flex-shrink-0 border-t border-gray-200 p-4">
                        <!-- Same user profile as mobile -->
                        <div class="group block w-full flex-shrink-0">
                            <div class="flex items-center">
                                <div>
                                    <img class="inline-block h-9 w-9 rounded-full"
                                        src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}"
                                        alt="">
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                        {{ auth()->user()->name }}</p>
                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <button type="submit" @click.prevent="$root.submit();"
                                            class="text-xs font-medium text-gray-500 group-hover:text-gray-700">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="flex flex-1 flex-col md:pl-64">
                <!-- Top header -->
                <div class="sticky top-0 z-10 bg-white pl-1 pt-1 sm:pl-3 sm:pt-3 md:hidden">
                    <button type="button"
                        class="-ml-0.5 -mt-0.5 inline-flex h-12 w-12 items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        @click="open = true">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>

                <!-- Main content area -->
                <main class="flex">
                    <div class="py-6 w-full">
                        {{ $slot }}
                    </div>
                </main>
            </div>

        </div>
    </div>
    @livewireScripts
    <script>
        document.addEventListener('livewire:initialized', () => {
            console.log('Livewire initialized');
        });
        
        document.addEventListener('alpine:initialized', () => {
            console.log('Alpine initialized');
        });
    </script>
</body>

</html>
