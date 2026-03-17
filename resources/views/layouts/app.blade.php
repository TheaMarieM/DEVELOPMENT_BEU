<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SPUP-BEU | Professional Behavioral Analytics</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <style>
            * { font-family: 'Inter', sans-serif; }
            .sidebar-item-active { background: rgba(255, 255, 255, 0.1); border-left: 4px solid #facc15; padding-left: 20px; }
            .card-shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08); }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 text-gray-900">

        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 h-screen w-64 bg-green-900 text-white z-50 shadow-xl flex flex-col">
            <div class="p-6 border-b border-green-800">
                <h1 class="text-white text-xl font-black tracking-tight">SPUP-BEU</h1>
                <p class="text-yellow-400 text-xs font-bold tracking-widest mt-0.5">ADMIN PORTAL</p>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'sidebar-item-active' : '' }} flex items-center gap-3 px-4 py-3 text-sm font-medium text-white {{ request()->routeIs('dashboard') ? '' : 'hover:bg-green-800' }} rounded transition-all">
                    <i class="fa-solid fa-chart-line w-5 {{ request()->routeIs('dashboard') ? 'text-yellow-400' : 'opacity-70' }}"></i>Dashboard
                </a>
                <a href="{{ route('incidents.index') }}" class="{{ request()->routeIs('incidents.*') ? 'sidebar-item-active' : '' }} flex items-center gap-3 px-4 py-3 text-sm font-medium text-white {{ request()->routeIs('incidents.*') ? '' : 'hover:bg-green-800' }} rounded transition-all">
                    <i class="fa-solid fa-clipboard-check w-5 {{ request()->routeIs('incidents.*') ? 'text-yellow-400' : 'opacity-70' }}"></i>Incident Records
                </a>
                <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'sidebar-item-active' : '' }} flex items-center gap-3 px-4 py-3 text-sm font-medium text-white {{ request()->routeIs('attendance.*') ? '' : 'hover:bg-green-800' }} rounded transition-all">
                    <i class="fa-solid fa-calendar-check w-5 {{ request()->routeIs('attendance.*') ? 'text-yellow-400' : 'opacity-70' }}"></i>Attendance
                </a>

                <div class="pt-4 pb-2 text-xs font-bold text-yellow-400 uppercase tracking-wider px-4">
                    User Management
                </div>

                <a href="{{ route('advisers.index') }}" class="{{ request()->routeIs('advisers.*') ? 'sidebar-item-active' : '' }} flex items-center gap-3 px-4 py-3 text-sm font-medium text-white {{ request()->routeIs('advisers.*') ? '' : 'hover:bg-green-800' }} rounded transition-all">
                    <i class="fa-solid fa-user-tie w-5 {{ request()->routeIs('advisers.*') ? 'text-yellow-400' : 'opacity-70' }}"></i>Advisers
                </a>
                <a href="{{ route('parents.index') }}" class="{{ request()->routeIs('parents.*') ? 'sidebar-item-active' : '' }} flex items-center gap-3 px-4 py-3 text-sm font-medium text-white {{ request()->routeIs('parents.*') ? '' : 'hover:bg-green-800' }} rounded transition-all">
                    <i class="fa-solid fa-users w-5 {{ request()->routeIs('parents.*') ? 'text-yellow-400' : 'opacity-70' }}"></i>Parents
                </a>
                <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'sidebar-item-active' : '' }} flex items-center gap-3 px-4 py-3 text-sm font-medium text-white {{ request()->routeIs('students.*') ? '' : 'hover:bg-green-800' }} rounded transition-all">
                    <i class="fa-solid fa-user-graduate w-5 {{ request()->routeIs('students.*') ? 'text-yellow-400' : 'opacity-70' }}"></i>Students
                </a>
            </nav>

            <!-- User Info Section -->
            <div class="border-t border-green-800 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white rounded hover:bg-red-700 transition-all w-full">
                        <i class="fa-solid fa-arrow-right-from-bracket w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 min-h-screen">
            @yield('content')
        </main>

        @stack('scripts')
    </body>
</html>
