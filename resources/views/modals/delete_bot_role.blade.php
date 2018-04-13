<div class="modal delete-bot-role-modal">
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
        $(function () {
            var delete_url = '',
                delete_item;
            $(document).on( 'click','.btn-delete', function () {
                delete_url = $(this).data('from');
                delete_item = $(this).parents('.role-item');
                $('.btn-modal-delete').show();
                $('.overlay-wrapper .overlay').hide();
                $('.delete-bot-role-modal .delete-content').show();
                id = $(this).data('button');

                $('.delete-bot-role-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });

            $('.btn-modal-delete').on('click', function(evt) {
                $('.delete-bot-role-modal .overlay-wrapper .overlay').show();
                if(id != void 0 && id){
                    $('.btn-modal-delete').hide();
                    delete_url = delete_url.replace(':id',id);
                    $.ajax({
                        url: delete_url,
                        data: { "_token": "{{ csrf_token() }}" },
                        type: 'DELETE',
                        success: function(data) {
                            $('.delete-bot-role-modal').modal('hide');
                            if(data.success && delete_item.length){
                                delete_item.remove();
                                if(data.status_flg != void 0 && data.status_flg){
                                    $('#role_create').show();
                                }else{
                                    $('#role_create').hide();
                                }
                                setMesssage('{{trans('message.delete_success' ,['name' => trans('default.bot_role')])}}', 2)
                            }
                        },
                        error: function(result){
                            var text = $.parseJSON(result.responseText);
                            $('.overlay-wrapper .overlay').hide();
                            $('.delete-bot-role-modal').modal('hide');
                            if(text != void 0 && text.errors != void 0 && text.errors.msg != void 0){
                                setMesssage(text.errors.msg)
                            }
                        }
                    });
                }else{
                    $('.delete-bot-role-modal .overlay-wrapper .overlay').hide();
                    setMesssage('{{trans('message.common_error' ,['name' => trans('default.bot_role')])}}');
                    $('.delete-bot-role-modal').modal('hide');
                }
            });

        });
    </script>
@endsection