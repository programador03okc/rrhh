@php($tiene_menu = true)
@extends('tesoreria.main')

@section('cuerpo')
	<div class="page-main" type="caja_chica">
		<legend class="mylegend">
			<h2>Flujo de Caja Chica</h2>
			<ol class="breadcrumb">
				<li>Tesoreria</li>
				<li>Caja Chica</li>
				<li>Flujo</li>
			</ol>
		</legend>
		<div class="row">
			<div class="col-md-12">

			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<select id="sel_empresa" class="form-control" onChange="cambiarEmpresa(this.value);">
					<option value="0">Elija una opción</option>
					@foreach ($empresas as $empresa)
						<option value="{{$empresa->id_empresa}}">{{$empresa->contribuyente->razon_social}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-6">
				<select id="sel_caja" class="form-control" onchange="cargarDatatable();">
					<option value="0">Elija una opción</option>
				</select>
			</div>
			<div class="col-md-3">
				<input type="date" class="form-control activation" value="{{ date("Y-m-j") }}" id="sel_fecha" onchange="cargarDatatable();">
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="mytable table table-striped table-condensed table-bordered" id="listaFlujoCajaChica">
					<thead>
					<tr>
						<th></th>
						<th>#</th>
						<th>Tipo</th>
						<th>Mov/Sede</th>
						<th>Nº Doc</th>
						<th>Proveedor</th>
						<th>Moneda</th>
						<th>T. Cambio</th>
						<th>Importe</th>
						<th>Observacion</th>
						<th>Accion</th>
					</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>

		<div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog" aria-labelledby="modalRegistroLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="modalRegistroLabel">Editar Flujo</h4>
					</div>
					<div class="modal-body py-5">
						<div class="row">
							<div class="col-md-12 mx-auto">
								<form id="frmAccion" type="register" form="formulario">

									<div class="form-group row">
										<div class="col-sm-6">
											<label for="reg_tipo">Tipo</label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fas fa-sign-in-alt text-success"></i></span>
												<select id="reg_tipo" class="form-control">
													<option value="I">Ingreso</option>
													<option value="E">Egreso</option>
												</select>
											</div>
										</div>
										<div class="col-sm-6">
											<label for="reg_mov_sede">Mov / Sede</label>
											<select id="reg_mov_sede" class="form-control">
												<option value="0">Elija una opción</option>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label for="reg_num_docu">N° Documento</label>
											<input type="text" class="form-control right" id="reg_num_docu">
										</div>
										<div class="col-sm-6">
											<label for="reg_proveedor">Proveedor</label>
											<select class="form-control activation js-example-basic-single" id="reg_proveedor" disabled="true">
												<option value="0">Elija una opción</option>
												@foreach ($proveedores as $prov)
													<option value="{{$prov->id_proveedor}}">{{$prov->contribuyente->nro_documento}}
														- {{$prov->contribuyente->razon_social}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-4">
											<label for="reg_moneda">Moneda</label>
											<select id="reg_moneda" class="form-control" onchange="cambioMoneda();">
												@foreach ($monedas as $moneda)
													<option data-simbolo="{{ $moneda->simbolo }}" value="{{$moneda->id_moneda}}">{{ $moneda->simbolo }} {{ $moneda->descripcion }}</option>
												@endforeach
											</select>
										</div>
										<div class="col-sm-4">
											<label for="reg_t_cambio">T.Cambio</label>
											<div class="input-group">
												<span class="input-group-addon disabled">S/</span>
												<input id="reg_t_cambio" type="number" class="form-control text-right" name="price" min="0" value="0" step="0.01" disabled>
											</div>
										</div>
										<div class="col-sm-4">
											<label for="reg_importe">Importe</label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fas fa-dollar-sign"></i></span>
												<input id="reg_importe" type="number" class="form-control text-right" name="price" min="0" value="0" step="0.01">
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-12">
											<label for="reg_observacion">Observacion:</label>
											<textarea id="reg_observacion" class="form-control" rows="2"></textarea>
										</div>
									</div>
								</form>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary">Guardar</button>
					</div>
				</div>
			</div>
		</div>

	</div>



@stop

@section('scripts')
	<script src="//cdn.datatables.net/plug-ins/1.10.19/api/fnReloadAjax.js"></script>
	<script type="text/javascript">

        var dataFlujoCajaChica;
        $(function () {
            var vardataTables = funcDatatables();


            dataFlujoCajaChica = $('#listaFlujoCajaChica').dataTable({
                'dom': vardataTables[1],
                'buttons': vardataTables[2],
                'language': vardataTables[0],
                // 'processing': true,
                //'ajax': '{{ route('ajax.cajachica', 0) }}',
                'columns': [

                    {'data': 'id'},
                    {'data': null},
                    {
                        'data': 'tipo_movimiento', 'render': function (data) {
                            //console.log(data);
                            if (data == 'I') {
                                return '<i class="fas fa-sign-in-alt fa-2x text-success"></i>';
                            } else if (data == 'E') {
                                return '<i class="fas fa-sign-out-alt fa-2x text-danger"></i>';
                            }
                        }
                    },
                    {'data': 'doc_operacion.descripcion'},
                    {'data': 'doc_pago'},
                    {'data': 'proveedor_id'},
                    {'data': 'moneda.simbolo'},
                    {'data': 'tipo_cambio'},
                    {'data': 'importe'},
                    {'data': 'observaciones'},
                    {
                        "render": function (data, type, row, meta) {
                            //console.log([data, type, row, meta]);
                            var datRet = '';
                            datRet += '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" data-placement="bottom" title="Editar" >' +
                                '<i class="fas fa-edit" data-toggle="modal" data-idx="' + meta.row + '" data-id="' + row.id + '" data-fieldname="' + row.fieldname + '" data-target="#modalRegistro"></i>' +
                                '</button>' +
                                '';
                            return datRet;
                            return "<button type='button' class='btn btn-info btn-md' data-toggle='modal' data-id=\"" + full[0] + "\" data-target='#myModal'> Edit </button>";
                        }
                    },/*
                {'defaultContent':
                        '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Editar" >'+
                        '<i class="fas fa-edit"></i></button>'+
                        '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Anular" >'+
                        '<i class="fas fa-trash"></i></button>'+
                        '<button type="button" class="seguro btn btn-warning boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Seguros" >'+
                        '<i class="fas fa-file-upload"></i></button>'+
                        '<button type="button" class="programacion btn btn-info boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Program. Mtto." >'+
                        '<i class="fas fa-clock"></i></button>' +
                        '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalRegistro" data-whatever="@getbootstrap">Open modal for @getbootstrap</button>'}
*/
                ],
                "rowCallback": function (row, data, index) {
                    $('td:eq(1)', row).html(index + 1);
                    //console.log(data);
                    /*if ( data.tipo == "E" ) {
                        $('td', row).addClass('warning');
                    }
                    else if ( data.tipo == "I" ) {
                        $('td', row).addClass('success');
                    }*/
                }
            });
            $('#listaFlujoCajaChica tbody').on('click', 'tr', function () {
                if ($(this).hasClass('eventClick')) {
                    $(this).removeClass('eventClick');
                } else {
                    $('#listaFlujoCajaChica').dataTable().$('tr.eventClick').removeClass('eventClick');
                    $(this).addClass('eventClick');
                }
                //var id = $(this)[0].firstChild.innerHTML;
                //clearForm(form);
                //mostrar_unidmed(id);
                //changeStateButton('historial');
            });
            //console.log(as.columns());

            $('#modalRegistro').on('hide.bs.modal', function (e) {
                // do something...
                $('#btnCancelar').click();
            });

            $('#modalRegistro').on('show.bs.modal', function (event) {
                var btn = $(event.relatedTarget);
                var datos = dataFlujoCajaChica.fnGetData(btn.data('idx'));
                console.log(datos);

                if (datos.length > 0) {


                    var modal = $(this);

                    modal.find('#reg_tipo').val(datos.tipo);
                    var iconTipo = null;
                    if (datos.tipo == 'E') {
                        iconTipo = 'fas fa-sign-out-alt text-danger';
                    } else if (datos.tipo == 'I') {
                        iconTipo = 'fas fa-sign-in-alt text-success';
                    }
                    if (iconTipo !== null) {
                        modal.find('#reg_tipo').parent().find('i').attr('class', iconTipo);
                    }


                    modal.find('.modal-title').text('Editar Flujo #' + btn.data('idx') + 1);

                } else {

                }


                /*
                            if(datos.tipo == 'E'){
                                console.log('eS ROJO00');
                                //$('.modal-backdrop').css('background-color', 'red');
                                $('.modal-backdrop.in').attr('style', 'background-color: red !important;');
                                modal.removeClass('modal-success');
                                modal.addClass('modal-warning');
                            }
                            else if(datos.tipo == 'I'){
                                modal.removeClass('modal-warning');
                                modal.addClass('modal-success');
                            }*/

                //modal.find('.modal-body input').val(recipient)
            });
        });


        function cambiarEmpresa(value) {
            console.log('cambiarEMpresa' + value);
            //baseUrl = 'mostrar_combos_emp/'+value;
            var url = '{{ route('ajax.almacenes', ':value') }}';
            url = url.replace(':value', value);
            console.log(url);

            $.ajax({
                type: 'GET',
                //headers: {'X-CSRF-TOKEN': token},
                url: url,
                dataType: 'JSON',

                success: function (result_emp) {
                    //console.log(result_emp);

                    var htmls = '<option value="0" disabled>Elija una opción</option>';
                    Object.keys(result_emp).forEach(function (key) {
                        //console.log(result_emp[key].almacenes);
                        htmls += '<optgroup label="' + result_emp[key].descripcion + '">';
                        Object.keys(result_emp[key].almacenes).forEach(function (key2) {
                            htmls += '<option value="' + result_emp[key].almacenes[key2].id_almacen + '">' + result_emp[key].almacenes[key2].descripcion + '</option>';
                        });
                        htmls += '</optgroup>';
                    });

                    $('#sel_caja').html(htmls);
                    $('#reg_mov_sede').html(htmls);

                    cargarDatatable();

                    /*
                    var sedes = result_emp.sedes;
                    var htmls = '<option value="0" disabled>Elija una opción</option>';
                    Object.keys(sedes).forEach(function (key){
                        htmls += '<option value="'+sedes[key]['id_sede']+'">'+sedes[key]['descripcion']+'</option>';
                    });
                    console.log(htmls);
                    $('[name="id_sede"]').html(htmls);

                     */
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 403) {
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

        function cargarDatatable() {

            var almacen = $('#sel_caja').val();
            var fecha = $('#sel_fecha').val();

            if ((almacen !== 0) && (fecha != null)) {
                var url = '{{ route('ajax.cajachica', ':value') }}?fecha=' + fecha;
                url = url.replace(':value', almacen);
                console.log(url);

                dataFlujoCajaChica.fnReloadAjax(url);
            }
        }

        // ####################### Partes de Formulario Manipulacion #############################

        $('#reg_num_docu').on('change', function () {
            if ($(this).val() !== '') {
                $('#reg_proveedor').attr('disabled', false);
            } else {
                $('#reg_proveedor').val(0).trigger("change");
                $('#reg_proveedor').attr('disabled', true);

            }
        });
        $('#reg_tipo').on('change', function () {
            var valor = $(this).val();
            var clase = null;

            switch (valor) {
                case 'I':
                    clase = 'fas fa-sign-in-alt text-success';
                    break;
                case 'E':
                    clase = 'fas fa-sign-out-alt text-danger';
                    break;
                default:
                    break;
            }

            $(this).parent().find('i').attr('class', clase);
        });


        function cambioMoneda() {
            /*
            $.get(url, function (data) {

            });
            */
            console.log('Cambio');
            var monedaSel = $('#reg_moneda').find(':selected').data('simbolo');
            console.log(monedaSel);
            $('#reg_t_cambio').siblings('span').html('<b>' + monedaSel + '</b>');
            $('#reg_importe').siblings('span').html('<b>' + monedaSel + '</b>');
        }

        cambioMoneda();

        // #########################################        ACCIONES DE BOTONES SUPERIORES      ########################

        $('#btnNuevo').on('click', function () {
            console.log('Funciona');

            $('#modalRegistro').modal('show')
            //return false;
        });


        function save_mtto(data, action) {
            console.log(action);
            if (action == 'register') {
                baseUrl = 'guardar_mtto';
            } else if (action == 'edition') {
                baseUrl = 'actualizar_mtto';
            }
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    if (response > 0) {
                        alert('Mantenimiento registrado con éxito');
                        changeStateButton('guardar');
                        var id_equipo = $('[name=id_equipo]').val();
                        console.log('id_equipo' + id_equipo);
                        listar_mtto_pendientes(id_equipo);
                        $('[name=id_mtto]').val(response);
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 403) {
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

	</script>

@stop
