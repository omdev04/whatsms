<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use YooKassa\Client;
use App\Traits\PaymentTrait;
use App\Models\Store;

class YooKassaController extends Controller
{
    use PaymentTrait;

    public function planPayWithYooKassa(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'YooKassa');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $yookassa_shop_id = isset($pre_pay->settings['yookassa_shop_id']) ? $pre_pay->settings['yookassa_shop_id'] : '';
            $yookassa_secret_key = isset($pre_pay->settings['yookassa_secret']) ? $pre_pay->settings['yookassa_secret'] : '';

            try {
                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $pre_pay->price,
                                'currency' => isset($pre_pay->currency) ? $pre_pay->currency : 'USD',
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route('plan.get.yookassa.status', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'status' => 'success']),
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );

                    Session::put('payment_id', $payment['id']);

                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', __('Something went wrong, Please try again'));
                    }
                } else {
                    return redirect()->route('plans.index')->with('error', __('Please Enter Valid Shop Id Key'));
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th);
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }
    public function planGetYooKassaStatus(Request $request, $plan_id, $amount)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];

        if (is_int((int)$yookassa_shop_id)) {
            $client = new Client();
            $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
            $paymentId = Session::get('payment_id');
            Session::forget('payment_id');
            if ($paymentId == null) {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            $payment = $client->getPaymentInfo($paymentId);

            if (isset($payment) && $payment->status == "succeeded") {
                try {
                    $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false);
                    return redirect()->route('plans.index')->with($verify->status, $verify->message);
                } catch (\Exception $e) {
                    return redirect()->route('plans.index')->with('error', __($e->getMessage()));
                }
            } else {
                return redirect()->route('plans.index')->with('error', __('Please Enter Valid Shop Id Key'));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Something went wrong, please try again.'));
        }
    }


    public function storePayWithYookassa(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'YooKassa');

        try {
            $yookassa_shop_id = isset($pre_pay->settings['yookassa_shop_id']) ? $pre_pay->settings['yookassa_shop_id'] : '';
            $yookassa_secret  = isset($pre_pay->settings['yookassa_secret']) ? $pre_pay->settings['yookassa_secret'] : '';

            if (is_int((int)$yookassa_shop_id)) {
                $client = new Client();
                $client->setAuth((int)$yookassa_shop_id, $yookassa_secret);
                $payment = $client->createPayment(
                    array(
                        'amount' => array(
                            'value' => $pre_pay->price,
                            'currency' => isset($pre_pay->currency) ? $pre_pay->currency : 'USD',
                        ),
                        'confirmation' => array(
                            'type' => 'redirect',
                            'return_url' => route('store.yookassa.status', [
                                'slug' => $slug,
                                'order_id' => $pre_pay->order_id,
                                'status' => 'success',
                            ]),
                        ),
                        'capture' => true,
                        'description' => 'Заказ №1',
                    ),
                    uniqid('', true)
                );

                Session::put('product_payment_id', $payment['id']);

                if ($payment['confirmation']['confirmation_url'] != null) {
                    return redirect($payment['confirmation']['confirmation_url']);
                } else {
                    return redirect()->route('store.slug', $slug)->with('error', __('Something went wrong, Please try again'));
                }
            } else {
                return redirect()->back()->with('error', __('Please Enter Valid Shop Id Key'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }

    public function getStorePaymentStatus(Request $request, $slug)
    {
        try {
            $store = Store::where('slug', $slug)->first();

            $payment_setting = Utility::getPaymentSetting($store->id);

            $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
            $yookassa_secret_key = $payment_setting['yookassa_secret'];

            if (is_int((int)$yookassa_shop_id)) {
                $client = new Client();
                $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                $paymentId = Session::get('product_payment_id');
                Session::forget('product_payment_id');
                if ($paymentId == null) {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }

                $payment = $client->getPaymentInfo($paymentId);

                if (isset($payment) && $payment->status == "succeeded") {
                    $status =  $this->statusThisProductOrder($request, $slug);
                    if ($status->status == 'success') {
                        return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                    }
                    return redirect()->route('store.slug', $slug)->with('error', __($status->message));
                } else {
                    return redirect()->route('store.slug', $slug)->with('error', __('Please Enter Valid Shop Id Key'));
                }
            } else {
                return redirect()->route('store.slug', $slug)->with('error', __('Something went wrong, please try again.'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }
}
