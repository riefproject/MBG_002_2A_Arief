<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen">
    @php
        $desktopNavBase = 'nav-link inline-flex items-center px-4 py-2 text-sm font-medium transition-all duration-200 rounded-md border-b-2 border-transparent hover:border-blue-500';
        $desktopNavIdle = 'text-gray-600 hover:text-gray-900 hover:bg-gray-50';
        $desktopNavActive = 'text-blue-600 border-blue-500 bg-blue-50';
        $mobileNavBase = 'mobile-nav-link flex items-center pl-3 pr-4 py-3 text-base font-medium transition-colors duration-200';
        $mobileNavIdle = 'text-gray-600 hover:text-gray-900 hover:bg-gray-50';
        $mobileNavActive = 'text-blue-600 bg-blue-50 border-r-4 border-blue-600';
        $isGudang = auth()->check() && auth()->user()->role === 'gudang';
        $isDapur = auth()->check() && auth()->user()->role === 'dapur';
    @endphp
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Main Navigation -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors duration-200">
                            {{ config('app.name') }}
                        </a>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-1">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                    class="{{ $desktopNavBase }} {{ !empty($navActive['dashboard'] ?? null) ? $desktopNavActive : $desktopNavIdle }}">
                                <x-heroicon-o-home class="w-4 h-4 mr-2" />
                                Dashboard
                            </a>
                        @endauth
                    </div>
                    @if($isDapur)
                        <div class="hidden sm:ml-4 sm:flex sm:space-x-1">
                            <a href="{{ route('user.permintaan.index') }}"
                                class="{{ $desktopNavBase }} {{ !empty($navActive['user_permintaan'] ?? null) ? $desktopNavActive : $desktopNavIdle }}">
                                <x-heroicon-o-inbox-arrow-down class="w-4 h-4 mr-2" />
                                Permintaan Bahan
                            </a>
                        </div>
                    @endif
                    @if($isGudang)
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-1">
                            <a href="{{ route('admin.bahan_baku') }}"
                                class="{{ $desktopNavBase }} {{ !empty($navActive['admin_bahan_baku'] ?? null) ? $desktopNavActive : $desktopNavIdle }}">
                                <x-heroicon-o-cube class="w-4 h-4 mr-2" />
                                Management Bahan Baku
                            </a>

                            <a href="{{ route('admin.permintaan.index') }}"
                                class="{{ $desktopNavBase }} {{ !empty($navActive['admin_permintaan'] ?? null) ? $desktopNavActive : $desktopNavIdle }}">
                                <x-heroicon-o-inbox-arrow-down class="w-4 h-4 mr-2" />
                                Proses Permintaan
                            </a>
                        </div>
                    @endif
                </div>

                <!-- User Menu & Mobile Menu Button -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                    @auth
                        <!-- Profile Link -->
                                <a href="{{ route('profile') }}"
                                    class="{{ $desktopNavBase }} {{ !empty($navActive['profile'] ?? null) ? $desktopNavActive : $desktopNavIdle }}">
                            <x-heroicon-o-user class="w-4 h-4 mr-2" />
                            Profile
                        </a>
                        
                        <!-- User Dropdown -->
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 hover:ring-2 hover:ring-blue-300 transition-all duration-200">
                                <span class="sr-only">Buka menu user</span>
                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center shadow-md">
                                    <span class="text-sm font-medium text-white">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <x-heroicon-o-chevron-down class="ml-2 h-4 w-4 text-gray-600" />
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-100">
                                
                                 <div class="py-1">
                                    <div class="px-4 py-3 text-sm text-gray-700 border-b border-gray-100 bg-gray-50">
                                        <div class="font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ Auth::user()->email }}</div>
                                        <div class="text-xs text-blue-600 mt-1 capitalize">{{ Auth::user()->role }}</div>
                                    </div>
                                    
                                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                        @csrf
                                        <button type="submit" 
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200">
                                            <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 mr-3" />
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors duration-200 shadow-md hover:shadow-lg">
                            <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4 mr-2" />
                            Login
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="sm:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <span class="sr-only">Buka menu utama</span>
                        <x-heroicon-o-bars-3 class="h-6 w-6" x-show="!mobileMenuOpen" />
                        <x-heroicon-o-x-mark class="h-6 w-6" x-show="mobileMenuOpen" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                @auth
                          <a href="{{ route('dashboard') }}"
                              class="{{ $mobileNavBase }} {{ !empty($navActive['dashboard'] ?? null) ? $mobileNavActive : $mobileNavIdle }}">
                        <x-heroicon-o-home class="w-5 h-5 mr-3" />
                        Dashboard
                    </a>
                    
                    <a href="{{ route('profile') }}"
                       class="{{ $mobileNavBase }} {{ !empty($navActive['profile'] ?? null) ? $mobileNavActive : $mobileNavIdle }}">
                        <x-heroicon-o-user class="w-5 h-5 mr-3" />
                        Profile
                    </a>

                    @if($isGudang)
                        <a href="{{ route('admin.permintaan.index') }}"
                           class="{{ $mobileNavBase }} {{ !empty($navActive['admin_permintaan'] ?? null) ? $mobileNavActive : $mobileNavIdle }}">
                            <x-heroicon-o-inbox-arrow-down class="w-5 h-5 mr-3" />
                            Proses Permintaan
                        </a>
                    @endif
                    @if($isDapur)
                        <a href="{{ route('user.permintaan.index') }}"
                           class="{{ $mobileNavBase }} {{ !empty($navActive['user_permintaan'] ?? null) ? $mobileNavActive : $mobileNavIdle }}">
                            <x-heroicon-o-inbox-arrow-down class="w-5 h-5 mr-3" />
                            Permintaan Bahan
                        </a>
                    @endif
                @endauth
            </div>
            
            @auth
                <div class="pt-4 pb-3 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center shadow-md">
                                <span class="text-sm font-medium text-white" id="mobile-avatar-initial">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-900" id="mobile-user-name">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-600" id="mobile-user-email">{{ Auth::user()->email }}</div>
                            <div class="text-xs text-blue-600 mt-1 capitalize">{{ Auth::user()->role }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center w-full text-left px-4 py-2 text-base font-medium text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors duration-200">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 mr-3" />
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="pt-4 pb-3 border-t border-gray-200 bg-gray-50">
                    <a href="{{ route('login') }}" 
                       class="flex items-center px-4 py-2 text-base font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md mx-4 transition-colors duration-200 shadow-md">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 mr-3" />
                        Login
                    </a>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Notification Container -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Scripts untuk JavaScript Native -->
    @stack('scripts')
</body>
</html>
