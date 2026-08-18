@extends('storefront.layout')
@section('store_content')
    <div class="main-wrapper">
        <div class="row no-gutters">
            <div class="col-xxl-9 col-lg-8 col-12">
                <div class="main-left-col tabs-wrapper">
                    {{-- Start header --}}
                    @include('storefront.partials.header')
                    {{-- End header --}}
                    <main class="wrapper">
                        <div class="tab-header">
                            <h2>{{ __('All Products') }}</h2>
                            <div class="tab-filter-wrp d-flex align-items-center justify-content-between">
                                <ul class="breadcrumb d-flex align-items-center">
                                    <li class="breadcrumb-item">
                                        <a href="#">{{ __('Dashboard') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="#">{{ __($store->name) }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="#">{{ __('Products') }}</a>
                                    </li>
                                </ul>
                                <div class="tabs-filter d-flex align-items-center">
                                    <div class="sorting-menu" id="sort_by">
                                        {{ __('Sort by') }}:
                                    </div>

                                    <div class="filter-tabs d-flex align-items-center" id="sort_by">
                                        <a href="{{ route('store.slug', [$store->slug, 'grid']) }}" class="grid"
                                            data-val="grid" id="grid">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 14 14" fill="none">
                                                <path
                                                    d="M2 0C1.46957 0 0.960859 0.210714 0.585786 0.585786C0.210714 0.960859 0 1.46957 0 2V4C0 4.53043 0.210714 5.03914 0.585786 5.41421C0.960859 5.78929 1.46957 6 2 6H4C4.53043 6 5.03914 5.78929 5.41421 5.41421C5.78929 5.03914 6 4.53043 6 4V2C6 1.46957 5.78929 0.960859 5.41421 0.585786C5.03914 0.210714 4.53043 0 4 0H2ZM2 8C1.46957 8 0.960859 8.21071 0.585786 8.58579C0.210714 8.96086 0 9.46957 0 10V12C0 12.5304 0.210714 13.0391 0.585786 13.4142C0.960859 13.7893 1.46957 14 2 14H4C4.53043 14 5.03914 13.7893 5.41421 13.4142C5.78929 13.0391 6 12.5304 6 12V10C6 9.46957 5.78929 8.96086 5.41421 8.58579C5.03914 8.21071 4.53043 8 4 8H2ZM8 2C8 1.46957 8.21071 0.960859 8.58579 0.585786C8.96086 0.210714 9.46957 0 10 0H12C12.5304 0 13.0391 0.210714 13.4142 0.585786C13.7893 0.960859 14 1.46957 14 2V4C14 4.53043 13.7893 5.03914 13.4142 5.41421C13.0391 5.78929 12.5304 6 12 6H10C9.46957 6 8.96086 5.78929 8.58579 5.41421C8.21071 5.03914 8 4.53043 8 4V2ZM8 10C8 9.46957 8.21071 8.96086 8.58579 8.58579C8.96086 8.21071 9.46957 8 10 8H12C12.5304 8 13.0391 8.21071 13.4142 8.58579C13.7893 8.96086 14 9.46957 14 10V12C14 12.5304 13.7893 13.0391 13.4142 13.4142C13.0391 13.7893 12.5304 14 12 14H10C9.46957 14 8.96086 13.7893 8.58579 13.4142C8.21071 13.0391 8 12.5304 8 12V10Z"
                                                    fill="white" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('store.slug', [$store->slug, 'list']) }}" class="list"
                                            data-val="list" id="list">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 14 14" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M0 1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H13C13.2652 0 13.5196 0.105357 13.7071 0.292893C13.8946 0.48043 14 0.734784 14 1C14 1.26522 13.8946 1.51957 13.7071 1.70711C13.5196 1.89464 13.2652 2 13 2H1C0.734784 2 0.48043 1.89464 0.292893 1.70711C0.105357 1.51957 0 1.26522 0 1ZM0 5C0 4.73478 0.105357 4.48043 0.292893 4.29289C0.48043 4.10536 0.734784 4 1 4H13C13.2652 4 13.5196 4.10536 13.7071 4.29289C13.8946 4.48043 14 4.73478 14 5C14 5.26522 13.8946 5.51957 13.7071 5.70711C13.5196 5.89464 13.2652 6 13 6H1C0.734784 6 0.48043 5.89464 0.292893 5.70711C0.105357 5.51957 0 5.26522 0 5ZM0 9C0 8.73478 0.105357 8.48043 0.292893 8.29289C0.48043 8.10536 0.734784 8 1 8H13C13.2652 8 13.5196 8.10536 13.7071 8.29289C13.8946 8.48043 14 8.73478 14 9C14 9.26522 13.8946 9.51957 13.7071 9.70711C13.5196 9.89464 13.2652 10 13 10H1C0.734784 10 0.48043 9.89464 0.292893 9.70711C0.105357 9.51957 0 9.26522 0 9ZM0 13C0 12.7348 0.105357 12.4804 0.292893 12.2929C0.48043 12.1054 0.734784 12 1 12H13C13.2652 12 13.5196 12.1054 13.7071 12.2929C13.8946 12.4804 14 12.7348 14 13C14 13.2652 13.8946 13.5196 13.7071 13.7071C13.5196 13.8946 13.2652 14 13 14H1C0.734784 14 0.48043 13.8946 0.292893 13.7071C0.105357 13.5196 0 13.2652 0 13Z"
                                                    fill="#C4C4C4" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="price-select">
                                        <select id="product_sort">
                                            <option value="">{{ __('Select Price') }}</option>
                                            <option value="hightolow">{{ __('High To Low') }}</option>
                                            <option value="lowtohigh">{{ __('Low To High') }}</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="tabs-container">
                            <div id="filter-tab-1" class="filter-tab-content active">
                                <ul class="tabs category-tab d-flex align-items-center pro_category">
                                    @php
                                        $key = 0;
                                    @endphp
                                    @foreach ($products as $item => $product)
                                        @php
                                            $total_product = count($product);
                                        @endphp
                                        @if ($total_product != 0)
                                            <li data-href="{{ $loop->iteration }}{!! str_replace(' ', '_', $item) !!}"
                                                class="custom-list-group-item productTab {{ $key == 0 ? 'active' : '' }}"
                                                data-tab="tab-{{ $key }}">
                                                {{ __($item) }}
                                                <span>
                                                    {{ __($total_product) }}
                                                </span>

                                            </li>
                                        @endif
                                        @php
                                            $key++;
                                        @endphp
                                    @endforeach

                                </ul>
                                <div class="tabs-container">

                                    <div id="product_view"></div>

                                </div>
                            </div>

                        </div>
                    </main>
                </div>
            </div>
            @include('storefront.card-summary')
        </div>
        {{-- Start footer --}}
        @include('storefront.partials.footer')
        {{-- End footer --}}
    </div>
@endsection
