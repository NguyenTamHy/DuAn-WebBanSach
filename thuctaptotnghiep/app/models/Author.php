<?php
// app/models/Author.php

require_once __DIR__ . '/../db.php';

class Author
{
    public static function all(): array
    {
        $stmt = db()->query("SELECT * FROM authors ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public static function find(int $id)
    {
        $stmt = db()->prepare("SELECT * FROM authors WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
