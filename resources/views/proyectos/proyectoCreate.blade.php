<div class="modal fade" tabindex="-1" role="dialog" id="modal-proyecto_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Proyecto</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Codigo</h5>
                        <input class="oculto" name="id_proyecto">
                        <input type="text"  name="codigo" placeholder="PY-00-000" class="form-control activation" readOnly>
                    </div>
                    <div class="col-md-9">
                        <h5>Descripción</h5>
                        <input class="oculto" name="id_op_com">
                        <div style="width: 100%; display:flex;">
                            <div style="width:90%; display:flex;">
                                <input type="text" name="nombre_opcion" class="form-control input-sm"/>
                            </div>
                            <div style="width:10%;">
                                <span class="input-group-addon input-sm " style="cursor:pointer;" 
                                    onClick="open_opcion_modal();">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Tipo</h5>
                        <select class="form-control activation" name="tp_proyecto">
                            <option value="0">Elija una opción</option>
                            @foreach ($tipos as $tp)
                                <option value="{{$tp->id_tp_proyecto}}">{{$tp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-9">
                        <h5>Cliente</h5>
                        <select class="form-control activation" name="cliente">
                            <option value="0">Elija una opción</option>
                            @foreach ($clientes as $cli)
                                <option value="{{$cli->id_cliente}}">{{$cli->razon_social}}</option>
                            @endforeach
                        </select>
                        {{-- boton para Crear Opciones --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Sistema de Contrato</h5>
                        <select class="form-control activation" name="sis_contrato">
                            <option value="0">Elija una opción</option>
                            @foreach ($sistemas as $sis)
                                <option value="{{$sis->id_sis_contrato}}">{{$sis->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Plazo de Ejecución</h5>
                        <div style="display:flex;">
                            <input type="number" name="plazo_ejecucion" class="form-control group-elemento" style="width:80px;text-align:right;"/>
                            <select class="form-control group-elemento activation" name="unid_program">
                                <option value="0">Elija una opción</option>
                                @foreach ($unid_program as $unid)
                                    <option value="{{$unid->id_unid_program}}">{{$unid->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h5>Importe</h5>
                        <div style="display:flex;">
                            <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readOnly/>
                            <input type="number" name="importe" class="form-control group-elemento" style="text-align: right;" />
                            <select class="form-control group-elemento activation" name="moneda" onChange="moneda();">
                                <option value="0">Elija una opción</option>
                                @foreach ($monedas as $mon)
                                    <option value="{{$mon->id_moneda}}">{{$mon->descripcion}} - {{$mon->simbolo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Modalidad</h5>
                        <select class="form-control activation" name="modalidad">
                            <option value="0">Elija una opción</option>
                            @foreach ($modalidades as $mod)
                                <option value="{{$mod->id_modalidad}}">{{$mod->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <h5>Fecha Inicio / Fecha Fin</h5>
                        <div style="display:flex;">
                            <input type="date" name="fecha_inicio" class="form-control"/>
                            <input type="date" name="fecha_fin" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>Jornal x día</h5>
                        <div class="input-group">
                            <input type="number" name="jornal" class="form-control"/>
                            <span class="input-group-addon">horas</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_proyecto();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
