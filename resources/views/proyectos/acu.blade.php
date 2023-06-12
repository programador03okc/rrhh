@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="acu">
    <legend class="mylegend">
        <h2>Análisis de Costos Unitarios</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Crear un ACU" 
                onClick="open_acu_create();">Crear ACU</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaAcu">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Und</th>
                        <th>Rend</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th width="50px">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
                {{-- <div class="row">
                    <div class="col-md-9">
                        <div>
                            <ul class="nav nav-tabs" id="myTab">
                                <li class="active"><a type="#insumos">Detalle de Insumos</a></li>
                                <li class=""><a type="#descripcion">Descripción</a></li>
                                <li class=""><a type="#presupuestos">Presupuestos</a></li>
                                <li class=""><a type="#lecciones">Lecciones Aprendidas</a></li>
                            </ul>
                            <div class="content-tabs">
                                <section id="insumos">
                                    <div class="input-group-okc">
                                        <input type="hidden" name="id_insumo">
                                        <input type="hidden" name="cod_insumo">
                                        <div class="input-new">
                                            <div class="input-group" style="width:3%;">
                                                <button type="button" class="btn btn-primary boton inferior" id="basic-addon2" onClick="insumoModal();">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                            <input type="text" class="input-new" readonly style="width:40%;" name="des_insumo"/>
                                            <span class="input-group-addon input-new" name="tp_insumo" style="width:5%;"></span>
                                            <span class="input-group-addon input-new" name="unidad" style="width:5%;"></span>
                                            <input type="number" class="input-new numero" name="cuadrilla" 
                                                style="width:10%;" onChange="calculaCantidad();"/>
                                            <input type="number" class="input-new numero" name="cantidad" 
                                                style="width:10%;" onChange="calculaPrecioTotal();"/>
                                            <input type="number" class="input-new numero" name="precio_unitario" 
                                                style="width:10%;" onChange="calculaPrecioTotal();"/>
                                            <input type="number" class="input-new numero" readOnly name="precio_total" 
                                                disabled="true"  style="width:10%;"/>
                                            <div class="input-group">
                                                <button type="button" class="btn btn-success boton inferior" id="basic-addon2" onClick="agregar();">
                                                    <i class="fas fa-plus-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>    
                                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" id="AcuInsumos">
                                        <thead>
                                            <tr>
                                                <th width="30" hidden>N°</th>
                                                <th>Código</th>
                                                <th width="250">Insumo</th>
                                                <th>Tipo</th>
                                                <th>UniMed</th>
                                                <th>Cuadrilla</th>
                                                <th>Cantidad</th>
                                                <th>Unitario</th>
                                                <th>Total</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </section>
                                <section id="descripcion">
                                    <form id="form-descripcion">
                                        <textarea class="oculto" name="observacion" cols="106" rows="8"></textarea>
                                    </form>
                                </section>
                                <section id="presupuestos">
                                    <table class="mytable table table-condensed table-bordered table-okc-view oculto" width="100%" id="AcuPresupuestos">
                                        <thead>
                                            <tr>
                                                <th width="30">N°</th>
                                                <th>Código</th>
                                                <th width="250">Descripción</th>
                                                <th>Razon Social</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </section>
                                <section id="lecciones">
                                    <table class="mytable table table-condensed table-bordered table-okc-view oculto" width="100%" id="AcuLecciones">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Código</th>
                                                <th width="250">Descripción</th>
                                                <th>Usuario</th>
                                                <th>Fecha</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </section>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <fieldset class="group-importes"><legend><h6>Importes</h6></legend>
                            <table id="acu-totales" class="table-group">
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td id='texto'>Total</td>
                                        <td width='15px'>S/</td>
                                        <td id='input'>
                                            <input type="text" name="total" readOnly style="width: 100px; text-align: right"/>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </fieldset>
                    </div>                  
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                    </div>
                </div> --}}
            {{-- </form> --}}
        </div>
    </div>
</div>
@include('proyectos.acuCreate')
@include('proyectos.insumoModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/acu.js')}}"></script>
{{-- <script src="{{('/js/proyectos/acuCreate.js')}}"></script> --}}
<script src="{{('/js/proyectos/insumoModal.js')}}"></script>
@include('layout.fin_html')