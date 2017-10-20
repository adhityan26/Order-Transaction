<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {$this->middleware('auth');
        $this->middleware('admin', ['only' => ['confirmDelivery', 'confirmOrder', 'rejectOrder', 'cancelOrder', 'remove']]);
    }

    /**
     * List Order
     * @api GET /order
     * @return json
     *
     * @SWG\Get(
     *     path="/order",
     *     description="List Order",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="order_number", in="query", description="User ID cart owner", required=false, type="string"),
     *     @SWG\Parameter(name="email", in="query", description="Product exist on cart", required=false, type="string"),
     *     @SWG\Parameter(name="phone_number", in="query", description="Product exist on cart", required=false, type="string"),
     *     @SWG\Parameter(name="user_id", in="query", description="Product exist on cart", required=false, type="number"),
     *     @SWG\Parameter(name="status", in="query", description="Product exist on cart", required=false, type="number"),
     *     @SWG\Parameter(name="tracking_no", in="query", description="Product exist on cart", required=false, type="string"),
     *     @SWG\Parameter(name="coupon_code", in="query", description="Product exist on cart", required=false, type="string"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success")
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["order_number", "email", "phone_number", "user_id", "status", "tracking_no", "coupon_code"]);
        $user = Auth::user();

        if (!$user->admin) {
            $param["user_id"] = $user->id;
        }

        $order = Order::getList($param, $page, $limit, ["order_number", "email", "phone_number", "user_id", "status", "tracking_no", "coupon_code"]);
        return response()->json($order);
    }

    /**
     * Create new Order
     * @api POST /order
     * @return json
     *
     * @SWG\Post(
     *     path="/order",
     *     description="Create new Order",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="name", in="formData", description="Coupon Name", required=true, type="string"),
     *     @SWG\Parameter(name="email", in="formData", description="Coupon Name", required=true, type="string"),
     *     @SWG\Parameter(name="phone_number", in="formData", description="Coupon Name", required=true, type="string"),
     *     @SWG\Parameter(name="address", in="formData", description="Coupon Name", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_method", in="formData", description="Coupon Name", required=true, type="string"),
     *     @SWG\Parameter(name="coupon_code", in="formData", description="Coupon Name", required=true, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function store(Request $req) {
        $this->validate($req, [
            "shipping_method" => "required|exists:shipping_costs,id",
            "address" => "required",
        ]);

        $param = $req->only(["name", "email", "phone_number", "address", "shipping_method", "coupon_code"]);
        $user = Auth::user();
        $param["user_id"] = $user->id;

        if (empty($param["name"])) {
            $param["name"] = $user->name;
        }

        if (empty($param["email"])) {
            $param["email"] = $user->email;
        }

        if (empty($param["phone_number"])) {
            $param["phone_number"] = $user->phone_number;
        }

        $param["status"] = Status::NEW;

        $order = Order::createOrder($user, $param);
        return response()->json($order);
    }

    /**
     * View Order detail
     * @api get /order/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/order/{id}",
     *     description="View Order detail",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Order ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $user = Auth::user();
        $order = Order::query()->where("id", $id);
        if (!$user->admin) {
            $order->where("user_id", $user->id);
        }
        $order->with(["product", "coupon", "shippingPackage"]);
        $order = $order->first();
        return response()->json($order);
    }

    /**
     * Confirm Order
     * @api get /order/{id}/confirm
     * @return json
     *
     * @SWG\Get(
     *     path="/order/{id}/confirm",
     *     description="Confirm Order",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Order ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function confirmOrder(Request $req, $id) {
        $order = Order::changeOrderStatus($id, Status::CONFIRMED);
        return response()->json($order);
    }

    /**
     * Reject Order
     * @api get /order/{id}/reject
     * @return json
     *
     * @SWG\Get(
     *     path="/order/{id}/reject",
     *     description="Reject Order",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Order ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function rejectOrder(Request $req, $id) {
        $order = Order::changeOrderStatus($id, Status::REJECTED);
        return response()->json($order);
    }

    /**
     * Cancel Order
     * @api get /order/{id}/cancel
     * @return json
     *
     * @SWG\Get(
     *     path="/order/{id}/cancel",
     *     description="Cancel Order",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Order ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function cancelOrder(Request $req, $id) {
        $order = Order::changeOrderStatus($id, Status::CANCELED);
        return response()->json($order);
    }

    /**
     * Ship Order
     * @api get /order/{id}/ship
     * @return json
     *
     * @SWG\Get(
     *     path="/order/{id}/ship",
     *     description="Ship Order",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Order ID", required=true, type="number"),
     *     @SWG\Parameter(name="awb", in="query", description="Tracking Number", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function shipOrder(Request $req, $id) {
        $this->validate($req, [
            "awb" => "required"
        ]);

        $awb = $req->input("awb");
        $order = Order::changeOrderStatus($id, Status::SHIPPED, $awb);
        return response()->json($order);
    }

    /**
     * Confirm Delivery Order
     * @api get /order/{id}/delivered
     * @return json
     *
     * @SWG\Get(
     *     path="/order/{id}/delivered",
     *     description="Cancel Order",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Order ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function confirmDeliveryOrder(Request $req, $id) {
        $order = Order::changeOrderStatus($id, Status::DELIVERED);
        return response()->json($order);
    }
}
