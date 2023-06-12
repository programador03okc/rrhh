<div class="modal fade" tabindex="-1" role="dialog" id="modal-req_atencion">
    <div class="modal-dialog" style="width:60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Atención de Requerimiento <label id="cod_req"></label></h3>
            </div>
            <div class="modal-body">
                <input type="text" id="id_requerimiento" hidden>
                <table width="100%" class="table-okc-view">
                    <tbody>
                        <tr>
                            <td>Grupo:</th>
                            <td><label id="des_grupo"></label></td>
                            <td width="80px">Fecha Req.:</td>
                            <td><label id="fecha_requerimiento"></label></td>
                        </tr>
                        <tr>
                            <td>Area o Proyecto:</td>
                            <td><label id="area_proy"></label></td>
                        </tr>
                        <tr>
                            <td width="120px">Concepto:</td>
                            <td><label id="concepto"></label></td>
                        </tr>
                        <tr>
                            <td>Responsable:</td>
                            <td><label id="responsable"></label></td>
                            <td>Prioridad:</td>
                            <td><label id="des_prioridad"></label></td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <table class="mytable table table-striped table-condensed table-bordered table-okc-view" 
                id="listaItems">
                    <thead>
                        <tr>
                            <td hidden></td>
                            <td>Nro</td>
                            <td>Código</td>
                            <td width="40%">Descripción</td>
                            <td width="15%">Posición</td>
                            <td width="10%">Cantidad</td>
                            <td>Und</td>
                            <td width="10%">Partida</td>
                            {{-- <td width="10%">Total</td> --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label id="mid_det" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="atender_req();">Atender</button>
            </div>
        </div>
    </div>
</div>