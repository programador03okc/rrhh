@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="aportacion">
    <legend><h2>Aportaciones del Empleador</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered" id="listaAportaciones">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Descripción</th>
                            <th>Porcentaje (%)</th>
                            <th>Desde / Hasta</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-tipo_aporte" type="register" form="formulario">
                <input type="hidden" name="id_aportacion" primary="ids">
                <div class="row">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Tipo de Aporte</h5>
                                <select class="form-control activation" name="id_variable_aportacion" disabled="true">
                                    <option value="0" selected disabled>Elija una opción</option>
                                    @foreach ($aport as $aport)
                                        <option value="{{$aport->id_variable_aportacion}}">{{$aport->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <h5>Valor</h5>
                                <input type="number" class="form-control activation text-center" name="valor" id="valor" step="any" disabled="true">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Fecha Inicio</h5>
                                <input type="date" class="form-control activation text-center" name="fecha_inicio" id="fecha_inicio" disabled="true">
                            </div>
                            <div class="col-md-6">
                                <h5>Fecha Fin</h5>
                                <input type="date" class="form-control activation text-center" name="fecha_fin" id="fecha_fin" disabled="true">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <h5>Concepto</h5>
                        <textarea class="form-control activation" name="concepto" id="concepto" disabled="true"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{ ('/template/plugins/moment.min.js') }}"></script>
<script src="{{ ('/js/rrhh/remuneraciones/aportacion.js') }}"></script>
@include('layout.fin_html')