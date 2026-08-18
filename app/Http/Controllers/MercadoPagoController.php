<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Auth;

class MercadoPagoController extends Controller
{
    //
    use PaymentTrait;
    public function mercadopagoPaymentPrepare(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'MercadoPago');
        $user = Auth::user();
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $admin_payment_setting = Utility::getAdminPaymentSetting();

            \MercadoPago\SDK::setAccessToken($admin_payment_setting['mercado_access_token']);
            try {

                $preference = new \MercadoPago\Preference();
                $item              = new \MercadoPago\Item();
                $item->title       = "Plan : " . $pre_pay->plan->name;
                $item->quantity    = 1;
                $item->unit_price  = (float)$pre_pay->plan->price;
                $preference->items = array($item);
                $success_url       = route('plan.mercado.payment.callback', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success']);
                $failure_url       = route('plan.mercado.payment.callback', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'failure']);
                $pending_url       = route('plan.mercado.payment.callback', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'pending']);

                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url,
                );

                $preference->auto_return = "approved";
                $preference->save();

                $payer = new \MercadoPago\Payer();
                $payer->name    = $user->name;
                $payer->email   = $user->email;
                $payer->address = array(
                    "street_name" => '',
                );
                if ($admin_payment_setting['mercado_mode'] == 'live') {
                    $redirectUrl = $preference->init_point;
                } else {
                    $redirectUrl = $preference->sandbox_init_point;
                }

                return redirect($redirectUrl);
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function mercadopagoPaymentCallback(Request $request, $plan_id, $amount)
    {
        try {
            $status = $request->status == 'approved' ? 'success' : 'cancel';
            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $status, false,);
            if ($verify->status == 'success') {
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    //Mercado Pago Prepare Payment
    public function mercadopagoStorePayment($slug, Request $request)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'MercadoPago');
        if ($pre_pay->status == 'success') {

            \MercadoPago\SDK::setAccessToken($pre_pay->settings['mercado_access_token']);
            try {
                $preference = new \MercadoPago\Preference();
                $item              = new \MercadoPago\Item();
                $item->title       = $pre_pay->store->name . "Order";
                $item->quantity    = 1;
                $item->unit_price  = $pre_pay->price;
                $preference->items = array($item);
                $success_url       = route('mercadopago.store.callback', [$pre_pay->slug, 'status' => 'success']);
                $failure_url       = route('mercadopago.store.callback', [$pre_pay->slug, 'status' => 'failure']);
                $pending_url       = route('mercadopago.store.callback', [$pre_pay->slug, 'status' => 'pending']);
                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url,
                );
                $preference->auto_return = "approved";
                $preference->save();

                $payer = new \MercadoPago\Payer();
                $payer->name    = $pre_pay->name;
                $payer->email   = $pre_pay->email;
                $payer->address = array(
                    "street_name" => '',
                );
                if ($pre_pay->settings['mercado_mode'] == 'live') {
                    $redirectUrl = $preference->init_point;
                } else {
                    $redirectUrl = $preference->sandbox_init_point;
                }

                return redirect($redirectUrl);
            } catch (\Exception $e) {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    //Mercado Pago
    public function mercadopagoStoreCallback($slug, Request $request)
    {
        try {
            $request->status = ($request->status == 'approved') ? 'success' : 'cancel';

            $status =  $this->statusThisProductOrder($request, $slug);
            if ($status->status == 'success') {
                return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
            }
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
