@extends('login')
@section('title') {{{ trans('default.sign_up') }}} :: @parent @stop
@section('styles')
    <link href="{{ elixir('css/iCheck.css') }}" rel="stylesheet">
@endsection

@section('content')
    {!! Form::open(['url' => '/auth/register', 'class' => 'form-signin', 'id' => 'form-mail-sign-up', 'role' => 'form']) !!}
    <div class=" card-box">
        <div class="panel-heading">
            <h3 class="text-center text-uppercase">{{{ trans('default.sign_up') }}}<br></h3>
        </div>
        <div class="panel-body">
            @include('errors.list')
            {!! Form::text('email', null, ['id' => 'inputEmail', 'class' => "form-control cmxform form-horizontal", 'placeholder' => trans('field.email')]) !!}
            <div class="form-group">
                <div class=" icheck minimal">
                    <div class="checkbox single-row">
                        <input type="checkbox" style="width: 20px" class="checkbox form-control icheckbox_minimal" id="terms_of_use" {{empty(old('terms_of_use')) ? '' : 'checked'}} name="terms_of_use" value="{{config('constants.active.enable')}}" />
                    </div>
                </div>
                <label class="control-label label-terms">{!! trans('field.terms_of_use') !!}</label>
            </div>
            <button class="btn btn-lg btn-login btn-block" type="submit">{{{ trans('button.send') }}}</button>
            <hr style="margin-top: 22px; margin-bottom: 22px; border: 0; border-top: 1px solid #e4eaec;">
            <h5>{{trans('account.existed_account')}}&nbsp;<a href="{{ url('login') }}" style="cursor: pointer">{{trans('button.login')}}</a></h5>
        </div>
    </div>
    @include('modals.terms_of_use')
    {!! Form::close() !!}
@endsection
@section('scripts')
    <script src="{{ elixir('js/iCheck.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#form-mail-sign-up").on('submit', function(e) {
                $(this).find(".btn-login").attr("disabled", true);
            });
            $('.form-mail-sign-up #terms_of_use').iCheck('check');

            $('.label-terms .link').on('click', function () {
                $('#terms_of_use_modal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });
        });
    </script>
@endsection

