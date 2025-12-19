<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email</title>
</head>
<body>
    <h2>Halo {{ $userName ?? 'User' }},</h2>
    <p>Kami menerima permintaan reset password untuk akun Anda:</p>
    
     <div style="background: #f4f4f4; padding: 10px; margin: 10px 0; text-align: center;">
        <a href="{{ $link }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
            Reset Password
        </a>
    </div>
    
     
    <br>
    <p>Terima kasih,<br>Tim Support</p>
</body>
</html>
 