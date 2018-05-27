<?php ob_end_clean();?>
@extends('layouts.app2')
@section('title') {{{ trans('title.conversations') }}} :: @parent @stop
@section('styles')
    <link href="{{ mix('build/css/iCheck.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="row conversation_index">
    <div class="col-md-12">
        <section class="panel minimum-panel">
            <header class="panel-heading tab-bg-dark-navy-blue nav-color conversation_header">
                <ul class="nav nav-tabs">
                    <li class="nav-conversation active">
                        <a data-toggle="tab" href="#conversation_tab" data-value="conversation_tab">{{{ trans('modal.conversations') }}}</a>
                    </li>
                </ul>
            </header>
            <div class="panel-body conversation_container hidden">
                @include('flash')
                <div class="box_message common_message"></div>

                <div class="tab-content">
                    <div class="tab-pane active" id="conversation_tab">
                        @include('demo.content-left')
                        @include('demo.content')
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
@section('scripts2')
    <script src="{{ mix('build/js/socket.io.js') }}"></script>
    <script src="{{ mix('/build/js/iCheck.js') }}"></script>
    <script type="text/javascript">
        'use strict';
        var default_timezone = '{{config('app.timezone')}}';
        var request_document_arr = new Object();
        $(function (){
            var user_id,
                user_login_id='{{Auth::user()->id}}',
                socket = io('{{config('app.url_socket')}}'),
                user_add_flg = [],
                log_last_time = null,
                message_limit_flg = true,
                users_show_count = parseInt('{{ 3 }}'), //get number of users are showing
                users_count_all = undefined, //number total of users
                chat_input_element = $('#chat-input'),
                room_id = null;
            socket.on('connect', function() {
                console.log('socket on connect');
                socket.emit('user_join', { user_id: user_login_id });

                socket.on('status_join', function (data) {
                    console.log('status_join: ', data);
                });

                socket.on('status_join_room', function (data) {
                    console.log('status_join_room: ', data);
                    room_id = data.room_id;
                });

                socket.on('receive_new_user', function (data) {
                    var show_user = true;
                    if(show_user){
                        getNewUser(data, false);
                    }
                });

                socket.on('server_send_message', function (data) {
                    console.log('server_send_message', data);
                    var member_name = data.member_name;
                    if(member_name != void 0 && Array.isArray(member_name)){
                        for(var i = 0; i < member_name.length; i++){
                            if(member_name[i]['id'] == data.user_id){
                                var user_send = $('<div>').html(member_name[i]['name'] + ' send message: ' + data.message);
                                $('.conversation-list').append(user_send);
                                return;
                            }
                        }
                    }
                    console.log('member empty');
                });
            });

            initSizeConversation();
            initPopover();

            //get more user
            /*$('.conversation_index .user_list_all').slimScroll().bind('slimscroll', function(e, pos){
                if(pos == 'bottom') {
                    $('.conversation_index .load_more_user').click();
                }
            });*/

            $('.conversation_index select.scenario_used').select2({
                "language": {
                    "noResults": function(){
                        return "{{trans('message.no_results_found')}}";
                    }
                }
            });

            function checkShowNewMessage(data){
                if (data.user_id == user_id && data.connect_page_id == connect_page_id) {
                    var show_new_msg = showMessage(data, request_document_arr, true);
                    if(data.type == '{{ config('constants.message_chat_type.user') }}' == show_new_msg) {
                        socket.emit('client_to_server_receive_message', { connect_page_id: '', user_id: user_id });
                    }
                    // update count notification
                    updateActiveTime(user_id);
                }else {
                    var answer_flg = true;
                    if(answer_flg){
                        getNotificationNewMessage(data);
                    }
                }
            }

            $(document).on('click', '.conversation_index .load_more_user', function (e) {
                $('#btn_modal_check_authenticate').addClass('user_filter');
                $('#btn_modal_check_authenticate').removeClass('btn_modal_check_authenticate');
                $('#btn_modal_check_authenticate').removeClass('content_chat');
                $('#conversation_authenticate_modal #inputPassword').val('');
            });

            function confirmSearch() {
                var url = '';
                $('.user_filter').on('click', function () {
                    $('#conversation_authenticate_modal .overlay-wrapper .overlay').show();
                    var data = {
                        '_token' : '{{csrf_token()}}',
                        password : $(this).parents('#conversation_authenticate_modal').find('#inputPassword').val()
                    };
                    $.ajax({
                        url: url,
                        data: data,
                        type: "POST",
                        success: function(data) {
                            $('#conversation_authenticate_modal').modal('hide');
                            filterUser();
                            var date = new Date();
                            var minutes = 10;
                            date.setTime(date.getTime() + (minutes * 60 * 1000));
                            $.cookie('conversation_authenticate', true, { expires: date });
                        },
                        error: function(result){
                            $('.overlay-wrapper .overlay').hide();
                            var errors = $.parseJSON(result.responseText);
                            if(errors.password != void 0 && errors.password != '') {
                                $('#conversation_authenticate_modal .user_password_error').show().html(errors.password);
                            }if(errors.msg != void 0 && errors.msg != '') {
                                $('#conversation_authenticate_modal .user_password_error').show().html(errors.msg);
                            }
                        }
                    });
                });
            }

            $('.conversation_index .check_key_send').on('ifClicked', function(event){
                sendKeyAjax();
            });

            $(document).on('click', '.conversation_index .switch-key-send', function(){
                sendKeyAjax();
//                $('#key_send').iCheck('toggle');
            });

            $('.conversation_index .icon-down').on('click', function () {
                $(this).hide();
                $('.icon-up, .filter-content').show();
                $.cookie("filter_open", '{{config('constants.active.enable')}}');
                changeHeightConversation();
            });

            $('.conversation_index .icon-up').on('click', function () {
                $(this).hide();
                $('.filter-content').hide();
                $('.icon-down').show();
                $.cookie("filter_open", '{{config('constants.active.disable')}}');
                changeHeightConversation();
            });
            
            $('.conversation_index .btn-search').on('click', function () {
                users_show_count = users_count_all = 0;
                users_is_limit_flg = false;
                user_id = null;
                var data = {
                    'action' : 'search',
                    'user_name' : $('input.user_name').val(),
                    'scenario_used' : $('select.scenario_used').val(),
                    'user_inputted_text' : $('input.user_inputted_text').val(),
                    'bookmark_flg' : $('input.bookmark_user').prop('checked')? '{{config('constants.active.enable')}}' : null,
                    'cv_flg' : $('input.cv_flg').prop('checked')? '{{config('constants.active.enable')}}' : null,
                    'bot_no_reply' : $('input.bot_no_reply').prop('checked')? '{{config('constants.active.enable')}}' : null
                };
                $('.conversation_index .user_list_all .user_item').remove();
                $('.conversation_index .chat_content, .conversation_index #user_all .load_more_user').hide();
                $('.conversation_index #user_all .load_more_user').attr('data-search', JSON.stringify(data)).click();
            });

            $('.conversation_index .btn-clear').on('click', function () {
                $('input.user_name, input.user_inputted_text').val('');
                $(".conversation_index .scenario_used").select2("val", '');
//                $(".conversation_index .bot_no_reply, .conversation_index .bookmark_user, .conversation_index .cv_flg").iCheck('uncheck');
                $('#btn-search').click();
            });

            function sendKeyAjax() {
                key_send = $('#key_send').prop('checked')? '{{config('constants.active.disable')}}' : '{{config('constants.active.enable')}}';
                $.ajax({
                    url: '{{ 'bot.setKeySend'}}',
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "key_send": key_send
                    },
                    success: function (data) {
                        var success = data.success;
                        if(success){
                        }
                    },
                    error: function (data) {
                        data = $.parseJSON(data.responseText);
                        if(data.msg != void 0 && data.msg != '') {
                            setMesssage(data.msg, 1, $('.conversation_index .common_message'), true);
                        }
                    }
                });
            }

            $('.conversation-list').scroll(function(){
                if($(this).scrollTop() === 0 && log_last_time != void 0){
                    getMessage(user_id, true);
                }
            });

            $(document).on('click', '.conversation_index .user_item', function (e) {
                var user_id_item = $(this).data('user_id');
                if (user_id != user_id_item && user_id_item != '' && !$(e.target).parents('.more_info_box').length) {
                    user_id = $(this).data('user_id');
                    console.log('msg: ', user_id);
                    var msg = {
                        user_id: user_login_id,
                        room_type: '{{config('constants.room_type.one_one')}}',
                        member : [user_login_id, user_id]
                    };
                    console.log('send to server: ', msg);
                    socket.emit('user_join_room', msg);
                    showContentChat(user_id);
                }
            });

            function showContentChat(user_id) {
                var user_item_active = $('.conversation_index .user_item[data-user_id=' + user_id + ']');
                $('.conversation_index .user_item').removeClass('active');
                user_item_active.addClass('active');
                user_item_active.find('.badge').addClass('hide');
                $('.conversation_index .chat_content .conversation-list').html('');

                //set avata to template
                $('.template_message .user_chat .user_avatar').attr('src', $(this).data('user_avatar'));

                log_last_time = null;
                message_limit_flg = true;
                getMessage(user_id);
                $('.conversation_index .chat_content').show();
                $('.conversation_index .notification_' + user_id).html('0');
                clearMessageSend();
            }

            function confirmContent(user_id) {
                var url = '';
                $('.content_chat').on('click', function () {
                    $('#conversation_authenticate_modal .overlay-wrapper .overlay').show();
                    var data = {
                        '_token' : '{{csrf_token()}}',
                        password : $(this).parents('#conversation_authenticate_modal').find('#inputPassword').val()
                    };
                    $.ajax({
                        url: url,
                        data: data,
                        type: "POST",
                        success: function(data) {
                            $('#conversation_authenticate_modal').modal('hide');
                            showContentChat(user_id);
                            var date = new Date();
                            var minutes = 10;
                            date.setTime(date.getTime() + (minutes * 60 * 1000));
                            $.cookie('conversation_authenticate', true, { expires: date });
                        },
                        error: function(result){
                            $('.overlay-wrapper .overlay').hide();
                            var errors = $.parseJSON(result.responseText);
                            if(errors.password != void 0 && errors.password != '') {
                                $('#conversation_authenticate_modal .user_password_error').show().html(errors.password);
                            }if(errors.msg != void 0 && errors.msg != '') {
                                $('#conversation_authenticate_modal .user_password_error').show().html(errors.msg);
                            }
                        }
                    });
                });
            }

            $(document).on('click', '.conversation_index .icon_pin', function (e) {
                var active_pin_user = $(this).hasClass('active'),
                    user_item = $(this).parents('.user_item'),
                    user_id = user_item.attr('data-user_id'),
                    icon_pin = $(this);
                if(user_id != void 0 && user_id != ''){
                    icon_pin.toggleClass('active');
                    var url = '{{ 'bot.conversation.bookmark'}}';
                    $.ajax({
                        url: url,
                        type: 'post',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "user_id": user_id,
                            "bookmark_flg" : active_pin_user ? '{{config('constants.active.disable')}}' : '{{config('constants.active.enable')}}'
                        },
                        success: function (data) {
                            var success = data.success;
                            if(success){
                            }
                        },
                        error: function (data) {
                            data = $.parseJSON(data.responseText);
                            if(data.msg != void 0 && data.msg != '') {
                                setMesssage(data.msg, 1, $('.conversation_index .common_message'), true);
                            }
                            icon_pin.toggleClass('active');
                        }
                    });
                    e.stopPropagation();
                }
            });
            $('.conversation_index .chat-send').on('click', function () {
                var message = chat_input_element.val(),
                        dataObj = {};
                if(room_id != void 0 && message != void 0 && message.trim().length > 0){
                    message = message.trim();
                    dataObj.message_type = '001';
                    dataObj.message = message;
                    dataObj.room_id = room_id;
                    dataObj.user_id = user_login_id;
                    // sendCrtData(room_id, user_id, dataObj);
                    console.log('user_send_message', dataObj);
                    socket.emit('user_send_message', dataObj);
                    clearMessageSend();
                }
            });

            $('.conversation_index .mark_all_read').on('click', function () {
                var url_mark_all_read = '{{ 'bot.conversation.markAllRead'}}';
                $.ajax({
                    url: url_mark_all_read,
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function (data) {
                        $('.conversation_index .user_item .notification').addClass('hide').html('0');
                        setMesssage('{{trans('message.success_mark_all_read')}}', 2, $('.conversation_index .common_message'), true);
                    },
                    error: function (data) {
                        data = $.parseJSON(data.responseText);
                        if(data.msg != void 0 && data.msg != '') {
                            setMesssage(data.msg, 1, $('.conversation_index .common_message'), true);
                        }
                    }
                })
            });
            
            function changeHeightConversation() {
                initUserScroll();
                initUserScroll('active');
                var filter_height = $('.conversation_index .panel-filter').innerHeight(),
                    conversation_height = $('.conversation_index .conversation_header').innerHeight();
                console.log(($(window).height() - conversation_height  - 96));
                $('.conversation_index .panel_right_conversation, .conversation_index .panel_left_conversation').css('min-height', (200) + 'px');
                var conversation_list = $('.conversation_index .conversation-list'),
                    send_height = $('.conversation_index .right_conversation .send_wrapper').innerHeight();

                conversation_list.slimscroll({
                    height : ($(window).height() - conversation_height - filter_height - send_height - 120) + 'px',
                    wheelStep: 20
                });
            }

            $('.conversation_index #chat-input').keyup(function(e){
                // truong hop enter: send, ctr+enter: new line
                if(key_send == '{{config('constants.active.enable')}}'){
                    if ((e.keyCode == 10 || e.keyCode == 13) && (e.ctrlKey || e.shiftKey)){
                        $(this).val(function(i,val){
                            return val + (e.shiftKey ? "" : "\n");
                        });
                    }else if(e.keyCode == 13){
                        sendText();
                    }
                } else{
                // truong hop enter: new line, ctr+enter: send
                    if ((e.keyCode == 10 || e.keyCode == 13) && (e.ctrlKey || e.shiftKey)){
                        sendText();
                    }
                }
            }).keypress(function(e){
                var tmp_val = $(this).val();
                if(key_send == '{{config('constants.active.enable')}}'){
                    if(e.keyCode == 13 && tmp_val.trim().length == 0){
                        e.preventDefault();
                    }
                }
            });

            function sendText() {
                $('.chat-send').click();
            }

            function fillUserMessage(message_data, sns_type, appendHead) {
                if(message_data.message != void 0 && message_data.message != '') {
                    var message = message_data.message;
                    var user_chat = $('.template_message .user_chat').clone();
                    var time_of_message = '';
                    //set time to message
                    if(message_data['created_at'] != void 0) {
                        time_of_message = getDateByTimezone(message_data['created_at'], 'H:mm:ss');
                    }
                    user_chat.find('.time_of_message').html(time_of_message);

                    //message type
                    user_chat.find('.ctext-wrap').addClass('user_text').addClass('user-border');
                    var text = '';
                    if(message.text != void 0 && message.text != ''){
                        text = message.text;
                    } else if(message != void 0 && message != ''){
                        text = message;
                    }
                    user_chat.find('.text_message').html(text);
                    if(appendHead != void 0 && appendHead){
                        $('.conversation_index .chat_content .conversation-list').prepend(user_chat);
                    }else{
                        $('.conversation_index .chat_content .conversation-list').append(user_chat);
                    }
                }
            }

            function fillBotChatFacebook(message_data, appendHead) {
                var bot_chat = $('.template_message .bot_chat').clone(),
                    time_of_message = '';

                //set time to message
                if(message_data['created_at'] != void 0) {
                    time_of_message = getDateByTimezone(message_data['created_at'], 'H:mm:ss');
                }
                bot_chat.find('.time_of_message').html(time_of_message);
                if(typeof message_data.message === "string" ){
                    bot_chat.find('.ctext-wrap').addClass('text');
                    bot_chat.find('.text_message').html(str_replace(message_data.message));
                }else if(message_data.message != void 0 && message_data.message.attachment != void 0 && message_data.message.attachment.payload != void 0 && message_data.message.attachment.payload.buttons != void 0 ){
                    bot_chat = viewFacebookButton(bot_chat, message_data.message);
                }else if(message_data.message != void 0 && message_data.message.quick_replies != void 0){
                    bot_chat = viewFacebookQuickReply(bot_chat, message_data.message);
                }else if(message_data.message != void 0 && message_data.message.attachment != void 0 && message_data.message.attachment.payload != void 0 && message_data.message.attachment.payload.elements != void 0 ){
                    bot_chat = viewGeneric(bot_chat, message_data);
                }else if(message_data.message != void 0 && message_data.message.attachment != void 0 && message_data.message.attachment.payload != void 0 && message_data.message.attachment.payload.url != void 0){
                    bot_chat = viewFile(bot_chat, message_data, false);
                }else if(typeof message_data.message.text == 'string'){
                    bot_chat.find('.ctext-wrap').addClass('text');
                    bot_chat.find('.text_message').html(str_replace(message_data.message.text));
                }else if(message_data.message != void 0 && (message_data.message.mail != void 0 || message_data.message.api != void 0)){
                    return;
                }
                if(appendHead != void 0 && appendHead){
                    $('.conversation_index .chat_content .conversation-list').prepend(bot_chat);
                }else{
                    $('.conversation_index .chat_content .conversation-list').append(bot_chat);
                }
            }

            function fillBotChatWebchat(message_data, appendHead) {
                var message = message_data.message;
                if(message != void 0 && message != '' && message.constructor == Array){
                    for(var i = 0 ; i < message.length; i++){
                        fillBotChatFacebook({
                            created_at : message_data.created_at,
                            message : message[i],
                        }, appendHead)
                    }
                }else if(message != void 0 && message != ''){
                    fillBotChatFacebook({
                        created_at : message_data.created_at,
                        message : message_data.message
                    }, appendHead)
                }
            }

            function fillBotChatLine(message_data, appendHead) {
                var message = message_data.message;
                if(message != void 0 && message != '' && message.constructor == Array){
                    for(var i = 0 ; i < message.length; i++){
                        var bot_chat = $('.template_message .bot_chat').clone(),
                            time_of_message = '';

                        bot_chat.addClass('bot_chat_line');
                        //set time to message
                        if(message_data['created_at'] != void 0) {
                            time_of_message = getDateByTimezone(message_data['created_at'], 'H:mm:ss');
                        }
                        bot_chat.find('.time_of_message').html(time_of_message);
                        if(message[i].type != void 0 && message[i].type == 'text'){
                            bot_chat.find('.text_message').html(str_replace(message[i].text));
                            bot_chat.find('.ctext-wrap').addClass('text');
                        }else if(message[i].template != void 0 && message[i].template.type == 'buttons'){
                            bot_chat = viewLineButton(bot_chat, message[i]);
                        }else if(message[i].template != void 0 && message[i].template.type == 'carousel'){
                            bot_chat = viewLineCarousel(bot_chat, message[i], message_data._id + '_element_' + i);
                        }else if(message[i] != void 0 && message[i].type == 'image'){
                            bot_chat = viewLineImage(bot_chat, message[i]);
                        }else if(message[i].template != void 0 && message[i].template.type == 'confirm'){
                            bot_chat = viewLineConfirm(bot_chat, message[i]);
                        }else if(message[i].type == 'location'){
                            bot_chat.find('.ctext-wrap').addClass('location_wrap');
                            bot_chat = viewLineLocation(bot_chat, message[i]);
                        }else if(message[i].type == 'sticker') {
                            bot_chat = viewLineSticker(bot_chat, message[i]);
                        }
                        if(appendHead != void 0 && appendHead){
                            $('.conversation_index .chat_content .conversation-list').prepend(bot_chat);
                        }else{
                            $('.conversation_index .chat_content .conversation-list').append(bot_chat);
                        }
                    }
                }
            }

            function getMessage(user_id, appendHead) {
                if(message_limit_flg != void 0 && message_limit_flg){
                    message_limit_flg = false;
                    var url_get_message = '{{url('api/demo/getConversation')}}';
                    $.ajax({
                        url: url_get_message,
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "user_id" : user_id,
                            'log_last_time': log_last_time,
                            "member" : [user_login_id, user_id]
                        },
                        type: 'POST',
                        success: function(data) {
                            var last_date = '';
                            // fill data log chat
                            if(data != void 0 && data.log_messages != void 0) {
                                if(data.log_last_time != void 0){
                                    log_last_time = data.log_last_time;
                                }
                                var data_log_message;
                                if(appendHead != void 0 && appendHead){
                                    data_log_message = data.log_messages;
                                }else{
                                    data_log_message = data.log_messages.reverse();
                                }
                                if(data_log_message.length != 0){
                                    message_limit_flg = true;
                                }
                                $.each(data_log_message, function(index, msg_data) {
                                    if(msg_data != void 0){
                                        if(index >= (data.log_messages.length - 1)) {
                                            last_date = msg_data.created_at;
                                        }
                                    }
                                });
                                $('.conversation_index .chat_content').removeClass('hidden');
                            }
                            //scroll
                            if(appendHead != void 0 && appendHead){
                                if(!message_limit_flg){
                                    initConversationScroll(false);
                                }else{
                                    $('.conversation-list').animate({scrollTop: '20'});
                                }
                            }else{
                                initConversationScroll(true);
                            }
                            changeHeightConversation();
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }
            }

            function CheckCarousel(prev, next) {
                if(prev.hasClass('slick-disabled') || prev.hasClass('slick-hidden')){
                    prev.hide();
                }else{
                    prev.show();
                }
                if(next.hasClass('slick-disabled') || next.hasClass('slick-hidden')){
                    next.hide();
                }else{
                    next.show();
                }
            }

            function initCarousel() {
                var carousels = $('.conversation_index .conversation-list .carousel_message .carousel-inner').not('.slick-initialized');
                if(carousels.length) {
                    $(carousels).each(function (i, e) {
                        var carousel_width = 256;
                        var next = $(this).parent().find('.right.carousel-slick-control'),
                            prev = $(this).parent().find('.left.carousel-slick-control'),
                            slidesToShow = 1;
                        if($(this).find('.item').length > 1){
                            carousel_width = 515;
                            slidesToShow = 2;
                        }
                        $(this).parent().css('width', carousel_width + 'px');
                        var option = {
                            slidesToScroll: 1,
                            arrows: true,
                            dots: false,
                            infinite: false,
                            autoplay: false,
                            cssEase: 'linear',
                            nextArrow: next,
                            prevArrow: prev
                        };
                        option.slidesToShow = slidesToShow;
                        $(this).on('init', function(event, slick){
                            CheckCarousel(prev, next);
                            var number_button = slick.$list.find('.slick-current .button').length;
                            if(number_button != void 0){
                                prev.addClass('type_' + number_button);
                                next.addClass('type_' + number_button);
                            }
                        });

                        $(this).slick(option).on('afterChange', function(event, slick, currentSlide, nextSlide){
                            CheckCarousel(prev, next)
                        })
                    });
                    carousels.slick("refresh");
                }
            }

            function showMessage(msg_data, log_request_document, is_new_msg, appendHead) {
                var result = true;
                if(msg_data != void 0){
                    try {
                        if (msg_data.type == '{{ config('constants.message_chat_type.user') }}') {
                            fillUserMessage(msg_data, '{{ '' }}', appendHead);

                        } else if (msg_data.type == '{{ config('constants.message_chat_type.bot') }}' && msg_data.message != void 0) {
                            fillBotChatFacebook(msg_data, appendHead);
                        }
                    } catch (error){
                        console.log(error);
                        result = false;
                    }
                }
                if(is_new_msg) {
                    //show send box when receive new message
                    $('.conversation_index .send_wrapper').removeClass('hidden');

                    $('.conversation_index .conversation-list').animate({
                        scrollTop: $('.conversation_index .conversation-list')[0].scrollHeight
                    }, 200);
                    initConversationScroll(true);
                    $('.conversation_index .panel_right_conversation').css('min-height', ($(window).height() - 96) + 'px');
                    initCarousel();
                    $('.slick-slider').slick("refresh");
                }
                return result;
            }

            function getNotificationNewMessage(data_msg) {
                //reset notification and time active for one user
                if((data_msg != void 0 && data_msg.user_id != void 0 && data_msg.type == 1)) {
                    var user_msg_id = data_msg.user_id,
                        user_item = $('.conversation_index .user_item[data-user_id=' + user_msg_id + ']');

                    if(user_item.length) {
                        var noti_elm = user_item.find('.notification_' + user_msg_id),
                            noti_new = !isNaN(parseInt(noti_elm.html())) ? parseInt(noti_elm.html()) : 0;
                        noti_new++;
                        user_item.find('.notification_' + user_msg_id).removeClass('hide').html(noti_new);
                        updateUserActiveTime(user_msg_id, data_msg.created_at);
                    }

                }
            }

            function getNewUser(user_data, is_load_more) {
                var user_elm = $('#user_all .conversation_index .user_item[data-user_id=' + user_data.user_id + ']');
                if(user_elm.length <=0 && user_data != void 0) {
                    var new_user = $('.template_message .user .user_item').clone();
                    var user_list_all = $('.conversation_index .user_list_all');
                    var user_name;
                    var profile_pic = '{{('build/images/no_avatar.png')}}';
                    //set info
                    new_user.attr('data-user_id', user_data.user_id);
                    new_user.addClass('user_' + user_data.user_id);
                    if(user_data.profile_pic !== void 0 && user_data.profile_pic !== ''){
                        profile_pic = user_data.profile_pic;
                    }
                    new_user.attr('data-user_avatar', profile_pic);
                    new_user.find('.user_name').html(user_data.user_full_name);
                    new_user.find('img').attr('src', profile_pic);
                    new_user.find('img').remove();
                    if(user_data.user_full_name != void 0){
                        user_name = user_data.user_full_name;
                    }else{
                        user_name = "{{trans('default.user')}}";
                        if(user_data.number_index){
                            user_name += ' ' + user_data.number_index;
                        }
                    }
                    if(user_data.user_email != void 0){
                        new_user.find('.user_email').html(user_data.user_email);
                    }
                    new_user.find('.user_name').html(user_name);
                    new_user.find('.notification').addClass('notification_' + user_data.user_id);
                    new_user.find('.user_date').html(getDateByTimezone(user_data.last_active_at));

                    if(is_load_more) {
                        user_list_all.find('.load_more_user').before(new_user);
                    } else {
                        user_list_all.prepend(new_user);
                    }

                    //hide load more user if get all, increase count attribute
                    users_show_count += 1;
                    if(users_show_count >= users_count_all) {
                        users_is_limit_flg = true;
                        $('.conversation_index .load_more_user').hide();
                    } else{
                        users_is_limit_flg = false;
                        $('.conversation_index #user_all .load_more_user').show();
                    }

                    initPopover();
                }
            }

            function clearMessageSend() {
                chat_input_element.val('').attr('placeholder', '{{trans('message.send_text')}}');
            }

            function sendCrtData(room_id, user_id, dataObj) {
                socket.emit('user_send_message', { room_id: room_id, user_id: user_id, data: dataObj});
            }

            function initSizeConversation() {
                initUserScroll();
                // initUserScroll('active');
                initConversationScroll();
                var filter_height = $('.conversation_index .panel-filter').innerHeight();
                var conversation_height = $('.conversation_index .conversation_header').innerHeight();

                $('.conversation_index .panel_right_conversation, .conversation_index .panel_left_conversation').css('min-height', (200) + 'px');
                $('.conversation_index .conversation_container').removeClass('hidden');
                console.log($(window).height() , '-', conversation_height);

            }

            function initUserScroll(tab_category) {
                var user_list_all = $('.conversation_index .user_list_all'),
                    filter_height = $('.conversation_index .panel-filter').height(),
                    conversation_height = $('.conversation_index .conversation_header').innerHeight();
                // if(tab_category == 'active') {
                //     user_list_all = $('.conversation_index .user_list_active');
                // }
                console.log( ($(window).height() - conversation_height) );
                user_list_all.slimscroll({
                    height : ($(window).height() - conversation_height - 160) + 'px',
                    wheelStep: 20
                });
            }

            function initConversationScroll(scroll_bottom) {
                var conversation_list = $('.conversation_index .conversation-list'),
                    send_height = $('.conversation_index .right_conversation .send_wrapper').innerHeight(),
                    filter_height = $('.conversation_index .panel-filter').innerHeight(),
                    conversation_height = $('.conversation_index .conversation_header').innerHeight(),
                    scroll_pos = 0;
                if(scroll_bottom) {
                    scroll_pos = conversation_list[0].scrollHeight;
                }

                conversation_list.slimscroll({
                    height : ($(window).height() - conversation_height - filter_height - send_height - 140) + 'px',
                    scrollTo: scroll_pos,
                    wheelStep: 20
                });
            }

            function updateUserActiveTime(user_id, date) {
                var user_item = $('.conversation_index .user_item[data-user_id=' + user_id + ']');
                //update time actve
                var time = getDateByTimezone(date);
                user_item.find('.user_date').html(time);
            }

            function viewFile(box_chat, message_data, is_user) {
                if(!is_user) {
                    message_data =  message_data.message.attachment;
                } else {
                    message_data =  message_data.message[0];
                }
                box_chat.find('.conversation-text').remove();

                var image = '<a class="link_file" target="_blank"><img class="img_file"></a>',
                    video = '<video controls="controls" class="video" src="" ></video>';
                var file_type = message_data.type,
                    file_url = message_data.payload.url,
                    file_preview = (file_type == 'file') ? '{{asset('images/pdf.png')}}' : file_url;

                var conversation_file = $('.template_message .conversation_file').clone();
                if (file_type == 'image' || file_type == 'file') {
                    conversation_file.append(image);
                    conversation_file.find('.img_file').attr('src', file_preview);
                    if(message_data.payload.sticker_id == void 0) {
                        conversation_file.find('.link_file').attr('href', file_url);
                    }
                    if (file_type == 'file') {
                        conversation_file.find('.img_file').addClass('file');
                    }
                    //if is sticker then not use tyle border radius
                    if(message_data.payload.sticker_id != void 0 && message_data.payload.sticker_id != '') {
                        conversation_file.removeClass('radius');
                    }

                } else if (file_type == 'video') {
                    conversation_file.append(video);
                    conversation_file.find('.video').attr('src', file_url);
                }
                box_chat.append(conversation_file);

                return box_chat;
            }

            function viewGeneric(box_chat, message_data) {
                box_chat.find('.conversation-text').remove();
                var carousel = $('.conversation_index .template_message .carousel_message').clone();
                var carousel_id = 'carousel_' + new Date().getTime();
                carousel.attr('id', carousel_id);
                carousel.find('.carousel-slick-control').attr('href', '#' + carousel_id);

                if(message_data.message != void 0 && message_data.message.attachment != void 0 && message_data.message.attachment.payload != void 0 && message_data.message.attachment.payload.elements != void 0) {
                    $.each(message_data.message.attachment.payload.elements, function (index, item) {
                        var carousel_item = $('.conversation_index .template_message .carousel_item .item').clone();
                        if(index == 0) {
                            carousel_item.addClass('active');
                        }
                        carousel_item.find('.carousel-caption-top').html(item.title);
                        carousel_item.find('.carousel-sub-caption').html(item.subtitle);
                        if(item.image_url != void 0 && item.image_url != '') {
                            carousel_item.find('.image img').attr('src', item.image_url);
                        } else {
                            carousel_item.find('.image img').remove();
                        }
                        //button list
                        if(item.buttons != void 0) {
                            $.each(item.buttons, function (i, button) {
                                var btn_item = $('.template_message .button').clone();
                                btn_item.find('a').html(button.title);
                                var btn_url = null, link = btn_item.find('a');
                                if(button.type == 'web_url') {
                                    btn_url = button.url;
                                    link.attr('href', btn_url);
                                }else{
                                    link.removeAttr('href');
                                    link.removeAttr('target')
                                }
                                btn_item.find('a').attr('href', btn_url);
                                carousel_item.append(btn_item);
                            });
                        }
                        carousel.find('.carousel-inner').append(carousel_item);
                    });
                    box_chat.append(carousel);
                }
                return box_chat;
            }

            function viewFacebookButton(bot_chat, message_data) {
                bot_chat.find('.ctext-wrap').addClass('text2 button-title');
                bot_chat.find('.conversation_file').remove();
                bot_chat.find('.text_message').html(message_data.attachment.payload.text);
                $.each(message_data.attachment.payload.buttons, function (ind, val) {
                    var btn_item = $('.template_message .button').clone(),
                        link =  btn_item.find('a');
                    if(val.type == 'web_url'){
                        link.attr('href', val.url);
                    }else{
                        link.removeAttr('href');
                        link.removeAttr('target')
                    }
                    link.html(val.title);
                    bot_chat.find('.ctext-wrap').append(btn_item)
                });
                return bot_chat
            }

            function viewFacebookQuickReply(bot_chat, message_data) {
                bot_chat.find('.conversation_file').remove();
                bot_chat.find('.ctext-wrap').addClass('text2 quick_reply');
                if(message_data != void 0 && message_data.quick_replies != void 0 && message_data.text != void 0 && message_data.text != '') {
                    bot_chat.find('.text_message').html('<span class="quick_reply_text">' + ((message_data.text != void 0) ? message_data.text : '' + '</span>'));
                    $.each(message_data.quick_replies, function (index, item) {
                        var btn_item;
                        if(item.content_type == 'location'){
                            btn_item = $('.template_message .fb_template .quick_replies_location_item').clone();
                        }else{
                            btn_item = $('.template_message .fb_template .quick_replies_item').clone();
                            btn_item.html(item.title);
                        }
                        btn_item.attr('href', item.url);
                        bot_chat.find('.ctext-wrap').append(btn_item);
                    });
                }
                return bot_chat;
            }

            function viewLineButton(bot_chat, message_data) {
                var img_preview = $('.template_message .line_button_image').clone();
                img_preview.find('img').attr('src', message_data.template.thumbnailImageUrl);
                bot_chat.find('.conversation_file').remove();
                bot_chat.find('.text_message').html(message_data.template.title);
                bot_chat.find('.text_alt').html(message_data.altText);
                bot_chat.find('.ctext-wrap').addClass('text2 button-title').prepend(img_preview);
                $.each(message_data.template.actions, function (ind, val) {
                    var btn_item = $('.template_message .button').clone();
                    if(val.type == 'uri'){
                        btn_item.find('a').attr('href', val.uri);
                    }
                    btn_item.find('a').html(val.label);
                    bot_chat.find('.ctext-wrap').append(btn_item);
                });
                return bot_chat;
            }

            function viewLineCarousel(bot_chat, message_data, carousel_id){
                bot_chat.find('.conversation-text').remove();
                var carousel = $('.conversation_index .template_message .carousel_message').clone();
                carousel_id = 'carousel_' + carousel_id;
                carousel.attr('id', carousel_id);
                carousel.find('.carousel-slick-control').attr('href', '#' + carousel_id);

                if(message_data.template != void 0 && message_data.template.columns != void 0) {
                    $.each(message_data.template.columns, function (index, item) {
                        var carousel_item = $('.conversation_index .template_message .carousel_item .item').clone();
                        if(index == 0) {
                            carousel_item.addClass('active');
                        }
                        carousel_item.find('.carousel-caption-top').html(item.title);
                        carousel_item.find('.carousel-sub-caption').html(item.text);
                        if(item.thumbnailImageUrl != void 0 && item.thumbnailImageUrl != '') {
                            carousel_item.find('.image img').attr('src', item.thumbnailImageUrl);
                        } else {
                            carousel_item.find('.image img').remove();
                        }
                        //button list
                        if(item.actions != void 0) {
                            $.each(item.actions, function (i, button) {
                                var btn_item = $('.template_message .button').clone();
                                btn_item.find('a').html(button.label);
                                var btn_url = null;
                                if(button.type == 'uri') {
                                    btn_url = button.uri;
                                }
                                btn_item.find('a').attr('href', btn_url);
                                carousel_item.append(btn_item);
                            });
                        }
                        carousel.find('.carousel-inner').append(carousel_item);
                    });
                    bot_chat.append(carousel);
                }
                return bot_chat;
            }

            function viewLineImage(bot_chat, message_data) {
                bot_chat.find('.ctext-wrap').addClass('text2 image_wrap');
                var image_item = $('.template_message .line_button_image').clone();
                image_item.find('img').attr('src', message_data.previewImageUrl);
                bot_chat.find('.text_message').html(image_item);
                return bot_chat;
            }

            function viewLineConfirm(bot_chat, message_data) {
                bot_chat.find('.ctext-wrap').addClass('line_confirm');
                bot_chat.find('.text_message').html(message_data.template.text);
                $.each(message_data.template.actions, function (index, value) {
                    var button = $('.line_template .confirm .button_item').clone();
                    button.find('a').attr('href', value.uri).html(value.label);
                    bot_chat.find('.text_alt').append(button);
                });
                return bot_chat;
            }

            function viewLineLocation(bot_chat, message_data) {
                var location_item = $('.line_template .location').clone(),
                    a_item = location_item.find('a').attr('href');
                a_item = a_item.replace(':lat_coordinates', message_data.latitude);
                a_item = a_item.replace(':long_coordinates', message_data.longitude);
                a_item = a_item.replace(':location_address', encodeURIComponent(message_data.address));
                location_item.find('a').attr('href', a_item);
                location_item.find('a').find('.title').html(message_data.title);
                location_item.find('a').find('.address').html(message_data.address);
                bot_chat.find('.text_message').html(location_item);
                return bot_chat;
            }

            function viewLineSticker(bot_chat, message_data){
                var url,
                    image = '<img class="img_sticker">';
                url = '/images/line_sticker/package_' + message_data.packageId + '/' + message_data.stickerId + '_key.png';
                bot_chat.find('.ctext-wrap').append(image);
                bot_chat.find('.img_sticker').attr('src', url);
                return bot_chat;
            }

            function showCreateMessage() {
                var right_conversation_left =  $('.conversation_index .panel_right_conversation').offset().left;
                var conversation_index_left =  $('.conversation_index').offset().left;
                var width = 670;
                var width_left = right_conversation_left - conversation_index_left;
                if(width_left < 0){
                    width = '98%';
                } else if(width_left < 340){
                        width = 2*width_left + 20;
                }
                var settings = {
                    trigger:'manual',
                    width: width,
                    height: 450,
                    closeable:true,
                    delay:300,
                    padding:false,
                    url:'.scenario-edit.content_message',
                    animation: 'pop',
                    title:'<h5>{{trans("title.create_message")}}</h5>',
                    onShow: function (element) {
                        $('.webui-popover .btn_action .checkValidate').on('click', function (ev) {
                            var check = checkErrorBoxMessage();
                            if (check) {
                                $('.right_conversation .scenario').webuiPopover('hide');
                                $('.panel_right_conversation #chat-input').focus();
                            }
                        });
                        cancelCreate();
                    },
                    onHide: function(element) {
                        clearHistory();
                    }
                };
//                $('.right_conversation .scenario').webuiPopover('destroy').webuiPopover(settings);
                $('.right_conversation .scenario').webuiPopover(settings);
                var content_message = $('.webui-popover .scenario_block  textarea.messages_bot_content').val();
                $('.right_conversation .scenario').on('click',function(e){
                    e.stopPropagation();
                    if (!content_message) {
                        $('.right_conversation .scenario').webuiPopover('show');
                        $('.webui-popover .fixedsidebar select#bot_content_type').val(bot_content_type[Object.keys(bot_content_type)[0]]).trigger('change');
                    }
                });

                //set constant variable for variable js common
                var template_button = '';
                setDataCommon('validation_msg_list', validation_msg_list);

                //change type of button data and scenario button
                $(document).on('change', '.button_group select.button_sub_type, .generic_group select.button_sub_type', function (event) {
                    var btn_box = $(this).parents('.button_type_container'),
                        button_image_box    = btn_box.find('.button_image_box'),
                        button_data_box     = btn_box.find('.button_data_box'),
                        button_scenario_box = btn_box.find('.button_scenario_box'),
                        placeholder         = '';
                        button_image_box.hide();
                        button_scenario_box.hide();
                        button_data_box.show();
                        btn_box.find('.button_sub_data').removeClass('validate-require validate-url validate-white-list');
                    switch ($(this).val()) {
                        case "{{ 'web_url' }}":
                            placeholder = '{{ trans('scenario.url') }}';
                            btn_box.find('.button_sub_data').addClass('validate-require validate-url validate-white-list');
                            break;
                    }
                    btn_box.find('.button_sub_data').attr('placeholder', placeholder);
                });

                $('.conversation_index .send_wrapper .chat-input').textcomplete([
                    {
                        match: /@(\w*)$/,
                        search: function (term, callback) {
                            callback($.map(variable_list, function (element) {
                                return element.indexOf(term) === 0 ? element : null;
                            }));
                        },
                        index: 1,
                        replace: function (element) {
                            return ['\{\{' + element + '}}', ''];
                        }
                    }
                ], {
                    maxCount: 1000,
                    listPosition: function (object_position) {
                        var position = "absolute";
                        var window = $(window);
                        this.$inputEl.add(this.$inputEl.parents()).each(function() {
                            var result;
                            switch ($(this).css("position")){
                                case "absolute":
                                    result = false;
                                    break;
                                case "fixed":
                                    result = (object_position.top -= window.scrollTop(), object_position.left -= window.scrollLeft(), position = "fixed", !1);
                                    break;
                                default:
                                    result = void 0;
                            }
//                            object_position.top -= 20;
                            return result;
                        });
                        this.$el.css(this._applyPlacement(object_position));
                        this.$el.css({
                            position: position
                        });
                        return this
                    }
                })
            }

            function clearHistory(){
                var content_message = $('.webui-popover .scenario_block  textarea.messages_bot_content').val();
                if (content_message != void 0 && content_message.length > 0) {
                    content_message = $.parseJSON(content_message);
                    if (!$.isEmptyObject(content_message.message)) {
                        $('.right_conversation .scenario .badge.up').css('display', 'inline-block', 'important');
                    }
                }else{
                    $('.right_conversation .scenario .badge.up').css('display', 'none');
                }
            }

            function showHideSendBox(last_date) {
                if(last_date != void 0 && last_date != '') {
                    var time_for_reply = parseFloat('{{ config('constants.conversation_option.time_for_reply') }}'),
                        time_last_msg = getDateByTimezone(last_date),
                        time_current = getDateByTimezone(new Date());

                    time_last_msg = new Date(time_last_msg);
                    time_current = new Date(time_current);
                    var time_space = (time_current - time_last_msg)/1000/60;

                    //example value for time_with_last_msg with space 30 minutes: -30
                    if(time_space > time_for_reply) {
                        $('.conversation_index .send_wrapper').addClass('hidden');
                    } else {
                        $('.conversation_index .send_wrapper').removeClass('hidden');
                    }
                }
            }

            function cancelCreate() {
                $('.webui-popover .btn_action .cancel').on('click', function (ev) {
                    $('.webui-popover .scenario_block  textarea.messages_bot_content').val('');
                    $('.right_conversation .scenario .badge.up').css('display', 'none');
                    $('.webui-popover .fixedsidebar select#bot_content_type').val(0).trigger('change');
                    clearMessageSend();
                    $('.right_conversation .scenario').webuiPopover('hide');
                });

            }

            //show more info
            function initPopover() {
                //flg to check for popover not run twice
                $('.conversation_index .left_conversation .user_item [data-toggle="popover"]').popover({
                    trigger: "manual",
                    html: true,
                    content: function() {
                        $('.conversation_index .left_conversation .user_item .popover').popover("hide");
                        console.log('sdsd');
                        return $('.template_message .user .more_info_content').html();
                    }
                }).on("click", function () {
                    var more_info_box = $(this).parents('.more_info_box');
                    if(!more_info_box.find('.popover.in').length) {
                        //get ajax user variable for template .table_more_info
                        var user_id = $(this).parents('li.user_item').data('user_id');
                        getUserVariable(user_id);

                        $(this).popover("show");
                    } else {
                        $(this).popover("hide");
                    }
                });

                $(document).on('click', function (e) {
                    if (!$(e.target).parents('.more_info_box').length) {
                        $('.conversation_index .left_conversation .user_item .popover').popover("hide");
                    }
                });
            }

            //get user by id and add to top list
            function getUser(user_data) {
                var result = true;
                var class_user_active = $('.conversation_index .user_item[data-user_id=' + user_data.user_id + ']');
                if(class_user_active.length == 0 && user_data.start_flg != void 0 && user_data.start_flg && user_data.user_id != void 0){
                    user_add_flg.push(user_data.user_id);
                    var url = '{{ 'bot.conversation.getNewUser'}}';
                    url = url.replace(':user_id', user_data.user_id);
                    $.ajax({
                        url: url,
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        type: 'POST',
                        async: false,
                        success: function(result) {
                            var success = result.success;
                            if(success){
                                users_count_all = result.users_count_all;
                                getNewUser(result.user_profile, false);
                            } else {
                                result = false;
                            }
                        },
                        error: function(error){
                            result = false;
                            console.log(error);
                        }
                    });
                }
                return result;
            }

            function updateActiveTime(user_id){
                var url_update_active_time = '{{ 'bot.conversation.notification'}}';
                    url_update_active_time = url_update_active_time.replace(':user_id', user_id);
                $.ajax({
                    url: url_update_active_time,
                    type: 'GET',
                    success: function(data) {
                    },
                    error: function(result){
                    }
                });
            }
        });

        function getDateByTimezone(created_at, format) {
            @if($date_format && $date_format_js_str)
            if(format == void 0 || format == '') {
                format = '{{$date_format_js_str}} H:mm:ss';
            }
            if(created_at != void 0){
                var m = moment.tz(created_at, default_timezone);
                return m.clone().tz('{{$date_format->getTimezone()->getName()}}').format(format);
            }
            @endif
                return '';
        }

        function setBorderChatEfo() {
            var borderChat = $('.conversation_index .conversation-efo .bot-border');
            var max_width = (Math.max.apply(Math, borderChat.map(function(){ return $(this).outerWidth(); }).get()));
            if(max_width > 300){
                borderChat.width(max_width)
            }
        }


        function str_replace(str) {
            if(str){
                str = str.replace(/ /g, '&nbsp;');
                return str.replace(/\n/g, '<br/>');
            }
        }

    </script>
@endsection

