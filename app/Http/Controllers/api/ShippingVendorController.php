<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\ShippingVendor;
use Illuminate\Http\Request;

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

    /**
     * List Shipping Vendor
     * @api GET /shippingVendor
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingVendor",
     *     description="List Shipping Vendor",
     *     tags={"/shippingVendor"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="name", in="query", description="User ID cart owner", required=false, type="string"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success")
     * )
     */
    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["name"]);
        $shippingVendor = ShippingVendor::getList($param, $page, $limit, ["name"]);
        return response()->json($shippingVendor);
    }

    /**
     * Create new Shipping Vendor
     * @api POST /shippingVendor
     * @return json
     *
     * @SWG\Post(
     *     path="/shippingVendor",
     *     description="Create new Shipping Vendor",
     *     tags={"/shippingVendor"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="name", in="formData", description="Vendor Name", required=true, type="string"),
     *     @SWG\Parameter(name="track_url", in="formData", description="Tracking site URL", required=true, type="string"),
     *     @SWG\Parameter(name="address", in="formData", description="Vendor Address", required=true, type="string"),
     *     @SWG\Parameter(name="phone_number", in="formData", description="Vendor Phone", required=true, type="string"),
     *     @SWG\Parameter(name="notes", in="formData", description="Notes", required=false, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
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

    /**
     * Update existing Shipping Vendor
     * @api Put /shippingVendor/{id}
     * @return json
     *
     * @SWG\Put(
     *     path="/shippingVendor/{id}",
     *     description="Update existing Shipping Vendor",
     *     tags={"/shippingVendor"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Vendor ID", required=true, type="number"),
     *     @SWG\Parameter(name="track_url", in="formData", description="Tracking site URL", required=false, type="string"),
     *     @SWG\Parameter(name="address", in="formData", description="Vendor Address", required=false, type="string"),
     *     @SWG\Parameter(name="phone_number", in="formData", description="Vendor Phone", required=false, type="string"),
     *     @SWG\Parameter(name="notes", in="formData", description="Notes", required=false, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function update(Request $req, $id) {
        $param = $req->only(['track_url', 'address', 'phone_number', 'notes']);
        $shippingVendor = ShippingVendor::updateData($id, $param);
        return response()->json($shippingVendor);
    }

    /**
     * Delete existing Shipping Vendor
     * @api Delete /shippingVendor/{id}
     * @return json
     *
     * @SWG\Delete(
     *     path="/shippingVendor/{id}",
     *     description="Delete existing Shipping Vendor",
     *     tags={"/shippingVendor"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Vendor ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function remove(Request $req, $id) {
        $shippingVendor = ShippingVendor::removeData($id);
        return response()->json($shippingVendor);
    }

    /**
     * Change Shipping Vendor Status
     * @api get /shippingVendor/{id}/status
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingVendor/{id}/status",
     *     description="Change Vendor Status status",
     *     tags={"/shippingVendor"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Vendor ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function changeStatus(Request $req, $id) {
        $shippingVendor = ShippingVendor::changeStatus($id);
        return response()->json($shippingVendor);
    }

    /**
     * View Shipping Vendor detail
     * @api get /shippingVendor/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/shippingVendor/{id}",
     *     description="View Shipping Vendor detail",
     *     tags={"/shippingVendor"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Vendor ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $shippingVendor = ShippingVendor::find($id);
        return response()->json($shippingVendor);
    }
}
