<div class="modal fade" tabindex="-1" role="dialog" id="modal-cuadro_comparativo">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Cuadro Comparativos</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaCuadroComaparativo">
                    <thead>
                        <tr>                        
                            <th>Id</th>
                            <th>Código Grupo</th>
                            <th>Proveedor</th>
                            <th>Empresa</th>
                            <!-- <th>Fecha Inicio</th> -->
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_grupo"></label>
                <button class="btn btn-sm btn-success" onClick="selectCuadroComparativo();">Aceptar</button>
            </div>
        </div>
    </div>
</div>