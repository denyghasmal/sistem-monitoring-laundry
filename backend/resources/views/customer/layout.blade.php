<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LaundryHub') - Layanan Laundry & Real-time Monitoring</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col antialiased">

    <!-- Top Navigation for Customers -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <!-- Logo -->
                <a href="{{ route('customer.index') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-sky-400 flex items-center justify-center text-white shadow-md shadow-brand-500/20">
                        <span class="material-symbols-outlined text-2xl">local_laundry_service</span>
                    </div>
                    <div>
                        <span class="font-extrabold text-lg text-slate-900 tracking-tight">LaundryHub</span>
                        <span class="text-[10px] bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full font-bold ml-1 border border-brand-200">Customer Web Portal</span>
                    </div>
                </a>

                <!-- Nav Menu -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('customer.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('customer.index') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Beranda & Layanan
                    </a>
                    <a href="{{ route('customer.order') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('customer.order') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Pesan Laundry
                    </a>
                    <a href="{{ route('customer.track') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('customer.track') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Lacak Cucian
                    </a>
                    <a href="{{ route('customer.history') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('customer.history') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Riwayat
                    </a>
                </nav>

                <!-- Action Button & Switch View -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 font-semibold text-slate-700 transition flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1">admin_panel_settings</span>
                        Panel Admin
                    </a>
                    <a href="{{ route('mobile.simulator') }}" 
                       class="text-xs px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold transition flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1">smartphone</span>
                        Mode HP
                    </a>
                </div>

            </div>
        </div>
    </header>

    <!-- Flash Notification -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-xs text-emerald-800 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-emerald-600 mr-2">check_circle</span>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-xs text-rose-800 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-rose-600 mr-2">error</span>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <p class="font-medium text-slate-700">LaundryHub - Portal Pelanggan Layanan Antar Jemput & Monitoring Laundry</p>
        <p class="mt-1">Terintegrasi langsung dengan Database & Backend RESTful API Laravel</p>
    </footer>

    @yield('scripts')
</body>
</html>

