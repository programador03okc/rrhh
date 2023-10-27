<?php

use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

//  Route::get('/{path?}', function () {
//      return view('index');
//  })->where('path', '.*');


// Route::get('/', function () {
//     return view('index');
// })->where('path', '.*');

/* Vista */

Route::get('/', function () {
	return redirect()->route('modulos');
});

Route::get('config', function () {
	return view('configuracion/main');
});
Route::get('almacen', function () {
	return view('almacen/main');
});
Route::get('equipo', function () {
	return view('equipo/main');
});
Route::get('proyectos', function () {
	return view('proyectos/main');
});
Route::get('contabilidad', function () {
	return view('contabilidad/main');
});
Route::get('logistica', function () {
	return view('logistica/main');
});
Route::get('admin', function (){
    return view('administracion/main');
});

Route::get('modulos', 'LoginController@index')->name('modulos');
Route::get('cargar_usuarios/{user}', [LoginController::class, 'mostrar_roles']);

Route::get('artisan', function () {
    //return app_path();
    Artisan::call('clear-compiled');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
});


Auth::routes();

Route::group(['middleware' => ['auth']], function () {
	Route::get('config', function () {
		return view('configuracion/main');
	});
	Route::get('rrhh', function () {
		return view('rrhh/main');
	});
	Route::get('almacen', function () {
		return view('almacen/main');
	});
	Route::get('equipo', function () {
		return view('equipo/main');
	});
	Route::get('proyectos', function () {
		return view('proyectos/main');
	});
	Route::get('contabilidad', function () {
		return view('contabilidad/main');
	});
	Route::get('logistica', function () {
		return view('logistica/main');
	});

	Route::group(['middleware' => ['roles:1,2,3,15,22,7'], 'prefix' => 'tesoreria', 'as' => 'tesoreria.'], function () {
		$roles['programador'] = [7, //Programador
		];
		$roles['req_sol'] = [22,  //Asistnte Administrativo
			7, //Programador
		];
		$roles['gerente_general'] = [1,  //Gerente General
		];
		$roles['gerente'] = [1,  //Gerente General
			2,  //Gerente Administrativo
			3,  //Gerente Comercial
			15,  //Gerente Proyectos
		];
		$roles['pagos'] = [22,  //Asistnte Administrativo
		];
		$roles['asis_ger_general'] = [22,  //Asistnte Administrativo
		];

		$entrar['solicitud'] = array_merge($roles['programador'], $roles['req_sol'], $roles['gerente']);
		$entrar['pagos'] = array_merge($roles['programador'], $roles['asis_ger_general'], $roles['pagos'], $roles['gerente']);

		View::share('entrar', $entrar);
		View::share('roles', $roles);
		View::share('rolesSeccion', $entrar);

		Route::get('', 'TesoreriaController@index')->name('index');
		Route::group(['prefix' => 'solicitud', 'as' => 'solicitud.'], function () use ($roles, $entrar) {
			Route::get('tipo/{id_tipo}', 'Tesoreria\SolicitudController@index')->name('tipo')->middleware('roles:' . implode(',', $entrar['solicitud']));
			Route::post('state', 'Tesoreria\SolicitudController@cambiarEstadoAjax')->name('update.state');
		});
		Route::group(['middleware' => ['roles:' . implode(',', $entrar['pagos'])], 'prefix' => 'planillapagos', 'as' => 'planillapagos.'], function () {
			Route::any('ordinario', 'Tesoreria\PlanillaPagosController@index')->name('ordinario');
			Route::any('extraordinario', 'Tesoreria\PlanillaPagosController@index')->name('extraordinario');

			Route::post('state', 'Tesoreria\PlanillaPagosController@cambiarEstadoAjax')->name('update.state');
		});
		Route::resources(['proveedor' => 'Tesoreria\ProveedorController',
			'cajachica' => 'Tesoreria\CajaChicaController', 'cajachica_movimientos' => 'Tesoreria\CajaChicaMovimientosController', 'solicitud' => 'Tesoreria\SolicitudController', 'planillapagos' => 'Tesoreria\PlanillaPagosController', 'tcambio' => 'Tesoreria\TipoCambioController',]
		);
		Route::group(['prefix' => 'pdf', 'as' => 'pdf.'], function () {
			Route::any('vale_salida/{vale_id}', 'Tesoreria\PdfController@generateValeSalida')->name('vale_salida');
			Route::any('historial/cajachica/{cajachica_id}', 'Tesoreria\PdfController@generarHistorialCajaChica')->name('historial.cajachica');
		});

		Route::get('eliminar_tablas', 'TesoreriaController@eliminarTablas');
		Route::group(['middleware' => ['roles:' . implode(',', $roles['programador'])], 'prefix' => 'administracion', 'as' => 'administracion.'], function () {
			Route::get('sol_tipos', 'Tesoreria\SolicitudesTiposController@index')->name('solicitudes_tipos.index');
			Route::post('guardar', 'Tesoreria\SolicitudesTiposController@store')->name('solicitudes_tipos.store');
		});
		Route::group(['middleware' => ['roles:' . implode(',', $roles['programador'])], 'prefix' => 'configuraciones', 'as' => 'configuraciones.'], function () {
			Route::get('/', 'Tesoreria\ConfiguracionesController@index')->name('index');
		});


	});
	Route::group(['prefix' => 'ajax', 'as' => 'ajax.'], function () {
		Route::any('data/{tipo}/{identificador}', 'Tesoreria\AjaxController@getDataPersonaContribuyente')->name('data.persona_contribuyente');
		Route::any('proveedores', 'Tesoreria\AjaxController@getProveedores')->name('proveedores');
		Route::any('cajaschicas', 'Tesoreria\AjaxController@getCajasChicas')->name('cajaschicas');
		Route::any('cajachica/{cajachica_id}/movimientos', 'Tesoreria\AjaxController@getCajaChicaMovimientos')->name('cajachica.movimientos');
		Route::get('cajachica/{cajachica_id}/saldos', 'Tesoreria\AjaxController@getSaldoCajaChica')->name('cajachica.saldos');
		Route::get('almacenes/{empresa?}/{sede?}', 'Tesoreria\AjaxController@ajaxListaAlmacenes')->name('almacenes');
		Route::get('t_cambio/{moneda_id}/{fecha?}', 'Tesoreria\AjaxController@ajaxTipoCambio')->name('t_cambio');
		Route::get('solicitudes_subtipos/{tipo_id}', 'Tesoreria\AjaxController@getSolicitudesSubTipos')->name('sol_subtipos');
		Route::get('solicitudes', 'Tesoreria\AjaxController@getSolicitudes')->name('solicitudes');
		Route::get('planillapagos', 'Tesoreria\AjaxController@getPlanillaPagos')->name('planillapagos');
		Route::get('sedes/{empresa_id}', 'Tesoreria\AjaxController@getSedes')->name('sedes');
		Route::get('areas/{sede_id}', 'Tesoreria\AjaxController@getGruposAreas')->name('areas');
		Route::get('solicitudes_subtipos/{tipo_id}', 'Tesoreria\AjaxController@getSolicitudesSubTipos')->name('sol_subtipos');
		Route::get('solicitudes', 'Tesoreria\AjaxController@getSolicitudes')->name('solicitudes');
		Route::get('presupuesto/{area_id}', 'Tesoreria\AjaxController@getPresupuesto')->name('presupuesto');

	});


	/* Configuración */
	Route::post('update_password', 'ConfiguracionController@cambiar_clave');
	Route::get('modulo', 'ConfiguracionController@view_modulos');
	Route::get('listar_modulo', 'ConfiguracionController@mostrar_modulo_table');
	Route::get('cargar_modulo/{id}', 'ConfiguracionController@mostrar_modulo_id');
	Route::post('guardar_modulo', 'ConfiguracionController@guardar_modulo');
	Route::post('editar_modulo', 'ConfiguracionController@actualizar_modulo');
	Route::get('anular_modulo/{id}', 'ConfiguracionController@anular_modulo');
	Route::get('cargar_modulos', 'ConfiguracionController@mostrar_modulos_combo');

	Route::get('aplicaciones', 'ConfiguracionController@view_aplicaciones');
	Route::get('cargar_submodulos/{id}', 'ConfiguracionController@mostrar_submodulo_id');
	Route::get('listar_aplicaciones', 'ConfiguracionController@mostrar_aplicaciones_table');
	Route::get('cargar_aplicaciones/{id}', 'ConfiguracionController@mostrar_aplicaciones_id');
	Route::post('guardar_aplicaciones', 'ConfiguracionController@guardar_aplicaciones');
	Route::post('editar_aplicaciones', 'ConfiguracionController@actualizar_aplicaciones');
	Route::get('anular_aplicaciones/{id}', 'ConfiguracionController@anular_aplicaciones');

	Route::get('usuarios', 'ConfiguracionController@view_usuario');
	Route::get('listar_usuarios', 'ConfiguracionController@mostrar_usuarios_table');
	Route::post('guardar_usuarios', 'ConfiguracionController@guardar_usuarios');
	// Route::post('editar_usuarios', 'ConfiguracionController@actualizar_usuarios');
	Route::get('anular_usuarios/{id}', 'ConfiguracionController@anular_usuarios');
	Route::get('cargar_aplicaciones_mod/{id}/{user}', 'ConfiguracionController@mostrar_aplicaciones_modulo');

	Route::get('cargar_departamento', 'ConfiguracionController@select_departamento');
	Route::get('cargar_provincia/{id}', 'ConfiguracionController@select_prov_dep');
	Route::get('cargar_distrito/{id}', 'ConfiguracionController@select_dist_prov');
	Route::get('cargar_estructura_org/{id}', 'ConfiguracionController@cargar_estructura_org'); /////// modal area

	Route::get('traer_ubigeo/{id}', 'ConfiguracionController@traer_ubigeo');

	Route::post('guardar_accesos', 'ConfiguracionController@guardar_accesos');
	Route::post('editar_accesos', 'ConfiguracionController@actualizar_accesos');
	Route::get('cargar_roles_usuario/{id}', 'ConfiguracionController@buscar_roles_usuario');

	/* Recursos Humanos */
	Route::get('rrhh', 'RecursosHumanosController@view_main');
	Route::get('persona', 'RecursosHumanosController@view_persona');
	Route::get('postulante', 'RecursosHumanosController@view_postulante');
	Route::get('trabajador', 'RecursosHumanosController@view_trabajador');
	Route::get('cargo', 'RecursosHumanosController@view_cargo');

	Route::get('periodo', 'RecursosHumanosController@view_periodo');
	Route::get('tareo', 'RecursosHumanosController@view_tareo');
	Route::get('asistencia', 'RecursosHumanosController@view_asistencia');
	Route::get('planilla', 'RecursosHumanosController@view_planilla');
	Route::get('horario', 'RecursosHumanosController@view_horario');
	Route::get('tolerancia', 'RecursosHumanosController@view_tolerancia');
	Route::get('est_civil', 'RecursosHumanosController@view_est_civil');
	Route::get('cond_derecho_hab', 'RecursosHumanosController@view_cond_derecho_hab');
	Route::get('niv_estudios', 'RecursosHumanosController@view_niv_estudio');
	Route::get('carreras', 'RecursosHumanosController@view_carrera');
	Route::get('tipo_trabajador', 'RecursosHumanosController@view_tipo_trabajador');
	Route::get('tipo_contrato', 'RecursosHumanosController@view_tipo_contrato');
	Route::get('modalidad', 'RecursosHumanosController@view_modalidad');
	Route::get('concepto_rol', 'RecursosHumanosController@view_concepto_rol');
	Route::get('cat_ocupacional', 'RecursosHumanosController@view_cat_ocupacional');
	Route::get('tipo_planilla', 'RecursosHumanosController@view_tipo_planilla');
	Route::get('tipo_merito', 'RecursosHumanosController@view_tipo_merito');
	Route::get('tipo_demerito', 'RecursosHumanosController@view_tipo_demerito');
	Route::get('tipo_bonificacion', 'RecursosHumanosController@view_tipo_bonificacion');
	Route::get('tipo_descuento', 'RecursosHumanosController@view_tipo_descuento');
	Route::get('tipo_retencion', 'RecursosHumanosController@view_tipo_retencion');
	Route::get('tipo_aportes', 'RecursosHumanosController@view_tipo_aportes');
	Route::get('derecho_hab', 'RecursosHumanosController@view_derecho_hab');
	Route::get('pension', 'RecursosHumanosController@view_pension');
	Route::get('merito', 'RecursosHumanosController@view_merito');
	Route::get('demerito', 'RecursosHumanosController@view_sancion');
	Route::get('salidas', 'RecursosHumanosController@view_salidas');
	Route::get('prestamos', 'RecursosHumanosController@view_prestamo');
	Route::get('vacaciones', 'RecursosHumanosController@view_vacaciones');
	Route::get('licencia', 'RecursosHumanosController@view_licencia');
	Route::get('horas_ext', 'RecursosHumanosController@view_horas_ext');
	Route::get('cese', 'RecursosHumanosController@view_cese');
	Route::get('neto', 'RecursosHumanosController@view_netos');
	Route::get('encargatura', 'RecursosHumanosController@view_encargatura');
	Route::get('utilidades', 'RecursosHumanosController@view_utilidades');
	Route::get('beneficios', 'RecursosHumanosController@view_beneficios');

	Route::post('filter_trabajadores', 'RecursosHumanosController@load_trabajadores_sueldo');
	Route::post('filter_trabajadores_beneficios', 'RecursosHumanosController@load_trabajadores_benefecios');
	Route::post('guardar-netos', 'RecursosHumanosController@guardar_netos')->name('guardar-netos');
	Route::post('guardar-seguro', 'RecursosHumanosController@guardar_seguro')->name('guardar-seguro');
	Route::post('guardar-encargatura', 'RecursosHumanosController@guardar_encargatura')->name('guardar-encargatura');
	Route::post('guardar-utilidades', 'RecursosHumanosController@guardar_utilidades')->name('guardar-utilidades');
	Route::post('guardar-beneficios', 'RecursosHumanosController@guardar_beneficios')->name('guardar-beneficios');
	Route::post('enviar-correos', 'RecursosHumanosController@enviar_correos')->name('enviar-correos');
	Route::post('enviar-correos-utilidades', 'RecursosHumanosController@enviar_correos_utilidades')->name('enviar-correos-utilidades');

	Route::get('bonificacion', 'RecursosHumanosController@view_bonificacion');
	Route::get('descuento', 'RecursosHumanosController@view_descuento');
	Route::get('retencion', 'RecursosHumanosController@view_retencion');
	Route::get('aportacion', 'RecursosHumanosController@view_aportacion');
	Route::get('reintegro', 'RecursosHumanosController@view_reintegro');

	Route::get('buscar_trabajador_id/{id}/{emp}/{sede}', 'RecursosHumanosController@buscar_trabajador_id');

	Route::post('cargar_csv', 'RecursosHumanosController@cargar_horario_reloj');
	Route::get('cargar_data_diaria/{empre}/{sede}/{tipo}/{fecha}', 'RecursosHumanosController@cargar_horario_diario');
	Route::post('grabar_asistencia', 'RecursosHumanosController@grabar_asistencia_diaria');
	Route::post('grabar_asistencia_final', 'RecursosHumanosController@grabar_asistencia_final');
	Route::get('cargar_asistencia/{empre}/{sede}/{tipo}/{ini}/{fin}', 'RecursosHumanosController@cargar_asistencia');
	Route::get('mostrar_permiso_asistencia/{id}/{fecha}', 'RecursosHumanosController@permisos_asistencia');
	Route::get('reporte_tardanzas/{from}/{to}/{empresa}/{sede}', 'RecursosHumanosController@reporte_tardanza');
	Route::get('cargar_data_remun/{emp}/{plani}/{mes}/{anio}/{type}/{empleado}/{grupal}/{spcc}', 'RecursosHumanosController@cargar_remuneraciones'); // SOLO PRUEBAS
	Route::get('pruebas_rrhh/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@info_pruebas'); // SOLO PRUEBAS
	Route::get('cargar_data_spcc/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@remuneracion_spcc');
	Route::get('procesar_planilla/{emp}/{plani}/{mes}', 'RecursosHumanosController@procesar_planilla');
	Route::get('generar_planilla_pdf/{emp}/{plani}/{mes}/{anio}/{firma}', 'RecursosHumanosController@generar_planilla_pdf');
	Route::get('generar_planilla_spcc_pdf/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@generar_planilla_spcc_pdf');
	Route::get('generar_planilla_spcc_pdf_mes/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@generar_planilla_spcc_pdf_mes');

	Route::get('reporte_planilla_xls/{emp}/{plani}/{mes}/{anio}/{filter}/{grupal}', 'RecursosHumanosController@reporte_planilla_xls');
	Route::get('reporte_planilla_benef_xls/{emp}/{plani}/{mes}/{anio}/{filter}/{grupal}', 'RecursosHumanosController@reporte_planilla_benef_xls');
	Route::get('reporte_planilla_spcc_xls/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@reporte_planilla_spcc_xls');
	Route::get('reporte_planilla_spcc_xls_mes/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@reporte_planilla_spcc_xls_mes');
	Route::get('reporte_planilla_grupal_xls/{plani}/{mes}/{anio}/{grupo}', 'RecursosHumanosController@reporte_planilla_grupal_xls');
	Route::get('generar_vacaciones/{id}/{emp}', 'RecursosHumanosController@generar_vacaciones_pdf');
	Route::get('reporte_planilla_trabajador_pdf/{emp}/{plani}/{mes}/{anio}/{trab}/{firma}', 'RecursosHumanosController@reporte_planilla_trabajador_xls');
	Route::get('reporte_planilla_utilidades/{emp}/{plani}/{anio}/{firma}', 'RecursosHumanosController@reporte_planilla_utilidades');
	// Route::get('generar_pdf_trabajdor/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@generar_pdf_trabajdor');
	Route::get('reporte_gastos/{plani}/{mes}/{anio}', 'RecursosHumanosController@reporte_gastos');
	Route::get('resumen_planilla/{plani}/{mes}/{anio}', 'RecursosHumanosController@resumen_planilla');
	// Route::get('generar_/{emp}/{plani}/{mes}/{anio}', 'RecursosHumanosController@generar_');

	Route::get('listar_personas', 'RecursosHumanosController@mostrar_persona_table');
	Route::get('cargar_persona/{id}', 'RecursosHumanosController@mostrar_persona_id');
	Route::post('guardar_persona', 'RecursosHumanosController@guardar_persona');
	Route::post('editar_persona', 'RecursosHumanosController@actualizar_persona');
	Route::get('anular_persona/{id}', 'RecursosHumanosController@anular_persona');
	Route::get('digitos_documento/{id}', 'RecursosHumanosController@mostrar_longitud_doc');

	Route::get('listar_postulantes', 'RecursosHumanosController@mostrar_postulante_table');
	Route::get('cargar_postulante/{id}', 'RecursosHumanosController@mostrar_postulante_id');
	Route::get('cargar_postulante_dni/{dni}', 'RecursosHumanosController@mostrar_postulante_dni');
	Route::post('guardar_informacion_postulante', 'RecursosHumanosController@guardar_informacion_postulante');
	Route::post('editar_informacion_postulante', 'RecursosHumanosController@actualizar_informacion_postulante');
	Route::get('listar_formacion_acad/{id}', 'RecursosHumanosController@mostrar_formacion_acad');
	Route::post('guardar_formacion_academica', 'RecursosHumanosController@guardar_formacion_academica');
	Route::post('editar_formacion_academica', 'RecursosHumanosController@actualizar_formacion_academica'); //FALTA ANULAR
	Route::get('cargar_formacion_click/{id}', 'RecursosHumanosController@mostrar_formacion_click');
	Route::get('listar_experiencia_lab/{id}', 'RecursosHumanosController@mostrar_experiencia_lab');
	Route::post('guardar_experiencia_laboral', 'RecursosHumanosController@guardar_experiencia_laboral');
	Route::post('editar_experiencia_laboral', 'RecursosHumanosController@actualizar_experiencia_laboral'); //FALTA ANULAR
	Route::get('cargar_experiencia_click/{id}', 'RecursosHumanosController@mostrar_experiencia_click');
	Route::get('listar_datos_extras/{id}', 'RecursosHumanosController@mostrar_datos_extras');
	Route::post('guardar_datos_extras', 'RecursosHumanosController@guardar_dextra_postulante'); //FALTA ANULAR
	Route::get('listar_observaciones/{id}', 'RecursosHumanosController@mostrar_observaciones');
	Route::post('guardar_observacion', 'RecursosHumanosController@guardar_observacion_postulante');
	Route::post('editar_observacion', 'RecursosHumanosController@actualizar_observacion_postulante'); //FALTA ANULAR
	Route::get('cargar_observacion_click/{id}', 'RecursosHumanosController@mostrar_observacion_click');

	Route::get('listar_trabajador', 'RecursosHumanosController@mostrar_trabajador_table');
	Route::get('cargar_trabajador/{id}', 'RecursosHumanosController@mostrar_trabajador_id');
	Route::get('cargar_trabajador_dni/{dni}', 'RecursosHumanosController@mostrar_trabajador_dni');
	Route::post('guardar_alta_trabajador', 'RecursosHumanosController@guardar_alta_trabajador');
	Route::post('editar_alta_trabajador', 'RecursosHumanosController@actualizar_alta_trabajador'); //FALTA ANULAR
	Route::get('listar_contrato_trab/{id}', 'RecursosHumanosController@mostrar_contrato_trab');
	Route::post('guardar_contrato_trabajador', 'RecursosHumanosController@guardar_contrato_trabajador');
	Route::post('editar_contrato_trabajador', 'RecursosHumanosController@actualizar_contrato_trabajador'); //FALTA ANULAR
	Route::get('cargar_contrato_click/{id}', 'RecursosHumanosController@mostrar_contrato_click');
	Route::get('listar_rol_trab/{id}', 'RecursosHumanosController@mostrar_rol_trab');
	Route::post('guardar_rol_trabajador', 'RecursosHumanosController@guardar_rol_trabajador');
	Route::post('editar_rol_trabajador', 'RecursosHumanosController@actualizar_rol_trabajador'); //FALTA ANULAR
	Route::get('actualizar_cierre_rol/{id}/{fecha}', 'RecursosHumanosController@actualizar_cierre_rol');
	Route::get('cargar_rol_click/{id}', 'RecursosHumanosController@mostrar_rol_click');
	Route::get('listar_cuentas_trab/{id}', 'RecursosHumanosController@mostrar_cuentas_trab');
	Route::post('guardar_cuentas_trabajador', 'RecursosHumanosController@guardar_cuentas_trabajador');
	Route::post('editar_cuentas_trabajador', 'RecursosHumanosController@actualizar_cuentas_trabajador'); //FALTA ANULAR
	Route::get('cargar_cuenta_click/{id}', 'RecursosHumanosController@mostrar_cuenta_click');

	Route::get('mostrar_combos_emp/{id}', 'RecursosHumanosController@buscar_sede');
	Route::get('mostrar_grupo_sede/{id}', 'RecursosHumanosController@buscar_grupo');
	Route::get('mostrar_area_grupo/{id}', 'RecursosHumanosController@buscar_area');

	Route::get('listar_cargo', 'RecursosHumanosController@mostrar_cargo_table');
	Route::get('cargar_cargo/{id}', 'RecursosHumanosController@mostrar_cargo_id');
	Route::post('guardar_cargo', 'RecursosHumanosController@guardar_cargo');
	Route::post('editar_cargo', 'RecursosHumanosController@actualizar_cargo');
	Route::get('anular_cargo/{id}', 'RecursosHumanosController@anular_cargo');

	Route::get('cargar_trabajador_dni_esc/{dni}', 'RecursosHumanosController@buscar_trab_dni');
	Route::get('cargar_persona_dni_esc/{dni}', 'RecursosHumanosController@buscar_persona_dni');

	Route::get('listar_merito/{id}', 'RecursosHumanosController@mostrar_merito_table');
	Route::get('cargar_merito/{id}', 'RecursosHumanosController@mostrar_merito_id');
	Route::post('guardar_merito', 'RecursosHumanosController@guardar_merito');
	Route::post('editar_merito', 'RecursosHumanosController@actualizar_merito');
	Route::get('anular_merito/{id}', 'RecursosHumanosController@anular_merito');

	Route::get('listar_sancion/{id}', 'RecursosHumanosController@mostrar_sancion_table');
	Route::get('listar_sancion', 'RecursosHumanosController@mostrar_sancion_table');
	Route::get('cargar_sancion/{id}', 'RecursosHumanosController@mostrar_sancion_id');
	Route::post('guardar_sancion', 'RecursosHumanosController@guardar_sancion');
	Route::post('editar_sancion', 'RecursosHumanosController@actualizar_sancion');
	Route::get('anular_sancion/{id}', 'RecursosHumanosController@anular_sancion');

	Route::get('listar_derecho_hab/{id}', 'RecursosHumanosController@mostrar_derechohabiente_table');
	Route::get('cargar_derecho_hab/{id}', 'RecursosHumanosController@mostrar_derechohabiente_id');
	Route::post('guardar_derecho_hab', 'RecursosHumanosController@guardar_derecho_habiente');
	Route::post('editar_derecho_hab', 'RecursosHumanosController@actualizar_derecho_habiente');
	Route::get('anular_derecho_hab/{id}', 'RecursosHumanosController@anular_derecho_habiente');

	Route::get('listar_salidas/{id}', 'RecursosHumanosController@mostrar_salidas_table');
	Route::get('cargar_salidas/{id}', 'RecursosHumanosController@mostrar_salidas_id');
	Route::post('guardar_salidas', 'RecursosHumanosController@guardar_salidas');
	Route::post('editar_salidas', 'RecursosHumanosController@actualizar_salidas');
	Route::get('anular_salidas/{id}', 'RecursosHumanosController@anular_salidas');

	Route::get('listar_prestamo/{id}', 'RecursosHumanosController@mostrar_prestamo_table');
	Route::get('cargar_prestamo/{id}', 'RecursosHumanosController@mostrar_prestamo_id');
	Route::post('guardar_prestamo', 'RecursosHumanosController@guardar_prestamo');
	Route::post('editar_prestamo', 'RecursosHumanosController@actualizar_prestamo');
	Route::get('anular_prestamo/{id}', 'RecursosHumanosController@anular_prestamo');

	Route::get('listar_vacaciones/{id}', 'RecursosHumanosController@mostrar_vacaciones_table');
	Route::get('cargar_vacaciones/{id}', 'RecursosHumanosController@mostrar_vacaciones_id');
	Route::post('guardar_vacaciones', 'RecursosHumanosController@guardar_vacaciones');
	Route::post('editar_vacaciones', 'RecursosHumanosController@actualizar_vacaciones');
	Route::get('anular_vacaciones/{id}', 'RecursosHumanosController@anular_vacaciones');

	Route::get('listar_licencia/{id}', 'RecursosHumanosController@mostrar_licencia_table');
	Route::get('cargar_licencia/{id}', 'RecursosHumanosController@mostrar_licencia_id');
	Route::post('guardar_licencia', 'RecursosHumanosController@guardar_licencia');
	Route::post('editar_licencia', 'RecursosHumanosController@actualizar_licencia');
	Route::get('anular_licencia/{id}', 'RecursosHumanosController@anular_licencia');

	Route::get('listar_horas_ext/{id}', 'RecursosHumanosController@mostrar_horas_ext_table');
	Route::get('cargar_horas_ext/{id}', 'RecursosHumanosController@mostrar_horas_ext_id');
	Route::post('guardar_horas_ext', 'RecursosHumanosController@guardar_horas_ext');
	Route::post('editar_horas_ext', 'RecursosHumanosController@actualizar_horas_ext');
	Route::get('anular_horas_ext/{id}', 'RecursosHumanosController@anular_horas_ext');

	Route::post('guardar_cese', 'RecursosHumanosController@guardar_cese');

	Route::get('listar_periodo', 'RecursosHumanosController@mostrar_periodo_table');
	Route::get('cargar_periodo/{id}', 'RecursosHumanosController@mostrar_periodo_id');
	Route::post('guardar_periodo', 'RecursosHumanosController@guardar_periodo');
	Route::post('editar_periodo', 'RecursosHumanosController@actualizar_periodo');
	Route::get('anular_periodo/{id}', 'RecursosHumanosController@anular_periodo');

	Route::get('listar_horarios', 'RecursosHumanosController@mostrar_horarios_table');
	Route::get('cargar_horario/{id}', 'RecursosHumanosController@mostrar_horario_id');
	Route::post('guardar_horario', 'RecursosHumanosController@guardar_horario');
	Route::post('editar_horario', 'RecursosHumanosController@actualizar_horario');
	Route::get('anular_horario/{id}', 'RecursosHumanosController@anular_horario');

	Route::get('listar_tolerancias', 'RecursosHumanosController@mostrar_tolerancia_table');
	Route::get('cargar_tolerancia/{id}', 'RecursosHumanosController@mostrar_tolerancia_id');
	Route::post('guardar_tolerancia', 'RecursosHumanosController@guardar_tolerancia');
	Route::post('editar_tolerancia', 'RecursosHumanosController@actualizar_tolerancia');
	Route::get('anular_tolerancia/{id}', 'RecursosHumanosController@anular_tolerancia');

	Route::get('listar_estado_civil', 'RecursosHumanosController@mostrar_estado_civil_table');
	Route::get('cargar_est_civil/{id}', 'RecursosHumanosController@mostrar_est_civil_id');
	Route::post('guardar_est_civil', 'RecursosHumanosController@guardar_estado_civil');
	Route::post('editar_est_civil', 'RecursosHumanosController@actualizar_estado_civil');
	Route::get('anular_est_civil/{id}', 'RecursosHumanosController@anular_estado_civil');

	Route::get('listar_condi_derecho_hab', 'RecursosHumanosController@mostrar_condiciondh_table');
	Route::get('cargar_cond_derecho_hab/{id}', 'RecursosHumanosController@mostrar_condiciondh_id');
	Route::post('guardar_cond_derecho_hab', 'RecursosHumanosController@guardar_condicion_dh');
	Route::post('editar_cond_derecho_hab', 'RecursosHumanosController@actualizar_condicion_dh');
	Route::get('anular_cond_derecho_hab/{id}', 'RecursosHumanosController@anular_condicion_dh');

	Route::get('listar_nivel_estudio', 'RecursosHumanosController@mostrar_nivel_estudio_table');
	Route::get('cargar_nivel_estudio/{id}', 'RecursosHumanosController@mostrar_nivel_estudios_id');
	Route::post('guardar_nivel_estudio', 'RecursosHumanosController@guardar_nivel_estudio');
	Route::post('editar_nivel_estudio', 'RecursosHumanosController@actualizar_nivel_estudio');
	Route::get('anular_nivel_estudio/{id}', 'RecursosHumanosController@anular_nivel_estudio');

	Route::get('listar_carrera', 'RecursosHumanosController@mostrar_carrera_table');
	Route::get('cargar_carrera/{id}', 'RecursosHumanosController@mostrar_carrera_id');
	Route::post('guardar_carrera', 'RecursosHumanosController@guardar_carrera');
	Route::post('editar_carrera', 'RecursosHumanosController@actualizar_carrera');
	Route::get('anular_carrera/{id}', 'RecursosHumanosController@anular_carrera');

	Route::get('listar_tipo_trabajador', 'RecursosHumanosController@mostrar_tipo_trabajador_table');
	Route::get('cargar_tipo_trabajador/{id}', 'RecursosHumanosController@mostrar_tipo_trabajador_id');
	Route::post('guardar_tipo_trabajador', 'RecursosHumanosController@guardar_tipo_trabajador');
	Route::post('editar_tipo_trabajador', 'RecursosHumanosController@actualizar_tipo_trabajador');
	Route::get('anular_tipo_trabajador/{id}', 'RecursosHumanosController@anular_tipo_trabajador');

	Route::get('listar_tipo_contrato', 'RecursosHumanosController@mostrar_tipo_contrato_table');
	Route::get('cargar_tipo_contrato/{id}', 'RecursosHumanosController@mostrar_tipo_contrato_id');
	Route::post('guardar_tipo_contrato', 'RecursosHumanosController@guardar_tipo_contrato');
	Route::post('editar_tipo_contrato', 'RecursosHumanosController@actualizar_tipo_contrato');
	Route::get('anular_tipo_contrato/{id}', 'RecursosHumanosController@anular_tipo_contrato');

	Route::get('listar_modalidad', 'RecursosHumanosController@mostrar_modalidad_table');
	Route::get('cargar_modalidad/{id}', 'RecursosHumanosController@mostrar_modalidad_id');
	Route::post('guardar_modalidad', 'RecursosHumanosController@guardar_modalidad');
	Route::post('editar_modalidad', 'RecursosHumanosController@actualizar_modalidad');
	Route::get('anular_modalidad/{id}', 'RecursosHumanosController@anular_modalidad');

	Route::get('listar_concepto_rol', 'RecursosHumanosController@mostrar_concepto_rol_table');
	Route::get('cargar_concepto_rol/{id}', 'RecursosHumanosController@mostrar_concepto_rol_id');
	Route::post('guardar_concepto_rol', 'RecursosHumanosController@guardar_concepto_rol');
	Route::post('editar_concepto_rol', 'RecursosHumanosController@actualizar_concepto_rol');
	Route::get('anular_concepto_rol/{id}', 'RecursosHumanosController@anular_concepto_rol');

	Route::get('listar_categoria_ocupacional', 'RecursosHumanosController@mostrar_categoria_ocupacional');
	Route::get('cargar_categoria_ocupacional/{id}', 'RecursosHumanosController@mostrar_categoria_ocupacional_id');
	Route::post('guardar_categoria_ocupacional', 'RecursosHumanosController@guardar_categoria_ocupacional');
	Route::post('editar_categoria_ocupacional', 'RecursosHumanosController@actualizar_categoria_ocupacional');
	Route::get('anular_categoria_ocupacional/{id}', 'RecursosHumanosController@anular_categoria_ocupacional');

	Route::get('listar_tipo_planilla', 'RecursosHumanosController@mostrar_tipo_planilla_table');
	Route::get('cargar_tipo_planilla/{id}', 'RecursosHumanosController@mostrar_tipo_planilla_id');
	Route::post('guardar_tipo_planilla', 'RecursosHumanosController@guardar_tipo_planilla');
	Route::post('editar_tipo_planilla', 'RecursosHumanosController@actualizar_tipo_planilla');
	Route::get('anular_tipo_planilla/{id}', 'RecursosHumanosController@anular_tipo_planilla');

	Route::get('listar_tipo_merito', 'RecursosHumanosController@mostrar_tipo_merito_table');
	Route::get('cargar_tipo_merito/{id}', 'RecursosHumanosController@mostrar_tipo_merito_id');
	Route::post('guardar_tipo_merito', 'RecursosHumanosController@guardar_tipo_merito');
	Route::post('editar_tipo_merito', 'RecursosHumanosController@actualizar_tipo_merito');
	Route::get('anular_tipo_merito/{id}', 'RecursosHumanosController@anular_tipo_merito');

	Route::get('listar_tipo_demerito', 'RecursosHumanosController@mostrar_tipo_demerito_table');
	Route::get('cargar_tipo_demerito/{id}', 'RecursosHumanosController@mostrar_tipo_demerito_id');
	Route::post('guardar_tipo_demerito', 'RecursosHumanosController@guardar_tipo_demerito');
	Route::post('editar_tipo_demerito', 'RecursosHumanosController@actualizar_tipo_demerito');
	Route::get('anular_tipo_demerito/{id}', 'RecursosHumanosController@anular_tipo_demerito');

	Route::get('listar_tipo_bonificacion', 'RecursosHumanosController@mostrar_tipo_bonificacion_table');
	Route::get('cargar_tipo_bonificacion/{id}', 'RecursosHumanosController@mostrar_tipo_bonificacion_id');
	Route::post('guardar_tipo_bonificacion', 'RecursosHumanosController@guardar_tipo_bonificacion');
	Route::post('editar_tipo_bonificacion', 'RecursosHumanosController@actualizar_tipo_bonificacion');
	Route::get('anular_tipo_bonificacion/{id}', 'RecursosHumanosController@anular_tipo_bonificacion');

	Route::get('listar_tipo_descuento', 'RecursosHumanosController@mostrar_tipo_descuento_table');
	Route::get('cargar_tipo_descuento/{id}', 'RecursosHumanosController@mostrar_tipo_descuento_id');
	Route::post('guardar_tipo_descuento', 'RecursosHumanosController@guardar_tipo_descuento');
	Route::post('editar_tipo_descuento', 'RecursosHumanosController@actualizar_tipo_descuento');
	Route::get('anular_tipo_descuento/{id}', 'RecursosHumanosController@anular_tipo_descuento');

	Route::get('listar_tipo_retencion', 'RecursosHumanosController@mostrar_tipo_retencion_table');
	Route::get('cargar_tipo_retencion/{id}', 'RecursosHumanosController@mostrar_tipo_retencion_id');
	Route::post('guardar_tipo_retencion', 'RecursosHumanosController@guardar_tipo_retencion');
	Route::post('editar_tipo_retencion', 'RecursosHumanosController@actualizar_tipo_retencion');
	Route::get('anular_tipo_retencion/{id}', 'RecursosHumanosController@anular_tipo_retencion');

	Route::get('listar_tipo_aporte', 'RecursosHumanosController@mostrar_tipo_aporte_table');
	Route::get('cargar_tipo_aporte/{id}', 'RecursosHumanosController@mostrar_tipo_aporte_id');
	Route::post('guardar_tipo_aporte', 'RecursosHumanosController@guardar_tipo_aporte');
	Route::post('editar_tipo_aporte', 'RecursosHumanosController@actualizar_tipo_aporte');
	Route::get('anular_tipo_aporte/{id}', 'RecursosHumanosController@anular_tipo_aporte');

	Route::get('listar_pension', 'RecursosHumanosController@mostrar_pension_table');
	Route::get('cargar_pension/{id}', 'RecursosHumanosController@mostrar_pension_id');
	Route::post('guardar_pension', 'RecursosHumanosController@guardar_pension');
	Route::post('editar_pension', 'RecursosHumanosController@actualizar_pension');
	Route::get('anular_pension/{id}', 'RecursosHumanosController@anular_pension');

	Route::get('cargar_regimen/{id}', 'RecursosHumanosController@cargar_regimen');
	
	Route::get('listar_bonificacion/{id}', 'RecursosHumanosController@mostrar_bonificacion_table');
	Route::get('cargar_bonificacion/{id}', 'RecursosHumanosController@mostrar_bonificacion_id');
	Route::post('guardar_bonificacion', 'RecursosHumanosController@guardar_bonificacion');
	Route::post('editar_bonificacion', 'RecursosHumanosController@actualizar_bonificacion');
	Route::get('anular_bonificacion/{id}', 'RecursosHumanosController@anular_bonificacion');

	Route::get('listar_descuento/{id}', 'RecursosHumanosController@mostrar_descuento_table');
	Route::get('cargar_descuento/{id}', 'RecursosHumanosController@mostrar_descuento_id');
	Route::post('guardar_descuento', 'RecursosHumanosController@guardar_descuento');
	Route::post('editar_descuento', 'RecursosHumanosController@actualizar_descuento');
	Route::get('anular_descuento/{id}', 'RecursosHumanosController@anular_descuento');

	Route::get('listar_retencion/{id}', 'RecursosHumanosController@mostrar_retencion_table');
	Route::get('cargar_retencion/{id}', 'RecursosHumanosController@mostrar_retencion_id');
	Route::post('guardar_retencion', 'RecursosHumanosController@guardar_retencion');
	Route::post('editar_retencion', 'RecursosHumanosController@actualizar_retencion');
	Route::get('anular_retencion/{id}', 'RecursosHumanosController@anular_retencion');

	Route::get('listar_aportacion', 'RecursosHumanosController@mostrar_aportacion_table');
	Route::get('cargar_aportacion/{id}', 'RecursosHumanosController@mostrar_aportacion_id');
	Route::post('guardar_aportacion', 'RecursosHumanosController@guardar_aportacion');
	Route::post('editar_aportacion', 'RecursosHumanosController@actualizar_aportacion');
	Route::get('anular_aportacion/{id}', 'RecursosHumanosController@anular_aportacion');

	Route::get('listar_reintegro/{id}', 'RecursosHumanosController@mostrar_reintegro_table');
	Route::get('cargar_reintegro/{id}', 'RecursosHumanosController@mostrar_reintegro_id');
	Route::post('guardar_reintegro', 'RecursosHumanosController@guardar_reintegro');
	Route::post('editar_reintegro', 'RecursosHumanosController@actualizar_reintegro');
	Route::get('anular_reintegro/{id}', 'RecursosHumanosController@anular_reintegro');

	// REPORTE RRHH
	Route::get('datos_personal', 'RecursosHumanosController@view_cv');
	Route::get('busqueda_postulante', 'RecursosHumanosController@view_busq_postu');
	Route::get('grupo_trabajador', 'RecursosHumanosController@view_grupo_trab');
	Route::get('cumple', 'RecursosHumanosController@view_cumple');
	Route::get('datos_generales', 'RecursosHumanosController@view_datos_generales');
	Route::get('reporte_afp', 'RecursosHumanosController@view_reporte_afp');

	Route::get('buscar_postulantes/{filtro}/{desc}', 'RecursosHumanosController@buscar_postulantes_reporte');
	Route::get('buscar_grupo_trabajador/{emp}/{grupo}', 'RecursosHumanosController@grupo_trabajador_reporte');
	Route::get('buscar_cumple/{filtro}', 'RecursosHumanosController@onomastico_reporte');
	Route::get('cargar_detalle_postulante/{id}', 'RecursosHumanosController@cargar_detalle_postulante');
	Route::get('buscar_datos_generales/{tipo}', 'RecursosHumanosController@datos_generales_reporte');
	Route::get('buscar_reporte_afp', 'RecursosHumanosController@reporte_afp');

	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	// /* Almacen */
	Route::get('tipo', 'AlmacenController@view_tipo');
	Route::get('listar_tipos', 'AlmacenController@mostrar_tp_productos');
	Route::get('mostrar_tipo/{id}', 'AlmacenController@mostrar_tp_producto');
	Route::post('guardar_tipo', 'AlmacenController@guardar_tp_producto');
	Route::post('actualizar_tipo', 'AlmacenController@update_tp_producto');
	Route::get('anular_tipo/{id}', 'AlmacenController@anular_tp_producto');
	Route::get('revisarTipo/{id}', 'AlmacenController@tipo_revisar_relacion');
	Route::get('categoria', 'AlmacenController@view_categoria');
	Route::get('listar_categorias', 'AlmacenController@mostrar_categorias');
	Route::get('mostrar_categoria/{id}', 'AlmacenController@mostrar_categoria');
	Route::post('guardar_categoria', 'AlmacenController@guardar_categoria');
	Route::post('actualizar_categoria', 'AlmacenController@update_categoria');
	Route::get('anular_categoria/{id}', 'AlmacenController@anular_categoria');
	Route::get('revisarCat/{id}', 'AlmacenController@cat_revisar');
	Route::get('subcategoria', 'AlmacenController@view_subcategoria');
	Route::get('listar_subcategorias', 'AlmacenController@mostrar_sub_categorias');
	Route::get('mostrar_subcategoria/{id}', 'AlmacenController@mostrar_sub_categoria');
	Route::post('guardar_subcategoria', 'AlmacenController@guardar_sub_categoria');
	Route::post('actualizar_subcategoria', 'AlmacenController@update_sub_categoria');
	Route::get('anular_subcategoria/{id}', 'AlmacenController@anular_sub_categoria');
	Route::get('revisarSubCat/{id}', 'AlmacenController@subcat_revisar');
	Route::get('clasificacion', 'AlmacenController@view_clasificacion');
	Route::get('listar_clasificaciones', 'AlmacenController@mostrar_clasificaciones');
	Route::get('mostrar_clasificacion/{id}', 'AlmacenController@mostrar_clasificacion');
	Route::post('guardar_clasificacion', 'AlmacenController@guardar_clasificacion');
	Route::post('actualizar_clasificacion', 'AlmacenController@update_clasificacion');
	Route::get('anular_clasificacion/{id}', 'AlmacenController@anular_clasificacion');
	Route::get('revisarClas/{id}', 'AlmacenController@clas_revisar');
	/**Producto */
	Route::get('producto', 'AlmacenController@view_producto');
	Route::get('prod_catalogo', 'AlmacenController@view_prod_catalogo');
	Route::get('listar_productos', 'AlmacenController@mostrar_productos');
	Route::get('mostrar_prods', 'AlmacenController@mostrar_prods');
	Route::get('mostrar_producto/{id}', 'AlmacenController@mostrar_producto');
	Route::post('guardar_producto', 'AlmacenController@guardar_producto');
	Route::post('actualizar_producto', 'AlmacenController@update_producto');
	Route::get('anular_producto/{id}', 'AlmacenController@anular_producto');
	Route::post('guardar_imagen', 'AlmacenController@guardar_imagen');
	Route::get('almacenes', 'AlmacenController@view_almacenes');
	Route::get('listar_almacenes', 'AlmacenController@mostrar_almacenes');
	Route::get('cargar_almacen/{id}', 'AlmacenController@mostrar_almacen');
	Route::post('guardar_almacen', 'AlmacenController@guardar_almacen');
	Route::post('editar_almacen', 'AlmacenController@update_almacen');
	Route::get('anular_almacen/{id}', 'AlmacenController@anular_almacen');
	Route::get('almacen_posicion/{id}', 'AlmacenController@almacen_posicion');
	Route::get('ubicacion', 'AlmacenController@view_ubicacion');
	Route::get('listar_estantes', 'AlmacenController@mostrar_estantes');
	Route::get('listar_estantes_almacen/{id}', 'AlmacenController@mostrar_estantes_almacen');
	Route::get('mostrar_estante/{id}', 'AlmacenController@mostrar_estante');
	Route::post('guardar_estante', 'AlmacenController@guardar_estante');
	Route::post('actualizar_estante', 'AlmacenController@update_estante');
	Route::get('anular_estante/{id}', 'AlmacenController@anular_estante');
	Route::get('revisar_estante/{id}', 'AlmacenController@revisar_estante');
	Route::post('guardar_estantes', 'AlmacenController@guardar_estantes');
	Route::get('listar_niveles', 'AlmacenController@mostrar_niveles');
	Route::get('listar_niveles_estante/{id}', 'AlmacenController@mostrar_niveles_estante');
	Route::get('mostrar_nivel/{id}', 'AlmacenController@mostrar_nivel');
	Route::post('guardar_nivel', 'AlmacenController@guardar_nivel');
	Route::post('actualizar_nivel', 'AlmacenController@update_nivel');
	Route::get('anular_nivel/{id}', 'AlmacenController@anular_nivel');
	Route::get('revisar_nivel/{id}', 'AlmacenController@revisar_nivel');
	Route::post('guardar_niveles', 'AlmacenController@guardar_niveles');
	Route::get('listar_posiciones', 'AlmacenController@mostrar_posiciones');
	Route::get('listar_posiciones_nivel/{id}', 'AlmacenController@mostrar_posiciones_nivel');
	Route::get('mostrar_posicion/{id}', 'AlmacenController@mostrar_posicion');
	Route::post('guardar_posiciones', 'AlmacenController@guardar_posiciones');
	Route::get('anular_posicion/{id}', 'AlmacenController@anular_posicion');
	/* Producto Ubicacion */
	Route::get('listar_ubicaciones_producto/{id}', 'AlmacenController@mostrar_ubicaciones_producto');
	Route::get('mostrar_ubicacion/{id}', 'AlmacenController@mostrar_ubicacion');
	Route::post('guardar_ubicacion', 'AlmacenController@guardar_ubicacion');
	Route::post('actualizar_ubicacion', 'AlmacenController@update_ubicacion');
	Route::get('anular_ubicacion/{id}', 'AlmacenController@anular_ubicacion');
	/* ProductoUbicacion Serie */
	Route::get('listar_series_producto/{id}', 'AlmacenController@listar_series_producto');
	Route::get('mostrar_serie/{id}', 'AlmacenController@mostrar_serie');
	Route::post('guardar_serie', 'AlmacenController@guardar_serie');
	Route::post('actualizar_serie', 'AlmacenController@update_serie');
	Route::get('anular_serie/{id}', 'AlmacenController@anular_serie');
	Route::get('tipo_almacen', 'AlmacenController@view_tipo_almacen');
	Route::get('listar_tipo_almacen', 'AlmacenController@mostrar_tipo_almacen');
	Route::get('cargar_tipo_almacen/{id}', 'AlmacenController@mostrar_tipo_almacenes');
	Route::post('guardar_tipo_almacen', 'AlmacenController@guardar_tipo_almacen');
	Route::post('editar_tipo_almacen', 'AlmacenController@update_tipo_almacen');
	Route::get('anular_tipo_almacen/{id}', 'AlmacenController@anular_tipo_almacen');
	Route::get('tipo_movimiento', 'AlmacenController@view_tipo_movimiento');
	Route::get('listar_tipoMov', 'AlmacenController@mostrar_tipos_mov');
	Route::get('mostrar_tipoMov/{id}', 'AlmacenController@mostrar_tipo_mov');
	Route::post('guardar_tipoMov', 'AlmacenController@guardar_tipo_mov');
	Route::post('actualizar_tipoMov', 'AlmacenController@update_tipo_mov');
	Route::get('anular_tipoMov/{id}', 'AlmacenController@anular_tipo_mov');
	Route::get('unid_med', 'AlmacenController@view_unid_med');
	Route::get('listar_unidmed', 'AlmacenController@mostrar_unidades_med');
	Route::get('mostrar_unidmed/{id}', 'AlmacenController@mostrar_unid_med');
	Route::post('guardar_unidmed', 'AlmacenController@guardar_unid_med');
	Route::post('actualizar_unidmed', 'AlmacenController@update_unid_med');
	Route::get('anular_unidmed/{id}', 'AlmacenController@anular_unid_med');
	/* Servicios */
	Route::get('tipoServ', 'AlmacenController@view_tipo_servicio');
	Route::get('listar_tipoServ', 'AlmacenController@mostrar_tp_servicios');
	Route::get('mostrar_tipoServ/{id}', 'AlmacenController@mostrar_tp_servicio');
	Route::post('guardar_tipoServ', 'AlmacenController@guardar_tp_servicio');
	Route::post('actualizar_tipoServ', 'AlmacenController@update_tp_servicio');
	Route::get('anular_tipoServ/{id}', 'AlmacenController@anular_tp_servicio');
	Route::get('servicio', 'AlmacenController@view_servicio');
	Route::get('listar_servicio', 'AlmacenController@mostrar_servicios');
	Route::get('mostrar_servicio/{id}', 'AlmacenController@mostrar_servicio');
	Route::post('guardar_servicio', 'AlmacenController@guardar_servicio');
	Route::post('actualizar_servicio', 'AlmacenController@update_servicio');
	Route::get('anular_servicio/{id}', 'AlmacenController@anular_servicio');
	/**Guia Compra */
	Route::get('guia_compra', 'AlmacenController@view_guia_compra');
	Route::get('mostrar_guias_compra', 'AlmacenController@mostrar_guias_compra');
	Route::get('mostrar_guia_compra/{id}', 'AlmacenController@mostrar_guia_compra');
	Route::post('guardar_guia_compra', 'AlmacenController@guardar_guia_compra');
	Route::post('actualizar_guia_compra', 'AlmacenController@update_guia_compra');
	Route::get('anular_guia_compra/{id}', 'AlmacenController@anular_guia_compra');
	Route::get('listar_guia_transportista/{guia}', 'AlmacenController@mostrar_transportistas');
	Route::get('mostrar_transportista/{id}', 'AlmacenController@mostrar_transportista');
	Route::post('guardar_transportista', 'AlmacenController@guardar_transportista');
	Route::post('actualizar_transportista', 'AlmacenController@update_transportista');
	Route::get('anular_transportista/{id}', 'AlmacenController@anular_transportista');
	Route::get('listar_guia_detalle/{guia}', 'AlmacenController@listar_guia_detalle');
	Route::get('mostrar_detalle/{id}', 'AlmacenController@mostrar_detalle');
	Route::post('guardar_detalle_oc', 'AlmacenController@guardar_detalle_oc');
	Route::post('guardar_guia_detalle', 'AlmacenController@guardar_guia_detalle');
	Route::post('update_detalle', 'AlmacenController@update_detalle');
	Route::get('anular_detalle/{id}', 'AlmacenController@anular_detalle');
	Route::get('listar_ocs', 'AlmacenController@listar_ocs');
	Route::get('listar_ordenes/{id}', 'AlmacenController@listar_ordenes');
	Route::get('listar_oc_det/{id}', 'AlmacenController@listar_oc_det');
	Route::get('anular_oc/{id}/{guia}', 'AlmacenController@anular_oc');
	Route::get('guia_ocs/{id}', 'AlmacenController@guia_ocs');
	Route::get('generar_ingreso/{id}/{id_usuario}', 'AlmacenController@generar_ingreso');
	Route::post('guardar_series', 'AlmacenController@guardar_series');
	Route::get('listar_series/{id}', 'AlmacenController@listar_series');
	Route::get('tipo_doc_almacen', 'AlmacenController@view_tipo_doc_almacen');
	Route::get('listar_tp_docs', 'AlmacenController@listar_tp_docs');
	Route::get('mostrar_tp_doc/{id}', 'AlmacenController@mostrar_tp_doc');
	Route::post('guardar_tp_doc', 'AlmacenController@guardar_tp_doc');
	Route::post('update_tp_doc', 'AlmacenController@update_tp_doc');
	Route::get('anular_tp_doc/{id}', 'AlmacenController@anular_tp_doc');
	/**Guia Venta */
	Route::get('guia_venta', 'AlmacenController@view_guia_venta');
	Route::get('listar_guias_compra/{id}', 'AlmacenController@listar_guias_compra');
	Route::post('guardar_guia_venta', 'AlmacenController@guardar_guia_venta');
	Route::post('actualizar_guia_venta', 'AlmacenController@update_guia_venta');
	Route::get('anular_guia_venta/{id}', 'AlmacenController@anular_guia_venta');
	Route::get('listar_guias_venta', 'AlmacenController@listar_guias_venta');
	Route::get('mostrar_guia_venta/{id}', 'AlmacenController@mostrar_guia_venta');
	Route::get('listar_ing_det/{id}/{tp}', 'AlmacenController@listar_ing_det');
	Route::get('listar_req/{id}', 'AlmacenController@listar_req');
	Route::post('guardar_detalle_ing', 'AlmacenController@guardar_detalle_ing');
	Route::get('listar_guia_ven_det/{id}', 'AlmacenController@listar_guia_ven_det');
	Route::post('guardar_guia_ven_detalle', 'AlmacenController@guardar_guia_ven_detalle');
	Route::post('update_guia_ven_detalle', 'AlmacenController@update_guia_ven_detalle');
	Route::get('anular_guia_ven_detalle/{id}', 'AlmacenController@anular_guia_ven_detalle');
	Route::get('generar_salida_guia/{id}/{usu}', 'AlmacenController@generar_salida_guia');
	Route::get('imprimir_salida/{id}', 'AlmacenController@imprimir_salida');
	Route::get('id_salida/{id}', 'AlmacenController@id_salida');
	Route::get('anular_guia/{doc}/{guia}', 'AlmacenController@anular_guia');
	Route::post('update_series', 'AlmacenController@update_series');
	Route::get('direccion_almacen/{id}', 'AlmacenController@direccion_almacen');
	Route::get('mostrar_clientes', 'AlmacenController@mostrar_clientes');
	/**Doc Compra */
	Route::get('doc_compra', 'AlmacenController@view_doc_compra');
	Route::get('listar_docs_compra', 'AlmacenController@listar_docs_compra');
	Route::get('listar_doc_guias/{id}', 'AlmacenController@listar_doc_guias');
	Route::get('listar_doc_items/{id}', 'AlmacenController@listar_doc_items');
	Route::post('guardar_doc_compra', 'AlmacenController@guardar_doc_compra');
	Route::post('actualizar_doc_compra', 'AlmacenController@update_doc_compra');
	Route::post('update_doc_detalle', 'AlmacenController@update_doc_detalle');
	Route::get('anular_doc_detalle/{id}', 'AlmacenController@anular_doc_detalle');
	Route::get('anular_doc_compra/{id}', 'AlmacenController@anular_doc_compra');
	Route::post('guardar_doc_guia', 'AlmacenController@guardar_doc_guia');
	Route::get('mostrar_doc_com/{id}', 'AlmacenController@mostrar_doc_com');
	Route::get('mostrar_ingreso/{id}', 'AlmacenController@mostrar_ingreso');
	Route::get('imprimir_ingreso/{id}', 'AlmacenController@imprimir_ingreso');
	Route::get('id_ingreso/{id}', 'AlmacenController@id_ingreso');
	Route::get('listar_guias_prov/{id}', 'AlmacenController@listar_guias_prov');
	Route::get('guardar_doc_items_guia/{id}/{id_doc}', 'AlmacenController@guardar_doc_items_guia');
	Route::get('mostrar_doc_detalle/{id}', 'AlmacenController@mostrar_doc_detalle');
	/**Doc Venta */
	Route::get('doc_venta', 'AlmacenController@view_doc_venta');
	Route::get('listar_docs_venta', 'AlmacenController@listar_docs_venta');
	Route::get('mostrar_doc_venta/{id}', 'AlmacenController@mostrar_doc_venta');
	Route::post('guardar_doc_venta', 'AlmacenController@guardar_doc_venta');
	Route::post('actualizar_doc_venta', 'AlmacenController@update_doc_venta');
	Route::get('anular_doc_venta/{id}', 'AlmacenController@anular_doc_venta');
	Route::get('listar_guias_emp/{id}', 'AlmacenController@listar_guias_emp');
	Route::get('guardar_docven_items_guia/{guia}/{id}', 'AlmacenController@guardar_docven_items_guia');
	Route::get('listar_docven_items/{id}', 'AlmacenController@listar_docven_items');
	Route::get('listar_docven_guias/{id}', 'AlmacenController@listar_docven_guias');
	Route::get('anular_guiaven/{id}/{guia}', 'AlmacenController@anular_guiaven');
	Route::post('update_docven_detalle', 'AlmacenController@update_docven_detalle');
	Route::get('saldo_actual/{id}/{ubi}', 'AlmacenController@saldo_actual');
	Route::get('costo_promedio/{id}/{ubi}', 'AlmacenController@costo_promedio');
	Route::post('tipo_cambio/{fecha}', 'AlmacenController@tipo_cambio');
	Route::get('cola_atencion', 'AlmacenController@view_cola_atencion');
	Route::get('listar_requerimientos', 'AlmacenController@listar_requerimientos');
	Route::get('listar_items_req/{id}', 'AlmacenController@listar_items_req');
	Route::post('generar_salida', 'AlmacenController@generar_salida');
	Route::post('guardar_guia_ven', 'AlmacenController@guardar_guia_ven');
	Route::get('imprimir_salida/{id}', 'AlmacenController@imprimir_salida');
	Route::get('req_almacen/{id}', 'AlmacenController@req_almacen');
	Route::post('guardar_prorrateo', 'AlmacenController@guardar_prorrateo');
	Route::post('update_doc_prorrateo', 'AlmacenController@update_doc_prorrateo');
	Route::post('update_guia_detalle', 'AlmacenController@update_guia_detalle');
	Route::get('listar_docs_prorrateo/{id}', 'AlmacenController@listar_docs_prorrateo');
	Route::get('listar_guia_detalle_prorrateo/{id}/{total}', 'AlmacenController@listar_guia_detalle_prorrateo');
	Route::get('eliminar_doc_prorrateo/{id}/{id_doc}', 'AlmacenController@eliminar_doc_prorrateo');
	Route::get('getTipoCambio/{fecha}', 'AlmacenController@getTipoCambio');
	Route::get('tipo_cambio/{fecha}', 'AlmacenController@tipo_cambio');

	/**Kardex de Almacen */
	Route::get('kardex_general', 'AlmacenController@view_kardex_general');
	Route::get('kardex_general/{id}/{fini}/{ffin}', 'AlmacenController@kardex_general');
	Route::get('kardex_sunat/{id}/{fini}/{ffin}', 'AlmacenController@download_kardex_sunat');
	Route::get('kardex_sunatx/{id}', 'AlmacenController@kardex_sunat');
	Route::get('movimientos_producto/{id}/{prod}', 'AlmacenController@movimientos_producto');
	Route::get('verifica_posiciones/{id}', 'AlmacenController@verifica_posiciones');
	Route::get('kardex_detallado', 'AlmacenController@view_kardex_detallado');
	Route::get('kardex_producto/{id}/{fini}/{ffin}', 'AlmacenController@kardex_producto');
	Route::get('kardex_detallado/{id}/{fini}/{ffin}', 'AlmacenController@download_kardex_producto');
	Route::get('saldos', 'AlmacenController@view_saldos');
	Route::get('saldos/{id}', 'AlmacenController@saldos');
	Route::get('saldo_producto/{id}/{prod}/{fec}', 'AlmacenController@saldo_producto');
	Route::get('lista_ingresos', 'AlmacenController@view_ingresos');
	Route::get('listar_ingresos/{alm}/{docs}/{cond}/{fini}/{ffin}/{prov}/{usu}/{mon}/{tra}', 'AlmacenController@listar_ingresos');
	Route::get('lista_salidas', 'AlmacenController@view_salidas');
	Route::get('listar_salidas/{alm}/{docs}/{cond}/{fini}/{ffin}/{cli}/{usu}/{mon}/{ref}', 'AlmacenController@listar_salidas');
	Route::get('select_almacenes_empresa/{id}', 'AlmacenController@select_almacenes_empresa');
	Route::get('update_revisado/{id}/{rev}/{obs}', 'AlmacenController@update_revisado');
	Route::get('busqueda_ingresos', 'AlmacenController@view_busqueda_ingresos');
	Route::get('listar_busqueda_ingresos/{alm}/{tp}/{des}/{doc}/{fini}/{ffin}', 'AlmacenController@listar_busqueda_ingresos');
	Route::get('imprimir_guia_ingreso/{id}', 'AlmacenController@imprimir_guia_ingreso');
	Route::get('busqueda_salidas', 'AlmacenController@view_busqueda_salidas');
	Route::get('listar_busqueda_salidas/{alm}/{tp}/{des}/{doc}/{fini}/{ffin}', 'AlmacenController@listar_busqueda_salidas');
	Route::get('listar_transportistas_com', 'AlmacenController@listar_transportistas_com');
	Route::get('listar_transportistas_ven', 'AlmacenController@listar_transportistas_ven');

	/* Proyectos */
	Route::get('sis_contrato', 'ProyectosController@view_sis_contrato');
	Route::get('listar_sis_contratos', 'ProyectosController@mostrar_sis_contratos');
	Route::get('mostrar_sis_contrato/{id}', 'ProyectosController@mostrar_sis_contrato');
	Route::post('guardar_sis_contrato', 'ProyectosController@guardar_sis_contrato');
	Route::post('actualizar_sis_contrato', 'ProyectosController@update_sis_contrato');
	Route::get('anular_sis_contrato/{id}', 'ProyectosController@anular_sis_contrato');
	Route::get('tipo_insumo', 'ProyectosController@view_tipo_insumo');
	Route::get('listar_tipo_insumos', 'ProyectosController@mostrar_tipos_insumos');
	Route::get('mostrar_tipo_insumo/{id}', 'ProyectosController@mostrar_tp_insumo');
	Route::post('guardar_tipo_insumo', 'ProyectosController@guardar_tp_insumo');
	Route::post('actualizar_tipo_insumo', 'ProyectosController@update_tp_insumo');
	Route::get('anular_tipo_insumo/{id}', 'ProyectosController@anular_tp_insumo');
	Route::get('revisar_tipo_insumo/{id}', 'ProyectosController@buscar_tp_insumo');
	Route::get('iu', 'ProyectosController@view_iu');
	Route::get('listar_ius', 'ProyectosController@mostrar_ius');
	Route::get('mostrar_iu/{id}', 'ProyectosController@mostrar_iu');
	Route::post('guardar_iu', 'ProyectosController@guardar_iu');
	Route::post('actualizar_iu', 'ProyectosController@update_iu');
	Route::get('anular_iu/{id}', 'ProyectosController@anular_iu');
	Route::get('revisar_iu/{id}', 'ProyectosController@buscar_iu');
	Route::get('insumo', 'ProyectosController@view_insumo');
	Route::get('listar_insumos', 'ProyectosController@mostrar_insumos');
	Route::get('mostrar_insumo/{id}', 'ProyectosController@mostrar_insumo');
	Route::post('guardar_insumo', 'ProyectosController@guardar_insumo');
	Route::post('actualizar_insumo', 'ProyectosController@update_insumo');
	Route::get('anular_insumo/{id}', 'ProyectosController@anular_insumo');
	Route::get('acu', 'ProyectosController@view_acu');
	Route::get('listar_acus', 'ProyectosController@mostrar_acus');
	Route::get('mostrar_acu/{id}', 'ProyectosController@mostrar_acu_todo');
	Route::get('listar_acu_detalle/{id}', 'ProyectosController@listar_acu_detalle');
	Route::post('guardar_acu', 'ProyectosController@guardar_acu');
	Route::post('actualizar_acu', 'ProyectosController@update_acu');
	Route::get('anular_acu/{id}', 'ProyectosController@anular_acu');
	Route::get('prueba/{det}', 'ProyectosController@prueba');
	Route::get('opcion', 'ProyectosController@view_opcion');
	Route::get('listar_opciones', 'ProyectosController@mostrar_opciones');
	Route::post('guardar_opcion', 'ProyectosController@guardar_opcion');
	Route::post('actualizar_opcion', 'ProyectosController@update_opcion');
	Route::get('anular_opcion/{id}', 'ProyectosController@anular_opcion');
	/**Presupuesto */
	Route::get('presint', 'ProyectosController@view_presint');
	Route::get('listar_presint/{id}', 'ProyectosController@mostrar_presupuestos');
	Route::get('mostrar_presint/{id}', 'ProyectosController@mostrar_presint');
	Route::get('mostrar_todo_presint/{id}', 'ProyectosController@mostrar_presupuesto_completo');
	Route::get('listar_cd/{id}', 'ProyectosController@listar_cd');
	Route::get('listar_ci/{id}', 'ProyectosController@listar_ci');
	Route::get('listar_gg/{id}', 'ProyectosController@listar_gg');
	Route::post('guardar_presint', 'ProyectosController@guardar_presint');
	Route::post('update_presint', 'ProyectosController@update_presint');
	Route::post('guardar_componente_cd', 'ProyectosController@guardar_componente_cd');
	Route::post('guardar_componente_ci', 'ProyectosController@guardar_componente_ci');
	Route::post('guardar_componente_gg', 'ProyectosController@guardar_componente_gg');
	Route::post('update_componente_cd', 'ProyectosController@update_componente_cd');
	Route::post('update_componente_ci', 'ProyectosController@update_componente_ci');
	Route::post('update_componente_gg', 'ProyectosController@update_componente_gg');
	Route::post('anular_compo_cd', 'ProyectosController@anular_compo_cd');
	Route::post('anular_compo_ci', 'ProyectosController@anular_compo_ci');
	Route::post('anular_compo_gg', 'ProyectosController@anular_compo_gg');
	Route::post('guardar_partida_cd', 'ProyectosController@guardar_partida_cd');
	Route::post('guardar_partida_ci', 'ProyectosController@guardar_partida_ci');
	Route::post('guardar_partida_gg', 'ProyectosController@guardar_partida_gg');
	Route::post('update_partida_cd', 'ProyectosController@update_partida_cd');
	Route::post('update_partida_ci', 'ProyectosController@update_partida_ci');
	Route::post('update_partida_gg', 'ProyectosController@update_partida_gg');
	Route::post('anular_partida_cd', 'ProyectosController@anular_partida_cd');
	Route::post('anular_partida_ci', 'ProyectosController@anular_partida_ci');
	Route::post('anular_partida_gg', 'ProyectosController@anular_partida_gg');
	Route::get('generar_propuesta/{id}', 'ProyectosController@generar_propuesta');
	Route::get('suma_partidas_ci/{padre}/{id}', 'ProyectosController@suma_partidas_ci');
	Route::get('actualiza_totales/{id}', 'ProyectosController@actualiza_totales');
	Route::get('propuesta', 'ProyectosController@view_propuesta');
	Route::get('preseje', 'ProyectosController@view_preseje');
	Route::get('cronoint', 'ProyectosController@view_cronoint');
	Route::get('proyecto', 'ProyectosController@view_proyecto');
	Route::get('listar_proyectos', 'ProyectosController@listar_proyectos');
	Route::post('guardar_proyecto', 'ProyectosController@guardar_proyecto');
	Route::post('actualizar_proyecto', 'ProyectosController@actualizar_proyecto');
	Route::get('anular_proyecto/{id}', 'ProyectosController@anular_proyecto');
	Route::get('listar_contratos_proy/{id}', 'ProyectosController@listar_contratos_proy');
	Route::post('guardar_contrato', 'ProyectosController@guardar_contrato');
	Route::get('abrir_adjunto/{adjunto}', 'ProyectosController@abrir_adjunto');
	Route::get('abrir_adjunto_partida/{adjunto}', 'ProyectosController@abrir_adjunto_partida');
	Route::get('anular_contrato/{id}', 'ProyectosController@anular_contrato');
	Route::get('listar_obs_cd/{id}', 'ProyectosController@listar_obs_cd');
	Route::get('listar_obs_ci/{id}', 'ProyectosController@listar_obs_ci');
	Route::get('listar_obs_gg/{id}', 'ProyectosController@listar_obs_gg');
	Route::get('anular_obs_partida/{id}', 'ProyectosController@anular_obs_partida');
	Route::post('guardar_obs_partida', 'ProyectosController@guardar_obs_partida');

	/* Maquinaria y Equipos */
	Route::get('equi_tipo', 'EquipoController@view_equi_tipo');
	Route::get('listar_equi_tipos', 'EquipoController@mostrar_equi_tipos');
	Route::get('mostrar_equi_tipo/{id}', 'EquipoController@mostrar_equi_tipo');
	Route::post('guardar_equi_tipo', 'EquipoController@guardar_equi_tipo');
	Route::post('actualizar_equi_tipo', 'EquipoController@update_equi_tipo');
	Route::get('anular_equi_tipo/{id}', 'EquipoController@anular_equi_tipo');
	Route::get('equi_cat', 'EquipoController@view_equi_cat');
	Route::get('listar_equi_cats', 'EquipoController@mostrar_equi_cats');
	Route::get('mostrar_equi_cat/{id}', 'EquipoController@mostrar_equi_cat');
	Route::post('guardar_equi_cat', 'EquipoController@guardar_equi_cat');
	Route::post('actualizar_equi_cat', 'EquipoController@update_equi_cat');
	Route::get('anular_equi_cat/{id}', 'EquipoController@anular_equi_cat');
	// Route::get('equipo', 'EquipoController@view_equipo');
	Route::get('listar_equipos', 'EquipoController@mostrar_equipos');
	Route::get('mostrar_equipo/{id}', 'EquipoController@mostrar_equipo');
	Route::post('guardar_equipo', 'EquipoController@guardar_equipo');
	Route::post('actualizar_equipo', 'EquipoController@update_equipo');
	Route::get('anular_equipo/{id}', 'EquipoController@anular_equipo');
	Route::get('equi_catalogo', 'EquipoController@view_equi_catalogo');
	Route::get('listar_seguros/{id}', 'EquipoController@listar_seguros');
	Route::post('guardar_seguro', 'EquipoController@guardar_seguro');
	Route::get('abrir_adjunto_seguro/{id}', 'EquipoController@abrir_adjunto_seguro');
	Route::get('anular_seguro/{id}', 'EquipoController@anular_seguro');
	Route::get('guardar_tipo_doc/{id}', 'EquipoController@guardar_tipo_doc');
	Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');
	Route::get('docs', 'EquipoController@view_docs');
	Route::get('mtto_realizados', 'EquipoController@view_mtto_realizados');
	Route::get('listar_mttos_detalle', 'EquipoController@listar_mttos_detalle');
	Route::get('listar_programaciones/{id}', 'EquipoController@listar_programaciones');
	Route::post('guardar_programacion', 'EquipoController@guardar_programacion');
	Route::get('anular_programacion/{id}', 'EquipoController@anular_programacion');
	/**Mantenimientos */
	Route::get('mtto_pendientes', 'EquipoController@view_mtto_pendientes');
	Route::get('listar_mtto_pendientes', 'EquipoController@listar_programaciones_pendientes');
	Route::get('listar_mtto_pendientes/{id}', 'EquipoController@listar_mtto_pendientes');
	Route::get('listar_todas_programaciones', 'EquipoController@listar_todas_programaciones');
	Route::get('mtto', 'EquipoController@view_mtto');
	Route::get('listar_mttos', 'EquipoController@listar_mttos');
	Route::get('mostrar_mtto/{id}', 'EquipoController@mostrar_mtto');
	Route::post('guardar_mtto', 'EquipoController@guardar_mtto');
	Route::post('actualizar_mtto', 'EquipoController@update_mtto');
	Route::get('anular_mtto/{id}', 'EquipoController@anular_mtto');
	Route::post('guardar_mtto_detalle', 'EquipoController@guardar_mtto_detalle');
	Route::post('update_mtto_detalle', 'EquipoController@update_mtto_detalle');
	Route::get('anular_mtto_detalle/{id}', 'EquipoController@anular_mtto_detalle');
	Route::get('listar_mtto_detalle/{id}', 'EquipoController@listar_mtto_detalle');
	Route::get('mostrar_mtto_detalle/{id}', 'EquipoController@mostrar_mtto_detalle');
	Route::get('listar_partidas/{id}', 'EquipoController@listar_partidas');
	Route::get('tp_combustible', 'EquipoController@view_tp_combustible');
	Route::get('listar_tp_combustibles', 'EquipoController@mostrar_tp_combustibles');
	Route::get('mostrar_tp_combustible/{id}', 'EquipoController@mostrar_tp_combustible');
	Route::post('guardar_tp_combustible', 'EquipoController@guardar_tp_combustible');
	Route::post('actualizar_tp_combustible', 'EquipoController@update_tp_combustible');
	Route::get('anular_tp_combustible/{id}', 'EquipoController@anular_tp_combustible');
	/**Solicitud de Equipos */
	Route::get('equi_sol', 'EquipoController@view_equi_sol');
	Route::get('presup_ejecucion/{id}', 'ProyectosController@mostrar_presup_ejecucion_contrato');
	Route::get('mostrar_solicitudes/{id}', 'EquipoController@mostrar_solicitudes');
	Route::get('mostrar_solicitud/{id}', 'EquipoController@mostrar_solicitud');
	Route::post('guardar_equi_sol', 'EquipoController@guardar_equi_sol');
	Route::post('actualizar_equi_sol', 'EquipoController@update_equi_sol');
	Route::get('anular_equi_sol/{id}', 'EquipoController@anular_equi_sol');
	Route::get('aprob_sol', 'EquipoController@view_aprob_sol');
	Route::get('listar_aprob_sol/{id}/{gr}', 'EquipoController@listar_aprob_sol');
	Route::post('guardar_aprobacion', 'EquipoController@guardar_aprobacion');
	Route::post('guardar_sustento', 'EquipoController@guardar_sustento');
	Route::post('solicitud_cambia_estado/{id}/{est}', 'EquipoController@solicitud_cambia_estado');
	Route::get('solicitud_flujos/{id}/{sol}', 'EquipoController@solicitud_flujos');
	Route::get('sol_todas', 'EquipoController@view_sol_todas');
	Route::get('listar_todas_solicitudes', 'EquipoController@listar_todas_solicitudes');
	Route::get('imprimir_solicitud/{id}', 'EquipoController@imprimir_solicitud');
	/**Asignacion de Equipos */
	Route::get('asignacion', 'EquipoController@view_asignacion');
	Route::get('listar_solicitudes', 'EquipoController@listar_solicitudes_aprobadas');
	Route::get('equipos_disponibles/{id}', 'EquipoController@equipos_disponibles');
	Route::post('guardar_asignacion', 'EquipoController@guardar_asignacion');
	Route::post('editar_asignacion', 'EquipoController@update_equi_asig');
	Route::get('anular_asignacion/{id}', 'EquipoController@anular_equi_asig');
	Route::get('control', 'EquipoController@view_control');
	Route::get('listar_asignaciones/{id}', 'EquipoController@listar_asignaciones');
	Route::get('mostrar_asignacion/{id}', 'EquipoController@mostrar_asignacion');
	Route::get('listar_controles/{id}', 'EquipoController@listar_controles');
	Route::post('guardar_control', 'EquipoController@guardar_control');
	Route::post('actualizar_control', 'EquipoController@update_control');
	Route::get('anular_control/{id}', 'EquipoController@anular_control');
	Route::get('mostrar_control/{id}', 'EquipoController@mostrar_control');
	Route::get('getTrabajador/{id}', 'EquipoController@getTrabajador');
	Route::get('kilometraje_actual/{id}', 'EquipoController@kilometraje_actual');
	Route::get('select_programaciones/{id}', 'EquipoController@select_programaciones');
	Route::get('decode5t/{id}', 'EquipoController@decode5t');
	/* Logistica */
	// Route::get('logistica/sedes/{empresa_id}','LogisticaController@getSedes')->name('logistica_sedes');
	// Route::get('logistica/areas/{sede_id}','LogisticaController@getGruposAreas')->name('logistica_areas');
	Route::get('logistica/mostrar_items', 'LogisticaController@mostrar_items');
	Route::get('logistica/mostrar_item/{id_item}', 'LogisticaController@mostrar_item');
	// Route::get('cargar_estructura_org/{id}', 'ConfiguracionController@cargar_estructura_org'); /////// modal area
	Route::get('logistica/proyectos_contratos', 'ProyectosController@mostrar_proyectos_contratos');
	Route::get('logistica/proyecto/{id}', 'ProyectosController@mostrar_proyecto');
	Route::get('logistica/requerimientos', 'LogisticaController@mostrar_requerimientos');
	Route::post('logistica/guardar-archivos-adjuntos', 'LogisticaController@guardar_archivos_adjuntos');
	Route::get('logistica/mostrar-archivos-adjuntos/{id_detalle_requerimiento}', 'LogisticaController@mostrar_archivos_adjuntos');
	Route::get('logistica/mostrar-adjuntos/{id_requerimiento}', 'LogisticaController@mostrar_adjuntos');
	Route::get('logistica/imprimir-requerimiento-pdf/{id}/{codigo}', 'LogisticaController@generar_requerimiento_pdf');
	Route::get('logistica/requerimiento/lista', 'LogisticaController@view_lista_requerimientos');
	Route::get('logistica/requerimiento/{id}/{codigo}', 'LogisticaController@mostrar_requerimiento')->where('id', '(.*)');
	Route::get('logistica/requerimiento/gestionar', 'LogisticaController@view_gestionar_requerimiento');
	Route::get('logistica/cotizacion/gestionar', 'LogisticaController@view_gestionar_cotizaciones');
	Route::get('logistica/cotizacion/cuadro-comparativo', 'LogisticaController@view_cuadro_comparativo');
	Route::get('logistica/orden/generar', 'LogisticaController@view_generar_orden');
	Route::post('logistica/guardar_requerimiento', 'LogisticaController@guardar_requerimiento');
	Route::put('logistica/actualizar_requerimiento/{id}', 'LogisticaController@actualizar_requerimiento');
	Route::get('logistica/listar_requerimientos', 'LogisticaController@listar_requerimiento_v2');
	Route::get('logistica/observar_req/{req}/{doc}', 'LogisticaController@observar_requerimiento_vista');
	Route::post('logistica/observar_detalles', 'LogisticaController@observar_requerimiento_item');
	Route::post('logistica/aprobar_documento', 'LogisticaController@aprobar_requerimiento');
	Route::post('logistica/observar_contenido', 'LogisticaController@observar_requerimiento');
	Route::post('logistica/denegar_documento', 'LogisticaController@denegar_requerimiento');
	Route::get('logistica/ver_flujos/{req}/{doc}', 'LogisticaController@flujo_aprobacion');
	Route::post('logistica/guardar_sustento', 'LogisticaController@guardar_sustento');
	Route::post('logistica/aceptar_sustento', 'LogisticaController@aceptar_sustento'); 

	/**Logistica Cotizaciones */
	Route::get('logistica/mostrar-archivos-adjuntos-proveedor/{id}', 'LogisticaController@mostrar_archivos_adjuntos_proveedor');
	Route::post('logistica/guardar-archivos-adjuntos-proveedor', 'LogisticaController@guardar_archivos_adjuntos_proveedor');
	Route::get('archivos_adjuntos_cotizacion/{id_cotizacion}', 'LogisticaController@mostrar_archivos_adjuntos_cotizacion');
	Route::get('gestionar_cotizaciones', 'LogisticaController@view_gestionar_cotizaciones');
	Route::get('listar_grupo_cotizaciones', 'LogisticaController@listar_grupo_cotizaciones');
	Route::get('listar_requerimientos_pendientes', 'LogisticaController@listar_requerimientos_pendientes');
	Route::get('detalle_requerimiento/{id}', 'LogisticaController@detalle_requerimiento');
	Route::get('guardar_cotizacion/{id}/{id_gru}', 'LogisticaController@guardar_cotizacion');
	Route::get('cotizaciones_por_grupo/{id}', 'LogisticaController@cotizaciones_por_grupo');
	Route::get('items_cotizaciones_por_grupo/{id}', 'LogisticaController@items_cotizaciones_por_grupo');
	Route::get('mostrar_grupo_cotizacion/{id}', 'LogisticaController@mostrar_grupo_cotizacion');
	Route::get('mostrar_proveedores', 'LogisticaController@mostrar_proveedores');
	Route::post('guardar_proveedor', 'LogisticaController@guardar_proveedor');

	Route::get('mostrar_email_proveedor/{id}', 'LogisticaController@mostrar_email_proveedor');
	Route::post('update_cotizacion', 'LogisticaController@update_cotizacion');
	Route::post('duplicate_cotizacion', 'LogisticaController@duplicate_cotizacion');
	Route::get('mostrar_cotizacion/{id}', 'LogisticaController@mostrar_cotizacion');
	Route::post('guardar_contacto', 'LogisticaController@guardar_contacto');
	Route::get('solicitud_cotizacion_excel/{id}', 'LogisticaController@solicitud_cotizacion_excel');
	Route::get('anular_cotizacion/{id}', 'LogisticaController@anular_cotizacion');
	Route::get('saldo_por_producto/{id}', 'AlmacenController@saldo_por_producto');

	Route::get('form_enviar_correo', 'CorreoController@crear');
	Route::post('enviar_correo', 'CorreoController@enviar');
	Route::post('cargar_archivo_correo', 'CorreoController@store');

	// logistica - cuadro comparativo
	Route::get('logistica/detalle_unidad_medida/{id_unidad_medida}', 'LogisticaController@detalle_unidad_medida');
	Route::get('logistica/cuadro_comparativo/mostrar_comparativo/{id1}', 'LogisticaController@mostrar_comparativo');
	Route::post('/logistica/valorizacion_item', 'LogisticaController@update_valorizacion_item');
	Route::post('/logistica/valorizacion_especificacion', 'LogisticaController@update_valorizacion_especificacion');
	Route::get('logistica/cuadro_comparativos/valorizacion/lista_item/{id_cotizacion}', 'LogisticaController@listaItemValorizar');
	Route::get('logistica/cuadro_comparativos', 'LogisticaController@mostrar_cuadro_comparativos');
	Route::get('logistica/cuadro_comparativo/{id}', 'LogisticaController@mostrar_cuadro_comparativo');
	Route::get('logistica/cuadro_comparativo/grupo_cotizaciones/{codigo_cotizacion}/{codigo_cuadro_comparativo}/{id_grupo_cotizacion}', 'LogisticaController@grupo_cotizaciones')->where('id', '(.*)');
	Route::post('logistica/cuadro_comparativo/guardar_buenas_pro', 'LogisticaController@guardar_buenas_pro');
	Route::put('logistica/cuadro_comparativo/eliminar_buena_pro/{id_valorizacion}', 'LogisticaController@eliminar_buena_pro');
	Route::get('logistica/cuadro_comparativo/exportar_excel/{id_grupo}', 'LogisticaController@solicitud_cuadro_comparativo_excel');
	/**Logistica Ordenes */
	Route::get('generar_orden', 'LogisticaController@view_generar_orden');
	Route::get('detalle_cotizacion/{id}', 'LogisticaController@detalle_cotizacion');
	Route::post('guardar_orden_compra', 'LogisticaController@guardar_orden_compra');
	Route::post('update_orden_compra', 'LogisticaController@update_orden_compra');
	Route::get('anular_orden_compra/{id}', 'LogisticaController@anular_orden_compra');
	Route::get('mostrar_cuentas_bco/{id}', 'LogisticaController@mostrar_cuentas_bco');
	Route::get('listar_ordenes', 'LogisticaController@listar_ordenes');
	Route::get('mostrar_orden/{id}', 'LogisticaController@mostrar_orden');
	Route::get('listar_detalle_orden/{id}', 'LogisticaController@listar_detalle_orden');
	Route::post('guardar_cuenta_banco', 'LogisticaController@guardar_cuenta_banco');
	Route::get('mostrar_impuesto/{id}/{fecha}', 'ProyectosController@mostrar_impuesto');
	Route::get('imprimir_orden_pdf/{id}', 'LogisticaController@imprimir_orden_pdf'); // PDF
	Route::get('generar_orden_pdf/{id}', 'LogisticaController@generar_orden_pdf'); // PDF
	Route::get('lista_proveedores', 'LogisticaController@view_lista_proveedores');
	Route::get('logistica/listar_proveedores', 'LogisticaController@listar_proveedores');


	/**Contabilidad */
	Route::get('cta_contable', 'ContabilidadController@view_cta_contable');
	Route::get('mostrar_cta_contables', 'ContabilidadController@mostrar_cuentas_contables');

	// APIs de Terceros
	Route::post('consulta_sunat', 'HynoTechController@consulta_sunat');


	// Route::get('hasObsDetReq/{id_req}', 'LogisticaController@hasObsDetReq');
	// // Route::get('get_header_observacion/{id_req}', 'LogisticaController@get_header_observacion');
	// Route::get('get_id_req_by_id_coti/{id_req}', 'LogisticaController@get_id_req_by_id_coti');
	// Route::get('get_orden/{id_req}', 'LogisticaController@get_orden');

	//////////////////////
	////ADMINISTRACION
	Route::get('empresas', 'AdministracionController@view_empresa');
	Route::get('sedes', 'AdministracionController@view_sede');
	Route::get('grupos', 'AdministracionController@view_grupo');
	Route::get('areas', 'AdministracionController@view_area');

	Route::get('listar_empresa', 'AdministracionController@mostrar_empresa_table');
	Route::get('cargar_empresa/{id}', 'AdministracionController@mostrar_empresa_id');
	Route::post('guardar_empresa_contri', 'AdministracionController@guardar_empresas');
	Route::post('editar_empresa_contri', 'AdministracionController@actualizar_empresas');

	Route::get('listar_contacto_empresa/{id}', 'AdministracionController@mostrar_contacto_empresa');
	Route::post('guardar_contacto_empresa', 'AdministracionController@guardar_contacto_empresa');
	Route::post('editar_contacto_empresa', 'AdministracionController@actualizar_contacto_empresa'); //FALTA ANULAR

	Route::get('listar_cuentas_empresa/{id}', 'AdministracionController@mostrar_cuentas_empresa');
	Route::post('guardar_cuentas_empresa', 'AdministracionController@guardar_cuentas_empresa');
	Route::post('editar_cuentas_empresa', 'AdministracionController@actualizar_cuentas_empresa'); //FALTA ANULAR

	Route::get('listar_sede', 'AdministracionController@mostrar_sede_table');
	Route::get('buscar_codigo_empresa/{value}/{type}', 'AdministracionController@codigoEmpresa');
	Route::get('cargar_sede/{id}', 'AdministracionController@mostrar_sede_id');
	Route::post('guardar_sede', 'AdministracionController@guardar_sede');
	Route::post('editar_sede', 'AdministracionController@actualizar_sede');
	Route::get('anular_sede/{id}', 'AdministracionController@anular_sede');

	Route::get('listar_grupo', 'AdministracionController@mostrar_grupo_table');
	Route::get('cargar_grupo/{id}', 'AdministracionController@mostrar_grupo_id');
	Route::post('guardar_grupo', 'AdministracionController@guardar_grupo');
	Route::post('editar_grupo', 'AdministracionController@actualizar_grupo');
	Route::get('anular_grupo/{id}', 'AdministracionController@anular_grupo');

	Route::get('listar_area', 'AdministracionController@mostrar_area_table');
	Route::get('cargar_area/{id}', 'AdministracionController@mostrar_area_id');
	Route::post('guardar_area', 'AdministracionController@guardar_area');
	Route::post('editar_area', 'AdministracionController@actualizar_area');
	Route::get('anular_area/{id}', 'AdministracionController@anular_area');

	Route::get('ver_session', 'LogisticaController@verSession');
});
