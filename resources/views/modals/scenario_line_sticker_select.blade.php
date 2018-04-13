<div class="modal line_sticker_select_modal" id="line_sticker_select_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <section class="panel">
                            <header class="panel-heading tab-bg-dark-navy-blue nav-color">
                                <ul class="nav nav-tabs">
                                    @if(isset($line_sticker_list) && count($line_sticker_list))
                                        <?php $i = 0; ?>
                                        @foreach($line_sticker_list as $package_id => $sticker_list)
                                            <?php $sticker_path = asset('images/line_sticker/package_'.$package_id); ?>
                                            <li class="nav-sticker-item {{ ($i==0) ? 'active' : '' }}">
                                                <a data-toggle="tab" href="#package_{{ $package_id }}">
                                                    <img class="tab_img" src="{{ $sticker_path.DIRECTORY_SEPARATOR.(($i==0) ?  'tab_on' : 'tab_off').'.png' }}" alt="" />
                                                </a>
                                            </li>
                                            <?php $i++; ?>
                                        @endforeach
                                    @endif
                                </ul>
                            </header>
                            <div class="panel-body">
                                <div class="box_message"></div>
                                {!! Form::hidden('scenario_sticker_selected', null, ['id' => 'scenario_sticker_selected', 'class' => '']) !!}

                                <div class="tab-content sticker_container">
                                    @if(isset($line_sticker_list) && count($line_sticker_list))
                                        <?php $i = 0; ?>
                                        @foreach($line_sticker_list as $package_id => $sticker_list)
                                            <?php $sticker_available  = 0; ?>
                                            <div id="package_{{ $package_id }}" class="tab-pane {{ ($i==0) ? 'active' : '' }}">
                                                @foreach($sticker_list as $sticker)
                                                    <?php $sticker_path = '/images/line_sticker/package_'.$package_id.'/'.$sticker->sticker_id.'_key.png'; ?>
                                                    @if(file_exists(public_path($sticker_path)))
                                                        <?php $sticker_available++; ?>
                                                        <div class="col-xs-2 sticker_item text-center" data-sticker="{{ $package_id.'_'.$sticker->sticker_id }}">
                                                            <a class="image_view" title="">
                                                                <img class="" src="{{ asset($sticker_path) }}" alt="" />
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                {{--show empty if í not sticker to show--}}
                                                @if($sticker_available <= 0)
                                                    <h5> {{{ trans('message.common_no_result')}}}</h5>
                                                @endif
                                            </div>
                                            <?php $i++; ?>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-cancel" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn_sticker_select">{{{ trans('scenario.sticker_select')}}}</button>
            </div>
        </div>
    </div>
</div>
@section('scripts3')
    <script>
        $(function () {
            $(document).on('click', '.scenario-edit .btn_sticker_select_popup', function () {
                $('#line_sticker_select_modal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                //data init
                $('#line_sticker_select_modal #scenario_sticker_selected').val('');
                $('#line_sticker_select_modal .sticker_item').removeClass('active');
            });
            if ($.fn.slimScroll) {
                $('#line_sticker_select_modal .sticker_container').slimscroll({
                    height: '400px',
                    wheelStep: 20
                });
            }
            //image on off for tabs when change
            $('#line_sticker_select_modal .nav-sticker-item').on('shown.bs.tab', function (e) {
                $('#line_sticker_select_modal .nav-sticker-item').each(function (i,e) {
                    var src = $(this).find('.tab_img').attr('src');
                    src = src.replace('on', 'off');
                    $(this).find('.tab_img').attr('src', src);
                });
                var src_active = $(e.target).find('.tab_img').attr('src');
                src_active = src_active.replace('off', 'on');
                $(e.target).find('.tab_img').attr('src', src_active);
            });
            //event select items
            $('#line_sticker_select_modal .sticker_item').on('click', function () {
                $('#line_sticker_select_modal .sticker_item').removeClass('active');
                $(this).addClass('active');

                var sticker_data = $(this).data('sticker');
                $('#line_sticker_select_modal #scenario_sticker_selected').val(sticker_data);

            });
            //event click button select
            $('#line_sticker_select_modal .btn_sticker_select').on('click', function (e) {
                var sticker_selected = $('#line_sticker_select_modal #scenario_sticker_selected').val();
                if(sticker_selected == '') {
                    var box_message =  $('#line_sticker_select_modal .box_message');
                    setMesssage('{{trans('message.sticker_select_empty')}}', 0, box_message, true);
                } else {
                    $("#line_sticker_select_modal .btn-cancel").click();
                }
            });
        });
    </script>
@endsection