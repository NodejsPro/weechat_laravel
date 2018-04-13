<div id="scenario-group-create-modal" class="modal scenario-group-create fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper ">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.scenario_group_add') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="box_message"></div>
                {!! Form::open(['route' => ['bot.scenarioGroup.store', Route::current()->getParameter('bot')], 'method' => 'POST', 'class' => 'cmxform form-horizontal', 'role' => 'form']) !!}
                <div class="cmxform form-horizontal">
                    <div class="form-group">
                        {!! Form::label('scenario_group_name', trans('field.scenario_group_name'), ['class' => "col-md-3 control-label required"]) !!}
                        <div class="col-md-7">
                            {!! Form::text('scenario_group_name', null, ['id' => 'scenario_group_name','class' => 'form-control']) !!}
                            <label for="inputName" class="error scenario-group-name-error"></label>
                        </div>
                    </div>
                </div>
                {!! Form::close()!!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-modal-close pull-left" data-dismiss="modal">{{{ trans('button.close') }}}</button>
                <button class="btn btn-info btn-modal-scenario-group-create">{{{ trans('button.save') }}}</button>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
        </div>
    </div>
</div>
<label class="error" id="crt-error"></label>
<script type="text/javascript">
    $(document).ready(function () {
        $('.btn-scenario-group-create').on('click', function () {
            $('.overlay-wrapper .overlay').hide();
            $('#scenario-group-create-modal .box_message, label.error').html('');
            $('#scenario-group-create-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        //submit ajax url
        var url = '{{route('bot.scenarioGroup.store', Route::current()->getParameter('bot'))}}';
        $('.btn-modal-scenario-group-create').on('click', function () {

            $('.overlay-wrapper .overlay').show();
            var data = {
                '_token' : '{{csrf_token()}}',
                name : $(this).parents('#scenario-group-create-modal').find('#scenario_group_name').val()
            };
            console.log(data);
            $.ajax({
                url: url,
                data: data,
                type: "POST",
                success: function(data) {
                    $('#scenario-group-create-modal').modal('hide');
                    location.reload();
                },
                error: function(result){
                    $('.overlay-wrapper .overlay').hide();
                    var errors = $.parseJSON(result.responseText);
                    if(errors.name != void 0 && errors.name != '') {
                        $('#scenario-group-create-modal .scenario-group-name-error').show().html(errors.name);
                    } else if(errors.errors != void 0 && errors.errors != '') {
                        setMesssage(errors.errors, 1, $('#scenario-group-create-modal .box_message'));
                    }
                }
            });
        });
        //update group name
        $('.bot-name-label').on('click', function () {
            $('p.alert-success, p.alert-danger').remove();
            $('.bot-action').hide();
            $('.bot-name-label').show();
            $(this).parent('.bot-item-content').find('.bot-action').show();
            $(this).css('display', 'none');
            var parent = $(this).parents('.bot-item-content');
            parent.find('.scenario_group_name').removeClass('hidden');
            parent.find('.text-input-content').removeClass('hidden').show();
            parent.find('.icon-content').removeClass('fa-icon-hidden').addClass('fa-icon-show').show();
        });
        $(document).on('click', function (e) {
            if (!$(e.target).parents('.bot-item-content').length) {
                $('.bot-action').hide();
                $('.bot-name-label').show();
            }
        });
        $('.bot-action-submit').on('click', function () {
            setMesssage('');
            var data = {
                '_token' : '{{csrf_token()}}',
                name : $(this).parents('.bot-item-content').find('.scenario_group_name').val()
            };
            var group_id =  $(this).data('group-id');
            var url = '{{route('bot.scenarioGroup.update', ['bot' => Route::current()->getParameter('bot'), 'scenarioGroup' => ':group_id'])}}';
            url = url.replace(':group_id', group_id);
            var pannel_parent = $(this).parents('.pannel-content');
            var text_content = $(this).parents('.bot-item-content');
            var text_name = text_content.find('.scenario_group_name').val();
            var text_name_old = text_content.find('.bot-name-label').html();
            text_content.find('.bot-name-label').show().html(text_name);
            text_content.find('.text-input-content').hide();
            text_content.find('.icon-content').removeClass('fa-icon-show').addClass('fa-icon-hidden');

            $.ajax({
                url: url,
                data: data,
                type: 'PUT',
                success: function(data) {
                    if(data.success) {
                        var message_success = '{{trans('message.update_success', ['name' => ':bot_name'])}}';
                        message_success = message_success.replace(':bot_name', text_name);
                        pannel_parent.find('.btn-transfer-bot').attr('data-page_name', text_name);
                        setMesssage(message_success, 2);
                    }else{
                        text_content.find('.bot-name-label').html(text_name_old);
                        var message_error = '{{trans('message.update_error', ['name' => ':bot_name'])}}';
                        message_error = message_error.replace(':bot_name', text_name);
                        setMesssage(message_error);
                    }
                },
                error: function(result){
                    text_content.find('.bot-name-label').html(text_name_old);
                    text_content.find('.scenario_group_name').val(text_name_old);
                    var errors = $.parseJSON(result.responseText);
                    $.each(errors, function(index, value) {
                        setMesssage(value);
                    });
                }
            })
        });
        $('.bot-action-close').on('click', function () {
            var parent = $(this).parents('.bot-item-content');
            parent.find('.scenario_group_name').val(parent.find('.bot-name-label').html());
            parent.find('.bot-name-label').show();
            parent.find('.text-input-content').hide();
            parent.find('.icon-content').removeClass('fa-icon-show').addClass('fa-icon-hidden');
        });
        // show/hide scenario list by group
        $('.tab_content .fa-chevron-down').on('click', function (ev) {
            var tab_title = $(this).data('title');
            var parent = $(this).parents('.tab_content');

            parent.find('.panel-body').toggle('blind', 200);
            if(parent.hasClass('tab_close')) {
                parent.addClass('tab_open').removeClass('tab_close');
                parent.find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                if(tab_title != void 0 && tab_title != '') {
                    window.location.hash = tab_title;
                }
            } else {
                parent.addClass('tab_close').removeClass('tab_open');
                parent.find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });
    });
</script>