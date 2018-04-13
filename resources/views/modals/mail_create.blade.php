<div class="modal mail-create-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title modal-title-mail">{{{ trans('modal.mail_add') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="row menu-content">
                    <div class="col-md-12 cmxform form-horizontal">
                        <div class="form-group">
                            {!! Form::label('inputEmailName', trans('field.email_name'), ['class' => 'col-md-3 control-label required']) !!}
                            <div class="col-md-7">
                                {!! Form::text('email_name', null, ['id' => 'inputEmailName', 'class' => "form-control"]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('inputFromName', trans('field.from_name'), ['class' => "col-md-3 control-label"]) !!}
                            <div class="col-md-7">
                                {!! Form::text('to', null, ['id' => 'inputFromName','class' => 'form-control']) !!}
                            </div>
                        </div>
                        {{--<div class="form-group">--}}
                            {{--{!! Form::label('inputFromEmail', trans('field.from_email'), ['class' => "col-md-3 control-label"]) !!}--}}
                            {{--<div class="col-md-7">--}}
                                {{--{!! Form::text('to', null, ['id' => 'inputFromEmail','class' => 'form-control']) !!}--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="form-group">
                            {!! Form::label('inputTo', trans('field.to'), ['class' => "col-md-3 control-label required"]) !!}
                            <div class="col-md-7">
                                {!! Form::text('to', null, ['id' => 'inputTo','class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('inputSubject', trans('field.subject'), ['class' => 'col-md-3 control-label required']) !!}
                            <div class="col-md-7">
                                {!! Form::text('subject', null, ['id' => 'inputSubject', 'class' => "form-control"]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('inputContent', trans('field.content'), ['class' => "col-md-3 control-label required"]) !!}
                            <div class="col-md-7">
                                {!! Form::textarea('content', null, ['id' => 'inputContent','class' => 'form-control', 'row' => '6']) !!}
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
    var id = '';
    $(function () {
        var urlEditMail = '{{ URL::route('bot.mail.edit', ['bot'=>$connect_page_id, 'mail'=>':mail_id']) }}';
        var urlUpdateMail = '{{ URL::route('bot.mail.update', ['bot'=>$connect_page_id, 'mail'=>':mail_id']) }}';
        var url_create = '{{ URL::route('bot.mail.store', $connect_page_id) }}';
        var url_update;
        var crt_success;
        var variable_list = <?php echo json_encode($variable_list); ?>;
        $('.mail-create-modal #inputSubject, .mail-create-modal #inputContent, .mail-create-modal #inputTo').textcomplete([
            {
                match: /@(\w*)$/,
                search: function (term, callback) {
                    callback($.map(variable_list, function (element) {
                        return element.indexOf(term) === 0 ? element : null;
                    }));
                },
                index: 1,
                replace: function (element) {
                    return ['\{\{' + element + '}}', ''];
                }
            }
        ], {
            maxCount: 1000
        });
        $('.btn-create-mail').on('click', function () {
            $('.overlay-wrapper .overlay').hide();
            resetCrtModal(true);
            crt_success = false;
            $('.mail-create-modal').modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#datatable_mail').on( 'click','a.btn-mail-edit', function () {
            var mail_id = $(this).data('button');
            var url_get = urlEditMail.replace(':mail_id', mail_id);
            url_update = urlUpdateMail.replace(':mail_id', mail_id);
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
            $(".error-mail-alert").remove();
            $('.overlay-wrapper .overlay').show();

            $.ajax({
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "from_name": $('#inputFromName').val(),
                    // "from_email": $('#inputFromEmail').val(),
                    "to": $('#inputTo').val(),
                    "email_name": $('#inputEmailName').val(),
                    "subject": $('#inputSubject').val(),
                    "content": $('#inputContent').val()
                },
                type: method,
                success: function(data) {
                    $('.overlay-wrapper .overlay').hide();
                    $('.mail-create-modal').modal('hide');
                    setMesssage('{{trans('message.save_success', ['name' => trans('default.mail')])}}', 2);
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
                    if(data != void 0 && data.mail != void 0){
                        setEditModal(data.mail);
                        $('.mail-create-modal').modal({
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
            $(".error-mail-alert").remove();
            $('#inputFromName').val('');
            // $('#inputFromEmail').val('');
            $('#inputTo').val('');
            $('#inputEmailName').val('');
            $('#inputSubject').val('');
            $('#inputContent').val('');
            var modal_title = $(".modal-title-mail");
            if(isCreate){
                $('.btn-modal-create').removeClass('hide');
                $('.btn-modal-update').addClass('hide');
                modal_title.html('{{{ trans('modal.mail_add') }}}');
            }else{
                $('.btn-modal-create').addClass('hide');
                $('.btn-modal-update').removeClass('hide');
                modal_title.html('{{{ trans('modal.mail_edit') }}}');
            }
        }

        function setEditModal(data){
            console.log(data);
            $(".error-mail-alert").remove();
            $("#inputFromName").val(data.from_name);
            // $("#inputFromEmail").val(data.from_email);
            $("#inputTo").val(data.to);
            $('#inputEmailName').val(data.name);
            $('#inputSubject').val(data.subject);
            $('#inputContent').val(data.content);
        }

        function showErrorMsg(data){
            if(data.from_name != void 0){
                setMsg(data.from_name[0], $("#inputFromName").parent());
            }

            // if(data.from_email != void 0){
            //     setMsg(data.from_email[0], $("#inputFromEmail").parent());
            // }

            if(data.to != void 0){
                setMsg(data.to[0], $("#inputTo").parent());
            }

            if (data.email_name != void 0) {
                setMsg(data.email_name[0], $("#inputEmailName").parent());
            }

            if (data.content != void 0) {
                setMsg(data.content[0], $("#inputContent").parent());
            }

            if (data.subject != void 0) {
                setMsg(data.subject[0], $("#inputSubject").parent());
            }
        }

        function setMsg(value, addTag){
            var msg = $("#crt-error").clone();
            msg.attr("id","");
            msg.addClass("error-mail-alert");
            msg.removeClass('hide');
            msg.html(value);
            msg.appendTo(addTag);
        }

        global_datatable = $('#datatable_mail').DataTable({
            info: false,
            processing: false,
            serverSide: true,
            ajax: {
                type: 'POST',
                url :'{!! route('bot.mail.list', $connect_page_id) !!}',
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
                {data: 'name', name: 'name'},
                {data: 'from_name', name: 'from_name'},
                // {data: 'from_email', name: 'from_email'},
                {data: 'to', name: 'to'},
                {data: 'subject', name: 'subject'},
                {data: 'content', name: 'content'},
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
    })
</script>