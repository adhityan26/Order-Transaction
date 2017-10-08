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
    }

    public function index() {
        $product = Product::getProductList();
        return response()->json($product);
    }

    public function store(Request $req) {
        $param = $req->only(["sku", "name", "price", "qty", "desc"]);
        $product = Product::createProduct($param);
        return response()->json($product);
    }

    public function update(Request $req, $id) {
        $param = $req->only(["sku", "name", "price", "qty", "desc"]);
        $product = Product::updateProduct($id, $param);
        return response()->json($product);
    }

    public function updateQty(Request $req, $id) {
        $product = Product::updateQty($id, $req->input("qty"));
        return response()->json($product);
    }

    public function show(Request $req, $id) {
        $product = Product::find($id);
        return response()->json($product);
    }
}
