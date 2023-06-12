@include('layout.head')
@include('layout.menu_equipo')
@include('layout.body_sin_option')
<div class="page-main" type="control">
    <legend class="mylegend">
        <h2>Control de Equipos</h2>
    </legend>
    <form id="form-control" type="register" form="formulario">
        <input class="oculto" name="id_control" primary="ids">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Equipo Asignado</h5>
                        <div class="input-group-okc">
                            <input type="hidden" name="id_asignacion" >
                            <input type="text" name="id_equipo" class="oculto">
                            <input type="text" class="form-control" aria-describedby="basic-addon2" 
                                readonly name="equipo_descripcion" disabled="true">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text" id="basic-addon2"
                                    onClick="asignacionModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>              
                    <div class="col-md-4">
                        <h5>Area</h5>
                        <input type="text" class="form-control activation" name="area_descripcion" disabled="true">
                    </div>
                    <div class="col-md-2">
                        <h5>Fecha de Asignación</h5>
                        <input type="date" class="form-control activation" name="fecha_asignacion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Solicitado por</h5>
                        <input type="text" name="id_trabajador" class="oculto"/>
                        <input type="text" name="trabajador" readOnly class="form-control">
                    </div>
                    <div class="col-md-4">
                        <h5>Fecha Inicio / Fecha Fin</h5>
                        <div style="display:flex;">
                            <input type="date" name="fecha_inicio" class="form-control" disabled="true"/>
                            <input type="date" name="fecha_fin" class="form-control" disabled="true"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <fieldset class="group-importes"><legend><h6>Recorrido Realizado</h6></legend>
                    <table id="detalle" class="table-group">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Kil.Inicio</th>
                                <th>Kil.Fin</th>
                                <th>Recorrido (Km)</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Chofer</th>
                                <th>Descripción del Recorrido</th>
                                <th>Monto (S/)</th>
                                <th>Galones</th>
                                <th>Observaciones</th>
                                <th>
                                    <i class="fas fa-plus-square icon-tabla green boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar" onClick="controlModal();"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </form>
</div>
@include('equipo.controlModal')
@include('equipo.asignacionModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/equipo/control.js')}}"></script>
<script src="{{('/js/equipo/asignacionModal.js')}}"></script>
@include('layout.fin_html')