<?php

namespace App\Http\Controllers;

use App\Traits\PaymentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayfastController extends Controller
{
    use PaymentTrait;

    public function generateSignature($data, $passPhrase = null)
    {

        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }
        return md5($getString);
    }

    public function index(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Payfast');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
            $user = Auth::user();
            try {
                $data = array(
                    'merchant_id' => !empty($pre_pay->settings['payfast_merchant_id']) ? $pre_pay->settings['payfast_merchant_id'] : '',
                    'merchant_key' => !empty($pre_pay->settings['payfast_merchant_key']) ? $pre_pay->settings['payfast_merchant_key'] : '',
                    'return_url' => route('payfast.payment.success', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id,  'status' => 'success']),
                    'cancel_url' => route('payfast.payment.success', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id,  'status' => 'cancel']),
                    'notify_url' => route('payfast.payment.success', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id,  'status' => 'notify']),
                    'name_first' => $user->name,
                    'name_last' => '',
                    'email_address' => $user->email,
                    'm_payment_id' => $pre_pay->order_id,
                    'amount' => number_format(sprintf('%.2f', $pre_pay->price), 2, '.', ''),
                    'item_name' => $pre_pay->plan->name,
                );

                $passphrase = !empty($pre_pay->settings['payfast_signature']) ? $pre_pay->settings['payfast_signature'] : '';
                $signature = $this->generateSignature($data, $passphrase);
                $data['signature'] = $signature;

                $htmlForm = '';

                foreach ($data as $name => $value) {
                    $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
                }

                return response()->json([
                    'success' => true,
                    'inputs' => $htmlForm,
                ]);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }
        } else {
            return response()->json(['status' => $pre_pay->status, 'plan_type' => $pre_pay->plan_type, 'message' => $pre_pay->message]);
        }
    }

    public function success(Request $request, $plan_id, $amount)
    {
        if ($request->status == 'success') {

            $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

            if ($verify->status == 'success') {
                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            }
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        } else {
            return redirect()->back()->with('error', __('Transaction has been failed!'));
        }
    }

    public function Paywithpayfast($slug, Request $request)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Payfast');
        $price = preg_replace('/^\$/', '', $pre_pay->price);
        if ($pre_pay->status == 'success') {
            try {

                $data = array(
                    'merchant_id' => !empty($pre_pay->settings['payfast_merchant_id']) ? $pre_pay->settings['payfast_merchant_id'] : '',
                    'merchant_key' => !empty($pre_pay->settings['payfast_merchant_key']) ? $pre_pay->settings['payfast_merchant_key'] : '',
                    'return_url' => route('payfast.success', ['order_id' => $pre_pay->order_id, 'slug' => $pre_pay->slug, 'status' => 'success']),
                    'notify_url' => route('payfast.success', ['order_id' => $pre_pay->order_id, 'slug' => $pre_pay->slug, 'status' => 'notify']),
                    'name_first' => $pre_pay->name,
                    'name_last' => '',
                    'email_address' => $pre_pay->email,
                    'm_payment_id' => $pre_pay->order_id,
                    'amount' => number_format(sprintf('%.2f', $price), 2, '.', ''),
                    'item_name' => $pre_pay->store->name,
                );
                $passphrase = !empty($pre_pay->settings['payfast_signature']) ? $pre_pay->settings['payfast_signature'] : '';
                $signature = $this->generateSignature($data, $passphrase);
                $data['signature'] = $signature;

                $htmlForm = '';

                foreach ($data as $name => $value) {
                    $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
                }
                return response()->json([
                    'success' => true,
                    'inputs' => $htmlForm,
                ]);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $pre_pay->message);
        }
    }

    public function payfastsuccess(Request $request, $slug)
    {
        if ($request->status == 'success') {

            $status = $this->statusThisProductOrder($request, $slug);
            return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
        } else {
            return redirect()->back()->with('error', __('Transaction has been failed!'));
        }
    }
}
