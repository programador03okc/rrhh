<div class="modal fade" tabindex="-1" role="dialog" id="modal-proveedor" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <form id="form-proveedor" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                    aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title">Nuevo Proveedor</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <input class="oculto" name="id_proveedor">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Tipo de Contribuyente</h5>
                                    <select class="form-control" name="id_tipo_contribuyente" required>
                                        <option value="0" disabled>Elija una opción</option>
                                        @foreach ($tp_contribuyente as $tp)
                                            <option value="{{$tp->id_tipo_contribuyente}}">{{$tp->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Tipo de Documento</h5>
                                    <select class="form-control" name="id_doc_identidad" required>
                                        <option value="0" disabled>Elija una opción</option>
                                        @foreach ($sis_identidad as $tp)
                                            @if($tp->id_doc_identidad === 2) 
                                                <option value="{{$tp->id_doc_identidad}}" selected>{{$tp->descripcion}}</option>
                                            @else 
                                                <option value="{{$tp->id_doc_identidad}}">{{$tp->descripcion}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <h5>Nro. de Documento</h5>
                                    <input type="number" name="nro_documento" class="form-control" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Razon Social</h5>
                                    <input type="text" name="razon_social" class="form-control" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <input type="submit" class="btn btn-sm btn-success" onClick="guardar_proveedor();">Guardar</input> --}}
                    <input type="submit" class="btn btn-success boton" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>  
</div>
