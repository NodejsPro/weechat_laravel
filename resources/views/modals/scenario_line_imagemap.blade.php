<div class="modal line_imagemap_template_modal" id="line_imagemap_template_modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('scenario.select_template') }}}</h4>
            </div>
            <div class="modal-body">
                <div class="row imagemap_template_content cmxform form-horizontal">
                    <div class="col-md-12">
                        @if(isset($line_imagemap_template_value_list) && $line_imagemap_template_value_list)
                            @foreach($line_imagemap_template_value_list as $index => $imagemap_template_area)
                                <?php
                                $template_type = $index+1;
                                $template_name = 'images/line_imagemap_template/type_'.$template_type.'_thumb.png';
                                ?>
                                @if(file_exists(public_path($template_name)))
                                    <div class="col-md-3 imagemap_template_box {{ ($template_type<=4) ? 'first_row' : '' }}" data-template_type="{{ $template_type }}" data-template_area="{{ $imagemap_template_area }}">
                                        <img src="{{ '/'.$template_name }}" alt="Imagemap template">
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="overlay" style="display: none">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn-modal-select">{{{ trans('button.select_template')}}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $(function () {
        $('.message_container_input .imagemap_group .select_template').on('click', function(evt) {
            $('#line_imagemap_template_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
        $('#line_imagemap_template_modal .imagemap_template_content .imagemap_template_box').on('click', function(evt) {
            $('#line_imagemap_template_modal .imagemap_template_content .imagemap_template_box').removeClass('active');
            $(this).addClass('active');
        });
        $('#line_imagemap_template_modal .btn-modal-select').on('click', function(evt) {
            $('#line_imagemap_template_modal').modal('hide');
        });
    });
</script>