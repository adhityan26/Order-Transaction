<?php namespace App\Model;

use App\Constant\Status;
use App\Exceptions\AppException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Order extends BaseModel
{
    protected $fillable = ['order_number', 'user_id', 'email', 'phone_number', 'grand_total', 'shipping_cost', 'shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_destination_address', 'coupon_id', 'discount_value', 'status', 'name', 'tracking_no'];

    public function product()
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, "coupon_id", "id");
    }

    public function shippingPackage()
    {
        return $this->belongsTo(ShippingPackage::class, "shipping_package_id", "id");
    }

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

    public static function changeOrderStatus($id, $status, $awb = "") {
        DB::beginTransaction();
        try {
            $payment = Payment::query()->where("order_id", $id)->where("status", Status::CONFIRMED);

            if ($payment->count() == 0 && $status == Status::CONFIRMED) {
                throw new AppException("Payment not found or not confirmed");
            }

            if (in_array($status, [Status::CANCELED, Status::REJECTED])) {
                $payment->status = $status;
                $payment->save();
            }

            $model = self::find($id);

            if (in_array($model->status, [Status::REJECTED, Status::CANCELED, Status::DELIVERED])) {
                throw new AppException("Order already closed");
            }

            if (in_array($model->status, [Status::CONFIRMED, Status::SHIPPED]) && $status <= $model->status) {
                throw new AppException("Order cannot be changed to previous status");
            }

            if ($status == Status::SHIPPED && $model->status != Status::CONFIRMED) {
                throw new AppException("Order not ready to be shipped");
            }

            if ($status == Status::SHIPPED) {
                $model->tracking_no = $awb;
            }

            if ($status == Status::DELIVERED && $model->status != Status::SHIPPED) {
                throw new AppException("Order is not yet shipped");
            }

            $model->status = $status;
            $order = $model->save();
        } catch (\Exception $e) {
            DB::rollback();
            throw new AppException($e->getMessage());
        }

        DB::commit();
        return $order;
    }

    public static function createOrder($user, $data) {
        DB::beginTransaction();
        $cart_item = Cart::query();
        $cart_item->where("user_id", $user->id);

        if (empty($data["name"])) {
            throw new AppException("Name should not be empty");
        }

        if (empty($data["email"])) {
            throw new AppException("Email should not be empty");
        }

        if (empty($data["phone_number"])) {
            throw new AppException("Phone Number should not be empty");
        }

        if ($cart_item->count() == 0) {
            DB::rollback();
            throw new AppException("No item found on cart");
        }

        $model = self::query();
        if (isset($data["coupon_code"]) && !empty($data["coupon_code"])) {
            $discount = Coupon::calculateCoupon($user, $data["coupon_code"]);

            $data["coupon_id"] = $discount["id"];
            $data["discount_value"] = $discount["value"];
        }

        $shippingCost = ShippingCost::with("shippingPackage")->where("id", $data["shipping_method"])->first();

        if (!$shippingCost) {
            throw new AppException("Shipping method not found");
        }

        $data["shipping_origin_province"] = $shippingCost->shipping_origin_province;
        $data["shipping_origin_city"] = $shippingCost->shipping_origin_city;
        $data["shipping_origin_district"] = $shippingCost->shipping_origin_district;
        $data["shipping_destination_province"] = $shippingCost->shipping_destination_province;
        $data["shipping_destination_city"] = $shippingCost->shipping_destination_city;
        $data["shipping_destination_district"] = $shippingCost->shipping_destination_district;
        $data["shipping_cost"] = $shippingCost->cost;
        $data["shipping_package_id"] = $shippingCost->shippingPackage->id;
        $data["order_number"] = "PO-" . \Carbon\Carbon::now()->format("y-m-d") . "-" . str_pad((self::whereDate("created_at", \Carbon\Carbon::now()->format("Y/m/d"))->count() + 1),5,"0",STR_PAD_LEFT);

        $order = $model->create($data);

        $items = $cart_item->get();

        if (!$order) {
            DB::rollback();
        }

        try {
            $orderTotal = 0;
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->product_id = $item->product_id;
                $order_item->qty = $item->qty;
                $order_item->price = $item->price;
                $order_item->total = $item->qty * ($item->price - $product->discount);
                $orderTotal += $order_item->total;
                $order_item->save();
                $item->delete();
                $product->qty = $product->qty - $item->qty;
                if ($product->qty < 0) {
                    throw new \Exception("Product $product->name is out of stock");
                }
                $product->save();
            }

            $order->grand_total = $order->shipping_cost + $orderTotal - $order->discount_value;
            $order->save();
        } catch (\Exception $e) {
            DB::rollback();
            throw new AppException($e->getMessage());
        }

        DB::commit();

        return $order;
    }
}
?>