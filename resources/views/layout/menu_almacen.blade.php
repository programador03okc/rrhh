
<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Jefe de Almacén</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Almacén</label><p>AL</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Productos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="tipo"> Tipo de Producto </a></li>
                <li><a href="categoria"> Categoría</a></li>
                <li><a href="subcategoria"> SubCategoría</a></li>
                <li><a href="clasificacion"> Clasificación</a></li>
                <li><a href="producto"> Producto</a></li>
                <li><a href="prod_catalogo"> Catálogo de Productos</a></li>
                <li><a href="unid_med"> Unidades de Medida </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Servicios</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="tipoServ"> Tipo de Servicio </a></li>
                <li><a href="servicio"> Servicio </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Organización</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="tipo_almacen"> Tipo Almacén </a></li>
                <li><a href="almacenes"> Almacenes </a></li>
                <li><a href="ubicacion"> Ubicaciones </a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Movimientos de Almacén</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="tipo_movimiento"> Tipos de Operación </a></li>
                <li><a href="tipo_doc_almacen"> Tipos de Documentos </a></li>
                <li><a href="guia_compra"> Compras / Ingresos </a></li>
                <li><a href="guia_venta"> Ventas / Salidas </a></li>
                <li><a href="cola_atencion"> Pendientes de Atención </a></li>
                {{-- <li><a href="traslado"> Traslado </a></li> --}}
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Documentos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="doc_compra"> Comprobantes de Compra </a></li>
                <li><a href="doc_venta"> Comprobantes de Venta </a></li>
                {{-- <li><a href="guia_venta"> Guias por Venta </a></li> --}}
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="lista_ingresos"> Reporte de Ingresos </a></li>
                <li><a href="lista_salidas"> Reporte de Salidas </a></li>
                <li><a href="busqueda_ingresos"> Búsqueda Avanzada de Ingresos </a></li>
                <li><a href="busqueda_salidas"> Búsqueda Avanzada de Salidas </a></li>
                <li><a href="kardex_general"> Kardex General </a></li>
                <li><a href="kardex_detallado"> Kardex por Producto </a></li>
                <li><a href="saldos"> Saldos por Almacén </a></li>
            </ul>
        </li>
        {{-- <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Maquinaria y Equipos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="equi_tipo"> Tipo de Equipos </a></li>
                <li><a href="equi_cat"> Categoria de Equipos </a></li>
                <li><a href="equi_catalogo"> Catálogo de Equipos </a></li>
                <li><a href="tp_combustible"> Tipo de Combustible </a></li>
                <li><a href="mtto_pendientes"> Mantenimientos Pendientes </a></li>
                <li><a href="mtto"> Mantenimiento de Equipo </a></li>
                <li><a href="equi_sol"> Solicitud de Equipo </a></li>
                <li><a href="asignacion"> Asignación de Equipos </a></li>
                <li><a href="control"> Control de Equipos </a></li>
            </ul>
        </li> --}}
    </ul>
</section>