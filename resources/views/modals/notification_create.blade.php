<div class="modal notification-create-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title modal-title-notification">{{{ trans('modal.notification_add') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="box_message"></div>
                <form class="form-horizontal cmxform notification_create_form" role="form">
                    <div class="form-group">
                        {!! Form::label('notification_name', trans('notification.name'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-6">
                            {!! Form::text('notification_name', null, ['id' => 'inputNotificationName','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('repeat', trans('notification.repeats'), ['class' => 'col-md-3 control-label required']) !!}
                        <div class="col-md-6">
                            {!! Form::select('repeat', $type_repeat, null, ['id' => 'inputRepeat','class' => 'type_repeat form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group repeat_every" style="display: {{(@$notification->repeat == $daily || old('repeat') == $daily) ? 'block' : 'none'}};">
                        {!! Form::label('repeatEvery', trans('notification.repeat_every'), ['class' => 'col-md-3 control-label required']) !!}
                        <div class="col-md-3">
                            {!! Form::select('repeat_every', $repeat_every, null, ['id' => 'inputRepeatEvery','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group repeat_on" style="display: {{(@$notification->repeat == $weekly || old('repeat') == $weekly) ? 'block' : 'none'}};;">
                        {!! Form::label('repeatOn', trans('notification.repeat_on'), ['class' => 'col-md-3 control-label required']) !!}
                        @if(count($repeat_on) > 0)
                            <div class="col-md-9">
                                @foreach($repeat_on as $key => $item)
                                    <div class="minimal-blue single-row">
                                        <div class="checkbox">
                                            <input type="checkbox" name="repeat_on[]" class="repeat_on" id="" value="{{$key}}">
                                            <label>{{$item}}</label>
                                        </div>
                                    </div>
                                @endforeach
                                <br>
                            </div>
                            <div class="col-md-8 col-md-offset-3" id="inputRepeatOn">
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        {!! Form::label('time', trans('notification.start_on'), ['class' => 'col-md-3 control-label required']) !!}
                        <div class="col-md-8">
                            {!! Form::text('time', null, ['id' => 'inputTime','class' => 'form-control datetimepicker']) !!}
                            <label for="inputTime" class="error time-error"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('scenario', trans('notification.scenario'), ['class' => 'col-md-3 control-label required']) !!}
                        <div class="col-md-8">
                            @if(count($scenario_bot_start_list) > 0)
                                {!! Form::select('scenario', $scenario_bot_start_list, null, ['id' => 'inputScenario','class' => 'form-control']) !!}
                            @else
                                <label class="control-label">{{{ trans('message.not_record') }}}</label>
                            @endif
                            <label class="control-label" style="font-size: 13px;">{{{ trans('notification.sellect_scenario') }}}</label><br/>
                        </div>
                    </div>
                    <hr/>
                    {{--start filter area--}}
                    <div class="form-group filter_status">
                        {!! Form::label('', trans('notification.mss_total_user', ['number' => $number_user]), ['class' => "col-md-offset-1 control-label", 'style' => 'text-align: left;']) !!}
                    </div>
                    <div class="form-group number_user">
                         <label class="col-md-offset-1 control-label"></label>
                         <p class="hidden">{{trans('notification.mss_number_user')}}</p>
                    </div>
                    <div class="filter_contain"></div>

                    <div class="form-group">
                        <div align="right" class="col-md-offset-8 col-md-2">
                            <button id="btn_filter_add" type="button" class="btn btn-info">{{trans('button.add_filter')}}</button>
                        </div>
                    </div>
                    {{--end filter item--}}
                </form>
            </div>

            {{--element template--}}
            <div class="hidden element_template">
                {{--filter item--}}
                <div class="template_filter">
                    {{----}}
                    {!! Form::text('', URL::route('bot.notification.filter', $connect_page_id), ['class' => "number_user_filter_url"]) !!}
                    {!! Form::text('', URL::route('bot.scenario.getOptionFilterByVariable', $connect_page_id), ['class' => "filter_variable_data_url"]) !!}
                    {!! Form::text('', csrf_token(), ['class' => "csrf_token"]) !!}
                    <div class="form-group col-md-8">
                        <label>Có n user thỏa mãn điều kiện</label>
                    </div>
                    <div class="col-md-12 filter_item">
                        <div class="form-group col-md-4">
                            {!! Form::select('', $filter_variable_list, null, ['class' => 'form-control filter_variable']) !!}
                        </div>
                        <div class="form-group col-md-3">
                            {!! Form::select('', [], null, ['class' => 'form-control filter_operator']) !!}
                        </div>
                        <div class="form-group col-md-4 filter_value_box">
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
                        <div class="form-group col-md-1">
                            <button type="button" class="btn btn-danger filter_btn_delete">{{trans('button.delete')}}</button>
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
                <button type="button" class="btn btn-info btn-modal-update hide">{{{ trans('button.save')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<label class="error" id="crt-error"></label>

<script>
    $(function () {
        var urlEditNotification = '{{ URL::route('bot.notification.edit', ['bot'=>$connect_page_id, 'notification'=>':notification_id']) }}';
        var urlUpdateNotification = '{{ URL::route('bot.notification.update', ['bot'=>$connect_page_id, 'notification'=>':notification_id']) }}';
        var url_create = '{{ URL::route('bot.notification.store', $connect_page_id) }}';
        var url_update;
        var crt_success;
        $('.datetimepicker').datetimepicker({
            sideBySide: true,
            format: '{{$date_format_js}} HH:mm:00',
            stepping: 5
        });

        $('#inputRepeatEvery').select2({
            minimumResultsForSearch: -1
        });

        $('#inputRepeat').select2({
            minimumResultsForSearch: -1
        }).on("change", function(e) {
            var  value = $(this).val();

            var  daily = '{{config('constants.group_type_notify.daily')}}';
            var  weekly = '{{config('constants.group_type_notify.weekly')}}';
            if(value == daily){
                $('.repeat_every').css("display","block");
                $('.repeat_on').css("display","none");
                $('#inputRepeatEvery').val("0").trigger('change');
            }else if(value == weekly){
                $('.repeat_on').css("display","block");
                $('.repeat_every').css("display","none");
                $('.repeat_on').iCheck('uncheck');
            }else{
                $('.repeat_every').css("display","none");
                $('.repeat_on').css("display","none");
            }
        });

        $('.btn-create-notification').click(function(){
            $('.overlay-wrapper .overlay').hide();
            resetCrtModal(true);
            crt_success = false;
            $('.notification-create-modal').modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#datatable_notification').on( 'click','a.btn-notification-edit', function () {
            var notification_id = $(this).data('button');
            var url_get = urlEditNotification.replace(':notification_id', notification_id);
            url_update = urlUpdateNotification.replace(':notification_id', notification_id);
            resetCrtModal(false);
            getEditData(url_get);
        });

        $('.btn-modal-create').on('click', function(evt) {
            sendCrtData(true, url_create);
        });

        $('.btn-modal-update').click(function () {
            sendCrtData(false, url_update);
        });

        function sendCrtData(isCreate, url){
            var method = "POST";
            if(!isCreate){
                method = "PUT";
            }
            $(".error-notification-alert").remove();
            $('.overlay-wrapper .overlay').show();
            var repeat_on = $(".repeat_on:checked").map(function(){
                return $(this).val();
            }).toArray();

            $.ajax({
                url: url,
                data: $('.notification_create_form').serialize(),
                type: method,
                success: function(data) {
                    $('.notification-create-modal').modal('hide');
                    setMesssage('{{trans('message.create_notify_success')}}', 2);
                    global_datatable.ajax.reload(null, false);
                },
                error: function(result){
                    var data = $.parseJSON(result.responseText);
                    showErrorMsg(data);
                },
                complete: function () {
                    $('.overlay-wrapper .overlay').hide();
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
                beforeSend: function () {
                    $('.overlay-wrapper .overlay').show();
                    $('.notification-create-modal').modal({
                        backdrop: 'static',
                        keyboard: false
                    })
                },
                success: function(data) {
                    if(data != void 0 && data.notification != void 0){
                        setEditModal(data.notification);
                        if (data.notification.filter != undefined && data.notification.filter.length > 0){
                            getNumberUser();
                        }
                    }
                },
                error: function(result){

                },
                complete: function () {
                    $('.overlay-wrapper .overlay').hide();
                }
            });
        }

        function resetCrtModal(isCreate){
            $(".error-notification-alert").remove();
            $('#inputNotificationName').val('');
            $('#inputTime').val('');
            $('.notification-create-modal .box_message').html('');
            $('#inputRepeatEvery').val(0).trigger('change');
            $('.repeat_on').iCheck('uncheck');
            var modal_title = $(".modal-title-notification");
            if(isCreate){
                $('.btn-modal-create').removeClass('hide');
                $('.btn-modal-update').addClass('hide');
                modal_title.html('{{{ trans('modal.notification_add') }}}');
            }else{
                $('.btn-modal-create').addClass('hide');
                $('.btn-modal-update').removeClass('hide');
                modal_title.html('{{{ trans('modal.notification_edit') }}}');
            }
            $("#inputRepeat").val('{{config('constants.group_type_notify.one_time')}}').trigger('change');

            //clear filter
            filterClear();
        }

        function setEditModal(data){
            $(".error-notification-alert").remove();
            $("#inputRepeat").val(data.repeat).trigger('change');
            $('#inputNotificationName').val(data.name);
            $('#inputRepeatEvery').val(data.repeat_every - 1).trigger('change');
            $('#inputScenario').val(data.scenario_id).trigger('change');
            $('#inputTime').val(data.real_time);

            $.each(data.repeat_on, function (index, value) {
                $('.repeat_on :input[value="'+value+'"]').iCheck('check');
            });

            //fill data filter
            fillFilterToForm(data.filter);
        }

        function showErrorMsg(data){
            if(data.notification_name != void 0){
                setMsg(data.notification_name[0], $("#inputNotificationName").parent());
            }

            if (data.time != void 0) {
                setMsg(data.time[0], $("#inputTime").parent());
            }

            if (data.repeat_on != void 0) {
                setMsg(data.repeat_on[0], $("#inputRepeatOn"));
            }

            if (data.scenario != void 0) {
                setMsg(data.scenario[0], $("#inputScenario").parent());
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

        global_datatable = $('#datatable_notification').DataTable({
            info: false,
            processing: false,
            serverSide: true,
            ajax: {
                type: 'POST',
                url :'{!! route('bot.notification.list', $connect_page_id) !!}',
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
                {data: 'repeat', name: 'repeat'},
                {data: 'start_time', name: 'start_time'},
                {data: 'scenario_name', name: 'scenario_name'},
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
            destroy: true,
            "fnDrawCallback": function(oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }else{
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                }
            }
        });

        $('#datatable_notification').on( 'click','a.notification_on_off', function () {
            notification_id     = $(this).data('button');
            notification_status = $(this).data('notification_status');
            urlOnOff = '{{ URL::route('bot.notification.active', [$connect_page_id, ':notification_id']) }}';
            urlOnOff = urlOnOff.replace(':notification_id', notification_id);
            $.ajax({
                url: urlOnOff,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "notification_id": notification_id,
                    "data"     : notification_status
                },
                type: 'POST',
                success: function(data) {
                    setMesssage('{{trans('message.create_notify_success')}}', 2);
                    global_datatable.ajax.reload(null, false);
                },
                error: function(result){
                    setMesssage('{{trans('message.common_error')}}', 1);
                    global_datatable.ajax.reload(null, false);
                }
            });
        });
    });
</script>