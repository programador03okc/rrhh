<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Jefe de Maquinaria y Equipos</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Maquinaria y Equipos</label><p>EQ</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="equi_tipo"> Tipo de Equipos </a></li>
                <li><a href="equi_cat"> Categoria de Equipos </a></li>
                {{-- <li><a href="equipo"> Equipo </a></li> --}}
                <li><a href="equi_catalogo"> Catálogo de Equipos </a></li>
                <li><a href="tp_combustible"> Tipo de Combustible </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Mantenimientos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                {{-- <li><a href="mtto_pendientes"> Programación de Mttos </a></li> --}}
                <li><a href="mtto"> Mantenimiento de Equipo </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Asignaciones</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="equi_sol"> Solicitud de Equipo </a></li>
                <li><a href="aprob_sol"> Aprobación de Solicitudes </a></li>
                <li><a href="asignacion"> Asignación de Equipos </a></li>
                <li><a href="control"> Control de Equipos </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="sol_todas"> Listado Solicitudes </a></li>
                <li><a href="docs"> Documentos del Equipo </a></li>
                <li><a href="mtto_pendientes"> Programación de Mttos </a></li>
                <li><a href="mtto_realizados"> Mttos Realizados </a></li>
            </ul>
        </li>
    </ul>
</section>