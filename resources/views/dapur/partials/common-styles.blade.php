{{-- Common Styles untuk semua halaman Dapur --}}
@push('styles')
<style>
    [x-cloak] {
        display: none !important;
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

    /* Professional Link State */
    .active-link {
        background-color: var(--primary-color);
        color: #000 !important;
    }

    /* Sidebar Expansion Animation */
    .sidebar-animate {
        transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.35s ease;
    }

    .sidebar-menu-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .sidebar-menu-item.active {
        background-color: var(--primary-color);
        color: #000 !important;
        font-weight: 600;
    }
    .sidebar-menu-item.active i {
        color: #000 !important;
    }
    .sidebar-menu-item.active span {
        color: #000 !important;
    }
</style>
@endpush

