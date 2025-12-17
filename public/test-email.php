<?php
 
 $to_email = isset($_GET['to']) ? $_GET['to'] : 'wbs@dslng.com';

// Konfigurasi SMTP DSLNG
$config = [
    'host' => 'exchange.dslng.com',
    'port' => 25,
    'username' => 'wbs@dslng.com',
    'password' => 'dslng.1740',
    'from_email' => 'wbs@dslng.com',
    'from_name' => 'WBS System',
    'to_email' => $to_email
];

function sendEmailSMTP($config, $to, $subject, $message) {
     $vendor_path = __DIR__ . '/../vendor/autoload.php';
    
    if (!file_exists($vendor_path)) {
        return "❌ Vendor autoload tidak ditemukan. Pastikan ini Laravel project.";
    }
    
    require_once $vendor_path;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->Port       = $config['port'];
        $mail->Timeout    = 30;
        
        // Port 25 biasanya tanpa encryption
        $mail->SMTPSecure = false;
        $mail->SMTPAutoTLS = false;
        
        // Untuk Exchange Server, mungkin perlu setting ini
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Encoding
        $mail->CharSet = 'UTF-8';
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        
        $mail->send();
        return "✅ Email berhasil dikirim ke: " . $to;
        
    } catch (Exception $e) {
        return "❌ Gagal mengirim email: " . $mail->ErrorInfo;
    }
}

// Fungsi test koneksi sederhana
function testConnection($host, $port) {
    $timeout = 10;
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    
    if ($socket) {
        fclose($socket);
        return "✅ Koneksi berhasil ke {$host}:{$port}";
    } else {
        return "❌ Koneksi gagal: {$errstr} (Error: {$errno})";
    }
}

// Fungsi validasi email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Proses pengiriman jika ada parameter action
$result_message = '';
$connection_result = '';

if (isset($_GET['action'])) {
    $connection_result = testConnection($config['host'], $config['port']);
    
    if ($_GET['action'] == 'send' && isValidEmail($to_email)) {
        $subject = "Test Email DSLNG - " . date('Y-m-d H:i:s');
        $message = "<h3>Test Email dari DSLNG SMTP</h3>";
        $message .= "<p>Waktu: " . date('Y-m-d H:i:s') . "</p>";
        $message .= "<p>Host: " . $config['host'] . "</p>";
        $message .= "<p>Port: " . $config['port'] . "</p>";
        $message .= "<p>Ini adalah email test dari sistem.</p>";
        
        $result_message = sendEmailSMTP($config, $to_email, $subject, $message);
    }
}

// HTML Output
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Email DSLNG</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 10px 0; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 3px; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 3px; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 3px; }
        .warning { color: orange; background: #fff3cd; padding: 10px; border-radius: 3px; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        input[type="email"] { padding: 8px; width: 300px; margin-right: 10px; }
        .form-group { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Test Email DSLNG SMTP</h1>
        
        <div class="card">
            <h3>📋 Konfigurasi SMTP</h3>
            <p><strong>Host:</strong> <?php echo htmlspecialchars($config['host']); ?></p>
            <p><strong>Port:</strong> <?php echo htmlspecialchars($config['port']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($config['username']); ?></p>
            <p><strong>From:</strong> <?php echo htmlspecialchars($config['from_email']); ?></p>
            <p><strong>To:</strong> 
                <?php 
                    if (isValidEmail($to_email)) {
                        echo htmlspecialchars($to_email);
                    } else {
                        echo '<span class="error">Email tidak valid!</span>';
                    }
                ?>
            </p>
        </div>
        
        <?php if ($connection_result): ?>
        <div class="card">
            <h3>📤 Hasil Test Koneksi</h3>
            <?php 
                if (strpos($connection_result, '✅') !== false) {
                    echo '<div class="success">' . $connection_result . '</div>';
                } else {
                    echo '<div class="error">' . $connection_result . '</div>';
                }
            ?>
        </div>
        <?php endif; ?>
        
        <?php if ($result_message): ?>
        <div class="card">
            <h3>📨 Hasil Pengiriman Email</h3>
            <?php 
                if (strpos($result_message, '✅') !== false) {
                    echo '<div class="success">' . $result_message . '</div>';
                } else {
                    echo '<div class="error">' . $result_message . '</div>';
                }
            ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>🚀 Test Actions</h3>
            <div class="form-group">
                <form method="GET" style="display: inline-block;">
                    <label for="email">Email Tujuan:</label>
                    <input type="email" id="email" name="to" value="<?php echo htmlspecialchars($to_email); ?>" placeholder="email@example.com" required>
                    <button type="submit" class="btn-warning">🔄 Ganti Email</button>
                </form>
            </div>
            
            <div class="form-group">
                <p>Email saat ini: <strong><?php echo htmlspecialchars($to_email); ?></strong></p>
                <a href="?to=<?php echo urlencode($to_email); ?>&action=test">
                    <button type="button">🔗 Test Koneksi Saja</button>
                </a>
                
                <?php if (isValidEmail($to_email)): ?>
                <a href="?to=<?php echo urlencode($to_email); ?>&action=send">
                    <button type="button" class="btn-success">📨 Kirim Test Email</button>
                </a>
                <?php else: ?>
                <div class="warning">⚠️ Masukkan email yang valid terlebih dahulu</div>
                <?php endif; ?>
            </div>
            
            <div class="info" style="margin-top: 15px;">
                <strong>Contoh URL:</strong>
                <ul>
                    <li><code>test-email.php?to=ali.nrdn14005@gmail.com</code></li>
                    <li><code>test-email.php?to=ali.nrdn14005@gmail.com&action=test</code></li>
                    <li><code>test-email.php?to=ali.nrdn14005@gmail.com&action=send</code></li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <h3>⚙️ Troubleshooting</h3>
            <div class="info">
                <strong>Jika email tidak terkirim:</strong>
                <ol>
                    <li>Pastikan server dapat mengakses <code>exchange.dslng.com:25</code></li>
                    <li>Install PHPMailer: <code>composer require phpmailer/phpmailer</code></li>
                    <li>Exchange Server mungkin membutuhkan setting khusus</li>
                    <li>Cek log error Laravel: <code>storage/logs/laravel.log</code></li>
                    <li>Coba disable firewall sementara untuk testing</li>
                </ol>
            </div>
        </div>
      
    </div>
</body>
</html>