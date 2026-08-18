{{Form::open(array('url'=>'location','method'=>'post','class'=>'needs-validation','novalidate'))}}
<div class="modal-body">
	<div class="row">
	    <div class="col-12">
	        <div class="form-group">
	            {{Form::label('name',__('Name'),array('class'=>'col-form-label')) }} <x-required></x-required>
	            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
	        </div>
	    </div>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>

{{Form::close()}}
