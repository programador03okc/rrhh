@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body')
<div class="page-main" type="preseje">
    <legend class="mylegend">
        <h2>Presupuesto de Ejecución</h2>
        <ol class="breadcrumb">
            <li><label id="codigo"></label></li>
            {{-- <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Propuesta Cliente" 
                onClick="generar_propuesta();">Generar Propuesta </button>
                <button type="button" class="btn btn-secondary" data-toggle="tooltip" 
                data-placement="bottom" title="Ver Presupuesto Interno" 
                onClick="abrir_propuesta();">Ver Presupuesto </button>
            </li> --}}
        </ol>
    </legend>
    <form id="form-preseje" type="register" form="formulario">
        <div class="row">
            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
            <input type="text" class="oculto" name="id_presupuesto" primary="ids">
            <input type="text" class="oculto" name="id_empresa">
            {{-- 1 Presupuesto Interno --}}
            <input type="text" class="oculto" name="id_tp_presupuesto" value="1">
            <input type="text" class="oculto" name="elaborado_por">
            <div class="col-md-6">
                <label>Seleccione Opcion Comercial</label>
                <div class="input-group-okc">
                    <input class="oculto" name="id_op_com" >
                    <input type="text" class="form-control" aria-describedby="basic-addon2" 
                        readonly name="nombre_opcion" disabled="true">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text activation" id="basic-addon2"
                            onClick="open_opcion_modal();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-3">
                <label>Cliente</label>
                <input type="text" class="form-control" name="razon_social"/>
            </div> --}}
            <div class="col-md-4">
                <label>Importe (Op.Comercial)</label>
                <div style="display:flex;">
                    <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readOnly/>
                    <input type="number" name="importe" class="form-control group-elemento activation" style="text-align:right;" disabled="true"/>
                    <select class="form-control group-elemento activation" name="moneda" onChange="sim_moneda();" disabled="true">
                        <option value="0">Elija una opción</option>
                        @foreach ($monedas as $mon)
                            <option value="{{$mon->id_moneda}}">{{$mon->descripcion}} - {{$mon->simbolo}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <label>Fecha Emisión</label>
                <input type="date" name="fecha_emision" class="form-control activation" disabled="true"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <table class="tabla-totales" width="100%">
                    <tbody>
                        <tr>
                            <td width="50%">Costo Directo</td>
                            <td width="20%"></td>
                            <td><input type="number" class="importe" name="total_costo_directo" disabled="true" value="0"/></td>
                            <td><input type="number" class="importe green" name="total_costo_directo_pc" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td>Costo Indirecto</td>
                            <td>
                                <input type="number" class="porcen activation" name="porcentaje_ci" disabled="true" value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe" name="total_ci" disabled="true" value="0"/></td>
                            <td><input type="number" class="importe green" name="total_ci_pc" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td>Gastos Generales</td>
                            <td>
                                <input type="number" class="porcen activation" name="porcentaje_gg" disabled="true" value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe" name="total_gg" disabled="true" value="0"/></td>
                            <td><input type="number" class="importe green" name="total_gg_pc" disabled="true" value="0"/></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>SubTotal</strong></td>
                            <td></td>
                            <td><input type="number" class="importe" name="sub_total" disabled="true" value="0"/></td>
                            <td><input type="number" class="importe green" name="sub_total_pc" disabled="true" value="0"/></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <table class="tabla-totales" width="100%">
                    <tbody>
                        <tr>
                            <td width="50%">SubTotal</td>
                            <td width="20%"></td>
                            <td><input type="number" class="importe" name="subtotal" disabled="true" value="0"/></td>
                            <td><input type="number" class="importe green" name="subtotal_pc" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td>Utilidad</td>
                            <td>
                                <input type="number" class="porcen activation" name="porcentaje_utilidad" disabled="true" value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe" name="total_utilidad" disabled="true" value="0"/></td>
                            <td><input type="number" class="importe green" name="total_utilidad_pc" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td>IGV</td>
                            <td>
                                <input type="number" class="porcen" name="porcentaje_igv" disabled="true" value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe" name="total_igv" disabled="true" value="0"/></td>
                            <td><input type="number" class="importe green" name="total_igv_pc" disabled="true" value="0"/></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>Total Presupuestado</strong></td>
                            <td></td>
                            <td><input type="number" class="importe" name="total_presupuestado" disabled="true" value="0"/></td>
                            <td><input type="number" class="importe green" name="total_presupuestado_pc" disabled="true" value="0"/></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12">
            <div id="tab-preseje">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a type="#cd">Costos Directos</a></li>
                    <li class=""><a type="#ci">Costos Indirectos</a></li>
                    <li class=""><a type="#gg">Gastos Generales</a></li>
                </ul>
                <div class="content-tabs">
                    <section id="cd" hidden>
                        <form id="form-cd" type="register">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaCD">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>P.Unit</th>
                                        <th>P.Parcial</th>
                                        <th>SubTotal</th>
                                        <th width="10%">
                                            <i class="fas fa-plus-square icon-tabla green boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Agregar Componente" onClick="agregar_componente_cd();"></i>
                                        </th>
                                        <th hidden>padre</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </form>
                    </section>
                    <section id="ci" hidden>
                        <form id="form-ci" type="register">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaCI">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>P.Unit</th>
                                        <th>Particip.</th>
                                        <th>Tiempo</th>
                                        <th>Veces</th>
                                        <th>P.Parcial</th>
                                        <th>SubTotal</th>
                                        <th width="10%">
                                            <i class="fas fa-plus-square icon-tabla green boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Agregar Componente" onClick="agregar_componente_ci();"></i>
                                        </th>
                                        <th hidden>padre</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </form>
                    </section>
                    <section id="gg" hidden>
                        <form id="form-gg" type="register">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaGG">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>P.Unit</th>
                                        <th>Particip.</th>
                                        <th>Tiempo</th>
                                        <th>Veces</th>
                                        <th>P.Parcial</th>
                                        <th>SubTotal</th>
                                        <th width="10%">
                                            <i class="fas fa-plus-square icon-tabla green boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Agregar Componente" onClick="agregar_componente_gg();"></i>
                                        </th>
                                        <th hidden>padre</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@include('proyectos.presupuesto.presejeModal')
@include('proyectos.presupuesto.partidaCDCreate')
@include('proyectos.presupuesto.partidaCICreate')
@include('proyectos.presupuesto.partidaGGCreate')
@include('proyectos.acuModal')
@include('proyectos.opcionModal')
@include('proyectos.presupuesto.presLeccion')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/presupuesto/preseje.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/presejeModal.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/partidaCDCreate.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/partidaCICreate.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/partidaGGCreate.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/compo_cd.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/compo_ci.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/compo_gg.js')}}"></script>
<script src="{{('/js/proyectos/acuModal.js')}}"></script>
<script src="{{('/js/proyectos/opcionModal.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/presLeccion.js')}}"></script>
@include('layout.fin_html')