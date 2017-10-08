<?php namespace App\Model;

use App\Exceptions\AppException;
use Illuminate\Support\Facades\DB;

class Order extends BaseModel
{
    protected $fillable = ['order_number', 'user_id', 'email', 'phone_number', 'grand_total', 'shipping_cost', 'shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_destination_address', 'coupon_id', 'discount_value', 'status'];

    public static function getList($params = [], $page = 1, $perPage = 10, $searchColumn = []) {
        $model = self::query();

        foreach ($searchColumn as $col) {
            if (isset($params[$col])) {
                if ($col == "order_number") {
                    $model->where($col, "like", "%" .$params[$col] . "%");
                } else {
                    $model->where($col, $params[$col]);
                }
            }
        }

        return $model->paginate($perPage, ['*'], "page", $page);
    }

    public static function changeStatus($id, $status) {
        $model = self::find($id);
        $model->status = $status;
        return $model->save();
    }

    public static function createData($user, $data) {
        DB::beginTransaction();
        $cart_item = Cart::query();
        $cart_item->where("user_id", $user->id);

        if ($cart_item->count() == 0) {
            DB::rollback();
            throw new AppException("No item found on cart");
        }

        $model = self::query();
        $order = $model->create($data);

        $items = $cart_item->get();

        foreach ($items as $item) {
            $order_item = new OrderItem();
        }

        if (!$order) {
            DB::rollback();
        }
        DB::commit();

        return $order;
    }
}
?>