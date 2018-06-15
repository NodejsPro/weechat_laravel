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
                                <th >{{{ trans('field.user_name') }}}</th>
                                <th >{{{ trans('field.phone') }}}</th>
                                <th>{{{trans('field.authority')}}}</th>
                                <th >{{{ trans('field.contact') }}}</th>
                                <th >{{{ trans('field.user_create') }}}</th>
                                <th >{{{ trans('field.action') }}}</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="clearfix group-add">
                        <a class="btn btn-info btn-add btn-create-user" href="{{route('user.create')}}">{{{ trans('button.add') }}}</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
    @include('modals.user_delete')
@endsection
@section('scripts2')
    <script></script>
    <script>
        $(document).ready(function () {
            global_datatable = $('#datatable_user').DataTable({
                info: false,
                processing: false,
                serverSide: true,
                ajax: {
                    type: 'POST',
                    url :'{!! route('user.list') !!}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },

                paging: true,
                searching: false,
//                searching: true,
                ordering:  true,
                "dom": '<"top"i>rt<"bottom pull-left"flp><"clear">',
                columns: [
                    {data: 'no', name: 'no', width: '5px'},
                    {
                        data: 'user_name',
                        name: 'user_name',
                        class: 'user_name',
                        mRender: function ( data, type, full ) {
                            return $("<div/>").html(data).text();
                        }
                    },
                    {data: 'phone', name: 'phone', class: 'phone'},
                    {data: 'authority', name: 'authority', class: 'authority'},
                    {data: 'contact', name: 'contact', class: 'contact'},
                    {data: 'user_create', name: 'user_create', class: 'user_create'},
                    {data: 'action', name: 'action', class: 'action'},
                ],
                language:
                    {
                        emptyTable: "<p class='pull-left'>{{trans('message.not_record')}}</p>",
                        zeroRecords: "<p class='pull-left'>{{trans('message.not_record')}}</p>",
                        paginate:
                            {
                                previous: "",
                                next: ""
                            }
                    },
                "pageLength": 10,
                "fnDrawCallback": function(oSettings) {
                    if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                        $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                    }else{
                        $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                    }
                },
                "bLengthChange": false,
                "bAutoWidth": false,
                scrollX: true,
                destroy: true
            });
        });
    </script>
@endsection