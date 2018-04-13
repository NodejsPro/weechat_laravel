<div id="scenario_fb_preview" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{{ trans('scenario.preview') }}}</h4>
            </div>
            <div class="modal-body">
                <!--Generic carousel aria-->
                <div id="generic_preview" class="carousel slide mesage_preview">
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox"></div>
                    <!-- Controls -->
                    <a class="left carousel-control" href="#generic_preview" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    </a>
                    <a class="right carousel-control" href="#generic_preview" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    </a>
                </div>

                {{--Button aria--}}
                <div id="button_preview" class="mesage_preview">
                    <div class="button_title"></div>
                </div>

                {{--File aria--}}
                <div id="file_preview" class="mesage_preview"></div>

                {{--Quick replies aria--}}
                <div id="quick_replies_preview" class="mesage_preview">
                    <span class="title"></span>
                    <div class="button_container"></div>
                </div>

                {{--origin template--}}
                <div class="origin_item hidden">
                    <div class="item">
                        <div class="carousel-top">
                            <div class="image">
                                <img src="" alt="">
                            </div>
                            <div class="carousel-caption-top"></div>
                            <div class="carousel-sub-caption"></div>
                        </div>
                    </div>
                    <div class="carousel-button">
                        <a href=""></a>
                    </div>
                    <div class="file_template">
                        <img class="image_view" src="" alt="" />
                        <img class="pdf_view" alt="" src="{{asset('images/pdf.png')}}">
                        <video class="video_view" controls="controls" src=""></video>
                    </div>
                    <span class="quick_replies_item"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.bot_scenario .bot_media_demo .preview', function(e){
            var bot_scenario    = $(this).parents('.bot_scenario'),
                data            = bot_scenario.find('textarea.messages_bot_content').val(),
                type            = bot_scenario.find('input.messages_bot_type').val();
            //clear old data
            $('#scenario_fb_preview #generic_preview .item').remove();
            $('#scenario_fb_preview #button_preview .carousel-button').remove();
            $('#button_preview .button_title').html('');
            $('#scenario_fb_preview .mesage_preview').hide();
            $('#quick_replies_preview .button_container').html('');
            $('#file_preview').html('');
            //init data
            if(data) {
                data = JSON.parse(data);
                initPreview(data['message'], type);
            }
            checkCarouselPreview();
        });
        // setting carousel
        $('#scenario_fb_preview #generic_preview').carousel({
            interval: false,
            wrap: false
        });
        $('#scenario_fb_preview #generic_preview').bind('slid.bs.carousel', function (e) {
            checkCarouselPreview();
        });
    });

    //set generic data
    function initPreview(data, type) {
        @if(isset($connect_page) && ($connect_page->sns_type == config('constants.group_type_service.facebook')
            || $connect_page->sns_type == config('constants.group_type_service.web_embed')))
            if(type == '{{ $bot_content_type['quick_replies'] }}') {
                setQuickReplyPreview(data);
                return;
            } else {
                if (data != void 0 && data.attachment != void 0 && data.attachment.payload != void 0) {
                    var data_preview = data.attachment.payload;
                    switch (type) {
                        case '{{ $bot_content_type['generic'] }}':
                            setGenericPreview(data_preview);
                            break;
                        case '{{ $bot_content_type['button'] }}':
                            setButtonPreview(data_preview);
                            break;
                        case '{{ $bot_content_type['file'] }}':
                            setFilePreview(data.attachment);
                            break;
                    }
                    return;
                }
            }
        @endif

        alert('{{ trans('message.type_message_invalid') }}');
    }

    function setButtonPreview(data){
        $('#button_preview').show();
        $('#button_preview .button_title').html(data.text);

        $(data).each(function(index, value){
            if(typeof(value.buttons) != void 0){
                $(value.buttons).each(function(i, button){
                    var btn = $('.origin_item .carousel-button').clone();
                    btn.find('a').html(button.title).attr({'href': '#'});
                    $('#button_preview').append(btn);
                });
            }
        });
    }

    function setGenericPreview(data) {
        $('#generic_preview').show();
        $(data.elements).each(function(index, value){
            var item = $('.origin_item .item').clone();
            var className = index == 0 ? 'active':'';

            item.addClass(className);
            if(value.image_url != void 0 && value.image_url != '') {
                item.find('img').show().attr({
                    'src': value.image_url,
                    'alt': value.title
                });
            } else {
                item.find('img').hide();
            }
            item.find('.carousel-caption-top').html(value.title);
            var subtitle = typeof(value.subtitle) != void 0 ? value.subtitle : '';
            item.find('.carousel-sub-caption').html(subtitle);

            if(typeof(value.buttons) != void 0) {
                $(value.buttons).each(function(i, button) {
                    if(button.title) {
                        var btn = $('.origin_item .carousel-button').clone();
                        btn.find('a').html(button.title);
                        btn.find('a').attr({'href': '#'});
                        item.append(btn);
                    }
                });
            }
            $('#scenario_fb_preview #generic_preview .carousel-inner').append(item);
        });
    }

    function setFilePreview(data) {
        $('#file_preview').show();
        var item;
        if(data.type == 'video' || data.type == 'image') {
            if(data.type == 'video') {
                item = $('.origin_item .file_template .video_view').clone();
            } else {
                item = $('.origin_item .file_template .image_view').clone();
            }
            if(data.payload.url != void 0) {
                item.attr({'src': data.payload.url});
            }
        } else if(data.type == 'file') {
            item = $('.origin_item .file_template .pdf_view').clone();
        }
        if(item) {
            $('#file_preview').append(item);
        }
    }

    function setQuickReplyPreview(data){
        $('#quick_replies_preview').show();
        $('#quick_replies_preview .title').html(data.text);
        var quick_replies = data.quick_replies;
        $(quick_replies).each(function(index, value){
            if(typeof(value.content_type) != void 0 && value.content_type){
                var text;
                if(value.content_type == 'text') {
                    if(value.title) {
                        text = value.title;
                    }
                } else if(value.content_type == 'location') {
                    text = '<img src="{{ asset('images/location_action.png') }}" class="location_icon" alt="">Send Location';
                }
                var btn = $('.origin_item .quick_replies_item').clone();
                btn.html(text);
                $('#quick_replies_preview .button_container').append(btn);
            }
        });
    }

    // check next back button carousel
    function checkCarouselPreview() {
        var arrow_left      = $('#generic_preview .left'),
            arrow_right     = $('#generic_preview .right');

        if ($('#generic_preview .carousel-inner .item').length > 1) {
            if($('#generic_preview .carousel-inner .item:first-child').hasClass('active')) {
                arrow_right.show();
                arrow_left.hide();
            } else if($('#generic_preview .carousel-inner .item:last-child').hasClass('active')) {
                arrow_right.hide();
                arrow_left.show();
            } else {
                arrow_right.show();
                arrow_left.show();
            }
        } else {
            arrow_right.hide();
            arrow_left.hide();
        }
    }
</script>