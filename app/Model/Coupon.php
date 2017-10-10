<?php namespace App\Model;

use App\Exceptions\AppException;
use Carbon\Carbon;

class Coupon extends BaseModel
{
    protected $fillable = ['code', 'name', 'desc', 'valid_from', 'valid_to', 'coupon_value', 'coupon_percentage', 'limit', 'limit_terms'];

    public static function calculateCoupon($user, $coupon_code) {
        $coupon = self::query();
        $coupon->where("code", $coupon_code);

        $coupon = $coupon->first();

        if ($coupon) {
            $cart_item = Cart::query();
            $cart_item->where("user_id", $user->id);

            if ($cart_item->count() == 0) {
                throw new AppException("No item found on cart");
            }

            $now = Carbon::now();
            $valid_from = new \Carbon\Carbon($coupon->valid_from);
            $valid_to = new \Carbon\Carbon($coupon->valid_to);
            $valid_to->setTime(23, 59, 59);

            if ($now->between($valid_from, $valid_to)) {
                if ($coupon->limit > 0) {
                    $orderCoupon = Order::where("coupon_id", $coupon->id)->count();
                    if ($orderCoupon > $coupon->limit) {
                        throw new AppException("Coupon is exceeding limit");
                    }
                }
                $cart_item->where("user_id", $user->id);

                $totalItem = $cart_item->sum("sub_total");
                $discountValue = 0;

                if ($coupon->coupon_percentage > 0) {
                    $discountValue = round($totalItem * $coupon->coupon_percentage / 100, PHP_ROUND_HALF_DOWN);
                    if ($coupon->coupon_value > 0 && $coupon->coupon_value < $discountValue) {
                        $discountValue = intval($coupon->coupon_value);
                    }
                }

                return [
                    "id" => $coupon->id,
                    "code" => $coupon->code,
                    "name" => $coupon->name,
                    "value" => $discountValue
                ];
            } else {
                throw new AppException("Coupon is expired");
            }
        } else {
            throw new AppException("Coupon not found");
        }
    }
}
?>