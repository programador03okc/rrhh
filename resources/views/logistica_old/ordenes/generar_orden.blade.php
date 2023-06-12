@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="generar_orden">
    <legend>
        <h2>Generar Orden</h2>
    </legend>
    <form id="form-prestamo" type="register" form="formulario">
        <input type="hidden" name="id_prestamo" primary="ids">
        <div class="row">
            <div class="col-md-3">
                <h5>Buscar Código Cuadro Comparativo</h5>
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
                <h5>Cargar Buena Pro</h5>
                <button type="button" class="btn btn-warning"><i class="fas fa-file-invoice"></i> Obtener Cuadro Comparativo</button>
            </div>

        </div>


        <div class="row">
            <div class="col-md-2">
                <h5>Tipo Comprobante</h5>
                <select class="form-control activation" name="tipo_comprobante" disabled="true">
                    <option value="0" disabled>Elija una opción</option>
                    <option value="1">Factura</option>
                    <option value="2">Boleta</option>
                    <option value="5">...</option>
                </select>
            </div>
            <div class="col-md-2">
                <h5>Condicion de Compra</h5>
                <select class="form-control activation" name="condicion_compra" disabled="true">
                    <option value="0" disabled>Elija una opción</option>
                    <option value="1">contado cash</option>
                    <option value="2">credito</option>
                </select>
            </div>
            <div class="col-md-2">
                <h5>Crédito (días)</h5>
                <input type="number" min="0" class="form-control activation" name="credito" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>N° Cuenta Principal</h5>
                <input type="text" class="form-control activation" name="nro_cuenta_principal" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>N° Cuenta Alternativa</h5>
                <input type="text" class="form-control activation" name="nro_cuenta_alternativa" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>N° Cuenta Detracción</h5>
                <input type="text" class="form-control activation" name="nro_cuenta_detraccion" disabled="true">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <h5>Lista de Items</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaPrestamoTrab" width="100%">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="120">COD. ITEM</th>
                            <th width="120">PRODUCTO</th>
                            <th width="120">UNIDAD</th>
                            <th width="100">CANTIDAD</th>
                            <th width="100">GARANTÍA</th>
                            <th width="100">PRECIO</th>
                            <th width="100">DESCUENTO</th>
                            <th width="100">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody id="trab-prestamo">
                        <tr>
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