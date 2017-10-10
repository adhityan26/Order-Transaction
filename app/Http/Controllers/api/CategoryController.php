<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Category;
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

    /**
     * List Category
     * @api GET /category
     * @return json
     *
     * @SWG\Get(
     *     path="/category",
     *     description="List Category",
     *     tags={"/category"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="name", in="query", description="Category Name", required=false, type="string"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success")
     * )
     */
    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["name"]);
        $category = Category::getList($param, $page, $limit, ["name"]);
        return response()->json($category);
    }

    /**
     * Create new Category
     * @api POST /category
     * @return json
     *
     * @SWG\Post(
     *     path="/category",
     *     description="Create new Category",
     *     tags={"/category"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="parent", in="formData", description="Parent Category", required=true, type="number"),
     *     @SWG\Parameter(name="name", in="formData", description="Category Name", required=true, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function store(Request $req) {
        $this->validate($req, [
            "parent" => "required|integer|exists:categories,id",
            "name" => "required",
        ]);
        $param = $req->only(["parent", "name", "status"]);
        $category = Category::createData($param);
        return response()->json($category);
    }

    /**
     * Update existing Category
     * @api Put /category/{id}
     * @return json
     *
     * @SWG\Put(
     *     path="/category/{id}",
     *     description="Update existing Category",
     *     tags={"/category"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Category ID", required=true, type="number"),
     *     @SWG\Parameter(name="parent", in="formData", description="Parent Category", required=false, type="number"),
     *     @SWG\Parameter(name="name", in="formData", description="Category Name", required=false, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function update(Request $req, $id) {
        $this->validate($req, [
            "parent" => "integer|exists:categories,id"
        ]);
        $param = $req->only(["parent", "name", "status"]);
        $category = Category::updateData($id, $param);
        return response()->json($category);
    }

    /**
     * Delete existing Category
     * @api Delete /category/{id}
     * @return json
     *
     * @SWG\Delete(
     *     path="/category/{id}",
     *     description="Delete existing Category",
     *     tags={"/category"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Category ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function remove(Request $req, $id) {
        $category = Category::removeData($id);
        return response()->json($category);
    }

    /**
     * Change Category Status
     * @api get /category/{id}/status
     * @return json
     *
     * @SWG\Get(
     *     path="/category/{id}/status",
     *     description="Change Category status",
     *     tags={"/category"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Category ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function changeStatus(Request $req, $id) {
        $category = Category::changeStatus($id);
        return response()->json($category);
    }

    /**
     * View Category detail
     * @api get /category/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/category/{id}",
     *     description="View Category detail",
     *     tags={"/category"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Category ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $category = Category::getDetail($id);
        return response()->json($category);
    }

    /**
     * Map Category Product
     * @api put /category/mapCategoryProduct
     * @return json
     *
     * @SWG\Put(
     *     path="/category/mapCategoryProduct",
     *     description="Map Category Product",
     *     tags={"/category"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="product_id", in="formData", description="Product ID", required=true, type="number"),
     *     @SWG\Parameter(name="category_id", in="formData", description="Product ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function mapCategoryProduct(Request $req) {
        $this->validate($req, [
            "product_id" => "required|exists:products,id",
            "category_id" => "required|exists:categories,id"
        ]);

        $param = $req->only("category_id", "product_id");
        $productCategory = ProductCategory::createData($param);
        return response()->json($productCategory);
    }
}
