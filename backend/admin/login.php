<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));

    $stmt = db()->prepare('SELECT * FROM admin WHERE username = :username AND password = :password LIMIT 1');
    $stmt->execute(['username' => $username, 'password' => $password]);
    $admin = $stmt->fetch();
    if ($admin) {
        $_SESSION['admin'] = $admin;
        header('Location: index.php');
        exit;
    }
    $error = '账号或密码错误';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>管理员登录</title><style>:root{--bg-1:#fff7ed;--bg-2:#ecfeff;--text:#0f172a;--muted:#64748b;--line:#cbd5e1;--primary:#ea580c;--primary-2:#f97316}*{box-sizing:border-box}body{font-family:"Segoe UI",PingFang SC,"Microsoft YaHei",sans-serif;background:radial-gradient(circle at 10% 10%,#ffedd5 0,#fff7ed 35%,transparent 36%),radial-gradient(circle at 85% 15%,#cffafe 0,#ecfeff 38%,transparent 39%),linear-gradient(150deg,var(--bg-1),var(--bg-2));display:flex;align-items:center;justify-content:center;height:100vh;margin:0;color:var(--text)}.card{background:rgba(255,255,255,.88);padding:34px;border-radius:22px;box-shadow:0 24px 60px rgba(15,23,42,.12),inset 0 0 0 1px rgba(255,255,255,.75);width:380px;backdrop-filter:blur(8px)}h2{margin:0 0 18px;font-size:28px;letter-spacing:1px}.error{margin:0 0 8px;color:#dc2626;background:#fee2e2;border-radius:10px;padding:10px 12px;font-size:14px}input,button{width:100%;padding:13px 14px;margin-top:12px;border-radius:12px;font-size:14px}input{border:1px solid var(--line);background:#fff;outline:0;transition:border-color .2s,box-shadow .2s}input:focus{border-color:#14b8a6;box-shadow:0 0 0 3px rgba(20,184,166,.14)}button{background:linear-gradient(120deg,var(--primary),var(--primary-2));color:#fff;border:0;font-weight:600;cursor:pointer;box-shadow:0 10px 24px rgba(249,115,22,.28);transition:transform .15s ease,box-shadow .2s ease}button:hover{transform:translateY(-1px);box-shadow:0 14px 30px rgba(249,115,22,.34)}button:active{transform:translateY(0)}</style></head>
<body>
<div class="card">
  <h2>管理员登录</h2>
  <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="账号">
    <input name="password" type="password" placeholder="密码">
    <button type="submit">登录</button>
  </form>
</div>
</body>
</html>