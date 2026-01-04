{{-- Dynamic Primary Color CSS Variables for Dapur --}}
@push('styles')
<style>
    /* Dynamic Primary Color System for Kitchen Dashboard */
    :root {
        --primary-color: {{ auth()->user()->primary_color ?? '#fa9a08' }};
        --primary-color-rgb: {{ auth()->user()->primary_color === '#fbbf24' ? '251, 191, 36' : (auth()->user()->primary_color === '#2f7d7a' ? '47, 125, 122' : '250, 154, 8') }};
        --primary-hover: {{ auth()->user()->primary_color === '#fbbf24' ? '#d9a61c' : (auth()->user()->primary_color === '#2f7d7a' ? '#1f5350' : '#d97706') }};
        --primary-light: {{ auth()->user()->primary_color === '#fbbf24' ? '#fde8a1' : (auth()->user()->primary_color === '#2f7d7a' ? '#9ec4c1' : '#fed7aa') }};
    }

    .theme-transition {
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
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

    /* Dynamic border hover states */
    .border-hover-primary:hover {
        border-color: var(--primary-color) !important;
    }

    /* Dynamic text hover states */
    .text-hover-primary:hover {
        color: var(--primary-color) !important;
    }

    /* Active link states */
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

    /* Navbar dynamic styling */
    .navbar-primary {
        background-color: var(--primary-color);
        color: #000;
    }

    .navbar-primary:hover {
        background-color: var(--primary-hover);
    }

    /* Sidebar dynamic styling */
    .sidebar-primary {
        border-left-color: var(--primary-color);
    }

    /* Badge/Label styling */
    .badge-primary {
        background-color: var(--primary-color);
        color: #000;
    }

    /* Progress bar styling */
    .progress-primary {
        background-color: var(--primary-color);
    }

    /* Accent color for icons */
    .icon-primary {
        color: var(--primary-color);
    }

    /* Background with opacity */
    .bg-primary-light {
        background-color: rgba(var(--primary-color-rgb), 0.1);
    }

    .bg-primary-lighter {
        background-color: rgba(var(--primary-color-rgb), 0.05);
    }

    /* Border with primary color */
    .border-primary {
        border-color: var(--primary-color) !important;
    }

    /* Text with primary color */
    .text-primary {
        color: var(--primary-color) !important;
    }
</style>
@endpush
