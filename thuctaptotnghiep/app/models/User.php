<?php
// app/models/User.php

require_once __DIR__ . '/../db.php';

class User
{
    public static function findByEmail(string $email)
    {
        $stmt = db()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function find(int $id)
    {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create(array $data)
    {
        $stmt = db()->prepare("
            INSERT INTO users (email, password_hash, name, phone, role, is_active)
            VALUES (:email, :password_hash, :name, :phone, 'USER', 1)
        ");
        $stmt->execute([
            ':email'         => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':name'          => $data['name'] ?? null,
            ':phone'         => $data['phone'] ?? null,
        ]);
        return (int)db()->lastInsertId();
    }
}
