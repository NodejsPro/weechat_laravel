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
        $(function () {
            var deleteUrl = '';
            $('#main-content-1').on( 'click','.btn-delete', function () {
                $('.alert').html('').hide();
                deleteUrl = $(this).data('from');
                $('.overlay-wrapper .overlay').hide();
                id = $(this).data('button');
                $('.delete-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });

            $('.btn-modal-delete').on('click', function(evt) {
                $('.overlay-wrapper .overlay').show();
                deleteUrl = deleteUrl.replace(':id',id);
                $.ajax({
                    url: deleteUrl,
                    data: { "_token": "{{ csrf_token() }}" },
                    type: 'DELETE',
                    success: function(data) {
                        $('.overlay-wrapper .overlay').hide();
                        $('.delete-modal').modal('hide');
                        setMesssage('{{trans('message.modal_delete_success')}}', 2);
                        global_datatable.ajax.reload(null, false);
                    },
                    error: function(result){
                        $('.overlay-wrapper .overlay').hide();
                        $('.delete-modal').modal('hide');
                        var text = $.parseJSON(result.responseText);
                        if(text.errors != void 0 && text.errors.msg != void 0){
                            setMesssage(text.errors.msg, 1);
                        }else{
                            setMesssage('{{trans('message.common_error')}}', 1);

                        }
                    }
                });
            });
        });
    </script>
@endsection