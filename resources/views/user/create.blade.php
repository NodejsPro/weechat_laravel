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
                        .kv-file-content{
                            max-height: 200px !important;
                            max-width: 200px !important;
                        }
                    </style>
                    <div id="user-wizard">
                        @if(!empty($errors))
                            {{Log::info($errors)}}
                        @endif
                        <h2>{{trans('user.first_step')}}</h2>
                        <section>
                            @if(ends_with(Route::currentRouteAction(), 'UserController@create'))
                                {!! Form::open(['url' => 'user', 'class' => 'cmxform form-horizontal form-action', 'role' => 'form', 'enctype' => 'multipart/form-data']) !!}
                            @elseif(ends_with(Route::currentRouteAction(), 'UserController@edit'))
                                {!! Form::model($user,[ 'route' => ['user.update', $user->id], 'method' => 'PUT', 'class' => 'cmxform form-horizontal form-action form-user-edit', 'role' => 'form' , 'enctype' => 'multipart/form-data']) !!}
                            @endif
                                <div class="form-group">
                                    <input type="hidden" name="contact" value="" id="contact"/>
                                    <label class="col-lg-2 control-label">{{trans('user.field_phone')}}</label>
                                    <div class="col-lg-8">
                                        {!! Form::text('phone', null, ['id' => 'input_phone','class' => 'form-control required', 'placeholder' => trans('user.field_phone')]) !!}
                                        @if ($errors->has('phone'))
                                            <label for="inputPhone" class="error">{{ $errors->first('phone') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{{trans('user.field_user_name')}}</label>
                                    <div class="col-lg-8">
                                        {!! Form::text('user_name', null, ['id' => 'input_user_name','class' => 'form-control required', 'placeholder' => trans('user.field_user_name')]) !!}
                                        @if ($errors->has('user_name'))
                                            <label for="inputUserName" class="error">{{ $errors->first('user_name') }}</label>
                                        @endif
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
                                        {!! Form::password('password', ['id' => 'input_password','class' => 'form-control', 'placeholder' => trans('user.field_password')]) !!}
                                        @if ($errors->has('password'))
                                            <label for="inputPassword" class="error">{{ $errors->first('password') }}</label>
                                        @endif
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
                                {!! Form::close() !!}
                        </section>
                        <h2>{{trans('user.last_step')}}</h2>
                        <section class="contact-list">
                            @if(isset($contacts))
                                <div class="form-horizontal clearfix">
                                    @if(isset($user))
                                        @foreach($contacts as $contact)
                                            <div class="col-md-3 contact-item {{!empty($user->contact) && in_array($contact->id, $user->contact) ? 'active' : ''}}" data-contact-id="{{$contact->_id}}">
                                                <div class="feed-box text-center pannel-content">
                                                    <section class="panel">
                                                        <div class="panel-body">
                                                            <a class="image_box" href="javascript:void 0" >
                                                                <img class="profile_img" alt="" src="{{asset($contact->avatar)}}">
                                                            </a>
                                                            <div class="description">
                                                                <div class="user-name">{{$contact->user_name}}</div>
                                                                <div class="phone">{{$contact->phone}}</div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach($contacts as $contact)
                                            <div class="col-md-3 contact-item" data-contact-id="{{$contact->_id}}">
                                                <div class="feed-box text-center pannel-content">
                                                    <section class="panel">
                                                        <div class="panel-body">
                                                            <a class="image_box" href="javascript:void 0" >
                                                                <img class="profile_img" alt="" src="{{asset($contact->avatar)}}">
                                                            </a>
                                                            <div class="description">
                                                                <div class="user-name">{{$contact->user_name}}</div>
                                                                <div class="phone">{{$contact->phone}}</div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
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
    <script src="{{ mix('build/js/jquery.validation.js') }}"></script>
    <script src="{{ mix('build/js/template_upload.js') }}"></script>
    <script src="{{ mix('build/js/iCheck.js') }}"></script>
    <script src="{{ mix('build/js/jquery.steps.js') }}"></script>

    @if(Lang::locale() != config('constants.language_file_input_js.en'))
{{--        <script src="{{ mix('build/js/te mplate_upload_language_'. Lang::locale() .'.js') }}"></script>--}}
    @endif
    <script type="text/javascript">
        $(document).ready(function () {
            @if(empty($user->contact))
            var contacts = {};
            @else
                var contacts = {};
                    @php
                        $contacts = [];
                    @endphp
                @foreach($user->contact as $item)
                    @php
                        $contacts[$item] = $item;
                    @endphp
                @endforeach
                contacts = <?php echo json_encode($contacts)?>;
            console.log(contacts);
            @endif
            console.log('start: ', contacts);
            function resizeJquerySteps() {
                var height = $('.body.current .form-horizontal').outerHeight() + 65;
                console.log('resizeJquerySteps height: ', height);
                $('#user-wizard .content').animate({ height: height }, "fast");
            }
            var form = $("#user-wizard");

            $.validator.methods.phone = function( value, element ) {
                return this.optional( element ) || /(09|01[2|6|8|9])+([0-9]{8})\b/.test( value );
            };

            $.validator.methods.userNameStrong = function( value, element ) {
                console.log(this.optional( element ));
                return this.optional( element ) || /([a-zA-Z0-9\-\_\.]+)/.test( value );
            };

            $.validator.methods.passwordStrong = function( value, element ) {
                return this.optional( element ) || /^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\`\~\!\@\#\$\%\^\&\*\(\)\_\+\=\-]).*$/.test( value );
            };

            form.steps({
                headerTag: "h2",
                bodyTag: "section",
                transitionEffect: "slideLeft",
                onStepChanging: function (event, currentIndex, newIndex){
                    if (currentIndex > newIndex){
                        return true;
                    }
                    if (currentIndex < newIndex)
                    {
                        // To remove error styles
                        form.find(".body:eq(" + newIndex + ") label.error").remove();
                        form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
                    }
                    var form_validate = form.find(".body:eq(" + currentIndex + ") form");
                    if(form_validate.length){
                        form_validate.validate({
                            rules:{
                                'phone':{
                                    required: true,
                                    phone: true
                                },
                                @if(isset($user))
                                    'user_name':{
                                        minlength: 6,
                                        userNameStrong: true
                                    },
                                    'password':{
                                        minlength: 6,
                                        passwordStrong: true
                                    },
                                @else
                                'user_name':{
                                    required: function () {
                                        return isEmpty($("#input_password").val()) ? false : true;
                                    },
                                    minlength: 6,
                                    userNameStrong: true
                                },
                                'password':{
                                    required: function () {
                                        return isEmpty($("#input_user_name").val()) ? false : true;
                                    },
                                    minlength: 6,
                                    passwordStrong: true
                                }
                                @endif
                            },
                            messages:{
                                'phone':{
                                    required: '{{trans('validation.required', ['attribute' => trans('user.field_phone')])}}',
                                    phone: '{{trans('user.msg_field_not_format', ['name' => trans('user.field_phone')])}}',
                                },
                                'user_name':{
                                    required: '{{trans('validation.required', ['attribute' => trans('user.field_user_name')])}}',
                                    userNameStrong: '{{trans('user.msg_name_not_strong')}}',
                                },
                                'password':{
                                    required: '{{trans('validation.required', ['attribute' => trans('user.field_password')])}}',
                                    passwordStrong: '{{trans('user.msg_password_not_strong')}}',
                                },
                            }
                        });
                    }else{
                        return true;
                    }
                    resizeJquerySteps();
                    return form.find(".body:eq(" + currentIndex + ") form").valid();
                },
                onStepChanged: function (event, currentIndex, priorIndex){
                    resizeJquerySteps();
                },
                onFinishing: function (event, currentIndex)
                {
                    console.log('onFinishing');
                    return true;
                },
                onFinished: function (event, currentIndex)
                {
                    $('#contact').val(JSON.stringify(contacts));
                    console.log('send data: ', contacts);
                    $('#user-wizard form').submit();
                }
            });

            setTimeout(function () {
                resizeJquerySteps();
            }, 500);

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

//            $( window ).resize(function() {
//                resizeJquerySteps();
//            });

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