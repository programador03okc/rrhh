@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="almacenes">
    <legend><h2>Almacenes</h2></legend>
    <div class="row">
        <div class="col-md-7">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaAlmacen">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Sede</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <form id="form-almacenes" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_almacen" primary="ids">
                <div class="row">
                    <div class="col-md-8">
                        <h5>Sede</h5>
                        <select class="form-control activation" name="id_sede" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($sedes as $sede)
                                <option value="{{$sede->id_sede}}">{{$sede->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Dirección</h5>
                        <input type="text" class="form-control activation" name="ubicacion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <h5>Tipo de Almacén</h5>
                        <select class="form-control activation" name="id_tipo_almacen" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{$tipo->id_tipo_almacen}}">{{$tipo->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/almacenes.js')}}"></script>
@include('layout.fin_html')