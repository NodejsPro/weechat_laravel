@extends('layouts.app')
@section('title') {{ isset($title) ? $title : '' }} :: @parent @stop

@section('content')
    <section class="panel">
        <header class="panel-heading">{{{ isset($title) ? $title : '' }}}</header>
        <div class="panel-body">
            <p>{{{ trans('message.change_plan_to_using_function') }}}</p>
            <br/><br/>
            <a class="btn btn-info" href="{{ route('plan.index') }}">{{{ trans('button.plan_setting') }}}</a>
        </div>
    </section>
@endsection


