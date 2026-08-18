@php
    \App::setLocale(isset($store->lang) ? $store->lang : 'en');
    $logo = \App\Models\Utility::get_file('uploads/is_cover_image/');
@endphp
<div class="products-container">
    @if ($flag == 'my_orders')
        <div id="tab-1" class="tabs-container active purchased-list">
            <div class="tab-content-body order-list-wrapper">
                <div id="category-tab-1" class="tab-content active">

                    <div class="order-list-topbar d-flex align-items-center justify-content-between">
                        <div class="order-list-left">
                            <h2>{{ __('Order List') }}</h2>
                        </div>
                        <div class="order-list-right">
                            <div class="back-btn-wrapper">
                                <a href="{{ route('store.slug', $store->slug) }}"
                                    class="btn btn-primary backhome">{{ __('Back to home') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="order-list">
                            <tbody>
                                <tr>
                                    <th>{{ __('Order') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <th>{{ __('Payment type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                                @foreach ($orders as $order_key => $order_items)
                                    <tr>
                                        <td>
                                            <a href="#" data-size="lg"
                                                data-url="{{ route('store.product.product_order_view', [$order_items->id, Auth::guard('customers')->user()->id, $store->slug]) }}"
                                                data-title="{{ $order_items->order_id }}" data-ajax-popup="true">
                                                <span class="btn-inner-text">{{ $order_items->order_id }}</span>
                                            </a>
                                        </td>
                                        <td>{{ \App\Models\Utility::dateFormat($order_items->created_at) }}</td>
                                        <td>{{ \App\Models\Utility::priceFormat($order_items->price) }}</td>
                                        <td>{{ $order_items->payment_type }}</td>
                                        <td>
                                            <span class="badge">
                                                {{ $order_items->status }}:
                                                {{ \App\Models\Utility::dateFormat($order_items->created_at) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="ac-viewbtn"
                                                data-url="{{ route('store.product.product_order_view', [$order_items->id, Auth::guard('customers')->user()->id, $store->slug]) }}"
                                                data-ajax-popup="true" class="view-btn order_view"
                                                data-title="{{ __('Your Order Details') }}" data-size="lg">
                                                <svg width="16" height="10" viewBox="0 0 16 10" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M8 0.231445C4.94303 0.231445 2.17081 1.90394 0.125192 4.62052C-0.0417306 4.84309 -0.0417306 5.15402 0.125192 5.37658C2.17081 8.09644 4.94303 9.76893 8 9.76893C11.057 9.76893 13.8292 8.09644 15.8748 5.37985C16.0417 5.15729 16.0417 4.84636 15.8748 4.6238C13.8292 1.90394 11.057 0.231445 8 0.231445ZM8.21929 8.35827C6.19004 8.48592 4.51427 6.81342 4.64191 4.7809C4.74665 3.10513 6.10494 1.74684 7.78071 1.6421C9.80996 1.51446 11.4857 3.18695 11.3581 5.21948C11.2501 6.89198 9.89179 8.25027 8.21929 8.35827ZM8.11783 6.80688C7.02465 6.87561 6.1213 5.97554 6.19331 4.88236C6.24895 3.97902 6.9821 3.24914 7.88545 3.19023C8.97862 3.12149 9.88197 4.02157 9.80996 5.11474C9.75105 6.02136 9.0179 6.75124 8.11783 6.80688Z"
                                                        fill="white" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        @foreach ($products as $key => $items)
            <div
                class="tab-content {{ $loop->iteration != 1 ? 'd-none' : 'active' }} {{ $loop->iteration }}{!! str_replace(' ', '_', $key) !!} product_tableese">
                <div class="row">
                    @foreach ($items as $product)
                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6 col-12 product_item">
                            <div class="product-card">
                                <div class="product-card-inner">
                                    <div class="product-card-image">
                                        <a href="#" class="img-wrapper" tabindex="0" data-size="lg"
                                            data-url="{{ $flag != 'my_orders' ? route('store.product.product_view', [$store->slug, $product->id]) : route('store.product.product_order_view', [$product->id, Auth::guard('customers')->user()->id, $store->slug]) }}"
                                            data-title="{{ $product->name }}" data-ajax-popup="true">
                                            <img src="{{ $logo . (isset($product->is_cover) && !empty($product->is_cover) ? $product->is_cover : 'default_img.png') }}"
                                                alt="product-image" loading="lazy">
                                        </a>
                                        <div class="pro-btn-wrapper">
                                            <div class="pro-btn">
                                                <a href="#" class="qv-btn" data-size="lg"
                                                    data-url="{{ $flag != 'my_orders' ? route('store.product.product_view', [$store->slug, $product->id]) : route('store.product.product_order_view', [$product->id, Auth::guard('customers')->user()->id, $store->slug]) }}"
                                                    data-title="{{ $product->name }}" data-ajax-popup="true">
                                                    <svg width="18" height="18" viewBox="0 0 18 18"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.14461 12.2355C7.43747 12.2355 6.05176 10.8498 6.05176 9.14266C6.05176 7.43552 7.43747 6.0498 9.14461 6.0498C10.8518 6.0498 12.2375 7.43552 12.2375 9.14266C12.2375 10.8498 10.8518 12.2355 9.14461 12.2355ZM9.14461 7.12123C8.03033 7.12123 7.12319 8.02838 7.12319 9.14266C7.12319 10.2569 8.03033 11.1641 9.14461 11.1641C10.2589 11.1641 11.166 10.2569 11.166 9.14266C11.166 8.02838 10.2589 7.12123 9.14461 7.12123Z"
                                                            fill="white" />
                                                        <path
                                                            d="M9.14445 15.5869C6.45873 15.5869 3.92302 14.0155 2.18016 11.2869C1.42302 10.1083 1.42302 8.18689 2.18016 7.00117C3.93016 4.2726 6.46588 2.70117 9.14445 2.70117C11.823 2.70117 14.3587 4.2726 16.1016 7.00117C16.8587 8.17974 16.8587 10.1012 16.1016 11.2869C14.3587 14.0155 11.823 15.5869 9.14445 15.5869ZM9.14445 3.7726C6.8373 3.7726 4.63016 5.15831 3.0873 7.57974C2.55159 8.41546 2.55159 9.8726 3.0873 10.7083C4.63016 13.1297 6.8373 14.5155 9.14445 14.5155C11.4516 14.5155 13.6587 13.1297 15.2016 10.7083C15.7373 9.8726 15.7373 8.41546 15.2016 7.57974C13.6587 5.15831 11.4516 3.7726 9.14445 3.7726Z"
                                                            fill="white" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-content">
                                        <div class="product-content-top">
                                            <p>{{ $product->SKU }}</p>
                                            <h3><a id="view" href="#" data-size="lg"
                                                    data-url="{{ $flag != 'my_orders' ? route('store.product.product_view', [$store->slug, $product->id]) : route('store.product.product_order_view', [$product->id, Auth::guard('customers')->user()->id, $store->slug]) }}"
                                                    data-title="{{ $product->name }}"
                                                    data-ajax-popup="true">{{ $product->name }}</a></h3>
                                        </div>
                                        <div class="product-content-bottom">
                                            <div class="cart-btn-wrp d-flex align-items-center justify-content-between">
                                                @if ($flag != 'my_orders')
                                                    @if ($product->enable_product_variant == 'on')
                                                        <a href="#!" class="cart-btn btn btn-addcart modal-target"
                                                            data-size="md"
                                                            data-url="{{ route('store-variant.variant', [$store->slug, $product->id]) }}"
                                                           data-ajax-popup="true" data-title="{{ __('Add Variant') }}"
                                                            data-name="custom-addcart" id="add_to_cart">

                                                            <span>{{ __('Add To Cart') }}</span>
                                                        </a>
                                                    @else
                                                        <a href="#!" class="cart-btn btn add_to_cart modal-target"
                                                            id="add_to_cart" data-toggle="tooltip"
                                                            data-id="{{ $product->id }}" data-size="lg"
                                                            data-url="{{ route('add-to-cart.product', [$store->slug, $product->id]) }}"
                                                            @if($product->quantity != 0)  data-title="item" data-ajax-popup="true" @endif>
                                                            <span>{{ __('Add To Cart') }}</span>
                                                        </a>
                                                    @endif
                                                @endif
                                                <div class="price">
                                                    @if ($product->enable_product_variant == 'on')
                                                        <ins>{{ __('In Variant') }}</ins>
                                                    @else
                                                        <ins>{{ \App\Models\Utility::priceFormat($product->price) }}</ins>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
