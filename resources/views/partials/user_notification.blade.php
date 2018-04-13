@if(isset($_user_notification_list) && count($_user_notification_list) > 0)
    <span class="nav notify-row {{$class_header_top or ''}}">
    <!-- notification dropdown start-->
    <li id="header_notification_bar" class="dropdown">
        <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0)" aria-expanded="false">
            <i class="fa fa-bell-o"></i>
            <span class="badge bg-warning user-notification-cnt">{{isset($_user_notification_unreads) && count($_user_notification_unreads) > 0 ? count($_user_notification_unreads) : ''}}</span>
        </a>
        <ul class="dropdown-menu extended notification" id="notification-data">
            <li class="notification-name">
                <p>{{trans('user_notification.name')}}</p>
            </li>
            <div id="notification-content">
                @forEach($_user_notification_list as $item)
                    @php
                        $class_notification = 'alert-success alert-unread';
                        if(in_array($item->id, $_user_notification_reads)){
                            $class_notification = 'alert-read';
                        }
                    @endphp
                    <li class="user-notification-item" data-id="{{$item->id}}">
                        <div class="alert {{$class_notification}} clearfix">
                            <div class="noti-info">
                                <a class="title" href="javascript:void(0)">{{$item->title}}</a>
                            </div>
                            <span class="pull-right">
                            <span class="time pull-right" style="">{{$item->start_date}}</span>
                        </span>
                        </div>
                    </li>
                @endforeach
            </div>
        </ul>
    </li>
        <!-- notification dropdown end -->
</span>
    <script type="text/javascript">
        $(function(){
            $('.notify-row .user-notification-item').on('click', function () {
                var id = $(this).data('id');
                if(id != void 0 && id != ''){
                    var url = '{{route('userNotification.show', ['userNotification' => ':id'])}}';
                    url = url.replace(':id', id);
                    window.location.href = url;
                }
            });
            @if($_user_notification_list && count($_user_notification_list) > 6)
            $('.notify-row #notification-content').slimScroll();
            @endif
        });
    </script>
@endif