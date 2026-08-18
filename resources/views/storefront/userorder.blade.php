@php
    if (!empty(session()->get('lang'))) {
        $currantLang = session()->get('lang');
    } else {
        $currantLang = $store->lang;
    }
    $languages = \App\Models\Utility::languages();
    $s_logo = \App\Models\Utility::get_file('uploads/store_logo/');
    $setting = App\Models\Utility::colorset();
    $settings = Utility::settings();

    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $settings['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=  ">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{ env('APP_NAME') }} - Online Whatsapp Store Builder">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ \App\Models\Utility::getValByName('header_text') ? \App\Models\Utility::getValByName('header_text') : config('app.name', 'WhatsStore') }}
        @yield('page-title')</title>
    <link rel="icon" href="{{ asset(Storage::url('uploads/logo/')) . '/favicon.png' }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/datepicker-bs5.min.css') }}">
    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/libs/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">

    <!-- vendor css -->

    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/css/custom.css') }}">

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
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
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
    @stack('css-page')
    <style>
        :root {
            --color-customColor: <?=$color ?>;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
</head>

<body class="{{ $store->store_theme }}">
    <!-- [ Pre-loader ] start -->


    <!-- Modal -->

    <!-- [ Header ] end -->

    <body id="printableArea">
        <div class="dash-container downOrder">
            <div class="dash-content">
                <!-- [ breadcrumb ] start -->
                <div class="page-header order-page-sec p-0">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="d-block d-flex align-items-center justify-content-between">
                                    {{-- <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.$store->logo))}}" id="navbar-logo" style="height: 40px;"> --}}
                                    <div class="page-header-title">
                                        @if (!empty($store->logo))
                                            <img src="{{ $s_logo . (isset($store->logo) && !empty($store->logo) ? $store->logo : 'logo.png') . '?timestamp=' . time() }}"
                                                id="navbar-logo" style="height: 40px;">
                                        @else
                                            <img src="{{ $s_logo . (isset($store->logo) && !empty($store->logo) ? $store->logo : 'logo.png') . '?timestamp=' . time() }}"
                                                id="navbar-logo" style="height: 40px;">
                                        @endif
                                    </div>
                                    <div class="">
                                        <a href="#" onclick="saveAsPDF();"
                                            class="btn btn-sm btn-primary btn-icon btn-download"
                                            data-bs-toggle="tooltip" data-bs-original-title="{{ __('Download') }}">
                                            <i class="fa fa-print text-white"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="row"> -->
                <div class="mt-4">
                    <!-- <div id="">
                        <div class="row">
                            <div class=" col-6 pb-2 invoice_logo"></div>
                            <div class=" col-6 pb-2 delivered_Status text-end">

                            </div> -->
                    <div class="row">
                        <div class="col-lg-8 d-flex">
                            <div class="card w-100">
                                <div
                                    class="card-header border-0 d-flex justify-content-between align-items-center gap-2">
                                    <h6 class="mb-0">{{ __('Items from Order') }} {{ $order->order_id }}
                                    </h6>

                                    @if ($order->status == 'pending')
                                        <button class="btn btn-sm btn-success">{{ __('Pending') }}</button>
                                    @elseif($order->status == 'Cancel Order')
                                        <button class="btn btn-sm btn-danger">{{ __('Order Canceled') }}</button>
                                    @else
                                        <button class="btn btn-sm btn-success">{{ __('Delivered') }}</button>
                                    @endif
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>{{ __('Item') }}</th>
                                                    <th>{{ __('Quantity') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                    <th>{{ __('Total') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $sub_tax = 0;
                                                    $total = 0;
                                                @endphp
                                                @foreach ($order_products->products as $key => $product)
                                                    @if ($product->variant_id != 0)
                                                        <tr>
                                                            <td class="total">
                                                                <span class="h6 text-sm">
                                                                    {{ $product->product_name . ' - ( ' . $product->variant_name . ' )' }}
                                                                </span>
                                                                @if (!empty($product->tax))
                                                                    @php
                                                                        $total_tax = 0;
                                                                    @endphp
                                                                    @foreach ($product->tax as $tax)
                                                                        @php
                                                                            $sub_tax =
                                                                                ($product->variant_price *
                                                                                    $product->quantity *
                                                                                    $tax->tax) /
                                                                                100;
                                                                            $total_tax += $sub_tax;
                                                                        @endphp
                                                                        {{ $tax->tax_name . ' ' . $tax->tax . '%' . ' (' . $sub_tax . ')' }}
                                                                    @endforeach
                                                                @else
                                                                    @php
                                                                        $total_tax = 0;
                                                                    @endphp
                                                                @endif

                                                            </td>
                                                            <td>
                                                                {{ $product->quantity }}
                                                            </td>
                                                            <td>
                                                                {{ App\Models\Utility::priceFormat($product->variant_price) }}
                                                            </td>
                                                            <td>
                                                                {{ App\Models\Utility::priceFormat($product->variant_price * $product->quantity + $total_tax) }}
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td class="total">
                                                                <span class="h6 text-sm">
                                                                    {{ $product->product_name }}
                                                                </span>
                                                                @if (!empty($product->tax))
                                                                    @php
                                                                        $total_tax = 0;
                                                                    @endphp
                                                                    @foreach ($product->tax as $tax)
                                                                        @php
                                                                            $sub_tax =
                                                                                ($product->price *
                                                                                    $product->quantity *
                                                                                    $tax->tax) /
                                                                                100;
                                                                            $total_tax += $sub_tax;
                                                                        @endphp
                                                                        {{ $tax->tax_name . ' ' . $tax->tax . '%' . ' (' . $sub_tax . ')' }}
                                                                    @endforeach
                                                                @else
                                                                    @php
                                                                        $total_tax = 0;
                                                                    @endphp
                                                                @endif

                                                            </td>
                                                            <td>
                                                                {{ $product->quantity }}
                                                            </td>
                                                            <td>
                                                                {{ App\Models\Utility::priceFormat($product->price) }}
                                                            </td>
                                                            <td>
                                                                {{ App\Models\Utility::priceFormat($product->price * $product->quantity + $total_tax) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4"><b>
                                                            {{ __('Order Notes') }} :
                                                        </b>

                                                        @if (!empty($user_details->special_instruct))
                                                            <dd class="p-2 d-inline">
                                                                {{ $user_details->special_instruct }}</dd>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tfoot>

                                        </table>
                                    </div>
                                </div>

                                @if ($order->status == 'delivered')
                                    <div class="card card-body mb-0 py-0">
                                        <div class="card my-5 bg-secondary">
                                            <div class="card-body">
                                                <div class="row justify-content-between align-items-center">
                                                    <div class="col-md-6 order-md-2 mb-4 mb-md-0">
                                                        <div class="d-flex align-items-center justify-content-md-end">

                                                            <button data-id="{{ $order->id }}"
                                                                data-value="{{ asset(Storage::url('uploads/downloadable_prodcut' . '/' . $product->downloadable_prodcut)) }}"
                                                                class="btn btn-sm btn-icon btn-primary downloadable_prodcut">{{ __('Download') }}</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 order-md-1">
                                                        <span class="h6 text-muted d-inline-block mr-3 mb-0"></span>
                                                        <span
                                                            class="h5 mb-0">{{ __('Get your product from here') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 d-flex">
                            <div class="card w-100">
                                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ __('Items from Order') }} {{ $order->order_id }}
                                    </h6>
                                </div>
                                <div class="card-header">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead class="thead-light">

                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ __('Sub Total') }} :</td>
                                                    <td>{{ App\Models\Utility::priceFormat($sub_total) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('Estimated Tax') }} :</td>
                                                    <td>{{ App\Models\Utility::priceFormat($final_taxs) }}</td>
                                                </tr>
                                                @if (!empty($discount_price))
                                                    <tr>
                                                        <td>{{ __('Apply Coupon') }} :</td>
                                                        <td>{{ $discount_price }}</td>
                                                    </tr>
                                                @endif
                                                @if (!empty($shipping_data))
                                                    @if (!empty($discount_value))
                                                        <tr>
                                                            <td>{{ __('Shipping Price') }} :</td>
                                                            <td>{{ App\Models\Utility::priceFormat($shipping_data->shipping_price) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>{{ __('Grand Total') }} :</th>
                                                            <th>{{ App\Models\Utility::priceFormat($grand_total + $shipping_data->shipping_price - $discount_value) }}
                                                            </th>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td>{{ __('Shipping Price') }} :</td>
                                                            <td>{{ App\Models\Utility::priceFormat($shipping_data->shipping_price) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>{{ __('Grand Total') }} :</th>
                                                            <th>{{ App\Models\Utility::priceFormat($grand_total + $shipping_data->shipping_price) }}
                                                            </th>
                                                        </tr>
                                                    @endif
                                                @elseif(!empty($discount_value))
                                                    <tr>
                                                        <th>{{ __('Grand  Total') }} :</th>
                                                        <th>{{ App\Models\Utility::priceFormat($grand_total - $discount_value) }}
                                                        </th>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <th>{{ __('Grand  Total') }} :</th>
                                                        <th>{{ App\Models\Utility::priceFormat($grand_total) }}
                                                        </th>
                                                    </tr>
                                                @endif

                                                <th>{{ __('Payment Type') }} :</th>
                                                <th>{{ $order['payment_type'] }}</th>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-4 d-flex">
                            <div class="card card-fluid w-100">
                                <div class="card-header">
                                    <h5>{{ __('Billing Information') }}</h5>
                                </div>
                                <div class="card-body">

                                    <dl class="row align-items-center order-wrapper">
                                        <dt class="col-4 h6 text-sm">{{ __('Name') }}</dt>
                                        <dd class="col-8 text-sm"> {{ $user_details->name }}</dd>
                                        <dt class="col-4 h6 text-sm">{{ __('Phone') }}</dt>
                                        <dd class="col-8 text-sm">{{ $user_details->phone }}</dd>
                                        <dt class="col-4 h6 text-sm">{{ __('Billing Address') }}</dt>
                                        <dd class="col-8 text-sm">{{ $user_details->billing_address }}</dd>
                                        <dt class="col-4 h6 text-sm">{{ __('Shipping Address') }}</dt>
                                        <dd class="col-8 text-sm">{{ $user_details->shipping_address }}
                                        </dd>
                                        @if (!empty($location_data && $shipping_data))
                                            <dt class="col-4 h6 text-sm">{{ __('Location') }}</dt>
                                            <dd class="col-8 text-sm">{{ $location_data->name }}</dd>
                                            <dt class="col-4 h6 text-sm">{{ __('Shipping Method') }}</dt>
                                            <dd class="col-8 text-sm">{{ $shipping_data->shipping_name }}
                                            </dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex">
                            <div class="card card-fluid w-100">
                                <div class="card-header">
                                    <h5>{{ __('Shipping Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <address class="mb-0 text-sm">
                                        <dl class="row align-items-center order-wrapper">
                                            <dt class="col-4 h6 text-sm">{{ __('Name') }}</dt>
                                            <dd class="col-8 text-sm"> {{ $user_details->name }}</dd>
                                            <dt class="col-4 h6 text-sm">{{ __('Phone') }}</dt>
                                            <dd class="col-8 text-sm">{{ $user_details->phone }}</dd>
                                            <dt class="col-4 h6 text-sm">{{ __('Billing Address') }}</dt>
                                            <dd class="col-8 text-sm">{{ $user_details->billing_address }}
                                            </dd>
                                            <dt class="col-4 h6 text-sm">{{ __('Shipping Address') }}</dt>
                                            <dd class="col-8 text-sm">{{ $user_details->shipping_address }}
                                            </dd>
                                            @if (!empty($location_data && $shipping_data))
                                                <dt class="col-4 h6 text-sm">{{ __('Location') }}</dt>
                                                <dd class="col-8 text-sm">{{ $location_data->name }}</dd>
                                                <dt class="col-4 h6 text-sm">{{ __('Shipping Method') }}
                                                </dt>
                                                <dd class="col-8 text-sm">
                                                    {{ $shipping_data->shipping_name }}</dd>
                                            @endif
                                        </dl>
                                    </address>
                                </div>
                            </div>
                        </div>
                        @if (
                            !empty($store['custom_field_title_1']) ||
                                !empty($user_details->custom_field_title_1) ||
                                !empty($store['custom_field_title_2']) ||
                                !empty($user_details->custom_field_title_2) ||
                                !empty($store['custom_field_title_3']) ||
                                !empty($user_details->custom_field_title_3) ||
                                !empty($store['custom_field_title_4']) ||
                                !empty($user_details->custom_field_title_4))
                            <div class="col-lg-4 d-flex">
                                <div class="card card-fluid w-100">
                                    <div class="card-header">
                                        <h5>{{ __('Extra Information') }}</h5>
                                    </div>
                                    <div class="card-body">

                                        <dl class="row order-wrp align-items-center order-wrapper">
                                            <dt class="col-4 h6 text-sm">
                                                {{ $store['custom_field_title_1'] }}
                                            </dt>
                                            <dd class="col-8 text-sm">
                                                {{ $user_details->custom_field_title_1 }}</dd>
                                            <dt class="col-4 h6 text-sm">
                                                {{ $store['custom_field_title_2'] }}
                                            </dt>
                                            <dd class="col-8 text-sm">
                                                {{ $user_details->custom_field_title_2 }}</dd>
                                            <dt class="col-4 h6 text-sm">
                                                {{ $store['custom_field_title_3'] }}
                                            </dt>
                                            <dd class="col-8 text-sm">
                                                {{ $user_details->custom_field_title_3 }}</dd>
                                            <dt class="col-4 h6 text-sm">
                                                {{ $store['custom_field_title_4'] }}
                                            </dt>
                                            <dd class="col-8 text-sm">
                                                {{ $user_details->custom_field_title_4 }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- </div> -->
                    <!-- </div> -->
                </div>

                <!-- </div> -->

            </div>
        </div>



        <!-- [ Main Content ] start -->



        <!-- [ Main Content ] end -->

        <footer class="dash-footer" id="footer-main">
            <div class="footer-wrapper">
                <div class="col-md-6">
                    <div class="copyright text-sm font-weight-bold text-md-left">
                        <span class="text-muted">
                            &copy; {{ date('Y') }}
                            {{ App\Models\Utility::getValByName('footer_text') ? App\Models\Utility::getValByName('footer_text') : config('app.name', 'WhatsStore SaaS') }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <ul class="nav justify-content-center justify-content-md-end mt-3 mt-md-0">
                        @if (!empty($store->email))
                            <li class="nav-item">
                                <a class="nav-link" href="mailto:{{ $store->email }}" target="_blank">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </li>
                        @endif
                        @if (!empty($store->whatsapp))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $store->whatsapp }}" target=”_blank”>
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </li>
                        @endif
                        @if (!empty($store->facebook))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $store->facebook }}" target="_blank">
                                    <i class="fab fa-facebook-square"></i>
                                </a>
                            </li>
                        @endif
                        @if (!empty($store->instagram))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $store->instagram }}" target="_blank">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </li>
                        @endif
                        @if (!empty($store->twitter))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $store->twitter }}" target="_blank">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </li>
                        @endif
                        @if (!empty($store->youtube))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $store->youtube }}" target="_blank">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </footer>


        <div id="invoice_logo_img" class="d-none">
            <div class="row align-items-center py-2 px-3">
                @if (!empty($store->invoice_logo))
                    <img alt="Image placeholder"
                        src="{{ asset(Storage::url('uploads/store_logo/' . $store->invoice_logo)) . '?timestamp=' . time() }}"
                        id="navbar-logo" style="height: 40px;">
                @else
                    <img alt="Image placeholder"
                        src="{{ asset(Storage::url('uploads/store_logo/invoice_logo.png')) . '?timestamp=' . time() }}"
                        id="navbar-logo" style="height: 40px;">
                @endif
            </div>
        </div>


        <script src="{{ asset('custom/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/dash.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script> --}}
        <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
        <script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
        <!-- Time picker -->
        <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
        <!-- datepicker -->
        <script src="{{ asset('assets/js/plugins/datepicker-full.min.js') }}"></script>
        <script src="{{ asset('custom/js/letter.avatar.js') }}"></script>
        @if (isset($settings['SITE_RTL']) && $settings['SITE_RTL'] == 'on')
            <script src="{{ asset('themes/' . $store->theme_dir . '/js/rtl-custom.js') }}"></script>
        @else
            <script src="{{ asset('themes/' . $store->theme_dir . '/js/custom.js') }}"></script>
        @endif

        <script type="text/javascript" src="{{ asset('custom/js/html2pdf.bundle.min.js') }}"></script>
        <script>
            var filename = $('#filesname').val();

            function saveAsPDF() {
                var element = document.getElementById('printableArea');
                var opt = {
                    margin: 0.3,
                    filename: filename,
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 4,
                        dpi: 72,
                        letterRendering: true
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'A2'
                    }
                };
                html2pdf().set(opt).from(element).save();
            }
        </script>
        <script>
            $("#deliver_btn").on('click', '#delivered', function() {
                var status = $(this).attr('data-value');
                var data = {
                    delivered: status,
                }
                $.ajax({
                    url: '{{ route('orders.update', $order->id) }}',
                    method: 'PUT',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.error == '') {
                            show_toastr('error', data.error, 'error');
                        } else {
                            show_toastr('success', data.success, 'success');

                        }
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                });
            });
        </script>
        <script>
            function myFunction() {
                var copyText = document.getElementById("myInput");
                copyText.select();
                copyText.setSelectionRange(0, 99999)
                document.execCommand("copy");
                show_toastr('Success', 'Link copied', 'success');
            }
        </script>
        <script>
            $(document).on('click', '.downloadable_prodcut', function() {
                var download_product = $(this).attr('data-value');
                var order_id = $(this).attr('data-id');

                var data = {
                    download_product: download_product,
                    order_id: order_id,
                }

                $.ajax({
                    url: '{{ route('user.downloadable_prodcut', $store->slug) }}',
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            show_toastr("success", data.message + '<br> <b>' + data.msg + '<b>', data[
                                "status"]);
                            $('.downloadab_msg').html('<span class="text-success">' + data.msg + '</sapn>');
                        } else {
                            show_toastr("Error", data.message + '<br> <b>' + data.msg + '<b>', data[
                                "status"]);
                        }
                    }
                });
            });
        </script>

        <script>
            function show_toastr(title, message, type) {
                var o, i;
                var icon = '';
                var cls = '';
                if (type == 'success') {
                    icon = 'fas fa-check-circle';
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
        </script>
    </body>

</html>
