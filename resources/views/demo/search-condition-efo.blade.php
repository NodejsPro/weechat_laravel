<section class="panel panel-filter">
    <div class="filter-content panel-body">
        <div class="form-horizontal cmxform">
            <div class="col-md-12" style="display: block">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{trans('conversation.starred')}}</span>
                    </div>
                    <div class="minimal">
                        <input type="checkbox" style="width: 20px" class="form-control checkbox bookmark_user" id="bookmark_user" name="bookmark" value="{{config('constants.active.enable')}}" />
                    </div>
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{trans('conversation.cv_flg')}}</span>
                    </div>
                    <div class="minimal">
                        <input type="checkbox" style="width: 20px" class="form-control checkbox cv_flg" id="cv_flg" name="cv_flg" value="{{config('constants.active.enable')}}" />
                    </div>
                    <button class="btn btn-info btn-search" id="btn-search">{{trans('button.conversation_search')}}</button>
                </div>
            </div>
        </div>
    </div>
</section>