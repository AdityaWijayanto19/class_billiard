{{-- Alpine.js Theme Manager Script --}}
@push('head')
<script>
    {{-- Helper function to get cookie value --}}
    function getCookie(name) {
        const nameEQ = name + "=";
        const cookies = document.cookie.split(';');
        for(let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if(cookie.indexOf(nameEQ) === 0) {
                return cookie.substring(nameEQ.length);
            }
        }
        return null;
    }

    {{-- Initialize theme immediately in head --}}
    (function() {
        try {
            const cookieTheme = getCookie('theme');
            const savedTheme = localStorage.getItem('theme');
            
            // Prioritize cookie, then localStorage, default to dark
            const theme = cookieTheme || savedTheme || 'dark';
            
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            console.log('Theme HEAD initialized:', theme, 'Cookie:', cookieTheme, 'LocalStorage:', savedTheme);
        } catch(e) {
            console.error('Theme initialization error:', e);
        }
    })();
</script>
@endpush

@push('scripts')
<script>
    {{-- Helper function to get cookie value --}}
    function getCookie(name) {
        const nameEQ = name + "=";
        const cookies = document.cookie.split(';');
        for(let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if(cookie.indexOf(nameEQ) === 0) {
                return cookie.substring(nameEQ.length);
            }
        }
        return null;
    }

    {{-- Theme Manager Alpine.js Component --}}
    document.addEventListener('alpine:init', () => {
        Alpine.data('themeManager', () => ({
            sidebarCollapsed: false,
            sidebarHover: false,
            darkMode: false, // Will be set in initTheme()

            initTheme() {
                // Set initial theme based on cookie, localStorage, or default to dark
                const cookieTheme = getCookie('theme');
                const savedTheme = localStorage.getItem('theme');
                
                // Prioritize cookie, then localStorage, default to dark
                const theme = cookieTheme || savedTheme || 'dark';
                
                this.darkMode = theme === 'dark';
                
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                
                // Sync localStorage with cookie if cookie exists
                if (cookieTheme && cookieTheme !== savedTheme) {
                    localStorage.setItem('theme', cookieTheme);
                }
                
                // Initialize sidebar state
                this.initSidebarState();
                
                console.log('Theme initialized:', this.darkMode ? 'dark' : 'light', 'Cookie:', cookieTheme, 'LocalStorage:', savedTheme);
            },

            toggleTheme() {
                this.darkMode = !this.darkMode;
                const theme = this.darkMode ? 'dark' : 'light';
                
                // Save to localStorage
                localStorage.setItem('theme', theme);
                
                // Set cookie with SameSite=Lax
                document.cookie = `theme=${theme}; path=/; max-age=31536000; SameSite=Lax`;
                
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                
                this.$nextTick(() => {
                    console.log('Theme toggled to:', theme);
                    console.log('Cookie value:', getCookie('theme'));
                    console.log('LocalStorage value:', localStorage.getItem('theme'));
                    console.log('HTML has dark class:', document.documentElement.classList.contains('dark'));
                });
            },

            initSidebarState() {
                // Load sidebar collapsed state from localStorage
                const savedState = localStorage.getItem('sidebarCollapsed');
                if (savedState === 'true') {
                    this.sidebarCollapsed = true;
                }
            },

            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                this.sidebarHover = false;
                localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
            },

            handleLogout() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Confirm Logout',
                        text: 'Are you sure you want to logout?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                        cancelButtonColor: '#1e1e1e',
                        confirmButtonText: 'Yes, Sign Out',
                        background: this.darkMode ? '#0A0A0A' : '#fff',
                        color: this.darkMode ? '#fff' : '#000',
                        customClass: {
                            popup: 'rounded-lg border border-white/5',
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const logoutForm = document.getElementById('logout-form');
                            if (logoutForm) {
                                logoutForm.submit();
                            } else {
                                // Fallback: create and submit form dynamically
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = '{{ route("logout") }}';
                                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                                if (csrfToken) {
                                    const csrfInput = document.createElement('input');
                                    csrfInput.type = 'hidden';
                                    csrfInput.name = '_token';
                                    csrfInput.value = csrfToken.getAttribute('content');
                                    form.appendChild(csrfInput);
                                }
                                document.body.appendChild(form);
                                form.submit();
                            }
                        }
                    });
                } else {
                    // If SweetAlert is not available, submit logout form directly
                    const logoutForm = document.getElementById('logout-form');
                    if (logoutForm) {
                        logoutForm.submit();
                    }
                }
            }
        }));
    });
</script>
@endpush

