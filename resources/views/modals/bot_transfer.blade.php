<div class="modal" id="bot_transfer_modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.transfer')}}}</h4>
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
                        {!! Form::label('transfer_select', trans('field.transfer_to_bot'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-8">
                            <div class="transfer_select_box">
                                @if(isset($pages) && (count($pages) > 1) && isset($page_group_list) && count($page_group_list) || isset($page_efo_list) && count($page_efo_list))
                                   {{-- {!! Form::select('', ["" => trans('default.un_selected')] + $page_group_list, null, ['class' => 'form-control transfer_select page_select', 'id' => '']) !!}
                                    {!! Form::select('', ["" => trans('default.un_selected')] + $page_efo_list, null, ['class' => 'form-control transfer_select efo_page_select', 'id' => '']) !!}--}}
                                    {!! Form::select('', ["" => trans('default.un_selected')], null, ['class' => 'form-control transfer_select', 'id' => '']) !!}
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
                        <p class="bot_transfer_message col-md-offset-3 col-md-8"></p>
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
        var transfer_url        = '{{route('bot.transfer')}}',
            page_id             = '',
            page_source_name = '',
            sns_type,
//            transfer_select     = $('#bot_transfer_modal select.transfer_select'),
            transfer_select,
            page_efo = '{{config('constants.group_type_service.web_embed_efo')}}'    ;

        $('.btn-transfer-bot').on('click', function() {
            sns_type = $(this).data('service');

           /* $('.transfer_select_box .page_select').hide();
            $('.transfer_select_box .efo_page_select').hide();
            if (sns_type == page_efo) {
                $('.transfer_select_box .efo_page_select').show();
                transfer_select     = $('#bot_transfer_modal select.efo_page_select');
            }else {
                $('.transfer_select_box .page_select').show();
                transfer_select     = $('#bot_transfer_modal select.page_select');
            }*/
            var transfer_modal      = $('#bot_transfer_modal');
            transfer_modal.find('.modal_form_transfer, .btn-modal-transfer, .bot_transfer_message').show();
            transfer_modal.find('.transfer_success, label.error').html('');
            setMesssage(null);
            page_id = $(this).data('page_id');
            //
            getListBotTransfer(sns_type, page_id);
            transfer_select     = $('#bot_transfer_modal select.transfer_select');
            //
            page_source_name = $(this).attr('data-page_name');

//            //hide target option select
//            transfer_select.find('option').removeClass('hidden');
////            transfer_select.find("option[value='" + page_id + "']").addClass('hidden');
//            transfer_select.find("option[value='" + page_id + "']").remove();
            //re-select first option available
            var first_option    = transfer_select.find("option[class!='hidden']").first().val();
            transfer_select.val(first_option);

            transfer_modal.find('.overlay-wrapper .overlay').hide();
            //update transfer notice
            transfer_modal.find('span.bot_transfer_name').html($(this).attr('data-page_name'));
            changeTransferNotice();

            transfer_modal.modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $('.btn-modal-transfer').on('click', function(evt) {
            var transfer_modal    = $('#bot_transfer_modal');
            transfer_modal.find('.overlay-wrapper .overlay').show();
            console.log(transfer_select.val());
            $.ajax({
                url: transfer_url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "transfer_to_bot" : transfer_select.val(),
                    "transfer_from_bot" : page_id
                },
                type: 'POST',
                success: function(data) {
                    transfer_modal.find('.overlay-wrapper .overlay').hide();
                    setMesssage(data.msg, 2);
                    $('#bot_transfer_modal').modal('hide');
                    location.reload();
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

        $('#bot_transfer_modal select.transfer_select').on('change', function () {
            changeTransferNotice();
        });
        //get list bot transfer
        function getListBotTransfer(sns_type, page_id) {
            var get_list_url = '{{route('bot.getListBotTransfer')}}';
            transfer_select     = $('#bot_transfer_modal select.transfer_select');
            $.ajax({
                url: get_list_url,
                type: 'GET',
                success: function(data) {
                    var page_efo_list = data.page_efo_list,
                        page_group_list = data.page_group_list;
                    if (sns_type == page_efo) {
                        transfer_select.find('option').not(':first').remove();
                        Object.keys(page_efo_list).forEach(function (id) {
                            if (id  != page_id) {
                                transfer_select.append('<option value="'+id+'">' + page_efo_list[id] + '</option>');
                            }
                        });
                    } else {
                        transfer_select.find('optgroup').remove();
                        transfer_select.find('option').not(':first').remove();
                        Object.keys(page_group_list).forEach(function (group) {
                            var group_bot = transfer_select.append('<optgroup label="'+group+'"></optgroup>');
                            Object.keys(page_group_list[group]).forEach(function (id) {
                                if (id != page_id) {
                                    group_bot.append('<option value="'+id+'">' + page_group_list[group][id] + '</option>');
                                }
                            });
                        });
                    }
                },
                error: function(result){

                }
            });
        }

        //update transfer notice
        function changeTransferNotice() {
            var transfer_modal      = $('#bot_transfer_modal'),
                bot_msg_template    = '{{ trans('message.bot_transfer_message') }}',
                bot_msg             = '',
                page_dist_name      = transfer_select.find("option:checked").html();

            if(page_dist_name != void 0 && page_dist_name != '' && page_dist_name != '{{trans('default.un_selected')}}' && page_source_name != void 0 && page_source_name != '') {
                bot_msg             = bot_msg_template.replace(':name1', '<b>' + page_dist_name + '</b>').replace(':name2', '<b>' + page_source_name + '</b>');
            }
            transfer_modal.find('.bot_transfer_message').html(bot_msg);
        }
    });
</script>
