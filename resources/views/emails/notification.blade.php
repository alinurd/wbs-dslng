<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .content {
            padding: 25px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .danger-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .detail-box {
            background: #e9ecef;
            padding: 12px;
            border-radius: 5px;
            margin: 12px 0;
        }
        .footer {
            background: #343a40;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 8px 0;
        }
        .code {
            font-family: 'Courier New', monospace;
            background: #2d3748;
            color: #68d391;
            padding: 8px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 12px 0;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 8px;
        }
        .badge-info { background: #17a2b8; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-danger { background: #dc3545; color: white; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 4px 0;
            vertical-align: top;
        }
        table td:first-child {
            width: 120px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $title }}</h1>
        </div>
        
        <div class="content">
            @if($type === 'urgent')
            <div class="warning-box">
                <strong>🚨 TINDAKAN SEGERA DIBUTUHKAN</strong>
            </div>
            @endif

            @if($type === 'reject')
            <div class="danger-box">
                <strong>❌ PENOLAKAN PENGADUAN</strong>
            </div>
            @endif

            <div>
                {!! $message !!}
            </div>

            @if(isset($actionUrl) && isset($actionText))
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ $actionUrl }}" class="btn">
                    {{ $actionText }}
                </a>
            </div>
            @endif

            @if(isset($additionalInfo))
            <div class="info-box">
                <strong>📋 Informasi Tambahan</strong><br>
                {!! $additionalInfo !!}
            </div>
            @endif

            @if(isset($codePengaduan))
            <div style="text-align: center; margin: 15px 0;">
                <div class="code">{{ $codePengaduan }}</div>
                <small>Gunakan kode ini untuk melacak status pengaduan</small>
            </div>
            @endif
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Whistleblowing System. All rights reserved.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>