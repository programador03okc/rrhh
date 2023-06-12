@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="netos">
    <legend><h2>Valores Netos - Planilla</h2></legend>
    <div class="row" id="planex">
		<div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="flash-message">
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <span class="close" data-dismiss="alert" aria-label="close">&times;</span>
                                {!! \Session::get('success') !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="box">
                        <div class="box-body" style="background-color: white; padding: 10px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Empresa</h5>
                                    <select id="id_empresa" class="form-control input-sm">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        @foreach ($emp as $emp)
                                            <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Tipo Planilla</h5>
                                    <select id="id_tipo_planilla" class="form-control input-sm">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        @foreach ($plani as $plani)
                                            <option value="{{$plani->id_tipo_planilla}}">{{$plani->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Mes</h5>
                                    <select id="mes" class="form-control input-sm">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        <option value="1">ENERO</option>
                                        <option value="2">FEBRERO</option>
                                        <option value="3">MARZO</option>
                                        <option value="4">ABRIL</option>
                                        <option value="5">MAYO</option>
                                        <option value="6">JUNIO</option>
                                        <option value="7">JULIO</option>
                                        <option value="8">AGOSTO</option>
                                        <option value="9">SETIEMBRE</option>
                                        <option value="10">OCTUBRE</option>
                                        <option value="11">NOVIEMBRE</option>
                                        <option value="12">DICIEMBRE</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Periodo</h5>
                                    <select id="periodo" class="form-control input-sm">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        @foreach ($peri as $peri)
                                            <option value="{{$peri->id_periodo}}">{{$peri->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Tipo de Proceso</h5>
                                    <select id="proceso" class="form-control input-sm" onchange="changeRoute(this.value);">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        <option value="1">SUELDOS NETOS</option>
                                        <option value="2">SEGURO DE LEY</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary btn-flat btn-sm" style="margin-top: 34px;" onclick="processFilter();">Procesar Filtros</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="box">
                        <div class="box-body" style="height: auto; max-height: 500px; overflow-y: scroll; background-color: white; padding: 10px;">
                            <form action="" method="POST" id="formDatax">
                                <input type="hidden" name="_method" value="POST">
                                @csrf
                                <input type="hidden" name="data_empresa">
                                <input type="hidden" name="data_tipo_planilla">
                                <input type="hidden" name="data_mes">
                                <input type="hidden" name="data_periodo">

                                <table class="mytable table table-condensed table-bordered table-okc-view">
                                    <thead>
                                        <tr>
                                            <th>Datos del trabajador</th>
                                            <th width="150">Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody id="result" style="color: #3a3f42;">
                                        <tr>
                                            <td colspan="2">No se encontraron resultados</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

@include('layout.footer')
@include('layout.scripts')
<script>
    $(document).ready(function(){
        $('.sidebar-mini').addClass('sidebar-collapse');
    });

    function processFilter() {
        var empre = $('#id_empresa').val();
        var plani = $('#id_tipo_planilla').val();
        var mes = $('#mes').val();
        var perio = $('#periodo').val();
        var proce = $('#proceso').val();
        var row = '';

        if (empre > 0 && plani > 0 && mes > 0 && perio > 0 && proce > 0){
            var periodo = $('#periodo option:selected').text();
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: 'filter_trabajadores',
                data: 'empresa=' + empre + '&planilla=' + plani + '&mes=' + mes + '&periodo=' + periodo + '&movim=' + 1,
                dataType: 'JSON',
                success: function(response) {
                    if (response.response == 'ok') {
                        var datax = response.data;
                        datax.forEach(function (element, index) {
                            row += `<tr>
                                <td>
                                    <input type="hidden" name="id_trabajador[]" value="`+ element.id_trabajador +`" />
                                    `+ element.apellido_paterno +` `+ element.nombres+`
                                </td>
                                <td>
                                    <input type="number" name="sueldo[]" class="form-control" value="0.00" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                            </tr>`;
                            $("#result").html(row);
                            $("[name=data_empresa]").val(empre);
                            $("[name=data_tipo_planilla]").val(plani);
                            $("[name=data_mes]").val(mes);
                            $("[name=data_periodo]").val(periodo);
                        });
                    } else {
                        alert('No se encontraron resultados para la busqueda');
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert('Debe seleccionar todos los campos');
        }
    }

    function changeRoute(value) {
        var ruta;
        if (value == 1) {
            ruta = "{{ route('guardar-netos') }}";
        } else {
            ruta = "{{ route('guardar-seguro') }}";
        }

        $("#formDatax").attr("action", ruta);
    }
</script>
@include('layout.fin_html')