<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Terbatas</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #0a0a0a;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body>
    <script>
        const isDarkMode = true;
        const referrer = document.referrer || '/admin';

        Swal.fire({
            title: 'Akses Terbatas',
            text: 'Maaf, Anda tidak memiliki izin untuk mengakses fitur ini. Hubungi admin jika merasa ini adalah kesalahan.',
            icon: 'info',
            background: '#0A0A0A',
            color: '#fff',
            confirmButtonColor: '#fa9a08',
            confirmButtonText: 'Kembali',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'rounded-lg border border-white/5',
                confirmButton: 'rounded-md text-xs font-bold px-5 py-2.5'
            }
        }).then(() => {
            // Redirect ke halaman sebelumnya setelah user confirm
            window.location.href = referrer;
        });
    </script>
</body>
</html>
