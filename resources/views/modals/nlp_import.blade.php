<div id="nlp_import_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content overlay-wrapper ">
			<div class="modal-header">
				<h4 class="modal-title">{{{ trans('modal.nlp_import') }}}</h4>
			</div>
			<div class="modal-body">
				<div class="row">
				{!! Form::open(['enctype' => 'multipart/form-data', 'class' => 'form-horizontal cmxform nlp_import_form col-md-12', 'role' => 'form']) !!}
					<div class="box_message"></div>
					<div class="col-md-12">
						<div class="form-group">
							{!! Form::hidden('upload_file_name', null, ['class' => 'upload_file_name']) !!}
							{!! Form::hidden('real_file_name', null, ['class' => 'real_file_name']) !!}
							<div class="col-md-12">
                                <button type="button" class="btn btn-info btn_select_file">{{{ trans('button.select_file') }}}</button>
                                <label class="content_label upload_file_label"></label>
							</div>
                            <div class="col-md-12">
                                <p class="content_label file_type_require">{{ trans('message.file_type_require', ['type' => str_replace(',', ', ', $upload_type_list)]) }}</p>
                                <label class="error col-md-12 upload_file_name_error"></label>
                            </div>
						</div>
					</div>
				{!! Form::close() !!}
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-modal-close pull-left" data-dismiss="modal">{{{ trans('button.close') }}}</button>
				<button type="button" class="btn btn-info btn-import">{{{ trans('button.import') }}}</button>
			</div>

			<div class="hidden origin_param">
				{!! Form::open([ 'route' => ['bot.nlp.upload', Route::current()->getParameter('bot')], 'method' => 'POST',
					'enctype' => 'multipart/form-data',
					'id' => 'upload_form',
					'class' => 'upload_file',
					'role' => 'form',
					'data-error_type' => trans('validation.mimetypes', ['attribute' => trans('field.file'), 'values' => ':values']),
					'data-error_size' => trans('validation.max.file', ['attribute' => trans('field.file'), 'max' => ':max'])]
					) !!}
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="row fileupload-buttonbar">
						<div class="col-md-12" style="padding-left: 3px;">
							<span class="btn btn-info fileinput-button">
								<span>{{trans('button.select_file')}}</span>
								<input id="input_upload_file" type="file" name="upload_file" accept="" title=" ">
							</span>
							<span class="fileupload-process"></span>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
			<div class="overlay" style="display: none">
				<i class="fa fa-refresh fa-spin fa-2x"></i>
			</div>
		</div>
	</div>
</div>
@section('scripts3')
	<script src="{{ elixir('js/fileupload.js') }}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			var upload_file_max_size = parseInt('{{ isset($upload_file_size) ? $upload_file_size : 10 }}');//unit: mb
			var upload_file_content = null;
            var nlp_id = '';

			$('#nlp_import_modal #upload_form').submit(function (e) {
				e.preventDefault();
			});

			$(document).on('click', '.nlp_index .btn_nlp_import', function () {
                nlp_id = $(this).data('id');
                resetCrtModal();
                if(nlp_id) {
                    var nlp_name = $(this).data('name');
                    //change modal title
                    if(nlp_name != void 0 && nlp_name != '') {
                        var modal_title = $('#nlp_import_modal .modal-header .modal-title');
                        var modal_title_new = modal_title.html() + ' (' + nlp_name + ')';
                        modal_title.html(modal_title_new);
                    }
                    $('#nlp_import_modal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                }
			});

			$('#nlp_import_modal .nlp_import_form .btn_select_file').on('click', function () {
				$('#nlp_import_modal #upload_form #input_upload_file').click();
			});

			$(document).on('click', '#nlp_import_modal #upload_form #input_upload_file', function () {
				//init upload
				upload_file();
			});

			$('#nlp_import_modal .btn-import').on('click', function () {
				clearMessage();
				$('#nlp_import_modal .overlay').show();
				//upload file to server and import process
				upload_file(true);
			});


			function upload_process() {
				var nlp_form = $('#nlp_import_modal .nlp_import_form');
				var url = '{{route('bot.nlp.import', [$connect_page_id, ':nlp_id'])}}';
                url = url.replace(':nlp_id', nlp_id);

				//upload file to server
				$.ajax({
					url: url,
					data: nlp_form.serializeArray(),
					type: 'POST',
					success: function(data) {
						clearMessage();
                        if(data.msg != void 0 && data.msg != '') {
                            setMesssage(data.msg, 2, $('.nlp_index .box_message'));
                        }
                        showErrorMsg(data, true);
						global_datatable.ajax.reload(null, false);
						$('#nlp_import_modal').modal('hide');
                        nlp_id = '';
					},
					error: function(result) {
						showErrorMsg(result);
						$('#nlp_import_modal .overlay').hide();
					}
				});
			}

			//upload file
			function upload_file(import_flg) {
				if(import_flg && upload_file_content != null) {
					upload_file_content.submit();
				} else {
					var nlp_form = $('#nlp_import_modal .nlp_import_form'),
						upload_form = $('#nlp_import_modal #upload_form'),
						upload_file_name_error = nlp_form.find('label.upload_file_name_error'),
						real_file_name = nlp_form.find('input.real_file_name'),
						upload_file_name = nlp_form.find('input.upload_file_name');

					//get error from ajax when not select file if click to upload button
					if(import_flg && upload_form.find('#input_upload_file')[0] != void 0 && upload_form.find('#input_upload_file')[0].files[0] == void 0) {
						upload_process();
					}

					upload_form.find('#input_upload_file').fileupload({
						//dropZone: upload_form.find('#input_upload_file'),
						add: function (e, data) {
							var data_file_upload = data.files[0];
							var error_name = '';
							var fileType = data_file_upload.name.split('.').pop().toLowerCase(),
								allowdtypes = '{{ isset($upload_type_list) ? $upload_type_list : '' }}';

                            allowdtypes = allowdtypes.split(',');
							upload_file_name_error.html('');
                            nlp_form.find('.file_type_require').removeClass('text-red');

                            if(allowdtypes.indexOf(fileType) < 0) {
//                                error_name = upload_form.data('error_type');
//                                error_name = error_name.replace(':values', allowdtypes.join(', '));
                                nlp_form.find('.file_type_require').addClass('text-red');

                            } else if (data_file_upload.size > upload_file_max_size*1024*1024) {//Byte to kb
                                error_name = upload_form.data('error_size');
                                error_name = error_name.replace(':max', upload_file_max_size*1024); //Kb
                            }

							if(error_name) {
								upload_file_name_error.html(error_name);
							} else {
								if(data.files[0].name != void 0 && data.files[0].name != '') {
									var upload_file_label = nlp_form.find('.upload_file_label');
									upload_file_label.html(data.files[0].name);
								}
								//set data to upload_file_content to call submit event to upload to server
								upload_file_content = data;
							}
						},
						fail: function(e, data){
//							console.log('fail', data);
							var error_content = data.jqXHR.responseJSON;
							if(error_content.msg != void 0 && error_content.msg.error != void 0 && error_content.msg.error != '') {
								upload_file_name_error.html(error_content.msg.error);
							}
						},
						success: function (data) {
//							console.log('success', data);
							if(data.success) {
								if(data.data != void 0 && data.data.file_name != void 0 &&  data.data.file_name != ''){
									upload_file_name.val(data.data.file_name);
									real_file_name.val(data.data.name);
								}
							}
						},
                        complete: function (data) {
                            upload_process();
                        }
					});
				}
			}

			function resetCrtModal() {
				var nlp_form = $('#nlp_import_modal .nlp_import_form'),
					upload_form = $('#nlp_import_modal #upload_form');

                $('#nlp_import_modal .modal-header .modal-title').html('{{{ trans('modal.nlp_import') }}}');
				nlp_form.find('input.real_file_name, input.upload_file_name').val('');
				nlp_form.find('label.upload_file_label').html('');
				upload_form.find('#input_upload_file').val('');
				upload_file_content = null;
				clearMessage();
				$('#nlp_import_modal .overlay').hide();
			}

			function clearMessage() {
				var nlp_form = $('#nlp_import_modal .nlp_import_form'),
					nlp_index = $('.nlp_index');
				nlp_form.find('label.error').html('');
                nlp_form.find('.file_type_require').removeClass('text-red');
				setMesssage('', 2, nlp_index.find('.box_message').first());
				setMesssage('', 2, nlp_form.find('.box_message'));
			}

			function showErrorMsg(data, index_page_flg) {
			    if(data != void 0 && data) {
                    var nlp_form = $('#nlp_import_modal .nlp_import_form');
                    var nlp_index = $('.nlp_index');

                    //Error after save to Nlp
                    var data_json = data.responseJSON;

                    var error_data = false;
                    if(data.error_data != void 0) {
                        error_data = data.error_data;
                    } else if(data_json != void 0 && data_json.error_data != void 0) {
                        error_data = data_json.error_data;
                    }

                    if(error_data) {
                        var nlp_error = '';
                        var nlp_error_count = 1;
                        $.each(error_data, function(i, e) {
                            if(e != '') {
                            	//show number error if more than 1 error
                            	if(error_data.length > 1) {
									nlp_error += (nlp_error != '') ? '<br/>' : '';
									nlp_error += nlp_error_count + '. ' + e;
								} else {
									nlp_error += e;
								}
                                nlp_error_count++;
                            }
                            if(nlp_error) {
                                var error_elm = nlp_form.find('.box_message');
                                if(index_page_flg != void 0 && index_page_flg) {
                                    error_elm = nlp_index.find('.box_message').first();
                                }
                                setMesssage(nlp_error, 1, error_elm);
                            }
                        });
                    } else {
                        //Error before save to Nlp
                        var error_common = '';
                        if(data_json != void 0) {
                            //error common message
                            if(data_json.msg != void 0 && data_json.msg != '') {
                                error_common = data_json.msg;
                            }

                            //validate inputs
                            $.each(data_json, function(input_name, err_content) {
                                if(err_content != '') {
                                    if(nlp_form.find('label.' + input_name + '_error').html() == '') {
                                        nlp_form.find('label.' + input_name + '_error').html(err_content);
                                    }
                                }
                            });
                        } else {
                            error_common = '{{ trans('message.common_error') }}';
                        }
                        if(error_common) {
                            setMesssage(error_common, 1, nlp_form.find('.box_message'));
                        }
                    }
                }
			}
		});
	</script>
@endsection