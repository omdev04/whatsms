<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthorizeNetController extends Controller
{
    use PaymentTrait;
    public function planPayWithAuthorizeNet(Request $request)
    {
        $pre_pay = $this->payThisPlan($request, 'AuthorizeNet');
        if ($pre_pay->status == 'success' && $pre_pay->plan_type !== 'free') {
            $plan_id = \Illuminate\Support\Facades\Crypt::encrypt($pre_pay->plan->id);

            try {
                return view('AuthorizeNet.request', compact('pre_pay', 'plan_id'));
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with($pre_pay->status, $pre_pay->message);
        }
    }

    public function planGetAuthorizeNetStatus(Request $request, $plan_id, $amount)
    {
        $input          = $request->all();
        $admin_settings = Utility::getAdminPaymentSetting();
        try {
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($admin_settings['authorizenet_merchant_login_id']);
            $merchantAuthentication->setTransactionKey($admin_settings['authorizenet_merchant_transaction_key']);
            $refId                  = 'ref' . time();

            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($input['cardNumber']);
            $creditCard->setExpirationDate($input['year'] . '-' . $input['month']);
            $creditCard->setCardCode($input['cvv']);

            $paymentOne             = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);

            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($amount);
            $transactionRequestType->setPayment($paymentOne);

            $requestNet             = new AnetAPI\CreateTransactionRequest();
            $requestNet->setMerchantAuthentication($merchantAuthentication);
            $requestNet->setRefId($refId);
            $requestNet->setTransactionRequest($transactionRequestType);
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __('something Went wrong!'));
        }
        $controller = new AnetController\CreateTransactionController($requestNet);
        if (!empty($admin_settings['authorizenet_mode']) && $admin_settings['authorizenet_mode'] == 'live') {

            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        } else {

            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    if ($request->status == 'success') {
                        $verify =  $this->statusThisPlan($request, $plan_id, $amount, $request->status, false);

                        return redirect()->route('plans.index')->with($verify->status, $verify->message);
                    } else {
                        return redirect()->route('plans.index')->with('error', __('Something went wrong, please try again.'));
                    }
                    if ($tresponse->getErrors() != null) {
                        return redirect()->route('plans.index')->with('error', __('Transaction Failed!'));
                    }
                }
            } else {
                $tresponse      = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return redirect()->route('plans.index')->with('error', __('Transaction Failed!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __('No reponse returned!'));
                }
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('No reponse returned!'));
        }
    }

    public function storePayWithAuthorizeNet(Request $request, $slug)
    {
        $request->merge(['slug' => $slug]);
        $pre_pay = $this->payThisProductOrder($request, $slug, 'AuthorizeNet');
        try {
            return view('AuthorizeNet.invoice', compact('pre_pay', 'slug'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function getStorePaymentStatus(Request $request, $slug)
    {
        try {
            $input      = $request->all();
            $amount     = $input['get_amount'];
            $store      = Store::where('slug', $slug)->first();

            $storepaymentSetting = Utility::getPaymentSetting($store->id);
            try {
                $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
                $merchantAuthentication->setName($storepaymentSetting['authorizenet_merchant_login_id']);
                $merchantAuthentication->setTransactionKey($storepaymentSetting['authorizenet_merchant_transaction_key']);
                $refId                  = 'ref' . time();

                $creditCard = new AnetAPI\CreditCardType();
                $creditCard->setCardNumber($input['cardNumber']);
                $creditCard->setExpirationDate($input['year'] . '-' . $input['month']);
                $creditCard->setCardCode($input['cvv']);

                $paymentOne             = new AnetAPI\PaymentType();
                $paymentOne->setCreditCard($creditCard);

                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authCaptureTransaction");
                $transactionRequestType->setAmount($amount);
                $transactionRequestType->setPayment($paymentOne);

                $requestNet             = new AnetAPI\CreateTransactionRequest();
                $requestNet->setMerchantAuthentication($merchantAuthentication);
                $requestNet->setRefId($refId);
                $requestNet->setTransactionRequest($transactionRequestType);

                $controller = new AnetController\CreateTransactionController($requestNet);
                if (!empty($storepaymentSetting['authorizenet_mode']) && $storepaymentSetting['authorizenet_mode'] == 'live') {
                    $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
                } else {
                    $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
                }
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', __($e->getMessage()));
            }

            if ($response != null) {
                if ($response->getMessages()->getResultCode() == "Ok") {
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        $status =  $this->statusThisProductOrder($request, $slug);
                        if ($status->status == 'success') {
                            return redirect()->route('store-complete.complete', [$slug, $status->order_id])->with($status->status, $status->message);
                        }
                        return redirect()->route('store.slug', $slug)->with('error', __($status->message));
                    }
                } else {
                    $tresponse      = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        return redirect()->route('store.slug', $slug)->with('error', __('Transaction Unsuccesfull'));
                    } else {
                        return redirect()->route('store.slug', $slug)->with('error', __('No reponse returned!'));
                    }
                }
            } else {
                return redirect()->route('store.slug', $slug)->with('error', __('No reponse returned!'));
            }
        } catch (\Throwable $th) {
            return redirect()->route('store.slug', $slug)->with('error', __($th->getMessage()));
        }
    }
}
