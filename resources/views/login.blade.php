<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistema ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="icon" type="image/ico" href="{{ asset('images/icono.ico')}}" />
	<link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/fonts/ionicons.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet" href="{{ asset('css/basic.css')}}">
    <link rel="stylesheet" href="{{ asset('css/login.css')}}">
</head>
<body>
    <div class="hold-transition login-page">
        <div class="login-box">
            <div class="login-header">
                <code class="text-success">Última Actualización: 07-08-2019</code>
            </div>
            <br>
            <div class="login-box-body">
                <div class="login-name"><h3>SISTEMA ERP</h3></div>
                <div class="login-img">
                    <img src="{{ asset('images/logo_okc.png') }}">
                </div>
                <form id="formLogin" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group has-feedback">
                        <input type="hidden" name="role">
                        <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario"
                            onblur="cargarRol(this.value);">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" name="password" class="form-control" placeholder="Contraseña" autocomplete="off">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-okc-login btn-block btn-flat">Iniciar Sesión</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <script src="{{ asset('template/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js')}}"></script>
    <script src="{{ asset('addons/sweetalert/sweetalert.js') }}"></script>
    <script src="{{ asset('js/login.js')}}"></script>
    <script>
        function cargarRol(value){
            console.log(value)
            baseUrl = 'cargar_usuarios/'+value;
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    $('[name=role]').val(response.rol);
                }
            }).fail( function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    </script>
</body>
</html>
