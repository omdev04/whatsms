@extends('layouts.admin')
@section('page-title')
    {{ $emailTemplate->name }}
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('custom/libs/summernote/summernote-bs4.css') }}">
@endpush
@push('script-page')
    <script src="{{ asset('custom/libs/summernote/summernote-bs4.js') }}"></script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Email Template') }}</li>
@endsection
@section('action-btn')

@endsection
@section('content')
    <div class="row template-row-gap">
        <div class="col-md-4 col-12">
            <div class="card mb-0 h-100">
                <div class="card-header card-body">
                    <h5></h5>
                    {{Form::model($currEmailTempLang, array('route' => array('updateEmail.settings', $currEmailTempLang->parent_id), 'method' => 'PUT')) }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            {{Form::label('name',__('Name'),['class'=>'col-form-label text-dark'])}}
                            {{Form::text('name',$emailTemplate->name,array('class'=>'form-control font-style','disabled'=>'disabled'))}}
                        </div>
                        <div class="form-group col-md-12">
                            {{Form::label('from',__('From'),['class'=>'col-form-label text-dark'])}}
                            {{Form::text('from',$emailTemplate->from,array('class'=>'form-control font-style','required'=>'required'))}}
                        </div>
                        {{Form::hidden('lang',$currEmailTempLang->lang,array('class'=>''))}}
                        <div class="col-12 text-end">
                            <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-8 col-12">
            <div class="card mb-0 h-100">
                <div class="card-header card-body">
                    <h5></h5>
                    <div class="row text-xs">
                        <h6 class="font-weight-bold mb-4">{{__('Variables')}}</h6>
                        @if($emailTemplate->name != "Owner Created")
                            <p class="col-6 mb-1">{{ __('App Name') }} : <span
                                class="pull-right text-primary">{app_name}</span></p>
                            <p class="col-6 mb-1">{{ __('Order Name') }} : <span
                                    class="pull-right text-primary">{order_name}</span></p>
                            <p class="col-6 mb-1">{{ __('Order Status') }} : <span
                                    class="pull-right text-primary">{order_status}</span></p>
                            <p class="col-6 mb-1">{{ __('Order URL') }} : <span
                                    class="pull-right text-primary">{order_url}</span></p>
                            <p class="col-6 mb-1">{{ __('Order Id') }} : <span
                                    class="pull-right text-primary">{order_id}</span></p>
                            <p class="col-6 mb-1">{{ __('Order Date') }} : <span
                                    class="pull-right text-primary">{order_date}</span></p>
                            <p class="col-6 mb-1">{{ __('Owner Name') }} : <span
                                    class="pull-right text-primary">{owner_name}</span></p>
                        @else
                            <p class="col-6 mb-1">{{ __('App Name') }} : <span
                                class="pull-right text-primary">{app_name}</span></p>
                            <p class="col-6 mb-1">{{ __('App URL') }} : <span
                                    class="pull-right text-primary">{app_url}</span></p>
                            <p class="col-6 mb-1">{{ __('Owner Name') }} : <span
                                    class="pull-right text-primary">{owner_name}</span></p>
                            <p class="col-6 mb-1">{{ __('Owner Email') }} : <span
                                    class="pull-right text-primary">{owner_email}</span></p>
                            <p class="col-6 mb-1">{{ __('Owner Password') }} : <span
                                    class="pull-right text-primary">{owner_password}</span></p>
                            <p class="col-6 mb-1">{{ __('Store URL') }} : <span
                                    class="pull-right text-primary">{store_url}</span></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5></h5>
                <div class="row">
                    <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                        <div class="card sticky-top language-sidebar mb-0 h-100">
                            <div class="list-group list-group-flush" id="useradd-sidenav">
                                @foreach($languages as $code => $lang)
                                <a class="list-group-item list-group-item-action border-0 py-2 {{($currEmailTempLang->lang == $code)?'active':''}}" href="{{route('manage.email.language',[$emailTemplate->id,$code])}}">
                                    {{Str::ucfirst($lang)}}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9 col-md-9 col-sm-9">
                        <div class="card h-100 p-3">
                            {{Form::model($currEmailTempLang, array('route' => array('updateEmail.settings', $currEmailTempLang->parent_id), 'method' => 'PUT')) }}
                            <div class="row">
                                <div class="form-group col-12">
                                    {{Form::label('subject',__('Subject'),['class'=>'col-form-label text-dark'])}}
                                    {{ Form::text('subject', null, ['class' => 'form-control font-style', 'required' => 'required','placeholder'=>__('Enter Subject')]) }}
                                </div>
                                <div class="form-group col-12">
                                    {{Form::label('content',__('Email Message'),['class'=>'col-form-label text-dark'])}}
                                    {{ Form::textarea('content', $currEmailTempLang->content, ['class' => 'summernote', 'required' => 'required']) }}
                                </div>

                                <div class="col-md-12 text-end mb-3">
                                    {{Form::hidden('lang',null)}}
                                    <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection