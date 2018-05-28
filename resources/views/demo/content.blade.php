@section('styles_conversation')
    <link href="{{ mix('build/css/conversation.css') }}" rel="stylesheet">
@endsection
<div class="col-sm-9 right_conversation">
    <section class="panel minimum-panel panel_right_conversation">
        <div class="box_message"></div>
        <div class="panel-body chat_content hidden">
            <div class="chat-conversation">
                <ul class="conversation-list"></ul>
            </div>
            @include('demo.content-input')
        </div>
    </section>
</div>
<div class="template_message hidden">
    <div class="user">
        <ul>
            {{--list user area--}}
            <li class="user_item" data-user_id="" data-user_avatar="">
                <a href="javascript:;">
                    <span class="pull-left"><i class="fa fa-star icon_pin"></i></span>
                    <img src="">
                    <p class="user_name"></p>
                    <p class="user_email"></p>
                    <p class="user_date"></p>
                    <div class="right_content pull-right">
                        <span class="badge bg-important pull-left notification hide"></span>
                        <div class="more_info_box pull-right">
                            <span class="more_info pull-right" data-toggle="popover"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </a>
            </li>
            {{--message area--}}
            <li class="clearfix user_chat">
                <div class="chat-avatar">
                    <img src="" alt="" class="user_avatar" height="35px" width="35px">
                    <p class="user_name"></p>
                    <p class="time_of_message"></p>
                </div>
                <div class="conversation-text">
                    <div class="ctext-wrap user_text">
                        <i class="text_message"></i>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="bot">
        <ul>
            <li class="clearfix odd bot_chat">
                <div class="chat-avatar">
                    <img src="{{ $bot_picture }}" alt="" class="bot_avatar" height="35px" width="35px">
                    <p class="user_name"></p>
                    <p class="time_of_message"></p>
                </div>
                <div class="conversation-text">
                    <div class="ctext-wrap text">
                        <div class="text_message"></div>
                        <div class="text_alt"></div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
