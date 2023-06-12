
<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Jefe de Contabilidad</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Contabilidad</label><p>CNT</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Datos</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="cta_contable"> Plan de Cuentas Contables </a></li>
                <li><a href="cta_detra"> Cuentas de Detracción </a></li>
                <li><a href="impuesto"> Impuestos </a></li>
            </ul>
        </li>
    </ul>
</section>