<?php namespace App\Model;

use App\Exceptions\AppException;

class Payment extends BaseModel
{
    protected $fillable = ['order_id', 'user_id', 'status', 'reference_no', 'user_account', 'user_bank_account', 'notes'];

    public static function changePaymentStatus($id, $status) {
        $model = self::find($id);
        $model->status = $status;
        return $model->save();
    }

    public static function createPayment($user, $data) {
        $order = Order::query();
        $order->where("user_id", $user->id)->where("id", $data["order_id"]);
        if ($order->count() == 0) {
            throw new AppException("Order not found");
        }
        $model = self::query();
        $data["user_id"] = $user->id;
        return $model->create($data);
    }
}
?>