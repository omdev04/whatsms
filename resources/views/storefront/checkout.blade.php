<div class="modal-body">
    <div class="signin-image">
        @include('storefront.' . $store->theme_dir . '.checkout_view')
    </div>
    <div class="login-btn-wrp d-flex">
        <a data-url="{{ route('customer.login', $store->slug) }}" data-ajax-popup="true" data-title="{{ __('Login') }}"
            data-toggle="modal" data-size="md" class="btn login-modal-btn guest-login" id="loginBtn">
            {{ __('sign in') }}
        </a>
        <a href="#footer" class="btn btn-transparent asGuest guest-login">{{ __('Continue as guest') }}</a>
    </div>
</div>
