@php
    $logo = asset(Storage::url('uploads/logo'));
    $company_favicon = Utility::getValByName('company_favicon');
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        @if (Auth::user()->type == 'super admin')
            {{ config('app.name', 'vCard SaaS') }}
        @else
            {{ Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'vCard SaaS') }}
        @endif
    </title>
    @if (Auth::user()->type == 'super admin')
        <link rel="icon" href="{{ $logo . '/favicon.png' }}" type="image" sizes="16x16">
    @else
        <link rel="icon"
            href="{{ isset($company_favicon) && !empty($company_favicon) ? asset(Storage::url($company_favicon)) : 'favicon.png' }}"
            type="image" sizes="16x16">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@php
    $plandata = App\Models\Plan::find($pre_pay->plan_id);
@endphp
<script src="https://api.paymentwall.com/brick/build/brick-default.1.5.0.min.js"></script>
<div id="payment-form-container"> </div>
<script>
    var brick = new Brick({
        public_key: '{{ isset($pre_pay->settings['paymentwall_public_key']) ? $pre_pay->settings['paymentwall_public_key'] : '' }}',
        amount: {{ $pre_pay->price }},
        currency: '{{ $pre_pay->currency }}',
        container: 'payment-form-container',
        action: '{{ route('plan.pay.with.paymentwall', ['plan_id' => $pre_pay->plan_id, 'coupon_id' => $pre_pay->coupon_id, 'amount' => $pre_pay->price, 'status' => 'success']) }}',
        success_url: '{{ route('plans.index') }}',
        form: {
            merchant: 'Paymentwall',
            product: '{{ $pre_pay->plan->name }}',
            pay_button: 'Pay',
            show_zip: true,
            show_cardholder: true
        }
    });

    brick.showPaymentForm(function(data) {

        if (errors.flag == 1) {
            window.location.href = '{{ route('callback.error', 1) }}';
        } else {
            window.location.href = '{{ route('callback.error', 2) }}';
        }
    }, function(errors) {
        if (errors.flag == 1) {
            window.location.href = '{{ route('callback.error', 1) }}';
        } else {
            window.location.href = '{{ route('callback.error', 2) }}';
        }
    });
</script>
