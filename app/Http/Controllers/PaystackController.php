<?php

namespace App\Http\Controllers;


use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Auth;

class PaystackController extends Controller
{
    use PaymentTrait;
    public function planPayWithPaystack(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Paystack');
        $user = Auth::user();

        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            try {
                $paystack_secret_key = $pre_pay->settings['paystack_secret_key'] ?? '';
                $amount = $pre_pay->price * 100;
                $email = $user->email;
                $currency = $pre_pay->currency;

                $paystack_payment_url = "https://api.paystack.co/transaction/initialize";
                $headers = [
                    'Authorization: Bearer ' . $paystack_secret_key,
                    'Content-Type: application/json',
                ];

                $fields = ['email' => $email, 'amount' => $amount, 'currency' => $currency,];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $paystack_payment_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                // Execute cURL request
                $response = curl_exec($ch);
                curl_close($ch);

                // Handle Paystack response
                if ($response) {
                    $paystack_response = json_decode($response, true);

                    if ($paystack_response && $paystack_response['status'] == true) {
                        return response()->json(['status' => 'success', 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'url' => $paystack_response['data']['authorization_url'], 'message' => __('Your payment in progress.')]);
                    } else {
                        return response()->json(['status' => 'error', 'message' => __('The transaction has failed.')]);
                    }
                } else {
                    return response()->json(['error' => __('Payment initialization failed.')]);
                }
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }
        } else {
            return response()->json(['status' => $pre_pay->status, 'plan_type' => $pre_pay->plan_type, 'message' => $pre_pay->message]);
        }
    }

    public function paystackGetPaymentStatus(Request $request, $code, $plan_id, $amount)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();

        $paystack_secret_key = $admin_payment_setting['paystack_secret_key'] ?? '';
        $paystack_verify_url = "https://api.paystack.co/transaction/verify/{$code}";
        $headers = [
            'Authorization: Bearer ' . $paystack_secret_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paystack_verify_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        $paystack_response = json_decode($response, true);

        if ($paystack_response && $paystack_response['status'] == 'success') {
            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);
            if ($verify->status == 'success') {
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        } else {
            return redirect()->back()->with('error', __('Payment verification failed.'));
        }
    }

    public function PayWithPaystack(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Paystack');

        if ($pre_pay->status == 'success') {
            try {
                $paystack_secret_key = $pre_pay->settings['paystack_secret_key'] ?? '';
                $price = preg_replace('/[^0-9\.]/', '', $pre_pay->price);
                $amount = (int)$price * 100;

                $email = $pre_pay->email;
                $currency = $pre_pay->currency;

                $paystack_payment_url = "https://api.paystack.co/transaction/initialize";
                $headers = [
                    'Authorization: Bearer ' . $paystack_secret_key,
                    'Content-Type: application/json',
                ];

                $fields = ['email' => $email, 'amount' => $amount, 'currency' => $currency,];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $paystack_payment_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);
                curl_close($ch);
                if ($response) {
                    $paystack_response = json_decode($response, true);
                    // Check if Paystack response is successful
                    if ($paystack_response && $paystack_response['status'] == true) {
                        return response()->json(['status' => 'success', 'price'=>$price,'url' => $paystack_response['data']['authorization_url'], 'message' => __('Your payment in progress.')]);
                    } else {
                        return response()->json(['status' => 'error', 'message' => __('The transaction has failed.')]);
                    }
                } else {
                    return response()->json(['error' => __('Payment initialization failed.')]);
                }
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $pre_pay->message);
        }
    }

    public function paystackPayment(Request $request, $slug, $code, $order_id)
    {
        $store = Store::where('slug', $slug)->first();
        $store_payment_setting = Utility::getPaymentSetting($store->id);

        $paystack_secret_key = $store_payment_setting['paystack_secret_key'] ?? '';
        $paystack_verify_url = "https://api.paystack.co/transaction/verify/{$code}";
        $headers = [
            'Authorization: Bearer ' . $paystack_secret_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paystack_verify_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        $paystack_response = json_decode($response, true);

        if ($paystack_response && $paystack_response['status'] == 'success') {
            // Payment successful, process order
            $status = $this->statusThisProductOrder($request, $slug);
            return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
        } else {
            return redirect()->back()->with('error', __('Payment verification failed.'));
        }
    }
}
