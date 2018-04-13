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
        var id = '', deleteUrl, file_item, file_name;
        $(function () {
            $('.btn-delete').click(function(){
                resetMessage();
                $('.overlay-wrapper .overlay').hide();
                deleteUrl = '{{ URL::route("bot.file.destroy", [Route::current()->getParameter('bot'), ":id"]) }}';
                file_item = $(this).parents('.data-file-item');
                id = file_item.attr('data');
                file_name = file_item.find('.file-name-label').html();
                $('.delete-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });

            $('.btn-modal-delete').on('click', function(evt) {
                $('.overlay-wrapper .overlay').show();
                deleteUrl =  deleteUrl.replace(':id',id);
                $.ajax({
                    url: deleteUrl,
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    type: 'DELETE',
                    success: function(data) {
                        var message = '{{trans('message.delete_success', ['name' => ':name'])}}';
                        message = message.replace(':name', file_name);
                        $('.overlay-wrapper .overlay').hide();
                        file_item.remove();
                        setMesssage(message, 2);
                        $('.delete-modal').modal('hide');
                    },
                    error: function(result){
                        var text = $.parseJSON(result.responseText);
                        $('.overlay-wrapper .overlay').hide();
                        if(text.errors != void 0 && text.errors.msg != void 0){
                            setMesssage(text.errors.msg, 1)
                        }
                    }
                });
            });

            function resetMessage() {
                $('.ul-file-upload ').html('');
            }
        });
    </script>
@endsection