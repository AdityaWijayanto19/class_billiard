{{-- Notification Styles (diambil dari dapur.blade.php) --}}
@push('styles')
<style>
    /* Notification Styles */
    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }
    
    .notification-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9998;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        visibility: hidden;
    }
    
    .notification-overlay.show {
        opacity: 1;
        pointer-events: auto;
        visibility: visible;
    }
    
    @media (min-width: 1024px) {
        .notification-overlay {
            display: none;
        }
    }
    
    .notification {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(var(--primary-color-rgb), 0.4);
        margin-bottom: 15px;
        animation: slideInRight 0.5s ease-out;
        display: flex;
        align-items: center;
        gap: 15px;
        min-width: 300px;
        position: relative;
        z-index: 9999;
    }
    
    @media (max-width: 640px) {
        .notification-container {
            top: 10px;
            right: 10px;
            left: 10px;
            max-width: none;
        }
        
        .notification {
            min-width: auto;
            width: 100%;
            padding: 16px;
        }
    }
    
    .notification.hide {
        animation: slideOutRight 0.5s ease-out forwards;
    }
    
    .notification-icon {
        font-size: 32px;
        animation: pulse 1s infinite;
    }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-title {
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 5px;
    }
    
    .notification-message {
        font-size: 14px;
        opacity: 0.95;
    }
    
    .notification-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    
    .notification-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
</style>
@endpush

