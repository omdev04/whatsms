<?php

namespace App\Http\Controllers;

use App\Traits\PaymentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaytrController extends Controller
{
    use PaymentTrait;

    public function PlanpayWithPaytr(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Paytr');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $authuser = Auth::user();
            try {
                $merchant_id    = isset($pre_pay->settings['paytr_merchant_id']) ? $pre_pay->settings['paytr_merchant_id'] : '';
                $merchant_key   = isset($pre_pay->settings['paytr_merchant_key']) ? $pre_pay->settings['paytr_merchant_key'] : '';
                $merchant_salt  = isset($pre_pay->settings['paytr_merchant_salt']) ? $pre_pay->settings['paytr_merchant_salt'] : '';

                $email = $authuser->email;
                $payment_amount = $pre_pay->price;
                $merchant_oid = $pre_pay->order_id;
                $user_name = $authuser->name;
                $user_address = !empty($pre_pay->store->address) ? $pre_pay->store->address : 'no address';
                $user_phone = !empty($pre_pay->store->whatsapp_number) ? $pre_pay->store->whatsapp : '0000000000';


                $user_basket = base64_encode(json_encode(array(
                    array("Plan", $payment_amount, 1),
                )));

                if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $ip = $_SERVER["HTTP_CLIENT_IP"];
                } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else {
                    $ip = $_SERVER["REMOTE_ADDR"];
                }

                $user_ip = $ip;
                $timeout_limit = "30";
                $debug_on = 1;
                $test_mode = 0;
                $no_installment = 0;
                $max_installment = 0;
                $currency = !empty($pre_pay->currency) ? $pre_pay->currency : 'USD';

                $payment_amount = $payment_amount * 100;
                $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
                $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

                $request['orderID'] = $pre_pay->order_id;
                $request['plan_id'] = $plan_id;
                $request['amount'] = $pre_pay->price;
                $request['status'] = 'failed';
                $payment_failed = $request->all();
                $request['status'] = 'success';
                $payment_success = $request->all();

                $post_vals = array(
                    'merchant_id' => $merchant_id,
                    'user_ip' => $user_ip,
                    'merchant_oid' => $merchant_oid,
                    'email' => $email,
                    'payment_amount' => $payment_amount,
                    'paytr_token' => $paytr_token,
                    'user_basket' => $user_basket,
                    'debug_on' => $debug_on,
                    'no_installment' => $no_installment,
                    'max_installment' => $max_installment,
                    'user_name' => $user_name,
                    'user_address' => $user_address,
                    'user_phone' => $user_phone,
                    'merchant_ok_url' => route('pay.paytr.success', ['amount' => $pre_pay->price, 'coupon_id' => $pre_pay->coupon_id, 'plan_id' => $plan_id, 'status' => 'success']),
                    'merchant_fail_url' => route('pay.paytr.success', ['amount' => $pre_pay->price, 'coupon_id' => $pre_pay->coupon_id, 'plan_id' => $plan_id, 'status' => 'failed']),
                    'timeout_limit' => $timeout_limit,
                    'currency' => $currency,
                    'test_mode' => $test_mode
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);


                $result = @curl_exec($ch);

                if (curl_errno($ch)) {
                    die("PAYTR IFRAME connection error. err:" . curl_error($ch));
                }

                curl_close($ch);

                $result = json_decode($result, 1);

                if ($result['status'] == 'success') {
                    $token = $result['token'];
                } else {
                    return redirect()->route('plans.index')->with('error', $result['reason']);
                }
                return view('paytr_payment.index', compact('token'));
            } catch (\Throwable $th) {
                return redirect()->route('plans.index')->with('error', $th->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function paytrsuccess(Request $request, $plan_id, $amount)
    {
        if ($request->status == "success") {
            try {
                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false);
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plans.index')->with('success', __('Your Transaction is fail please try again.'));
        }
    }

    public function PayWithPaytr(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Paytr');
        try {
            $email = $pre_pay->email;
            $payment_amount = (float)($pre_pay->price);
            $merchant_oid = $pre_pay->order_id;
            $user_name = $pre_pay->name;
            $user_address = !empty($pre_pay->store->address) ? $pre_pay->store->address : 'no address';
            $user_phone = !empty($pre_pay->store->whatsapp_number) ? $pre_pay->store->whatsapp : '0000000000';

            $user_basket = base64_encode(json_encode(array(
                array("Product", $payment_amount, 1),
            )));

            if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }

            $merchant_id    = isset($pre_pay->settings['paytr_merchant_id']) ? $pre_pay->settings['paytr_merchant_id'] : '';
            $merchant_key   = isset($pre_pay->settings['paytr_merchant_key']) ? $pre_pay->settings['paytr_merchant_key'] : '';
            $merchant_salt  = isset($pre_pay->settings['paytr_merchant_salt']) ? $pre_pay->settings['paytr_merchant_salt'] : '';

            $user_ip = $ip;
            $timeout_limit = "30";
            $debug_on = 1;
            $test_mode = 0;
            $no_installment = 0;
            $max_installment = 0;
            $currency = $pre_pay->currency;
            $payment_amount = $payment_amount * 100;
            $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
            $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

            $request['store_payment_status'] = 'failed';
            $status = $request->all();
            $payment_failed = $status['store_payment_status'];
            $request['store_payment_status'] = 'success';
            $status = $request->all();
            $payment_success = $status['store_payment_status'];

            $post_vals = array(
                'merchant_id' => $merchant_id,
                'user_ip' => $user_ip,
                'merchant_oid' => $merchant_oid,
                'email' => $email,
                'payment_amount' => $payment_amount,
                'paytr_token' => $paytr_token,
                'user_basket' => $user_basket,
                'debug_on' => $debug_on,
                'no_installment' => $no_installment,
                'max_installment' => $max_installment,
                'user_name' => $user_name,
                'user_address' => $user_address,
                'user_phone' => $user_phone,
                'merchant_ok_url' => route('store.pay.paytr.success', ['status' => 'success', 'slug' => $slug, 'order_id' => $pre_pay->order_id]),
                'merchant_fail_url' => route('store.pay.paytr.success', ['status' => 'failed',  'slug' => $slug, 'order_id' => $pre_pay->order_id]),
                'timeout_limit' => $timeout_limit,
                'currency' => $currency,
                'test_mode' => $test_mode
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            $result = @curl_exec($ch);

            if (curl_errno($ch)) {
                die("PAYTR IFRAME connection error. err:" . curl_error($ch));
            }

            curl_close($ch);

            $result = json_decode($result, 1);

            if ($result['status'] == 'success') {
                $token = $result['token'];
            } else {
                return redirect()->back()->with('error', $result['reason']);
            }

            return view('paytr_payment.index', compact('token'));
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }

    public function paytrsuccessCallback(Request $request, $slug)
    {
        if ($request->status == "success") {
            try {
                $status =  $this->statusThisProductOrder($request, $slug);
                if ($status->status == 'success') {
                    return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                }
                return redirect()->route('store.slug', $slug)->with('error', __($status->message));
            } catch (\Exception $e) {
                return redirect()->route('store.slug', $slug)->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('store.slug', $slug)->with('success', __('Your Transaction is fail please try again.'));
        }
    }
}
