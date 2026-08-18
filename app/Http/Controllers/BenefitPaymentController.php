<?php

namespace App\Http\Controllers;

use App\Models\Store;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Auth;

class BenefitPaymentController extends Controller
{
    use PaymentTrait;
    public function initiatePayment(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Benefit');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
            $user = Auth::user();

            $userData =
                [
                    "amount" => $pre_pay->price,
                    "currency" => $pre_pay->settings['currency'] ?? 'USD',
                    "customer_initiated" => true,
                    "threeDSecure" => true,
                    "save_card" => false,
                    "description" => " Plan - " . $pre_pay->plan->name,
                    "metadata" => ["udf1" => "Metadata 1"],
                    "reference" => ["transaction" => "txn_01", "order" => "ord_01"],
                    "receipt" => ["email" => true, "sms" => true],
                    "customer" => ["first_name" => $user->name, "middle_name" => "", "last_name" => "", "email" => $user->email, "phone" => ["country_code" => 965, "number" => 51234567]],
                    "source" => ["id" => "src_bh.benefit"],
                    "post" => ["url" => "https://webhook.site/fd8b0712-d70a-4280-8d6f-9f14407b3bbd"],
                    "redirect" => ["url" => route('benefit.call_back', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success'])],
                ];
            $responseData = json_encode($userData);
            $client = new Client();
            try {
                $response = $client->request('POST', 'https://api.tap.company/v2/charges', [
                    'body' => $responseData,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $pre_pay->settings['benefit_secret_key'],
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ],
                ]);
            } catch (\Throwable $th) {
                return redirect()->route('plans.index')->with('error', __('Currency Not Supported.Contact To Your Site Admin'));
            }

            $data = $response->getBody();
            $res = json_decode($data);
            return redirect($res->transaction->url);
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function call_back(Request $request, $plan_id, $amount)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $secret_key = $admin_payment_setting['benefit_secret_key'] ?? '';

        try {
            $post = $request->all();
            $client = new Client();
            $response = $client->request('GET', 'https://api.tap.company/v2/charges/' . $post['tap_id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secret_key,
                    'accept' => 'application/json',
                ],
            ]);

            $json = $response->getBody();
            $data = json_decode($json);
            $status_code = $data->gateway->response->code;
            if ($status_code == '00') {
                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);
                if ($verify->status == 'success') {
                    return redirect()->route('plans.index')->with($verify->status, $verify->message);
                }
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __('Transaction has been cancelled!'));
        }
    }

    public function storeInitiatePayment(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Benefit');

        if ($pre_pay->status == 'success') {

            try {
                $customerData =
                    [
                        "amount" => $pre_pay->price,
                        "currency" =>  $pre_pay->currency ?? 'USD',
                        "customer_initiated" => true,
                        "threeDSecure" => true,
                        "save_card" => false,
                        "description" => $pre_pay->store->name,
                        "metadata" => ["udf1" => "Metadata 1"],
                        "reference" => ["transaction" => "txn_01", "order" => "ord_01"],
                        "receipt" => ["email" => true, "sms" => true],
                        "customer" => ["first_name" => $pre_pay->name, "middle_name" => "", "last_name" => "", "email" => $pre_pay->email, "phone" => ["country_code" => 965, "number" => 51234567]],
                        "source" => ["id" => "src_bh.benefit"],
                        "post" => ["url" => "https://webhook.site/fd8b0712-d70a-4280-8d6f-9f14407b3bbd"],
                        "redirect" => ["url" => route('store.benefit.call_back', ['slug' => $pre_pay->slug, 'order_id' => $pre_pay->order_id, 'status' => 'success'])],
                    ];
                $responseData = json_encode($customerData);
            } catch (\Throwable $th) {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', __('Currency Not Supported.Contact To Your Site Admin'));
            }
            $client = new Client();
            try {
                $response = $client->request('POST', 'https://api.tap.company/v2/charges/', [
                    'body' => $responseData,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $pre_pay->settings['benefit_secret_key'],
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ],
                ]);
            } catch (\Throwable $th) {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', __('Currency Not Supported.Contact To Your Site Admin'));
            }

            $data = $response->getBody();
            $res = json_decode($data);
            return redirect($res->transaction->url);
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function storeCall_back(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();
        if (Auth::check() && Utility::CustomerAuthCheck($slug) == false) {
            $store_payment_setting = Utility::getPaymentSetting();
        } else {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }
        $secret_key = $store_payment_setting['benefit_secret_key'] ?? '';

        try {
            $post = $request->all();
            $client = new Client();
            $response = $client->request('GET', 'https://api.tap.company/v2/charges/' . $post['tap_id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secret_key,
                    'accept' => 'application/json',
                ],
            ]);

            $json = $response->getBody();
            $data = json_decode($json);
            $status_code = $data->gateway->response->code;
            if ($status_code == '00') {
                $status =  $this->statusThisProductOrder($request, $slug);

                if ($status->status == 'success') {
                    return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                }
                return redirect()->route('store.slug', $slug)->with('error', __($status->message));
            } else {
                return redirect()->route('store.slug', $slug)->with('error', __('Your Transaction is fail please try again'));
            }
        } catch (Exception $e) {
            return redirect()->route('store.slug', $slug)->with('error', __('Transaction has been cancelled!'));
        }
    }
}
