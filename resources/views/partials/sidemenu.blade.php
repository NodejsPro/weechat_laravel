<?php
$connect_page_id =  Route::current()->getParameter('bot');
$connect_page    = ConnectPage::where('_id', $connect_page_id)->first();
$scenario_id_current = Route::current()->getParameter('scenario');
$scenario_group_id_current = '';
if (isset($scenario_current->group_id)){$scenario_group_id_current = $scenario_current->group_id;}
$function_rule = config('constants.function_rule');
if(isset($connect_page->picture)){
    if($_is_template_flg){
        $bot_picture = $_destination . '/template/' . $connect_page->picture;
    }elseif($connect_page->sns_type == config('constants.group_type_service.web_embed') || $connect_page->sns_type == config('constants.group_type_service.web_embed_efo') || $connect_page->sns_type == config('constants.group_type_service.line')){
        $bot_picture = $_destination . '/bot_picture/' . $connect_page->picture;
    }else{
        $bot_picture = $connect_page->picture;
    }
}else {
    $bot_picture = $_destination .'/bot_picture/default_'.config('constants.group_type_service_key')[$connect_page->sns_type].'.png';
}
?>
<aside class="main-sidebar">
    <div id="sidebar" class="nav-collapse">
        <div class="user-panel">
            <img src="{{ $bot_picture }}" class="img-circle">
            <div class="feed-box">
                <img class="bot_type" src="/images/{{ config('constants.group_type_service_key.'.$connect_page->sns_type)}}.png" alt="">
            </div>
            <p class="bot-name-header" title="{{ @$connect_page->page_name }}">{{ @$connect_page->page_name }}</p>
        </div>
        <!-- sidebar menu start-->
        <div class="leftside-navigation" tabindex="5000" style="overflow: hidden; outline: none;">
            <ul class="sidebar-menu" id="nav-accordion">

                @if(isset($_sns_rule) && count($_sns_rule))
                    <?php
                    $menu_icon = [
                        'scenario' => 'fa-comments',
                        'menu' => 'fa-bars',
                        'notification' => 'fa-location-arrow',
                        'file' => 'fa-file',
                        'library' => 'fa-book',
                        'variable' => 'fa-chain',
                        'api' => 'fa-connectdevelop',
                        'mail' => 'fa-envelope',
                        'nlp' => 'fa fa-spinner',
                        'conversation' => 'fa-comments',
                        'report' => 'fa-bar-chart-o',
                        'cv' => 'fa-bar-chart-o',
                    ];
                    $lang        = Lang::locale();
                    $column_lang = 'name_'.$lang;
                    ?>
                    @foreach($_sns_rule as $rule_name => $rule)
                        @if($rule['code'] == $function_rule['scenario'])
                            <li data-help_content="{{{ trans('menu.info_scenario') }}}" class="sub-menu">
                                <a href="{!! URL::route('bot.scenario.index', $connect_page_id) !!}" class="{{ Request::is('bot/*/scenario') || Request::is('bot/*/scenario/*/edit') || Request::is('bot/*/scenario/preview') ? 'active' : '' }} sidebar-check"><i class="fa {{ @$menu_icon[$rule_name] }}"></i> <span>{{{ $rule->$column_lang }}}</span></a>
                                <ul class="sub">
                                    <li class="{{ Request::is('bot/*/scenario') || Request::is('bot/*/scenario/preview') ? 'active' : '' }}">
                                        <a href="{!! URL::route('bot.scenario.index', $connect_page_id) !!}">{{{ trans('menu.scenario_list') }}}</a>
                                    </li>
                                    @if(isset($_scenario_list) && count($_scenario_list))
                                        @foreach($_scenario_list as $scenario)
                                            @if(!isset($scenario->group_id) || (isset($scenario->group_id) && $scenario->group_id == ""))
                                                <li class="{{ ($scenario_id_current == $scenario->id) ? 'active' : '' }}">
                                                    <a href="{!! URL::route('bot.scenario.edit', [$connect_page_id, $scenario->id]) !!}">{{{ $scenario->name }}}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if(isset($_scenario_group_list) && count($_scenario_group_list))
                                        @foreach($_scenario_group_list as $scenario_group)
                                            <li class="sub-menu dcjq-parent-li {{($scenario_group_id_current == $scenario_group->id) ? 'active' : ''}}">
                                                <a href="javascript:;" class="dcjq-parent">
                                                    <span>{{$scenario_group->name}}</span>
                                                    <span class="dcjq-icon"></span>
                                                </a>
                                                @if(isset($_scenario_list) && count($_scenario_list))
                                                    <ul class="sub" style="display: block;">
                                                        @foreach($_scenario_list as $scenario)
                                                            @if(isset($scenario->group_id) && $scenario->group_id && $scenario->group_id == $scenario_group->id)
                                                                <li class="{{ ($scenario_id_current == $scenario->id) ? 'active' : '' }}">
                                                                    <a href="{!! URL::route('bot.scenario.edit', [$connect_page_id, $scenario->id]) !!}">{{{ $scenario->name }}}</a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                        @else
                            <!--Ignore slot and some menu if Bot is template-->
                            @if($rule['code'] == $function_rule['slot'] || $rule['code'] == @$function_rule['report2'])
                                    @continue
                            @endif
                            {{--efo report conversation--}}
                            @if($connect_page->sns_type == config('constants.group_type_service.web_embed_efo') && $rule['code'] == $function_rule['report'])
                                @php
                                    $rule_name = 'cv';
                                @endphp
                            @endif

                            <li data-help_content="{{{ trans('menu.info_'.($rule_name == 'cv' ? 'report' : $rule_name)) }}}">
                                <a href="{!! URL::route('bot.'.$rule_name.'.index', $connect_page_id) !!}" class="{{Request::is('bot/*/'.$rule_name) || Request::is('bot/*/'.$rule_name.'/*') ? 'active' : ''}} sidebar-check"><i class="fa {{ @$menu_icon[$rule_name] }}"></i> <span>{{{ $rule->$column_lang }}}</span></a>
                            </li>
                        @endif
                    @endforeach
                @endif
                @if(!$_is_template_flg)
                    <li data-help_content="{{{ trans('menu.info_bot_setting') }}}" class="sub-menu dcjq-parent-li">
                        @if(isset($connect_page) && $connect_page->sns_type == config('constants.group_type_service.line'))
                            <a href="{!! action('BotSettingController@botSetting', $connect_page_id) !!}" class="dcjq-parent {{Request::is('botSetting/*') ? 'active' : ''}} sidebar-check"><i class="fa fa-gavel"></i> <span>{{{ trans('menu.bot_setting') }}}</span></a>
                        @elseif(isset($connect_page) && $connect_page->sns_type == config('constants.group_type_service.chatwork'))
                            <a href="{!! action('BotSettingController@botSetting', $connect_page_id) !!}" class="dcjq-parent {{Request::is('botSetting/*') ? 'active' : ''}} sidebar-check"><i class="fa fa-gavel"></i> <span>{{{ trans('menu.bot_setting') }}}</span></a>
                        @else
                        <a href="{!! action('BotSettingController@botInfo', $connect_page_id) !!}" class="dcjq-parent {{Request::is('botSetting/*') ? 'active' : ''}} sidebar-check"><i class="fa fa-gavel"></i> <span>{{{ trans('menu.bot_setting') }}}</span></a>
                        @endif
                    </li>
                @endif
            </ul></div>
        <!-- sidebar menu end-->
        <div id="ascrail2000" class="nicescroll-rails" style="width: 3px; z-index: auto; cursor: default; position: absolute; top: 0px; left: 237px; height: 253px; opacity: 0; display: block;"><div style="position: relative; top: 0px; float: right; width: 3px; height: 62px; border: 0px solid rgb(255, 255, 255); border-radius: 0px; background-color: rgb(31, 181, 173); background-clip: padding-box;"></div></div><div id="ascrail2000-hr" class="nicescroll-rails" style="height: 3px; z-index: auto; top: 250px; left: 0px; position: absolute; cursor: default; display: none; width: 237px; opacity: 0;"><div style="position: relative; top: 0px; height: 3px; width: 240px; border: 0px solid rgb(255, 255, 255); border-radius: 0px; background-color: rgb(31, 181, 173); background-clip: padding-box;"></div></div></div>
</aside>


