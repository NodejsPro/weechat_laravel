<div id="role_create_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content overlay-wrapper ">
			<div class="modal-header">
				<h4 class="modal-title">{{{ trans('modal.bot_role_create') }}}</h4>
			</div>
			<div class="modal-body">
				<div class="row role_content">
					<div class="form-inline" role="form"><br/>
						<div class="col-md-12">
							<div class="form-group form-group-clear">
								{!! Form::label('bot_role_email', trans('field.bot_role_email'), ['class' => "control-label col-md-2 label-email required",  "for" => 'bot_role_email']) !!}
								<div class="col-md-10">
									<div class="row">
										<div class="col-md-8 col-email">
											{!! Form::text('email', null, ['class' => 'form-control  email bot_role_email' , 'id' => 'bot_role_email']) !!}
										</div>
										<div class="col-md-3 group-authority">
											{!! Form::select('authority', $bot_role_authority, null, ['class' => 'form-control item_authority_selected', 'style' => 'width: 100%']) !!}
										</div>
										<div for="name" class="col-md-12 email_error"></div>
										<div class="col-md-12 bot_role_description">{{trans('message.bot_role_create_description')}}</div>
										<input type="hidden" name="inputItemAuthority" id="inputItemAuthority">
									</div>
								</div>
							</div>
							<br/>
							<br/>
						</div>
					</div>
				</div>
				<div class="row common_error">
					<div class="col-md-12">
						<p class="text-red">
							{{{ trans("message.common_error") }}}
						</p>
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-modal-close pull-left" data-dismiss="modal">{{{ trans('button.close') }}}</button>
				<button class="btn btn-info btn-modal-add">{{{ trans('button.add') }}}</button>
				<button class="btn btn-info btn-modal-edit">{{{ trans('button.add') }}}</button>
			</div>
			<div class="overlay">
				<i class="fa fa-refresh fa-spin fa-2x"></i>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var url_create = '{{URL::route('bot.role.store', ['bot' => $connect_page_id])}}',
			role_item,
			role_id;

        $('.item_authority_selected').select2({
            "language": {
                "noResults": function(){
                    return "{{trans('message.no_results_found')}}";
                }
            },
            minimumResultsForSearch: -1
        });

        $('#role_create').on('click', function () {
            resetCtr(true);
            showModal();
        });

        $(document).on( 'click','.role_list .btn-edit', function () {
            role_id = $(this).attr('data-button');
            var url = '{{URL::route('bot.role.edit', ['bot' => $connect_page_id, 'role' => ':role_id'])}}';
            url = url.replace(':role_id', role_id);
            role_item = $(this).parents('.role-item');
            resetCtr(false);
            getEditData(url);
        });

        $('#role_create_modal .btn-modal-edit').on('click', function () {
            var url = '{{URL::route('bot.role.update', ['bot' => $connect_page_id, 'role' => ':role_id'])}}';
            url = url.replace(':role_id', role_id);
			sendCrtData(false, url);
        });

        $('#role_create_modal .btn-modal-add').on('click', function () {
			console.log('btn-modal-add click');
            $('#role_create_modal .overlay-wrapper .overlay').show();
            sendCrtData(true, url_create);
        });

        function resetCtr(isCreate) {
            setMesssage('');
            $('#role_create_modal .overlay-wrapper .overlay').hide();
            $('#bot_role_email').val('');
            $('#role_create_modal .email_error, #role_create_modal .common_error').hide();
            $('#role_create_modal .role_content').show();
            $('#role_create_modal .item_authority_selected').val('{{config('constants.bot_role_authority.editor')}}').trigger('change');
            var modal_title = $('#role_create_modal .modal-title');
            if(isCreate){
                modal_title.html('{{trans('modal.bot_role_create')}}');
                $('.btn-modal-edit').hide();
                $('.btn-modal-add').show();
                $('#bot_role_email').removeAttr('disabled');
            }else{
                $('.btn-modal-edit').show();
                $('.btn-modal-add').hide();
                $('#bot_role_email').attr('disabled', 'disabled');
                modal_title.html('{{trans('modal.bot_role_edit')}}');
            }
        }

		function sendCrtData(isCreate, url) {
            var bot_role_authority = <?php echo $bot_role_authority?>;
		    var method = 'POST';
		    if(!isCreate){
		        method = 'PUT';
			}
            $.ajax({
                url: url,
                data: {
                    '_token': "{{ csrf_token() }}",
                    'email': $('#bot_role_email').val(),
                    'bot_role_authority' : $('#role_create_modal .item_authority_selected').val(),
                },
                type: method,
                success: function(result) {
                    var data = [];
                    if(result.bot_role != void 0){
                        var bot_role = result.bot_role;
                        if(isCreate){
                            data.push(bot_role);
                            showData(data);
                            checkPlan();
                        }else{
                            setEditModal(bot_role);
                            if(bot_role.authority != void 0){
                                role_item.find('.role-authority').html(bot_role_authority[bot_role.authority]);
                            }
                        }
					}
					if(result.msg != void 0){
                        setMesssage(result.msg, 2);
					}
                    $('#role_create_modal .overlay-wrapper .overlay').hide();
                    $('#role_create_modal').modal('hide');
                },
                error: function(result){
                    $('#role_create_modal .overlay-wrapper .overlay').hide();
                    var errors = $.parseJSON(result.responseText);
                    //save common error
                    var msg = '';
                    if(errors.errors != void 0 && errors.errors.msg != void 0) {
                        msg = errors.errors.msg;
                    }
                    if(errors.email != void 0 && errors.email[0] != void 0){
                        msg = errors.email[0];
                    }
                    if(errors.common_error != void 0){
                        $('#role_create_modal .common_error').show();
                        $('#role_create_modal .common_error .text-red').html(errors.common_error).show();
                        $('#role_create_modal .role_content, #role_create_modal .btn-modal-add').hide();
					}
                    if(msg != '' && isCreate){
                        $('#role_create_modal .email_error').html(msg).show();
                    } else if(msg != ''){
                        setMesssage(msg);
                        $('#role_create_modal').modal('hide');
					}
                }
            });
        }

        function getEditData(url){
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    $('.overlay-wrapper .overlay').hide();
                    if(data != void 0 && data.bot_role != void 0){
                        setEditModal(data.bot_role);
                        showModal();
                    }
                },
                error: function(result){
                    $('#role_create_modal .overlay-wrapper .overlay').hide();
                    var errors = $.parseJSON(result.responseText);
                    //save common error
                    var msg = '';
                    if(errors.errors != void 0 && errors.errors.msg != void 0) {
                        msg = errors.errors.msg;
                    }
                    if(msg != ''){
                        setMesssage(msg);
                    }
                }
            });
        }

        function setEditModal(data){
            $('#bot_role_email').val(data.user_email).attr('disabled', 'disabled');
            $('#role_create_modal .item_authority_selected').val(data.authority).trigger('change');
        }

        function showModal(){
            $('.overlay-wrapper .overlay').hide();
            $('#role_create_modal').modal({
                backdrop: 'static',
                keyboard: false,
            });
        }

    });

    function showData(data, is_empty) {
        var bot_role_authority = <?php echo $bot_role_authority?>;
        var role_content = $('.role_index .role-content');
        if(is_empty != void 0 && is_empty){
            role_content.empty();
        }
        $(data).each(function (index, item) {
            var picture,
                id = item._id,
                role = $('.role_index .role-item-sample .role-item').clone();
            if(item.picture == void 0){
                picture = '{{elixir('images/no_avatar.png')}}';
            } else {
                picture = item.picture
            }
            if(item.confirmed_at == void 0){
                role.find('.role-status').html('{{trans('default.bot_role_status_pending')}}')
            }
            role.find('.role-email').html(item.user_email);
            if(item.user_name != void 0){
                role.find('.role-name').html(item.user_name)
            }
            if(item.authority != void 0){
                role.find('.role-authority').html(bot_role_authority[item.authority]);
            }
            role.find('.image').attr({'src' : picture});
            role.find('.btn-delete, .btn-edit').attr({'data-button' : id});
            if(item.status_flg != void 0 && item.status_flg){
                role.find('.btn-delete, .btn-edit').hide();
            }
            role_content.append(role);
        });

    }

    function checkPlan(bot_roles) {
        $('#role_create').show();
				@if(isset($plan) && $plan->max_admin != config('constants.plan.unlimit') && is_numeric($plan->max_admin))
        var max_bot_role = '{{$plan->max_admin}}',
            role_length;
        if(bot_roles != void 0){
            role_length = bot_roles.length;
		}else{
            role_length = $('.bot_setting_index .role_list .role-item').length;
		}
        if(role_length >= max_bot_role){
            $('#role_create').hide();
        }
		@endif
    }
</script>