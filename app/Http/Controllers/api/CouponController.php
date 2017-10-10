<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
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
        $param = $request->only(['code', 'name', 'valid_from', 'valid_to']);
        $param["limit"] = $request->input("limit_coupon");
        $coupon = Coupon::getList($param, $page, $limit, ["code"]);
        return response()->json($coupon);
    }

    public function store(Request $req) {
        $this->validate($req, [
            "code" => "required|unique:coupons,code",
            "name" => "required",
            "valid_from" => "date",
            "valid_to" => "date",
            "coupon_value" => "integer",
            "coupon_percentage" => "integer",
            "limit" => "integer",
            "limit_terms" => "integer",
        ]);
        $param = $req->only(['code', 'name', 'desc', 'valid_from', 'valid_to', 'coupon_value', 'coupon_percentage', 'limit', 'limit_terms']);
        $coupon = Coupon::createData($param);
        return response()->json($coupon);
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
        $coupon = Coupon::updateData($id, $param);
        return response()->json($coupon);
    }

    public function remove(Request $req, $id) {
        $coupon = Coupon::removeData($id);
        return response()->json($coupon);
    }

    public function changeStatus(Request $req, $id) {
        $coupon = Coupon::changeStatus($id);
        return response()->json($coupon);
    }

    public function show(Request $req, $id) {
        $coupon = Coupon::getDetail($id);
        return response()->json($coupon);
    }

    public function mapCouponCategory(Request $req) {
        $this->validate($req, [
            "product_id" => "required",
            "coupon_id" => "required"
        ]);

        $param = $req->only("category_id", "coupon_id");
        $couponCategory = CouponCategory::createData($param);
        return response()->json($couponCategory);
    }

    public function viewCouponDiscount(Request $req, $coupon_code) {
        $user = Auth::user();

        return response()->json(Coupon::calculateCoupon($user, $coupon_code));
    }
}
