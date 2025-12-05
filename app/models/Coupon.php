<?php
// app/models/Coupon.php

require_once __DIR__ . '/../db.php';

class Coupon
{
    public static function findByCode(string $code)
    {
        $stmt = db()->prepare("
            SELECT *
            FROM coupons
            WHERE code = ?
              AND is_active = 1
              AND (valid_from IS NULL OR valid_from <= CURRENT_DATE())
              AND (valid_to IS NULL OR valid_to >= CURRENT_DATE())
            LIMIT 1
        ");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }

    public static function calcDiscount(array $coupon, float $subtotal): float
    {
        if ($subtotal < (float)$coupon['min_order']) {
            return 0;
        }

        if ($coupon['type'] === 'percent') {
            $discount = $subtotal * ((float)$coupon['value'] / 100.0);
        } else {
            $discount = (float)$coupon['value']; // fixed
        }

        if ($discount > $subtotal) {
            $discount = $subtotal;
        }
        return $discount;
    }
}
