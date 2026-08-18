@php
    $profile = \App\Models\Utility::get_file('uploads/customerprofile/');
@endphp
<div class="modal-body">
    <div class="product-view-body">
        {{ Form::model($userDetail, ['route' => ['customer.profile.update', $slug, $userDetail], 'method' => 'put', 'enctype' => 'multipart/form-data']) }}
        <div class="modal-form-container">
            <div class="form-container-title">
                <h5>{{ __('Main Information') }}</h5>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="form-group">
                        <label for="name">{{ __('Name') }}</label>
                        {{ Form::text('name', null, ['class' => 'form-control font-style']) }}
                        @error('name')
                            <span class="invalid-name" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4  col-md-4 col-12">
                    <div class="form-group">
                        <label for="email">{{ __('Email') }}</label>
                        {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter User Email')]) }}
                        @error('email')
                            <span class="invalid-email" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="form-group">
                        <label for="">{{ __('Avatar') }}</label>
                        <div class="upload-btn-wrapper">
                            <label for="file-1" class="file-upload btn">
                                {{ __('Choose file here')}}
                            </label>
                            <img src="{{ asset('custom/img/upload.svg') }}" alt="upload" class="img-fluid">
                            <input type="file" name="profile" id="file-1" class="file-input" style="display:none">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-form-container">
            <div class="form-container-title">
                <h5>{{ __('Password Informations') }}</h5>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="form-group">
                        <label for="current_password">{{ __('Current Password') }}</label>
                        {{ Form::password('current_password', ['class' => 'form-control', 'placeholder' => __('Enter Current Password')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="form-group">
                        <label for="new_password">{{ __('New Password') }}</label>
                        {{ Form::password('new_password', ['class' => 'form-control', 'placeholder' => __('Enter New Password')]) }}
                        @error('new_password')
                            <span class="invalid-new_password" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="form-group">
                        <label for="confirm_password">{{ __('Re-type New Password') }}</label>
                        {{ Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __('Enter Re-type New Password')]) }}
                        @error('confirm_password')
                            <span class="invalid-confirm_password" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="form-footer">
            {{ Form::button(__('Save Changes'), ['type' => 'submit', 'class' => 'btn text-white ml-1  float-right ml-2 bg--gray hover-translate-y-n3 icon-font']) }}
        </div>
        {{ Form::close() }}
    </div>
</div>
