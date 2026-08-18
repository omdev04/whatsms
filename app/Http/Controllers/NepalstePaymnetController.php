<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\PaymentTrait;

class NepalstePaymnetController extends Controller
{
    use PaymentTrait;

    public function planPayWithNepalste(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Nepalste');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
            try {
                $parameters = [
                    'identifier'        => 'DFU80XZIKS',
                    'currency'          => $pre_pay->currency,
                    'amount'            => $pre_pay->price,
                    'details'           => $pre_pay->plan->name,
                    'ipn_url'           => route('nepalste.status', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id,  'status' => 'notify']),
                    'cancel_url'        => route('nepalste.status', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id,  'status' => 'cancel']),
                    'success_url'       => route('nepalste.status', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id,  'status' => 'success']),
                    'public_key'        => $pre_pay->settings['nepalste_public_key'] ?? '',
                    'site_logo'         => 'https://nepalste.com.np/assets/images/logoIcon/logo.png',
                    'checkout_theme'    => 'dark',
                    'customer_name'     => 'John Doe',
                    'customer_email'    => 'john@mail.com',
                ];

                if ($pre_pay->settings['nepalste_mode']  == 'live') {
                    //live end point
                    $url = "https://nepalste.com.np/payment/initiate";
                } else {
                    //test end point
                    $url = "https://nepalste.com.np/sandbox/payment/initiate";
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS,  $parameters);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($result, true);

                if (isset($result['success'])) {
                    return redirect($result['url']);
                } else {
                    return redirect()->route('plans.index')->with('error', __($result['message']));
                }
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planGetNepalsteStatus(Request $request, $plan_id, $amount)
    {
        try {
            if ($request->status == 'success') {
                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->route('plans.index')->with('error', __("The transaction has been failed"));
            }
        } catch (\Throwable $th) {
            return redirect()->route('plans.index')->with('error', __($th->getMessage()));
        }
    }

    public function planGetNepalsteCancel(Request $request)
    {
        return redirect()->back()->with('error', __('Transaction has failed'));
    }

    public function storePayWithNepalste(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Nepalste');
        try {
            $parameters = [
                'identifier'        => 'DFU80XZIKS',
                'currency'          => $pre_pay->currency,
                'amount'            => $pre_pay->price,
                'details'           => $slug,
                'ipn_url'           => route('store.nepalste.status', ['slug' => $slug, 'order_id' => $pre_pay->order_id, 'status' => 'fail']),
                'cancel_url'        => route('store.nepalste.status', ['slug' => $slug, 'order_id' => $pre_pay->order_id, 'status' => 'cancel']),
                'success_url'       => route('store.nepalste.status', ['slug' => $slug, 'order_id' => $pre_pay->order_id, 'status' => 'success']),
                'public_key'        => $pre_pay->settings['nepalste_public_key'] ?? '',
                'site_logo'         => 'https://nepalste.com.np/assets/images/logoIcon/logo.png',
                'checkout_theme'    => 'dark',
                'customer_name'     => 'John Doe',
                'customer_email'    => 'john@mail.com',
            ];

            if ($pre_pay->settings['nepalste_mode'] ?? '' == 'live') {
                //live end point
                $url = "https://nepalste.com.np/payment/initiate";
            } else {
                //test end point
                $url = "https://nepalste.com.np/sandbox/payment/initiate";
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $parameters);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result, true);

            if (isset($result['success'])) {
                return redirect($result['url']);
            } else {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', __($result['message']));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }

    public function getStorePaymentStatus(Request $request, $slug)
    {
        try {
            $status =  $this->statusThisProductOrder($request, $slug);
            if ($status->status == 'success') {
                return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
            }
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        } catch (\Throwable $th) {

            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }

    public function storeGetNepalsteCancel(Request $request)
    {
        return redirect()->back()->with('error', __('Transaction has failed'));
    }
}
