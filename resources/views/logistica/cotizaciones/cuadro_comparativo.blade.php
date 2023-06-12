@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="cuadro_comparativo">
    <legend>
        <h2>Cuadro Comparativo</h2>
    </legend>
    <form id="form-cuadro_comparativo" type="register" form="formulario">
        <input type="hidden" name="id_grupo_cotizacion">
        <div class="row">
            <div class="col-md-5">
                <div class="input-group">
                    <div class="input-group-btn">
                        <select class="btn btn-default dropdown-toggle" name="tipoCodigo">
                            <option value="1" selected>Código Cuadro Comparativo</option>
                            <option value="2">Código Cotización</option>
                        </select>
                    </div>
                    <input type="text" class="form-control" name="codigo" />
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button" onclick="getGrupoCotizaciones()">
                            Buscar
                        </button>
                    </div>

                </div>
            </div>
            <div class="col-md-5">
                <div class="input-group-btn">
                    <button class="btn btn-success" type="button" onclick="cotizacionModal()">
                        Busqueda Avanzada
                    </button>
                </div>
            </div>

 
        </div>
        <div class="row">
            <div class="col-md-12">
                <h5>Lista de Items por cotización</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view" id="listaGrupoCotizaciones" width="100%">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="120">COD. CUADRO COMP.</th>
                            <th width="120">COD. COTIZACIÓN</th>
                            <th width="120">COD. REQUERIMIENTO</th>
                            <th width="100">PROVEEDOR</th>
                            <th width="10">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="trab-prestamo">
                        <tr>
                            <td></td>
                            <td colspan="4"> No hay datos registrados</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Valorizar" name="btnValorizarCotizacion" disabled>
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


</div>

<div class="row">
    <div class="col-sm-12">

    </div>
</div>

<div class="row">
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button type="button" class="btn btn-primary btn-sm disabled" title="Mostrar Cuadro Comparativo" name="btnMostrarCuadroComparativo" onclick="mostrarCuadroComparativo();">
                    <strong>Mostrar Cuadro Comparativo</strong>
                </button>
                <button type="button" class="btn btn-info btn-sm disabled" title="Exportar Cuadro Comparativo" name="btnExportarCuadroComparativo" onclick="exportarCuadroComparativo();">
                    <i class="far fa-file-excel"></i> <strong>Exportar Cuadro</strong>
                </button>
            </div>
            <div class="panel-body"></div>

            <div id="head-cuadro"></div>
            <br>

            <table class="mytable table table-condensed table-bordered table-okc-view" id="cuadro_comparativo">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <legend>
        <h4> <strong>Buena Pro</strong></h4>
        </legend>

        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div id="panel-buena_pro"></div>
        </div>

    </div>
</div>

<div id="btn-action-buena_pro"></div>

</form>


</div>


@include('logistica.cotizaciones.cotizacionModal')
@include('logistica.cotizaciones.modal_buena_pro')
@include('logistica.cotizaciones.modal_historial_cuadro_comparativo')
@include('logistica.cotizaciones.modal_valorizar_cotizacion')
@include('logistica.cotizaciones.modal_valorizacion_especificacion')

@include('layout.footer')
@include('layout.scripts')
<script src="{{ asset('/js/logistica/cuadro_comparativo/index.js')}}"></script>
<script src="{{ asset('/js/logistica/cotizacionModal.js')}}"></script>
@include('layout.fin_html')