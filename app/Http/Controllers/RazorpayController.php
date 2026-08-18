<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;

class RazorpayController extends Controller
{
    //
    use PaymentTrait;
    public function planPayWithrazorpay(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Razorpay');

        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            return response()->json(['status' => 'success', 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'success' => __('Your payment in progress.'),]);
        } else {
            return response()->json(['status' => $pre_pay->status, 'plan_type' => $pre_pay->plan_type, 'message' => $pre_pay->message]);
        }
    }

    public function razorpayGetPaymentStatus(Request $request, $code, $plan_id, $amount)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $ch = curl_init('https://api.razorpay.com/v1/payments/' . $code . '');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_USERPWD, $admin_payment_setting['razorpay_public_key'] . ':' . $admin_payment_setting['razorpay_secret_key']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        $payment_status = ($response->status == 'authorized')  ? 'success' : 'fail';

        if ($payment_status == 'success') {

            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

            if ($verify->status == 'success') {
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        } else {
            return redirect()->back()->with('error', __('Payment verification failed.'));
        }
    }

    public function PayWithrazorpay(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Razorpay');
        $price = preg_replace('/^\$/', '', $pre_pay->price); // Removes the dollar sign at the beginning
        if ($pre_pay->status == 'success') {
            try {
                return response()->json(['status' => 'success', 'price'=>$price,'success' => __('Your payment in progress.'),]);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $pre_pay->message);
        }
    }

    public function razorpayPayment(Request $request, $slug, $code, $order_id)
    {

        $store = Store::where('slug', $slug)->first();
        $store_payment_setting = Utility::getPaymentSetting($store->id);

        $ch = curl_init('https://api.razorpay.com/v1/payments/' . $code . '');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_USERPWD, $store_payment_setting['razorpay_public_key'] . ':' . $store_payment_setting['razorpay_secret_key']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        $payment_status = ($response->status == 'authorized')  ? 'success' : 'fail';

        if ($payment_status == 'success') {

            $status = $this->statusThisProductOrder($request, $slug);
            return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
        } else {
            return redirect()->back()->with('error', __('Payment verification failed.'));
        }
    }
}
