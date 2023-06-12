@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="guia_venta">
    <legend class="mylegend">
        <h2 id="titulo">Guía de Venta / Salida</h2>
        <ol class="breadcrumb">
            <li><label>GR</label></li>
            <li><label id="serie"></label></li>
            <li><label id="numero"></label>
            <button type="submit" class="btn btn-success" onClick="generar_salida();">Generar Salida </button>
            <button type="submit" class="btn btn-primary">Imprimir </button>
            <button type="button" class="btn btn-secondary" data-toggle="tooltip" 
                data-placement="bottom" title="Ver Salida de Almacén" 
                onClick="abrir_salida();">Ver Salida </button>
            </li>
        </ol>
    </legend>
    {{-- <input type="hidden" name="id_guia" primary="ids"> --}}
    <div class="col-md-12" id="tab-guia_venta">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a type="#general">Datos Generales</a></li>
            {{-- <li class=""><a type="#transportista">Guías de Transportista</a></li> --}}
        </ul>
        <div class="content-tabs">
            <section id="general" hidden>
                <form id="form-general" type="register">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_guia_ven" primary="ids">
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
                                    placeholder="000" >
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" name="numero"
                                    placeholder="000000" onBlur="ceros_numero('numero');">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Almacén</h5>
                            <select class="form-control activation" name="id_almacen" onChange="direccion();" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>                        
                        <div class="col-md-5">
                            <h5>Empresa</h5>
                            <select class="form-control activation js-example-basic-single" name="id_empresa" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($empresas as $emp)
                                    <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Almacén</h5>
                            <input type="date" class="form-control activation" name="fecha_almacen" value="<?=date('Y-m-d');?>" >
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
                            <input type="hidden" name="usuario">
                            <h5 id="nombre_usuario">Elaborado por: <label></label></h5>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="cod_estado" hidden/>
                            <h5 id="estado">Estado: <label></label></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-importes"><legend><h6>Documentos Relacionados</h6></legend>
                                <table id="oc" class="table-group">
                                    <thead>
                                        <tr>
                                            <td colSpan="7">
                                                <div style="width: 100%; display:flex;">
                                                    <div style="width:30%;">
                                                        <select class="form-control js-example-basic-single" name="tipo" onChange="onChangeTipo();">
                                                            <option value="0" disabled>Seleccione un tipo</option>
                                                            <option value="1" selected>Guía de Compra</option>
                                                            <option value="2">Requerimiento</option>
                                                            <option value="3">Orden de Compra Cliente</option>
                                                        </select>
                                                    </div>
                                                    <div style="width:60%;">
                                                        <select class="form-control js-example-basic-single" name="docs_sustento">
                                                        </select>
                                                    </div>
                                                    <div style="width:10%;">
                                                        <button type="button" class="btn btn-success boton"  
                                                            style="padding:5px;height:29px;width:100px;font-size:12px;" 
                                                            data-toggle="tooltip" data-placement="bottom" title="Agregar"
                                                            onClick="agrega_sustento();">
                                                            Agregar
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <th width="10%">Código</th>
                                            <th width="10%">Fecha Emisión</th>
                                            <th width="40%">Proveedor</th>
                                            <th>Condición</th>
                                            <th>Fecha Entrega</th>
                                            <th>Lugar Entrega</th>
                                            <th>Acción</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="group-importes"><legend><h6>Items de la Guía de Venta</h6></legend>
                                <table class="table-group" width="100%"
                                    id="listaDetalle">
                                    <thead>
                                        <tr>
                                            <th width='10%'>Doc Nro.</th>
                                            <th width='10%'>Código</th>
                                            <th width='40%'>Descripción</th>
                                            <th>Posición</th>
                                            <th width='10%'>Cant.</th>
                                            <th>Unid.</th>
                                            {{-- <th>Unit.</th>
                                            <th>Total</th> --}}
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
                <form id="form-transportista" type="register"> --}}
                {{-- <input type="hidden" name="id_guia">
                    <div class="row">
                        <div class="col-md-5">
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
                    </div> --}}
                {{-- </form>
            </section> --}}
        </div>
    </div>
</div>
@include('almacen.guia_ventaModal')
@include('almacen.guia_venta_oc')
@include('almacen.guia_ven_series')
@include('almacen.productoModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/guia_venta.js')}}"></script>
<script src="{{('/js/almacen/guia_venta_oc.js')}}"></script>
<script src="{{('/js/almacen/guia_venta_detalle.js')}}"></script>
<script src="{{('/js/almacen/guia_ventaModal.js')}}"></script>
<script src="{{('/js/almacen/guia_ven_series.js')}}"></script>
<script src="{{('/js/almacen/productoModal.js')}}"></script>
@include('layout.fin_html')