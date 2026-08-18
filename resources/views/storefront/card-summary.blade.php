@php
    $logo = \App\Models\Utility::get_file('uploads/is_cover_image/');
@endphp

<div class="col-xxl-3 col-lg-4 col-12">
    <div class="main-right-col">
        <div class="cart-wrapper" id="card-summary">
            <div class="cart-box-wrp">
                <div class="coupon-box">
                    <h2>{{ __('Coupon Code') }}</h2>
                    <div class="input-wrp d-flex align-items-center">
                        <input type="text" class="form-control coupon hidd_val" placeholder="Enter Coupon Code"
                            id="stripe_coupon">
                        <input type="hidden" name="coupon" class="form-control hidden_coupon" value="">
                        <a href="#" class="btn apply-coupon">
                            <div class="btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="7" viewBox="0 0 9 7"
                                    fill="none">
                                    <path
                                        d="M3.24824 6.47344C2.41797 5.64316 1.59277 4.80527 0.759961 3.975C0.668555 3.88359 0.668555 3.73125 0.759961 3.63984L1.71719 2.68262C1.80859 2.59121 1.96094 2.59121 2.05234 2.68262L3.4209 4.05117L6.94004 0.529492C7.03398 0.438086 7.18379 0.438086 7.27773 0.529492L8.2375 1.48926C8.33145 1.5832 8.33145 1.73301 8.2375 1.82441L3.5834 6.47344C3.49199 6.56738 3.34219 6.56738 3.24824 6.47344Z"
                                        fill="#C18064" />
                                </svg>
                            </div>
                            {{ __('Accept') }}
                        </a>

                    </div>
                </div>
            </div>
            <div class="cart-body-wrp">
                <div class="cart-body">
                    <h3>{{ __('Cart') }}</h3>
                    @if (!empty($pro_cart) && count($pro_cart['products']) > 0)
                        @php
                            $sub_tax = 0;
                            $total = 0;
                            $sub_total = 0;
                        @endphp
                        @foreach ($pro_cart['products'] as $key => $product)
                            @if ($product['variant_id'] != 0)
                                <div class="cart-item d-flex" id="product-variant-id-{{ $product['variant_id'] }}">
                                    <div class="cart-image">
                                        <a href="#" class="img-ratio">
                                            <img src="{{ asset($product['image']) }}" alt="cart-image" loading="lazy">
                                        </a>
                                    </div>
                                    <div class="cart-content d-flex justify-content-between align-items-end"
                                        id="product-variant-id-{{ $product['variant_id'] }}">
                                        <div class="cart-left">
                                            <a href="#"
                                                class="cart-title">{{ $product['product_name'] . ' - ' . $product['variant_name'] }}</a>
                                            @php
                                                $total_tax = 0;
                                            @endphp
                                            <p>{{ __('Price per product:') }}
                                                <b><ins>{{ \App\Models\Utility::priceFormat($product['variant_price']) }}</ins></b>
                                            </p>

                                            @if ($product['tax'] > 0)
                                                @foreach ($product['tax'] as $k => $tax)
                                                    @php
                                                        $sub_tax =
                                                            ($product['variant_price'] *
                                                                $product['quantity'] *
                                                                $tax['tax']) /
                                                            100;
                                                        $total_tax += $sub_tax;
                                                    @endphp
                                                    <span class="variant_tax_{{ $k }}">
                                                        {{ $tax['tax_name'] . ' ' . $tax['tax'] . '%' . ' (' . $sub_tax . ')' }}
                                                    </span>
                                                @endforeach
                                                @php

                                                    $totalprice =
                                                        $product['variant_price'] * $product['quantity'] + $total_tax;
                                                    $subprice = $product['variant_price'] * $product['quantity'];
                                                    $total += $totalprice;
                                                    $sub_total += $subprice;
                                                @endphp
                                            @endif
                                        </div>
                                        <div class="cart-right">
                                            <div class="price">
                                                <ins class="subtotal"
                                                    id="product-variant-id-{{ $product['variant_id'] }}">{{ \App\Models\Utility::priceFormat($product['variant_price'] * $product['quantity']) }}<span
                                                        class="currency-type"></span></ins>
                                            </div>
                                            <div class="qty-wrp d-flex align-items-center">
                                                <div class="qty-spinner d-flex" data-id="{{ $key }}">
                                                    <button type="button" class="quantity-decrement product_qty"
                                                        data-id="{{ $product['id'] }}"
                                                        value="{{ $product['quantity'] }}" data-option="decrease"
                                                        min="0">
                                                        <svg width="12" height="2" viewBox="0 0 12 2"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0 0.251343V1.74871H12V0.251343H0Z" fill="#61AFB3">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <input type="text"
                                                        class="quantity pro_variant_id product_qty_input"
                                                        add_to_cart_variant="pro_variant_id"
                                                        data-id="{{ $product['variant_id'] }}"
                                                        data-cke-saved-name="quantity" name="quantity"
                                                        id="product_qty_input" value="{{ $product['quantity'] }}"
                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                                    <button type="button" class="quantity-increment product_qty"
                                                        data-id="{{ $product['id'] }}"
                                                        value="{{ $product['quantity'] }}" data-option="increase">
                                                        <svg width="12" height="12" viewBox="0 0 12 12"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M6.74868 5.25132V0H5.25132V5.25132H0V6.74868H5.25132V12H6.74868V6.74868H12V5.25132H6.74868Z"
                                                                fill="#61AFB3"></path>
                                                        </svg>
                                                    </button>
                                                </div>

                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['delete.cart_item', $store->slug, $product['id'], $product['variant_id']],
                                                ]) !!}
                                                <a href="#" class="remove-item show_confirm"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete') }}">
                                                    @if ($store->theme_dir == 'theme1' || $store->theme_dir == 'theme3')
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="7"
                                                            height="7" viewBox="0 0 7 7" fill="none">
                                                            <path
                                                                d="M4.61563 3.49998L6.43647 1.68707C6.58554 1.53799 6.66929 1.33581 6.66929 1.12498C6.66929 0.914162 6.58554 0.711975 6.43647 0.562901C6.28739 0.413827 6.0852 0.330078 5.87438 0.330078C5.66356 0.330078 5.46137 0.413827 5.3123 0.562901L3.49938 2.38373L1.68647 0.562901C1.53739 0.413827 1.3352 0.330078 1.12438 0.330078C0.91356 0.330078 0.711372 0.413827 0.562299 0.562901C0.413225 0.711975 0.329476 0.914162 0.329476 1.12498C0.329476 1.33581 0.413225 1.53799 0.562299 1.68707L2.38313 3.49998L0.562299 5.3129C0.488097 5.3865 0.429202 5.47406 0.38901 5.57053C0.348818 5.667 0.328125 5.77048 0.328125 5.87498C0.328125 5.97949 0.348818 6.08297 0.38901 6.17944C0.429202 6.27591 0.488097 6.36347 0.562299 6.43707C0.635894 6.51127 0.723453 6.57017 0.819925 6.61036C0.916397 6.65055 1.01987 6.67124 1.12438 6.67124C1.22889 6.67124 1.33237 6.65055 1.42884 6.61036C1.52531 6.57017 1.61287 6.51127 1.68647 6.43707L3.49938 4.61623L5.3123 6.43707C5.38589 6.51127 5.47345 6.57017 5.56993 6.61036C5.6664 6.65055 5.76987 6.67124 5.87438 6.67124C5.97889 6.67124 6.08237 6.65055 6.17884 6.61036C6.27531 6.57017 6.36287 6.51127 6.43647 6.43707C6.51067 6.36347 6.56956 6.27591 6.60975 6.17944C6.64995 6.08297 6.67064 5.97949 6.67064 5.87498C6.67064 5.77048 6.64995 5.667 6.60975 5.57053C6.56956 5.47406 6.51067 5.3865 6.43647 5.3129L4.61563 3.49998Z"
                                                                fill="white" />
                                                        </svg>
                                                    @elseif(
                                                        $store->theme_dir == 'theme2' ||
                                                            $store->theme_dir == 'theme4' ||
                                                            $store->theme_dir == 'theme5' ||
                                                            $store->theme_dir == 'theme6' ||
                                                            $store->theme_dir == 'theme7')
                                                        <svg width="17" height="19" viewBox="0 0 17 19"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M5.33779 0.52751C5.33782 0.387738 5.39334 0.253696 5.49215 0.154838C5.59096 0.0559806 5.72498 0.000395233 5.86475 0.000296875L11.1339 0C11.2738 0.000324112 11.4077 0.0560634 11.5065 0.155001C11.6053 0.253938 11.6608 0.388001 11.6609 0.527807V1.83098H5.33779V0.52751ZM14.0014 18.2451C13.9879 18.4506 13.8965 18.6432 13.7457 18.7834C13.5949 18.9237 13.3962 19.001 13.1903 18.9996H3.74781C3.54195 18.999 3.34392 18.9206 3.19349 18.7801C3.04305 18.6395 2.95136 18.4473 2.93682 18.2419L2.1288 6.42193H14.8638L14.0016 18.245L14.0014 18.2451ZM16.1006 5.35139H0.898438V4.12679C0.898663 3.80199 1.02777 3.49057 1.25741 3.26088C1.48704 3.0312 1.79844 2.90203 2.12323 2.90173L14.8756 2.90132C15.2004 2.90182 15.5117 3.03111 15.7413 3.26082C15.9709 3.49053 16.1 3.80191 16.1003 4.12667V5.35128L16.1006 5.35139ZM6.05174 16.1818C6.05174 16.2521 6.06559 16.3217 6.09248 16.3866C6.11938 16.4515 6.1588 16.5105 6.2085 16.5602C6.25819 16.6099 6.31719 16.6494 6.38212 16.6763C6.44706 16.7031 6.51665 16.717 6.58693 16.717C6.65722 16.717 6.72681 16.7031 6.79174 16.6763C6.85667 16.6494 6.91567 16.6099 6.96537 16.5602C7.01507 16.5105 7.05449 16.4515 7.08139 16.3866C7.10828 16.3217 7.12212 16.2521 7.12212 16.1818V8.67654C7.121 8.5354 7.06415 8.40041 6.96396 8.30099C6.86378 8.20156 6.72836 8.14574 6.58721 8.14569C6.44606 8.14564 6.31061 8.20137 6.21035 8.30072C6.11009 8.40008 6.05315 8.53503 6.05193 8.67617V16.1818H6.05174ZM9.8703 16.1818C9.8703 16.3238 9.92669 16.4599 10.0271 16.5603C10.1275 16.6607 10.2636 16.7171 10.4056 16.7171C10.5475 16.7171 10.6837 16.6607 10.7841 16.5603C10.8844 16.4599 10.9408 16.3238 10.9408 16.1818V8.67654C10.9397 8.53538 10.8828 8.40037 10.7826 8.30093C10.6824 8.20149 10.547 8.14567 10.4058 8.14562C10.2647 8.14557 10.1292 8.2013 10.0289 8.30067C9.92865 8.40004 9.87171 8.53501 9.87048 8.67617L9.8703 16.1818Z"
                                                                fill="#FC0005" />
                                                        </svg>
                                                    @endif
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="cart-item d-flex" id="product-id-{{ $product['product_id'] }}">
                                    <div class="cart-image">
                                        <a href="#" class="img-ratio">
                                            <img src="{{ asset($product['image']) }}" alt="cart-image"
                                                loading="lazy">
                                        </a>
                                    </div>
                                    <div class="cart-content d-flex justify-content-between align-items-end"
                                        id="product-id-{{ $product['product_id'] }}">
                                        <div class="cart-left">
                                            <a href="#" class="cart-title">{{ $product['product_name'] }}</a>
                                            @php
                                                $total_tax = 0;
                                            @endphp
                                            <p>{{ __('Price Per Product:') }}
                                                <b><ins>{{ \App\Models\Utility::priceFormat($product['price']) }}</ins></b>
                                            </p>
                                            @if ($product['tax'] > 0)
                                                @foreach ($product['tax'] as $k => $tax)
                                                    @php
                                                        $sub_tax =
                                                            ($product['price'] * $product['quantity'] * $tax['tax']) /
                                                            100;
                                                        $total_tax += $sub_tax;
                                                    @endphp
                                                    <span class="tax_{{ $k }}">
                                                        {{ $tax['tax_name'] . ' ' . $tax['tax'] . '%' . ' (' . $sub_tax . ')' }}
                                                    </span>
                                                @endforeach
                                                @php
                                                    $totalprice = $product['price'] * $product['quantity'] + $total_tax;
                                                    $subprice = $product['price'] * $product['quantity'];
                                                    $total += $totalprice;
                                                    $sub_total += $subprice;
                                                @endphp
                                            @endif
                                        </div>
                                        <div class="cart-right">
                                            <div class="price">
                                                <ins class="subtotal"
                                                    id="product-id-{{ $product['product_id'] }}">{{ \App\Models\Utility::priceFormat($product['price'] * $product['quantity']) }}<span
                                                        class="currency-type"></span></ins>
                                            </div>
                                            <div class="qty-wrp d-flex align-items-center">
                                                <div class="qty-spinner d-flex" data-id="{{ $key }}">
                                                    <button type="button" class="quantity-decrement product_qty"
                                                        data-id="{{ $product['id'] }}"
                                                        value="{{ $product['quantity'] }}" data-option="decrease"
                                                        min="0">
                                                        <svg width="12" height="2" viewBox="0 0 12 2"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0 0.251343V1.74871H12V0.251343H0Z"
                                                                fill="#61AFB3">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <input type="text"
                                                        class="quantity pro_variant_id product_qty_input"
                                                        add_to_cart_variant="pro_variant_id"
                                                        data-id="{{ $product['variant_id'] }}"
                                                        data-cke-saved-name="quantity" name="quantity"
                                                        id="product_qty_input" value="{{ $product['quantity'] }}"
                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                                    <button type="button" class="quantity-increment product_qty"
                                                        data-id="{{ $product['id'] }}"
                                                        value="{{ $product['quantity'] }}" data-option="increase">
                                                        <svg width="12" height="12" viewBox="0 0 12 12"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M6.74868 5.25132V0H5.25132V5.25132H0V6.74868H5.25132V12H6.74868V6.74868H12V5.25132H6.74868Z"
                                                                fill="#61AFB3"></path>
                                                        </svg>
                                                    </button>
                                                </div>

                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['delete.cart_item', $store->slug, $product['id'], $product['variant_id']],
                                                ]) !!}
                                                <a href="#" class="remove-item show_confirm"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete') }}">
                                                    @if ($store->theme_dir == 'theme1' || $store->theme_dir == 'theme3')
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="7"
                                                            height="7" viewBox="0 0 7 7" fill="none">
                                                            <path
                                                                d="M4.61563 3.49998L6.43647 1.68707C6.58554 1.53799 6.66929 1.33581 6.66929 1.12498C6.66929 0.914162 6.58554 0.711975 6.43647 0.562901C6.28739 0.413827 6.0852 0.330078 5.87438 0.330078C5.66356 0.330078 5.46137 0.413827 5.3123 0.562901L3.49938 2.38373L1.68647 0.562901C1.53739 0.413827 1.3352 0.330078 1.12438 0.330078C0.91356 0.330078 0.711372 0.413827 0.562299 0.562901C0.413225 0.711975 0.329476 0.914162 0.329476 1.12498C0.329476 1.33581 0.413225 1.53799 0.562299 1.68707L2.38313 3.49998L0.562299 5.3129C0.488097 5.3865 0.429202 5.47406 0.38901 5.57053C0.348818 5.667 0.328125 5.77048 0.328125 5.87498C0.328125 5.97949 0.348818 6.08297 0.38901 6.17944C0.429202 6.27591 0.488097 6.36347 0.562299 6.43707C0.635894 6.51127 0.723453 6.57017 0.819925 6.61036C0.916397 6.65055 1.01987 6.67124 1.12438 6.67124C1.22889 6.67124 1.33237 6.65055 1.42884 6.61036C1.52531 6.57017 1.61287 6.51127 1.68647 6.43707L3.49938 4.61623L5.3123 6.43707C5.38589 6.51127 5.47345 6.57017 5.56993 6.61036C5.6664 6.65055 5.76987 6.67124 5.87438 6.67124C5.97889 6.67124 6.08237 6.65055 6.17884 6.61036C6.27531 6.57017 6.36287 6.51127 6.43647 6.43707C6.51067 6.36347 6.56956 6.27591 6.60975 6.17944C6.64995 6.08297 6.67064 5.97949 6.67064 5.87498C6.67064 5.77048 6.64995 5.667 6.60975 5.57053C6.56956 5.47406 6.51067 5.3865 6.43647 5.3129L4.61563 3.49998Z"
                                                                fill="white" />
                                                        </svg>
                                                    @elseif(
                                                        $store->theme_dir == 'theme2' ||
                                                            $store->theme_dir == 'theme4' ||
                                                            $store->theme_dir == 'theme5' ||
                                                            $store->theme_dir == 'theme6' ||
                                                            $store->theme_dir == 'theme7')
                                                        <svg width="17" height="19" viewBox="0 0 17 19"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M5.33779 0.52751C5.33782 0.387738 5.39334 0.253696 5.49215 0.154838C5.59096 0.0559806 5.72498 0.000395233 5.86475 0.000296875L11.1339 0C11.2738 0.000324112 11.4077 0.0560634 11.5065 0.155001C11.6053 0.253938 11.6608 0.388001 11.6609 0.527807V1.83098H5.33779V0.52751ZM14.0014 18.2451C13.9879 18.4506 13.8965 18.6432 13.7457 18.7834C13.5949 18.9237 13.3962 19.001 13.1903 18.9996H3.74781C3.54195 18.999 3.34392 18.9206 3.19349 18.7801C3.04305 18.6395 2.95136 18.4473 2.93682 18.2419L2.1288 6.42193H14.8638L14.0016 18.245L14.0014 18.2451ZM16.1006 5.35139H0.898438V4.12679C0.898663 3.80199 1.02777 3.49057 1.25741 3.26088C1.48704 3.0312 1.79844 2.90203 2.12323 2.90173L14.8756 2.90132C15.2004 2.90182 15.5117 3.03111 15.7413 3.26082C15.9709 3.49053 16.1 3.80191 16.1003 4.12667V5.35128L16.1006 5.35139ZM6.05174 16.1818C6.05174 16.2521 6.06559 16.3217 6.09248 16.3866C6.11938 16.4515 6.1588 16.5105 6.2085 16.5602C6.25819 16.6099 6.31719 16.6494 6.38212 16.6763C6.44706 16.7031 6.51665 16.717 6.58693 16.717C6.65722 16.717 6.72681 16.7031 6.79174 16.6763C6.85667 16.6494 6.91567 16.6099 6.96537 16.5602C7.01507 16.5105 7.05449 16.4515 7.08139 16.3866C7.10828 16.3217 7.12212 16.2521 7.12212 16.1818V8.67654C7.121 8.5354 7.06415 8.40041 6.96396 8.30099C6.86378 8.20156 6.72836 8.14574 6.58721 8.14569C6.44606 8.14564 6.31061 8.20137 6.21035 8.30072C6.11009 8.40008 6.05315 8.53503 6.05193 8.67617V16.1818H6.05174ZM9.8703 16.1818C9.8703 16.3238 9.92669 16.4599 10.0271 16.5603C10.1275 16.6607 10.2636 16.7171 10.4056 16.7171C10.5475 16.7171 10.6837 16.6607 10.7841 16.5603C10.8844 16.4599 10.9408 16.3238 10.9408 16.1818V8.67654C10.9397 8.53538 10.8828 8.40037 10.7826 8.30093C10.6824 8.20149 10.547 8.14567 10.4058 8.14562C10.2647 8.14557 10.1292 8.2013 10.0289 8.30067C9.92865 8.40004 9.87171 8.53501 9.87048 8.67617L9.8703 16.1818Z"
                                                                fill="#FC0005" />
                                                        </svg>
                                                    @endif
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div class="cart-total">
                            <ul>
                                <li class="d-flex align-items-center justify-content-between">
                                    <span>{{ __('Subtotal') }}</span>
                                    <span class="sub_total_price"
                                        data-value="{{ $total }}">{{ App\Models\Utility::priceFormat($sub_total) }}</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between">
                                    <span>{{ __('Coupon') }}</span>
                                    <span class="dicount_price">{{ __('0.00') }}</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between">
                                    @if (!empty($pro_cart) && count($pro_cart['products']) > 0)
                                        <span>{{ __('Shipping') }}</span>
                                        <span class="shipping_price">{{ __('0.00') }}</span>
                                    @endif
                                </li>
                                @if (!empty($taxArr))
                                    @foreach ($taxArr['tax'] as $k => $tax)
                                        @if ($product['variant_id'] != 0)
                                            <li class="d-flex align-items-center justify-content-between"
                                                id="product-variant-id-{{ $product['variant_id'] }}">
                                            @else
                                            <li class="d-flex align-items-center justify-content-between"
                                                id="product-id-{{ $product['product_id'] }}">
                                        @endif
                                        <span> {{ $tax }}</span>

                                        <span
                                            class="total_tax_{{ $k }}">{{ \App\Models\Utility::priceFormat($taxArr['rate'][$k]) }}</span>
                                        </li>
                                    @endforeach
                                @endif

                                <li class="d-flex align-items-center justify-content-between total">
                                    <span>{{ __('Total (Incl Tax)') }}</span>
                                    <span class="total-amount" data-original="$0.00">
                                        <input type="hidden" class="product_total" value="{{ $total }}">
                                        <input type="hidden" class="total_pay_price"
                                            value="{{ App\Models\Utility::priceFormat($total) }}">
                                        <b class="final_total_price pro_total_price" id="displaytotal"
                                            data-original="{{ \App\Models\Utility::priceFormat(!empty($total) ? $total : 0) }}">
                                            {{ App\Models\Utility::priceFormat($total) }}</b></span></span>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="cart-total">
                            <ul>
                                <li class="d-flex align-items-center justify-content-between">
                                    <span>{{ __('Subtotal') }}</span>
                                    <span>{{ __('0.00') }}</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between">
                                    <span>{{ __('Coupon') }}</span>
                                    <span class="dicount_price">{{ __('0.00') }}</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between">
                                    @if (!empty($pro_cart) && count($pro_cart['products']) > 0)
                                        <span>{{ __('Shipping') }}</span>
                                        <span class="shipping_price">{{ __('0.00') }}</span>
                                    @endif
                                </li>
                                @if (!empty($taxArr))
                                    @foreach ($taxArr['tax'] as $k => $tax)
                                        <span> {{ $tax }}</span>
                                        <span>{{ \App\Models\Utility::priceFormat($taxArr['rate'][$k]) }}</span>
                                    @endforeach
                                @endif

                                <li class="d-flex align-items-center justify-content-between total">
                                    <span><b>{{ __('Total (Incl Tax)') }}</b></span>
                                    <span class="total-amount final_total_price pro_total_price" id="displaytotal"
                                        data-original="{{ \App\Models\Utility::priceFormat(!empty($total) ? $total : 0) }}">
                                        <b>{{ __('0.00') }}</b>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            <div class="checkout-fields">
                <div class="details-form-wrp">
                    <div class="details-form">
                        <h4>{{ __('Delivery Details') }}</h4>
                        <div class="row detail-form">
                            <div class="col-xl-6 col-lg-12 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    {{ Form::label('name', __('Name')) }}
                                    {{ Form::text('name', old('name'), ['class' => 'form-control active fname', 'required' => 'required', 'Placeholder' => 'Octavia Parks']) }}
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    {{ Form::label('email', __('Email')) }}
                                    {{ Form::email('email', old('email'), ['class' => 'form-control active email', 'required' => 'required', 'Placeholder' => 'wekykukisa@mailinator.com']) }}
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    {{ Form::label('phone', __('Phone')) }}
                                    {{ Form::text('phone', old('phone'), ['class' => 'form-control active phone', 'required' => 'required', 'Placeholder' => '+1 (874) 175-7277']) }}
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::label('billingaddress', __('Address line 1')) }}
                                    {{ Form::text('billing_address', old('billing_address'), ['class' => 'form-control active billing_address', 'required' => 'required', 'Placeholder' => '17 Windmill Brae , Aberdeen']) }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::label('shipping_address', __('Address line 2')) }}
                                    {{ Form::text('shipping_address', old('shipping_address'), ['class' => 'form-control active shipping_address', 'Placeholder' => 'Aberdeen City , AB11 6HU']) }}
                                </div>
                            </div>
                            @if (!empty($store->custom_field_title_1))
                                <div class="col-12">
                                    <div class="form-group">
                                        {{ Form::label('custom_field_title_1', $store->custom_field_title_1) }}
                                        {{ Form::text('custom_field_title_1', '+1 (776) 912-8656', ['class' => 'form-control active custom_field_title_1']) }}
                                    </div>
                                </div>
                            @endif
                            @if (!empty($store->custom_field_title_2))
                                <div class="col-12">
                                    <div class="form-group">
                                        {{ Form::label('custom_field_title_2', $store->custom_field_title_2) }}
                                        {{ Form::text('custom_field_title_2', 'United Kingdom', ['class' => 'form-control active custom_field_title_2']) }}
                                    </div>
                                </div>
                            @endif
                            @if (!empty($store->custom_field_title_3))
                                <div class="col-12">
                                    <div class="form-group">
                                        {{ Form::label('custom_field_title_3', $store->custom_field_title_3) }}
                                        {{ Form::text('custom_field_title_3', 'Pariatur Voluptas q', ['class' => 'form-control active custom_field_title_3']) }}
                                    </div>
                                </div>
                            @endif
                            @if (!empty($store->custom_field_title_4))
                                <div class="col-12">
                                    <div class="form-group">
                                        {{ Form::label('custom_field_title_4', $store->custom_field_title_4) }}
                                        {{ Form::text('custom_field_title_4', '10001', ['class' => 'form-control active custom_field_title_4']) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if (!empty($pro_cart) && count($pro_cart['products']) > 0)

                            @if ($store->enable_shipping == 'on')
                                @if (count($locations) != 1)
                                    @if (count($shippings) != 0)
                                        <h4>{{ __('Shipping Location') }}</h4>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::select('location_id', $locations, null, ['class' => 'active acticard-titleve form-control change_location', 'required' => 'required']) }}
                                                </div>

                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center"
                                                        id="shipping_location_content">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endif
                        <h4>{{ __('Order Notes') }}</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::textarea('special_instruct', null, ['class' => 'special_instruct form-control', 'rows' => 3, 'placeholder' => 'Description']) }}
                                </div>
                            </div>
                        </div>
                        @if (
                            $store_settings['is_checkout_login_required'] == null ||
                                ($store_settings['is_checkout_login_required'] == 'off' && !Auth::guard('customers')->user()))
                            <a class="btn checkout-btn w-100 checkoutBtn btn-submit" data-toggle="tooltip"
                                data-size="lg" id="checkoutBtn"
                                data-url="{{ route('store-process.checkout', [$store->slug]) }}"
                                data-ajax-popup="true" data-title="CheckOut">
                                {{ __('Proceed to checkout') }}

                                <div class="btn-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.9119 15.6002H9.7119C9.15751 15.5999 8.6203 15.4077 8.19154 15.0563C7.76279 14.7048 7.46893 14.2157 7.3599 13.6722L5.7439 5.60016H3.1999C2.98773 5.60016 2.78425 5.51588 2.63422 5.36585C2.48419 5.21582 2.3999 5.01234 2.3999 4.80016C2.3999 4.58799 2.48419 4.38451 2.63422 4.23448C2.78425 4.08445 2.98773 4.00016 3.1999 4.00016H6.3999C6.58706 3.99639 6.76961 4.05837 6.91579 4.17531C7.06196 4.29224 7.1625 4.45674 7.1999 4.64016L8.9279 13.3602C8.9653 13.5436 9.06584 13.7081 9.21202 13.825C9.35819 13.942 9.54075 14.0039 9.7279 14.0002H17.9119C18.0979 14.0043 18.2796 13.9435 18.4256 13.8281C18.5716 13.7128 18.6729 13.5501 18.7119 13.3682L19.9999 7.36816C20.0253 7.25002 20.0236 7.12765 19.995 7.01026C19.9663 6.89286 19.9114 6.78348 19.8344 6.69033C19.7575 6.59718 19.6604 6.52268 19.5505 6.47242C19.4406 6.42217 19.3207 6.39746 19.1999 6.40016H18.3999C18.1877 6.40016 17.9842 6.31588 17.8342 6.16585C17.6842 6.01582 17.5999 5.81234 17.5999 5.60016C17.5999 5.38799 17.6842 5.18451 17.8342 5.03448C17.9842 4.88445 18.1877 4.80016 18.3999 4.80016H19.1999C19.5632 4.79178 19.9236 4.86599 20.254 5.01721C20.5845 5.16843 20.8762 5.3927 21.1073 5.67311C21.3384 5.95353 21.5029 6.28276 21.5882 6.63597C21.6735 6.98919 21.6775 7.35717 21.5999 7.71216L20.2959 13.7122C20.1777 14.2538 19.8757 14.7378 19.4411 15.0819C19.0065 15.4261 18.4662 15.6092 17.9119 15.6002Z"
                                            fill="black" />
                                        <path
                                            d="M10.4 21.6003C9.92533 21.6003 9.46131 21.4595 9.06663 21.1958C8.67195 20.9321 8.36434 20.5573 8.18269 20.1187C8.00104 19.6802 7.95351 19.1976 8.04612 18.7321C8.13872 18.2665 8.3673 17.8389 8.70294 17.5032C9.03859 17.1676 9.46623 16.939 9.93178 16.8464C10.3973 16.7538 10.8799 16.8013 11.3184 16.983C11.757 17.1646 12.1318 17.4722 12.3955 17.8669C12.6592 18.2616 12.8 18.7256 12.8 19.2003C12.8 19.8368 12.5471 20.4473 12.0971 20.8974C11.647 21.3474 11.0365 21.6003 10.4 21.6003ZM10.4 18.4003C10.2418 18.4003 10.0871 18.4472 9.95555 18.5351C9.82399 18.623 9.72145 18.748 9.6609 18.8941C9.60035 19.0403 9.58451 19.2012 9.61537 19.3564C9.64624 19.5116 9.72243 19.6541 9.83432 19.766C9.9462 19.8779 10.0887 19.9541 10.2439 19.9849C10.3991 20.0158 10.56 19.9999 10.7061 19.9394C10.8523 19.8788 10.9773 19.7763 11.0652 19.6448C11.1531 19.5132 11.2 19.3585 11.2 19.2003C11.2 18.9881 11.1157 18.7846 10.9657 18.6346C10.8157 18.4846 10.6122 18.4003 10.4 18.4003Z"
                                            fill="black" />
                                        <path
                                            d="M17.6 21.6003C17.1253 21.6003 16.6613 21.4595 16.2666 21.1958C15.8719 20.9321 15.5643 20.5573 15.3826 20.1187C15.201 19.6802 15.1535 19.1976 15.2461 18.7321C15.3387 18.2665 15.5672 17.8389 15.9029 17.5032C16.2385 17.1676 16.6662 16.939 17.1317 16.8464C17.5973 16.7538 18.0799 16.8013 18.5184 16.983C18.9569 17.1646 19.3318 17.4722 19.5955 17.8669C19.8592 18.2616 20 18.7256 20 19.2003C20 19.8368 19.7471 20.4473 19.297 20.8974C18.8469 21.3474 18.2365 21.6003 17.6 21.6003ZM17.6 18.4003C17.4417 18.4003 17.2871 18.4472 17.1555 18.5351C17.0239 18.623 16.9214 18.748 16.8608 18.8941C16.8003 19.0403 16.7845 19.2012 16.8153 19.3564C16.8462 19.5116 16.9224 19.6541 17.0343 19.766C17.1461 19.8779 17.2887 19.9541 17.4439 19.9849C17.5991 20.0158 17.7599 19.9999 17.9061 19.9394C18.0523 19.8788 18.1772 19.7763 18.2651 19.6448C18.353 19.5132 18.4 19.3585 18.4 19.2003C18.4 18.9881 18.3157 18.7846 18.1656 18.6346C18.0156 18.4846 17.8121 18.4003 17.6 18.4003Z"
                                            fill="black" />
                                        <path
                                            d="M15.9999 9.6H11.1999C10.9877 9.6 10.7842 9.51571 10.6342 9.36569C10.4842 9.21566 10.3999 9.01217 10.3999 8.8C10.3999 8.58783 10.4842 8.38434 10.6342 8.23431C10.7842 8.08429 10.9877 8 11.1999 8H15.9999C16.2121 8 16.4156 8.08429 16.5656 8.23431C16.7156 8.38434 16.7999 8.58783 16.7999 8.8C16.7999 9.01217 16.7156 9.21566 16.5656 9.36569C16.4156 9.51571 16.2121 9.6 15.9999 9.6Z"
                                            fill="black" />
                                        <path
                                            d="M13.5998 12.0001C13.3876 12.0001 13.1841 11.9158 13.0341 11.7658C12.8841 11.6158 12.7998 11.4123 12.7998 11.2001V6.4001C12.7998 6.18792 12.8841 5.98444 13.0341 5.83441C13.1841 5.68438 13.3876 5.6001 13.5998 5.6001C13.812 5.6001 14.0155 5.68438 14.1655 5.83441C14.3155 5.98444 14.3998 6.18792 14.3998 6.4001V11.2001C14.3998 11.4123 14.3155 11.6158 14.1655 11.7658C14.0155 11.9158 13.812 12.0001 13.5998 12.0001Z"
                                            fill="black" />
                                    </svg>
                                </div>
                            </a>
                        @else
                            <a class="btn checkoutBtn checkout-btn w-100 authUser btn-submit">
                                {{ __('Proceed to checkout') }}

                                <div class="btn-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.9119 15.6002H9.7119C9.15751 15.5999 8.6203 15.4077 8.19154 15.0563C7.76279 14.7048 7.46893 14.2157 7.3599 13.6722L5.7439 5.60016H3.1999C2.98773 5.60016 2.78425 5.51588 2.63422 5.36585C2.48419 5.21582 2.3999 5.01234 2.3999 4.80016C2.3999 4.58799 2.48419 4.38451 2.63422 4.23448C2.78425 4.08445 2.98773 4.00016 3.1999 4.00016H6.3999C6.58706 3.99639 6.76961 4.05837 6.91579 4.17531C7.06196 4.29224 7.1625 4.45674 7.1999 4.64016L8.9279 13.3602C8.9653 13.5436 9.06584 13.7081 9.21202 13.825C9.35819 13.942 9.54075 14.0039 9.7279 14.0002H17.9119C18.0979 14.0043 18.2796 13.9435 18.4256 13.8281C18.5716 13.7128 18.6729 13.5501 18.7119 13.3682L19.9999 7.36816C20.0253 7.25002 20.0236 7.12765 19.995 7.01026C19.9663 6.89286 19.9114 6.78348 19.8344 6.69033C19.7575 6.59718 19.6604 6.52268 19.5505 6.47242C19.4406 6.42217 19.3207 6.39746 19.1999 6.40016H18.3999C18.1877 6.40016 17.9842 6.31588 17.8342 6.16585C17.6842 6.01582 17.5999 5.81234 17.5999 5.60016C17.5999 5.38799 17.6842 5.18451 17.8342 5.03448C17.9842 4.88445 18.1877 4.80016 18.3999 4.80016H19.1999C19.5632 4.79178 19.9236 4.86599 20.254 5.01721C20.5845 5.16843 20.8762 5.3927 21.1073 5.67311C21.3384 5.95353 21.5029 6.28276 21.5882 6.63597C21.6735 6.98919 21.6775 7.35717 21.5999 7.71216L20.2959 13.7122C20.1777 14.2538 19.8757 14.7378 19.4411 15.0819C19.0065 15.4261 18.4662 15.6092 17.9119 15.6002Z"
                                            fill="black" />
                                        <path
                                            d="M10.4 21.6003C9.92533 21.6003 9.46131 21.4595 9.06663 21.1958C8.67195 20.9321 8.36434 20.5573 8.18269 20.1187C8.00104 19.6802 7.95351 19.1976 8.04612 18.7321C8.13872 18.2665 8.3673 17.8389 8.70294 17.5032C9.03859 17.1676 9.46623 16.939 9.93178 16.8464C10.3973 16.7538 10.8799 16.8013 11.3184 16.983C11.757 17.1646 12.1318 17.4722 12.3955 17.8669C12.6592 18.2616 12.8 18.7256 12.8 19.2003C12.8 19.8368 12.5471 20.4473 12.0971 20.8974C11.647 21.3474 11.0365 21.6003 10.4 21.6003ZM10.4 18.4003C10.2418 18.4003 10.0871 18.4472 9.95555 18.5351C9.82399 18.623 9.72145 18.748 9.6609 18.8941C9.60035 19.0403 9.58451 19.2012 9.61537 19.3564C9.64624 19.5116 9.72243 19.6541 9.83432 19.766C9.9462 19.8779 10.0887 19.9541 10.2439 19.9849C10.3991 20.0158 10.56 19.9999 10.7061 19.9394C10.8523 19.8788 10.9773 19.7763 11.0652 19.6448C11.1531 19.5132 11.2 19.3585 11.2 19.2003C11.2 18.9881 11.1157 18.7846 10.9657 18.6346C10.8157 18.4846 10.6122 18.4003 10.4 18.4003Z"
                                            fill="black" />
                                        <path
                                            d="M17.6 21.6003C17.1253 21.6003 16.6613 21.4595 16.2666 21.1958C15.8719 20.9321 15.5643 20.5573 15.3826 20.1187C15.201 19.6802 15.1535 19.1976 15.2461 18.7321C15.3387 18.2665 15.5672 17.8389 15.9029 17.5032C16.2385 17.1676 16.6662 16.939 17.1317 16.8464C17.5973 16.7538 18.0799 16.8013 18.5184 16.983C18.9569 17.1646 19.3318 17.4722 19.5955 17.8669C19.8592 18.2616 20 18.7256 20 19.2003C20 19.8368 19.7471 20.4473 19.297 20.8974C18.8469 21.3474 18.2365 21.6003 17.6 21.6003ZM17.6 18.4003C17.4417 18.4003 17.2871 18.4472 17.1555 18.5351C17.0239 18.623 16.9214 18.748 16.8608 18.8941C16.8003 19.0403 16.7845 19.2012 16.8153 19.3564C16.8462 19.5116 16.9224 19.6541 17.0343 19.766C17.1461 19.8779 17.2887 19.9541 17.4439 19.9849C17.5991 20.0158 17.7599 19.9999 17.9061 19.9394C18.0523 19.8788 18.1772 19.7763 18.2651 19.6448C18.353 19.5132 18.4 19.3585 18.4 19.2003C18.4 18.9881 18.3157 18.7846 18.1656 18.6346C18.0156 18.4846 17.8121 18.4003 17.6 18.4003Z"
                                            fill="black" />
                                        <path
                                            d="M15.9999 9.6H11.1999C10.9877 9.6 10.7842 9.51571 10.6342 9.36569C10.4842 9.21566 10.3999 9.01217 10.3999 8.8C10.3999 8.58783 10.4842 8.38434 10.6342 8.23431C10.7842 8.08429 10.9877 8 11.1999 8H15.9999C16.2121 8 16.4156 8.08429 16.5656 8.23431C16.7156 8.38434 16.7999 8.58783 16.7999 8.8C16.7999 9.01217 16.7156 9.21566 16.5656 9.36569C16.4156 9.51571 16.2121 9.6 15.9999 9.6Z"
                                            fill="black" />
                                        <path
                                            d="M13.5998 12.0001C13.3876 12.0001 13.1841 11.9158 13.0341 11.7658C12.8841 11.6158 12.7998 11.4123 12.7998 11.2001V6.4001C12.7998 6.18792 12.8841 5.98444 13.0341 5.83441C13.1841 5.68438 13.3876 5.6001 13.5998 5.6001C13.812 5.6001 14.0155 5.68438 14.1655 5.83441C14.3155 5.98444 14.3998 6.18792 14.3998 6.4001V11.2001C14.3998 11.4123 14.3155 11.6158 14.1655 11.7658C14.0155 11.9158 13.812 12.0001 13.5998 12.0001Z"
                                            fill="black" />
                                    </svg>

                                </div>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="payment-method" id="asGuest">
                    <h4>{{ __('Payment Method') }}</h4>
                    <div class="row">
                        @if ($store->enable_whatsapp == 'on')
                            <div class="col-sm-6 col-12">
                                <a id="order-whatsapp" data-toggle="modal" data-target="#checkoutModal"
                                    href="#" class="third-party-payment whatsap-btn payment whatsapp">
                                    <img src="{{ asset('themes/' . $store->theme_dir . '/images/whatsapp.png') }}"
                                        alt="whatsapp">
                                    <p>{{ __('Order On') }} <b>{{ __('WhatsApp') }}</b></p>
                                </a>
                            </div>
                        @endif
                        @if ($store->enable_telegram == 'on')
                            <div class="col-sm-6 col-12">
                                <a id="order-telegram" href="#"
                                    class="third-party-payment telegram telegram-btn">
                                    <img src="{{ asset('themes/' . $store->theme_dir . '/images/telegram.png') }}"
                                        alt="telegram">
                                    <p>{{ __('Order On') }} <b>{{ __('Telegram') }}</b></p>
                                </a>
                            </div>
                        @endif
                        @if ($store['enable_bank'] == 'on')
                            <div class="col-lg-6 col-md-4 col-sm-6 col-12 ">
                                <form style="margin-top: 0" action="{{ route('user.bank_transfer', $store->slug) }}"
                                    method="POST" id="bank_transfer_form" class="payment-method-form"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="upload-btn-wrapper">
                                        <label for="bank_transfer_invoice"
                                            class="file-upload btn payment-btn bg-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                viewBox="0 0 17 17" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M6.67952 7.2448C6.69833 7.59772 6.42748 7.89908 6.07456 7.91789C5.59289 7.94357 5.21139 7.97498 4.91327 8.00642C4.51291 8.04864 4.26965 8.29456 4.22921 8.64831C4.17115 9.15619 4.12069 9.92477 4.12069 11.0589C4.12069 12.193 4.17115 12.9616 4.22921 13.4695C4.26972 13.8238 4.51237 14.0691 4.91213 14.1112C5.61223 14.1851 6.76953 14.2586 8.60022 14.2586C10.4309 14.2586 11.5882 14.1851 12.2883 14.1112C12.6881 14.0691 12.9307 13.8238 12.9712 13.4695C13.0293 12.9616 13.0798 12.193 13.0798 11.0589C13.0798 9.92477 13.0293 9.15619 12.9712 8.64831C12.9308 8.29456 12.6875 8.04864 12.2872 8.00642C11.9891 7.97498 11.6076 7.94357 11.1259 7.91789C10.773 7.89908 10.5021 7.59772 10.5209 7.2448C10.5397 6.89187 10.8411 6.62103 11.194 6.63984C11.695 6.66655 12.0987 6.69958 12.4214 6.73361C13.3713 6.8338 14.1291 7.50771 14.2428 8.50295C14.3077 9.07016 14.3596 9.88879 14.3596 11.0589C14.3596 12.229 14.3077 13.0476 14.2428 13.6148C14.1291 14.6095 13.3732 15.2837 12.4227 15.384C11.6667 15.4638 10.4629 15.5384 8.60022 15.5384C6.73752 15.5384 5.5337 15.4638 4.77779 15.384C3.82728 15.2837 3.07133 14.6095 2.95763 13.6148C2.89279 13.0476 2.84082 12.229 2.84082 11.0589C2.84082 9.88879 2.89279 9.07016 2.95763 8.50295C3.0714 7.50771 3.82911 6.8338 4.77903 6.73361C5.10175 6.69958 5.50546 6.66655 6.00642 6.63984C6.35935 6.62103 6.6607 6.89187 6.67952 7.2448Z"
                                                    fill="white"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M6.81509 4.79241C6.56518 5.04232 6.16 5.04232 5.91009 4.79241C5.66018 4.5425 5.66018 4.13732 5.91009 3.88741L8.14986 1.64764C8.39977 1.39773 8.80495 1.39773 9.05486 1.64764L11.2946 3.88741C11.5445 4.13732 11.5445 4.5425 11.2946 4.79241C11.0447 5.04232 10.6395 5.04232 10.3896 4.79241L9.24229 3.64508V9.77934C9.24229 10.1328 8.95578 10.4193 8.60236 10.4193C8.24893 10.4193 7.96242 10.1328 7.96242 9.77934L7.96242 3.64508L6.81509 4.79241Z"
                                                    fill="white"></path>
                                            </svg>
                                            {{ __('Upload ') }}
                                        </label>
                                        <input type="file" name="bank_transfer_invoice" id="bank_transfer_invoice"
                                            class="file-input" style="display: none">
                                        <input type="hidden" name="product_id">
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-6 col-md-4 col-sm-6 col-12">

                                <button type="submit" class="btn payment-btn"
                                    id="bank_transfer">{{ __('Bank Transfer ') }}</button>
                            </div>
                        @endif
                        @if ($store['enable_cod'] == 'on')
                            <div class="col-lg-6 col-md-4 col-sm-6 col-12">
                                <button type="submit" class="btn payment-btn"
                                    id="cash_on_delivery">{{ __('Order on COD') }}</button>
                            </div>
                        @endif
                        @php
                            $paymentMethods = [
                                'stripe' => [
                                    'enabled' =>
                                        isset($store_payments['is_stripe_enabled']) &&
                                        $store_payments['is_stripe_enabled'] == 'on',
                                    'route' => route('stripe.post', $store->slug),
                                    'buttonText' => __('Pay via Stripe'),
                                    'method' => 'POST',
                                    'id' => 'payment-stripe-form',
                                    'id1' => 'owner-stripe',
                                ],
                                'paypal' => [
                                    'enabled' =>
                                        isset($store_payments['is_paypal_enabled']) &&
                                        $store_payments['is_paypal_enabled'] == 'on',
                                    'route' => route('pay.with.paypal', $store->slug),
                                    'buttonText' => __('Pay via PayPal'),
                                    'method' => 'POST',
                                    'id' => 'payment-paypal-form',
                                    'id1' => 'owner-paypal',
                                ],
                                'flutterwave' => [
                                    'enabled' =>
                                        isset($store_payments['is_flutterwave_enabled']) &&
                                        $store_payments['is_flutterwave_enabled'] == 'on',
                                    'buttonText' => __('Pay via Flutterwave'),
                                    'method' => 'POST',
                                    'route' => route('pay.with.flutterwave', $store->slug),
                                    'id' => 'payment-flutterwave-form',
                                    'id1' => 'owner-flutterwave',
                                ],
                                'paytm' => [
                                    'enabled' =>
                                        isset($store_payments['is_paytm_enabled']) &&
                                        $store_payments['is_paytm_enabled'] == 'on',
                                    'route' => route('paytm.prepare.payments', $store->slug),
                                    'buttonText' => __('Pay via Paytm'),
                                    'method' => 'POST',
                                    'id' => 'payment-paytm-form',
                                    'id1' => 'owner-paytm',
                                    'order_id' => str_pad(
                                        !empty($order->id) ? $order->id + 1 : 1,
                                        4,
                                        '100',
                                        STR_PAD_LEFT,
                                    ),
                                    'csrf' => csrf_token(),
                                ],
                                'mercado' => [
                                    'enabled' =>
                                        isset($store_payments['is_mercado_enabled']) &&
                                        $store_payments['is_mercado_enabled'] == 'on',
                                    'route' => route('mercadopago.store.prepare', $store->slug),
                                    'buttonText' => __('Pay via Mercado Pago'),
                                    'method' => 'POST',
                                    'id' => 'payment-mercadopago-form',
                                    'id1' => 'owner-mercadopago',
                                ],
                                'mollie' => [
                                    'enabled' =>
                                        isset($store_payments['is_mollie_enabled']) &&
                                        $store_payments['is_mollie_enabled'] == 'on',
                                    'route' => route('mollie.prepare.payments', $store->slug),
                                    'buttonText' => __('Pay via Mollie'),
                                    'id' => 'payment-mollie-form',
                                    'id1' => 'owner-mollie',
                                    'method' => 'POST',
                                ],
                                'skrill' => [
                                    'enabled' =>
                                        isset($store_payments['is_skrill_enabled']) &&
                                        $store_payments['is_skrill_enabled'] == 'on',
                                    'route' => route('skrill.prepare.payments', $store->slug),
                                    'buttonText' => __('Pay via Skrill'),
                                    'id' => 'payment-skrill-form',
                                    'id1' => 'owner-skrill',
                                    'method' => 'POST',
                                ],
                                'coingate' => [
                                    'enabled' =>
                                        isset($store_payments['is_coingate_enabled']) &&
                                        $store_payments['is_coingate_enabled'] == 'on',
                                    'route' => route('coingate.prepare', $store->slug),
                                    'buttonText' => __('Pay via Coingate'),
                                    'id' => 'payment-coingate-form',
                                    'id1' => 'owner-coingate',
                                    'method' => 'POST',
                                ],
                                'paymentwall' => [
                                    'enabled' =>
                                        isset($store_payments['is_paymentwall_enabled']) &&
                                        $store_payments['is_paymentwall_enabled'] == 'on',
                                    'route' => route('paymentwall.index', $store->slug),
                                    'buttonText' => __('Pay via Paymentwall'),
                                    'id' => 'payment-paymentwall-form',
                                    'id1' => 'owner-paymentwall',
                                    'method' => 'POST',
                                ],

                                'toyyibpay' => [
                                    'enabled' =>
                                        isset($store_payments['is_toyyibpay_enabled']) &&
                                        $store_payments['is_toyyibpay_enabled'] == 'on',
                                    'route' => route('toyyibpay.prepare.payments', $store->slug),
                                    'buttonText' => __('Pay via Toyyibpay'),
                                    'id' => 'payment-toyyibpay-form',
                                    'id1' => 'owner-toyyibpay',
                                    'method' => 'POST',
                                ],
                                'iyzipay' => [
                                    'enabled' =>
                                        isset($store_payments['is_iyzipay_enabled']) &&
                                        $store_payments['is_iyzipay_enabled'] == 'on',
                                    'route' => route('iyzipay.prepare.payment', $store->slug),
                                    'buttonText' => __('Pay via Iyzipay'),
                                    'id' => 'payment-iyzipay-form',
                                    'id1' => 'owner-iyzipay',
                                    'method' => 'POST',
                                ],
                                'paytab' => [
                                    'enabled' =>
                                        isset($store_payments['is_paytab_enabled']) &&
                                        $store_payments['is_paytab_enabled'] == 'on',
                                    'route' => route('pay.with.paytab', $store->slug),
                                    'buttonText' => __('Pay via Paytab'),
                                    'id' => 'payment-paytab-form',
                                    'id1' => 'owner-paytab',
                                    'method' => 'POST',
                                ],
                                'benefit' => [
                                    'enabled' =>
                                        isset($store_payments['is_benefit_enabled']) &&
                                        $store_payments['is_benefit_enabled'] == 'on',
                                    'route' => route('store.benefit.initiate', $store->slug),
                                    'buttonText' => __('Pay via Benefit'),
                                    'id' => 'payment-benefit-form',
                                    'id1' => 'owner-benefit',
                                    'method' => 'POST',
                                ],
                                'cashfree' => [
                                    'enabled' =>
                                        isset($store_payments['is_cashfree_enabled']) &&
                                        $store_payments['is_cashfree_enabled'] == 'on',
                                    'route' => route('store.cashfree.initiate', $store->slug),
                                    'buttonText' => __('Pay via Cashfree'),
                                    'id' => 'payment-cashfree-form',
                                    'id1' => 'owner-cashfree',
                                    'method' => 'POST',
                                ],
                                'aamarpay' => [
                                    'enabled' =>
                                        isset($store_payments['is_aamarpay_enabled']) &&
                                        $store_payments['is_aamarpay_enabled'] == 'on',
                                    'route' => route('store.pay.aamarpay.payment', $store->slug),
                                    'buttonText' => __('Pay via Aamarpay'),
                                    'id' => 'payment-aamarpay-form',
                                    'id1' => 'owner-aamarpay',
                                    'method' => 'POST',
                                ],
                                'paytr' => [
                                    'enabled' =>
                                        isset($store_payments['is_paytr_enabled']) &&
                                        $store_payments['is_paytr_enabled'] == 'on',
                                    'route' => route('store.pay.paytr.payment', $store->slug),
                                    'buttonText' => __('Pay via PayTR'),
                                    'id' => 'payment-paytr-form',
                                    'id1' => 'owner-paytr',
                                    'method' => 'POST',
                                ],
                                'yookassa' => [
                                    'enabled' =>
                                        isset($store_payments['is_yookassa_enabled']) &&
                                        $store_payments['is_yookassa_enabled'] == 'on',
                                    'route' => route('store.pay.yookassa.payment', $store->slug),
                                    'buttonText' => __('Pay via Yookassa'),
                                    'id' => 'payment-yookassa-form',
                                    'id1' => 'owner-yookassa',
                                    'method' => 'POST',
                                ],
                                'midtrans' => [
                                    'enabled' =>
                                        isset($store_payments['is_midtrans_enabled']) &&
                                        $store_payments['is_midtrans_enabled'] == 'on',
                                    'route' => route('store.pay.midtrans.payment', $store->slug),
                                    'buttonText' => __('Pay via Midtrans'),
                                    'id' => 'payment-midtrans-form',
                                    'id1' => 'owner-midtrans',
                                    'method' => 'POST',
                                ],
                                'xendit' => [
                                    'enabled' =>
                                        isset($store_payments['is_xendit_enabled']) &&
                                        $store_payments['is_xendit_enabled'] == 'on',
                                    'route' => route('store.pay.xendit.payment', $store->slug),
                                    'buttonText' => __('Pay via Xendit'),
                                    'id' => 'payment-xendit-form',
                                    'id1' => 'owner-xendit',
                                    'method' => 'POST',
                                ],

                                'fedapay' => [
                                    'enabled' =>
                                        isset($store_payments['is_fedapay_enabled']) &&
                                        $store_payments['is_fedapay_enabled'] == 'on',
                                    'route' => route('store.pay.fedapay.payment', $store->slug),
                                    'buttonText' => __('Pay via Fedapay'),
                                    'id' => 'payment-fedapay-form',
                                    'id1' => 'owner-fedapay',
                                    'method' => 'POST',
                                ],
                                'nepalste' => [
                                    'enabled' =>
                                        isset($store_payments['is_nepalste_enabled']) &&
                                        $store_payments['is_nepalste_enabled'] == 'on',
                                    'route' => route('store.pay.nepalste.payment', $store->slug),
                                    'buttonText' => __('Pay via Nepalste'),
                                    'id' => 'payment-nepalste-form',
                                    'id1' => 'owner-nepalste',
                                    'method' => 'POST',
                                ],
                                'payhere' => [
                                    'enabled' =>
                                        isset($store_payments['is_payhere_enabled']) &&
                                        $store_payments['is_payhere_enabled'] == 'on',
                                    'route' => route('store.pay.payhere.payment', $store->slug),
                                    'buttonText' => __('Pay via Payhere'),
                                    'id' => 'payment-payhere-form',
                                    'id1' => 'owner-payhere',
                                    'method' => 'POST',
                                ],
                                'cinetpay' => [
                                    'enabled' =>
                                        isset($store_payments['is_cinetpay_enabled']) &&
                                        $store_payments['is_cinetpay_enabled'] == 'on',
                                    'route' => route('store.pay.cinetpay.payment', $store->slug),
                                    'buttonText' => __('Pay via Cinetpay'),
                                    'id' => 'payment-cinetpay-form',
                                    'id1' => 'owner-cinetpay',
                                    'method' => 'POST',
                                ],
                                'authorizenet' => [
                                    'enabled' =>
                                        isset($store_payments['is_authorizenet_enabled']) &&
                                        $store_payments['is_authorizenet_enabled'] == 'on',
                                    'route' => route('store.pay.authorizenet.payment', $store->slug),
                                    'buttonText' => __('Pay via AuthorizeNet'),
                                    'id' => 'payment-authorizenet-form',
                                    'id1' => 'owner-authorizenet',
                                    'method' => 'POST',
                                ],
                                'tap' => [
                                    'enabled' =>
                                        isset($store_payments['is_tap_enabled']) &&
                                        $store_payments['is_tap_enabled'] == 'on',
                                    'route' => route('store.pay.with.tap', $store->slug),
                                    'buttonText' => __('Pay via Tap'),
                                    'id' => 'payment-tap-form',
                                    'id1' => 'owner-tap',
                                    'method' => 'POST',
                                ],
                                'ozow' => [
                                    'enabled' =>
                                        isset($store_payments['is_ozow_enabled']) &&
                                        $store_payments['is_ozow_enabled'] == 'on',
                                    'route' => route('store.pay.with.ozow', $store->slug),
                                    'buttonText' => __('Pay via Ozow'),
                                    'id' => 'payment-ozow-form',
                                    'id1' => 'owner-ozow',
                                    'method' => 'POST',
                                ],
                                'paiment_pro' => [
                                    'enabled' =>
                                        isset($store_payments['is_paiment_pro_enabled']) &&
                                        $store_payments['is_paiment_pro_enabled'] == 'on',
                                    'route' => route('store.pay.paimentpro.payment', $store->slug),
                                    'buttonText' => __('Pay via Paiment Pro'),
                                    'id' => 'payment-paiment_pro-form',
                                    'id1' => 'owner-paiment_pro',
                                    'method' => 'POST',
                                ],
                            ];

                            $scriptPaymentMethods = [
                                'paystack' => [
                                    'enabled' =>
                                        isset($store_payments['is_paystack_enabled']) &&
                                        $store_payments['is_paystack_enabled'] == 'on',
                                    'buttonText' => __('Pay via Paystack'),
                                    'onclick' => 'payWithPaystack()',
                                    'method' => 'button',
                                ],

                                'razorpay' => [
                                    'enabled' =>
                                        isset($store_payments['is_razorpay_enabled']) &&
                                        $store_payments['is_razorpay_enabled'] == 'on',
                                    'buttonText' => __('Pay via Razorpay'),
                                    'onclick' => 'payRazorPay()',
                                    'method' => 'button',
                                ],
                                'payfast' => [
                                    'enabled' =>
                                        isset($store_payments['is_payfast_enabled']) &&
                                        $store_payments['is_payfast_enabled'] == 'on',
                                    'buttonText' => __('Pay via Payfast'),
                                    'onclick' => 'payPayfast()',
                                    'method' => 'button',
                                ],
                                'khalti' => [
                                    'enabled' =>
                                        isset($store_payments['is_khalti_enabled']) &&
                                        $store_payments['is_khalti_enabled'] == 'on',
                                    'buttonText' => __('Pay via Khalti'),
                                    'onclick' => 'payKhalti()',
                                    'method' => 'button',
                                ],
                            ];
                        @endphp

                        @foreach ($paymentMethods as $method => $details)
                            @if ($details['enabled'])
                                <div class="col-lg-6 col-md-4 col-sm-6 col-12">
                                    <form method="{{ $details['method'] }}" action="{{ $details['route'] }}"
                                        id="{{ $details['id'] }}">
                                        @csrf
                                        <input type="hidden" name="id"
                                            value="{{ date('Y-m-d') }}-{{ strtotime(date('Y-m-d H:i:s')) }}-payatm">
                                        <input type="hidden" name="order_id"
                                            value="{{ str_pad(!empty($order->id) ? $order->id + 1 : 0 + 1, 4, '100', STR_PAD_LEFT) }}">
                                        <input type="hidden" name="type" class="customer_type">
                                        <input type="hidden" name="coupon_id" class="customer_coupon_id">
                                        <input type="hidden" name="dicount_price" class="customer_dicount_price">
                                        <input type="hidden" name="shipping_price" class="customer_shipping_price">
                                        <input type="hidden" name="shipping_name" class="customer_shipping_name">
                                        <input type="hidden" name="shipping_id" class="customer_shipping_id">
                                        <input type="hidden" name="total_price" class="customer_total_price">
                                        <input type="hidden" name="product" class="customer_product">
                                        <input type="hidden" name="order_id" class="customer_order_id">
                                        <input type="hidden" name="name" class="customer_name">
                                        <input type="hidden" name="email" class="customer_email">
                                        <input type="hidden" name="phone" class="customer_phone">
                                        <input type="hidden" name="custom_field_title_1"
                                            class="customer_custom_field_title_1">
                                        <input type="hidden" name="custom_field_title_2"
                                            class="customer_custom_field_title_2">
                                        <input type="hidden" name="custom_field_title_3"
                                            class="customer_custom_field_title_3">
                                        <input type="hidden" name="custom_field_title_4"
                                            class="customer_custom_field_title_4">
                                        <input type="hidden" name="billing_address"
                                            class="customer_billing_address">
                                        <input type="hidden" name="shipping_address"
                                            class="customer_shipping_address">
                                        <input type="hidden" name="special_instruct"
                                            class="customer_special_instruct">
                                        @php
                                            $skrill_data = [
                                                'transaction_id' => md5(
                                                    date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id',
                                                ),
                                                'user_id' => 'user_id',
                                                'amount' => 'amount',
                                                'currency' => 'currency',
                                            ];
                                            session()->put('skrill_data', $skrill_data);
                                        @endphp

                                        <button type="submit" class="btn payment-btn" id="{{ $details['id1'] }}">
                                            {{ $details['buttonText'] }}
                                        </button>
                                    </form>

                                </div>
                            @endif
                        @endforeach
                        @if (isset($store_payments['is_paiment_pro_enabled']) && $store_payments['is_paiment_pro_enabled'] == 'on')
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Mobile Number') }}</label>
                                    <input type="text" id="mobile_number" name="mobile_number"
                                        class="form-control paimentpro_mobile_number" data-from="mobile_number"
                                        placeholder="{{ __('Enter Mobile Number') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Channel') }}</label>

                                    <input type="text" id="channel" name="channel"
                                        class="form-control paimentpro_channel" data-from="channel"
                                        placeholder="{{ __('Enter Channel') }}" required>
                                    <small
                                        class="example">{{ __('Example : OMCIV2,MOMO,CARD,FLOOZ ,PAYPAL') }}</small>
                                </div>
                            </div>
                        @endif
                        @foreach ($scriptPaymentMethods as $method => $details)
                            @if ($details['enabled'])
                                <div class="col-lg-6 col-md-4 col-sm-6 col-12">
                                    @if ($method === 'paystack')
                                        <button type="button" class="btn payment-btn"
                                            onclick="{{ $details['onclick'] }}">
                                            {{ $details['buttonText'] }}
                                        </button>
                                    @elseif ($method === 'razorpay')
                                        <button type="button" class="btn payment-btn"
                                            onclick="{{ $details['onclick'] }}">
                                            {{ $details['buttonText'] }}
                                        </button>
                                    @elseif ($method === 'payfast')
                                        @php
                                            $pfHost =
                                                $store_payments['payfast_mode'] == 'sandbox'
                                                    ? 'sandbox.payfast.co.za'
                                                    : 'www.payfast.co.za';
                                        @endphp
                                        <form role="form" class="payfast-form"
                                            action={{ 'https://' . $pfHost . '/eng/process' }} method="post"
                                            class="require-validation" id="payfast-form">
                                            <div class="card-btn">
                                                <div id="get-payfast-inputs"></div>
                                                <input type="hidden" name="order_id" id="order_id"
                                                    value="{{ \Illuminate\Support\Facades\Crypt::encrypt($order_id) }}">
                                                <button type="button" class="btn payment-btn"
                                                    onclick="payPayfast()">
                                                    {{ $details['buttonText'] }}
                                                </button>
                                            </div>
                                        </form>
                                    @elseif ($method === 'khalti')
                                        <button type="button" class="btn payment-btn"
                                            onclick="{{ $details['onclick'] }}">
                                            {{ $details['buttonText'] }}
                                        </button>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (isset($store_payments['is_razorpay_enabled']) && $store_payments['is_razorpay_enabled'] == 'on')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        function payRazorPay() {
            const razorPayCallback = "{{ url('razorpay') }}";
            const slug = "{{ $store->slug }}";
            const orderId = '{{ time() }}';
            const totalPrice = $('.final_total_price').html().replace("{{ $store->currency }}", "").trim();
            const ajaxData = getPaymentData('razorpay', totalPrice, orderId);

            $.ajax({
                url: '{{ route('Payment.with.Razorpay', [$store->slug]) }}',
                method: 'POST',
                data: ajaxData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.status === 'success') {
                        const options = {
                            key: "{{ $store_payments['razorpay_public_key'] }}", // Razorpay Key Id
                            amount: Math.round(data.price * 100),
                            currency: '{{ $store->currency }}',
                            name: '{{ $store->name }}',
                            description: `Order ID: ${orderId}`,
                            image: "{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') }}",
                            handler: function(response) {
                                window.location.href =
                                    `${razorPayCallback}/${slug}/${response.razorpay_payment_id}/${orderId}?status=${data.status}`;
                            },
                            theme: {
                                color: "#528FF0"
                            }

                        };
                        const razorpay = new Razorpay(options);
                        razorpay.open();
                    } else {
                        show_toastr("Error", data.message, data.status);
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }
    </script>
@endif
@if (isset($store_payments['is_paystack_enabled']) && $store_payments['is_paystack_enabled'] == 'on')
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function payWithPaystack() {
            const paystackCallback = "{{ url('/paystack') }}";
            const slug = "{{ $store->slug }}";
            const orderId = '{{ !empty($order->id) ? $order->id + 1 : time() }}';
            const totalPrice = $('.final_total_price').html().replace("{{ $store->currency }}", "").trim();
            var price = totalPrice.replace(/^\$/, '');
            const ajaxData = getPaymentData('paystack', totalPrice, orderId);

            $.ajax({
                url: '{{ route('pay.with.paystack', [$store->slug]) }}',
                method: 'POST',
                data: ajaxData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.status === 'success') {
                        const handler = PaystackPop.setup({
                            key: '{{ $store_payments['paystack_public_key'] }}',
                            email: ajaxData.email,
                            amount: Math.round(data.price * 100),
                            currency: '{{ $store->currency }}',
                            ref: `pay_ref_id${Math.floor(Math.random() * 1e9 + 1)}`,
                            metadata: {
                                custom_fields: [{
                                    display_name: "Mobile Number",
                                    variable_name: "mobile_number",
                                    value: ajaxData.phone
                                }]
                            },
                            callback: function(response) {
                                window.location.href =
                                    `${paystackCallback}/${slug}/${response.reference}/${orderId}?status=${data.status}`;
                            },

                            onClose: function() {
                                alert('Window closed');
                            }
                        });
                        handler.openIframe();
                    } else {
                        show_toastr("Error", data.message, data.status);
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }
    </script>
@endif
<script>
    function payPayfast() {
        const orderId = '{{ time() }}';
        const totalPrice = $('.final_total_price').html().replace("{{ $store->currency }}", "").trim();
        const ajaxData = getPaymentData('payfast', totalPrice, orderId);

        $.ajax({
            url: '{{ route('payfast', $store->slug) }}',
            method: 'POST',
            data: ajaxData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {

                if (data.success == true) {
                    $('#get-payfast-inputs').append(data.inputs);
                    $('.payfast-form').submit();
                } else {
                    show_toastr('Error', data.success, 'error')
                }
            }
        });
    }
</script>
<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>

<script>
    var config = {
        "publicKey": "{{ isset($store_payments['khalti_public_key']) ? $store_payments['khalti_public_key'] : '' }}",
        "productIdentity": "1234567890",
        "productName": "demo",
        "productUrl": "{{ env('APP_URL') }}",
        "paymentPreference": [
            "KHALTI",
            "EBANKING",
            "MOBILE_BANKING",
            "CONNECT_IPS",
            "SCT",
        ],
        "eventHandler": {
            onSuccess(payload) {
                if (payload.status == 200) {
                    var order_id = '{{ $order_id = '#' . time() }}';
                    var coupon_id = $('.hidden_coupon').attr('data_id');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-Token': '{{ csrf_token() }}'
                        }
                    });
                    var returndata = {
                        payload: payload,
                        coupon_id: coupon_id,
                        order_id: order_id,
                    }
                    $.ajax({
                        url: "{{ route('store.get.khalti.status', $store->slug) }}",
                        method: 'POST',
                        data: returndata,
                        success: function(data) {

                            if (data.status == 'success') {
                                show_toastr(data["status"], data["success"], data["status"]);
                                setTimeout(() => {
                                    var url =
                                        '{{ route('store-complete.complete', [$store->slug, ':id']) }}';
                                    url = url.replace(':id', data["order_id"]);
                                    window.location.href = url;
                                }, 1000);
                            } else {
                                show_toastr('error', 'Payment Failed', 'error');
                            }
                        },
                        error: function(err) {
                            show_toastr('error', err.response, 'error');
                        },
                    });
                }
            },
            onError(error) {
                show_toastr('error', error, 'error')
            },
            onClose() {}
        }

    };

    function payKhalti() {
        const orderId = '{{ time() }}';
        const totalPrice = $('.final_total_price').html().replace("{{ $store->currency }}", "").trim();

        const ajaxData = getPaymentData('khalti', totalPrice, orderId);
        var checkout = new KhaltiCheckout(config);

        $.ajax({
            url: "{{ route('store.pay.with.khalti', $store->slug) }}",
            method: 'POST',
            data: ajaxData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.status == 'success') {
                    show_toastr('success', 'Your Order Successfully Added', 'success');
                    setTimeout(() => {
                        const orderId = data.order_id;
                        const url =
                            '{{ route('store-complete.complete', [$store->slug, 'ORDER_ID_PLACEHOLDER']) }}'
                            .replace('ORDER_ID_PLACEHOLDER',
                                orderId);

                        window.location.href = url;

                    }, 1000);
                } else {
                    checkout.show({
                        amount: data * 100
                    });
                }
            }
        });
    }
</script>
<script>
    function getPaymentData(type, totalPrice, orderId) {

        return {
            type,
            coupon_id: $('.hidden_coupon').attr('data_id'),

            dicount_price: $('.dicount_price').html(),
            shipping_price: $('.shipping_price').html(),
            shipping_name: $('.change_location').find(":selected").text(),
            shipping_id: $("input[name='shipping_id']:checked").val(),
            total_price: totalPrice,
            order_id: orderId,
            name: $('.detail-form .fname').val(),
            email: $('.detail-form .email').val(),
            phone: $('.detail-form .phone').val(),
            custom_field_title_1: $('.detail-form .custom_field_title_1').val(),
            custom_field_title_2: $('.detail-form .custom_field_title_2').val(),
            custom_field_title_3: $('.detail-form .custom_field_title_3').val(),
            custom_field_title_4: $('.detail-form .custom_field_title_4').val(),
            billing_address: $('.detail-form .billing_address').val(),
            shipping_address: $('.detail-form .shipping_address').val(),
            special_instruct: $('.special_instruct').val()
        };
    }
</script>
