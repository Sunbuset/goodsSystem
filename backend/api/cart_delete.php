<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';

try {
    $userId = (int) request_data('user_id', 0);
    $cartId = (int) request_data('cart_id', 0);

    if ($userId <= 0) {
        json_fail('用户未登录');
    }
    if ($cartId <= 0) {
        json_fail('购物车商品不存在');
    }

    $stmt = db()->prepare('DELETE FROM cart WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $cartId, 'user_id' => $userId]);

    json_ok(null, '已删除购物车商品');
} catch (Throwable $e) {
    json_fail('删除购物车失败：' . $e->getMessage());
}
