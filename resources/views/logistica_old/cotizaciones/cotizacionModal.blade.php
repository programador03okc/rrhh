<div class="modal fade" tabindex="-1" role="dialog" id="modal-cotizacion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Grupo de Cotizaciones</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaCotizacion">
                    <thead>
                        <tr>
                            <th hidden>Id</th>
                            <th>Codigo</th>
                            <th>RUC</th>
                            <th>Razon Social</th>
                            <th>Cotización</th>
                            <th>Requerimiento</th>
                            {{-- <th>Estado</th> --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="id_grupo_cotizacion" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectGrupoCotizacion();">Aceptar</button>
            </div>
        </div>
    </div>
</div>