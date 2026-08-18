<?php

use App\Http\Controllers\AamarpayController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PlanRequestController;
use App\Http\Controllers\StoreAnalytic;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProductCategorieController;
use App\Http\Controllers\ProductTaxController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PageOptionController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ProductCouponController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LandingPageSectionsController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\PaymentWallPaymentController;
use App\Http\Controllers\PayfastController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\Customer\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BanktransferController;
use App\Http\Controllers\ExpresscheckoutController;
use App\Http\Controllers\IyziPayController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AiTemplateController;
use App\Http\Controllers\AuthorizeNetController;
use App\Http\Controllers\BenefitPaymentController;
use App\Http\Controllers\CashfreeController;
use App\Http\Controllers\CinetPayController;
use App\Http\Controllers\CustomDomainRequestController;
use App\Http\Controllers\FedapayController;
use App\Http\Controllers\KhaltiPaymentController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\NepalstePaymnetController;
use App\Http\Controllers\OzowController;
use App\Http\Controllers\PaiementProController;
use App\Http\Controllers\PayHereController;
use App\Http\Controllers\PaytabController;
use App\Http\Controllers\PaytrController;
use App\Http\Controllers\ReferralProgramController;
use App\Http\Controllers\TapPaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ToyyibpayController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\XenditPaymentController;
use App\Http\Controllers\YooKassaController;
use App\Http\Controllers\MollieController;
use App\Http\Controllers\SkrillController;
use App\Http\Controllers\CoingateController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\FlutterwaveController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\PaytmPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

require __DIR__ . '/auth.php';


Route::get('/login/{lang?}', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
Route::get('/register/{ref_id?}/{lang?}', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');
Route::get('/password/forgot/{lang?}', [AuthenticatedSessionController::class, 'showLinkRequestForm'])->name('change.langPass');

Route::get('/', [DashboardController::class, 'index'])->middleware(['XSS'])->name('start');
Route::post('store-product', [StoreController::class, 'filterproductview'])->name('filter.product.view');
Route::get('get-products-variant-quantity', [ProductController::class, 'getProductsVariantQuantity'])->name('get.products.variant.quantity');

Route::group(['middleware' => ['verified']], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'XSS'])->name('dashboard');

    Route::middleware(['auth'])->group(function () {

        Route::resource('stores', StoreController::class);
        Route::post('store-setting/{id}', [StoreController::class, 'savestoresetting'])->name('settings.store');
    });

    Route::middleware(['auth', 'XSS'])->group(function () {
        Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language')->middleware(['auth', 'XSS']);
        Route::get('manage-language/{languages}', [LanguageController::class, 'manageLanguage'])->name('manage.language')->middleware(['auth', 'XSS']);
        Route::post('store-language-data/{lang}', [LanguageController::class, 'storeLanguageData'])->name('store.language.data')->middleware(['auth', 'XSS']);
        Route::post('disable-language', [LanguageController::class, 'disableLang'])->name('disablelanguage')->middleware(['auth', 'XSS']);
        Route::get('create-language', [LanguageController::class, 'createLanguage'])->name('create.language')->middleware(['auth', 'XSS']);
        Route::post('store-language', [LanguageController::class, 'storeLanguage'])->name('store.language')->middleware(['auth', 'XSS']);
        Route::delete('/lang/{lang}', [LanguageController::class, 'destroyLang'])->name('lang.destroy')->middleware(['auth', 'XSS']);
    });

    Route::middleware(['auth', 'XSS'])->group(function () {
        Route::get('store-grid/grid', [StoreController::class, 'grid'])->name('store.grid');
        Route::get('store-customDomain/customDomain', [StoreController::class, 'customDomain'])->name('store.customDomain');
        Route::get('store-subDomain/subDomain', [StoreController::class, 'subDomain'])->name('store.subDomain');
        Route::get('store-plan/{id}/plan', [StoreController::class, 'upgradePlan'])->name('plan.upgrade');
        Route::get('store-plan-active/{id}/plan/{pid}', [StoreController::class, 'activePlan'])->name('plan.active');
        Route::DELETE('store-delete/{id}', [StoreController::class, 'storedestroy'])->name('user.destroy');
        Route::DELETE('ownerstore-delete/{id}', [StoreController::class, 'ownerstoredestroy'])->name('ownerstore.destroy');
        Route::get('store-edit/{id}', [StoreController::class, 'storedit'])->name('user.edit');
        Route::Put('store-update/{id}', [StoreController::class, 'storeupdate'])->name('user.update');
    });

    Route::get('plan_request', [PlanRequestController::class, 'index'])->name('plan_request.index')->middleware(['auth', 'XSS']);
    Route::get('request_frequency/{id}', [PlanRequestController::class, 'requestView'])->name('request.view')->middleware(['auth', 'XSS']);
    Route::get('request_send/{id}', [PlanRequestController::class, 'userRequest'])->name('send.request')->middleware(['auth', 'XSS']);
    Route::get('request_response/{id}/{response}', [PlanRequestController::class, 'acceptRequest'])->name('response.request')->middleware(['auth', 'XSS']);
    Route::get('request_cancel/{id}', [PlanRequestController::class, 'cancelRequest'])->name('request.cancel')->middleware(['auth', 'XSS']);

    Route::post('/plan-pay-with-banktransfer', [BanktransferController::class, 'planPayWithBanktransfer'])->name('plan.pay.with.banktransfer')->middleware(['auth', 'XSS']);
    Route::get('bank_transfer_show/{id}', [BanktransferController::class, 'bank_transfer_show'])->name('bank_transfer.show');
    Route::post('status_edit/{id}', [BanktransferController::class, 'StatusEdit'])->name('status.edit');


    Route::get('/store-change/{id}', [StoreController::class, 'changeCurrantStore'])->name('change_store')->middleware(['auth', 'XSS']);

    Route::get('/change/mode', [DashboardController::class, 'changeMode'])->name('change.mode');
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile')->middleware(['auth', 'XSS']);


    Route::put('change-password', [DashboardController::class, 'updatePassword'])->name('update.password');
    Route::put('edit-profile', [DashboardController::class, 'editprofile'])->name('update.account')->middleware(['auth', 'XSS']);
    Route::get('storeanalytic', [StoreAnalytic::class, 'index'])->name('storeanalytic')->middleware(['auth', 'XSS']);


    Route::middleware(['auth', 'XSS'])->group(function () {
        Route::post('business-setting', [SettingController::class, 'saveBusinessSettings'])->name('business.setting');
        Route::post('company-setting', [SettingController::class, 'saveCompanySettings'])->name('company.setting');
        Route::post('email-setting', [SettingController::class, 'saveEmailSettings'])->name('email.setting');
        Route::post('system-setting', [SettingController::class, 'saveSystemSettings'])->name('system.setting');
        Route::post('pusher-setting', [SettingController::class, 'savePusherSettings'])->name('pusher.setting');


        Route::post('test-mail', [SettingController::class, 'testMail'])->name('test.mail')->middleware(['auth', 'XSS']);
        Route::get('test-mail', [SettingController::class, 'testMail'])->name('test.mail')->middleware(['auth', 'XSS']);
        Route::post('test-mail/send', [SettingController::class, 'testSendMail'])->name('test.send.mail')->middleware(['auth', 'XSS']);

        Route::get('settings', [SettingController::class, 'index'])->name('settings');
    });
    Route::post('payment-setting', [SettingController::class, 'savePaymentSettings'])->name('payment.setting')->middleware(['auth']);


    Route::middleware(['auth', 'XSS'])->group(function () {

        Route::resource('product_categorie', ProductCategorieController::class);
    });


    Route::get('/config-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');
        return redirect()->back()->with('success', 'Clear Cache successfully.');
    });

    Route::post('cookie-setting', [SettingController::class, 'saveCookieSettings'])->name('cookie.setting');
    Route::any('/cookie-consent', [SettingController::class, 'CookieConsent'])->name('cookie-consent');

    Route::middleware(['auth', 'XSS'])->group(function () {

        Route::resource('product_tax', ProductTaxController::class);
    });

    Route::middleware(['auth', 'XSS'])->group(function () {

        Route::resource('shipping', ShippingController::class);
    });

    Route::resource('location', LocationController::class)->middleware(['auth', 'XSS']);
    // Route::resource('page_options', PageOptionController::class)->middleware(['auth', 'XSS']);
    // Route::resource('blog', BlogController::class)->middleware(['auth', 'XSS']);

    // Route::get('blog-social', [BlogController::class, 'socialBlog'])->middleware(['auth', 'XSS'])->name('blog.social');
    // Route::post('store-social-blog', [BlogController::class, 'storeSocialblog'])->middleware(['auth', 'XSS'])->name('store.socialblog');

    Route::resource('webhook', WebhookController::class)->middleware(['auth', 'XSS',]);

    Route::get('/plans', [PlanController::class, 'index'])->middleware(['auth', 'XSS'])->name('plans.index');

    Route::get('/plans/create', [PlanController::class, 'create'])->middleware(['auth', 'XSS'])->name('plans.create');

    Route::post('/plans', [PlanController::class, 'store'])->middleware(['auth', 'XSS'])->name('plans.store');

    Route::get('/plans/edit/{id}', [PlanController::class, 'edit'])->middleware(['auth', 'XSS'])->name('plans.edit');

    Route::put('/plans/{id}', [PlanController::class, 'update'])->middleware(['auth', 'XSS'])->name('plans.update');

    Route::delete('plans/delete/{id}', [PlanController::class, 'destroy'])->name('plans.destroy')->middleware(['auth', 'XSS']);

    Route::post('/user-plans/', [PlanController::class, 'userPlan'])->middleware(['auth', 'XSS'])->name('update.user.plan');

    Route::get('plan-trial/{id}', [PlanController::class, 'planTrial'])->name('plan.trial')->middleware(['auth', 'XSS']);

    Route::get('themes', [themeController::class, 'index'])->name('themes.theme')->middleware(['auth', 'XSS']);

    Route::post('pwa-settings/{id}', [StoreController::class, 'pwasetting'])->name('setting.pwa')->middleware(['auth', 'XSS']);

    Route::resource('orders', OrderController::class)->middleware(['auth', 'XSS']);

    Route::get('order-receipt/{id}', [OrderController::class, 'receipt'])->middleware(['auth', 'XSS'])->name('order.receipt');
    Route::delete('oreder/product/{id}/{variant_id?}/{order_id}/{key}', [OrderController::class, 'delete_order_item'])->name('delete.order_item');
    Route::delete('oreder/{order_id}', [OrderController::class, 'delete_plan_order'])->name('delete.plan_order');

    Route::middleware(['auth', 'XSS'])->group(function () {

        Route::get('/product-variants/create', [ProductController::class, 'productVariantsCreate'])->name('product.variants.create');
        Route::get('/product-variants/edit/{product_id}', [ProductController::class, 'productVariantsEdit'])->name('product.variants.edit');
        Route::post('/product-variants-possibilities/{product_id}', [ProductController::class, 'getProductVariantsPossibilities'])->name('product.variants.possibilities');
        Route::get('/get-product-variants-possibilities', [ProductController::class, 'getProductVariantsPossibilities'])->name('get.product.variants.possibilities');
        Route::get('product/grid', [ProductController::class, 'grid'])->name('product.grid');
        Route::delete('product/{id}/delete', [ProductController::class, 'fileDelete'])->name('products.file.delete');
        Route::delete('product/variant/{id}/{product_id}', [ProductController::class, 'VariantDelete'])->name('products.variant.delete');
    });



    Route::resource('product', ProductController::class)->middleware(['auth', 'XSS']);
    Route::post('product/{id}/update', [ProductController::class, 'productUpdate'])->middleware(['auth'])->name('products.update');
    Route::get('/store-resource/edit/display/{id}', [StoreController::class, 'storeenable'])->middleware(['auth', 'XSS'])->name('store-resource.edit.display');
    Route::put('/store-resource/display/{id}', [StoreController::class, 'storeenableupdate'])->middleware(['auth', 'XSS'])->name('store-resource.display');

    Route::middleware(['auth', 'XSS'])->group(function () {

        Route::resource('store-resource', StoreController::class);
    });


    Route::middleware(['XSS'])->group(function () {

        Route::get('order', [StripePaymentController::class, 'index'])->name('order.index');
        Route::get('/stripe/{code}', [StripePaymentController::class, 'stripe'])->name('stripe');
    });

    //plan payment

    //stripe
    Route::post('stripe-payment', [StripePaymentController::class, 'addpayment'])->middleware(['XSS'])->name('stripe.payment');
    Route::get('plan-stripe-status/{id}/{amount}', [StripePaymentController::class, 'planGetStatus'])->middleware(['auth', 'XSS'])->name('plan.stripe.status');

    //Paypal
    Route::post('plan-pay-with-paypal', [PaypalController::class, 'planPayWithPaypal'])->middleware(['auth', 'XSS'])->name('plan.pay.with.paypal');
    Route::get('{id}/{amount}/get-store-payment-status', [PaypalController::class, 'storeGetPaymentStatus'])->middleware(['auth', 'XSS'])->name('get.store.payment.status');

    //paytm
    Route::post('paytm-prepare-plan', [PaytmPaymentController::class, 'paytmPaymentPrepare'])->middleware(['auth'])->name('paytm.prepare.plan');
    Route::post('paytm-payment-plan', [PaytmPaymentController::class, 'paytmPlanGetPayment'])->middleware(['auth'])->name('plan.paytm.callback');

    //mollie
    Route::post('mollie-prepare-plan', [MollieController::class, 'molliePaymentPrepare'])->middleware(['auth'])->name('mollie.prepare.plan');
    Route::get('mollie-payment-plan/{plan_id}/{amount}', [MollieController::class, 'molliePlanGetPayment'])->middleware(['auth'])->name('plan.mollie.callback');

    //skrill
    Route::post('skrill-prepare-plan', [SkrillController::class, 'skrillPaymentPrepare'])->middleware(['auth'])->name('skrill.prepare.plan');
    Route::get('{id}/{amount}/skrill-payment-plan', [SkrillController::class, 'skrillPlanGetPayment'])->middleware(['auth'])->name('plan.skrill.callback');

    //coingate
    Route::post('plan-pay-with-coingate', [CoingateController::class, 'coingatePaymentPrepare'])->middleware(['auth', 'XSS'])->name('coingate.prepare.plan');
    Route::get('{id}/{amount}/get-store-coingate-status', [CoingateController::class, 'coingatePlanGetPayment'])->middleware(['auth', 'XSS'])->name('coingate.payment.callback');

    //paymentwall
    Route::post('paymentwall', [PaymentWallPaymentController::class, 'index'])->name('paymentwall');
    Route::post('plan-pay-with-paymentwall/{plan_id}/{amount}', [PaymentWallPaymentController::class, 'planPayWithPaymentwall'])->name('plan.pay.with.paymentwall');
    Route::any('/plan/error/{flag}', [PaymentWallPaymentController::class, 'paymenterror'])->name('callback.error');

    //toyyibpay
    Route::post('toyyibpay-prepare-plan', [ToyyibpayController::class, 'toyyibpayPaymentPrepare'])->middleware(['auth'])->name('toyyibpay.prepare.plan');
    Route::get('toyyibpay-payment-plan/{plan_id}/{amount}', [ToyyibpayController::class, 'toyyibpayPlanGetPayment'])->middleware(['auth'])->name('plan.toyyibpay.callback');

    //iyzipay
    Route::post('iyzipay/prepare', [IyziPayController::class, 'initiatePayment'])->name('iyzipay.payment.init');
    Route::post('iyzipay/callback/plan/{id}/{amount}', [IyzipayController::class, 'iyzipayCallback'])->name('iyzipay.payment.callback');

    //paytab
    Route::post('plan-pay-with-paytab', [PaytabController::class, 'planPayWithpaytab'])->middleware(['auth'])->name('plan.pay.with.paytab');
    Route::any('plan-paytab-success/{plan_id}/{amount}', [PaytabController::class, 'PaytabGetPayment'])->middleware(['auth'])->name('plan.paytab.success');

    //benefit
    Route::any('/payment/initiate/{plan_id}', [BenefitPaymentController::class, 'initiatePayment'])->name('benefit.initiate');
    Route::any('call_back/{plan_id}/{amount}', [BenefitPaymentController::class, 'call_back'])->name('benefit.call_back');

    //cashfree
    Route::post('cashfree/payments/store', [CashfreeController::class, 'cashfreePaymentStore'])->name('cashfree.payment');
    Route::any('cashfree/payments/success/{plan_id}/{amount}', [CashfreeController::class, 'cashfreePaymentSuccess'])->name('cashfreePayment.success');

    //aamarpay
    Route::post('/aamarpay/payment', [AamarpayController::class, 'pay'])->name('pay.aamarpay.payment');
    Route::any('/aamarpay/success/{data}', [AamarpayController::class, 'aamarpaysuccess'])->name('pay.aamarpay.success');

    //paytr
    Route::post('/paytr/payment/{plan_id}', [PaytrController::class, 'PlanpayWithPaytr'])->name('pay.paytr.payment');
    Route::get('/paytr/sussess/{plan_id}/{amount}', [PaytrController::class, 'paytrsuccess'])->name('pay.paytr.success');

    //yookassa
    Route::any('/plan/yookassa/payment', [YooKassaController::class, 'planPayWithYooKassa'])->name('plan.pay.with.yookassa');
    Route::get('/plan/yookassa/{plan_id}/{amount}', [YooKassaController::class, 'planGetYooKassaStatus'])->name('plan.get.yookassa.status');

    //midtrans
    Route::any('/plan/midtrans/payment', [MidtransController::class, 'planPayWithMidtrans'])->name('plan.pay.with.midtrans');
    Route::any('/midtrans/callback/{plan_id}/{amount}', [MidtransController::class, 'planGetMidtransStatus'])->name('plan.get.midtrans.status');

    //xendit
    Route::any('/plan/xendit/payment', [XenditPaymentController::class, 'planPayWithXendit'])->name('plan.pay.with.xendit');
    Route::any('/xendit/payment/status/{plan_id}/{amount}', [XenditPaymentController::class, 'planGetXenditStatus'])->name('plan.xendit.status');

    //paiement pro
    Route::post('/plan/paiementpro/payment', [PaiementProController::class, 'planPayWithPaiementPro'])->name('plan.pay.with.paiementpro');
    Route::get('paiementpro/status', [PaiementProController::class, 'planGetPaiementProStatus'])->name('paiementpro.status');

    //fedapay
    Route::post('/plan/fedapay/payment', [FedapayController::class, 'planPayWithFedapay'])->name('plan.pay.with.fedapay');
    Route::get('fedapay/payment/{plan_id}/{amount}', [FedapayController::class, 'planGetFedapayStatus'])->name('plan.pay.fedapay.status');

    //nepalste
    Route::post('/plan/nepalste/payment', [NepalstePaymnetController::class, 'planPayWithNepalste'])->name('plan.pay.with.nepalste');
    Route::get('nepalste/status/{plan_id}/{amount}', [NepalstePaymnetController::class, 'planGetNepalsteStatus'])->name('nepalste.status');

    //payhere
    Route::post('/plan/payhere/payment', [PayHereController::class, 'planPayWithPayHere'])->name('plan.pay.with.payhere');
    Route::any('payhere/status/{plan_id}/{amount}', [PayHereController::class, 'planGetPayHereStatus'])->name('payhere.status');

    //cinetpay
    Route::post('/plan/cinetpay/payment', [CinetPayController::class, 'planPayWithCinetPay'])->name('plan.pay.with.cinetpay');
    Route::post('/plan/cinetpay/payment/return', [CinetPayController::class, 'planCinetPayReturn'])->name('plan.cinetpay.return');

    //tap
    Route::post('plan-pay-with-tap', [TapPaymentController::class, 'planPayWithTap'])->name('plan.pay.with.tap')->middleware(['auth']);
    Route::any('plan-get-tap-status/{plan_id}/{amount}', [TapPaymentController::class, 'planGetTapStatus'])->name('plan.get.tap.status');

    //ozow
    Route::post('plan-pay-with-ozow', [OzowController::class, 'planPayWithOzow'])->name('plan.pay.with.ozow')->middleware(['auth']);
    Route::any('plan-get-ozow-status/{plan_id}', [OzowController::class, 'planGetOzowStatus'])->name('plan.get.ozow.status');

    //paystack
    Route::post('plan-pay-with-paystack', [PaystackController::class, 'planPayWithPaystack'])->middleware(['auth', 'XSS'])->name('plan.pay.with.paystack');
    Route::get('paystack-plan/{code}/{plan_id}/{amount}', [PaystackController::class, 'paystackGetPaymentStatus'])->middleware(['auth'])->name('paystack.plan.callback');

    //flutterwave
    Route::post('plan-pay-with-flutterwave', [FlutterwaveController::class, 'planPayWithflutterwave'])->middleware(['auth', 'XSS'])->name('plan.pay.with.flutterwave');
    Route::get('flutterwave-plan/{plan_id}/{amount}', [FlutterwaveController::class, 'flutterwaveGetPaymentStatus'])->middleware(['auth'])->name('flutterwave.plan.callback');

    //razorpay
    Route::post('razorpay-plan-payment', [RazorpayController::class, 'planPayWithrazorpay'])->middleware(['auth', 'XSS'])->name('plan.razorpay.payment');
    Route::get('razorpay-payment/{code}/{plan_id}/{amount}', [RazorpayController::class, 'razorpayGetPaymentStatus'])->middleware(['auth'])->name('razorpay.plan.callback');

    //mercado
    Route::post('mercadopago-prepare-plan', [MercadoPagoController::class, 'mercadopagoPaymentPrepare'])->middleware(['auth'])->name('mercadopago.prepare.plan');
    Route::any('plan-mercado-payment-callback/{plan_id}/{amount}', [MercadoPagoController::class, 'mercadopagoPaymentCallback'])->middleware(['auth'])->name('plan.mercado.payment.callback');

    //payfast
    Route::post('payfast-plan', [PayfastController::class, 'index'])->name('payfast.payment')->middleware(['auth']);
    Route::get('payfast-plan/{plan_id}/{amount}', [PayfastController::class, 'success'])->name('payfast.payment.success')->middleware(['auth']);

    //khalti
    Route::post('plan-pay-with-khalti', [KhaltiPaymentController::class, 'planPayWithKhalti'])->name('plan.pay.with.khalti')->middleware(['auth']);
    Route::any('plan-get-khalti-status', [KhaltiPaymentController::class, 'planGetKhaltiStatus'])->name('plan.get.khalti.status');


    //authorizenet
    Route::post('plan-pay-with-authorizenet', [AuthorizeNetController::class, 'planPayWithAuthorizeNet'])->name('plan.pay.with.authorizenet')->middleware(['auth']);
    Route::any('plan-get-authorizenet-status/{plan_id}/{amount}', [AuthorizeNetController::class, 'planGetAuthorizeNetStatus'])->name('plan.get.authorizenet.status');

    Route::get('/apply-coupon', [CouponController::class, 'applyCoupon'])->middleware(['auth', 'XSS'])->name('apply.coupon');

    Route::resource('coupons', CouponController::class)->middleware(['auth', 'XSS']);

    Route::post('prepare-payment', [PlanController::class, 'preparePayment'])->middleware(['auth', 'XSS'])->name('prepare.payment');


    Route::post('/payment/{code}', [PlanController::class, 'payment'])->middleware(['auth', 'XSS'])->name('payment');


    Route::get(
        'qr-code',
        function () {
            return QrCode::generate();
        }
    );

    Route::resource('product-coupon', ProductCouponController::class)->middleware(['auth', 'XSS']);



    // Plan Purchase Payments methods

    Route::get('plan/prepare-amount', [PlanController::class, 'planPrepareAmount'])->name('plan.prepare.amount');

    Route::get(
        '/get_landing_page_section/{name}',
        function ($name) {
            $plans = DB::table('plans')->get();
            return view('custom_landing_page.' . $name, compact('plans'));
        }
    );

    Route::get('email_templates', [EmailTemplateController::class, 'index'])->name('email_template.index')->middleware(['auth', 'XSS']);
    // Route::get('email_template_lang/{lang?}', [EmailTemplateController::class, 'emailTemplate'])->name('email_template')->middleware(['auth', 'XSS']);
    Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->name('manage.email.language')->middleware(['auth', 'XSS']);
    Route::put('email_template_lang/{id}/', [EmailTemplateController::class, 'updateEmailSettings'])->name('updateEmail.settings')->middleware(['auth']);
    Route::put('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->name('store.email.language')->middleware(['auth', 'XSS']);
    Route::put('email_template_status/{id}', [EmailTemplateController::class, 'updateStatus'])->name('status.email.language')->middleware(['auth', 'XSS']);
    Route::put('email_template_status/{id}', [EmailTemplateController::class, 'updateStatus'])->name('email_template.update')->middleware(['auth', 'XSS']);

    Route::get('{id}/export/product', [ProductController::class, 'export'])->name('product.export');
    Route::get('{id}/export/order', [OrderController::class, 'export'])->name('order.export');
    Route::get('export/shipping', [ShippingController::class, 'export'])->name('shipping.export');
    Route::get('export/category', [ProductCategorieController::class, 'export'])->name('category.export');
    Route::get('export/tax', [ProductTaxController::class, 'export'])->name('tax.export');
    Route::get('export/customer', [StoreController::class, 'exports'])->name('customer.exports');
    Route::get('export/store', [StoreController::class, 'export'])->name('store.export');
    Route::get('export/coupons', [CouponController::class, 'export'])->name('coupons.export');
    Route::get('export/plan_requests', [PlanRequestController::class, 'export'])->name('planrequests.export');

    Route::get('import/coupon/file', [ProductCouponController::class, 'importFile'])->name('coupon.file.import');
    Route::post('import/coupon', [ProductCouponController::class, 'import'])->name('coupon.import');
    Route::get('export/coupon', [ProductCouponController::class, 'export'])->name('coupon.export');

    ////////////////////

    Route::get('product/import/export', [ProductController::class, 'fileImportExport'])->name('product.file.import');
    Route::post('product/import', [ProductController::class, 'fileImport'])->name('product.import');

    /*==================================Recaptcha====================================================*/
    Route::post('/recaptcha-settings', [SettingController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store')->middleware(['auth', 'XSS']);

    /*==============================================================================================================================*/

    Route::any('user-reset-password/{id}', [StoreController::class, 'userPassword'])->name('user.reset');
    Route::post('user-reset-password/{id}', [StoreController::class, 'userPasswordReset'])->name('user.password.update');
    Route::get('user-login/{id}', [StoreController::class, 'LoginManage'])->name('users.login');

    /*================================================================================================================================*/


    /*========================================================================================================================*/

    Route::get('/customer', [StoreController::class, 'customerindex'])->name('customer.index')->middleware(['XSS']);
    Route::get('/customer/view/{id}', [StoreController::class, 'customershow'])->name('customer.show')->middleware(['XSS']);

    Route::post('storage-settings', [SettingController::class, 'storageSettingStore'])->name('storage.setting.store')->middleware(['auth', 'XSS']);
});

//store payment

//stripe
Route::post('/stripe/{slug?}', [StripePaymentController::class, 'stripePost'])->name('stripe.post')->middleware(['XSS']);
Route::get('store-payment/userpayment/stripe', [StripePaymentController::class, 'getProductStatus'])->name('store.payment.stripe');

//paypal
Route::post('pay-with-paypal/{slug?}', [PaypalController::class, 'PayWithPaypal'])->middleware(['XSS'])->name('pay.with.paypal');
Route::get('{id}/get-payment-status{slug?}', [PaypalController::class, 'GetPaymentStatus'])->middleware(['XSS'])->name('get.payment.status');

//paytm
Route::post('{slug}/paytm/prepare-payments/', [PaytmPaymentController::class, 'paytmOrder'])->name('paytm.prepare.payments');
Route::post('paytm/callback/', [PaytmPaymentController::class, 'paytmCallback'])->name('paytm.callback');

//mollie
Route::post('{slug}/mollie/prepare-payments/', [MollieController::class, 'mollieOrder'])->name('mollie.prepare.payments');
Route::get('{slug}/mollie/callback/', [MollieController::class, 'mollieCallback'])->name('mollie.callback');

//skrill
Route::post('{slug}/skrill/prepare-payments/', [SkrillController::class, 'skrillPayment'])->name('skrill.prepare.payments');
Route::get('{slug}/skrill-callback', [SkrillController::class, 'skrillCallback'])->name('skrill.callback');

//coingate
Route::post('pay-with-coingate/{slug?}', [CoingateController::class, 'PayWithCoingate'])->middleware(['XSS'])->name('coingate.prepare');
Route::get('get-coingate-status{slug?}', [CoingateController::class, 'GetCoingateStatus'])->middleware(['XSS'])->name('get.coingate.status');

//paymentwall

Route::any('{slug}/paymentwall/order/', [PaymentWallPaymentController::class, 'orderindex'])->name('paymentwall.index');
Route::post('/{slug}/order-pay-with-paymentwall/', [PaymentWallPaymentController::class, 'orderPayWithPaymentwall'])->name('order.pay.with.paymentwall');
Route::any('{slug}/order/error/{flag}', [PaymentWallPaymentController::class, 'orderpaymenterror'])->name('order.callback.error');

//toyyibpay
Route::post('{slug}/toyyibpay/prepare-payments/', [ToyyibpayController::class, 'toyyibpaypayment'])->name('toyyibpay.prepare.payments');
Route::get('toyyibpay/callback/{get_amount}/{slug?}', [ToyyibpayController::class, 'toyyibpaycallback'])->name('toyyibpay.callback');

//iyzipay
Route::post('iyzipay/prepare-payments/{slug}', [IyziPayController::class, 'iyzipaypayment'])->name('iyzipay.prepare.payment');
Route::post('iyzipay/callback/{slug}', [IyzipayController::class, 'iyzipaypaymentCallback'])->name('iyzipay.callback');

//paytab
Route::post('pay-with-paytab/{slug}', [PaytabController::class, 'PayWithpaytab'])->name('pay.with.paytab');
Route::any('paytab-success/store/{slug}', [PaytabController::class, 'PaytabGetPaymentCallback'])->name('paytab.success');

//benefit
Route::any('{slug}/payment/initiate', [BenefitPaymentController::class, 'storeInitiatePayment'])->name('store.benefit.initiate');
Route::any('/store-benefit/payments/call_back/{slug}', [BenefitPaymentController::class, 'storeCall_back'])->name('store.benefit.call_back');

//cashfree
Route::post('{slug}/cashfree/payments/store', [CashfreeController::class, 'payWithCashfree'])->name('store.cashfree.initiate');
Route::any('store/cashfree/payments/success/{slug?}', [CashfreeController::class, 'storeCashfreePaymentSuccess'])->name('store.cashfreePayment.success');

//aamarpay
Route::post('{slug}/aamarpay/payment', [AamarpayController::class, 'payWithAamarpay'])->name('store.pay.aamarpay.payment');
Route::any('aamarpay/payment/success/store/{data}', [AamarpayController::class, 'AamarpayStoreSuccess'])->name('store.aamarpay.success');

//paytr
Route::post('{slug}/paytr/payment/', [PaytrController::class, 'PayWithPaytr'])->name('store.pay.paytr.payment');
Route::get('/paytr/sussess-store/{slug}', [PaytrController::class, 'paytrsuccessCallback'])->name('store.pay.paytr.success');

//yookassa
Route::post('{slug}/yookassa/payment/', [YooKassaController::class, 'storePayWithYookassa'])->name('store.pay.yookassa.payment');
Route::any('{slug}/yookassa/status/', [YooKassaController::class, 'getStorePaymentStatus'])->name('store.yookassa.status');

//midtrans
Route::any('{slug}/midtrans/payment/', [MidtransController::class, 'storePayWithMidtrans'])->name('store.pay.midtrans.payment');
Route::any('midtrans/status/{slug?}', [MidtransController::class, 'getStorePaymentStatus'])->name('store.midtrans.status');

//xendit
Route::any('{slug}/xendit/payment/', [XenditPaymentController::class, 'storePayWithXendit'])->name('store.pay.xendit.payment');
Route::any('/xendit/status/{slug?}', [XenditPaymentController::class, 'getStorePaymentStatus'])->name('store.xendit.status');

//paiement pro
Route::any('{slug}/paimentpro/payment/', [PaiementProController::class, 'storePayWithPaimentPro'])->name('store.pay.paimentpro.payment');
Route::any('/paimentpro/status/{slug?}', [PaiementProController::class, 'getStorePaymentStatus'])->name('store.paimentpro.status');

//fedapay
Route::any('{slug}/fedapay/payment/', [FedapayController::class, 'storePayWithFedapay'])->name('store.pay.fedapay.payment');
Route::any('/fedapay/store/{slug?}', [FedapayController::class, 'getStorePaymentFedapayStatus'])->name('store.fedapay.status');

//nepalste
Route::post('{slug}/nepalste/payment', [NepalstePaymnetController::class, 'storePayWithNepalste'])->name('store.pay.nepalste.payment');
Route::get('nepalste/status/store/{slug?}', [NepalstePaymnetController::class, 'getStorePaymentStatus'])->name('store.nepalste.status');

//payhere
Route::post('{slug}/payhere/payment', [PayHereController::class, 'storePayWithPayHere'])->name('store.pay.payhere.payment');
Route::any('payhere/store/{slug?}', [PayHereController::class, 'getStorePaymentStatus'])->name('store.payhere.status');

//cinetpay
Route::post('{slug}/cinetpay/payment', [CinetPayController::class, 'storePayWithCinetPay'])->name('store.pay.cinetpay.payment');
Route::post('/cinetpay/payment/return/store/', [CinetPayController::class, 'storeCinetPayReturn'])->name('store.cinetpay.return');

//tap
Route::any('/store-tap-payment/{slug}', [TapPaymentController::class, 'storePayWithTap'])->name('store.pay.with.tap');
Route::any('/store-tap-status/{slug?}',  [TapPaymentController::class, 'storeGetTapStatus'])->name('store.tap.status');

//ozow
Route::any('/store-ozow-payment/{slug}', [OzowController::class, 'storePayWithOzow'])->name('store.pay.with.ozow');
Route::any('/store-get-ozow-status/{slug}', [OzowController::class, 'storeGetOzowStatus'])->name('store.get.ozow.status');

//paystack
Route::post('pay-with-paystack/{slug?}', [PaystackController::class, 'PayWithPaystack'])->middleware(['XSS'])->name('pay.with.paystack');
Route::get('paystack/{slug}/{code}/{order_id}', [PaystackController::class, 'paystackPayment'])->name('get.paystack.status');

//flutterwave
Route::post('pay-with-flutterwave/{slug?}', [FlutterwaveController::class, 'PayWithflutterwave'])->middleware(['XSS'])->name('pay.with.flutterwave');
Route::get('flutterwave/{slug}', [FlutterwaveController::class, 'flutterwavePayment'])->name('get.flutterwave.status');
Route::get('plan/{pay}', [FlutterwaveController::class, 'Checkout'])->name('flutterwave.pay');

//razorpay
Route::post('payment-with-razorpay/{slug?}', [RazorpayController::class, 'PayWithrazorpay'])->middleware(['XSS'])->name('Payment.with.Razorpay');
Route::get('razorpay/{slug}/{code}/{order_id}', [RazorpayController::class, 'razorpayPayment'])->name('get.razorpay.status');

//mercado
Route::post('mercadopago/prepare-payments/{slug?}', [MercadoPagoController::class, 'mercadopagoStorePayment'])->name('mercadopago.store.prepare');
Route::any('mercadopago/callback/{slug?}', [MercadoPagoController::class, 'mercadopagoStoreCallback'])->name('mercadopago.store.callback');

//payfast
Route::post('{slug}/payfast', [PayfastController::class, 'Paywithpayfast'])->name('payfast');
Route::get('payfast/{slug}', [PayfastController::class, 'payfastsuccess'])->name('payfast.success');

//khalti
Route::any('/store-khalti-payment/{slug}', [KhaltiPaymentController::class, 'storePayWithKhalti'])->name('store.pay.with.khalti');
Route::any('/store-get-khalti-status/{slug}', [KhaltiPaymentController::class, 'getStorePaymentStatus'])->name('store.get.khalti.status');

// AuthorizeNet
Route::any('{slug}/authorizenet/payment', [AuthorizeNetController::class, 'storePayWithAuthorizeNet'])->name('store.pay.authorizenet.payment');
Route::any('/store-get-authorizenet-status/{slug}', [AuthorizeNetController::class, 'getStorePaymentStatus'])->name('store.get.authorizenet.status');

Route::get('/apply-productcoupon', [ProductCouponController::class, 'applyProductCoupon'])->name('apply.productcoupon');

Route::post('owner-payment-setting/{slug?}', [SettingController::class, 'saveOwnerPaymentSettings'])->name('owner.payment.setting')->middleware(['auth', 'XSS']);
Route::post('owner-email-setting/{slug?}', [SettingController::class, 'saveOwneremailSettings'])->name('owner.email.setting')->middleware(['auth', 'XSS']);
Route::post('owner-twilio-setting/{slug?}', [SettingController::class, 'saveOwnertwilioSettings'])->name('owner.twilio.setting')->middleware(['auth', 'XSS']);
Route::post('owner-whatsapp-setting/{slug?}', [SettingController::class, 'saveOwnerWhatsappSettings'])->name('owner.whatsapp.setting')->middleware(['auth', 'XSS']);

Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('pixels', [SettingController::class, 'index'])->name('pixel.index');
    Route::get('pixel/create', [SettingController::class, 'pixel_create'])->name('pixel.create');
    Route::post('pixel', [SettingController::class, 'pixel_store'])->name('pixel.store');
    Route::delete('pixel-delete/{id}', [SettingController::class, 'pixeldestroy'])->name('pixel.destroy');
    Route::get('pixel-edit/{id}', [SettingController::class, 'pixelEdit'])->name('pixel.edit');
    Route::any('pixel-update/{id}', [SettingController::class, 'pixelUpdate'])->name('pixel.update');
});

Route::get('store/remove-session/{slug}', [StoreController::class, 'removeSession'])->name('remove.session');

Route::get('store/{slug?}/{view?}', [StoreController::class, 'storeSlug'])->name('store.slug')->middleware(['domain-check']);

Route::get('store-variant/{slug?}/product/{id}', [StoreController::class, 'storeVariant'])->name('store-variant.variant');
Route::post('user-product_qty/{slug?}/product/{id}/{variant_id?}', [StoreController::class, 'productqty'])->name('user-product_qty.product_qty');
Route::post('user-location/{slug}/location/{id}', [StoreController::class, 'UserLocation'])->name('user.location');
Route::post('user-shipping/{slug}/shipping/{id}', [StoreController::class, 'UserShipping'])->name('user.shipping');
Route::delete('delete_cart_item/{slug?}/product/{id}/{variant_id?}', [StoreController::class, 'delete_cart_item'])->name('delete.cart_item');
Route::get('add-to-cart-product/{slug?}/product/{id}', [StoreController::class, 'addToCartProduct'])->name('add-to-cart.product');

Route::get('store/{slug?}/product/{id}', [StoreController::class, 'productView'])->name('store.product.product_view');
Route::get('store-checkout/{slug?}', [StoreController::class, 'processToCheckout'])->name('store-process.checkout');

Route::get('store-complete/{slug?}/{id}', [StoreController::class, 'complete'])->name('store-complete.complete');



Route::get('/{slug?}/order/{id}', [StoreController::class, 'userorder'])->name('user.order');

Route::post('{slug?}/whatsapp', [StoreController::class, 'whatsapp'])->name('user.whatsapp');
Route::post('{slug?}/telegram', [StoreController::class, 'telegram'])->name('user.telegram');

Route::get('change-language-store/{slug?}/{lang}', [LanguageController::class, 'changeLanquageStore'])->middleware(['XSS'])->name('change.languagestore');

Route::post('store/{slug?}', [StoreController::class, 'changeTheme'])->name('store.changetheme');


Route::post('/store/custom-msg/{slug}', [StoreController::class, 'customMassage'])->name('customMassage');
Route::post('store/get-massage/{slug}', [StoreController::class, 'getWhatsappUrl'])->name('get.whatsappurl');

Route::post('store/{slug}/downloadable_prodcut', [StoreController::class, 'downloadable_prodcut'])->name('user.downloadable_prodcut');

Route::get('{slug}/customer-login', [CustomerLoginController::class, 'showLoginForm'])->name('customer.loginform');
Route::post('{slug}/customer-login/{cart?}', [CustomerLoginController::class, 'login'])->name('customer.login')->middleware(['XSS']);

Route::get('{slug}/user-create', [StoreController::class, 'userCreate'])->name('store.usercreate');
Route::post('{slug}/user-create', [StoreController::class, 'userStore'])->name('store.userstore');

Route::get('{slug}/customer-home', [StoreController::class, 'customerHome'])->name('customer.home')->middleware(['customerauth']);

Route::get('{slug}/customer-profile/{id}', [CustomerLoginController::class, 'profile'])->name('customer.profile')->middleware(['customerauth']);
Route::put('{slug}/customer-profile/update/{id}', [CustomerLoginController::class, 'profileUpdate'])->name('customer.profile.update')->middleware(['customerauth']);
Route::post('{slug}/customer-logout', [CustomerLoginController::class, 'logout'])->name('customer.logout');

// Route::get('store-payment/{slug?}/userpayment', [StoreController::class, 'userPayment'])->name('store-payment.payment');


Route::post('{slug?}/cod', [StoreController::class, 'cod'])->name('user.cod');
Route::post('{slug?}/bank_transfer', [StoreController::class, 'bank_transfer'])->name('user.bank_transfer');


Route::get('store/product/{order_id}/{customer_id}/{slug}', [StoreController::class, 'orderview'])->name('store.product.product_order_view');

Route::resource('product', ProductController::class)->middleware(['auth', 'XSS']);
// Route::get('product/{id}/update', [ProductController::class, 'productUpdate'])->middleware(['auth'])->name('products.update');
Route::get('/store-resource/edit/display/{id}', [StoreController::class, 'storeenable'])->middleware(['auth', 'XSS'])->name('store-resource.edit.display');
Route::put('/store-resource/display/{id}', [StoreController::class, 'storeenableupdate'])->middleware(['auth', 'XSS'])->name('store-resource.display');

Route::any('add-to-cart/store/{slug?}/{product_id?}/{quantity?}/{variant_id?}', [StoreController::class, 'addToCart'])->name('user.addToCart');
// Route::any('add-to-cart/{slug?}/cart/{product_id?}/{quantity?}/{variant_id?}', [StoreController::class, 'addToCart'])->name('user.addToCart');
Route::get('/create-expresscheckout/{product_id?}', [ExpresscheckoutController::class, 'create'])->name('expresscheckout.create');
Route::post('/store-expresscheckout/{product_id?}', [ExpresscheckoutController::class, 'store'])->name('expresscheckout.store');
Route::get('/edit-expresscheckout/{product_id?}', [ExpresscheckoutController::class, 'edit'])->name('expresscheckout.edit');
Route::post('/update-expresscheckout/{product_id?}', [ExpresscheckoutController::class, 'update'])->name('expresscheckout.update');
Route::delete('/delete-expresscheckout/{product_id?}', [ExpresscheckoutController::class, 'destroy'])->name('expresscheckout.destroy');



Route::resource('roles', RoleController::class)->middleware(['auth', 'XSS']);
Route::resource('users', UserController::class)->middleware(['auth', 'XSS']);
Route::get('users/reset/{id}', [UserController::class, 'reset'])->name('users.reset')->middleware(['auth', 'XSS']);
Route::post('users/reset/{id}', [UserController::class, 'updatePassword'])->name('users.resetpassword')->middleware(['auth', 'XSS']);
Route::get('owner-user-login/{id}', [UserController::class, 'UserLoginManage'])->name('owner.users.login');
Route::resource('permissions', PermissionController::class)->middleware(['auth', 'XSS',]);

Route::post('chatgptkey', [SettingController::class, 'chatgptkey'])->name('settings.chatgptkey');
Route::get('generate/{template_name}', [AiTemplateController::class, 'create'])->name('generate');
Route::post('generate/keywords/{id}', [AiTemplateController::class, 'getKeywords'])->name('generate.keywords');
Route::post('generate/response', [AiTemplateController::class, 'AiGenerate'])->name('generate.response');

// store links (Admin Side)
Route::get('/store-links/{id}', [StoreController::class, 'storelinks'])->middleware(['auth', 'XSS'])->name('store.links');

// Company Login (Admin Side)
Route::get('users/{id}/login-with-owner', [UserController::class, 'LoginWithOwner'])->middleware(['auth', 'XSS'])->name('login.with.owner');
Route::get('login-with-owner/exit', [UserController::class, 'ExitOwner'])->middleware(['auth', 'XSS'])->name('exit.owner');

// Admin Hub
Route::get('owner-info/{id}', [UserController::class, 'OwnerInfo'])->name('owner.info');
Route::post('user-unable', [UserController::class, 'UserUnable'])->name('user.unable');

// plan enable/disable
Route::post('plan/active', [PlanController::class, 'planActive'])->name('plan.enable')->middleware(['auth', 'XSS']);

// Refund
Route::get('/refund/{id}/{user_id}', [PlanController::class, 'refund'])->name('order.refund');


//=================================Customdomain Request Module ====================================//

Route::get('custom_domain_request', [CustomDomainRequestController::class, 'index'])->name('custom_domain_request.index')->middleware(['auth', 'XSS']);
Route::delete('custom_domain_request/{id}/destroy', [CustomDomainRequestController::class, 'destroy'])->name('custom_domain_request.destroy')->middleware(['XSS']);
Route::get('custom_domain_request/{id}/{response}', [CustomDomainRequestController::class, 'updateRequestStatus'])->name('custom_domain_request.request')->middleware(['auth', 'XSS']);


//================================= referral ====================================//

Route::get('referral-program/owner', [ReferralProgramController::class, 'ownerIndex'])->name('referral-program.owner')->middleware(['auth', 'XSS']);
Route::resource('referral-program', ReferralProgramController::class)->middleware(['auth', 'XSS']);
Route::get('request-amount-sent/{id}', [ReferralProgramController::class, 'requestedAmountSent'])->name('request.amount.sent')->middleware(['auth', 'XSS']);
Route::get('request-amount-cancel/{id}', [ReferralProgramController::class, 'requestCancel'])->name('request.amount.cancel')->middleware(['auth', 'XSS']);
Route::post('request-amount-store/{paidAmount}', [ReferralProgramController::class, 'requestedAmountStore'])->name('request.amount.store')->middleware(['auth', 'XSS']);
Route::get('request-amount/{id}/{status}', [ReferralProgramController::class, 'requestedAmount'])->name('amount.request')->middleware(['auth', 'XSS']);
