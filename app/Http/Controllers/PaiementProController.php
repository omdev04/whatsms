<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaymentTrait;

class PaiementProController extends Controller
{
    use PaymentTrait;

    public function planPayWithPaiementPro(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'Paiement Pro');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);
            $user               = Auth::user();

            $data = array(
                'merchantId'            => isset($pre_pay->settings['paiment_pro_merchant_id']) ? $pre_pay->settings['paiment_pro_merchant_id'] : '',
                'amount'                =>  $pre_pay->price,
                'description'           => "Api PHP",
                'channel'               => $pre_pay->channel,
                'countryCurrencyCode'   => isset($pre_pay->settings['currency']) ? $pre_pay->settings['currency'] : 'USD',
                'referenceNumber'       => "REF-" . time(),
                'customerEmail'         => $user->email,
                'customerFirstName'     => $user->name,
                'customerLastname'      => $user->name,
                'customerPhoneNumber'   => $pre_pay->mobile_number,
                'notificationURL'       => route('paiementpro.status', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'status' => 'notify',]),
                'returnURL'             => route('paiementpro.status', ['plan_id' => $plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'status' => 'success',]),
                'returnContext'         => json_encode([
                    'coupon_code' => $pre_pay->coupon,
                ]),
            );

            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paiementpro.net/webservice/onlinepayment/init/curl-init.php");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $response = curl_exec($ch);

            curl_close($ch);
            $response = json_decode($response);

            if (isset($response->success) && $response->success == true) {
                // redirect to approve href
                return redirect($response->url);

                return redirect()
                    ->route('plans.index', \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id))
                    ->with('error', __('Something went wrong. OR Unknown error occurred'));
            } else {
                return redirect()
                    ->route('plans.index', \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id))
                    ->with('error', $response->message ?? __('Something went wrong.'));
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planGetPaiementProStatus(Request $request, $plan_id, $amount)
    {
        try {
            if ($request->responsecode == 0) {
                $request->status = 'success';
                $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false,);

                return redirect()->route('plans.index')->with($verify->status, $verify->message);
            } else {
                return redirect()->back()->with('error', __('Transaction has benn failed.'));
            }
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function storePayWithPaimentPro(Request $request, $slug)
    {
        $pre_pay = $this->payThisProductOrder($request, $slug, 'Paiement Pro');

        try {
            if ($pre_pay->settings['paiment_pro_merchant_id'] != null) {
                try {
                    $data = array(
                        'merchantId'            => $pre_pay->settings['paiment_pro_merchant_id'] ?? null,
                        'amount'                => $pre_pay->price,
                        'description'           => "Api PHP",
                        'channel'               => $request->channel ?? 'CARD',
                        'countryCurrencyCode'   => isset($pre_pay->currency) ? $pre_pay->currency : 'USD',
                        'referenceNumber'       => "REF-" . time(),
                        'customerEmail'         => $pre_pay->email ?? null,
                        'customerFirstName'     => $pre_pay->name ?? null,
                        'customerLastname'      => $pre_pay->name ?? null,
                        'customerPhoneNumber'   => $pre_pay->phone ?? null,
                        'notificationURL'       => route('store.paimentpro.status', ['slug' => $slug, 'orderId'  => $pre_pay->order_id, 'status' => 'notify']),
                        'returnURL'             => route('store.paimentpro.status', ['slug' => $slug, 'orderId'  => $pre_pay->order_id, 'status' => 'success']),
                        'returnContext'         => json_encode([
                            'coupon_code' => $coupon->code ?? '',
                        ]),
                    );

                    $data = json_encode($data);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.paiementpro.net/webservice/onlinepayment/init/curl-init.php");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    $response = curl_exec($ch);

                    curl_close($ch);
                    $response = json_decode($response);
                    if (isset($response->success) && $response->success == true) {
                        // redirect to approve href
                        return redirect($response->url);

                        return redirect()->route('store.slug', $pre_pay->slug)->with('error', __('Something went wrong. OR Unknown error occurred'));
                    } else {
                        return redirect()->route('store.slug', $pre_pay->slug)->with('error', $response->message ?? __('Something went wrong.'));
                    }
                } catch (\Throwable $th) {
                    return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
                }
            } else {
                return redirect()->route('store.slug', $pre_pay->slug)->with('error', $pre_pay->message);
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $pre_pay->slug)->with('error', $th->getMessage());
        }
    }
    public function getStorePaymentStatus(Request $request, $slug)
    {
        try {
            if ($request->responsecode == 0) {
                $status =  $this->statusThisProductOrder($request, $slug);
                if ($status->status == 'success') {
                    return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                }
                return redirect()->route('store.slug', $slug)->with('error', __($status->message));
            } else {
                return redirect()->route('store.slug', $slug)->with('error', __('Transaction has been failed.'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }
}
