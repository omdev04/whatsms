@php
    if (!empty(session()->get('lang'))) {
        $currantLang = session()->get('lang');
    } else {
        $currantLang = $store->lang;
    }
    $data = DB::table('settings');
    $languages = \App\Models\Utility::languages();
    $logo = \App\Models\Utility::get_file('uploads/is_cover_image/');
    $p_logo = \App\Models\Utility::get_file('uploads/product_image/');
    $data = $data->where('created_by', '>', 1)->where('store_id', $store->id)->where('name', 'SITE_RTL')->first();
    $settings = Utility::settings();

@endphp
<div class="modal-body">
    <div class="row no-gutters">
        <div class="col-md-6 col-12">
            <div class="qv-slider-wrp">
                <div class="qv-main-slider">
                    @if (!$products_image->isEmpty())
                        @foreach ($products_image as $key => $product)
                            <div class="qv-itm">
                                <div class="qv-itm-img img-ratio">
                                    <img
                                        src="{{ $p_logo . (isset($products_image[$key]->product_images) && !empty($products_image[$key]->product_images) ? $products_image[$key]->product_images : 'default_img.png') }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <img src="{{ $logo . (isset($products->is_cover) && !empty($products->is_cover) ? $products->is_cover : 'default_img.png') }}"
                            alt="product-img">
                    @endif

                </div>
                <div class="qv-thumb-slider">
                    @if (is_object($products_image))
                        @foreach ($products_image as $key => $product)
                            <div class="qv-thumb-itm">
                                <div class="qv-thumb-img img-ratio">
                                    @if (!empty($products_image[$key]->product_images))
                                        <img src="{{ $p_logo . $products_image[$key]->product_images }}">
                                    @else
                                        <img src="{{ $p_logo . $products_image[$key]->product_images }}">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>


        <div class="col-md-6 col-12">
            <div class="qv-right-content">
                <span
                    class="product-label">{{ isset($products->categories) && !empty($products->categories) ? $products->categories->name : '' }}</span>
                <div class="section-title">
                    <h2>{{ $products->name }}</h2>
                </div>

                <div class="price variation_price1">
                    @if ($products->enable_product_variant == 'on')
                        <ins> {{ __('Please Select Variants') }}</ins>
                    @else
                        <ins>{{ \App\Models\Utility::priceFormat($products->price) }}</ins>
                    @endif
                </div>
                <div class="id-wrp d-flex align-items-center">
                    <label>{{ __('ID:') }}</label>
                    <span>{{ __('#') }}{{ $products->SKU }}</span>
                </div>
                @if ($products->enable_product_variant == 'on')
                    <div class="pv-selection">
                        <input type="hidden" id="product_id" value="{{ $products->id }}">
                        <input type="hidden" id="variant_id" value="">
                        <input type="hidden" id="variant_qty" value="">
                        @foreach ($product_variant_names as $key => $variant)
                            <div class="form-group">
                                <label class="form-control-label">
                                    {{ __('Select') . ' ' . $variant->variant_name }}
                                </label>

                                <select name="product[{{ $key }}]" id="pro_variants_name"
                                    class="form-control custom-select variant-selection pro_variants_name{{ $key }}"
                                    data-product-id="{{ $products->id }}"> <!-- Make sure product ID is passed here -->
                                    <option value="0">{{ __('Select') }}</option>
                                    @foreach ($variant->variant_options as $key => $values)
                                        <option value="{{ $values }}">
                                            {{ $values }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="cart-btn-wrp d-flex align-items-center">

                    @if ($products->enable_product_variant == 'on')
                        <a href="#!" type="submit" class="btn cart-btn add_to_cart_variant add_to_cart_display store_item"
                            id="add_to_cart" data-id="{{ $products->id }}">
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

                            {{ __('Add To Cart') }}
                        </a>
                    @else
                        <a href="#!" type="submit" class="btn cart-btn add_to_cart modal-target" id="add_to_cart"
                            data-toggle="tooltip" data-id="{{ $products->id }}" data-size="lg"
                            data-url="{{ route('add-to-cart.product', [$store->slug, $products->id]) }}"
                            @if($products->quantity != 0)  data-title="item" data-ajax-popup="true" @endif>
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

                            {{ __('Add To Cart') }}
                        </a>
                    @endif
                </div>
                <div class="stock-badge d-flex align-items-center">
                    <label>{{ __('Availability:') }}</label>
                    @if ($products->enable_product_variant == 'on')
                        @if ($products->quantity == 0)
                            <span class="variant_stock1">{{ __('Out Of Stock') }}</span>
                        @else
                            <span class="variant_stock1">{{ __('In Stock') }}</span>
                        @endif
                    @else
                        @if ($products->quantity == 0)
                            <span>{{ __('Out Of Stock') }}</span>
                        @else
                            <span>{{ __('In Stock') }}</span>
                        @endif
                    @endif
                </div>

                <div class="qty-badge d-flex align-items-center">
                    <label>{{ __('Quantity') }} <b>{{ __('Avi. Quantity') }}</b></label>
                    <span class="variant_qty">
                        @if ($products->enable_product_variant == 'on')
                            {{ __('0') }}
                        @else
                            {{ $products->quantity }}
                        @endif
                    </span>
                </div>

                <div class="qv-description">
                    <p>{{ strip_tags($products->description) }}</p>
                    <ul>
                        @if (!empty($products->custom_field_1) && !empty($products->custom_value_1))
                            <li>{{ $products->custom_field_1 }} : {{ $products->custom_value_1 }}</li>
                        @endif
                        @if (!empty($products->custom_field_2) && !empty($products->custom_value_2))
                            <li>{{ $products->custom_field_2 }} : {{ $products->custom_value_2 }}</li>
                        @endif
                        @if (!empty($products->custom_field_3) && !empty($products->custom_value_3))
                            <li>{{ $products->custom_field_3 }} : {{ $products->custom_value_3 }}</li>
                        @endif
                        @if (!empty($products->custom_field_4) && !empty($products->custom_value_4))
                            <li>{{ $products->custom_field_4 }} : {{ $products->custom_value_4 }}</li>
                        @endif

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@if (isset($settings['SITE_RTL']) && $settings['SITE_RTL'] == 'on')
    <script src="{{ asset('themes/' . $store->theme_dir . '/js/rtl-custom.js') }}"></script>
@else
    <script src="{{ asset('themes/' . $store->theme_dir . '/js/custom.js') }}"></script>
@endif

<script>
    const storeSlug = "{{ $store->slug }}";

    $(document).on('change', '.variant-selection', function() {
        const selectedOption = $(this).val();
        if (selectedOption !== "0") {
            const productId = $(this).data('product-id');
            // Dynamically construct the data URL with Blade variables
            const dataUrl =
                "{{ route('add-to-cart.product', ['__store_slug', '__product_id', 'variation_id']) }}"
                .replace('__product_id', productId)
                .replace('__store_slug', storeSlug)
                .replace('variation_id', selectedOption);

            $('.add_to_cart_display').attr({
                "data-toggle": "tooltip",
                "data-size": "lg",
                "data-url": dataUrl,
                // "data-ajax-popup": "true"
            });
        }
    });
</script>
