{{Form::model($user,array('route' => array('user.password.update', $user->id), 'method' => 'post','class'=>'needs-validation','novalidate')) }}
<div class="modal-body">
    <div class="row">
      <div class="form-group col-md12">
          {{ Form::label('password', __('Password'),array('class' => 'col-form-label')) }} <x-required></x-required>
         <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{__('Enter Password')}}">
         @error('password')
         <span class="invalid-feedback" role="alert">
                 <strong>{{ $message }}</strong>
             </span>
         @enderror
      </div>
      <div class="form-group col-md-12">
          {{ Form::label('password_confirmation', __('Confirm Password'),array('class' => 'col-form-label')) }} <x-required></x-required>
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password"  placeholder="{{__('Re-Enter Password')}}">
      </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Save')}}</button>
</div>


{{ Form::close() }}  