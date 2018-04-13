@extends('layouts.app2')
@section('title'){{{ trans('modal.personal_information') }}} :: @parent @stop
@php
    $embot_plan_flg = false;
    if(config('app.plan') == 'EMBOT' ){
        $embot_plan_flg = true;
    }
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
                        {!! Form::label('email', trans('field.email'), ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-6">
                            <label id="accountEmail" class="control-label">{{{ $user->email }}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('name', trans('field.name'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::text('name', null, ['id' => 'inputName', 'class' => "form-control"]) !!}
                            @if ($errors->has('name'))
                                <label for="inputName" class="error">{{ $errors->first('name') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('authority', trans('field.authority'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            <label id="authority" class="control-label">{{{ @array_search($user->authority, config('constants.authority')) }}}</label>
                        </div>
                    </div>
                    @if($user->authority != $authority['admin'])
                        @if($user->authority == $authority['agency'])
                            <div class="form-group group-max-user-number">
                                {!! Form::label('max_user_number', trans('field.max_user_number'), ['class' => 'col-md-2 control-label required']) !!}
                                <div class="col-md-3">
                                    {!! Form::label('max_user_number',$user->max_user_number,['id' => 'inputMaxUserNumber', 'class' => "control-label" ])!!}
                                </div>
                            </div>
                        @endif
                        @if($embot_plan_flg && isset($user_plan))
                            <div class="form-group">
                                {!! Form::label('embot_plan', trans('embot_plan.plan'), ['class' => 'col-md-2 control-label']) !!}
                                <div class="col-md-6">
                                    <label id="embot_plan" class="control-label">{{{ trans('embot_plan.plan_' . array_search($user_plan->code, config('constants.embot_plan'))) }}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('embot_plan', trans('embot_plan.yearly_user'), ['class' => 'col-md-2 control-label']) !!}
                                <div class="col-md-6">
                                    <label id="authority" class="control-label">{{{ isset($yearly_user_number) ? $yearly_user_number : $user_plan->yearly_user_number }}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('embot_plan', trans('field.max_bot_number'), ['class' => 'col-md-2 control-label']) !!}
                                <div class="col-md-6">
                                    <label id="yearly_fee" class="control-label">{{{ isset($user->max_bot_number) ? $user->max_bot_number : $user_plan->max_bot_number }}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('embot_plan', trans('embot_plan.yearly_fee'), ['class' => 'col-md-2 control-label']) !!}
                                <div class="col-md-6">
                                    <label id="yearly_fee" class="control-label">{{{ isset($yearly_fee) ? $yearly_fee : $user_plan->yearly_fee }}}</label>
                                </div>
                            </div>
                        @else
                            @if($user->created_id != null && $user->plan == null)
                                <div class="form-group group-max-bot-number">
                                    {!! Form::label('max_bot_number', trans('field.max_bot_number'), ['class' => 'col-md-2 control-label required']) !!}
                                    <div class="col-md-3">
                                        {!! Form::label('max_bot_number', $user->max_bot_number,['id' => 'inputMaxBotNumber', 'class' => "control-label" ])!!}
                                    </div>
                                </div>
                            @elseif($user->plan != null)
                                <div class="form-group group-max-bot-number">
                                    {!! Form::label('max_bot_number', trans('field.plan'), ['class' => 'col-md-2 control-label required']) !!}
                                    <div class="col-md-3">
                                        <a href="{{ url('plan')}}" style="color: #00b3ee;">{!! Form::label('max_bot_number', $plans[$user->plan],['id' => 'inputMaxBotNumber', 'class' => "control-label", "style" => "cursor: pointer;" ])!!}</a>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                    <div class="form-group">
                        <input name="_token" type="hidden" value="{{csrf_token()}}">
                        {!! Form::label('language', trans('field.language'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-3">
                            {!! Form::select('language', config('constants.language_type'), $user->locale,['id' => 'selectLanguage', 'class' => "form-control" ])!!}
                            @if ($errors->has('language'))
                                <label for="inputLanguage" class="error">{{ $errors->first('language') }}</label>
                            @endif
                        </div>
                    </div>
                    @if($user->authority != $authority['admin'] && !empty($user->white_list_domain))
                        <div class="form-group">
                            {!! Form::label('white_list_domain', trans('field.white_list_domain'), ['class' => "col-md-2 control-label"]) !!}
                            <div class="col-md-6">
                                {!! Form::textarea('white_list_domain', implode(', ', $user->white_list_domain), ['id' => 'white_list_domain','class' => 'form-control', 'rows' => '5', 'disabled' => true])!!}
                                @if ($errors->has('comment'))
                                    <label for="inputComment" class="error">{{ $errors->first('comment') }}</label>
                                @endif
                            </div>
                        </div>
                    @endif
                    {{--reset password--}}
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-3">
                            <a class="btn btn-default show_frm_reset">
                                <i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;{{trans('account.password_reset')}}
                            </a>
                        </div>
                    </div>
                    <div class="reset_pass" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('password', trans('field.password'), ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-3">
                                {!! Form::password('password', ['id' => 'inputPassword','class' => 'form-control']) !!}
                                @if ($errors->has('password'))
                                    <label for="inputPassword" class="error">{{ $errors->first('password') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('password_confirmation', trans('field.password_confirmation'), ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-3">
                                {!! Form::password(
                                'password_confirmation', ['id' => 'inputPasswordConfirmation','class' => 'form-control'])
                                !!}
                                @if ($errors->has('password_confirmation'))
                                    <label for="inputPasswordConfirmation" class="error">{{ $errors->first('password_confirmation') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{--end--}}
                    <div class="columns separator"></div>
                    <div class="form-group">
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