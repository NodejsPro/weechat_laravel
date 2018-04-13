<div class="modal delete-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper overlay-wrapper-delete">
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
        var global_datatable;
        var id = '';
        var delete_datatable_msg_elm = '';
        $(function () {
            var deleteUrl = '';
            $('#main-content, #main-content-1').on( 'click','.btn-delete', function () {
                deleteUrl = $(this).data('from');
                $('.overlay-wrapper-delete .overlay').hide();
                id = $(this).data('button');
                $('.delete-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });

            $('.btn-modal-delete').on('click', function(evt) {
                $('.overlay-wrapper-delete .overlay').show();
                deleteUrl = deleteUrl.replace(':id',id);
                $.ajax({
                    url: deleteUrl,
                    data: { "_token": "{{ csrf_token() }}" },
                    type: 'DELETE',
                    success: function(data) {
                        //push event delete success
                        $(document).trigger('delete_success');

                        $('.overlay-wrapper-delete .overlay').hide();
                        $('.delete-modal').modal('hide');
                        setMesssage('{{trans('message.modal_delete_success')}}', 2, delete_datatable_msg_elm);
                        if(data.status_add_app != void 0 && data.status_add_app){
                            $('.nlp_index .btn-create-nlp').show();
                        }
                        global_datatable.ajax.reload(function (data) {
                            //update variable option in slot when change action variable
                            if('{{Request::is('bot/*/variable') || Request::is('bot/*/variable/*')}}') {
                                updateSlotVariable(data.data);
                            }
                        }, false);
                    },
                    error: function(result){
                        //push event delete error
                        $(document).trigger('delete_error');

                        var text = $.parseJSON(result.responseText);
                        $('.overlay-wrapper-delete .overlay').hide();
                        $('.delete-modal').modal('hide');
                        setMesssage('{{trans('message.common_error')}}', 1, delete_datatable_msg_elm);
                    }
                });
            });
        });

        //VARIABLE PAGE: update variable option in slot when change action variable
        function updateSlotVariable(data) {
            var select_item_variable = $('#slot_create_modal .slot_origin_block select.item_variable');
            select_item_variable.find('option').remove();
            $(data).each(function (i, e) {
                select_item_variable.append('<option value="'+ e['id'] +'">' + e['variable_name'] + '</option>');
            });
        }
    </script>
@endsection