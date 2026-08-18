<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\PaymentTrait;

class OzowController extends Controller
{
    public    $currancy;
    use PaymentTrait;

    function generate_request_hash_check($inputString)
    {
        $stringToHash = strtolower($inputString);
        return $this->get_sha512_hash($stringToHash);
    }

    function get_sha512_hash($stringToHash)
    {
        return hash('sha512', $stringToHash);
    }

    public function planPayWithOzow(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'ozow');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $countryCode    = "ZA";
            $bankReference  = time() . 'FKU';
            $transactionReference = time();
            if ($pre_pay->plan) {

                try {
                    $cancelUrl  = route('plan.get.ozow.status', [$plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'cancel',]);
                    $errorUrl   = route('plan.get.ozow.status', [$plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'error',]);
                    $successUrl = route('plan.get.ozow.status', [$plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success',]);
                    $notifyUrl  = route('plan.get.ozow.status', [$plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'notify',]);

                    $inputString    = $pre_pay->settings['ozow_site_key'] . $countryCode . $pre_pay->currency . $pre_pay->price . $transactionReference . $bankReference . $cancelUrl . $errorUrl . $successUrl . $notifyUrl . $pre_pay->settings['ozow_mode'] . $pre_pay->settings['ozow_private_key'];
                    $hashCheck      = $this->generate_request_hash_check($inputString);

                    $data = [
                        "countryCode"           => $countryCode,
                        "amount"                => $pre_pay->price,
                        "transactionReference"  => $transactionReference,
                        "bankReference"         => $bankReference,
                        "cancelUrl"             => $cancelUrl,
                        "currencyCode"          => $pre_pay->currency,
                        "errorUrl"              => $errorUrl,
                        "isTest"                => $pre_pay->settings['ozow_mode'],
                        "notifyUrl"             => $notifyUrl,
                        "siteCode"              => $pre_pay->settings['ozow_site_key'],
                        "successUrl"            => $successUrl,
                        "hashCheck"             => $hashCheck,
                    ];

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL             => 'https://api.ozow.com/postpaymentrequest',
                        CURLOPT_RETURNTRANSFER  => true,
                        CURLOPT_ENCODING        => '',
                        CURLOPT_MAXREDIRS       => 10,
                        CURLOPT_TIMEOUT         => 0,
                        CURLOPT_FOLLOWLOCATION  => true,
                        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST   => 'POST',
                        CURLOPT_POSTFIELDS      => json_encode($data),
                        CURLOPT_HTTPHEADER      => array(
                            'Accept: application/json',
                            'ApiKey: ' . $pre_pay->settings['ozow_api_key'],
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    $json_attendance = json_decode($response, true);
                    if (isset($json_attendance['url']) && $json_attendance['url'] != null) {
                        return redirect()->away($json_attendance['url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', $response['message'] ?? __('Something went wrong'));
                    }
                } catch (\Exception $e) {
                    return redirect()->route('plans.index')->with('error', $e->getMessage());
                }
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planGetOzowStatus(Request $request, $plan_id, $amount)
    {
        try {
            if ($request->return_type == 'success') {

                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->route('plans.index')->with('error', __("The transaction has been failed"));
            }
        } catch (\Throwable $th) {
            return redirect()->route('plans.index')->with('error', __($th->getMessage()));
        }
    }

    public function storePayWithOzow(Request $request, $slug)
    {

        $pre_pay = $this->payThisProductOrder($request, $slug, 'ozow');
        if ($pre_pay->status == 'success') {

            try {

                $isTest         = isset($pre_pay->settings['ozow_mode']) && $pre_pay->settings['ozow_mode'] == 'sandbox'  ? 'true' : 'false';
                $countryCode    = "ZA";

                $bankReference  = time() . 'FKU';
                $transactionReference = time();

                $cancelUrl  = route('store.get.ozow.status', ['slug' => $slug, 'orderId' => $pre_pay->order_id, 'status' => 'cancel']);
                $errorUrl   = route('store.get.ozow.status', ['slug' => $slug, 'orderId' => $pre_pay->order_id, 'status' => 'error']);
                $successUrl = route('store.get.ozow.status', ['slug' => $slug, 'orderId' => $pre_pay->order_id, 'status' => 'success']);
                $notifyUrl  = route('store.get.ozow.status', ['slug' => $slug, 'orderId' => $pre_pay->order_id, 'status' => 'notify']);

                // Calculate the hash with the exact same data being sent
                $inputString    = $pre_pay->settings['ozow_site_key'] . $countryCode . $pre_pay->currency . $pre_pay->price . $transactionReference . $bankReference . $cancelUrl . $errorUrl . $successUrl . $notifyUrl . $isTest . $pre_pay->settings['ozow_private_key'];
                $hashCheck      = $this->generate_request_hash_check($inputString);

                $data = [
                    "countryCode"           => $countryCode,
                    "amount"                => $pre_pay->price,
                    "transactionReference"  => $transactionReference,
                    "bankReference"         => $bankReference,
                    "cancelUrl"             => $cancelUrl,
                    "currencyCode"          => $pre_pay->currency,
                    "errorUrl"              => $errorUrl,
                    "isTest"                => $isTest, // boolean value here is okay
                    "notifyUrl"             => $notifyUrl,
                    "siteCode"              => $pre_pay->settings['ozow_site_key'],
                    "successUrl"            => $successUrl,
                    "hashCheck"             => $hashCheck,
                ];

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL             => 'https://api.ozow.com/postpaymentrequest',
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_ENCODING        => '',
                    CURLOPT_MAXREDIRS       => 10,
                    CURLOPT_TIMEOUT         => 0,
                    CURLOPT_FOLLOWLOCATION  => true,
                    CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST   => 'POST',
                    CURLOPT_POSTFIELDS      => json_encode($data),
                    CURLOPT_HTTPHEADER      => array(
                        'Accept: application/json',
                        'ApiKey: ' . $pre_pay->settings['ozow_api_key'],
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $json_attendance = json_decode($response, true);
                if (isset($json_attendance['url']) && $json_attendance['url'] != null) {
                    return redirect()->away($json_attendance['url']);
                } else {
                    return redirect()->route('store.slug', $pre_pay->slug)->with('error', __('Transaction has been failed'));
                }
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', $response['message'] ?? __('Something went wrong.'));
            } catch (\Throwable $e) {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function storeGetOzowStatus(Request $request, $slug)
    {
        try {
            $status =  $this->statusThisProductOrder($request, $slug);
            if ($status->status == 'success') {
                return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
            }
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', $th->getMessage());
        }
    }
}
