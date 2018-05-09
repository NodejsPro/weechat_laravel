<header class="header fixed-top clearfix">
    @if(ends_with(Route::currentRouteAction(), ['BotController@listbot', 'BotController@index', 'BotController@create', 'BotController@listUserBot', 'BotController@show', 'BotController@createLineBot', 'BotController@confirm', 'BotController@createWebEmbedBot', 'BotController@createChatworkBot'])
        || str_contains(Route::currentRouteAction(), ['UserController', 'TemplateController', 'PlanController', 'PaymentCardController', 'PaymentController', 'SupportController', 'UserNotificationController', 'PaymentGatewayController'])
    )
        <a href="{{URL::route('user.index')}}" class="logo system-logo-top">{{config('app.name')}}</a>
    @else
        <div class="brand brand-check">
            <div>
                <a href="{!! URL::route('bot.index') !!}" class="logo system-logo-main">
                    {{config('app.name')}}
                </a>
            </div>

            <div class="sidebar-toggle-box change-color">
                <div class="fa fa-bars"></div>
            </div>
        </div>
    @endif
    <div class="top-nav nav-center clearfix">
        <ul class="nav pull-right top-menu">
            <li class="dropdown">
                <a class="{{Request::is('demo') ? 'blue-color' : ''}}" href="{{ url('demo')}}"><span>{{{ trans('menu.demo')}}}</span></a>
            </li>
            <li class="dropdown">
                <a class="{{Request::is('user') || Request::is('user/*/edit') ? 'blue-color' : ''}}" href="{{ url('user')}}"><span>{{{ trans('menu.user_management')}}}</span></a>
            </li>
            <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                    <span class="username">{{{ trans('account.account_management')}}}</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu extended logout">
                    <li>
                        <a class="{{Request::is('account/edit') ? 'blue-color' : ''}}" href="{{ url('account/edit')}}"><span>{{{ trans('account.user_info') }}}</span></a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="{!! URL::to('logout') !!}"
                   onclick="event.preventDefault();
         document.getElementById('logout-form').submit();">
                    {{{ trans('menu.logout') }}}
                </a>
                <form id="logout-form"
                      action="{!! URL::to('logout') !!}"
                      method="POST"
                      style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
    </div>
</header>

