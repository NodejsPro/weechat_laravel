@extends('login')
@section('title') {{{ trans('default.reset_forget_password') }}} :: @parent @stop

@section('content')
    {!! Form::open(['url' => '/password/email', 'class' => 'form-signin', 'id' => 'form-mail-reset', 'role' => 'form']) !!}
    <div class=" card-box">
        <div class="panel-heading">
            <h3 class="text-center text-uppercase">{{{ trans('default.reset_forget_password') }}}<br></h3>
        </div>
        <div class="panel-body">
            @include('errors.list')
            {!! Form::text('email', null, ['id' => 'inputEmail', 'class' => "form-control", 'placeholder' => trans('field.email')]) !!}
            <button class="btn btn-lg btn-login btn-block" type="submit">{{{ trans('button.reset_btn') }}}</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#form-mail-reset").on('submit', function(e) {
                $(this).find(".btn-login").attr("disabled", true);
            });
        });
    </script>
@endsection

