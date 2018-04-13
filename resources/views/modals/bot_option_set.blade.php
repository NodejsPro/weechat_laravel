<div class="modal" id="bot_setting_modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('scenario.setting_option')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="row modal_form_transfer cmxform">
                    <div class="form-group col-md-12 form-horizontal">
                        {!! Form::label('option_select', trans('scenario.option'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-8">
                            <div class="option_select_box">
                                @if(isset($service_message_name_list) &&  count($service_message_name_list))
                                    {!! Form::select('', $service_message_name_list, null, ['class' => 'form-control option_select', 'id' => '', 'style' => 'width: 100%']) !!}
                                @else
                                    <h5> {{{ trans('message.common_no_result')}}}</h5>
                                @endif
                            </div>
                            <label for="option_select_error" class="error option_select_error" style="display: none;"></label>
                        </div>
                    </div>
                    <div class="form-group col-md-12 form-horizontal">
                        {!! Form::label('scenario_select', trans('field.scenario_select'), ['class' => "col-md-3 control-label"]) !!}
                        <div class="col-md-8">
                            <div class="scenario_select_box">
                                @if(isset($scenario_set_option) &&  count($scenario_set_option))
                                    {!! Form::select('', ["" => trans('default.un_selected')] + $scenario_set_option, null, ['class' => 'form-control scenario_select', 'id' => '', 'style' => 'width: 100%']) !!}
                                @else
                                    <h5> {{{ trans('message.common_no_result')}}}</h5>
                                @endif
                            </div>
                            <label for="scenario_select_error" class="error scenario_select_error" style="display: none;"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p class="set_option_message col-md-offset-3 col-md-8"></p>
                    </div>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn-modal-set_option">{{{ trans('button.save')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script type="text/javascript">
    $(function () {
        var set_option_url = '{{route('bot.setOption')}}',
            page_id = '',
            option_select = '',
            scenario_select,
            list_option_message,
            reload_flg = true,
            list_option_by_bot;
        $('.scenario_select, .option_select').select2({
            minimumResultsForSearch: 1
        });

        $('.btn_set_option').on('click', function() {
            option_select     = $('#bot_setting_modal select.option_select');
            scenario_select     = $('#bot_setting_modal select.scenario_select');
            //reset select box
            list_option_by_bot = $(this).data('list_option_by_bot');
            list_option_message = $(this).data('option_message');
            if (reload_flg) {
                if (list_option_by_bot != null && list_option_by_bot.length) {
                    option_select.val(list_option_by_bot[0].option).trigger('change.select2');
                    scenario_select.val(list_option_by_bot[0].scenario_connect).trigger('change.select2');
                }else {
                    option_select.val(option_select.find("option").first().val()).trigger('change.select2');
                    scenario_select.val(scenario_select.find("option").first().val()).trigger('change.select2');
                }
            }
            if (list_option_message[option_select.val()] != undefined) {
                $('.set_option_message').html(list_option_message[option_select.val()]);
            }
            page_id = $(this).data('page_id');

            var set_option_modal      = $('#bot_setting_modal');
            set_option_modal.find('.transfer_success, label.error').html('');
            setMesssage(null);
            setValueOption();
            set_option_modal.find('.overlay-wrapper .overlay').hide();
            set_option_modal.modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $('.btn-modal-set_option').on('click', function(evt) {
            var set_option_modal    = $('#bot_setting_modal');
            set_option_modal.find('.overlay-wrapper .overlay').show();

            $.ajax({
                url: set_option_url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "option" : option_select.val(),
                    "scenario_connect" : scenario_select.val(),
                    "connect_page_id" : page_id
                },
                type: 'POST',
                success: function(data) {
                    set_option_modal.find('.overlay-wrapper .overlay').hide();
                    setMesssage(data.msg, 2);
                    $('#bot_setting_modal').modal('hide');
                    reload_flg = false;
                },
                error: function(result){
                    set_option_modal.find('.overlay-wrapper .overlay').hide();
                    var error_data = $.parseJSON(result.responseText),
                        error_msg  = '',
                        option_error_msg  = '';
                    if(error_data.option != void 0){
                        option_error_msg = error_data.option[0];
                        set_option_modal.find('label.option_select_error').html(option_error_msg).show();
                    }else if(error_data.scenario_connect != void 0){
                        error_msg = error_data.scenario_connect[0];
                    }else if(error_data.msg != void 0){
                        error_msg = error_data.msg;
                    } else {
                        error_msg = error_data;
                    }
                    set_option_modal.find('label.scenario_select_error').html(error_msg).show();
                }
            });
        });

        function setValueOption() {
            $(document).on('change', '.option_select', function (event) {
                if (list_option_by_bot != null && list_option_by_bot.length) {
                    var action_for_option = [];
                    $(list_option_by_bot).each(function (index, item) {
                        action_for_option[item.option] = item.scenario_connect;
                    });
                    if (action_for_option[$(this).val()] != undefined) {
                        scenario_select.val(action_for_option[$(this).val()]).trigger('change.select2');
                    }else {
                        scenario_select.val(scenario_select.find("option").first().val()).trigger('change.select2');
                    }
                    if (list_option_message[$(this).val()] != undefined) {
                        $('.set_option_message').html(list_option_message[$(this).val()]);
                    }
                }
            });
        }
    });
</script>
