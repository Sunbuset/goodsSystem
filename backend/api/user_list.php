<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';

try {
    $stmt = db()->query('SELECT id, username, nickname, avatar, openid, create_time FROM user ORDER BY id DESC');
    json_ok(['list' => $stmt->fetchAll()]);
} catch (Throwable $e) {
    json_fail('获取用户列表失败：' . $e->getMessage());
}