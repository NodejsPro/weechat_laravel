@extends('login')
@section('title') 500 :: @parent @stop

@section('content')
    {!! Form::open(['url' => 'login', 'class' => 'form-signin', 'role' => 'form']) !!}
    <div class=" card-box">
        <div class="panel-heading">
            <h3 class="text-center text-uppercase">{{{ trans('message.500_msg') }}}<br></h3>
        </div>
        <div class="panel-body page-not-found">
            @if(Auth::check())
                <a href="{{ url('/') }}">{{{ trans('button.back_to_home') }}}</a>
            @else
                <a href="{{ url('login') }}" >{{{ trans('button.back_to_home') }}}</a>
            @endif
        </div>
    </div>
    {!! Form::close() !!}
@endsection