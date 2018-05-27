<div class="col-sm-3 left_conversation">
    <section class="panel minimum-panel panel_left_conversation">
        <?php
            $user_tab_active = 'all';
            ?>
        <header class="panel-heading"><span class="user_header_title" id="user_header_title">{{trans('menu.conversations')}} {{trans('conversation.match_user', ["count" =>1])}} </span><span class="pull-right mark_all_read">{{trans('field.mark_all_read')}}</span></header>
        <div class="panel-body panel-user">
            <div class="tab-content">
                @if(isset($user_profiles))
                    @foreach($user_profiles as $user_list_type => $user_profile)
                        <div class="tab-pane col-md-12 active" id="user_{{ $user_list_type }}">
                            <ul class="nav nav-pills nav-stacked mail-nav user_list_all">
                                @if(count($user_profile) > 0)
                                    @foreach($user_profile as $index => $user)
                                        @php
                                            $user_profile_pic = ('build/images/no_avatar.png');
                                        @endphp
                                        <li class="user_item" data-user_id="{{ $user->id }}" data-user_avatar="{{ $user_profile_pic }}">
                                            <a href="javascript:;">
                                                <span class="pull-left"><i class="fa fa-star icon_pin {{ $user->bookmark_flg == 1 ? 'active' : ''}}"></i></span>
                                                <img src="{{ $user_profile_pic }}" width="150" height="150">
                                                <p class="user_name">
                                                    {{trans('default.user').' '.($user->user_name)}}<br/>
                                                    {{$user->phone}}
                                                </p>
                                                <?php
                                                $last_time = $user->updated_at;
                                                $last_time = strtotime($last_time);
                                                if(isset($user->last_time_at)) {
                                                    $last_time = floor($user->last_time_at / 1000);
                                                }

                                                $date_format->setTimestamp($last_time);
                                                ?>
                                                <p class="user_date">{{$date_format->format($date_format_str.' H:i:s')}}</p>
                                                <div class="right_content pull-right">
                                                    <span class="badge bg-important pull-left notification notification_{{$user->_id}} {{(isset($user->unread_cnt) && $user->unread_cnt > 0)  ? '' : 'hide'}}">{{(isset($user->unread_cnt) && $user->unread_cnt > 0) ? $user->unread_cnt : ''}}</span>
                                                    <div class="more_info_box pull-right">
                                                        <span class="more_info pull-right" data-toggle="popover"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                    <p class="load_more_user" style="display: none">{{trans('field.load_more')}}</p>
                                @endif
                            </ul>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
</div>

