<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Coupon;
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
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->middleware('admin', ['only' => ['store', 'update']]);
    }

    /**
     * List Coupon
     * @api GET /coupon
     * @return json
     *
     * @SWG\Get(
     *     path="/coupon",
     *     description="List coupon",
     *     tags={"/coupon"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="code", in="query", description="Coupon Code", required=false, type="string"),
     *     @SWG\Parameter(name="name", in="query", description="Coupon Name", required=false, type="string"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success")
     * )
     */
    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(['code', 'name']);
        $coupon = Coupon::getList($param, $page, $limit, ["code", "name"]);
        return response()->json($coupon);
    }

    /**
     * Create new Coupon
     * @api POST /coupon
     * @return json
     *
     * @SWG\Post(
     *     path="/coupon",
     *     description="Create new Coupon",
     *     tags={"/coupon"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="code", in="formData", description="Coupon Code", required=true, type="string"),
     *     @SWG\Parameter(name="name", in="formData", description="Coupon Name", required=true, type="string"),
     *     @SWG\Parameter(name="valid_from", in="formData", description="Coupon Valid From (YYYY/MM/DD)", required=true, type="string"),
     *     @SWG\Parameter(name="valid_to", in="formData", description="Coupon Valid To (YYYY/MM/DD)", required=true, type="string"),
     *     @SWG\Parameter(name="coupon_value", in="formData", description="Coupon Value", required=true, type="number"),
     *     @SWG\Parameter(name="coupon_percentage", in="formData", description="Coupon Percentage (in %)", required=true, type="number"),
     *     @SWG\Parameter(name="limit", in="formData", description="Coupon limit (0 mean unlimited)", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
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

    /**
     * Update existing Coupon
     * @api Put /coupon/{id}
     * @return json
     *
     * @SWG\Put(
     *     path="/coupon/{id}",
     *     description="Update existing Coupon",
     *     tags={"/coupon"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Coupon ID", required=true, type="number"),
     *     @SWG\Parameter(name="name", in="formData", description="Coupon Name", required=false, type="string"),
     *     @SWG\Parameter(name="valid_from", in="formData", description="Coupon Valid From (YYYY/MM/DD)", required=false, type="string"),
     *     @SWG\Parameter(name="valid_to", in="formData", description="Coupon Valid To (YYYY/MM/DD)", required=false, type="string"),
     *     @SWG\Parameter(name="coupon_value", in="formData", description="Coupon Value", required=false, type="number"),
     *     @SWG\Parameter(name="coupon_percentage", in="formData", description="Coupon Percentage (in %)", required=false, type="number"),
     *     @SWG\Parameter(name="limit", in="formData", description="Coupon limit (0 mean unlimited)", required=false, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
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

    /**
     * Delete existing Coupon
     * @api Delete /coupon/{id}
     * @return json
     *
     * @SWG\Delete(
     *     path="/coupon/{id}",
     *     description="Delete existing Coupon",
     *     tags={"/coupon"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Coupon ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function remove(Request $req, $id) {
        $coupon = Coupon::removeData($id);
        return response()->json($coupon);
    }

    /**
     * Change Coupon Status
     * @api get /coupon/{id}/status
     * @return json
     *
     * @SWG\Get(
     *     path="/coupon/{id}/status",
     *     description="Change Coupon status",
     *     tags={"/coupon"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Coupon ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function changeStatus(Request $req, $id) {
        $coupon = Coupon::changeStatus($id);
        return response()->json($coupon);
    }

    /**
     * View Coupon detail
     * @api get /coupon/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/coupon/{id}",
     *     description="View Coupon detail",
     *     tags={"/coupon"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Coupon ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $coupon = Coupon::getDetail($id);
        return response()->json($coupon);
    }

//    public function mapCouponCategory(Request $req) {
//        $this->validate($req, [
//            "product_id" => "required",
//            "coupon_id" => "required"
//        ]);
//
//        $param = $req->only("category_id", "coupon_id");
//        $couponCategory = CouponCategory::createData($param);
//        return response()->json($couponCategory);
//    }

    /**
     * Preview Coupon detail by product in cart
     * @api get /coupon/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/coupon/preview/{coupon_code}",
     *     description="Preview Coupon detail by product in cart",
     *     tags={"/coupon"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="coupon_code", in="path", description="Coupon Code", required=true, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function viewCouponDiscount(Request $req, $coupon_code) {
        $user = Auth::user();

        return response()->json(Coupon::calculateCoupon($user, $coupon_code));
    }
}
