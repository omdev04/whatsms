<?php

namespace App\Http\Controllers;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Models\Utility;
use App\Traits\PaymentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaytmPaymentController extends Controller
{
    use PaymentTrait;

    public function planpaymentSetting()
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        config(
            [
                'services.paytm-wallet.env' => isset($admin_payment_setting['paytm_mode']) ? $admin_payment_setting['paytm_mode'] : '',
                'services.paytm-wallet.merchant_id' => isset($admin_payment_setting['paytm_merchant_id']) ? $admin_payment_setting['paytm_merchant_id'] : '',
                'services.paytm-wallet.merchant_key' =>  isset($admin_payment_setting['paytm_merchant_key']) ? $admin_payment_setting['paytm_merchant_key'] : '',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' => isset($admin_payment_setting['paytm_industry_type']) ? $admin_payment_setting['paytm_industry_type'] : '',
            ]
        );
    }

    public function paytmPaymentPrepare(Request $request)
    {
        $this->planpaymentSetting();
        $pre_pay = $this->payThisPlan($request, 'Paytm');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $validator = \Validator::make(
                $request->all(),
                [
                    'plan_id' => 'required',
                    'total_price' => 'required',
                    'mobile_number' => 'required|numeric',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $data =  [
                'plan_id' => $plan_id,
                'coupon_id' => $pre_pay->coupon_id,
                'amount' => $pre_pay->price,
                'status' => 'success',
            ];
            $payment = PaytmWallet::with('receive');
            $payment->prepare(
                [
                    'order' => $pre_pay->order_id,
                    'user' => Auth::user()->id,
                    'mobile_number' => $pre_pay->mobile_number,
                    'email' => Auth::user()->email,
                    'amount' => $pre_pay->price,
                    'callback_url' => route('plan.paytm.callback', $data),
                ]
            );

            return $payment->receive();
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function paytmPlanGetPayment(Request $request)
    {
        try {
            $plan_id               = $request->plan_id;

            $this->planpaymentSetting();

            $transaction = PaytmWallet::with('receive');
            $response = $transaction->response();

            if ($transaction->isSuccessful()) {
                $verify =  $this->statusThisPlan($request, $plan_id, $request->amount, $request->status, false);
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->route('plans.index')->with('error', __('Transaction Unsuccesfull'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('plans.index')->with('error', __('Something went wrong, please try again.'));
        }
    }

    public function paytmOrder($slug, Request $request)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Paytm');

        try {
            $this->planpaymentSetting();
            $data =  [
                'slug' => $slug,
                'order_id' => $pre_pay->order_id,
                'status' => 'success',
            ];
            $payment = PaytmWallet::with('receive');
            $payment->prepare(
                [
                    'order' => $pre_pay->order_id,
                    'user' => $pre_pay->id,
                    'mobile_number' => $pre_pay->phone,
                    'email' => $pre_pay->email,
                    'amount' => $pre_pay->price,
                    'callback_url' => route('paytm.callback', $data),
                ]
            );
            return $payment->receive();
        } catch (\Throwable $th) {

            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }

    public function paytmCallback(Request $request)
    {
        try {
            $slug     = $request->slug;
            $this->planpaymentSetting();

            $transaction = PaytmWallet::with('receive');

            $response = $transaction->response();
            if ($transaction->isSuccessful()) {
                $status =  $this->statusThisProductOrder($request, $slug);
                if ($status->status == 'success') {
                    return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                }
                return redirect()->route('store.slug', $slug)->with('error', __($status->message));
            } else if ($transaction->isFailed()) {
                return redirect()->route('store.slug', $slug)->with('error', __('Transaction Unsuccesfull'));
            } else if ($transaction->isOpen()) {
                return redirect('/');
            } else {
                return redirect()->route('store.slug', $slug)->with('error', __('Payment not made'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', __('Something went wrong, please try again.'));
        }
    }
}
