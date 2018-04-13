<div id="api_create_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content overlay-wrapper ">
			<div class="modal-header">
				<h4 class="modal-title">{{{ trans('modal.api_add') }}}</h4>
			</div>
			<div class="modal-body">
				<div class="row">
				{!! Form::open([ 'route' => ['bot.api.store', Route::current()->getParameter('bot')], 'method' => 'POST', 'class' => 'form-horizontal cmxform api_form col-md-12', 'role' => 'form']) !!}
					<div class="form-group">
						{!! Form::label('name', trans('field.api.name'), ['class' => "col-md-2 control-label required"]) !!}
						<div class="col-md-5">
							{!! Form::text('name', null, ['class' => 'form-control name']) !!}
							<label for="name" class="error name_error"></label>
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('method', trans('field.api.method'), ['class' => "col-md-2 control-label required"]) !!}
						<div class="col-md-5">
							{!! Form::select('method', array('GET'=>'GET', 'POST'=>'POST'), null, ['class' => 'form-control method', 'style' => 'width: 100%']) !!}
							<label for="method" class="error method_error"></label>
						</div>
					</div>
					<div class="form-group {{ ($sns_type != config('constants.group_type_service.web_embed_efo') && count($api_type) <= 1 || count($api_type) < 1) ? 'hidden' : '' }}">
						{!! Form::label('api_type', trans('field.api.type'), ['class' => "col-md-2 control-label required"]) !!}
						<div class="col-md-5">
							{!! Form::select('api_type', $api_type, null, ['class' => 'form-control api_type', 'style' => 'width: 100%']) !!}
							<label for="api_type" class="error api_type_error"></label>
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('url', trans('field.url'), ['class' => "col-md-2 control-label required"]) !!}
						<div class="col-md-8">
							{!! Form::text('url', null, ['class' => 'form-control url']) !!}
							<label for="url" class="error url_error"></label>
						</div>
					</div>

					<div class="col-md-12 request_box">
						{!! Form::label('', trans('field.request'), ['class' => "control-label label_box"]) !!}
						<div class="row param_label">
							<div class="col-md-4">
								{!! Form::label('parameter', trans('field.parameter')) !!}
							</div>
							<div class="col-md-3"></div>
							<div class="col-md-4">
								{!! Form::label('parameter', trans('field.variable')) !!}
							</div>
						</div>
						<div class="request_container"></div>
						<div class="form-group">
							<div align="right" class="col-md-offset-9 col-md-3">
								<button id="btn_add_request" type="button" class="btn btn-info">{{trans('button.add')}}</button>
							</div>
						</div>
					</div>

					<div class="col-md-12 response_box">
						{!! Form::label('', trans('field.response'), ['class' => "control-label label_box"]) !!}
						<div class="row param_label">
							<div class="col-md-4">
								{!! Form::label('parameter', trans('field.variable')) !!}
							</div>
							<div class="col-md-3"></div>
							<div class="col-md-4">
								{!! Form::label('parameter', trans('field.parameter')) !!}
							</div>
						</div>
						<div class="response_container"></div>
						<div class="form-group">
							<div align="right" class="col-md-offset-9 col-md-3">
								<button id="btn_add_response" type="button" class="btn btn-info">{{trans('button.add')}}</button>
							</div>
						</div>
					</div>
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
				<button class="btn btn-info btn-modal-add">{{{ trans('button.save') }}}</button>
			</div>

			<div class="hidden origin_param">
				<div class="form-group request_element">
					<div class="col-md-4">
						{!! Form::text('', null, ['class' => 'form-control param_group']) !!}
						<label class="error param_error"></label>
					</div>
					<div class="col-md-3">
						{!! Form::select('', $variable_input_type, null, ['class' => 'form-control variable_type_group', 'style' => 'width: 100%']) !!}
					</div>
					<div class="col-md-4">
						<div class="value_group_box">
							{!! Form::select('', $variable_custom, null, ['class' => 'form-control value_group', 'style' => 'width: 100%']) !!}
						</div>
						{!! Form::text('', null, ['class' => 'form-control hidden input_value_group']) !!}
						<label class="error value_error"></label>
					</div>
					<div class="col-md-1">
						<button type="button" class="btn btn-danger btn_delete_param" disabled="disabled">X</button>
					</div>
				</div>

				<div class="form-group response_element">
					<div class="col-md-4">
						{!! Form::select('', $variable_custom, null, ['class' => 'form-control value_group', 'style' => 'width: 100%']) !!}
						<label class="error value_error"></label>
					</div>
					<div class="col-md-3"></div>
					<div class="col-md-4">
						{!! Form::text('', null, ['class' => 'form-control param_group']) !!}
						<label class="error param_error"></label>
					</div>
					<div class="col-md-1">
						<button type="button" class="btn btn-danger btn_delete_param" disabled="disabled">X</button>
					</div>
				</div>
			</div>
			<div class="overlay">
				<i class="fa fa-refresh fa-spin fa-2x"></i>
			</div>
		</div>
	</div>
</div>
@section('scripts2')
	<script type="text/javascript">
		$(document).ready(function () {
			var url_api_store 	= '{{route('bot.api.store', $connect_page_id)}}',
				url_api_update 	= '{{ URL::route('bot.api.update', [$connect_page_id, ':api_id']) }}',
				api_id  		= '';

			$('#api_create_modal select.method, #api_create_modal select.api_type').select2({
				language: {
					"noResults": function(){
						return "{{trans('message.no_results_found')}}";
					}
				},
				minimumResultsForSearch: -1
			});

			$('.api_index .btn_api_create').on('click', function () {
				resetCrtModal();
				checkApiType();
				$('#api_create_modal').modal({
					backdrop: 'static',
					keyboard: false
				});
			});

			$(document).on('click', '.api_index .btn_api_update', function () {
				resetCrtModal();
				$('#api_create_modal .modal-title').html('{{{ trans('modal.api_edit') }}}');
				api_id = $(this).data('id');
				if(api_id != void 0 && api_id != '') {
					var url_api_get = url_api_update.replace(':api_id', api_id);
					getEditData(url_api_get);
				}
			});

			$('#api_create_modal #btn_add_request').on('click', function (event) {
				cloneRequestBox();
			});
			$('#api_create_modal #btn_add_response').on('click', function (event) {
				cloneResponseBox();
			});

			$(document).on('click', '#api_create_modal .btn_delete_param', function (event) {
				$(this).parents('.form-group').remove();
				indexParam();
			});
			$(document).on('change', '#api_create_modal select.api_type', function (event) {
				checkApiType();
			});
			$(document).on('change', '#api_create_modal select.variable_type_group', function (event) {
				var request_box = $(this).parents('.request_element');
				if($(this).val() == '{{ config('constants.variable_type_input.constant_value') }}') {
					request_box.find('.value_group_box').addClass('hidden');
					request_box.find('input.input_value_group').removeClass('hidden');
				} else {
					request_box.find('.value_group_box').removeClass('hidden');
					request_box.find('input.input_value_group').addClass('hidden');
				}
			});

			// globe submit ajax
			$('#api_create_modal .btn-modal-add').on('click', function () {
				$('#api_create_modal .overlay').show();
				var isCreate 	 = true,
					url_api_sent = url_api_store;
				if(api_id != void 0 && api_id != '') {
					url_api_sent = url_api_update.replace(':api_id', api_id);
					isCreate 	 = false;
				}
				sendCrtData(isCreate, url_api_sent);
			});

			function checkApiType() {
				var api_create_modal = $('#api_create_modal'),
					request_container = api_create_modal.find('.request_container'),
					response_container = api_create_modal.find('.response_container'),
					api_type = api_create_modal.find('select.api_type').val();
				if ('{{$sns_type}}' == '{{config('constants.group_type_service.web_embed_efo')}}') {
					api_create_modal.find('.response_box').hide();
					api_create_modal.find('.label_box').hide();
					response_container.html('');
					var request_first_item = $('.request_container .request_element').first();
					if($('.request_container .request_element').length <= 0){
						cloneRequestBox();
					}
					request_first_item.find('.btn_delete_param').hide();
				} else {
					if(api_type == '{{ config('constants.api_type.variable_setting') }}') {
						api_create_modal.find('.response_box').show();
						api_create_modal.find('.label_box').show();
						if(request_container.find('.request_element').length <= 0){
							cloneRequestBox();
						}
						if(response_container.find('.response_element').length <= 0){
							cloneResponseBox();
						}
					} else {
						api_create_modal.find('.response_box').hide();
						api_create_modal.find('.label_box').hide();
						response_container.html('');
					}
					indexParam();
					checkDisableParam();
				}
			}

			function cloneRequestBox() {
				var api_create_modal = $('#api_create_modal');
				//clone param view
				var clone = api_create_modal.find('.origin_param .request_element').clone();
				api_create_modal.find('.request_container').append(clone);
				indexParam();
				initSelect2();
			}

			function cloneResponseBox() {
				var api_create_modal = $('#api_create_modal');
				//clone param view
				var clone = api_create_modal.find('.origin_param .response_element').clone();
				api_create_modal.find('.response_container').append(clone);
				indexParam();
				initSelect2();
			}

			function initSelect2() {
				var api_create_modal = $('#api_create_modal');
				api_create_modal.find('.api_form select.value_group:not(.select2-hidden-accessible), .api_form select.variable_type_group:not(.select2-hidden-accessible)').select2({
					minimumResultsForSearch: -1,
					language: {
						"noResults": function(){
							return "{{trans('message.no_results_found')}}";
						}
					}
				});
			}

			function resetCrtModal () {
				api_id = '';
				var api_create_modal = $('#api_create_modal');
				api_create_modal.find('.overlay').hide();
				api_create_modal.find('form.api_form').show();
				//clear old data
				api_create_modal.find('input.name, input.url').val('');
				api_create_modal.find('select.method').val(api_create_modal.find('select.method option').first().val()).trigger('change.select2');
				api_create_modal.find('select.api_type').val(api_create_modal.find('select.api_type option').first().val()).trigger('change.select2');
				api_create_modal.find('.request_container, .response_container').html('');
				api_create_modal.find('label.error').html('');
				api_create_modal.find('.modal-title').html('{{{ trans('modal.api_add') }}}');
				checkLabel();
			}

			function sendCrtData(isCreate, url) {
				var api_create_modal = $('#api_create_modal');
				var method = "POST";
				if(!isCreate){
					method = "PUT";
				}
				$.ajax({
					url: url,
					data: api_create_modal.find('form.api_form').serializeArray(),
					type: method,
					success: function(data) {
						api_create_modal.modal('hide');
						setMesssage('{{ trans('message.save_success', ['name' => trans('default.api')]) }}', 2, $('.api_index .box_message'));
						global_datatable.ajax.reload(null, false);
					},
					error: function(result){
						showErrorMsg(result);
						api_create_modal.find('.overlay').hide();
					}
				});
			}

			function getEditData(url){
				var api_create_modal = $('#api_create_modal');
				$.ajax({
					url: url,
					type: 'GET',
					success: function(data) {
						if(data.api != void 0) {
							setEditModal(data.api);
							indexParam();
						}
						$('#api_create_modal').modal({
							backdrop: 'static',
							keyboard: false
						});
					},
					error: function(result){
						api_create_modal.find('form.api_form').hide();
						showErrorMsg(result);
					}
				});
			}

			function setEditModal(data) {
				var api_create_modal = $('#api_create_modal');
				var error_input 	 = ['name', 'method', 'api_type', 'url'];
				$(error_input).each(function (i, input) {
					if(data[input] != void 0 && data[input] != '') {
						var elm = api_create_modal.find('.' + input);
						elm.val(data[input]);
						if(elm.is("select")) {
							elm.trigger('change.select2');
						}
					}
				});
				//api param
				setParam('request');
				setParam('response');

				function setParam(type) {
					if(data[type] != void 0 && data[type].length) {
						$(data[type]).each(function (i, e) {
							//clone slot item
							var clone = api_create_modal.find('.origin_param .' + type + '_element').clone();
							if(type == 'request') {
								clone.find('input.param_group').val(e.param);
								if(e.variable_type != void 0 && e.variable_type != '') {
									clone.find('select.variable_type_group').val(e.variable_type).trigger('change.select2');
								} else {
									clone.find('select.variable_type_group').val('{{ config('constants.variable_type_input.select_variable') }}').trigger('change.select2');
								}

								if(e.variable_type != void 0 && e.variable_type == '{{ config('constants.variable_type_input.constant_value') }}') {
									clone.find('input.input_value_group').removeClass('hidden').val(e.value);
									clone.find('.value_group_box').addClass('hidden');
								} else {
									clone.find('select.value_group').val(e.value).trigger('change.select2');
								}
							} else {
								clone.find('select.value_group').val(e.value).trigger('change.select2');
								clone.find('input.param_group').val(e.param);
							}

							api_create_modal.find('.' + type + '_container').append(clone);
						});
						initSelect2();
					}
				}
				checkApiType();
			}

			function indexParam() {
				var api_create_modal = $('#api_create_modal');
				api_create_modal.find('.request_container .request_element').each(function (i, e) {
					var prefix_name = 'request['+ i +']';
					$(this).find('input.param_group').attr('name', prefix_name +'[param]');
					$(this).find('select.value_group').attr('name', prefix_name+ '[value]');
					$(this).find('input.input_value_group').attr('name', prefix_name+ '[input_value]');
					$(this).find('select.variable_type_group').attr('name', prefix_name+ '[variable_type]');
				});
				api_create_modal.find('.response_container .response_element').each(function (i, e) {
					var prefix_name = 'response['+ i +']';
					$(this).find('input.param_group').attr('name', prefix_name + '[param]');
					$(this).find('select.value_group').attr('name', prefix_name + '[value]');
				});
				checkLabel();
				checkDisableParam();
			}

			function checkLabel() {
				var api_create_modal = $('#api_create_modal');
				if(api_create_modal.find('.request_container .request_element').length > 0){
					api_create_modal.find('.request_box .param_label').show();
				}else{
					api_create_modal.find('.request_box .param_label').hide();
				}
			}

			function checkDisableParam() {
				var api_create_modal = $('#api_create_modal'),
					request_container = api_create_modal.find('.request_container'),
					response_container = api_create_modal.find('.response_container'),
					api_type = api_create_modal.find('select.api_type').val();

				//alway hold a request and a response if api type is variable setting
				request_container.find('.btn_delete_param').attr('disabled', null);
				response_container.find('.btn_delete_param').attr('disabled', null);

				if(api_type == '{{ config('constants.api_type.variable_setting') }}') {
					if(request_container.find('.request_element').length <= 1) {
						request_container.find('.btn_delete_param').attr('disabled', 'disabled');
					}
					if(response_container.find('.response_element').length <= 1) {
						response_container.find('.btn_delete_param').attr('disabled', 'disabled');
					}
				}
			}

			function showErrorMsg(data) {
				var api_create_modal = $('#api_create_modal');
				api_create_modal.find('label.error').html('');

				data = $.parseJSON(data.responseText);
				if(data.msg != void 0 && data.msg != '') {
					api_create_modal.find('.common_msg .error').html(data.msg);
				}
				//input show validate
				var error_input = ['name', 'method', 'url', 'api_type'];
				$(error_input).each(function (i, input) {
					if(data[input] != void 0 && data[input] != '') {
						api_create_modal.find('label.' + input + '_error').html(data[input]);
					}
				});
				//param item validate
				api_create_modal.find('.request_container .request_element').each(function (i, e) {
					setMsgParam($(this), 'request', i);
				});
				api_create_modal.find('.response_container .response_element').each(function (i, e) {
					setMsgParam($(this), 'response', i);
				});

				function setMsgParam(elm, type, index) {
					var error_param = type + '.' + index  + '.param';
					var error_value = type + '.' + index  + '.value';
					var error_input_value = type + '.' + index  + '.input_value';

					if(data[error_param] != void 0 && data[error_param][0] != void 0 && data[error_param][0] != '') {
						var msg = data[error_param][0].replace(error_param, '{{ trans('field.parameter') }}');
						elm.find('label.param_error').html(msg);
					}
					if(data[error_value] != void 0 && data[error_value][0] != void 0 && data[error_value][0] != '') {
						var msg = data[error_value][0].replace(error_value, '{{ trans('field.api.value') }}');
						elm.find('label.value_error').html(msg);
					}
					if(data[error_input_value] != void 0 && data[error_input_value][0] != void 0 && data[error_input_value][0] != '') {
						var msg = data[error_input_value][0].replace(error_input_value, '{{ trans('field.api.value') }}');
						elm.find('label.value_error').html(msg);
					}
				}
			}

			global_datatable = $('#datatable_api').DataTable({
				info: false,
				processing: false,
				serverSide: true,
				ajax: {
					type: 'POST',
					url :'{!! route('bot.api.list', $connect_page_id) !!}',
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
					{ data: 'method', name: 'method' },
					{ data: 'url_param', name: 'url_param' },
					{ data: 'api_type', name: 'api_type' },
					{ data: 'action', name: 'action', orderable: false, searchable: false, width: '220px', visible: '{{ $_view_template_flg }}' }
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