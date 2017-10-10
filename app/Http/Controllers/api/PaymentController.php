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

    /**
     * List Payment
     * @api GET /payment
     * @return json
     *
     * @SWG\Get(
     *     path="/payment",
     *     description="List Payment",
     *     tags={"/payment"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="payment_date", in="query", description="Date of payment", required=false, type="string"),
     *     @SWG\Parameter(name="user_id", in="query", description="Order User ID", required=false, type="string"),
     *     @SWG\Parameter(name="user_account", in="query", description="Sender Bank User Account", required=false, type="string"),
     *     @SWG\Parameter(name="user_bank_account", in="query", description="Sender Bank Name", required=false, type="string"),
     *     @SWG\Parameter(name="order_id", in="query", description="Order ID", required=false, type="string"),
     *     @SWG\Parameter(name="limit", in="query", description="Limit data", required=true, type="number", default=10),
     *     @SWG\Parameter(name="page", in="query", description="Limit data", required=true, type="number", default=1),
     *     @SWG\Response(response=200, description="Operation success")
     * )
     */
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

    /**
     * Create new Payment
     * @api POST /payment
     * @return json
     *
     * @SWG\Post(
     *     path="/payment",
     *     description="Create new Payment",
     *     tags={"/payment"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="order_id", in="formData", description="Order ID", required=true, type="number"),
     *     @SWG\Parameter(name="user_account", in="formData", description="Sender Bank User Account", required=true, type="string"),
     *     @SWG\Parameter(name="user_bank_account", in="formData", description="Sender Bank Name", required=true, type="string"),
     *     @SWG\Parameter(name="reference_no", in="formData", description="Reference No", required=true, type="string"),
     *     @SWG\Parameter(name="notes", in="formData", description="Notes", required=true, type="string"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
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

    /**
     * Confirm Payment
     * @api get /payment/{id}/confirm
     * @return json
     *
     * @SWG\Get(
     *     path="/payment/{id}/confirm",
     *     description="Confirm Payment",
     *     tags={"/order"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Payment ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function confirmPayment(Request $req, $id) {
        $payment = Payment::changePaymentStatus($id, Status::CONFIRMED);
        return response()->json($payment);
    }

    /**
     * Reject Payment
     * @api get /payment/{id}/reject
     * @return json
     *
     * @SWG\Get(
     *     path="/payment/{id}/reject",
     *     description="Reject Payment",
     *     tags={"/payment"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="payment ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function rejectPayment(Request $req, $id) {
        $payment = Payment::changePaymentStatus($id, Status::REJECTED);
        return response()->json($payment);
    }

    /**
     * Cancel Payment
     * @api get /payment/{id}/cancel
     * @return json
     *
     * @SWG\Get(
     *     path="/payment/{id}/cancel",
     *     description="Cancel Payment",
     *     tags={"/payment"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Payment ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function cancelPayment(Request $req, $id) {
        $payment = Payment::changePaymentStatus($id, Status::CANCELED);
        return response()->json($payment);
    }

    /**
     * View Payment detail
     * @api get /payment/{id}
     * @return json
     *
     * @SWG\Get(
     *     path="/payment/{id}",
     *     description="View Payment detail",
     *     tags={"/payment"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", in="path", description="Payment ID", required=true, type="number"),
     *     @SWG\Response(response=200, description="Operation success"),
     *     security={
     *         {"AccessToken": {}}
     *     }
     * )
     */
    public function show(Request $req, $id) {
        $payment = Payment::find($id);
        return response()->json($payment);
    }
}
