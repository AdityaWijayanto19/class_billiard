<!DOCTYPE html>
<html lang="id" x-data="themeManager" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dapur - Billiard Class')</title>

    <!-- Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Dynamic Primary Color System for Dapur */
        :root {
            --primary-color: {{ auth()->user()->primary_color ?? '#fa9a08' }};
            --primary-color-rgb: {{ auth()->user()->primary_color === '#fbbf24' ? '251, 191, 36' : (auth()->user()->primary_color === '#2f7d7a' ? '47, 125, 122' : '250, 154, 8') }};
            --primary-hover: {{ auth()->user()->primary_color === '#fbbf24' ? '#d9a61c' : (auth()->user()->primary_color === '#2f7d7a' ? '#1f5350' : '#d97706') }};
            --primary-light: {{ auth()->user()->primary_color === '#fbbf24' ? '#fde8a1' : (auth()->user()->primary_color === '#2f7d7a' ? '#9ec4c1' : '#fed7aa') }};
        }

        .theme-transition {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Standardized Scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #1e1e1e;
        }

        /* Professional Link State - Dynamic Color */
        .active-link {
            background-color: var(--primary-color);
            color: #000 !important;
            transition: background-color 0.2s ease;
        }

        .submenu-active {
            color: var(--primary-color) !important;
            font-weight: 700;
            transition: color 0.2s ease;
        }

        /* Dynamic Focus Rings */
        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 1px var(--primary-color) !important;
        }

        /* Button styles with dynamic colors */
        .btn-primary {
            background-color: var(--primary-color);
            color: #000;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(var(--primary-color-rgb), 0.3);
        }

        .btn-primary:active {
            transform: scale(0.95);
        }

        /* Dynamic border styles */
        .border-primary {
            border-color: var(--primary-color) !important;
        }

        .border-hover-primary:hover {
            border-color: var(--primary-hover) !important;
        }

        /* Dynamic text colors */
        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-hover-primary:hover {
            color: var(--primary-hover) !important;
        }

        /* Dynamic badge styles */
        .badge-primary {
            background-color: var(--primary-color);
            color: #000;
        }

        .badge-primary-light {
            background-color: var(--primary-light);
            color: #000;
        }

        /* Dynamic icon colors */
        .icon-primary {
            color: var(--primary-color);
        }

        .icon-hover-primary:hover {
            color: var(--primary-hover);
        }

        /* No scrollbar utility */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @stack('head')
</head>

<body
    class="font-['Plus_Jakarta_Sans'] antialiased theme-transition bg-white dark:bg-[#050505] text-slate-900 dark:text-slate-200"
    x-data="themeManager()"
    x-init="initTheme()"
    @class="{ 'sidebarCollapsed': sidebarCollapsed, 'sidebarHover': sidebarHover }">

    <!-- SIDEBAR -->
    @include('dapur.partials.sidebar')

    <!-- MAIN CONTENT -->
    <div class="ml-72 theme-transition sidebar-animate" :class="(sidebarCollapsed && !sidebarHover) ? 'md:ml-20' : 'md:ml-72'">
        <!-- HEADER/NAVBAR -->
        @include('dapur.partials.navbar')

        <!-- PAGE CONTENT -->
        <main class="min-h-screen p-8 md:p-12">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
