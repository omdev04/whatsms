<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lahirulhr\PayHere\PayHere;
use App\Traits\PaymentTrait;

class PayHereController extends Controller
{
    use PaymentTrait;

    public function planPayWithPayHere(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'PayHere');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $authuser  = Auth::user();
            try {
                $config = ['payhere.api_endpoint' => $pre_pay->settings['payhere_mode'] ?? '' === 'sandbox' ? 'https://sandbox.payhere.lk/' : 'https://www.payhere.lk/',];

                $config['payhere.merchant_id']      = $pre_pay->settings['payhere_merchant_id'] ?? '';
                $config['payhere.merchant_secret']  = $pre_pay->settings['payhere_merchant_secret_key'] ?? '';
                $config['payhere.app_secret']       = $pre_pay->settings['payhere_app_secret_key'] ?? '';
                $config['payhere.app_id']           = $pre_pay->settings['payhere_app_id'] ?? '';
                config($config);

                $hash = strtoupper(
                    md5(
                        $pre_pay->settings['payhere_merchant_id'] ?? '' .
                            $pre_pay->order_id .
                            number_format($pre_pay->price, 2, '.', '') .
                            'LKR' .
                            strtoupper(md5($pre_pay->settings['payhere_merchant_secret_key'] ?? ''))
                    )
                );

                $data = [
                    'first_name'    => $authuser->name,
                    'last_name'     => '',
                    'email'         => $authuser->email,
                    'phone'         => $authuser->mobile_no ?? '',
                    'address'       => 'Main Rd',
                    'city'          => 'Anuradhapura',
                    'country'       => 'Sri lanka',
                    'order_id'      => $pre_pay->order_id,
                    'items'         => $pre_pay->plan->name ?? 'Add-on',
                    'currency'      => $pre_pay->currency,
                    'amount'        => $pre_pay->price,
                    'hash'          => $hash,
                ];

                return PayHere::checkOut()->data($data)
                    ->successUrl(route('payhere.status', ['plan_id'  => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount'   => $pre_pay->price, 'order_id' => !empty($pre_pay->order_id) ? $pre_pay->order_id : '', 'status'   => 'success']))
                    ->failUrl(route('payhere.status', ['plan_id'  => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount'   => $pre_pay->price, 'order_id' => !empty($pre_pay->order_id) ? $pre_pay->order_id : '', 'status'   => 'fail']))->renderView();
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planGetPayHereStatus(Request $request, $plan_id, $amount)
    {
        try {
            if ($request->status == 'success') {
                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->route('plans.index')->with('error', __("The transaction has been failed"));
            }
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
        }
    }

    public function storePayWithPayHere(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'PayHere');
        try {
            if ($pre_pay->status == 'success') {
                try {
                    $config = ['payhere.api_endpoint' => $pre_pay->settings['payhere_mode'] ?? '' === 'sandbox' ? 'https://sandbox.payhere.lk/' : 'https://www.payhere.lk',];

                    $config['payhere.merchant_id']      = $pre_pay->settings['payhere_merchant_id'] ?? '';
                    $config['payhere.merchant_secret']  = $pre_pay->settings['payhere_merchant_secret_key'] ?? '';
                    $config['payhere.app_secret']       = $pre_pay->settings['payhere_app_secret_key'] ?? '';
                    $config['payhere.app_id']           = $pre_pay->settings['payhere_app_id'] ?? '';
                    config($config);

                    $hash = strtoupper(
                        md5(
                            $pre_pay->settings['payhere_merchant_id'] ?? '' .
                                $pre_pay->order_id .
                                number_format($pre_pay->price, 2, '.', '') .
                                'LKR' .
                                strtoupper(md5($pre_pay->settings['payhere_merchant_secret_key'] ?? ''))
                        )
                    );

                    $data = [
                        'first_name'    => $cust_details['name'] ?? null,
                        'last_name'     => '',
                        'email'         => $cust_details['email'] ?? null,
                        'phone'         => $cust_details['phone'] ?? null,
                        'address'       => 'Main Rd',
                        'city'          => 'Anuradhapura',
                        'country'       => 'Sri lanka',
                        'order_id'      => $pre_pay->order_id,
                        'items'         => $slug ?? 'Add-on',
                        'currency'      => $pre_pay->currency,
                        'amount'        => $pre_pay->price,
                        'hash'          => $hash,
                    ];


                    return PayHere::checkOut()
                        ->data($data)
                        ->successUrl(route('store.payhere.status', ['slug' => $slug, 'order_id' => $pre_pay->order_id, 'status' => 'success']))
                        ->failUrl(route('store.payhere.status', ['slug' => $slug, 'order_id' => $pre_pay->order_id, 'status' => 'fail']))
                        ->renderView();
                } catch (\Exception $e) {
                    return redirect()->route('store.slug', $pre_pay->slug)->with('error', $e->getMessage());
                }
            } else {
                return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }

    public function getStorePaymentStatus(Request $request, $slug)
    {
        try {
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
