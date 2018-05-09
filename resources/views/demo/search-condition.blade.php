<section class="panel panel-filter">
    <div class="filter-content panel-body">
        <div class="form-horizontal cmxform">
            @if($connect_page->sns_type != $web_embed_sns)
                <div class="col-md-12">
                    <div class="input_box col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{  trans('conversation.user_name') }}</label>
                            {!! Form::text('user_name', null, ['class' => 'form-control user_name']) !!}
                            <label for="name" class="error user_name_error"></label>
                        </div>
                    </div>
                    <div class="check_box col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{trans('conversation.starred')}}</label>
                            <div class="minimal">
                                <input type="checkbox" class="form-control bookmark_user" id="bookmark_user" name="bookmark" value="{{config('constants.active.enable')}}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="button_box col-md-12">
                    <div class="form-group">
                        <button class="btn btn-default btn-clear">{{trans('button.clear')}}</button>
                        <button class="btn btn-info btn-search" id="btn-search">{{trans('button.conversation_search')}}</button>
                    </div>
                </div>
            @else
                <div class="web_filter_box col-md-12">
                    <div class="check_box">
                        <div class="form-group">
                            <label class="control-label">{{trans('conversation.starred')}}</label>
                            <div class="minimal">
                                <input type="checkbox" class="form-control bookmark_user" id="bookmark_user" name="bookmark" value="{{config('constants.active.enable')}}" />
                            </div>
                        </div>
                    </div>
                    <div class="button_box form-group ">
                        <button class="btn btn-info btn-search" id="btn-search">{{trans('button.conversation_search')}}</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>