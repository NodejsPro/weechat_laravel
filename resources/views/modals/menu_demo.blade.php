<div class="modal menu-demo-modal" id="menu-demo-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper type_{{ config('constants.group_type_service_key.'.$connect_page->sns_type) }}" >
            <div class="modal-body">
                <div class="row menu-content">
                    <div class="device_demo"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="menu-list" id="menu-list">
                    <div class="menu-title text-change text-center" id="menu-header">
                        <span class="np-btn btn-action-menu btn-prev-menu pull-left" id="btn-prev-menu" href="#" ><i class="fa fa-angle-left pagination-left"></i></span>
                        <div class="title text-center" id="menu-header-text">{{trans('modal.persistent_menu_title')}}</div>
                    </div>
                    <div class="menu-item-content" id="menu-item-content"></div>
                </div>
                @if($connect_page->sns_type == config('constants.group_type_service.web_embed'))
                    <img class="menu_arrow" src="/images/menu_arrow_down.png">
                @endif
            </div>
        </div>
        <div class="template" style="display: none">
            <div class="menu-demo-add">
                <div class="menu-add-content">
                    <div class="persistent-menu-item text-change" menu_id="">
                        <a class="menu-item-link" href="#">
                            <span class="submenu-title"></span>
                            <span class="np-btn btn-action-menu btn-next-menu pull-right" href="#"><i class="fa fa-angle-right pagination-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $(function () {
        var menu_data = [];
        var parent_id = null, current_id = null;
        $('.persistent_menu_demo').click(function () {
            parent_id = null;
            current_id = null;
            $('#menu-item-content').html('');
            $('#btn-prev-menu').hide();
            $('#menu-header-text').html('{{trans('modal.persistent_menu_title')}}');
            $.ajax({
                url: '{{route('bot.menu.demo', Route::current()->getParameter('bot'))}}',
                data: {
                    '_token':   "{{ csrf_token() }}",
                },
                type: 'POST',
                success: function(result) {
                    menu_data = result.data, success = result.success;
                    if(success != void 0 && menu_data != void 0 && success){
                        generateDataDemo(menu_data);
                    }
                },
                error: function(result){
                    $('.overlay-wrapper .overlay').hide();
                    $('.menu-create-modal .menu-success').hide();
                    var errors = $.parseJSON(result.responseText);
                    //save common error
                    if(errors.errors != void 0 && errors.errors.msg != void 0) {
                    }
                }
            });
            $('.menu-demo-modal').modal({
                keyboard: false,
            });
        });
        $('.menu-demo-modal .persistent-menu-item').on('click', function () {
            var click_menu_id = $(this).attr('menu_id');
            if (click_menu_id) {
                $('#menu-item-content').html('');
                $('#btn-prev-menu').show();
                if (current_id) {
                    $.each(menu_data, function (index, data) {
                        if (data.id == current_id) {
                            $.each(data.submenu, function (index, data) {
                                if (data.id == click_menu_id) {
                                    $('#menu-header-text').html(data.title);
                                    parent_id = current_id;
                                    current_id = click_menu_id;
                                    generateDataDemo(data.submenu);
                                    return false;
                                }
                            });
                            return false;
                        }
                    })
                } else {
                    $.each(menu_data, function (index, data) {
                        if (data.id == click_menu_id) {
                            $('#menu-header-text').html(data.title);
                            parent_id = current_id;
                            current_id = click_menu_id;
                            generateDataDemo(data.submenu);
                            return false;
                        }
                    })
                }
            }
        });
        $('#menu-header').on('click', function (){
            if(parent_id){
                $.each(menu_data, function (index, data) {
                    if (data.id == parent_id) {
                        $('#menu-header-text').html(data.title);
                        current_id = parent_id;
                        parent_id = null;
                        generateDataDemo(data.submenu);
                        return false;
                    }
                })
            }else{
                parent_id = null;
                current_id = null;
                $('#menu-header-text').html('{{trans('modal.persistent_menu_title')}}');
                $('#btn-prev-menu').hide();
                generateDataDemo(menu_data);
            }
        });

        function generateDataDemo(data) {
            var menu_item_content = $('#menu-item-content').html('');
            if(current_id){
                $('#btn-prev-menu').show();
                $('#menu-header').addClass('submenu-cursor');
            }else{
                $('#btn-prev-menu').hide();
                $('#menu-header').removeClass('submenu-cursor');
            }
            $.each(data, function (index, value) {
                var menu_item_add = $('.template .menu-add-content').clone(true);
                var btn_submenu = menu_item_add.find('.btn-next-menu');
                var menu_item = menu_item_add.find('.persistent-menu-item');
                menu_item_add.find('.submenu-title').html(value.title);
                menu_item.attr({
                    'menu_id' : ''
                });
                btn_submenu.hide();
                if(value.submenu != void 0){
                    menu_item.attr({
                        'menu_id' : value.id,
                    });
                    btn_submenu.show();
                }
                menu_item_content.append(menu_item_add);
            })
        }
    });
</script>
