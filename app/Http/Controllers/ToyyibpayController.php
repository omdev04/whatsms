<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Traits\PaymentTrait;
use Illuminate\Support\Facades\Auth;

class ToyyibpayController extends Controller
{
    use PaymentTrait;
    public $secretKey, $callBackUrl, $returnUrl, $categoryCode, $is_enabled, $invoiceData;


    public function index()
    {
        return view('payment');
    }

    public function toyyibpayPaymentPrepare(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Toyyibpay');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            $some_data = array(

                'userSecretKey'           => $pre_pay->settings['toyyibpay_secret_key'] ?? '',
                'categoryCode'            => $pre_pay->settings['toyyibpay_category_code'] ?? '',
                'billName'                => $pre_pay->plan->name,
                'billDescription'         => $pre_pay->plan->name,
                'billPriceSetting'        => 1,
                'billPayorInfo'           => 1,
                'billAmount'              => $pre_pay->price,
                'billReturnUrl'           =>  route('plan.toyyibpay.callback', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'success']),
                'billCallbackUrl'         => route('plan.toyyibpay.callback', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'order_id' => $pre_pay->order_id, 'status' => 'cancel']),
                'billExternalReferenceNo' => $pre_pay->order_id,
                'billTo'                  => Auth::user()->name,
                'billEmail'               => Auth::user()->email,
                'billPhone'               => Auth::user()->phone ?? '0000000000',
                'billSplitPayment'        => 0,
                'billSplitPaymentArgs'    => '',
                'billPaymentChannel'      => '0',
                'billContentEmail'        => __("Thank you for purchasing our product!"),
                'billChargeToCustomer'    => 1,
                'billExpiryDate'          => date('d-m-Y', strtotime('+3 days')),
                'billExpiryDays'          => 3,

            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);

            $obj = json_decode($result);
            return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function toyyibpayPlanGetPayment(Request $request, $planId, $getAmount)
    {

        $verify =  $this->statusThisPlan($request, $planId, $getAmount, $request->status, false,);

        if ($verify->status == 'success') {
            return redirect()->route('plans.index')->with($verify->status, $verify->message);
        }
        return redirect()->route('plans.index')->with($verify->status, $verify->message);
    }
    public function toyyibpaypayment(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Toyyibpay');

        if ($pre_pay->status == 'success') {

            $some_data = array(

                'userSecretKey'           => $pre_pay->settings['toyyibpay_secret_key'] ?? '',
                'categoryCode'            => $pre_pay->settings['toyyibpay_category_code'] ?? '',
                'billName'                => $pre_pay->store->name,
                'billDescription'         => $pre_pay->store->name,
                'billPriceSetting'        => 1,
                'billPayorInfo'           => 1,
                'billAmount'              => $pre_pay->price,
                'billReturnUrl'           => route('toyyibpay.callback', [$pre_pay->slug, 'status' => 'success']),
                'billCallbackUrl'         => route('toyyibpay.callback', [$pre_pay->slug, 'status' => 'success']),
                'billExternalReferenceNo' => $pre_pay->order_id,
                'billTo'                  => $pre_pay->name,
                'billEmail'               => $pre_pay->email,
                'billPhone'               => str_replace(array("+", "(", ")", "-"), "", $pre_pay->phone),
                'billSplitPayment'        => 0,
                'billSplitPaymentArgs'    => '',
                'billPaymentChannel'      => '0',
                'billContentEmail'        => __("Thank you for purchasing our product!"),
                'billChargeToCustomer'    => 1,
                'billExpiryDate'          => date('d-m-Y', strtotime('+3 days')),
                'billExpiryDays'          => 3,

            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);

            $obj = json_decode($result);
            return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);
        } else {
            return redirect()->route('store.slug', $pre_pay->slug)->with($pre_pay->status, $pre_pay->message);
        }
    }
    public function toyyibpaycallback(Request $request, $slug)
    {
        try {
            $status =  $this->statusThisProductOrder($request, $slug, true);
            if ($status->status == 'success') {
                return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
            }
            return redirect()->route('store.slug', $slug)->with('error', __($status->message));
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }
}
