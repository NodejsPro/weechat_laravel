<div class="modal" id="bot_copy_modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.copy_bot')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="row modal_form_transfer cmxform">
                    <div class="form-group col-md-12 form-horizontal">
                        {!! Form::label('source_bot', trans('field.source_bot'), ['class' => "col-md-3 control-label"]) !!}
                        <div class="col-md-8">
                            <span class="bot_transfer_name control-label pull-left" style="font-weight: bold;"></span>
                        </div>
                    </div>
                    <div class="form-group col-md-12 form-horizontal">
                        {!! Form::label('user_select', trans('field.copy_to_user'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-8">
                            <div class="user_select_box">
                                @if(isset($user_list) &&  count($user_list))
                                    {!! Form::select('', ["" => trans('default.un_selected')] + $user_list, null, ['class' => 'form-control user_select', 'id' => '', 'style' => 'width: 100%']) !!}
                                @else
                                    <h5> {{{ trans('message.common_no_result')}}}</h5>
                                @endif
                            </div>
                            <label for="transfer_to_bot" class="error transfer_to_bot" style="display: none;"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p class="transfer_success"></p>
                    </div>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-danger btn-modal-copy_to_user">{{{ trans('button.execute')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script type="text/javascript">
    $(function () {
        var transfer_url = '{{route('bot.copyToUser')}}',
            page_id      = '',
            current_user = '',
            page_source_name = '',
            sns_type,
            transfer_select;
        $('.user_select').select2({
            minimumResultsForSearch: 1
        });
        $('.btn_copy_bot_to_user').on('click', function() {
            sns_type = $(this).data('service');
            transfer_select     = $('#bot_copy_modal select.user_select');
            //reset select box
            transfer_select.val(transfer_select.find("option").first().val()).trigger('change.select2');

            var transfer_modal      = $('#bot_copy_modal');
            transfer_modal.find('.transfer_success, label.error').html('');
            setMesssage(null);
            current_user = $(this).data('current_user');
            page_id = $(this).data('page_id');
            page_source_name = $(this).attr('data-page_name');

            //hide target option select
            transfer_select.find('option').removeClass('hidden');
            transfer_select.find("option[value='" + current_user + "']").addClass('hidden');

            transfer_select.find("option[value='" + current_user + "']").remove();
            //re-select first option available
            var first_option    = transfer_select.find("option[class!='hidden']").first().val();
            transfer_select.val(first_option);

            transfer_modal.find('.overlay-wrapper .overlay').hide();
            //update transfer notice
            transfer_modal.find('span.bot_transfer_name').html($(this).attr('data-page_name'));

            transfer_modal.modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $('.btn-modal-copy_to_user').on('click', function(evt) {
            var transfer_modal    = $('#bot_copy_modal');
            transfer_modal.find('.overlay-wrapper .overlay').show();
            console.log(transfer_select.val());
            $.ajax({
                url: transfer_url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "to_user" : transfer_select.val(),
                    "user_name" : transfer_select.find('option:selected').text(),
                    "transfer_from_bot" : page_id
                },
                type: 'POST',
                success: function(data) {
                    transfer_modal.find('.overlay-wrapper .overlay').hide();
                    setMesssage(data.msg, 2);
                    $('#bot_copy_modal').modal('hide');
                    $('.btn_copy_bot_to_user[data-page_id='+ page_id +']').closest('.pannel-content').remove();
//                    setTimeout(function () {
//                        location.reload();
//                    }, 2000);
                },
                error: function(result){
                    transfer_modal.find('.overlay-wrapper .overlay').hide();
                    var error_data = $.parseJSON(result.responseText),
                        error_msg  = '';
                    if(error_data.to_user != void 0){
                        error_msg = error_data.to_user[0];

                    } else if(error_data.msg != void 0){
                        error_msg = error_data.msg;
                    } else {
                        error_msg = error_data;
                    }
                    transfer_modal.find('label.transfer_to_bot').html(error_msg).show();
                }
            });
        });
    });
</script>
