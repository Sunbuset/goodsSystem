<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $userId = (int) request_data('user_id', 0);
    $goodsList = request_data('goodsList', []);
    if (!is_array($goodsList) || count($goodsList) === 0) {
        json_fail('订单商品不能为空');
    }
    if ($userId <= 0) {
        json_fail('用户未登录');
    }

    $pdo = db();
    $totalPrice = 0.0;
    $goodsInfo = [];

    foreach ($goodsList as $item) {
        $goodsId = (int) ($item['id'] ?? 0);
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        $stmt = $pdo->prepare('SELECT id, name, price, stock FROM goods WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $goodsId]);
        $goods = $stmt->fetch();
        if (!$goods) {
            json_fail('商品不存在');
        }
        if ((int) $goods['stock'] < $quantity) {
            json_fail($goods['name'] . ' 库存不足');
        }
        $totalPrice += (float) $goods['price'] * $quantity;
        $goodsInfo[] = [
            'id' => (int) $goods['id'],
            'name' => $goods['name'],
            'price' => $goods['price'],
            'quantity' => $quantity,
        ];

        $update = $pdo->prepare('UPDATE goods SET stock = stock - :quantity WHERE id = :id');
        $update->execute(['quantity' => $quantity, 'id' => $goodsId]);
    }

    $orderNo = 'OD' . date('YmdHis') . random_int(1000, 9999);
    $insert = $pdo->prepare('INSERT INTO orders (order_no, user_id, goods_info, total_price, status, create_time) VALUES (:order_no, :user_id, :goods_info, :total_price, :status, :create_time)');
    $insert->execute([
        'order_no' => $orderNo,
        'user_id' => $userId,
        'goods_info' => json_encode($goodsInfo, JSON_UNESCAPED_UNICODE),
        'total_price' => number_format($totalPrice, 2, '.', ''),
        'status' => 0,
        'create_time' => now_string(),
    ]);

    $goodsIds = array_values(array_unique(array_map(static function ($item) {
        return (int) ($item['id'] ?? 0);
    }, $goodsList)));
    $goodsIds = array_filter($goodsIds, static function ($value) {
        return $value > 0;
    });

    if (count($goodsIds) > 0) {
        $placeholders = implode(',', array_fill(0, count($goodsIds), '?'));
        $deleteCart = $pdo->prepare('DELETE FROM cart WHERE user_id = ? AND goods_id IN (' . $placeholders . ')');
        $deleteCart->execute(array_merge([$userId], $goodsIds));
    }

    json_ok(['order_no' => $orderNo], '下单成功');
} catch (Throwable $e) {
    json_fail('创建订单失败：' . $e->getMessage());
}