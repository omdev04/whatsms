{{ Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="form-group col-md-12">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('name', $user->name, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('Email', __('Email'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::email('email', $user->email, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('User Role', __('User Role'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::select('role', $roles, $user->roles, ['class' => 'form-control', 'placeholder' => __('Select Role'), 'required' => 'required']) }}
            </div>
            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary"
                    data-bs-dismiss="modal">
                <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
            </div>
        </div>
    </div>
{!! Form::close() !!}
