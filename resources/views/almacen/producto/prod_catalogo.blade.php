@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="producto">
    <legend class="mylegend">
        <h2>Catálogo de Productos</h2>
        <ol class="breadcrumb">
            <li><label id="tipo_descripcion"> </li>
            <li><label id="cat_descripcion"></li>
            <li><label id="subcat_descripcion"></li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" id="listaProductoCatalogo">
                <thead>
                    <tr>
                        <th></th>
                        <th>Cod.Tp</th>
                        <th>Tipo</th>
                        <th>Cod.Cat</th>
                        <th>Categoría</th>
                        <th>Cod.Subcat</th>
                        <th>Subcatgoría</th>
                        <th>Cod.Clas</th>
                        <th>Clasificación</th>
                        <th>Código</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/prod_catalogo.js')}}"></script>
@include('layout.fin_html')