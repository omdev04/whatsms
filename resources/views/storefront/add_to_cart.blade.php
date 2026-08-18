@php
    $logo = \App\Models\Utility::get_file('uploads/is_cover_image/');
@endphp
<div class="modal-body d-flex align-items-center">
    <div class="cart-modal-img">
        <a href="#" class="img-ratio">
            <img src="{{ $logo . (isset($products->is_cover) && !empty($products->is_cover) ? $products->is_cover : 'default_img.png') }}"
                alt="product-img">
        </a>
    </div>
    <div class="cart-modal-info">
        <h3>{{ $products->name }}</h3>
        @php
            $variants = json_decode($products['variants_json'], true);

        @endphp

        <div class="cart-variable" id="product-variant-id-{{ $products['variant_id'] }}">
            @if ($products->enable_product_variant == 'on')
                <p>
                    {{ $variants[0]['variant_name'] }} : {{ $variants[0]['variant_options'][0] }}
                </p>
            @endif
            <p>{{ isset($products->categories) && !empty($products->categories) ? $products->categories->name : '' }}
            </p>
        </div>
    </div>
</div>
