{{-- Sidebar & Main Content Styles (mengikuti styling admin) --}}
@push('styles')
<style>
    /* Hide scrollbar but keep functionality */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Ensure smooth margin-left transition */
    .min-h-screen.flex.flex-col {
        transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Responsive Styles for Tablet and Mobile */
    @media (max-width: 1024px) {
        /* Sidebar width on mobile - collapsed = 80px, expanded = 280px */
        #sidebar {
            transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #sidebar.w-20 {
            width: 80px !important;
        }
        #sidebar.w-72 {
            width: 280px !important;
        }
        /* Main content margin on mobile - follows sidebar width */
        .min-h-screen.flex.flex-col.ml-20 {
            margin-left: 80px !important;
        }
        .min-h-screen.flex.flex-col.ml-72 {
            margin-left: 280px !important;
        }
    }
    
    @media (max-width: 768px) {
        /* Sidebar width on small mobile - collapsed = 80px, expanded = 260px */
        #sidebar.w-20 {
            width: 80px !important;
        }
        #sidebar.w-72 {
            width: 260px !important;
        }
        /* Main content margin on small mobile */
        .min-h-screen.flex.flex-col.ml-20 {
            margin-left: 80px !important;
        }
        .min-h-screen.flex.flex-col.ml-72 {
            margin-left: 260px !important;
        }
    }
</style>
@endpush

