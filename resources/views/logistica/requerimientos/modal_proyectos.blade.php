 
<div class="modal fade" tabindex="-1" role="dialog" id="modal-proyectos">
    <div class="modal-dialog" style="width: 84%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Proyectos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaProyectos">
                    <thead>
                        <tr>
                                    <td class="hidden"></td>
                                    <td>Nro Contrato</td>
                                    <td>Fecha Contrato</td>
                                    <td>Proyecto</td>
                                    <td>Cliente</td>
                                    <td>Mnd</td>
                                    <td>Importe</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_proyecto"></label>
                <button class="btn btn-sm btn-success" onClick="selectProyecto();">Aceptar</button>
            </div>
        </div>
    </div>
</div>