<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Traits\PaymentTrait;

class PaypalController extends Controller
{

    use PaymentTrait;
    public function planPayWithPaypal(Request $request)
    {

        $pre_pay = $this->payThisPlan($request, 'Paypal');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            config(
                [
                    'paypal.sandbox.client_id' => $pre_pay->settings['paypal_client_id'] ?? '',
                    'paypal.sandbox.client_secret' => $pre_pay->settings['paypal_secret_key'] ?? '',
                    'paypal.mode' => $pre_pay->settings['paypal_mode'] ?? '',
                ]
            );
            $provider = new PayPalClient;

            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            Session::put('paypal_payment_id', $paypalToken['access_token']);

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('get.store.payment.status', ['id' => $plan_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success', 'coupon_id' => $pre_pay->coupon_id]),
                    "cancel_url" =>  route('get.store.payment.status', ['id' => $plan_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'cancel', 'coupon_id' => $pre_pay->coupon_id]),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => $pre_pay->currency,
                            "value" => $pre_pay->price,
                        ],
                    ],
                ],
            ]);
            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return redirect()->away($links['href']);
                    }
                }
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed'));
            } else {
                return redirect()->route('plans.index')->with('error', $response['message'] ?? __('Something went wrong.'));
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function storeGetPaymentStatus(Request $request, $plan_id, $amount)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();

        config(
            [
                'paypal.sandbox.client_id' => $admin_payment_setting['paypal_client_id'] ?? '',
                'paypal.sandbox.client_secret' => $admin_payment_setting['paypal_secret_key'] ?? '',
                'paypal.mode' => $admin_payment_setting['paypal_mode'] ?? '',
            ]
        );
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        $payment_id = Session::get('paypal_payment_id');
        Session::forget('paypal_payment_id');

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);
            if ($verify->status == 'success') {
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        }
        return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
    }
    public function PayWithPaypal(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Paypal');
        if ($pre_pay->status == 'success') {

            config(
                [
                    'paypal.sandbox.client_id' => $pre_pay->settings['paypal_client_id'] ?? '',
                    'paypal.sandbox.client_secret' => $pre_pay->settings['paypal_secret_key'] ?? '',
                    'paypal.mode' => $pre_pay->settings['paypal_mode'] ?? '',
                ]
            );
            $provider = new PayPalClient;

            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            Session::put('paypal_payment_id', $paypalToken['access_token']);

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('get.payment.status', [$pre_pay->slug, 'status' => 'success']),
                    "cancel_url" => route('get.payment.status', [$pre_pay->slug, 'status' => 'cancel']),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => $pre_pay->currency,
                            "value" => $pre_pay->price,
                        ],
                    ],
                ],
            ]);
            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return redirect()->away($links['href']);
                    }
                }
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', __('Transaction has been failed'));
            } else {

                return redirect()->route('store.slug', $pre_pay->slug)->with('error', $response['message'] ?? __('Something went wrong.'));
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function GetPaymentStatus(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();
        $store_payment_setting = Utility::getPaymentSetting($store->id);

        config(
            [
                'paypal.sandbox.client_id' => $store_payment_setting['paypal_client_id'] ?? '',
                'paypal.sandbox.client_secret' => $store_payment_setting['paypal_secret_key'] ?? '',
                'paypal.mode' => $store_payment_setting['paypal_mode'] ?? '',
            ]
        );
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $status =  $this->statusThisProductOrder($request, $slug);
            if ($status->status == 'success') {
                return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
            }
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        }
        return redirect()->route('store.slug', $slug)->with('error', __('Transaction has been failed.'));
    }
}
