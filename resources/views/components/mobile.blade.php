<div class="{{ $divClass }}">
    <div class="form-group">
        {{Form::label($name,$label,['class'=>'form-label'])}}
        {{Form::text($name,$value,array('class'=>$class,'placeholder'=>$placeholder,'pattern' => '^\+\d{1,3}\d{9,13}$','id'=>$id,'required'=>$required))}}
        <div class=" text-xs text-danger" style="font-size: 0.825rem !important; color: rgba(255, 58, 110, 1) !important;">
            {{ __('Please use with country code. (ex. +91)') }}
        </div>
    </div>
</div>
