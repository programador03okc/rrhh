@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="netos">
    <legend><h2>Planilla de Utilidades</h2></legend>
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
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-body" style="background-color: white; padding: 10px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Tipo de Filtro</h5>
                                    <select id="tipo_filtro" class="form-control input-sm">
                                        <option value="1">Trabajadores Activos</option>
                                        <option value="2">Trabajadores Cesados</option>
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
                                <div class="col-md-12">
                                    <h5 style="color: #3a3f42;">Empresa</h5>
                                    <select id="id_empresa" class="form-control input-sm">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        @foreach ($emp as $emp)
                                            <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Periodo</h5>
                                    <select id="id_periodo" class="form-control input-sm">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        @foreach ($peri as $periodo)
                                            <option value="{{$periodo->id_periodo}}">{{$periodo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Utilidad a Distribuir</h5>
                                    <input type="number" id="utilidad_dist" class="form-control input-sm" style="text-align: center;" value="0.00" step="any">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Total días laborados</h5>
                                    <input type="number" id="total_dias_lab" class="form-control input-sm" style="text-align: center;" value="0.00" step="any">
                                </div>
                                <div class="col-md-6">
                                    <h5 style="color: #3a3f42;">Suma Total Remuneraciones</h5>
                                    <input type="number" id="sum_total_remun" class="form-control input-sm" style="text-align: center;" value="0.00" step="any">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h5 style="color: #3a3f42;">Firma</h5>
                                    <select id="firma" class="form-control input-sm">
                                        <option value="1">SI</option>
                                        <option value="0">NO</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <button type="button" class="btn btn-primary btn-flat btn-block btn-sm" style="margin-top: 32px;" onclick="processFilter();">Procesar Filtros</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger btn-flat btn-block btn-sm" onclick="processBoleta();">Imprimir Boleta</button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-info btn-flat btn-block btn-sm" onclick="enviarBoleta();">Envios Correos</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="box">
                        <div class="box-body" style="height: auto; max-height: 500px; overflow-y: scroll; background-color: white; padding: 10px;">
                            <form action="{{ route('guardar-utilidades') }}" method="POST" id="formDatax">
                                <input type="hidden" name="_method" value="POST">
                                @csrf
                                <input type="hidden" name="data_empresa">
                                <input type="hidden" name="data_tipo_planilla">
                                <input type="hidden" name="data_periodo">
                                <input type="hidden" name="data_utilidad_dist">
                                <input type="hidden" name="data_total_dias_lab">
                                <input type="hidden" name="data_sum_total_remun">

                                <table class="mytable table table-condensed table-bordered table-okc-view">
                                    <thead>
                                        <tr>
                                            <th style="vertical-align: middle;">Datos del trabajador</th>
                                            <th width="110" style="text-align: center; vertical-align: middle;">N° Total Días laborados</th>
                                            <th width="110" style="text-align: center; vertical-align: middle;">Participación del trabajador (días)</th>
                                            <th width="110" style="text-align: center; vertical-align: middle;">Remuneración Total</th>
                                            <th width="110" style="text-align: center; vertical-align: middle;">Participación del trabajador (remuneración)</th>
                                            <th width="110" style="text-align: center; vertical-align: middle;">5ta Categoría</th>
                                            <th width="110" style="text-align: center; vertical-align: middle;">Participación por pagar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="result" style="color: #3a3f42;">
                                        <tr>
                                            <td colspan="7">No se encontraron resultados</td>
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

<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-correos">
    <div class="modal-dialog" style="width: 55%;">
        <div class="modal-content">
			<form action="{{ route('enviar-correos-utilidades') }}" method="POST">
				<input type="hidden" name="_method" value="POST">
                @csrf
				<input type="hidden" name="email_data_empresa">
				<input type="hidden" name="email_data_tipo_planilla">
				<input type="hidden" name="email_data_periodo">
				<input type="hidden" name="email_data_id_periodo">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title">Envío de Boletas por Correo</h3>
				</div>
				<div class="modal-body">
					<div class="row" id="result-mail">
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success">Procesar Envio</button>
				</div>
			</form>
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
        var perio = $('#id_periodo').val();
        var ut_di = $('#utilidad_dist').val();
        var td_la = $('#total_dias_lab').val();
        var st_re = $('#sum_total_remun').val();
        var filter = $('#tipo_filtro').val();
        var row = '';
        var data = '';

        if (empre > 0 && plani > 0 && perio > 0) {
            if (filter == 1) {
                data = 'empresa=' + empre + '&planilla=' + plani + '&periodo=' + perio + '&movim=' + 1;
            } else {
                data = 'empresa=' + empre + '&planilla=' + plani + '&periodo=' + perio + '&movim=' + 0;
            }

            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: 'filter_trabajadores',
                data: data,
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
                                    <input type="number" name="dias_lab_trabajador[]" class="form-control" value="0.00" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                                <td>
                                    <input type="number" name="dias_parti_trabajador[]" class="form-control" value="0.00" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                                <td>
                                    <input type="number" name="remun_total_trabajador[]" class="form-control" value="0.00" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                                <td>
                                    <input type="number" name="remun_parti_trabajador[]" class="form-control" value="0.00" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                                <td>
                                    <input type="number" name="renta_quinta[]" class="form-control" value="0.00" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                                <td>
                                    <input type="number" name="remun_parti_pagar[]" class="form-control" value="0.00" step="any" min="0" style="height: 25px; text-align: right;" />
                                </td>
                            </tr>`;
                            $("#result").html(row);
                            $("[name=data_filter]").val(filter);
                            $("[name=data_empresa]").val(empre);
                            $("[name=data_tipo_planilla]").val(plani);
                            $("[name=data_periodo]").val(perio);
                            $("[name=data_utilidad_dist]").val(ut_di);
                            $("[name=data_total_dias_lab]").val(td_la);
                            $("[name=data_sum_total_remun]").val(st_re);
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

    function processBoleta(){
        var empre = $('#id_empresa').val();
        var plani = $('#id_tipo_planilla').val();
        var perio = $('#id_periodo').val();
        var firma = $('#firma').val();
    
        if (empre > 0 && plani > 0 && perio > 0 ){
            var periodo = $('#periodo option:selected').text();
            window.open('reporte_planilla_utilidades/'+empre+'/'+plani+'/'+perio+'/'+firma);
        }else{
            alert('Debe seleccionar todos los campos');
        }
    }

    function enviarBoleta(){
        var empre = $('#id_empresa').val();
        var plani = $('#id_tipo_planilla').val();
        var perio = $('#id_periodo').val();
        var row = '';
    
        if (empre > 0 && plani > 0 && perio > 0){
            if (plani == 1){

                var periodo = $('#periodo option:selected').text();
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: 'filter_trabajadores',
                    data: 'empresa=' + empre + '&planilla=' + plani + '&periodo=' + periodo + '&movim=' + 4,
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.response == 'ok') {
                            var datax = response.data;
                            datax.forEach(function (element, index) {
                                row += `<div class="col-md-6">
                                    <div class="form-group" style="font-size: 10.5px">
                                        <input type="hidden" name="datos[]" value="`+ element.apellido_paterno +` `+ element.apellido_paterno +` `+ element.nombres +`">
                                        <input type="hidden" name="correo[]" value="` + element.correo + `">
                                        <input type="hidden" name="dni[]" value="` + element.nro_documento + `">
                                        
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="id_trabajador[]" value="`+ element.id_trabajador +`" checked>
                                                `+ element.apellido_paterno +` `+ element.apellido_paterno +` `+ element.nombres +` <br> (` + element.correo + `)
                                            </label>
                                        </div>
                                    </div>
                                </div>`;
                            });
                        }
                        $("#result-mail").html(row);
                        $("[name=email_data_empresa]").val(empre);
                        $("[name=email_data_tipo_planilla]").val(plani);
                        $("[name=email_data_periodo]").val(periodo);
                        $("[name=email_data_id_periodo]").val(perio);
                        $('#modal-correos').modal('show');
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }else{
                alert('Solo Régimen Común puede generar Boleta de Pagos');
            }
        }else{
            alert('Debe seleccionar los 3 primeros campos');
        }
    }    
</script>
@include('layout.fin_html')