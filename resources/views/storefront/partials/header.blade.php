<header class="site-header">
    <div class="main-navigationbar d-flex align-items-center">
        <div class="logo-col">
            <h1>
                <a href="">
                    <img
                        src="{{ $s_logo . (isset($store_settings['logo']) && !empty($store_settings['logo']) ? $store_settings['logo'] : 'logo.png') . '?timestamp=' . time() }}">
                </a>
            </h1>
        </div>
        <div class="navigationbar-row d-flex align-items-center justify-content-between">
            <div class="navigation-left">
                <h2>{{ $store->name }}</h2>
                <p>{{ $store->storeAddress($store->address) }}
                    {{ $store->storeAddress($store->city) }}
                    {{ $store->storeAddress($store->state) }}
                    {{ $store->storeAddress($store->country, 'country') }}</p>
            </div>
            <div class="navigation-right d-flex align-items-center">
                @if ($store->theme_dir == 'theme1' || $store->theme_dir == 'theme2')
                    <div class="search-bar">
                        <div class="search-drp-btn btn" id="btn-search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"
                                fill="none">
                                <path
                                    d="M11.1751 10.2099L8.60234 7.63711C9.22175 6.81252 9.55613 5.80882 9.555 4.7775C9.555 2.1432 7.4118 0 4.7775 0C2.1432 0 0 2.1432 0 4.7775C0 7.4118 2.1432 9.555 4.7775 9.555C5.80882 9.55613 6.81252 9.22175 7.63711 8.60234L10.2099 11.1751C10.3401 11.2915 10.51 11.3537 10.6846 11.3488C10.8592 11.3439 11.0253 11.2724 11.1488 11.1488C11.2724 11.0253 11.3439 10.8592 11.3488 10.6846C11.3537 10.51 11.2915 10.3401 11.1751 10.2099ZM1.365 4.7775C1.365 4.10257 1.56514 3.4428 1.94011 2.88162C2.31508 2.32043 2.84804 1.88305 3.47159 1.62476C4.09515 1.36648 4.78129 1.2989 5.44325 1.43057C6.10521 1.56224 6.71325 1.88725 7.1905 2.3645C7.66775 2.84174 7.99276 3.44979 8.12443 4.11175C8.2561 4.77371 8.18852 5.45985 7.93024 6.08341C7.67196 6.70696 7.23457 7.23992 6.67338 7.61489C6.1122 7.98986 5.45243 8.19 4.7775 8.19C3.87278 8.18891 3.00543 7.82904 2.3657 7.1893C1.72596 6.54957 1.36609 5.68222 1.365 4.7775Z"
                                    fill="#060606" />
                            </svg>
                        </div>
                        <form action="#" class="header-search-form">
                            <div class="form-input d-flex align-items-center">
                                <button class="search-btn" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                        viewBox="0 0 12 12" fill="none">
                                        <path
                                            d="M11.1751 10.2099L8.60234 7.63711C9.22175 6.81252 9.55613 5.80882 9.555 4.7775C9.555 2.1432 7.4118 0 4.7775 0C2.1432 0 0 2.1432 0 4.7775C0 7.4118 2.1432 9.555 4.7775 9.555C5.80882 9.55613 6.81252 9.22175 7.63711 8.60234L10.2099 11.1751C10.3401 11.2915 10.51 11.3537 10.6846 11.3488C10.8592 11.3439 11.0253 11.2724 11.1488 11.1488C11.2724 11.0253 11.3439 10.8592 11.3488 10.6846C11.3537 10.51 11.2915 10.3401 11.1751 10.2099ZM1.365 4.7775C1.365 4.10257 1.56514 3.4428 1.94011 2.88162C2.31508 2.32043 2.84804 1.88305 3.47159 1.62476C4.09515 1.36648 4.78129 1.2989 5.44325 1.43057C6.10521 1.56224 6.71325 1.88725 7.1905 2.3645C7.66775 2.84174 7.99276 3.44979 8.12443 4.11175C8.2561 4.77371 8.18852 5.45985 7.93024 6.08341C7.67196 6.70696 7.23457 7.23992 6.67338 7.61489C6.1122 7.98986 5.45243 8.19 4.7775 8.19C3.87278 8.18891 3.00543 7.82904 2.3657 7.1893C1.72596 6.54957 1.36609 5.68222 1.365 4.7775Z"
                                            fill="#060606" />
                                    </svg>
                                </button>
                                <input type="search" id="search" class="form-control" placeholder="Search">
                            </div>
                        </form>
                    </div>
                @endif
                @if (Utility::CustomerAuthCheck($store->slug) == true)
                    <div class="user-drp">
                        <a href="javascript:void(0);" class="btn login-modal-btn">
                            <span>{{ ucFirst(Auth::guard('customers')->user()->name) }}</span>
                            <span class="login-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16"
                                fill="none">
                                <path
                                    d="M6.99959 8.2694C8.92743 8.2694 10.4953 6.22348 10.4953 4.29596C10.4953 2.36828 8.92743 0.799805 6.99959 0.799805C5.07207 0.799805 3.50391 2.36812 3.50391 4.2958C3.50407 6.22348 5.07223 8.2694 6.99959 8.2694Z"
                                    fill="white" />
                                <path
                                    d="M9.62446 8.30518C8.94622 8.82182 8.1003 9.12934 7.18398 9.12934H6.81598C5.89966 9.12934 5.05358 8.82182 4.3755 8.30518C2.16142 8.6631 0.470703 10.5823 0.470703 12.8972C0.470703 14.1689 3.3939 15.1999 6.99998 15.1999C10.6061 15.1999 13.5293 14.1689 13.5293 12.8972C13.5293 10.5823 11.8384 8.6631 9.62446 8.30518Z"
                                    fill="white" />
                            </svg>
                            </span>
                        </a>
                        <div class="menu-dropdown">
                            <ul>
                                <li><a href="{{ route('store.slug', $store->slug) }}">{{ __('My Dashboard') }}</a>
                                </li>
                                <li>
                                    <a href="#"
                                        data-url="{{ route('customer.profile', [$store->slug, \Illuminate\Support\Facades\Crypt::encrypt(Auth::guard('customers')->user()->id)]) }}"
                                        data-ajax-popup="true" data-title="{{ __('My Profile') }}" class="my-profile"
                                        data-size="lg">{{ __('My Profile') }}</a>
                                </li>
                                <li><a href="#" id="myproducts" data-val="myproducts">{{ __('My Orders') }}</a>
                                </li>
                                <li>
                                    {{-- <a href="#">{{('Logout')}}</a></li> --}}
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('customer-frm-logout').submit();">{{ __('Logout') }}
                                    </a>
                                    <form id="customer-frm-logout"
                                        action="{{ route('customer.logout', $store->slug) }}" method="POST"
                                        class="d-none">
                                        {{ csrf_field() }}
                                    </form>
                            </ul>
                        </div>
                    </div>
                @else
                    <style>
                        .header-right .user-drp>a::before {
                            display: none !important;
                        }
                    </style>
                    <div class="user-drp">
                        <a data-url="{{ route('customer.login', $store->slug) }}" data-ajax-popup="true"
                            data-title="{{ __('Login') }}" data-toggle="modal" id="login-BTN" data-size="md"
                            class="btn login-modal-btn">
                            <span> {{ __('Log in') }}</span>
                            <span class="login-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16"
                                    viewBox="0 0 14 16" fill="none">
                                    <path
                                        d="M6.99959 8.2694C8.92743 8.2694 10.4953 6.22348 10.4953 4.29596C10.4953 2.36828 8.92743 0.799805 6.99959 0.799805C5.07207 0.799805 3.50391 2.36812 3.50391 4.2958C3.50407 6.22348 5.07223 8.2694 6.99959 8.2694Z"
                                        fill="white" />
                                    <path
                                        d="M9.62446 8.30518C8.94622 8.82182 8.1003 9.12934 7.18398 9.12934H6.81598C5.89966 9.12934 5.05358 8.82182 4.3755 8.30518C2.16142 8.6631 0.470703 10.5823 0.470703 12.8972C0.470703 14.1689 3.3939 15.1999 6.99998 15.1999C10.6061 15.1999 13.5293 14.1689 13.5293 12.8972C13.5293 10.5823 11.8384 8.6631 9.62446 8.30518Z"
                                        fill="white" />
                                </svg>
                            </span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>
