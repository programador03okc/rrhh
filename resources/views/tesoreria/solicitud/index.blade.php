@extends('tesoreria.layout.tesoreria')

@php($responsableSeccion = [7,22])

@php($pagina = ['titulo' => 'Tesoreria > Solicitudes', 'tiene_menu' => true, 'slug' => 'solicitudes'])

@php($modals['modalSolicitud'] = ['id' => 'modalSolicitud', 'titulo' => 'Solicitud', 'class' => '', 'style' => 'width: 90%'])
@php($modals['modalPartidas'] = ['id' => 'modalPartidas', 'titulo' => 'Partidas Presupuestales', 'class' => 'modal-dialog-centered '])

@section($modals['modalSolicitud']['id'])
	<div class="row">
		<div class="col-md-{{ $adm?'8':'12' }} mx-auto">
			<form id="frmAccion" type="register" form="formulario">
				<input type="hidden" name="reg_id" id="reg_id" primary="ids" class="oculto">
				<input type="hidden" name="reg_code" id="reg_code">
				<div class="form-group row">
					<div class="col-sm-6">
						<label for="reg_tipo">Tipo</label>
						<select name="reg_tipo" id="reg_tipo" class="form-control activation"
								onChange="llenarOtroCombo('reg_subtipo', '{{ route('ajax.sol_subtipos',['::v']) }}', {v: this.value}, [{value: 'id', text: 'descripcion'}] );"
								required>
							<option value="">Elija una opción</option>
							@foreach ($solicitud_tipos as $tipo)
								<option value="{{$tipo->id}}">{{$tipo->descripcion}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-6">
						<label for="reg_subtipo">Sub Tipo</label>
						<select name="reg_subtipo" id="reg_subtipo" class="form-control activation" required>
							<option value="">Elija una opción</option>
						</select>
					</div>

				</div>
				<div class="form-group row">
					<div class="col-sm-12">
						<label for="reg_detalle">Detalle General</label>
						<input type="text" name="reg_detalle" id="reg_detalle" class="form-control activation"
							   placeholder="..." required>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-sm-4">
						<label for="reg_empresa">Empresa</label>
						<select name="reg_empresa" id="reg_empresa" class="form-control activation"
								onChange="llenarOtroCombo('reg_sede', '{{ route('ajax.sedes',['::v']) }}', {v: this.value}, [ {value: 'id_sede', text: 'descripcion'}] );"
								required>
							<option value="">Elija una opción</option>
							@foreach ($empresas as $empresa)
								<option value="{{$empresa->id_empresa}}">{{$empresa->contribuyente->razon_social}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-4">
						<label for="reg_sede">Sede</label>
						<select name="reg_sede" id="reg_sede" class="form-control activation"
								onChange="llenarOtroCombo('reg_area', '{{ route('ajax.areas',['::v']) }}', {v: this.value}, [{text: 'descripcion', campo: 'areas'}, {value: 'id_area', text: 'descripcion'}] );"
								required>
							<option value="">Elija una opción</option>
						</select>
					</div>
					<div class="col-sm-4">
						<label for="reg_area">Area</label>
						<select name="reg_area" id="reg_area" class="form-control activation" required>
							<option value="">Elija una opción</option>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-sm-4">
						<label for="reg_moneda">Moneda</label>
						<select name="reg_moneda" id="reg_moneda" class="form-control activation"
								onchange="cambioMoneda();" required>
							@foreach ($monedas as $moneda)
								<option data-simbolo="{{ $moneda->simbolo }}"
										value="{{$moneda->id_moneda}}">{{ $moneda->simbolo }} {{ $moneda->descripcion }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-4">
						<label for="reg_importe">Importe</label>
						<div class="input-group">
							<span class="input-group-addon esMoneda"></span>
							<input name="reg_importe" id="reg_importe" type="number"
								   class="form-control text-right activation" min="0" value="0" step="0.01" required readonly>
						</div>
					</div>
					<div class="col-sm-4">
						<label for="reg_prioridad">Prioridad</label>
						<select name="reg_prioridad" id="reg_prioridad" class="form-control activation" required>
							@foreach ($prioridades as $prioridad)
								<option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-12">
						<label for="reg_usuario_final">Asignado a:</label>
						<select name="reg_usuario_final" id="reg_usuario_final" class="form-control activation">
							<option value="">Elija una opción</option>
							@foreach ($trabajadores as $trabajador)
								<option value="{{ $trabajador->id_trabajador }}">{{ $trabajador->postulante->persona->nombre_completo }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</form>

		</div>
		@if($adm)
			<div class="col-md-4 mx-auto">
				<div class="panel panel-danger" id="panelAdministracion">
					<div class="panel-heading">Acciones de Administracion</div>
					<div class="panel-body">
						@if(Auth::user()->hasAnyRole( $responsableSeccion ))
						<form method="post" action="{{ route('tesoreria.planillapagos.ordinario') }}">
							@csrf
							<input type="hidden" value="" name="idSolicitud">
							<div class="form-group col-sm-12" style="display: none;">
								<button type="submit" id="btnAdminProcesarPago" class="btn btn-block btn-lg btn-danger">
									<i class="fas fa-forward"></i> Procesar Pago <i class="fas fa-forward"></i>
								</button>
							</div>
						</form>
						@endif
						<div class="form-group col-sm-12">
							<textarea name="adm_comentarios" id="adm_comentarios" placeholder="Escriba un comentario..." class="form-control" rows="2"></textarea>
						</div>
						<div class="form-group col-sm-6">
							<button id="btnAdminAprobado" onclick="cambiarEstado(2);" class="btn btn-block btn-lg btn-success">
								<i class="fas fa-check"></i> Aprobado
							</button>
						</div>
						<div class="form-group col-sm-6">
							<button id="btnAdminDenegado" onclick="cambiarEstado(4);" class="btn btn-block btn-lg btn-danger">
								<i class="fas fa-times"></i> Denegado
							</button>
						</div>
						<div class="form-group col-sm-12">
							<label for="adm_cambiar_estado">Otro Estado:</label>
							<select name="adm_cambiar_estado" id="adm_cambiar_estado" onchange="cambiarEstado(this.value)" class="form-control activation" required>
								@foreach ($estados as $estado)
									<option value="{{ $estado->id_estado_doc }}">{{ $estado->estado_doc }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
		@endif
	</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-12">
						<h4>Detalle de Solicitud</h4>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-sm-5">
						<input name="reg_det_descripcion" id="reg_det_descripcion" type="text" class="form-control  input-sm" placeholder="Descripcion detallada">
					</div>
					<div class="col-sm-3">

						<div class="input-group" id="ctrlPartidas" style="cursor: pointer;">
							<input type="hidden" id="reg_det_partida_id" name="reg_det_partida_id" value="">
							<input name="reg_det_partida" id="reg_det_partida" type="text" class="form-control" placeholder="Partida Pres." readonly="">
							<span class="input-group-addon" title="Buscar Partidas">
							<i class="fas fa-search"></i>
						</span>
						</div>

					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<span class="input-group-addon esMoneda"></span>
							<input name="reg_det_estimado" id="reg_det_estimado" type="number"
								   class="form-control text-right activation" min="0" step="0.01" placeholder="Cost. Est.">
						</div>
					</div>
					<div class="col-sm-1">
						<button type="button" class="btn btn-success" id="btnAddDet">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<table class="table table-striped table-bordered" id="listaSolicitudDetalles" width="100%">
							<thead>
							<tr>
								<th></th>
								<th>Descripcion</th>
								<th>Partida</th>
								<th>Cost. Est.</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
@stop

@section($modals['modalPartidas']['id'])
	<div class="row">
		<div class="col-md-12 mx-auto" id="listaPartidas">



		</div>
	</div>
@stop



@section('cuerpo_seccion')
	<legend class="mylegend">
		<h2>Estado de solicitudes</h2>
		<ol class="breadcrumb">
			<li>Solicitud</li>
			<li>Estado</li>
		</ol>
	</legend>
	<div class="row">
		<div class="col-md-12">
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped table-bordered" id="listaSolicitudes">
				<thead>
				<tr>
					<th></th>
					<th>Codigo</th>
					<th>Tipo</th>
					<th>Detalle</th>
					<th>Importe</th>
					<th>Fecha</th>
					<th>Empresa / Sede / Area</th>
					<th>Observacion</th>
					<th>Estado</th>

				</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
@stop


@section('scripts_seccion')
	<script type="text/javascript">

		$('#reg_usuario_final').select2({
            placeholder: "Seleccionar ...",
            allowClear: true,
            dropdownAutoWidth: true,
            dropdownParent: $("#modalSolicitud")
        });


        let vardataTables = funcDatatables();

        let dataSolicitudes = $('#listaSolicitudes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            select: true,
            order: [],
            // 'processing': true,
            'ajax': {
                url: '{{ route('ajax.solicitudes') }}',
				@if($tipo_solicitud !== null)
					data: {
					    filtro: JSON.stringify([
                            {campo: 'estado_id', condicion: '', valor: '{{ $tipo_solicitud }}'}
                        ])
					}
				@endif
			},
            'columns': [

                {'data': 'id'},
                {'data': 'codigo'},
                {
                    'data': 'subtipo', 'render': function (subtipo) {
                        htmlRet = '<strong>' + subtipo.tipo.descripcion + '</strong><br>';
                        htmlRet += '<em>' + subtipo.descripcion + '</em>';
                        return htmlRet;
                    }
                },
                {'data': 'detalle'},
                {
                    'data': null, className: 'text-right', render: function (data) {
                        return data.moneda.simbolo + ' ' + data.importe;
                    }
                },
                {'data': 'fecha_humanos'},
                {
                    'data': 'area', render: function (area) {
                        htmlRet = '<strong>' + area.grupo.sede.empresa.contribuyente.razon_social + '</strong>' +
                            ' <small class="text-muted">' + area.grupo.sede.descripcion + '</small><br>';
                        htmlRet += '<em class="text-uppercase">' + area.grupo.descripcion + '</em> <small class="text-muted text-capitalize">' + area.descripcion + '</small>';
                        return htmlRet;
                    }
                },
                {'data': 'observacion'},
                {'data': 'estado', render: function (data) {
						return '<span class="label label-' + data.bootstrap_color + '">' + data.estado_doc + '</span>';
                    }},
            ],
        "rowCallback": function( row, data, index ) {
            $('td:eq(0)',row).html(index + 1);
        }
        });

        let dataSolicitudDetalle = $('#listaSolicitudDetalles').DataTable({
            //dom: vardataTables[1],
            //buttons: vardataTables[2],
            language: vardataTables[0],
            select: true,
            paging:   false,
            info:     false,
            searching: false,
            columns: [
				{data: 'partida_id'},
				{data: 'descripcion', width: '60%'},
				{data: 'partida', width: '15%'},
				{data: 'estimado', width: '15%'},
				{data: null, width: '10%', sortable:false, render:function () {
						return '<button class="btnEliminarFila btn btn-danger"><i class="fas fa-trash"></i></button>';
                    }},
			]

        });

        dataSolicitudDetalle.on( 'draw', function () {
            //console.log( 'Redraw occurred at: '+new Date().getTime() );
			let suma = dataSolicitudDetalle.column( 3 )
                .data()
                .reduce( function (a, b) {
                    return Number(a) + Number(b);
                }, 0 );
			$('#reg_importe').val(suma);
			console.log(suma);
        } );

        $('#listaSolicitudes tbody').on('click', 'tr', function () {

            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#listaSolicitudes').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }


            let tolSel = $('.eventClick');
            if (tolSel.length == 0) {
                changeStateButton('inicio');
            } else {
                changeStateButton('historial');
            }

        });

        $('#listaSolicitudes tbody').on('dblclick', 'tr', function () {

            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('#listaSolicitudes').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            $('#btnHistorial').click();


        });

        // ########## ACCIONES BOTONES MODAL #####
        $('#btnNuevo').on('click', function () {

			@if($adm)
            $('#panelAdministracion').find("*").prop("disabled", true);
			@endif
            $('#modalSolicitud').modal('show')
        });
        $('#btnEditar').on('click', function () {
            let data = dataSolicitudes.row('.eventClick').data();
            llenarFormulario(data);
            console.log(data);
			@if($adm)
            $('#panelAdministracion').find("*").prop("disabled", true);
			@endif
            $('#modalSolicitud').modal('show');
            let found = [1,3].includes(data.estado_id);
            if(!found){
                Swal.fire({
                    title: 'Info!',
                    html: '<span>No se permite editar esta solicitud porque fue <strong>'+data.estado.estado_doc + '</strong></span>',
                    type: 'info',
                    confirmButtonText: 'OK'
                });
                $('#modalSolicitud').find("*").prop("disabled", true);
                $('#modalSolicitud').find("#btnCancelar_modalSolicitud").prop("disabled", false);
                throw new Error('No se Puede Editar');
            }
        });
        $('#btnHistorial').on('click', function () {
            let data = dataSolicitudes.row('.eventClick').data();
            llenarFormulario(data);
            //console.log(data);
            $('#reg_id').val(data.id);
            $('#modalSolicitud').find("*").prop("disabled", true);
            $('#modalSolicitud').find("#btnCancelar_modalSolicitud").prop("disabled", false);
			@if($adm)
            $('#panelAdministracion').find("*").prop("disabled", false);
			@endif
            $('#modalSolicitud').modal('show');

            throw new Error('Visualizar Contenido');
        });

        $('#btnGuardar_modalSolicitud').on('click', function () {
            $('#btnGuardar').click();
        });


        $('#modalSolicitud').on('hide.bs.modal', function (e) {
            // do something...
            limpiarFormulario();

            dataSolicitudDetalle.clear().draw();

            $('#modalSolicitud').find("*").prop("disabled", false);
            $('#btnCancelar').click();
        });


        $('#ctrlPartidas').on('click', function () {
            //$('#modalPartidas').modal('show');
            Swal.fire({
                title: '<strong>Partidas Presupuestales</strong>',
                width: '80%',
                //type: 'error',
                html: $('#listaPartidas').html(),
                confirmButtonText: 'Revisar'
            });
        });


        $('#btnAddDet').on('click', function () {
            let descripcion = $('#reg_det_descripcion').val();
            let partida = $('#reg_det_partida').val();
            let partida_id = $('#reg_det_partida_id').val();
            let estimado = $('#reg_det_estimado').val();

            if((descripcion == '') || (partida == '') || (estimado =='')){
                Swal.fire({
                    title: '<strong>Datos faltantes</strong>',
                    type: 'error',
                    html: '',
                    confirmButtonText: 'Revisar'
                });
			}
            else{
                dataSolicitudDetalle.row.add(
                    {partida_id: partida_id, descripcion: descripcion, partida: partida, estimado: estimado}
                ).draw( false );

                $('#reg_det_descripcion').val();
                $('#reg_det_partida').val();
                $('#reg_det_partida_id').val();
                $('#reg_det_estimado').val();
			}


        });

        $('#listaSolicitudDetalles').on("click", ".btnEliminarFila", function(){
            //console.log($(this).parent());
            dataSolicitudDetalle.row($(this).parents('tr')).remove().draw(false);
        });

		@if($adm)
        $('#btnAdminProcesarPago').on('click', function () {

        });

        function cambiarEstado(estado_id) {
            let codeReg = $('#reg_code').val();
            let comentario = $('#adm_comentarios').val();
            let idReg = $('#reg_id').val();
            baseUrl = '{{ route('tesoreria.solicitud.update', '::v') }}';
            baseUrl = baseUrl.replace('::v', idReg);
            baseMethod = 'PATCH';

            $.ajax({
                type: baseMethod,
                url: baseUrl,
                data: {
                    codigo: codeReg,
					estado: estado_id,
					observacion: comentario
				},
                dataType: 'JSON',
                success: function (response) {
                    if (response.error) {
                        console.log(response.msg)
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            type: 'error',
                            confirmButtonText: 'Revisar'
                        });
                        throw new Error('Error en el procesamiento de datos');
                        //changeStateButton('guardar');
                    } else {
                        let timerInterval;
                        Swal.fire({
                            type: 'success',
                            title: 'Completado!',
                            footer: 'Cerrando en <strong></strong> segundos',
                            html: 'Actualizacion Exitosa.',
                            timer: 2000,
                            onBeforeOpen: () => {
                                //let tFaltante = Math.round(1.56) // Swal.getTimeLeft();
                                Swal.showLoading();
                                timerInterval = setInterval(() => {
                                    Swal.getFooter().querySelector('strong')
                                        .textContent = Math.ceil((Swal.getTimerLeft() / 1000)); //parseFloat((Swal.getTimerLeft()/1000).toFixed(2));// Math.round(Swal.getTimerLeft() /2 , 0);
                                }, 100)
                            },
                            onClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            if (
                                // Read more about handling dismissals
                                result.dismiss === Swal.DismissReason.timer
                            ) {
                                console.log('Cerrado Automaticamente - Swal');
                                dataSolicitudes.ajax.reload();

                                $('#modalSolicitud').modal('hide');
                            }
                        })
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {                    if(jqXHR.status == 403){
                        /*
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            type: 'error',
                            confirmButtonText: 'Revisar'
                        });
                        */
                        Swal.fire({
                            title: 'No Autorizado!',
                            text: jqXHR.responseJSON.message,
                            imageUrl: '{{ asset('images/guard_man.png') }}',
                            imageWidth: 100,
                            imageHeight: 100,
                            backdrop: 'rgba(255,0,13,0.4)'


                        })
					}
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
            });
        }
		@endif

        // ### ACCIONES BOTON MODAL 2




		// ###### Acciones SELECT

		$('#reg_area').on('change', function () {
		    //console.log($(this).val());
			//console.log(value);

            baseUrl = '{{ route('ajax.presupuesto', '::v') }}';
            baseUrl = baseUrl.replace('::v', $(this).val() );
            baseMethod = 'GET';

            $.ajax({
                type: baseMethod,
                url: baseUrl,
                //data: data,
                dataType: 'JSON',
                success: function (response) {

                    $('#listaPartidas').html(response);

                    /*let htmlR = ''
                    $.each(response, function( index, value ) {
                        $.each(value, function( index2, value2 ) {
                            htmlR += '<div class="panel panel-default">' +
								'<div class="panel-heading" data-toggle="collapse" data-target="#collapse' + value2.codigo + '" aria-expanded="true" aria-controls="collapse' + value2.codigo + '">' +
								'<h3 class="panel-title">' + value2.descripcion + '</h3>' +
								'</div>' +
								'<div class="panel-body" id="collapse' + value2.codigo + '" class="collapse show" aria-labelledby="headingOne" data-parent="#listaPartidas">' +
								'<table class="table table-striped table-bordered" id="listaPartidas">\n' +
                                '\t\t\t\t<thead>\n' +
                                '\t\t\t\t<tr>\n' +
                                '\t\t\t\t\t<th>Codigo</th>\n' +
                                '\t\t\t\t\t<th>Descripcion</th>\n' +
                                '\t\t\t\t\t<th>Importe</th>\n' +
                                '\t\t\t\t</tr>\n' +
                                '\t\t\t\t</thead>\n' +
								'\t\t\t\t<tbody>\n' +
								'<tr><th>' + value2.codigo + '</th><td>' + value2.descripcion + '</td><td>' + value2.total + '</td></tr>';
                            htmlR += '\t\t\t\t</tbody>\n' +
                                '\t\t\t</table>' +
								'</div>' +
								'</div>';

                            console.log(value2.hijos_recursivo);

                            let varAR = flatten(value2.hijos_recursivo);

                            console.dir(varAR);


                        });
                    });
                    console.log(response.length)
					$('#listaPartidas').html(htmlR);*/
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {                    if(jqXHR.status == 403){
                        /*
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            type: 'error',
                            confirmButtonText: 'Revisar'
                        });
                        */
                        Swal.fire({
                            title: 'No Autorizado!',
                            text: jqXHR.responseJSON.message,
                            imageUrl: '{{ asset('images/guard_man.png') }}',
                            imageWidth: 100,
                            imageHeight: 100,
                            backdrop: 'rgba(255,0,13,0.4)'


                        })
					}
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
            });

        });

        function apertura(id_presup){
            if ($("#pres-"+id_presup+" ").attr('class') == 'oculto'){
                $("#pres-"+id_presup+" ").removeClass('oculto');
                $("#pres-"+id_presup+" ").addClass('visible');
            } else {
                $("#pres-"+id_presup+" ").removeClass('visible');
                $("#pres-"+id_presup+" ").addClass('oculto');
            }
        }

        function selectPartida(id_partida){
            var codigo = $("#par-"+id_partida+" ").find("td[name=codigo]")[0].innerHTML;
            var descripcion = $("#par-"+id_partida+" ").find("td[name=descripcion]")[0].innerHTML;

            console.log(codigo);

            //$('#modalPartidas').modal('hide');
            Swal.close()
            $('[name=reg_det_partida_id]').val(id_partida);
            $('[name=reg_det_partida]').val(codigo);
            //$('[name=des_partida]').val(descripcion);
        }

        // ######  ACCION MOSTRAR ANULADOS

		function mostrarData(val) {
			let urlMostrar = '{{ route('ajax.solicitudes') }}';
            urlMostrar = urlMostrar.replace('::v', val);
            console.log(urlMostrar);

            let filtro = [
				{campo: 'estado_id', condicion: '', valor: val}
			];


            $.get( urlMostrar, {filtro: JSON.stringify(filtro) } ).done(function( data ) {
                dataSolicitudes.clear().draw();
                dataSolicitudes.rows.add(data.data); // Add new data
                dataSolicitudes.columns.adjust().draw(); // Redraw the DataTable
			});



            //console.log(dataPlanillaPagos);

            //dataPlanillaPagos.fnClearTable();

            //dataPlanillaPagos.ajax(urlMostrar);

        }

        // ############# ACCIONES DE BOTONES ###########

        function guardarSolicitud(data, action) {
            let error = false;
            let htmlE = '';
            let jsonDetalle = [];

            $("#frmAccion [required]").each(function () {
                if ($(this).val() === '') {
                    $(this).parent().removeClass('has-success');
                    $(this).parent().addClass('has-error');
                    htmlE += '<li class="list-group-item">' + $(this).parent().find('label').text() + '</li>\n';
                    error = true;
                } else {
                    $(this).parent().removeClass('has-error');
                    $(this).parent().addClass('has-success');
                }
            });

            if ( ! dataSolicitudDetalle.data().any() ) {
                error = true;
            }
            else{
                dataSolicitudDetalle.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                    var datRow = this.data();
                    //console.log(datRow);
                    jsonDetalle.push(datRow);
                    // ... do something with data(), or this.node(), etc
                } );
			}

            jsonDetalle = JSON.stringify(jsonDetalle);

            //if (dataSolicitudDetalle.items)

            if (error) {

                Swal.fire({
                    title: '<strong>Datos faltantes</strong>',
                    type: 'error',
                    html: '<ul class="list-group">\n' + htmlE + '</ul>',
                    confirmButtonText: 'Revisar'
                });
                throw new Error('Datos faltantes en el formulario');
            } else {

                data += '&detalle_solicitud=' + jsonDetalle;
                console.log(data);
                if (action == 'register') {
                    baseUrl = '{{ route('tesoreria.solicitud.store') }}';
                    baseMethod = 'POST';
                } else if (action == 'edition') {
                    let idReg = $('#reg_id').val();
                    baseUrl = '{{ route('tesoreria.solicitud.update', '::v') }}';
                    baseUrl = baseUrl.replace('::v', idReg);
                    baseMethod = 'PATCH';
                }
                console.log(baseUrl);
                $.ajax({
                    type: baseMethod,
                    url: baseUrl,
                    data: data,
                    dataType: 'JSON',
                    success: function (response) {
                        if (response.error) {
                            console.log(response.msg)
                            Swal.fire({
                                title: 'Error!',
                                text: response.msg,
                                type: 'error',
                                confirmButtonText: 'Revisar'
                            });
                            throw new Error('Error en el procesamiento de datos');
                            //changeStateButton('guardar');
                        } else {
                            let timerInterval;
                            Swal.fire({
                                type: 'success',
                                title: 'Completado!',
                                footer: 'Cerrando en <strong></strong> segundos',
                                html: 'Datos registrados exitosamente.',
                                timer: 3000,
                                onBeforeOpen: () => {
                                    //let tFaltante = Math.round(1.56) // Swal.getTimeLeft();
                                    Swal.showLoading();
                                    timerInterval = setInterval(() => {
                                        Swal.getFooter().querySelector('strong')
                                            .textContent = Math.ceil((Swal.getTimerLeft() / 1000)); //parseFloat((Swal.getTimerLeft()/1000).toFixed(2));// Math.round(Swal.getTimerLeft() /2 , 0);
                                    }, 100)
                                },
                                onClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                if (
                                    // Read more about handling dismissals
                                    result.dismiss === Swal.DismissReason.timer
                                ) {
                                    console.log('Cerrado Automaticamente - Swal');
                                    dataSolicitudes.ajax.reload();

                                    $('#modalSolicitud').modal('hide');
                                }
                            })
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {                    if(jqXHR.status == 403){
                        /*
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            type: 'error',
                            confirmButtonText: 'Revisar'
                        });
                        */
                        Swal.fire({
                            title: 'No Autorizado!',
                            text: jqXHR.responseJSON.message,
                            imageUrl: '{{ asset('images/guard_man.png') }}',
                            imageWidth: 100,
                            imageHeight: 100,
                            backdrop: 'rgba(255,0,13,0.4)'


                        })
					}
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        }

        function anularSolicitud(id) {
            id = dataSolicitudes.row('.eventClick').data().id;
            if (id > 0) {
                let idReg = id;
                baseUrl = '{{ route('tesoreria.solicitud.destroy', '::v') }}';
                baseUrl = baseUrl.replace('::v', idReg);
                let baseMethod = 'DELETE';

                $.ajax({
                    type: baseMethod,
                    url: baseUrl,
                    //data: data,
                    dataType: 'JSON',
                    success: function (response) {
                        if (response.error) {
                            console.log(response.msg)
                            Swal.fire({
                                title: 'Error!',
                                text: response.msg,
                                type: 'error',
                                confirmButtonText: 'Revisar'
                            });
                            throw new Error('Error en el procesamiento de datos');
                            //changeStateButton('guardar');
                        } else {
                            let timerInterval;
                            Swal.fire({
                                type: 'success',
                                title: 'Completado!',
                                footer: 'Cerrando en <strong></strong> segundos',
                                html: 'Documento Anulado.',
                                timer: 3000,
                                onBeforeOpen: () => {
                                    //let tFaltante = Math.round(1.56) // Swal.getTimeLeft();
                                    Swal.showLoading();
                                    timerInterval = setInterval(() => {
                                        Swal.getFooter().querySelector('strong')
                                            .textContent = Math.ceil((Swal.getTimerLeft() / 1000)); //parseFloat((Swal.getTimerLeft()/1000).toFixed(2));// Math.round(Swal.getTimerLeft() /2 , 0);
                                    }, 100)
                                },
                                onClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                if (
                                    // Read more about handling dismissals
                                    result.dismiss === Swal.DismissReason.timer
                                ) {
                                    console.log('Cerrado Automaticamente - Swal');

                                    dataSolicitudes.ajax.reload();

                                    $('#modalSolicitud').modal('hide');
                                }
                            })
                            //changeStateButton('guardar');
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {                    if(jqXHR.status == 403){
                        /*
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            type: 'error',
                            confirmButtonText: 'Revisar'
                        });
                        */
                        Swal.fire({
                            title: 'No Autorizado!',
                            text: jqXHR.responseJSON.message,
                            imageUrl: '{{ asset('images/guard_man.png') }}',
                            imageWidth: 100,
                            imageHeight: 100,
                            backdrop: 'rgba(255,0,13,0.4)'


                        })
					}
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }


        }

        // ######## Funciones HELP #####

        function llenarFormulario(data) {
            console.log(data);
            $('#modalSolicitudLabel').text(data.codigo);
            $('#reg_id').val(data.id);
            $('#reg_code').val(data.codigo);

            $('#reg_tipo').val(data.subtipo.solicitudes_tipo_id).change();
            $('#reg_subtipo').val(data.subtipo.id).change();

            $('#reg_detalle').val(data.detalle).change();

            $('#reg_prioridad').val(data.prioridad_id).change();

            $('#reg_empresa').val(data.area.grupo.sede.id_empresa).change();
            $('#reg_sede').val(data.area.grupo.id_sede).change();
            $('#reg_area').val(data.area_id).change();

            $('#reg_moneda').val(data.moneda_id).change();
            $('#reg_importe').val(data.importe).change();

            //$('#reg_usuario_final').select2('val','');
            $('#reg_usuario_final').val(data.trabajador_id);
            $('#reg_usuario_final').select2().trigger('change');


            $.each(data.detalles, function (idx, val) {
                dataSolicitudDetalle.row.add(
                    {partida_id: val.partida_id, descripcion: val.descripcion, partida: val.partida.codigo, estimado: val.estimado}
                ).draw( false );
            });
			@if(Auth::user()->hasAnyRole( $responsableSeccion ) && ($tipo_solicitud !== null))
			if(data.estado_id === ('{{ $tipo_solicitud }}' *1 )){
                $('#btnAdminProcesarPago').parent().show();
                $('[name=idSolicitud]').val(data.id);
			}
			console.log([data.estado_id, '{{ $tipo_solicitud }}']);
			@endif
        }

        function limpiarFormulario() {
            $('#modalSolicitudLabel').text('Solicitud: ');
            $('#reg_id').val('');
            $('#reg_code').val('');

            $('#reg_tipo').val('');
            $('#reg_subtipo').val('');

            $('#reg_detalle').val('');

            $('#reg_prioridad').val('');
            $('#reg_empresa').val('');
            $('#reg_sede').val('');
            $('#reg_area').val('');

            $('#reg_moneda').val('');
            $('#reg_importe').val('');

            //$('#reg_usuario_final').select2('val','');

			@if(Auth::user()->hasAnyRole( $responsableSeccion ))
            $('#btnAdminProcesarPago').parent().hide();
            $('[name=idSolicitud]').val('');
			@endif
        }

	</script>

@stop



