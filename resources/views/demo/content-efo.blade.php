@section("styles_conversation_efo")
{{--    <link href="{{ elixir('css/conversation-efo-all.css') }}" rel="stylesheet">--}}
@endsection
<div class="col-sm-9 right_conversation conversation-efo blue-template">
    <section class="panel minimum-panel panel_right_conversation">
        <div class="box_message"></div>
        <div class="panel-body chat_content hidden">
            <div class="chat-conversation">
                <ul class="conversation-list"></ul>
            </div>
        </div>
    </section>
</div>
@section("scripts_conversation_efo")
    <script >
        var bot_content_type       = <?php '' ?>;
//        setDataCommon('bot_lang', bot_lang);
    </script>
{{--    <script src="{{elixir('js/conversation-efo-all.js')}}" type="text/javascript"></script>--}}
    {{--<script src="{{ asset('js/slick.js?'.time()) }}"></script>--}}
    {{--<script src="{{ asset('js/conversation-efo.js?'.time()) }}"></script>--}}
@endsection
