<div id="scenario_line_preview" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{{ trans('scenario.preview') }}}</h4>
            </div>
            <div class="modal-body">
                <!--Generic carousel aria-->
                <div id="carousel_preview" class="carousel slide mesage_preview">
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox"></div>
                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel_preview" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    </a>
                    <a class="right carousel-control" href="#carousel_preview" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    </a>
                </div>

                {{--Button aria--}}
                <div id="button_preview" class="mesage_preview">
                    <div class="image"><img src="" alt=""></div>
                    <div class="title"><a></a></div>
                    <div class="text"><a></a></div>
                    <div class="button_box"></div>
                </div>

                {{--Confirm aria--}}
                <div id="confirm_preview" class="mesage_preview">
                    <div class="button_title"></div>
                    <div class="button_box"></div>
                </div>

                {{--File aria--}}
                <div id="file_preview" class="mesage_preview"></div>

                {{--location aria--}}
                <div id="location_preview" class="mesage_preview">
                    <div class="location_icon"><img src="{{ asset('images/line_pin.png') }}" alt=""></div>
                    <div class="message">
                        <p class="title"></p>
                        <p class="address"></p>
                    </div>
                </div>

                {{--origin template--}}
                <div class="origin_item hidden">
                    <div class="item">
                        <div class="carousel-top">
                            <div class="image">
                                <img src="" alt="">
                            </div>
                            <div class="title"><a></a></div>
                            <div class="text"><a></a></div>
                            <div class="button_box"></div>
                        </div>
                    </div>
                    <div class="button_item">
                        <a></a>
                    </div>
                    <div class="file_template">
                        <img class="image_view" src="" alt="" usemap="#map_image_view"/>
                        <video class="video_view" controls="controls" src=""></video>
                    </div>
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
            //hide all preview
            $('#scenario_line_preview .mesage_preview').hide();
            //init data
            if(data) {
                data = JSON.parse(data);
                initPreview(data, type);
            }
            checkCarouselPreview();
        });

        // setting carousel
        $('#scenario_line_preview #carousel_preview').carousel({
            interval: false,
            wrap: false
        });
        $('#scenario_line_preview #carousel_preview').bind('slid.bs.carousel', function (e) {
            checkCarouselPreview();
        });
    });

    //set generic data
    function initPreview(data, type) {
        if(data['message'] != void 0 && data['message'] != '') {
            var data_message = data['message'];
            @if(isset($connect_page) && $connect_page->sns_type == config('constants.group_type_service.line'))
                    switch (type) {
                case '{{ $bot_content_type['carousel'] }}':
                    setCarouselPreview(data_message);
                    break;
                case '{{ $bot_content_type['button'] }}':
                    setButtonPreview(data_message);
                    break;
                case '{{ $bot_content_type['file'] }}':
                    setFilePreview(data_message);
                    break;
                case '{{ $bot_content_type['confirm'] }}':
                    setConfirmPreview(data_message);
                    break;
                case '{{ $bot_content_type['location'] }}':
                    setLocationPreview(data_message);
                    break;
                case '{{ $bot_content_type['imagemap'] }}':
                    setImagemapPreview(data);
                    break;
                default: alert('{{ trans('message.type_message_invalid') }}');
            }
            @endif
        }
    }

    function setFilePreview(data) {
        $('#scenario_line_preview #file_preview').html('').show();
        var item;
        if(data && data.type != void 0 && (data.type == 'video' || data.type == 'image')) {
            if(data.type == 'video') {
                item = $('#scenario_line_preview .origin_item .file_template .video_view').clone();
            } else {
                item = $('#scenario_line_preview .origin_item .file_template .image_view').clone();
            }
            if(data.originalContentUrl != void 0) {
                item.attr({'src': data.originalContentUrl});
            }
        }
        if(item) {
            $('#scenario_line_preview #file_preview').append(item);
        }
    }

    function setImagemapPreview(data) {
        var file_preview = $('#scenario_line_preview #file_preview');
        file_preview.html('').show();
        var item = $('#scenario_line_preview .origin_item .file_template .image_view').clone();
        if(data.message.baseUrl != void 0) {
            item.attr({'src': data.message.baseUrl});
        }
        if(item) {
            file_preview.append(item);
        }
        initMapImage(data);
    }

    function setButtonPreview(data){
        var preview_box = $('#scenario_line_preview #button_preview');
        //clear old data
        preview_box.find('.title a, .text a, .button_box').html('');
        if(data.template != void 0 && data.template != '') {
            data = data.template;
            preview_box.show();
            preview_box.find('.title a').html(data.title);
            preview_box.find('.text a').html(data.text);
            if(data.thumbnailImageUrl != void 0 && data.thumbnailImageUrl != '') {
                preview_box.find('.image').show();
                preview_box.find('.image img').attr('src', data.thumbnailImageUrl);
            } else {
                preview_box.find('.image').hide();
            }

            $(data.actions).each(function(index, button){
                if(button.label != void 0 && button.label != '') {
                    var btn = $('#scenario_line_preview .origin_item .button_item').clone();
                    btn.find('a').html(button.label);
                    preview_box.find('.button_box').append(btn);
                }
            });
        }
    }

    function setConfirmPreview(data){
        if(data.template != void 0 && data.template != '') {
            data = data.template;
            var preview_box = $('#scenario_line_preview #confirm_preview');
            //clear old data
            preview_box.find('.button_title, .button_box').html('');
            preview_box.show();
            preview_box.find('.button_title').html(data.text);

            $(data.actions).each(function (index, button) {
                if (button.label != void 0 && button.label != '') {
                    var btn = $('#scenario_line_preview .origin_item .button_item').clone();
                    btn.find('a').html(button.label);
                    preview_box.find('.button_box').append(btn);
                }
            });
        }
    }

    function setCarouselPreview(data) {
        var preview_box = $('#scenario_line_preview #carousel_preview');
        preview_box.find('.carousel-inner .item').remove();

        if(data.template != void 0 && data.template != '' && data.template.columns != void 0 && data.template.columns != '') {
            data = data.template.columns;
            preview_box.show();
            $(data).each(function (index, carousel_item) {
                var item = $('.origin_item .item').clone();
                var className = index == 0 ? 'active' : '';

                item.addClass(className);
                item.find('.image img').attr({
                    'src': carousel_item.thumbnailImageUrl,
                    'alt': ''
                });
                item.find('.title a').html(carousel_item.title);
                item.find('.text a').html(carousel_item.text);

                if (typeof(carousel_item.actions) != void 0) {
                    $(carousel_item.actions).each(function (i, button) {
                        if (button.label) {
                            var btn = $('.origin_item .button_item').clone();
                            btn.find('a').html(button.label);
                            item.find('.button_box').append(btn);
                        }
                    });
                }
                preview_box.find('.carousel-inner').append(item);
            });
        }
    }

    function setLocationPreview(data){
        var preview_box = $('#scenario_line_preview #location_preview');
        //clear old data
        preview_box.find('.title, .address').html('');
        preview_box.show();
        if (data.title != void 0 && data.title != '') {
            preview_box.find('.title').html(data.title);
        }
        if (data.address != void 0 && data.address != '') {
            preview_box.find('.address').html(data.address);
        }
    }

    // check next back button carousel
    function checkCarouselPreview() {
        var arrow_left      = $('#carousel_preview .left'),
            arrow_right     = $('#carousel_preview .right');

        if ($('#carousel_preview .carousel-inner .item').length > 1) {
            if($('#carousel_preview .carousel-inner .item:first-child').hasClass('active')) {
                arrow_right.show();
                arrow_left.hide();
            } else if($('#carousel_preview .carousel-inner .item:last-child').hasClass('active')) {
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

    //set map for image after show modal preview
    function initMapImage(data) {
        if(data.message.actions != void 0 && data.message.actions.length) {
            var template_type = data.template_type;
            var file_preview = $('#scenario_line_preview #file_preview');
            var map_image = $('<map name="map_image_view"></map>');

            $(data.message.actions).each(function (i, e) {
                if(e.type != void 0 && e.type == 'uri' && e.linkUri != void 0) {
                    var area_index = i+1;
                    var map_image_position = getMapImagePosition(template_type, area_index);
                    var map_area = '<area shape="rect" coords="' + map_image_position.x1 + ',' + map_image_position.y1 + ',' + map_image_position.x2 + ',' + map_image_position.y2 + '" href="' + e.linkUri + '" alt="" target="_blank">';
                    map_image.append(map_area);
                }
            });
            file_preview.append(map_image);
        }

        function getMapImagePosition(template_type, area_index) {
            var preview_w = 250,
                preview_h = 250;
            var result = {
                'x1': 0,
                'y1': 0,
                'x2': 0,
                'y2': 0
            };

            var w_half = Math.floor(preview_w / 2);
            var h_half = Math.floor(preview_h / 2);
            var w_third = Math.floor(preview_w / 3);
            var h_third = Math.floor(preview_h / 3);
            var w_four = Math.floor(preview_w / 4);
            var h_four = Math.floor(preview_h / 4);

            //imagemap_area_index is number from 1->6 (area 1->6)
            template_type = parseInt(template_type);
            area_index = parseInt(area_index);
            switch (template_type) {
                case 1:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A
                     */
                    result['x2'] = preview_w;
                    result['y2'] = preview_h;
                    break;
                case 2:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     |
                     A   |   B
                     |
                     */
                    result['y2'] = preview_h;
                    if (area_index == 1) {
                        result['x2'] = w_half;
                    } else if (area_index == 2) {
                        result['x1'] = w_half;
                        result['x2'] = preview_w;
                    }
                    break;
                case 3:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A
                     -------
                     B
                     */
                    result['x2'] = preview_w;
                    if (area_index == 1) {
                        result['y2'] = h_half;
                    } else if (area_index == 2) {
                        result['y1'] = h_half;
                        result['y2'] = preview_h;
                    }
                    break;
                case 4:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A           A
                     -------------
                     B           B
                     -------------
                     C           C
                     */
                    result['x2'] = preview_w;
                    if (area_index == 1) {
                        result['y2'] = h_third;
                    } else if (area_index == 2) {
                        result['y1'] = h_third;
                        result['y2'] = h_third*2;
                    } else if (area_index == 3) {
                        result['y1'] = h_third*2;
                        result['y2'] = preview_h;
                    }
                    break;
                case 5:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A   |  B
                     ----|----
                     C   |  D
                     */

                    if (area_index == 1) {
                        result['x2'] = w_half;
                        result['y2'] = h_half;

                    } else if (area_index == 2) {
                        result['x1'] = h_half;
                        result['x2'] = preview_w;
                        result['y2'] = h_half;
                    } else if (area_index == 3) {
                        result['y1'] = h_half;
                        result['x2'] = w_half;
                        result['y2'] = preview_h;
                    } else if (area_index == 4) {
                        result['x1'] = w_half;
                        result['y1'] = h_half;
                        result['x2'] = preview_w;
                        result['y2'] = preview_h;
                    }
                    break;
                case 6:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A
                     ---------
                     B  |  C
                     */
                    if (area_index == 1) {
                        result['x2'] = preview_w;
                        result['y2'] = h_half;

                    } else if (area_index == 2) {
                        result['y1'] = h_half;
                        result['x2'] = h_half;
                        result['y2'] = preview_h;

                    } else if (area_index == 3) {
                        result['x1'] = w_half;
                        result['y1'] = h_half;
                        result['x2'] = preview_w;
                        result['y2'] = preview_h;
                    }
                    break;
                case 7:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A (W: 50%)
                     A
                     ---------
                     B (W: 25%)
                     ---------
                     c (W: 25%)
                     */
                    if (area_index == 1) {
                        result['x2'] = preview_w;
                        result['y2'] = h_half;

                    } else if (area_index == 2) {
                        result['y1'] = h_half;
                        result['x2'] = preview_w;
                        result['y2'] = h_four*3;

                    } else if (area_index == 3) {
                        result['y1'] = h_four*3;
                        result['x2'] = preview_w;
                        result['y2'] = preview_h;
                    }
                    break;
                case 8:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A   |  B  |  C
                     ----|-----|----
                     D   |  E  |  F
                     */
                    if (area_index == 1) {
                        result['x2'] = w_third;
                        result['y2'] = h_half;
                    } else if (area_index == 2) {
                        result['x1'] = w_third;
                        result['x2'] = w_third*2;
                        result['y2'] = h_half;
                    } else if (area_index == 3) {
                        result['x1'] = w_third*2;
                        result['x2'] = preview_w;
                        result['y2'] = h_half;

                    } else if (area_index == 4) {
                        result['y1'] = h_half;
                        result['x2'] = w_third;
                        result['y2'] = preview_h;

                    } else if (area_index == 5) {
                        result['x1'] = w_third;
                        result['y1'] = h_half;
                        result['x2'] = w_third*2;
                        result['y2'] = preview_h;

                    } else if (area_index == 6) {
                        result['x1'] = w_third*2;
                        result['y1'] = h_half;
                        result['x2'] = preview_w;
                        result['y2'] = preview_h;
                    }
                    break;
                }
            return result;
        }
    }
</script>