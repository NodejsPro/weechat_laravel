<div id="nlp_create_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content overlay-wrapper  overlay-wrapper-luis-create">
			<div class="modal-header">
				<h4 class="modal-title">{{{ trans('modal.nlp_add') }}}</h4>
			</div>
			<div class="modal-body">
				<div class="row">
				{!! Form::open([ 'route' => ['bot.nlp.store', Route::current()->getParameter('bot')], 'method' => 'POST', 'class' => 'form-horizontal cmxform nlp_form col-md-12', 'role' => 'form']) !!}
					<div class="form-group">
						{!! Form::label('name', trans('field.name'), ['class' => "col-md-2 control-label required"]) !!}
						<div class="col-md-8">
							{!! Form::text('name', null, ['class' => 'form-control name']) !!}
							<label for="name" class="error name_error"></label>
						</div>
					</div>
					<div class="form-group group-culture">
						{!! Form::label('method', trans('field.culture'), ['class' => "col-md-2 control-label required"]) !!}
						<div class="col-md-4">
							{!! Form::select('culture', config('luis.culture_nlp'), null, ['class' => 'form-control culture', 'style' => 'width: 100%']) !!}
							<label for="name" class="error culture_error"></label>
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('name', trans('field.description'), ['class' => "col-md-2 control-label"]) !!}
						<div class="col-md-8">
							{!! Form::textarea(
                                'description', null, ['id' => 'inputDescription','class' => 'form-control description', 'rows' => '6'])
                            !!}
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
			<div class="overlay">
				<i class="fa fa-refresh fa-spin fa-2x"></i>
			</div>
		</div>
	</div>
</div>
@section('scripts2')
	<script type="text/javascript">
		$(document).ready(function () {
			var url_nlp_store 	= '{{route('bot.nlp.store', $connect_page_id)}}',
				url_nlp_update 	= '{{ URL::route('bot.nlp.update', [$connect_page_id, ':nlp_id']) }}',
				url_nlp_edit 	= '{{ URL::route('bot.nlp.edit', [$connect_page_id, ':nlp_id']) }}',
				url_nlp_train 	= '{{ URL::route('bot.nlp.train', [$connect_page_id, ':nlp_id']) }}',
				nlp_id  		= '';
			$('.nlp_index .overlay-wrapper-luis-create .overlay').hide();

            $('.culture').select2({
                "language": {
                    "noResults": function(){
                        return "{{trans('message.no_results_found')}}";
                    }
                },
                minimumResultsForSearch: -1
            });

			$('.nlp_index .btn-create-nlp').on('click', function () {
				resetCrtModal(true);
				$('#nlp_create_modal').modal({
					backdrop: 'static',
					keyboard: false
				});
			});

            $(document).on('click', '.nlp_index .btn_nlp_update', function () {
                resetCrtModal();
                $('#nlp_create_modal .modal-title').html('{{{ trans('modal.nlp_edit') }}}');
                nlp_id = $(this).data('id');
                if(nlp_id != void 0 && nlp_id != '') {
                    var url_nlp_get = url_nlp_edit.replace(':nlp_id', nlp_id);
                    getEditData(url_nlp_get);
                }
            });

			function resetCrtModal (isCreate) {
				nlp_id = '';
				var nlp_create_modal = $('#nlp_create_modal');
				nlp_create_modal.find('.overlay-wrapper-luis-create .overlay').hide();
				nlp_create_modal.find('form.nlp_form').show();
				//clear old data
				nlp_create_modal.find('input.name, textarea.description').val('');
                if(isCreate != void 0 && isCreate){
                    $('.group-culture').show();
                    $('.culture').val('{{key(config('luis.culture_nlp'))}}').trigger('change');
                } else{

                    $('.group-culture').hide();
                }
                nlp_create_modal.find('label.error').html('');
				nlp_create_modal.find('.modal-title').html('{{{ trans('modal.nlp_add') }}}');
			}

			function sendCrtData(isCreate, url) {
				var nlp_create_modal = $('#nlp_create_modal');
				var method = "POST";
				if(!isCreate){
					method = "PUT";
				}
				$.ajax({
					url: url,
					data: nlp_create_modal.find('form.nlp_form').serializeArray(),
					type: method,
					success: function(data) {
					    if(data.success != void 0 && data.success){
                            nlp_create_modal.modal('hide');
                            setMesssage('{{ trans('message.save_success', ['name' => trans('default.nlp')]) }}', 2, $('.nlp_index .box_message'));
                            if(isCreate != void 0 && isCreate){
                                $('.btn-create-nlp').show();
                                if(data.status_add_app != void 0 && !data.status_add_app){
                                    $('.btn-create-nlp').hide();
								}
                            }
                            global_datatable.ajax.reload(null, false);
                        } else{
					        var message = data.errors !== void 0 && data.errors.msg !== void 0 ? data.errors.msg : '';
                            setMesssage(message);
                            nlp_create_modal.find('.overlay-wrapper-luis-create .overlay').hide();
						}
					},
					error: function(result){
						showErrorMsg(result);
						nlp_create_modal.find('.overlay-wrapper-luis-create .overlay').hide();
					}
				});
			}

			function getEditData(url){
				var nlp_create_modal = $('#nlp_create_modal');
				$.ajax({
					url: url,
					type: 'GET',
					success: function(data) {
                        if(data.nlp != void 0) {
							setEditModal(data.nlp);
						}
						$('#nlp_create_modal').modal({
							backdrop: 'static',
							keyboard: false
						});
					},
					error: function(result){
						nlp_create_modal.find('form.nlp_form').hide();
						showErrorMsg(result);
					}
				});
			}

			function setEditModal(data) {
				var nlp_create_modal = $('#nlp_create_modal');
				var error_input 	 = ['name', 'culture', 'description'];
                $(error_input).each(function (i, input) {
                    if(data[input] != void 0 && data[input] != '') {
                        var elm = nlp_create_modal.find('.' + input);
                        elm.val(data[input]);
						if(elm.is("select")) {
                            elm.trigger('change.select2');
						}
					}
				});
			}

			function showErrorMsg(data) {
				var nlp_create_modal = $('#nlp_create_modal');
                nlp_create_modal.find('label.error').html('');

                data = $.parseJSON(data.responseText);
                if(data.errors != void 0 && data.errors.msg != void 0) {
                    $('#nlp_create_modal').modal('hide');
                    setMesssage(data.errors.msg);
                }
				//input show validate
				var error_input = ['name', 'culture'];
				$(error_input).each(function (i, input) {
					if(data[input] != void 0 && data[input] != '') {
						nlp_create_modal.find('label.' + input + '_error').html(data[input]);
					}
				});
			}

            $('#nlp_create_modal .btn-modal-add').on('click', function () {
                $('#nlp_create_modal .overlay-wrapper-luis-create .overlay').show();
                var isCreate 	 = true,
                    url_nlp_sent = url_nlp_store;
                if(nlp_id != void 0 && nlp_id != '') {
                    url_nlp_sent = url_nlp_update.replace(':nlp_id', nlp_id);
                    isCreate 	 = false;
                }
                sendCrtData(isCreate, url_nlp_sent);
            });

			$(document).on('click', '.btn_nlp_train', function () {
                nlp_id = $(this).data('id');
                var current_train = $(this);
                var status = $(this).attr('disabled');
                if(nlp_id != void 0 && nlp_id != '' && status == void 0) {
                    $('.nlp_index .overlay-wrapper-lui-list .overlay').show();
                    var url = url_nlp_train.replace(':nlp_id', nlp_id);
                    $.ajax({
                        url: url,
                        type: 'POST',
                        success: function(data) {
                            if(data.success != void 0 && data.success){
                                setMesssage(data.msg, 2, $('.nlp_index .box_message'));
                                if(data.train_status != void 0 && data.train_status == '{{config('constants.train_status.training')}}'){
                                    $('.train_status_' + nlp_id).html('{{trans('field.training')}}');
                                    $(current_train).attr('disabled', '');
                                } else if(data.train_status != void 0 && data.train_status == '{{config('constants.train_status.trained')}}'){
                                    $('.train_status_' + nlp_id).html('{{trans('field.trained')}}');
                                    $(current_train).removeAttr('disabled');
                                }
                            }
                            $('.nlp_index .overlay-wrapper-lui-list .overlay').hide();
                        },
						error: function(result){
							$('.nlp_index .overlay-wrapper-lui-list .overlay').hide();
                            var text = $.parseJSON(result.responseText);
                            if(text != void 0 && text.msg != void 0){
                                setMesssage(text.msg, 1);
                            }
						}
                    });
                }
            });

            global_datatable = $('#datatable_nlp').DataTable({
                info: false,
                processing: false,
                serverSide: true,
                ajax: {
                    type: 'POST',
                    url :'{!! route('bot.nlp.list', $connect_page_id) !!}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },

                paging: true,
                searching: false,
                ordering:  false,
                "dom": '<"top"i>rt<"bottom"flp><"clear">',
                columns: [
                    { data: 'no', name: 'no', width: '50px' },
                    { data: 'name', name: 'name' },
                    { data: 'culture', name: 'culture' },
                    { data: 'app_name', name: 'app_name' },
                    { data: 'app_id', name: 'app_id' },
                    { data: 'description', name: 'description' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '300px', visible: '{{ $_view_template_flg }}' }
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