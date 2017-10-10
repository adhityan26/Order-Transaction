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

    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["sku", "name"]);
        $product = Product::getList($param, $page, $limit, ["sku", "name"]);
        return response()->json($product);
    }

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

    public function update(Request $req, $id) {
        $this->validate($req, [
            "price" => "integer",
            "qty" => "integer",
        ]);

        $param = $req->only(["name", "price", "qty", "desc", "status"]);
        $product = Product::updateData($id, $param);
        return response()->json($product);
    }

    public function changeStatus(Request $req, $id) {
        $product = Product::changeStatus($id);
        return response()->json($product);
    }

    public function show(Request $req, $id) {
        $product = Product::find($id);
        return response()->json($product);
    }
}
