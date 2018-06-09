@extends('layouts.app2')
@section('title'){{{ trans('modal.personal_information') }}} :: @parent @stop
@php
@endphp
@section('content')
    <div class="row">
        <div class="center-block edit-account" style="float: none">
            <ul class="breadcrumb">
                <li><i class="fa fa-bars"></i> {{{ trans('account.account_management') }}}</li>
            </ul>
            <section class="panel minimum-panel">
                <header class="panel-heading">{{{ trans('account.user_info') }}}</header>
                <div class="panel-body">
                    @include('flash')
                    {!! Form::model($user,[ 'url' => 'account/update', 'method' => 'POST', 'class' => 'cmxform form-horizontal', 'role' => 'form']) !!}
                    <div class="form-group">
                        {!! Form::label('user_name', trans('field.user_name'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::text('user_name', null, ['id' => 'input_user_name','class' => 'form-control required', 'placeholder' => trans('user.field_user_name')]) !!}
                            @if ($errors->has('user_name'))
                                <label for="user_name" class="error">{{ $errors->first('user_name') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('phone', trans('field.phone'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::text('phone', null, ['id' => 'input_user_name','class' => 'form-control required', 'placeholder' => trans('user.field_phone')]) !!}
                            @if ($errors->has('phone'))
                                <label for="phone" class="error">{{ $errors->first('phone') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('contact', trans('field.contact'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::label('contact', $contact_name, ['class' => 'control-label']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('authority', trans('field.authority'), ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-6">
                            <label id="authority" class="control-label">{{{ @array_search($user->authority, config('constants.authority')) }}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('password', trans('field.password'), ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-3">
                            {!! Form::password('password', ['id' => 'inputPassword','class' => 'form-control']) !!}
                            @if ($errors->has('password'))
                                <label for="inputPassword" class="error">{{ $errors->first('password') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="columns separator"></div>
                    <div class="form-group" style="">
                        <div class="col-md-offset-2 col-md-6">
                            {!! Form::submit(trans('button.save'), ['class' => 'btn btn-info']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </section>
        </div>
    </div>
@endsection
@section("scripts")
    <script>
        $(function () {
            $('.show_frm_reset').on('click', function (ev) {
                $('.edit-account .reset_pass').toggle('blind', 200);
            })
        });
    </script>
@endsection