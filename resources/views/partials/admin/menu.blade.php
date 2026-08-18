@php
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::GetLogo();
    // $setting = Utility::settings();
    //    if(\Auth::user()->type=="Super Admin")
    //     {
    //         $company_logo=Utility::get_superadmin_logo();
    //     }
    //     else
    //     {
    //         $company_logo=Utility::get_company_logo();
    //     }
@endphp
@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <nav class="dash-sidebar light-sidebar transprent-bg">
    @else
        <nav class="dash-sidebar light-sidebar">
@endif

<div class="navbar-wrapper">
    <div class="m-header justify-content-center">
        <a href="{{ route('dashboard') }}" class="b-brand">
            @if ($setting['cust_darklayout'] == 'on')
                <img src="{{ $logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-light.png') . '?timestamp='. time() }}"
                    alt="" width="201px" height="41px" />
            @else
                <img src="{{ $logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') . '?timestamp='. time() }}"
                    alt="" width="201px" height="41px" />
            @endif
        </a>
    </div>
    <div class="navbar-content">
        <ul class="dash-navbar">
            @if (Auth::user()->type == 'super admin')
                @can('Manage Dashboard')
                    <li class="dash-item {{ Request::segment(1) == 'dashboard' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('dashboard') }}"
                            class="dash-link {{ request()->is('dashboard') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-home"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endcan
                @can('Manage Store')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'store-resource' || Request::route()->getName() == 'store.grid' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('store-resource.index') }}"
                            class="dash-link {{ request()->is('store-resource') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i data-feather="user"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Stores') }}</span>
                        </a>
                    </li>
                @endcan
                @can('Manage Coupans')
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'coupons' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('coupons.index') }}"
                            class="dash-link {{ request()->is('coupons') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-tag"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Coupons') }}</span>
                        </a>
                    </li>
                @endcan

                @can('Manage Plans')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'plans' || Request::route()->getName() == 'stripe' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('plans.index') }}"
                            class="dash-link {{ request()->is('plans') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-trophy"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Plans') }}</span>
                        </a>
                    </li>
                @endcan

                @can('Manage Plan Request')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'plan_request' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('plan_request.index') }}"
                            class="dash-link {{ request()->is('plan_request') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-brand-telegram"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Plan Requests') }}</span>
                        </a>
                    </li>
                @endcan

                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'custom_domain_request' ? ' active' : 'collapsed' }}">
                    <a href="{{ route('custom_domain_request.index') }}"
                        class="dash-link {{ request()->is('custom_domain_request') ? 'active' : '' }}">
                        <span class="dash-micon">
                            <i class="ti ti-browser"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Domain Requests') }}</span>
                    </a>
                </li>

                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'referral-program' ? ' active' : 'collapsed' }}">
                    <a href="{{ route('referral-program.index') }}"
                        class="dash-link {{ request()->is('referral-program') ? 'active' : '' }}">
                        <span class="dash-micon">
                            <i class="ti ti-discount-2"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Referral Program') }}</span>
                    </a>
                </li>

                @can('Manage Email Template')
                    {{-- <li
                        class="dash-item dash-hasmenu {{ Request::route()->getName() == 'manage.email.language' || Request::route()->getName() == 'manage.email.language' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('manage.email.language', \Auth::user()->lang) }}"
                            class="dash-link {{ request()->is('email_template') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-mail"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Email Templates') }}</span>
                        </a>
                    </li> --}}
                    <li
                        class="dash-item dash-hasmenu {{ Request::route()->getName() == 'email_template.index' || Request::route()->getName() == 'email_template.index' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('email_template.index') }}"
                            class="dash-link {{ request()->is('email_template') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-mail"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Email Templates') }}</span>
                        </a>
                    </li>
                @endcan

                @if (Auth::user()->type == 'super admin')
                    @include('landingpage::menu.landingpage')
                @endif
                @can('Manage Settings')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'settings' || Request::route()->getName() == 'store.editproducts' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('settings') }}"
                            class="dash-link {{ request()->is('settings') ? 'active' : '' }}">
                            <span class="dash-micon"> <i data-feather="settings"></i>
                            </span>
                            <span class="dash-mtext">
                                @if (Auth::user()->type == 'super admin')
                                    {{ __('Settings') }}
                                @endif
                            </span>
                        </a>
                    </li>
                @endcan
            @else
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'dashboard' || Request::segment(1) == 'storeanalytic' || Request::route()->getName() == 'orders.show' ? ' active dash-trigger' : 'collapsed' }}">
                    <a href="#!" class="dash-link ">
                        <span class="dash-micon">
                            <i class="ti ti-home"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Dashboard') }}</span>
                        <span class="dash-arrow">
                            <i data-feather="chevron-right"></i>
                        </span>
                    </a>
                    <ul
                        class="dash-submenu {{ Request::segment(1) == 'dashboard' || Request::segment(1) == 'storeanalytic' ? ' show' : '' }}">
                        @can('Manage Dashboard')
                            <li class="dash-item {{ Request::route()->getName() == 'dashboard' ? ' active' : '' }}">
                                <a class="dash-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                        @endcan
                        @can('Manage Store Analytics')
                            <li class="dash-item {{ Request::route()->getName() == 'storeanalytic' ? ' active' : '' }}">
                                <a class="dash-link" href="{{ route('storeanalytic') }}">{{ __('Store Analytics') }}</a>
                            </li>
                        @endcan
                        @can('Manage Orders')
                            <li
                                class="dash-item {{ Request::segment(1) == 'orders.index' || Request::route()->getName() == 'orders.show' ? ' active dash-trigger' : 'collapsed' }}">
                                <a class="dash-link" href="{{ route('orders.index') }}">{{ __('Orders') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @can('Manage Themes')
                    <li class="dash-item {{ Request::segment(1) == 'themes' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('themes.theme') }}"
                            class="dash-link {{ request()->is('themes') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-layout-2"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Themes') }}</span>
                        </a>
                    </li>
                @endcan
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'users' || Request::segment(1) == 'roles' ? ' active dash-trigger' : 'collapsed' }}">
                    <a href="#!" class="dash-link ">
                        <span class="dash-micon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Staff') }}</span>
                        <span class="dash-arrow">
                            <i data-feather="chevron-right"></i>
                        </span>
                    </a>
                    <ul
                        class="dash-submenu {{ Request::segment(1) == 'roles' || Request::segment(1) == 'roles' ? ' show' : '' }}">
                        @can('Manage Role')
                            <li class="dash-item {{ Request::route()->getName() == 'roles' ? ' active' : '' }}">
                                <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                            </li>
                        @endcan
                        @can('Manage User')
                            <li
                                class="dash-item {{ Request::segment(1) == 'users.index' || Request::route()->getName() == 'users.show' ? ' active dash-trigger' : 'collapsed' }}">
                                <a class="dash-link" href="{{ route('users.index') }}">{{ __('User') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>

                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'product' || Request::segment(1) == 'product_categorie' || Request::segment(1) == 'product_tax' || Request::segment(1) == 'product-coupon' || Request::segment(1) == 'shipping' || Request::segment(1) == 'subscriptions' || Request::segment(1) == 'custom-page' || Request::segment(1) == 'blog' ? ' active dash-trigger' : 'collapsed' }}">
                    <a href="#!" class="dash-link">
                        <span class="dash-micon">
                            <i class="ti ti-layout-2"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Shop') }}</span>
                        <span class="dash-arrow">
                            <i data-feather="chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dash-submenu {{ Request::segment(1) == 'product.index' ? ' show' : '' }}">
                        @can('Manage Products')
                            <li
                                class="dash-item {{ Request::route()->getName() == 'product.index' || Request::route()->getName() == 'product.create' || Request::route()->getName() == 'product.edit' || Request::route()->getName() == 'product.show' || Request::route()->getName() == 'product.grid' ? ' active' : '' }}">
                                <a class="dash-link" href="{{ route('product.index') }}">{{ __('Products') }}</a>
                            </li>
                        @endcan
                        @can('Manage Product category')
                            <li
                                class="dash-item {{ Request::route()->getName() == 'product_categorie.index' ? ' active' : '' }}">
                                <a class="dash-link"
                                    href="{{ route('product_categorie.index') }}">{{ __('Product Category') }}</a>
                            </li>
                        @endcan
                        @can('Manage Product Tax')
                            <li
                                class="dash-item {{ Request::route()->getName() == 'product_tax.index' ? ' active' : '' }}">
                                <a class="dash-link" href="{{ route('product_tax.index') }}">{{ __('Product Tax') }}</a>
                            </li>
                        @endcan
                        @can('Manage Product Coupan')
                            <li
                                class="dash-item {{ Request::route()->getName() == 'product-coupon.index' || Request::route()->getName() == 'product-coupon.show' ? ' active' : '' }}">
                                <a class="dash-link"
                                    href="{{ route('product-coupon.index') }}">{{ __('Product Coupon') }}</a>
                            </li>
                        @endcan
                        @if (isset($plan->shipping_method) && $plan->shipping_method == 'on')
                            @can('Manage Shipping')
                                <li
                                    class="dash-item {{ Request::route()->getName() == 'shipping.index' ? ' active' : '' }}">
                                    <a class="dash-link" href="{{ route('shipping.index') }}">{{ __('Shipping') }}</a>
                                </li>
                            @endcan
                        @endif
                    </ul>
                </li>
                @can('Manage Customers')
                    <li
                        class="dash-item {{ Request::segment(1) == 'customer.index' || Request::route()->getName() == 'customer.show' ? ' active dash-trigger ' : 'collapsed' }}">
                        <a href="{{ route('customer.index') }}"
                            class="dash-link {{ request()->is('customer.index') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-user"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Customers') }}</span>
                        </a>
                    </li>
                @endcan
                @can('Manage Plans')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'plans' || Request::route()->getName() == 'stripe' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('plans.index') }}"
                            class="dash-link {{ request()->is('plans') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-trophy"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Plans') }}</span>
                        </a>
                    </li>
                @endcan
                
                @if (Auth::user()->type == 'Owner')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'referral-program' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('referral-program.owner') }}"
                            class="dash-link {{ request()->is('referral-program') ? 'active' : '' }}">
                            <span class="dash-micon">
                                <i class="ti ti-discount-2"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Referral Program') }}</span>
                        </a>
                    </li>
                @endif

                @can('Manage Settings')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'settings' || Request::route()->getName() == 'store.editproducts' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('settings') }}"
                            class="dash-link {{ request()->is('settings') ? 'active' : '' }}">
                            <span class="dash-micon"> <i data-feather="settings"></i>
                            </span>
                            <span class="dash-mtext">
                                {{ __('Store Settings') }}
                            </span>
                        </a>
                    </li>
                @endcan
            @endif
        </ul>

    </div>
</div>
</div>
</nav>
