<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ config('app.name', 'Sistema ERP') }} | {{ ($pagina['titulo'] ?? '') }}</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="{{ asset('template/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/fonts/ionicons.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/dist/css/AdminLTE.min.css') }}">
	<link rel="stylesheet" href="{{ asset('template/plugins/iCheck/square/blue.css') }}">
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
	<!-- ESTILOS -->
@yield('styles_modulo')
@yield('styles_seccion')
<!-- FIN ESTILOS -->

</head>
<body class="hold-transition skin-okc sidebar-mini">

<div class="hold-transition login-page">
	<div class="login-box">
		<div class="login-header">
			<code>Última Actualización: 22-01-2019</code>
		</div>
		<div class="login-name"><h3>SISTEMA ERP</h3></div>
		<div class="login-box-body">
			<form id="formLogin"  action="{{ route('login') }}">
				@csrf
				<div class="form-group has-feedback">
					<input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario">
					<span class="glyphicon glyphicon-user form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback">
					<select class="form-control" name="company">
						<option value="0" disabled selected>Elija una empresa</option>
						@foreach ($empresas as $empresa)
							<option value="{{$empresa->id_empresa}}">{{$empresa->contribuyente->razon_social}}</option>
						@endforeach
					</select>
					<span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback">
					<select class="form-control" name="role">
						<option value="0" disabled selected>Elija un rol</option>
						<option value="1">Programador</option>
					</select>
					<span class="glyphicon glyphicon-folder-close form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback">
					<input type="password" name="password" class="form-control" placeholder="Contraseña">
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

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('template/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- SCRIPTS -->
<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('[name=usuario]').on('change', function () {
        let Usr = $(this).val();
        $.get('/cargar_usuarios/' + Usr, function (data) {
            let html = '<option value="0" disabled selected>Elija un rol</option>';
            $.each(data, function (idx, dato) {
                console.log(dato);
                html += '<option value="' + dato.id_rol + '">' + dato.descripcion + '</option>';
            });
            $('[name=role]').html(html);
        })
    });
    $("#formLogin").submit(function(e){
        let formData = $(this).serialize();
        console.log(formData);
        $.ajax({
            type: 'POST',
            url: '{{ route('login') }}',
            data: formData,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.success) {
                    window.location.href = response.redirectto;
				}
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {

                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
        });
        e.preventDefault();
    });
</script>
<!-- FIN SCRIPTS -->

</body>
</html>
