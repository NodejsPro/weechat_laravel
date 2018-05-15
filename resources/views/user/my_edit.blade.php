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
                <header class="panel-heading">
                    {{{ trans('account.user_info') }}}
                    @if(!empty($user->plan))
                        <div class="pull-right div-unsubscribe">
                            <a class="btn btn-info" id="btn-unsubscribe" href="{{action('UserController@unsubscribe')}}">{{trans('button.unsubscribe')}}</a>
                        </div>
                    @endif
                </header>
                <div class="panel-body">
                    @include('flash')
                    {!! Form::model($user,[ 'url' => 'account/update', 'method' => 'POST', 'class' => 'cmxform form-horizontal', 'role' => 'form']) !!}
                    <div class="form-group">
                        {!! Form::label('user_name', trans('field.user_name'), ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-6">
                            <label id="accountEmail" class="control-label">{{{ $user->user_name }}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('phone', trans('field.phone'), ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-6">
                            <label id="phone" class="control-label">{{{ $user->phone }}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('authority', trans('field.authority'), ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-6">
                            <label id="authority" class="control-label">{{{ @array_search($user->authority, config('constants.authority')) }}}</label>
                        </div>
                    </div>
                    <div class="columns separator"></div>
                    <div class="form-group" style="display: none">
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