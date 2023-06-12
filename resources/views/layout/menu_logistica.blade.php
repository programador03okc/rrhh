<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="{{ asset('img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Programador</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Logistica</label><p>LG</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Requerimientos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="/logistica/requerimiento/lista"> Listado</a></li>
                <li><a href="/logistica/requerimiento/gestionar"> Gestionar Requerimiento </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Cotizaciones</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="/gestionar_cotizaciones"> Gestión de Cotizaciones </a></li>
                <li><a href="/logistica/cotizacion/cuadro-comparativo"> Gestión de Cuadro Comparativo</a></li>

            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Ordenes</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="/generar_orden"> Generar Orden </a></li>
 
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Proveedores</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="/lista_proveedores"> Lista de Proveedores </a></li>
 
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <!-- <ul class="treeview-menu">
                <li><a href="Proveedores"> Proveedores </a></li>
            </ul> -->
        </li>
    </ul>
</section>