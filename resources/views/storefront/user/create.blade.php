<div class="modal-body">
    <div class="section-title">
        <h2>{{ __('Register Form') }}</h2>
    </div>
    {!! Form::open(['route' => ['store.userstore', $slug], 'class' => 'login-form-main'], ['method' => 'post']) !!}
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>{{ __('Full Name') }}</label>
                <input class="form-control" name="name" type="text" required="required"
                    placeholder="{{ __('Enter Name') }}">
            </div>
        </div>
        @error('name')
            <span class="error invalid-email text-danger" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <div class="col-12">
            <div class="form-group">
                <label>{{ __('Email') }}</label>
                <input type="email" name="email" class="form-control" required="required"
                    placeholder="{{ __('Enter Email') }}">
            </div>
        </div>
        @error('email')
            <span class="error invalid-email text-danger" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <x-mobile lable="{{ __('Number') }}" class="form-control" name="phone_number" required='true'
            placeholder="{{ __('Enter Number') }}"></x-mobile>

        <div class="col-12">
            <div class="form-group">
                <label>{{ __('Password') }}</label>
                <input type="password" name="password" class="form-control" required="required"
                    placeholder="{{ __('Enter Password') }}">
            </div>
        </div>
        @error('password')
            <span class="error invalid-email text-danger" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <div class="col-12">
            <div class="form-group">
                <label>{{ __('Confirm Password') }}</label>
                <input type="password" name="password_confirmation" class="form-control" required="required"
                    placeholder="{{ __('Enter Confirm Password') }}">
            </div>
        </div>
        @error('password_confirmation')
            <span class="error invalid-email text-danger" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <button type="submit" class="btn register-btn w-100">{{ __('Register') }}</button>
    <div class="login-info d-flex align-items-center justify-content-center">
        <p>{{ __('Already registered ?') }}</p>
        <a data-url="{{ route('customer.loginform', $slug) }}" data-ajax-popup="true" data-title="{{ __('Login') }}"
            data-toggle="modal" class="login-modal-btn">{{ __('Login') }}</a>
    </div>

    {!! Form::close() !!}
</div>
