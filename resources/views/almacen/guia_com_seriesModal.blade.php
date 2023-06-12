<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_com_barras">
    <div class="modal-dialog" style="width:30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Ingrese la(s) Series - <label id="descripcion"></label></h3>
            </div>
            <div class="modal-body">
            <input type="text" class="oculto" name="cant_items"/>
            <input type="text" class="oculto" name="id_guia_det"/>
            <input type="text" class="oculto" name="anulados"/>
                <div class="row">
                    <div class="col-md-12">
                        <div style="width: 100%; display:flex; font-size:12px;">
                            <div style="width:77%;">
                                <input name="serie_prod" class="form-control" type="text" style="height:30px;">
                            </div>
                            <div style="width:23%;">
                                <button type="button" class="btn btn-warning" id="basic-addon2" 
                                    style="padding:0px;height:34px;width:98%;height:30px;font-size:12px;" onClick="agregar_serie();"
                                    data-toggle="tooltip" data-placement="right" title="Agregar Serie">
                                    Agregar
                                    {{-- <i class="fas fa-plus-square"></i> --}}
                                </button>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input type="file" id="importar" class="filestyle"
                            data-buttonName="btn-primary" data-buttonText="Importar"
                            data-size="sm" data-iconName="fa fa-folder-open" 
                            accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    <br/>
                        <table class="mytable table table-striped table-condensed table-bordered" 
                        id="listaBarras">
                            <thead>
                                <tr>
                                    <td hidden></td>
                                    <td width="90%">Serie</td>
                                    <td width="10%">Acción</td>
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