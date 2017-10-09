<?php
namespace App\Constant;

class Status
{
    const NEW = 1;
    const CONFIRMED = 2;
    const CANCELED = 3;
    const SHIPPED = 4;
    const DELIVERED = 5;
    const REJECTED = 0;

    public static function getName($status) {
        switch ($status) {
            case self::NEW: return "New"; break;
            case self::CONFIRMED: return "Confirmed"; break;
            case self::CANCELED: return "Canceled"; break;
            case self::REJECTED: return "Rejected"; break;
            case self::SHIPPED: return "Shipped"; break;
            case self::DELIVERED: return "Delivered"; break;
        }
    }
}