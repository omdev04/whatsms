<form method="post" action="{{ route('coupons.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                {{Form::label('name',__('Name'),array('class'=>'col-form-label'))}} <x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            </div>

            <div class="form-group col-md-6">
                {{Form::label('discount',__('Discount') ,array('class'=>'col-form-label')) }} <x-required></x-required>
                {{Form::number('discount',null,array('class'=>'form-control','step'=>'0.01','placeholder'=>__('Enter Discount'),'required'=>'required'))}}
                <span class="small">{{__('Note: Discount in Percentage')}}</span>
            </div>
            <div class="form-group col-md-6">
                {{Form::label('limit',__('Limit') ,array('class'=>'col-form-label'))}} <x-required></x-required>
                {{Form::number('limit',null,array('class'=>'form-control','placeholder'=>__('Enter Limit'),'required'=>'required'))}}
            </div>
           <div class="form-group col-md-12" id="auto">
                <label for="code" class="col-form-label">{{__('Code')}}</label> <x-required></x-required>
                <div class="input-group add-coupon-code">
                    <input class="form-control" name="code" type="text" id="auto-code" value="" required placeholder="{{__('Enter Code')}}">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text bg-primary text-white" id="code-generate" data-bs-toggle="tooltip" title="{{__('Generate')}}"><i class="fa fa-history pr-1"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
    </div>
</form>

