@extends('login')
@section('title'){{{ trans('modal.complete_unsubscribe') }}} :: @parent @stop
@section('content')
    <div class="row">
        <div class="card-box complete-unsubscribe-account">
            <div class="panel-heading">
                <h3 class="text-center text-uppercase text-complete-unsubscribe">{{{ trans('modal.complete_unsubscribe') }}}<br></h3>
            </div>
            <div class="panel-body page-not-found">
                {!! Form::label('name', trans('unsubscribe.complete_unsubscribe_header', ['system_name' => env('APP_NAME')]), ['class' => "label-unsubscribe"]) !!}
                <div class="unsubscribe-content">{{trans('unsubscribe.complete_unsubscribe_body', ['system_name' => env('APP_NAME')])}}</div>
                <a href="{{env('APP_TOP_URL')}}">{{trans('button.back_to_home')}}</a>
            </div>
        </div>
    </div>
@endsection
@section("scripts")
    <script>
    </script>
@endsection