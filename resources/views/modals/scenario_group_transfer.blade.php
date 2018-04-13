<div class="modal" id="group_transfer_modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.group_transfer')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="row modal_form_transfer cmxform">
                    {{--<div class="form-group col-md-12 form-horizontal">--}}
                        {{--{!! Form::label('source_bot', trans('field.source_bot'), ['class' => "col-md-3 control-label"]) !!}--}}
                        {{--<div class="col-md-8">--}}
                            {{--<span class="bot_transfer_name control-label pull-left" style="font-weight: bold;"></span>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group col-md-12 form-horizontal">
                        {!! Form::label('transfer_select', trans('field.transfer_to_bot'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-8">
                            <div class="transfer_select_box">
                                @if(isset($pages) && (count($pages) > 1) && isset($page_group_list) && count($page_group_list) || isset($page_efo_list) && count($page_efo_list))
                                    {!! Form::select('', ["" => trans('default.un_selected')] + $page_group_list, null, ['class' => 'form-control transfer_select page_select', 'id' => '']) !!}
                                    {!! Form::select('', ["" => trans('default.un_selected')] + $page_efo_list, null, ['class' => 'form-control transfer_select efo_page_select', 'id' => '']) !!}
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
                        <p class="group_transfer_message col-md-offset-3 col-md-8"></p>
                        <p class="transfer_success"></p>
                    </div>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-danger btn-modal-transfer">{{{ trans('button.execute')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script type="text/javascript">
    $(function () {
        var transfer_url = '{{route('bot.scenarioGroup.transfer', $connect_page_id)}}',
            page_id = '',
            group_id = '',
            group_name = '',
            sns_type,
            transfer_select,
            page_efo = '{{config('constants.group_type_service.web_embed_efo')}}';

        $('.btn-transfer-bot').on('click', function() {
            sns_type = $(this).data('service');
            page_id = $(this).data('page_id');
            $('#group_transfer_modal .page_select').hide();
            $('#group_transfer_modal .efo_page_select').hide();
            if (sns_type == page_efo) {
                $('#group_transfer_modal .efo_page_select').show();
                transfer_select     = $('#group_transfer_modal select.efo_page_select');
            }else {
                $('#group_transfer_modal .page_select').show();
                transfer_select     = $('#group_transfer_modal select.page_select');
            }
            var transfer_modal = $('#group_transfer_modal');
            transfer_modal.find('.modal_form_transfer, .btn-modal-transfer, .group_transfer_message').show();
            transfer_modal.find('.transfer_success, label.error').html('');
            setMesssage(null);
            group_id = $(this).data('group_id');
            group_name = $(this).attr('data-group_name');
            sns_type = $(this).attr('data-service');

            //hide target option select
            transfer_select.find("option[value='" + page_id + "']").remove();
            //re-select first option available
            var first_option = transfer_select.find("option[class!='hidden']").first().val();
            transfer_select.val(first_option);

            transfer_modal.find('.overlay-wrapper .overlay').hide();
            //update transfer notice
            changeTransferNotice();

            transfer_modal.modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $('.btn-modal-transfer').on('click', function(evt) {
            var transfer_modal    = $('#group_transfer_modal');
            transfer_modal.find('.overlay-wrapper .overlay').show();
            var page_dist_name      = transfer_select.find("option:checked").html();
            $.ajax({
                url: transfer_url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "transfer_to_bot" : transfer_select.val(),
                    "page_dist_name" : page_dist_name,
                    "group_id" : group_id,
                    "group_name" : group_name,
                    "sns_type" : sns_type
                },
                type: 'POST',
                success: function(data) {
                    transfer_modal.find('.overlay-wrapper .overlay').hide();
                    setMesssage(data.msg, 2);
                    $('#group_transfer_modal').modal('hide');
                },
                error: function(result){
                    transfer_modal.find('.overlay-wrapper .overlay').hide();
                    var error_data = $.parseJSON(result.responseText),
                        error_msg  = '';
                    if(error_data.transfer_to_bot != void 0){
                        error_msg = error_data.transfer_to_bot[0];

                    } else if(error_data.transfer_from_bot != void 0){
                        error_msg = error_data.transfer_from_bot[0];

                    } else if(error_data.msg != void 0){
                        error_msg = error_data.msg;
                    } else {
                        error_msg = error_data;
                    }
                    transfer_modal.find('label.transfer_to_bot').html(error_msg).show();
                }
            });
        });

        $('#group_transfer_modal select.transfer_select').on('change', function () {
            changeTransferNotice();
        });

        //update transfer notice
        function changeTransferNotice() {
            var transfer_modal      = $('#group_transfer_modal'),
                bot_msg_template    = '{{ trans('message.group_transfer_message') }}',
                bot_msg             = '',
                page_dist_name      = transfer_select.find("option:checked").html();

            if(page_dist_name != void 0 && page_dist_name != '' && page_dist_name != '{{trans('default.un_selected')}}' && group_name != void 0 && group_name != '') {
                bot_msg             = bot_msg_template.replace(':bot_name', '<b>' + page_dist_name + '</b>').replace(':group_name', '<b>' + group_name + '</b>');
            }
            transfer_modal.find('.group_transfer_message').html(bot_msg);
        }
    });
</script>
