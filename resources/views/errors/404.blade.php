@extends('login')
@section('title') 404 :: @parent @stop

@section('content')
    {!! Form::open(['url' => 'logout', 'class' => 'form-signin', 'id' => 'form-signin', 'role' => 'form']) !!}
    <div class=" card-box">
        <div class="panel-heading">
            <h3 class="text-center text-uppercase">{{{ trans('message.404_msg') }}}<br></h3>
        </div>
        <div class="panel-body page-not-found">
            @if(Auth::check())
                @if(Auth::user()->authority == config('constants.authority.client'))
                    <a href="http://weechat.local.vn/logout" onclick="event.preventDefault(); document.getElementById('form-signin').submit();">
                        {{trans('menu.logout')}}
                    </a>
                @else
                    <a href="{{ url('/') }}">{{{ trans('button.back_to_home') }}}</a>
                @endif
            @else
                <a href="{{ url('login') }}" >{{{ trans('button.back_to_home') }}}</a>
            @endif
        </div>
    </div>
    {!! Form::close() !!}
@endsection


