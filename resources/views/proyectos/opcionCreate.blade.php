<div class="modal fade" tabindex="-1" role="dialog" id="modal-opcion_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Opción Comercial</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Codigo</h5>
                        <input class="oculto" name="id_op_com"  >
                        <input type="text"  name="codigo" placeholder="OP-00-000" class="form-control activation" readOnly>
                    </div>
                    <div class="col-md-9">
                        <h5>Descripción</h5>
                        <input type="text"  name="descripcion" class="form-control activation">
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
                        <h5>Fecha Emisión</h5>
                        <input type="date" name="fecha_emision" class="form-control"/>
                    </div>
                    <div class="col-md-6">
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
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_opcion();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
