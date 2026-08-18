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
                <div class="stock-wrp d-flex align-items-center">
                    <div class="id-wrp d-flex align-items-center">
                        <label>{{ __('ID:') }}</label>
                        <span>{{ __('#') }}{{ $products->SKU }}</span>
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
                            id="add_to_cart" data-id="{{ $products->id }}" >
                            {{ __('Add To Cart') }}
                        </a>
                    @else
                        <a href="#!" type="submit" class="btn cart-btn add_to_cart modal-target" id="add_to_cart"
                            data-toggle="tooltip" data-id="{{ $products->id }}" data-size="lg"
                            data-url="{{ route('add-to-cart.product', [$store->slug, $products->id]) }}"
                            @if($products->quantity != 0) data-title="item" data-ajax-popup="true" @endif>
                            {{ __('Add To Cart') }}
                        </a>
                    @endif
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
