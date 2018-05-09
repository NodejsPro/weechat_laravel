<?php
$bot_picture = '/bot_picture/default_'.config('constants.group_type_service_key').'.png';
?>
<aside class="main-sidebar">
    <div id="sidebar" class="nav-collapse">
        <div class="user-panel">
            <img src="{{ $bot_picture }}" class="img-circle">
            <div class="feed-box">
                <img class="bot_type" src="/images/{{ config('constants.group_type_service_key.')}}.png" alt="">
            </div>
            <p class="bot-name-header" title="{{ @$connect_page->page_name }}">{{ @$connect_page->page_name }}</p>
        </div>
        <!-- sidebar menu start-->
        <div class="leftside-navigation" tabindex="5000" style="overflow: hidden; outline: none;">
            <ul class="sidebar-menu" id="nav-accordion">
            </ul></div>
        <!-- sidebar menu end-->
        <div id="ascrail2000" class="nicescroll-rails" style="width: 3px; z-index: auto; cursor: default; position: absolute; top: 0px; left: 237px; height: 253px; opacity: 0; display: block;"><div style="position: relative; top: 0px; float: right; width: 3px; height: 62px; border: 0px solid rgb(255, 255, 255); border-radius: 0px; background-color: rgb(31, 181, 173); background-clip: padding-box;"></div></div><div id="ascrail2000-hr" class="nicescroll-rails" style="height: 3px; z-index: auto; top: 250px; left: 0px; position: absolute; cursor: default; display: none; width: 237px; opacity: 0;"><div style="position: relative; top: 0px; height: 3px; width: 240px; border: 0px solid rgb(255, 255, 255); border-radius: 0px; background-color: rgb(31, 181, 173); background-clip: padding-box;"></div></div></div>
</aside>


