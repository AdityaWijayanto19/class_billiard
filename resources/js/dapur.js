/**
 * Dapur Dashboard Common JavaScript
 * File ini berisi semua fungsi umum yang digunakan di semua halaman dapur
 */

// ============================================
// SIDEBAR CLOCK FUNCTIONALITY
// ============================================
function updateSidebarClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: 'Asia/Jakarta'
    });
    const clockElement = document.getElementById('sidebar-clock');
    if (clockElement) {
        clockElement.textContent = timeString;
    }
}

// Initialize sidebar clock
function initSidebarClock() {
    // Update clock every second
    setInterval(updateSidebarClock, 1000);
    updateSidebarClock();
}

// ============================================
// SIDEBAR TOGGLE FUNCTIONALITY
// ============================================
function initSidebarToggle() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const mainContent = document.querySelector('.main-content');

    if (!sidebar) return;

    function toggleSidebar() {
        const isMobile = window.innerWidth <= 1024;
        if (isMobile) {
            const isCollapsed = sidebar.classList.contains('collapsed');
            if (isCollapsed) {
                // Open sidebar
                sidebar.classList.remove('collapsed');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.add('show');
                }
                // Prevent body scroll when sidebar is open
                document.body.style.overflow = 'hidden';
            } else {
                // Close sidebar
                sidebar.classList.add('collapsed');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }
                // Restore body scroll
                document.body.style.overflow = '';
            }
        }
    }

    // Initialize sidebar as collapsed on mobile, visible on desktop
    if (window.innerWidth <= 1024) {
        sidebar.classList.add('collapsed');
    } else {
        sidebar.classList.remove('collapsed');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Close sidebar when window is resized to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('collapsed');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
            document.body.style.overflow = '';
        } else {
            // Ensure sidebar is collapsed on mobile
            if (!sidebar.classList.contains('collapsed')) {
                sidebar.classList.add('collapsed');
            }
        }
    });
}

// ============================================
// INITIALIZE ALL FUNCTIONS
// ============================================
function initDapurCommon() {
    // Initialize sidebar clock
    initSidebarClock();
    
    // Initialize sidebar toggle
    initSidebarToggle();
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDapurCommon);
} else {
    // DOM is already ready
    initDapurCommon();
}

