<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>@section('title')  {{Config::get('app.name')}} @show</title>
    @section('meta_keywords')
        <meta name="keywords" content="CHATBOT"/>
    @show @section('meta_author')
        <meta name="author" content="hailt"/>
        <link href="{{ mix('build/css/app.css') }}" rel="stylesheet">
    @yield('styles')
    @yield('styles_conversation')

    <!-- Fonts -->
        <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
        <!-- Scripts -->
        <script src="{{ mix('build/js/app.js') }}"></script>
        <link rel="shortcut icon" href="{{{ URL::asset('favicon.ico') }}}">
        <style>
            @if (Cookie::get('locale') == 'vn')
              body {font-family: arial !important;}
            .modal-body label{
                font-family: arial !important;
            }
            @endif


            .container {
                margin-right: auto;
                margin-left: auto;
                padding-left: 15px;
                padding-right: 15px;
            }
            @media (min-width: 768px) {
                .container {
                    width: 750px;
                }
            }
            @media (min-width: 992px) {
                .container {
                    width: 970px;
                }
            }
            @media (min-width: 1200px) {
                .container {
                    width: 1170px;
                }
            }
            @media (max-width: 479px){
                body {
                    margin-top: 10px!important;
                }
                .nav-center{
                    line-height: 40px;
                }
            }
            @media (max-width: 767px) and (min-width: 480px){
                .header {
                    position: relative! important;
                    margin-top: 10px ! important;
                }
            }
        </style>
</head>
<body>
<section class="container">
    @include('partials.header')
    <section id="main-content-1">
        <section class="wrapper">
            @yield('content')
        </section>
    </section>
</section>

@yield('scripts')
@yield('scripts2')
</body>
</html>