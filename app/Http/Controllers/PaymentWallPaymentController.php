<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Crypt;
use App\Models\Plan;
use App\Models\Store;
use App\Traits\PaymentTrait;

class PaymentWallPaymentController extends Controller
{
    use PaymentTrait;

    public function index(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'PaymentWall');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            return view('plans.paymentwall', compact('pre_pay'));
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function paymenterror(Request $request, $flag)
    {
        if ($flag == 1) {
            return redirect()->route("plans.index")->with('success', __('Transaction has been Successfull! '));
        } else {
            return redirect()->route("plans.index")->with('error', __('Transaction has been failed! '));
        }
    }

    public function planPayWithPaymentwall(Request $request, $plan_id, $amount)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $planid = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan               = Plan::find($planid);

        $result = array();
        //The parameter after verify/ is the transaction reference to be verified

        \Paymentwall_Config::getInstance()->set(array(
            'private_key' => $admin_payment_setting['paymentwall_private_key']
        ));

        $parameters = $_POST;
        $chargeInfo = array(
            'email' => $parameters['email'],
            'history[registration_date]' => '1489655092',
            'amount' => $plan->price,
            'currency' => !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : 'USD',
            'token' => $parameters['brick_token'],
            'fingerprint' => $parameters['brick_fingerprint'],
            'description' => 'Order #123'
        );

        $charge = new \Paymentwall_Charge();
        $charge->create($chargeInfo);
        $responseData = json_decode($charge->getRawResponseData(), true);
        $response = $charge->getPublicData();
        if ($charge->isSuccessful() and empty($responseData['secure'])) {
            if ($charge->isCaptured()) {
                if ($request->status == 'success') {
                    $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false);
                    $res['flag'] = 1;
                    return $res;
                } else {
                    $res['flag'] = 2;
                    return $res;
                }
            } elseif ($charge->isUnderReview()) {
                $res['flag'] = 2;
                return $res;
            }
        } else {
            $res['flag'] = 2;
            return $res;
        }
    }

    public function orderindex(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'PaymentWall');
        try {
            return view('storefront.paymentwall', compact('pre_pay', 'slug'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }
    public function orderPayWithPaymentwall(Request $request, $slug)
    {
        try {
            $store    = Store::where('slug', $slug)->first();
            $cart     = session()->get($slug);
            $response_data = $cart['response_data'];
            if (\Auth::check() && Utility::CustomerAuthCheck($slug) == false) {
                $store_payment_setting = Utility::getPaymentSetting();
            } else {
                $store_payment_setting = Utility::getPaymentSetting($store->id);
            }

            $totalprice   = 0;
            $products = $response_data['all_products'];

            if ($products) {

                $result = array();
                //The parameter after verify/ is the transaction reference to be verified

                \Paymentwall_Config::getInstance()->set(array(
                    'private_key' => $store_payment_setting['paymentwall_private_key']
                ));
                $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $cart['response_data']['total_price'])));
                $parameters = $_POST;
                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $totalprice,
                    'currency' => !empty($store->currency_code) ? $store->currency_code : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );

                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(), true);
                $response = $charge->getPublicData();

                if ($charge->isSuccessful() and empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                        $status =  $this->statusThisProductOrder($request, $slug);
                        if ($status->status == 'success') {
                            $res['flag'] = 1;
                            $res['slug'] = $slug;
                            $res['order_id'] = Crypt::encrypt($status->order_id);
                            return $res;
                        }
                    } elseif ($charge->isUnderReview()) {
                        $res['flag'] = 2;
                        $res['slug'] = $slug;
                        return $res;
                    }
                } else {
                    $res['flag'] = 2;
                    $res['slug'] = $slug;
                    return $res;
                }
            } else {
                $res['flag'] = 2;
                $res['slug'] = $slug;
                return $res;
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }
    public function orderpaymenterror(Request $request, $flag, $slug)
    {
        if ($flag == 1) {
            return redirect()->route('store.slug', $slug)->with('success', __('Transaction has been Successfull! '));
        } else {
            return redirect()->route('store.slug', $slug)->with('error', __('Transaction has been failed! '));
        }
    }
}
