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
                        {!! Form::label('phone', trans('field.phone'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::text('phone', null, ['id' => 'inputPhone', 'class' => "form-control"]) !!}
                            @if ($errors->has('phone'))
                                <label for="inputPhone" class="error">{{ $errors->first('phone') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('user_name', trans('field.user_name'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::text('user_name', null, ['id' => 'inputUserName','class' => 'form-control']) !!}
                            @if ($errors->has('user_name'))
                                <label for="inputUserName" class="error">{{ $errors->first('user_name') }}</label>
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
                    <div class="form-group group-bot-sns">
                        {!! Form::label('contact', trans('add_user.sns_type'), ['class' => "col-md-2 control-label"]) !!}
                        <div class="col-md-6">
                            {!! Form::select('contact[]', $contact, null, ['class' => 'form-control select2-init contact ', 'multiple' => 'multiple', 'style' => 'width: 100%']) !!}
                            @if ($errors->has('contact'))
                                <label for="sns_type_list" class="error">{{ $errors->first('contact') }}</label>
                            @endif
                            <label for="contact" class="contact">{{ trans('add_user.contact') }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('password', trans('field.password'), ['class' => 'col-md-2 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::text('password', null, ['id' => 'inputPassword','class' => 'form-control']) !!}
                            @if ($errors->has('password'))
                                <label for="inputPassword" class="error">{{ $errors->first('password') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('bot_icon', trans('field.bot_icon'), ['class' => "col-md-2 control-label"]) !!}
                        <div class="col-md-6">
                            <div class="kv-avatar center-block text-center">
                                <input id="web-embed-update" name="picture" type="file" class="" title=" ">
                            </div>
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
    <script src="{{ mix('build/js/template_upload.js') }}"></script>
    <script src="{{ mix('build/js/iCheck.js') }}"></script>

    @if(Lang::locale() != config('constants.language_file_input_js.en'))
{{--        <script src="{{ mix('build/js/te mplate_upload_language_'. Lang::locale() .'.js') }}"></script>--}}
    @endif
    <script type="text/javascript">
        $(document).ready(function () {

            $('.user-create select.select2-init').select2({
                "language": {
                    "noResults": function(){
                        return "{{trans('message.no_results_found')}}";
                    }
                },
                placeholder: "{{trans('field.select_holder')}}",
                minimumResultsForSearch: -1
            });

            $('#white-list-domain_tag').addClass('none');

            $('#btn-user-change').on('click', function () {
                $('#user-change-option').show();
                return false;
            });

            $("#web-embed-update").fileinput({
                language: 'vn',
                overwriteInitial: true,
                required : false,
                maxFileSize: 2048,
                showUpload: false,
                showClose: false,
                showCaption: false,
                browseLabel: '',
                removeLabel: '',
                browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
                removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
                removeTitle: '',
                elErrorContainer: '#error-web-embed-update',
                msgErrorClass: 'alert alert-block alert-danger',
                defaultPreviewContent: '<img src="{{'/images/profile.png'}}"  class="preview_avatar">',
                previewSettings: {
                    image:
                        {
                            'max-width': '200px !important',
                            'max-height': '200px !important'
                        }
                },
                indicatorLoadingTitle: '',
                msgUploadThreshold: '',
                msgUploadBegin: '',
                msgLoading: '',
                msgProgress: '',
                msgUploadEnd: '',
                allowedFileExtensions: ['jpg', 'png', 'jpeg']
            });
            $('.btn-file').attr({'class': 'btn btn-info btn-file'});

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
                if($('#selectAuthority').val() == '{{$authority['super_admin']}}'){
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

        setTimeout(function () {
            $('#inputEmail').removeAttr('readonly');
        }, 1000);
    </script>
@endsection