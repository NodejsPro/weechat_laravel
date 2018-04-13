<div class="modal slot_create_modal" id="slot_create_modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.slot_add')}}}</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal cmxform frm_slot_create" role="form">
                    <div class="form-group">
                        {!! Form::label('slot_name', trans('field.slot_name'), ['class' => "col-md-3 control-label slot_name required"]) !!}
                        <div class="col-md-6">
                            {!! Form::text('slot_name', null, ['class' => "form-control slot_name"]) !!}
                            <label for="slot_name" class="error slot_name_error"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('slot_action', trans('field.slot_activity'), ['class' => "col-md-3 control-label slot_action required"]) !!}
                        <div class="col-md-6">
                            {!! Form::select('slot_action', $slot_action, null, ['class' => "form-control slot_action", 'style' => 'width: 100%']) !!}
                            <label for="slot_action" class="error slot_action_error"></label>
                        </div>
                    </div>
                    <div class="form-group slot_scenario_box">
                        {!! Form::label('', '', ['class' => "col-md-3 control-label"]) !!}
                        <div class="col-md-6">
                            {!! Form::select('slot_scenario', $scenario_list, null, ['class' => "form-control slot_scenario", 'style' => 'width: 100%']) !!}
                            <label for="slot_scenario" class="error slot_scenario_error"></label>
                        </div>
                    </div>
                    <div class="form-group slot_api_box hidden">
                        {!! Form::label('', '', ['class' => "col-md-3 control-label"]) !!}
                        <div class="col-md-6">
                            {!! Form::select('slot_api', $api_list, null, ['class' => "form-control slot_api", 'style' => 'width: 100%']) !!}
                            <label for="slot_api" class="error slot_api_error"></label>
                        </div>
                    </div>
                    <div class="form-group slot_email_box hidden">
                        {!! Form::label('', '', ['class' => "col-md-3 control-label"]) !!}
                        <div class="col-md-6">
                            {!! Form::select('slot_email', $mail_list, null, ['class' => "form-control slot_email", 'style' => 'width: 100%']) !!}
                            <label for="slot_email" class="error slot_email_error"></label>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="col-md-offset-3">{{ trans('message.slot_variable_notice') }}</label>
                    </div>
                    <table class="table table_slot">
                        <thead>
                            <tr role="row">
                                <th><span class="control-label required">{{ trans('field.slot_item_name') }}</span></th>
                                <th><span class="control-label required">{{ trans('field.variable_name') }}</span></th>
                                <th><span class="control-label required">{{ trans('field.slot_question') }}</span></th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-info btn_slot_item_add pull-right">{{{ trans('button.slot_add')}}}</button>
                        </div>
                    </div>
                </form>
                <div class="row common_msg">
                    <div class="col-md-12">
                        <label class="error"></label>
                    </div>
                </div>
            </div>
            <div class="overlay" style="display: none">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn-modal-create">{{{ trans('button.save')}}}</button>
            </div>
        </div>
    </div>
    <div class="slot_origin_block hidden">
        <table>
            <tr class="slot_item">
                <td class="col-md-3">
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::text('item_name[]', null, ['class' => "form-control item_name"]) !!}
                            <label for="item_name" class="error item_name_error"></label>
                        </div>
                    </div>
                </td>
                <td class="col-md-3">
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::select('item_variable[]', $variable_custom, null, ['class' => "form-control item_variable", 'style' => 'width: 100%']) !!}
                            <label for="item_name" class="error item_variable_error"></label>
                        </div>
                    </div>
                </td>
                <td class="col-md-4">
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::text('item_question[]', null, ['class' => "form-control item_question"]) !!}
                            <label for="item_question" class="error item_question_error"></label>
                        </div>
                    </div>
                </td>
                <td class="col-md-2 slot_action">
                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger btn_slot_item_delete" disabled="disabled">{{{ trans('button.delete')}}}</button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
@section('scripts4')
<script>
    $(function () {
        setCurrentDataTable();
        var url_slot_store 	= '{{route('bot.slot.store', $connect_page_id)}}',
            url_slot_update = '{{ URL::route('bot.slot.update', [$connect_page_id, ':slot_id']) }}',
            slot_id  		= '';

        $('#slot_create_modal select.slot_action,' +
            '#slot_create_modal select.slot_scenario,' +
            '#slot_create_modal select.slot_api,' +
            '#slot_create_modal select.slot_email').select2({
            minimumResultsForSearch: -1,
            language: {
                "noResults": function(){
                    return "{{trans('message.no_results_found')}}";
                }
            }
        });

        //init model
        $('.variable_list .btn_create_slot').on('click', function () {
            resetCrtModal();
            cloneSlotItem();
            slot_id = '';
            $('#slot_create_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        //add slot item
        $('#slot_create_modal .btn_slot_item_add').on('click', function () {
            cloneSlotItem();
        });

        //delete slot item
        $(document).on('click', '#slot_create_modal .btn_slot_item_delete', function () {
            $(this).parents('.slot_item').remove();
            setIndexSlotItem();
        });

        $('#slot_create_modal select.slot_action').on('change', function () {
            selectByAction();
        });

        //button update model
        $('.variable_list #slot').on('click', '.btn_slot_edit', function (e) {
            resetCrtModal();
            $('#slot_create_modal .modal-title').html('{{ trans('modal.slot_update') }}');

            //get slot data to edit
            slot_id = $(this).data('id');
            if(slot_id != void 0 && slot_id != '') {
                var url_slot_get = url_slot_update.replace(':slot_id', slot_id);
                getEditData(url_slot_get);
            }
            $('#slot_create_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#slot_create_modal .btn-modal-create').on('click', function(evt) {
            $('#slot_create_modal .overlay').show();
            var isCreate = true,
                url_slot_sent = url_slot_store;
            if(slot_id != void 0 && slot_id != '') {
                url_slot_sent = url_slot_update.replace(':slot_id', slot_id);
                isCreate 	 = false;
            }
            sendCrtData(isCreate, url_slot_sent);
        });

        function sendCrtData(isCreate, url) {
            var method = "POST";
            if (!isCreate) {
                method = "PUT";
            }
            var slot_modal = $('#slot_create_modal'),
                data       = {},
                action     = slot_modal.find('select.slot_action').val();

            data['_token'] = '{{ csrf_token() }}';
            data['name']   = slot_modal.find('input.slot_name').val();
            data['action'] = action;
            switch (action) {
                case '{{ config('constants.slot_action.scenario') }}' :
                    data['scenario'] = slot_modal.find('select.slot_scenario').val();
                    break;
                case '{{ config('constants.slot_action.api') }}' :
                    data['api'] = slot_modal.find('select.slot_api').val();
                    break;
                case '{{ config('constants.slot_action.email') }}' :
                    data['email'] = slot_modal.find('select.slot_email').val();
                    break;
            }

            data['item_name']       = [];
            data['item_variable']   = [];
            data['item_question']   = [];
            slot_modal.find('.frm_slot_create .slot_item').each(function (i, e) {
                data['item_name'][i]     = $(this).find('input.item_name').val();
                data['item_variable'][i] = $(this).find('select.item_variable').val();
                data['item_question'][i] = $(this).find('input.item_question').val();
            });
            $.ajax({
                url: url,
                data: data,
                type: method,
                success: function(data) {
                    $('.slot_create_modal').modal('hide');
                    setMesssage('{{ trans('message.save_success', ['name' => trans('default.slot')]) }}', 2, $('.variable_list .box_message'));
                    slot_datatable.ajax.reload(null, false);
                },
                error: function(result){
                    var text = $.parseJSON(result.responseText);
                    showErrorMsg(text);
                    slot_modal.find('.overlay').hide();
                }
            });
        }

        function getEditData(url){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if(data.slot != void 0) {
                        var slot_data = data.slot;
                        setEditModal(slot_data);
                    }
                },
                error: function(result){
                    var text = $.parseJSON(result.responseText);
                    showErrorMsg(text);
                }
            });
        }

        /**
         * Set data edit to form
         **/
        function setEditModal(data) {
            //fill data to input
            var slot_modal = $('#slot_create_modal');
            var error_input = ['name', 'action'];
            $(error_input).each(function (i, input) {
                if(data[input] != void 0 && data[input] != '') {
                    var elm = slot_modal.find('.slot_' + input);
                    elm.val(data[input]);
                    if(elm.is("select")) {
                        elm.trigger('change.select2');
                    }
                }
            });
            //action input data
            switch (data['action']) {
                case '{{ config('constants.slot_action.scenario') }}' :
                    slot_modal.find('select.slot_scenario').val(data['action_data']).trigger('change.select2');
                    break;
                case '{{ config('constants.slot_action.api') }}' :
                    slot_modal.find('select.slot_api').val(data['action_data']).trigger('change.select2');
                    break;
                case '{{ config('constants.slot_action.email') }}' :
                    slot_modal.find('select.slot_email').val(data['action_data']).trigger('change.select2');
                    break;
            }
            selectByAction();
            //slot item
            if(data.item != void 0 && data.item.length) {
                $(data.item).each(function (i, e) {
                    //clone slot item
                    var slot_item = slot_modal.find('.slot_origin_block .slot_item').clone();
                    slot_item.find('input.item_name').val(e.name);
                    slot_item.find('select.item_variable').val(e.variable).trigger('change.select2');
                    slot_item.find('input.item_question').val(e.question);

                    slot_modal.find('.table_slot tbody').append(slot_item);
                });
            }
            setIndexSlotItem();
            initSelect2();
        }

        //cleat data form
        function resetCrtModal() {
            var slot_modal = $('#slot_create_modal');
            slot_modal.find('.modal-title').html('{{ trans('modal.slot_add') }}');
            slot_modal.find('.table_slot tbody, label.error').html('');
            slot_modal.find('input.slot_name').val('');
            slot_modal.find('select.slot_email').val(slot_modal.find('select.slot_email option').first().val()).trigger('change.select2');
            slot_modal.find('select.slot_action').val(slot_modal.find('select.slot_action option').first().val()).trigger('change.select2');
            slot_modal.find('select.slot_scenario').val(slot_modal.find('select.slot_scenario option').first().val()).trigger('change.select2');
            slot_modal.find('select.slot_api').val(slot_modal.find('select.slot_api option').first().val()).trigger('change.select2');
            slot_modal.find('.overlay').hide();
            selectByAction();
        }

        function showErrorMsg(data) {
            var slot_modal = $('#slot_create_modal');
            slot_modal.find('label.error').html('');

            if(data.msg != void 0 && data.msg != '') {
                slot_modal.find('.common_msg .error').html(data.msg).addClass('text-red');
            }
            //input show validate
            var error_input = ['name', 'action', 'scenario', 'api', 'email'];
            $(error_input).each(function (i, input) {
                if(data[input] != void 0 && data[input] != '') {
                    slot_modal.find('.frm_slot_create .slot_' + input + '_error').html(data[input]);
                }
            });
            //slot item validate
            slot_modal.find('.frm_slot_create .slot_item').each(function (i, e) {
                var name_key     = 'item_name.' + i,
                        variable_key = 'item_variable.' + i,
                        question_key = 'item_question.' + i;

                if(data[name_key] != void 0 && data[name_key][0] != void 0 && data[name_key][0] != '') {
                    $(this).find('label.item_name_error').html(data[name_key][0].replace(name_key, '{{ trans('field.name') }}'));
                }
                if(data[variable_key] != void 0 && data[variable_key][0] != void 0 && data[variable_key][0] != '') {
                    $(this).find('label.item_variable_error').html(data[variable_key][0].replace(variable_key, '{{ trans('field.variable') }}'));
                }
                if(data[question_key] != void 0 && data[question_key][0] != void 0 && data[question_key][0] != '') {
                    $(this).find('label.item_question_error').html(data[question_key][0].replace(question_key, '{{ trans('field.question') }}'));
                }
            });
        }

        /**
         * clone first slot item
         **/
        function cloneSlotItem() {
            var slot_modal  = $('#slot_create_modal');
            var slot_item   = slot_modal.find('.slot_origin_block .slot_item').clone();
            slot_modal.find('.table_slot tbody').append(slot_item);
            setIndexSlotItem();
            initSelect2();
        }
        /**
         * check to show, hide button delete slot item in form
         */
        function checkSlotDelete() {
            var slot_item = $('#slot_create_modal .table_slot .btn_slot_item_delete');
            if(slot_item.length <= 1) {
                slot_item.attr('disabled', 'disabled');
            } else {
                slot_item.attr('disabled', null);
            }
        }

        /**
         * set index for name inputs of slots
         */
        function setIndexSlotItem() {
            var slot_item = $('#slot_create_modal .table_slot .slot_item');
            slot_item.each(function (i, e) {
                $(this).find('input.item_name').attr('name', 'item_name['+i+']');
                $(this).find('select.item_variable').attr('name', 'item_variable['+i+']');
                $(this).find('input.item_question').attr('name', 'item_question['+i+']');
            });
            checkSlotDelete();
        }

        //show, hide option when change action
        function selectByAction() {
            var slot_modal  = $('#slot_create_modal');
            var action      = $('#slot_create_modal select.slot_action').val();
            $('#slot_create_modal .slot_api_box, #slot_create_modal .slot_email_box, #slot_create_modal .slot_scenario_box').addClass('hidden');
            switch (action) {
                case '{{ config('constants.slot_action.scenario') }}' :
                    slot_modal.find('.slot_scenario_box').removeClass('hidden');
                    break;
                case '{{ config('constants.slot_action.api') }}' :
                    slot_modal.find('.slot_api_box').removeClass('hidden');
                    break;
                case '{{ config('constants.slot_action.email') }}' :
                    slot_modal.find('.slot_email_box').removeClass('hidden');
                    break;
            }
        }

        function initSelect2() {
            //init select2 for new select input
            $('#slot_create_modal .table_slot select.item_variable:not(.select2-hidden-accessible)').select2({
                minimumResultsForSearch: -1,
                language: {
                    "noResults": function(){
                        return "{{trans('message.no_results_found')}}";
                    }
                }
            });
        }

        slot_datatable = $('#datatable_slot').DataTable({
            info: false,
            processing: false,
            serverSide: true,
            ajax: {
                type: 'POST',
                url :'{!! route('bot.slot.list', $connect_page_id) !!}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            paging: true,
            searching: false,
            ordering:  true,
            "dom": '<"top"i>rt<"bottom"flp><"clear">',
            columns: [
                { data: 'no', name: 'no', width: '50px' },
                { data: 'name', name: 'name' },
                { data: 'action_custom', name: 'action_custom' },
                { data: 'item_name', name: 'item_name', className: 'slot_item' },
                { data: 'item_variable', name: 'item_variable', className: 'slot_item' },
                { data: 'item_question', name: 'item_question', className: 'slot_item last' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '220px', visible: '{{ $_view_template_flg }}' }
            ],
            language:
            {
                emptyTable: "<p class='pull-left'>{{trans('message.not_record')}}</p>",
                zeroRecords: "<p class='pull-left'>{{trans('message.not_record')}}</p>",
                paginate:
                {
                    previous: "",
                    next: ""
                }
            },
            "fnDrawCallback": function(oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }else{
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                }
            },
            "pageLength": 10,
            "bLengthChange": false,
            "bAutoWidth": false,
            destroy: true
        });
    });
</script>
@endsection
