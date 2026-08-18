{{ Form::open(array('route' => 'discover_store', 'method'=>'post', 'enctype' => "multipart/form-data",'class'=>'needs-validation','novalidate')) }}
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('discover_heading', __('Heading'), ['class' => 'form-label']) }} <x-required></x-required>
                    {{ Form::text('discover_heading',null, ['class' => 'form-control ', 'placeholder' => __('Enter Heading'), 'required' => 'required']) }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('discover_description', __('Description'), ['class' => 'form-label']) }}
                    {{ Form::textarea('discover_description', null, ['class' => 'form-control summernote','id' => 'classic-editor', 'placeholder' => __('Enter Description')]) }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('discover_logo', __('Logo'), ['class' => 'form-label']) }} <x-required></x-required>
                    <input type="file" name="discover_logo" class="form-control" required="required">
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
