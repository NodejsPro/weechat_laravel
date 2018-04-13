<div id="nlp_entity_child_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content overlay-wrapper">
			<div class="modal-header">
				<h4 class="modal-title">{{{ trans('modal.entity_child_list') }}}</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<section class="panel">
						<div class="panel-body">
							<div class="box_message"></div>
							<div class="entity_child_container">
								<table class="table entity_child_list">
									<tbody></tbody>
								</table>
							</div>
						</div>
					</section>
				</div>
			</div>

			<div class="template_element hidden">
				<table>
					<tr class="entity_child_item">
						<td class="no"></td>
						<td class="name"></td>
						<td>
							<div class="todo-action-list">
								<a class="btn btn-danger btn-delete pull-right" data-button="" data-from="" href="javascript:void(0)">{{{ trans('button.delete') }}}</a>
							</div>
						</td>
					</tr>
					<tr class="not_record">
						<td colspan="4">{{trans('message.not_record')}}</td>
					</tr>
				</table>
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
@section('scripts4')
	<script type="text/javascript">
		$(function(){
			if ($.fn.slimScroll) {
				$('#nlp_entity_child_modal .entity_child_container').slimscroll({
					height: '400px',
					width: '100%',
					wheelStep: 20
				});
			}

			//show child entity list
			$(document).on('click', '.nlp_detail .btn-child-list', function (e) {
				var entity_parent_id = $(this).data('entity_parent_id');
				var entity_name = $(this).data('entity_name');
				var entity_type = $(this).data('entity_type');
				var entity_child = $(this).data('entity_child');

				if(entity_child != void 0 && entity_child != '' && entity_parent_id != void 0 && entity_parent_id != '' && entity_type != void 0 && entity_type != '') {
					fillEntityChildList(entity_child, entity_parent_id, entity_type);

					var nlp_entity_child_modal = $('#nlp_entity_child_modal');
					nlp_entity_child_modal.find('.overlay').show();
					nlp_entity_child_modal.find('.modal-title').html('{{ trans('modal.entity_child_list') }}' + ' ('  + entity_name + ')');
					nlp_entity_child_modal.find('.box_message').html('');
					nlp_entity_child_modal.modal({
						backdrop: true,
						keyboard: false
					});

					nlp_entity_child_modal.find('.overlay').hide();
				}
			});

			//fill data entity child to modal
			function fillEntityChildList(data, entity_parent_id, entity_type) {
				if(data != '') {
					data = data.split('|');
					if(data.length) {
						var nlp_entity_child_modal = $('#nlp_entity_child_modal');
						var entity_child_list = nlp_entity_child_modal.find('.entity_child_list tbody');
						entity_child_list.html('');

						$.each(data,function(i, e) {
							var entity_item = e.split(',');
							if(entity_item.length >= 2) {
								var entity_child_id = entity_item[0];
								var entity_child_name = entity_item[1];

								var entity_child_item = nlp_entity_child_modal.find('.template_element tr.entity_child_item').clone();
								entity_child_item.find('.no').html(i+1);
								entity_child_item.find('.name').html(entity_child_name);
								//delete button
								var btn_delete = entity_child_item.find('.btn-delete');
								var url = '{{ route('bot.nlp.entity.destroy', ['bot' => $connect_page_id, 'nlp_id' => $nlp->id, 'entity_id' => ':id', 'entity_type' => ':entity_type', 'entity_parent' => ':entity_parent']) }}';
								url = url.replace(':entity_parent', entity_parent_id);
								url = url.replace(':entity_type', entity_type + '_child');
								btn_delete.attr('data-button', entity_child_id).attr('data-from', url);

								entity_child_list.append(entity_child_item);
							}
						});
					}
					//entity hierachy  min child is 1
					if(data.length <= 1) {
						entity_child_list.find('.btn-delete').addClass('hidden');
					}
				}
			}
		});
	</script>
@endsection