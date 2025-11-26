<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Halo {{ $userName ?? 'User' }},</h2>
    <p>Kami menerima permintaan reset password untuk akun Anda.</p>
    
    <p>Silakan klik link di bawah ini untuk reset password:</p>
    <a href="{{ $resetLink }}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
        Reset Password
    </a>
    
    <p>Link ini akan kadaluarsa dalam {{ $expiresIn }} menit.</p>
    <p>Jika Anda tidak meminta reset password, silakan abaikan email ini.</p>
    
    <br>
    <p>Terima kasih,<br>Tim Support</p>
</body>
</html>