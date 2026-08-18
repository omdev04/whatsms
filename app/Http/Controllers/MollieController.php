<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Auth;

class MollieController extends Controller
{
    //
    use PaymentTrait;
    //Mollie Prepare payment

    // Mollie Plan PreparePayment
    public function molliePaymentPrepare(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Mollie');
        $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($pre_pay->settings['mollie_api_key'] ?? '');
            $price = number_format((float)$pre_pay->price, 2, '.', '');


            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" => $pre_pay->settings['currency'] ?? 'USD',
                        "value" => $price,
                    ],
                    "description" => $pre_pay->plan->name,
                    "redirectUrl" => route('plan.mollie.callback', ['plan_id' => $plan_id,  'coupon_id' => $pre_pay->coupon_id,'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success']),
                    "cancelUrl" => route('plan.mollie.callback', ['plan_id' => $plan_id,  'coupon_id' => $pre_pay->coupon_id,'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'cancel']),

                ]
            );
            session()->put('mollie_payment_id', $payment->id);

            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function molliePlanGetPayment(Request $request, $plan_id, $amount)
    {

        $admin_payment_setting = Utility::getAdminPaymentSetting();

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($admin_payment_setting['mollie_api_key'] ?? '');
        if (session()->has('mollie_payment_id')) {
            $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

            if ($payment->isPaid()) {
                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);
                if ($verify->status == 'success') {
                    return redirect()->route('plans.index')->with($verify->status, $verify->message);
                }
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->back()->with('error', __('Transaction has been failed.'));

            session()->forget('mollie_payment_id');
        }
        return redirect()->back()->with('error', __('Transaction has been failed.'));
    }

    public function mollieOrder($slug, Request $request)
    {
        $pre_pay = $this->payThisProductOrder($request,$slug, 'Mollie');

        if ($pre_pay->status == 'success') {

            $request = $request->all();
            $mollie  = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($pre_pay->settings['mollie_api_key']);

            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" => $pre_pay->store->currency,
                        "value" => $pre_pay->price,
                    ],
                    "description" => "payment for product",
                    "redirectUrl" => route('mollie.callback', ['slug' => $pre_pay->slug, 'order_id' => $pre_pay->order_id, 'status' => 'success']),
                    "cancelUrl" => route('mollie.callback', ['slug' => $pre_pay->slug, 'order_id' => $pre_pay->order_id, 'status' => 'cancel']),


                ]
            );
            session()->put('mollie_payment_id', $payment->id);

            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    //Mollie Callback payment
    public function mollieCallback($slug, Request $request)
    {
        $store = Store::where('slug', $slug)->first();
        if (Auth::check() && Utility::CustomerAuthCheck($slug) == false) {
            $store_payment_setting = Utility::getPaymentSetting();
        } else {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }


        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($store_payment_setting['mollie_api_key'] ?? '');

        if (session()->has('mollie_payment_id')) {
            $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

            if ($payment->isPaid()) {

                $status =  $this->statusThisProductOrder($request, $slug);

                if ($status->status == 'success') {
                    return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                }
                return redirect()->route('store.slug', $slug)->with('error', __($status->message));
            } else {
                return redirect()->back()->with('error', __('Transaction has been failed.'));
            }
        }
    }
}
