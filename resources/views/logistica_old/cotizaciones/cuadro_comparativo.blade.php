@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="prestamo">
    <legend>
        <h2>Cuadro Comparativo</h2>
    </legend>
    <form id="form-prestamo" type="register" form="formulario">
        <input type="hidden" name="id_prestamo" primary="ids">
        <div class="row">
            <div class="col-md-3">
                <h5>Buscar Código Cotización</h5>
                <input type="hidden" class="form-control" name="id_trabajador">
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="nro_documento" placeholder="Ingrese Código de Cotización" aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarCuadroComparativo();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <h5>Buscar Código Cuadro Comparativo</h5>
                <input type="hidden" class="form-control" name="id_trabajador">
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="nro_documento" placeholder="Ingrese Código de Cuadro Comparativo" aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarCuadroComparativo();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <h5>Lista de Items</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaPrestamoTrab" width="100%">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="120">COD. CUADRO COMP.</th>
                            <th width="120">COD. COTIZ.</th>
                            <th width="120">COD. REQ.</th>
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
                                    <button type="button" class="btn btn-warning btn-sm" title="Valorizar">
                                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="btn-group" role="group" aria-label="Basic example"><button type="button" class="btn btn-primary btn-lg"><i class="fas fa-plus-circle"></i> Agregar solicitud de Cotización</button></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <h5>Relación Item/Proveedor</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaPrestamoTrab" width="100%">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="100">COD. Coti.</th>
                            <th width="100">ITEM</th>
                            <th width="120">DESCRIPCIÓN</th>
                            <th width="100">PROVEEDOR</th>
                            <th width="100">CORREO</th>
                            <th width="100">EMPRESA</th>
                            <th width="100">ESTADO</th>
                            <th width="120">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="trab-prestamo">
                        <tr>
                            <td></td>
                            <td colspan="7"> No hay datos registrados</td>
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

@include('layout.footer')
@include('layout.scripts')
@include('layout.fin_html')