<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Obydul\LaraSkrill\SkrillClient;
use Obydul\LaraSkrill\SkrillRequest;
use App\Traits\PaymentTrait;

class SkrillController extends Controller
{
    use PaymentTrait;

    public function skrillPaymentPrepare(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Skrill');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $user    = Auth::user()->current_store;
            $store   = Store::where('id', $user)->first();

            if (!empty($store->logo)) {
                $logo = asset(Storage::url('uploads/store_logo/' . $store->logo));
            } else {
                $logo = asset(Storage::url('uploads/store_logo/logo.png'));
            }

            $skill               = new SkrillRequest();
            $skill->pay_to_email = $pre_pay->settings['skrill_email'] ?? '';
            $skill->return_url = route('plan.skrill.callback', ['id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success']) . '&transaction_id=' . md5($request['transaction_id']);
            $skill->cancel_url = route('plan.skrill.callback', ['id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'cancel']);

            $skill->logo_url     = $logo;

            $skill->transaction_id  = MD5($request['transaction_id']);
            $skill->amount          = $pre_pay->plan->price;
            $skill->currency        = $pre_pay->settings['currency'] ?? 'USD';
            $skill->language        = 'EN';
            $skill->prepare_only    = '1';
            $skill->merchant_fields = 'site_name, customer_email';
            $skill->site_name       = $store->name;
            $skill->customer_email  = Auth::user()->email;


            $client = new SkrillClient($skill);
            $sid    = $client->generateSID();

            $jsonSID = json_decode($sid);
            if ($jsonSID != null && $jsonSID->code == "BAD_REQUEST") {
                return redirect()->route('plans.index')->with('error', $jsonSID->message);
            }

            $redirectUrl = $client->paymentRedirectUrl($sid);
            if ($request['transaction_id']) {
                $data = [
                    'amount' => $pre_pay->plan->price,
                    'trans_id' => MD5($request['transaction_id']),
                    'currency' => $store->currency,
                    'slug' => $store->slug,
                ];
                session()->put('skrill_data', $data);
            }

            return redirect($redirectUrl);
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function skrillPlanGetPayment(Request $request, $plan_id, $amount)
    {

        $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

        if ($verify->status == 'success') {
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        }
        return redirect()->route('plans.index')->with($verify->status, $verify->message);
    }

    public function skrillPayment($slug, Request $request)
    {

        $pre_pay = $this->payThisProductOrder($request, $slug, 'Skrill');

        if ($pre_pay->status == 'success') {
            $skrill_data = [
                'transaction_id' =>   date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id',

            ];
            $request = $request->all();
            if (!empty($store->logo)) {
                $logo = asset(Storage::url('uploads/store_logo/' . $pre_pay->store->logo));
            } else {

                $logo = asset(Storage::url('uploads/store_logo/logo.png'));
            }

            $skill               = new SkrillRequest();
            $skill->pay_to_email = $pre_pay->settings['skrill_email'] ?? '';
            $skill->return_url = route('skrill.callback', ['slug' => $pre_pay->slug, 'status' => 'success']) . '?transaction_id=' . md5($skrill_data['transaction_id']);
            $skill->cancel_url = route('skrill.callback', ['slug' => $pre_pay->slug, 'status' => 'cancel']);

            $skill->logo_url     = $logo;

            $skill->transaction_id  = MD5($skrill_data['transaction_id']);
            $skill->amount          = $pre_pay->price;
            $skill->currency        = $pre_pay->store->currency;
            $skill->language        = 'EN';
            $skill->prepare_only    = '1';
            $skill->merchant_fields = 'site_name, customer_email';
            $skill->site_name       = $pre_pay->store->name;
            $skill->customer_email  = $pre_pay->email;

            $client = new SkrillClient($skill);
            $sid    = $client->generateSID();


            $jsonSID = json_decode($sid);
            if ($jsonSID != null && $jsonSID->code == "BAD_REQUEST") {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', $jsonSID->message);
            }

            $redirectUrl = $client->paymentRedirectUrl($sid);
            if ($skrill_data['transaction_id']) {
                $data = [
                    'amount'   => $pre_pay->price,
                    'trans_id' => MD5($skrill_data['transaction_id']),
                    'currency' => $pre_pay->store->currency,
                    'slug'     => $pre_pay->store->slug,
                ];
                session()->put('skrill_data', $data);
            }

            return redirect($redirectUrl);
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function skrillCallback(Request $request, $slug)
    {
        $status =  $this->statusThisProductOrder($request, $slug);
        if ($status->status == 'success') {
            return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
        }
        return redirect()->route('store.slug', $slug)->with('error', __($status->message));
    }
}
