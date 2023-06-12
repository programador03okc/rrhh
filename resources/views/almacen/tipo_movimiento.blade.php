@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="tipoMov">
    <legend><h2>Tipos de Movimientos en Almacén</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-striped table-condensed table-bordered" 
                id="listaTipoMov">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Codigo</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-tipo_movimiento" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Tipo</h5>
                        <input type="hidden" name="id_tp_mov" primary="ids">
                        <select class="form-control activation" name="tipo" disabled="true">
                            <option value="0" selected>Elija una opción</option>
                            <option value="1">Carga Inicial</option>
                            <option value="2">Ingreso</option>
                            <option value="3">Salida</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <h5>Codigo</h5>
                        <input type="text" class="form-control activation"  name="tp_mov" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <input type="text" class="form-control activation" name="descripcion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                    <h5>Estado</h5>
                    <select class="form-control activation" name="estado" readonly>
                        <option value="1" selected>Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/tipo_movimiento.js')}}"></script>
@include('layout.fin_html')