<?php
// app/models/User.php

require_once __DIR__ . '/../db.php';

class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = db();
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password_hash, name, phone, role, is_active)
            VALUES (:email, :password_hash, :name, :phone, :role, :is_active)
        ");
        $stmt->execute([
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':name'          => $data['name'] ?? null,
            ':phone'         => $data['phone'] ?? null,
            ':role'          => $data['role'] ?? 'USER',
            ':is_active'     => $data['is_active'] ?? 1,
        ]);

        return (int)$pdo->lastInsertId();
    }

    /** Tạo token reset mật khẩu, lưu vào bảng password_resets */
    public static function createResetToken(int $userId): string
{
    $pdo   = db();
    $token = bin2hex(random_bytes(32)); // 64 ký tự hex, an toàn

    // Xoá token cũ của user, nếu có
    $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$userId]);

    // Dùng luôn NOW() của MySQL và cộng thêm 1 giờ
    $stmt = $pdo->prepare("
        INSERT INTO password_resets (user_id, token, expires_at, created_at)
        VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR), NOW())
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':token'   => $token,
    ]);

    return $token;
}

    /** Tìm user theo reset token (chỉ token còn hạn) */
    public static function findByResetToken(string $token): ?array
{
    $pdo = db();
    $stmt = $pdo->prepare("
        SELECT u.*
        FROM password_resets pr
        JOIN users u ON u.id = pr.user_id
        WHERE pr.token = :token
          AND pr.expires_at > NOW()
        LIMIT 1
    ");
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch();
    return $row ?: null;
}


    public static function clearResetToken(int $userId): void
    {
        $pdo = db();
        $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$userId]);
    }

    public static function updatePassword(int $userId, string $newHash): void
    {
        $pdo = db();
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newHash, $userId]);
    }
}
