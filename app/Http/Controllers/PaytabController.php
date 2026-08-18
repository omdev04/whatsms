<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Store;
use App\PayTab\paypage;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Auth;

class PaytabController extends Controller
{
    use PaymentTrait;
    public $paytab_profile_id, $paytab_server_key, $paytab_region, $is_enabled;

    public function paymentConfig()
    {
        if (Auth::check()) {
            $payment_setting = Utility::getAdminPaymentSetting();
            config([
                'paytabs.profile_id' =>  $payment_setting['paytab_profile_id'] ?? '',
                'paytabs.server_key' => $payment_setting['paytab_server_key'] ?? '',
                'paytabs.region' => $payment_setting['paytab_region'] ?? '',
                'paytabs.currency' => $payment_setting['currency'] ?? 'USD',
            ]);
        }
    }
    public function planPayWithpaytab(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Paytab');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $user = Auth::user();
            $this->paymentconfig();

            $paypage = new paypage();
            $pay = $paypage->sendPaymentCode('all')
                ->sendTransaction('sale')
                ->sendCart(1, $pre_pay->price, 'plan payment')
                ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                ->sendURLs(
                    route('plan.paytab.success', ['status' => 'success', 'plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'success' => 1]),
                    route('plan.paytab.success', ['status' => 'cancel', 'plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'success' => 0])
                )
                ->sendLanguage('en')
                ->sendFramed($on = false)
                ->create_pay_page();
            return $pay;
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }
    public function PaytabGetPayment(Request $request, $plan_id, $amount)
    {

        if ($request->success == "1") {
            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);
            if ($verify->status == 'success') {
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        } else {
            return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
        }
    }
    public function PayWithpaytab(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Paytab');

        $store = Store::where('slug', $slug)->first();
        $companyPaymentSetting = Utility::getPaymentSetting($store->id);
        if ($pre_pay->status == 'success') {

            config([
                'paytabs.profile_id' =>  $companyPaymentSetting['paytab_profile_id'] ?? '',
                'paytabs.server_key' =>  $companyPaymentSetting['paytab_server_key'] ?? '',
                'paytabs.region' => $companyPaymentSetting['paytab_region'] ?? '',
                'paytabs.currency' => $store->currency,
            ]);
            $paypage = new paypage();
            $pay = $paypage->sendPaymentCode('all')
                ->sendTransaction('sale')
                ->sendCart(1, $pre_pay->price, 'store payment')
                ->sendCustomerDetails(isset($pre_pay->name) ? $pre_pay->name : "", isset($pre_pay->email) ? $pre_pay->email : '', '', '', '', '', '', '', '')
                ->sendURLs(
                    route('paytab.success', ['slug' => $pre_pay->slug, 'order_id' => $pre_pay->order_id, 'status' => 'success', 'success' => 1]),
                    route('paytab.success', ['slug' => $pre_pay->slug, 'order_id' => $pre_pay->order_id, 'status' => 'success', 'success' => 0])
                )
                ->sendLanguage('en')
                ->sendFramed($on = false)
                ->create_pay_page();

            return $pay;
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function PaytabGetPaymentCallback(Request $request, $slug)
    {
        if ($request->success == "1") {
            $status =  $this->statusThisProductOrder($request, $slug);

            if ($status->status == 'success') {
                return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
            }
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        } else {
            return redirect()->route('store.slug', $slug)->with('error', __('Your Transaction is fail please try again'));
        }
    }
}
