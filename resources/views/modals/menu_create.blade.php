<div class="modal menu-create-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.menu_add') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="row menu-content">
                    <div class="col-md-12 cmxform form-horizontal">
                        <div class="form-group">
                            <div class="col-md-12">
                                <div id="error-common"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('title', trans('field.title'), ['class' => "col-md-3 control-label required"]) !!}
                            <div class="col-md-8">
                                {!! Form::hidden('id', null, ['id' => 'inputId','class' => 'form-control']) !!}
                                {!! Form::text('title', null, ['id' => 'inputTitle','class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('type', trans('field.type'), ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8 icheck">
                                <div class="minimal-blue single-row" >
                                    @foreach(config('constants.persistent_menu_type') as $code => $val)
                                        <label class="select-type">
                                            <div class="radio ">
                                                {{ Form::radio('type', $val, $code == 'url' ? true : false) }}
                                                {{{ trans('field.'.$code) }}}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group group-url">
                            {!! Form::label('url', trans('field.url'), ['class' => 'col-md-3 control-label required']) !!}
                            <div class="col-md-8">
                                {!! Form::text('url', null, ['id' => 'inputUrl','class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group group-scenario">
                            {!! Form::label('scenario', trans('field.scenario_select'), ['class' => 'col-md-3 control-label required']) !!}
                            <div class="col-md-8">
                                @if(count($scenario_list) > 0)
                                    {!! Form::select('scenario_id', $scenario_list, null, ['id' => 'inputScenario','class' => 'form-control']) !!}
                                @else
                                    <label class="control-label">{{{ trans('message.not_record') }}}</label><br/>
                                @endif
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
                <button type="button" class="btn btn-info btn-modal-create">{{{ trans('button.add')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<label class="error" id="crt-error"></label>
<script>
    var id = '';
    var parent_id = '';
    var scenario_list = [];
    var level = null;
    var menu_item = '';
    var url_scenario_edit = '{{URL::route("bot.scenario.edit", [Route::current()->getParameter('bot'), ":id"]) }}';
    @foreach($scenario_list as $index => $value)
        scenario_list['{{$index}}'] = '{{$value}}';
    @endforeach
@if($connect_page)
    $(function () {
        showType();

        //Create
        var urlCreate = '{{ URL::route('bot.menu.store', $connect_page->id) }}';
        var urlUpdate = '{{ URL::route("bot.menu.update", [$connect_page->id, ':id']) }}';
        var urlUpdate2 = '';
        var change_action_menu = '';
        var sns_type = '{{ $connect_page->sns_type }}';

        $('.btn-change-action-menu').on('click', function () {
            change_action_menu = $(this).attr('data-change-type');
            var panel_current = $(this).parents('.panel');
            level = panel_current.attr('data-level');
            parent_id = panel_current.attr('data-parent-id');
            $(".error-menu-alert").remove();
            setMesssage('');

            var menu_level_limit = parseInt('{{ config('constants.persistent_menu_level_limit.facebook') }}');
            menu_level_limit = menu_level_limit - 1;
            //hide submenu if exist 2 level menu
            var sub_menu_option = $('input[name="type"]:eq(2)').parents('label');
            if(level < menu_level_limit) {
                sub_menu_option.show();
            }else{
                sub_menu_option.hide();
            }
            $('.btn-modal-reset').show();
            if(change_action_menu == 1){
                // create
                $('.menu-create-modal .overlay-wrapper .overlay').hide();
                $('.btn-modal-create').html('{{ trans('button.save')}}').show();
                $('.modal-title').html('{{ trans('modal.menu_add')}}');
                //clear data
                $('input[name=id]').val('');
                $('input[name=title]').val('');
                $('input[name="type"]:eq(0)').iCheck('check');
                $('input[name=url]').val('');
                $('select[name="scenario_id"] option:eq(0)').prop('selected', true);
            }else{
                //update
                menu_item = $(this).parents('.li-menu');
                var menu_id = menu_item.attr('data-menu-id');
                urlUpdate2 = urlUpdate.replace(':id', menu_id);
                $('.menu-create-modal .overlay-wrapper .overlay').show();
                $('.modal-title').html('{{ trans('modal.menu_edit')}}');
                $('.btn-modal-create').html('{{ trans('button.save')}}').show();
                $.ajax({
                    url: urlUpdate2,
                    type: 'GET',
                    success: function(data) {
                        var menuData = data.data;
                        if(menuData !=void 0){
                            $('input[name=id]').val(menuData._id);
                            $('input[name=title]').val(menuData.title);
                            $('input[name="type"][value="'+menuData.type+'"]').iCheck('check');
                            $('input[name=url]').val(menuData.url);
                            var scenario_id     = menuData.scenario_id.split('_').shift();
                            $('select[name="scenario_id"] option[value="'+scenario_id+'"]').prop('selected', true);
                        }
                        $('.menu-create-modal .overlay-wrapper .overlay').hide();
                    },
                    error: function(result){
                        $('.menu-create-modal .overlay-wrapper .overlay').hide();
                        var errors = $.parseJSON(result.responseText);
                        if(errors.errors != void 0 && errors.errors.msg != void 0){
                            setMesssage(errors.errors.msg);
                        }
                        $('.menu-create-modal').modal('hide');
                    }
                });
            }
            $('.menu-create-modal').modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        //store or update by ajax
        $('.btn-modal-create').on('click', function(evt) {
            $(".error-menu-alert").remove();
            $('.menu-create-modal .overlay-wrapper .overlay').show();
            var menu_id     = $('input[name=id]').val(),
                actionUrl   = urlCreate,
                method      = 'POST';
            //action update
            if(menu_id != '' && menu_id != void 0) {
                actionUrl   = urlUpdate2;
                method      = 'PUT';
            }
            var data_send = {
                '_token':   "{{ csrf_token() }}",
                'id':       menu_id,
                'parent_id': parent_id,
                'title':    $('input[name=title]').val(),
                'type':     $("input[name=type]:checked").val(),
                'url':      $('input[name=url]').val(),
                'scenario_id': $('select[name=scenario_id]').val(),
            };
            $.ajax({
                url: actionUrl,
                data: data_send,
                type: method,
                success: function(result) {
                    $('.menu-create-modal .overlay-wrapper .overlay').hide();
                    var data = result.data;
                    var message = result.msg ? result.msg : '';
                    if(data != void 0) {
                        if (menu_id != '' && menu_id != void 0) {
                            //update
                            setData(menu_item, data);
                            if(level == 0 && menu_item.hasClass('active')){
                                if(data.type == '{{config('constants.persistent_menu_type.submenu')}}'){
                                    $('.menu-sub .menu-heading').html(data.title);
                                }else{
                                    $('.menu-sub, .menu-sub-sub').hide();
                                    $('.main-menu .li-menu').removeClass('active');
                                }
                            }else if(level == 1 && menu_item.hasClass('active') ){
                                if(data.type == '{{config('constants.persistent_menu_type.submenu')}}'){
                                    $('.menu-sub-sub .menu-heading').html(data.title);
                                }else{
                                    $('.menu-sub .li-menu').removeClass('active');
                                    $('.menu-sub-sub').hide();
                                }
                            }
                        } else {
                            // store
                            var item = $('.add-menu-item .li-menu').clone(true);
                            setData(item, data);
                            $('.btn-create-' + level).before(item);
                            var menu_item_count = getMenuItemCount(level);

                            @if($connect_page->sns_type == config('constants.group_type_service.facebook'))
                                if((level == 0 && menu_item_count >= '{{ config('constants.persistent_menu_limit.facebook') }}') || (level > 0 && menu_item_count == '{{ config('constants.persistent_submenu_limit.facebook') }}')){
                                    $(".btn-create-"+level).hide()
                                }
                            @elseif($connect_page->sns_type == config('constants.group_type_service.web_embed'))
                                if((level == 0 && menu_item_count >= '{{ config('constants.persistent_menu_limit.web_embed') }}') || (level > 0 && menu_item_count == '{{ config('constants.persistent_submenu_limit.web_embed') }}')){
                                    $(".btn-create-"+level).hide()
                                }
                            @endif

                            $('.menu_not_create').hide();
                            message = '{{trans('message.save_success', ['name' => trans('modal.persistent_menu')])}}'
                        }
                        $('.menu-create-modal').modal('hide');
                        setMesssage(message, 2)
                        $('.persistent_menu_demo').show();
                        $('.apply-menu').removeClass('disabled');
                    }
                },
                error: function(result){
                    $('.menu-create-modal .overlay-wrapper .overlay').hide();
                    $('.menu-create-modal .menu-success').hide();
                    var errors = $.parseJSON(result.responseText);
                    showErrorMsg(errors);
                }
            });
        });
    });
    @endif
    //change options when select type
    $("input[name=type]").on('ifChanged', function(event){
        showType();
    });
    function showType() {
        var type = $("input[name=type]:checked").val();
        if(type == '{{{ config('constants.persistent_menu_type.url') }}}') {
            $('.group-url').show();
            $('.group-scenario').hide();
        }else if(type == '{{{ config('constants.persistent_menu_type.scenario_select') }}}') {
            $('.group-url').hide();
            $('.group-scenario').show();
        }else{
            $('.group-url').hide();
            $('.group-scenario').hide();
        }
    }
    function setData(item, data) {
        item.find('.menu-item-title').html(data.title);
        var type_content = $('.add-menu-item .type-content').clone();
        if(data.type == '{{config('constants.persistent_menu_type.url')}}'){
            type_content.find('.type-select-name').html('{{{ trans('field.url') }}}' + ': ');
            type_content.find('.type-link').attr('href', data.url).html(data.url);
            item.find('.menu-item-type').html(type_content.html());
            item.find('.btn-submenu').css('visibility', 'hidden');
        }else if(data.type == '{{config('constants.persistent_menu_type.scenario_select')}}'){
            var scenario_id     = data.scenario_id.split('_').shift();
            var _scenario_name = scenario_list[scenario_id] != void 0 ? scenario_list[scenario_id] : '';
            var url = url_scenario_edit.replace(':id', scenario_id);
            type_content.find('.type-select-name').html('{{{ trans('field.scenario_select') }}}' + ': ');
            type_content.find('.type-link').attr('href', url).html(_scenario_name);
            item.find('.menu-item-type').html(type_content.html());
            item.find('.btn-submenu').css('visibility', 'hidden');
        }else{
            item.find('.menu-item-type').html('<br/>');
            item.find('.btn-submenu').css('visibility', 'visible');
        }
        item.attr({
                'data-menu-id'          : data._id,
                'data-priority-order'   : data.priority_order,
                'data-menu-type'        : data.type,
            }
        );
        var url_delete = '{{ URL::route("bot.menu.destroy", [Route::current()->getParameter('bot'), ":id"]) }}';
        item.find('.btn-delete').attr({
            'data-from' : url_delete
        });
    }
    function getMenuItemCount(level) {
        var count = 0;
        if(level ==0){
            count = $('.main-menu .li-menu').length;
        }else if(level ==1){
            count = $('.menu-sub .li-menu').length;
        }else{
            count = $('.menu-sub-sub .li-menu').length;
        }
        return count;
    }

    function showErrorMsg(data){
        if(data.title != void 0){
            setMsg(data.title[0], $("#inputTitle").parent());
        }

        if (data.url != void 0) {
            setMsg(data.url[0], $("#inputUrl").parent());
        }

        if (data.errors != void 0 && data.errors.msg_url != void 0) {
            setMsg(data.errors.msg_url, $("#inputUrl").parent());
        }

        if (data.scenario != void 0) {
            setMsg(data.scenario[0], $("#inputScenario").parent());
        }

        if (data.errors != void 0 && data.errors.msg_scenario != void 0) {
            setMsg(data.errors.msg_scenario, $("#inputScenario").parent());
        }

        if (data.type != void 0) {
            setMsg(data.type[0], $(".select-type").parent());
        }

        if (data.errors != void 0 && data.errors.msg != void 0) {
            setMsg(data.errors.msg, $("#error-common").parent());
        }
    }

    function setMsg(value, addTag){
        var msg = $("#crt-error").clone().show();
        msg.attr("id","");
        msg.addClass("error-menu-alert");
        msg.removeClass('hide');
        msg.html(value);
        msg.appendTo(addTag);
    }
</script>