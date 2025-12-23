<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #667eea; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
         .button-container { background: #f4f4f4; padding: 20px; margin: 20px 0; text-align: center; }
        .login-button { display: inline-block; padding: 12px 30px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .login-button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $title }}</h1>
        </div>
        <div class="content">
            {{-- {{dd($content)}} --}}
            {!! $content !!}
        </div>
         
        <div class="button-container">
            <a href="{{ route('dashboard') }}" class="login-button">
                Login disini
            </a>
        </div>
        <div class="footer">
            <hr style='border: none; border-top: 1px solid #e9ecef; margin: 30px 0;'>
        
        <p style='color: #6c757d; font-size: 12px; text-align: center;'>
            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
        </p>
        
            <p>&copy; {{ date('Y') }} Whistleblowing System</p>
        </div>
    </div>
</body>
</html>