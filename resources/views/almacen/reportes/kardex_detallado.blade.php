@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="kardex_detallado">
    <legend class="mylegend">
        <h2>Kardex por Producto</h2>
        {{-- <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Kardex Sunat" 
                    onClick="downloadKardexSunat();">Kardex Sunat</button>
                <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Ingrese los filtros" 
                    onClick="open_filtros();">Filtros</button>
            </li>
        </ol> --}}
    </legend>
    <div class="row">
        <div class="col-md-12">
            <div class="input-group-okc">
                <input class="oculto" name="id_producto">
                <input type="text" class="form-control" readonly 
                    placeholder="Seleccione un producto..." aria-describedby="basic-addon2" name="descripcion">
                <div class="input-group-append">
                    <button type="button" class="input-group-text" id="basic-addon2" onClick="productoModal();">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon"> Desde: </span>
                <input type="date" class="form-control" name="fecha_inicio">
                <span class="input-group-addon"> Hasta: </span>
                <input type="date" class="form-control" name="fecha_fin">
            </div>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Kardex" 
                onClick="generar_kardex();">Actualizar Kardex</button>
            <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Exportar Kardex" 
                onClick="download_kardex_excel();">Excel</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="resultado"></div>
    </div>
    {{-- <div class="row">
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
    </div> --}}
</div>
@include('almacen.producto.productoModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/kardex_detallado.js')}}"></script>
<script src="{{('/js/almacen/productoModal.js')}}"></script>
@include('layout.fin_html')