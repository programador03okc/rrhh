<header class="main-header">
	<a href="index2.html" class="logo">
		<span class="logo-mini">ERP</span>
		<span class="logo-lg">ERP OKC</span>
	</a>
	<nav class="navbar navbar-static-top">
		<a href="#" class="sidebar-okc" data-toggle="offcanvas" role="button"><i class="fas fa-bars"></i></a>

		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<!-- Notifications 1 -->
				<li><a href="/modulos">Modulos</a></li>
				<li><a href="/config">Configuración</a></li>
				<!-- Profile -->
				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="{{asset("img/user2-160x160.jpg")}}" class="user-image" alt="User Image">
					<span class="hidden-xs">{{ Auth::user()->nombre_corto }}</span>
					</a>
					<ul class="dropdown-menu">
						<li class="user-header">
							<img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
							<p>{{ Auth::user()->trabajador->postulante->persona->nombre_completo }}
								<small>{{ Auth::user()->cargo }}</small>
							</p>
						</li>
						<li class="user-footer">
							<div class="pull-left"><a href="javascript: void(0)" onclick="changePassword();" class="btn btn-default btn-flat">Perfil</a></div>
							<div class="pull-right">
								<form id="logout-form" action="{{ route('logout') }}" method="POST">
									@csrf
									<button type="submit" class="btn btn-default btn-flat">Salir</button>
								</form>
							</div>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
</header>