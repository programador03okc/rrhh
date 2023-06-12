@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="saldos">
    <legend class="mylegend">
        <h2>Reporte de Saldos por Almacén</h2>
        <ol class="breadcrumb">
            <li>
                {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Saldos" 
                    onClick="downloadKardexSunat();">Saldos por Almacén</button>
                <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Ingrese los filtros" 
                    onClick="open_filtros();">Filtros</button> --}}
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <h5>Almacén</h5>
            <div style="display:flex;">
                <select class="form-control" name="almacen">
                    @foreach ($almacenes as $alm)
                        <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Saldos" 
                    onClick="listarSaldos();">Buscar</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaSaldos">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Posicion</th>
                        <th>Und</th>
                        <th>Saldo</th>
                        <th>CostoProm.</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
{{-- @include('almacen.kardex_filtro') --}}
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/saldos.js')}}"></script>
@include('layout.fin_html')