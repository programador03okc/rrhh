@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="opcion">
    <legend class="mylegend">
        <h2>Gestión de Opciones Comerciales</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear Opción" 
                onClick="open_opcion_create();">Crear Opción</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaOpcion">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Código</th>
                        <th>Fecha Emisión</th>
                        <th>Descripción</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Mnd</th>
                        <th>Importe</th>
                        <th>Elaborado por</th>
                        <th>Estado</th>
                        <th width="50px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.opcionCreate')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/opcion.js')}}"></script>
@include('layout.fin_html')