@php
    $plan = Utility::user_plan();
@endphp
{{ Form::open(['url' => 'store-resource', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
{{-- {{ Form::open(['route' => 'store-resource.store', 'method' => 'post']) }} --}}
<div class="modal-body">
    <div class="row">
        <div class="col-6"></div>
        <div class="col-6 text-end">
            @if (\Auth::user()->type == 'super admin')
                <a class="btn btn-sm btn-primary" href="#" data-size="lg" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['store']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Generate') }}" data-title="{{ __('Generate Coupon Name') }}">
                    <i class="fas fa-robot"></i>
                    {{ __('Generate With AI') }}
                </a>
            @else
                @if ($plan['enable_chatgpt'] == 'on')
                    <a class="btn btn-sm btn-primary" href="#" data-size="lg" data-ajax-popup-over="true"
                        data-url="{{ route('generate', ['store']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ __('Generate') }}" data-title="{{ __('Generate Coupon Name') }}">
                        <i class="fas fa-robot"></i>
                        {{ __('Generate With AI') }}
                    </a>
                @endif
            @endif
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('store_name', __('Store Name'), ['class' => 'col-form-label']) }}
                <x-required></x-required>
                {{ Form::text('store_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Store Name'), 'required' => 'required']) }}
            </div>
        </div>
        @if (\Auth::user()->type == 'super admin')
            <div class="col-12">
                <div class="form-group">
                    {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }} <x-required></x-required>
                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }} <x-required></x-required>
                    {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-6 form-group">
                <label for="password_switch">{{ __('Login is enable') }}</label>
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer"
                        value="on" id="password_switch">
                    <label class="form-check-label" for="password_switch"></label>
                </div>
            </div>
            <div class="col-12 ps_div d-none">
                <div class="form-group">
                    {{ Form::label('password', __('Password'), ['class' => 'col-form-label']) }}
                    <x-required></x-required>
                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Password')]) }}
                    @error('password')
                        <small class="invalid-password" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </small>
                    @enderror
                </div>
            </div>
        @endif
    </div>

    @if (\Auth::user()->type != 'super admin')
        <div class="d-flex mb-3 align-items-center justify-content-between">
            <h6>{{ __('Theme Settings') }}</h6>
            {{ Form::hidden('themefile', null, ['id' => 'themefile1']) }}
        </div>
        <div class="border border-primary rounded p-3">
            <div class="row gy-4 ">
                @foreach (\App\Models\Utility::themeOne() as $key => $v)
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="theme-card border-primary">
                            <div class="theme-card-inner">
                                <div class="theme-image border  rounded">
                                    <img src="{{ asset(Storage::url('uploads/store_theme/' . $key . '/Home.png')) }}"
                                        class="color img-center pro_max_width pro_max_height {{ $key }}_img"
                                        data-id="{{ $key }}" alt="">
                                </div>
                                <div class="theme-content mt-3">
                                    <div class="d-flex mt-2 align-items-center" id="radio_{{ $key }}">
                                        <div class="color-inputs">
                                            @foreach ($v as $css => $val)
                                                <label class="colorinput">
                                                    <input name="theme_color" type="radio" id="color1-theme4"
                                                        value="{{ $css }}" data-theme="{{ $key }}"
                                                        data-imgpath="{{ $val['img_path'] }}"
                                                        class="colorinput-input color-{{ $loop->index++ }}"
                                                        {{ isset($store_settings['store_theme']) && $store_settings['store_theme'] == $css && $store_settings['theme_dir'] == $key ? 'checked' : '' }}>
                                                    <span class="border-box">
                                                        <span class="colorinput-color"
                                                            style="background:#{{ $val['color'] }}"></span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" class="btn  btn-primary">{{ __('Create') }}</button>
    </div>
    <script>
        $('body').on('click', 'input[name="theme_color"]', function() {
            var eleParent = $(this).attr('data-theme');
            $('#themefile1').val(eleParent);
            var imgpath = $(this).attr('data-imgpath');
            $('.' + eleParent + '_img').attr('src', imgpath);
        });
        $('body').ready(function() {
            setTimeout(function(e) {
                var checked = $("input[type=radio][name='theme_color']:checked");
                $('#themefile1').val(checked.attr('data-theme'));
                $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
            }, 300);
        });
        $(".color").click(function() {

            var dataId = $(this).attr("data-id");
            $('#radio_' + dataId).trigger('click');

            var first_check = $('#radio_' + dataId).find('.color-0').trigger("click");
            $(".theme-card").each(function() {
                $(".theme-card").removeClass('selected');
            });
            $('.s_' + dataId).toggleClass('selected');
        });
    </script>
</div>


{{ Form::close() }}

{{-- Password  --}}
<script>
    $(document).on('change', '#password_switch', function() {
        if ($(this).is(':checked')) {
            $('.ps_div').removeClass('d-none');
            $('#password').attr("required", true);

        } else {
            $('.ps_div').addClass('d-none');
            $('#password').val(null);
            $('#password').removeAttr("required");
        }
    });
    $(document).on('click', '.login_enable', function() {
        setTimeout(function() {
            $('.modal-body').append($('<input>', {
                type: 'hidden',
                val: 'true',
                name: 'login_enable'
            }));
        }, 2000);
    });
</script>
