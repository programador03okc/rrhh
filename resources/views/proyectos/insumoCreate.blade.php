<div class="modal fade" tabindex="-1" role="dialog" id="modal-insumo_create">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Nuevo Insumo</h3>
            </div>
            <div class="modal-body">
                {{-- <form id="form-insumo" type="register" form="formulario"> --}}
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Codigo</h5>
                            <input class='oculto' name="id_insumo">
                            <input type="text" class="form-control activation" name="codigo" placeholder="0000" disabled="true">
                        </div>
                        <div class="col-md-9">
                            <h5>Descripción</h5>
                            <input type="text" class="form-control activation" name="descripcion">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Tipo Insumo</h5>
                            <select class="form-control activation" name="tp_insumo">
                                <option value="0">Elija una opción</option>
                                @foreach ($tipos as $tp_insumo)
                                    <option value="{{$tp_insumo->id_tp_insumo}}">{{$tp_insumo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-9">
                            <h5>Indices Unificados</h5>
                            <select class="form-control activation js-example-basic-single" name="iu">
                                <option value="0">Elija una opción</option>
                                @foreach ($ius as $iu)
                                    <option value="{{$iu->id_iu}}">{{$iu->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Precio (sin IGV)</h5>
                            <div class="input-group">
                                <span class="input-group-addon">S/</span>
                                <input type="number" class="form-control activation numero" name="precio">
                                {{-- <span class="input-group-addon">.00</span> --}}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <h5>Flete</h5>
                            <div class="input-group">
                                <span class="input-group-addon">S/</span>
                                <input type="number" class="form-control activation numero" name="flete">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Unidad de Medida</h5>
                            <select class="form-control activation" name="unid_medida">
                                <option value="0">Elija una opción</option>
                                @foreach ($unidades as $unid)
                                    <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Peso Unitario</h5>
                            <div class="input-group">
                                <input type="number" class="form-control activation numero" name="peso_unitario">
                                <span class="input-group-addon">Kg.</span>
                            </div>
                        </div>
                    </div>
                    <br/>
                    {{-- <div class="row">
                        <div class="col-md-3">
                            <h5>Estado</h5>
                            <select class="form-control activation" name="estado" readonly>
                                <option value="1" selected>Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                        </div>
                    </div> --}}
                {{-- </form> --}}
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_insumo();">Guardar</button>
            </div>
        </div>
    </div>
</div>
