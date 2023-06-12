@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="orden">
    <legend class="mylegend">
        <h2>Generar Orden</h2>
        <ol class="breadcrumb">
            <li><label id="codigo"></label></li>
            <li>
                <button type="button" class="btn btn-danger" data-toggle="tooltip" 
                data-placement="bottom" title="Imprimir Orden de Compra" 
                onClick="imprimir_orden();">
                <i class="fas fa-print"></i>  Imprimir </button>
            </li>
        </ol>
    </legend>
    <form id="form-orden" type="register" form="formulario">
        <input type="hidden" name="id_orden_compra" primary="ids">
        <div class="row">
            <div class="col-md-3">
                <h5>Proveedor</h5>
                <div style="display:flex;">
                    <input class="oculto" name="id_proveedor"/>
                    <input class="oculto" name="id_contrib"/>
                    <input type="text" class="form-control" name="razon_social" disabled
                        aria-describedby="basic-addon1" required>
                    {{-- <button type="button" class="input-group-text" id="basic-addon1" onClick="proveedorModal();">
                        <i class="fa fa-search"></i>
                    </button> --}}
                </div>
            </div>
            <div class="col-md-3">
                <h5>Condición</h5>
                <div style="display:flex;">
                    <select class="form-control group-elemento activation" name="id_condicion" 
                        style="width:120px;text-align:center;" disabled="true">
                        @foreach ($condiciones as $cond)
                            <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                        @endforeach
                    </select>
                    <input type="number" name="plazo_dias" class="form-control activation group-elemento" style="text-align:right;" disabled/>
                    <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled/>
                </div>
            </div>
            <div class="col-md-3">
                <h5>Tipo de Documento</h5>
                <select class="form-control activation js-example-basic-single" 
                    name="id_tp_documento" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($tp_doc as $tp)
                        <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <h5>Cargar Buena Pro</h5>
                <input class="oculto" name="id_grupo_cotizacion"/>
                <input class="oculto" name="id_cotizacion"/>
                <button type="button" class="btn btn-warning activation" onClick="cotizacionModal();">
                    <i class="fas fa-file-invoice"></i> 
                    Obtener Cuadro Comparativo</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <h5>N° Cuenta Principal</h5>
                <input class="oculto" name="nro_cuenta_principal"/>
                <div style="display:flex;">
                    <select class="form-control activation" name="id_cta_principal"></select>
                    <button type="button" class="btn-primary activation" title="Agregar Cuenta Banco" onClick="agregar_cta_banco(1,1);">
                        <i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="col-md-4">
                <h5>N° Cuenta Alternativa</h5>
                <input class="oculto" name="nro_cuenta_alternativa"/>
                <select class="form-control activation" name="id_cta_alternativa"></select>
            </div>
            <div class="col-md-4">
                <h5>N° Cuenta Detracción</h5>
                <div style="display:flex;">
                    <input class="oculto" name="nro_cuenta_detraccion"/>
                    <select class="form-control activation" name="id_cta_detraccion"></select>
                    <button type="button" class="btn-primary activation" title="Agregar Cuenta Detracción" onClick="agregar_cta_banco(2,4);">
                        <i class="fas fa-plus"></i></button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaDetalleOrden" width="100%">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="80">COD. ITEM</th>
                            <th width="200">PRODUCTO</th>
                            <th width="30">UNIDAD</th>
                            <th width="50">CANTIDAD</th>
                            {{-- <th width="100">GARANTÍA</th> --}}
                            <th width="50">PRECIO</th>
                            <th width="50">DESCUENTO</th>
                            <th width="50">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td></td>
                            <td colspan="7"> No hay datos registrados</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="7"></td>
                            <th scope="row">Descuento</th>
                            <td><input type="text" class="form-control icd-okc" name="Descuento" placeholder="S/.0"></td>
                        </tr>
                        <tr>
                            <td colspan="7"></td>
                            <th scope="row">Monto Neto</th>
                            <td><input type="text" class="form-control icd-okc" name="monto_neto" placeholder="S/.0"></td>
                        </tr>
                        <tr>
                            <td colspan="7"></td>
                            <th scope="row">Monto con IGV</th>
                            <td><input type="text" class="form-control icd-okc" name="monto_con_igv" placeholder="S/.0"></td>
                        </tr>
                        <tr>
                            <td colspan="7"></td>
                            <th scope="row">Monto Sub-Total</th>
                            <td><input type="text" class="form-control icd-okc" name="monto_sub_total" placeholder="S/.0"></td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="cod_estado" hidden/>
                <h5 id="estado">Estado: <label></label></h5>
                <div class="row">   
                    <div class="col-md-9">   
                        <h5>Responsable</h5>
                        <select class="form-control activation js-example-basic-single" name="responsable">
                            <option value="0">Elija una opción</option>
                            @foreach ($responsables as $responsable)
                                <option value="{{$responsable->id_responsable}}">{{$responsable->nombre_responsable}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <table class="tabla-totales" width="100%">
                    <tbody>
                        <tr>
                            <td width="50%">SubTotal</td>
                            <td width="20%"></td>
                            <td><input type="number" class="importe" name="monto_subtotal" readOnly value="0"/></td>
                        </tr>
                        {{-- <tr>
                            <td>Descuentos</td>
                            <td>
                                <input type="number" class="porcen activation" name="porcen_descuento" readOnly value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe" name="total_descuento" readOnly value="0"/></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td></td>
                            <td><input type="number" class="importe" name="total" readOnly value="0"/></td>
                        </tr> --}}
                        <tr>
                            <td>IGV</td>
                            <td>
                                <input type="number" class="porcen activation" name="igv_porcentaje" readOnly value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe" name="monto_igv" readOnly value="0"/></td>
                        </tr>
                        {{-- <tr>
                            <td>Otros Cargos</td>
                            <td>
                            </td>
                            <td><input type="number" class="importe" name="otros" readOnly value="0"/></td>
                        </tr> --}}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>Importe Total</strong></td>
                            <td></td>
                            <td><input type="number" class="importe" name="monto_total" readOnly value="0"/></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
</div>
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.cotizacionModal')
@include('logistica.ordenes.ordenesModal')
@include('logistica.ordenes.add_cta_banco')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/logistica/generar_orden.js')}}"></script>
<script src="{{('/js/logistica/proveedorModal.js')}}"></script>
<script src="{{('/js/logistica/cotizacionModal.js')}}"></script>
<script src="{{('/js/logistica/ordenesModal.js')}}"></script>
<script src="{{('/js/logistica/add_cta_banco.js')}}"></script>
@include('layout.fin_html')