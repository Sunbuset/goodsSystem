<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';

try {
    $userId = (int) request_data('user_id', 0);
    if ($userId <= 0) {
        json_fail('用户未登录');
    }

    $sql = 'SELECT c.id, c.user_id, c.goods_id, c.quantity, c.checked, c.create_time, c.update_time,
                   g.name, g.price, g.cover, g.stock
            FROM cart c
            INNER JOIN goods g ON g.id = c.goods_id
            WHERE c.user_id = :user_id
            ORDER BY c.id DESC';
    $stmt = db()->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $list = [];

    foreach ($stmt->fetchAll() as $row) {
        $list[] = [
            'id' => (int) $row['id'],
            'user_id' => (int) $row['user_id'],
            'goods_id' => (int) $row['goods_id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'cover' => $row['cover'],
            'stock' => (int) $row['stock'],
            'quantity' => (int) $row['quantity'],
            'checked' => (int) $row['checked'] === 1,
            'create_time' => $row['create_time'],
            'update_time' => $row['update_time'],
        ];
    }

    json_ok(['list' => $list]);
} catch (Throwable $e) {
    json_fail('获取购物车失败：' . $e->getMessage());
}
