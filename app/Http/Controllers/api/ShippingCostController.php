<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Product;
use App\Model\ShippingCost;
use App\Model\ShippingPackage;
use App\Model\ShippingVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingCostController extends Controller
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
        $param = $request->only(['shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_etd']);
        $shippingCost = ShippingCost::getList($param, $page, $limit, ['shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_etd']);
        return response()->json($shippingCost);
    }

    public function store(Request $req) {
        $this->validate($req, [
            "shipping_package_id" => "required|exists:shipping_packages,id",
            "shipping_origin_province" => "required",
            "shipping_origin_city" => "required",
            "shipping_origin_district" => "required",
            "shipping_destination_province" => "required",
            "shipping_destination_city" => "required",
            "shipping_destination_district" => "required",
            "shipping_etd" => "required|integer",
            "cost" => "required|integer"
        ]);
        $param = $req->only(['shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_etd', 'cost']);
        $param["status"] = Status::NEW;
        $shippingCost = ShippingCost::createData($param);
        return response()->json($shippingCost);
    }

    public function update(Request $req, $id) {
        $this->validate($req, [
            "shipping_origin_province" => "required",
            "shipping_origin_city" => "required",
            "shipping_origin_district" => "required",
            "shipping_destination_province" => "required",
            "shipping_destination_city" => "required",
            "shipping_destination_district" => "required",
            "shipping_etd" => "required|integer",
            "cost" => "required|integer"
        ]);
        $param = $req->only(['shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_etd', 'cost']);
        $shippingCost = ShippingCost::updateData($id, $param);
        return response()->json($shippingCost);
    }

    public function remove(Request $req, $id) {
        $shippingCost = ShippingCost::removeData($id);
        return response()->json($shippingCost);
    }

    public function changeStatus(Request $req, $id) {
        $shippingCost = ShippingCost::changeStatus($id);
        return response()->json($shippingCost);
    }

    public function show(Request $req, $id) {
        $shippingCost = ShippingCost::find($id);
        return response()->json($shippingCost);
    }
}
