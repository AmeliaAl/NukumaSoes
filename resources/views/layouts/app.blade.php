<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    @vite('resources/js/app.js')
    <!-- Alpine.js (CDN for reliability) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-fixed bg-center relative overflow-hidden">
        <div class="absolute inset-0 bg-red-900/10 pointer-events-none z-0"></div>

        <!-- SIDEBAR (Dynamic Width) -->
        <div :class="sidebarOpen ? 'w-64 lg:w-72 translate-x-0' : 'w-0 -translate-x-full lg:translate-x-0'" class="fixed inset-y-0 left-0 z-30 transition-all duration-300 ease-in-out lg:relative overflow-hidden flex-shrink-0">
            <x-sidebar />
        </div>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative z-10 transition-all duration-300" :class="sidebarOpen ? 'lg:ml-0' : 'lg:ml-0'">
            
            <!-- TOP BAR (Toggle & Profile) -->
            <header class="flex justify-between items-center py-4 px-6 bg-white/80 backdrop-blur-md shadow-sm border-b border-white/20">
                <!-- Left: Sidebar Toggle -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-red-900 hover:bg-red-50 p-2 rounded-lg transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Right: Profile Dropdown (Hover) -->
                <div x-data="{ profileOpen: false }" @mouseenter="profileOpen = true" @mouseleave="profileOpen = false" class="relative">
                    <button class="flex items-center space-x-3 focus:outline-none">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-red-600 font-medium capitalize">{{ Auth::user()->role }}</p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-red-700 to-red-900 flex items-center justify-center text-white font-bold shadow-md border-2 border-white">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="profileOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 border border-gray-100 z-50">
                        
                        <div class="px-4 py-2 border-b border-gray-100 md:hidden">
                            <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-red-600 capitalize">{{ Auth::user()->role }}</p>
                        </div>

                        <!-- Profile Link -->
                        <a href="{{ route('profile.edit') }}" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors flex items-center gap-2 border-b border-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-800 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- SCROLLABLE CONTENT AREA -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto w-full p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
