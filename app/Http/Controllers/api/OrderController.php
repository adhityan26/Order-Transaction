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

    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["order_number", "email", "phone_number", "user_id", "status", "tracking_no"]);
        $user = Auth::user();

        if (!$user->admin) {
            $param["user_id"] = $user->id;
        }

        $order = Order::getList($param, $page, $limit, ["order_number", "email", "phone_number", "user_id", "status", "tracking_no"]);
        return response()->json($order);
    }

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

    public function show(Request $req, $id) {
        $order = Order::find($id);
        return response()->json($order);
    }

    public function confirmOrder(Request $req, $id) {
        $order = Order::changeOrderStatus($id, Status::CONFIRMED);
        return response()->json($order);
    }

    public function rejectOrder(Request $req, $id) {
        $order = Order::changeOrderStatus($id, Status::REJECTED);
        return response()->json($order);
    }

    public function cancelOrder(Request $req, $id) {
        $order = Order::changeOrderStatus($id, Status::CANCELED);
        return response()->json($order);
    }

    public function shipOrder(Request $req, $id) {
        $this->validate($req, [
            "awb" => "required"
        ]);

        $awb = $req->input("awb");
        $order = Order::changeOrderStatus($id, Status::SHIPPED, $awb);
        return response()->json($order);
    }

    public function confirmDeliveryOrder(Request $req, $id) {
        $order = Order::changeOrderStatus($id, Status::DELIVERED);
        return response()->json($order);
    }
}
