<?php

namespace App\Http\Controllers\api;

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
        $this->middleware('admin', ['only' => ['store', 'update', 'updateQty']]);
    }

    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["sku", "name"]);
        $product = Product::getList($request->all(), $page, $limit, $param);
        return response()->json($product);
    }

    public function store(Request $req) {
        $this->validate($req, [
            "sku" => "required|unique:products,sku",
            "name" => "required",
            "price" => "required|integer",
            "qty" => "required|integer",
        ]);

        $param = $req->only(["sku", "name", "price", "qty", "desc", "status"]);
        $product = Product::createData($param);
        return response()->json($product);
    }

    public function update(Request $req, $id) {
        $this->validate($req, [
            "name" => "required",
            "price" => "required|integer",
            "qty" => "required|integer",
        ]);

        $param = $req->only(["name", "price", "qty", "desc", "status"]);
        $product = Product::updateData($id, $param);
        return response()->json($product);
    }

    public function updateQty(Request $req, $id) {
        $this->validate($req, [
            "qty" => "required|integer",
        ]);
        $product = Product::updateQty($id, $req->input("qty"));
        return response()->json($product);
    }

    public function show(Request $req, $id) {
        $product = Product::find($id);
        return response()->json($product);
    }
}
