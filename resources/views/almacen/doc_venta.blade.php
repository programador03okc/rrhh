@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="doc_venta">
    <legend class="mylegend">
        <h2>Comprobante de Venta</h2>
        <ol class="breadcrumb">
            <li><label>Fact</label></li>
            <li><label id="serie"></label></li>
            <li><label id="numero"></label>
            {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Generar Ingreso a Almacén" onClick="generar_ingreso();">Generar Ingreso </button>
            <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Generar Factura de Compra" onClick="generar_factura();">Generar Factura </button></li> --}}
        </ol>
    </legend>
    <form id="form-doc_venta" type="register" form="formulario">
        <input type="text" class="oculto" name="id_doc_ven" primary="ids">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
            <div class="row">
                <div class="col-md-4">
                    <h5>Serie-Número</h5>
                    <div class="input-group">
                        <input type="text" class="form-control activation" name="serie" 
                            placeholder="F000" disabled="true">
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
                    <input type="date" class="form-control " name="fecha_emision" value="<?=date('Y-m-d');?>" disabled="true">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5>Condición</h5>
                    <div style="display:flex;">
                        <select class="form-control group-elemento activation" name="id_condicion" disabled="true">
                            @foreach ($condiciones as $cond)
                                <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                            @endforeach
                        </select>
                        <input type="number" name="credito_dias" class="form-control group-elemento activation" style="text-align:right;" disabled/>
                        <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled/>
                    </div>
                </div>
                <div class="col-md-5">
                    <h5>Empresa</h5>
                    <select class="form-control activation js-example-basic-single" name="id_empresa" disabled="true">
                        <option value="0">Elija una opción</option>
                        @foreach ($empresas as $emp)
                            <option value="{{$emp->id_empresa}}">{{$emp->nro_documento}} - {{$emp->razon_social}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <h5>Fecha de Vencimiento</h5>
                    <input type="date" class="form-control " name="fecha_vcmto" value="<?=date('Y-m-d');?>" disabled="true">
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
                    <h5>Clasif. de los Bienes y Servicios</h5>
                    <select class="form-control activation" name="id_guia_clas" disabled="true">
                        <option value="0">Elija una opción</option>
                        @foreach ($clasificaciones as $clas)
                            <option value="{{$clas->id_clasificacion}}">{{$clas->descripcion}}</option>
                        @endforeach
                    </select>
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
                                                <select class="form-control " name="id_guia">
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
                    <fieldset class="group-importes"><legend><h6>Items del Documento de Venta</h6></legend>
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
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <table class="tabla-totales" width="100%">
                        <tbody>
                            <tr>
                                <td width="50%">SubTotal</td>
                                <td width="20%"></td>
                                <td><input type="number" class="importe" name="sub_total" disabled="true" value="0"/></td>
                                {{-- <td><input type="number" class="importe green" name="total_costo_directo_pc" disabled="true" value="0"/></td> --}}
                            </tr>
                            <tr>
                                <td>Anticipos</td>
                                <td>
                                    <input type="number" class="porcen activation" name="porcentaje_ci" disabled="true" value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_ant_igv" disabled="true" value="0"/></td>
                                {{-- <td><input type="number" class="importe green" name="total_ci_pc" disabled="true" value="0"/></td> --}}
                            </tr>
                            <tr>
                                <td>Descuentos</td>
                                <td>
                                    <input type="number" class="porcen activation" name="porcen_descuento" disabled="true" value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_descuento" disabled="true" value="0"/></td>
                                {{-- <td><input type="number" class="importe green" name="total_gg_pc" disabled="true" value="0"/></td> --}}
                            </tr>
                            <tr>
                                <td>ISC</td>
                                <td>
                                    <input type="number" class="porcen activation" name="porcentaje_ci" disabled="true" value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_isc" disabled="true" value="0"/></td>
                                {{-- <td><input type="number" class="importe green" name="total_ci_pc" disabled="true" value="0"/></td> --}}
                            </tr>
                            <tr>
                                <td>IGV</td>
                                <td>
                                    <input type="number" class="porcen activation" name="porcentaje_igv" disabled="true" value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_igv" disabled="true" value="0"/></td>
                                {{-- <td><input type="number" class="importe green" name="total_ci_pc" disabled="true" value="0"/></td> --}}
                            </tr>
                            <tr>
                                <td>Otros Cargos</td>
                                <td>
                                    {{-- <input type="number" class="porcen activation" name="porcentaje_ci" disabled="true" value="0"/>
                                    <label>%</label> --}}
                                </td>
                                <td><input type="number" class="importe" name="otros" disabled="true" value="0"/></td>
                                {{-- <td><input type="number" class="importe green" name="total_ci_pc" disabled="true" value="0"/></td> --}}
                            </tr>
                            <tr>
                                <td>Otros Tributos</td>
                                <td>
                                    {{-- <input type="number" class="porcen activation" name="porcentaje_ci" disabled="true" value="0"/>
                                    <label>%</label> --}}
                                </td>
                                <td><input type="number" class="importe" name="otros_trib" disabled="true" value="0"/></td>
                                {{-- <td><input type="number" class="importe green" name="total_ci_pc" disabled="true" value="0"/></td> --}}
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Importe Total</strong></td>
                                <td></td>
                                <td><input type="number" class="importe" name="total_a_pagar" disabled="true" value="0"/></td>
                                {{-- <td><input type="number" class="importe green" name="sub_total_pc" disabled="true" value="0"/></td> --}}
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {{-- <div class="col-md-6">
                    <table class="tabla-totales" width="100%">
                        <tbody>
                            <tr>
                                <td width="50%">SubTotal</td>
                                <td width="20%"></td>
                                <td><input type="number" class="importe" name="subtotal" disabled="true" value="0"/></td>
                            </tr>
                            <tr>
                                <td>Anticipos IGV (SPOT)</td>
                                <td>
                                    <input type="number" class="porcen activation" name="porcentaje_utilidad" disabled="true" value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_utilidad" disabled="true" value="0"/></td>
                            </tr>
                            <tr>
                                <td>IGV</td>
                                <td>
                                    <input type="number" class="porcen" name="porcentaje_igv" disabled="true" value="0"/>
                                    <label>%</label>
                                </td>
                                <td><input type="number" class="importe" name="total_igv" disabled="true" value="0"/></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Total Neto</strong></td>
                                <td></td>
                                <td><input type="number" class="importe" name="total_presupuestado" disabled="true" value="0"/></td>
                            </tr>
                        </tfoot>
                    </table>
                </div> --}}
            </div>
        </form>
    </div>
@include('almacen.doc_ventaModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/doc_venta.js')}}"></script>
<script src="{{('/js/almacen/doc_ventaModal.js')}}"></script>
{{-- <script src="{{('/js/almacen/doc_com_detalle.js')}}"></script> --}}
@include('layout.fin_html')