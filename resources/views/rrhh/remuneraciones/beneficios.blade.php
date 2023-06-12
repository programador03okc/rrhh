@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="netos">
    <legend><h2>Remuneraciones de Beneficios - Planilla</h2></legend>
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
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body" style="background-color: white; padding: 10px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5 style="color: #3a3f42;">Empresa</h5>
                                    <select id="id_empresa" class="form-control input-sm">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        @foreach ($emp as $emp)
                                            <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <h5 style="color: #3a3f42;">Tipo Planilla</h5>
                                    <select id="id_tipo_planilla" class="form-control input-sm">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        @foreach ($plani as $plani)
                                            <option value="{{$plani->id_tipo_planilla}}">{{$plani->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <h5>Periodo</h5>
                                            <select id="periodo" class="form-control input-sm">
                                                <option value="0" selected disabled>Elija una opcion</option>
                                                @foreach ($peri as $peri)
                                                    <option value="{{$peri->id_periodo}}">{{$peri->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-7">
                                            <h5>Mes</h5>
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
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-flat btn-block btn-sm" style="margin-top: 34px;" onclick="processFilter();">Procesar Filtros</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body" style="height: auto; max-height: 500px; overflow-y: scroll; background-color: white; padding: 10px;">
                            <form action="{{ route('guardar-beneficios') }}" method="POST" id="formDatax">
                                <input type="hidden" name="_method" value="POST">
                                @csrf
                                <input type="hidden" name="data_empresa">
                                <input type="hidden" name="data_tipo_planilla">
                                <input type="hidden" name="data_periodo">
                                <input type="hidden" name="data_mes">

                                <table class="mytable table table-condensed table-bordered table-okc-view">
                                    <thead>
                                        <tr>
                                            <th>Datos del trabajador</th>
                                            <th width="180">Importe CTS</th>
                                            <th width="180">Importe Gratif.</th>
                                            <th width="180">Importe Vacac. Truncas</th>
											<th width="180">Bonific. ExtraOrd.</th>
											<th width="180">Otros Descuentos</th>
                                        </tr>
                                    </thead>
                                    <tbody id="result" style="color: #3a3f42;">
                                        <tr>
                                            <td colspan="6">No se encontraron resultados</td>
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
        var perio = $('#periodo').val();
        var mes = $('#mes').val();
        var row = '';

        if (empre > 0 && plani > 0 && perio.length > 0 && mes.length > 0) {
            var periodo = $('#periodo option:selected').text();
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: 'filter_trabajadores_beneficios',
                data: 'empresa=' + empre + '&planilla=' + plani + '&periodo=' + periodo + '&mes=' + mes + '&movim=' + 0,
                dataType: 'JSON',
                success: function(response) {
                    if (response.response == 'ok') {
                        var datax = response.data;
                        
                        datax.forEach(function (element, index) {
                            var cts = parseFloat(element.cts);
                            var gratificacion = parseFloat(element.gratificacion);
                            var vacaciones = parseFloat(element.vacaciones);
                            var bonificacion = parseFloat(element.bonificacion);
                            var descuento = parseFloat(element.descuento);
                            row += `<tr>
                                <td>
                                    <input type="hidden" name="id_trabajador[]" value="`+ element.id_trabajador +`" />
                                    `+ element.dts_trabajador +`
                                </td>
                                <td>
                                    <input type="number" name="cts[]" class="form-control" value="`+ cts.toFixed(2) +`" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                                <td>
                                    <input type="number" name="grati[]" class="form-control" value="`+ gratificacion.toFixed(2) +`" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                                <td>
                                    <input type="number" name="truncas[]" class="form-control" value="`+ vacaciones.toFixed(2) +`" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
								<td>
                                    <input type="number" name="bonif[]" class="form-control" value="`+ bonificacion.toFixed(2) +`" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                                <td>
                                    <input type="number" name="descuento[]" class="form-control" value="`+ descuento.toFixed(2) +`" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                            </tr>`;
                            $("#result").html(row);
                            $("[name=data_empresa]").val(empre);
                            $("[name=data_tipo_planilla]").val(plani);
                            $("[name=data_periodo]").val(periodo);
                            $("[name=data_mes]").val(mes);
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
</script>
@include('layout.fin_html')