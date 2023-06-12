<div class="modal fade" tabindex="-1" role="dialog" id="modal-requerimiento">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Requerimientos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                    id="listaRequerimientoPendientes">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Codigo</th>
                            <th>Concepto</th>
                            <th>Area</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_requerimiento" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectRequerimiento();">Aceptar</button>
            </div>
        </div>
    </div>
</div>