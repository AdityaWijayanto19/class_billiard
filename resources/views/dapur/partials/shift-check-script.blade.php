{{-- Script untuk check shift end dan auto-logout --}}
@push('scripts')
<script>
    // Check shift end and auto-logout (REAL-TIME dengan session shift_end)
    function checkShiftEndRealTime() {
        const shiftEndMeta = document.querySelector('meta[name="shift-end-timestamp"]');
        
        if (!shiftEndMeta) {
            // Fallback ke API check jika meta tidak ada
            checkShiftEndAPI();
            return;
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
                        body: `Shift akan berakhir dalam ${minutesUntilEnd} menit. Pastikan semua order sudah selesai.`,
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
                        html: `<p class="text-lg mb-2">Shift akan berakhir dalam <strong>${minutesUntilEnd} menit</strong>!</p><p class="text-sm">Pastikan semua order sudah selesai sebelum shift berakhir.</p>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim()
                    });
                }
                
                localStorage.setItem('shiftWarningShown', String(currentMinute));
            }
        }
    }
    
    // Fallback: API check jika meta tidak tersedia
    async function checkShiftEndAPI() {
        try {
            const response = await fetch('{{ route("shift.check") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (!result.success && result.shift_ended) {
                // Shift has ended, logout user
                let message = result.message || 'Shift Anda telah berakhir. Anda akan di-logout.';
                
                if (result.orders_transferred > 0) {
                    message += `\n\n${result.orders_transferred} orderan yang belum selesai telah dipindahkan ke shift berikutnya.`;
                }
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Shift Berakhir',
                        html: `<p class="text-lg mb-2">${message}</p>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        background: document.documentElement.classList.contains('dark') ? '#0A0A0A' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    }).then(() => {
                        performLogout();
                    });
                } else {
                    // If SweetAlert is not available, submit logout form immediately
                    setTimeout(() => {
                        performLogout();
                    }, 2000);
                }
                return;
            }
            
            // Update shift end timestamp if available
            if (result.shift_end_timestamp) {
                window.shiftEndTimestamp = result.shift_end_timestamp;
            }
        } catch (error) {
            console.error('Error checking shift end:', error);
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
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Wait for DOM to be ready before checking shift end
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            // Check shift end every 10 seconds untuk real-time auto-logout
            setInterval(checkShiftEndRealTime, 10000);
            checkShiftEndRealTime();
        });
    } else {
        // DOM is already ready
        // Check shift end every 10 seconds untuk real-time auto-logout
        setInterval(checkShiftEndRealTime, 10000);
        checkShiftEndRealTime();
    }
    
    // Request notification permission on page load
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
</script>
@endpush
