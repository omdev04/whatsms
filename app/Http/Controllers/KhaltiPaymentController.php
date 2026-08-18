<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Session;

class KhaltiPaymentController extends Controller
{
    use PaymentTrait;
    public function planPayWithKhalti(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Khalti');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            try {
                $amount     = $pre_pay->price;
                $coupon_id  = $pre_pay->coupon_id;
                Session::put('amount', $amount);
                Session::put('coupon_id', $coupon_id);
                return $amount;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return response()->json(['status' => $pre_pay->status, 'plan_type' => $pre_pay->plan_type, 'message' => $pre_pay->message]);
        }
    }

    public function planGetKhaltiStatus(Request $request)
    {
        $amount = Session::get('amount');
        $coupon_id = Session::get('coupon_id');
        $request->merge(['coupon_id' => $coupon_id]);
        Session::forget($coupon_id);
        if ($request->payload['status'] == 200) {
            $request->status = 'success';
            $verify =  $this->statusThisPlan($request, $request->plan_id, $amount, $request->status, false,);
            if ($verify->status == 'success') {
                return response()->json(['status' => $verify->status, 'success' => __($verify->message),]);
            }
            return response()->json(['status' => 'error', 'success' => __('Transaction has been failed.'),]);
        } else {
            return response()->json(['status' => 'error', 'success' => __('Transaction has been failed.'),]);
        }
    }

    public function storePayWithKhalti(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Khalti');

        if ($pre_pay->status == 'success') {

            try {
                $get_amount = preg_replace('/^\$/', '', $pre_pay->price); // Removes the dollar sign at the beginning
                return $get_amount;
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', __($e->getMessage()));
            }
        } else {
            return response()->json(['status' => $pre_pay->status,'message' => $pre_pay->message]);
        }
    }

    public function getStorePaymentStatus(Request $request, $slug)
    {
        if ($request->payload['status'] == 200) {
            $request->status = 'success';
            $status = $this->statusThisProductOrder($request, $slug);
            return response()->json(['status' => $status->status, 'order_id' => $status->order_id, 'success' => __($status->message),]);
        } else {
            return response()->json(['status' => 'error', 'success' => __('Transaction has been failed.'),]);
        }
    }
}
