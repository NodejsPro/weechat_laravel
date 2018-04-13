@extends('layouts.app2')
@section('title'){{{ trans('modal.unsubscribe') }}} :: @parent @stop
@section('styles')
    <link href="{{ elixir('css/iCheck.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="row">
        <div class="center-block unsubscribe-account" style="float: none">
            <ul class="breadcrumb">
                <li><i class="fa fa-bars"></i> {{{ trans('account.account_management') }}}</li>
            </ul>
            <section class="panel minimum-panel">
                <header class="panel-heading">
                    {{{ trans('modal.unsubscribe') }}}
                </header>
                <div class="panel-body">
                    @include('flash')
                    {!! Form::model($user, ['url' => action('UserController@UpdateUnsubscribe'), 'method' => 'POST', 'class' => 'cmxform form-horizontal form-unsubscribe', 'role' => 'form']) !!}
                    <div class="form-group">
                        <input type="hidden" value="{{csrf_token()}}"/>
                        {!! Form::label('name', trans('unsubscribe.label_header', ['system_name' => env('APP_NAME')]), ['class' => "col-md-12 label-unsubscribe"]) !!}
                        <div class="col-md-12 unsubscribe-content">
                            <ul>
                                <li>{{trans('unsubscribe.unsubscribe_notice_one')}}</li>
                                <li>{{trans('unsubscribe.unsubscribe_notice_two')}}</li>
                            </ul><br/>
                            <div class=" icheck minimal">
                                <div class="checkbox single-row">
                                    <input type="checkbox" style="width: 20px" class="checkbox form-control icheckbox_minimal" id="agree" name="agree" value="{{config('constants.active.enable')}}" />
                                </div>
                            </div>
                            <label class="control-label">{{trans('unsubscribe.unsubscribe_agree')}}</label>
                            <div class="error-content">
                                @if ($errors->has('agree'))
                                    <label for="name" class="error agree_error">{{ $errors->first('agree') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="columns separator"></div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <a class="btn btn-default btn-back" href="/account/edit">{{{ trans('button.back') }}}</a>
                            {!! Form::submit(trans('button.system_unsubscribe', ['system_name' => env('APP_NAME')]), ['class' => 'btn btn-info', 'id' => 'btn-unsubscribe']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </section>
        </div>
    </div>
@endsection
@section("scripts")
    <script src="{{ elixir('js/iCheck.js') }}"></script>
    <script>
    </script>
@endsection