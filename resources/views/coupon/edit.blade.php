<form method="post" action="{{ route('coupons.update', $coupon->id) }}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="name">{{__('Name')}}</label> <x-required></x-required>
                <input type="text" name="name" class="form-control" required value="{{$coupon->name}}" placeholder="{{__('Enter Name')}}">
            </div>

            <div class="form-group col-md-6">
                <label for="discount">{{__('Discount')}}</label> <x-required></x-required>
                <input type="number" name="discount" class="form-control" required step="0.01" value="{{$coupon->discount}}" placeholder="{{__('Enter Discount')}}">
                <span class="small">{{__('Note: Discount in Percentage')}}</span>
            </div>
            <div class="form-group col-md-6">
                <label for="limit">{{__('Limit')}}</label> <x-required></x-required>
                <input type="number" name="limit" class="form-control" required value="{{$coupon->limit}}" placeholder="{{__('Enter Limit')}}">
            </div>

            <div class="form-group col-md-12" id="auto">
                <label for="code" class="col-form-label">{{__('Code')}}</label> <x-required></x-required>
                <div class="input-group add-coupon-code">
                    <input class="form-control" name="code" type="text" id="auto-code" value="{{$coupon->code}}" required placeholder="{{__('Enter Code')}}">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text bg-primary text-white" id="code-generate" data-bs-toggle="tooltip" title="{{__('Generate')}}"><i class="fa fa-history pr-1"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
    </div>
   
</form>

