@extends('layouts.app2')
@section('title')
    @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
        {{{ trans('modal.user_add') }}}
    @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit'))
        {{{ trans('modal.user_edit') }}}
    @endif
    ::
    @parent @stop
@section('styles')
    <link href="{{ mix('build/css/template_upload.css') }}" rel="stylesheet">
    <link href="{{ mix('build/css/jquery.steps.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="row user-create">
        <div class="center-block" style="float: none">
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
                    <style>
                        /*.wizard > .content{*/
                            /*min-height: 48em;*/
                        /*}*/
                        .wizard > .content > .body label.error{
                            margin-left: 0px;
                        }
                        .contact-item .pannel-content{
                            border: 2px solid #eff2f7;
                            margin-bottom: 10px;
                            height: 170px;
                            overflow: hidden;
                        }
                        .contact-item.active .pannel-content{
                            border: 2px solid #1fb5ad;
                        }
                        .wizard > .content .contact-paging .pagination{
                            display: block;
                            list-style: none !important;
                        }
                        .wizard > .content .contact-paging .pagination li{
                            display: inline;
                        }
                        .wizard > .content .body{
                            width: 100%;
                            padding-left: 0px;
                            padding-right: 0px;
                            height: 100%;
                        }
                        .wizard > .content .contact-item{
                            padding-left: 7.5px;
                            padding-right: 7.5px;
                        }
                        .contact-paging{
                            position: absolute;
                            bottom: 5px;
                            right:0px;
                        }
                    </style>
                    <div id="user-wizard">
                        <h2>{{trans('user.first_step')}}</h2>
                        <section>
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('user.field_phone')}}</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="phone" class="form-control" id="input_phone" placeholder="{{trans('user.field_phone')}}" data-error_required="{{trans('validation.required', ['attribute' => trans('user.field_phone')])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('user.field_user_name')}}</label>
                                    <div class="col-lg-8">
                                        <input type="text" name='user_name' class="form-control" id="input_user_name" placeholder="{{trans('user.field_user_name')}}" data-error_required="{{trans('validation.required', ['attribute' => trans('user.field_user_name')])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('user.field_authority')}}</label>
                                    <div class="col-lg-8">
                                        {!! Form::select('authority',$group, null,['id' => 'input_authority', 'class' => "form-control select2-init",  "data-error_required" => trans('validation.required', ['attribute' => trans('user.field_authority')])])!!}
                                        @if ($errors->has('authority'))
                                            <label for="inputAuthority" class="error">{{ $errors->first('authority') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('user.field_password')}}</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="password" class="form-control" id="input_password" placeholder="{{trans('user.field_password')}}" data-error_required="{{trans('validation.required', ['attribute' => trans('user.field_password')])}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('user.field_avatar')}}</label>
                                    <div class="col-lg-8">
                                        <div class="kv-avatar center-block text-center">
                                            <input type="file" id="avatar-upload" name="avatar" class="form-control" placeholder="{{trans('user.field_avatar')}}">
                                        </div>
                                        <div id="error-avatar"></div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <h2>{{trans('user.last_step')}}</h2>
                        <section class="contact-list">
                            @php
                                $contacts = [1,2,3,4, 5,6,7,8,9,10,11,12];
                            @endphp
                            @if(isset($contacts))
                                <div class="form-horizontal clearfix">
                                    @foreach($contacts as $contact)
                                        <div class="col-md-3 contact-item" data-contact-id="{{$contact}}">
                                            <div class="feed-box text-center pannel-content">
                                                <section class="panel">
                                                    <div class="panel-body">
                                                        <a class="image_box" href="javascript:void 0" >
                                                            <img class="profile_img" alt="" src="{{asset('images/profile.png') }}">
                                                        </a>
                                                        <div class="description">
                                                            <div class="user-name">user-name</div>
                                                            <div class="phone">phone</div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="contact-paging col-md-12">
                                    <ul class = "pagination pull-right">
                                        <li><a href = "#">&laquo;</a></li>
                                        <li><a href = "#">1</a></li>
                                        <li><a href = "#">2</a></li>
                                        <li><a href = "#">&raquo;</a></li>
                                    </ul>
                                </div>
                            @endif
                        </section>
                    </div>
                    <div class="columns separator"></div>

                    {!! Form::close() !!}
                </div>
                <label class="error" id="error-cnt"></label>
            </section>
        </div>
    </div>
    <script src="{{ mix('build/js/template_upload.js') }}"></script>
    <script src="{{ mix('build/js/iCheck.js') }}"></script>
    <script src="{{ mix('build/js/jquery.steps.js') }}"></script>

    @if(Lang::locale() != config('constants.language_file_input_js.en'))
{{--        <script src="{{ mix('build/js/te mplate_upload_language_'. Lang::locale() .'.js') }}"></script>--}}
    @endif
    <script type="text/javascript">
        $(document).ready(function () {
            var contacts = {};
            function resizeJquerySteps() {
                var height = $('.body.current .form-horizontal').outerHeight() + 60;
                console.log('resizeJquerySteps height: ', height);
                $('#user-wizard .content').animate({ height: height }, "fast");
            }
            var form = $("#user-wizard");
            form.steps({
                headerTag: "h2",
                bodyTag: "section",
                transitionEffect: "slideLeft",
                onStepChanging: function (event, currentIndex, newIndex){
                    if (currentIndex > newIndex){
                        return true;
                    }
                    $('.form-horizontal label.error').remove();
                    console.log('onStepChanging: ');
                    console.log('currentIndex: ' + currentIndex, 'newIndex', newIndex);
                    if(currentIndex == 0){
                        var errors = validateUser();
                        console.log('errors: ', errors);
                        if(errors.length == 0){
                            return true;
                        }else{
                            for(var i = 0; i < errors.length ; i++){
                                console.log('error: ' + errors[i]);
                                var element = $('#input_' + errors[i]);
                                showMessageError(element, element.data('error_required'))
                            }
                        }
                    }else{
//                        return true
                    }
                },
                onStepChanged: function (event, currentIndex, priorIndex){
                    resizeJquerySteps();
//                    // Used to skip the "Warning" step if the user is old enough.
//                    console.log('onStepChanged');
//                    console.log('currentIndex: ' + currentIndex, 'newIndex', priorIndex, 'event', event);
////                    if (currentIndex === 2 && Number($("#age-2").val()) >= 18)
//                    if (currentIndex === 0 ){
//                        form.steps("next");
//                    }
//                    // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
//                    if (currentIndex === 1 && priorIndex === 2){
//                        form.steps("previous");
//                    }
                },
                onFinishing: function (event, currentIndex)
                {
                    console.log('onFinishing');
                    return false;
//                    resizeJquerySteps();
//                    form.validate().settings.ignore = ":disabled";
//                    return form.valid();
                },
                onFinished: function (event, currentIndex)
                {
                    console.log('onFinished');
//                    resizeJquerySteps();
                    alert("Submitted!");
                }
            });

            setTimeout(function () {
                resizeJquerySteps();
            }, 1200);

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

            $("#avatar-upload").fileinput({
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
                elErrorContainer: '#error-avatar',
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

            $('.contact-item').on('click', function(){
                $(this).toggleClass('active');
                var contact_id = $(this).data('contact-id');
                if($(this).hasClass('active')){
                    contacts[contact_id] = contact_id;
                }else{
                    delete contacts[contact_id];
                }
                console.log('contacts: ', contacts);
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
            });
            
            function validateUser() {
                var error_arr = [];
                var check_element = ['phone', 'user_name', 'authority', 'password'];
                for(var i = 0; i < check_element.length; i++){
                    var value = $('#input_'+ check_element[i]).val();
                    if(isEmpty(value)){
                        error_arr.push(check_element[i]);
                    }
                }
                return error_arr;
            }

            function isEmpty(value) {
                var result = true;
                if(value != void 0 && value.length > 0 && ((typeof value === 'string' || value instanceof String) && value.trim().length > 0)){
                    result = false;
                }
                return result;
            }

            function showMessageError(element, msg) {
                var error = $('#error-cnt').clone();
                error.attr('id', '');
                error.html(msg);
                $(element).parent().append(error);
            }
        });

        setTimeout(function () {
            $('#inputEmail').removeAttr('readonly');
        }, 1000);
    </script>
@endsection