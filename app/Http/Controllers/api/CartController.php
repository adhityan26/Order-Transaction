<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Cart;
use App\Model\Product;
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

    public function remove(Request $req, $id) {
        $cart = Cart::removeData($id);
        return response()->json($cart);
    }

    public function show(Request $req, $id) {
        $cart = Cart::find($id);
        return response()->json($cart);
    }
}
