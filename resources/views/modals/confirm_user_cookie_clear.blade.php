<div class="modal confirm-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('default.confirm')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="row confirm-content">
                    <div class="col-md-12">
                        {{{ trans("efo_scenario.user_cookie_clear_confirm") }}}
                    </div>
                </div>
                <div class="row confirm-error">
                    <div class="col-md-12">
                        <p class="text-red">
                            {{{ trans("message.common_error") }}}
                        </p>
                    </div>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('efo_scenario.no')}}}</button>
                <button type="button" class="btn btn-danger btn-modal-clear">{{{ trans('efo_scenario.yes')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@section('scripts')
    <script>
        var id = '';
        $(function () {
            $('.customize_container').on( 'click','.btn-clear-cookie_user', function () {
                $('.btn-modal-clear').show();
                $('.overlay-wrapper .overlay').hide();
                $('.confirm-modal .confirm-content').show();
                $('.confirm-modal .confirm-error').hide();

                $('.confirm-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });


            $('.btn-modal-clear').on('click', function(evt) {
                $('.overlay-wrapper .overlay').show();
                $('.btn-modal-clear').hide();
                $('.bot_setting_index .overlay').hide();
                $.ajax({
                    url: '{{ route('bot.clearCookieUser', $connect_page->id) }}',
                    type: 'POST',
                    success: function(data) {
                        $('.confirm-modal').modal('hide');
                        $('.bot_setting_index .web_chat_form label.error').html('');
                        //show msg
                        if(data.success != void 0 && data.success && data.msg != void 0 &&  data.msg != ''){
                            setMesssage(data.msg, 2);
                        }
//                        location.reload();
                    },
                    error: function(result){
                        var text = $.parseJSON(result.responseText);
                        $('.overlay-wrapper .overlay').hide();
                        $('.confirm-modal .confirm-content').hide();
                        $('.confirm-modal .confirm-error .text-red').html(text.errors.msg);
                        $('.confirm-modal .confirm-error').show();
                    },
                    complete: function( xhr ) {
                        $('.bot_setting_index .overlay').hide();
                    }
                });
            });

        });
    </script>
@endsection