<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>ERP OKC</title>
        <link rel="icon" type="image/ico" href="{{ asset('images/icono.ico') }}">
        <link rel="stylesheet" href="{{ asset("template/bootstrap/css/bootstrap.min.css")}} ">
        <link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.css')}}">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="{{ asset('template/dist/css/skins/_all-skins.min.css')}}">
        <link rel="stylesheet" href="{{ asset("css/app.css") }}">
        @yield("links")
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            <!-- Layout / Header -->
            @include("themes/header")
            <!-- Layout / Aside -->
            {{-- @include("themes/aside") --}}
            <aside class="main-sidebar">
                <section class="sidebar">
                    @yield("aside")
                </section>
            </aside>
            <!-- Content Page -->
            <div class="content-wrapper" id="wrapper-okc" style="min-height: 100vh;">
                @yield("option")
                <!-- Title Page -->
                <section class="content-header">
                    <h1>@yield("title")</h1>
                </section>
                <!-- Content Page -->
                <section class="content">
                    @yield("content")
                </section>
            </div>
        </div>
        <script src="{{ asset('template/plugins/jQuery/jquery.min.js')}}"></script>
        <script src="{{ asset('template/plugins/jQueryUI/jquery-ui.min.js')}}"></script>
        <script src="{{ asset('template/bootstrap/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('template/dist/js/app.min.js') }}"></script>
        @yield("scripts")
    </body>
</html>