{{-- Order Card Styles (diambil dari dapur.blade.php) --}}
@push('styles')
<style>
    /* Modern Order Card Styles */
    .order-card-modern {
        position: relative;
        backdrop-filter: blur(10px);
    }

    .order-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .order-card-modern:hover::before {
        opacity: 1;
    }

    .order-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(var(--primary-color-rgb), 0.3);
    }

    .complete-order-btn {
        position: relative;
        overflow: hidden;
    }

    .complete-order-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(var(--primary-color-rgb), 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .complete-order-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .complete-order-btn span {
        position: relative;
        z-index: 1;
    }

    .complete-order-btn i {
        position: relative;
        z-index: 1;
    }

    /* Order cards responsive */
    @media (max-width: 1024px) {
        .grid.grid-cols-\[repeat\(auto-fill\,minmax\(350px\,1fr\)\)\] {
            grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
        }
    }

    @media (max-width: 768px) {
        /* Order card mobile */
        .bg-\[#fa9a08\] {
            padding: 1rem !important;
        }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
        /* Tablet styles */
        .grid.grid-cols-\[repeat\(auto-fill\,minmax\(350px\,1fr\)\)\] {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }
</style>
@endpush

