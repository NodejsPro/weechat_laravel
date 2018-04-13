<?php $connect_page_id =  Route::current()->getParameter('bot'); ?>
<div class="modal file-select-modal" id="scenario-file-list">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row file-select-content">
                    <div class="col-md-12">
                        <section class="panel">
                            <header class="panel-heading tab-bg-dark-navy-blue nav-color">
                                <ul class="nav nav-tabs nav-file">
                                    <li class="nav-file-item nav-image active">
                                        <a data-toggle="tab" href="#image">{{trans('scenario.image')}}</a>
                                    </li>
                                    <li class="nav-file-item nav-video">
                                        <a data-toggle="tab" href="#video">{{trans('scenario.video')}}</a>
                                    </li>
                                    <li class="nav-file-item nav-pdf">
                                        <a data-toggle="tab" href="#pdf">{{trans('scenario.pdf')}}</a>
                                    </li>
                                </ul>
                            </header>
                            <div class="tab-content file-content">
                                <br/>
                                <div class="box_message"></div>
                                {!! Form::hidden('scenario_file_selected', null, ['id' => 'scenario_file_selected', 'class' => '']) !!}
                                @php
                                    $path_upload = $_destination.DIRECTORY_SEPARATOR.$connect_page_id.DIRECTORY_SEPARATOR;
                                    $path_sub_thumbnail = $_destination.DIRECTORY_SEPARATOR.$connect_page_id.DIRECTORY_SEPARATOR.config('constants.sub_thumbnail').DIRECTORY_SEPARATOR;
                                @endphp
                                @foreach(config('constants.file_type') as $type => $key)
                                    <div id="{{$type}}" class="tab-pane {{ $type == 'image' ? 'active' : '' }}">
                                        <div class="panel-body panel-option-scroll"></div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-cancel" data-dismiss="modal">{{{ trans('button.cancel')}}}</button>
                <button type="button" class="btn btn-info btn-file-select">{{{ trans('scenario.file_select')}}}</button>
            </div>
            <div class="file-item-content-sample" style="display: none">
                <div class="col-md-4 file-select-item" data-filename="">
                    <div class="text-center pannel-file-content file-item">
                        <section class="panel panel-resize">
                            <div class="panel-body panel-resize-body">
                                <div class="file-view">
                                    <span class="helper"></span>
                                </div>
                            </div>
                            <div class="file-item-content">
                                <h1 class="file-name-label"></h1>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="file-type">
                    <a class="image_view" title="">
                        <img class="upload-image" src="" alt="" />
                    </a>
                    <video controls="controls" class="upload-video" src="" ></video>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script>
        $(function () {
            var file_selected = '';
            runFileSelect();

            $(document).on('click', '.scenario-edit .btn-scenario-file-list', function () {
                if ($(this).data('type') != undefined && $(this).data('type') == 'radio_image') {
                    $('.file-select-modal .panel-heading .nav-video, .file-select-modal .panel-heading .nav-pdf').hide();
                    if ($(this).closest('.radio_container').find('.generic_box_content').hasClass('active')) {
                        $(this).closest('.radio_container').find('.generic_box_content').removeClass('active');
                    }
                    $(this).closest('.generic_box_content').addClass('active');
                }
                runFileSelect();
                ajaxGetFile();
                $('#scenario-file-list').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                // check type messages by show/hide tab
                $('#scenario-file-list').find('.nav-image, .nav-video, .nav-pdf').removeClass('hide');
                var current_bot_type = botMessageType();

                @if(isset($connect_page) && $connect_page->sns_type == config('constants.group_type_service.facebook'))
                    if(current_bot_type == '{{$bot_content_type['generic']}}'){
                        $('#scenario-file-list').find('.nav-video, .nav-pdf').addClass('hide');
                    }
                @elseif(isset($connect_page) && $connect_page->sns_type == config('constants.group_type_service.line'))
                    $('#scenario-file-list').find('.nav-pdf').addClass('hide');
                    switch (current_bot_type) {
                        case '{{$bot_content_type['button']}}':
                        case '{{$bot_content_type['carousel']}}':
                        case '{{$bot_content_type['imagemap']}}':
                            $('#scenario-file-list').find('.nav-video').addClass('hide');
                            break;
                    }
                @endif

                //hide type tab if exist except attribute
                var type_except = $(this).data('except');
                if(type_except != void 0 && type_except != '') {
                    type_except = type_except.split(',');
                    $(type_except).each(function (i, e) {
                        if($('#scenario-file-list').find('.nav-' + e).length) {
                            $('#scenario-file-list').find('.nav-' + e).addClass('hide');
                        }
                    });
                }
            });
            $('#scenario-file-list .file-select-item').on('click', function () {
                $('#scenario-file-list .panel-resize-body').removeClass('file-select-border');
                $(this).find('.panel-resize-body').addClass('file-select-border');
                $('#scenario-file-list #scenario_file_selected').val($(this).data('filename'));
            });
            $('#scenario-file-list .btn-file-select').on('click', function (e) {
                file_selected = $('#scenario-file-list #scenario_file_selected').val();
                if(file_selected == '') {
                    setMessageModal('{{trans('message.file_select_empty')}}', 1);
                } else {
                    $("#scenario-file-list .btn-cancel").click();
                }
            });
        });
        function runFileSelect() {
            setMessageModal('');
            file_selected = '';
            $('#scenario-file-list .panel-resize-body').removeClass('file-select-border');
            $('#scenario-file-list .nav-file-item').removeClass('active');
            $('#scenario-file-list .nav-file-item.nav-image').addClass('active');
            $('#scenario-file-list #scenario_file_selected').val('');
            $('#scenario-file-list .nav-file, #scenario-file-list .tab-pane').removeClass('active');
            $('#scenario-file-list #image').addClass('active');

        }
        function setMessageModal(message, type) {
            if (message != '' && message != null) {
                var msg_close = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                if(type == '' || type == void 0 || type == 1) {
                    $('#scenario-file-list .box_message').html('<p class="alert alert-danger">' + message + msg_close + '</p>');
                }else{
                    $('#scenario-file-list .box_message').html('<p class="alert alert-success">' + message + msg_close + '</p>');
                }
            } else {
                $('#scenario-file-list .box_message').html('');
            }
        }

        function ajaxGetFile() {
            // clear old data
            $('.tab-pane .panel-option-scroll').empty();
            var url = '{{action('FileController@getFileName', $connect_page_id)}}';
            $.ajax({
                method: "POST",
                url: url,
                data: {
                    '_token' : '{{csrf_token()}}'
                },
                success: function (result) {
                    if(result.success){
                        var data = result.data;
                        if(data){
                            var path = '{{$path_upload}}';
                            var path_thumbnail = path + '{{config('constants.sub_thumbnail').DIRECTORY_SEPARATOR}}';;
                            $.each(data, function (key, arrData) {
                                $.each(arrData , function (index, itemData) {
                                    var item = $('.file-item-content-sample .file-select-item').clone(true);
                                    //fill data
                                    item.attr('data-filename', path + itemData.file_name);
                                    item.find('.file-name-label').html(itemData.name);
                                    var file = '';
                                    if(itemData.type == '{{config('constants.file_type.image')}}'){
                                        file = $('.file-item-content-sample .image_view').clone(true);
                                        file.attr('title', itemData.name).find('.upload-image').attr({
                                            'src' : path_thumbnail + itemData.file_name,
                                            'alt' : itemData.name
                                        })
                                    }else if(itemData.type == '{{config('constants.file_type.video')}}'){
                                        file = $('.file-item-content-sample .upload-video').clone(true);
                                        file.attr('title', itemData.name).attr({
                                            'src' : path + itemData.file_name
                                        });
                                    }else{
                                        file = $('.file-item-content-sample .image_view').clone(true);
                                        file.attr('title', itemData.name).find('.upload-image').attr({
                                            'src' : '{{asset('images/pdf.png')}}',
                                            'alt' : itemData.name
                                        })
                                    }
                                    item.find('.helper').after(file);
                                    $('#' + key + ' .panel-option-scroll').prepend(item);
                                })
                            })
                        }
                    }
                },
                error: function(result){
                    var status = $.parseJSON(result.status);
                    if(status && status == '404'){
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection