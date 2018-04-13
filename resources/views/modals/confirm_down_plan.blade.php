<div class="modal down-plan-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('default.confirm_down_plan')}}}</h4>
            </div>
            <div class="modal-body">
                <div class="row confirm-content">
                    <div class="col-md-12 content"></div>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.cancel')}}}</button>
                <button type="button" class="btn btn-danger btn-modal-down-plan">{{{ trans('field.yes')}}}</button>
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
            var url = '';

            $('.controlarea .btn_down_plan').on( 'click', function () {
                var plan_group = $(this).data('group_plan'),
                    apply_date = $(this).data('apply_date'),
                    current_plan = plan_group[$(this).data('current_plan')],
                    change_plan = plan_group[$(this).data('change_plan')],
                    confirm_content = '{!! trans('plan.message_down_plan', ['planA' => ':planA', 'planB' => ':planB', 'date' => ':date']) !!}';
                confirm_content = confirm_content.replace('planA', current_plan).replace('planB', change_plan).replace('date', apply_date);

                $('.down-plan-modal .confirm-content').find('.content').html(confirm_content);
                $('.down-plan-modal').find('.btn-modal-down-plan').attr('plan-code', $(this).data('change_plan'));
                $('.btn-modal-down-plan').show();
                $('.overlay-wrapper .overlay').hide();
                $('.down-plan-modal .confirm-content').show();

                $('.down-plan-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });


            $('.btn-modal-down-plan').on('click', function(evt) {
                $('.overlay-wrapper .overlay').show();
                $('.btn-modal-down-plan').hide();

                var plan_code = $(this).attr('plan-code'),
                    url = '{{ URL::route('plan.change', ['plan_code'=>':plan_code']) }}';
                    url = url.replace(':plan_code', plan_code);
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(data) {
                        $('.down-plan-modal').modal('hide');
                        location.reload();
                    },
                    error: function(result){

                    }
                });
            });

        });
    </script>
@endsection