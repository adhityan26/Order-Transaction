<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->middleware('admin', ['only' => ['changeStatus', 'store', 'update', 'updateQty']]);
    }

    /**
     * List Product
     * @api GET /product
     * @return json
     *
     * @SWG\Get(
     *     path="/product",
     *     description="List Product",
     *     tags={"/product"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="sku", in="query", description="Product Stock Keeping Unit", required=false, type="string"),
     *     @SWG\Parameter(name="name", in="query", description="Product Name", required=false, type="string"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success")
     * )
     */
    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["sku", "name"]);
        $product = Product::getList($param, $page, $limit, ["sku", "name"]);
        return response()->json($product);
    }

    /**
     * Create new Product
     * @api POST /product
     * @return json
     *
     * @SWG\Post(
     *     path="/product",
     *     description="Create new Product",
     *     tags={"/product"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="sku", in="formData", description="Product Stock Keeping Unit", required=true, type="string"),
     *     @SWG\Parameter(name="name", in="formData", description="Product Name", required=true, type="string"),
     *     @SWG\Parameter(name="price", in="formData", description="Product Price", required=true, type="number"),
     *     @SWG\Parameter(name="qty", in="formData", description="Product Quantity", required=true, type="number"),
     *     @SWG\Parameter(name="desc", in="formData", description="Product Description", required=false, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function store(Request $req) {
        $this->validate($req, [
            "sku" => "required|unique:products,sku",
            "name" => "required",
            "price" => "required|integer",
            "qty" => "required|integer",
        ]);

        $param = $req->only(["sku", "name", "price", "qty", "desc"]);
        $param["status"] = Status::NEW;
        $product = Product::createData($param);
        return response()->json($product);
    }

    /**
     * Update existing Product
     * @api Put /product/{id}
     * @return json
     *
     * @SWG\Put(
     *     path="/product/{id}",
     *     description="Update existing Product",
     *     tags={"/product"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Product ID", required=true, type="number"),
     *     @SWG\Parameter(name="name", in="formData", description="Product Name", required=false, type="string"),
     *     @SWG\Parameter(name="price", in="formData", description="Product Price", required=false, type="number"),
     *     @SWG\Parameter(name="qty", in="formData", description="Product Quantity", required=false, type="number"),
     *     @SWG\Parameter(name="desc", in="formData", description="Product Description", required=false, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function update(Request $req, $id) {
        $this->validate($req, [
            "price" => "integer",
            "qty" => "integer",
        ]);

        $param = $req->only(["name", "price", "qty", "desc", "status"]);
        $product = Product::updateData($id, $param);
        return response()->json($product);
    }

    /**
     * Change Product Status
     * @api get /product/{id}/status
     * @return json
     *
     * @SWG\Get(
     *     path="/product/{id}/status",
     *     description="Change Product status",
     *     tags={"/product"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Product ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function changeStatus(Request $req, $id) {
        $product = Product::changeStatus($id);
        return response()->json($product);
    }

    /**
     * View Product detail
     * @api get /product/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/product/{id}",
     *     description="View Product detail",
     *     tags={"/product"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Product ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $product = Product::find($id);
        return response()->json($product);
    }
}
