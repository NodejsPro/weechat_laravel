@extends('login')
@section('title') {{{ trans('default.sign_up') }}} :: @parent @stop

@section('content')
    {!! Form::open(['url' => '/auth/register/create', 'class' => 'form-signin', 'role' => 'form']) !!}
    <div class=" card-box">
        <div class="panel-heading">
            <h3 class="text-center text-uppercase">{{{ trans('default.sign_up') }}}</h3>
        </div>
        <div class="panel-body">
            @include('errors.list')
            @include('flash')
            <input type="hidden" name="token" value="{{ $token }}">
            {!! Form::text('show_email', @$email, ['id' => 'email', 'class' => "form-control", 'placeholder' => trans('field.email'), 'disabled'=> 'disabled']) !!}
            {!! Form::hidden('email', @$email, ['id' => 'email']) !!}
            {!! Form::text('name', null, ['id' => 'name', 'class' => "form-control", 'placeholder' => trans('field.name')]) !!}
            {!! Form::password('password', ['id' => 'password', 'class' => "form-control", 'placeholder' => trans('field.password')]) !!}
            {!! Form::password('password_confirmation', ['id' => 'password_confirmation', 'class' => "form-control", 'placeholder' => trans('field.password_confirmation')]) !!}
            <button class="btn btn-lg btn-login btn-block" type="submit">{{{ trans('button.send') }}}</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection



