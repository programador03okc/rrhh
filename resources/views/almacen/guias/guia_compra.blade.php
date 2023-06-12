@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="guia_compra">
    <legend class="mylegend">
        <h2 id="titulo">Guía de Compra / Ingreso</h2>
        <ol class="breadcrumb">
            <li><label>GR</label></li>
            <li><label id="serie"></label></li>
            <li><label id="numero"></label>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Ingreso a Almacén" 
                onClick="generar_ingreso();">Generar Ingreso </button>
                <a onClick="generar_factura();">
                    <input type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Generar Factura de Compra" 
                    value="Generar Factura"/>
                </a>
                <button type="button" class="btn btn-secondary" data-toggle="tooltip" 
                data-placement="bottom" title="Ver Ingreso a Almacén" 
                onClick="abrir_ingreso();">Ver Ingreso </button>
            </li>
        </ol>
    </legend>
    <div class="col-md-12" id="tab-guia_compra">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#general">Datos Generales</a></li>
            {{-- <li class=""><a type="#transportista">Guías de Transportista</a></li> --}}
            <li class=""><a type="#prorrateo">Prorratear Costos</a></li>
        </ul>
        <div class="content-tabs">
            <section id="general" hidden>
                <form id="form-general" type="register">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="text" class="oculto" name="id_guia" primary="ids">
                {{-- <input type="text" class="oculto" name="id_guia"> --}}
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Tipo de Documento</h5>
                            <select class="form-control activation js-example-basic-single" name="id_tp_doc_almacen" disabled="true" onChange="actualiza_titulo();">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_doc_almacen as $prov)
                                    <option value="{{$prov->id_tp_doc_almacen}}">{{$prov->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control activation" name="serie" 
                                    placeholder="000" disabled="true">
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" 
                                    name="numero" onBlur="ceros_numero('numero');" placeholder="000000" disabled="true">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>" disabled="true">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén</h5>
                            <select class="form-control activation js-example-basic-single" name="id_almacen" onChange="direccion();" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h5>Proveedor</h5>
                            <div style="display:flex;">
                                <select class="form-control activation" name="id_proveedor" disabled="true">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($proveedores as $prov)
                                        <option value="{{$prov->id_proveedor}}">{{$prov->nro_documento}} - {{$prov->razon_social}}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn-primary activation " title="Agregar Proveedor" disabled="true"
                                    onClick="agregar_proveedor();">
                                <strong>+</strong></button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Almacén</h5>
                            <input type="date" class="form-control activation" name="fecha_almacen" value="<?=date('Y-m-d');?>" disabled="true">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Tipo de Operación</h5>
                            <select class="form-control activation js-example-basic-single" name="id_operacion" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_operacion as $tp)
                                    <option value="{{$tp->id_operacion}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h5>Motivo del Traslado</h5>
                            <select class="form-control activation js-example-basic-single" name="id_motivo" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($motivos as $mot)
                                    <option value="{{$mot->id_motivo}}">{{$mot->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Clasif. de los Bienes y Servicios</h5>
                            <select class="form-control activation" name="id_guia_clas" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($clasificaciones as $clas)
                                    <option value="{{$clas->id_clasificacion}}">{{$clas->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Transportista</h5>
                            <select class="form-control activation js-example-basic-single" 
                                name="transportista" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($proveedores as $prov)
                                    <option value="{{$prov->id_proveedor}}">{{$prov->nro_documento}} - {{$prov->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h5>Guía Transportista Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control activation" name="tra_serie" 
                                    placeholder="000">
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" name="tra_numero"
                                    placeholder="000000" onBlur="ceros_numero('tra_numero');">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Traslado</h5>
                            <input type="date" class="form-control activation" name="fecha_traslado" value="<?=date('Y-m-d');?>" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Punto de Partida</h5>
                            <input type="text" class="form-control activation" name="punto_partida">
                        </div>
                        <div class="col-md-5">
                            <h5>Punto de Llegada</h5>
                            <input type="text" class="form-control activation" name="punto_llegada">
                        </div>
                        <div class="col-md-3">
                            <h5>Marca/Modelo/Placa</h5>
                            <input type="text" class="form-control activation" name="placa">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                        </div>
                        <div class="col-md-5">
                            <h5>Responsable</h5>
                            <select class="form-control activation js-example-basic-single" 
                                name="usuario" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_trabajador}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="cod_estado" hidden/>
                            <h5 id="estado">Estado: <label></label></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-importes"><legend><h6>Documento(s) de Sustento</h6></legend>
                                <table id="oc" class="table-group">
                                    <thead>
                                        <tr>
                                            <td colSpan="7">
                                                <div style="width: 100%; display:flex;">
                                                    {{-- <div style="width:30%;">
                                                        <select class="form-control js-example-basic-single" name="tipo">
                                                            <option value="0" disabled>Seleccione un tipo</option>
                                                            <option value="1">Orden de Compra</option>
                                                        </select>
                                                    </div> --}}
                                                    <div style="width:90%;">
                                                        <select class="form-control js-example-basic-single" name="id_orden_compra">
                                                        </select>
                                                    </div>
                                                    <div style="width:10%;">
                                                        <button type="button" class="btn btn-success boton"  
                                                            style="padding:5px;height:29px;width:100px;font-size:12px;" 
                                                            data-toggle="tooltip" data-placement="bottom" title="Agregar"
                                                            onClick="agrega_oc();">
                                                            Agregar
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="8%">Código</th>
                                            <th width="10%">Fecha Emisión</th>
                                            <th>Proveedor</th>
                                            <th>Tramitado por</th>
                                            {{-- <th>Condición</th>
                                            <th>Fecha Entrega</th>
                                            <th>Lugar Entrega</th> --}}
                                            <th width="10%">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-importes"><legend><h6>Items de la Guía de Compra</h6></legend>
                                <table class="table-group" width="100%"
                                    id="listaDetalle">
                                    <thead>
                                        <tr>
                                            <th width='10%'>OC Nro.</th>
                                            <th width='10%'>Código</th>
                                            <th width='30%'>Descripción</th>
                                            <th>Posición</th>
                                            <th>Cant.</th>
                                            <th>Unid.</th>
                                            <th>Unit.</th>
                                            <th>Total</th>
                                            <th width='5%'>
                                                <i class="fas fa-plus-square icon-tabla green boton" 
                                                    data-toggle="tooltip" data-placement="bottom" 
                                                    title="Agregar Producto" onClick="productoModal();"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </section>
            {{-- <section id="transportista" hidden>
                <form id="form-transportista" type="register">
                <input type="hidden" name="id_guia">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Proveedor</h5>
                                    <select class="form-control activation js-example-basic-single" 
                                    name="id_proveedor_tra" disabled="true">
                                        <option value="0">Elija una opción</option>
                                        @foreach ($proveedores as $prov)
                                            <option value="{{$prov->id_proveedor}}">{{$prov->nro_documento}} - {{$prov->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Serie-Número</h5>
                                    <input type="hidden" name="id_guia_com_tra" primary="ids"/>
                                    <div class="input-group">
                                        <input type="text" class="form-control activation" name="serie_tra" 
                                            placeholder="F001" >
                                        <span class="input-group-addon">-</span>
                                        <input type="text" class="form-control activation" name="numero_tra"
                                            placeholder="000000">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Fecha de Emisión</h5>
                                    <input type="date" class="form-control activation" name="fecha_emision_tra">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Marca/Modelo/Placa</h5>
                                    <input type="text" class="form-control activation" name="placa">
                                </div>
                                <div class="col-md-6">
                                    <h5>Referencia</h5>
                                    <input type="text" class="form-control activation" name="referencia">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaTransportista">
                                <thead>
                                    <tr>
                                        <td hidden></td>
                                        <td>Transportista</td>
                                        <td>Serie-Número</td>
                                        <td>Modelo-Placa</td>
                                        <td>Estado</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section> --}}
            <section id="prorrateo" hidden>
                {{-- <form id="form-prorrateo" type="register"> --}}
                <div class="row">
                    <div class="col-md-12">
                        {{-- enctype="multipart/form-data"  --}}
                        <form id="form-datos-prorrateo" method="post">
                            <input type="hidden" name="id_guia">
                            <input class="oculto" name="id_prorrateo">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td width="200px" >
                                            <h5>Tipo</h5>
                                            <div style="display:flex;">
                                                <select class="form-control" name="id_tp_prorrateo" required>
                                                    <option value="0" disabled>Elija una opción</option>
                                                    @foreach ($tp_prorrateo as $tp)
                                                        <option value="{{$tp->id_tp_prorrateo}}">{{$tp->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                                {{-- <button type="button" class="green" title="Agregar Tipo" onClick="agregar_tipo();">
                                                <strong>+</strong></button> --}}
                                            </div>
                                        </td>
                                        <td width="350px">
                                            <h5>Proveedor</h5>
                                            <div style="display:flex;">
                                                <input class="oculto" name="doc_id_proveedor"/>
                                                <input class="oculto" name="id_contrib"/>
                                                <input type="text" class="form-control" name="razon_social" placeholder="Seleccione un proveedor..." 
                                                    onChange="change_proveedor();" aria-describedby="basic-addon1" required>
                                                <button type="button" class="input-group-text" id="basic-addon1" onClick="proveedorModal();">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Serie-Número</h5>
                                            <div style="display:flex;">
                                                <input type="text" class="form-control" style="width:70px;" name="pro_serie" required
                                                    placeholder="001">
                                                <span class="input-group-addon">-</span>
                                                <input type="text" class="form-control" style="width:120px;" name="pro_numero" required
                                                    placeholder="000000" onChange="ceros_numero('pro_numero');">
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Fecha Emisión</h5>
                                            <input type="date" name="doc_fecha_emision" class="form-control" 
                                                onChange="getTipoCambio();" required/>
                                        </td>
                                        <td width="120px">
                                            <h5>Moneda</h5>
                                            <select class="form-control" name="id_moneda" required>
                                                <option value="0" disabled>Elija una opción</option>
                                                @foreach ($monedas as $tp)
                                                    <option value="{{$tp->id_moneda}}">{{$tp->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="80px" >
                                            <h5>TpCambio</h5>
                                            <input type="number" name="tipo_cambio" class="form-control right" step="0.001"/>
                                        </td>
                                        <td width="160px" >
                                            <h5>Sub Total</h5>
                                            <div style="display:flex;">
                                                <input type="number" name="sub_total" class="form-control" step="0.01"
                                                    onChange="calculaImporte();" required/>
                                            </div>
                                        </td>
                                        <td width="160px" >
                                            <h5>Importe <label id="abreviatura"></label></h5>
                                            <input type="number" name="importe" class="form-control" step="0.01" required/>
                                        </td>
                                        <td>
                                            <h5>Add</h5>
                                            <input type="submit" class="btn btn-success" value="Agregar"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                            id="listaProrrateos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tipo de Prorrateo</th>
                                    <th>Serie-Número</th>
                                    <th>Fecha Emisión</th>
                                    <th>Mnd</th>
                                    <th>Total</th>
                                    <th>Tipo Cambio</th>
                                    <th>Importe</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="borde-group-verde">
                            <h4 style="margin:0;">Guía de Remisión</h4>
                            <table width="100%">
                                <tr height="20px">
                                    <td></td>
                                    <td>Moneda</td>
                                    <td width="20">:</td>
                                    <td style="color: #398439;"><label id="moneda"></label></td>
                                    <td>Total</td>
                                    <td width="20">:</td>
                                    <td width="130"><input type="number" class="form-control right" name="total_suma"/></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="borde-group-rojo">
                            <table width="100%">
                                <tr>
                                    <td><h4 style="margin:0;">Costo Adicional Total</h4></td>
                                    <td colSpan="4" width="130"><input type="number" class="form-control right" name="total_comp"/></td>
                                </tr>
                                <tr>
                                    <td>Prorratear según:</td>
                                    <td>
                                        <input type="radio" name="prorrateo" value="1"> Importes
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>    
                                        <input type="radio" name="prorrateo" value="2"> Cantidades
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                            id="listaDetalleProrrateo">
                            <thead>
                                <tr>
                                    <th width='10%'>OC Nro.</th>
                                    <th width='10%'>Código</th>
                                    <th width='30%'>Descripción</th>
                                    <th>Cant.</th>
                                    <th>Unid.</th>
                                    <th>Valor Compra</th>
                                    <th>Adicional</th>
                                    <th>P.(+)Costos</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="5" class="right">
                                        <button type="button" class="btn-success" title="Copiar Precio Unitario" onClick="copiar_unitario();">
                                        <strong>Guardar Unitarios</strong></button>
                                    </td>
                                    <td><input type="text" class="form-control right" readOnly name="total_suma"/></td>
                                    <td><input type="text" class="form-control right" readOnly name="total_adicional"/></td>
                                    <td><input type="text" class="form-control right" readOnly name="total_costo"/></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                {{-- </form> --}}
            </section>
        </div>
    </div>
</div>
@include('almacen.guias.guia_compraModal')
@include('almacen.guias.guia_com_ocModal')
@include('almacen.guias.guia_com_seriesModal')
@include('almacen.documentos.doc_com_guiaModal')
@include('almacen.documentos.doc_com_create')
@include('almacen.producto.productoModal')
@include('almacen.guias.ocModal')
@include('logistica.cotizaciones.proveedorModal')
@include('logistica.cotizaciones.add_proveedor')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/guia_compra.js')}}"></script>
<script src="{{('/js/almacen/guia_compraModal.js')}}"></script>
<script src="{{('/js/almacen/guia_detalle.js')}}"></script>
<script src="{{('/js/almacen/guia_transportista.js')}}"></script>
<script src="{{('/js/almacen/guia_com_ocModal.js')}}"></script>
<script src="{{('/js/almacen/guia_com_seriesModal.js')}}"></script>
<script src="{{('/js/almacen/doc_com_guiaModal.js')}}"></script>
<script src="{{('/js/almacen/doc_com_create.js')}}"></script>
<script src="{{('/js/almacen/productoModal.js')}}"></script>
<script src="{{('/js/almacen/ocModal.js')}}"></script>
<script src="{{('/js/logistica/proveedorModal.js')}}"></script>
<script src="{{('/js/logistica/add_proveedor.js')}}"></script>
@include('layout.fin_html')