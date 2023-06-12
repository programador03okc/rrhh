<div class="modal fade" tabindex="-1" role="dialog" id="modal-valorizarCotizacion">
    <div class="modal-dialog" style="width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Valorizar Cotización</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" id="listarItemCotizacion">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2" width="100">CODIGO</th>
                            <th rowspan="2">DESCRIPCIÓN</th>
                            <th rowspan="2">UNIDAD</th>
                            <th rowspan="2">CANTIDAD</th>
                            <th rowspan="2">PRECIO REF.</th>
                            <th colspan="9" class="text-center">PROVEEDOR</th>
                        </tr>

                        <tr>
                            <th>UNID.</th>
                            <th width="80">CANT.</th>
                            <th width="80">PRECIO</th>
                            <th width="80">TOTAL</th>
                            <th width="80">FLETE</th>
                            <th width="50">% DES.</th>
                            <th width="80">MONTO DES.</th>
                            <th width="80">SUBTOTAL</th>
                            <th width="50">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
 
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_item"></label>
                <label style="display: none;" id="id_producto"></label>
                <label style="display: none;" id="id_servicio"></label>
                <label style="display: none;" id="id_equipo"></label>
                <!-- <button class="btn btn-sm btn-success" onClick="">Aceptar</button> -->
            </div>
        </div>
    </div>
</div>