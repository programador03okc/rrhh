<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-acu">
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Costos Unitarios</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaAcu">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Und</th>
                            <th>Rend</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_acu" style="display: none;"></label>
                <label id="cod_acu" style="display: none;"></label>
                <label id="des_acu" style="display: none;"></label>
                <label id="tot_acu" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectAcu();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
