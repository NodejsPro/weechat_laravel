<div class="modal bot-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('default.confirm')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="row bot-content">
                    <div class="col-md-12">
                        {{{ trans("message.model_bot_use_system_confirm") }}}
                    </div>
                </div>
                <div class="row bot-success">
                    <div class="col-md-12">
                        <p>
                            {{{ trans('message.set_origin_page_success') }}}
                        </p>
                    </div>
                </div>
                <div class="row bot-error">
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
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn-modal-confirm">{{{ trans('button.confirm')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@section('scripts')
    <script>
        var success = false;
        var id = '';
        $(function () {
            var url = '';
            var id="";
            $('.btn-system-app').click(function(){
                url = $(this).data('from');
                id = $(this).data('connect-page-id');
                success = false;
                $('.overlay-wrapper .overlay').hide();
                $('.bot-modal .bot-content').show();
                $('.bot-modal .bot-error').hide();
                $('.bot-modal .bot-success').hide();
                $('.bot-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
                return false;
            });

            $('.btn-modal-confirm').on('click', function(evt) {
                console.log($('.bot-modal .overlay-wrapper .overlay').length);
                $('.bot-modal .overlay-wrapper .overlay').show();
                $('.btn-modal-confirm').hide();
                $.ajax({
                    url: url,
                    data: { "_token": "{{ csrf_token() }}"},
                    type: 'POST',
                    success: function(data) {
                        $('.bot-modal .overlay-wrapper .overlay').hide();
                        $('.bot-modal .bot-content').hide();
                        $('.bot-modal .bot-error').hide();
                        $('.bot-modal .bot-success').show();
                        success = true;
                    },
                    error: function(result){
                        var text = $.parseJSON(result.responseText);
                        $('.bot-modal .overlay-wrapper .overlay').hide();
                        $('.bot-modal .bot-content').hide();
                        $('.bot-modal .bot-error .text-red').html(text.errors.msg);
                        $('.bot-modal .bot-error').show();
                        $('.bot-modal .bot-success').hide();
                    }
                });
            });
            $(".btn-modal-close").on('click', function(evt) {
                if(success){
                    location.reload();
                }
                return true;
            });

        });
    </script>
@endsection