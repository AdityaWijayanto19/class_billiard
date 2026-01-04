<!DOCTYPE html>
<html lang="id" x-data="themeManager" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Billiard Class')</title>

    <!-- Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Dynamic Primary Color System */
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

        /* Sidebar Expansion Animation */
        .sidebar-animate {
            transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.35s ease;
        }

        /* Dynamic border hover states */
        .border-hover-primary:hover {
            border-color: var(--primary-color) !important;
        }

        /* Dynamic text hover states */
        .text-hover-primary:hover {
            color: var(--primary-color) !important;
        }

        /* Background with opacity */
        .bg-primary-light {
            background-color: rgba(var(--primary-color-rgb), 0.1);
        }

        .bg-primary-lighter {
            background-color: rgba(var(--primary-color-rgb), 0.05);
        }

        /* Border with opacity */
        .border-primary-light {
            border-color: rgba(var(--primary-color-rgb), 0.2);
        }
    </style>
</head>

<body
    class="font-['Plus_Jakarta_Sans'] antialiased theme-transition bg-white dark:bg-[#050505] text-slate-900 dark:text-slate-200"
    x-data="{ sidebarCollapsed: false, sidebarHover: false }">

    <!-- SIDEBAR -->
    <aside @mouseenter="if(sidebarCollapsed) sidebarHover = true" @mouseleave="sidebarHover = false"
        class="fixed top-0 left-0 h-screen z-50 theme-transition border-r border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-[#0A0A0A] flex flex-col sidebar-animate"
        :class="[(sidebarCollapsed && !sidebarHover) ? 'w-20' : 'w-72', (sidebarCollapsed && sidebarHover) ? 'shadow-[20px_0_50px_rgba(0,0,0,0.2)] dark:shadow-[20px_0_50px_rgba(0,0,0,0.5)]' : '']">
        <!-- Logo Area -->
        <div class="h-20 flex items-center px-6 shrink-0 border-b border-slate-200 dark:border-white/5 overflow-hidden">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--primary-color);">
                    <i class="ri-shield-star-fill text-black text-lg"></i>
                </div>
                <div x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity.duration.300ms
                    class="flex flex-col whitespace-nowrap">
                    <span class="font-bold text-sm tracking-tight dark:text-white uppercase leading-none">Billiard
                        Admin</span>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">Core System</span>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-8 no-scrollbar">
            <!-- Group: Main -->
            <div>
                <p x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                    class="text-[10px] font-bold text-slate-400 dark:text-gray-600 uppercase tracking-widest px-4 mb-4">
                    Main</p>
                <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.dashboard') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-dashboard-2-line text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Dashboard Overview</span>
            </a>
                </div>
            </div>

            <!-- Group: Website CMS - Only if user has admin role (super_admin or admin) -->
            @if(auth()->user()->hasAnyRole(['super_admin', 'admin']))
            <div
                x-data="{ open: {{ request()->routeIs('admin.cms.hero', 'admin.cms.tentang-kami', 'admin.cms.about-founder', 'admin.cms.keunggulan-fasilitas', 'admin.cms.portfolio-achievement', 'admin.cms.tim-kami', 'admin.cms.testimoni-pelanggan', 'admin.cms.event', 'admin.cms.footer', 'admin.cms.contact') ? 'true' : 'false' }} }">
                <p x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                    class="text-[10px] font-bold text-slate-400 dark:text-gray-600 uppercase tracking-widest px-4 mb-4">
                    Content</p>

                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-slate-200/50 dark:hover:bg-white/5 transition-all text-slate-600 dark:text-slate-400 group">
                    <div class="flex items-center gap-4">
                        <i class="ri-stack-line text-lg" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Website CMS</span>
                    </div>
                    <i x-show="!sidebarCollapsed || sidebarHover"
                        class="ri-arrow-down-s-line text-xs transition-transform duration-300"
                        :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open && (!sidebarCollapsed || sidebarHover)" x-collapse
                    class="pl-12 space-y-1 mt-2 border-l border-slate-200 dark:border-white/5 ml-6">
                    @php
                        $cmsLinks = [
                            ['r' => 'admin.cms.hero', 'l' => 'Hero Section'],
                            ['r' => 'admin.cms.tentang-kami', 'l' => 'Tentang Kami'],
                            ['r' => 'admin.cms.about-founder', 'l' => 'About Founder'],
                            ['r' => 'admin.cms.keunggulan-fasilitas', 'l' => 'Keunggulan'],
                            ['r' => 'admin.cms.portfolio-achievement', 'l' => 'Portfolio'],
                            ['r' => 'admin.cms.tim-kami', 'l' => 'Tim Kami'],
                            ['r' => 'admin.cms.testimoni-pelanggan', 'l' => 'Testimoni'],
                            ['r' => 'admin.cms.event', 'l' => 'Event'],
                            ['r' => 'admin.cms.footer', 'l' => 'Footer'],
                            ['r' => 'admin.cms.contact', 'l' => 'Contact'],
                        ];
                    @endphp
                    @foreach($cmsLinks as $m)
                        <a href="{{ route($m['r']) }}"
                            class="block py-1.5 text-[11px] font-bold transition-all {{ request()->routeIs($m['r']) ? 'submenu-active' : 'text-slate-500 dark:text-gray-500' }}"
                            @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                            {{ $m['l'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Group: Management -->
            <div>
                <p x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                    class="text-[10px] font-bold text-slate-400 dark:text-gray-600 uppercase tracking-widest px-4 mb-4">
                    Management</p>
                <div class="space-y-1">
                    {{-- User Management: Check permission user.view --}}
                    @if(auth()->user()->hasPermissionTo('user.view'))
                    <a href="{{ route('admin.manage-users.index') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.manage-users.*') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-user-settings-line text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Manage Users</span>
                    </a>
                    @endif

                    {{-- Permissions Management: Check permission role.view --}}
                    @if(auth()->user()->hasPermissionTo('role.view'))
                    <a href="{{ route('admin.permissions.select-user') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.permissions.*') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-shield-check-line text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Role Permissions</span>
                    </a>
                    @endif

                    {{-- Orders: Check permission order.view --}}
                    @if(auth()->user()->hasPermissionTo('order.view'))
                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.orders.*') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-shopping-cart-line text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Orders</span>
                    </a>
                    @endif

                    {{-- Categories: Check permission category.view --}}
                    @if(auth()->user()->hasPermissionTo('category.view'))
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.categories.*') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-price-tag-3-line text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Categories Menu</span>
                    </a>
                    @endif

                    {{-- Menus: Check permission menu.view --}}
                    @if(auth()->user()->hasPermissionTo('menu.view'))
                    <a href="{{ route('admin.menus.index') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.menus.*') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-restaurant-line text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Manage Menu</span>
                    </a>
                    @endif

                    {{-- Tables/Barcode: Check permission table.view --}}
                    @if(auth()->user()->hasPermissionTo('table.view'))
                    <a href="{{ route('admin.tables.index') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.tables.*') || request()->routeIs('admin.barcode.*') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-qr-code-line text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Manage Table & Barcode</span>
                    </a>
                    @endif

                    {{-- Sales Analytics: Check permission report.view --}}
                    @if(auth()->user()->hasPermissionTo('report.view'))
                    <a href="{{ route('admin.sales-analytics') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.sales-analytics') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-bar-chart-line text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Sales Analytics</span>
                    </a>
                    @endif

                    {{-- Food Inventory: Check permission inventory.view --}}
                    @if(auth()->user()->hasPermissionTo('inventory.view'))
                    <a href="{{ route('admin.inventory.index') }}"
                        class="flex items-center gap-4 px-4 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.inventory.*') ? 'active-link' : 'hover:bg-slate-200/50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                        <i class="ri-stack-fill text-lg"></i>
                        <span x-show="!sidebarCollapsed || sidebarHover" x-transition.opacity
                            class="font-bold text-xs tracking-tight whitespace-nowrap">Food Stock</span>
                    </a>
                    @endif
                </div>
            </div>
        </nav>

        <!-- Sidebar Toggle Bottom -->
        <div class="p-4 border-t border-slate-200 dark:border-white/5">
            <button @click="sidebarCollapsed = !sidebarCollapsed; sidebarHover = false"
                class="w-full h-9 flex items-center justify-center rounded-md bg-slate-200 dark:bg-white/5 transition-all group" style="color: inherit;" @mouseenter="$el.style.backgroundColor = 'var(--primary-color)'; $el.style.color = '#000';" @mouseleave="$el.style.backgroundColor = ''; $el.style.color = '';">
                <i :class="sidebarCollapsed ? 'ri-arrow-right-s-line' : 'ri-arrow-left-s-line'" class="text-sm"></i>
            </button>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="min-h-screen flex flex-col transition-all duration-300" :class="sidebarCollapsed ? 'ml-20' : 'ml-72'">

        <!-- HEADER -->
        <header
            class="h-16 px-8 flex items-center justify-between sticky top-0 z-40 bg-white/80 dark:bg-[#050505]/80 backdrop-blur-md border-b border-slate-200 dark:border-white/5">
            <div>
                <h1 class="text-sm font-bold dark:text-white tracking-tight">@yield('title', 'Dashboard Overview')</h1>
            </div>

            <div class="flex items-center gap-4">
                <!-- Theme Switcher -->
                <button @click="toggleTheme"
                    class="w-8 h-8 rounded-md border border-slate-200 dark:border-white/10 flex items-center justify-center transition-all" @mouseenter="$el.style.borderColor = 'var(--primary-color)'" @mouseleave="$el.style.borderColor = ''">
                    <i x-show="!darkMode" class="ri-moon-line text-sm"></i>
                    <i x-show="darkMode" class="ri-sun-line text-sm" x-cloak style="color: var(--primary-color);"></i>
                </button>

                <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2.5 group">
                        <img class="w-8 h-8 rounded-md object-cover border border-slate-200 dark:border-white/10"
                            src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()?->name) }}&background={{ str_replace('#', '', auth()->user()->primary_color ?? '#fa9a08') }}&color=000&bold=true"
                            alt="">
                        <div class="text-left hidden md:block">
                            <p
                                class="text-[11px] font-bold dark:text-white leading-none transition-colors"
                                @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                                {{ Auth::user()?->name }}</p>
                        </div>
                </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-3 w-52 bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/10 rounded-lg shadow-xl p-1 z-50">
                    <a href="{{ route('admin.profile.edit') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-md text-xs font-bold hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                            <i class="ri-user-line text-sm" style="color: var(--primary-color);"></i> Edit Profile
                    </a>
                        <button @click="handleLogout"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-xs font-bold text-red-500 hover:bg-red-500/5 transition-all text-left">
                            <i class="ri-logout-box-r-line text-sm"></i> Sign Out
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-8 md:p-12">
            <div class="max-w-[1600px] mx-auto">
                <!-- ALERTS -->
                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show"
                        class="mb-6 p-6 bg-red-500/10 border border-red-500/30 rounded-lg flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
                        <i class="ri-alert-fill text-red-500 text-2xl shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-red-600 dark:text-red-400 mb-3">{{ session('error') }}</p>
                            <div class="flex gap-2">
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-md transition-all">
                                        <i class="ri-logout-box-r-line mr-1"></i> Logout Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                        <button @click="show = false" class="text-red-500 hover:text-red-700 shrink-0">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="mb-6 p-4 bg-amber-500/10 border border-amber-500/30 rounded-lg flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
                        <i class="ri-alert-line text-amber-500 text-xl shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ session('warning') }}</p>
                        </div>
                        <button @click="show = false" class="text-amber-500 hover:text-amber-700 shrink-0">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                        class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-lg flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
                        <i class="ri-checkbox-circle-fill text-emerald-500 text-xl shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 shrink-0">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- FOOTER -->
        <footer
            class="px-12 py-6 flex justify-between items-center text-[10px] font-bold text-slate-400 dark:text-gray-600 uppercase tracking-widest border-t border-slate-100 dark:border-white/5">
            <p>&copy; {{ date('Y') }} Class Billiard v1.0.0</p>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Secure Core
                </span>
            </div>
        </footer>
    </div>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('themeManager', () => ({
                darkMode: localStorage.getItem('theme') === 'dark' ||
                    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    const theme = this.darkMode ? 'dark' : 'light';
                    
                    // Set localStorage
                    localStorage.setItem('theme', theme);
                    
                    // Set cookie untuk persist antar reload dan bisa dipakai server-side
                    document.cookie = `theme=${theme}; path=/; max-age=31536000`;
                    
                    // Update DOM class
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                handleLogout() {
                    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
            Swal.fire({
                        title: 'Confirm Logout',
                        text: "Sesi administrasi akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                        background: this.darkMode ? '#0A0A0A' : '#fff',
                        color: this.darkMode ? '#fff' : '#000',
                confirmButtonColor: primaryColor || '#fa9a08',
                        cancelButtonColor: '#1e1e1e',
                        confirmButtonText: 'Yes, Sign Out',
                customClass: {
                            popup: 'rounded-lg border border-white/5',
                            confirmButton: 'rounded-md text-xs font-bold px-5 py-2.5',
                            cancelButton: 'rounded-md text-xs font-bold px-5 py-2.5'
                }
            }).then((result) => {
                        if (result.isConfirmed) document.getElementById('logout-form').submit();
            });
                }
            }));

        });

        // Handle Flash Alert Messages
        document.addEventListener('DOMContentLoaded', () => {
            const alertType = "{{ session('alert_type') }}";
            const alertTitle = "{{ session('alert_title') }}";
            const alertMessage = "{{ session('alert_message') }}";
            const alertIcon = "{{ session('alert_icon', 'info') }}";

            if (alertType && alertTitle && alertMessage) {
                const isDarkMode = document.documentElement.classList.contains('dark');
                const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
                
                Swal.fire({
                    title: alertTitle,
                    text: alertMessage,
                    icon: alertIcon,
                    background: isDarkMode ? '#0A0A0A' : '#fff',
                    color: isDarkMode ? '#fff' : '#000',
                    confirmButtonColor: primaryColor || '#fa9a08',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'rounded-lg border border-white/5',
                        confirmButton: 'rounded-md text-xs font-bold px-5 py-2.5'
                    }
                });
            }
        });

        // Helper function untuk SweetAlert2 dengan theme konsisten
        window.showAlert = function(config = {}) {
            const isDark = document.documentElement.classList.contains('dark');
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
            const defaultConfig = {
                background: isDark ? '#0A0A0A' : '#fff',
                color: isDark ? '#fff' : '#000',
                confirmButtonColor: primaryColor || '#fa9a08',
                customClass: {
                    popup: 'rounded-lg border border-white/5',
                    confirmButton: 'rounded-md text-xs font-bold px-5 py-2.5',
                    cancelButton: 'rounded-md text-xs font-bold px-5 py-2.5'
                }
            };
            return Swal.fire({ ...defaultConfig, ...config });
        };

        // Observer untuk deteksi perubahan dark mode dan update alert jika terbuka
        const observer = new MutationObserver(() => {
            const popup = document.querySelector('.swal2-container');
            if (popup && popup.offsetParent !== null) { // Alert sedang tampil
                const isDark = document.documentElement.classList.contains('dark');
                popup.style.background = isDark ? 'rgba(10, 10, 10, 0.95)' : 'rgba(255, 255, 255, 0.95)';
                const title = popup.querySelector('.swal2-title');
                const html = popup.querySelector('.swal2-html-container');
                if (title) title.style.color = isDark ? '#fff' : '#000';
                if (html) html.style.color = isDark ? '#fff' : '#000';
            }
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    </script>
    @stack('scripts')
</body>

</html>