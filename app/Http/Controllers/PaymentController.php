<?php

namespace App\Http\Controllers;

use App\Models\Utility;


class PaymentController extends Controller
{




    public function planpaymentSetting()
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        config(
            [
                'services.paytm-wallet.env' => isset($admin_payment_setting['paytm_mode']) ? $admin_payment_setting['paytm_mode'] : '',
                'services.paytm-wallet.merchant_id' => isset($admin_payment_setting['paytm_merchant_id']) ? $admin_payment_setting['paytm_merchant_id'] : '',
                'services.paytm-wallet.merchant_key' =>  isset($admin_payment_setting['paytm_merchant_key']) ? $admin_payment_setting['paytm_merchant_key'] : '',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' => isset($admin_payment_setting['paytm_industry_type']) ? $admin_payment_setting['paytm_industry_type'] : '',
            ]
        );
    }
}
