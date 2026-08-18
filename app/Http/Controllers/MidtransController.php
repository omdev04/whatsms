<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaymentTrait;

class MidtransController extends Controller
{
    use PaymentTrait;

    public function planPayWithMidtrans(Request $request)
    {
        try {
            $pre_pay = $this->payThisPlan($request, 'Midtrans');
            if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
                $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
                \Midtrans\Config::$serverKey = isset($pre_pay->settings['midtrans_secret']) ? $pre_pay->settings['midtrans_secret'] : '';
                if (isset($pre_pay->settings['midtrans_mode']) ? $pre_pay->settings['midtrans_mode'] : 'sandbox' == 'sandbox') {
                    \Midtrans\Config::$isProduction = false;
                } else {
                    \Midtrans\Config::$isProduction = true;
                }
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;
                $params = array(
                    'transaction_details' => array(
                        'gross_amount' =>intval(round($pre_pay->price)),

                        'order_id' => $pre_pay->order_id,
                    ),
                    'customer_details' => array(
                        'first_name' => Auth::user()->name,
                        'last_name' => '',
                        'email' => Auth::user()->email,
                        'phone' => '8787878787',
                    ),
                );
                $snapToken = \Midtrans\Snap::getSnapToken($params);

                $data = [
                    'snap_token' => $snapToken,
                    'midtrans_secret' => isset($pre_pay->settings['midtrans_secret']) ? $pre_pay->settings['midtrans_secret'] : '',
                    'amount' => $pre_pay->price,
                    'coupon_id' => $pre_pay->coupon_id,
                    'plan_id' => $plan_id,
                    'status' => 'success',
                    'fallback_url' => 'plan.get.midtrans.status'
                ];

                return view('midtras.payment', compact('data'));
            } else {
                return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
            }
        } catch (\Throwable $th) {
            return redirect()->route('plans.index')->with('error', __('Something went wrong, please try again.'));
        }
    }

    public function planGetMidtransStatus(Request $request, $plan_id, $amount)
    {
        try {
            $response = json_decode($request->json, true);
            if (isset($response['status_code']) && $response['status_code'] == 200) {
                try {
                    $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false);
                    return redirect()->route('plans.index')->with($verify->status, $verify->message);
                } catch (\Exception $e) {
                    return redirect()->route('plans.index')->with('error', __($e->getMessage()));
                }
            } else {
                return redirect()->route('plans.index')->with('error', $response['status_message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function storePayWithMidtrans(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Midtrans');

        try {
            if (isset($pre_pay->settings['midtrans_secret'])) {

                \Midtrans\Config::$serverKey = isset($pre_pay->settings['midtrans_secret']) ? $pre_pay->settings['midtrans_secret'] : '';
                if (isset($pre_pay->settings['midtrans_mode']) ? $pre_pay->settings['midtrans_mode'] : 'sandbox' == 'sandbox') {
                    \Midtrans\Config::$isProduction = false;
                } else {
                    \Midtrans\Config::$isProduction = true;
                }
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                $params = array(
                    'transaction_details' => array(
                        'order_id' => $pre_pay->order_id,
                        'gross_amount' => intval(round($pre_pay->price)),
                    ),
                    'customer_details' => array(
                        'first_name' => $pre_pay->name,
                        'last_name' => '',
                        'email' => $pre_pay->email,
                        'phone' => '8787878787',
                    ),
                );
                $snapToken = \Midtrans\Snap::getSnapToken($params);


                $data = [
                    'snap_token' => $snapToken,
                    'midtrans_secret' => isset($pre_pay->settings['midtrans_secret']) ? $pre_pay->settings['midtrans_secret'] : '',
                    'slug' => $slug,
                    'order_id' => $pre_pay->order_id,
                    'status' => 'success',
                    'fallback_url' => 'store.midtrans.status'
                ];

                return view('midtras.payment', compact('data'));
            } else {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', __('Please enter valid midtrans secret key'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }
    public function getStorePaymentStatus(Request $request, $slug)
    {
        try {
            $response = json_decode($request->json, true);
            if (isset($response['status_code']) && $response['status_code'] == 200) {
                $status =  $this->statusThisProductOrder($request, $slug);
                if ($status->status == 'success') {
                    return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                }
                return redirect()->route('store.slug', $slug)->with('error', __($status->message));
            } else {
                return redirect()->route('store.slug', $slug)->with('error', $response['status_message']);
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }
}
