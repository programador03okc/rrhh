<div class="modal fade" tabindex="-1" role="dialog" id="modal-detalle-requerimiento">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalle Requerimiento</h3>
            </div>
            <div class="modal-body">
                <form id="form-detalle-requerimiento" type="register" form="formulario">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Item</h5>
                            <div style="display:flex;">
                                <input hidden="true" type="text" name="estado">
                                <input hidden="true" type="text" name="id_item">
                                <input hidden="true" type="text" name="id_producto">
                                <input hidden="true" type="text" name="id_servicio">
                                <input hidden="true" type="text" name="id_equipo">
                                <input hidden="true" type="text" name="id_tipo_item">
                                <input hidden="true" type="text" name="id_detalle_requerimiento">
                                <input type="text" name="codigo_item" class="form-control group-elemento" style="width:200px;text-align:center;" readonly="">
                                <div class="input-group-okc">
                                <input type="text" class="form-control" name="descripcion_item" placeholder="" aria-describedby="basic-addon4" 
                                    onkeydown="handleKeyDown(event);" 
                                    onKeyPress="handleKeyPress(event);"
                                    >
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" id="basic-addon7" onClick="catalogoItemsModal();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>                            
                        </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Unidad de Medida</h5>
                            <select name="unidad_medida_item" class="form-control activation" >
                                    <option value="">Elija una opción</option>
                                @foreach ($unidades_medida as $unidad_medida)
                                    <option value="{{$unidad_medida->id_unidad_medida}}">{{ $unidad_medida->descripcion}}</option>
                                @endforeach

                            </select>                        
                        </div>
                        <div class="col-md-1">
                            <h5>Cantidad</h5>
                            <input type="text" class="form-control activation" name="cantidad_item" step="any">
                        </div>
                        <div class="col-md-2">
                            <h5>Precio Ref.</h5>
                            <input type="text" class="form-control activation" name="precio_ref_item" step="any">
                        </div>
        
                        <div class="col-md-3">
                            <h5>Fecha Máxima de Entrega</h5>
                            <input type="date" class="form-control activation" name="fecha_entrega_item" step="any">
                        </div>
                        <div class="col-md-3">
                            <h5>Lugar de Entrega</h5>
                            <input type="text" class="form-control activation" name="lugar_entrega_item" step="any">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Partida</h5>
                            <div style="display:flex;">
                                <input type="hidden" name="id_partida">
                                <input type="text" name="cod_partida" class="form-control group-elemento" style="width:200px;text-align:center;" readonly="">
                                <div class="input-group-okc">
                                <input type="text" class="form-control" name="des_partida" placeholder="" aria-describedby="basic-addon8">
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" id="basic-addon8" onClick="partidasModal();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>                            
                        </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_requerimiento"></label>
                    <label><h5><span class="label label-warning" id="obs_det"></span></h5></label>
                    <button class="btn btn-sm btn-primary" name="btn-agregar-item" onClick="agregarItem();">Agregar</button>
                    <button class="btn btn-sm btn-success" name="btn-aceptar-cambio" onClick="aceptarCambiosItem();">Aceptar</button>

            </div>
        </div>
    </div>
</div>

@include('logistica.requerimientos.modal_catalogo_items')
@include('logistica.requerimientos.modal_partidas')
