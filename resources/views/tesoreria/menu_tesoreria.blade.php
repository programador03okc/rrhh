<style type="text/css">
    .sidebar-menu li.activado{
        background-color: #c92424;
    }
</style>
<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: TEST</p>
            <a href="#"><i class="fa fa-circle"></i> Finanzas</a>
        </div>
    </div>

{{--
    <ul class="sidebar-menu tree" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu" style="">
                <li><a class="active" href="index.html"><i class="fa fa-circle-o"></i> Dashboard v1</a></li>
                <li class="active"><a href="index2.html"><i class="fa fa-circle-o"></i> Dashboard v2</a></li>
            </ul>
        </li>
    </ul>
--}}

    <ul class="sidebar-menu tree">
        <li class="okc-menu-title"><label>Tesoreria</label><p>FIN</p></li>
        <li class="treeview {{ (strpos(request()->route()->getName(), 'solicitud') !== false) ? 'active' : '' }}">
            <a href="cta_contable">
                <i class="fas fa-coins"></i> <span>Solicitud</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="{{ (strpos(request()->route()->getName(), 'solicitud.create') !== false) ? 'activado' : '' }}"><a href="{{ route('tesoreria.solicitud.create') }}"> Generar Solicitud </a></li>
                <li class="{{ (strpos(request()->route()->getName(), 'solicitud.index') !== false) ? 'activado' : '' }}"><a href="{{ route('tesoreria.solicitud.index') }}">Estado de Solicitudes</a></li>
            </ul>
        </li>
        <li class="treeview {{ (strpos(request()->route()->getName(), 'cajachica') !== false) ? 'active' : '' }}">
            <a href="cta_contable">
                <i class="fas fa-coins"></i> <span>Caja Chica</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="{{ (strpos(request()->route()->getName(), 'cajachica.index') !== false) ? 'activado' : '' }}"><a href="{{ route('tesoreria.cajachica.index') }}"> Flujo </a></li>
                {{--<li><a href="cta_detra"> Cuentas de Detracción </a></li>
                <li><a href="impuesto"> Impuestos </a></li>--}}
            </ul>
        </li>
        <li class="treeview {{ (strpos(request()->route()->getName(), 'cajachica') !== false) ? 'active' : '' }}">
            <a href="cta_contable">
                <i class="fas fa-coins"></i> <span>Administracion</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="{{ (strpos(request()->route()->getName(), 'cajachica.index') !== false) ? 'activado' : '' }}"><a href="{{ route('tesoreria.cajachica.index') }}"> Nueva Caja Chica </a></li>
            </ul>
        </li>
    </ul>
</section>