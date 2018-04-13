<div id="scenario-create-modal" class="modal scenario-create fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper ">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.scenario_add') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="box_message"></div>
                <div class="update_check hidden">
                    {!! Form::hidden('update_flg', 0, ['class' => 'form-control update_flg']) !!}
                    {!! Form::hidden('scenario_id', null, ['class' => 'form-control scenario_id']) !!}
                </div>
                {!! Form::open(['route' => ['bot.scenario.store', Route::current()->getParameter('bot')], 'method' => 'POST', 'class' => 'cmxform form-horizontal filterForm', 'role' => 'form']) !!}
                {!! Form::hidden('scenario_group_id', null, ['class' => 'form-control scenario_group_id']) !!}
                {!! Form::hidden('scenario_id', null, ['class' => 'form-control scenario_id']) !!}
                <div class="cmxform form-horizontal">
                    <div class="form-group">
                        {!! Form::label('scenario_name', trans('field.scenario_name'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-7">
                            {!! Form::text('name', null, ['id' => 'scenario_name','class' => 'form-control']) !!}
                            <label for="inputName" class="error scenario-name-error"></label>
                        </div>
                    </div>
                    @if($connect_page->sns_type != config('constants.group_type_service.web_embed_efo'))
                        <div class="form-group">
                            {!! Form::label('scenario_name', trans('field.keyword_match_select'), ['class' => "col-md-3 control-label"]) !!}
                            <div class="col-md-7">
                                {!! Form::select('library[]', $library_list, null, ['class' => 'form-control button_sub_type', 'multiple' => 'multiple', 'style' => 'width: 100%']) !!}
                                <h6>{{ trans('message.keyword_match_scenario_notice') }}</h6>
                            </div>
                        </div>

                        @if(isset($variable_custom_list) && count($variable_custom_list))
                            <div class="form-group attach_variable_contain">
                                {!! Form::label('attach_value', trans('field.attach_variable'), ['class' => "col-md-3 control-label"]) !!}
                                <div class="col-md-9">
                                    <div class="col-xs-4 attach_variable_box">
                                        {!! Form::select('attach_id', ['' => ' '] + $variable_custom_list, null, ['class' => 'form-control attach_id']) !!}
                                    </div>
                                    <div class="col-xs-6 attach_value_box hidden">
                                        {!! Form::text('attach_value', null, ['class' => 'form-control attach_value']) !!}
                                    </div>
                                </div>
                                <div class="col-md-offset-3 col-md-9">
                                    <h6>{{ trans('message.attach_variable_notice') }}</h6>
                                </div>
                            </div>
                        @endif

                        {{--filter area--}}
                        <div class="form-group filter_status">
                            {!! Form::label('scenario_name', trans('field.filter_status'), ['class' => "col-md-3 control-label"]) !!}
                            {!! Form::label('scenario_name', trans('field.filter_status_message'), ['class' => "col-md-7 control-label", 'style' => 'text-align: left;']) !!}
                        </div>
                        <div class="filter_contain"></div>

                        <div class="form-group">
                            <div align="right" class="col-md-offset-8 col-md-2">
                                <button id="btn_filter_add" type="button" class="btn btn-info">{{trans('button.add_filter')}}</button>
                            </div>
                        </div>
                        {{--end filter area--}}
                    @endif

                    <div class="columns separator"></div>
                </div>
                {!! Form::close()!!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-modal-close pull-left" data-dismiss="modal">{{{ trans('button.close') }}}</button>
                <button class="btn btn-info btn-modal-scenario-create">{{{ trans('button.save') }}}</button>
            </div>

            {{--element template--}}
            <div class="hidden element_template">
                {{--filter item--}}
                <div class="template_filter">
                    {{----}}
                    {!! Form::text('', URL::route('bot.scenario.getOptionFilterByVariable', $connect_page_id), ['class' => "filter_variable_data_url"]) !!}
                    {!! Form::text('', csrf_token(), ['class' => "csrf_token"]) !!}

                    <div class="form-group col-md-2 select_option">
                        {!! Form::select('', ['and' => 'AND','or' => 'OR'], null, ['class' => 'form-control filter_option']) !!}
                    </div>

                    <div class="col-md-12 filter_item">
                        <div class="form-group col-md-2 field_filter_option"></div>
                        <div class="form-group col-md-2">
                            {!! Form::select('', $filter_variable_list, null, ['class' => 'form-control filter_variable']) !!}
                        </div>
                        <div class="form-group col-md-2">
                            {!! Form::select('', [], null, ['class' => 'form-control filter_operator']) !!}
                        </div>
                        <div class="form-group col-md-2 filter_value_box">
                            {{--text type--}}
                            {!! Form::text('', null, ['class' => 'form-control filter_value_text']) !!}
                            {{--select type--}}
                            {!! Form::select('', [], null, ['class' => 'form-control hidden filter_value_select']) !!}
                            {{--date type: day, month select--}}
                            <div class="col-md-12 filter_date_box hidden">
                                <div class="col-xs-6 filter_value_hour_box">
                                    <select class="form-control filter_value_hour">
                                        @for($i=0; $i<=23; $i++)
                                            @if($i<10)
                                                <?php $i = '0'.$i; ?>
                                            @endif
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-xs-6 filter_value_minute_box">
                                    <select class="form-control filter_value_minute">
                                        @for($i=0; $i<=59; $i++)
                                            @if($i<10)
                                                <?php $i = '0'.$i; ?>
                                            @endif
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3 filter_scenario_value">
                            {!! Form::select('', ['' => trans('field.scenario_select')] + $scenario_select_list->toArray(), null, ['class' => 'form-control filter_scenario']) !!}
                        </div>
                        <div class="form-group col-md-1">
                            <button type="button" class="btn btn-danger filter_btn_delete">{{trans('button.delete')}}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#scenario-create-modal select.button_sub_type').select2({
            "language": {
                "noResults": function(){
                    return "{{trans('message.no_results_found')}}";
                }
            }
        });
    });

    // hide edit view after create or update scenario
    $('.btn-scenario-create').on('click', function () {
        var scenario_group_id = $(this).data('scenario_group_id'),
            scenario_id = $(this).data('scenario_id');
        $('.filterForm').find('.scenario_group_id').attr('value', scenario_group_id);
        $('.filterForm').find('.scenario_id').attr('value', scenario_id);

        $('.overlay-wrapper .overlay').hide();
        $('#scenario-create-modal .box_message, label.error').html('');
        //clear filter
        filterClear();
        clearAttachVariable();
        $('#scenario-create-modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#scenario-create-modal .attach_variable_contain select.attach_id').on('change', function () {
        var attach_value_box = $('#scenario-create-modal .attach_variable_contain .attach_value_box');
        if($(this).val() != void 0 && $(this).val() != '') {
            attach_value_box.removeClass('hidden');
        } else {
            attach_value_box.addClass('hidden');
        }
    });

    // globe submit ajax url
    var url = '{{route('bot.scenario.store', Route::current()->getParameter('bot'))}}';
    $('.btn-modal-scenario-create').on('click', function () {
        var url2 = url;
        if($('.update_flg').val() =='{{config('constants.active.enable')}}'){
            url2 = '{{ URL::route('bot.scenario.updateScenarioInfo', [Route::current()->getParameter('bot'), ':scenario_id']) }}';
            url2 = url2.replace(':scenario_id', $('.scenario_id').val());
        }
        $('.overlay-wrapper .overlay').show();
        $.ajax({
            url: url2,
            data: $('.filterForm').serializeArray(),
            type: "POST",
            success: function(data) {
                $('#scenario-create-modal').modal('hide');
                location.reload();
            },
            error: function(result){
                $('.overlay-wrapper .overlay').hide();
                var errors = $.parseJSON(result.responseText);
                if(errors.name != void 0 && errors.name != '') {
                    $('#scenario-create-modal .scenario-name-error').show().html(errors.name);
                } else if(errors.errors != void 0 && errors.errors != '') {
                    setMesssage(errors.errors, 1, $('#scenario-create-modal .box_message'));
                }
            }
        });
    });

    function clearAttachVariable() {
        var scenario_create_modal = $('#scenario-create-modal'),
            variable_first_option_val = scenario_create_modal.find('.attach_variable_contain select.attach_id option').first().val();

        //reset input
        if(variable_first_option_val != void 0) {
            scenario_create_modal.find('.attach_variable_contain select.attach_id').val(variable_first_option_val);
        }
        scenario_create_modal.find('.attach_variable_contain .attach_value_box').addClass('hidden').find('input.attach_value').val('');
    }
</script>