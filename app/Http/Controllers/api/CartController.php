<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List Cart
     * @api GET /cart
     * @return json
     *
     * @SWG\Get(
     *     path="/cart",
     *     description="List Cart",
     *     tags={"/cart"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="user_id", in="query", description="User ID cart owner", required=false, type="number"),
     *     @SWG\Parameter(name="product_id", in="query", description="Product exist on cart", required=false, type="number"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["user_id", "product_id"]);
        $user = Auth::user();

        if ($user->admin != 1) {
            $param["user_id"] = $user->id;
        }
        $cart = Cart::getList($param, $page, $limit, ["user_id", "product_id"]);
        return response()->json($cart);
    }

    /**
     * Create/update cart
     * @api POST /coupon
     * @return json
     *
     * @SWG\Post(
     *     path="/cart",
     *     description="Create new Cart",
     *     tags={"/cart"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="product_id", in="formData", description="Product ID", required=true, type="number"),
     *     @SWG\Parameter(name="qty", in="formData", description="Product quantity", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function store(Request $req) {
        $this->validate($req, [
            "product_id" => "required|exists:products,id",
            "qty" => "required|integer",
        ]);

        $param = $req->only(["product_id", "qty"]);
        $user = Auth::user();

        $param["user_id"] = $user->id;

        $cart = Cart::createData($param);
        return response()->json($cart);
    }

    /**
     * Delete existing Cart
     * @api Delete /cart/{id}
     * @return json
     *
     * @SWG\Delete(
     *     path="/cart/{id}",
     *     description="Delete existing Cart",
     *     tags={"/cart"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Cart ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function remove(Request $req, $id) {
        $user = Auth::user();
        $cart = Cart::removeCart($user, $id);
        return response()->json($cart);
    }

    /**
     * View Cart detail
     * @api get /cart/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/cart/{id}",
     *     description="View Cart detail",
     *     tags={"/cart"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Cart ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $cart = Cart::query()->with("product")->where("id", $id)->first();
        return response()->json($cart);
    }
}
