<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <title>@section('title')  {{Config::get('app.name')}} @show</title>
    @section('meta_keywords')
        <meta name="keywords" content="Chatbot"/>
    @show @section('meta_author')
        <meta name="author" content="hailt"/>


        <link href="{{ mix('build/css/app.css') }}" rel="stylesheet">

    @yield('styles')

    <!-- Fonts -->
        <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
        <link rel="shortcut icon" href="{{{ URL::asset('favicon.ico') }}}">
        <style>
            .wrapper-page {
                margin: 5% auto;
                position: relative;
                /*width: 420px;*/
            }
            body {
                background: #ebeff2 !important;
                font-family: 'Noto Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
                margin: 0;
                padding-bottom: 65px;
                overflow-x: hidden;
                color: #797979;
            }
            @if (Cookie::get('locale') == 'vn')
              body {font-family: arial !important;}
            .modal-body label{
                font-family: arial !important;
            }
            @endif
            .wrapper-page .card-box {
                border: 1px solid rgba(54, 64, 74, 0.1);
            }

            .card-box {
                padding: 20px;
                border: 1px solid rgba(54, 64, 74, 0.05);
                -webkit-border-radius: 5px;
                border-radius: 5px;
                -moz-border-radius: 5px;
                background-clip: padding-box;
                margin-bottom: 20px;
                background-color: #ffffff;
            }
            .text-custom {
                color: #5fbeaa !important;
            }
            .panel-heading {
                background: #fff;
            }
            .form-signin{
                max-width: 420px;
                margin: 10px auto;
            }
            @media (max-width: 479px){
                .form-signin{
                    margin: 5px auto;
                }
                body {
                    margin-top: 10px !important;
                }
                .h3, h3 {
                    font-size: 20px;
                }
                .form-signin input[type="text"], .form-signin input[type="password"] {
                    font-size: 14px;
                }
                .wrapper-page {
                    margin: 0% auto;
                }
                .container {
                    padding-right: 7px;
                    padding-left: 7px;
                }
            }


            .form-control{
                height: 38px;
            }
            .add-on {
                margin-top: -41px;
            }
        </style>
</head>
<body class="login-body">
<div class="container">
    <div class="wrapper-page">
        {{--<p align="center" class="system-logo">
           {{config('app.name')}}
        </p>--}}
        <div align="center" class="system-logo">
            @if(strtolower(config('app.name')) == "embot")
                <img src="/images/logo_manager_embot2.png"/>
            @else
                <img src="/images/logo_manager_botchan2.png"/>
            @endif
        </div>
        @yield('content')
    </div>
</div>
</body>
<!-- Scripts -->

<script src="{{ mix('build/js/app.js') }}"></script>
@yield('scripts')
</html>