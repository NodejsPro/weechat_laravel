<div class="modal create-library-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title modal-title-group">{{{ trans('modal.group_add')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="box_message"></div>
                <form class="form-horizontal cmxform library_group" role="form">
                    <div class="form-group">
                        <label for="group_name" class="col-md-3 control-label required">{{trans('field.library_sheet_type')}}</label>
                        <div class="col-md-8">
                            {!! Form::select('library_sheet_type', $library_sheet_type, null, ['class' => 'form-control select2-library', 'id' => 'library_sheet_type', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('group_name', trans('field.group_name'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-8">
                            {!! Form::text('group_name', null, ['id' => 'inputGroupName', 'class' => "form-control"]) !!}
                        </div>
                    </div>
                    <div class="form-group read-type-group" style="display: none;">
                        {!! Form::label('sheet_id', trans('field.sheet_id'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-8">
                            {!! Form::text('sheet_id', null, ['id' => 'inputSheetId', 'class' => "form-control sheet-type-item"]) !!}
                            {!! Form::label('read_google_sheet_description', trans('message.read_google_sheet_description', ['column_bot' => config('constants.sheet_setting_column.bot'), 'column_user' => config('constants.sheet_setting_column.user') ]), ['class' => 'label-description'])!!}
                        </div>
                    </div>
                    <div class="form-group connect-scenario-group">
                        {!! Form::label('connect_scenario', trans('field.connect_scenario'), ['class' => "col-md-3 control-label"]) !!}
                        <div class="col-md-8">
                            {!! Form::select('scenario_id', $scenario_connect, null, ['id' => 'inputScenarioId', 'class' => "form-control select2-library", 'style' => 'width:100%'])!!}
                            {!! Form::label('connect_scenario_after_keyword_matching', trans('message.connect_scenario_after_keyword_matching'), ['class' => 'label-description'])!!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('all_dialog_flg', trans('field.all_dialog_checkbox'), ['class' => "col-md-3 control-label"]) !!}
                        <div class="col-md-9 all_dialog_input_box minimal">
                            <input type="checkbox" value="1" style="width: 20px" class="checkbox form-control icheck" id="all_dialog_flg" name="all_dialog_flg" />
                            {!! Form::label('all_dialog_notice', trans('message.all_dialog_message'), ['class' => 'label-description'])!!}
                        </div>
                    </div>
                </form>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn-modal-create">{{{ trans('button.save')}}}</button>
            </div>
        </div>
    </div>
</div>
<label class="error" id="crt-error"></label>
<script>
    $(function () {
        var url_create = '{{ URL::route('bot.library.store', $connect_page_id) }}';
        var url_edit = '{{ URL::route('bot.library.edit', ['bot'=>$connect_page_id, 'library'=>':library_id']) }}';
        var url_update = '{{ URL::route('bot.library.update', ['bot'=>$connect_page_id, 'library'=>':library_id']) }}';
        var form_method = 'POST';
        var library_id = null;

        $('.btn-create-library').click(function(){
            resetCrtModal(true);
            showModal();
        });

        initSelect2();

        $('.btn-modal-create').on('click', function(evt) {
            var url = url_create;
            if(form_method == 'PUT'){
                url = url_update.replace(':library_id', library_id);
            }
            sendCrtData(url);
        });

        $(document).on( 'click','#datatable_group .btn-library-edit', function () {
            form_method = 'PUT';
            library_id = $(this).data('button');
            var url_edit2 = url_edit.replace(':library_id', library_id);
            resetCrtModal(false);
            getEditData(url_edit2);
        });

        function sendCrtData(url){
            $(".error-notification-alert").remove();
            $('.overlay-wrapper .overlay').show();

            $.ajax({
                url: url,
                data: $('.library_group').serialize(),
                type: form_method,
                success: function(data) {
                    $('.overlay-wrapper .overlay').hide();
                    $('.create-library-modal').modal('hide');
                    setMesssage('{{trans('message.save_success', ["name" => trans('default.group')])}}', 2, $('.library_list .box_message').first());
                    global_datatable.ajax.reload(null, false);
                },
                error: function(result){
                    $('.overlay-wrapper .overlay').hide();
                    var data = $.parseJSON(result.responseText);
                    showErrorMsg(data);
                }
            });
        }

        function getEditData(url){
            $('.overlay-wrapper .overlay').show();
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if(data != void 0 && data.library != void 0){
                        setEditModal(data.library);
                        showModal();
                    }
                },
                error: function(result){

                }
            });
        }

        function setEditModal(data){
            $('#inputGroupName').val(data.name);
            if(!data.all_dialog_flg){
                $('#all_dialog_flg').iCheck('uncheck');
            }
            var type = data.library_sheet_type;
            if(type != void 0 && type == '{{config('constants.library_sheet_type.reference_google_spreadsheet')}}'){
                $('#inputSheetId').val(data.sheet_id);
            } else{
                type = '{{config('constants.library_sheet_type.add_keyword_matching')}}';
            }
            $('select#library_sheet_type').val(type).trigger('change');
            if(data.scenario_id != void 0){
                $('select#inputScenarioId').val(data.scenario_id).trigger('change');
            }
        }

        function resetCrtModal(is_create){
            $(".error-notification-alert").remove();
            $(".alert").remove();
            $('#inputGroupName').val('');
            $('#inputSheetId').val('');
            $('#all_dialog_flg').iCheck('check');
            $('select#library_sheet_type').val('{{config('constants.library_sheet_type.add_keyword_matching')}}').trigger('change');
            $('select#inputScenarioId').val('').trigger('change');
            var modal_title = $(".modal-title-group");
            if(is_create){
                form_method = 'POST';
                library_id = null;
                modal_title.html('{{{ trans('modal.group_add') }}}');
            }else{
                modal_title.html('{{{ trans('modal.group_edit') }}}');
            }
        }

        function showModal(){
            $('.overlay-wrapper .overlay').hide();
            $('.create-library-modal').modal({
                backdrop: 'static',
                keyboard: false
            })
        }

        function showErrorMsg(data){
            if(data.group_name != void 0){
                setMsg(data.group_name[0], $("#inputGroupName").parent());
            }
            if(data.sheet_id != void 0){
                setMsg(data.sheet_id[0], $("#inputSheetId").parent());
            }
        }

        function setMsg(value, addTag){
            var msg = $("#crt-error").clone();
            msg.attr("id","");
            msg.addClass("error-notification-alert");
            msg.removeClass('hide');
            msg.html(value);
            msg.appendTo(addTag);
        }

        $(document).on('change', 'select#library_sheet_type', function (event) {
            checkLibraryRead();
        });

        function checkLibraryRead() {
            var library_sheet_type = $('select#library_sheet_type').val();
            if(library_sheet_type == '{{config('constants.library_sheet_type.add_keyword_matching')}}'){
                $('.read-type-group').hide();
            } else{
                $('.read-type-group').show();
            }
        }
        global_datatable = $('#datatable_group').DataTable({
            info: false,
            processing: false,
            serverSide: true,
            ajax: {
                type: 'POST',
                url :'{!! route('bot.library.list', $connect_page_id) !!}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },

            paging: true,
            searching: false,
            ordering:  true,
            "dom": '<"top"i>rt<"bottom pull-left"flp><"clear">',
            columns: [
                {data: 'no', name: 'no', width: '50px'},
                {data: 'name', name: 'name', class: 'library_name'},
                {data: 'library_status_type', name: 'library_status_type', class: 'library_sheet_type'},
                {data: 'google_spread_next_time', name: 'google_spread_next_time', class: 'google_spread_next_time', width: '170px'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '220px', visible: '{{ $_view_template_flg }}'}
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
            "pageLength": 10,
            "bLengthChange": false,
            "bAutoWidth": false,
             scrollX: true,
             destroy: true,
            "fnDrawCallback": function(oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }else{
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                }
                if(oSettings._iRecordsTotal && !$(oSettings.nTableWrapper).find('.table-note').length){
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').before('<div class="table-note">{{trans('message.google_spreadsheet_note', ['column_user' => config('constants.sheet_setting_column.user'), 'column_bot' => config('constants.sheet_setting_column.bot')])}}</div>')
                } else if(oSettings._iRecordsTotal == 0){
                    $(oSettings.nTableWrapper).find('.table-note').remove();
                }
            }
        });

        function initSelect2() {
            $('select.select2-library').select2({
                language: {
                    "noResults": function(){
                        return "{{trans('message.no_results_found')}}";
                    }
                },
                minimumResultsForSearch: -1
            });
        }

    });
</script>
