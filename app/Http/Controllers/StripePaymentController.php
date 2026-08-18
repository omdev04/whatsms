<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use Stripe;;

use Illuminate\Http\RedirectResponse;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class StripePaymentController extends Controller
{
    public $currancy;
    public $stripe_secret;
    use PaymentTrait;


    public function index()
    {
        $objUser = Auth::user();
        if ($objUser->type == 'super admin') {
            $orders = Order::select(
                [
                    'orders.*',
                    'users.name as user_name',
                ]
            )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->get();
        } else {
            $orders = Order::select(
                [
                    'orders.*',
                    'users.name as user_name',
                ]
            )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->where('users.id', '=', $objUser->id)->get();
        }

        return view('order.index', compact('orders'));
    }

    public function stripe($code)
    {
        if (Auth::user()->type != 'super admin') {
            $admin_payments_setting = Utility::getAdminPaymentSetting();
            if ((isset($admin_payments_setting['is_stripe_enabled']) && $admin_payments_setting['is_stripe_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_paypal_enabled']) && $admin_payments_setting['is_paypal_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_paystack_enabled']) &&
                    $admin_payments_setting['is_paystack_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_flutterwave_enabled']) &&
                    $admin_payments_setting['is_flutterwave_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_razorpay_enabled']) &&
                    $admin_payments_setting['is_razorpay_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_mercado_enabled']) &&
                    $admin_payments_setting['is_mercado_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_paytm_enabled']) && $admin_payments_setting['is_paytm_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_mollie_enabled']) && $admin_payments_setting['is_mollie_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_skrill_enabled']) && $admin_payments_setting['is_skrill_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_coingate_enabled']) &&
                    $admin_payments_setting['is_coingate_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_paymentwall_enabled']) &&
                    $admin_payments_setting['is_paymentwall_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_payfast_enabled']) &&
                    $admin_payments_setting['is_payfast_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_toyyibpay_enabled']) &&
                    $admin_payments_setting['is_toyyibpay_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_manuallypay_enabled']) &&
                    $admin_payments_setting['is_manuallypay_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_bank_enabled']) && $admin_payments_setting['is_bank_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_iyzipay_enabled']) &&
                    $admin_payments_setting['is_iyzipay_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_paytab_enabled']) && $admin_payments_setting['is_paytab_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_benefit_enabled']) &&
                    $admin_payments_setting['is_benefit_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_cashfree_enabled']) &&
                    $admin_payments_setting['is_cashfree_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_aamarpay_enabled']) &&
                    $admin_payments_setting['is_aamarpay_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_paytr_enabled']) && $admin_payments_setting['is_paytr_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_yookassa_enabled']) && $admin_payments_setting['is_yookassa_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_midtrans_enabled']) && $admin_payments_setting['is_midtrans_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_xendit_enabled']) && $admin_payments_setting['is_xendit_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_paiment_pro_enabled']) && $admin_payments_setting['is_paiment_pro_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_fedapay_enabled']) && $admin_payments_setting['is_fedapay_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_nepalste_enabled']) && $admin_payments_setting['is_nepalste_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_payhere_enabled']) && $admin_payments_setting['is_payhere_enabled'] == 'on') ||
                (isset($admin_payments_setting['is_cinetpay_enabled']) && $admin_payments_setting['is_cinetpay_enabled'] == 'on')
            ) {
                try {
                    $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($code);
                    $plan    = Plan::find($plan_id);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error',  $e->getMessage());
                }
                if ($plan) {
                    $plan    = $plan;
                    $admin_payments_details = Utility::getAdminPaymentSetting();

                    return view('plans/stripe', compact('plan', 'admin_payments_details'));
                } else {
                    return redirect()->back()->with('error', __('Plan is deleted.'));
                }
            } else {
                return redirect()->back()->with('error', __('Admin payment setting is not set.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function addPayment(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Stripe');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
            $this->stripe_secret = $pre_pay->settings['stripe_secret'] ??  '';
            if ($pre_pay->price > 0.0) {
                $return_type = 'stripe';
                $stripe_formatted_price = in_array(
                    $this->currancy,
                    [
                        'MGA',
                        'BIF',
                        'CLP',
                        'PYG',
                        'DJF',
                        'RWF',
                        'GNF',
                        'UGX',
                        'JPY',
                        'VND',
                        'VUV',
                        'XAF',
                        'KMF',
                        'KRW',
                        'XOF',
                        'XPF',
                    ]
                ) ? number_format($pre_pay->price, 2, '.', '') : number_format($pre_pay->price, 2, '.', '') * 100;

                $return_url_parameters = function ($return_type) {
                    return '&return_type=' . $return_type . '&payment_processor=stripe';
                };
                Stripe\Stripe::setApiKey($this->stripe_secret);
                $data = \Stripe\Checkout\Session::create(
                    [
                        'payment_method_types' => ['card'],
                        'line_items' => [
                            [
                                'price_data' => [
                                    'currency' => $pre_pay->currency,
                                    'product_data' => [
                                        'name' => $pre_pay->plan->name,
                                        'description' => $pre_pay->plan->duration,
                                    ],
                                    'unit_amount' => $stripe_formatted_price,
                                ],
                                'quantity' => 1,
                            ],
                        ],
                        'mode' => 'payment',
                        'metadata' => [
                            'order_id' => $pre_pay->order_id,
                        ],
                        'success_url' =>  route('plan.stripe.status', ['id' => $plan_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, $return_url_parameters('success'), 'status' => 'success', 'coupon_id' => $pre_pay->coupon_id]),
                        'cancel_url' => route('plan.stripe.status', ['id' => $plan_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id,  $return_url_parameters('cancel'), 'status' => 'cancel', 'coupon_id' => $pre_pay->coupon_id]),
                    ]
                );

                $data = $data ?? false;
                if ($pre_pay->status == 'success') {
                    return new RedirectResponse($data->url);
                } else {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed'));
                }
            } else {
                return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }
    public function planGetStatus(Request $request, $plan_id, $amount)
    {
        if ($request->return_type == 'success') {
            $admin_payment_setting = Utility::getAdminPaymentSetting();

            $stripe         = new \Stripe\StripeClient($admin_payment_setting['stripe_secret'] ?? '');
            $stripe_session = Cache::get($request->other_order_id);
            $payment_intent =  $stripe_session->payment_intent ?? '';
            $receipt_url    = "";

            if (isset($payment_intent) && !empty($payment_intent)) {
                $paymentIntents     = $stripe->paymentIntents->retrieve($payment_intent, []);
                $receipt_url        = $paymentIntents->charges->data[0]->receipt_url;
            }

            Session::forget($request->other_order_id);
            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        } else {
            return redirect()->route('plans.index')->with('error', __("The transaction has been failed"));
        }
    }

    public function stripePost(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Stripe', false);
        if ($pre_pay->status == 'success') {
            $this->stripe_secret = $pre_pay->settings['stripe_secret'] ?? '';
            $return_type = $pre_pay->type;
            $stripe_formatted_price = in_array(
                $this->currancy,
                [
                    'MGA',
                    'BIF',
                    'CLP',
                    'PYG',
                    'DJF',
                    'RWF',
                    'GNF',
                    'UGX',
                    'JPY',
                    'VND',
                    'VUV',
                    'XAF',
                    'KMF',
                    'KRW',
                    'XOF',
                    'XPF',
                ]
            ) ? number_format($pre_pay->price, 2, '.', '') : number_format($pre_pay->price, 2, '.', '') * 100;

            $return_url_parameters = function ($return_type) {
                return '&return_type=' . $return_type . '&payment_processor=stripe';
            };
            Stripe\Stripe::setApiKey($this->stripe_secret);
            $data = \Stripe\Checkout\Session::create(
                [
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => $pre_pay->store->currency,
                                'product_data' => [
                                    'name' => $pre_pay->l_name,
                                    'description' => " Stripe payment of order - " . $pre_pay->order_id,
                                ],
                                'unit_amount' => $stripe_formatted_price,
                            ],
                            'quantity' => 1,
                        ],
                    ],
                    'mode' => 'payment',
                    'metadata' => [
                        'order_id' => $pre_pay->order_id,
                    ],
                    'success_url' =>  route('store.payment.stripe', ['order_id' => $pre_pay->order_id, 'slug' => $pre_pay->slug, 'status' => 'success', $return_url_parameters('success'),]),

                    'cancel_url'        => route('store.payment.stripe', ['order_id' => $pre_pay->order_id, 'slug' => $pre_pay->slug, 'status' => 'cancel', $return_url_parameters('cancel'),]),
                ]
            );

            $data = $data ?? false;
            if ($pre_pay->status == 'success') {
                return new RedirectResponse($data->url);
            } else {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', __('Transaction has been failed'));
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }
    public function getProductStatus(Request $request)
    {
        $slug = $request->slug;
        $store = Store::where('slug', $request->slug)->first();
        $store_payment_setting = Utility::getPaymentSetting($store->id);

        $stripe         = new \Stripe\StripeClient($store_payment_setting['stripe_secret'] ?? '');
        $stripe_session = Cache::get($request->other_order_id);
        $payment_intent = $stripe_session->payment_intent ?? '';
        $receipt_url    = "";

        if (isset($payment_intent) && !empty($payment_intent)) {
            $paymentIntents     = $stripe->paymentIntents->retrieve($payment_intent, []);
            $receipt_url        = $paymentIntents->charges->data[0]->receipt_url;
        }

        Session::forget($request->other_order_id);
        $status =  $this->statusThisProductOrder($request, $slug, false);
        if ($status->status == 'success') {
            return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
        } else {
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        }
    }
}
