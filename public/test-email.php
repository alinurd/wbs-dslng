<?php
// Akses via: http://domain-anda.com/test-email.php?to=ali.nrdn14005@gmail.com

// Ambil email dari parameter GET
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
    // Cek apakah PHPMailer sudah ada di Laravel
    $vendor_path = __DIR__ . '/../vendor/autoload.php';
    
    if (!file_exists($vendor_path)) {
        return "❌ Vendor autoload tidak ditemukan. Pastikan ini Laravel project.";
    }
    
    require_once $vendor_path;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Enable debugging (sangat membantu)
        $mail->SMTPDebug = 3; // Level 1-4, 3 untuk detail
        $mail->Debugoutput = function($str, $level) {
            echo "Debug level $level: $str<br>";
        };
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->Port       = $config['port'];
        $mail->Timeout    = 30;
        
        // Untuk Exchange Server - COBA BEBERAPA KOMBINASI
        // Kombinasi 1: Auth PLAIN (default)
        // Kombinasi 2: Auth LOGIN
        // Kombinasi 3: Auth NTLM
        
        $mail->AuthType = 'LOGIN'; // Coba ganti: 'LOGIN', 'PLAIN', 'NTLM', 'CRAM-MD5'
        
        // Untuk port 25 biasanya tanpa encryption
        $mail->SMTPSecure = false;
        $mail->SMTPAutoTLS = false;
        
        // Beberapa Exchange butuh setting khusus
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Coba tanpa auth (sesuai kode lama CI)
        if (isset($_GET['noauth'])) {
            $mail->SMTPAuth = false;
            $mail->Username = '';
            $mail->Password = '';
        }
        
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
        return "❌ Gagal mengirim email: " . $mail->ErrorInfo . 
               "<br><br>Debug info: Coba akses URL dengan parameter:<br>" .
               "1. ?to=EMAIL&auth=login<br>" .
               "2. ?to=EMAIL&auth=plain<br>" .
               "3. ?to=EMAIL&noauth=1<br>" .
               "4. ?to=EMAIL&port=587";
    }
}

// Fungsi test koneksi dengan auth
function testConnectionWithAuth($host, $port, $username, $password) {
    $timeout = 10;
    
    // Test koneksi dasar
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    
    if (!$socket) {
        return "❌ Koneksi gagal: {$errstr} (Error: {$errno})";
    }
    
    $response = fgets($socket, 4096);
    fclose($socket);
    
    return "✅ Koneksi berhasil ke {$host}:{$port}<br>Response: " . htmlspecialchars($response);
}

// Fungsi validasi email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Proses pengiriman jika ada parameter action
$result_message = '';
$connection_result = '';

if (isset($_GET['action'])) {
    $connection_result = testConnectionWithAuth(
        $config['host'], 
        $config['port'], 
        $config['username'], 
        $config['password']
    );
    
    if ($_GET['action'] == 'send' && isValidEmail($to_email)) {
        // Coba berbagai konfigurasi berdasarkan parameter
        if (isset($_GET['auth'])) {
            $config['auth_type'] = $_GET['auth'];
        }
        
        if (isset($_GET['port'])) {
            $config['port'] = $_GET['port'];
        }
        
        $subject = "Test Email DSLNG - " . date('Y-m-d H:i:s');
        $message = "<h3>Test Email dari DSLNG SMTP</h3>";
        $message .= "<p>Waktu: " . date('Y-m-d H:i:s') . "</p>";
        $message .= "<p>Host: " . $config['host'] . "</p>";
        $message .= "<p>Port: " . $config['port'] . "</p>";
        $message .= "<p>Username: " . $config['username'] . "</p>";
        if (isset($_GET['noauth'])) {
            $message .= "<p><strong>Mode: Tanpa Authentication</strong></p>";
        }
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
        .container { max-width: 1000px; margin: 0 auto; }
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
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        input[type="email"] { padding: 8px; width: 300px; margin-right: 10px; }
        .form-group { margin: 10px 0; }
        .auth-options { display: flex; flex-wrap: wrap; gap: 10px; margin: 15px 0; }
        .auth-option { background: white; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        pre { background: #333; color: #fff; padding: 10px; overflow-x: auto; }
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
            <p><strong>Password:</strong> <?php echo htmlspecialchars($config['password']); ?></p>
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
            <h3>🔑 Authentication Options</h3>
            <div class="auth-options">
                <div class="auth-option">
                    <strong>Standard Auth</strong><br>
                    <a href="?to=<?php echo urlencode($to_email); ?>&action=send">
                        <button class="btn-success">🔐 Standard Login</button>
                    </a>
                </div>
                
                <div class="auth-option">
                    <strong>Auth Type: LOGIN</strong><br>
                    <a href="?to=<?php echo urlencode($to_email); ?>&action=send&auth=login">
                        <button>🔑 AUTH LOGIN</button>
                    </a>
                </div>
                
                <div class="auth-option">
                    <strong>Auth Type: PLAIN</strong><br>
                    <a href="?to=<?php echo urlencode($to_email); ?>&action=send&auth=plain">
                        <button>📝 AUTH PLAIN</button>
                    </a>
                </div>
                
                <div class="auth-option">
                    <strong>No Authentication</strong><br>
                    <a href="?to=<?php echo urlencode($to_email); ?>&action=send&noauth=1">
                        <button class="btn-warning">🚫 Tanpa Auth</button>
                    </a>
                </div>
                
                <div class="auth-option">
                    <strong>Port 587 (TLS)</strong><br>
                    <a href="?to=<?php echo urlencode($to_email); ?>&action=send&port=587">
                        <button>🔒 Port 587</button>
                    </a>
                </div>
            </div>
            
            <div class="warning">
                <strong>Note:</strong> Berdasarkan kode CI lama, ada kemungkinan:
                <ol>
                    <li>Exchange Server menggunakan AUTH LOGIN</li>
                    <li>Exchange Server menggunakan NTLM authentication</li>
                    <li>Port 25 tanpa authentication (internal network only)</li>
                    <li>Password yang berbeda</li>
                </ol>
            </div>
        </div>
        
        <div class="card">
            <h3>🚀 Test Actions</h3>
            <div class="form-group">
                <form method="GET" style="display: inline-block;">
                    <label for="email">Email Tujuan:</label>
                    <input type="email" id="email" name="to" value="<?php echo htmlspecialchars($to_email); ?>" placeholder="ali.nrdn14005@gmail.com" required>
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
                    <button type="button" class="btn-success">📨 Standard Test Email</button>
                </a>
                <?php else: ?>
                <div class="warning">⚠️ Masukkan email yang valid terlebih dahulu</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <h3>⚙️ Troubleshooting Authentication</h3>
            <div class="info">
                <strong>Solusi untuk "Could not authenticate":</strong>
                <ol>
                    <li><strong>Cek kredensial:</strong> Pastikan username dan password benar</li>
                    <li><strong>Domain dalam username:</strong> Coba format berbeda:
                        <ul>
                            <li><code>wbs@dslng.com</code> (saat ini)</li>
                            <li><code>dslng\wbs</code> (domain\username)</li>
                            <li><code>wbs</code> (username saja)</li>
                        </ul>
                    </li>
                    <li><strong>Auth method:</strong> Exchange mungkin butuh AUTH LOGIN bukan PLAIN</li>
                    <li><strong>Password:</strong> Cek case sensitivity</li>
                    <li><strong>Port alternatif:</strong> Coba port 587 dengan TLS</li>
                    <li><strong>Internal network:</strong> Mungkin hanya bisa dari dalam jaringan DSLNG</li>
                </ol>
            </div>
            
            <div class="warning">
                <strong>Jika semua gagal:</strong>
                <ol>
                    <li>Coba dari server/PC yang ada di jaringan internal DSLNG</li>
                    <li>Hubungi admin IT DSLNG untuk konfirmasi:
                        <ul>
                            <li>SMTP settings yang benar</li>
                            <li>Apakah butuh VPN/internal network?</li>
                            <li>Apakah user wbs@dslng.com aktif?</li>
                            <li>Authentication method yang digunakan</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>
        
        <div class="card">
            <h3>📝 Quick Test URLs</h3>
            <pre>
// Test koneksi
test-email.php?to=<?php echo $to_email; ?>&action=test

// Standard test
test-email.php?to=<?php echo $to_email; ?>&action=send

// Dengan AUTH LOGIN
test-email.php?to=<?php echo $to_email; ?>&action=send&auth=login

// Tanpa authentication (seperti kode CI lama)
test-email.php?to=<?php echo $to_email; ?>&action=send&noauth=1

// Port 587 dengan TLS
test-email.php?to=<?php echo $to_email; ?>&action=send&port=587
            </pre>
        </div>
    </div>
</body>
</html>