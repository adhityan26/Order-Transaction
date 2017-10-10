<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Product;
use App\Model\ShippingPackage;
use App\Model\ShippingVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingPackageController extends Controller
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
        $param = $request->only(["code", "shipping_package_id"]);
        $shippingPackage = ShippingPackage::getList($param, $page, $limit, ["code", "shipping_package_id"]);
        return response()->json($shippingPackage);
    }

    public function store(Request $req) {
        $this->validate($req, [
            "shipping_vendor_id" => "required|exists:shipping_vendors,id",
            "code" => "required",
            "description" => "required"
        ]);
        $param = $req->only(['shipping_vendor_id', 'code', 'description']);
        $param["status"] = Status::NEW;
        $shippingPackage = ShippingPackage::createData($param);
        return response()->json($shippingPackage);
    }

    public function update(Request $req, $id) {
        $this->validate($req, [
            "valid_from" => "date",
            "valid_to" => "date",
            "coupon_value" => "integer",
            "coupon_percentage" => "integer",
            "limit" => "integer",
            "limit_terms" => "integer",
        ]);
        $param = $req->only(['name', 'desc', 'valid_from', 'valid_to', 'coupon_value', 'coupon_percentage', 'limit', 'limit_terms']);
        $shippingPackage = ShippingPackage::updateData($id, $param);
        return response()->json($shippingPackage);
    }

    public function remove(Request $req, $id) {
        $shippingPackage = ShippingPackage::removeData($id);
        return response()->json($shippingPackage);
    }

    public function changeStatus(Request $req, $id) {
        $shippingPackage = ShippingPackage::changeStatus($id);
        return response()->json($shippingPackage);
    }

    public function show(Request $req, $id) {
        $shippingPackage = ShippingPackage::find($id);
        return response()->json($shippingPackage);
    }
}
