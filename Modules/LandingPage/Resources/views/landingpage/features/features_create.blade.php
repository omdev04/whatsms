{{ Form::open(array('route' => 'features_store', 'method'=>'post', 'enctype' => "multipart/form-data",'class'=>'needs-validation','novalidate')) }}
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('other_features_heading', __('Heading'), ['class' => 'form-label']) }} <x-required></x-required>
                    {{ Form::text('other_features_heading',null, ['class' => 'form-control ', 'placeholder' => __('Enter Heading'), 'required' => 'required']) }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('other_featured_description', __('Description'), ['class' => 'form-label']) }}
                    {{ Form::textarea('other_featured_description', null, ['class' => 'form-control summernote', 'placeholder' => __('Enter Description')]) }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('other_feature_buy_now_link', __('Buy Now Link'), ['class' => 'form-label']) }}
                    {{ Form::text('other_feature_buy_now_link', null, ['class' => 'form-control', 'placeholder' => __('Enter Link')]) }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('other_features_image', __('Image'), ['class' => 'form-label']) }}
                    <input type="file" name="other_features_image" class="form-control">
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
