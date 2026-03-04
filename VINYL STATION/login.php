<?php
session_start();
require __DIR__ . '/core/config.php';

if (!empty($_SESSION['is_admin'])) { header("Location: views/list.php"); exit; }

// --- ระบบสร้าง Admin อัตโนมัติในฐานข้อมูล (ครั้งแรก) ---
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
if ($stmt->fetchColumn() == 0) {
    // เข้ารหัสผ่านให้ปลอดภัย (Hash)
    $default_pass = password_hash('414428', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')")->execute([$default_pass]);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // เช็คว่าเจอผู้ใช้ และรหัสผ่านที่ถอดรหัสแล้วตรงกันหรือไม่
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['is_admin'] = true;
        header("Location: views/list.php"); exit;
    } else { 
        $error = 'Username หรือ Password ไม่ถูกต้อง!'; 
    }
}
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Admin Login - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --input-bg: #f5f5f7; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --input-bg: #2c2c2e; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 40px 0; transition: 0.3s; }
        .container { background: var(--card); padding: 50px 40px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 15px 50px var(--shadow); border: 1px solid var(--border); transition: 0.3s; text-align: center; position: relative;}
        h1 { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 26px; margin-top: 0; margin-bottom: 10px; color: var(--text-main); }
        p { color: var(--text-muted); font-size: 14px; margin-bottom: 30px; }
        .error-msg { color: #ffffff; background: #ff3b30; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
        input { width: 100%; padding: 14px; margin-bottom: 20px; background: var(--input-bg); border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); font-family: 'Kanit', sans-serif; font-size: 14px; box-sizing: border-box; transition: 0.3s; }
        input:focus { border-color: var(--text-main); outline: none; }
        button[type="submit"] { width: 100%; padding: 14px; background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; border-radius: 25px; font-size: 15px; font-weight: 800; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: 0.3s; margin-top: 10px; text-transform: uppercase; }
        button[type="submit"]:hover { background: #333333 !important; }
        .footer-link { margin-top: 25px; font-size: 13px; }
        .footer-link a { color: var(--text-muted); text-decoration: none; transition: 0.3s; }
        .footer-link a:hover { color: var(--text-main); }
        .theme-btn-inline { position: absolute; top: 20px; right: 20px; background: var(--card); border: 2px solid var(--text-main); color: var(--text-main); width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; }
        .theme-btn-inline:hover { background: var(--text-main); color: var(--bg); }
    </style>
</head>
<body>
<div class="container">
    <button id="theme-toggle" class="theme-btn-inline">🌙</button>
    <h1>ADMIN LOGIN</h1>
    <p>เข้าสู่ระบบ Database Authentication</p>
    <?php if ($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Username" required autofocus>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">LOGIN SECURELY</button>
    </form>
    <div class="footer-link"><a href="views/list.php">← Back to Collection</a></div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('theme-toggle');
        const updateIcon = (t) => btn.innerHTML = t === 'dark' ? '☀️' : '🌙';
        updateIcon(document.documentElement.getAttribute('data-theme'));
        btn.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next); updateIcon(next);
        });
    });
</script>
</body>
</html>