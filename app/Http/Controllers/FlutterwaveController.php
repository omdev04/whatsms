<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FlutterwaveController extends Controller
{
    //
    use PaymentTrait;
    public function planPayWithflutterwave(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Flutterwave');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
            $user = Auth::user();
            return redirect()->route('flutterwave.pay', encrypt([
                'email'         => $user->email,
                'name'          => $user->name,
                'price'         => $pre_pay->price ?? 0,
                'redirect_url'  => route('flutterwave.plan.callback', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success']),
                'currency'      => $pre_pay->currency ?? 'USD',
                'public_key'    => $pre_pay->settings['flutterwave_public_key'] ?? '',
            ]));
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function flutterwaveGetPaymentStatus(Request $request, $plan_id, $amount)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();

        $secret_key             = $admin_payment_setting['flutterwave_secret_key'] ?? '';

        $transaction_id = $request->transaction_id;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secret_key,
        ])->get("https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify");


        if ($response->successful() && $response['status'] === 'success') {
            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

            if ($verify->status == 'success') {
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        } else {
            return redirect()->route('plans.index') > with('error', __('Payment verification failed.'));
        }
    }

    public function PayWithflutterwave(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Flutterwave');

        if ($pre_pay->status == 'success') {

            return redirect()->route('flutterwave.pay', encrypt([
                'email'         => $pre_pay->email,
                'name'          => $pre_pay->name,
                'price'         => $pre_pay->price ?? 0,
                'redirect_url'  => route('get.flutterwave.status', [$pre_pay->slug, 'status' => 'success']),
                'currency'      => $pre_pay->currency ?? 'USD',
                'public_key'    => $pre_pay->settings['flutterwave_public_key'] ?? '',
            ]));
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $pre_pay->message);
        }
    }


    public function flutterwavePayment(Request $request, $slug)
    {

        $store = Store::where('slug', $slug)->first();
        $store_payment_setting = Utility::getPaymentSetting($store->id);
        $secret_key             = $store_payment_setting['flutterwave_secret_key'] ?? '';

        $transaction_id = $request->transaction_id;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secret_key,
        ])->get("https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify");


        if ($response->successful() && $response['status'] === 'success') {
            // Payment successful, process order
            $status = $this->statusThisProductOrder($request, $slug);
            return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
        } else {
            return redirect()->route('store.slug', $slug)->with('error', __('Payment verification failed.'));
        }
    }
    public function Checkout($pay)
    {
        $data = decrypt($pay);
        return view('Flutterwave.pay', compact('data'));
    }
}
