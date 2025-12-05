<?php
// app/auth.php

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_user_name(): ?string
{
    return $_SESSION['user_name'] ?? null;
}

function current_user_role(): string
{
    return $_SESSION['user_role'] ?? 'USER';
}

function is_logged_in(): bool
{
    return current_user_id() !== null;
}

function is_admin(): bool
{
    return is_logged_in() && current_user_role() === 'ADMIN';
}
