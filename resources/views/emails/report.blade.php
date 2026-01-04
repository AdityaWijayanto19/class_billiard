<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Excel</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #fa9a08;">Laporan {{ $reportType }} - Billiard Class</h2>
        
        <p>Yth. Penerima,</p>
        
        <p>Berikut kami kirimkan laporan {{ strtolower($reportType) }} dalam format Excel yang telah Anda minta.</p>
        
        <p>File Excel terlampir pada email ini.</p>
        
        <p style="margin-top: 30px;">
            Salam,<br>
            <strong>Sistem Billiard Class</strong>
        </p>
    </div>
</body>
</html>

