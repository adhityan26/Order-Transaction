<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\ShippingCost;
use Illuminate\Http\Request;

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

    /**
     * List Shipping Cost
     * @api GET /shippingCost
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingCost",
     *     description="List Shipping Cost",
     *     tags={"/shippingCost"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="shipping_package_id", in="query", description="Package ID", required=false, type="number"),
     *     @SWG\Parameter(name="shipping_origin_province", in="query", description="Province Origin", required=false, type="string"),
     *     @SWG\Parameter(name="shipping_origin_city", in="query", description="City Origin", required=false, type="string"),
     *     @SWG\Parameter(name="shipping_origin_district", in="query", description="District Origin", required=false, type="string"),
     *     @SWG\Parameter(name="shipping_destination_province", in="query", description="Province Destination", required=false, type="string"),
     *     @SWG\Parameter(name="shipping_destination_city", in="query", description="Province City", required=false, type="string"),
     *     @SWG\Parameter(name="shipping_destination_district", in="query", description="Province District", required=false, type="string"),
     *     @SWG\Parameter(name="shipping_etd", in="query", description="Shipping Estimation Date", required=false, type="number"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success")
     * )
     */
    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(['shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_etd']);
        $shippingCost = ShippingCost::getList($param, $page, $limit, ['shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_etd']);
        return response()->json($shippingCost);
    }

    /**
     * Create new Shipping Cost
     * @api POST /shippingCost
     * @return json
     *
     * @SWG\Post(
     *     path="/shippingCost",
     *     description="Create new Shipping Cost",
     *     tags={"/shippingCost"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="shipping_package_id", in="formData", description="Package ID", required=true, type="number"),
     *     @SWG\Parameter(name="shipping_origin_province", in="formData", description="Province Origin", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_origin_city", in="formData", description="City Origin", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_origin_district", in="formData", description="District Origin", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_destination_province", in="formData", description="Province Destination", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_destination_city", in="formData", description="Province City", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_destination_district", in="formData", description="Province District", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_etd", in="formData", description="Shipping Estimation Date", required=true, type="number"),
     *     @SWG\Parameter(name="cost", in="formData", description="Shipping Estimation Date", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
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

    /**
     * Update existing Shipping Cost
     * @api Put /shippingCost/{id}
     * @return json
     *
     * @SWG\Put(
     *     path="/shippingCost/{id}",
     *     description="Update existing Shipping Cost",
     *     tags={"/shippingCost"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Cost ID", required=true, type="number"),
     *     @SWG\Parameter(name="shipping_origin_province", in="formData", description="Province Origin", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_origin_city", in="formData", description="City Origin", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_origin_district", in="formData", description="District Origin", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_destination_province", in="formData", description="Province Destination", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_destination_city", in="formData", description="Province City", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_destination_district", in="formData", description="Province District", required=true, type="string"),
     *     @SWG\Parameter(name="shipping_etd", in="formData", description="Shipping Estimation Date", required=true, type="number"),
     *     @SWG\Parameter(name="cost", in="formData", description="Shipping Estimation Date", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function update(Request $req, $id) {
        $this->validate($req, [
            "shipping_etd" => "integer",
            "cost" => "integer"
        ]);
        $param = $req->only(['shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_etd', 'cost']);
        $shippingCost = ShippingCost::updateData($id, $param);
        return response()->json($shippingCost);
    }

    /**
     * Delete existing Shipping Cost
     * @api Delete /shippingCost/{id}
     * @return json
     *
     * @SWG\Delete(
     *     path="/shippingCost/{id}",
     *     description="Delete existing Shipping Cost",
     *     tags={"/shippingCost"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Cost ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function remove(Request $req, $id) {
        $shippingCost = ShippingCost::removeData($id);
        return response()->json($shippingCost);
    }

    /**
     * Change Shipping Cost Status
     * @api get /shippingCost/{id}/status
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingCost/{id}/status",
     *     description="Change Cost Status status",
     *     tags={"/shippingPackage"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Cost ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function changeStatus(Request $req, $id) {
        $shippingCost = ShippingCost::changeStatus($id);
        return response()->json($shippingCost);
    }

    /**
     * View Shipping Cost detail
     * @api get /shippingPackage/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingCost/{id}",
     *     description="View Shipping Cost detail",
     *     tags={"/shippingCost"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Cost ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $shippingCost = ShippingCost::find($id);
        return response()->json($shippingCost);
    }
}
