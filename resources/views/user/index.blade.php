<?php ob_end_clean();?>
@extends('layouts.app2')
@section('title') {{{ trans('menu.user_management') }}} :: @parent @stop
@section('content')
    <div class="row">
        <div class="center-block user-list" style="float: none">
            <ul class="breadcrumb">
                <li><i class="fa fa-bars"></i> {{{ trans('menu.user_management') }}}</li>
            </ul>
            <section class="panel minimum-panel" style="position: relative">
                <header class="panel-heading">{{{ trans('title.user') }}}</header>
                <div class="panel-body table-responsive">
                    @include('flash')
                    <div class="box_message"></div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="inputSearch" placeholder="{{trans('account.enter_text_search')}}" style="width: 30%;">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="datatable_user">
                            <thead>
                            <tr role="row">
                                <th >No.</th>
                                <th >{{{ trans('field.email') }}}</th>
                                <th >{{{ trans('field.name') }}}</th>
                                <th >{{{ trans('field.company_name') }}}</th>
                                <th>{{{trans('field.authority')}}}</th>
                                <th>{{{trans('add_user.sns_type')}}}</th>
                                @if($login_user->authority == $authority['supper_admin'])
                                    <th >{{{ trans('field.user_number') }}}</th>
                                @endif
                                <th >{{{ trans('field.bot_number') }}}</th>
                                @if($login_user->authority == $authority['supper_admin'])
                                    <th >{{{ trans('field.user_create') }}}</th>
                                @endif
                                <th>{{{trans('field.white_list_domain')}}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix group-add">
                        <a class="btn btn-info btn-add btn-create-user" href="{{route('user.create')}}">{{{ trans('button.add') }}}</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
{{--    @include('modals.user_delete')--}}
@endsection
@section('scripts2')

@endsection