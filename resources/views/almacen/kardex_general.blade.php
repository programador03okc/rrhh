@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="kardex_general">
    <legend class="mylegend">
        <h2>Kardex General</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Kardex Sunat" 
                    onClick="downloadKardexSunat();">Kardex Sunat</button>
                <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Ingrese los filtros" 
                    onClick="open_filtros();">Filtros</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="kardexGeneral">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Fecha Mov.</th>
                        <th>Posicion</th>
                        <th>Und</th>
                        <th>Ing.</th>
                        <th>Sal.</th>
                        <th>Saldo</th>
                        <th>Ing.</th>
                        <th>Sal.</th>
                        <th>Valoriz.</th>
                        <th>Tp</th>
                        <th>Movimiento</th>
                        <th>Guía</th>
                        <th>Doc</th>
                        <th>Req.</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('almacen.kardex_filtro')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/kardex_general.js')}}"></script>
@include('layout.fin_html')