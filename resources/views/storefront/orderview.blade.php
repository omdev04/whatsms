@php
    $s_logo = \App\Models\Utility::get_file('uploads/store_logo/');
@endphp
@foreach ($all_orders as $order_key => $order)
    <div class="modal-body">
        <div class="order-view-body">
            <div class="order-view-header d-flex justify-content-between">
                <div class="section-title">
                    <h2 class="order">{{ __('Items from Order') }}</h2>
                    <span>{{ $order->order_id }}</span>
                </div>
                <div class="sub-header">
                    <span class="badge">
                        {{ $order->status }}:
                        {{ \App\Models\Utility::dateFormat($order->created_at) }}
                    </span>
                    <a href="#" onclick="saveAsPDF();" data-bs-toggle="tooltip"
                        data-bs-original-title="{{ __('Download') }}" class="print-btn">
                        <span>
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20.2889 5.75395H17.9556V1.50971C17.9556 1.10473 17.6272 0.776367 17.2222 0.776367H4.77778C4.3728 0.776367 4.04443 1.10469 4.04443 1.50971V5.75395H1.7111C0.767594 5.75395 0 6.52159 0 7.4651V14.8429C0 15.7864 0.767594 16.554 1.7111 16.554H4.04456V20.4906C4.04456 20.8955 4.37289 21.2239 4.77791 21.2239H17.2221C17.6271 21.2239 17.9554 20.8956 17.9554 20.4906V16.554H20.2889C21.2324 16.554 22 15.7864 22 14.8429V7.4651C22 6.52163 21.2324 5.75395 20.2889 5.75395ZM5.51109 2.24306H16.4889V5.75395H5.51109V2.24306ZM16.4887 19.7572H5.51126C5.51126 19.6139 5.51126 13.9348 5.51126 13.7576H16.4888C16.4887 13.9393 16.4887 19.6194 16.4887 19.7572ZM17.2222 10.0601H15.3555C14.9505 10.0601 14.6222 9.73174 14.6222 9.32672C14.6222 8.92169 14.9505 8.59337 15.3555 8.59337H17.2222C17.6272 8.59337 17.9556 8.92169 17.9556 9.32672C17.9556 9.73174 17.6272 10.0601 17.2222 10.0601Z"
                                    fill="#060606" />
                            </svg>
                        </span> {{ __('Print') }}</a>
                </div>
            </div>
            <div id="printableArea">
                <div class="order-view-details d-flex">
                    <div class="order-view-left">
                        <div class="table-responsive">
                            <table class="order-view">
                                <thead>
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
                                    @foreach ($order->order_products->products as $key => $product)
                                        @if ($product->variant_id != 0)
                                            <tr>
                                                <td>
                                                    {{ $product->product_name . ' - ( ' . $product->variant_name . ' )' }}
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
                                                <td>{{ $product->quantity }}</td>
                                                <td>
                                                    {{ App\Models\Utility::priceFormat($product->variant_price) }}
                                                </td>
                                                <td>
                                                    {{ App\Models\Utility::priceFormat($product->variant_price * $product->quantity + $total_tax) }}
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>
                                                    {{ $product->product_name }}
                                                    @if (!empty($product->tax))
                                                        @php
                                                            $total_tax = 0;
                                                        @endphp
                                                        @foreach ($product->tax as $tax)
                                                            @php
                                                                $sub_tax =
                                                                    ($product->price * $product->quantity * $tax->tax) /
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
                                                <td>{{ $product->quantity }}</td>
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
                            </table>
                        </div>
                    </div>
                    <div class="order-view-right">
                        <div class="order-subtotal">
                            <ul>
                                <li>
                                    <span class="sum-left">{{ __('Sub Total') }} :</span>
                                    <span
                                        class="sum-right">{{ App\Models\Utility::priceFormat($order->sub_total) }}</span>
                                </li>
                                <li>
                                    <span class="sum-left">{{ __('Estimated Tax') }} :</span>
                                    <span
                                        class="sum-right">{{ App\Models\Utility::priceFormat($order->final_taxs) }}</span>
                                </li>
                                @if (!empty($order->discount_price))
                                    <li>
                                        <span class="sum-left">{{ __('Apply Coupon') }} :</span>
                                        <span class="sum-right">{{ $order->discount_price }}</span>
                                    </li>
                                @endif
                                @if (!empty($order->shipping_data))
                                    @if (!empty($order->discount_value))
                                        <li>
                                            <span class="sum-left">{{ __('Shipping Price') }} :</span>
                                            <span
                                                class="sum-right">{{ App\Models\Utility::priceFormat($order->shipping_data->shipping_price) }}</span>
                                        </li>
                                        <li>
                                            <span class="sum-left">{{ __('Grand Total') }} :</span>
                                            <span
                                                class="sum-right">{{ App\Models\Utility::priceFormat($order->grand_total + $order->shipping_data->shipping_price - $order->discount_value) }}</span>
                                        </li>
                                        <li>
                                            <span class="sum-left">{{ __('Payment Type') }} :</span>
                                            <span class="sum-right">{{ $order['payment_type'] }}</span>
                                        </li>
                                    @else
                                        <li>
                                            <span class="sum-left">{{ __('Shipping Price') }} :</span>
                                            <span
                                                class="sum-right">{{ App\Models\Utility::priceFormat($order->shipping_data->shipping_price) }}</span>
                                        </li>
                                        <li>
                                            <span class="sum-left">{{ __('Grand Total') }} :</span>
                                            <span
                                                class="sum-right">{{ App\Models\Utility::priceFormat($order->grand_total + $order->shipping_data->shipping_price) }}</span>
                                        </li>
                                        <li>
                                            <span class="sum-left">{{ __('Payment Type') }} :</span>
                                            <span class="sum-right">{{ $order['payment_type'] }}</span>
                                        </li>
                                    @endif
                                @elseif(!empty($order->discount_value))
                                    <li>
                                        <span class="sum-left">{{ __('Grand Total') }} :</span>
                                        <span
                                            class="sum-right">{{ App\Models\Utility::priceFormat($order->grand_total - $order->discount_value) }}</span>
                                    </li>
                                    <li>
                                        <span class="sum-left">{{ __('Payment Type') }} :</span>
                                        <span class="sum-right">{{ $order['payment_type'] }}</span>
                                    </li>
                                @else
                                    <li>
                                        <span class="sum-left">{{ __('Grand Total') }} :</span>
                                        <span
                                            class="sum-right">{{ App\Models\Utility::priceFormat($order->price) }}</span>
                                    </li>
                                    <li>
                                        <span class="sum-left">{{ __('Payment Type') }} :</span>
                                        <span class="sum-right">{{ $order['payment_type'] }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="order-view-footer">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-6 col-12 footer-info">
                            <h3>{{ __('Shipping Information') }}</h3>
                            <ul>
                                <li>
                                    <span class="li-left">{{ __('Name') }}</span>
                                    <span class="li-right">{{ $order->user_details->name }}</span>
                                </li>
                                <li>
                                    <span class="li-left">{{ __('Phone') }}</span>
                                    <span class="li-right">{{ $order->user_details->phone }}</span>
                                </li>
                                <li>
                                    <span class="li-left">{{ __('Billing Address') }}</span>
                                    <span class="li-right">{{ $order->user_details->billing_address }}</span>
                                </li>
                                <li>
                                    <span class="li-left">{{ __('Shipping Address') }}</span>
                                    <span class="li-right">{{ $order->user_details->shipping_address }}</span>
                                </li>
                                <li>
                                    <span class="li-left">{{ __('Location') }}</span>
                                    <span class="li-right">{{ $order->location_data->name }}</span>
                                </li>
                                <li>
                                    <span class="li-left">{{ __('Shipping Method') }}</span>
                                    <span class="li-right">{{ $order->shipping_data->shipping_name }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-12 footer-info">
                            <h3>{{ __('Billing Information') }}</h3>
                            <ul>
                                <li>
                                    <span class="li-left">{{ __('Name') }}</span>
                                    <span class="li-right">{{ $order->user_details->name }}</span>
                                </li>
                                <li>
                                    <span class="li-left">{{ __('Phone') }}</span>
                                    <span class="li-right">{{ $order->user_details->phone }}</span>
                                </li>
                                <li>
                                    <span class="li-left">{{ __('Billing Address') }}</span>
                                    <span class="li-right">{{ $order->user_details->billing_address }}</span>
                                </li>
                                <li>
                                    <span class="li-left">{{ __('Shipping Address') }}</span>
                                    <span class="li-right">{{ $order->user_details->shipping_address }}</span>
                                </li>
                                @if (!empty($order->location_data && $order->shipping_data))
                                    <li>
                                        <span class="li-left">{{ __('Location') }}</span>
                                        <span class="li-right">{{ $order->location_data->name }}</span>
                                    </li>
                                    <li>
                                        <span class="li-left">{{ __('Shipping Method') }}</span>
                                        <span class="li-right">{{ $order->shipping_data->shipping_name }}</span>
                                    </li>
                                @endif
                            </ul>
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
                            <div class="col-lg-4 col-12 footer-info">
                                <h3>{{ __('Extra Information') }}</h3>
                                <ul>
                                    <li>
                                        <span class="li-left">{{ $store['custom_field_title_1'] }}</span>
                                        <span class="li-right">{{ $order->user_details->custom_field_title_1 }}</span>
                                    </li>
                                    <li>
                                        <span class="li-left">{{ $store['custom_field_title_2'] }}</span>
                                        <span class="li-right">{{ $order->user_details->custom_field_title_2 }}</span>
                                    </li>
                                    <li>
                                        <span class="li-left">{{ $store['custom_field_title_3'] }}</span>
                                        <span class="li-right">{{ $order->user_details->custom_field_title_3 }}</span>
                                    </li>
                                    <li>
                                        <span class="li-left"> {{ $store['custom_field_title_4'] }}</span>
                                        <span class="li-right">{{ $order->user_details->custom_field_title_4 }}</span>
                                    </li>

                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script type="text/javascript" src="{{ asset('custom/js/html2pdf.bundle.min.js') }}"></script>
<script>
    var filename = $('#filesname').val();

    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var logo_html = $('#invoice_logo_img').html();
        $('.invoice_logo').empty();
        $('.invoice_logo').html(logo_html);

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
        setTimeout(function() {
            $('.invoice_logo').empty();
        }, 0);
    }

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
