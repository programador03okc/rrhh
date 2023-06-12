@include('layout.head')
@include('layout.menu_logistica')
@include('layout.body')
<div class="page-main" type="lista_requerimiento">
    <legend><h2>Lista de Requerimientos</h2></legend>
    <div class="row">
        <div class="col-sm-12">
            <!-- <caption>Requerimientos: Registrados | Aprobados</caption> -->
            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaReq" width="100%">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>CODIGO</th>
                        <th>FECHA</th>
                        <th>TIPO</th>
                        <th width="120">EMPRESA</th>
                        <th>AREA / PROYECTO</th>
                        <th>CREADO POR</th>
                        <th width="70">ESTADO</th>
                        <th width="90">ACCIÓN</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-flujo-aprob">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detalles del Requerimiento</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                <div class="col-md-12" id="req-detalle"></div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="flujo-detalle"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12" id="flujo-proximo"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('logistica.requerimientos.modal_adjuntar_archivos_requerimiento')
@include('logistica.requerimientos.aprobacion.modal_aprobacion')
@include('logistica.requerimientos.aprobacion.modal_obs')

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/logistica/listar_requerimiento.js')}}"></script>
<script src="{{('/js/logistica/aprobacion/aprobacion.js')}}"></script>
@include('layout.fin_html')