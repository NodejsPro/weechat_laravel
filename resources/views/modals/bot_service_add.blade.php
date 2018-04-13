<div class="modal show-service-modal" id="show-service-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title">{{trans('modal.bot_add')}}</h4>
            </div>
            @if(isset($service_list) && count($service_list) >0)
            <div class="modal-body modal-scroll">
                <div class="box_message"></div>
                <div class="row">
                    {!! Form::open([ 'route' => ['bot.serviceAdd'],  'id' => 'frmServiceAdd','method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                        {!! Form::hidden('service_item_key', null, ['id' => 'service-item-select','class' => 'form-control']) !!}
                    @php
                        $group_service_key = config('constants.group_type_service_key');
                        $group_service = config('constants.group_type_service');
                        if(!$_add_service_for_plan_flg){
                            if(isset($service_list[$group_service['web_embed_efo']])){
                                unset($service_list[$group_service['web_embed_efo']]);
                            }
                            if(isset($service_list[$group_service['chatwork']])){
                                unset($service_list[$group_service['chatwork']]);
                            }
                        }
                    @endphp
                        @foreach($service_list as $service_code => $service)
                            <div class="col-md-12">
                                <div class="feed-box service-item-content {{$service['active_flg'] ? '' : 'service-hidden'}}" data-key="{{$service_code}}">
                                    <section class="panel">
                                        <div class="panel-body">
                                            <div class="img-content">
                                                <img alt="" src="{{('/images/'.array_search($service_code, $service_type).'.png')}}"/>
                                            </div>
                                            <div class="bot-item-content">
                                                <h1 class="service-name">{{$service['lang']}}</h1>
                                                <div class="service-description">
                                                    <div class="col-md-10 div-content service-description-content">
                                                        <p>{!! trans('message.service_'. strtolower(@$group_service_key[$service_code]) .'_description') !!}</p>
                                                        @if(!isset($service['active_flg']) || !$service['active_flg'])
                                                            <p>{!! trans('message.service_not_develop') !!}</p>
                                                        @endif
                                                    </div>
                                                    @if(isset($service['active_flg']) && $service['active_flg'])
                                                        <div class="col-md-2 div-content service-action">
                                                            <button class="btn btn-info btn-modal-add" data-service-key="{{$service_code}}">{{trans('button.add')}}</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        @endforeach
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left btn-modal-close" data-dismiss="modal">{{{ trans('button.close')}}}</button>
                <button type="button" class="btn btn-info btn-modal-service-compare" style="display: none">{{{ trans('button.service_compare')}}}</button>
            </div>
            @endif
        </div>
    </div>
</div>
<script type="text/javascript">
    var key_selection = '';
    $(document).ready(function () {
        @if(!empty($service_list) && count($service_list) > 3)
            $('#show-service-modal .modal-scroll').slimScroll({
                'height' : 'calc(100vh - 200px)'
            });
        @endif
        $('#bot_service_add').on('click', function () {
            var result = false;
            @if(empty($service_list))
                setMesssage('{{trans('message.service_not_registered')}}');
                $("html, body").animate({ scrollTop: 0 }, "fast");
            @else
                var url = '{{route('bot.checkBotLimit')}}';
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        if(data.success != void 0){
                            if(!data.success){
                                setMesssage('{{trans('message.bot_add_limit')}}');
                            } else {
                                result = true;
                                init();
                            }
                        }else{
                            setMesssage('{{trans('message.common_error')}}');
                        }
                    },
                    error: function(data){
                    },
                    complete: function(data){
                        if(!result) {
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                        }
                    }
                });
            @endif
        });
        $('.service-item-content').on('click', function () {
            $('.service-item-content').removeClass('active');
            key_selection = $(this).attr('data-key');
            $('input[name="service_item_key"]').val(key_selection);
            $(this).addClass('active');
        });
        $('.btn-modal-add').on('click', function () {
            var key = $(this).attr('data-service-key');
            if(key != void 0 ){
                $('input[name="service_item_key"]').val(key);
                $('#frmServiceAdd').submit();
            }
        })
    });
    function init() {
        $('input[name="service_item_key"]').val('');
        $('.service-item-content').removeClass('active');
        key_selection = '';
        setMesssage('');
        $('#show-service-modal').modal({
            backdrop: 'static',
            keyboard: false,
        });
    }
    function setMessageModal(message) {
        if (message != '' && message != null) {
            $('#show-service-modal .box_message').html('<p class="alert alert-danger">' + message + ' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>');
        } else {
            $('#show-service-modal .box_message').html('');
        }
    }
</script>
