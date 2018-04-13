<div class="modal library-edit-modal">
    <div class="modal-dialog">
        <div class="modal-content overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">{{{ trans('modal.question_add')}}}</h4>
            </div>
            <div class="modal-body body">
                <div class="adv-table editable-table edit-content">
                    <div class="clearfix content-modal">
                        <div class="btn-tools pull-right">
                            <div class="fileupload fileupload-new pull-left" data-provides="fileupload">
                                <form id="form-excel" class="form-excel" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="row">
                                        <span class="btn btn-info btn-file">
                                            <span class="fileupload-new"><i class="fa fa-paper-clip"></i>{{trans('button.import')}}</span>
                                           <input id="import_file" type="file" name="import_file"  multiple title=" ">
                                        </span>
                                    </div>
                                </form>
                            </div>
                            <a href="#" class="btn btn-info pull-left export-csv">{{trans('button.export')}}</a>
                            <a href="#" class="btn btn-info pull-left download-template" style="margin-left: 2px;">{{trans('button.download_template')}}</a>
                        </div>
                        <div class="space15 clearfix"></div>
                        <div class="box_message"></div>
                        <div class="row question_container" style="margin-bottom: 50px">
                            <div class="col-md-12">
                                <div class="question-content">
                                    <table  class="table table-hover" id="editable-sample">
                                        <thead>
                                        <tr>
                                            <th>{{trans('field.question')}}</th>
                                            <th>{{trans('field.answer')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody class="append_item"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="group-add">
                            <button class="btn btn-info btn-add btn-add-item">{{trans('button.add')}}</button>
                        </div>
                    </div>
                </div>
                <table class="hidden origin_element">
                    <tbody>
                    <tr class="row-item">
                        <td>
                            {!! Form::text('question', null, ['class' => 'form-control tags question']) !!}
                        </td>
                        <td>
                            <div class="sellect">
                                {!! Form::select('type', @$library_type, null, ['id' => '','class' => 'type form-control pull-left']) !!}
                            </div>
                            {!! Form::textarea(
                                'text', null, ['id' => '','class' => 'form-control text', 'rows' => '3', 'data-old_value' => ''])
                            !!}
                            {!! Form::select('scenario', $scenario_list, null, ['id' => '','class' => 'scenario form-control hide']) !!}
                        </td>
                        <td>
                            <a style="cursor: pointer;" class="show-hide-cut" href="#"><i class="glyphicon glyphicon-remove"></i></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="overlay">
                <i class="fa fa-refresh fa-spin fa-2x"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn-modal-edit">{{{ trans('button.save')}}}</button>
            </div>
        </div>
    </div>
</div>

<script>
    var success = false;
    var id = '';
    $(document).ready(function () {
        var urlEditLibrary = '';
        var urlImport = '';
        var content_item;
        $(document).on('click', '.show-hide-cut', function () {
            $(this).parents('.row-item').remove();
            deleteRow();
        });
        $('#datatable_group').on( 'click','a.btn-add-question', function () {
            //delete old libraries
            setMesssage('');
            $('.library-edit-modal .box_message').html('');
            $('.question-content').removeClass('question-content-scroll');
            $('.append_item tr.row-item').remove();
            $('.overlay-wrapper .overlay').show();
            id = $(this).data('button');
            urlEditLibrary = '{{ URL::route('bot.library.edit', ['bot'=>$connect_page_id, 'library'=>':library_id']) }}';
            urlEditLibrary = urlEditLibrary.replace(':library_id', id);

            urlImport = '{{ URL::route('bot.library.import', ['bot'=>$connect_page_id, 'library'=>':library_id']) }}';
            urlImport = urlImport.replace(':library_id', id);

            $('#form-excel').attr("action", urlImport);
            $('#form-excel input').attr("data-url", urlImport);
            $('.export-csv').attr('data-library-id', id);
            $('.library-edit-modal .btn-modal-edit').attr('data-library-id', '');
            $('.library-edit-modal .btn-modal-edit').attr('data-library-id', id);

            $('.library-edit-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            // call fill data edit
            $.ajax({
                url: urlEditLibrary,
                type: 'GET',
                success: function(data) {
                    $('.overlay-wrapper .overlay').hide();
                    setMessageModal('');
                    if(data != void 0 && data.library != void 0){
                        var dataLibrary = data.library,
                            dataMessage = dataLibrary.messages;
                        ///append message data
                        if(Array.isArray(dataMessage)){
                            if(dataMessage.length >=5){
                                $('.question-content').addClass('question-content-scroll');
                            }
                            for (var i =0; i < dataMessage.length; i++) {
                                //add html element
                                content_item = $('.origin_element .row-item').clone();
                                $('.append_item').append(content_item);
                                //put value
                                var last_row = $(".append_item tr.row-item:last-child");
                                last_row.find("input.question").val(dataMessage[i].question);
                                last_row.find("select.type").val(dataMessage[i].type);
                                if (dataMessage[i].type != undefined && dataMessage[i].type == '{{config('constants.type_library.scenario')}}'){
                                    last_row.find("select.scenario").removeClass('hide');
                                    last_row.find("textarea.text").addClass('hide');
                                    last_row.find("select.scenario").val(dataMessage[i].answer);
                                }else {
                                    last_row.find("textarea.text").val(dataMessage[i].answer);
                                }
                                last_row.find('.question').tagsInput();
                                $('.tagsinput textarea').addClass('none');
                                textFilterVariable(last_row.find('.text'));
                            }
                            selectType();
                            deleteRow();
                        }
                    }
                },
                error: function(result){
                    $('.overlay-wrapper .overlay').hide();
                    setMessageModal('{{trans('message.common_error')}}');
                }
            });
        });
        // add row item
        addRow();
        // download file template
        downloadTemplate();
        // update data
        updateData();
        // import file excel
        importData();
        // export file csv
        exportData();
    });

    function deleteRow() {
        if($('.append_item .row-item').length >3){
            $('.question-content').removeClass('question-content-scroll').addClass('question-content-scroll');
        }else{
            $('.question-content').removeClass('question-content-scroll');
        }
    }

    function setMessageModal(message, type) {
        if (message != '' && message != null) {
            if(type == '' || type == void 0 || type == 1){
                $('.library-edit-modal .box_message').html('<p class="alert alert-danger">' + message + ' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>');
            }else{
                $('.library-edit-modal .box_message').html('<p class="alert alert-success">' + message + ' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>');
            }
        } else {
            $('.library-edit-modal .box_message').html('');
        }
    }

    function selectType() {
        // event change type anwers
        $('.question_container .append_item .type').each(function (i, e) {
            if (!$(this).hasClass('select2-hidden-accessible')){
                $(this).select2({
                    minimumResultsForSearch: -1
                });
            }
        });
        $('.question_container .append_item .type').on('change', function (e) {
            var  value = $(this).val();
            var  text = '{{config('constants.type_library.text')}}';
            var  scenario = '{{config('constants.type_library.scenario')}}';
            var parent = $(this).parents('.row-item');
            if(value == text){
                parent.find('.text').removeClass('hide');
                parent.find('.scenario').addClass('hide');
            }
            if(value == scenario){
                parent.find('.scenario').removeClass('hide');
                parent.find('.text').addClass('hide');
            }

        });
    }

    function textFilterVariable(item) {
        var variable_list = <?php echo json_encode($variable_list); ?>;
        item.textcomplete([
            {
                match: /@(\w*)$/,
                search: function (term, callback) {
                    callback($.map(variable_list, function (element) {
                        return element.indexOf(term) === 0 ? element : null;
                    }));
                },
                index: 1,
                replace: function (element) {
                    return ['\{\{' + element + '}}', ''];
                }
            }
        ],{
                maxCount: 1000,
                zIndex: '1052'
           }
        );
    }

    function downloadTemplate() {
        var urlDownload = '{{ URL::route('library.template') }}';
        $('.download-template').on('click', function (evt) {
            $.ajax({
                url: urlDownload,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                type: 'GET',
                success: function(data) {
                    success = true;
                    window.open('{{action('LibraryController@downloadTemplate')}}');
                },
                error: function(result){

                }
            });
        });
    }

    function importData() {
        var content_item;
        $('#import_file').fileupload({
            dropZone: $('#drop'),
            add: function (e, data) {
                var data_upload = $("#import_file").serializeArray();
                var jqXHR = data.submit()
            },progress: function(e, data){
                setMessageModal('');
                $('.library-edit-modal .overlay-wrapper .overlay').show();
            },fail:function(e, data){
                deleteRow();
                $('.library-edit-modal .overlay-wrapper .overlay').hide();
                var response = $.parseJSON(data.jqXHR.responseText);
                var error_message = [];
                $.each(response, function(index, value) {
                    error_message.push(value[0]);
                });
                setMessageModal(error_message.join("\n"));
            },success:function (data) {
                $('.library-edit-modal .overlay-wrapper .overlay').hide();
                if(data != void 0){
                    if(data.success) {
                        var count_row_success = data.count_row_import - data.count_row_error;
                        var message = '{!! trans('message.import_success', ['success'=>':success', 'error'=>':error']) !!}';
                        message = message.replace(':success', count_row_success).replace(':error', data.count_row_error);
                        setMessageModal(message, 2);
                        var dataImport = data.dataImport;
                        $.each(dataImport, function(index, value) {
                            if(value[0][0] != void 0){
                                //add html element
                                content_item = $('.origin_element .row-item').clone();
                                $('.append_item').append(content_item);
                                //put value
                                var last_row = $(".append_item tr.row-item:last-child");
                                last_row.find("input.question").val(value[0][0]);
                                last_row.find("select.type").val(value[0][1]);
                                last_row.find("textarea.text").val(value[0][2]);
                                if (value[0][1] == '{{config('constants.type_library.scenario')}}'){
                                    last_row.find("select.scenario").removeClass('hide');
                                    last_row.find("textarea.text").addClass('hide');
                                    last_row.find("select.scenario").val(value['scenario_id']);
                                }
                                last_row.find('.question').tagsInput();
                                $('.tagsinput textarea').addClass('none');
                                textFilterVariable(last_row.find('.text'));

                            }else{
                                setMessageModal('{{trans('message.import_error')}}')
                            }
                        });

                    }
                }
                selectType();
                deleteRow();

            }
        });
    }

    function exportData() {
        var urlExport;
        var urlDownloadCSV;
        $('.export-csv').on('click', function (evt) {
            id = $(this).data('library-id');
            $('.library-edit-modal .overlay-wrapper .overlay').show();
            setMessageModal('');
            setMesssage('');
            urlExport = '{{ URL::route('bot.library.export', ['bot'=>$connect_page_id, 'library'=>':library_id']) }}';
            urlExport = urlExport.replace(':library_id', id);
            urlDownloadCSV = '{{action('LibraryController@downloadCSV', ['bot'=>$connect_page_id, 'library'=>':id'])}}';
            urlDownloadCSV = urlDownloadCSV.replace(':id', id);
            $.ajax({
                url: urlExport,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                type: 'GET',
                success: function(data) {
                    $('.library-edit-modal .overlay-wrapper .overlay').hide();
                    success = true;
                    window.open(urlDownloadCSV);
                },
                error: function(result){
                    $('.library-edit-modal .overlay-wrapper .overlay').hide();
                    var response = $.parseJSON(result.responseText);
                    setMessageModal(response.errors);
                }
            });
        });
    }

    function updateData() {
        var urlUpdateLibrary;
        $('.library-edit-modal .btn-modal-edit').on('click', function(evt) {
            $('.overlay-wrapper .overlay').show();
            id = $('.library-edit-modal .btn-modal-edit').attr('data-library-id');
            urlUpdateLibrary = '{{ URL::route('bot.library.keyword', ['bot'=>$connect_page_id, 'library'=>':library_id']) }}';
            urlUpdateLibrary = urlUpdateLibrary.replace(':library_id', id);
            var parent = $('.append_item tr.row-item');
            var questions = parent.find("input.question");
            var type = parent.find("select.type");
            var text = parent.find("textarea.text");
            var scenario = parent.find("select.scenario");
            var answer = '';
            var dataObj = [];
            for(var i = 0; i < questions.length; i++ ){
                if (type.eq(i).val() == '{{config('constants.type_library.text')}}'){
                    answer = text.eq(i).val();
                }
                if (type.eq(i).val() == '{{config('constants.type_library.scenario')}}'){
                    answer = scenario.eq(i).val();
                }
                dataObj.push({
                    'type' : type.eq(i).val(),
                    'question' : questions.eq(i).val(),
                    'answer' : answer
                });
            }
            $.ajax({
                url: urlUpdateLibrary,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "data": dataObj
                },
                type: 'POST',
                success: function(data) {
                    $('.overlay-wrapper .overlay').hide();
                    $('.library-edit-modal .content-modal').show();
                    setMessageModal('{{trans('message.saved_success')}}', 2);
                    success = true;
                },
                error: function(result){
                    var text = $.parseJSON(result.responseText);
                    $('.overlay-wrapper .overlay').hide();
                    setMessageModal(text.errors.msg);
                }
            });
        });
    }

    function addRow() {
        $('.btn-add-item').on('click', function () {
            content_item = $('.origin_element .row-item').clone();
            $('.append_item').append(content_item);
            $('.append_item tr:last-child .question').tagsInput();
            $('.tagsinput textarea').addClass('none');
            deleteRow();
            selectType();
            textFilterVariable($('.append_item tr:last-child .text'));
        });
    }


</script>
