<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Product;
use App\Model\ProductCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->middleware('admin', ['only' => ['store', 'update', 'mapCategoryProduct']]);
    }

    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["name"]);
        $category = Category::getList($request->all(), $page, $limit, $param);
        return response()->json($category);
    }

    public function store(Request $req) {
        $this->validate($req, [
            "parent" => "required|integer",
            "name" => "required",
        ]);
        $param = $req->only(["parent", "name", "status"]);
        $category = Category::createData($param);
        return response()->json($category);
    }

    public function update(Request $req, $id) {
        $this->validate($req, [
            "parent" => "required|integer",
            "name" => "required",
        ]);
        $param = $req->only(["parent", "name", "status"]);
        $category = Category::updateData($id, $param);
        return response()->json($category);
    }

    public function remove(Request $req, $id) {
        $category = Category::removeData($id);
        return response()->json($category);
    }

    public function changeStatus(Request $req, $id) {
        $category = Category::changeStatus($id);
        return response()->json($category);
    }

    public function show(Request $req, $id) {
        $category = Category::getDetail($id);
        return response()->json($category);
    }

    public function mapCategoryProduct(Request $req) {
        $this->validate($req, [
            "product_id" => "required",
            "category_id" => "required"
        ]);

        $param = $req->only("category_id", "product_id");
        $productCategory = ProductCategory::createData($param);
        return response()->json($productCategory);
    }
}
