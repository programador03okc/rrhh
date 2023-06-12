@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="requerimiento">
    <legend>
        <h2>Gestionar Requerimiento</h2>
    </legend>
    <form id="form-requerimiento" type="register" form="formulario">
        <input type="hidden" name="id_requerimiento" primary="ids">
        <div class="row">
            <div class="col-md-3">
                <h5>Buscar Requerimiento</h5>
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="codigo" placeholder="Ingrese Código de Req." aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="get_requerimiento_por_codigo();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <h5>Concepto/Motivo</h5>
                <input type="text" class="form-control activation" name="concepto">
            </div>
            <div class="col-sm-2">
                <h5>&nbsp;</h5>
                <div class="btn-group" role="group" aria-label="Imprimir Requerimiento"><button type="button" name="btn-imprimir-requerimento-pdf" class="btn btn-danger btn-sm" onclick="ImprimirRequerimientoPdf()" disabled><i class="fas fa-file-pdf"></i> Imprimir</button></div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-2">
                <h5>Fecha</h5>
                <input type="date" class="form-control activation" name="fecha_requerimiento" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>Prioridad</h5>
                <select class="form-control activation" name="prioridad" disabled="true">
                @foreach ($prioridades as $prioridad)
                    <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <h5>Moneda</h5>
                <select class="form-control activation" name="moneda" disabled="true">
                @foreach ($monedas as $moneda)
                    <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                @endforeach
                </select>
            </div>
            <!-- <div class="form-group row"> -->
            <div class="col-md-3">
                <h5>Empresa</h5>
                <select name="empresa" id="empresa" class="form-control activation"
                    required>
                    <option value="">Elija una opción</option>
                    @foreach ($empresas as $empresa)
                        <option value="{{$empresa->id_empresa}}">{{ $empresa->contribuyente->razon_social}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <h5>Area</h5>
                <input type="hidden" class="form-control" name="id_grupo">
                <input type="hidden" class="form-control" name="id_area">
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="nombre_area" disabled="true">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" onclick="modal_area();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row hidden" id="section-proyectos">
            <div class="col-md-7">
                <h5>Proyecto</h5>
                <div style="display:flex;">
                    <input hidden="true" type="text" name="id_proyecto" class="activation">
                    <input type="text" name="codigo_proyecto" class="form-control group-elemento" style="width:130px; text-align:center;" readonly>
                    <div class="input-group-okc">
                        <input type="text" class="form-control" name="descripcion_proyecto" placeholder="" aria-describedby="basic-addon4" disabled="true">
                        <div class="input-group-append">
                            <button type="button" class="input-group-text" id="basic-addon2" onClick="modal_proyectos();">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>                            
                </div>
            </div>
            <div class="col-md-3">
                <h5>Cliente</h5>
                <input type="text" class="form-control" name="cliente" step="any" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>Presupuesto</h5>
                <input hidden="true"  type="text" name="id_presupuesto">
                <input type="text" class="form-control" name="presupuesto" step="any" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="group-importes"><legend><h6>Detalle de Requerimiento</h6></legend>
                <table class="table-group" id="ListaDetalleRequerimiento" width="100%">
                    <thead>
                        <tr>
                            <th class="invisible">#</th>
                            <th>CODIGO</th>
                            <th>DESCRIPCION</th>
                            <th width="60">UNIDAD</th>
                            <th width="70">CANTIDAD</th>
                            <th width="70">PRECIO REF.</th>
                            <th width="100">FECHA ENTREGA</th>
                            <th width="100">LUGAR ENTREGA</th>
                            <th width="120">
                                <center><button class="btn btn-xs btn-success activation" onClick="detalleRequerimientoModal(event);" id="btn-add"
                                    data-toggle="tooltip" data-placement="bottom"  title="Agregar Detalle" disabled><i class="fas fa-plus"></i>
                                </button></center>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="body_detalle_requerimiento">
                        <tr id="default_tr">
                            <td></td>
                            <td colspan="7"> No hay datos registrados</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </form>
</div>
@include('logistica.requerimientos.modal_adjuntar_archivos_requerimiento')
@include('logistica.requerimientos.modal_historial_requerimiento')
@include('logistica.requerimientos.modal_detalle_requerimiento')
@include('logistica.requerimientos.modal_empresa_area')
@include('logistica.requerimientos.modal_proyectos')
@include('logistica.requerimientos.aprobacion.modal_sustento')

@include('layout.footer')
@include('layout.scripts')
<script src="{{ asset('/js/logistica/requerimiento.js') }}"></script>
<script src="{{ asset('/js/publico/modal_area.js')}}"></script>
@include('layout.fin_html')