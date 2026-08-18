<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Store;
use App\Traits\PaymentTrait;

class CashfreeController extends Controller
{
    use PaymentTrait;
    public function paymentConfig()
    {
        if (\Auth::check()) {
            $payment_setting = Utility::getAdminPaymentSetting();
            config(
                [
                    'services.cashfree.key' => isset($payment_setting['cashfree_api_key']) ? $payment_setting['cashfree_api_key'] : '',
                    'services.cashfree.secret' => isset($payment_setting['cashfree_secret_key']) ? $payment_setting['cashfree_secret_key'] : '',
                ]
            );
        }
    }

    public function cashfreePaymentStore(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Cashfree');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $user = \Auth::user();
            $this->paymentConfig();
            $url = config('services.cashfree.url');

            $headers = array(
                "Content-Type: application/json",
                "x-api-version: 2022-01-01",
                "x-client-id: " . config('services.cashfree.key'),
                "x-client-secret: " . config('services.cashfree.secret')
            );

            $data = json_encode([
                'order_id' => $pre_pay->order_id,
                'order_amount' => $pre_pay->price,
                "order_currency" => !empty($pre_pay->currency) ? $pre_pay->currency : 'USD',
                "order_name" => $pre_pay->plan->name,
                "customer_details" => [
                    "customer_id" => 'customer_' . $user->id,
                    "customer_name" => $user->name,
                    "customer_email" => $user->email,
                    "customer_phone" => '1234567890',
                ],
                "order_meta" => [
                    "return_url" => route('cashfreePayment.success', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success'])
                ]
            ]);
            try {
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $resp = curl_exec($curl);
                curl_close($curl);
                return redirect()->to(json_decode($resp)->payment_link);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Currency Not Supported.Contact To Your Site Admin'));
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function cashfreePaymentSuccess(Request $request, $plan_id, $amount)
    {
        if ($request->status == "success") {
            $this->paymentConfig();

            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->order_id . '/settlements', [
                    'headers' => [
                        'accept' => 'application/json',
                        'x-api-version' => '2022-09-01',
                        "x-client-id" => config('services.cashfree.key'),
                        "x-client-secret" => config('services.cashfree.secret')
                    ],
                ]);

                $respons = json_decode($response->getBody());
                if ($respons->order_id && $respons->cf_payment_id != NULL) {

                    $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                        'headers' => [
                            'accept' => 'application/json',
                            'x-api-version' => '2022-09-01',
                            'x-client-id' => config('services.cashfree.key'),
                            'x-client-secret' => config('services.cashfree.secret'),
                        ],
                    ]);
                    $info = json_decode($response->getBody());

                    if ($info->payment_status == "SUCCESS") {

                        $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false);
                        return redirect()->route('plans.index')->with($verify->status, $verify->message);
                    } else {
                        return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
                    }
                } else {
                    return redirect()->route('plans.index')->with('error', __('Payment Failed.'));
                }
                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
        }
    }

    public function payWithCashfree(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Cashfree');
        try {
            config(
                [
                    'services.cashfree.key' => isset($pre_pay->settings['cashfree_api_key']) ? $pre_pay->settings['cashfree_api_key'] : '',
                    'services.cashfree.secret' => isset($pre_pay->settings['cashfree_secret_key']) ? $pre_pay->settings['cashfree_secret_key'] : '',
                ]
            );
            $url = config('services.cashfree.url');

            $headers = array(
                "Content-Type: application/json",
                "x-api-version: 2022-01-01",
                "x-client-id: " . config('services.cashfree.key'),
                "x-client-secret: " . config('services.cashfree.secret')
            );
            $data = json_encode([
                'order_id' => $pre_pay->order_id,
                'order_amount' => $pre_pay->price,
                "order_currency" => $pre_pay->currency,
                "order_name" => $pre_pay->name,
                "customer_details" => [
                    "customer_id" => 'customer_' . $pre_pay->id,
                    "customer_name" => $pre_pay->name,
                    "customer_email" => $pre_pay->email,
                    "customer_phone" => '1234567890',
                ],
                "order_meta" => [
                    "return_url" => route('store.cashfreePayment.success', ['slug' => $slug, 'order_id' => $pre_pay->order_id, 'status' => 'success'])
                ]
            ]);
            try {

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $resp = curl_exec($curl);

                curl_close($curl);
                return redirect()->to(json_decode($resp)->payment_link);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Currency Not Supported.Contact To Your Site Admin'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }

    public function storeCashfreePaymentSuccess(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();
        $storepaymentSetting = Utility::getPaymentSetting($store->id);
        config(
            [
                'services.cashfree.key' => isset($storepaymentSetting['cashfree_api_key']) ? $storepaymentSetting['cashfree_api_key'] : '',
                'services.cashfree.secret' => isset($storepaymentSetting['cashfree_secret_key']) ? $storepaymentSetting['cashfree_secret_key'] : '',
            ]
        );
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->order_id . '/settlements', [
                'headers' => [
                    'accept' => 'application/json',
                    'x-api-version' => '2022-09-01',
                    "x-client-id" => config('services.cashfree.key'),
                    "x-client-secret" => config('services.cashfree.secret')
                ],
            ]);
            $respons = json_decode($response->getBody());
            if ($respons->order_id && $respons->cf_payment_id != NULL) {

                $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                    'headers' => [
                        'accept' => 'application/json',
                        'x-api-version' => '2022-09-01',
                        'x-client-id' => config('services.cashfree.key'),
                        'x-client-secret' => config('services.cashfree.secret'),
                    ],
                ]);
                $info = json_decode($response->getBody());

                if ($info->payment_status == "SUCCESS") {
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
                    return redirect()->route('store.slug', $slug)->with('error', __('Your Transaction is fail please try again'));
                }
            } else {
                return redirect()->route('store.slug', $slug)->with('error', __('Your payment is cancel'));
            }
        } catch (\Exception $e) {
            return redirect()->route('store.slug', $slug)->with('error', __($e->getMessage()));
        }
    }
}
