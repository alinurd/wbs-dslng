<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email</title>
</head>
<body>
    <h2>Halo {{ $userName ?? 'User' }},</h2>
    <p>Berikut adalah kode verifikasi untuk akun Anda:</p>
    
    <div style="background: #f4f4f4; padding: 10px; margin: 10px 0;">
        <h1 style="text-align: center; color: #333;">{{ $verificationCode }}</h1>
    </div>
    
    <p>Kode ini akan kadaluarsa dalam {{ $expiresIn }} menit.</p>
    <p>Jika Anda tidak meminta kode ini, silakan abaikan email ini.</p>
    
    <br>
    <p>Terima kasih,<br>Tim Support</p>
</body>
</html>