@include('layout.head')
@include('layout.menu_equipo')
@include('layout.body_sin_option')
<div class="page-main" type="aprob_sol">
    <legend class="mylegend">
        <h2>Aprobaciones de Solicitud de Equipos</h2>
        {{-- <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Mantenimiento" 
                onClick="mantenimiento_create();">Generar Mantenimiento</button>
            </li>
        </ol> --}}
    </legend>
    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
    <div class="col-md-12" id="tab-sol_aprob">
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a type="#aprobaciones">Aprobaciones</a></li>
        <li class=""><a type="#todas">Todas las Solicitudes</a></li>
    </ul>
    <div class="content-tabs">
        <section id="aprobaciones" hidden>
            <form id="form-aprobaciones" type="register">
            <div class="row">
                <div class="col-md-12">
                    {{-- <fieldset class="group-table"> --}}
                        <table class="mytable table table-condensed table-bordered table-okc-view"
                        id="listaSolAprobaciones">
                            <thead>
                                <tr>
                                    <th hidden>Id</th>
                                    <th>Código</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Solicitado por</th>
                                    <th>Area</th>
                                    <th>Categoria</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    {{-- </fieldset> --}}
                </div>
            </div>
            </form>
        </section>
        <section id="todas" hidden>
            <form id="form-todas" type="register">
            <div class="row">
                <div class="col-md-12">
                {{-- <fieldset class="group-table"> --}}
                    <table class="mytable table table-condensed table-bordered table-okc-view"
                    id="listaSolTodas">
                        <thead>
                            <tr>
                                <th hidden>Id</th>
                                <th>Código</th>
                                <th>Fecha Solicitud</th>
                                <th>Solicitado por</th>
                                <th>Area</th>
                                <th>Categoria</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    {{-- </fieldset> --}}
                </div>
            </div>
            </form>
        </section>
    </div>
</div>
@include('equipo.aprob_flujos')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/equipo/aprob_sol.js')}}"></script>
@include('layout.fin_html')