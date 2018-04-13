@extends('login')
@section('content')
    {!! Form::open(['url' => 'login', 'class' => 'form-signin', 'role' => 'form']) !!}
    <div class=" card-box">
        <div class="panel-heading">
            <h4 class="text-center text-uppercase">{{{ trans('message.link_register_actived') }}}<br></h4>
        </div>
        <div class="panel-body page-not-found">
            <a href="{{ url('login') }}" >{{{ trans('button.back_to_home') }}}</a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection


