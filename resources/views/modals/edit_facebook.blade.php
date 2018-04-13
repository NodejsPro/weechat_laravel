<div class="scenario-edit content_message" style="display: none;">
    <div class="col-md-8 scenario-body">
        {{--<section class="panel">--}}
        <div class="scenario_block_origin">
            {{--user scenario block--}}
            <div class="message_box user_scenario" style="">
                <div class="delete_user_box">
                    <div class="delete_bot_box">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                    <a data-html="true" class="hidden" rel="nofollow" data-method="delete" href="#"></a>
                </div>
                <span class="drag-marker pull-left"><i></i></span>
                <i class="fa fa-user" aria-hidden="true"></i>
                <div class="user_form_box">
                    {{ Form::hidden('messages_user_content', null, ['class'=>'form-control messages_user_content', 'required' => 'true']) }}
                    {{ Form::hidden('messages_user_type', $user_content_type['text'], ['class'=>'form-control messages_user_type']) }}
                    {{ Form::hidden('messages_user_variable', '', ['class'=>'form-control messages_user_variable']) }}
                    {{--element show--}}
                    <div class="user_media_demo">
                        <textarea class="form-control user_media_content hidden" readonly rows="3" cols="58"></textarea>
                        <ul class="tagit ui-widget ui-widget-content ui-corner-all"></ul>
                    </div>
                </div>
            </div>
            {{--choice block--}}
            <div class="tagit-choice-block"  style="display: none;">
                <li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable">
                    <span class="taget-label"></span>
                    <a class="tagit-close">
                        <span class="text-icon">×</span>
                        <span class="fa fa-times"></span>
                    </a>
                </li>
            </div>
            {{--bot scenario block--}}
            <div class="message_box bot_scenario bot_message_box">
                <div class="delete_bot_box">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </div>
                <div class="abstract_bot_box">
                    <span class="drag-marker pull-left"><i></i></span>
                    <div class="bot_icon_image_box">
                        <img class="bot_icon_image" src="{{ $bot_picture }}" alt="Bot logo">
                    </div>
                    <div class="bot_form_box">
                        {{--bot media demo block--}}
                        <div class="bot_media_demo">
                            <textarea class="form-control bot_media_content" readonly rows="3" cols="58"></textarea>
                            <div class="label_box">
                                <span class="type_label bot_type hidden"
                                      @foreach($file_type as $key => $code)
                                      {{{ "data-".$key."=".trans('scenario.'.$key) }}}
                                      @endforeach
                                      data-button="{{ trans('scenario.button') }}"
                                      data-generic="{{ trans('scenario.generic') }}"
                                      data-file="{{ trans('scenario.file') }}"
                                      data-api="{{ trans('scenario.api') }}"
                                      data-quick_replies="{{ trans('scenario.quick_replies') }}"
                                      data-scenario_connect="{{ trans('scenario.scenario_connect') }}"
                                      data-mail="{{ trans('scenario.mail') }}"
                                ></span>
                                <span class="type_label preview hidden" data-toggle="modal" data-target="#scenario_fb_preview"><i class="fa fa-eye" aria-hidden="true"></i></span>
                            </div>
                        </div>

                        <div class="bot_content_box hidden">
                            {{ Form::textarea('messages_bot_content', null, ['class'=>'form-control messages_bot_content', 'required' => 'true', 'readonly' => 'false', 'rows' => 3, 'cols' => 58]) }}
                            <input class="messages_bot_type" type="hidden" name="messages_bot_type" value="{{ $bot_content_type['text'] }}">
                        </div>
                    </div>
                </div>
            </div>
            {{--Virtual Bot for User message block--}}
            <div class="bot_message_box bot_virtual_box">
                <div class="abstract_bot_box">
                    <div class="bot_icon_image_box">
                        <img class="bot_icon_image" src="{{ $bot_picture  }}" alt="Bot logo">
                    </div>
                    <div class="bot_form_box">
                        <div class="bot_content_box">
                            {{ Form::textarea(null, mb_strtoupper(trans('field.library'), 'UTF-8').'[ANSWER]', ['class'=>'form-control bot_library_view', 'readonly' => 'false', 'rows' => 3, 'cols' => 58, 'data-label' => mb_strtoupper(trans('field.library'), 'UTF-8')]) }}
                            {{ Form::textarea(null, mb_strtoupper(trans('field.api_variable_setting'), 'UTF-8').'[ANSWER]', ['class'=>'form-control bot_api_variable_setting_view hidden', 'readonly' => 'false', 'rows' => 3, 'cols' => 58, 'data-label' => mb_strtoupper(trans('field.api_variable_setting'), 'UTF-8')]) }}
                        </div>
                    </div>
                </div>
            </div>
            {{--button block type--}}
            <div class="btn_box col-md-12">
                <div class="form-group button_delete_box">
                    <div class="col-md-12 delete_btn_box pull-right">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="form-group button_title_box">
                    <div class="col-md-12">
                        {!! Form::text('button_sub_title', null, ['class' => 'form-control button_sub_title validate-require validate-max maximum-length-20', 'placeholder' => trans('scenario.title')]) !!}
                    </div>
                </div>
                <div class="button_type_container">
                    <div class="form-group button_type_box">
                        <div class="col-md-12">
                            {!! Form::select('button_sub_type', $type_button_list, null, ['class' => 'form-control button_sub_type']) !!}
                        </div>
                    </div>
                    <div class="button_option">
                        <div class="form-group button_data_box">
                            <div class="col-md-12">
                                {!! Form::text('button_sub_data', null, ['class' => 'form-control button_sub_data validate-require validate-url validate-white-list', 'placeholder' => trans('scenario.url')]) !!}
                            </div>
                        </div>
                        <div class="form-group button_image_box" style="display: none">
                            <div class="col-md-12">
                                {!! Form::text('button_sub_image', null, ['class' => 'form-control button_sub_image', 'placeholder' => trans('scenario.image_url')]) !!}
                            </div>
                        </div>
                        <div class="form-group button_scenario_box" style="display: none">
                            <div class="col-md-12">
                                {!! Form::select('button_sub_scenario', $scenario_list, null, ['class' => 'form-control button_sub_scenario']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--generic block type--}}
            <div class="generic_box item">
                <div class="delete_box">
                    <div class="delete_generic_box pull-right">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="generic_box_content">
                    <div class="form-group title_box">
                        <div class="col-md-12">
                            {!! Form::text('title', null, ['class' => 'form-control title validate-require validate-max maximum-length-80', 'placeholder' => trans('scenario.title')]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::text('sub_title', null, ['class' => 'form-control sub_title validate-max maximum-length-80', 'placeholder' => trans('scenario.sub_title')]) !!}
                        </div>
                    </div>
                    <div class="form-group item_url_box">
                        <div class="col-md-12">
                            {!! Form::text('item_url', null, ['class' => 'form-control item_url validate-url validate-white-list', 'placeholder' => trans('scenario.url')]) !!}
                        </div>
                    </div>
                    <div class="form-group image_url_box">
                        <div class="col-md-12">
                            {!! Form::text('image_url', null, ['class' => 'form-control image_url validate-url', 'placeholder' => trans('scenario.image_url')]) !!}
                        </div>
                    </div>
                    <div class="form-group file_select_box">
                        <div class="col-md-12">
                            <button class="btn btn-info btn-scenario-file-list pull-left" type="button">{{{ trans('scenario.file_select') }}}</button>
                            <form id="upload" class="upload_image" action="{{action('FileController@upload', Route::current()->getParameter('bot'))}}" method="POST" enctype="multipart/form-data" style="float: inherit;" data-type="generic" data-error_type="{{trans('validation.mimetypes', ['attribute' => trans('field.file'), 'values' => ':values'])}}" data-error_size="{{trans('validation.max.file', ['attribute' => trans('field.file'), 'max' =>  40000])}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="image_upload" value="">
                                <div class="row fileupload-buttonbar">
                                    <div class="col-md-12" style="padding-left: 3px;">
                                        <span class="btn btn-info fileinput-button">
                                            <span>{{trans('button.file_upload')}}</span>
                                            <input id="upload_file" type="file" name="upload_file" title=" ">
                                        </span>
                                        <span class="fileupload-process"></span>
                                    </div>
                                </div>
                            </form>
                            <div class="box_message col-md-12"></div>
                        </div>
                    </div>
                    <hr/>
                    <div class="generic_button_container">
                        {{--content button--}}
                    </div>
                </div>
            </div>
            {{--generic button block type--}}
            <div class="generic_button_template">
                <div class="button_type_container">
                    <div class="form-group button_delete_box">
                        <div class="col-md-12 delete_btn_box pull-right">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="form-group button_title_box">
                        <div class="col-md-12">
                            {!! Form::text('button_title', null, ['class' => 'form-control button_title validate-require validate-max maximum-length-20', 'placeholder' => trans('scenario.button_title')]) !!}
                        </div>
                    </div>
                    <div class="form-group button_type_box">
                        <div class="col-md-12">
                            {!! Form::select('button_sub_type', $type_button_list, null, ['class' => 'form-control button_sub_type']) !!}
                        </div>
                    </div>
                    <div class="button_option">
                        <div class="form-group button_data_box">
                            <div class="col-md-12">
                                {!! Form::text('button_sub_data', null, ['class' => 'form-control button_sub_data validate-require validate-url validate-white-list', 'placeholder' => trans('scenario.url')]) !!}
                            </div>
                        </div>
                        <div class="form-group button_image_box" style="display: none">
                            <div class="col-md-12">
                                {!! Form::text('button_sub_image', null, ['class' => 'form-control button_sub_image', 'placeholder' => trans('scenario.image_url')]) !!}
                            </div>
                        </div>
                        <div class="form-group button_scenario_box" style="display: none">
                            <div class="col-md-12">
                                {!! Form::select('button_sub_scenario', $scenario_list, null, ['class' => 'form-control button_sub_scenario']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--quick replies block type--}}
            <div class="quick_replies_box col-md-12">
                <div class="form-group button_delete_box">
                    <div class="col-md-12 delete_btn_box pull-right">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="form-group button_type_box">
                    <div class="col-md-12">
                        {!! Form::select('button_sub_type', $type_qr_list, null, ['class' => 'form-control button_sub_type']) !!}
                    </div>
                </div>
                <div class="form-group button_title_box">
                    <div class="col-md-12">
                        {!! Form::text('button_sub_title', null, ['class' => 'form-control button_sub_title validate-require validate-max maximum-length-20', 'placeholder' => trans('scenario.button_title')]) !!}
                    </div>
                </div>
                <div class="form-group button_scenario_box">
                    <div class="col-md-12 is_scenario">
                        <input type="checkbox" class="qr_is_scenario" value="1"/>
                        {!! Form::label(trans('scenario.scenario_connect') ) !!}
                    </div>
                    <div class="col-md-12">
                        {!! Form::select('button_sub_scenario', $scenario_list, null, ['class' => 'form-control button_sub_scenario hidden']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body hide">
            @include('flash')
            <div class="scenario_block">
                @if(isset($data_messages) && count($data_messages))
                    @foreach($data_messages as $message)
                        @if($message['message_type'] == $message_type['user'])
                            <?php $type = $message['type']; ?>
                            <div class="message_box user_scenario" style="">
                                <div class="delete_user_box">
                                    <div class="delete_bot_box">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </div>
                                    <a data-html="true" class="hidden" rel="nofollow" data-method="delete" href="#"></a>
                                </div>
                                <span class="drag-marker pull-left"><i></i></span>
                                <i class="fa fa-user" aria-hidden="true"></i>
                                <div class="user_form_box">
                                    <input class="form-control messages_user_content" required="true" name="messages_user_content" type="hidden" value="{{{ @$message['content'] }}}">
                                    <input class="form-control messages_user_type" name="messages_user_type" type="hidden" value="{{ $message['type'] }}">
                                    <input class="form-control messages_user_variable" name="messages_user_variable" type="hidden" value="{{ @$message['variable_id'] }}">

                                    {{--element show--}}
                                    <div class="user_media_demo">
                                        <?php $is_tagit = ($type != $user_content_type['api_variable_setting']) ?>
                                        <textarea class="form-control user_media_content {{ $is_tagit ? 'hidden' : '' }}" readonly rows="3" cols="58">{{ mb_strtoupper(trans('field.api_variable_setting'), 'UTF-8').'['.(!$is_tagit ? @$api_variable_setting_list[$message['content']] : '').']' }}</textarea>
                                        <ul class="tagit ui-widget ui-widget-content ui-corner-all {{ !$is_tagit ? 'hidden' : '' }}">
                                            @if(isset($message['content']) && $is_tagit)
                                                <?php $user_list_text = isset($message['content']) ? explode(',', $message['content']) : []; ?>
                                                @foreach ($user_list_text as $value)
                                                    @if($type == $user_content_type['library'])
                                                        <?php $value = mb_strtoupper(trans('field.library'), 'UTF-8').'['.(isset($library_list[$value]) ? $library_list[$value] : '').']'; ?>
                                                    @endif
                                                    @if($value)
                                                        <li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable">
                                                            <span class="taget-label">{{{ $value }}}</span>
                                                            <a class="tagit-close">
                                                                <span class="text-icon">×</span>
                                                                <span class="fa fa-times"></span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @elseif($message['message_type'] == $message_type['bot'])
                            <div class="message_box bot_scenario bot_message_box">
                                <div class="delete_bot_box">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                                <div class="abstract_bot_box">
                                    <span class="drag-marker pull-left"><i></i></span>
                                    <div class="bot_icon_image_box">
                                        <img class="bot_icon_image" src="{{ $bot_picture }}" alt="Bot logo">
                                    </div>
                                    <div class="bot_form_box">
                                        <?php
                                        $type       = isset($message['type']) ? $message['type'] : '';
                                        $content    = isset($message['content']) ? $message['content'] : '';

                                        $type_label  = '';
                                        $title_label = '';
                                        $msg_content = json_decode($content);
                                        $msg_content = $msg_content->message;
                                        //check exist to  set title and type for message
                                        switch ($type) {
                                            case $bot_content_type['text']:
                                                if(isset($msg_content->text)) {
                                                    $title_label = $msg_content->text;
                                                }
                                                break;
                                            case $bot_content_type['button']:
                                                if(isset($msg_content->attachment) && isset($msg_content->attachment->payload) && isset($msg_content->attachment->payload->text)) {
                                                    $title_label = $msg_content->attachment->payload->text;
                                                }
                                                $type_label     = array_search($type, $bot_content_type);
                                                break;
                                            case $bot_content_type['generic']:
                                                $title_label    = '';
                                                $type_label     = 'generic';
                                                break;
                                            case $bot_content_type['file']:
                                                $type_label     = 'file';
                                                if(isset($msg_content->attachment) && isset($msg_content->attachment->payload) && isset($msg_content->attachment->payload->url)) {
                                                    $title_label    = $msg_content->attachment->payload->url;
                                                    if($msg_content->attachment->type) {
                                                        $type_label = $msg_content->attachment->type;
                                                    }
                                                }
                                                break;
                                            case $bot_content_type['quick_replies']:
                                                if(isset($msg_content->text)) {
                                                    $title_label    = isset($msg_content->text) ? $msg_content->text : '';
                                                }
                                                $type_label         = 'quick_replies';
                                                break;
                                            case $bot_content_type['api']:
                                                if(isset($msg_content->api)) {
                                                    $title_label    = isset($api_list[$msg_content->api]) ? $api_list[$msg_content->api] : '';
                                                }
                                                $type_label         = 'api';
                                                break;
                                            case $bot_content_type['scenario_connect']:
                                                if(isset($msg_content->scenario)) {
                                                    $title_label    = isset($scenario_list[$msg_content->scenario]) ? $scenario_list[$msg_content->scenario] : '';
                                                }
                                                $type_label         = 'scenario_connect';
                                                break;
                                            case $bot_content_type['mail']:
                                                if(isset($msg_content->mail)) {
                                                    $title_label    = isset($mail_list[$msg_content->mail]) ? $mail_list[$msg_content->mail] : '';
                                                }
                                                $type_label         = 'mail';
                                                break;

                                        }
                                        ?>
                                        <div class="bot_media_demo">
                                            <textarea class="form-control bot_media_content" readonly rows="3" cols="58">{{{ $title_label }}}</textarea>
                                            <div class="label_box">
                                                <span class="type_label bot_type {{ ($type == $bot_content_type['text']) ? 'hidden' : '' }}"
                                                      @foreach($file_type as $key => $code)
                                                      {{{ "data-".$key."=".trans('scenario.'.$key) }}}
                                                      @endforeach
                                                      data-button="{{ trans('scenario.button') }}"
                                                      data-generic="{{ trans('scenario.generic') }}"
                                                      data-file="{{ trans('scenario.file') }}"
                                                      data-api="{{ trans('scenario.api') }}"
                                                      data-quick_replies="{{ trans('scenario.quick_replies') }}"
                                                      data-scenario_connect="{{ trans('scenario.scenario_connect') }}"
                                                      data-mail="{{ trans('scenario.mail') }}"
                                                >{{{ trans('scenario.'.$type_label) }}}</span>
                                                <span class="type_label preview {{ ($type == $bot_content_type['text'] || $type == $bot_content_type['api'] || $type == $bot_content_type['scenario_connect'] || $type == $bot_content_type['mail']) ? 'hidden' : '' }}" data-toggle="modal" data-target="#scenario_fb_preview"><i class="fa fa-eye" aria-hidden="true"></i></span>
                                            </div>
                                        </div>

                                        <div class="bot_content_box hidden">
                                            <textarea class="form-control messages_bot_content" required="true" readonly="false" rows="3" cols="58" name="messages_bot_content">{{{ $content }}}</textarea>
                                            <input class="messages_bot_type" type="hidden" name="messages_bot_type" value="{{ $type }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
            <div class="columns end_scenario"></div>
            @if($_view_template_flg)
                <div class="action-group">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="add_pattern_user">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div align="right" class="col-md-6">
                            <div class="add_pattern_bot">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        {{--</section>--}}
    </div>
    <div class="fixedsidebar_ctn">
        <section class="panel fixedsidebar message_container_input">
            {!! Form::hidden('messages_focus', null, ['class' => 'messages_focus']) !!}

            <div class="panel-body col-md-12 message_bot_area cmxform form-horizontal" style="display: none">
                <div class="header_message_input">
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::label('bot_content_type', trans('scenario.type') ) !!}
                            <?php
                            $label_bot_type_attr =  [
                                    'id'=> 'bot_content_type',
                                    'class' => 'form-control'
                            ];
                            if(!$_view_template_flg) {
                                $label_bot_type_attr['disabled'] = 'disabled';
                            }
                            ?>
                            {!! Form::select('bot_content_type', $bot_type_list, null, $label_bot_type_attr) !!}
                        </div>
                    </div>
                    <div class="form-group add_group_box" style="display: none">
                        <div class="col-md-12">
                            <button class="btn btn-info add_item_generic pull-left" type="button">{{{ trans('button.add') }}}</button>
                        </div>
                    </div>
                </div>

                <div class="fixedsidebar-content">
                    <div class="text_group type_group form-group">
                        <div class="col-md-12">
                            {{ Form::textarea('text', null, ['class'=>'form-control input_text_type validate-require', 'rows' => 7, 'cols' => 58]) }}
                            <h6> {{{ trans('scenario.guide_add_variable')}}}</h6>
                        </div>
                    </div>
                    <div class="button_group type_group">
                        <div class="button_container">
                            <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::text('button_title', null, ['class' => 'form-control button_title validate-require validate-max maximum-length-640', 'placeholder' => trans('scenario.title')]) !!}
                                </div>
                            </div>
                            <hr/>
                        </div>
                    </div>
                    <div class="generic_group type_group">
                        <div id="c-slide" class="carousel slide generic_carousel">
                            <div class="carousel-inner generic_container">
                                {{--generic item--}}
                            </div>
                        </div>
                    </div>
                    <div class="file_group type_group">
                        <div class="form-group">
                            <div class="col-md-12">
                                {{ Form::textarea('', null, ['class' => 'form-control file_url_input validate-require validate-url', 'rows' => '3', 'placeholder' => trans('scenario.image_url')]) }}
                            </div>
                        </div>
                        <div class="form-group file_select_box">
                            <div class="col-md-12">
                                <button class="btn btn-info btn-scenario-file-list pull-left" type="button">{{{ trans('scenario.file_select') }}}</button>
                                <form id="upload" class="upload_image" action="{{action('FileController@upload', Route::current()->getParameter('bot'))}}" method="POST" enctype="multipart/form-data" data-error_type="{{trans('validation.mimetypes', ['attribute' => trans('field.file'), 'values' => ':values'])}}" data-type="file" data-error_size="{{trans('validation.max.file', ['attribute' => trans('field.file'), 'max' =>  40000])}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="row fileupload-buttonbar">
                                        <div class="col-md-12" style="padding-left: 3px;">
                                        <span class="btn btn-info fileinput-button">
                                            <span>{{trans('button.file_upload')}}</span>
                                            <input id="upload_file" type="file" name="upload_file" title=" ">
                                        </span>
                                            <span class="fileupload-process"></span>
                                        </div>
                                    </div>
                                </form>
                                <div class="box_message col-md-12"></div>
                            </div>
                        </div>
                    </div>
                    <div class="quick_replies_group type_group">
                        <div class="quick_replies_container">
                            <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::text('quick_replies_title', null, ['class' => 'form-control quick_replies_title validate-require', 'placeholder' => trans('scenario.title')]) !!}
                                </div>
                            </div>
                            @if(isset($variable_custom) && count($variable_custom))
                                <div class="form-group">
                                    <div class="col-md-12 is_variable minimal">
                                        <input type="checkbox" id="qr_is_variable" class="icheck qr_is_variable" value="1"/>
                                        {!! Form::label('qr_is_variable', trans('scenario.variable_select_label') ) !!}
                                    </div>
                                    <div class="col-md-12">
                                        {!! Form::select('quick_replies_variable', $variable_custom, null, ['class' => 'form-control quick_replies_variable hidden']) !!}
                                    </div>
                                </div>
                            @endif
                            <hr/>
                        </div>
                    </div>
                    <div class="api_group type_group form-group">
                        <div class="col-md-12">
                            @if(isset($api_list) && count($api_list))
                                {!! Form::select('', $api_list, null, ['class' => 'form-control api_select', 'style' => 'width: 100%']) !!}
                            @else
                                <h5> {{{ trans('message.api_no_result')}}}</h5>
                            @endif
                        </div>
                    </div>
                    <div class="scenario_connect_group type_group form-group">
                        <div class="col-md-12">
                            @if(isset($scenario_list) && count($scenario_list))
                                {!! Form::select('', $scenario_list, null, ['class' => 'form-control scenario_select', 'style' => 'width: 100%']) !!}
                            @else
                                <h5> {{{ trans('message.scenario_no_result')}}}</h5>
                            @endif
                        </div>
                    </div>
                    <div class="mail_group type_group form-group">
                        <div class="col-md-12">
                            @if(isset($mail_list) && count($mail_list))
                                {!! Form::select('', $mail_list, null, ['class' => 'form-control mail_select', 'style' => 'width: 100%']) !!}
                            @else
                                <h5> {{{ trans('message.mail_no_result')}}}</h5>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="footer_message_input">
                    <div class="form-group add_item_box">
                        <div class="col-md-12">
                            <button class="btn btn-info add_item_common" type="button">{{{ trans('button.add_button') }}}</button>
                        </div>
                    </div>
                    <div class="carousel_slide">
                        <a data-slide="prev" href="#c-slide" class="left carousel-control">
                            <i class="fa fa-angle-left"></i>
                        </a>
                        <a data-slide="next" href="#c-slide" class="right carousel-control">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </div>
                    <div class="carousel_indicator_item col-md-12">
                        <ol class="carousel-indicators out generic_indicators">
                            {{--Carousel indicators--}}
                        </ol>
                    </div>
                </div>
            </div>
            <div class="panel-body col-md-12 message_user_area cmxform form-horizontal" style="display: none">
                <div class="header_message_input">
                    {!! Form::label('user_content_type', trans('scenario.type') ) !!}
                    <div class="form-group">
                        <div class="col-md-12">
                            <?php
                            $label_user_type_attr =  [
                                    'id'=> 'user_content_type',
                                    'class' => 'form-control'
                            ];
                            if(!$_view_template_flg) {
                                $label_user_type_attr['disabled'] = 'disabled';
                            }
                            ?>
                            {!! Form::select('user_content_type', $user_type_list, null, $label_user_type_attr) !!}
                        </div>
                    </div>
                </div>

                <div class="fixedsidebar-content">
                    <div class="text_group type_group">
                        <div class="form-group">
                            <div class="col-md-12">
                                {!! Form::text('user_text', null, ['class' => 'form-control user_text_type']) !!}
                            </div>
                        </div>
                        @if(isset($variable_custom) && count($variable_custom))
                            <div class="form-group">
                                <div class="col-md-12 is_variable minimal">
                                    <input type="checkbox" id="user_text_variable" class="icheck use_variable" value="1"/>
                                    {!! Form::label('user_text_variable', trans('scenario.user_variable_select_label') ) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('select_variable', $variable_custom, null, ['class' => 'form-control select_variable hidden', 'id' => '']) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="library_group type_group">
                        <div class="form-group">
                            <div class="col-md-12">
                                <table class="table table-library table-hover">
                                    <tbody>
                                    @if(isset($library_list) && count($library_list))
                                        @foreach($library_list as $id => $label)
                                            <tr>
                                                <td class="library_input_box minimal">
                                                    <input type="checkbox" class="icheck library_list" value="{{{ $id }}}" data-name="{{{ $label }}}"/>
                                                </td>
                                                <td class="library_label_box"><a>{{{ $label }}}</a></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="no_result">
                                            <td><h5> {{{ trans('message.library_no_result')}}}</h5></td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(isset($variable_custom) && count($variable_custom))
                            <div class="form-group">
                                <div class="col-md-12 is_variable minimal">
                                    <input type="checkbox" id="user_text_variable" class="icheck use_variable" value="1"/>
                                    {!! Form::label('user_text_variable', trans('scenario.user_variable_select_label') ) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('select_variable', $variable_custom, null, ['class' => 'form-control select_variable hidden', 'id' => '']) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="api_variable_setting_group type_group">
                        <div class="form-group">
                            <div class="col-md-12">
                                @if(isset($api_variable_setting_list) && count($api_variable_setting_list))
                                    {!! Form::select('', $api_variable_setting_list, null, ['class' => 'form-control api_variable', 'style' => 'width: 100%']) !!}
                                @else
                                    <h5> {{{ trans('message.api_variable_setting_no_result')}}}</h5>
                                @endif
                            </div>
                        </div>
                        @if(isset($variable_custom) && count($variable_custom))
                            <div class="form-group">
                                <div class="col-md-12 is_variable minimal">
                                    <input type="checkbox" id="api_vs_variable" class="icheck use_variable" value="1"/>
                                    {!! Form::label('api_vs_variable', trans('scenario.user_variable_select_label') ) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('select_variable', $variable_custom, null, ['class' => 'form-control select_variable hidden', 'id' => '']) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="footer_message_input"></div>
            </div>
            <div class="pull-right btn_action">
                <div class="form-group space_btn_update"></div>
                @if($_view_template_flg)
                    <button class="btn btn-default cancel" type="button">{{{ trans('button.cancel') }}}</button>
                    <button class="btn btn-info checkValidate" type="button">{{{ trans('button.save') }}}</button>
                @endif
                <div class="box_message"></div>
            </div>
        </section>
    </div>
    <div class="overlay">
        <i class="fa fa-refresh fa-spin fa-2x"></i>
    </div>
</div>
@include('modals.scenario_file_select')

