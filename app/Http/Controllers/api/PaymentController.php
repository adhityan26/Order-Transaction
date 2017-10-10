<?php

namespace App\Http\Controllers\api;

use App\Constant\Status;
use App\Http\Controllers\Controller;
use App\Model\Payment;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin', ['only' => ['confirmPayment', 'rejectPayment', 'cancelPayment']]);
    }

    public function index(Request $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $param = $request->only(["payment_date", "user_id", "user_account", "user_bank_account", "status", "order_id"]);
        $user = Auth::user();

        if (!$user->admin) {
            $param["user_id"] = $user->id;
        }

        $payment = Payment::getList($param, $page, $limit, ["payment_date", "user_id", "user_account", "user_bank_account", "status", "order_id"]);
        return response()->json($payment);
    }

    public function store(Request $req) {
        $this->validate($req, [
            "order_id" => "required|exists:orders,id",
//            "total_payment" => "required|integer",
            "user_account" => "required",
            "user_bank_account" => "required",
        ]);

        $param = $req->only(["order_id", "reference_no", "user_account", "user_bank_account", "notes"]);
        $param["status"] = Status::NEW;
        $param["total_payment"] = 0;
        $user = Auth::user();

        $payment = Payment::createPayment($user, $param);
        return response()->json($payment);
    }

    public function confirmPayment(Request $req, $id) {
        $payment = Payment::changePaymentStatus($id, Status::CONFIRMED);
        return response()->json($payment);
    }

    public function rejectPayment(Request $req, $id) {
        $payment = Payment::changePaymentStatus($id, Status::REJECTED);
        return response()->json($payment);
    }

    public function cancelPayment(Request $req, $id) {
        $payment = Payment::changePaymentStatus($id, Status::CANCELED);
        return response()->json($payment);
    }

    public function show(Request $req, $id) {
        $payment = Payment::find($id);
        return response()->json($payment);
    }
}
