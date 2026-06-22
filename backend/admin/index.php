<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$pdo = db();
$tab = $_GET['tab'] ?? 'goods';
$statusMap = [0 => '待付款', 1 => '待发货', 2 => '已完成'];

if ($tab === 'goods' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'price' => (float) ($_POST['price'] ?? 0),
            'cover' => trim((string) ($_POST['cover'] ?? '')),
            'detail' => trim((string) ($_POST['detail'] ?? '')),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'category_id' => (int) ($_POST['category_id'] ?? 0),
        ];
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE goods SET name=:name, price=:price, cover=:cover, detail=:detail, stock=:stock, category_id=:category_id WHERE id=:id');
            $data['id'] = $id;
            $stmt->execute($data);
        } else {
            $stmt = $pdo->prepare('INSERT INTO goods (name, price, cover, detail, stock, category_id, create_time) VALUES (:name, :price, :cover, :detail, :stock, :category_id, :create_time)');
            $data['create_time'] = now_string();
            $stmt->execute($data);
        }
        header('Location: index.php?tab=goods');
        exit;
    }
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM goods WHERE id = :id');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        header('Location: index.php?tab=goods');
        exit;
    }
}

if ($tab === 'orders' && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
    $stmt->execute(['status' => (int) $_POST['status'], 'id' => (int) $_POST['id']]);
    header('Location: index.php?tab=orders');
    exit;
}

if ($tab === 'users' && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM user WHERE id = :id');
    $stmt->execute(['id' => (int) $_POST['id']]);
    header('Location: index.php?tab=users');
    exit;
}

$goods = $pdo->query('SELECT * FROM goods ORDER BY id DESC')->fetchAll();
$orders = $pdo->query('SELECT * FROM orders ORDER BY id DESC')->fetchAll();
$users = $pdo->query('SELECT * FROM user ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>商城后台管理</title>
  <style>
    :root{--bg:#f8fafc;--line:#dbe4ef;--line-soft:#eaf0f7;--text:#0f172a;--muted:#64748b;--primary:#ea580c;--primary-2:#f97316;--danger:#dc2626;--success:#059669}
    *{box-sizing:border-box}
    body{font-family:"Segoe UI",PingFang SC,"Microsoft YaHei",sans-serif;background:radial-gradient(circle at 18% -5%,#ffedd5 0,#fff7ed 28%,transparent 29%),radial-gradient(circle at 102% 10%,#cffafe 0,#ecfeff 34%,transparent 35%),var(--bg);margin:0;color:var(--text)}
    .wrap{display:flex;min-height:100vh}
    .side{width:240px;background:linear-gradient(180deg,#0f172a,#1e293b);color:#fff;padding:30px 22px;box-shadow:14px 0 38px rgba(15,23,42,.25)}
    .side h2{margin:0 0 18px;font-size:24px;letter-spacing:1px}
    .side a{display:block;color:#cbd5e1;text-decoration:none;padding:11px 14px;margin-bottom:8px;border-radius:12px;transition:background .2s,color .2s,transform .15s}
    .side a:hover{background:rgba(148,163,184,.18);color:#fff;transform:translateX(2px)}
    .main{flex:1;padding:28px}
    .topbar{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:18px;padding:20px 24px;background:rgba(255,255,255,.82);border:1px solid rgba(255,255,255,.95);backdrop-filter:blur(8px);border-radius:18px;box-shadow:0 16px 34px rgba(15,23,42,.08)}
    .topbar h1{margin:0;font-size:30px;letter-spacing:1px}
    .topbar div{color:var(--muted);font-size:14px}
    .card{background:rgba(255,255,255,.9);border-radius:18px;padding:22px;margin-bottom:22px;border:1px solid rgba(255,255,255,.96);box-shadow:0 16px 38px rgba(15,23,42,.08)}
    .card h3{margin:0 0 14px;font-size:22px}
    table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid var(--line);border-radius:14px;background:#fff}
    th,td{border-bottom:1px solid var(--line-soft);padding:12px 10px;text-align:left;vertical-align:top}
    th{background:#fff7ed;color:#7c2d12;font-weight:600}
    tr:last-child td{border-bottom:0}
    input,textarea,select,button{padding:9px 10px}
    input,textarea,select{width:100%;margin-top:6px;border:1px solid var(--line);border-radius:10px;font-size:14px;outline:0;transition:border-color .2s,box-shadow .2s;background:#fff}
    input:focus,textarea:focus,select:focus{border-color:#14b8a6;box-shadow:0 0 0 3px rgba(20,184,166,.14)}
    button{border:0;border-radius:10px;background:linear-gradient(120deg,var(--primary),var(--primary-2));color:#fff;font-weight:600;cursor:pointer;box-shadow:0 10px 22px rgba(249,115,22,.25);transition:transform .14s ease,box-shadow .2s ease}
    button:hover{transform:translateY(-1px);box-shadow:0 14px 30px rgba(249,115,22,.34)}
    button:active{transform:translateY(0)}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .actions button{margin-right:8px;background:linear-gradient(120deg,#ef4444,var(--danger));box-shadow:0 8px 18px rgba(239,68,68,.24)}
    .actions button:hover{box-shadow:0 12px 24px rgba(239,68,68,.32)}
    form button[type="submit"]{margin-top:10px}
    @media (max-width:960px){.wrap{flex-direction:column}.side{width:100%;box-shadow:none}.main{padding:16px}.topbar{flex-direction:column;align-items:flex-start}.grid{grid-template-columns:1fr}}
  </style>
</head>
<body>
<div class="wrap">
  <aside class="side">
    <h2>后台管理</h2>
    <a href="?tab=goods">商品管理</a>
    <a href="?tab=orders">订单管理</a>
    <a href="?tab=users">用户管理</a>
    <a href="logout.php">退出登录</a>
  </aside>
  <main class="main">
    <div class="topbar">
      <h1>商城管理系统</h1>
      <div>当前管理员：<?php echo htmlspecialchars((string) ($_SESSION['admin']['username'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
    </div>

    <?php if ($tab === 'goods'): ?>
      <div class="card">
        <h3>添加 / 修改商品</h3>
        <form method="post">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="id" value="0">
          <div class="grid">
            <label>名称<input name="name" required></label>
            <label>价格<input name="price" type="number" step="0.01" required></label>
            <label>封面图<input name="cover"></label>
            <label>库存<input name="stock" type="number" required></label>
            <label>分类ID<input name="category_id" type="number" value="1"></label>
          </div>
          <label>详情<textarea name="detail" rows="4"></textarea></label>
          <button type="submit">保存商品</button>
        </form>
      </div>
      <div class="card">
        <h3>商品列表</h3>
        <table>
          <tr><th>ID</th><th>名称</th><th>价格</th><th>库存</th><th>操作</th></tr>
          <?php foreach ($goods as $item): ?>
            <tr>
              <td><?php echo (int) $item['id']; ?></td>
              <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars((string) $item['price'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo (int) $item['stock']; ?></td>
              <td class="actions">
                <form method="post" style="display:inline">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <button type="submit">删除</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php elseif ($tab === 'orders'): ?>
      <div class="card">
        <h3>订单列表</h3>
        <table>
          <tr><th>ID</th><th>订单号</th><th>总价</th><th>状态</th><th>操作</th></tr>
          <?php foreach ($orders as $item): ?>
            <tr>
              <td><?php echo (int) $item['id']; ?></td>
              <td><?php echo htmlspecialchars($item['order_no'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars((string) $item['total_price'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($statusMap[(int) $item['status']] ?? '未知', ENT_QUOTES, 'UTF-8'); ?></td>
              <td>
                <form method="post">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <select name="status">
                    <option value="0" <?php echo (int) $item['status'] === 0 ? 'selected' : ''; ?>>待付款</option>
                    <option value="1" <?php echo (int) $item['status'] === 1 ? 'selected' : ''; ?>>待发货</option>
                    <option value="2" <?php echo (int) $item['status'] === 2 ? 'selected' : ''; ?>>已完成</option>
                  </select>
                  <button type="submit">更新状态</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php else: ?>
      <div class="card">
        <h3>用户列表</h3>
        <table>
          <tr><th>ID</th><th>账号</th><th>昵称</th><th>OpenID</th><th>操作</th></tr>
          <?php foreach ($users as $item): ?>
            <tr>
              <td><?php echo (int) $item['id']; ?></td>
              <td><?php echo htmlspecialchars((string) ($item['username'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($item['nickname'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($item['openid'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td>
                <form method="post">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <button type="submit">删除</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>
  </main>
</div>
</body>
</html>