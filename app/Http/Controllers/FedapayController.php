<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\PaymentTrait;

class FedapayController extends Controller
{

    use PaymentTrait;

    public function planPayWithFedapay(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Fedapay');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $fedapay            = !empty($pre_pay->settings['fedapay_secret_key']) ? $pre_pay->settings['fedapay_secret_key'] : '';
            $fedapay_mode       = !empty($pre_pay->settings['fedapay_mode']) ? $pre_pay->settings['fedapay_mode'] : 'sandbox';

            try {
                \FedaPay\FedaPay::setApiKey($fedapay);

                \FedaPay\FedaPay::setEnvironment($fedapay_mode);
                $transaction = \FedaPay\Transaction::create([
                    "description"   => "Fedapay Payment",
                    "amount"        => (int) $pre_pay->price,
                    "currency"      => ["iso" => $pre_pay->currency],

                    "callback_url"  => route('plan.pay.fedapay.status', [
                        'plan_id'       => $plan_id,
                        "coupon_id"     => $pre_pay->coupon_id,
                        "amount"        => $pre_pay->price,
                        'order_id'      => $pre_pay->order_id,
                        "status"        => 'callback'
                    ]),
                    "cancel_url"    => route('plan.pay.fedapay.status', [
                        'plan_id'       => $plan_id,
                        "coupon_id"     => $pre_pay->coupon_id,
                        "amount"        => $pre_pay->price,
                        'order_id'      => $pre_pay->order_id,
                        "status"        => 'cancel'
                    ]),
                ]);
                $token = $transaction->generateToken();

                return redirect($token->url);
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planGetFedapayStatus(Request $request, $plan_id, $amount)
    {
        try {
            if ($request->status == 'approved') {
                $request->status = 'success';

                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->route('plans.index')->with('error', __("The transaction has been failed"));
            }
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function storePayWithFedapay(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Fedapay');
        try {
            $fedapay       = !empty($pre_pay->settings['fedapay_secret_key']) ? $pre_pay->settings['fedapay_secret_key'] : '';
            $fedapay_mode  = !empty($pre_pay->settings['fedapay_mode']) ? $pre_pay->settings['fedapay_mode'] : 'sandbox';
            $currency      = isset($pre_pay->currency) ? $pre_pay->currency : 'USD';

            try {
                \FedaPay\FedaPay::setApiKey($fedapay);

                \FedaPay\FedaPay::setEnvironment($fedapay_mode);

                $transaction = \FedaPay\Transaction::create([
                    "description"  => "Fedapay Payment",
                    "amount"       => (int) $pre_pay->price,
                    "currency"     => ["iso" => $currency],
                    "callback_url" => route('store.fedapay.status', [
                        'slug'     => $slug,
                        'orderId'  => $pre_pay->order_id,
                        'status'   => 'success',
                    ]),
                    "cancel_url"   => route('store.fedapay.status', [
                        'slug'     => $slug,
                        'orderId'  => $pre_pay->order_id,
                        'status'   => 'cancel',
                    ]),
                ]);
                $token = $transaction->generateToken();

                return redirect($token->url);
            } catch (\Exception $e) {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', $e->getMessage());
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }
    public function getStorePaymentFedapayStatus(Request $request, $slug)
    {
        try {
            $request->status = 'success';
            $status =  $this->statusThisProductOrder($request, $slug);

            if ($status->status == 'success') {
                return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
            }
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        } catch (\Throwable $th) {

            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }
}
