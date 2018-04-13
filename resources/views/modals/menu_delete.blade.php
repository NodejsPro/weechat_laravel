<div class="modal delete-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('default.confirm')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="row delete-content">
                    <div class="col-md-12">
                        {{{ trans("message.model_delete_confirm") }}}
                    </div>
                </div>
                <div class="row delete-success">
                    <div class="col-md-12">
                        <p>
                            {{{ trans("message.modal_delete_success") }}}
                        </p>
                    </div>
                </div>
                <div class="row delete-error">
                    <div class="col-md-12">
                        <p class="text-red">
                            {{{ trans("message.common_error") }}}
                        </p>
                    </div>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-danger btn-modal-delete">{{{ trans('button.delete')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@section('scripts')
    <script>
        var id = '';
        var menu_item = '';
        var level = '';
        $(function () {
            var deleteUrl = '';
            $('.btn-delete').click(function(){
                $('.delete-modal .modal-title').html('{{trans('default.confirm')}}')
                deleteUrl = $(this).data('from');
                $('.btn-modal-delete').show();
                $('.overlay-wrapper .overlay').hide();
                $('.delete-modal .delete-content').show();
                $('.delete-modal .delete-error').hide();
                $('.delete-modal .delete-success').hide();
                menu_item = $(this).parents('.li-menu');
                id = menu_item.attr('data-menu-id');
                level = $(this).parents('.panel').attr('data-level');
                $('.delete-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });

            $('.btn-modal-delete').on('click', function(evt) {
                $('.overlay-wrapper .overlay').show();
                $('.btn-modal-delete').hide();

                deleteUrl = deleteUrl.replace(':id',id);
                $.ajax({
                    url: deleteUrl,
                    data: { "_token": "{{ csrf_token() }}" },
                    type: 'DELETE',
                    success: function(data) {
                        var data = data.data;
                        $('.apply-menu').removeClass('disabled');
                        $('.overlay-wrapper .overlay').hide();
                        $('.delete-modal .delete-content').hide();
                        $('.delete-modal .delete-error').hide();
                        $('.delete-modal .delete-success').show();
                        if(data != void 0){
                            $('.btn-create-' + level).show();
                            if(menu_item.hasClass('active')){
                                if(level == 0){
                                    $('.menu-sub, .menu-sub-sub').hide();
                                }else if(level == 1){
                                    $('.menu-sub-sub').hide();
                                }
                            }
                            menu_item.remove();
                            setMesssage('{{trans('message.delete_success', ['name' => trans('default.menu_item')])}}', 2);
                        }else{
                            setMesssage('{{trans('message.delete_error', ['name' => trans('default.menu_item')])}}');
                        }
                        $('.delete-modal').modal('hide');
                    },
                    error: function(result){
                        var text = $.parseJSON(result.responseText);
                        $('.overlay-wrapper .overlay').hide();
                        setMesssage(text.errors.msg);
                        $('.delete-modal').modal('hide');
                    }
                });
            });
        });
    </script>
@endsection