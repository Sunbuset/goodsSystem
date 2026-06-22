<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';

try {
    $userId = (int) request_data('user_id', 0);
    $cartId = (int) request_data('cart_id', 0);
    $quantityRaw = request_data('quantity', null);
    $checked = request_data('checked', null);

    if ($userId <= 0) {
        json_fail('用户未登录');
    }
    if ($cartId <= 0) {
        json_fail('购物车商品不存在');
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT id FROM cart WHERE id = :id AND user_id = :user_id LIMIT 1');
    $stmt->execute(['id' => $cartId, 'user_id' => $userId]);
    if (!$stmt->fetch()) {
        json_fail('购物车商品不存在');
    }

    $fields = [];
    $params = ['id' => $cartId, 'user_id' => $userId, 'update_time' => now_string()];

    if ($quantityRaw !== null) {
        $quantity = (int) $quantityRaw;
    }

    if (isset($quantity) && $quantity > 0) {
        $fields[] = 'quantity = :quantity';
        $params['quantity'] = $quantity;
    }

    if ($checked !== null) {
        $fields[] = 'checked = :checked';
        $params['checked'] = (int) $checked === 1 ? 1 : 0;
    }

    if (count($fields) === 0) {
        json_fail('没有可更新的内容');
    }

    $fields[] = 'update_time = :update_time';
    $sql = 'UPDATE cart SET ' . implode(', ', $fields) . ' WHERE id = :id AND user_id = :user_id';
    $update = $pdo->prepare($sql);
    $update->execute($params);

    json_ok(null, '已更新购物车');
} catch (Throwable $e) {
    json_fail('更新购物车失败：' . $e->getMessage());
}
