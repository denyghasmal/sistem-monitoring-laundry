<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LaundryHub') - Rancang Bangun Aplikasi Laundry</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts & Material Icons -->
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

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand Logo & Title -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-sky-400 flex items-center justify-center text-white shadow-md shadow-brand-500/20">
                        <span class="material-symbols-outlined text-2xl">local_laundry_service</span>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-lg text-slate-900 tracking-tight">LaundryHub</span>
                            <span class="bg-brand-50 text-brand-700 text-xs px-2 py-0.5 rounded-full font-semibold border border-brand-200">v1.0</span>
                        </div>
                        <p class="text-xs text-slate-500 hidden sm:block">Sistem Pemesanan & Monitoring Real-time</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex items-center space-x-1 sm:space-x-2">
                    <a href="{{ route('customer.index') }}" 
                       class="px-3 py-2 rounded-lg text-sm font-semibold transition text-brand-700 bg-brand-50 hover:bg-brand-100 flex items-center">
                        <span class="material-symbols-outlined text-lg mr-1 align-sub">storefront</span>
                        Web Pelanggan
                    </a>

                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="material-symbols-outlined text-lg mr-1 align-sub">dashboard</span>
                        Dashboard Admin
                    </a>

                    <a href="{{ route('mobile.simulator') }}" 
                       class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('mobile.simulator') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="material-symbols-outlined text-lg mr-1 align-sub">smartphone</span>
                        Simulator Mobile
                    </a>

                    <a href="{{ route('api.docs') }}" 
                       class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('api.docs') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="material-symbols-outlined text-lg mr-1 align-sub">api</span>
                        API Explorer
                    </a>
                </nav>

                <!-- Status Badge -->
                <div class="hidden md:flex items-center space-x-2">
                    <div class="flex items-center bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                        Backend API Live
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center justify-between text-emerald-800">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-emerald-600 mr-2">check_circle</span>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg shadow-sm flex items-center justify-between text-rose-800">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-rose-600 mr-2">error</span>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <p>Aplikasi Pemesanan dan Monitoring Layanan Laundry Berbasis Mobile (Flutter & Laravel)</p>
        <p class="mt-1">Dibuat berdasarkan Blueprint & Dokumen Analisis Jurnal Sistem Informasi</p>
    </footer>

    @yield('scripts')
</body>
</html>

