@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="cotizacion">
    <legend>
        <h2>Gestionar Cotizaciones</h2>
    </legend>
    <form id="form-cotizacion" type="register" form="formulario">
        <input type="hidden" name="id_grupo_cotizacion" value="0" primary="ids">
        <div class="row">
            <div class="col-md-2">
                <h5>Buscar Requerimiento</h5>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-success btn-block" onClick="requerimientoModal();">
                        {{-- <i class="fas fa-plus-circle"></i>  --}}
                        Agregar Requerimiento
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <h5>Código Cuadro Comp.</h5>
                <input type="text" class="form-control" name="codigo_grupo" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>Fecha Inicio</h5>
                <input type="date" class="form-control activation" name="fecha_inicio" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>Fecha Fin</h5>
                <input type="date" class="form-control activation" name="fecha_fin" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h5>Lista de Items</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaItemsRequerimiento" width="100%"> 
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="120">COD.REQ.</th>
                            <th width="120">COD. ITEM</th>
                            <th width="400">DESCRIPCIÓN</th>
                            <th width="100">UNIDAD</th>
                            <th width="100">CANTIDAD</th>
                            <th width="100">PRECIO REF.</th>
                        </tr>
                    </thead>
                    <tbody>
                     {{-- id="trab-requerimiento" --}}
                        {{-- <tr>
                            <td></td>
                            <td colspan="7"> No hay datos registrados</td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-primary btn-lg" onClick="generar_cotizacion();">
                        <i class="fas fa-plus-circle"></i> 
                        Agregar solicitud de Cotización
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <h5>Relación Item/Proveedor</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaCotizaciones" width="100%">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="100">COD. Coti.</th>
                            <th width="60">ITEM</th>
                            <th width="250">PROVEEDOR</th>
                            <th width="100">CORREO</th>
                            <th width="150">EMPRESA</th>
                            <th width="100">ESTADO</th>
                            <th width="150">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="trab-prestamo">
                        <tr>
                            <td></td>
                            <td colspan="6"> No hay datos registrados</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" title="Formato de Solicitud de Cotizacion">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" title="Envio">
                                        <i class="fas fa-share-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@include('logistica.cotizaciones.cotizacionModal')
@include('logistica.cotizaciones.requerimientoModal')
@include('logistica.cotizaciones.cotizacion_proveedor')
@include('logistica.cotizaciones.proveedorModal')
@include('equipo.add_proveedor')
@include('logistica.cotizaciones.add_contacto')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/logistica/gestionar_cotizaciones.js')}}"></script>
<script src="{{('/js/logistica/cotizacionModal.js')}}"></script>
<script src="{{('/js/logistica/requerimientoModal.js')}}"></script>
<script src="{{('/js/logistica/proveedorModal.js')}}"></script>
<script src="{{('/js/equipo/add_proveedor.js')}}"></script>
<script src="{{('/js/logistica/add_contacto.js')}}"></script>
@include('layout.fin_html')