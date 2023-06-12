<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_ven_series">
    <div class="modal-dialog" style="width:35%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Seleccione la(s) Series - <label id="descripcion" style="width:9px;"></label></h3>
            </div>
            <div class="modal-body">
                <input type="text" class="oculto" name="cant_items"/>
                <input type="text" class="oculto" name="id_guia_ven_det"/>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-striped table-condensed table-bordered" 
                        id="listaSeries">
                            <thead>
                                <tr>
                                    <td hidden></td>
                                    <td width="10%"></td>
                                    <td width="90%">Serie</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label id="mid_barra" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="guardar_series();">Guardar</button>
            </div>
        </div>
    </div>
</div>