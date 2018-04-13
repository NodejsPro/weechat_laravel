<div class="modal create-variable-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title modal-title-variable">{{{ trans('modal.variable_add')}}}</h4>
            </div>
            <div class="modal-body">
                <form class="cmxform form-horizontal" role="form">
                    <div class="show-config">
                        <div class="form-group">
                            {!! Form::label('variable_name', trans('field.variable_name'), ['class' => "col-md-3 control-label required"]) !!}
                            <div class="col-md-6">
                                {!! Form::text('variable_name', null, ['id' => 'variableName', 'class' => "form-control"]) !!}
                            </div>
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
                <button type="button" class="btn btn-info btn-modal-update hide">{{{ trans('button.save')}}}</button>
            </div>
        </div>
    </div>
</div>
<label class="error" id="crt-error"></label>
@section('scripts3')
<script>
    $(function () {
        setCurrentDataTable();
        var urlCreateVariable = '{{ URL::route('bot.variable.store', $connect_page_id) }}';
        var urlEditVariable = '{{ URL::route('bot.variable.edit', ['bot'=>$connect_page_id, 'variable'=>':variable_id']) }}';
        var urlUpdateVariable = '{{ URL::route('bot.variable.update', ['bot'=>$connect_page_id, 'variable'=>':variable_id']) }}';
        $('.variable_list .btn-create-variable').click(function(){

            $('.create-variable-modal .overlay').hide();
            resetCrtModal(true);
            $('.create-variable-modal').modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#datatable_custom_variable').on( 'click','a.btn-variable-edit', function () {
            var variable_id = $(this).data('button');
            var url_get = urlEditVariable.replace(':variable_id', variable_id);
            url_update = urlUpdateVariable.replace(':variable_id', variable_id);
            resetCrtModal(false);
            getEditData(url_get);
        });

        $('.create-variable-modal .btn-modal-create').on('click', function(evt) {
            sendCrtData(true, urlCreateVariable);
        });

        $('.create-variable-modal .btn-modal-update').click(function () {
            sendCrtData(false, url_update);
        });

        function getEditData(url){
            $.ajax({
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                type: 'GET',
                success: function(data) {
                    $('.create-variable-modal .overlay').hide();
                    if(data != void 0 && data.variable != void 0){
                        setEditModal(data.variable);
                        $('.create-variable-modal').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                    }
                },
                error: function(result){
                    $('.create-variable-modal .overlay').hide();
                }
            });
        }

        function setEditModal(data){
            console.log(data);
            $(".error-notification-alert").remove();
            $(".create-variable-modal #variableName").val(data.variable_name);
        }

        function resetCrtModal(isCreate){
            $(".error-variable-alert").remove();
            $('.create-variable-modal #variableName').val('');
            var modal_title = $(".modal-title-variable");
            if(isCreate){
                $('.create-variable-modal .btn-modal-create').removeClass('hide');
                $('.create-variable-modal .btn-modal-update').addClass('hide');
                modal_title.html('{{{ trans('modal.variable_add') }}}');
            }else{
                $('.create-variable-modal .btn-modal-create').addClass('hide');
                $('.create-variable-modal .btn-modal-update').removeClass('hide');
                modal_title.html('{{{ trans('modal.variable_edit') }}}');
            }
        }

        function setMsg(value, addTag){
            var msg = $("#crt-error").clone();
            msg.attr("id","");
            msg.addClass("error-variable-alert");
            msg.removeClass('hide');
            msg.html(value);
            msg.appendTo(addTag);
        }

        function showErrorMsg(data){
            if (data.errors != void 0){
                setMsg(data.errors.msg, $(".create-variable-modal #variableName").parent());
            }else if (data.variable_name != void 0){
                setMsg(data.variable_name[0], $(".create-variable-modal #variableName").parent());
            }
        }

        function sendCrtData(isCreate, url){
            var method = "POST";
            if(!isCreate){
                method = "PUT";
            }
            $(".error-variable-alert").remove();
            $('.create-variable-modal .overlay').show();
            var name   = $('.create-variable-modal #variableName').val();

            $.ajax({
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "variable_name": name,
                    'type' : 'custom_variable'
                },
                type: method,
                success: function(data) {
                    $('.create-variable-modal .overlay').hide();
                    $('.create-variable-modal').modal('hide');
                    setMesssage('{{trans('message.save_success', ['name' => trans('field.variable')])}}', 2);
                    variable_custom_datatable.ajax.reload(function (data) {
                        updateSlotVariable(data.data);
                    }, false);
                },
                error: function(result){
                    $('.create-variable-modal .overlay').hide();
                    var data = $.parseJSON(result.responseText);
                    console.log(data);
                    showErrorMsg(data);
                }
            });
        }

        variable_custom_datatable = $('#datatable_custom_variable').DataTable({
            info: false,
            processing: false,
            serverSide: true,
            ajax: {
                type: 'POST',
                url :'{!! route('bot.variable.list.custom', $connect_page_id) !!}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },

            paging: true,
            searching: false,
            ordering:  true,
            "dom": '<"top"i>rt<"bottom pull-left"flp><"clear">',
            columns: [
                { data: 'no', name: 'no', width: '50px' },
                { data: 'id', name: 'id', orderable: false, width: '200px'},
                { data: 'variable_name', name: 'variable_name'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '200px', visible: '{{ $_view_template_flg }}' }
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
            destroy: true,
            "fnDrawCallback": function(oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }else{
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                }
                if(oSettings._iRecordsTotal && !$('.custom_variable .chatwork-variable').length){
                    $('.custom_variable .variable-content').html('<a class= "btn btn-info chatwork-variable" href="{{URL::route('bot.reportChatwork.variable', $connect_page_id)}}" class="btn btn-info" data-connect_page_id="{{$connect_page_id}}">{{{ trans('report.variable') }}}</a>');
                } else if(oSettings._iRecordsTotal == 0){
                    $('.custom_variable .chatwork-variable').remove();
                }
            }
        });
    });
</script>
@endsection