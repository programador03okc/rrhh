@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="cola_atencion">
    <legend class="mylegend">
        <h2>Pendientes de Atención</h2>
        {{-- <ol class="breadcrumb">
            <li>
                <button type="button" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Atender Documento" 
                onClick="atender();">Atender </button>
                <button type="button" class="btn btn-danger" data-toggle="tooltip" 
                data-placement="bottom" title="Anular Documento" 
                onClick="anular();">Anular </button>
            </li>
        </ol> --}}
    </legend>
    <div class="row">
        <div class="col-md-6">
            <h5>Almacén</h5>
            <select class="form-control activation" name="id_almacen">
                <option value="0">Elija una opción</option>
                @foreach ($almacenes as $alm)
                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaPendientes">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th></th>
                        {{-- <th>Nro</th> --}}
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>Responsable</th>
                        <th>Grupo</th>
                        <th width="20%">Concepto</th>
                        <th width="25%">Area/Proyecto</th>
                        <th width="12%">Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

        </div>
    </div>
</div>
@include('almacen.reportes.req_atencionModal')
@include('almacen.guias.guia_ven_create')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/cola_atencion.js')}}"></script>
<script src="{{('/js/almacen/req_atencionModal.js')}}"></script>
{{-- <script src="{{('/js/almacen/guia_ven_create.js')}}"></script> --}}
@include('layout.fin_html')