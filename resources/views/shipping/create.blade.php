@php
    $plan = Utility::user_plan();
@endphp
{{ Form::open(['url' => 'shipping', 'method' => 'post','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-6"></div>
        <div class="col-6 text-end">
            @if ($plan['enable_chatgpt'] == 'on')
                <a class="btn btn-sm btn-primary" href="#" data-size="lg" data-ajax-popup-over="true"
                    data-url="{{ route('generate', ['products_shipping']) }}" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="{{ __('Generate') }}"
                    data-title="{{ __('Generate Shipping Name') }}"> <i
                        class="fas fa-robot"></i>{{ __('Generate With AI') }}
                </a>
            @endif
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-control-label col-form-label']) }} <x-required></x-required>
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('price', __('Price'), ['class' => 'form-control-label col-form-label']) }} <x-required></x-required>
                {{ Form::text('price', null, ['class' => 'form-control', 'placeholder' => __('Enter Price'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('Location', __('Location'), ['class' => 'form-control-label col-form-label']) }} <x-required></x-required>
                {!! Form::select('location[]', $locations, null, [
                    'class' => 'form-control multi-select',
                    'id' => 'location',
                    'data-toggle' => 'select',
                    'multiple',
                ]) !!}
                <p class="text-danger d-none" id="location_validation">{{__('The location filed is required.')}}</p>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn  btn-primary" id="submit">{{ __('Create') }}</button>
</div>

{{ Form::close() }}

<script>
    $(function(){
        $("#submit").click(function() {
            var location =  $("#location option:selected").length;
            if(location == 0){
                $('#location_validation').removeClass('d-none')
                return false;
            } else {
                $('#location_validation').addClass('d-none')
            }
        });
    });
</script>