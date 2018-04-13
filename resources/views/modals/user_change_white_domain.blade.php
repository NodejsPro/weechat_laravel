<div class="modal change-domain-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('default.confirm')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="row delete-content">
                    <div class="col-md-12">
                        {{{ trans("message.model_change_white_list_domain_confirm") }}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info pull-right btn-modal-change-domain-confirm">{{{ trans('button.change_domain_confirm')}}}</button>
                <button type="button" class="btn btn-danger pull-right btn-modal-cancel">{{{ trans('button.change_domain_cancel')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@section('scripts')
    <script>
        $(function () {
            $('.btn-modal-change-domain-confirm').on('click', function(evt) {
                $('#change_white_domain').val('1');
                $('.form-user-edit').submit();
            });
            $(".btn-modal-cancel").on('click', function(evt) {
                $('#change_white_domain').val('0');
                $('.change-domain-modal').hide();
                $('.form-user-edit').submit();
            });
        });
    </script>
@endsection