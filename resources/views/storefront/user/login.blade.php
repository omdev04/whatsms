<div class="modal-body">
    <div class="section-title">
        <h2>{{ __('Login Form') }}</h2>
    </div>
    {!! Form::open(
        [
            'route' => ['customer.login', $slug, !empty($is_cart) && $is_cart == true ? $is_cart : false],
            'class' => 'login-form',
        ],
        ['method' => 'POST'],
    ) !!}
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>{{ __('Email') }}</label>
                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter Email']) }}

            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label>{{ __('Password') }}</label>
                {{ Form::password('password', ['class' => 'form-control', 'id' => 'exampleInputPassword1', 'placeholder' => 'Enter Password']) }}
            </div>
        </div>
    </div>
    <button type="submit" class="btn login-btn w-100">{{ __('Login') }}</button>
    <div class="login-info d-flex align-items-center justify-content-center">
        <p>{{ __('Don\'t have account ?') }}</p>
        <a data-url="{{ route('store.usercreate', $slug) }}" data-ajax-popup="true" data-title="Register"
            data-toggle="modal" class=" register-modal-btn text-primary">
            <p>{{ __('Register') }}</p>
        </a>
    </div>
    {!! Form::close() !!}
</div>
