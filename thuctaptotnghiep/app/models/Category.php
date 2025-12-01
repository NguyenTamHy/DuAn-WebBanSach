<?php
// app/models/Category.php

require_once __DIR__ . '/../db.php';

class Category
{
    public static function all(): array
    {
        $stmt = db()->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug)
    {
        $stmt = db()->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function find(int $id)
    {
        $stmt = db()->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
