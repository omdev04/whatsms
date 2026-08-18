@php
    $profile = \App\Models\Utility::get_file('uploads/customerprofile/');
@endphp
@extends('layouts.admin')
@section('page-title')
    {{ __('Store Customers') }}
@endsection
@section('title')
    {{ __('Store Customers') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>

    <li class="breadcrumb-item active" aria-current="page">{{ __('Store Customers') }}</li>
@endsection

@section('action-btn')
    <div class="row gy-4 align-items-center">
        <div class="col-auto">
            <div class="d-flex">
                <a href="{{ route('customer.exports', $store->id) }} " class="btn btn-sm btn-icon  text-white bg-primary"
                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Export') }}">
                    <i class="ti ti-download"></i>
                </a>
            </div>
        </div>
    </div>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple">
                            <thead>
                                <tr>
                                    <th> {{ __('Customer Avatar') }}</th>
                                    <th> {{ __('Name') }}</th>
                                    <th> {{ __('Email') }}</th>
                                    <th> {{ __('Phone No') }}</th>
                                    <th class="text-right"> {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr class="font-style">
                                        <td>
                                            <div class="media align-items-center">
                                                <div>
                                                    <img src="{{ !empty($customer->avatar) ? $profile . '/' . $customer->avatar : $profile . '/avatar.png' }}"
                                                        id="blah" class="theme-avtar rounded border-2 border border-primary" />
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone_number }}</td>
                                        <td class="Action">
                                            <div class="d-flex">
                                                @can('Show Customers')
                                                    <a href="{{ route('customer.show', $customer->id) }}"
                                                        class="btn btn-sm btn-icon  text-white bg-warning me-2"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('View') }}">
                                                        <i class="ti ti-eye f-20"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
