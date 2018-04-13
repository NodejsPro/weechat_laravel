<div id="setting-webhook-modal" class="modal scenario-group-create fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper ">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('server_setting_chatwork.label_header') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="box_message"></div>
                {!! Form::open(['route' => ['bot.setWebhookUrl', Route::current()->getParameter('bot')], 'method' => 'POST', 'class' => 'cmxform form-horizontal', 'role' => 'form']) !!}
                <div class="cmxform form-horizontal">
                    <div class="form-group">
                        {!! Form::label('webhook_url', trans('field.webhook_url'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-8">
                            {!! Form::text('url', @$connect_page->webhook_url, ['id' => 'webhook_url','class' => 'form-control']) !!}
                            <label for="inputUrl" class="error webhook-url-error"></label>
                        </div>
                    </div>
                </div>
                {!! Form::close()!!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-modal-close pull-left" data-dismiss="modal">{{{ trans('button.close') }}}</button>
                <button class="btn btn-info btn-modal-webhook-url-save">{{{ trans('button.save') }}}</button>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
        </div>
    </div>
</div>
<label class="error" id="crt-error"></label>
<script type="text/javascript">
    $(document).ready(function () {
        $('.btn_update_webhook_url').on('click', function () {
            $('.overlay-wrapper .overlay').hide();
            $('#setting-webhook-modal .box_message, label.error').html('');
            $('#setting-webhook-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        //submit ajax url
        var url = '{{route('bot.setWebhookUrl', Route::current()->getParameter('bot'))}}';
        $('.btn-modal-webhook-url-save').on('click', function () {
            $('#setting-webhook-modal .overlay-wrapper .overlay').show();
            var data = {
                '_token' : '{{csrf_token()}}',
                'url' : $(this).parents('#setting-webhook-modal').find('#webhook_url').val()
            };
            $.ajax({
                url: url,
                data: data,
                type: "POST",
                success: function(data) {
                    $('#setting-webhook-modal').modal('hide');
                    location.reload();
                },
                error: function(result){
                    $('.overlay-wrapper .overlay').hide();
                    var errors = $.parseJSON(result.responseText);
                    if(errors.url != void 0 && errors.url != '') {
                        $('#setting-webhook-modal .webhook-url-error').show().html(errors.url);
                    } else if(errors.errors != void 0 && errors.errors != '') {
                        setMesssage(errors.errors, 1, $('#setting-webhook-modal .box_message'));
                    }
                }
            });
        });
    });
</script>