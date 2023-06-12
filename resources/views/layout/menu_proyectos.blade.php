<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Jefe de Proyectos</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Proyectos</label><p>PY</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="sis_contrato"> Sistemas de Contrato </a></li>
                <li><a href="tipo_insumo"> Tipos de Insumo </a></li>
                <li><a href="iu"> Indices Unificados </a></li>
                <li><a href="insumo"> Insumos </a></li>
                <li><a href="acu"> Análisis de Costos Unitarios </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Opcion Comercial</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="opcion"> Opción Comercial </a></li>
                <li><a href="presint"> Presupuesto Interno </a></li>
                <li><a href="propuesta"> Propuesta Cliente </a></li>
                <li><a href="cronoint"> Cronograma Interno </a></li>
                <li><a href="#"> Cronograma Cliente </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Proyecto Elaboración</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="proyecto"> Gestión de Proyectos </a></li>
                <li><a href="preseje"> Presupuesto de Ejecución </a></li>
                <li><a href="#"> Cronograma de Ejecución </a></li>
                <li><a href="#"> Aprobación </a></li>
                {{-- <li><a href="#"> Seguimiento de Presupuesto </a></li>
                <li><a href="#"> Cronograma de Desembolso </a></li> --}}
                <li><a href="#"> Residentes </a></li>
                <li><a href="#"> Portafolio de Proyectos </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Proyecto Ejecución</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="#"> Valorización </a></li>
                <li><a href="#"> Informe de Valorización </a></li>
                <li><a href="#"> Ejecutado vs Presupuestado </a></li>
            </ul>
        </li>
    </ul>
</section>