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
    body{font-family:Arial;background:#f5f7fb;margin:0;color:#1f2937}
    .wrap{display:flex;min-height:100vh}
    .side{width:220px;background:#111827;color:#fff;padding:24px;box-sizing:border-box}
    .side a{display:block;color:#cbd5e1;text-decoration:none;padding:10px 0}
    .main{flex:1;padding:24px}
    .card{background:#fff;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 10px 30px rgba(0,0,0,.06)}
    table{width:100%;border-collapse:collapse}
    th,td{border-bottom:1px solid #e5e7eb;padding:10px;text-align:left;vertical-align:top}
    input,textarea,select,button{padding:8px;box-sizing:border-box}
    input,textarea,select{width:100%;margin-top:6px}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .actions button{margin-right:8px}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
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