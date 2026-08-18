{{ Form::open(['url' => 'product_tax', 'method' => 'post','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    {{ Form::label('tax_name', __('Tax Name'), ['class' => 'col-form-label']) }} <x-required></x-required>
                    {{ Form::text('tax_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Tax Name'), 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    {{ Form::label('rate', __('Rate') . ' ' . '(%)', ['class' => 'col-form-label']) }} <x-required></x-required>
                    {{ Form::text('rate', null, ['class' => 'form-control', 'placeholder' => __('Enter Rate'), 'required' => 'required']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" class="btn  btn-primary">{{ __('Create') }}</button>
    </div>
</div>

{{ Form::close() }}
