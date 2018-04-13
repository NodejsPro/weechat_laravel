<div id="nlp_label_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content overlay-wrapper">
			<div class="modal-header">
				<h4 class="modal-title">{{{ trans('modal.utterances_list') }}}</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<section class="panel">
						<div class="panel-body">
							<div class="box_message"></div>
							<div class="utterances_container">
								<table class="table utterances_list" id="datatable_nlp_label_intent">
									<thead class="hidden">
										<tr role="row">
											<th >No.</th>
											<th >{{{ trans('field.text') }}}</th>
											<th >{{{ trans('field.action') }}}</th>
										</tr>
									</thead>
								</table>
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
			</div>
		</div>
	</div>
</div>
@section('scripts3')
	<script type="text/javascript">
		$(function(){
			if ($.fn.slimScroll) {
				$('#nlp_label_modal .utterances_container').slimscroll({
					height: '550px',
					width: '100%',
					wheelStep: 20
				});
			}

			nlp_label_intent_datatable = $('#datatable_nlp_label_intent').DataTable({
				info: false,
				processing: false,
				serverSide: true,
				ajax: {
					type: 'POST',
					url :'{!! route('bot.nlp.label.intent.list', [$connect_page_id, $nlp]) !!}',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				},

				paging: true,
				searching: false,
				ordering:  true,
				"dom": '<"top"i>rt<"bottom pull-left"flp><"clear">',
				columns: [
					{ data: 'no', name: 'no', width: '50px' },
					{ data: 'text', name: 'text'},
					{data: 'action', name: 'action', orderable: false, searchable: false, width: '80px'}
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
				"pageLength": 500000,
				"bLengthChange": false,
				"bAutoWidth": false,
				destroy: true,
				"fnDrawCallback": function(oSettings) {
					if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
						$(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
					}else{
						$(oSettings.nTableWrapper).find('.dataTables_paginate').show();
					}

					//hide loading
					$('#nlp_label_modal .overlay').hide();
				}
			});
		});
	</script>
@endsection