<div id="scenario_clone_template_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.select_template') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <section class="panel">
                        <div class="panel-body">
                            {!! Form::hidden('template_selected_id', null, ['id' => 'template_selected_id', 'class' => '']) !!}
                            <div class="col-md-12">
                                <label class="template_modal_message" style="display: none;"></label>
                            </div>
                            <div class="template_container">
                                <ul class="table template_list table-hover">
                                    @if(isset($template_list) && count($template_list))
                                        @php
                                            $group_type_service_key = config('constants.group_type_service_key');
                                        @endphp
                                        @foreach($template_list as $template)
                                            <li class="template_box col-sm-4">
                                                <div class="template_item" data-template_id="{{ $template->id }}">
                                                    <div class="template_picture">
                                                        <img src="{{{ $_destination . ($template->picture ? '/template/'.$template->picture : '/bot_picture/default_'.$group_type_service_key[$template->sns_type].'.png') }}}" alt="{{{ $template->page_name }}}">
                                                    </div>
                                                    <div class="label_box col-md-12"><p title="{{ $template->page_name }}">{{{ str_limit($template->page_name, 20, '...') }}}</p></div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <h5> {{{ trans('message.common_no_result')}}}</h5>
                                    @endif

                                </ul>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn_clone_template hidden">{{{ trans('button.execute')}}}</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        if ($.fn.slimScroll) {
            $('#scenario_clone_template_modal .template_container').slimscroll({
                height: '450px',
                width: '100%',
                wheelStep: 20,
            });
        }
        //init modal
        $('.scenario_index .btn_bot_clone').on('click', function (e) {
            var clone_template = $('#scenario_clone_template_modal');
            clone_template.find('#template_selected_id').val('');
            clone_template.find('.template_item').removeClass('active');
            clone_template.find('.btn_clone_template').addClass('hidden');
            clone_template.find('.overlay-wrapper .overlay').hide();
            clone_template.find('.overlay-wrapper .overlay').hide();
            clone_template.find('.modal-title').html('{{{ trans('modal.select_template') }}}');

            clone_template.modal({
                backdrop: 'static',
                keyboard: false
            })
        });
        //select template item
        $('#scenario_clone_template_modal .template_item').on('click', function (e) {
            var clone_template  = $('#scenario_clone_template_modal'),
                template_id     = $(this).data('template_id');

            clone_template.find('.template_item').removeClass('active');
            $(this).addClass('active');

            clone_template.find('#template_selected_id').val(template_id);
            clone_template.find('.btn_clone_template').removeClass('hidden');
        });
        //transfer process
        $('#scenario_clone_template_modal .btn_clone_template').on('click', function (e) {
            var clone_template = $('#scenario_clone_template_modal'),
                    page_from_id   = clone_template.find('#template_selected_id').val(),
                    page_to_id     = '{{ $connect_page->id }}';

            clone_template.find('.overlay-wrapper .overlay').show();
            $.ajax({
                url: '{{ route('bot.transfer' )}}',
                data: {
                    "_token"    : "{{ csrf_token() }}",
                    "transfer_to_bot"   : page_to_id,
                    "transfer_from_bot" : page_from_id,
                    "template_to_bot_flg" : true
                },
                type: 'POST',
                success: function(data) {
                    clone_template.modal('hide');
                    location.reload();
                },
                error: function(result){
                    clone_template.find('.overlay-wrapper .overlay').hide();
                    var error_data = $.parseJSON(result.responseText),
                            error_msg  = '';

                    if(error_data.transfer_from_bot_name != void 0){
                        error_msg = error_data.transfer_from_bot_name[0];

                    } else if(error_data.transfer_to_bot != void 0){
                        error_msg = error_data.transfer_to_bot[0];

                    } else if(error_data.transfer_from_bot != void 0){
                        error_msg = error_data.transfer_from_bot[0];

                    } else if(error_data.msg != void 0){
                        error_msg = error_data.msg;
                    } else {
                        error_msg = error_data;
                    }
                    clone_template.find('label.template_modal_message').addClass('.text-red').html(error_msg).show();
                }
            });
        });

    });
</script>