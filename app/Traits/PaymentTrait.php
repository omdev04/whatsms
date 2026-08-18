<?php
// Step 1: Create the Trait
namespace App\Traits;

use App\Models\Order;
use App\Models\PurchasedProducts;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Utility;
use App\Models\Shipping;
use App\Models\UserDetail;
use App\Models\ProductVariantOption;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Cache;

trait PaymentTrait
{
    public function payThisPlan($request, $package_name)
    {

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);

        $plan   = Plan::find($planID);
        $get_amount = $plan->price;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $currency = $admin_payment_setting['currency'];
        Session::put('type', $package_name);
        if ($plan) {
            try {

                $coupon_id = null;
                $price     = $plan->price;
                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price          = $plan->price - $discount_value;
                        if ($coupons->limit == $usedCoupun) {
                            $request_data = $request->merge(['status'    => 'error', 'message'   =>   __('This coupon code has expired!'),])->all();
                            return (object) $request_data;
                        }
                        $coupon_id = $coupons->id;
                    } else {
                        $request_data = $request->merge(['status'    => 'error', 'message'   =>   __('This coupon code is invalid or has expired!'),])->all();
                        return (object) $request_data;
                    }
                }
                $get_amount = $price;
                $coupons = Coupon::find($coupon_id);
                $user = Auth::user();
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                if ($price <=  0.0) {
                    $authuser       = Auth::user();

                    $authuser->plan = $plan->id;
                    $authuser->save();

                    $assignPlan = $authuser->assignPlan($plan->id, $request->paypal_payment_frequency);
                    if (!empty($coupons)) {
                        $userCoupon            = new UserCoupon();
                        $userCoupon->user   = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order  = $orderID;
                        $userCoupon->save();


                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }

                    if ($assignPlan['is_success'] == true && !empty($plan)) {
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                        $planorder                 = new PlanOrder();
                        $planorder->order_id = $orderID;
                        $planorder->name = $user->name;
                        $planorder->email = $user->email;
                        $planorder->card_number = null;
                        $planorder->card_exp_month = null;
                        $planorder->card_exp_year = null;
                        $planorder->plan_name = $plan->name;
                        $planorder->plan_id = $plan->id;
                        $planorder->price = $price == null ? 0 : $price;
                        $planorder->price_currency = !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : 'USD';
                        $planorder->txn_id = '';
                        $planorder->payment_type = $package_name;
                        $planorder->payment_status = 'succeeded';
                        $planorder->receipt = NULL;
                        $planorder->user_id = $user->id;
                        $planorder->store_id = $user->current_store;
                        $planorder->save();
                        $request_data = $request->merge(['status'    => 'success',  'plan_type' => 'free', 'message'   =>   __('Plan Successfully Activated.'),])->all();
                        return (object) $request_data;
                    }
                } else {
                    $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                    $request_data = $request->merge([
                        'order_id'  => $order_id,
                        'plan_id'  =>  $plan->id,
                        'price'     => $get_amount,
                        'currency'  => $currency,
                        'settings'  => (array) $admin_payment_setting,
                        'success_url'  => route('plans.index'),
                        'plan_type' => $plan_type ?? 'paid',
                        'plan' => $plan,
                        'coupon_id'      => $coupons->id ?? null,
                        'status'    => 'success',
                        'type' => $package_name,
                        'message'   =>   __('Your Payment in progress.'),
                    ])->all();
                    return (object) $request_data;
                }
            } catch (\Exception $e) {
                $request_data = $request->merge(['status'    => 'success', 'message'   =>   __($e->getMessage()),])->all();
                return (object) $request_data;
            }
        } else {
            $request_data = $request->merge(['status'    => 'success', 'message'   =>   __('Plan is deleted'),])->all();
            return (object) $request_data;
        }
    }
    public function statusThisPlan($request, $plan_id, $amount, $status)
    {
        $user     = Auth::user();
        $store_id = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan = Plan::find($planID);
        $type =  Session::get('type');
        if ($plan) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($request->has('coupon_id') && $request->coupon_id != '') {
                $coupons = Coupon::find($request->coupon_id);
                if (!empty($coupons)) {
                    $userCoupon         = new UserCoupon();
                    $userCoupon->user   = $user->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order  = $orderID;
                    $userCoupon->save();
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }
            }
            if ($status == 'success' || $status == 'successful') {
                if ($status == 'success' || $status == 'successful') {
                    $statuses = 'succeeded';
                }
                $planorder                 = new PlanOrder();
                $planorder->order_id       = $orderID;
                $planorder->name           = $user->name;
                $planorder->email           = $user->email;
                $planorder->card_number    = '';
                $planorder->card_exp_month = '';
                $planorder->card_exp_year  = '';
                $planorder->plan_name      = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = $amount;
                $planorder->price_currency = !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : 'USD';
                $planorder->txn_id         = '';
                $planorder->payment_type   = $type;
                $planorder->payment_status = $statuses;
                $planorder->receipt        = '';
                $planorder->user_id        = $user->id;
                $planorder->store_id       = $store_id;
                $planorder->save();

                $assignPlan = $user->assignPlan($plan->id);
                Utility::referralTransaction($plan);

                if ($assignPlan['is_success']) {
                    return (object) ['status'  => 'success', 'message' =>  __('Plan activated Successfully.'),];
                } else {

                    return (object) ['status'  => 'error', 'message' =>  __($assignPlan['error']),];
                }
                return (object) ['status'  => 'error', 'message' =>  __('Transaction has been ' . __($statuses)),];
                session()->forget($type);
            } else {
                return (object) ['status'  => 'error', 'message' =>  __('Something went wrong.'),];
            }
        } else {
            return (object) ['status'  => 'error', 'message' =>  __('Plan is deleted.'),];
        }
    }
    public function payThisProductOrder($request, $slug, $package_name, $cache = false)
    {
        $slug = $request->slug;
        $store = Store::where('slug', $slug)->first();
        if ((!empty(Auth::guard('customers')->user()) && $store->is_checkout_login_required == 'on') || $store->is_checkout_login_required == 'off') {
            $shipping = Shipping::where('store_id', $store->id)->first();
            if (!empty($shipping) && $store->enable_shipping == 'on') {
                if ($request->shipping_price == '0.00') {
                    $request_data = $request->merge(['status'    => 'error', 'message'   =>   __('Please select shipping.'),])->all();
                    return (object) $request_data;
                }
            }
            if (empty($store)) {
                $request_data = $request->merge(['status'    => 'error', 'message'   =>   __('Store not available.'),])->all();
                return (object) $request_data;
            }
            $validator = \Validator::make(

                $request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'billing_address' => 'required',
                ]
            );
            if ($validator->fails()) {
                $request_data = $request->merge(['status'    => 'error', 'message'   =>   __('All field is required.'),])->all();
                return (object) $request_data;
            }
            $userdetail = new UserDetail();

            $userdetail['store_id'] = $store['id'];
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
                $request_data = $request->merge(['status'    => 'error', 'message'   =>   __('Please add to product into cart.'),])->all();
                return (object) $request_data;
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
            $currency = $store->currency;

            $objUser = \Auth::user();

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
            if ($products) {
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


                try {

                    if ($coupon != "") {
                        if ($coupon['enable_flat'] == 'off') {
                            $discount_value = ($totalprice / 100) * $coupon['discount'];
                            $totalprice          = $totalprice - $discount_value;
                        } else {
                            $discount_value = $coupon['flat_discount'];
                            $totalprice          = $totalprice - $discount_value;
                        }
                    }

                    $price = number_format($total, 2);

                    $url    = route('store.slug', ['slug' => $slug, 'status' => 'error']);

                    $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                    $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $request->total_price)));
                    // $totalprice = preg_replace('/[^\d.]/', '', $totalprice); // Remove non-numeric characters except the decimal point
                    $cart['totalprice'] = $totalprice;
                    $cart['coupon_id'] = $request->coupon_id;
                    $cart['coupon_json'] = json_encode($coupon);
                    $cart['dicount_price'] = $request->dicount_price;
                    $cart['currency_code'] = $store->currency_code;
                    $cart['user_id'] = $store['id'];
                    $cart['type'] = $package_name;
                    ($cache) ?  Cache::put($slug, $cart) : Session::put($slug, $cart);

                    $request_data = $request->merge([
                        'order_id'  => $order_id,
                        'l_name'  =>  $store['name'],
                        'store'   => $store,
                        'price'     => $totalprice,
                        'currency'  => $currency,
                        'settings'  => (array) $store_payment_setting,
                        'success_url'  => $url,
                        'status'    => 'success',
                        'slug'      => $slug,
                        'type' => $package_name,
                        'message'   =>   __('Your Payment in progress.'),
                    ])->all();


                    return (object) $request_data;
                } catch (\Exception $e) {
                    $request_data = $request->merge(['status'    => 'error', 'message'   =>   __($e->getMessage()),])->all();
                    return (object) $request_data;
                }
            } else {
                $request_data = $request->merge(['status'    => 'error', 'message'   =>   __('Transaction has been failed!'),])->all();
                return (object) $request_data;
            }
        } else {
            $request_data = $request->merge(['status'    => 'error', 'message'   =>   __('You need to login.'),])->all();
            return (object) $request_data;
        }
    }
    public function statusThisProductOrder($request, $slug, $cache = false)
    {
        try {

            if ($request->status == 'success' || $request->status == 'successful') {


                $cart = ($cache) ?  Cache::get($slug) : Session::get($slug);
                ($cache) ?  Cache::forget($slug) : Session::forget($slug);

                $type = $cart['type'] ?? '';
                $cust_details = $cart['cust_details'];
                $shipping_data = $cart['shipping_data'];
                $product_id = $cart['product_id'];
                $totalprice = $cart['totalprice'];
                $totalprice = preg_replace('/[^\d.]/', '', $totalprice);
                $coupon = $cart['coupon_json'];
                $products = $cart['all_products'];

                $customer               = Auth::guard('customers')->user();
                $store        = Store::where('slug', $slug)->first();
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
                $order->price_currency  = $store->currency;
                $order->txn_id          = '';
                $order->payment_type    = $type;
                $order->payment_status  = 'approved';
                $order->receipt         = '';
                $order->user_id         = $cart['user_id'];
                $order->customer_id     = isset($customer->id) ? $customer->id : 0;

                $order->save();

                //webhook
                $module = 'New Order';
                $webhook =  Utility::webhook($module, $store->id);
                if ($webhook) {
                    $parameter = json_encode($products);
                    //
                    // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                    $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                    if ($status != true) {
                        $msg  = 'Webhook call failed.';
                    }
                }

                foreach ($products['products'] as $k_pro => $product_id) {

                    $purchased_product = new PurchasedProducts();
                    $purchased_product->product_id  = $product_id['product_id'];
                    $purchased_product->customer_id = isset($customer->id) ? $customer->id : 0;
                    $purchased_product->order_id   = $order->id;
                    $purchased_product->save();
                }

                $order_email = $order->email;
                $owner = User::find($store->created_by);
                $owner_email = $owner->email;
                $order_id = Crypt::encrypt($order->id);
                $dArr = [
                    'order_name' => $order->name,
                ];
                $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);
                $resp1 = Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);
                if (isset($store->is_twilio_enabled) && $store->is_twilio_enabled == "on") {
                    Utility::order_create_owner($order, $owner, $store);
                    Utility::order_create_customer($order, $customer, $store);
                }

                return (object) ['order_id'  =>  Crypt::encrypt($order->id), 'slug' => $slug, 'status'  => 'success', 'message' =>  __('Transaction has been success.'),];
            } else {
                return (object) ['slug' => $slug, 'status'  => 'error', 'message' =>  __('Transaction has been failed!'),];
            }
        } catch (\Exception $exception) {
            return (object) ['slug' => $slug, 'status'  => 'error', 'message' =>  __($exception->getMessage()),];
        }
    }
}
