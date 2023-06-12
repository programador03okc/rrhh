<div class="modal fade" tabindex="-1" role="dialog" id="modal-partidaCDCreate" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Partida</label></h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Código</h5>
                        <input type="text" name="codigo" class="form-control right" readOnly/>
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <img id="img" src="{{('img/product-default.png')}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="file" name="imagen" id="imagen" class="filestyle"
                                    data-buttonName="btn-primary" data-buttonText="Seleccionar imagen"
                                    data-size="sm" data-iconName="fa fa-folder-open" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row"> --}}
                            <div class="col-md-12">
                                <input class="oculto" name="id_partida">
                                <input class="oculto" name="cod_compo">
                                <input class="oculto" name="id_cd">
                                <h5>Seleccione un A.C.U.</h5>
                                <div style="width: 100%; display:flex;">
                                    <div style="width:90%; display:flex;">
                                        <input class="oculto" name="id_cu">
                                        <input type="text" name="cod_acu" class="form-control input-sm" readOnly style="width:70px;"/>
                                        <input type="text" name="des_acu" class="form-control input-sm" readOnly/>
                                    </div>
                                    <div style="width:10%;">
                                        <span class="input-group-addon input-sm " style="cursor:pointer;" 
                                            onClick="acuModal('cd');">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Cantidad</h5>
                                <input type="number" name="cantidad" onChange="calculaPrecioTotal();" class="form-control right"/>
                            </div>
                            <div class="col-md-4">
                                <h5>Precio Unitario</h5>
                                <input type="number" name="precio_unitario" onChange="calculaPrecioTotal();" class="form-control right" readOnly/>
                            </div>
                            <div class="col-md-4">
                                <h5>Precio Total</h5>
                                <input type="number" name="precio_total" class="form-control right" readOnly/>
                            </div>
                            
                        {{-- </div>
                    </div> --}}
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Sist. de Contrato</h5>
                        <select class="form-control group-elemento activation" name="id_sistema" >
                            <option value="0">Elija una opción</option>
                            @foreach ($sistemas as $sis)
                                <option value="{{$sis->id_sis_contrato}}">{{$sis->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Unid. Medida</h5>
                        <select class="form-control" style="font-size:12px;" 
                            name="unid_medida" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($unidades as $unid)
                                <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_partida_cd();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
