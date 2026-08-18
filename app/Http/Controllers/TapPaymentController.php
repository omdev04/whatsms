<?php

namespace App\Http\Controllers;

use App\Package\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaymentTrait;

class TapPaymentController extends Controller
{
    use PaymentTrait;

    public function planPayWithTap(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Tap');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
            $authuser = Auth::user();

            try {
                $TapPay = new Payment(['tap_secret_key' => $pre_pay->settings['tap_secret_key'] ?? '']);
                return $TapPay->charge([
                    'amount' => $pre_pay->price,
                    'currency' => $pre_pay->currency,
                    'threeDSecure' => 'true',
                    'description' => 'test description',
                    'statement_descriptor' => 'sample',
                    'customer' => ['first_name' => $authuser->name, 'email' => $authuser->email,],
                    'source' => ['id' => 'src_card'],
                    'post' => ['url' => null],
                    // 'merchant' => ['id' => 'YOUR-MERCHANT-ID'  //Include this when you are going to live ],
                    'redirect' => [
                        'url' => route('plan.get.tap.status', [$plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success'])
                    ]
                ], true);
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planGetTapStatus(Request $request, $plan_id, $amount)
    {
        try {
            if ($request->status == 'success') {
                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->route('plans.index')->with('error', __("The transaction has been failed"));
            }
        } catch (\Throwable $th) {
            return redirect()->route('plans.index')->with('error', $th->getMessage());
        }
    }

    public function storePayWithTap(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Tap');
        if ($pre_pay->status == 'success') {

            try {
                $TapPay = new Payment(['tap_secret_key' => $pre_pay->settings['tap_secret_key'] ?? '']);
                return $TapPay->charge([
                    'amount' => $pre_pay->price,
                    'currency' => !empty($pre_pay->currency) ?  $pre_pay->currency : 'USD',
                    'threeDSecure' => 'true',
                    'description' => 'test description',
                    'statement_descriptor' => 'sample',
                    'customer' => ['first_name' => isset($request->name) ? $request->name : '', 'email' => isset($request->email) ? $request->email : '',],
                    'source' => ['id' => 'src_card'],
                    'post' => ['url' => null],
                    // 'merchant' => [
                    //    'id' => 'YOUR-MERCHANT-ID'  //Include this when you are going to live
                    // ],
                    'redirect' => ['url' => route('store.tap.status', ['slug' => $slug, 'orderId' => $pre_pay->order_id, 'status' => 'success'])]
                ], true);
            } catch (\Throwable $e) {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function storeGetTapStatus(Request $request, $slug)
    {
        try {
            $status =  $this->statusThisProductOrder($request, $slug);
            if ($status->status == 'success') {
                return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
            }
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        } catch (\Exception $e) {
            return redirect()->route('store.slug', $slug)->with('error', $e->getMessage());
        }
    }
}
