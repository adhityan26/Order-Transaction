<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\ShippingPackage;
use Illuminate\Http\Request;

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

    /**
     * List Shipping Package
     * @api GET /shippingPackage
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingPackage",
     *     description="List Shipping Package",
     *     tags={"/shippingPackage"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="code", in="query", description="Package Code", required=false, type="string"),
     *     @SWG\Parameter(name="shipping_vendor_id", in="query", description="Vendor ID", required=false, type="string"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success")
     * )
     */
    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["code", "shipping_vendor_id"]);
        $shippingPackage = ShippingPackage::getList($param, $page, $limit, ["code", "shipping_package_id"]);
        return response()->json($shippingPackage);
    }

    /**
     * Create new Shipping Package
     * @api POST /shippingPackage
     * @return json
     *
     * @SWG\Post(
     *     path="/shippingPackage",
     *     description="Create new Shipping Package",
     *     tags={"/shippingPackage"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="shipping_vendor_id", in="formData", description="Vendor ID", required=true, type="number"),
     *     @SWG\Parameter(name="code", in="formData", description="Package Name", required=true, type="string"),
     *     @SWG\Parameter(name="description", in="formData", description="Description", required=true, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
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

    /**
     * Update existing Shipping Package
     * @api Put /shippingPackage/{id}
     * @return json
     *
     * @SWG\Put(
     *     path="/shippingPackage/{id}",
     *     description="Update existing Shipping Package",
     *     tags={"/shippingPackage"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Package ID", required=true, type="number"),
     *     @SWG\Parameter(name="code", in="formData", description="Package Name", required=true, type="string"),
     *     @SWG\Parameter(name="description", in="formData", description="Description", required=true, type="string"),
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
        $shippingPackage = ShippingPackage::updateData($id, $param);
        return response()->json($shippingPackage);
    }

    /**
     * Delete existing Shipping Package
     * @api Delete /shippingPackage/{id}
     * @return json
     *
     * @SWG\Delete(
     *     path="/shippingPackage/{id}",
     *     description="Delete existing Shipping Package",
     *     tags={"/shippingPackage"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Package ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function remove(Request $req, $id) {
        $shippingPackage = ShippingPackage::removeData($id);
        return response()->json($shippingPackage);
    }

    /**
     * Change Shipping Package Status
     * @api get /shippingPackage/{id}/status
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingPackage/{id}/status",
     *     description="Change Package Status status",
     *     tags={"/shippingPackage"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Package ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function changeStatus(Request $req, $id) {
        $shippingPackage = ShippingPackage::changeStatus($id);
        return response()->json($shippingPackage);
    }

    /**
     * View Shipping Package detail
     * @api get /shippingPackage/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingPackage/{id}",
     *     description="View Shipping Package detail",
     *     tags={"/shippingPackage"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Package ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $shippingPackage = ShippingPackage::find($id);
        return response()->json($shippingPackage);
    }
}
