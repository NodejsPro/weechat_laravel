<div class="modal user-notification-create-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title modal-title-user-notification">{{{ trans('user_notification.modal_title_add') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="row notification-content">
                    <div class="col-md-12 cmxform form-horizontal">
                        <div class="form-group">
                            {!! Form::label('inputTitle', trans('user_notification.title'), ['class' => 'col-md-3 control-label required']) !!}
                            <div class="col-md-8">
                                {!! Form::text('title', null, ['id' => 'inputTitle','class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('inputStartDate', trans('user_notification.start_date'), ['class' => 'col-md-3 control-label required']) !!}
                            <div class="col-md-8">
                                {!! Form::text('start_date', null, ['id' => 'inputStartDate','class' => 'form-control datetimepicker']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('inputDetail', trans('user_notification.detail'), ['class' => 'col-md-3 control-label required']) !!}
                            <div class="col-md-8">
                                {!! Form::textarea('detail', null, ['id' => 'inputDetail','class' => 'form-control', 'row' => '6']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn-modal-create">{{{ trans('button.save')}}}</button>
                <button type="button" class="btn btn-info btn-modal-update">{{{ trans('button.save')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<label class="error" id="crt-error"></label>
<script>
    $(function () {
        var urlEditNotification = '{{ URL::route('userNotification.edit', ['userNotification'=>':user_notification_id']) }}';
        var urlUpdateNotification = '{{ URL::route('userNotification.update', ['userNotification'=>':user_notification_id']) }}';
        var url_create = '{{ URL::route('userNotification.store') }}';
        var url_update;
        var crt_success;

        $('.btn-create-notification').on('click', function () {
            $('.overlay-wrapper .overlay').hide();
            resetCrtModal(true);
            crt_success = false;
            $('.user-notification-create-modal').modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#datatable_user_notification').on( 'click','a.btn-user-notification-edit', function () {
            var user_notification_id = $(this).data('button');
            var url_get = urlEditNotification.replace(':user_notification_id', user_notification_id);
            url_update = urlUpdateNotification.replace(':user_notification_id', user_notification_id);
            resetCrtModal(false);
            getEditData(url_get);
        });

        $('.btn-modal-create').on('click', function(evt) {
            sendCrtData(true, url_create);
        });

        $('.btn-modal-update').click(function () {
            sendCrtData(false, url_update);
        });

        $(".btn-modal-close").on('click', function(evt) {

        });

        function sendCrtData(isCreate, url){
            var method = "POST";
            if(!isCreate){
                method = "PUT";
            }
            $(".error-user-notification-alert").remove();
            $('.overlay-wrapper .overlay').show();

            $.ajax({
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "title": $('#inputTitle').val(),
                    "detail": $('#inputDetail').val(),
                    "start_date": $('#inputStartDate').val(),
                },
                type: method,
                success: function(data) {
                    $('.overlay-wrapper .overlay').hide();
                    $('.user-notification-create-modal').modal('hide');
                    setMesssage('{{trans('message.save_success', ['name' => trans('user_notification.name')])}}', 2);
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
            $.ajax({
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                type: 'GET',
                success: function(data) {
                    $('.overlay-wrapper .overlay').hide();
                    if(data != void 0 && data.user_notification != void 0){
                        setEditModal(data.user_notification);
                        $('.user-notification-create-modal').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                    }
                },
                error: function(result){
                    $('.overlay-wrapper .overlay').hide();
                }
            });
        }

        function resetCrtModal(isCreate){
            $(".error-user-notification-alert").remove();
            $('#inputTitle').val('');
            $('#inputDetail').val('');
            $('#inputStartDate').val('');
            var modal_title = $(".modal-title-user-notification");
            if(isCreate){
                $('.btn-modal-create').removeClass('hide');
                $('.btn-modal-update').addClass('hide');
                modal_title.html('{{{ trans('user_notification.modal_title_add') }}}');
            }else{
                $('.btn-modal-create').addClass('hide');
                $('.btn-modal-update').removeClass('hide');
                modal_title.html('{{{ trans('user_notification.modal_title_edit') }}}');
            }
        }

        function setEditModal(data){
            $(".error-user-notification-alert").remove();
            $('#inputTitle').val(data.title);
            $('#inputDetail').val(data.detail);
            $('#inputStartDate').val(data.start_date);
        }

        function showErrorMsg(data){
            console.log(data);
            if(data.title != void 0){
                setMsg(data.title[0], $("#inputTitle").parent());
            }
            if(data.detail != void 0){
                setMsg(data.detail[0], $("#inputDetail").parent());
            }
            if(data.start_date != void 0){
                setMsg(data.start_date[0], $("#inputStartDate").parent());
            }
            var errors = data.errors;
            if(errors != void 0 && errors.msg != void 0){
                setMsg(errors.msg, $(".notification-content").parent(), true);
            }
        }

        function setMsg(value, addTag, append_head){
            var msg = $("#crt-error").clone();
            msg.attr("id","");
            msg.addClass("error-user-notification-alert");
            msg.removeClass('hide');
            msg.html(value);
            if(append_head == void 0){
                msg.appendTo(addTag);
            }else{
                msg.prependTo(addTag);
            }
        }

        global_datatable = $('#datatable_user_notification').DataTable({
            info: false,
            processing: false,
            serverSide: true,
            ajax: {
                type: 'POST',
                url :'{!! route('userNotification.list') !!}',
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
                {data: 'title', name: 'title', className: 'title'},
                {data: 'start_date', name: 'start_date', className: 'start_date'},
                {data: 'detail', name: 'detail', className: 'detail'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '220px'},
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
            "fnDrawCallback": function(oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }else{
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                }
            },
            "bLengthChange": false,
            "bAutoWidth": false,
            destroy: true
        });

        $('.datetimepicker').datetimepicker({
            sideBySide: true,
            format: 'YYYY-MM-DD',
        });

    })
</script>