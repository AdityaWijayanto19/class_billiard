{{-- Common Styles untuk Admin Layout --}}
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

    .submenu-active {
        color: var(--primary-color) !important;
        font-weight: 700;
    }

    /* Sidebar Expansion Animation */
    .sidebar-animate {
        transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.35s ease;
    }
</style>

