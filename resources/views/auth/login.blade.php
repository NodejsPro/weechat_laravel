@extends('login')
@section('title') {{{ trans('title.title_login') }}} :: @parent @stop

@section('content')
    {!! Form::open(['url' => 'login', 'class' => 'form-signin', 'role' => 'form']) !!}
    <div class=" card-box">
        <div class="panel-heading">
            <h3 class="text-center text-uppercase">{{{ trans('button.login') }}}<br></h3>
        </div>
        <div class="panel-body">
            @include('errors.list')
            @include('flash')
            {!! Form::text('user_name', null, ['id' => 'inputUsername', 'class' => "form-control", 'placeholder' => trans('field.user_name')]) !!}
            {!! Form::password('password', ['id' => 'inputPassword', 'class' => "form-control", 'placeholder' => trans('field.password')]) !!}
            {!! Form::checkbox('remember', null, false) !!} &nbsp;{{{ trans('button.remember_me') }}}
            <button class="btn btn-lg btn-login btn-block" type="submit">{{{ trans('button.login') }}}</button>
            <br />
            <a href="{{url("/password/reset")}}" class="text-dark"><i class="fa fa-lock m-r-5"></i> {{{ trans('default.login_forget_password')}}}</a>
            <hr style="margin-top: 22px; margin-bottom: 22px; border: 0; border-top: 1px solid #e4eaec;">
            <h5>{{trans('account.no_account')}}&nbsp;<a href="{{url("/auth/register")}}" class="text-dark">{{{ trans('default.sign_up')}}}</a></h5>

        </div>
    </div>
    {!! Form::close() !!}
@endsection


