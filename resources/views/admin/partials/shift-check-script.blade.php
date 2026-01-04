{{-- Script untuk check shift end dan auto-logout (Admin) --}}
<script>
    // Check shift end and auto-logout (REAL-TIME dengan session shift_end)
    function checkShiftEndRealTime() {
        const shiftEndMeta = document.querySelector('meta[name="shift-end-timestamp"]');
        
        if (!shiftEndMeta) {
            return; // Skip jika meta tidak ada
        }
        
        const shiftEndTimestamp = parseInt(shiftEndMeta.getAttribute('content'));
        const now = Math.floor(Date.now() / 1000);
        
        // Cek apakah shift sudah berakhir
        if (now >= shiftEndTimestamp) {
            // Shift telah berakhir - logout otomatis
            let message = 'Shift Anda telah berakhir. Anda akan di-logout.';
            
            if (typeof showAlert !== 'undefined') {
                showAlert({
                    icon: 'info',
                    title: 'Shift Berakhir',
                    html: `<p class="text-lg mb-2">${message}</p>`,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    performLogout();
                });
            } else {
                performLogout();
            }
            return;
        }
        
        // Cek apakah 5 menit sebelum shift berakhir untuk notifikasi
        const minutesUntilEnd = Math.floor((shiftEndTimestamp - now) / 60);
        if (minutesUntilEnd <= 5 && minutesUntilEnd >= 0) {
            const lastNotification = localStorage.getItem('shiftWarningShown');
            const currentMinute = Math.floor(now / 60);
            
            if (lastNotification !== String(currentMinute)) {
                // Show browser notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('⏰ Peringatan Shift', {
                        body: `Shift akan berakhir dalam ${minutesUntilEnd} menit!`,
                        icon: '{{ asset("logo.png") }}',
                        tag: 'shift-warning',
                        requireInteraction: true
                    });
                }
                
                // Show SweetAlert notification
                if (typeof showAlert !== 'undefined') {
                    showAlert({
                        icon: 'warning',
                        title: '⏰ Peringatan!',
                        html: `<p class="text-lg mb-2">Shift akan berakhir dalam <strong>${minutesUntilEnd} menit</strong>!</p>`,
                        confirmButtonText: 'OK'
                    });
                }
                
                localStorage.setItem('shiftWarningShown', String(currentMinute));
            }
        }
    }
    
    // Helper function untuk logout
    function performLogout() {
        const logoutForm = document.getElementById('logout-form');
        if (logoutForm) {
            logoutForm.submit();
        } else {
            // Fallback: create and submit form
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
            const body = document.body;
            if (body) {
                body.appendChild(form);
                form.submit();
            }
        }
    }
    
    // Wait for DOM to be ready before checking shift end
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            // Check shift end every 10 seconds untuk real-time auto-logout
            setInterval(checkShiftEndRealTime, 10000);
            checkShiftEndRealTime();
            
            // Request notification permission on page load
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        });
    } else {
        // DOM is already ready
        // Check shift end every 10 seconds untuk real-time auto-logout
        setInterval(checkShiftEndRealTime, 10000);
        checkShiftEndRealTime();
        
        // Request notification permission on page load
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
</script>

