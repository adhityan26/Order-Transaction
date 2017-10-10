<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Product;
use App\Model\ShippingVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingVendorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'viewCouponDiscount']]);
        $this->middleware('admin', ['only' => ['store', 'update']]);
    }

    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["name"]);
        $shippingVendor = ShippingVendor::getList($param, $page, $limit, ["name"]);
        return response()->json($shippingVendor);
    }

    public function store(Request $req) {
        $this->validate($req, [
            "name" => "required",
            "track_url" => "required",
            "address" => "required",
            "phone_number" => "required"
        ]);
        $param = $req->only(['name', 'track_url', 'address', 'phone_number', 'notes']);
        $param["status"] = Status::NEW;
        $shippingVendor = ShippingVendor::createData($param);
        return response()->json($shippingVendor);
    }

    public function update(Request $req, $id) {
        $param = $req->only(['track_url', 'address', 'phone_number', 'notes']);
        $shippingVendor = ShippingVendor::updateData($id, $param);
        return response()->json($shippingVendor);
    }

    public function remove(Request $req, $id) {
        $shippingVendor = ShippingVendor::removeData($id);
        return response()->json($shippingVendor);
    }

    public function changeStatus(Request $req, $id) {
        $shippingVendor = ShippingVendor::changeStatus($id);
        return response()->json($shippingVendor);
    }

    public function show(Request $req, $id) {
        $shippingVendor = ShippingVendor::find($id);
        return response()->json($shippingVendor);
    }
}
