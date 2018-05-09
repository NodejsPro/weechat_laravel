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
