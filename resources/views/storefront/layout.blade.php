@php
    $settings = Utility::settings();
    // store RTL
    $s_logo = \App\Models\Utility::get_file('uploads/store_logo/');
    $logo = \App\Models\Utility::get_file('uploads/is_cover_image/');
    $data = DB::table('settings');
    $logo1 = \App\Models\Utility::get_file('uploads/logo/');
    $company_favicon = \App\Models\Utility::getValByName('company_favicon');
    $meta_image = \App\Models\Utility::get_file('uploads/meta_image');
    $profile = \App\Models\Utility::get_file('uploads/customerprofile/');
    $meta_image = \App\Models\Utility::get_file('uploads/meta_image');
    $meta_img = $store->meta_image;
    if (!isset($meta_img)) {
        $meta_img = 'meta_image.png';
    }
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $settings['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="WorkDo">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ __('Home') }} - {{ $store->tagline ? $store->tagline : env('APP_NAME', ucfirst($store->name)) }}
    </title>
    <meta name="description" content="{{ ucfirst($store->name) }} - {{ ucfirst($store->tagline) }}">
    <meta name="keywords" content="{{ $store->meta_keyword }}">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $store->meta_keyword }}">
    <meta name="description" content="{{ ucfirst($store->meta_description) }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $store->meta_keyword }}">
    <meta property="og:description" content="{{ ucfirst($store->meta_description) }}">
    <meta property="og:image" content="{{ $meta_image . '/' . $meta_img }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $store->meta_keyword }}">
    <meta property="twitter:description" content="{{ ucfirst($store->meta_description) }}">
    <meta property="twitter:image" content="{{ $meta_image . '/' . $meta_img }}">
    <link rel="icon"
        href="{{ $logo1 . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '?timestamp=' . time() }}">
    @if ($store->theme_dir == 'theme1')
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
            rel="stylesheet">
    @elseif($store->theme_dir == 'theme2')
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
            rel="stylesheet">
    @elseif($store->theme_dir == 'theme3')
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    @elseif($store->theme_dir == 'theme4')
        <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
            rel="stylesheet">
    @elseif($store->theme_dir == 'theme5')
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet">
    @elseif($store->theme_dir == 'theme6')
        <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
            rel="stylesheet" />
    @elseif($store->theme_dir == 'theme7')
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
            rel="stylesheet">
    @endif

    @if (isset($settings['SITE_RTL']) && $settings['SITE_RTL'] == 'on')
        @if (!empty($store->store_theme))
            <link rel="stylesheet"
                href="{{ asset('themes/' . $store->theme_dir . '/css/' . $store->theme_dir . '-v1-rtl' . '.css') }}">
            <link rel="stylesheet"
                href="{{ asset('themes/' . $store->theme_dir . '/css/responsive-' . $store->theme_dir . '-v1-rtl' . '.css') }}">
        @endif
    @else
        @if (!empty($store->store_theme))
            <link rel="stylesheet"
                href="{{ asset('themes/' . $store->theme_dir . '/css/' . $store->theme_dir . '-v1' . '.css') }}">
            <link rel="stylesheet"
                href="{{ asset('themes/' . $store->theme_dir . '/css/responsive-' . $store->theme_dir . '-v1' . '.css') }}">
        @endif
    @endif

    <link rel="stylesheet" href="{{ asset('custom/libs/animate.css/animate.min.css') }}">
    <link rel='stylesheet' href='{{ asset('assets/css/cookieconsent.css') }}' media="screen" />
    <link rel="stylesheet" href="{{ asset('custom/css/swiper-bundle.min.css') }}" id="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">

    {{-- pwa customer app --}}
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="mobile-wep-app-capable" content="yes">
    <meta name="apple-mobile-wep-app-capable" content="yes">
    <meta name="msapplication-starturl" content="/">
    <link rel="stylesheet" href="{{ asset('custom/css/theme-custom.css') }}" id="stylesheet')}}">

    <link rel="apple-touch-icon"
        href="{{ $logo1 . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}" />
    @if ($store->enable_pwa_store == 'on')
        <link rel="manifest"
            href="{{ asset('storage/uploads/customer_app/store_' . $store_settings->id . '/manifest.json') }}" />
    @endif
    @if (!empty($store->pwa_store($store->slug)->theme_color))
        <meta name="theme-color" content="{{ $store->pwa_store($store->slug)->theme_color }}" />
    @endif
    @if (!empty($store->pwa_store($store->slug)->background_color))
        <meta name="apple-mobile-web-app-status-bar"
            content="{{ $store->pwa_store($store->slug)->background_color }}" />
    @endif
    @foreach ($pixelScript as $script)
        <?= $script ?>
    @endforeach

    <style>
        .backhome {
            padding: 30px, 20px, 10, 0px !important;
            margin: 7px !important;
        }
    </style>
    {{-- floating whatsapp --}}
    <link rel="stylesheet" href="{{ asset('assets/css/floating-wpp.min.css') }}">
</head>

<body class="{{ $store->store_theme }}">
    @php
        if (!empty(session()->get('lang'))) {
            $currantLang = session()->get('lang');
        } else {
            $currantLang = $store->lang;
        }

        $languages = \App\Models\Utility::languages();
    @endphp
    <input type="hidden" id="return_url">
    <input type="hidden" id="return_order_id">
    @yield('store_content')

    <div class="overlay"></div>

    <div class="modal d-flex align-items-center" id="quickview-modal">
        <div class="modal-wrapper">
            <div class="modal-inner">
                <div class="cart"></div>
                <button type="button" class="modal-close close-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"
                        fill="none">
                        <path
                            d="M15 0C6.72535 0 0 6.72535 0 15C0 23.2746 6.72535 30 15 30C23.2746 30 30 23.2746 30 15C30 6.72535 23.2746 0 15 0ZM15 28.4859C7.57042 28.4859 1.51408 22.4296 1.51408 15C1.51408 7.57042 7.57042 1.51408 15 1.51408C22.4296 1.51408 28.4859 7.57042 28.4859 15C28.4859 22.4296 22.4296 28.4859 15 28.4859Z"
                            fill="#060606" />
                        <path
                            d="M16.0564 14.9298L20.4578 10.5283C20.7395 10.2467 20.7395 9.7537 20.4578 9.47201C20.1761 9.19032 19.6832 9.19032 19.4015 9.47201L15.0001 13.8734L10.5987 9.47201C10.317 9.19032 9.82401 9.19032 9.54232 9.47201C9.26063 9.7537 9.26063 10.2467 9.54232 10.5283L13.9437 14.9298L9.54232 19.296C9.26063 19.5776 9.26063 20.0706 9.54232 20.3523C9.68317 20.4931 9.89443 20.5636 10.0705 20.5636C10.2465 20.5636 10.4578 20.4931 10.5987 20.3523L15.0001 15.9509L19.4015 20.3523C19.5423 20.4931 19.7536 20.5636 19.9296 20.5636C20.1057 20.5636 20.317 20.4931 20.4578 20.3523C20.7395 20.0706 20.7395 19.5776 20.4578 19.296L16.0564 14.9298Z"
                            fill="#060606" />
                    </svg>
                </button>
                <div class="body">
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('custom/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/floating-wpp.min.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            $('.floating-wpp').floatingWhatsApp({
                phone: '{{ $store->whatsapp_contact_number }}',
                popupMessage: 'how may i help you?',
                showPopup: true,
                message: 'Message To Send',
                headerTitle: 'Ask Questions'
            });
        });
    </script>

    <script src="{{ asset('custom/js/swiper-bundle.min.js') }}"></script>

    <script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
    {{-- pwa customer app --}}

    @if ($store->enable_pwa_store == 'on')
        <script type="text/javascript">
            const container = document.querySelector("body")

            const coffees = [];

            if ("serviceWorker" in navigator) {

                window.addEventListener("load", function() {
                    navigator.serviceWorker
                        .register("{{ asset('serviceWorker.js') }}")
                        .then(res => console.log("service worker registered"))
                        .catch(err => console.log("service worker not registered", err))

                })
            }
        </script>
    @endif

    <script src="{{ asset('assets/js/cookieconsent.js') }}"></script>
    @php

        $cookie_settings = Utility::AdminSettings();
    @endphp


    @if ($cookie_settings['enable_cookie'] == 'on' && $cookie_settings['cookie_logging'] == 'on')
        @include('layouts.cookie_consent')
    @endif


    <!--scripts start here-->
    <script src="{{ asset('themes/' . $store->theme_dir . '/js/jquery.min.js') }}"></script>
    <script src="{{ asset('themes/' . $store->theme_dir . '/js/slick.min.js') }}"></script>
    @if (isset($settings['SITE_RTL']) && $settings['SITE_RTL'] == 'on')
        <script src="{{ asset('themes/' . $store->theme_dir . '/js/rtl-custom.js') }}"></script>
    @else
        <script src="{{ asset('themes/' . $store->theme_dir . '/js/custom.js') }}"></script>
    @endif
    <!--scripts end here-->

    {{-- quickview-modal --}}
    <script>
        $(document).on('click', '#loginBtn', function() {
            $(".close").trigger("click");
        });

        $(document).on('click', '.asGuest', function() {
            $(".close").trigger("click");

            // PHP logic to check payment flags
            @php
                $store_flags = ['enable_whatsapp', 'enable_telegram', 'enable_cod'];
                $store_payment_flags = ['is_stripe_enabled', 'is_paypal_enabled', 'is_paystack_enabled', 'is_flutterwave_enabled', 'is_razorpay_enabled', 'is_mercado_enabled', 'is_paytm_enabled', 'is_mollie_enabled', 'is_skrill_enabled', 'is_coingate_enabled', 'is_paymentwall_enabled', 'is_payfast_enabled', 'is_toyyibpay_enabled', 'is_manuallypay_enabled', 'is_bank_enabled', 'is_iyzipay_enabled', 'is_paytab_enabled', 'is_benefit_enabled', 'is_cashfree_enabled', 'is_aamarpay_enabled', 'is_paytr_enabled'];

                $allPaymentsDisabled = collect($store_flags)->contains(fn($flag) => isset($store[$flag]) && $store[$flag] === 'on') || collect($store_payment_flags)->contains(fn($flag) => isset($store_payments[$flag]) && $store_payments[$flag] === 'on');
            @endphp

            // Pass the PHP variable to JavaScript
            let allPaymentsDisabled = @json($allPaymentsDisabled);

            if (!allPaymentsDisabled) {
                show_toastr('Error', 'Payment method not found.<br>Please contact the store owner.', 'error');
            } else {
                $("#asGuest, #paymentsBtn").show();
                $("#checkout-modal").hide();
                $("body").removeClass("no-scroll");
            }
        });



        $(document).on('click', '.authUser', function() {
            @php
                // Define the flags to check
                $store_flags = ['enable_whatsapp', 'enable_telegram', 'enable_cod'];
                $store_payment_flags = ['is_stripe_enabled', 'is_paypal_enabled', 'is_paystack_enabled', 'is_flutterwave_enabled', 'is_razorpay_enabled', 'is_mercado_enabled', 'is_paytm_enabled', 'is_mollie_enabled', 'is_skrill_enabled', 'is_coingate_enabled', 'is_paymentwall_enabled', 'is_payfast_enabled', 'is_toyyibpay_enabled', 'is_manuallypay_enabled', 'is_bank_enabled', 'is_iyzipay_enabled', 'is_paytab_enabled', 'is_benefit_enabled', 'is_cashfree_enabled', 'is_aamarpay_enabled', 'is_paytr_enabled'];

                // Check if any of the flags are enabled
                $allPaymentsDisabled = collect($store_flags)->contains(fn($flag) => isset($store[$flag]) && $store[$flag] === 'on') || collect($store_payment_flags)->contains(fn($flag) => isset($store_payments[$flag]) && $store_payments[$flag] === 'on');
            @endphp

            @if (!$allPaymentsDisabled)
                show_toastr('Error', 'Payment method not found.<br>Please contact the store owner.', 'error');
            @else

                $("#asGuest, #paymentsBtn").show();
                $(".checkoutBtn").hide();
                $("body").removeClass("no-scroll");
            @endif
        });
        $('.authUser').on('click', function() {
            var name = $('.fname').val();
            var email = $('.email').val();
            var phone = $('.phone').val();
            var billing_address = $('.billing_address').val();
            var shipping_address = $('.shipping_address').val();
            var custom_field_title_1 = $('.custom_field_title_1').val();
            var custom_field_title_2 = $('.custom_field_title_2').val();
            var custom_field_title_3 = $('.custom_field_title_3').val();
            var custom_field_title_4 = $('.custom_field_title_4').val();
            var special_instruct = $('.special_instruct').val();

            var ajaxData = {
                name: name,
                email: email,
                phone: phone,
                custom_field_title_1: custom_field_title_1,
                custom_field_title_2: custom_field_title_2,
                custom_field_title_3: custom_field_title_3,
                custom_field_title_4: custom_field_title_4,
                billing_address: billing_address,
                shipping_address: shipping_address,
                special_instruct: special_instruct,
            }
        });


        $(document).on('click', '#guestBtn,#loginBtn', function() {
            $(".checkoutBtn").hide();
        });

        @if (!empty($pro_cart) && count($pro_cart['products']) > 0)
            $(".checkoutBtn").show();
            $("#asGuest,#paymentsBtn").hide();
            // $("#paymentsBtn").hide();
        @else
            $(".checkoutBtn,#asGuest,#paymentsBtn ").hide();
            // $("#asGuest").hide();
            // $("#paymentsBtn").hide();
        @endif
    </script>
    @php
        $store_settings = \App\Models\Store::where('slug', $store->slug)->first();
    @endphp
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $store_settings->google_analytic }}"></script>

    {!! $store_settings->storejs !!}

    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', '{{ $store_settings->google_analytic }}');
    </script>


    <!-- Facebook Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            // s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ !empty($store_settings->facebook_pixel) }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $store_settings->facebook_pixel }}&ev=PageView&noscript=1" /></noscript>
    <!-- End Facebook Pixel Code -->

    <script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).on('click', 'a[data-ajax-popup="true"]', function() {
            var title = $(this).data('title');

            var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
            var url = $(this).data('url');

            // Determine modal ID based on title
            var modalId = '';
            if (title === 'Login') {
                modalId = 'login-modal';
            } else if (title === 'Register') {
                modalId = 'register-modal';
            } else if (title === 'Add Variant') {
                modalId = 'variant-modal';
            } else if (title === 'item') {
                modalId = 'cart-modal';
            } else if (title === 'CheckOut') {
                modalId = 'checkout-modal';
            } else if (title === 'Your Order Details') {
                modalId = 'orderview-modal';
            } else if (title === 'My Profile') {
                modalId = 'profileview-modal';
            } else {
                modalId = 'quickview-modal';
            }


            // Dynamically update the ID of the modal
            if (title === 'Login') {
                if ($('#quickview-modal').length) {
                    var modal = $('#quickview-modal');
                    modal.attr('id', modalId);
                } else if ($('#register-modal').length) {
                    var modal = $('#register-modal');
                    modal.attr('id', modalId);
                } else if ($('#variant-modal').length) {
                    var modal = $('#variant-modal');
                    modal.attr('id', modalId);
                } else if ($('#cart-modal').length) {
                    var modal = $('#cart-modal');
                    modal.attr('id', modalId);
                } else if ($('#checkout-modal').length) {
                    var modal = $('#checkout-modal');
                    modal.attr('id', modalId);
                }

            } else if (title === 'Register') {
                var modal = $('#login-modal');
                modal.attr('id', modalId);
            } else if (title === 'Add Variant') {
                if ($('#quickview-modal').length) {
                    var modal = $('#quickview-modal');
                    modal.attr('id', modalId);
                } else if ($('#register-modal').length) {
                    var modal = $('#register-modal');
                    modal.attr('id', modalId);
                } else if ($('#login-modal').length) {
                    var modal = $('#login-modal');
                    modal.attr('id', modalId);
                } else if ($('#cart-modal').length) {
                    var modal = $('#cart-modal');
                    modal.attr('id', modalId);
                } else if ($('#checkout-modal').length) {
                    var modal = $('#checkout-modal');
                    modal.attr('id', modalId);
                }


            } else if (title === 'item') {
                if ($('#quickview-modal').length) {
                    var modal = $('#quickview-modal');
                    modal.attr('id', modalId);
                } else if ($('#register-modal').length) {
                    var modal = $('#register-modal');
                    modal.attr('id', modalId);
                } else if ($('#login-modal').length) {
                    var modal = $('#login-modal');
                    modal.attr('id', modalId);
                } else if ($('#variant-modal').length) {
                    var modal = $('#variant-modal');
                    modal.attr('id', modalId);
                } else if ($('#checkout-modal').length) {
                    var modal = $('#checkout-modal');
                    modal.attr('id', modalId);
                } else if ($('#profileview-modal').length) {
                    var modal = $('#profileview-modal');
                    modal.attr('id', modalId);
                }

            } else if (title === 'CheckOut') {
                if ($('#login-modal').length) {
                    var modal = $('#login-modal');
                    modal.attr('id', modalId);
                } else if ($('#register-modal').length) {
                    var modal = $('#register-modal');
                    modal.attr('id', modalId);
                } else if ($('#variant-modal').length) {
                    var modal = $('#variant-modal');
                    modal.attr('id', modalId);
                } else if ($('#cart-modal').length) {
                    var modal = $('#cart-modal');
                    modal.attr('id', modalId);
                } else {
                    var modal = $('#quickview-modal');
                    modal.attr('id', modalId);
                }

            } else if (title === 'Your Order Details') {
                if ($('#quickview-modal').length) {
                    var modal = $('#quickview-modal');
                    modal.attr('id', modalId);
                } else if ($('#profileview-modal').length) {
                    var modal = $('#profileview-modal');
                    modal.attr('id', modalId);
                }
            } else if (title === 'My Profile') {
                if ($('#quickview-modal').length) {
                    var modal = $('#quickview-modal');
                    modal.attr('id', modalId);
                } else if ($('#variant-modal').length) {
                    var modal = $('#variant-modal');
                    modal.attr('id', modalId);
                } else if ($('#orderview-modal').length) {
                    var modal = $('#orderview-modal');
                    modal.attr('id', modalId);
                }

            } else {
                if ($('#login-modal').length) {
                    var modal = $('#login-modal');
                    modal.attr('id', modalId);
                } else if ($('#register-modal').length) {
                    var modal = $('#register-modal');
                    modal.attr('id', modalId);
                } else if ($('#variant-modal').length) {
                    var modal = $('#variant-modal');
                    modal.attr('id', modalId);
                } else if ($('#cart-modal').length) {
                    var modal = $('#cart-modal');
                    modal.attr('id', modalId);
                } else if ($('#checkout-modal').length) {
                    var modal = $('#checkout-modal');
                    modal.attr('id', modalId);
                } else if ($('#profileview-modal').length) {
                    var modal = $('#profileview-modal');
                    modal.attr('id', modalId);
                }
            }

            $("#" + modalId + " .modal-title").html(title);
            $("#" + modalId + " .modal-wrapper .modal-inner").addClass(
                'modal-' + size);

            $.ajax({
                url: url,
                success: function(data) {
                    $("#" + modalId).addClass('active');
                    $('#' + modalId + ' .body').html(data);

                    if (title === 'item') {
                        // Append the custom HTML to the .cart div
                        const cartHTML = `<div class="cart-modal-topbar d-flex align-items-center">
                            <span>
                                <svg width="17" height="13" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.7511 0.506786C16.4191 0.174821 15.881 0.174821 15.549 0.506786L5.36544 10.6904L1.45106 6.77603C1.11913 6.44407 0.580972 6.4441 0.248974 6.77603C-0.0829912 7.10797 -0.0829912 7.64612 0.248974 7.97809L4.7644 12.4934C5.09623 12.8254 5.63479 12.8251 5.96649 12.4934L16.7511 1.70887C17.083 1.37694 17.083 0.838751 16.7511 0.506786Z" fill="#060606"/>
                                </svg>
                            </span>
                             <h2>{{ __('Item Added To Your Cart') }}</h2>
                            </div>`;
                        $('#' + modalId + ' .cart').html(
                            cartHTML); // Replace the content of the .cart div
                    }

                    taskCheckbox();
                    commonLoader();
                    common_bind("#" + modalId);
                    common_bind_select("#" + modalId);
                    $('#enable_subscriber').trigger('change');
                    $('#enable_flat').trigger('change');
                    $('#enable_domain').trigger('change');
                    $('#enable_header_img').trigger('change');
                    $('#enable_product_variant').trigger('change');
                    $('#enable_social_button').trigger('change');
                },
                error: function(data) {
                    data = data.responseJSON;
                }
            });
        });



        $(document).on('click', '.close-button', function() {
            $(this).parents("#quickview-modal").removeClass("active");
        });

        function taskCheckbox() {
            var checked = 0;
            var count = 0;
            var percentage = 0;

            count = $("#check-list input[type=checkbox]").length;
            checked = $("#check-list input[type=checkbox]:checked").length;
            percentage = parseInt(((checked / count) * 100), 10);
            if (isNaN(percentage)) {
                percentage = 0;
            }
            $(".custom-label").text(percentage + "%");
            $('#taskProgress').css('width', percentage + '%');


            $('#taskProgress').removeClass('bg-warning');
            $('#taskProgress').removeClass('bg-primary');
            $('#taskProgress').removeClass('bg-success');
            $('#taskProgress').removeClass('bg-danger');

            if (percentage <= 15) {
                $('#taskProgress').addClass('bg-danger');
            } else if (percentage > 15 && percentage <= 33) {
                $('#taskProgress').addClass('bg-warning');
            } else if (percentage > 33 && percentage <= 70) {
                $('#taskProgress').addClass('bg-primary');
            } else {
                $('#taskProgress').addClass('bg-success');
            }
        }

        $(function() {
            commonLoader();
            $(document).on("click", ".show_confirm", function() {
                var form = $(this).closest("form");
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "This action can not be undone. Do you want to continue?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });
        });

        function commonLoader() {



            if ($(".multi-select").length > 0) {
                $($(".multi-select")).each(function(index, element) {
                    var id = $(element).attr('id');
                    var multipleCancelButton = new Choices(
                        '#' + id, {
                            removeItemButton: true,
                        }
                    );
                });

            }

            if ($(".pc-dt-simple").length) {
                const dataTable = new simpleDatatables.DataTable(".pc-dt-simple");
            }


            // if ($(".pc-tinymce-2").length) {

            //     tinymce.init({
            //         selector: '.pc-tinymce-2',
            //         height: "400",
            //         content_style: 'body { font-family: "Inter", sans-serif; }'
            //     });
            // }


            if ($(".d_week").length) {

                $(".d_week").each(function(index) {
                    (function() {
                        const d_week = new Datepicker(document.querySelector('.d_week'), {
                            buttonClass: 'btn',
                            format: 'yyyy-mm-dd',
                        });
                    })();
                });
            }

            if ($(".pc-timepicker-2").length) {
                document.querySelector(".pc-timepicker-2").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                });
            }

            if ($("#data_picker1").length) {

                $("#data_picker1").each(function(index) {
                    (function() {
                        const d_week = new Datepicker(document.querySelector('#data_picker1'), {
                            buttonClass: 'btn',
                            format: 'yyyy-mm-dd',
                        });
                    })();
                });
            }


            if ($("#data_picker2").length) {
                $("#data_picker2").each(function(index) {
                    (function() {
                        const d_week = new Datepicker(document.querySelector('#data_picker2'), {
                            buttonClass: 'btn',
                            format: 'yyyy-mm-dd',
                        });
                    })();
                });
            }
            if ($("#pc-daterangepicker-2").length) {

                document.querySelector("#pc-daterangepicker-2").flatpickr({
                    mode: "range",

                });
            }


            if ($(".jscolor").length) {
                jscolor.installByClassName("jscolor");
            }

            // for Choose file
            $(document).on('change', 'input[type=file]', function() {
                var fileclass = $(this).attr('data-filename');
                var finalname = $(this).val().split('\\').pop();
                $('.' + fileclass).html(finalname);
            });
        }

        function common_bind(selector = "body") {
            var $datepicker = $(selector + ' .datepicker');
            if ($(".datepicker").length) {
                $('.datepicker').daterangepicker({
                    singleDatePicker: true,
                    format: 'yyyy-mm-dd',
                    locale: date_picker_locale,
                });

            }
            if ($(".custom-datepicker").length) {
                $('.custom-datepicker').daterangepicker({
                    singleDatePicker: true,
                    format: 'Y-MM',
                    locale: {
                        format: 'Y-MM'
                    }
                });
            }

            if ($(".summernote").length) {
                $('.summernote').summernote({
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                        ['list', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'unlink']],
                    ],
                    height: 250,
                });
                // $('.summernote-simple').summernote({
                //     dialogsInBody: !0,
                //     minHeight: 200,
                //     toolbar: [
                //         ['style', ['style']],
                //         ["font", ["bold", "italic", "underline", "clear", "strikethrough"]],
                //         ['fontname', ['fontname']],
                //         ['color', ['color']],
                //         ["para", ["ul", "ol", "paragraph"]],
                //     ]
                // });
            }
        }

        function common_bind_select(selector = "body") {
            if (jQuery().selectric) {
                $(".selectric").selectric({
                    disableOnMobile: false,
                    nativeOnMobile: false
                });
            }
            if ($(".jscolor").length) {
                jscolor.installByClassName("jscolor");
            }

            var Select = function() {
                var e = $('[data-toggle="select"]');
                e.length && e.each(function() {
                    $(this).select({
                        minimumResultsForSearch: -1
                    })
                })
            }()
        }
    </script>


    {{-- search --}}
    <script>
        $('.search-drp-btn').on('click', function(e) {
            e.preventDefault();
            setTimeout(function() {
                $(".header-search form").toggleClass("active");
                $('.overlay').addClass('menu-overlay menu-search');
            }, 50);
        });

        $(document).ready(function() {
            $("#search").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".product_tableese .product_item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        $('#search').keydown(function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });

        $(document).ready(function() {
            $("#btn-search").on("click", function() {
                var value = $(this).val().toLowerCase();
                $(".product_tableese .product_item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
    <script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <script>
        // show toaster
        function show_toastr(title, message, type) {

            var o, i;
            var icon = '';
            var cls = '';
            if (type == 'success') {
                icon = 'fas fa-check-circle';
                // cls = 'success';
                cls = 'success';
            } else {
                icon = 'fas fa-times-circle';
                cls = 'danger';
            }
            $.notify({
                icon: icon,
                title: " " + title,
                message: message,
                url: ""
            }, {
                element: "body",
                type: cls,
                allow_dismiss: !0,
                placement: {
                    from: 'top',
                    align: 'right'
                },
                offset: {
                    x: 15,
                    y: 15
                },
                spacing: 10,
                z_index: 1080,
                delay: 2500,
                timer: 6000,
                url_target: "_blank",
                mouse_over: !1,
                animate: {
                    enter: o,
                    exit: i
                },

                template: '<div class = "toast bg-' + cls + ' fade show animated fadeInDown"' +
                    'role = "alert"' +
                    'aria-live = "assertive"' +
                    'aria-atomic = "true"' +
                    'data-notify - position = "top-right"' +
                    'style ="display: inline-block; margin: 0px auto; position: fixed; transition: all 0.5s ease-in-out 0s; z-index: 1080; top: 15px; animation-iteration-count: 1;">' +
                    '<div class = "d-flex"> <div class = "toast-body"> ' + message +
                    '</div><button type="button" class="btn-close btn-close-white me-2 m-auto"  style="padding-top: 15px;" aria-hidden="true" data-dismiss="modal" data-notify="dismiss" data-bs-dismiss="toast" aria-label="Close"></button>' +
                    '</div></div>'
            });
        }

        // location & shipping
        $(document).ready(function() {
            $('.change_location').trigger('change');

            setTimeout(function() {
                var shipping_id = $("input[name='shipping_id']:checked").val();
                getTotal(shipping_id);
            }, 200);
        });

        $(document).on('change', '.shipping_mode', function() {
            var shipping_id = this.value;
            getTotal(shipping_id);
        });

        $(document).on('change', '.change_location', function() {
            var location_id = $('.change_location').val();

            if (location_id == 0) {
                $('#location_hide').hide();

            } else {
                $('#location_hide').show();

            }

            $.ajax({
                url: '{{ route('user.location', [$store->slug, '_location_id']) }}'.replace(
                    '_location_id',
                    location_id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                method: 'POST',
                context: this,
                dataType: 'json',

                success: function(data) {
                    var html = '';
                    var shipping_id =
                        '{{ isset($cust_details['shipping_id']) ? $cust_details['shipping_id'] : '' }}';
                    $.each(data.shipping, function(key, value) {
                        var checked = '';
                        if (shipping_id != '' && shipping_id == value.id) {
                            checked = 'checked';
                        }
                        html +=
                            '<div class = "radio-group" >' +
                            '<input type = "radio" name="shipping_id"' +
                            'data-id="' + value.price + '" value="' + value.id + '"' +
                            '" id="shipping_price' + key + '" class="shipping_mode"' + checked +
                            '>' +
                            ' <label name="shipping_label" for="shipping_price' + key +
                            '" class="shipping_label"> ' + value.name +
                            '</label></div>';

                    });
                    $('#shipping_location_content').html(html);
                }
            });
        });
        // total price
        function getTotal(shipping_id) {
            // var pro_total_price = $('.pro_total_price').attr('data-original');
            var sub_total_price = $('.sub_total_price').attr('data-value');

            var coupon = $('.coupon').val();

            if (shipping_id == undefined) {
                $('.shipping_price_add').hide();
                return false
            } else {
                $('.shipping_price_add').show();
            }

            $.ajax({
                url: '{{ route('user.shipping', [$store->slug, '_shipping']) }}'.replace('_shipping',
                    shipping_id),
                data: {
                    "pro_total_price": sub_total_price,
                    "coupon": coupon,
                    "_token": "{{ csrf_token() }}",
                },
                method: 'POST',
                context: this,
                dataType: 'json',

                success: function(data) {
                    // var price = data.price + sub_total_price;
                    $('.shipping_price').html(data.price);
                    $('.pro_total_price').html(data.total_price);
                    $('.pro_total_price').attr('data-original', data.total_price);
                }
            });
        }

        //  apply-coupon
        $(document).on('click', '.apply-coupon', function(e) {
            e.preventDefault();
            var ele = $(this);
            // var coupon = ele.closest('.row').find('.coupon').val();
            var coupon = $('#stripe_coupon').val();
            var hidden_field = $('.hidden_coupon').val();
            var price = $('#card-summary .product_total').val();
            var shipping_price = $('.shipping_price').html();
            /* if(coupon == ""){
                    show_toastr('Error', 'Please apply coupon code.', 'error');
                }*/
            if (coupon == hidden_field && coupon != "") {
                show_toastr('Error', 'Coupon Already Used', 'error');
            } else {
                if (coupon != '') {
                    $.ajax({
                        url: '{{ route('apply.productcoupon') }}',
                        datType: 'json',
                        data: {
                            price: price,
                            shipping_price: shipping_price,
                            store_id: {{ $store->id }},
                            coupon: coupon
                        },
                        success: function(data) {
                            $('#stripe_coupon, #paypal_coupon').val(coupon);
                            if (data.is_success) {
                                $('.hidden_coupon').val(coupon);
                                $('.hidden_coupon').attr(data);

                                $('.dicount_price').html(data.discount_price);
                                $('.pro_total_price').attr('data-original', data.final_price);
                                var html = '';
                                // html += '<span data-value="' + data.final_price + '">' + data.final_price + '</span>'
                                //html += '<span data-value="' + data.final_price + '">' + data.final_price + '</span>'
                                html += data.final_price;
                                $('.final_total_price').html(html);

                                // $('.coupon-tr').show().find('.coupon-price').text(data.discount_price);
                                // $('.final-price').text(data.final_price);
                                show_toastr('Success', data.message, 'success');
                            } else {
                                // $('.coupon-tr').hide().find('.coupon-price').text('');
                                $('.final-price').text(data.final_price);

                                show_toastr('Error', data.message, 'error');
                            }
                        }
                    })
                } else {
                    var hidd_cou = $('.hidd_val').val();
                    if (hidd_cou == "") {
                        var total_pa_val = $(".total_pay_price").val();
                        $(".final_total_price").html(total_pa_val);
                        $(".dicount_price").html(0.00);
                    }
                    show_toastr('Error', '{{ __('Invalid Coupon Code.') }}', 'error');
                }
            }

        });

        //product view, filter , add to cart
        $(document).ready(function() {


            var url = window.location.href;
            var n = url.split("/");
            if (n[n.length - 1] == 'login') {
                $("#login-BTN").trigger('click');
            }


            var type = 'hightolow';
            // when change sorting order
            $('#product_sort').on('change', function() {
                const type = $(this).val(); // Get the selected value
                if (type) {
                    ajaxFilterProjectView(type); // Call your function with the selected value
                }
            });

            $('#myproducts').on('click', function() {
                type = $(this).attr('data-val');
                ajaxFilterProjectView(type);
            });


            ajaxFilterProjectView(type);

            function ajaxFilterProjectView(type) {
                var mainEle = $('#product_view');
                var view = '{{ $view }}';
                var slug = '{{ $store->slug }}';

                $.ajax({
                    url: '{{ route('filter.product.view') }}',
                    type: 'POST',
                    data: {
                        view: view,
                        types: type,
                        slug: slug,
                    },
                    dataType: 'JSON',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        mainEle.html(data.html);
                        var data_id = $('.pro_category').find('.custom-list-group-item.active').attr(
                            'data-href');
                        $('#product_view .collection-items').addClass('0All');
                        $('#product_view .collection-items.' + data_id).removeClass('0All');
                        $('#sort_by .' + view).addClass('active');
                    }
                });

            }
        });

        $(document).on('click', '.custom-list-group-item', function() {
            var dataHref = $(this).attr('data-href');
            $('#product_view .collection-items').removeClass('active');
            $('#product_view .collection-items').addClass('d-none');
            $('.' + dataHref).addClass('active');
            $('.' + dataHref).removeClass('d-none');
            $('div').removeClass('nav-open');
        });

        $(document).on('click', '.add_to_cart', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                type: "POST",
                url: '{{ route('user.addToCart', ['__product_id', $store->slug]) }}'.replace(
                    '__product_id', id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    if (response.status == "Success") {
                        $("#shoping_count").attr("value", response.item_count);

                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Adjust the delay time as needed (2000ms = 2 seconds)
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function(result) {
                    // Handle error if needed
                }
            });
        });


        $(document).on('click', '.add_to_cart_variant', function(e) {
            // e.preventDefault();
            var id = $(this).attr('data-id');
            var variants = [];
            $(".variant-selection").each(function(index, element) {
                variants.push(element.value);
            });

            if (jQuery.inArray('0', variants) != -1) {
                show_toastr('Error', "{{ __('Please select all option.') }}", 'error');
                return false;
            }

            var variation_ids = $('#variant_id').val();

            $.ajax({
                url: '{{ route('user.addToCart', ['__product_id', $store->slug, 'variation_id']) }}'
                    .replace('__product_id', id).replace('variation_id', variation_ids ?? 0),
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    variants: variants.join(' : '),
                },
                success: function(response) {
                    if (response.status == "Success") {
                        $("#shoping_count").attr("value", response.item_count);
                        setTimeout(() => {

                            location.reload();
                        }, 2000);
                    } else {
                        show_toastr('Error', response.error, 'error');

                    }
                },
                error: function(result) {}
            });
        })

        $(document).on('change', '.variant-selection', function() {
            var variants = [];
            $(".variant-selection").each(function(index, element) {
                if (element.value != '' && element.value != undefined) {
                    var el_val = element.value;
                    variants.push(el_val);
                }
            });
            if (variants.length > 0) {

                $.ajax({
                    url: '{{ route('get.products.variant.quantity') }}',
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        variants: variants.join(' : '),
                        product_id: $('#product_id').val()
                    },

                    success: function(data) {
                        if (data.variant_id == 0) {
                            $('.variant_stock1').addClass('d-none');
                            $('.variation_price1').html('Please Select Variants');
                            if (store.theme_dir === 'theme1' || store.theme_dir === 'theme2' || store
                                .theme_dir === 'theme4') {
                                $(".variation_price1").css({
                                    "font": "var(--h5)",
                                    "color": "var(--black)"
                                });
                            } else if (store.theme_dir === 'theme3') {
                                $(".variation_price1").css({
                                    "font": "var(--h5)",
                                    "color": "var(--white)"
                                });
                            }
                            $('.variant_qty').html('0');
                            $('.store_item').removeAttr('data-title data-ajax-popup');
                        } else {
                            $('.variation_price1').html(data.price);
                            $('#variant_id').val(data.variant_id);
                            $('.variant_qty').html(data.quantity);
                            $('.variant_stock1').removeClass('d-none');
                            if (data.quantity != 0) {

                                $('.variant_stock1').html('In Stock');
                                $('.store_item').attr({
                                    'data-title': 'item',
                                    'data-ajax-popup': 'true'
                                });
                            } else {
                                $('.variant_stock1').html('Out Of Stock');
                                $('.store_item').removeAttr('data-title data-ajax-popup');
                            }
                        }
                    }
                });
            }
        });

        // product & variant product card-summary (increase-decrease)
        $(".productTab").click(function(e) {
            $('.side-menu-wrapper ').removeClass('active-menu');
            $('.overlay ').removeClass('menu-overlay');
            $('body').removeClass('no-scroll active-menu');
        });

        $(".product_qty_input").on('blur', function(e) {
            e.preventDefault();
            var product_id = $(this).attr('data-id');
            var arrkey = $(this).parents('div').attr('data-id');
            var quantity = $(this).closest('div').find('input[name="quantity"]').val();
            var sum = 0;
            var subtax = 0;
            var total = 0;

            setTimeout(function() {
                if (quantity == 0 || quantity == '' || quantity < 0) {
                    location.reload();
                    return false;
                }

                $.ajax({
                    url: '{{ route('user-product_qty.product_qty', ['__product_id', $store->slug, 'arrkeys']) }}'
                        .replace('__product_id', product_id).replace('arrkeys', arrkey),
                    type: "post",
                    headers: {
                        'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "product_qty": quantity,
                    },
                    success: function(response) {
                        if (response.status == "Success") {
                            var taxsss = [];

                            if ('carttotal' in response) {
                                // var data = data['items'];
                                $.each(response.product, function(key, value) {
                                    total += value.total;
                                    if (value.variant_id != 0) {
                                        sum += value.variant_subtotal;

                                        if (value.tax.length > 0) {
                                            $.each(value.tax, function(key, tv) {
                                                var t_name = tv.tax_name;
                                                var subtax = value
                                                    .variant_price * value
                                                    .quantity * tv.tax / 100;

                                                if (taxsss[key] != undefined) {
                                                    taxsss[key] = taxsss[key] +
                                                        subtax;
                                                } else {
                                                    taxsss[key] = subtax;
                                                }
                                                $('.total_tax_' + key).text(
                                                    addCommas(
                                                        taxsss[key]));


                                                $('#product-variant-id-' + value
                                                    .variant_id +
                                                    ' .variant_tax_' +
                                                    key
                                                ).text(tv.tax_name + ' ' +
                                                    tv.tax +
                                                    '% ' + '(' + subtax +
                                                    ')');
                                            });
                                        } else {
                                            $('#product-variant-id-' + value
                                                .variant_id +
                                                ' .variant_tax').text('-');
                                        }
                                        // $('#product-variant-id-' + value.variant_id +'.sub_total_price').text(addCommas(total));
                                        $('#product-variant-id-' + value.variant_id +
                                                '.subtotal')
                                            .text(addCommas(value.total));

                                    } else {
                                        sum += value.subtotal;

                                        if (value.tax.length > 0) {
                                            $.each(value.tax, function(key, tv) {
                                                var t_name = tv.tax_name;

                                                var subtax = value.price * value
                                                    .quantity *
                                                    tv.tax / 100;

                                                if (taxsss[key] != undefined) {
                                                    taxsss[key] = taxsss[key] +
                                                        subtax;
                                                } else {
                                                    taxsss[key] = subtax;
                                                }
                                                $('.total_tax_' + key).text(
                                                    addCommas(
                                                        taxsss[key]));

                                                $('#product-id-' + value.id +
                                                    ' .tax_' +
                                                    key).text(tv.tax_name +
                                                    ' ' + tv
                                                    .tax + '% ' + '(' +
                                                    subtax + ')'
                                                );
                                            });

                                        } else {

                                            $('#product-id-' + value.product_id +
                                                ' .tax').text('-');
                                        }
                                        // $('#product-id-' + value.id +' .sub_total_price').text(addCommas(total));

                                        $('#product-id-' + value.product_id +
                                            '.subtotal').text(
                                            addCommas(value.total));

                                    }
                                });
                                $('#displaytotal').text(addCommas(sum));
                                $('.sub_total_price').text(addCommas(total));
                            }

                        } else {
                            show_toastr('Error', response.error, 'error');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);

                            // location.reload(); // then reload the page.(3)
                        }
                    },
                    error: function(result) {}
                });
            }, 500);
        });

        var site_currency_symbol_position = '{{ $store['currency_symbol_position'] }}';
        var site_currency_symbol = '{{ $store['currency_code'] }}';

        function addCommas(num) {
            var number = parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
            return ((site_currency_symbol_position == "pre") ? site_currency_symbol : '') + number + ((
                site_currency_symbol_position == "post") ? site_currency_symbol : '');
        }

        $(".product_qty").on('click', function(e) {
            val = $('#product_qty_input').val();
            e.preventDefault();

            var product_id = $(this).attr('data-id');
            var arrkey = $(this).parents('div').attr('data-id');
            // var qty_id = $(this).val();
            var sum = 0;
            var subtax = 0;
            var total = 0;
            var quantity = $(this).closest('div').find('input[name="quantity"]').val();

            if ($(this).attr('data-option') == 'decrease') {
                qty_id = parseInt(quantity) - parseInt(1);
            } else {
                qty_id = parseInt(quantity) + parseInt(1);
            }

            setTimeout(function() {
                if (qty_id != 0 || qty_id == '' || qty_id < 0) {
                    // location.reload();
                    return false;
                }
            });

            if (qty_id != 0) {

                $.ajax({
                    url: '{{ route('user-product_qty.product_qty', ['__product_id', $store->slug, 'arrkeys']) }}'
                        .replace('__product_id', product_id).replace('arrkeys', arrkey),
                    type: "post",
                    headers: {
                        'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "product_qty": qty_id,
                    },
                    success: function(response) {
                        if (response.status == "Success") {
                            var taxsss = [];

                            if ('carttotal' in response) {
                                // var data = data['items'];
                                $.each(response.product, function(key, value) {
                                    total += value.total;
                                    if (value.variant_id != 0) {
                                        sum += value.variant_subtotal;

                                        if (value.tax.length > 0) {
                                            $.each(value.tax, function(key, tv) {
                                                var t_name = tv.tax_name;
                                                var subtax = value.variant_price * value
                                                    .quantity * tv.tax / 100;

                                                if (taxsss[key] != undefined) {
                                                    taxsss[key] = taxsss[key] + subtax;
                                                } else {
                                                    taxsss[key] = subtax;
                                                }
                                                $('.total_tax_' + key).text(addCommas(
                                                    taxsss[key]));


                                                $('#product-variant-id-' + value
                                                    .variant_id + ' .variant_tax_' +
                                                    key
                                                ).text(tv.tax_name + ' ' + tv.tax +
                                                    '% ' + '(' + subtax + ')');
                                            });
                                        } else {
                                            $('#product-variant-id-' + value.variant_id +
                                                ' .variant_tax').text('-');
                                        }
                                        // $('#product-variant-id-' + value.variant_id +'.sub_total_price').text(addCommas(total));
                                        $('#product-variant-id-' + value.variant_id +
                                                '.subtotal')
                                            .text(addCommas(value.total));

                                    } else {
                                        sum += value.subtotal;

                                        if (value.tax.length > 0) {
                                            $.each(value.tax, function(key, tv) {
                                                var t_name = tv.tax_name;

                                                var subtax = value.price * value
                                                    .quantity *
                                                    tv.tax / 100;
                                                if (taxsss[key] != undefined) {
                                                    taxsss[key] = taxsss[key] + subtax;
                                                } else {
                                                    taxsss[key] = subtax;
                                                }
                                                $('.total_tax_' + key).text(addCommas(
                                                    taxsss[key]));

                                                $('#product-id-' + value.id + ' .tax_' +
                                                    key).text(tv.tax_name + ' ' + tv
                                                    .tax + '% ' + '(' + subtax + ')'
                                                );
                                            });

                                        } else {
                                            $('#product-id-' + value.product_id + ' .tax').text(
                                                '-');
                                        }
                                        // $('#product-id-' + value.id +'.sub_total_price').text(addCommas(total));
                                        $('#product-id-' + value.product_id + '.subtotal').text(
                                            addCommas(value.total));

                                    }
                                });
                                $('#displaytotal').text(addCommas(sum));
                                $('.sub_total_price').text(addCommas(total));
                            }

                        } else {
                            show_toastr('Error', response.error, 'error');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);

                            // location.reload(); // then reload the page.(3)
                        }
                    },
                    error: function(result) {}
                });
            };
        });

        var quantity = 0;
        $('.quantity-increment').click(function() {
            var t = $(this).siblings('.quantity');
            var quantity = parseInt($(t).val());
            $(t).val(quantity + 1);
        });
        $('.quantity-decrement').click(function() {
            var t = $(this).siblings('.quantity');
            var quantity = parseInt($(t).val());
            if (quantity > 1) {
                $(t).val(quantity - 1);
            }
        });
    </script>


    {{-- payment script --}}
    <script>
        $(document).on('click', '[id^="owner-"]', function(event) {
            event.preventDefault();

            // Get payment type and form ID
            var paymentType = $(this).attr('id').replace('owner-', '');
            var formId = 'payment-' + paymentType + '-form';

            // Collect order details
            var order_id = '{{ !empty($order->id) ? $order->id + 1 : 1 }}';
            var total_price = $('.final_total_price').text().replace("{{ $store->currency_code }}", "").trim();
            var dicount_price = $('.dicount_price').text().trim();
            var shipping_price = $('.shipping_price').text().trim();
            var shipping_name = $('.change_location').find(":selected").text().trim();
            var shipping_id = $("input[name='shipping_id']:checked").val() || '';

            // Collect customer details
            var name = $('.detail-form .fname').val();
            var email = $('.detail-form .email').val();
            var phone = $('.detail-form .phone').val();

            // Collect custom field titles
            var custom_field_titles = [];
            for (var i = 1; i <= 4; i++) {
                custom_field_titles.push($('.detail-form .custom_field_title_' + i).val());
            }

            var billing_address = $('.detail-form .billing_address').val();
            var shipping_address = $('.detail-form .shipping_address').val();
            var special_instruct = $('.special_instruct').val();

            // Retrieve coupon_id from the hidden input field (similar to your previous code)
            var coupon_id = $('.hidden_coupon').attr('data_id') || ''; // Adjust as per the actual element

            // Populate hidden input fields
            $(".customer_type").val(paymentType);
            $(".customer_coupon_id").val(coupon_id); // Set the coupon_id here
            $(".customer_dicount_price").val(dicount_price);
            $(".customer_shipping_price").val(shipping_price);
            $(".customer_shipping_name").val(shipping_name);
            $(".customer_shipping_id").val(shipping_id);
            $(".customer_total_price").val(total_price);
            $(".customer_order_id").val(order_id);
            $(".customer_name").val(name);
            $(".customer_email").val(email);
            $(".customer_phone").val(phone);

            for (var j = 1; j <= custom_field_titles.length; j++) {
                $('.customer_custom_field_title_' + j).val(custom_field_titles[j - 1]);
            }

            $(".customer_billing_address").val(billing_address);
            $(".customer_shipping_address").val(shipping_address);
            $(".customer_special_instruct").val(special_instruct);

            // Additional condition for paimentpro
            if (paymentType === 'paiment_pro') {
                var paimentpro_mobile_number = $('.paimentpro_mobile_number').val().trim();
                var paimentpro_channel = $('.paimentpro_channel').val().trim();

                if (paimentpro_mobile_number.length > 0 && paimentpro_channel.length > 0) {
                    document.getElementById(formId).submit();
                } else {
                    show_toastr("Error", 'Mobile number and Channel field is required.', 'error');
                }
            } else {
                // Submit the form for other payment types
                document.getElementById(formId).submit();
            }
        });
    </script>

    <script>
        //whatsapp
        $(document).on('click', '#order-whatsapp', function() {
            var product_array = '{{ $encode_product }}';
            var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
            var order_id = '{{ $order_id = '#' . time() }}';

            // var total_price = $('#Subtotal .total_price').attr('data-value');
            var total_price = $('.final_total_price').attr('data-original');
            var coupon_id = $('.hidden_coupon').attr('data_id');
            var dicount_price = $('.dicount_price').html();
            var shipping_price = $('.shipping_price').html();
            var shipping_name = $('.change_location').find(":selected").text();
            var shipping_id = $("input[name='shipping_id']:checked").val();

            var name = $('.detail-form .fname').val();
            var email = $('.detail-form .email').val();
            var phone = $('.detail-form .phone').val();

            var custom_field_title_1 = $('.detail-form .custom_field_title_1').val();
            var custom_field_title_2 = $('.detail-form .custom_field_title_2').val();
            var custom_field_title_3 = $('.detail-form .custom_field_title_3').val();
            var custom_field_title_4 = $('.detail-form .custom_field_title_4').val();

            var billing_address = $('.detail-form .billing_address').val();
            var shipping_address = $('.detail-form .shipping_address').val();
            var special_instruct = $('.special_instruct').val();


            var ajaxData = {
                coupon_id: coupon_id,
                dicount_price: dicount_price,
                shipping_price: shipping_price,
                shipping_name: shipping_name,
                shipping_id: shipping_id,
                total_price: total_price,
                product: product,
                order_id: order_id,
                name: name,
                email: email,
                phone: phone,
                custom_field_title_1: custom_field_title_1,
                custom_field_title_2: custom_field_title_2,
                custom_field_title_3: custom_field_title_3,
                custom_field_title_4: custom_field_title_4,
                billing_address: billing_address,
                shipping_address: shipping_address,
                special_instruct: special_instruct,

                wts_number: $('#wts_number').val()
            }

            getWhatsappUrl(ajaxData);

            var submitAjax = null;

            submitAjax = $.ajax({
                url: '{{ route('user.whatsapp', $store->slug) }}',
                method: 'POST',
                data: ajaxData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    if (submitAjax != null) {
                        submitAjax.abort();
                    }
                },
                success: function(data) {
                    if (data.status == 'success') {

                        removesession();

                        show_toastr(data.status, data.success, data.status);

                        setTimeout(function() {
                            var url =
                                '{{ route('store-complete.complete', [$store->slug, ':id']) }}';
                            url = url.replace(':id', data.order_id);

                            window.location.href = url;
                        }, 1000);

                        setTimeout(function() {
                            var get_url_msg_url = $('#return_url').val();
                            var append_href = get_url_msg_url + '  ' +
                                '{{ route('user.order', [$store->slug, Crypt::encrypt(!empty($order->id) ? $order->id + 1 : 0 + 1)]) }}';
                            @php
                                $browser = new \Detection\MobileDetect();
                            @endphp
                            @if ($browser->isMobile() || $browser->isTablet())
                                window.location.href = append_href; // Redirect for mobile
                            @else
                                window.open(append_href,
                                    '_blank'); // Open in new tab for desktop
                            @endif
                        }, 20);

                    } else {
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        });

        //telegram
        $(document).on('click', '#order-telegram', function() {
            var product_array = '{{ $encode_product }}';
            var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
            var order_id = '{{ $order_id = !empty($order->id) ? $order->id + 1 : 0 + 1 }}';

            // var total_price = $('#Subtotal .total_price').attr('data-value');
            var total_price = $('.final_total_price').attr('data-original');
            var coupon_id = $('.hidden_coupon').attr('data_id');
            var dicount_price = $('.dicount_price').html();
            var shipping_price = $('.shipping_price').html();
            var shipping_name = $('.change_location').find(":selected").text();
            var shipping_id = $("input[name='shipping_id']:checked").val();

            var name = $('.detail-form .fname').val();
            var email = $('.detail-form .email').val();
            var phone = $('.detail-form .phone').val();

            var custom_field_title_1 = $('.detail-form .custom_field_title_1').val();
            var custom_field_title_2 = $('.detail-form .custom_field_title_2').val();
            var custom_field_title_3 = $('.detail-form .custom_field_title_3').val();
            var custom_field_title_4 = $('.detail-form .custom_field_title_4').val();

            var billing_address = $('.detail-form .billing_address').val();
            var shipping_address = $('.detail-form .shipping_address').val();
            var special_instruct = $('.special_instruct').val();

            var submitAjaxtelegram = null;

            var ajaxData = {
                type: 'telegram',
                coupon_id: coupon_id,
                dicount_price: dicount_price,
                shipping_price: shipping_price,
                shipping_name: shipping_name,
                shipping_id: shipping_id,
                total_price: total_price,
                product: product,
                order_id: order_id,
                name: name,
                email: email,
                phone: phone,
                custom_field_title_1: custom_field_title_1,
                custom_field_title_2: custom_field_title_2,
                custom_field_title_3: custom_field_title_3,
                custom_field_title_4: custom_field_title_4,
                billing_address: billing_address,
                shipping_address: shipping_address,
                special_instruct: special_instruct,

                wts_number: $('#wts_number').val()
            }

            getWhatsappUrl(ajaxData);

            var submitAjaxtelegram = null;

            submitAjaxtelegram = $.ajax({
                url: '{{ route('user.telegram', $store->slug) }}',
                method: 'POST',
                data: ajaxData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    if (submitAjaxtelegram != null) {
                        submitAjaxtelegram.abort();
                    }
                },
                success: function(data) {
                    if (data.status == 'success') {

                        show_toastr('success', data.success, data.status);
                        // show_toastr(data["success"], '{!! session('+data["status"]+') !!}', data["status"]);

                        setTimeout(function() {

                            removesession();

                            var url =
                                '{{ route('store-complete.complete', [$store->slug, ':id']) }}';
                            url = url.replace(':id', data.order_id);

                            window.location.href = url;
                        }, 1000);

                    } else {
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        });

        //cod
        $(document).on('click', '#cash_on_delivery', function() {
            var product_array = '{{ $encode_product }}';
            var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
            var order_id = '{{ $order_id = !empty($order->id) ? $order->id + 1 : 0 + 1 }}';

            // var total_price = $('#Subtotal .total_price').attr('data-value');
            var total_price = $('.final_total_price').attr('data-original');
            var coupon_id = $('.hidden_coupon').attr('data_id');
            var dicount_price = $('.dicount_price').html();
            var shipping_price = $('.shipping_price').html();
            var shipping_name = $('.change_location').find(":selected").text();
            var shipping_id = $("input[name='shipping_id']:checked").val();

            var name = $('.detail-form .fname').val();
            var email = $('.detail-form .email').val();
            var phone = $('.detail-form .phone').val();

            var custom_field_title_1 = $('.detail-form .custom_field_title_1').val();
            var custom_field_title_2 = $('.detail-form .custom_field_title_2').val();
            var custom_field_title_3 = $('.detail-form .custom_field_title_3').val();
            var custom_field_title_4 = $('.detail-form .custom_field_title_4').val();

            var billing_address = $('.detail-form .billing_address').val();
            var shipping_address = $('.detail-form .shipping_address').val();
            var special_instruct = $('.special_instruct').val();

            var submitAjaxtelegram = null;

            var ajaxData = {
                type: 'COD',
                coupon_id: coupon_id,
                dicount_price: dicount_price,
                shipping_price: shipping_price,
                shipping_name: shipping_name,
                shipping_id: shipping_id,
                total_price: total_price,
                product: product,
                order_id: order_id,
                name: name,
                email: email,
                phone: phone,
                custom_field_title_1: custom_field_title_1,
                custom_field_title_2: custom_field_title_2,
                custom_field_title_3: custom_field_title_3,
                custom_field_title_4: custom_field_title_4,
                billing_address: billing_address,
                shipping_address: shipping_address,
                special_instruct: special_instruct,
            }

            $.ajax({
                url: '{{ route('user.cod', $store->slug) }}',
                method: 'POST',
                data: ajaxData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.status == 'success') {
                        // show_toastr(data["success"], '{!! session('+data["status"]+') !!}', data["status"]);
                        show_toastr(data["status"], data["success"], data["status"]);

                        setTimeout(function() {
                            var url =
                                '{{ route('store-complete.complete', [$store->slug, ':id']) }}';
                            url = url.replace(':id', data.order_id);
                            window.location.href = url;
                        }, 1000);
                        removesession();
                    } else {
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        });

        //bank transfer
        $(document).on('click', '#bank_transfer', function() {
            var product_array = '{!! $encode_product !!}';
            var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
            var order_id = '{{ $order_id = !empty($order->id) ? $order->id + 1 : 0 + 1 }}';
            var total_price = $('.final_total_price').attr('data-original');
            var coupon_id = $('.hidden_coupon').attr('data_id');
            var dicount_price = $('.dicount_price').html();
            var shipping_price = $('.shipping_price').html();
            var shipping_name = $('.change_location').find(":selected").text();
            var shipping_id = $("input[name='shipping_id']:checked").val();
            var name = $('.detail-form .fname').val();
            var email = $('.detail-form .email').val();
            var phone = $('.detail-form .phone').val();

            var custom_field_title_1 = $('.detail-form .custom_field_title_1').val();
            var custom_field_title_2 = $('.detail-form .custom_field_title_2').val();
            var custom_field_title_3 = $('.detail-form .custom_field_title_3').val();
            var custom_field_title_4 = $('.detail-form .custom_field_title_4').val();

            var billing_address = $('.detail-form .billing_address').val();
            var shipping_address = $('.detail-form .shipping_address').val();
            var special_instruct = $('.special_instruct').val();
            var files = $('#bank_transfer_invoice')[0].files;


            var formData = new FormData($("#bank_transfer_form")[0]);
            formData.append('product', product_array);
            formData.append('order_id', order_id);
            formData.append('total_price', total_price);
            if (coupon_id != undefined) {
                formData.append('coupon_id', coupon_id);
            }
            formData.append('dicount_price', dicount_price);
            formData.append('shipping_price', shipping_price);
            formData.append('shipping_name', shipping_name);
            if (shipping_id != undefined) {
                formData.append('shipping_id', shipping_id);
            }
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            if (custom_field_title_1 != undefined) {
                formData.append('custom_field_title_1', custom_field_title_1);
            }
            if (custom_field_title_2 != undefined) {
                formData.append('custom_field_title_2', custom_field_title_2);
            }
            if (custom_field_title_3 != undefined) {
                formData.append('custom_field_title_3', custom_field_title_3);
            }
            if (custom_field_title_4 != undefined) {
                formData.append('custom_field_title_4', custom_field_title_4);
            }
            if (billing_address != undefined) {
                formData.append('billing_address', billing_address);
            }
            if (shipping_address != undefined) {
                formData.append('shipping_address', shipping_address);
            }
            if (special_instruct != undefined) {
                formData.append('special_instruct', special_instruct);
            }

            formData.append('files', files);

            $.ajax({
                url: '{{ route('user.bank_transfer', $store->slug) }}',
                method: 'POST',
                // data: data,
                data: formData,
                contentType: false,
                // cache: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.status == 'success') {
                        removesession();

                        // show_toastr(data["success"], '{!! session('+data["status"]+') !!}', data["status"]);
                        show_toastr(data["status"], data["success"], data["status"]);
                        setTimeout(function() {
                            var url =
                                '{{ route('store-complete.complete', [$store->slug, ':id']) }}';
                            url = url.replace(':id', data.order_id);
                            window.location.href = url;
                        }, 2500);
                    } else {

                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        });

        function getWhatsappUrl(ajaxData = '') {
            $.ajax({
                url: '{{ route('get.whatsappurl', $store->slug) }}',
                method: 'post',
                data: ajaxData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.status == 'success') {
                        $('#return_url').val(data.url);
                        $('#return_order_id').val(data.order_id);

                    } else {
                        $('#return_url').val('')
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        }
        //for create/get Telegram Url
        function getTelegramUrl(ajaxData = '') {
            $.ajax({
                url: '{{ route('get.whatsappurl', $store->slug) }}',
                method: 'post',
                data: ajaxData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.status == 'success') {
                        $('#return_url').val(data.url);
                        $('#return_order_id').val(data.order_id);

                    } else {
                        $('#return_url').val('')
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        }

        function removesession() {
            $.ajax({
                url: '{{ route('remove.session', $store->slug) }}',
                method: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {

                }
            });
        }
    </script>
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        @if (
            !empty($store_payments['is_stripe_enabled']) &&
                isset($store_payments['is_stripe_enabled']) &&
                $store_payments['is_stripe_enabled'] == 'on' &&
                !empty($store_payments['stripe_key']) &&
                !empty($store_payments['stripe_secret']))
            @php
                $stripe_session = Session::get('stripe_session');
            @endphp
            @if (isset($stripe_session) && $stripe_session)
                <
                script >
                    var stripe = Stripe('{{ $store_payments['stripe_key'] }}');
                stripe.redirectToCheckout({
                    sessionId: '{{ $stripe_session->id }}',
                }).then((result) => {});
    </script>
    @endif
    @endif
    </script>
    <script type="text/javascript"></script>
    <script>
        $('#guest').click(function() {
            $('#signIn').removeClass('active').addClass('inactive');
            $(this).removeClass('inactive').addClass('active').css("background-color", "silver");
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#card-summary").offset().top
            }, 1000);
        });
    </script>
    @if (Session::has('success'))
        <script>
            show_toastr('{{ __('Success') }}', '{!! session('success') !!}', 'success');
        </script>
        {{ Session::forget('success') }}
    @endif
    @if (Session::has('error'))
        <script>
            show_toastr('{{ __('Error') }}', '{!! session('error') !!}', 'error');
        </script>
        {{ Session::forget('error') }}
    @endif
    <script>
        $('.menu-toggle-btn').on('click', function(e) {
            e.preventDefault();
            setTimeout(function() {
                $('body').addClass('no-scroll active-menu');
                $(".side-menu-wrapper").toggleClass("active-menu");
                $('.overlay').addClass('menu-overlay');
            }, 50);
        });
        $('body').on('click', '.overlay.menu-overlay, .menu-close-icon', function(e) {
            e.preventDefault();
            $('body').removeClass('no-scroll active-menu');
            $(".side-menu-wrapper").removeClass("active-menu");
            $('.overlay').removeClass('menu-overlay');
        });
    </script>
    @stack('scripts')
</body>

</html>
