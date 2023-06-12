@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fas fa-tachometer-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Requerimientos</span>
                <span class="info-box-text">Generados</span>
                <span class="info-box-number">1,410</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fas fa-tachometer-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Requerimientos</span>
                <span class="info-box-text">Aprobados</span>
                <span class="info-box-number">1,410</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fas fa-tachometer-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Requerimientos</span>
                <span class="info-box-text">Observados</span>
                <span class="info-box-number">1,410</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fas fa-tachometer-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Requerimientos</span>
                <span class="info-box-text">Anulados</span>
                <span class="info-box-number">1,410</span>
            </div>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
@include('layout.fin_html')