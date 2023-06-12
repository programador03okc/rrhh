<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-insumo">
    <div class="modal-dialog" style="width: 650px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de insumos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaInsumo">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Und</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_insumo" style="display: none;"></label>
                <label id="cod_insumo" style="display: none;"></label>
                <label id="des_insumo" style="display: none;"></label>
                <label id="tp_insumo" style="display: none;"></label>
                <label id="unid_medida" style="display: none;"></label>
                <label id="precio" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectInsumo();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
