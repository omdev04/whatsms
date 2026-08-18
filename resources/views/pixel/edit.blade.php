{{ Form::model($pixel_field, ['route' => ['pixel.update', $pixel_field->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
@csrf
@method('put')
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{ Form::select('platform',$pixals_platforms,$pixel_field->platform, ['class' => 'form-control item form-control-solid mb-7 mt-3','placeholder'=>'Please Select','required'=>'required']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('pixel_id',__('Pixel ID').' '.'(%)',array('class'=>'col-form-label')) }}
                {{Form::text('pixel_id',null,array('class'=>'form-control','placeholder'=>__('Enter Pixel ID'),'required'=>'required'))}}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
</div>

{{Form::close()}}
