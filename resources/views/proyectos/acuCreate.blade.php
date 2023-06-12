<div class="modal fade" tabindex="-1" role="dialog" id="modal-acu_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Analisis de Costos Unitarios</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-1">
                        <h5>Codigo</h5>
                        <input class="oculto" name="id_cu">
                        <input type="text"  name="codigo" class="form-control activation"  placeholder="0000"  readOnly>
                    </div>
                    <div class="col-md-7">
                        <h5>Descripción</h5>
                        <input type="text"  name="descripcion" class="form-control activation">
                    </div>
                    <div class="col-md-2">
                        <h5>Rendimiento por Día</h5>
                        <div class="input-group">
                            {{-- <span class="input-group-addon">S/</span> --}}
                            <input type="number" class="form-control activation numero" name="rendimiento">
                            <span class="input-group-addon" name="abreviatura"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h5>Unidad de Medida</h5>
                        <select class="form-control activation" name="unid_medida" onChange="unid_abrev();">
                            <option value="0">Elija una opción</option>
                            @foreach ($unidades as $unid)
                                <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}} - {{$unid->abreviatura}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <br/>
                        <input class="oculto" name="anulados" >
                        <table  width="100%">
                            <tbody>
                                <tr>
                                    <td width="40%">
                                        <label>Insumo</label>
                                        <div style="width: 100%; display:flex;">
                                            <div style="width:90%; display:flex;">
                                                <input class="oculto" name="id_insumo">
                                                <input type="text" name="cod_insumo" class="form-control input-sm" readOnly style="width:70px;"/>
                                                <input type="text" name="des_insumo" class="form-control input-sm" readOnly/>
                                            </div>
                                            <div style="width:10%;">
                                                <span class="input-group-addon input-sm " style="cursor:pointer;" 
                                                    onClick="insumoModal();">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                        
                                    </td>
                                    <td width="5%">
                                        <label>Tipo</label>
                                        <input type="text" name="tp_insumo" readOnly class="form-control input-sm"/>
                                    </td>
                                    <td>
                                        <label>Unidad</label>
                                        <input type="text" name="unidad" readOnly class="form-control input-sm"/>
                                    </td>
                                    <td>
                                        <label>Cuadrilla</label>
                                        <input type="number" name="cuadrilla" class="form-control input-sm" 
                                            onChange="calculaCantidad();"/>
                                    </td>
                                    <td>
                                        <label>Cantidad</label>
                                        <input type="number" name="cantidad" class="form-control input-sm" 
                                            onChange="calculaPrecioTotal();"/>
                                    </td>
                                    <td>
                                        <label>Unitario</label>
                                        <input type="number" name="precio_unitario" readOnly class="form-control input-sm" 
                                            onChange="calculaPrecioTotal();"/>
                                    </td>
                                    <td>
                                        <label>Total</label>
                                        <input type="number" name="precio_total" readOnly class="form-control input-sm" />
                                    </td>
                                    <td>
                                        <label>Add</label>
                                        <button type="button" class="btn btn-success input-sm" id="basic-addon2" onClick="agregar();">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                            id="AcuInsumos"  style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th hidden>N°</th>
                                    <th>Código</th>
                                    <th width="40%">Insumo</th>
                                    <th>Tipo</th>
                                    <th>UniMed</th>
                                    <th width="70">Cuadrilla</th>
                                    <th width="70">Cantidad</th>
                                    <th>Unitario</th>
                                    <th width="100">Total</th>
                                    <th width="70">Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="7"></td>
                                    <td><input type="text" name="total_acu" class="form-control input-sm" readOnly/></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <textarea name="observacion" class="form-control" rows="4" cols="50"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_acu();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
