<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #667eea; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; background: #f8f9fa; border-radius: 0 0 8px 8px; }
        .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; margin-top: 20px; }
        .success-box { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .config-box { background: #e2e3e5; color: #383d41; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .info-box { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .config-item { display: flex; justify-content: space-between; margin: 8px 0; }
        .config-label { font-weight: bold; }
        .config-value { color: #495057; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $subject }}</h1>
            <p>Whistleblowing System DSLNG</p>
        </div>
        
        <div class="content">
            <p>Halo,</p>
            
            <div class="success-box">
                <h3>✅ Test Email Berhasil!</h3>
                <p>Email ini mengkonfirmasi bahwa konfigurasi email Anda berfungsi dengan benar.</p>
            </div>

            <div class="info-box">
                <h4>Detail Test:</h4>
                <ul>
                    <li><strong>Penerima:</strong> {{ $recipient }}</li>
                    <li><strong>Waktu Kirim:</strong> {{ $testTime }}</li>
                    <li><strong>Sistem:</strong> Whistleblowing System DSLNG</li>
                    <li><strong>Tujuan:</strong> Test Konfigurasi Email</li>
                    <li><strong>Status:</strong> <span style="color: #28a745;">KONEKSI BERHASIL</span></li>
                </ul>
            </div>

            <div class="config-box">
                <h4>📧 Konfigurasi yang Digunakan :</h4>
                <div class="config-item">
                    <span class="config-label">Mailer:</span>
                    <span class="config-value">{{ substr($config['mailer'] ?? 'smtp', 0, 3) }}***</span>
                </div>
                <div class="config-item">
                    <span class="config-label">SMTP Host:</span>
                    <span class="config-value">{{ substr($config['host'] ?? '', 0, 8) }}*********.com</span>
                </div>
                <div class="config-item">
                    <span class="config-label">Port:</span>
                    <span class="config-value">***</span>
                </div>
                <div class="config-item">
                    <span class="config-label">Enkripsi:</span>
                    <span class="config-value">{{ substr($config['encryption'] ?? 'tls', 0, 1) }}**</span>
                </div>
                <div class="config-item">
                    <span class="config-label">Username:</span>
                    <span class="config-value">{{ substr($config['username'] ?? '', 0, 3) }}*****@*******.com</span>
                </div>
                <div class="config-item">
                    <span class="config-label">From Address:</span>
                    <span class="config-value">{{ substr($config['from_address'] ?? '', 0, 3) }}*****@*******.com</span>
                </div>
                <div class="config-item">
                    <span class="config-label">From Name:</span>
                    <span class="config-value">WBS DSLNG System</span>
                </div>
                <div class="config-item">
                    <span class="config-label">Autentikasi:</span>
                    <span class="config-value" style="color: #28a745;">BERHASIL</span>
                </div>
            </div>

            <div class="info-box">
                <h4>🔧 Ringkasan Teknis:</h4>
                <ul>
                    <li>✅ Koneksi SMTP Terbangun</li>
                    <li>✅ Autentikasi Berhasil</li>
                    <li>✅ Pengiriman Email Terkonfirmasi</li>
                    <li>✅ Enkripsi TLS/SSL Aktif</li>
                    <li>✅ Identitas Pengirim Terverifikasi</li>
                </ul>
            </div>

            <p><strong>Artinya:</strong></p>
            <p>Konfigurasi email Anda telah berhasil diuji dan diverifikasi. Sistem sekarang dapat mengirim:</p>
            <ul>
                <li>Email verifikasi pengguna</li>
                <li>Email reset password</li>
                <li>Notifikasi sistem</li>
                <li>Update status laporan</li>
                <li>Email pemberitahuan</li>
            </ul>

            <p><strong>Langkah Selanjutnya:</strong></p>
            <ul>
                <li>✅ Konfigurasi terverifikasi - tidak ada tindakan diperlukan</li>
                <li>Pantau pengiriman email di log audit</li>
                <li>Test skenario email lainnya jika diperlukan</li>
            </ul>

            <p>Ini adalah pesan test otomatis. Anda dapat mengabaikan email ini dengan aman.</p>
        </div>
        
        <div class="footer">
            <p><strong>Whistleblowing System DSLNG</strong></p>
            <p>Ini adalah pesan sistem otomatis. Mohon tidak membalas email ini.</p>
            <p>Hubungi administrator sistem untuk perubahan konfigurasi.</p>
            <p>&copy; {{ date('Y') }} PT. Donggi-Senoro LNG. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>