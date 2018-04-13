<div id="recommend_import_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content overlay-wrapper ">
			<div class="modal-header">
				<h4 class="modal-title">{{{ trans('modal.recommend_import') }}}</h4>
			</div>
			<div class="modal-body">
				<div class="row">
				{!! Form::open([ 'route' => ['bot.recommend.store', Route::current()->getParameter('bot')], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal cmxform recommend_form col-md-12', 'role' => 'form']) !!}
					<div class="box_message"></div>
					<div class="box_message2"></div>
					<div class="form-group">
						{!! Form::label('name', trans('field.data_set_name'), ['class' => "col-md-3 control-label required"]) !!}
						<div class="col-md-7">
							{!! Form::text('name', null, ['class' => 'form-control name']) !!}
							{!! Form::hidden('upload_file_name', null, ['class' => 'upload_file_name']) !!}
							{!! Form::hidden('real_file_name', null, ['class' => 'real_file_name']) !!}
							{!! Form::hidden('recommend_import_id', null, ['class' => 'recommend_import_id']) !!}
							<label for="name" class="error name_error"></label>
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('data', trans('field.data_info'), ['class' => "col-md-3 control-label"]) !!}
						<div class="col-md-9">
							<h6><?php /*echo '100 data (Update day: '.date('Y/m/d').')'; */?></h6>
						</div>
					</div>
					<div class="col-md-offset-3 col-md-9">
						{!! Form::label('label_box', trans('field.download'), ['class' => "col-md-12 label_box"]) !!}
						<div class="form-group">
							<div class="col-md-6">
								<div class="col-md-5">
									{!! Form::radio('download_type', config('constants.active.disable'), true, ['class' => 'icheck download_type']) !!}
									{!! Form::label('download_type', trans('field.csv'), ['class' => "col-md-3"]) !!}
								</div>
								<div class="col-md-5">
									{!! Form::radio('download_type', config('constants.active.enable'), false, ['class' => 'icheck download_type']) !!}
									{!! Form::label('download_type', trans('field.json'), ['class' => "col-md-3"]) !!}
								</div>
								<label for="" class="error col-md-12 download_type_error"></label>
							</div>
							<div class="col-md-3">
								<button type="button" class="btn btn-info btn-modal-download disabled">{{{ trans('button.download') }}}</button>
							</div>
						</div>
						<hr/>
						{!! Form::label('label_box', trans('field.upload'), ['class' => "col-md-12 label_box"]) !!}
						<div class="form-group">
							<div class="col-md-12">
								<div class="col-md-3">
									<button type="button" class="btn btn-info btn_select_file">{{{ trans('button.select_file') }}}</button>
									{{--<input type="file" class="input-upload"/>--}}
								</div>
								<div class="col-md-5">
									<p class="content_label">{{ isset($upload_type_list) ? $upload_type_list : '' }}</p>
								</div>
								<label class="col-md-12 content_label upload_file_label"></label>
								<label class="error col-md-12 upload_file_name_error"></label>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<div class="col-md-5">
									{!! Form::radio('upload_type', config('constants.active.disable'), true, ['class' => 'icheck upload_type']) !!}
									{!! Form::label('upload_type', trans('field.add'), ['class' => "col-md-3"]) !!}
								</div>
								<div class="col-md-5">
									{!! Form::radio('upload_type', config('constants.active.enable'), false, ['class' => 'icheck upload_type']) !!}
									{!! Form::label('upload_type', trans('field.insert'), ['class' => "col-md-3"]) !!}
								</div>
								<label for="" class="error col-md-12 upload_type_error"></label>
							</div>
							<div class="col-md-3">
								<button type="button" class="btn btn-info btn-upload">{{{ trans('button.upload') }}}</button>
							</div>
						</div>
					</div>
					{{--<div class="form-group">
						{!! Form::label('status', trans('field.status'), ['class' => "col-md-3 control-label"]) !!}
						<div class="col-md-9">
							<h6>Start process</h6>
						</div>
					</div>--}}
				{!! Form::close() !!}
				</div>
				<div class="row common_msg">
					<div class="col-md-12">
						<label class="error text-red"></label>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-modal-close pull-left" data-dismiss="modal">{{{ trans('button.close') }}}</button>
{{--				<button class="btn btn-info btn-modal-add">{{{ trans('button.save') }}}</button>--}}
			</div>

			<div class="hidden origin_param">
				{!! Form::open([ 'route' => ['bot.recommend.upload', Route::current()->getParameter('bot')], 'method' => 'POST',
					'enctype' => 'multipart/form-data',
					'id' => 'upload_form',
					'class' => 'upload_file',
					'role' => 'form',
					'data-error_type' => trans('validation.mimetypes', ['attribute' => trans('field.file'), 'values' => ':values']),
					'data-error_size' => trans('validation.max.file', ['attribute' => trans('field.file'), 'min' => 40000])]
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
@section('scripts2')
	<script src="{{ elixir('js/fileupload.js') }}"></script>
	<script src="{{ elixir('js/iCheck.js') }}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			var upload_file_max_size = parseInt('{{ isset($upload_file_size) ? $upload_file_size : 10 }}');//unit: mb
			var upload_file_content = null;

			$('#recommend_import_modal #upload_form').submit(function (e) {
				e.preventDefault();
			});

			$('.recommend_index .btn_recommend_import').on('click', function () {
				$('#recommend_import_modal .modal-title').html('{{ trans('modal.recommend_import') }}');
				resetCrtModal();
				$('#recommend_import_modal').modal({
					backdrop: 'static',
					keyboard: false
				});
			});

			$('#recommend_import_modal .recommend_form input.icheck').iCheck({
				checkboxClass: 'icheckbox_minimal col-md-2',
				radioClass: 'iradio_minimal col-md-2'
			});

			$('#recommend_import_modal .recommend_form .btn_select_file').on('click', function () {
				$('#recommend_import_modal #upload_form #input_upload_file').click();
			});

			$(document).on('click', '#recommend_import_modal #upload_form #input_upload_file', function () {
				//init upload
				upload_file();
			});

			$('#recommend_import_modal .recommend_form .btn-upload').on('click', function () {
				clearMessage();
				$('#recommend_import_modal .overlay').show();
				//upload file to server and import process
				upload_file(true);
			});

			$(document).on('click', '.recommend_index .btn_recommend_import_update', function () {
				resetCrtModal();
				$('#recommend_import_modal').modal({
					backdrop: 'static',
					keyboard: false
				});
				$('#recommend_import_modal .modal-title').html('{{ trans('modal.recommend_import_update') }}');

				var recommend_id = $(this).data('id');
				var recommend_form = $('#recommend_import_modal .recommend_form');
				var url = '{{route('bot.recommend.show', [$connect_page_id, ':recommend_id'])}}';
				url = url.replace(':recommend_id', recommend_id, url);
				$('#recommend_import_modal .overlay').show();
				$.ajax({
					url: url,
					type: 'GET',
					success: function(data) {
						if(data != void 0 && data.data != void 0) {
							var recommend_import = data.data;
							recommend_form.find('input.name').val(recommend_import.name);
							recommend_form.find('input.recommend_import_id').val(recommend_id);
						}
					},
					error: function(result) {
						showErrorMsg(result);
					},
					complete: function () {
						$('#recommend_import_modal .overlay').hide();
					}
				});
			});

			function upload_process() {
				var recommend_form = $('#recommend_import_modal .recommend_form');
				var url = '{{route('bot.recommend.store', $connect_page_id)}}';
				var ajax_type = 'POST';
				var recommend_id = recommend_form.find('input.recommend_import_id').val();
				if(recommend_id != void 0 && recommend_id != '') {
					url = '{{route('bot.recommend.update', [$connect_page_id, ':recommend_id'])}}';
					url = url.replace(':recommend_id', recommend_id, url);
					ajax_type = 'PUT';
				}
				//upload file to server
				$.ajax({
					url: url,
					data: recommend_form.serializeArray(),
					type: ajax_type,
					success: function(data) {
						clearMessage();
						showErrorMsg(data, true);
						global_datatable.ajax.reload(null, false);
						$('#recommend_import_modal').modal('hide');
					},
					error: function(result) {
						showErrorMsg(result);
						$('#recommend_import_modal .overlay').hide();
					}
				});
			}

			//upload file
			function upload_file(import_flg) {
				if(import_flg && upload_file_content != null) {
					upload_file_content.submit();
				} else {
					var recommend_form = $('#recommend_import_modal .recommend_form'),
						upload_form = $('#recommend_import_modal #upload_form'),
						upload_file_name_error = recommend_form.find('label.upload_file_name_error'),
						real_file_name = recommend_form.find('input.real_file_name'),
						upload_file_name = recommend_form.find('input.upload_file_name');

					//get error from ajax when not select file if click to upload button
					if(import_flg && upload_form.find('#input_upload_file')[0] != void 0 && upload_form.find('#input_upload_file')[0].files[0] == void 0) {
						upload_process();
					}

					upload_form.find('#input_upload_file').fileupload({
						//dropZone: upload_form.find('#input_upload_file'),
						add: function (e, data) {
							var data_file_upload = data.files[0];
							var error_text = '';
							var fileType = data_file_upload.name.split('.').pop().toLowerCase(),
								allowdtypes = '{{ isset($upload_type_list) ? $upload_type_list : '' }}';

							upload_file_name_error.html('');
							if (data_file_upload.size > (upload_file_max_size * 1024 * 1024)) {
								error_text = upload_form.data('error_size');
							} else if (allowdtypes.indexOf(fileType) < 0) {
								error_text = upload_form.data('error_type');
								error_text = error_text.replace(':values', allowdtypes);
							}
							if(error_text) {
								upload_file_name_error.html(error_text);
							} else {
								if(data.files[0].name != void 0 && data.files[0].name != '') {
									var upload_file_label = recommend_form.find('.upload_file_label');
									upload_file_label.html(data.files[0].name);
								}
								//set data to upload_file_content to call submit event to upload to server
								upload_file_content = data;
							}
						},
						fail: function(e, data){
//							console.log('fail', data);
							var error_content = $.parseJSON(data.jqXHR.responseText);
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
									//upload_file_name_error.html(data.msg);
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
				var recommend_form = $('#recommend_import_modal .recommend_form'),
					upload_form = $('#recommend_import_modal #upload_form');

				recommend_form.find('input.name, input.upload_file_name, input.recommend_import_id').val('');
				recommend_form.find('label.upload_file_label').html('');
				recommend_form.find('input.upload_type[value="' + {{ config('constants.active.disable') }} + '"]').iCheck('check');
				recommend_form.find('input.download_type[value="' + {{ config('constants.active.disable') }} + '"]').iCheck('check');
				upload_form.find('#input_upload_file').val('');
				upload_file_content = null;
				clearMessage();
				$('#recommend_import_modal .overlay').hide();
			}

			function clearMessage() {
				var recommend_form = $('#recommend_import_modal .recommend_form'),
					table_index_list = $('.recommend_index .table_recommend');
				recommend_form.find('label.error').html('');
				setMesssage('', 2, table_index_list.find('.box_message'));
				setMesssage('', 2, table_index_list.find('.box_message2'));
				setMesssage('', 2, recommend_form.find('.box_message'));
				setMesssage('', 2, recommend_form.find('.box_message2'));
			}

			function showErrorMsg(data, index_page) {
				if(index_page == void 0 || !index_page) {
					data = data.responseJSON;
				}

				var recommend_form = $('#recommend_import_modal .recommend_form'),
					table_index_list = $('.recommend_index .table_recommend');
				var error_common = '';
				var success_common = '';

				if(data != void 0) {
					console.log(data);
					//error common message
					if(data.msg != void 0 && data.msg != '') {
						if(data.success != void 0 && data.success) {
							success_common = data.msg;
						} else {
							error_common = data.msg;
						}
					}

					//error file import
					var error_file_import = data;
					if(data.data != void 0) {
						error_file_import = data.data;
					}

					//list error file
					if(error_file_import.error != void 0 && error_file_import.error.length) {
						$.each(error_file_import.error, function(i, err_content) {
							if(err_content != '') {
								if(error_common) {
									error_common += '<br/>';
								}
								error_common += err_content;
							}
						});
					}
					//list success file
					if(error_file_import.file_success != void 0 && error_file_import.file_success.length) {
						$.each(error_file_import.file_success, function(i, file_success_content) {
							if(file_success_content != '') {
								if(success_common) {
									success_common += '<br/>';
								}
								success_common += file_success_content;
							}
						});
					}


					//validate inputs
					$.each(data, function(input_name, err_content) {
						if(err_content != '') {
							if(recommend_form.find('label.' + input_name + '_error').html() == '') {
								recommend_form.find('label.' + input_name + '_error').html(err_content);
							}
						}
					});
				} else {
					error_common = '{{ trans('message.common_error') }}';
				}

				if(success_common) {
					var error_elm = recommend_form.find('.box_message');
					if(index_page != void 0 && index_page) {
						error_elm = table_index_list.find('.box_message');
					}
					setMesssage(success_common, 2, error_elm);
				}
				if(error_common) {
					var error_elm = recommend_form.find('.box_message2');
					if(index_page != void 0 && index_page) {
						error_elm = table_index_list.find('.box_message2');
					}
					setMesssage(error_common, 1, error_elm);
				}
			}

			global_datatable = $('#datatable_recommend').DataTable({
				info: false,
				processing: false,
				serverSide: true,
				ajax: {
					type: 'POST',
					url :'{!! route('bot.recommend.importList', $connect_page_id) !!}',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				},

				paging: true,
				searching: false,
				ordering:  true,
				"dom": '<"top"i>rt<"bottom"flp><"clear">',
				columns: [
					{ data: 'no', name: 'no', width: '50px' },
					{ data: 'name', name: 'name' },
					{ data: 'file_name', name: 'file_name', width: '130px' },
//					{ data: 'import_status', name: 'import_status' },
					{ data: 'action', name: 'action', orderable: false, searchable: false, width: '150px'}
				],
				language:
				{
					emptyTable: "<p class='pull-left'>{{trans('message.not_record')}}</p>",
					zeroRecords: "<p class='pull-left'>{{trans('message.not_record')}}</p>",
					paginate:
					{
						previous: "",
						next: ""
					}
				},
				"fnDrawCallback": function(oSettings) {
					if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
						$(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
					}else{
						$(oSettings.nTableWrapper).find('.dataTables_paginate').show();
					}
				},
				"pageLength": 10,
				"bLengthChange": false,
				"bAutoWidth": false,
				destroy: true
			});
		});
	</script>
@endsection