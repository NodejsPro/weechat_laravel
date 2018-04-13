@extends('layouts.app2')
@section('title')
    @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
        {{{ trans('modal.user_add') }}}
    @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit'))
        {{{ trans('modal.user_edit') }}}
    @endif
    ::
    @parent @stop
@section('content')
    @php
        $user_login = Auth::user();
        if(isset($embot_env_flg) && $embot_env_flg){
            $embot_plan = @$user->embot_plan;
            $yearly_user = @$user->yearly_user;
            $yearly_fee = @$user->yearly_fee;
            $max_bot_number = @$user->max_bot_number;
            $embot_plan_free = config('constants.embot_plan.free');
            // case edit
            if(ends_with(Route::currentRouteAction(), 'UserController@edit')){
                if($user->authority == $authority['client'] && isset($user->created_id) && !isset($user->embot_plan)){
                    $plan_default = config('constants.embot_plan_default');
                    $embot_plan = $plan_default['code'];
                    $yearly_user = $plan_default['yearly_user'];
                    $yearly_fee = $plan_default['yearly_fee'];
                }elseif($user->authority == $authority['client'] && !isset($user->created_id) && !isset($user->embot_plan)){
                    $embot_plan = config('constants.embot_plan.free');
                    $max_bot_number = @$embot_plan_value['embot_max_bot'][$embot_plan_free];
                }
            // case create
            }else{
                $max_bot_number = @$embot_plan_value['embot_max_bot'][$embot_plan_free];
            }
        }
    @endphp
    <div class="row user-create">
        <div class="center-block" style="float: none">
            <!--breadcrumbs start -->
            <ul class="breadcrumb">
                <li><a href="{!! URL::to('user') !!}"><i class="fa fa-bars"></i> {{{ trans('menu.user_management') }}}</a></li>
                <li class="active">
                    @if(!isset($user))
                        {{{ trans('modal.user_add') }}}
                    @else
                        {{{ trans('modal.user_edit') }}}
                    @endif
                </li>
            </ul>
            <!--breadcrumbs end -->
            <section class="panel minimum-panel">
                <header class="panel-heading">
                    @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
                        {{{ trans('modal.user_add') }}}
                    @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit'))
                        {{{ trans('modal.user_edit') }}}
                    @endif
                </header>
                <div class="panel-body">
                    @include('flash')
                    @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
                        {!! Form::open(['url' => 'user', 'class' => 'cmxform form-horizontal form-action', 'role' => 'form']) !!}
                    @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit'))
                        {!! Form::model($user,[ 'route' => ['user.update', $user->id], 'method' => 'PUT', 'class' => 'cmxform form-horizontal form-action form-user-edit', 'role' => 'form']) !!}
                    @endif
                    <div class="form-group">
                        {!! Form::label('email', trans('field.email'), ['class' => "col-md-2 control-label required"]) !!}
                        <div class="col-md-6">
                            {!! Form::hidden('change_white_domain', null, ['id' => 'change_white_domain','class' => 'form-control']) !!}
                            {!! Form::text('email', null, ['id' => 'inputEmail','class' => 'form-control', 'readonly']) !!}
                            @if ($errors->has('email'))
                                <label for="inputEmail" class="error">{{ $errors->first('email') }}</label>
                            @endif
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
                        {!! Form::label('company', trans('field.company_name'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::text('company_name', null, ['id' => 'inputCompanyName','class' => 'form-control']) !!}
                            @if ($errors->has('company_name'))
                                <label for="inputCompanyName" class="error">{{ $errors->first('company_name') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="form-group group-authority">
                        {!! Form::label('authority', trans('field.authority'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-3">
                            {!! Form::select('authority',$group, null,['id' => 'selectAuthority', 'class' => "form-control select2-init" ])!!}
                            @if ($errors->has('authority'))
                                <label for="inputAuthority" class="error">{{ $errors->first('authority') }}</label>
                            @endif
                        </div>
                    </div>
                    @if(isset($embot_env_flg) && $embot_env_flg)
                        <div class="group-embot">
                            <div class="form-group group-embot-plan">
                                {!! Form::label('template', trans('embot_plan.plan'), ['class' => "col-md-2 control-label required"]) !!}
                                <div class="col-md-3">
                                    {!! Form::select('embot_plan',  $embot_plan_value['embot_plan'], $embot_plan, ['class' => 'form-control select2-init embot_plan', 'id' => 'embot_plan', 'style' => 'width: 100%']) !!}
                                    @if ($errors->has('embot_plan'))
                                        <label for="embot_plan" class="error">{{ $errors->first('embot_plan') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group group-max-bot-number" style="display: none">
                                {!! Form::label('max_bot_number', trans('field.max_bot_number'), ['class' => 'col-md-2 control-label required']) !!}
                                <div class="col-md-3">
                                    {!! Form::text('max_bot_number', $max_bot_number ,['id' => 'inputMaxBotNumber', 'class' => "form-control" ])!!}
                                    @if ($errors->has('max_bot_number'))
                                        <label for="inputMaxBotNumber" class="error">{{ $errors->first('max_bot_number') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group group-max-user-number" style="display: none">
                                {!! Form::label('max_user_number', trans('field.max_user_number'), ['class' => 'col-md-2 control-label required']) !!}
                                <div class="col-md-3">
                                    {!! Form::text('max_user_number',null,['id' => 'inputMaxUserNumber', 'class' => "form-control" ])!!}
                                    @if ($errors->has('max_user_number'))
                                        <label for="inputMaxUserNumber" class="error">{{ $errors->first('max_user_number') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group group-yearly-user-number">
                                {!! Form::label('yearly_user', trans('embot_plan.yearly_user'), ['class' => "col-md-2 control-label required"]) !!}
                                <div class="col-md-3">
                                    {!! Form::select('embot_yearly_user', $embot_plan_value['embot_yearly_user'], null, ['class' => 'form-control select2-init embot_yearly_user', 'id' => 'embot_yearly_user', 'style' => 'width: 100%']) !!}
                                    @if ($errors->has('embot_yearly_user'))
                                        <label for="inputMaxBotNumber" class="error">{{ $errors->first('embot_yearly_user') }}</label>
                                    @endif
                                </div>
                                <div class="col-md-7 col-unit">
                                    <label for="inputMaxBotNumber" class="unit unit_currency">{{trans('embot_plan.unit_user')}}</label>
                                </div>
                            </div>
                            <div class="form-group group-embot-customize-yearly-user">
                                {!! Form::label('embot_yearly_user', trans('embot_plan.yearly_user'), ['class' => "col-md-2 control-label required"]) !!}
                                <div class="col-md-3">
                                    {!! Form::text('embot_yearly_user_number', $yearly_user, ['class' => 'form-control embot_yearly_user_number required', 'id' => 'inputEmbotMaxBot']) !!}
                                    @if ($errors->has('embot_yearly_user_number'))
                                        <label for="inputMaxBotNumber" class="error">{{ $errors->first('embot_yearly_user_number') }}</label>
                                    @endif
                                </div>
                                <div class="col-md-7 col-unit">
                                    <label for="inputMaxBotNumber" class="unit unit_currency">{{trans('embot_plan.unit_user')}}</label>
                                </div>
                            </div>
                            <div class="form-group group-embot-yearly-fee">
                                {!! Form::label('embot_yearly_fee', trans('embot_plan.yearly_fee'), ['class' => "col-md-2 control-label required"]) !!}
                                <div class="col-md-3">
                                    {!! Form::text('embot_yearly_fee', $yearly_fee, ['class' => 'form-control embot_yearly_fee  required', 'id' => 'embot_yearly_fee']) !!}
                                    @if ($errors->has('embot_yearly_fee'))
                                        <label for="inputMaxBotNumber" class="error">{{ $errors->first('embot_yearly_fee') }}</label>
                                    @endif
                                </div>
                                <div class="col-md-7 col-unit">
                                    <label for="inputMaxBotNumber" class="unit unit_currency">{{trans('embot_plan.unit_currency')}}</label>
                                </div>
                            </div>
                            <div class="form-group group-yearly-fee">
                                {!! Form::label('yearly_fee', trans('embot_plan.yearly_fee'), ['class' => "col-md-2 control-label"]) !!}
                                <div class="col-md-3">
                                    <label for="yearly-fee" class="yearly-fee" style="margin-top: 8px; font-weight: normal"></label>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($user_login->authority == config('constants.authority.admin'))
                        <div class="form-group group-template">
                            {!! Form::label('template', trans('field.template'), ['class' => "col-md-2 control-label"]) !!}
                            <div class="col-md-6">
                                {!! Form::select('bot_template[]', $templates, null, ['class' => 'form-control select2-init bot_template', 'multiple' => 'multiple', 'style' => 'width: 100%']) !!}
                                @if ($errors->has('bot_template'))
                                    <label for="bot_template" class="error">{{ $errors->first('bot_template') }}</label>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if(ends_with(Route::currentRouteAction(), 'UserController@create') ||
                        (ends_with(Route::currentRouteAction(), 'UserController@edit')) && isset($user) && ($user->created_id == $user_login->id))
                        <div class="form-group group-bot-sns">
                            {!! Form::label('template', trans('add_user.sns_type'), ['class' => "col-md-2 control-label"]) !!}
                            <div class="col-md-6">
                                {!! Form::select('sns_type_list[]', $sns_type_list, null, ['class' => 'form-control select2-init sns_type_list ', 'multiple' => 'multiple', 'style' => 'width: 100%']) !!}
                                @if ($errors->has('sns_type_list'))
                                    <label for="sns_type_list" class="error">{{ $errors->first('sns_type_list') }}</label>
                                @endif
                                <label for="sns-type-empty" class="sns-type-empty">{{ trans('add_user.add_sns_type_empty') }}</label>
                            </div>
                        </div>
                    @endif
                    @if((ends_with(Route::currentRouteAction(), 'UserController@edit') && Auth::user()->authority == $authority['admin']) && $user->authority == $authority['client'] && !empty($user->created_id))

                        <div class="form-group">
                            {!! Form::label('person_charge', trans('field.person_charge'), ['class' => 'col-md-2 control-label required']) !!}
                            <div class="col-md-6">
                                <?php $user_create = $user->getOneUser($user->created_id);?>
                                {!! Form::label('user_charge_name', @$user_create->name, ['class' => 'col-md-3 control-label user_charge_label']) !!}
                                @if($user_list_change && count($user_list_change) >0)
                                    <button class="btn btn-info col-md-offset-1 pull-left" id="btn-user-change" >{{{ trans('button.user_change') }}}</button>
                                    <div class="col-md-5" id="user-change-option" style="display: {{old('created_id') ? 'block' : 'none'}}">
                                        <i class="fa fa-times fa-close-user-change"> &nbsp;</i>
                                        {!! Form::select('created_id',$user_list_change, null,['id' => 'created_id', 'class' => "form-control pull-left" ])!!}
                                    </div>
                                @endif
                            </div>
                            @if ($errors->has('created_id'))
                                <div class="col-md-6 col-md-offset-2 error-user-change"><label for="inputCreatedId" class="error">{{ $errors->first('created_id') }}</label></div>
                            @endif
                        </div>
                    @endif
                    @if(isset($embot_env_flg) && !$embot_env_flg)
                        <div class="form-group group-max-user-number" style="display: none">
                            {!! Form::label('max_user_number', trans('field.max_user_number'), ['class' => 'col-md-2 control-label required']) !!}
                            <div class="col-md-3">
                                {!! Form::text('max_user_number',null,['id' => 'inputMaxUserNumber', 'class' => "form-control" ])!!}
                                @if ($errors->has('max_user_number'))
                                    <label for="inputMaxUserNumber" class="error">{{ $errors->first('max_user_number') }}</label>
                                @endif
                            </div>
                        </div>
                        @if(!isset($user) || isset($user) && $user->created_id != null)
                            <div class="form-group group-max-bot-number" style="display: none">
                                {!! Form::label('max_bot_number', trans('field.max_bot_number'), ['class' => 'col-md-2 control-label required']) !!}
                                <div class="col-md-3">
                                    {!! Form::text('max_bot_number', null,['id' => 'inputMaxBotNumber', 'class' => "form-control" ])!!}
                                    @if ($errors->has('max_bot_number'))
                                        <label for="inputMaxBotNumber" class="error">{{ $errors->first('max_bot_number') }}</label>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                    {{--@endif--}}
                    <div class="form-group">
                        @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
                            {!! Form::label('password', trans('field.password'), ['class' => 'col-md-2 control-label required']) !!}
                        @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit'))
                            {!! Form::label('password', trans('field.password'), ['class' => 'col-md-2 control-label']) !!}
                        @endif
                        <div class="col-md-3">
                            {!! Form::password('password', ['id' => 'inputPassword','class' => 'form-control']) !!}
                            @if ($errors->has('password'))
                                <label for="inputPassword" class="error">{{ $errors->first('password') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
                            {!! Form::label('password_confirmation', trans('field.password_confirmation'), ['class' => 'col-md-2 control-label required']) !!}
                        @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit'))
                            {!! Form::label('password_confirmation', trans('field.password_confirmation'), ['class' => 'col-md-2 control-label']) !!}
                        @endif
                        <div class="col-md-3">
                            {!! Form::password(
                                'password_confirmation', ['id' => 'inputPasswordConfirmation','class' => 'form-control'])
                            !!}
                            @if ($errors->has('password_confirmation'))
                                <label for="inputPasswordConfirmation" class="error">{{ $errors->first('password_confirmation') }}</label>
                            @endif
                        </div>
                    </div>
                    @if(ends_with(Route::currentRouteAction(), 'UserController@create') ||
                    (ends_with(Route::currentRouteAction(), 'UserController@edit') && $user_login->_id == $user->created_id))
                        <div class="form-group group-white-list-domain">
                            {!! Form::label('white_list_domain', trans('field.white_list_domain'), ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-6">
                                @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
                                    {!! Form::text('white_list_domain', $user_login->authority == $authority['agency'] && !empty($user_login->white_list_domain) ? implode(',', $user_login->white_list_domain) : '' ,['id' => 'white-list-domain', 'class' => "form-control" ])!!}
                                @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit') && $user->authority != $authority['admin'] && $user->created_id == $user_login->_id)
                                    {!! Form::text('white_list_domain', !empty($user->white_list_domain) ? implode(',', $user->white_list_domain) : '' ,['id' => 'white-list-domain', 'class' => "form-control" ])!!}
                                @endif
                                @if($user_login->authority == $authority['admin'] && $errors->has('domain_name_error'))
                                    <label for='white_list_domain' class='error'>{{trans('message.domain_format_incorrect')}}</label>
                                @elseif($user_login->authority == $authority['agency'] && $errors->has('domain_name_error'))
                                    @if(empty($user_login->white_list_domain))
                                        <label for='white_list_domain' class='error'>{{trans('message.domain_format_incorrect')}}</label>
                                    @else
                                        <label for='white_list_domain' class='info'>{{trans('message.white_domain_notice')}}</label>
                                    @endif
                                @elseif($user_login->authority == $authority['agency'] && isset($user) && $user_login->_id == $user->created_id)
                                    <label for='white_list_domain' class='info'>{{trans('message.white_domain_notice')}}</label>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        {!! Form::label('comment', trans('field.comment'), ['class' => "col-md-2 control-label"]) !!}
                        <div class="col-md-6">
                            {!! Form::textarea(
                                'comment', null, ['id' => 'inputComment','class' => 'form-control', 'rows' => '6'])
                            !!}
                            @if ($errors->has('comment'))
                                <label for="inputComment" class="error">{{ $errors->first('comment') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="columns separator"></div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <div class="btn-group-back">
                                <a class="btn btn-default btn-back" href="{{route('user.index')}}">{{{ trans('button.back') }}}</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group-create">
                                @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
                                    <button class="btn btn-info btn-create" type="submit">{{{ trans('button.save') }}}</button>
                                @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit'))
                                    <button class="btn btn-info btn-edit" type="submit">{{{ trans('button.save') }}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </section>
        </div>
        @include('modals.user_change_white_domain')
    </div>
    <script type="text/javascript">
        var embot_plan_list = <?php echo json_encode($embot_plan_value['embot_plan_list'])?>;
        var embot_yearly_user_free = <?php echo json_encode($embot_plan_value['embot_yearly_user_free'])?>;
        var embot_yearly_user_not_free = <?php echo json_encode($embot_plan_value['embot_yearly_user_not_free'])?>;
        var embot_max_bot = <?php echo json_encode($embot_plan_value['embot_max_bot'])?>;
        $(document).ready(function () {
            var white_list_domain = $('#white-list-domain');
            var domain_error = [];
            @if(isset($errors) && $errors->has('domain_name_error'))
                @foreach($errors->get('domain_name_error') as $domain)
                    domain_error.push('{{$domain}}');
                @endforeach
            @endif
            $('.tag').removeClass('domain-error');
            if(white_list_domain.length >0){
                white_list_domain.tagsInput({
                    onChange: function(elem, elem_tags) {
                        if(domain_error.length > 0) {
                            $('.tag').each(function () {
                                var text = $(this).find('span').text().replace(/\s/g, '');
                                for(var i = 0 ;i < domain_error.length; i++){
                                    if(text === domain_error[i]){
                                        $(this).addClass('domain-error');
                                    }
                                }
                            });
                        }
                    }
                });
            }

            $('.user-create select.select2-init').select2({
                "language": {
                    "noResults": function(){
                        return "{{trans('message.no_results_found')}}";
                    }
                },
                minimumResultsForSearch: -1
            });

            @if(isset($embot_env_flg) && $embot_env_flg)
                checkAuthorityEmbot($('#selectAuthority').val());

                $('#embot_yearly_user').on('change', function () {
                    var plan = $('#embot_plan').val();
                    var yearly_user_number = $('#embot_yearly_user').val();
                    checkPrice(plan, yearly_user_number);
                });

                $('#embot_plan').on('change', function () {
                    var plan = $('#embot_plan').val();
                    if(plan == '{{config('constants.embot_plan.platinum')}}' || plan == '{{config('constants.embot_plan.customize')}}'){
                        $('#inputMaxBotNumber').val('');
                    }else if(embot_max_bot[plan] != void 0){
                        $('#inputMaxBotNumber').val(embot_max_bot[plan]);
                    }
                    changeYearlyUserNumber(plan);
                    var yearly_user_number = $('#embot_yearly_user').val();
                    checkPrice(plan, yearly_user_number);
                });
            @endif

            $('#white-list-domain_tag').addClass('none');

            checkAuthority($('#selectAuthority').val());
            checkBotSns($('#selectAuthority').val());

            $('#selectAuthority').on('change', function () {
                @if($user_login->authority == $authority['admin'])
                    checkBotSns($(this).val());
                @endif
                checkAuthority($(this).val());
            });

            $('#btn-user-change').on('click', function () {
                $('#user-change-option').show();
                return false;
            });

            @if(isset($user))
                $('.btn-edit').on('click', function (event) {
                    var user_change_option = $('#user-change-option');
                if(user_change_option.length == 0 || user_change_option.css('display') == 'none'){
                        $('#created_id').val('');
                    }else{
                        event.preventDefault();
                        var url_change = '{{action('UserController@changeManager', ['user_id' => $user->_id, 'manager_id' => ':manager_id'])}}';
                        url_change = url_change.replace(':manager_id', $('#created_id').val());
                        var data = {
                            '_token' : '{{csrf_token()}}',
                            'user_authority' : $('#selectAuthority').val()
                        };
                        @if(isset($user) && $user->created_id == Auth::user()->_id)
                            data.white_list_domain = $('#white-list-domain').val();
                        @endif
                        $.ajax({
                            method: "POST",
                            url: url_change,
                            data: data,
                            success: function (data) {
                                var success = data.success;
                                if(success){
                                    $('.change-domain-modal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    }).show();
                                }else{
                                    $('.form-user-edit').submit();
                                }
                            }
                        });
                    }
                });
            @endif

            $('.user-create .btn-info').on('click', function () {
                if($('#selectAuthority').val() == '{{$authority['admin']}}'){
                    $('input[name="white_list_domain"]').val('');
                    $('.bot_template').select2('val', '');
                    $('.sns_type_list').select2('val', '');
                }
            });

            $('.fa-close-user-change').on('click', function () {
                $('#user-change-option, .error-user-change').hide();
                $('#change_white_domain').val('');
            })
        });

        @if(isset($embot_env_flg) && $embot_env_flg)
            function checkPrice(plan_code, yearly_user_number) {
                var found = false;
                if(plan_code && yearly_user_number){
                    embot_plan_list.forEach(function(plan) {
                        if(plan.code == plan_code && plan.yearly_user == yearly_user_number){
                            var numberformat = parseFloat(plan.yearly_fee).toLocaleString();
                            $('.yearly-fee').html(numberformat + ' ' + '{{trans('embot_plan.unit_currency')}}');
                            found = true;
                            return;
                        }
                    });
                }
                if(!found){
                    $('.yearly-fee').html('&nbsp;');
                }
            }

            function changeYearlyUserNumber(plan) {
                var yearly_user = $('#embot_yearly_user');
                var yearly_user_old = null;
                @if(isset($user->embot_yearly_user))
                    yearly_user_old = '{{$user->embot_yearly_user}}';
                @endif
                $('.group-max-bot-number').show();
                if(plan == '{{config('constants.embot_plan.free')}}'){
                    setData(yearly_user, embot_yearly_user_free, yearly_user_old);
                    $('.group-yearly-user-number, .group-yearly-fee').show();
                    $('.group-embot-customize-yearly-user, .group-embot-yearly-fee').hide();
                } else if(plan == '{{config('constants.embot_plan.customize')}}'){
                    $('.group-yearly-user-number, .group-yearly-fee').hide();
                    $('.group-embot-customize-yearly-user, .group-embot-yearly-fee').show();
                }else{
                    if(plan == '{{config('constants.embot_plan.platinum')}}'){
                        $('.group-max-bot-number').hide();
                    }
                    $('.group-yearly-user-number, .group-yearly-fee').show();
                    $('.group-embot-customize-yearly-user, .group-embot-yearly-fee').hide();
                    setData(yearly_user, embot_yearly_user_not_free, yearly_user_old);
                }
            }

            function setData(element, data, value_old) {
                var options = element.data('select2').options.options;
                element.html('');
                var items = [];
                var found = false;
                Object.keys(data).forEach(function (value) {
                    items.push({
                        "id": value,
                        "text": data[value]
                    });
                    if(value_old == value){
                        found = true;
                    }
                    element.append("<option value=\"" + value + "\">" + data[value] + "</option>");
                });
                options.data = items;
                element.select2(options);
                if(found){
                    element.val(value_old).trigger('change');
                }
            }

            function checkAuthorityEmbot(authority) {
                if(authority == '{{$authority['client']}}'){
                    $('.group-yearly-user-number, .group-embot-customize-yearly-user, .group-embot-yearly-fee, .group-yearly-fee, .group-embot-plan').show();
                    changeYearlyUserNumber($('#embot_plan').val());
                    checkPrice($('#embot_plan').val(), $('#embot_yearly_user').val());
                }else{
                    $('.group-yearly-user-number, .group-embot-plan, .group-embot-customize-yearly-user, .group-embot-yearly-fee, .group-yearly-fee').hide();
                }
                return;
            }
        @endif

        function checkAuthority(authority) {
            @if(Auth::user()->authority == $authority['agency'])
                $('.group-max-user-number').hide();
                $('.group-max-bot-number, .group-white-list-domain').show();
            @else
            if(authority){
                if(authority == '{{$authority['client']}}'){
                    $('.group-max-bot-number, .group-white-list-domain').show();
                    $('.group-max-user-number').hide();
                    $('.group-max-bot-number').show();
                    showTemplateList();
                }else if(authority == '{{$authority['admin']}}'){
                    $('.group-max-user-number, .group-max-bot-number, .group-white-list-domain, .group-template').hide();
                }else{
                    showTemplateList();
                    $('.group-max-user-number, .group-max-bot-number, .group-white-list-domain').show();
                }

                @if(isset($embot_env_flg) && $embot_env_flg)
                    checkAuthorityEmbot(authority);
                @endif
            }
            @endif
        }

        function checkBotSns(authority) {
            if(authority == '{{$authority['admin']}}'){
                $('.group-bot-sns').hide();
            }else{
                $('.group-bot-sns').show();
            }
        }

        function showTemplateList() {
            @if(!isset($user) || (isset($user) && $user_login->authority == $authority['admin'] && $user->created_id == $user_login->id))
                $('.group-template').show();
            @else
                $('.group-template').hide();
            @endif
        }

        setTimeout(function () {
            $('#inputEmail').removeAttr('readonly');
        }, 1000);
    </script>
@endsection