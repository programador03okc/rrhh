@include('layout.head')
@include('layout.menu_equipo')
@include('layout.body')
<div class="page-main" type="mtto_realizados">
    <legend class="mylegend">
        <h2>Mantenimientos Realizados</h2>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-striped table-condensed table-bordered"
             id="listaMttoRealizados">
                <thead>
                    <tr>
                        <th hidden>Id</th>
                        <th></th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Mantenimiento</th>
                        <th>Precio</th>
                        <th>Obs</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
{{-- @include('equipo.mtto_programacion') --}}
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/equipo/mtto_realizados.js')}}"></script>
@include('layout.fin_html')