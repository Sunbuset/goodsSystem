<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';

try {
    $userId = (int) request_data('user_id', 0);
    $goodsId = (int) request_data('goods_id', 0);
    $quantity = max(1, (int) request_data('quantity', 1));
    $checked = (int) request_data('checked', 1) === 1 ? 1 : 0;

    if ($userId <= 0) {
        json_fail('用户未登录');
    }
    if ($goodsId <= 0) {
        json_fail('商品不存在');
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT id FROM goods WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $goodsId]);
    if (!$stmt->fetch()) {
        json_fail('商品不存在');
    }

    $stmt = $pdo->prepare('SELECT id, quantity FROM cart WHERE user_id = :user_id AND goods_id = :goods_id LIMIT 1');
    $stmt->execute(['user_id' => $userId, 'goods_id' => $goodsId]);
    $cart = $stmt->fetch();

    if ($cart) {
        $update = $pdo->prepare('UPDATE cart SET quantity = :quantity, checked = :checked, update_time = :update_time WHERE id = :id');
        $update->execute([
            'quantity' => $quantity,
            'checked' => $checked,
            'update_time' => now_string(),
            'id' => (int) $cart['id'],
        ]);
    } else {
        $insert = $pdo->prepare('INSERT INTO cart (user_id, goods_id, quantity, checked, create_time, update_time) VALUES (:user_id, :goods_id, :quantity, :checked, :create_time, :update_time)');
        $insert->execute([
            'user_id' => $userId,
            'goods_id' => $goodsId,
            'quantity' => $quantity,
            'checked' => $checked,
            'create_time' => now_string(),
            'update_time' => now_string(),
        ]);
    }

    json_ok(null, '已保存购物车');
} catch (Throwable $e) {
    json_fail('保存购物车失败：' . $e->getMessage());
}
