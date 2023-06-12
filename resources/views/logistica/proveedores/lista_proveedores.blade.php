@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="proveedores">
    <legend>
        <h2>Lista de Proveedores</h2>
    </legend>

    <a class="btn btn-primary invisible disabled" id="btn_nuevo_proveedor" role="button" data-toggle="collapse" href="#collapseProveedores" aria-expanded="false" aria-controls="collapseExample">
        Agregar Proveedor
    </a>

    <div class="collapse" id="collapseProveedores">
        <div class="well">

            <div id="tab-proveedores">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                <li class="active"><a type="#contribuyente" >Proveedor</a></li>
                <li><a type="#cuentas_bancarias">Cuentas Bancarias</a></li>
                <li><a type="#contactos" >Contactos</a></li>
                <li><a type="#adjuntos">Adjuntos</a></li>
                </ul>

                <div class="content-tabs">
                    <section id="contribuyente" hidden>
                    <form id="form-contribuyente" type="register" form="formulario">
                        <div class="row">
                            <div class="col-md-3">
                                <h5>RUC</h5>
                                <div style="display:flex;">
                                    <input  type="text" pattern="^[0-9]{11}$"class="form-control icd-okc" name="nro_documento" />
                                    <button type="button" class="btn-primary" title="Verificar Nro Documento" onclick="consultaSunat();">
                                        <i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Razón Social</h5>
                                <input class="form-control icd-okc" name="razon_social" />
                            </div>
                            <div class="col-md-3">
                                <h5>Condición</h5>
                                <input class="form-control icd-okc" name="condición" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Tipo Empresa</h5>
                                <input class="form-control icd-okc" name="tipo_empresa" />
                            </div>
                            <div class="col-md-2">
                                <h5>Fecha Inscripción</h5>
                                <input class="form-control icd-okc" name="fecha_inscripcion" />
                            </div>
                            <div class="col-md-2">
                                <h5>Estado</h5>
                                <input class="form-control icd-okc" name="estado" />
                            </div>
                            <div class="col-md-4">
                                <h5>Dirección</h5>
                                <input class="form-control icd-okc" name="direccion" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Actividad Economica</h5>
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaActividadEconomica" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible">#</th>
                                                <th width="150">DECRIPCIÓN</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="agregarActividadEconomica(event);" id="btn_agregar_actividad_economica" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyActividadEconomica">
                                            <tr>
                                                <td class="invisible">1</td>
                                                <td>OTRAS ACTIVIDADES DE TECNOLOGÍA DE LA INFORMACIÓN Y DE SERVICIOS INFORMÁTICOS</td>
                                                <td>
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                                                        <button
                                                            class="btn btn-secondary btn-sm  disabled"
                                                            name="btnEditarActividadEconomica"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="editarActividadEconomica(event, 0);"
                                                            data-original-title="Editar"
                                                        >
                                                            <i class="fas fa-edit"></i>
                                                        </button
                                                        ><button
                                                            class="btn btn-danger btn-sm  disabled"
                                                            name="btnEliminarActividadEconomica"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="eliminarActividadEconomica(event, 0);"
                                                            data-original-title="Eliminar"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button
                                                        > 
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Establecimientos</h5>
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaEstablecimientos" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible">#</th>
                                                <th width="150">TIPO</th>
                                                <th width="150">DIRECCIÓN</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="AgregarEstablecimiento(event);" id="btn_agregar_establecimiento" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyEstablecimientos">
                                            <tr>
                                                <td class="invisible">1</td>
                                                <td>DEPOSITO</td>
                                                <td>URB.LOS ROSALES #9494</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                                                        <button
                                                            class="btn btn-secondary btn-sm  disabled"
                                                            name="btnEditarItem"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="editarEstablecimiento(event, 0);"
                                                            data-original-title="Editar"
                                                        >
                                                            <i class="fas fa-edit"></i>
                                                        </button
                                                        ><button
                                                            class="btn btn-danger btn-sm  disabled"
                                                            name="btnEliminarItem"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="eliminarEstablecimiento(event, 0);"
                                                            data-original-title="Eliminar"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button
                                                        > 
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </form>
                    </section>
                    
                    <section id="cuentas_bancarias" hidden>
                        <form id="form-cuentas_bancarias">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Banco</h5>
                                    <div style="display:flex;">
                                        <select class="form-control js-example-basic-single" name="banco">
                                            <option value="0">Elija una opción</option>

                                        </select>
                                        <button type="button" class="btn-primary activation" title="Agregar Cuenta Banco" onClick="btn_agregar_cta_banco(1,1);">
                                        <i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Tipo Cuenta</h5>
                                    <div style="display:flex;">
                                        <select class="form-control js-example-basic-single" name="tipo_cuenta">
                                            <option value="0">Elija una opción</option>
                                        </select>   
                                        <button type="button" class="btn-primary activation" title="Agregar Tipo Cuenta" onClick="btn_agregar_tipo_cuenta(1,1);">
                                            <i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Nro Cuenta</h5>
                                    <input class="form-control icd-okc" name="nro_cuenta" />
                                </div>
                                <div class="col-md-3">
                                    <h5>Nro Cuenta Interbancaria</h5>
                                    <input class="form-control icd-okc" name="nro_cuenta_interbancaria" />
                                </div>

                            </div>
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaCuentasBancarias" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible">#</th>
                                                <th width="150">BANCO</th>
                                                <th>TIPO CUENTA</th>
                                                <th >NRO CUENTA</th>
                                                <th >NRO INTERBANCARIA</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="AgregarCuantaBancaria(event);" id="btn_add_cuenta_bancaria" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyCuentasBancarias">
                                            <tr>
                                                <td class="invisible">1</td>
                                                <td>INTERBANK</td>
                                                <td>PRINCIPAL</td>
                                                <td>993993391-99291</td>
                                                <td>1232993993391-99291</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                                                        <button
                                                            class="btn btn-secondary btn-sm  disabled"
                                                            name="btnEditarCuentaBancaria"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="editarCuentaBancaria(event, 0);"
                                                            data-original-title="Editar"
                                                        >
                                                            <i class="fas fa-edit"></i>
                                                        </button
                                                        ><button
                                                            class="btn btn-danger btn-sm  disabled"
                                                            name="btnEliminarCuentaBancaria"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="eliminarCuentaBancaria(event, 0);"
                                                            data-original-title="Eliminar"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button
                                                        > 
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>

                    <section id="contactos" hidden>
                    <form id="form-contactos">
                            <div class="row">
 
                                <div class="col-md-3">
                                    <h5>Nombres</h5>
                                    <input class="form-control icd-okc" name="nombre_contacto" />
                                </div>
                                <div class="col-md-2">
                                    <h5>Teléfono</h5>
                                    <input class="form-control icd-okc" name="telefono_contacto" />
                                </div>
                                <div class="col-md-2">
                                    <h5>E-mail</h5>
                                    <input class="form-control icd-okc" name="email_contacto" />
                                </div>
                                <div class="col-md-2">
                                    <h5>Cargo</h5>
                                    <input class="form-control icd-okc" name="cargo_contacto" />
                                </div>
                                <div class="col-md-3">
                                    <h5>Establecmiento</h5>
                                    <div style="display:flex;">
                                        <select class="form-control js-example-basic-single" name="establecimiento">
                                            <option value="0">Elija una opción</option>
                                        </select>   
                                        <button type="button" class="btn-primary activation" title="Agregar Establecimiento" onClick="agregar_establecimiento(1,1);">
                                            <i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaContactos" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible">#</th>
                                                <th width="150">Nombres</th>
                                                <th>Telefono</th>
                                                <th >E-mail</th>
                                                <th >Cargo</th>
                                                <th >Estabecimiento</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="agregarContacto(event);" id="btn_add_contacto" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyContactos">
                                            <tr>
                                                <td class="invisible">1</td>
                                                <td>Peter Parker</td>
                                                <td>58585848</td>
                                                <td>peterparker@maximaeirl.com.pe</td>
                                                <td>Jefe de Logistica y Almacén</td>
                                                <td>Urb:bombardas Mz.P Lt.450</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                                                        <button
                                                            class="btn btn-secondary btn-sm  disabled"
                                                            name="btnEditarContacto"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="editarContacto(event, 0);"
                                                            data-original-title="Editar"
                                                        >
                                                            <i class="fas fa-edit"></i>
                                                        </button
                                                        ><button
                                                            class="btn btn-danger btn-sm  disabled"
                                                            name="btnEliminarContacto"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="eliminarContacto(event, 0);"
                                                            data-original-title="Eliminar"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button
                                                        > 
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
 
                    <section id="adjuntos" hidden>
                        <form id="form-adjuntos">
                            <div class="row">
                            <div class="col-md-12">
                                <h5>Brochure</h5>
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaArchivoAdjuntosProveedor" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible">#</th>
                                                <th>Archivo</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="agregarAdjunto(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyArchivosAdjuntosProveedor">
                                            <tr>
                                                <td class="invisible">1</td>
                                                <td>brochure.pdf</td>
                                                <td>
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Second group">
                                                        <button
                                                            class="btn btn-primary btn-sm  disabled"
                                                            name="btnVerDescargarAdjunto"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="verDescargarAdjunto(event, 0);"
                                                            data-original-title="Ver/Descargar"
                                                        >
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button
                                                            class="btn btn-danger btn-sm  disabled"
                                                            name="btnEliminarAdjunto"
                                                            data-toggle="tooltip"
                                                            title=""
                                                            onclick="eliminarAdjunto(event, 0);"
                                                            data-original-title="Eliminar"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button> 
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            </div>

                        </form>
                    </section>
                </div>

            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <!-- <caption>Requerimientos: Registrados | Aprobados</caption> -->
            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaProveedores" width="100%">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th width="190">RAZON SOCIAL</th>
                        <th width="90">DOCUMENTO</th>
                        <th>TIPO CONTRIBUYENTE</th>
                        <th>DIRECCIÓN</th>
                        <th>TELEFONO</th>
                        <th>ESTADO</th>
                        <th width="90">ACCIÓN</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-flujo-aprob">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalles del Requerimiento</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="req-detalle"></div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="flujo-detalle"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12" id="flujo-proximo"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('logistica.requerimientos.modal_adjuntar_archivos_requerimiento')
@include('logistica.requerimientos.aprobacion.modal_aprobacion')
@include('logistica.requerimientos.aprobacion.modal_obs')

@include('layout.footer')
@include('layout.scripts')

<script src="{{('/js/logistica/proveedores/listar_proveedores.js')}}"></script>

<script src="{{('/js/publico/consulta_sunat.js')}}"></script>
<!-- <script src="{{('/js/logistica/aprobacion/aprobacion.js')}}"></script> -->
@include('layout.fin_html')