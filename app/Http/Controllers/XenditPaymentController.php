<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use App\Xendit\Invoice;
use App\Xendit\Xendit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\PaymentTrait;

class XenditPaymentController extends Controller
{
    use PaymentTrait;

    public function planPayWithXendit(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Xendit');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $xendit_api = isset($pre_pay->settings['xendit_api']) ? $pre_pay->settings['xendit_api'] : '';

            Xendit::setApiKey($xendit_api);
            $params = [
                'external_id' => $pre_pay->order_id,
                'payer_email' => Auth::user()->email,
                'description' => 'Payment for order ' . $pre_pay->order_id,
                'amount' => $pre_pay->price,
                'callback_url' =>  route('plan.xendit.status', ['amount' => $pre_pay->price, 'coupon_id' => $pre_pay->coupon_id, 'plan_id' => $plan_id, 'status' => 'notify']),
                'success_redirect_url' => route('plan.xendit.status', ['amount' => $pre_pay->price, 'coupon_id' => $pre_pay->coupon_id, 'plan_id' => $plan_id, 'status' => 'success']),
                'failure_redirect_url' => route('plan.xendit.status', ['amount' => $pre_pay->price, 'coupon_id' => $pre_pay->coupon_id, 'plan_id' => $plan_id, 'status' => 'failure']),
            ];

            $invoice = Invoice::create($params);
            Session::put('invoice', $invoice);

            return redirect($invoice['invoice_url']);
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planGetXenditStatus(Request $request, $plan_id, $amount)
    {
        try {
            $payment_setting = Utility::getAdminPaymentSetting();
            $xendit_api = $payment_setting['xendit_api'];
            Xendit::setApiKey($xendit_api);

            $session = Session::get('invoice');
            $getInvoice = Invoice::retrieve($session['id']);

            if ($getInvoice['status'] == 'PAID') {

                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false);
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with('error', __('Transaction has been failed!'));
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function storePayWithXendit(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Xendit');

        try {
            $xendit_api = isset($pre_pay->settings['xendit_api']) ? $pre_pay->settings['xendit_api'] : '';

            Xendit::setApiKey($xendit_api);
            $params = [
                'external_id' => $pre_pay->order_id,
                'payer_email' => $pre_pay->email,
                'description' => 'Payment for order ' . $pre_pay->order_id,
                'amount' => $pre_pay->price,
                'callback_url' =>  route('store.xendit.status', ['slug' => $slug, 'orderId' => $pre_pay->order_id, 'status' => 'success']),
                'success_redirect_url' => route('store.xendit.status', ['slug' => $slug, 'orderId' => $pre_pay->order_id, 'status' => 'success']),
            ];

            $Xenditinvoice = Invoice::create($params);
            Session::put('invoicepay', $Xenditinvoice);
            return redirect($Xenditinvoice['invoice_url']);
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }

    public function getStorePaymentStatus(Request $request, $slug)
    {
        try {
            $payment_setting = Utility::getAdminPaymentSetting();
            $xendit_api = $payment_setting['xendit_api'];
            Xendit::setApiKey($xendit_api);
            $session = Session::get('invoicepay');
            $getInvoice = Invoice::retrieve($session['id']);
            if ($getInvoice['status'] == 'PAID') {
                $status =  $this->statusThisProductOrder($request, $slug);

                if ($status->status == 'success') {
                    return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                }

                return redirect()->route('store.slug', $slug)->with('error', __($status->message));
            } else {
                return redirect()->route('store.slug', $slug)->with('error', __('Transaction has been failed.'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }
}
