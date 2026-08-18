<?php

namespace App\Http\Controllers;

use App\Traits\PaymentTrait;
use App\Coingate\Coingate;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\ProductVariantOption;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Models\UserDetail;
use App\Models\PurchasedProducts;
use App\Models\User;
class CoingateController extends Controller
{
    use PaymentTrait;

    public function coingatePaymentPrepare(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Coingate');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            Coingate::config(
                array(
                    'environment'               => $pre_pay->settings['coingate_mode'] ?? 'live',
                    'auth_token'                => $pre_pay->settings['coingate_auth_token'] ?? '',
                    'curlopt_ssl_verifypeer'    => FALSE
                )
            );
            $post_params = array(
                'order_id' => time(),
                'price_amount' => $pre_pay->price,
                'price_currency' => $pre_pay->settings['currency'] ?? 'USD',
                'receive_currency' => $pre_pay->settings['currency'] ?? 'USD',
                "success_url" => route('coingate.payment.callback', ['id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success']),
                "cancel_url" =>  route('coingate.payment.callback', ['id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'cancel']),
                'title' => 'Order #' . time(),
            );

            $response = Coingate::coingatePayment($post_params, 'POST');
            if ($response['status_code'] === 200) {
                $order = $response['response'];
                session(['coingate_order_id' => $order['id']]);
                return redirect($order['payment_url']);
            } else {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed'));
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function coingatePlanGetPayment(Request $request, $plan_id, $amount)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        Coingate::config(
            array(
                'environment'               => $admin_payment_setting['coingate_mode'] ?? 'live',
                'auth_token'                => $admin_payment_setting['coingate_auth_token'] ?? '',
                'curlopt_ssl_verifypeer'    => FALSE
            )
        );
        $coingate_order_id = session('coingate_order_id');
        $response = Coingate::coingatePayment($coingate_order_id, 'GET');
        if (isset($response['status']) && $response['status'] == 'paid') {
            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

            if ($verify->status == 'success') {
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
            Session::forget('coingate_order_id');
        }
        return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
    }

    public function PayWithCoingate($slug, Request $request)
    {
        $store = Store::where('slug', $slug)->first();
        if ((!empty(Auth::guard('customers')->user()) && $store->is_checkout_login_required == 'on') || $store->is_checkout_login_required == 'off') {

            $store = Store::where('slug', $slug)->first();
            $shipping = Shipping::where('store_id', $store->id)->first();
            if (!empty($shipping) && $store->enable_shipping == 'on') {
                if ($request->shipping_price == '0.00') {
                    return redirect()->route('store.slug', $slug)->with('error', __('Please select shipping.'));
                }
            }
            if (empty($store)) {
                return redirect()->route('store.slug', $slug)->with('error', __('Store not available.'));
            }
            $validator = \Validator::make(

                $request->all(),
                [
                    'name' => 'required|max:120',
                    'phone' => 'required',
                    'billing_address' => 'required',
                ]
            );
            if ($validator->fails()) {
                return redirect()->route('store.slug', $slug)->with('error', __('All field is required.'));
            }
            $userdetail = new UserDetail();

            $userdetail['store_id'] = $store->id;
            $userdetail['name']     = $request->name;
            $userdetail['email']    = $request->email;
            $userdetail['phone']    = $request->phone;

            $userdetail['custom_field_title_1'] = $request->custom_field_title_1;
            $userdetail['custom_field_title_2'] = $request->custom_field_title_2;
            $userdetail['custom_field_title_3'] = $request->custom_field_title_3;
            $userdetail['custom_field_title_4'] = $request->custom_field_title_4;


            $userdetail['billing_address']  = $request->billing_address;
            $userdetail['shipping_address'] = !empty($request->shipping_address) ? $request->shipping_address : '-';
            $userdetail['special_instruct'] = $request->special_instruct;
            $userdetail->save();
            $userdetail->id;

            $cart     = session()->get($slug);
            $products = $cart;
            $order_id = $request['order_id'];
            if (empty($cart)) {
                return redirect()->route('store.slug', $slug)->with('error', __('Please add to product into cart.'));
            }
            $cust_details = [
                "id" => $userdetail->id,
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "custom_field_title_1" => $request->custom_field_title_1,
                "custom_field_title_2" => $request->custom_field_title_2,
                "custom_field_title_3" => $request->custom_field_title_3,
                "custom_field_title_4" => $request->custom_field_title_4,
                "billing_address" => $request->billing_address,
                "shipping_address" => $request->shipping_address,
                "special_instruct" => $request->special_instruct,
            ];
            if (!empty($request->coupon_id)) {
                $coupon = ProductCoupon::where('id', $request->coupon_id)->first();
            } else {
                $coupon = '';
            }
            $store        = Store::where('slug', $slug)->first();
            $user_details = $cust_details;

            $store_payment_setting = Utility::getPaymentSetting($store->id);

            $objUser = Auth::user();

            $total        = 0;
            $sub_tax      = 0;
            $sub_total    = 0;
            $total_tax    = 0;
            $product_name = [];
            $product_id   = [];
            $totalprice   = 0;
            $tax_name     = [];

            foreach ($products['products'] as $key => $product) {
                if ($product['variant_id'] == 0) {
                    $new_qty                = $product['originalquantity'] - $product['quantity'];
                    $product_edit           = Product::find($product['product_id']);
                    $product_edit->quantity = $new_qty;
                    $product_edit->save();

                    $tax_price = 0;
                    if (!empty($product['tax'])) {
                        foreach ($product['tax'] as $key => $taxs) {
                            $tax_price += $product['price'] * $product['quantity'] * $taxs['tax'] / 100;
                        }
                    }
                    $totalprice     += $product['price'] * $product['quantity'];
                    $product_name[] = $product['product_name'];
                    $product_id[]   = $product['id'];
                } elseif ($product['variant_id'] != 0) {
                    $new_qty                   = $product['originalvariantquantity'] - $product['quantity'];
                    $product_variant           = ProductVariantOption::find($product['variant_id']);
                    $product_variant->quantity = $new_qty;
                    $product_variant->save();

                    $tax_price = 0;
                    if (!empty($product['tax'])) {
                        foreach ($product['tax'] as $key => $taxs) {
                            $tax_price += $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;
                        }
                    }
                    $totalprice     += $product['variant_price'] * $product['quantity'];
                    $product_name[] = $product['product_name'] . ' - ' . $product['variant_name'];
                    $product_id[]   = $product['id'];
                }
            }

            $coupon_id = null;
            $price     = $total + $total_tax;
            $price = $totalprice + $tax_price;
            if (isset($cart['coupon'])) {
                if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                    $discount_value = ($price / 100) * $cart['coupon']['coupon']['discount'];
                    $price          = $price - $discount_value;
                } else {
                    $discount_value = $cart['coupon']['coupon']['flat_discount'];
                    $price          = $price - $discount_value;
                }
            }
            if (!empty($request->shipping_id)) {
                $shipping = Shipping::find($request->shipping_id);
                if (!empty($shipping)) {
                    $totalprice     = $price + $shipping->price;
                    $shipping_name  = $shipping->name;
                    $shipping_price = $shipping->price;
                    $shipping_data  = json_encode(
                        [
                            'shipping_name' => $shipping_name,
                            'shipping_price' => $shipping_price,
                            'location_id' => $shipping->location_id,
                        ]
                    );
                }
            } else {
                $shipping_data = '';
            }

            $cart['cust_details'] = $cust_details;
            $cart['shipping_data'] = $shipping_data;
            $cart['product_id'] = $product_id;
            $cart['all_products'] = $products;
            if ($coupon != "") {
                if ($coupon['enable_flat'] == 'off') {
                    $discount_value = ($totalprice / 100) * $coupon['discount'];
                    $totalprice          = $totalprice - $discount_value;
                } else {
                    $discount_value = $coupon['flat_discount'];
                    $totalprice          = $totalprice - $discount_value;
                }
            }
            $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $request->total_price)));
            $cart['totalprice'] = $totalprice;
            $cart['coupon_id'] = $request->coupon_id;
            $cart['coupon_json'] = json_encode($coupon);
            $cart['dicount_price'] = $request->dicount_price;
            $cart['currency_code'] = $store->currency_code;
            $cart['user_id'] = $store['id'];
            session()->put($slug, $cart);
            if ($products) {
                if (Utility::CustomerAuthCheck($store->slug)) {
                    $customer = Auth::guard('customers')->user()->id;
                } else {
                    $customer = 0;
                }

                $customer               = Auth::guard('customers')->user();
                $order                  = new Order();
                $order->order_id        = '#' . time();
                $order->name            = $cust_details['name'];
                $order->email           = $cust_details['email'];
                $order->card_number     = '';
                $order->card_exp_month  = '';
                $order->card_exp_year   = '';
                $order->status          = 'pending';
                $order->phone           = $cust_details['phone'];
                $order->user_address_id = $cust_details['id'];
                $order->shipping_data   = !empty($shipping_data) ? $shipping_data : '';
                $order->product_id      = implode(',', $product_id);
                $order->price           = $totalprice;
                $order->coupon          = $cart['coupon_id'];
                $order->coupon_json     = $coupon;
                $order->discount_price  = !empty($cart['dicount_price']) ? $cart['dicount_price'] : '0';
                $order->coupon          = $cart['coupon_id'];
                $order->product         = json_encode($products);
                $order->price_currency  = $cart['currency_code'];
                $order->txn_id          = '';
                $order->payment_type    = __('Coingate');
                $order->payment_status  = 'pending';
                $order->receipt         = '';
                $order->user_id         = $cart['user_id'];
                $order->customer_id     = isset($customer->id) ? $customer->id : '';
                $order->save();

                //webhook
                $module = 'New Order';
                $webhook =  Utility::webhook($module, $store->id);
                if ($webhook) {
                    $parameter = json_encode($product);
                    //
                    // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                    $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                    if ($status != true) {
                        $msg  = 'Webhook call failed.';
                    }
                }

                if ((!empty(Auth::guard('customers')->user()) && $store->is_checkout_login_required == 'on') || (!empty(Auth::guard('customers')->user()) && $store->is_checkout_login_required == 'off')) {
                    foreach ($products['products'] as $k_pro => $product_id) {

                        $purchased_product = new PurchasedProducts();
                        $purchased_product->product_id  = $product_id['product_id'];
                        $purchased_product->customer_id = $customer->id;
                        $purchased_product->order_id   = $order->id;
                        $purchased_product->save();
                    }
                }
                try {

                    Coingate::config(
                        array(
                            'environment'               => $store_payment_setting['coingate_mode'] ?? 'live',
                            'auth_token'                => $store_payment_setting['coingate_auth_token'] ?? '',
                            'curlopt_ssl_verifypeer'    => FALSE
                        )
                    );

                    $post_params = array(
                        'order_id' => $order->id,
                        'price_amount' => $totalprice,
                        'price_currency' => $store['currency'],
                        'receive_currency' => $store['currency'],
                        'callback_url' => route('get.coingate.status', $slug),
                        'cancel_url' => route('get.coingate.status', $slug),
                        'success_url' => route(
                            'get.coingate.status',
                            [
                                'slug' => $store->slug,
                                'order_id' => $order->id,
                            ]
                        ),
                        'title' => 'Order #' . time(),
                    );
                    $order_email = $order->email;
                    $order_name = $order->name;

                    $response = Coingate::coingatePayment($post_params, 'POST');
                    if ($response['status_code'] === 200) {
                        $order = $response['response'];
                        session(['coingate_order_id' => $order['id']]);
                        session()->forget($slug);
                        return redirect($order['payment_url']);
                    } else {
                        return redirect()->back()->with('error', __('Oops, something went wrong, please try again.'));
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
            } else {
                return redirect()->back()->with('error', __('Transaction has been failed.'));
            }
        } else {

            return redirect()->back()->with('error', __('You need to login'));
        }
    }

    public function GetCoingateStatus(Request $request, $slug)
    {
        try {
            $slug = $request->slug;
            $store        = Store::where('slug', $slug)->first();
            $store_payment_setting = Utility::getPaymentSetting($store->id);
            if ($request->has('order_id')) {
                $order = Order::where('id', $request->order_id)->first();
                if ($order) {
                    Coingate::config(
                        array(
                            'environment'               => $store_payment_setting['coingate_mode'] ?? 'live',
                            'auth_token'                => $store_payment_setting['coingate_auth_token'] ?? '',
                            'curlopt_ssl_verifypeer'    => FALSE
                        )
                    );
                    $coingate_order_id = session('coingate_order_id');
                    $response = Coingate::coingatePayment($coingate_order_id, 'GET');
                    if (isset($response['status']) && $response['status'] == 'paid') {
                        $order->payment_status = 'approved';
                        $order->save();

                        $order_email = $order->email;
                        $owner = User::find($store->created_by);
                        $owner_email = $owner->email;
                        $order_id = Crypt::encrypt($order->id);
                        // if (isset($store->mail_driver) && !empty($store->mail_driver)) {
                        $dArr = [
                            'order_name' => $order->name,
                        ];
                        $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);
                        $resp1 = Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);
                        // }
                        if (isset($store->is_twilio_enabled) && $store->is_twilio_enabled == "on") {
                            $customer = Auth::guard('customers') ?? null;
                            Utility::order_create_owner($order, $owner, $store);
                            Utility::order_create_customer($order, $customer, $store);
                        }
                        $msg = redirect()->route(
                            'store-complete.complete',
                            [
                                $slug,
                                Crypt::encrypt($order->id),
                            ]
                        )->with('success', __('Transaction has been success') . ((isset($msg)) ? '<br> <span class="text-danger">' . $msg . '</span>' : ''));

                        session()->forget($slug);
                        return $msg;
                    } else {
                        return redirect()->back()->with('error', __('Oops, something went wrong, please try again.'));
                    }
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Oops, something went wrong, please try again.'));
        }
    }
}
