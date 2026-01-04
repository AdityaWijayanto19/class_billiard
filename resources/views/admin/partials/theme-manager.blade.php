{{-- Alpine.js Theme Manager Script untuk Admin --}}
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
                showAlert({
                    title: 'Confirm Logout',
                    text: "Sesi administrasi akan diakhiri.",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Sign Out',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) performLogout();
                });
            }
        }));
    });
</script>

