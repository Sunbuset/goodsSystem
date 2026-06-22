<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $username = trim((string) request_data('username', ''));
    $password = trim((string) request_data('password', ''));

    if ($username === '' || $password === '') {
        json_fail('账号和密码不能为空');
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM user WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        $insert = $pdo->prepare('INSERT INTO user (username, password, nickname, avatar, openid, create_time) VALUES (:username, :password, :nickname, :avatar, :openid, :create_time)');
        $insert->execute([
            'username' => $username,
            'password' => $password,
            'nickname' => $username,
            'avatar' => '',
            'openid' => 'account_' . sha1($username),
            'create_time' => now_string(),
        ]);
        $user = [
            'id' => (int) $pdo->lastInsertId(),
            'username' => $username,
            'password' => $password,
            'nickname' => $username,
            'avatar' => '',
            'openid' => 'account_' . sha1($username),
        ];
    } elseif ((string) ($user['password'] ?? '') !== $password) {
        json_fail('账号或密码错误');
    }

    unset($user['password']);

    json_ok(['user' => $user], '登录成功');
} catch (Throwable $e) {
    json_fail('登录失败：' . $e->getMessage());
}