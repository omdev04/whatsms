<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Utility;
use App\Traits\PaymentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CinetPayController extends Controller
{
    use PaymentTrait;

    public function planPayWithCinetPay(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'CinetPay');

        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
            $authuser           = Auth::user();

            try {
                if ($pre_pay->currency != 'XOF' && $pre_pay->currency != 'CDF' && $pre_pay->currency != 'USD' && $pre_pay->currency != 'KMF' && $pre_pay->currency != 'GNF') {
                    return redirect()->route('plans.index')->with('error', __('Availabe currencies: XOF, CDF, USD, KMF, GNF'));
                }

                $cinetpay_data =  [
                    "amount"                => $pre_pay->price,
                    "currency"              => $pre_pay->currency,
                    "apikey"                => $pre_pay->settings['cinetpay_api_key'] ?? '',
                    "site_id"               => $pre_pay->settings['cinetpay_site_id'] ?? '',
                    "transaction_id"        => $pre_pay->order_id,
                    "description"           => "Plan purchase",
                    "return_url"            => route('plan.cinetpay.return', [$plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success']) . '?_token=' . csrf_token(),
                    "metadata"              => "user001",
                    'customer_name'         => isset($authuser->name) ? $authuser->name : 'Test',
                    'customer_surname'      => isset($authuser->name) ? $authuser->name : 'Test',
                    'customer_email'        => isset($authuser->email) ? $authuser->email : 'test@gmail.com',
                    'customer_phone_number' => isset($authuser->mobile_number) ? $authuser->mobile_number : '1234567890',
                    'customer_address'      => isset($authuser->address) ? $authuser->address  : 'A-101, alok area, USA',
                    'customer_city'         => 'texas',
                    'customer_country'      => 'BF',
                    'customer_state'        => 'USA',
                    'customer_zip_$authusercode'     => isset($authuser->zipcode) ? $authuser->zipcode : '432876',
                ];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 45,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($cinetpay_data),
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTPHEADER => array(
                        "content-type:application/json"
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                //On recupère la réponse de CinetPay
                $response_body = json_decode($response, true);

                if (isset($response_body['code']) && $response_body['code'] == '201') {

                    $payment_link = $response_body["data"]["payment_url"]; // Retrieving the payment URL
                    return redirect($payment_link);
                } else {
                    return redirect()->route('plans.index')->with('error', __("The currency has been not supported!"));
                }
            } catch (\Exception $e) {
                Log::debug($e->getMessage());
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planCinetPayReturn(Request $request, $plan_id, $amount)
    {

        if (isset($request->transaction_id) || isset($request->token) && $request->status == 'success') {
            $payment_setting = Utility::getAdminPaymentSetting();

            $cinetpay_check = [
                "apikey"            => $payment_setting['cinetpay_api_key'] ?? '',
                "site_id"           => $payment_setting['cinetpay_site_id'] ?? '',
                "transaction_id"    => $request->transaction_id ?? null
            ];

            $response       = $this->getPayStatus($cinetpay_check);
            $response_body  = json_decode($response, true);

            if (isset($response_body['code']) && $response_body['code'] == '00') {

                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->route('plans.index')->with('error', __("The transaction has been failed"));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Your Payment has failed!'));
        }
    }


    public function getPayStatus($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment/check',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "content-type:application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return redirect()->back()->with('error', __('Something went wrong!'));
        } else {
            return ($response);
        }
    }

    public function storePayWithCinetPay(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'CinetPay');

        if ($pre_pay->status == 'success') {
            try {
                $cinetpay_api_key   = $pre_pay->settings['cinetpay_api_key'] ?? '';
                $cinetpay_site_id   = $pre_pay->settings['cinetpay_site_id'] ?? '';
                try {
                    if (
                        $pre_pay->currency != 'XOF' &&
                        $pre_pay->currency != 'CDF' &&
                        $pre_pay->currency != 'USD' &&
                        $pre_pay->currency != 'KMF' &&
                        $pre_pay->currency != 'GNF'
                    ) {
                        return redirect()->route('store.slug', $pre_pay->slug)->with('error', __('Availabe currencies: XOF, CDF, USD, KMF, GNF'));
                    }

                    $cinetpay_data =  [
                        "amount"                => $pre_pay->price,
                        "currency"              => $pre_pay->currency,
                        "apikey"                => $cinetpay_api_key,
                        "site_id"               => $cinetpay_site_id,
                        "transaction_id"        => $pre_pay->order_id,
                        "description"           => "Product purchase",
                        "return_url"            => route('store.cinetpay.return', ['slug' => $slug, 'order_id' => $pre_pay->order_id, 'status' => 'success']) . '?_token=' . csrf_token(),
                        "metadata"              => "user001",
                        'customer_name'         => isset($pre_pay->name) ? $pre_pay->name : 'Test',
                        'customer_surname'      => isset($pre_pay->name) ? $pre_pay->name : 'Test',
                        'customer_email'        => isset($pre_pay->email) ? $pre_pay->email : 'test@gmail.com',
                        'customer_phone_number' => isset($pre_pay->phone) ? $pre_pay->phone : '1234567890',
                        'customer_address'      => isset($pre_pay->billing_address) || isset($pre_pay->shipping_address) ? $pre_pay->billing_address . ' ' . $pre_pay->shipping_address : 'A-101, alok area, USA',
                        'customer_city'         => 'texas',
                        'customer_country'      => 'BF',
                        'customer_state'        => 'USA',
                        'customer_zip_code'     => isset($pre_pay->zipcode) ? $pre_pay->zipcode : '432876',
                    ];

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 45,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($cinetpay_data),
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_HTTPHEADER => array(
                            "content-type:application/json"
                        ),
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);

                    //On recupère la réponse de CinetPay
                    $response_body = json_decode($response, true);

                    if (isset($response_body['code']) && $response_body['code'] == '201') {

                        $payment_link = $response_body["data"]["payment_url"]; // Retrieving the payment URL
                        return redirect($payment_link);
                    } else {
                        return redirect()->route('store.slug', $pre_pay->slug)->with('error', $response_body["description"]);
                    }
                } catch (\Exception $e) {
                    return redirect()->route('store.slug', $pre_pay->slug)->with('error', $e->getMessage());
                }
            } catch (\Throwable $th) {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function storeCinetPayReturn(Request $request, $slug)
    {


        if (isset($request->transaction_id) || isset($request->token)) {

            $store                  = Store::where('slug', $slug)->first();
            $companyPaymentSetting  = Utility::getPaymentSetting($store->id);

            $cinetpay_check = [
                "apikey"            => $companyPaymentSetting['cinetpay_api_key'] ?? '',
                "site_id"           => $companyPaymentSetting['cinetpay_site_id'] ?? '',
                "transaction_id"    => $request->transaction_id ?? null
            ];

            $response       = $this->getPayStatus($cinetpay_check);
            $response_body  = json_decode($response, true);
            if (isset($response_body['code']) && $response_body['code'] == '00') {
                $status =  $this->statusThisProductOrder($request, $slug);

                if ($status->status == 'success') {
                    return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                }
                return redirect()->route('store.slug', $slug)->with('error', __($status->message));
            } else {
                return redirect()->route('store.slug', $slug)->with('error', __('Your Payment has failed!'));
            }
        } else {
            return redirect()->route('store.slug', $slug)->with('error', __('Your Payment has failed!'));
        }
    }
}
