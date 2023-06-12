@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="doc_compra">
    <legend class="mylegend">
        <h2>Comprobante de Compra</h2>
        <ol class="breadcrumb">
            <li><label>Fact</label></li>
            <li><label id="serie"></label></li>
            <li><label id="numero"></label>
            {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Generar Ingreso a Almacén" onClick="generar_ingreso();">Generar Ingreso </button>
            <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Generar Factura de Compra" onClick="generar_factura();">Generar Factura </button></li> --}}
        </ol>
    </legend>
    <form id="form-doc_compra" type="register" form="formulario">
    <input type="text" class="oculto" name="id_doc_com" primary="ids">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        {{-- <input type="text" class="oculto" name="id_doc"> --}}
            <div class="row">
                <div class="col-md-4">
                    <h5>Serie-Número</h5>
                    <div class="input-group">
                        <input type="text" class="form-control activation" name="serie" 
                            placeholder="F001" disabled="true">
                        <span class="input-group-addon">-</span>
                        <input type="text" class="form-control activation" name="numero"
                            placeholder="000000" disabled="true" onChange="ceros_numero();">
                    </div>
                </div>
                <div class="col-md-5">
                    <h5>Tipo de Documento</h5>
                    <select class="form-control activation js-example-basic-single" 
                        name="id_tp_doc" disabled="true">
                        <option value="0">Elija una opción</option>
                        @foreach ($tp_doc as $tp)
                            <option value="{{$tp->id_tp_doc}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <h5>Fecha de Emisión</h5>
                    <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5>Condición</h5>
                    <div style="display:flex;">
                        <select class="form-control group-elemento activation" name="id_condicion" 
                            onChange="change_dias();" disabled="true">
                            @foreach ($condiciones as $cond)
                                <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                            @endforeach
                        </select>
                        <input type="number" name="credito_dias" class="form-control activation group-elemento" style="text-align:right;" disabled/>
                        <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled/>
                        {{-- <span type="text" class="form-control group-elemento" style="width:100px;text-align:center;">días</span> --}}
                    </div>
                </div>
                <div class="col-md-5">
                    <h5>Proveedor</h5>
                    <select class="form-control activation js-example-basic-single" name="id_proveedor" disabled="true">
                        <option value="0">Elija una opción</option>
                        @foreach ($proveedores as $prov)
                            <option value="{{$prov->id_proveedor}}">{{$prov->nro_documento}} - {{$prov->razon_social}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <h5>Fecha de Vencimiento</h5>
                    <input type="date" class="form-control activation" name="fecha_vcmto" value="<?=date('Y-m-d');?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5>Moneda / Tipo de Cambio</h5>
                    <div style="display:flex;">
                        <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" value="S/" readOnly/>
                        <input type="number" name="tipo_cambio" class="form-control group-elemento" style="text-align: right;"value="3.15248" readOnly/>
                        <select class="form-control group-elemento activation" name="moneda" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($moneda as $mon)
                                <option value="{{$mon->id_moneda}}">{{$mon->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    {{-- <h5>Clasif. de los Bienes y Servicios</h5>
                    <select class="form-control activation" name="id_guia_clas" disabled="true">
                        <option value="0">Elija una opción</option>
                        @foreach ($clasificaciones as $clas)
                            <option value="{{$clas->id_clasificacion}}">{{$clas->descripcion}}</option>
                        @endforeach
                    </select> --}}
                </div>
                <div class="col-md-3">
                    {{-- <input type="button" class="btn btn-primary" onClick="getTipoCambio();" value="Tipo Cambio"/> --}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                </div>
                <div class="col-md-5">
                    <input type="text" class="oculto" name="usuario">
                    <h5 id="nombre_usuario">Responsable: <label></label></h5>
                </div>
                <div class="col-md-3">
                    <input type="text" class="oculto" name="cod_estado">
                    <h5 id="estado">Estado: <label></label></h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <fieldset class="group-importes"><legend><h6>Guía(s) de Remisión</h6></legend>
                        <table id="guias" class="table-group">
                            <thead>
                                <tr>
                                    <td colSpan='5'>
                                        <div style="width: 100%; display:flex;">
                                            <div style="width:90%;">
                                                <select class="form-control js-example-basic-single" name="id_guia">
                                                </select>
                                            </div>
                                            <div style="width:10%;">
                                                <button type="button" class="btn btn-success" id="basic-addon2" 
                                                    style="padding:5px;height:29px;font-size:12px;" 
                                                    data-toggle="tooltip" data-placement="bottom" title="Agregar Guía"
                                                    onClick="agrega_guia();">
                                                    Agregar Guía
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Guía Nro</th>
                                    <th>Fecha Emisión</th>
                                    <th>Proveedor</th>
                                    <th>Motivo</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </fieldset>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <fieldset class="group-importes"><legend><h6>Items del Documento de Compra</h6></legend>
                        <table class="table-group" width="100%"
                            id="listaDetalle">
                            <thead>
                                <tr>
                                    <th width='10%'>Guía Nro.</th>
                                    <th width='10%'>Código</th>
                                    <th width='30%'>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Unitario</th>
                                    <th>%Dscto</th>
                                    <th>Total Dscto</th>
                                    <th>Precio Total</th>
                                    <th width='5%'>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </fieldset>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-secondary" data-toggle="tooltip" 
                    data-placement="bottom" title="pruebs" 
                    onClick="actualiza_totales();">Ver</button>
                </div>
                <div class="col-md-6">
                    <table class="tabla-totales" width="100%">
                        <tbody>
                            <tr>
                                <td width="50%">SubTotal</td>
                                <td width="20%"></td>
                                <td><input type="number" class="importe" name="sub_total" readOnly value="0"/></td>
                            </tr>
                            <tr>
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
                            </tr>
                            <tr>
                                <td>Anticipos</td>
                                <td>
                                    <input type="number" class="porcen activation" name="porcen_anticipo" readOnly value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_ant_igv" readOnly value="0"/></td>
                            </tr>
                            {{-- <tr>
                                <td>ISC</td>
                                <td>
                                    <input type="number" class="porcen activation" name="porcen_isc" readOnly value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_isc" readOnly value="0"/></td>
                            </tr> --}}
                            <tr>
                                <td>IGV</td>
                                <td>
                                    <input type="number" class="porcen activation" name="porcen_igv" readOnly value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_igv" readOnly value="0"/></td>
                            </tr>
                            <tr>
                                <td>Otros Cargos</td>
                                <td>
                                </td>
                                <td><input type="number" class="importe" name="otros" readOnly value="0"/></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Importe Total</strong></td>
                                <td></td>
                                <td><input type="number" class="importe" name="total_a_pagar" readOnly value="0"/></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </form>
    </div>
@include('almacen.documentos.doc_compraModal')
@include('almacen.documentos.doc_com_detalle')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/doc_compra.js')}}"></script>
<script src="{{('/js/almacen/doc_compraModal.js')}}"></script>
<script src="{{('/js/almacen/doc_com_detalle.js')}}"></script>
@include('layout.fin_html')