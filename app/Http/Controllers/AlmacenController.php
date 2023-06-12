<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use PDF;
use App\Models\almacen\mov_alm as Movimiento;
use App\Models\almacen\mov_alm_det as MovDetalle;
use App\Models\almacen\guia_com as GuiaCompra;
use Illuminate\Support\Collection as Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set('America/Lima');

class AlmacenController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_tipo(){
        return view('almacen/producto/tipo');
    }
    function view_categoria(){
        $tipos = $this->mostrar_tipos_cbo();
        return view('almacen/producto/categoria', compact('tipos'));
    }
    function view_subcategoria(){
        return view('almacen/producto/subcategoria');
    }
    function view_clasificacion(){
        return view('almacen/producto/clasificacion');
    }
    function view_prod_catalogo(){
        return view('almacen/producto/prod_catalogo');
    }
    function view_producto(){
        $clasificaciones = $this->mostrar_clasificaciones_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $posiciones = $this->mostrar_posiciones_cbo();
        $ubicaciones = $this->mostrar_ubicaciones_cbo();
        return view('almacen/producto/producto', compact('clasificaciones','unidades','posiciones','ubicaciones'));
    }
    function view_almacenes(){
        $sedes = $this->mostrar_sedes_cbo();
        $tipos = $this->mostrar_tp_almacen_cbo();
        return view('almacen/ubicacion/almacenes', compact('sedes','tipos'));
    }
    function view_ubicacion(){
        $almacenes = $this->mostrar_almacenes_cbo();
        $estantes = $this->mostrar_estantes_cbo();
        $niveles = $this->mostrar_niveles_cbo();
        return view('almacen/ubicacion/ubicacion', compact('almacenes','estantes','niveles'));
    }
    function view_tipo_almacen(){
        return view('almacen/variables/tipo_almacen');
    }
    function view_tipo_servicio(){
        return view('almacen/variables/tipoServ');
    }
    function view_servicio(){
        $tipos = $this->mostrar_tp_servicios_cbo();
        $detracciones = $this->mostrar_detracciones_cbo();
        return view('almacen/variables/servicio', compact('tipos','detracciones'));
    }
    function view_tipo_movimiento(){
        return view('almacen/variables/tipo_movimiento');
    }
    function view_unid_med(){
        return view('almacen/variables/unid_med');
    }
    function view_guia_compra(){
        $proveedores = $this->mostrar_proveedores_cbo();
        $almacenes = $this->mostrar_almacenes_cbo();
        $posiciones = $this->mostrar_posiciones_cbo();
        $motivos = $this->mostrar_motivos_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        // $condiciones = $this->mostrar_condiciones_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $monedas = $this->mostrar_moneda_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        $tp_operacion = $this->tp_operacion_cbo_ing();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        $tp_prorrateo = $this->select_tp_prorrateo();
        $usuarios = $this->select_usuarios();
        return view('almacen/guias/guia_compra', compact('proveedores','almacenes','posiciones','motivos','clasificaciones','tp_doc','monedas','tp_doc_almacen','tp_operacion','tp_contribuyente','sis_identidad','tp_prorrateo','usuarios'));
    }
    function view_guia_venta(){
        $almacenes = $this->mostrar_almacenes_cbo();
        $posiciones = $this->mostrar_posiciones_cbo();
        $motivos = $this->mostrar_motivos_cbo();
        // $condiciones = $this->mostrar_condiciones_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $empresas = $this->select_empresa();
        $proveedores = $this->mostrar_proveedores_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        $tp_operacion = $this->tp_operacion_cbo_sal();
        $tp_contribuyente = $this->tp_contribuyente_cbo();
        $sis_identidad = $this->sis_identidad_cbo();
        return view('almacen/guias/guia_venta', compact('almacenes','posiciones','motivos','clasificaciones','empresas','proveedores','tp_doc_almacen','tp_operacion','tp_contribuyente','sis_identidad'));
    }
    function view_doc_compra(){
        $proveedores = $this->mostrar_proveedores_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $condiciones = $this->mostrar_condiciones_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $moneda = $this->mostrar_moneda_cbo();
        $detracciones = $this->mostrar_detracciones_cbo();
        $impuestos = $this->mostrar_impuestos_cbo();
        return view('almacen/documentos/doc_compra', compact('proveedores','clasificaciones','condiciones','tp_doc','moneda','detracciones','impuestos'));
    }
    function view_doc_venta(){
        $empresas = $this->select_empresa();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $condiciones = $this->mostrar_condiciones_cbo();
        $tp_doc = $this->mostrar_tp_doc_cbo();
        $moneda = $this->mostrar_moneda_cbo();
        return view('almacen/documentos/doc_venta', compact('empresas','clasificaciones','condiciones','tp_doc','moneda'));
    }
    function view_cola_atencion(){
        $motivos = $this->mostrar_motivos_cbo();
        $clasificaciones = $this->mostrar_guia_clas_cbo();
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/cola_atencion', compact('motivos','clasificaciones','almacenes'));
    }
    function view_kardex_general(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/kardex_general', compact('almacenes','empresas'));
    }
    function view_tipo_doc_almacen(){
        $tp_doc = $this->mostrar_tp_doc_cbo();
        return view('almacen/variables/tipo_doc_almacen', compact('tp_doc'));
    }
    function view_kardex_detallado(){
        return view('almacen/reportes/kardex_detallado');
    }
    function view_saldos(){
        $almacenes = $this->mostrar_almacenes_cbo();
        return view('almacen/reportes/saldos', compact('almacenes'));
    }
    function view_ingresos(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        $tp_operacion = $this->tp_operacion_cbo_ing();
        $usuarios = $this->select_almaceneros();
        return view('almacen/reportes/lista_ingresos', compact('almacenes','empresas','tp_doc_almacen','tp_operacion','usuarios'));
    }
    function view_salidas(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        $tp_operacion = $this->tp_operacion_cbo_sal();
        $usuarios = $this->select_almaceneros();
        return view('almacen/reportes/lista_salidas', compact('almacenes','empresas','tp_doc_almacen','tp_operacion','usuarios'));
    }
    function view_busqueda_ingresos(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_ing();
        return view('almacen/reportes/busqueda_ingresos', compact('almacenes','empresas','tp_doc_almacen'));
    }
    function view_busqueda_salidas(){
        $empresas = $this->select_empresa();
        $almacenes = $this->mostrar_almacenes_cbo();
        $tp_doc_almacen = $this->tp_doc_almacen_cbo_sal();
        return view('almacen/reportes/busqueda_salidas', compact('almacenes','empresas','tp_doc_almacen'));
    }
    /* Combos */
    public function tp_contribuyente_cbo(){
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')->get();
        return $data;
    }
    public function sis_identidad_cbo(){
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }
    public function select_tp_prorrateo(){
        $data = DB::table('almacen.tp_prorrateo')
            ->select('tp_prorrateo.id_tp_prorrateo', 'tp_prorrateo.descripcion')
            ->where('tp_prorrateo.estado', '=', 1)
            ->orderBy('tp_prorrateo.id_tp_prorrateo', 'asc')->get();
        return $data;
    }
    public function tp_operacion_cbo_ing(){
        $data = DB::table('almacen.tp_ope')
            ->select('tp_ope.id_operacion','tp_ope.cod_sunat','tp_ope.descripcion')
            ->where('tp_ope.estado', 1)
            ->whereIn('tp_ope.tipo',[1,3])
            ->get();
        return $data;
    }
    public function tp_operacion_cbo_sal(){
        $data = DB::table('almacen.tp_ope')
            ->select('tp_ope.id_operacion','tp_ope.cod_sunat','tp_ope.descripcion')
            ->where('tp_ope.estado', 1)
            ->whereIn('tp_ope.tipo',[2,3])
            ->get();
        return $data;
    }
    public function tp_doc_almacen_cbo_ing(){
        $data = DB::table('almacen.tp_doc_almacen')
            ->select('tp_doc_almacen.id_tp_doc_almacen','tp_doc_almacen.descripcion')
            ->where([['tp_doc_almacen.estado', '=', 1],
                    ['tp_doc_almacen.tipo', '=', 1]])
            ->get();
        return $data;
    }
    public function tp_doc_almacen_cbo_sal(){
        $data = DB::table('almacen.tp_doc_almacen')
            ->select('tp_doc_almacen.id_tp_doc_almacen','tp_doc_almacen.descripcion')
            ->where([['tp_doc_almacen.estado', '=', 1],
                    ['tp_doc_almacen.tipo', '=', 2]])
            ->get();
        return $data;
    }
    public function mostrar_impuestos_cbo(){
        $data = DB::table('contabilidad.cont_impuesto')
            ->select('cont_impuesto.id_impuesto','cont_impuesto.descripcion',
            'cont_impuesto.porcentaje')
            ->where('cont_impuesto.estado', '=', 1)
            ->get();
        return $data;
    }
    public function select_empresa(){
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')->get();
        return $data;
    }
    public function mostrar_proyecto_cbo(){
        $data = DB::table('proyectos.proy_contrato')
            ->select('proy_proyecto.id_proyecto','proy_proyecto.descripcion')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->where('proy_contrato.estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_area_cbo(){
        $data = DB::table('administracion.adm_area')
            ->select('adm_area.id_area',DB::raw("CONCAT(adm_grupo.descripcion,' - ',adm_area.descripcion) as area_descripcion"))
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','adm_area.id_grupo')
            ->where('adm_area.estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_trabajadores_cbo(){
        $data = DB::table('rrhh.rrhh_trab')
            ->select('rrhh_trab.id_trabajador',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('rrhh_trab.estado', '=', 1)
            ->get();
        return $data;
    }
    public function select_usuarios(){
        $data = DB::table('configuracion.sis_usua')
            ->select('sis_usua.id_usuario','rrhh_trab.id_trabajador',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('sis_usua.estado', '=', 1)
            ->get();
        return $data;
    }
    public function select_almaceneros(){
        $data = DB::table('rrhh.rrhh_rol')
            ->select('sis_usua.id_usuario','rrhh_rol.id_trabajador',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','rrhh_rol.id_trabajador')
            ->join('configuracion.sis_usua','sis_usua.id_trabajador','=','rrhh_rol.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where([['sis_usua.estado', '=', 1],['rrhh_rol.id_area','=',8]])//Area = Almacen
            ->get();
        return $data;
    }
    public function mostrar_equipo_cbo(){
        $data = DB::table('logistica.equipo')
            ->select('equipo.id_equipo','equipo.codigo','equipo.descripcion')
            ->where('estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_unid_program_cbo(){
        $data = DB::table('proyectos.proy_unid_program')
            ->select('proy_unid_program.id_unid_program','proy_unid_program.descripcion')
            ->where('estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_tp_combustible_cbo(){
        $data = DB::table('logistica.tp_combustible')
            ->select('tp_combustible.id_tp_combustible','tp_combustible.descripcion')
            ->where('estado', '=', 1)
                ->orderBy('tp_combustible.codigo','asc')->get();
        return $data;
    }
    public function mostrar_tp_seguro_cbo(){
        $data = DB::table('logistica.equi_tp_seguro')
            ->select('equi_tp_seguro.id_tp_seguro','equi_tp_seguro.descripcion')
            ->where('estado', '=', 1)
            ->get();
        return $data;
    }
    public function mostrar_tipos_cbo(){
        $data = DB::table('almacen.alm_tp_prod')
            ->select('alm_tp_prod.id_tipo_producto','alm_tp_prod.descripcion')
            ->where('estado', '=', 1)
                ->orderBy('alm_tp_prod.id_tipo_producto','asc')->get();
        return $data;
    }
    public function mostrar_clasificaciones_cbo(){
        $data = DB::table('almacen.alm_clasif')
            ->select('alm_clasif.id_clasificacion','alm_clasif.descripcion')
            ->where([['alm_clasif.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_unidades_cbo(){
        $data = DB::table('almacen.alm_und_medida')
            ->select('alm_und_medida.id_unidad_medida','alm_und_medida.descripcion',
                'alm_und_medida.abreviatura')
            ->where([['alm_und_medida.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_unidades(){
        $data = DB::table('almacen.alm_und_medida')
            ->select('alm_und_medida.*')
            ->where([['alm_und_medida.estado', '=', 1]])
                ->orderBy('id_unidad_medida')
                ->get();
        return response()->json($data); 
    }
    public function mostrar_tp_servicios_cbo(){
        $data = DB::table('logistica.log_tp_servi')
            ->select('log_tp_servi.id_tipo_servicio','log_tp_servi.descripcion')
            ->where([['log_tp_servi.estado', '=', 1]])
                ->orderBy('id_tipo_servicio')
                ->get();
        return $data;
    }
    public function mostrar_tp_almacen_cbo(){
        $data = DB::table('almacen.alm_tp_almacen')
            ->select('alm_tp_almacen.id_tipo_almacen','alm_tp_almacen.descripcion')
            ->where([['alm_tp_almacen.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_sedes_cbo(){
        $data = DB::table('administracion.sis_sede')
            ->select('sis_sede.*')
            ->where([['sis_sede.estado', '=', 1]])
                ->orderBy('id_sede')
                ->get();
        return $data;
    }
    public function mostrar_almacenes_cbo(){
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.descripcion')
            ->where([['alm_almacen.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function select_almacenes_empresa($id_empresa){
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.id_almacen','alm_almacen.descripcion')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->where([['alm_almacen.estado', '=', 1],
                     ['adm_empresa.id_empresa', '=', $id_empresa]])
                ->orderBy('alm_almacen.descripcion')
                ->get();
        return $data;
    }
    public function mostrar_estantes_cbo(){
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.id_estante','alm_ubi_estante.codigo')
            ->where([['alm_ubi_estante.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function mostrar_niveles_cbo()
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.id_nivel','alm_ubi_nivel.codigo')
            ->where([['alm_ubi_nivel.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function mostrar_posiciones_cbo()
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.id_posicion','alm_ubi_posicion.codigo')
            ->where([['alm_ubi_posicion.estado', '=', 1]])
                ->orderBy('codigo')
                ->get();
        return $data;
    }
    public function mostrar_ubicaciones_cbo()
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.id_prod_ubi','alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.estado', '=', 1]])
                ->orderBy('cod_posicion')
                ->get();
        return $data;
    }
    public function mostrar_detracciones_cbo()
    {
        $data = DB::table('contabilidad.cont_detra_det')
            ->select('cont_detra_det.id_detra_det','cont_detra.cod_sunat','cont_detra_det.porcentaje','cont_detra.descripcion')
            ->join('contabilidad.cont_detra','cont_detra.id_cont_detra','=','cont_detra_det.id_detra')
            ->where([['cont_detra_det.estado', '=', 1]])
                ->orderBy('cont_detra.descripcion')
                ->get();
        return $data;
    }
    public function mostrar_proveedores_cbo()
    {
        $data = DB::table('logistica.log_prove')
            ->select('log_prove.id_proveedor','adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['log_prove.estado', '=', 1]])
                ->orderBy('adm_contri.nro_documento')
                ->get();
        return $data;
    }
    public function mostrar_motivos_cbo()
    {
        $data = DB::table('almacen.guia_motivo')
            ->select('guia_motivo.id_motivo','guia_motivo.descripcion')
            ->where([['guia_motivo.estado', '=', 1]])
            ->orderBy('guia_motivo.id_motivo')
            ->get();
        return $data;
    }
    public function mostrar_guia_clas_cbo()
    {
        $data = DB::table('almacen.guia_clas')
            ->select('guia_clas.id_clasificacion','guia_clas.descripcion')
            ->where([['guia_clas.estado', '=', 1]])
            ->orderBy('guia_clas.id_clasificacion')
            ->get();
        return $data;
    }
    public function mostrar_condiciones_cbo()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago','log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado',1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }
    public function mostrar_tp_doc_cbo()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.id_tp_doc','cont_tp_doc.cod_sunat','cont_tp_doc.descripcion')
            ->where([['cont_tp_doc.estado', '=', 1]])
            ->orderBy('cont_tp_doc.id_tp_doc')
            ->get();
        return $data;
    }
    public function mostrar_moneda_cbo()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda','sis_moneda.simbolo','sis_moneda.descripcion')
            ->where([['sis_moneda.estado', '=', 1]])
            ->orderBy('sis_moneda.id_moneda')
            ->get();
        return $data;
    }
    public function mostrar_equi_tipos_cbo(){
        $data = DB::table('logistica.equi_tipo')
            ->select('equi_tipo.id_tipo','equi_tipo.codigo','equi_tipo.descripcion')
            ->where([['estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_equi_cats_cbo(){
        $data = DB::table('logistica.equi_cat')
            ->select('equi_cat.id_categoria','equi_cat.codigo','equi_cat.descripcion')
            ->where([['estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_propietarios_cbo(){
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa','adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->where([['adm_empresa.estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_clientes()
    {
        $data = DB::table('comercial.com_cliente')
            ->select('com_cliente.id_cliente','com_cliente.id_contribuyente',
                'adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where([['com_cliente.estado', '=', 1]])
                ->orderBy('adm_contri.nro_documento')
                ->get();
        $output['data'] = $data;
        return $output;
    }

    //Tipo de Producto
    public function mostrar_tp_productos(){
        $data = DB::table('almacen.alm_tp_prod')
            ->select('alm_tp_prod.*')
            ->where([['alm_tp_prod.estado', '=', 1]])
                ->orderBy('id_tipo_producto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    
    public function mostrar_tp_producto($id){
        $data = DB::table('almacen.alm_tp_prod')
            ->where([['alm_tp_prod.id_tipo_producto', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_tp_producto(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_tipo_producto = DB::table('almacen.alm_tp_prod')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
                'fecha_registro' => $fecha
            ],
                'id_tipo_producto'
            );
        return response()->json($id_tipo_producto);
    }
    public function update_tp_producto(Request $request)
    {
        $data = DB::table('almacen.alm_tp_prod')
        ->where('id_tipo_producto',$request->id_tipo_producto)
        ->update([
            'descripcion' => $request->descripcion,
            'estado' => $request->estado
        ]);
        return response()->json($data);
    }
    public function anular_tp_producto(Request $request,$id){
        $data = DB::table('almacen.alm_tp_prod')
        ->where('id_tipo_producto',$id)
        ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }
    public function tipo_revisar_relacion($id){
        $data = DB::table('almacen.alm_cat_prod')
        ->where([['id_tipo_producto','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }

    //Categorias
    public function mostrar_categorias(){
        $data = DB::table('almacen.alm_cat_prod')
            ->select('alm_cat_prod.*', 'alm_tp_prod.descripcion as tipo_descripcion')
            ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->where([['alm_cat_prod.estado', '=', 1]])
                ->orderBy('id_categoria')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_categoria($id){
        $data = DB::table('almacen.alm_cat_prod')
        ->select('alm_cat_prod.*', 'alm_tp_prod.descripcion as tipo_descripcion',
                 'alm_tp_prod.id_tipo_producto')
        ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->where([['alm_cat_prod.id_categoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function categoria_nextId($id_tipo_producto){
        $cantidad = DB::table('almacen.alm_cat_prod')
        ->where('id_tipo_producto',$id_tipo_producto)
        ->get()->count();
        $val = $this->leftZero(3,$cantidad);
        $nextId = "".$id_tipo_producto."".$val;
        return $nextId;
    }
    public function guardar_categoria(Request $request){
        $codigo = $this->categoria_nextId($request->id_tipo_producto);
        $fecha = date('Y-m-d H:i:s');
        $id_categoria = DB::table('almacen.alm_cat_prod')->insertGetId(
            [
                'codigo' => $codigo,
                'id_tipo_producto' => $request->id_tipo_producto,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
                'fecha_registro' => $fecha
            ],
                'id_categoria'
            );
        return response()->json($id_categoria);
    }
    public function update_categoria(Request $request)
    {
        // $codigo = $this->categoria_nextId($request->id_tipo_producto);
        $id_categoria = DB::table('almacen.alm_cat_prod')
        ->where('id_categoria',$request->id_categoria)
        ->update([
                // 'codigo' => $codigo,
                'id_tipo_producto' => $request->id_tipo_producto,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado
            ]);
        return response()->json($id_categoria);
    }
    public function anular_categoria(Request $request,$id){
        $id_categoria = DB::table('almacen.alm_cat_prod')
        ->where('id_categoria',$id)
        ->update([ 'estado' => 2 ]);
        return response()->json($id_categoria);
    }
    public function cat_revisar($id){
        $data = DB::table('almacen.alm_subcategoria')
        ->where([['id_categoria','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }
    //SubCategorias
    public function mostrar_sub_categorias(){
        $data = DB::table('almacen.alm_subcategoria')
        ->select('alm_subcategoria.*', 'alm_cat_prod.descripcion as cat_descripcion',
                'alm_tp_prod.descripcion as tipo_descripcion')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_subcategoria.id_categoria')
        ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
        ->where('alm_subcategoria.estado',1)
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_sub_categoria($id){
        $data = DB::table('almacen.alm_subcategoria')
        ->select('alm_subcategoria.*', 'alm_cat_prod.descripcion as cat_descripcion',
                'alm_cat_prod.id_categoria','alm_tp_prod.id_tipo_producto',
                'alm_tp_prod.descripcion as tipo_descripcion')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_subcategoria.id_categoria')
        ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->where([['alm_subcategoria.id_subcategoria', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function subcategoria_nextId($id_categoria){
        $cantidad = DB::table('almacen.alm_subcategoria')
        ->where('id_categoria',$id_categoria)
        ->get()->count();
        $val = $this->leftZero(2,$cantidad);
        $cat = DB::table('almacen.alm_cat_prod')->select('codigo')
                ->where('id_categoria',$id_categoria)->first();
        $nextId = "".$cat->codigo."".$val;
        return $nextId;
    }
    public function guardar_sub_categoria(Request $request){
        $codigo = $this->subcategoria_nextId($request->id_categoria);
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.alm_subcategoria')->insertGetId(
            [
                'codigo' => $codigo,
                'id_categoria' => $request->id_categoria,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
                'fecha_registro' => $fecha
            ],
                'id_subcategoria'
            );
        return response()->json($data);
    }
    public function update_sub_categoria(Request $request)
    {
        $id_sub_cat = DB::table('almacen.alm_subcategoria')
        ->where('id_subcategoria',$request->id_subcategoria)
        ->update([
                'id_categoria' => $request->id_categoria,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado
            ]);
        return response()->json($id_sub_cat);
    }
    public function anular_sub_categoria(Request $request,$id){
        $id_sub_cat = DB::table('almacen.alm_subcategoria')
        ->where('id_subcategoria',$id)
        ->update([ 'estado' => 2 ]);
        return response()->json($id_sub_cat);
    }
    public function subcat_revisar($id){
        $data = DB::table('almacen.alm_prod')
        ->where([['id_subcategoria','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }
    //Clasificaciones
    public function mostrar_clasificaciones(){
        $data = DB::table('almacen.alm_clasif')
            ->select('alm_clasif.*')
            ->where([['alm_clasif.estado', '=', 1]])
                ->orderBy('id_clasificacion')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_clasificacion($id){
        $data = DB::table('almacen.alm_clasif')
            ->where([['alm_clasif.id_clasificacion', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_clasificacion(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_clasificacion = DB::table('almacen.alm_clasif')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
                'fecha_registro' => $fecha
            ],
                'id_clasificacion'
            );
        return response()->json($id_clasificacion);
    }
    public function update_clasificacion(Request $request)
    {
        $data = DB::table('almacen.alm_clasif')
            ->where('id_clasificacion',$request->id_clasificacion)
            ->update([
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
                // 'fecha_registro' => $request->fecha_registro
            ]);
        return response()->json($data);
    }
    public function anular_clasificacion(Request $request,$id){
        $data = DB::table('almacen.alm_clasif')
            ->where('id_clasificacion',$id)
            ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }
    public function clas_revisar($id){
        $data = DB::table('almacen.alm_prod')
        ->where([['id_clasif','=',$id],
                ['estado','=',1]])
        ->get()->count();
        return response()->json($data);
    }
    //Productos
    public function mostrar_prods(){
        $prod = DB::table('almacen.alm_prod')
            ->select('alm_prod.id_producto', 'alm_prod.codigo', 'alm_prod.descripcion',
            'alm_prod.id_unidad_medida','alm_prod_antiguo.cod_antiguo')
            ->join('almacen.alm_prod_antiguo','alm_prod_antiguo.id_producto','=','alm_prod.id_producto')
            ->get();
        $output['data'] = $prod;
        return response()->json($output);
    }
    public function mostrar_productos(){
        $data = DB::table('almacen.alm_prod')
            ->select('alm_prod.id_producto', 'alm_prod.codigo', 'alm_prod.descripcion',
            'alm_subcategoria.codigo as cod_sub_cat',    
            'alm_subcategoria.descripcion as subcat_descripcion',
                'alm_cat_prod.codigo as cod_cat', 'alm_cat_prod.descripcion as cat_descripcion',
                'alm_tp_prod.id_tipo_producto', 'alm_tp_prod.descripcion as tipo_descripcion',
                'alm_clasif.id_clasificacion',
                'alm_clasif.descripcion as clasif_descripcion')
            ->join('almacen.alm_subcategoria','alm_subcategoria.id_subcategoria','=','alm_prod.id_subcategoria')
            ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_subcategoria.id_categoria')
            ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->join('almacen.alm_clasif','alm_clasif.id_clasificacion','=','alm_prod.id_clasif')
            ->get();
            $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_producto($id){
        $producto = DB::table('almacen.alm_prod')
        ->select('alm_prod.*', 'alm_subcategoria.descripcion as subcat_descripcion',
                'alm_cat_prod.descripcion as cat_descripcion',
                'alm_tp_prod.descripcion as tipo_descripcion')
        ->join('almacen.alm_subcategoria','alm_subcategoria.id_subcategoria','=','alm_prod.id_subcategoria')
        ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_subcategoria.id_categoria')
        ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
        ->where([['alm_prod.id_producto', '=', $id]])
            ->get();
        
        $antiguos = DB::table('almacen.alm_prod_antiguo')
        ->where([['alm_prod_antiguo.id_producto', '=', $id]])
        ->orderBy('cod_antiguo')->get();

        $data = ["producto"=>$producto,"antiguos"=>$antiguos];

        return response()->json($data);
    }

    public function next_correlativo_prod($id_subcategoria, $id_clasif)
    {
        $cantidad = DB::table('almacen.alm_prod')
            ->where([['id_subcategoria', '=', $id_subcategoria],
                    ['id_clasif','=',$id_clasif]])
            ->get()->count();
        $subcat = DB::table('almacen.alm_subcategoria')->select('codigo')
            ->where('id_subcategoria', $id_subcategoria)->first();
        $clasif = $this->leftZero(2,$id_clasif);
        $prod = $this->leftZero(4,$cantidad+1);
        $nextId = $subcat->codigo."".$clasif."".$prod;
        return $nextId;
    }

    public function guardar_imagen(Request $request)
    {
        $update = false;
        $namefile = "";
        if ($request->codigo !== "" && $request->codigo !== null){
            $nfile = $request->file('imagen');
            if (isset($nfile)){
                $namefile = $request->codigo.'.'.$nfile->getClientOriginalExtension();
                \File::delete(public_path('productos/'.$namefile));
                // if (file_exists(public_path('productos/'+$namefile))){
                //     unlink(public_path('productos/'+$namefile));
                // }else{
                //     dd('El archivo no existe.');
                // }
                Storage::disk('archivos')->put('productos/'.$namefile, \File::get($nfile));
            } else {
                $namefile = null;
            }
            $update = DB::table('almacen.alm_prod')
            ->where('id_producto', $request->id_producto)
            ->update(['imagen' => $namefile]);    
        }

        if ($update){
            $status = 1;
        } else {
            $status = 0;
        }
        $array = array("status"=>$status, "imagen"=>$namefile);
        return response()->json($array);
    }
    
    public function guardar_producto(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_correlativo_prod($request->id_subcategoria, $request->id_clasif);

        $id_producto = DB::table('almacen.alm_prod')->insertGetId(
            [
                'codigo' => $codigo,
                'codigo_anexo' => $request->codigo_anexo,
                'codigo_proveedor' => $request->codigo_proveedor,
                'id_clasif' => $request->id_clasif,
                'id_subcategoria' => $request->id_subcategoria,
                'descripcion' => $request->descripcion,
                'id_unidad_medida' => $request->id_unidad_medida,
                'id_unid_equi' => $request->id_unid_equi,
                'cant_pres' => $request->cant_pres,
                'series' => ($request->series == '1')?true:false,
                'afecto_igv' => ($request->afecto_igv == '1')?true:false,
                'estado' => $request->estado,
                'fecha_registro' => $fecha
                // 'precio_unitario' => $request->precio_unitario,
                // 'fecha_vencimiento' => $request->fecha_vencimiento,
                // 'consumible' => $request->consumible,
                // 'imagen' => $namefile,
            ],
                'id_producto'
            );
        
        $id_item = DB::table('almacen.alm_item')->insertGetId(
            [   'id_producto' => $id_producto,
                'codigo' => $codigo,
                'fecha_registro' => $fecha
            ],  'id_item');
        
        return response()->json($id_producto);
    }

    public function update_producto(Request $request)
    {
        $data = DB::table('almacen.alm_prod')
            ->where('id_producto', $request->id_producto)
            ->update([
                'codigo' => $request->codigo,
                'codigo_anexo' => $request->codigo_anexo,
                'codigo_proveedor' => $request->codigo_proveedor,
                'id_subcategoria' => $request->id_subcategoria,
                'id_clasif' => $request->id_clasif,
                'descripcion' => $request->descripcion,
                'id_unidad_medida' => $request->id_unidad_medida,
                'id_unid_equi' => $request->id_unid_equi,
                'cant_pres' => $request->cant_pres,
                'series' => ($request->series == '1')?true:false,
                'afecto_igv' => ($request->afecto_igv == '1')?true:false,
                'estado' => $request->estado,
                'fecha_registro' => $request->fecha_registro
                // 'precio_unitario' => $request->precio_unitario,
                // 'fecha_vencimiento' => $request->fecha_vencimiento,
                // 'consumible' => $request->consumible,
                // 'imagen' => $request->imagen,
            ]);
        return response()->json($data);
    }

    public function anular_producto(Request $request,$id){
        $data = DB::table('almacen.alm_prod')
            ->where('id_producto',$id)
            ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }


    //Tipo de Servicio
    public function mostrar_tp_servicios(){
        $data = DB::table('logistica.log_tp_servi')
            ->select('log_tp_servi.*')
            ->where([['log_tp_servi.estado', '=', 1]])
                ->orderBy('id_tipo_servicio')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_tp_servicio($id){
        $data = DB::table('logistica.log_tp_servi')
            ->select('log_tp_servi.*')
            ->where([['log_tp_servi.id_tipo_servicio', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_tp_servicio(Request $request)
    {
        $data = DB::table('logistica.log_tp_servi')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'estado' => $request->estado
            ],
                'id_tipo_servicio'
            );
        return response()->json($data);
    }
    public function update_tp_servicio(Request $request)
    {
        $data = DB::table('logistica.log_tp_servi')
            ->where('id_tipo_servicio', $request->id_tipo_servicio)
            ->update([
                'descripcion' => $request->descripcion,
                'estado' => $request->estado
            ]);
        return response()->json($data);
    }
    public function anular_tp_servicio(Request $request, $id)
    {
        $data = DB::table('logistica.log_tp_servi')
            ->where('id_tipo_servicio', $id)
            ->update([
                'descripcion' => $request->descripcion,
                'estado' => 2
            ]);
        return response()->json($data);
    }

    //Categoria Servicios
    public function mostrar_cat_servicios(){
        $data = DB::table('logistica.log_cat_serv')
            ->select('log_cat_serv.*','log_tp_servi.descripcion as tipo_descripcion')
            ->join('logistica.log_tp_servi','log_tp_servi.id_tipo_servicio','=','log_cat_serv.id_tipo_servicio')
                ->where([['log_cat_serv.estado', '=', 1]])
                ->orderBy('id_categoria')
                ->get();
        return response()->json($data);
    }
    public function mostrar_cat_servicio($id){
        $data = DB::table('logistica.log_cat_serv')
            ->select('log_cat_serv.*','log_tp_servi.descripcion as tipo_descripcion',
                     'log_tp_servi.id_tipo_servicio')
            ->join('logistica.log_tp_servi','log_tp_servi.id_tipo_servicio','=','log_cat_serv.id_tipo_servicio')
                ->where([['log_cat_serv.id_categoria', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_cat_servicio(Request $request){
        $data = DB::table('logistica.log_cat_serv')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'id_tipo_servicio' => $request->id_tipo_servicio,
                'estado' => $request->estado,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_categoria'
            );
        return response()->json($data);
    }
    public function update_cat_servicio(Request $request, $id){
        $data = DB::table('logistica.log_cat_serv')->where('id_categoria', $id)
            ->update([
                'descripcion' => $request->descripcion,
                'id_tipo_servicio' => $request->id_tipo_servicio,
                'estado' => $request->estado
            ]);
        return response()->json($data);
    }
    public function update_cat_servicio_anular(Request $request, $id){
        $data = DB::table('logistica.log_cat_serv')->where('id_categoria', $id)
            ->update([
                'estado' => 2
            ]);
        return response()->json($data);
    }

    //Catalogo de Servicios
    public function mostrar_servicios(){
        $data = DB::table('logistica.log_servi')
            ->orderBy('codigo')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_servicio($id){
        $data = DB::table('logistica.log_servi')
        ->select('log_servi.*','log_tp_servi.id_tipo_servicio',
                 'log_tp_servi.descripcion as tipo_descripcion')
        // ->join('logistica.log_cat_serv','log_cat_serv.id_categoria','=','log_servi.id_cat_servicio')
        ->join('logistica.log_tp_servi','log_servi.id_tipo_servicio','=','log_tp_servi.id_tipo_servicio')
            ->where([['log_servi.id_servicio', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function next_correlativo_ser($id_tipo_servicio){
        $cantidad = DB::table('logistica.log_servi')
            ->where([['log_servi.id_tipo_servicio', '=', $id_tipo_servicio]])
            ->get()->count();
        $tipo = $this->leftZero(2,$id_tipo_servicio);
        $serv = $this->leftZero(3,$cantidad+1);
        $nextId = $tipo."".$serv;
        return $nextId;
    }

    public function guardar_servicio(Request $request){
        $codigo = $this->next_correlativo_ser($request->id_tipo_servicio);
        $fecha = date('Y-m-d H:i:s');

        $id_servicio = DB::table('logistica.log_servi')->insertGetId(
            [
                'codigo' => $codigo,
                'descripcion' => $request->descripcion,
                'id_tipo_servicio' => $request->id_tipo_servicio,
                'estado' => $request->estado,
                'fecha_registro' => $fecha
            ],
                'id_servicio'
            );

        $id_item = DB::table('almacen.alm_item')->insertGetId(
            [
                'id_servicio' => $id_servicio,
                'codigo' => $codigo,
                'fecha_registro' => $fecha
            ],
                'id_item'
            );
            
        return response()->json($id_servicio);
    }
    
    public function update_servicio(Request $request){
        $data = DB::table('logistica.log_servi')
            ->where('id_servicio', $request->id_servicio)
            ->update([
                // 'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'id_tipo_servicio' => $request->id_tipo_servicio,
                'estado' => $request->estado
            ]);
        return response()->json($data);
    }

    public function anular_servicio(Request $request, $id){
        $data = DB::table('logistica.log_servi')
            ->where('id_servicio', $id)
            ->update(['estado' => 2]);
        return response()->json($data);
    }

    /*Almacen*/
    public function mostrar_almacenes()
    {
        $data = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.*', 'sis_sede.descripcion as sede_descripcion',
            'alm_tp_almacen.descripcion as tp_almacen')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('almacen.alm_tp_almacen','alm_tp_almacen.id_tipo_almacen','=','alm_almacen.id_tipo_almacen')
            ->where([['alm_almacen.estado', '=', 1]])
                ->orderBy('id_almacen')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_almacen($id)
    {
        $data = DB::table('almacen.alm_almacen')
        ->select('alm_almacen.*', 'sis_sede.descripcion as sede_descripcion')
        ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
        ->where([['alm_almacen.id_almacen', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_almacen(Request $request)
    {
        $id_almacen = DB::table('almacen.alm_almacen')->insertGetId(
            [
                'id_sede' => $request->id_sede,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'id_tipo_almacen' => $request->id_tipo_almacen,
                'estado' => $request->estado
            ],
                'id_almacen'
            );
        return response()->json($id_almacen);
    }

    public function update_almacen(Request $request)
    {
        $data = DB::table('almacen.alm_almacen')->where('id_almacen', $request->id_almacen)
            ->update([
                'id_sede' => $request->id_sede,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'id_tipo_almacen' => $request->id_tipo_almacen
            ]);
        return response()->json($data);
    }

    public function anular_almacen(Request $request, $id)
    {
        $data = DB::table('almacen.alm_almacen')->where('id_almacen', $id)
            ->update([
                'estado' => 2
            ]);
        return response()->json($data);
    }
    /* Estante */
    public function mostrar_estantes()
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*','alm_almacen.id_almacen',
            'alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_estantes_almacen($id)
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*', 'alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_estante.id_almacen', '=', $id]])
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_estante($id)
    {
        $data = DB::table('almacen.alm_ubi_estante')
            ->select('alm_ubi_estante.*')
            ->where([['alm_ubi_estante.id_estante', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_estante(Request $request){
        $id_almacen = DB::table('almacen.alm_ubi_estante')->insertGetId(
            [
                'id_almacen' => $request->id_almacen,
                'codigo' => $request->codigo,
                'estado' => 1
            ],
                'id_estante'
            );
        return response()->json($id_almacen);
    }
    public function guardar_estantes(Request $request){
        $id_almacen = $request->id_almacen;
        $desde = $request->desde;
        $hasta = $request->hasta;

        for ($i=$desde; $i<=$hasta; $i++) { 
            $codigo = $this->leftZero(2,$id_almacen)."-".$this->leftZero(2,$i);

            $exist = DB::table('almacen.alm_ubi_estante')
                ->where('codigo',$codigo)->get()->count();
            
            if ($exist === 0){
                $data = DB::table('almacen.alm_ubi_estante')->insertGetId([
                    'id_almacen' => $id_almacen,
                    'codigo' => $codigo,
                    'estado' => 1
                ],
                    'id_estante'
                );
            }
        }
        return response()->json($data);
    }
    public function update_estante(Request $request){
        $data = DB::table('almacen.alm_ubi_estante')
            ->where([['alm_ubi_estante.id_estante','=',$request->id_estante]])
            ->update([
                'id_almacen' => $request->id_almacen,
                'codigo' => $request->codigo
            ]);
        return response()->json($data);
    }
    public function anular_estante(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_estante')
            ->where([['alm_ubi_estante.id_estante','=',$id]])
            ->update(['estado' => 2]);
        return response()->json($data);
    }
    public function revisar_estante($id){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_estante','=',$id],
                    ['estado','=', 1]])
            ->get()->count();
        return response()->json($data);
    }
/* Nivel */
    public function mostrar_niveles()
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*','alm_almacen.id_almacen',
            'alm_almacen.descripcion as alm_descripcion',
            'alm_ubi_estante.id_estante','alm_ubi_estante.codigo as cod_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_niveles_estante($id)
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_nivel.id_estante', '=', $id]])
            ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_nivel($id)
    {
        $data = DB::table('almacen.alm_ubi_nivel')
            ->select('alm_ubi_nivel.*','alm_almacen.id_almacen',
            'alm_ubi_estante.id_estante')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_nivel.id_nivel', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_nivel(Request $request){
        $id_almacen = DB::table('almacen.alm_ubi_nivel')->insertGetId(
            [
                'id_estante' => $request->id_estante,
                'codigo' => $request->codigo,
                'estado' => 1
            ],
                'id_nivel'
            );
        return response()->json($id_almacen);
    }
    public function guardar_niveles(Request $request){
        $abc = [0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z'];
        $desde = array_search($request->desde,$abc);
        $hasta = array_search($request->hasta,$abc);
        $i = 0;
        for ($i=$desde; $i<=$hasta; $i++) {
            $codigo = $request->cod_estante."-".$abc[$i];

            $exist = DB::table('almacen.alm_ubi_nivel')
                ->where('codigo',$codigo)->get()->count();
            
            if ($exist === 0){
                $data = DB::table('almacen.alm_ubi_nivel')->insertGetId([
                    'id_estante' => $request->id_estante,
                    'codigo' => $codigo,
                    'estado' => 1
                ],
                    'id_nivel'
                );
            }
        }
        return response()->json($data);
    }
    public function update_nivel(Request $request){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_nivel','=',$request->id_nivel]])
            ->update([
                'id_estante' => $request->id_estante,
                'codigo' => $request->codigo
            ]);
        return response()->json($data);
    }
    public function anular_nivel(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_nivel')
            ->where([['alm_ubi_nivel.id_nivel','=',$id]])
            ->update(['estado' => 2]);
        return response()->json($data);
    }
    public function revisar_nivel($id){
        $data = DB::table('almacen.alm_ubi_posicion')
            ->where([['alm_ubi_posicion.id_nivel','=',$id],
                    ['estado','=', 1]])
            ->get()->count();
        return response()->json($data);
    }
    /* Posicion */
    public function mostrar_posiciones()
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante','alm_ubi_nivel.codigo as cod_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_posiciones_nivel($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_estante.codigo as cod_estante','alm_ubi_nivel.codigo as cod_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_nivel', '=', $id]])
            ->orderBy('codigo')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_posicion($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_ubi_posicion.*','alm_almacen.id_almacen',
            'alm_ubi_estante.id_estante','alm_ubi_nivel.id_nivel')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_posicion', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_posicion(Request $request){
        $id_almacen = DB::table('almacen.alm_ubi_posicion')->insertGetId(
            [
                'id_nivel' => $request->id_nivel,
                'codigo' => $request->codigo,
                'estado' => 1
            ],
                'id_posicion'
            );
        return response()->json($id_almacen);
    }
    public function guardar_posiciones(Request $request){
        $cod_nivel = $request->cod_nivel;
        $desde = $request->desde;
        $hasta = $request->hasta;
        $i = 0;
        for ($i=$desde; $i<=$hasta; $i++) {
            $codigo = $cod_nivel."-".$this->leftZero(2,$i);

            $exist = DB::table('almacen.alm_ubi_posicion')
                ->where('codigo',$codigo)->get()->count();
            
            if ($exist === 0){
                $data = DB::table('almacen.alm_ubi_posicion')->insertGetId([
                    'id_nivel' => $request->id_nivel,
                    'codigo' => $codigo,
                    'estado' => 1
                ],
                    'id_posicion'
                );
            }
        }
        return response()->json($data);
    }

    public function anular_posicion(Request $request, $id){
        $data = DB::table('almacen.alm_ubi_posicion')
            ->where([['alm_ubi_posicion.id_posicion','=',$id]])
            ->update(['estado' => 2]);
        return response()->json($data);
    }
    
    public function almacen_posicion($id)
    {
        $data = DB::table('almacen.alm_ubi_posicion')
            ->select('alm_almacen.descripcion as alm_descripcion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_ubi_posicion.id_posicion', '=', $id]])
                ->get();
        return response()->json($data);
    }
    /** Producto Ubicacion */
    public function mostrar_ubicaciones_producto($id)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*', 'alm_almacen.descripcion as alm_descripcion',
                'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_producto', '=', $id]])
            ->orderBy('cod_posicion')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_ubicacion($id)
    {
        $data = DB::table('almacen.alm_prod_ubi')
            ->select('alm_prod_ubi.*','alm_almacen.descripcion as alm_descripcion')
                // 'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
            ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
            ->where([['alm_prod_ubi.id_prod_ubi', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_ubicacion(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_almacen = DB::table('almacen.alm_prod_ubi')->insertGetId(
            [
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'stock' => $request->stock,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_prod_ubi'
            );
        return response()->json($id_almacen);
    }
    public function update_ubicacion(Request $request){
        $data = DB::table('almacen.alm_prod_ubi')
            ->where('id_prod_ubi', $request->id_prod_ubi)
            ->update([
                'id_posicion' => $request->id_posicion,
                'stock' => $request->stock
            ]);
        return response()->json($data);
    }
    public function anular_ubicacion(Request $request, $id){
        $data = DB::table('almacen.alm_prod_ubi')
            ->where([['alm_prod_ubi.id_prod_ubi','=',$id]])
            ->update(['estado' => 2]);
        return response()->json($data);
    }
    /**ProductoUbicacion Series */
    public function listar_series_producto($id)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select('alm_prod_serie.*', 'alm_almacen.descripcion as alm_descripcion',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia_com"),
            DB::raw("CONCAT('GR-',guia_ven.serie,'-',guia_ven.numero) as guia_ven"))
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_prod_serie.id_almacen')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','alm_prod_serie.id_guia_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','alm_prod_serie.id_guia_ven_det')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->where([['alm_prod_serie.id_prod', '=', $id]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_serie($id)
    {
        $data = DB::table('almacen.alm_prod_serie')
            ->select('alm_prod_serie.*')
            ->where([['alm_prod_serie.id_prod_serie', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_serie(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_almacen = DB::table('almacen.alm_prod_serie')->insertGetId(
            [
                'id_prod' => $request->id_prod,
                'id_almacen' => $request->id_almacen,
                'serie' => $request->serie,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_prod_serie'
            );
        return response()->json($id_almacen);
    }
    public function update_serie(Request $request){
        $data = DB::table('almacen.alm_prod_serie')
            ->where('id_prod_serie', $request->id_prod_serie)
            ->update([
                'id_prod' => $request->id_prod,
                'serie' => $request->serie
            ]);
        return response()->json($data);
    }
    public function anular_serie(Request $request, $id){
        $data = DB::table('almacen.alm_prod_serie')
            ->where([['alm_prod_serie.id_prod_serie','=',$id]])
            ->update(['estado' => 2]);
        return response()->json($data);
    }

    /* Tipo Almacen */
    public function mostrar_tipo_almacen(){
        $data = DB::table('almacen.alm_tp_almacen')->orderBy('id_tipo_almacen')->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tipo_almacenes($id){
        $data = DB::table('almacen.alm_tp_almacen')->orderBy('id_tipo_almacen')
            ->where([['alm_tp_almacen.id_tipo_almacen', '=', $id]])->get();
        return response()->json($data);
    }

    public function guardar_tipo_almacen(Request $request){
        $id_almacen = DB::table('almacen.alm_tp_almacen')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'estado' => 1
            ],
                'id_tipo_almacen'
            );
        return response()->json($id_almacen);
    }

    public function update_tipo_almacen(Request $request){
        $data = DB::table('almacen.alm_tp_almacen')->where('id_tipo_almacen', $request->id_tipo_almacen)
            ->update([
                'descripcion' => $request->descripcion,
                'estado' => 1
            ]);
        return response()->json($data);
    }
    public function anular_tipo_almacen($id){
        $data = DB::table('almacen.alm_tp_almacen')->where('id_tipo_almacen', $id)
            ->update([
                'estado' => 2
            ]);
        return response()->json($data);
    }

    /* Tipo de Movimiento */
    public function mostrar_tipos_mov()
    {
        $data = DB::table('almacen.tp_ope')
            ->where([['tp_ope.estado', '=', 1]])
                ->orderBy('id_operacion')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tipo_mov($id)
    {
        $data = DB::table('almacen.tp_ope')
        ->where([['tp_ope.id_operacion', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_tipo_mov(Request $request)
    {
        $id_operacion = DB::table('almacen.tp_ope')->insertGetId(
            [
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'cod_sunat' => $request->cod_sunat,
                'estado' => $request->estado,
            ],
                'id_operacion'
            );
        return response()->json($id_operacion);
    }

    public function update_tipo_mov(Request $request)
    {
        $data = DB::table('almacen.tp_ope')
            ->where('id_operacion', $request->id_operacion)
            ->update([
                'tipo' => $request->tipo,
                'cod_sunat' => $request->cod_sunat,
                'descripcion' => $request->descripcion
            ]);
        return response()->json($data);
    }

    public function anular_tipo_mov(Request $request, $id)
    {
        $data = DB::table('almacen.tp_ope')->where('id_operacion', $id)
            ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }

    /* Unidades de Medida */
    public function mostrar_unidades_med()
    {
        $data = DB::table('almacen.alm_und_medida')
            ->where([['alm_und_medida.estado', '=', 1]])
                ->orderBy('id_unidad_medida')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_unid_med($id)
    {
        $data = DB::table('almacen.alm_und_medida')
        ->where([['alm_und_medida.id_unidad_medida', '=', $id]])
            ->get();
        return response()->json($data);
    }

    public function guardar_unid_med(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_unidad_medida = DB::table('almacen.alm_und_medida')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'abreviatura' => $request->abreviatura,
                'estado' => $request->estado,
                // 'fecha_registro' => $fecha,
            ],
                'id_unidad_medida'
            );
        return response()->json($id_unidad_medida);
    }

    public function update_unid_med(Request $request)
    {
        $data = DB::table('almacen.alm_und_medida')
            ->where('id_unidad_medida', $request->id_unidad_medida)
            ->update([
                'abreviatura' => $request->abreviatura,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
            ]);
        return response()->json($data);
    }

    public function anular_unid_med(Request $request, $id)
    {
        $data = DB::table('almacen.alm_und_medida')->where('id_unidad_medida', $id)
            ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }
    
    /**Guia Compra */
    public function mostrar_guias_compra()
    {
        $data = DB::table('almacen.guia_com')
        ->select('guia_com.*','adm_contri.razon_social','adm_estado_doc.estado_doc as des_estado')
        ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->orderBy('id_guia')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_guia_compra($id){
        $guia = DB::table('almacen.guia_com')
        ->select('guia_com.*','adm_estado_doc.estado_doc AS des_estado')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
        ->where([['guia_com.id_guia', '=', $id]])
            ->get();
        return response()->json($guia);
    }
    public function guardar_guia_compra(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_com')->insertGetId(
            [
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                'id_guia_clas' => $request->id_guia_clas,
                'id_operacion' => $request->id_operacion,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'transportista' => $request->transportista,
                'fecha_traslado' => $request->fecha_traslado,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'placa' => $request->placa,
                'usuario' => 3,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_guia'
            );
        // $output['data'] = 'id_guia'
        return response()->json(["id_guia"=>$id_guia,"id_proveedor"=>$request->id_proveedor]);
    }

    public function update_guia_compra(Request $request)
    {
        $data = DB::table('almacen.guia_com')
            ->where('id_guia', $request->id_guia)
            ->update([
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_operacion' => $request->id_operacion,
                'id_guia_clas' => $request->id_guia_clas,
                'id_motivo' => $request->id_motivo,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'transportista' => $request->transportista,
                'fecha_traslado' => $request->fecha_traslado,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'placa' => $request->placa,
                // 'usuario' => 3,
            ]);
        return response()->json($data);
    }

    public function anular_guia_compra(Request $request, $id)
    {
        $data = DB::table('almacen.guia_com')->where('id_guia', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function nextMovimiento($tipo, $fecha, $id_alm){
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $tp = '';
        switch($tipo){
            case 0: $tp = 'Ini';break;
            case 1: $tp = 'Ing';break;
            case 2: $tp = 'Sal';break;
            default:break;
        }

        $data = DB::table('almacen.mov_alm')
        ->where([['id_tp_mov','=',$tipo],
                ['id_almacen','=',$id_alm]])
        ->whereYear('fecha_emision','=',$yyyy)
        // ->whereMonth('fecha_emision','=',$mes)
        ->count();
        
        $correlativo = $this->leftZero(3, $data+1);
        $alm = $this->leftZero(2, $id_alm);
        $codigo = $tp.'-'.$alm.'-'.$anio.$correlativo;

        return $codigo;
    }
    /**Generar Ingreso */
    public function generar_ingreso($id_guia, $id_usuario){
        
        $fecha = date('Y-m-d H:i:s');
        $fecha_emision = date('Y-m-d');
        $guia = DB::table('almacen.guia_com')->where('id_guia',$id_guia)->first();
        
        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','log_valorizacion_cotizacion.precio_cotizado')
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['guia_com_det.id_guia_com','=',$id_guia],
                    ['guia_com_det.estado','=',1]])->get()->toArray();

        $codigo = $this->nextMovimiento(1,
                        $guia->fecha_almacen,
                        $guia->id_almacen);
        
        $id_ingreso = DB::table('almacen.mov_alm')->insertGetId(
            [
                'id_almacen' => $guia->id_almacen,
                'id_tp_mov' => 1,//Ingresos
                'codigo' => $codigo,
                'fecha_emision' => $guia->fecha_almacen,
                'id_guia_com' => $guia->id_guia,
                'revisado' => 0,
                'usuario' => $id_usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_mov_alm'
            );
        // $nuevo_detalle = [];
        $cant = 0;

        // foreach ($detalle as $det){
        //     $exist = false;
        //     foreach ($nuevo_detalle as $nue => $value){
        //         if ($det->id_producto == $value['id_producto']){
        //             $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
        //             $nuevo_detalle[$nue]['valorizacion'] = floatval($value['valorizacion']) + floatval($det->total);
        //             $exist = true;
        //         }
        //     }
        //     if ($exist === false){
        //         $nuevo = [
        //             'id_producto' => $det->id_producto,
        //             'id_posicion' => $det->id_posicion,
        //             'id_oc_det' => (isset($det->id_oc_det)) ? $det->id_oc_det : 0,
        //             'cantidad' => floatval($det->cantidad),
        //             'valorizacion' => floatval($det->total)
        //             ];
        //         array_push($nuevo_detalle, $nuevo);
        //     }
        // }

        foreach ($detalle as $det){
            $prec = ($det->precio_cotizado !== null ? $det->precio_cotizado : $det->unitario);
            $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                [
                    'id_mov_alm' => $id_ingreso,
                    'id_producto' => $det->id_producto,
                    'id_posicion' => $det->id_posicion,
                    'cantidad' => $det->cantidad,
                    'valorizacion' => (floatval($det->cantidad) * floatval($prec)),
                    'usuario' => $id_usuario,
                    'id_guia_com_det' => $det->id_guia_com_det,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm_det'
                );
                
            if ($det->id_posicion !== null){
                
                $ubi = DB::table('almacen.alm_prod_ubi')
                    ->where([['id_producto','=',$det->id_producto],
                            ['id_posicion','=',$det->id_posicion]])
                    ->first();
                //traer stockActual
                $saldo = $this->saldo_actual($det->id_producto, $det->id_posicion);
                $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);

                if (!isset($ubi->id_posicion)){
                    DB::table('almacen.alm_prod_ubi')->insert([
                        'id_producto' => $det->id_producto,
                        'id_posicion' => $det->id_posicion,
                        'stock' => $saldo,
                        'costo_promedio' => $costo,
                        'estado' => 1,
                        'fecha_registro' => $fecha
                        ]);
                } else {
                    DB::table('almacen.alm_prod_ubi')
                    ->where('id_prod_ubi',$ubi->id_prod_ubi)
                    ->update([  'stock' => $saldo,
                                'costo_promedio' => $costo
                            ]);
                }
            }
            // if ($det->id_oc_det !== null && $det->id_oc_det > 0){
            //     //cambiar estado orden
            //     DB::table('logistica.log_det_ord_compra')
            //     ->where('id_detalle_orden',$det->id_oc_det)
            //     ->update(['estado'=>6]);//En Almacen
                
            //     //cambiar estado requerimiento
            //     DB::table('almacen.alm_det_req')
            //     ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_valorizacion_cotizacion','=','log_valorizacion_cotizacion.id_valorizacion_cotizacion')
            //     ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
            //     ->where('log_det_ord_compra.id_detalle_orden',$det->id_oc_det)
            //     ->update(['estado'=>6]);//En Almacen
            // }
        }

        //cambiar estado guiacom
        DB::table('almacen.guia_com')
            ->where('id_guia',$id_guia)->update(['estado'=>9]);//Procesado

        return response()->json($id_ingreso);
    }
    public function req_almacen($id_oc_det){
        $data = DB::table('almacen.alm_det_req')
        ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_detalle_requerimiento','=','alm_det_req.id_detalle_requerimiento')
        ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_valorizacion_cotizacion','=','log_valorizacion_cotizacion.id_valorizacion_cotizacion')
        ->where('log_det_ord_compra.id_detalle_orden','=',$id_oc_det)
        // ->update(['estado'=>6]);//En Almacen
        ->get();
        // ->update(['alm_det_req.estado' => 6]);//En almacen
        return $data;
    }
    public function id_item($id_producto){
        $item = DB::table('almacen.alm_item')
        ->where('alm_item.id_producto',$id_producto)
        ->first();
        return $item->id_item;
    }
    public function id_ingreso($id_guia){
        $ing = DB::table('almacen.mov_alm')
        ->where('mov_alm.id_guia_com',$id_guia)
        ->first();
        return response()->json($ing->id_mov_alm);
    }
    public function get_ingreso($id){
        $ingreso = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','alm_almacen.descripcion as des_almacen',
            DB::raw("CONCAT(tp_doc_almacen.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia"),
            DB::raw("CONCAT(cont_tp_doc.abreviatura,'-',doc_com.serie,'-',doc_com.numero) as doc"),
            'doc_com.fecha_emision as doc_fecha_emision','tp_doc_almacen.descripcion as tp_doc_descripcion',
            'guia_com.fecha_emision as fecha_guia','sis_usua.usuario as nom_usuario',
            'adm_contri.razon_social','adm_contri.direccion_fiscal','adm_contri.nro_documento','tp_ope.cod_sunat',
            'tp_ope.descripcion as ope_descripcion','empresa.razon_social as empresa_razon_social',
            'empresa.nro_documento as ruc_empresa','doc_com.tipo_cambio','sis_moneda.descripcion as des_moneda',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as persona"))
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->join('contabilidad.adm_contri as empresa','empresa.id_contribuyente','=','adm_empresa.id_contribuyente')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
            ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
            ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('mov_alm.id_mov_alm',$id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.codigo_anexo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura',
            'sis_moneda.simbolo','log_valorizacion_cotizacion.subtotal')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','alm_prod.id_moneda')
            ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['mov_alm_det.id_mov_alm','=',$id],['mov_alm_det.estado','=',1]])
            ->get();
        
        $ocs = DB::table('almacen.guia_com_oc')
            ->select('log_ord_compra.codigo')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
            ->where([['guia_com_oc.id_guia_com','=',$ingreso->id_guia_com],
                    ['guia_com_oc.estado','=',1]])
            ->get();

        return ['ingreso'=>$ingreso,'detalle'=>$detalle,'ocs'=>$ocs];
    }
    public function imprimir($id_ing){
        $result = $this->get_ingreso($id_ing);
        $ingreso = $result['ingreso'];
        // $detalle = $result->detalle;
        // $ocs = $result->ocs;
        return $ingreso->codigo;
    }
    public function imprimir_ingreso($id_ing){
        // $ing = $this->mostrar_ingreso(Request $request_ingreso);
        $id = $this->decode5t($id_ing);
        $result = $this->get_ingreso($id);
        $ingreso = $result['ingreso'];
        $detalle = $result['detalle'];
        $ocs = $result['ocs'];

        $cod_ocs = '';
        foreach($ocs as $oc){
            if ($cod_ocs == ''){
                $cod_ocs .= $oc->codigo;
            } else {
                $cod_ocs .= ', '.$oc->codigo;
            }
        }

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:12px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <p style="text-align:right;font-size:14px;margin:0px;"><strong>N° '.$ingreso->codigo.'</strong></p>
                <p style="text-align:right;font-size:12px;margin:0px;">Fecha de Ingreso: '.$ingreso->fecha_emision.'</p>
                <h3 style="margin:0px;"><center>INGRESO A ALMACÉN</center></h3>
                <h5><center>'.$ingreso->id_almacen.' - '.$ingreso->des_almacen.'</center></h5>
                
                <table border="0">
                    <tr>
                        <td class="subtitle">Guía N°</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$ingreso->guia.'</td>
                        <td>Fecha Guía</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->fecha_guia.'</td>
                    </tr>
                    <tr>
                        <td width=110px>Documento</td>
                        <td width=10px>:</td>
                        <td width=300px>'.$ingreso->doc.'</td>
                        <td width=100px>Fecha Factura</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->doc_fecha_emision.'</td>
                    </tr>
                    <tr>
                        <td>Proveedor</td>
                        <td>:</td>
                        <td>'.$ingreso->nro_documento.' - '.$ingreso->razon_social.'</td>
                        <td class="subtitle">Tipo Movim.</td>
                        <td>:</td>
                        <td class="verticalTop">'.$ingreso->cod_sunat.' '.$ingreso->ope_descripcion.'</td>
                    </tr>
                    <tr>
                        <td>Orden de Compra</td>
                        <td>:</td>
                        <td>'.$cod_ocs.'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Responsable</td>
                        <td>:</td>
                        <td>'.$ingreso->persona.'</td>
                    </tr>
                </table>
                <br/>
                <table id="detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código</th>
                            <th width=40% >Descripción</th>
                            <th>Posición</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>Mnd.</th>
                            <th>Valor.</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $i = 1;
                    foreach($detalle as $det){
                        $html.='
                        <tr>
                            <td class="right">'.$i.'</td>
                            <td>'.$det->codigo.'</td>
                            <td>'.$det->descripcion.'</td>
                            <td>'.$det->cod_posicion.'</td>
                            <td class="right">'.$det->cantidad.'</td>
                            <td>'.$det->abreviatura.'</td>
                            <td>'.$det->simbolo.'</td>
                            <td class="right">'.$det->valorizacion.'</td>
                        </tr>';
                        $i++;
                    }
                    $html.='</tbody>
                </table>
                <p style="text-align:right;font-size:11px;">Elaborado por: '.$ingreso->nom_usuario.' '.$ingreso->fecha_registro.'</p>

            </body>
        </html>';
        
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('ingreso.pdf');
    }
    public function mostrar_ingreso($id){
        $ingreso = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','alm_almacen.descripcion as des_almacen',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
            'guia_com.fecha_emision as fecha_guia','sis_usua.usuario as nom_usuario')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            ->where('mov_alm.id_mov_alm',$id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->where('mov_alm_det.estado',1)
            ->get();

        return response()->json(['ingreso'=>$ingreso,'detalle'=>$detalle]);
    }
    /**Guia Compra Transportista */
    public function mostrar_transportistas($id){
        $data = DB::table('almacen.guia_com_tra')
        ->select('guia_com_tra.*','adm_contri.razon_social')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com_tra.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where([['guia_com_tra.id_guia', '=', $id]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_transportista($id){
        $data = DB::table('almacen.guia_com_tra')
        ->where([['guia_com_tra.id_guia_com_tra', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_transportista(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_com_tra')->insertGetId(
            [
                'id_guia' => $request->id_guia,
                'serie' => $request->serie_tra,
                'numero' => $request->numero_tra,
                'id_proveedor' => $request->id_proveedor_tra,
                'fecha_emision' => $request->fecha_emision_tra,
                'referencia' => $request->referencia,
                'placa' => $request->placa,
                'usuario' => 3,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_guia_com_tra'
            );
        return response()->json($id_guia);
    }

    public function update_transportista(Request $request)
    {
        $data = DB::table('almacen.guia_com_tra')
            ->where('id_guia_com_tra', $request->id_guia_com_tra)
            ->update([
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'referencia' => $request->referencia,
                'placa' => $request->placa,
                // 'usuario' => 3,
            ]);
        return response()->json($data);
    }

    public function anular_transportista(Request $request, $id)
    {
        $data = DB::table('almacen.guia_com_tra')->where('id_guia_com_tra', $id)
            ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }
    public function verifica_posiciones($id_guia){
        $detalle = DB::table('almacen.guia_com_det')
            ->where('id_guia_com',$id_guia)->get();
        $pos = false;
        foreach($detalle as $d){
            if ($d->id_posicion == null){
                $pos = true;
            }
        }
        return ($pos) ? 'Debe ingresar las posiciones de todos los items' : '';
    }
    /**Guia Detalle */
    public function listar_guia_detalle($id){
        $data = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_und_medida.abreviatura','alm_prod.series','log_ord_compra.codigo AS cod_orden')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_com_det.id_posicion')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->leftjoin('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
        ->where([['guia_com_det.id_guia_com', '=', $id],
                 ['guia_com_det.estado','=',1]])
            ->get();

        $html = '';
        $suma = 0;
        foreach($data as $det){

            $id_guia_det = $det->id_guia_com_det;
            $oc = $det->cod_orden;
            $codigo = $det->codigo;
            $descripcion = $det->descripcion;
            $cantidad = $det->cantidad;
            $abrev = $det->abreviatura;
            $id_posicion = $det->id_posicion;
            $unitario = ($det->unitario_adicional > 0 ? $det->unitario_adicional : $det->unitario);
            $total = $unitario * $det->cantidad;
            $suma += $total;
            $chk = ($det->series ? 'true' : 'false');
            $series = '';

            $det_series = DB::table('almacen.alm_prod_serie')
            ->where([['alm_prod_serie.id_prod','=',$det->id_producto],
                     ['alm_prod_serie.id_guia_det','=',$id_guia_det]])
            ->get();

            foreach($det_series as $det){
                if ($series !== ''){
                    $series += ', '+$det_series->serie;
                } else {
                    $series = 'Series: '+$det_series->serie;
                }
            }

            $html .= 
            '<tr id="reg-'.$id_guia_det.'">
                <td>'.$oc.'</td>
                <td><input type="text" class="oculto" name="series" value="'.$chk.'"/>'.$codigo.'</td>
                <td>'.$descripcion.' '.$series.'</td>
                <td>
                    <select class="input-data" name="id_posicion" disabled="true">
                        <option value="0">Elija una opción</option>';
                        $pos = $this->mostrar_posiciones_cbo();
                        foreach ($pos as $row) {
                            if ($id_posicion == $row->id_posicion){
                                $html.='<option value="'.$row->id_posicion.'" selected>'.$row->codigo.'</option>';
                            } else {
                                $html.='<option value="'.$row->id_posicion.'">'.$row->codigo.'</option>';
                            }
                        }
                    $html.='</select>
                </td>
                <td><input type="number" class="input-data right" name="cantidad" value="'.$cantidad.'" onChange="calcula_total('.$id_guia_det.');" disabled="true"/></td>
                <td>'.$abrev.'</td>
                <td><input type="number" class="input-data right" name="unitario" value="'.$unitario.'" disabled="true"/></td>
                <td><input type="number" class="input-data right" name="total" value="'.$total.'" disabled="true"/></td>
                <td style="display:flex;">
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" onClick="agrega_series('.$id_guia_det.','.$codigo.','.$cantidad.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$id_guia_det.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle('.$id_guia_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$id_guia_det.');"></i>
                </td>
            </tr>';
        }
        return json_encode(['html'=>$html,'suma'=>$suma]);
        // return response()->json($data);
    }
    public function mostrar_detalle($id){
        $data = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*',DB::raw("CONCAT(alm_prod.codigo,'-',
            alm_prod.descripcion) as producto"),'alm_und_medida.abreviatura')
            ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
            ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
            ->where([['guia_com_det.id_guia_com_det', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function guardar_detalle_oc(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $oc = explode(',',$request->id_oc_det);
        $prod = explode(',',$request->id_producto);
        $pos = explode(',',$request->id_posicion);
        $cant = explode(',',$request->cantidad);
        $unid = explode(',',$request->id_unid_med);
        $unit = explode(',',$request->unitario);
        // $total = explode(',',$request->total);
        $count = count($oc);

        for ($i=0; $i<$count; $i++){
            $id_guia_com = $request->id_guia_com;
            $id_oc_det = $oc[$i];
            $id_producto = $prod[$i];
            $id_posicion = $pos[$i];
            $cantidad = $cant[$i];
            $id_unid_med = $unid[$i];
            $unitario = $unit[$i];
            // $total = $total[$i];

            $p = DB::table('almacen.guia_com_det')
            ->where([['guia_com_det.id_guia_com','=',$id_guia_com],
                    ['guia_com_det.id_producto','=',$id_producto],
                    ['guia_com_det.id_oc_det','=',$id_oc_det],
                    ['guia_com_det.estado','=',1]])
            ->first();
            
            if (isset($p)){//variable declarada que su valor NO es nulo
                $cant = floatval($p->cantidad) + floatval($cantidad);
                $data = DB::table('almacen.guia_com_det')
                ->where('id_guia_com_det', $p->id_guia_com_det)
                ->update(['cantidad' => $cant]);
            }
            else {
                $data = DB::table('almacen.guia_com_det')->insertGetId(
                [
                    'id_guia_com' => $id_guia_com,
                    'id_producto' => $id_producto,
                    'id_posicion' => $id_posicion,
                    'cantidad' => $cantidad,
                    'id_unid_med' => $id_unid_med,
                    'id_oc_det' => $id_oc_det,
                    'unitario' => $unitario,
                    'total' => ($cantidad * $unitario),
                    'usuario' => 3,
                    'estado' => 1,
                    'fecha_registro' => $fecha
                ],
                    'id_guia_com_det'
                );
            }
        }

        $id_oc = DB::table('logistica.log_det_ord_compra')
            ->where('id_detalle_orden', $oc[0])->first();

        $exist = DB::table('almacen.guia_com_oc')
            ->where([['id_oc','=', $id_oc->id_orden_compra],
                    ['id_guia_com','=', $request->id_guia_com],
                    ['estado','=', 1]])->first();

        if (empty($exist)){
            $this->guardar_oc($request->id_guia_com, $id_oc->id_orden_compra);
        }

        return response()->json($data);
    }
    public function usuario(){
        $usu = Auth::user()->id_usuario;
        return response()->json($usu);
    }
    public function guardar_guia_detalle(Request $request)
    {
        $usu = Auth::user()->id_usuario;
        $data = DB::table('almacen.guia_com_det')->insertGetId([
                'id_guia_com' => $request->id_guia,
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                'id_unid_med' => $request->id_unid_med,
                'unitario' => $request->unitario,
                'unitario_adicional' => 0,
                'total' => $request->total,
                'usuario' => $usu,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_guia_com_det'
            );
        return response()->json(['data:'=>$data,'usuario:'=>$usu]);
    }
    public function update_detalle(Request $request)
    {
        $data = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det', $request->id_guia_com_det)
            ->update([
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                'unitario' => $request->unitario,
                'total' => $request->total,
                // 'id_unid_med' => $request->id_unid_med
            ]);
        return response()->json($data);
    }
    public function anular_detalle(Request $request, $id)
    {
        $data = DB::table('almacen.guia_com_det')->where('id_guia_com_det', $id)
            ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }

    /**Guia Compra OC */
    public function guardar_oc($id_guia,$id_oc){
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.guia_com_oc')->insertGetId(
            [
                'id_guia_com' => $id_guia,
                'id_oc' => $id_oc,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_guia_com_oc'
            );
        return response()->json($data);
    }
    public function anular_oc($id,$guia)
    {
        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*')
            ->join('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->where([['log_ord_compra.id_orden_compra','=',$id],
                    ['guia_com_det.id_guia_com','=',$guia]])
            ->get()->toArray();

        foreach($detalle as $det){
            $dat = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det', $det->id_guia_com_det)
            ->update([ 'estado' => 2 ]);
        }

        $data = DB::table('almacen.guia_com_oc')
            ->where([['id_oc','=',$id],['id_guia_com','=',$guia]])
            ->update([ 'estado' => 2 ]);

        return response()->json($data);
    }
    public function guia_ocs($id_guia){
        $data = DB::table('almacen.guia_com_oc')
        ->select('guia_com_oc.id_oc','log_ord_compra.codigo','adm_contri.razon_social','log_ord_compra.fecha',
        //'log_cdn_pago.descripcion as condicion','log_esp_compra.forma_pago_credito','log_esp_compra.fecha_entrega','log_esp_compra.lugar_entrega',
        DB::raw("CONCAT(log_ord_compra.codigo,' - ',adm_contri.razon_social) as orden"),
        DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('logistica.log_cotizacion','log_cotizacion.id_cotizacion','=','log_ord_compra.id_cotizacion')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','log_ord_compra.id_usuario')
        ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
        ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        // ->leftjoin('logistica.log_esp_compra','log_esp_compra.id_especificacion_compra','=','log_cotizacion.id_especificacion_compra')
        // ->join('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','log_esp_compra.id_condicion_pago')
        ->where([['guia_com_oc.id_guia_com','=',$id_guia],['guia_com_oc.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    public function listar_ordenes($id_proveedor){
        $data = DB::table('logistica.log_ord_compra')
            ->select('log_ord_compra.id_orden_compra',
            DB::raw("CONCAT(log_ord_compra.codigo,' - ',adm_contri.razon_social) AS orden"))
            ->join('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->where([['log_ord_compra.id_proveedor','=',$id_proveedor]])
            ->get();
            
        return response()->json($data);
    }
    public function listar_oc_det($id){
        $data = DB::table('logistica.log_det_ord_compra')
            ->select('log_det_ord_compra.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_und_medida.abreviatura','alm_und_medida.id_unidad_medida','alm_item.id_producto',
            'log_ord_compra.codigo as cod_orden','log_valorizacion_cotizacion.subtotal',
            'log_valorizacion_cotizacion.cantidad_cotizada')
            ->join('almacen.alm_item','alm_item.id_item','=','log_det_ord_compra.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            // ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_producto','=','alm_prod.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
            ->join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            // ->leftjoin('almacen.guia_com_det','guia_com_det.id_oc_det','=','log_det_ord_compra.id_detalle_orden')
            ->where([['log_det_ord_compra.id_orden_compra','=',$id],
                    ['log_det_ord_compra.estado','=',1]])
            ->get();
        
            $html = '';
            foreach($data as $det){
                $guia = DB::table('almacen.guia_com_det')
                ->select(DB::raw('SUM(guia_com_det.cantidad) as sum_cantidad'))
                ->where([['id_oc_det','=',$det->id_detalle_orden],
                        ['estado','=',1]])
                ->first();
                $cantidad_nueva = $det->cantidad_cotizada - ($guia->sum_cantidad !== null ? $guia->sum_cantidad : 0);

                if ($cantidad_nueva > 0){
                    $posicion = DB::table('almacen.alm_prod_ubi')
                    ->select('alm_prod_ubi.*','alm_ubi_posicion.codigo as cod_posicion')
                    ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                    ->where([['alm_prod_ubi.id_producto','=',$det->id_producto],
                            ['alm_prod_ubi.estado','=',1]])
                    ->first();
    
                    $id_oc_det = $det->id_detalle_orden;
                    $oc = $det->cod_orden;
                    $id_oc = $det->id_orden_compra;
                    $id_producto = $det->id_producto;
                    $codigo = $det->codigo;
                    $descripcion = $det->descripcion;
                    $cantidad = $cantidad_nueva;
                    $id_unid_med = $det->id_unidad_medida;
                    $abrev = $det->abreviatura;
                    $unitario = $det->subtotal / $det->cantidad_cotizada;
                    $total = $det->subtotal;
                    $cod_posicion = (isset($posicion->cod_posicion) ? $posicion->cod_posicion : '');
    
                    $html .= 
                    '<tr id="oc-'.$id_oc_det.'">
                        <td><input type="checkbox"></td>
                        <td><input type="text" name="id_oc_det" class="oculto" value="'.$id_oc_det.'"/>'.$oc.'</td>
                        <td><input type="text" name="id_producto" class="oculto" value="'.$id_producto.'"/>'.$codigo.'</td>
                        <td>'.$descripcion.'</td>
                        <td>
                            <select class="input-data" name="id_posicion">
                                <option value="0">Elija una opción</option>';
                                $pos = $this->mostrar_posiciones_cbo();
                                foreach ($pos as $row) {
                                    if ($cod_posicion == $row->id_posicion){
                                        $html.='<option value="'.$row->id_posicion.'" selected>'.$row->codigo.'</option>';
                                    } else {
                                        $html.='<option value="'.$row->id_posicion.'">'.$row->codigo.'</option>';
                                    }
                                }
                            $html.='</select>
                        </td>
                        <td><input type="number" name="cantidad" class="input-data right" onChange="calcula_total_oc('.$id_oc_det.');"  value="'.$cantidad.'"/></td>
                        <td><input type="text" name="id_unid_med" class="oculto" value="'.$id_unid_med.'"/>'.$abrev.'</td>
                        <td><input type="number" name="unitario" class="input-data right" readOnly value="'.$unitario.'"/></td>
                        <td><input type="number" name="total" class="input-data right" readOnly value="'.$total.'"/></td>
                    </tr>';    
                }
            }
        // return response()->json($data);
        return json_encode($html);
    }
    /**Guardar Series */
    public function guardar_series(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $se = explode(',',$request->series);
        $count = count($se);
        $data = 0;
        if (!empty($request->series)){
            $id = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','guia_com.id_almacen')
            ->where('id_guia_com_det',$request->id_guia_det)
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->first();
    
            for ($i=0; $i<$count; $i++){
                $serie = $se[$i];
                $data = DB::table('almacen.alm_prod_serie')->insertGetId(
                    [
                        'id_prod'=>$id->id_producto,
                        'id_almacen'=>$id->id_almacen,
                        'serie'=>$serie,
                        'estado'=>1,
                        'fecha_registro'=>$fecha,
                        'id_guia_det'=>$request->id_guia_det
                    ],
                    'id_prod_serie'
                );
            }
        }
        $an = explode(',',$request->anulados);
        $can = count($an);

        if (!empty($request->anulados)){
            for ($i=0;$i<$can;$i++){
                $data = DB::table('almacen.alm_prod_serie')
                ->where('id_prod_serie',$an[$i])
                ->update([ 'estado' => 2 ]);
            }
        }

        return response()->json($data);
    }
    public function listar_series($id_guia_det){
        $series = DB::table('almacen.alm_prod_serie')
        ->where([['id_guia_det','=',$id_guia_det],
                 ['estado','=',1]])
        ->get();
        return response()->json($series);
    }

    /**Comprobante de Compra */
    public function listar_docs_compra(){
        $data = DB::table('almacen.doc_com')
        ->select('doc_com.*','adm_contri.razon_social','adm_estado_doc.estado_doc as des_estado')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','doc_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
        // ->where('doc_com.estado',1)
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_doc_com($id){
        $doc = DB::table('almacen.doc_com')
            ->select('doc_com.*','adm_estado_doc.estado_doc',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_usuario"))
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_com.estado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','doc_com.usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('doc_com.id_doc_com',$id)
            ->get();
        return response()->json(['doc'=>$doc]);
    }
    public function listar_doc_guias($id_doc){
        $guias = DB::table('almacen.doc_com_guia')
        ->select('doc_com_guia.*',DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
        'guia_com.fecha_emision as fecha_guia','guia_motivo.descripcion as des_motivo',
        'adm_contri.razon_social')
        ->join('almacen.guia_com','guia_com.id_guia','=','doc_com_guia.id_guia_com')
        ->join('almacen.guia_motivo','guia_motivo.id_motivo','=','guia_com.id_motivo')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where([['doc_com_guia.id_doc_com','=',$id_doc],
                ['doc_com_guia.estado','=',1]])
        ->get();
        $html ='';
        foreach($guias as $guia){
            $html .= '
            <tr id="doc-'.$guia->id_doc_com_guia.'">
                <td>'.$guia->guia.'</td>
                <td>'.$guia->fecha_guia.'</td>
                <td>'.$guia->razon_social.'</td>
                <td>'.$guia->des_motivo.'</td>
                <td><i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular Guia" onClick="anular_guia('.$guia->id_guia_com.','.$guia->id_doc_com_guia.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }
    public function listar_docven_guias($id_doc){
        $guias = DB::table('almacen.doc_ven_guia')
        ->select('doc_ven_guia.*',DB::raw("CONCAT('GR-',guia_ven.serie,'-',guia_ven.numero) as guia"),
        'guia_ven.fecha_emision as fecha_guia','guia_motivo.descripcion as des_motivo',
        'adm_contri.razon_social')
        ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','doc_ven_guia.id_guia_ven')
        ->join('almacen.guia_motivo','guia_motivo.id_motivo','=','guia_ven.id_motivo')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','guia_ven.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->where([['doc_ven_guia.id_doc_ven','=',$id_doc],
                ['doc_ven_guia.estado','=',1]])
        ->get();
        $html ='';
        foreach($guias as $guia){
            $html .= '
            <tr id="doc-'.$guia->id_doc_ven_guia.'">
                <td>'.$guia->guia.'</td>
                <td>'.$guia->fecha_guia.'</td>
                <td>'.$guia->razon_social.'</td>
                <td>'.$guia->des_motivo.'</td>
                <td><i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular Guia" onClick="anular_guia('.$guia->id_guia_ven.','.$guia->id_doc_ven_guia.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }
    public function listar_guias_prov($id_proveedor){
        $data = DB::table('almacen.guia_com')
            ->select('guia_com.*',DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
            'adm_contri.razon_social','adm_estado_doc.estado_doc')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->leftjoin('almacen.doc_com_guia','doc_com_guia.id_guia_com','=','guia_com.id_guia')
            ->where('guia_com.id_proveedor',$id_proveedor)
            ->where('guia_com.estado',9)//Guias procesadas
            ->where('doc_com_guia.id_guia_com',null)
            ->orWhere('doc_com_guia.estado',2)
            ->get();
        return response()->json($data);
    }
    public function listar_guias_compra($id_almacen){
        $data = DB::table('almacen.guia_com')
            ->select('guia_com.*','adm_contri.razon_social')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            // ->join('logistica.guia_motivo','guia_motivo.id_motivo','=','guia_com.id_motivo')
            ->where([['guia_com.id_almacen','=',$id_almacen]])
            ->get();
        return response()->json($data);
        // return json_encode($html);
    }
    public function listar_req($id_empresa){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','adm_grupo.id_sede')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
            ->where([['adm_empresa.id_empresa','=',$id_empresa],
                    ['alm_req.estado','=',1]])//cambiar estado
            ->get();
        return response()->json($data);
    }
    public function mostrar_doc_detalle($id_doc_det){
        $data = DB::table('almacen.doc_com_det')
            ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
            'alm_und_medida.abreviatura')
            ->join('almacen.alm_item','alm_item.id_item','=','doc_com_det.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
            ->where([['doc_com_det.id_doc_det','=',$id_doc_det]])
            ->first();
        return response()->json($data);
    }
    public function listar_doc_items($id_doc){
        $detalle = DB::table('almacen.doc_com_det')
            ->select('doc_com_det.*','alm_prod.codigo','alm_prod.descripcion',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia"),
            'alm_und_medida.abreviatura')
            ->join('almacen.alm_item','alm_item.id_item','=','doc_com_det.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_com_det.id_unid_med')
            ->where([['doc_com_det.id_doc','=',$id_doc],
                    ['doc_com_det.estado','=',1]])
            ->get();
        $html = '';
        foreach($detalle as $det){
            $html .= '
            <tr id="det-'.$det->id_doc_det.'">
                <td>'.$det->guia.'</td>
                <td>'.$det->codigo.'</td>
                <td>'.$det->descripcion.'</td>
                <td><input type="number" class="input-data right" name="cantidad" 
                    value="'.$det->cantidad.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td>'.$det->abreviatura.'</td>
                <td><input type="number" class="input-data right" name="precio_unitario" 
                    value="'.$det->precio_unitario.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="porcen_dscto" 
                    value="'.$det->porcen_dscto.'" onChange="calcula_dscto('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="total_dscto" 
                    value="'.$det->total_dscto.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="precio_total" 
                    value="'.$det->precio_total.'" disabled="true"/>
                </td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$det->id_doc_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$det->id_doc_det.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }
    public function listar_docven_items($id_doc){
        $detalle = DB::table('almacen.doc_ven_det')
            ->select('doc_ven_det.*','alm_prod.codigo','alm_prod.descripcion',
            DB::raw("CONCAT('GR-',guia_ven.serie,'-',guia_ven.numero) as guia"),
            'alm_und_medida.abreviatura')
            ->join('almacen.alm_item','alm_item.id_item','=','doc_ven_det.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','doc_ven_det.id_guia_ven_det')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','doc_ven_det.id_unid_med')
            ->where([['doc_ven_det.id_doc','=',$id_doc],
                    ['doc_ven_det.estado','=',1]])
            ->get();
        $html = '';
        foreach($detalle as $det){
            $html .= '
            <tr id="det-'.$det->id_doc_det.'">
                <td>'.$det->guia.'</td>
                <td>'.$det->codigo.'</td>
                <td>'.$det->descripcion.'</td>
                <td><input type="number" class="input-data right" name="cantidad" 
                    value="'.$det->cantidad.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td>'.$det->abreviatura.'</td>
                <td><input type="number" class="input-data right" name="precio_unitario" 
                    value="'.$det->precio_unitario.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="porcen_dscto" 
                    value="'.$det->porcen_dscto.'" onChange="calcula_dscto('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="total_dscto" 
                    value="'.$det->total_dscto.'" onChange="calcula_total('.$det->id_doc_det.');" 
                    disabled="true"/>
                </td>
                <td><input type="number" class="input-data right" name="precio_total" 
                    value="'.$det->precio_total.'" disabled="true"/>
                </td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$det->id_doc_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$det->id_doc_det.');"></i>
                </td>
            </tr>';
        }
        return json_encode($html);
    }

    public function guardar_doc_compra(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_doc = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'id_condicion' => $request->id_condicion,
                'credito_dias' => $request->credito_dias,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => $request->sub_total,
                'total_descuento' => $request->total_descuento,
                'porcen_descuento' => $request->porcen_descuento,
                'total' => $request->total,
                'total_igv' => $request->total_igv,
                'total_ant_igv' => $request->total_ant_igv,
                'porcen_igv' => $request->porcen_igv,
                'porcen_anticipo' => $request->porcen_anticipo,
                'total_otros' => $request->total_otros,
                'total_a_pagar' => $request->total_a_pagar,
                'usuario' => 3,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_doc_com'
            );
        return response()->json(["id_doc"=>$id_doc,"id_proveedor"=>$request->id_proveedor]);
    }
    public function update_doc_compra(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_com')
            ->where('id_doc_com',$request->id_doc_com)
            ->update([
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_proveedor' => $request->id_proveedor,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'id_condicion' => $request->id_condicion,
                'credito_dias' => $request->credito_dias,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => $request->sub_total,
                'total_descuento' => $request->total_descuento,
                'porcen_descuento' => $request->porcen_descuento,
                'total' => $request->total,
                'total_igv' => $request->total_igv,
                'total_ant_igv' => $request->total_ant_igv,
                'porcen_igv' => $request->porcen_igv,
                'porcen_anticipo' => $request->porcen_anticipo,
                'total_otros' => $request->total_otros,
                'total_a_pagar' => $request->total_a_pagar,
            ]);
        return response()->json($data);
    }
    public function anular_doc_compra($id)
    {
        $data = DB::table('almacen.doc_com')->where('id_doc_com', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function update_doc_detalle(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_com_det')
            ->where('id_doc_det', $request->id_doc_det)
            ->update([
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'porcen_dscto' => $request->porcen_dscto,
                'total_dscto' => $request->total_dscto,
                'precio_total' => $request->precio_total,
            ]);
        return response()->json($data);
    }
    public function anular_doc_detalle($id_doc_det)
    {
        $data = DB::table('almacen.doc_com_det')
            ->where('id_doc_det', $id_doc_det)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function guardar_doc_items_guia($id_guia, $id_doc){
        $fecha = date('Y-m-d H:i:s');
        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','log_valorizacion_cotizacion.precio_cotizado as precio')//jalar el precio de la oc o cotizacion
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['guia_com_det.id_guia_com','=',$id_guia],
                    ['guia_com_det.estado','=',1 ]])
            ->get();
        $nuevo_detalle = [];
        $cant = 0;
    
        foreach ($detalle as $det){
            $exist = false;
            foreach ($nuevo_detalle as $nue => $value){
                if ($det->id_producto == $value['id_producto'] && $det->id_guia_com == $value['id_guia_com']){
                    $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
                    $nuevo_detalle[$nue]['unitario'] = floatval($value['unitario']) + floatval($det->unitario);
                    $nuevo_detalle[$nue]['total'] = floatval($value['total']) + floatval($det->total);
                    $exist = true;
                }
            }
            if ($exist === false){
                $nuevo = [
                    'id_guia_com_det' => $det->id_guia_com_det,
                    'id_guia_com' => $det->id_guia_com,
                    'id_producto' => $det->id_producto,
                    'id_unid_med' => $det->id_unid_med,
                    'cantidad' => floatval($det->cantidad),
                    'unitario' => floatval($det->precio),
                    'total' => (floatval($det->cantidad) * floatval($det->precio))
                    ];
                array_push($nuevo_detalle, $nuevo);
            }
        }
        foreach($nuevo_detalle as $det){
            $item = DB::table('almacen.alm_item')
                ->where('id_producto',$det['id_producto'])
                ->first();

            $id_det = DB::table('almacen.doc_com_det')->insert(
                [
                    'id_doc'=>$id_doc,
                    'id_item'=>$item->id_item,
                    'cantidad'=>$det['cantidad'],
                    'id_unid_med'=>$det['id_unid_med'],
                    'precio_unitario'=>$det['unitario'],
                    'sub_total'=>$det['total'],
                    'porcen_dscto'=>0,
                    'total_dscto'=>0,
                    'precio_total'=>$det['total'],
                    'id_guia_com_det'=>$det['id_guia_com_det'],
                    'estado'=>1,
                    'fecha_registro'=>$fecha
                ]);
        }
        $guia = DB::table('almacen.doc_com_guia')->insert(
            [
                'id_doc_com'=>$id_doc,
                'id_guia_com'=>$id_guia,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ]);
        $ingreso = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_com',$id_guia)
            ->first();

        if (isset($ingreso->id_mov_alm)){
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm',$ingreso->id_mov_alm)
                ->update(['id_doc_com'=>$id_doc]);
        }

        return response()->json($guia);
    }
    public function guardar_doc_guia(Request $request){
        $fecha = date('Y-m-d H:i:s');

        $guia = DB::table('almacen.guia_com')
            ->select('guia_com.*')
            ->where('id_guia',$request->id_guia)
            ->first();

        $detalle = DB::table('almacen.guia_com_det')
            ->select('guia_com_det.*','log_valorizacion_cotizacion.precio_cotizado')
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->where([['guia_com_det.id_guia_com','=',$request->id_guia],
                    ['guia_com_det.estado','=',1 ]])
            ->get();

        $id_doc = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie'=>$request->serie,
                'numero'=>$request->numero,
                'id_tp_doc'=>$request->id_tp_doc,
                'id_proveedor'=>$guia->id_proveedor,
                'fecha_emision'=>$request->fecha_emision,
                'fecha_vcmto'=>$request->fecha_emision,
                // 'id_condicion'=>$guia->id_condicion,
                // 'credito_dias'=>$guia->credito_dias,
                'moneda'=>1,
                'usuario'=>3,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ],
                'id_doc_com'
        );
        $sub_total = 0;

        foreach($detalle as $det){
            $unitario = ($det->precio_cotizado !== null) ? $det->precio_cotizado : $det->unitario;
            $total = $unitario * $det->cantidad;
            $sub_total += $total;

            $item = DB::table('almacen.alm_item')
                ->where('id_producto',$det->id_producto)
                ->first();

            $id_det = DB::table('almacen.doc_com_det')->insertGetId(
                [
                    'id_doc'=>$id_doc,
                    'id_item'=>$item->id_item,
                    'cantidad'=>$det->cantidad,
                    'id_unid_med'=>$det->id_unid_med,
                    'precio_unitario'=>$unitario,
                    'sub_total'=>$total,
                    'porcen_dscto'=>0,
                    'total_dscto'=>0,
                    'precio_total'=>$total,
                    'id_guia_com_det'=>$det->id_guia_com_det,
                    'estado'=>1,
                    'fecha_registro'=>$fecha
                ],
                    'id_doc_det'
            );
        }
        //obtiene IGV
        $impuesto = DB::table('contabilidad.cont_impuesto')
            ->where([['codigo','=','IGV'],['fecha_inicio','<',$request->fecha_emision]])
            ->orderBy('fecha_inicio','desc')
            ->first();
        $igv = $impuesto->porcentaje * $sub_total / 100;

        //actualiza totales
        DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
        ->update([
            'sub_total'=>$sub_total,
            'total_descuento'=>0,
            'porcen_descuento'=>0,
            'total'=>$sub_total,
            'total_igv'=>$igv,
            'total_ant_igv'=>0,
            'porcen_igv' => $request->porcen_igv,
            'porcen_anticipo' => $request->porcen_anticipo,
            'total_otros' => $request->total_otros,
            'total_a_pagar'=>($sub_total + $igv)
        ]);

        $guia = DB::table('almacen.doc_com_guia')->insertGetId(
            [
                'id_doc_com'=>$id_doc,
                'id_guia_com'=>$request->id_guia,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ],
                'id_doc_com_guia'
        );
        $ingreso = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_com',$request->id_guia)
            ->first();

        if (isset($ingreso->id_mov_alm)){
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm',$ingreso->id_mov_alm)
                ->update(['id_doc_com'=>$id_doc]);
        }
        return response()->json($id_doc);
    }
    public function anular_guia($doc,$guia)
    {
        $detalle = DB::table('almacen.doc_com_det')
            ->select('doc_com_det.*')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','doc_com_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->where([['doc_com_det.id_doc','=',$doc],
                     ['guia_com.id_guia','=',$guia]])
            ->get()->toArray();

        foreach($detalle as $det){
            DB::table('almacen.doc_com_det')
            ->where('id_doc_det', $det->id_doc_det)
            ->update([ 'estado' => 2 ]);
        }

        $data = DB::table('almacen.doc_com_guia')
            ->where([['id_doc_com','=',$doc],['id_guia_com','=',$guia]])
            ->update(['estado' => 2]);

        return response()->json($data);
    }
    public function anular_guiaven($doc,$guia)
    {
        $detalle = DB::table('almacen.doc_ven_det')
            ->select('doc_ven_det.*')
            ->join('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','doc_ven_det.id_guia_ven_det')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->where([['doc_ven_det.id_doc','=',$doc],
                     ['guia_ven.id_guia_ven','=',$guia]])
            ->get()->toArray();

        foreach($detalle as $det){
            DB::table('almacen.doc_ven_det')
            ->where('id_doc_det', $det->id_doc_det)
            ->update([ 'estado' => 2 ]);
        }

        $data = DB::table('almacen.doc_ven_guia')
            ->where([['id_doc_ven','=',$doc],['id_guia_ven','=',$guia]])
            ->update(['estado' => 2]);

        return response()->json($data);
    }
    public function listar_requerimientos(){
        $data = DB::table('almacen.alm_req')
            ->select('alm_req.*','proy_proyecto.descripcion as proy_descripcion',
            'adm_area.descripcion as area_descripcion',
            'adm_prioridad.descripcion as des_prioridad','adm_grupo.descripcion as des_grupo',
            DB::raw("concat(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as responsable"))
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','alm_req.id_usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->join('administracion.adm_prioridad','adm_prioridad.id_prioridad','=','alm_req.id_prioridad')
            ->join('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->leftjoin('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','alm_req.id_proyecto')
            ->leftjoin('administracion.adm_area','adm_area.id_area','=','alm_req.id_area')
            ->where([['alm_req.estado','=',1],
                    ['alm_req.id_tipo_requerimiento','=',1]])
            ->get();
        // $i = 1;
        // $html = '';

        // foreach($data as $reg){
        //     $html .= '
        //     <tr id="req-'.$reg->id_requerimiento.'">
        //         <td>
        //             <input type="checkbox" class="flat-red">
        //         </td>
        //         <td>'.$i.'</td>
        //         <td>'.$reg->codigo.'</td>
        //         <td>'.$reg->fecha_requerimiento.'</td>
        //         <td>'.$reg->responsable.'</td>
        //         <td>'.$reg->concepto.'</td>';
        //         if ($reg->id_proyecto !== null){
        //             $html.='<td>'.$reg->proy_descripcion.'</td>';
        //         } else {
        //             $html.='<td>'.$reg->area_descripcion.'</td>';
        //         }
        //         $html.='<td><i class="fas fa-search-plus icon-tabla blue"></i></td>
        //     </tr>
        //     ';
        //     $i++;
        // }
        $output['data']=$data;
        return response()->json($output);
    }
    public function listar_items_req($id){
        $data = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_und_medida.abreviatura','alm_ubi_posicion.codigo as cod_posicion')
            ->join('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_prod_ubi','alm_prod_ubi.id_producto','=','alm_prod.id_producto')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
            ->where('alm_det_req.id_requerimiento',$id)
            ->get();
        // $i = 1;
        // $html = '';

        // foreach($data as $reg){
        //     $html .= '
        //     <tr id="det-'.$reg->id_detalle_requerimiento.'">
        //         <td>
        //             <input type="checkbox" class="flat-red">
        //         </td>
        //         <td>'.$i.'</td>
        //         <td>'.$reg->codigo.'</td>
        //         <td>'.$reg->descripcion.'</td>
        //         <td>'.$reg->cod_posicion.'</td>
        //         <td>'.$reg->cantidad.'</td>
        //         <td>'.$reg->abreviatura.'</td>
        //         <td>'.$reg->partida.'</td>
        //     </tr>
        //     ';
        //     $i++;
        // }
        // return json_encode($html);
        $output['data']=$data;
        return response()->json($output);
    }
    public function id_producto($id_item){
        $item = DB::table('almacen.alm_item')
        ->where('id_item', $id_item)
        ->first();
        return $item->id_producto;
    }
    public function generar_salida(Request $request){
        
        $fecha = date('Y-m-d H:i:s');
        $fecha_emision = date('Y-m-d');
        $codigo = $this->nextMovimiento(2,$fecha_emision,1);
        
        $id_salida = DB::table('almacen.mov_alm')->insertGetId(
            [
                'id_almacen' => 1,
                'id_tp_mov' => 2,//Salidas
                'codigo' => $codigo,
                'fecha_emision' => $fecha_emision,
                'id_guia_ven' => $request->id_guia,
                'id_req' => $request->id_req,
                'revisado' => 0,
                'usuario' => 3,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_mov_alm'
            );

        $det = $request->detalle;
        $array = json_decode($det, true);
        $count = count($array);

        if ($count > 0){
            for ($i=0; $i<$count; $i++){
                $id_prod = $this->id_producto($array[$i]['id_item']);

                $id_pos = DB::table('almacen.alm_prod_ubi')
                    ->where([['id_producto','=',$id_prod]])
                    ->first();

                //traer stockActual
                $saldo = $this->saldo_actual($id_prod, $id_pos->id_posicion);
                $costo = $this->costo_promedio($id_prod, $id_pos->id_posicion);

                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                [
                    'id_mov_alm' => $id_salida,
                    'id_producto' => $id_prod,
                    'id_posicion' => $id_pos->id_posicion,
                    'cantidad' => $array[$i]['cantidad'],
                    'valorizacion' => ($costo * $array[$i]['cantidad']),
                    'usuario' => 3,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm_det'
                );

                if ($id_pos->id_posicion !== null){                
                    DB::table('almacen.alm_prod_ubi')
                    ->where('id_prod_ubi',$id_pos->id_prod_ubi)
                    ->update([  'stock' => $saldo,
                                'costo_promedio'=>$costo
                            ]);
                }
            }
        }
        DB::table('almacen.guia_ven')
            ->where('id_guia',$id_guia)->update(['estado'=>9]);//Procesado

        return response()->json($id_salida);
    }

    // public function generar_salida(Request $request){
    //     try
    //     {
    //         DB::beginTransaction();
    //         $mov = new Movimiento;
    //         $mov->id_almacen = 1;
    //         $mov->codigo = $this->nextMovimiento(3,7,date('Y-m-d'),1);
    //         $mov->id_req = $request->id_req;
    //         $mov->id_guia = $request->id_guia;
    //         $mov->id_tp_mov = 7;
    //         $mov->fecha_emision = date('Y-m-d');
    //         $mov->usuario = 3;
    //         $mov->estado = 1;
    //         $mov->fecha_registro = date('Y-m-d H:i:s');
    //         $mov->save();
    //         $id = $mov['id_mov_alm'];

    //         $det = $request->detalle;
    //         $array = json_decode($det, true);
    //         $count = count($array);

    //         if ($count > 0){
    //             for ($i=0; $i<$count; $i++){
    //                 $id_prod = $this->id_producto($array[$i]['id_item']);
    //                 $d = new MovDetalle;

    //                 $d->id_mov_alm = $id;
    //                 $d->id_producto = $id_prod;
    //                 $d->cantidad = $array[$i]['cantidad'];
    //                 $d->valorizacion = $array[$i]['precio_referencial'];
    //                 $d->usuario = 3;
    //                 $d->estado = 1;
    //                 $d->fecha_registro = date('Y-m-d H:i:s');
    //                 $d->save();
    //             }
    //         }
    //         DB::commit();
    //         return response()->json($id);
    //     } catch (\Exception $e)
    //     {
    //         dd($e->getMessage());
    //         DB::rollback();
    //         return response()->json('Lo sentimos ha ocurrido un error');
    //     }
    // }
    public function id_salida($id_guia){
        $ing = DB::table('almacen.mov_alm')
        ->where([['mov_alm.id_guia_ven','=',$id_guia],
                ['mov_alm.estado','=',1]])
        ->first();
        return response()->json($ing->id_mov_alm);
    }
    public function imprimir_salida($id_sal){
        $id = $this->decode5t($id_sal);
        $salida = DB::table('almacen.mov_alm')
            ->select('mov_alm.*','alm_almacen.descripcion as des_almacen',
            'alm_req.codigo as cod_req','alm_req.fecha_requerimiento',
            'alm_req.concepto','adm_grupo.descripcion as grupo_descripcion',
            'sis_usua.usuario as nom_usuario','tp_ope.cod_sunat','tp_ope.descripcion as ope_descripcion',
            'proy_proyecto.descripcion as proy_descripcion','proy_proyecto.codigo as cod_proyecto',
            DB::raw("CONCAT('GR-',guia_ven.serie,'-',guia_ven.numero) as guia"),
            'guia_motivo.descripcion as motivo_descripcion',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as persona"))
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','mov_alm.id_req')
            ->leftjoin('administracion.adm_grupo','adm_grupo.id_grupo','=','alm_req.id_grupo')
            ->leftjoin('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','alm_req.id_proyecto')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_ven.id_operacion')
            ->leftjoin('almacen.guia_motivo','guia_motivo.id_motivo','=','guia_ven.id_motivo')
            // ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            // ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('mov_alm.id_mov_alm',$id)
            ->first();

        $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->where([['mov_alm_det.id_mov_alm','=',$id],['mov_alm_det.estado','=',1]])
            ->get();

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:12px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                </style>
            </head>
            <body>
                <p style="text-align:right;font-size:14px;margin:0px;"><strong>N° '.$salida->codigo.'</strong></p>
                <p style="text-align:right;font-size:12px;margin:0px;">Fecha de Salida: '.$salida->fecha_emision.'</p>
                <h3 style="margin:0px;"><center>SALIDA DE ALMACÉN</center></h3>
                <h5><center>'.$salida->id_almacen.' - '.$salida->des_almacen.'</center></h5>
                
                <table border="0">';
                if (isset($salida->cod_req)){
                    $html.='
                    <tr>
                        <td width=120px>Requerimiento</td>
                        <td width=10px>:</td>
                        <td width=300px>'.$salida->cod_req.'</td>
                        <td width=120px>Fecha de Req.</td>
                        <td width=10px>:</td>
                        <td>'.$salida->fecha_requerimiento.'</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Concepto</td>
                        <td width=10px>:</td>
                        <td class="verticalTop">'.$salida->concepto.'</td>
                        <td>Grupo</td>
                        <td width=10px>:</td>
                        <td>'.$salida->grupo_descripcion.'</td>
                    </tr>                
                    <tr>
                        <td>Proyecto</td>
                        <td>:</td>
                        <td colSpan="4">'.$salida->cod_proyecto.' - '.$salida->proy_descripcion.'</td>
                    </tr>
                    ';
                }
                if (isset($salida->guia)){
                    $html.='
                    <tr>
                        <td class="subtitle">Guía de Venta</td>
                        <td>:</td>
                        <td class="verticalTop">'.$salida->guia.'</td>
                        <td>Motivo del Traslado</td>
                        <td>:</td>
                        <td> '.$salida->motivo_descripcion.'</td>
                    </tr>
                    ';
                }
                    $html.='
                    <tr>
                        <td class="subtitle">Tipo Movimiento</td>
                        <td>:</td>
                        <td class="verticalTop">'.$salida->cod_sunat.' '.$salida->ope_descripcion.'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Generado por</td>
                        <td>:</td>
                        <td>'.$salida->persona.'</td>
                    </tr>
                </table>
                <br/>
                <table id="detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código</th>
                            <th width=45% >Descripción</th>
                            <th>Posición</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>Valor.</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $i = 1;
                    foreach($detalle as $det){
                        $html.='
                        <tr>
                            <td class="right">'.$i.'</td>
                            <td>'.$det->codigo.'</td>
                            <td>'.$det->descripcion.'</td>
                            <td>'.$det->cod_posicion.'</td>
                            <td class="right">'.$det->cantidad.'</td>
                            <td>'.$det->abreviatura.'</td>
                            <td class="right">'.$det->valorizacion.'</td>
                        </tr>';
                        $i++;
                    }
                    $html.='</tbody>
                </table>
                <p style="text-align:right;font-size:11px;">Elaborado por: '.$salida->nom_usuario.' '.$salida->fecha_registro.'</p>

            </body>
        </html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->stream();
        return $pdf->download('salida.pdf');
        // return response()->json(['salida'=>$salida,'detalle'=>$detalle]);
    }
    public function guardar_guia_ven(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_ven')->insertGetId(
            [
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_empresa' => $request->id_empresa,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                // 'id_guia_clas' => $request->id_guia_clas,
                // 'id_guia_cond' => $request->id_guia_cond,
                'id_cliente' => $request->id_cliente,
                'usuario' => $request->usuario,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_guia_ven'
            );
        
            $det = $request->detalle;
            $array = json_decode($det, true);
            $count = count($array);

            if ($count > 0){
                for ($i=0; $i<$count; $i++){
                    $id_prod = $this->id_producto($array[$i]['id_item']);
                    
                    $data = DB::table('almacen.guia_ven_det')->insertGetId(
                        [
                            'id_guia_ven' => $id_guia,
                            'id_producto' => $id_prod,
                            // 'id_posicion' => $array[$i]['id_posicion'],
                            'cantidad' => $array[$i]['cantidad'],
                            // 'id_unid_med' => $array[$i]['id_unid_med'],
                            // 'id_oc_det' => $array[$i]['id_unid_med'],
                            // 'unitario' => $array[$i]['unitario'],
                            // 'total' => $array[$i]['total'],
                            // 'usuario' => $request->usuario,
                            // 'estado' => 1,
                            // 'fecha_registro' => $fecha
                        ],
                            'id_guia_ven_det'
                        );
                }
            }
        // return response()->json(["id_guia"=>$id_guia,"id_proveedor"=>$request->id_proveedor]);
        return response()->json($id_guia);
    }
    public function kardex_general($almacenes, $finicio, $ffin){
        $alm_array = explode(',',$almacenes);
        
        $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision','mov_alm.id_tp_mov',
            'alm_prod.descripcion as prod_descripcion','alm_prod.codigo as prod_codigo',
            'alm_und_medida.abreviatura','alm_ubi_posicion.codigo as posicion',
            'tp_ope_com.cod_sunat as cod_sunat_com','tp_ope_com.descripcion as tp_com_descripcion',
            'tp_ope_ven.cod_sunat as cod_sunat_ven','tp_ope_ven.descripcion as tp_ven_descripcion',
            DB::raw("CONCAT('GR-',guia_com.serie,'-',guia_com.numero) as guia_com"),
            DB::raw("CONCAT('GR-',guia_ven.serie,'-',guia_ven.numero) as guia_ven"),
            DB::raw("CONCAT(tp_doc_com.abreviatura,'-',doc_com.serie,'-',doc_com.numero) as doc_com"),
            DB::raw("CONCAT(tp_doc_ven.abreviatura,'-',doc_ven.serie,'-',doc_ven.numero) as doc_ven"),
            'alm_req.codigo as cod_req','guia_com.id_guia','guia_ven.id_guia_ven',
            'doc_com.id_doc_com','doc_ven.id_doc_ven')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->leftjoin('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_ope as tp_ope_com','tp_ope_com.id_operacion','=','guia_com.id_operacion')
            ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_com','tp_doc_com.id_tp_doc','=','doc_com.id_tp_doc')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_ope as tp_ope_ven','tp_ope_ven.id_operacion','=','guia_ven.id_operacion')
            ->leftjoin('almacen.doc_ven','doc_ven.id_doc_ven','=','mov_alm.id_doc_ven')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_ven','tp_doc_ven.id_tp_doc','=','doc_ven.id_tp_doc')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','mov_alm.id_req')
            ->where([['mov_alm.fecha_emision','>=',$finicio],
                    ['mov_alm.fecha_emision','<=',$ffin],
                    ['mov_alm_det.estado','=',1]])
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->orderBy('alm_prod.codigo','asc')
            ->orderBy('mov_alm.fecha_emision','asc')
            ->orderBy('mov_alm.id_tp_mov','asc')
            ->get();

        $saldo = 0;
        $saldo_valor = 0;
        $movimientos = [];
        $codigo = '';

        foreach($data as $d){
            if ($d->prod_codigo !== $codigo){
                $saldo = 0;
                $saldo_valor = 0;
            }
            if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0){
                $saldo += $d->cantidad;
                $saldo_valor += $d->valorizacion;
            } 
            else if ($d->id_tp_mov == 2){
                $saldo -= $d->cantidad;
                $saldo_valor -= $d->valorizacion;
            }
            $codigo = $d->prod_codigo;
            $nuevo = [
                "id_mov_alm_det"=>$d->id_mov_alm_det,
                "prod_codigo"=>$d->prod_codigo,
                "prod_descripcion"=>$d->prod_descripcion,
                "fecha_emision"=>$d->fecha_emision,
                "posicion"=>$d->posicion,
                "abreviatura"=>$d->abreviatura,
                "tipo"=>$d->id_tp_mov,
                "cantidad"=>$d->cantidad,
                "saldo"=>$saldo,
                "valorizacion"=>$d->valorizacion,
                "saldo_valor"=>$saldo_valor,
                "cod_sunat_com"=>$d->cod_sunat_com,
                "cod_sunat_ven"=>$d->cod_sunat_ven,
                "tp_com_descripcion"=>$d->tp_com_descripcion,
                "tp_ven_descripcion"=>$d->tp_ven_descripcion,
                "id_guia_com"=>$d->id_guia,
                "id_guia_ven"=>$d->id_guia_ven,
                "id_doc_com"=>$d->id_doc_com,
                "id_doc_ven"=>$d->id_doc_ven,
                "doc_com"=>$d->doc_com,
                "doc_ven"=>$d->doc_ven,
                "guia_com"=>$d->guia_com,
                "guia_ven"=>$d->guia_ven,
                "doc_com"=>$d->doc_com,
                "doc_ven"=>$d->doc_ven,
                "req"=>$d->cod_req,
            ];
            array_push($movimientos, $nuevo);
        }
        return response()->json($movimientos);
    }
    public function saldos($almacen){
        $data = DB::table('almacen.alm_prod_ubi')
        ->select('alm_prod_ubi.*','alm_prod.codigo','alm_prod.descripcion','alm_ubi_posicion.codigo as cod_posicion',
        'alm_und_medida.abreviatura')
        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->where([['alm_almacen.id_almacen','=',$almacen],
                ['alm_prod_ubi.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    public function saldo_actual($id_producto, $id_posicion){
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as ingresos"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm_det.id_posicion','=',$id_posicion],
                     ['id_tp_mov','<=',1],//ingreso o carga inicial
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.cantidad) as salidas"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm_det.id_posicion','=',$id_posicion],
                     ['id_tp_mov','=',2],//salida
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $saldo = 0;
        if ($ing->ingresos !== null) $saldo += $ing->ingresos;
        if ($sal->salidas !== null) $saldo -= $sal->salidas;

        return $saldo;
    }
    public function costo_promedio($id_producto, $id_posicion){
        $ing = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as ingresos"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm_det.id_posicion','=',$id_posicion],
                     ['id_tp_mov','<=',1],//ingreso o carga inicial
                     ['mov_alm_det.estado','=',1]])
            ->first();

        $sal = DB::table('almacen.mov_alm_det')
            ->select(DB::raw("SUM(mov_alm_det.valorizacion) as salidas"))
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->where([['mov_alm_det.id_producto','=',$id_producto],
                     ['mov_alm_det.id_posicion','=',$id_posicion],
                     ['id_tp_mov','=',2],//salida
                     ['mov_alm_det.estado','=',1]])
            ->first();
        
        $valorizacion = 0;
        if ($ing->ingresos !== null) $valorizacion += $ing->ingresos;
        if ($sal->salidas !== null) $valorizacion -= $sal->salidas;

        $saldo = $this->saldo_actual($id_producto, $id_posicion);

        return ($saldo > 0 ? $valorizacion/$saldo : 0);
    }
    /**Guia de Venta */
    public function listar_guias_venta(){
        $data = DB::table('almacen.guia_ven')
            ->select('guia_ven.*','adm_contri.razon_social','adm_estado_doc.estado_doc',
            'sis_usua.usuario as nombre_usuario')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','guia_ven.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','guia_ven.usuario')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_guia_venta($id){
        $data = DB::table('almacen.guia_ven')
            ->select('guia_ven.*','cliente.razon_social as cliente_razon_social',
            'adm_contri.razon_social','adm_estado_doc.estado_doc',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','guia_ven.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->join('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->join('contabilidad.adm_contri as cliente','cliente.id_contribuyente','=','com_cliente.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','guia_ven.usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('id_guia_ven',$id)
            ->get();
        return response()->json($data);
    }
    public function guardar_guia_venta(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_guia = DB::table('almacen.guia_ven')->insertGetId(
            [
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_empresa' => $request->id_empresa,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                'id_operacion' => $request->id_operacion,
                'transportista' => $request->transportista,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'fecha_traslado' => $request->fecha_traslado,
                'placa' => $request->placa,
                'usuario' => 3,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_guia_ven'
            );
        // $output['data'] = 'id_guia'
        return response()->json(["id_guia_ven"=>$id_guia,"id_almacen"=>$request->id_almacen]);
    }
    public function update_guia_venta(Request $request)
    {
        $data = DB::table('almacen.guia_ven')
            ->where('id_guia_ven', $request->id_guia_ven)
            ->update([
                'id_tp_doc_almacen' => $request->id_tp_doc_almacen,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_empresa' => $request->id_empresa,
                'fecha_emision' => $request->fecha_emision,
                'fecha_almacen' => $request->fecha_almacen,
                'id_almacen' => $request->id_almacen,
                'id_motivo' => $request->id_motivo,
                'transportista' => $request->transportista,
                'tra_serie' => $request->tra_serie,
                'tra_numero' => $request->tra_numero,
                'punto_partida' => $request->punto_partida,
                'punto_llegada' => $request->punto_llegada,
                'fecha_traslado' => $request->fecha_traslado,
                'id_cliente' => $request->id_cliente,
                'placa' => $request->placa
            ]);
        return response()->json($data);
    }
    public function anular_guia_venta(Request $request, $id)
    {
        $data = DB::table('almacen.guia_ven')->where('id_guia_ven', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function listar_ing_det($id_doc, $tipo)
    {
        //Guia de Compra
        if ($tipo == 1){
            $det_ing = DB::table('almacen.mov_alm_det')
            ->where([['mov_alm.id_guia_com','=',$id_doc],['mov_alm_det.estado','=',1]])
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura',
            'guia_com.serie','guia_com.numero','mov_alm.codigo as cod_mov')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->get();

        //Requerimiento
        } else if ($tipo == 2){
            $det_ing = DB::table('almacen.mov_alm_det')
            ->where([['alm_req.id_requerimiento','=',$id_doc],['mov_alm_det.estado','=',1]])
            ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
            'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura',
            'guia_com.serie','guia_com.numero','mov_alm.codigo as cod_mov')
            // ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
            ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
            ->leftjoin('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_valorizacion_cotizacion','=','log_det_ord_compra.id_valorizacion_cotizacion')
            ->leftjoin('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','log_valorizacion_cotizacion.id_detalle_requerimiento')
            ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->get();
            
        //Orden de Compra Cliente
        } else if ($tipo == 3){
            $det_ing = DB::table('almacen.mov_alm')
            ->where([['id_occ','=',$id_doc],['estado','=',1]])->get();
        }
        $html = '';
        
        if (isset($det_ing)){
            foreach($det_ing as $d){
                // $data = DB::table('almacen.mov_alm_det')
                //     ->select('mov_alm_det.*','alm_prod.codigo','alm_prod.descripcion',
                //     'alm_ubi_posicion.codigo as cod_posicion','alm_und_medida.abreviatura')
                //     ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
                //     ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
                //     ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
                //     ->where([['mov_alm_det.id_mov_alm','=',$i->id_mov_alm],
                //             ['mov_alm_det.estado','=',1]])
                //     ->get();
        
                // foreach($data as $d){
                    $html.='
                    <tr>
                        <td><input type="checkbox"></td>
                        <td hidden><input name="id" style="display:none;" value="'.$d->id_mov_alm_det.'"/></td>
                        <td>GR-'.$d->serie.'-'.$d->numero.'</td>
                        <td>'.$d->cod_mov.'</td>
                        <td>'.$d->codigo.'</td>
                        <td>'.$d->descripcion.'</td>
                        <td>'.$d->cod_posicion.'</td>
                        <td>'.$d->cantidad.'</td>
                        <td>'.$d->abreviatura.'</td>
                        <td>'.strval($d->valorizacion / $d->cantidad).'</td>
                        <td>'.$d->valorizacion.'</td>
                    </tr>
                    ';
                // }
            }
        }
        return json_encode($html);
        // return response()->json($det_ing);
    }
    public function guardar_detalle_ing(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_ing_det = explode(',',$request->id_mov_alm_det);
        $count = count($id_ing_det);
        $ing_det = '';

        $id_guia_ven = $request->id_guia_ven;

        for ($i=0; $i<$count; $i++){
            $id = $id_ing_det[$i];

            $ing_det = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','alm_prod.id_unidad_medida')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->where([['mov_alm_det.id_mov_alm_det','=',$id]])->first();
            
            $data = DB::table('almacen.guia_ven_det')->insertGetId(
            [
                'id_guia_ven' => $id_guia_ven,
                'id_producto' => $ing_det->id_producto,
                'id_posicion' => $ing_det->id_posicion,
                'cantidad'    => $ing_det->cantidad,
                'id_unid_med' => $ing_det->id_unidad_medida,
                'id_ing_det'  => $id,
                // 'usuario' => 3,
                'estado'      => 1,
                'fecha_registro' => $fecha
            ],
                'id_guia_ven_det'
            );
        }

        // $id_oc = DB::table('logistica.log_det_ord_compra')
        //     ->where('id_detalle_orden', $oc[0])->first();

        // $exist = DB::table('almacen.guia_com_oc')
        //     ->where([['id_oc','=', $id_oc->id_orden_compra],
        //             ['id_guia_com','=', $request->id_guia_com],
        //             ['estado','=', 1]])->first();

        // if (empty($exist)){
        //     $this->guardar_oc($request->id_guia_com, $id_oc->id_orden_compra);
        // }
        return response()->json($data);
    }
    public function listar_guia_ven_det($id_guia){
        $data = DB::table('almacen.guia_ven_det')
        ->select('guia_ven_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_ubi_posicion.codigo as cod_posicion','mov_alm.codigo as cod_mov',
        'alm_und_medida.abreviatura','mov_alm_det.id_guia_com_det')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
        ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_ven_det.id_posicion')
        ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_mov_alm_det','=','guia_ven_det.id_ing_det')
        ->leftjoin('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        // ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
        ->where([['guia_ven_det.id_guia_ven','=',$id_guia],
                ['guia_ven_det.estado','=',1]])
        ->get();
        $html = '';
        foreach($data as $d){
            $html.='
            <tr id="reg-'.$d->id_guia_ven_det.'">
                <td>'.$d->cod_mov.'</td>
                <td>'.$d->codigo.'</td>
                <td>'.$d->descripcion.'</td>
                <td>
                    <select class="input-data" name="id_posicion" disabled="true">
                        <option value="0">Elija una opción</option>';
                        $pos = $this->mostrar_posiciones_cbo();
                        foreach ($pos as $row) {
                            if ($d->id_posicion == $row->id_posicion){
                                $html.='<option value="'.$row->id_posicion.'" selected>'.$row->codigo.'</option>';
                            } else {
                                $html.='<option value="'.$row->id_posicion.'">'.$row->codigo.'</option>';
                            }
                        }
                    $html.='</select>
                </td>
                <td><input type="number" name="cantidad" value="'.$d->cantidad.'" class="input-data right" disabled/></td>
                <td>'.$d->abreviatura.'</td>
                <td style="display:flex;">
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" onClick="agrega_series('.$d->id_guia_com_det.','.$d->codigo.','.$d->cantidad.','.$d->id_guia_ven_det.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('.$d->id_guia_ven_det.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar Item" onClick="update_detalle('.$d->id_guia_ven_det.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('.$d->id_guia_ven_det.');"></i>
                </td>
            </tr>
            ';
        }
        return json_encode($html);
    }
    public function guardar_guia_ven_detalle(Request $request)
    {
        $data = DB::table('almacen.guia_ven_det')->insertGetId([
                'id_guia_ven' => $request->id_guia_ven,
                'id_producto' => $request->id_producto,
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                'id_unid_med' => $request->id_unid_med,
                // 'unitario' => $request->unitario,
                // 'total' => $request->total,
                // 'usuario' => $request->usuario,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_guia_ven_det'
            );
        return response()->json($data);
    }
    public function update_guia_ven_detalle(Request $request)
    {
        $data = DB::table('almacen.guia_ven_det')
            ->where('id_guia_ven_det', $request->id_guia_ven_det)
            ->update([
                'id_posicion' => $request->id_posicion,
                'cantidad' => $request->cantidad,
                // 'unitario' => $request->unitario,
                // 'total' => $request->total,
                // 'id_unid_med' => $request->id_unid_med
            ]);
        return response()->json($data);
    }
    public function anular_guia_ven_detalle(Request $request, $id)
    {
        $data = DB::table('almacen.guia_ven_det')->where('id_guia_ven_det', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function generar_salida_guia($id_guia, $id_usuario){
        
        $fecha = date('Y-m-d H:i:s');
        $fecha_emision = date('Y-m-d');

        $guia = DB::table('almacen.guia_ven')
            ->where('id_guia_ven',$id_guia)->first();
        
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*','alm_prod.codigo','alm_prod.descripcion')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','guia_ven_det.id_producto')
            ->where([['id_guia_ven','=',$id_guia],
                    ['guia_ven_det.estado','=',1]])->get()->toArray();
        
        $msj = 'No hay saldo en almacén de los siguiente(s) producto(s):';
        $sin_saldo = 0;

        foreach($detalle as $det){
            $saldo = $this->saldo_producto($guia->id_almacen,$det->id_producto,$guia->fecha_almacen);
            if ($saldo['saldo'] < $det->cantidad){
                $msj .= $det->codigo.' '.$det->descripcion.' = '.$saldo['saldo'];
                $sin_saldo++;
            }
        }

        if ($sin_saldo > 0){
            return response()->json($msj);
        } 
        else {
            $codigo = $this->nextMovimiento(2,
                            $guia->fecha_almacen,
                            $guia->id_almacen);
            
            $id_salida = DB::table('almacen.mov_alm')->insertGetId(
                [
                    'id_almacen' => $guia->id_almacen,
                    'id_tp_mov' => 2,//salidas
                    'codigo' => $codigo,
                    'fecha_emision' => $guia->fecha_almacen,
                    'id_guia_ven' => $id_guia,
                    'revisado' => 0,
                    'usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_registro' => $fecha,
                ],
                    'id_mov_alm'
                );
            $nuevo_detalle = [];
            $cant = 0;
    
            // foreach ($detalle as $det){
            //     $exist = false;
            //     foreach ($nuevo_detalle as $nue => $value){
            //         if ($det->id_producto == $value['id_producto']){
            //             $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
            //             // $nuevo_detalle[$nue]['valorizacion'] = floatval($value['valorizacion']) + floatval($det->total);
            //             $exist = true;
            //         }
            //     }
            //     if ($exist === false){
            //         $nuevo = [
            //             'id_producto' => $det->id_producto,
            //             'id_posicion' => $det->id_posicion,
            //             // 'id_oc_det' => (isset($det->id_oc_det)) ? $det->id_oc_det : 0,
            //             'cantidad' => floatval($det->cantidad)
            //             // 'valorizacion' => floatval($det->total)
            //             ];
            //         array_push($nuevo_detalle, $nuevo);
            //     }
            // }
    
            foreach ($detalle as $det){
                $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);
                $valorizacion = $costo * $det->cantidad;
    
                $id_det = DB::table('almacen.mov_alm_det')->insertGetId(
                    [
                        'id_mov_alm' => $id_salida,
                        'id_producto' => $det->id_producto,
                        'id_posicion' => $det->id_posicion,
                        'cantidad' => $det->cantidad,
                        'valorizacion' => $valorizacion,
                        'id_guia_ven_det' => $det->id_guia_ven_det,
                        'usuario' => $id_usuario,
                        'estado' => 1,
                        'fecha_registro' => $fecha,
                    ],
                        'id_mov_alm_det'
                    );
                    
                if ($det->id_posicion !== null){
                    $ubi = DB::table('almacen.alm_prod_ubi')
                        ->where([['id_producto','=',$det->id_producto],
                                ['id_posicion','=',$det->id_posicion]])
                        ->first();
                    //traer stockActual
                    $saldo = $this->saldo_actual($det->id_producto, $det->id_posicion);
                    $costo = $this->costo_promedio($det->id_producto, $det->id_posicion);
    
                    if (!isset($ubi->id_posicion)){
                        DB::table('almacen.alm_prod_ubi')->insert([
                            'id_producto' => $det->id_producto,
                            'id_posicion' => $det->id_posicion,
                            'stock' => $saldo,
                            'costo_promedio' => $costo,
                            'estado' => 1,
                            'fecha_registro' => $fecha
                        ]);
                    } else {
                        DB::table('almacen.alm_prod_ubi')
                        ->where('id_prod_ubi',$ubi->id_prod_ubi)
                        ->update([  'stock' => $saldo,
                                    'costo_promedio' => $costo
                                ]);
                    }
                }
            }
            // cambiar estado guiaven
            DB::table('almacen.guia_ven')
                ->where('id_guia_ven',$id_guia)->update(['estado'=>9]);//Procesado

            return response()->json($id_salida);
        }
    }

    /**Comprobante de Venta */
    public function listar_docs_venta(){
        $data = DB::table('almacen.doc_ven')
        ->select('doc_ven.*','adm_contri.razon_social','adm_estado_doc.estado_doc')
        ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','doc_ven.id_empresa')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
        ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_ven.estado')
        // ->where('doc_ven.estado',1)
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_doc_venta($id){
        $doc = DB::table('almacen.doc_ven')
            ->select('doc_ven.*','adm_estado_doc.estado_doc',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_usuario"))
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','doc_ven.estado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','doc_ven.usuario')
            ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
            ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
            ->where('doc_ven.id_doc_ven',$id)
            ->get();
        return response()->json(['doc'=>$doc]);
    }
    public function guardar_doc_venta(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_doc = DB::table('almacen.doc_ven')->insertGetId(
            [
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_empresa' => $request->id_empresa,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'id_condicion' => $request->id_condicion,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => $request->sub_total,
                'total_descuento' => $request->total_descuento,
                'porcen_descuento' => $request->porcen_descuento,
                'total' => $request->total,
                'total_igv' => $request->total_igv,
                'total_ant_igv' => $request->total_ant_igv,
                'total_a_pagar' => $request->total_a_pagar,
                'usuario' => 3,
                'estado' => 1,
                'fecha_registro' => $fecha,
            ],
                'id_doc_ven'
            );
        return response()->json(["id_doc"=>$id_doc,"id_empresa"=>$request->id_empresa]);
    }
    public function update_doc_venta(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_ven')
            ->where('id_doc_ven',$request->id_doc_ven)
            ->update([
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tp_doc' => $request->id_tp_doc,
                'id_empresa' => $request->id_empresa,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vcmto' => $request->fecha_vcmto,
                'id_condicion' => $request->id_condicion,
                'moneda' => $request->moneda,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => $request->sub_total,
                'total_descuento' => $request->total_descuento,
                'porcen_descuento' => $request->porcen_descuento,
                'total' => $request->total,
                'total_igv' => $request->total_igv,
                'total_ant_igv' => $request->total_ant_igv,
                'total_a_pagar' => $request->total_a_pagar,
            ]);
        return response()->json($data);
    }
    public function anular_doc_venta($id)
    {
        $data = DB::table('almacen.doc_ven')->where('id_doc_ven', $id)
            ->update([ 'estado' => 7 ]);
        return response()->json($data);
    }
    public function update_docven_detalle(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('almacen.doc_ven_det')
            ->where('id_doc_det', $request->id_doc_det)
            ->update([
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'porcen_dscto' => $request->porcen_dscto,
                'total_dscto' => $request->total_dscto,
                'precio_total' => $request->precio_total,
            ]);
        return response()->json($data);
    }
    public function anular_docven_detalle($id_doc_det)
    {
        $data = DB::table('almacen.doc_ven_det')
            ->where('id_doc_det', $id_doc_det)
            ->update(['estado' => 7]);
        return response()->json($data);
    }
    public function listar_guias_emp($id_empresa){
        $data = DB::table('almacen.guia_ven')
            ->select('guia_ven.*','adm_contri.razon_social','adm_estado_doc.estado_doc')
            ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','guia_ven.id_empresa')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
            ->join('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->where('guia_ven.id_empresa',$id_empresa)
            ->get();
        return response()->json($data);
    }
    public function docven($id_guia, $id_doc){
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as precio_unitario'))//jalar el precio unitario del ingreso
            ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_mov_alm','=','guia_ven_det.id_ing_det')
            ->where([['guia_ven_det.id_guia_ven','=',$id_guia],
                    ['guia_ven_det.estado','=',1 ]])
            ->get();
        return $detalle;
    }
    public function guardar_docven_items_guia($id_guia, $id_doc){
        $fecha = date('Y-m-d H:i:s');
        $detalle = DB::table('almacen.guia_ven_det')
            ->select('guia_ven_det.*', DB::raw('(mov_alm_det.valorizacion / mov_alm_det.cantidad) as precio_unitario'))//jalar el precio unitario del ingreso
            ->leftjoin('almacen.mov_alm_det','mov_alm_det.id_mov_alm','=','guia_ven_det.id_ing_det')
            ->where([['guia_ven_det.id_guia_ven','=',$id_guia],
                    ['guia_ven_det.estado','=',1 ]])
            ->get();
        $nuevo_detalle = [];
        $cant = 0;
    
        foreach ($detalle as $det){
            $exist = false;
            foreach ($nuevo_detalle as $nue => $value){
                if ($det->id_producto == $value['id_producto'] && $det->id_guia_ven == $value['id_guia_ven']){
                    $nuevo_detalle[$nue]['cantidad'] = floatval($value['cantidad']) + floatval($det->cantidad);
                    $nuevo_detalle[$nue]['precio_unitario'] = floatval($value['precio_unitario']) + floatval($det->precio_unitario);
                    // $nuevo_detalle[$nue]['precio_total'] = floatval($value['precio_total']) + floatval($det->precio_total);
                    $exist = true;
                }
            }
            if ($exist === false){
                $nuevo = [
                    'id_guia_ven_det' => $det->id_guia_ven_det,
                    'id_guia_ven' => $det->id_guia_ven,
                    'id_producto' => $det->id_producto,
                    'id_unid_med' => $det->id_unid_med,
                    'cantidad' => floatval($det->cantidad),
                    'precio_unitario' => floatval($det->precio_unitario),
                    'precio_total' => floatval($det->cantidad * $det->precio_unitario)
                    ];
                array_push($nuevo_detalle, $nuevo);
            }
        }
        foreach($nuevo_detalle as $det){
            $item = DB::table('almacen.alm_item')
                ->where('id_producto',$det['id_producto'])
                ->first();

            $id_det = DB::table('almacen.doc_ven_det')->insert(
                [
                    'id_doc'=>$id_doc,
                    'id_item'=>$item->id_item,
                    'cantidad'=>$det['cantidad'],
                    'id_unid_med'=>$det['id_unid_med'],
                    'precio_unitario'=>$det['precio_unitario'],
                    'sub_total'=>$det['precio_total'],
                    'porcen_dscto'=>0,
                    'total_dscto'=>0,
                    'precio_total'=>$det['precio_total'],
                    'id_guia_ven_det'=>$det['id_guia_ven_det'],
                    'estado'=>1,
                    'fecha_registro'=>$fecha
                ]);
        }
        $guia = DB::table('almacen.doc_ven_guia')->insert(
            [
                'id_doc_ven'=>$id_doc,
                'id_guia_ven'=>$id_guia,
                'estado'=>1,
                'fecha_registro'=>$fecha
            ]);
        $salida = DB::table('almacen.mov_alm')
            ->where('mov_alm.id_guia_ven',$id_guia)
            ->first();

        if (isset($salida->id_mov_alm)){
            DB::table('almacen.mov_alm')
                ->where('id_mov_alm',$salida->id_mov_alm)
                ->update(['id_doc_ven'=>$id_doc]);
        }

        return response()->json($guia);
    }
    public function update_series(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $serie = explode(',',$request->series);
        $count = count($serie);

        for ($i=0; $i<$count; $i++){
            $id_prod_serie = $serie[$i];
            if ($id_prod_serie !== null){
                $update = DB::table('almacen.alm_prod_serie')
                ->where('id_prod_serie',$id_prod_serie)
                ->update(['id_guia_ven_det' => $request->id_guia_ven_det]);
            }
        }
        return response()->json($update);
    }
    public function saldo_producto($id_almacen,$id_producto,$fecha){
        $saldo = 0;

        $ingresos = DB::table('almacen.mov_alm_det')
        ->select(DB::raw('SUM(mov_alm_det.cantidad) as cant_ingresos'),
                 DB::raw('SUM(mov_alm_det.valorizacion) as val_ingresos'))
        ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['mov_alm_det.id_producto','=',$id_producto],
                ['mov_alm_det.estado','=',1],
                ['mov_alm.fecha_emision','<=',$fecha],
                ['mov_alm.id_tp_mov','=',1]])
        ->first();

        $salidas = DB::table('almacen.mov_alm_det')
        ->select(DB::raw('SUM(mov_alm_det.cantidad) as cant_salidas'),
                 DB::raw('SUM(mov_alm_det.valorizacion) as val_salidas'))
        ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['mov_alm_det.id_producto','=',$id_producto],
                ['mov_alm_det.estado','=',1],
                ['mov_alm.fecha_emision','<=',$fecha],
                ['mov_alm.id_tp_mov','=',2]])
        ->first();

        $saldo = $ingresos->cant_ingresos - $salidas->cant_salidas;
        $valorizacion = $ingresos->val_ingresos - $salidas->val_salidas;

        return ['saldo'=>$saldo,'valorizacion'=>$valorizacion];
    }
    public function movimientos_producto($id_almacen,$id_producto,$finicio,$ffin){
        $detalle = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision','mov_alm.id_tp_mov as tipo',
            // 'alm_prod.descripcion as prod_descripcion','alm_prod.codigo as prod_codigo',
            // 'alm_und_medida.abreviatura','alm_ubi_posicion.codigo as posicion',
            // 'tp_mov.tp_mov','tp_mov.tipo',
            'guia_com.fecha_emision as guia_com_fecha',
            'guia_com.serie as guia_com_serie','guia_com.numero as guia_com_numero',
            'tp_doc_com.cod_sunat as cod_sunat_com','doc_com.serie as doc_com_serie','doc_com.numero as doc_com_numero',
            'doc_com.fecha_emision as doc_com_fecha','guia_ven.fecha_emision as guia_ven_fecha',
            'guia_ven.serie as guia_ven_serie','guia_ven.numero as guia_ven_numero',
            'tp_doc_ven.cod_sunat as cod_sunat_ven','doc_ven.serie as doc_ven_serie','doc_ven.numero as doc_ven_numero',
            'doc_ven.fecha_emision as doc_ven_fecha','tp_op_com.cod_sunat as op_sunat_ing',
            'tp_op_ven.cod_sunat as op_sunat_sal','doc_com_sunat.cod_doc_sunat as doc_sunat_com',
            'doc_ven_sunat.cod_doc_sunat as doc_sunat_ven')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            // ->join('almacen.tp_mov','tp_mov.id_tp_mov','=','mov_alm.id_tp_mov')
            ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->leftjoin('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
            ->leftjoin('almacen.tp_doc_almacen as doc_com_sunat','doc_com_sunat.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_op_com','tp_op_com.id_operacion','=','guia_com.id_operacion')
            ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','mov_alm.id_doc_com')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_com','tp_doc_com.id_tp_doc','=','doc_com.id_tp_doc')
            ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
            ->leftjoin('almacen.tp_doc_almacen as doc_ven_sunat','doc_ven_sunat.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope as tp_op_ven','tp_op_ven.id_operacion','=','guia_ven.id_operacion')
            ->leftjoin('almacen.doc_ven','doc_ven.id_doc_ven','=','mov_alm.id_doc_ven')
            ->leftjoin('contabilidad.cont_tp_doc as tp_doc_ven','tp_doc_ven.id_tp_doc','=','doc_ven.id_tp_doc')
            ->leftjoin('almacen.alm_req','alm_req.id_requerimiento','=','mov_alm.id_req')
            ->where([['mov_alm.id_almacen','=',$id_almacen],
                    ['mov_alm_det.id_producto','=',$id_producto],
                    ['mov_alm.fecha_emision','>=',$finicio],
                    ['mov_alm.fecha_emision','<=',$ffin],
                    ['mov_alm_det.estado','=',1]])
            // ->orderBy('alm_prod.descripcion','asc')
            ->orderBy('mov_alm.fecha_emision','asc')
            ->orderBy('mov_alm.id_tp_mov','asc')
            ->get();
        return $detalle;
    }
    public function kardex_sunat($almacenes, $finicio, $ffin){
        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:12px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle thead tr td{
                    padding: 4px;
                    background-color: #ddd;
                }
                #detalle tbody tr td{
                    font-size:11px;
                    padding: 4px;
                }
                .right{
                    text-align: right;
                }
                .left{
                    text-align: left;
                }
                .sup{
                    vertical-align:top;
                }
                </style>
            </head>
            <body>
                <h3 style="margin:0px;"><center>REGISTRO DE INVENTARIO PERMANENTE VALORIZADO - DETALLE DEL INVENTARIO VALORIZADO</center></h3>';
                
                $alm_array = explode(',',$almacenes);
                $count = count($alm_array);
                $mes = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SETIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                
                $mes_inicio = $mes[(date('m', strtotime($finicio))*1)-1];
                $yyyy_inicio = date('Y',strtotime($finicio));
                $mes_fin = $mes[(date('m', strtotime($ffin))*1)-1];
                $yyyy_fin = date('Y',strtotime($ffin));

                for ($i=0; $i<$count; $i++){
                    $id_almacen = $alm_array[$i];
                    $alm = DB::table('almacen.alm_almacen')
                    ->select('alm_almacen.*','adm_contri.razon_social','adm_contri.nro_documento')
                    ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
                    ->join('administracion.adm_empresa','adm_empresa.id_empresa','=','sis_sede.id_empresa')
                    ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','adm_empresa.id_contribuyente')
                    ->where('id_almacen',$id_almacen)->first();

                    $html.='
                    <table id="detalle" border="0" class="table table-condensed table-bordered table-hover sortable" width="100%">
                    <thead>
                        <tr>
                            <th class="left">Periodo:</th>
                            <th class="left">'.$mes_inicio.' '.$yyyy_inicio.' - '.$mes_fin.' '.$yyyy_fin.'</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">R.U.C.:</th>
                            <th class="left">'.$alm->nro_documento.'</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Razon Social:</th>
                            <th class="left">'.$alm->razon_social.'</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Establecimiento:</th>
                            <th class="left">'.$alm->ubicacion.'</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <th class="left">Metodo Valuación:</th>
                            <th class="left">PROMEDIO PONDERADO</th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th>
                        </tr>
                        <tr>
                            <td rowspan="2"></td>
                            <td rowspan="2"></td>
                            <td rowspan="2">Fecha</td>
                            <td rowspan="2">Tipo</td>
                            <td rowspan="2">Serie</td>
                            <td rowspan="2">Numero</td>
                            <td rowspan="2">Fecha</td>
                            <td rowspan="2">Tipo</td>
                            <td rowspan="2">Serie</td>
                            <td rowspan="2">Numero</td>
                            <td rowspan="2">Tp.Ope</td>
                            <td colspan="3"><center>Entradas</center></td>
                            <td colspan="3"><center>Salidas</center></td>
                            <td colspan="3"><center>Saldo Final</center></td>
                        </tr>
                        <tr>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                            <td>Cantidad</td>
                            <td>Costo Unit.</td>
                            <td>Costo Total</td>
                        </tr>
                    </thead>
                    <tbody>';

                    $productos = DB::table('almacen.alm_prod_ubi')
                        ->select('alm_prod_ubi.*','alm_prod.codigo as prod_codigo',
                        'alm_prod.descripcion as prod_descripcion','alm_und_medida.abreviatura')
                        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
                        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
                        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
                        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
                        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
                        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                        ->where([['alm_prod_ubi.estado','=',1],
                                ['alm_almacen.id_almacen','=',$id_almacen]])
                        ->get();
            
            
                        foreach($productos as $prod){
                            $detalle = $this->movimientos_producto($id_almacen, $prod->id_producto, $finicio, $ffin);
                            $size = count($detalle);

                            if ($size > 0){

                                $html.='
                                <tr>
                                    <td>Código de Existencia:</td>
                                    <td>01 MERCADERIAS</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>
                                <tr>
                                    <td>Descripción:</td>
                                    <td>'.$prod->prod_codigo.' '.$prod->prod_descripcion.'</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>
                                <tr>
                                    <td>Codigo de Unidad:</td>
                                    <td>'.$prod->abreviatura.'</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>';
    
                                $saldo = 0;
                                $saldo_valor = 0;
                                $total_ing = 0;
                                $total_sal = 0;
                                $cant_ing = 0;
                                $cant_sal = 0;
                                $stock_inicial = false;
    
                                $html.='
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td>
                                    <td>Stock Inicial:</td>
                                    <td class="right" style="mso-number-format:"0.00";">'.$saldo.'</td>
                                    <td class="right" style="mso-number-format:"0.00";">'.($saldo !== 0 ? ($saldo_valor / $saldo) : 0).'</td>
                                    <td class="right" style="mso-number-format:"0.00";">'.$saldo_valor.'</td>
                                </tr>';
    
                                foreach($detalle as $det){
                                    if ($det->tipo == 1 || $det->tipo == 0){
                                        $saldo += $det->cantidad;
                                        $saldo_valor += $det->valorizacion;
                                    } 
                                    else if ($det->tipo == 2){
                                        $saldo -= $det->cantidad;
                                        $saldo_valor -= $det->valorizacion;
                                    }
                                    if ($det->tipo == 1){
                                        $total_ing += floatval($det->valorizacion);
                                        $cant_ing += floatval($det->cantidad);
                                        $unitario = floatval($det->valorizacion) / floatval($det->cantidad);
                                        $saldo_unitario = $saldo !== 0 ? ($saldo_valor / $saldo) : 0;
    
                                        $html.='
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>'.$det->doc_com_fecha.'</td>
                                            <td>'.$det->cod_sunat_com.'</td>
                                            <td>'.$det->doc_com_serie.'</td>
                                            <td>'.$det->doc_com_numero.'</td>
                                            <td>'.$det->guia_com_fecha.'</td>
                                            <td>'.$det->doc_sunat_com.'</td>
                                            <td>'.$det->guia_com_serie.'</td>
                                            <td>'.$det->guia_com_numero.'</td>
                                            <td>'.$det->op_sunat_ing.'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($det->cantidad,2).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($unitario,3).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($det->valorizacion,3).'</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo,2).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo_unitario,3).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo_valor,3).'</td>
                                        </tr>';
                                    }
                                    else if ($det->tipo == 2){
                                        $total_sal += floatval($det->valorizacion);
                                        $cant_sal += floatval($det->cantidad);
                                        $unitario = floatval($det->valorizacion) / floatval($det->cantidad);
                                        $saldo_unitario = $saldo !== 0 ? ($saldo_valor / $saldo) : 0;
    
                                        $html.='
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>'.$det->doc_ven_fecha.'</td>
                                            <td>'.$det->cod_sunat_ven.'</td>
                                            <td>'.$det->doc_ven_serie.'</td>
                                            <td>'.$det->doc_ven_numero.'</td>
                                            <td>'.$det->guia_ven_fecha.'</td>
                                            <td>'.$det->doc_sunat_ven.'</td>
                                            <td>'.$det->guia_ven_serie.'</td>
                                            <td>'.$det->guia_ven_numero.'</td>
                                            <td>'.$det->op_sunat_sal.'</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right">0</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.floatval($det->cantidad).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.(floatval($det->valorizacion) / floatval($det->cantidad)).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.$det->valorizacion.'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo,2).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo_unitario,3).'</td>
                                            <td class="right" style="mso-number-format:"0.00";">'.number_format($saldo_valor,3).'</td>
                                        </tr>';
                                    }
                                    // $codigo = $det->prod_codigo;
                                }
                                $html.='
                                <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td></td><td></td><td></td><td></td>
                                    <td><strong>Total:</strong></td>
                                    <td class="right"><strong>'.$cant_ing.'</strong></td><td></td>
                                    <td class="right"><strong>'.$total_ing.'</strong></td>
                                    <td class="right"><strong>'.$cant_sal.'</strong></td><td></td>
                                    <td class="right"><strong>'.$total_sal.'</strong></td><td></td><td></td><td></td>
                                </tr>'; 
                            }
                        }
                            $html.='
                        </tbody>
                    </table>';
                    }
                $html.='
            </body>
        </html>';
        
        return $html;
        // return $detalle;
    }
    public function download_kardex_sunat($almacenes, $finicio, $ffin){
        $data = $this->kardex_sunat($almacenes, $finicio, $ffin);
        return view('almacen/reportes/kardex_sunat_excel', compact('data'));
    }
    public function direccion_almacen($id_almacen){
        $alm = DB::table('almacen.alm_almacen')
        ->where('id_almacen',$id_almacen)
        ->first();
        $data = $alm->ubicacion; 
        return response()->json($data);
    }

    public function listar_tp_docs(){
        $data = DB::table('almacen.tp_doc_almacen')
        ->where('estado',1)->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_tp_doc($id){
        $data = DB::table('almacen.tp_doc_almacen')
        ->where('id_tp_doc_almacen',$id)
        ->get();
        return response()->json($data);
    }

    public function guardar_tp_doc(Request $request){
        $fecha = date('Y-m-d H:i:s');
        $id_tp_doc = DB::table('almacen.tp_doc_almacen')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'estado' => 1,
                'usuario' => $request->usuario,
                'fecha_registro' => $fecha
            ],
                'id_tp_doc_almacen'
            );
        return response()->json($id_tp_doc);
    }
    
    public function update_tp_doc(Request $request){
        $data = DB::table('almacen.tp_doc_almacen')
            ->where('id_tp_doc_almacen', $request->id_tp_doc_almacen)
            ->update([ 'descripcion' => $request->descripcion ]);
        return response()->json($data);
    }

    public function anular_tp_doc(Request $request, $id){
        $data = DB::table('almacen.tp_doc_almacen')
            ->where('id_tp_doc_almacen', $id)
            ->update(['estado' => 2]);
        return response()->json($data);
    }
    public function listar_ocs(){
        $data = DB::table('logistica.log_ord_compra')
            ->select('log_ord_compra.*','adm_contri.razon_social')
            ->join('logistica.log_prove','log_prove.id_proveedor','=','log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_kardex_producto($id_producto,$finicio,$ffin){
        $producto = DB::table('almacen.alm_prod')
            ->select('alm_prod.*','alm_und_medida.abreviatura','alm_subcategoria.descripcion as des_subcategoria',
            'alm_cat_prod.descripcion as des_categoria','alm_tp_prod.descripcion as des_tipo',
            'alm_tp_prod.id_tipo_producto','alm_cat_prod.codigo as cat_codigo',
            'alm_subcategoria.codigo as subcat_codigo','alm_clasif.descripcion as des_clasificacion')
            ->join('almacen.alm_subcategoria','alm_subcategoria.id_subcategoria','=','alm_prod.id_subcategoria')
            ->join('almacen.alm_cat_prod','alm_cat_prod.id_categoria','=','alm_subcategoria.id_categoria')
            ->join('almacen.alm_tp_prod','alm_tp_prod.id_tipo_producto','=','alm_cat_prod.id_tipo_producto')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','alm_prod.id_unidad_medida')
            ->join('almacen.alm_clasif','alm_clasif.id_clasificacion','=','alm_prod.id_clasif')
            ->where('alm_prod.id_producto',$id_producto)
            ->first();

        $html = '
        <table border="0" class="table-group">
            <tbody>
                <tr>
                    <th width="80px">Código</th>
                    <td>'.$producto->codigo.'</td>
                    <th width="80px">Descripción</th>
                    <td colspan="3">'.$producto->descripcion.'</td>
                    <th width="80px">Unid.Med.</th>
                    <td>'.$producto->abreviatura.'</td>
                </tr>
                <tr>
                    <th>Tipo</th>
                    <td width="23%">'.$producto->des_tipo.'</td>
                    <th>Categoría</th>
                    <td>'.$producto->des_categoria.'</td>
                    <th>Sub-Categoría</th>
                    <td>'.$producto->des_subcategoria.'</td>
                    <th>Clasificación</th>
                    <td>'.$producto->des_clasificacion.'</td>
                </tr>
            </tbody>
        </table>';

        $almacenes = DB::table('almacen.alm_almacen')
            ->select('alm_almacen.*','alm_tp_almacen.descripcion as des_tipo_almacen',
            'sis_sede.descripcion as des_sede')
            ->join('almacen.alm_tp_almacen','alm_tp_almacen.id_tipo_almacen','=','alm_almacen.id_tipo_almacen')
            ->join('administracion.sis_sede','sis_sede.id_sede','=','alm_almacen.id_sede')
            ->where('alm_almacen.estado',1)
            ->get();

        foreach($almacenes as $alm){
            $data = DB::table('almacen.mov_alm_det')
                ->select('mov_alm_det.*','alm_ubi_posicion.codigo as cod_posicion',
                'mov_alm.fecha_emision','mov_alm.id_tp_mov',
                DB::raw("CONCAT(tp_doc_com.abreviatura,'-',guia_com.serie,'-',guia_com.numero) as guia_com"),
                'tp_doc_com.cod_doc_sunat as cod_sunat_doc_com',
                'tp_ope_com.cod_sunat as cod_sunat_ope_com',
                'tp_ope_com.descripcion as des_ope_com',
                DB::raw("CONCAT(tp_doc_ven.abreviatura,'-',guia_ven.serie,'-',guia_ven.numero) as guia_ven"),
                // 'tp_doc_ven.descripcion as des_doc_ven',
                'tp_doc_ven.cod_doc_sunat as cod_sunat_doc_ven',
                'tp_ope_ven.cod_sunat as cod_sunat_ope_ven',
                'tp_ope_ven.descripcion as des_ope_ven')
                ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
                ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
                ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
                ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
                ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
                ->leftjoin('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
                ->leftjoin('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
                ->leftjoin('almacen.tp_doc_almacen as tp_doc_com','tp_doc_com.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
                ->leftjoin('almacen.tp_ope as tp_ope_com','tp_ope_com.id_operacion','=','guia_com.id_operacion')
                ->leftjoin('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','mov_alm_det.id_guia_ven_det')
                ->leftjoin('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
                ->leftjoin('almacen.tp_doc_almacen as tp_doc_ven','tp_doc_ven.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
                ->leftjoin('almacen.tp_ope as tp_ope_ven','tp_ope_ven.id_operacion','=','guia_ven.id_operacion')
                ->where([['mov_alm_det.id_producto','=',$id_producto],
                        ['mov_alm.fecha_emision','>=',$finicio],
                        ['mov_alm.fecha_emision','<=',$ffin],
                        ['alm_almacen.id_almacen','=',$alm->id_almacen],
                        ['mov_alm_det.estado','=',1]])
                ->orderBy('mov_alm.fecha_emision','asc')
                ->orderBy('mov_alm.id_tp_mov','asc')
                ->get();

            if (count($data) > 0){
                $html.='
                <table border="0" class="table-group">
                    <tbody>
                        <tr>
                            <th width="80px">Almacén</th>
                            <td width="23%">'.$alm->descripcion.'</td>
                            <th width="120px">Tipo de Almacén</th>
                            <td>'.$alm->des_tipo_almacen.'</td>
                        </tr>
                        <tr>
                            <th width="80px">Sede</th>
                            <td>'.$alm->des_sede.'</td>
                            <th width="80px">Dirección</th>
                            <td>'.$alm->ubicacion.'</td>                    
                        </tr>
                    </tbody>
                </table>
                <table class="table-group">
                    <thead>
                        <tr>
                            <th>CodOpe</th>
                            <th>Tipo de Operacion</th>
                            <th>Fecha</th>
                            <th>Documento</th>
                            <th>Factura</th>
                            <th>Proveedor</th>
                            <th>Ingreso</th>
                            <th>Salida</th>
                            <th>Saldo</th>
                            <th>Ingreso</th>
                            <th>Salida</th>
                            <th>Valorizacion</th>
                            <th>Posicion</th>
                        </tr>
                    </thead>
                    <tbody>';
                $saldo = 0;
                $saldo_valor = 0;

                foreach($data as $d){
                    if ($d->id_tp_mov == 1 || $d->id_tp_mov == 0){
                        $saldo += $d->cantidad;
                        $saldo_valor += $d->valorizacion;
                    } 
                    else if ($d->id_tp_mov == 2){
                        $saldo -= $d->cantidad;
                        $saldo_valor -= $d->valorizacion;
                    }
                    if ($d->id_tp_mov == 1){
                        $html.='
                        <tr>
                            <td>'.$d->cod_sunat_ope_com.'</td>
                            <td>'.$d->des_ope_com.'</td>
                            <td>'.$d->fecha_emision.'</td>
                            <td>'.$d->guia_com.'</td>
                            <td></td>
                            <td></td>
                            <td>'.$d->cantidad.'</td>
                            <td>0</td>
                            <td style="mso-num  ber-format:"0.00";">'.$saldo.'</td>
                            <td style="mso-number-format:"0.00";">'.$d->valorizacion.'</td>
                            <td>0</td>
                            <td style="mso-number-format:"0.00";">'.$saldo_valor.'</td>
                            <td>'.$d->cod_posicion.'</td>
                        </tr>';
                    }
                    else if ($d->id_tp_mov == 2){
                        $html.='
                        <tr>
                            <td>'.$d->cod_sunat_ope_ven.'</td>
                            <td>'.$d->des_ope_ven.'</td>
                            <td>'.$d->fecha_emision.'</td>
                            <td>'.$d->guia_ven.'</td>
                            <td></td>
                            <td></td>
                            <td>0</td>
                            <td>'.$d->cantidad.'</td>
                            <td>'.$saldo.'</td>
                            <td>0</td>
                            <td>'.$d->valorizacion.'</td>
                            <td>'.$saldo_valor.'</td>
                            <td>'.$d->cod_posicion.'</td>
                        </tr>';
                    }
                }
                $html.='</tbody></table>';
            }
        }    
        
        return $html;
    }
    public function kardex_producto($id_producto,$finicio,$ffin){
        $html = $this->listar_kardex_producto($id_producto,$finicio,$ffin);
        return json_encode($html);
    }
    public function download_kardex_producto($almacenes, $finicio, $ffin){
        $data = $this->listar_kardex_producto($almacenes, $finicio, $ffin);
        return view('almacen/reportes/kardex_detallado_excel', compact('data'));
    }
    public function saldo_por_producto($id_producto){
        $data = DB::table('almacen.alm_prod_ubi')
        ->select('alm_prod_ubi.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_ubi_posicion.codigo as cod_posicion','alm_almacen.descripcion as des_almacen')
        ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_prod_ubi.id_producto')
        ->join('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','alm_prod_ubi.id_posicion')
        ->join('almacen.alm_ubi_nivel','alm_ubi_nivel.id_nivel','=','alm_ubi_posicion.id_nivel')
        ->join('almacen.alm_ubi_estante','alm_ubi_estante.id_estante','=','alm_ubi_nivel.id_estante')
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','alm_ubi_estante.id_almacen')
        ->where([['alm_prod_ubi.id_producto','=',$id_producto],
                ['alm_prod_ubi.stock','>',0],['alm_prod_ubi.estado','=',1]])
        ->get();
        return response()->json($data);
    }
    public function listar_ingresos($almacenes, $documentos, $condiciones, $fecha_inicio, $fecha_fin, $id_proveedor, $id_usuario, $moneda, /*$referenciado,*/ $transportista){
        $alm_array = explode(',',$almacenes);
        $doc_array = explode(',',$documentos);
        $con_array = explode(',',$condiciones);

        $hasWhere = [];
        if ($id_proveedor !== null && $id_proveedor > 0){
            $hasWhere[] = ['guia_com.id_proveedor','=',$id_proveedor];
        }
        if ($id_usuario !== null && $id_usuario > 0){
            $hasWhere[] = ['guia_com.usuario','=',$id_usuario];
        }
        if ($moneda == 1 || $moneda == 2){
            $hasWhere[] = ['doc_com.moneda','=',$moneda];
        }
        if ($transportista !== null && $transportista > 0){
            $hasWhere[] = ['guia_com.transportista','=',$transportista];
        }

        $count = count($doc_array);
        $docs = [];
        $alm = [];
        $oc = '';

        for ($i=0; $i<$count; $i++){
            if ($doc_array[$i] > 100){ //Docs
                $docs[] = [$doc_array[$i] - 100];
            } 
            else if ($doc_array[$i] < 100){ //Alm
                $alm[] = [intval($doc_array[$i])];
            }
            else {
                $oc = intval($doc_array[$i]);
            }
        }

        $data = DB::table('almacen.mov_alm')
        ->select('mov_alm.*','sis_moneda.simbolo','doc_com.total','doc_com.fecha_vcmto',
        'doc_com.total_igv','doc_com.total_a_pagar','cont_tp_doc.abreviatura',
        'doc_com.credito_dias','log_cdn_pago.descripcion as des_condicion',
        'doc_com.fecha_emision as fecha_doc','alm_almacen.descripcion as des_almacen',
        'doc_com.tipo_cambio','doc_com.moneda',
        DB::raw("CONCAT(doc_com.serie,'-',doc_com.numero) as doc"),
        DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia"),
        'guia_com.fecha_emision as fecha_guia','adm_contri.nro_documento',
        'adm_contri.razon_social','tp_ope.descripcion as des_operacion',
        DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
        ->join('almacen.guia_com','guia_com.id_guia','=','mov_alm.id_guia_com')
        ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
        ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
        ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        ->leftjoin('almacen.doc_com','doc_com.id_doc_com','=','mov_alm.id_doc_com')
        ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_com.id_tp_doc')
        ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','doc_com.id_condicion')
        ->whereIn('mov_alm.id_almacen',$alm_array)
        ->whereIn('guia_com.id_tp_doc_almacen',$alm)
        ->whereIn('doc_com.id_tp_doc',$docs)
        ->whereIn('guia_com.id_operacion',$con_array)
        ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
        ->where($hasWhere)
        ->get();

        return response()->json($data);
        // return response()->json(['docs'=>$docs,'alm'=>$alm,'oc'=>$oc]);
    }
    public function listar_salidas($almacenes, $documentos, $condiciones, $fecha_inicio, $fecha_fin, $id_cliente, $id_usuario, $moneda, $referenciado){
        $alm_array = explode(',',$almacenes);
        $doc_array = explode(',',$documentos);
        $con_array = explode(',',$condiciones);

        $hasWhere = [];
        if ($id_cliente !== null && $id_cliente > 0){
            $hasWhere[] = ['guia_ven.id_cliente','=',$id_cliente];
        }
        if ($id_usuario !== null && $id_usuario > 0){
            $hasWhere[] = ['guia_ven.usuario','=',$id_usuario];
        }
        if ($moneda == 1 || $moneda == 2){
            $hasWhere[] = ['doc_ven.moneda','=',$moneda];
        }

        $data = DB::table('almacen.mov_alm')
        ->select('mov_alm.*','sis_moneda.simbolo','doc_ven.total','doc_ven.fecha_vcmto',
                'doc_ven.total_igv','doc_ven.total_a_pagar','cont_tp_doc.abreviatura',
                'doc_ven.credito_dias','log_cdn_pago.descripcion as des_condicion',
                'doc_ven.fecha_emision as fecha_doc','alm_almacen.descripcion as des_almacen',
                'doc_ven.tipo_cambio','doc_ven.moneda',
                DB::raw("CONCAT(doc_ven.serie,'-',doc_ven.numero) as doc"),
                DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia"),
                'guia_ven.fecha_emision as fecha_guia','adm_contri.nro_documento',
                'adm_contri.razon_social','tp_ope.descripcion as des_operacion',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_trabajador"))
        ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
        ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','mov_alm.id_guia_ven')
        ->join('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
        ->join('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
        ->join('almacen.tp_ope','tp_ope.id_operacion','=','guia_ven.id_operacion')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','mov_alm.usuario')
        ->join('rrhh.rrhh_trab','rrhh_trab.id_trabajador','=','sis_usua.id_trabajador')
        ->join('rrhh.rrhh_postu','rrhh_postu.id_postulante','=','rrhh_trab.id_postulante')
        ->join('rrhh.rrhh_perso','rrhh_perso.id_persona','=','rrhh_postu.id_persona')
        ->leftjoin('almacen.doc_ven','doc_ven.id_doc_ven','=','mov_alm.id_doc_ven')
        ->leftjoin('contabilidad.cont_tp_doc','cont_tp_doc.id_tp_doc','=','doc_ven.id_tp_doc')
        ->leftjoin('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_ven.moneda')
        ->leftjoin('logistica.log_cdn_pago','log_cdn_pago.id_condicion_pago','=','doc_ven.id_condicion')
        ->whereIn('mov_alm.id_almacen',$alm_array)
        ->whereIn('guia_ven.id_tp_doc_almacen',$doc_array)
        ->whereIn('guia_ven.id_operacion',$con_array)
        ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
        ->where($hasWhere)
        ->get();

        return response()->json($data);
    }
    public function update_revisado($id_mov_alm, $revisado, $obs){
        $data = DB::table('almacen.mov_alm')
        ->where('id_mov_alm',$id_mov_alm)
        ->update(['revisado' => $revisado,
                  'obs' => $obs ]);
        return response()->json($data);
    } 
    public function listar_busqueda_ingresos($almacenes, $tipo, $descripcion, $documentos, $fecha_inicio, $fecha_fin){
        $alm_array = explode(',',$almacenes);
        $doc_array = explode(',',$documentos);
        $des = strtoupper($descripcion);
        $hasWhere = '';

        if ($tipo == 1){
            $hasWhere = 'alm_prod.descripcion';
        } 
        else if ($tipo == 2){
            $hasWhere = 'alm_prod.codigo';
        } 
        else if ($tipo == 3){
            $hasWhere = 'alm_prod.codigo_anexo';
        }

        if ($descripcion !== '<vacio>'){
            $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision',
            'tp_doc_almacen.abreviatura as tp_doc','guia_com.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia"),
            'adm_contri.razon_social','adm_contri.nro_documento','alm_almacen.descripcion as alm_descripcion',
            'alm_prod.codigo_anexo','alm_prod.codigo','alm_prod.descripcion',
            'tp_ope.descripcion as ope_descripcion','adm_estado_doc.estado_doc')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','
            
            .id_guia_com')
            ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
            ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->whereIn('guia_com.id_tp_doc_almacen',$doc_array)
            ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
            ->where($hasWhere,'like','%'.$des.'%')
            // ->where( ( ($des !== '') ? [$hasWhere,'like','%'.$des.'%'] : '' ) )
            ->get();
        } else {
            $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision',
            'tp_doc_almacen.abreviatura as tp_doc','guia_com.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_com.serie,'-',guia_com.numero) as guia"),
            'adm_contri.razon_social','adm_contri.nro_documento','alm_almacen.descripcion as alm_descripcion',
            'alm_prod.codigo_anexo','alm_prod.codigo','alm_prod.descripcion',
            'tp_ope.descripcion as ope_descripcion','adm_estado_doc.estado_doc')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.guia_com_det','guia_com_det.id_guia_com_det','=','mov_alm_det.id_guia_com_det')
            ->join('almacen.guia_com','guia_com.id_guia','=','guia_com_det.id_guia_com')
            ->leftjoin('logistica.log_prove','log_prove.id_proveedor','=','guia_com.id_proveedor')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_com.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_com.id_operacion')
            ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_com.estado')
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->whereIn('guia_com.id_tp_doc_almacen',$doc_array)
            ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
            ->get();
        }

        return response()->json($data);
    }
    public function imprimir_guia_ingreso($id_ing){
        $id = $this->decode5t($id_ing);
        $result = $this->get_ingreso($id);
        $ingreso = $result['ingreso'];
        $detalle = $result['detalle'];
        $ocs = $result['ocs'];

        $cod_ocs = '';
        foreach($ocs as $oc){
            if ($cod_ocs == ''){
                $cod_ocs .= $oc->codigo;
            } else {
                $cod_ocs .= ', '.$oc->codigo;
            }
        }
        $revisado = ($ingreso->revisado !== 0 ? 'No Revisado' : 
                    ($ingreso->revisado !== 1 ? 'Revisado' : 'Observado'));
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');

        $html = '
        <html>
            <head>
                <style type="text/css">
                *{ 
                    font-family: "DejaVu Sans";
                }
                table{
                    width:100%;
                    font-size:11px;
                }
                #detalle thead{
                    padding: 4px;
                    background-color: #e5e5e5;
                }
                #detalle tbody tr td,
                #detalle tfoot tr td{
                    font-size:11px;
                    padding: 4px;
                }
                #detalle tfoot{
                    border-top: 1px dashed #343a40;
                }
                .right{
                    text-align: right;
                }
                .sup{
                    vertical-align:top;
                }
                .guinda{
                    background-color: #8f1c1c;
                }
                </style>
            </head>
            <body>
                <table width="100%">
                    <tr>
                        <td>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$ingreso->ruc_empresa.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">'.$ingreso->empresa_razon_social.'</p>
                            <p style="text-align:left;font-size:10px;margin:0px;">.::Sistema ERP v1.0::.</p>
                        </td>
                        <td>
                            <p style="text-align:right;font-size:10px;margin:0px;">Fecha: '.$fecha_actual.'</p>
                            <p style="text-align:right;font-size:10px;margin:0px;">Hora : '.$hora_actual.'</p>
                        </td>
                    </tr>
                </table>
                <div style="border:1px #212121 solid;padding:2px;background-color:#e5e5e5;width:60%;margin:auto">
                    <h3 style="margin:0px;"><center>'.$ingreso->tp_doc_descripcion.'</center></h3>
                </div>
                <h5 style="margin:5px;"><center>'.$revisado.'</center></h5>
                
                <table border="0" style="border:1px #212121 dashed;padding:3px;">
                    <tr>
                        <td width=120px class="subtitle">Sucursal / Almacén</td>
                        <td width=10px>:</td>
                        <td colSpan="7" class="verticalTop">'.$ingreso->empresa_razon_social.' / '.$ingreso->des_almacen.'</td>
                    </tr>
                    <tr>
                        <td>TD.</td>
                        <td width=10px>:</td>
                        <td width=130px>'.$ingreso->guia.'</td>
                        <td width=50px>Fecha</td>
                        <td width=10px>:</td>
                        <td width=100px>'.$ingreso->fecha_guia.'</td>
                        <td width=50px>Moneda</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->des_moneda.'</td>
                        <td width=30px>T.C.</td>
                        <td width=10px>:</td>
                        <td width=40px>'.$ingreso->tipo_cambio.'</td>
                    </tr>
                    <tr>
                        <td>Señores</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">'.$ingreso->razon_social.'</td>
                        <td width=50px>Teléfono(s)</td>
                        <td width=10px>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Dirección</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">'.$ingreso->direccion_fiscal.'</td>
                        <td width=50px>RUC</td>
                        <td width=10px>:</td>
                        <td>'.$ingreso->nro_documento.'</td>
                    </tr>
                    <tr>
                        <td>Responsable</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">'.$ingreso->persona.'</td>
                        <td width=50px>Condición</td>
                        <td width=10px>:</td>
                        <td colSpan="4">'.$ingreso->ope_descripcion.'</td>
                    </tr>
                    <tr>
                        <td>Cod. Ingreso</td>
                        <td width=10px>:</td>
                        <td width=130px colSpan="4">'.$ingreso->codigo.'</td>
                        <td width=50px>Fecha Ing</td>
                        <td width=10px>:</td>
                        <td colSpan="4">'.$ingreso->fecha_emision.'</td>
                    </tr>
                </table>
                <br/>
                <table id="detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código</th>
                            <th>Cód.Anexo</th>
                            <th width=40% >Descripción</th>
                            <th>Cant.</th>
                            <th>Unid.</th>
                            <th>V.Compra</th>
                            <th>Agregado</th>
                            <th>P.Total</th>
                            <th>Unitario</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $i = 1;
                    $total = 0;
                    $unitarios = 0;

                    foreach($detalle as $det){
                        $unitario = floatval($det->subtotal)/floatval($det->cantidad);
                        $total += floatval($det->subtotal);
                        $unitarios += floatval($unitario);
                        $html.='
                        <tr>
                            <td class="right">'.$i.'</td>
                            <td>'.$det->codigo.'</td>
                            <td>'.$det->codigo_anexo.'</td>
                            <td>'.$det->descripcion.'</td>
                            <td class="right">'.$det->cantidad.'</td>
                            <td>'.$det->abreviatura.'</td>
                            <td class="right">'.$det->subtotal.'</td>
                            <td class="right">0</td>
                            <td class="right">'.$det->subtotal.'</td>
                            <td class="right">'.$unitario.'</td>
                        </tr>';
                        $i++;
                    }
                    $igv = $total * 0.18;
                    $html.='</tbody>
                    <tfoot>
                        <tr>
                            <td class="right" colSpan="6"><strong>Totales</strong></td>
                            <td class="right">'.$total.'</td>
                            <td class="right">0</td>
                            <td class="right">'.$total.'</td>
                            <td class="right">'.$unitarios.'</td>
                        </tr>
                    </tfoot>
                </table>
                <br/>
                <div width=200px style="border:1px #212121 solid;padding:2px;background-color:#e5e5e5;">
                    <table>
                        <tr>
                            <td class="right"><strong>Monto Neto: </strong></td>
                            <td class="right">'.$total.'</td>
                            <td class="right"><strong>Impuesto: </strong></td>
                            <td class="right">'.$igv.'</td>
                            <td class="right"><strong>Total Doc: </strong></td>
                            <td class="right">'.($total + $igv).'</td>
                        </tr>
                    </table>
                </div>
                <p style="text-align:right;font-size:11px;">Elaborado por: '.$ingreso->nom_usuario.' '.$ingreso->fecha_registro.'</p>

            </body>
        </html>';
        
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
        return $pdf->download('ingreso.pdf');
    }
    public function listar_busqueda_salidas($almacenes, $tipo, $descripcion, $documentos, $fecha_inicio, $fecha_fin){
        $alm_array = explode(',',$almacenes);
        $doc_array = explode(',',$documentos);
        $des = strtoupper($descripcion);
        $hasWhere = '';

        if ($tipo == 1){
            $hasWhere = 'alm_prod.descripcion';
        } 
        else if ($tipo == 2){
            $hasWhere = 'alm_prod.codigo';
        } 
        else if ($tipo == 3){
            $hasWhere = 'alm_prod.codigo_anexo';
        }

        if ($descripcion !== '<vacio>'){
            $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision',
            'tp_doc_almacen.abreviatura as tp_doc','guia_ven.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia"),
            'adm_contri.razon_social','adm_contri.nro_documento','alm_almacen.descripcion as alm_descripcion',
            'alm_prod.codigo_anexo','alm_prod.codigo','alm_prod.descripcion',
            'tp_ope.descripcion as ope_descripcion','adm_estado_doc.estado_doc')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','mov_alm_det.id_guia_ven_det')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_ven.id_operacion')
            ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->whereIn('guia_ven.id_tp_doc_almacen',$doc_array)
            ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
            ->where($hasWhere,'like','%'.$des.'%')
            // ->where( ( ($des !== '') ? [$hasWhere,'like','%'.$des.'%'] : '' ) )
            ->get();
        } else {
            $data = DB::table('almacen.mov_alm_det')
            ->select('mov_alm_det.*','mov_alm.fecha_emision',
            'tp_doc_almacen.abreviatura as tp_doc','guia_ven.fecha_emision as fecha_guia',
            DB::raw("CONCAT(guia_ven.serie,'-',guia_ven.numero) as guia"),
            'adm_contri.razon_social','adm_contri.nro_documento','alm_almacen.descripcion as alm_descripcion',
            'alm_prod.codigo_anexo','alm_prod.codigo','alm_prod.descripcion',
            'tp_ope.descripcion as ope_descripcion','adm_estado_doc.estado_doc')
            ->join('almacen.mov_alm','mov_alm.id_mov_alm','=','mov_alm_det.id_mov_alm')
            ->join('almacen.alm_almacen','alm_almacen.id_almacen','=','mov_alm.id_almacen')
            ->join('almacen.alm_prod','alm_prod.id_producto','=','mov_alm_det.id_producto')
            // ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','mov_alm_det.id_posicion')
            ->join('almacen.guia_ven_det','guia_ven_det.id_guia_ven_det','=','mov_alm_det.id_guia_ven_det')
            ->join('almacen.guia_ven','guia_ven.id_guia_ven','=','guia_ven_det.id_guia_ven')
            ->leftjoin('comercial.com_cliente','com_cliente.id_cliente','=','guia_ven.id_cliente')
            ->leftjoin('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->leftjoin('almacen.tp_doc_almacen','tp_doc_almacen.id_tp_doc_almacen','=','guia_ven.id_tp_doc_almacen')
            ->leftjoin('almacen.tp_ope','tp_ope.id_operacion','=','guia_ven.id_operacion')
            ->leftjoin('administracion.adm_estado_doc','adm_estado_doc.id_estado_doc','=','guia_ven.estado')
            ->whereIn('mov_alm.id_almacen',$alm_array)
            ->whereIn('guia_ven.id_tp_doc_almacen',$doc_array)
            ->whereBetween('mov_alm.fecha_emision',[$fecha_inicio, $fecha_fin])
            ->get();
        }

        return response()->json($data);
    }
    public function listar_transportistas_com(){
        $data = DB::table('almacen.guia_com')->distinct()
        ->select('guia_com.transportista','adm_contri.id_contribuyente','adm_contri.razon_social','adm_contri.nro_documento')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_com.transportista')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where('guia_com.estado','<>',7)
        ->groupBy('guia_com.transportista','adm_contri.id_contribuyente','adm_contri.razon_social','adm_contri.nro_documento')
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function listar_transportistas_ven(){
        $data = DB::table('almacen.guia_ven')->distinct()
        ->select('guia_ven.transportista','adm_contri.id_contribuyente','adm_contri.razon_social','adm_contri.nro_documento')
        ->join('logistica.log_prove','log_prove.id_proveedor','=','guia_ven.transportista')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','log_prove.id_contribuyente')
        ->where('guia_ven.estado','<>',7)
        ->groupBy('guia_ven.transportista','adm_contri.id_contribuyente','adm_contri.razon_social','adm_contri.nro_documento')
        ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function guardar_prorrateo(Request $request){
        $id_doc_com = DB::table('almacen.doc_com')->insertGetId(
            [
                'serie' => $request->pro_serie,
                'numero' => $request->pro_numero,
                'id_proveedor' => $request->doc_id_proveedor,
                'moneda' => $request->id_moneda,
                'fecha_emision' => $request->doc_fecha_emision,
                'tipo_cambio' => $request->tipo_cambio,
                'sub_total' => $request->sub_total,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_doc_com'
            );

        $data = DB::table('almacen.guia_com_prorrateo')->insertGetId(
            [
                'id_guia_com' => $request->id_guia,
                'id_tp_prorrateo' => $request->id_tp_prorrateo,
                'id_doc_com' => $id_doc_com,
                'tipo' => $request->prorrateo,
                'importe' => $request->importe
            ],
                'id_prorrateo'
            );
            
        return response()->json($data);
    }
    public function listar_docs_prorrateo($id){
        $data = DB::table('almacen.guia_com_prorrateo')
            ->select('guia_com_prorrateo.*','doc_com.serie','doc_com.numero',
            'tp_prorrateo.descripcion as des_tp_prorrateo','sis_moneda.simbolo',
            'doc_com.sub_total','doc_com.fecha_emision','doc_com.tipo_cambio')
            ->join('almacen.doc_com','doc_com.id_doc_com','=','guia_com_prorrateo.id_doc_com')
            ->join('almacen.tp_prorrateo','tp_prorrateo.id_tp_prorrateo','=','guia_com_prorrateo.id_tp_prorrateo')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','doc_com.moneda')
            ->where('guia_com_prorrateo.id_guia_com',$id)
            ->get();
        $i = 1;
        $html = '';
        $total = 0;

        foreach($data as $d){
            $total += floatval($d->importe);
            $html .= '
            <tr id="det-'.$d->id_prorrateo.'">
                <td>'.$i.'</td>
                <td>'.$d->des_tp_prorrateo.'</td>
                <td>'.$d->serie.'-'.$d->numero.'</td>
                <td>'.$d->fecha_emision.'</td>
                <td>'.$d->simbolo.'</td>
                <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="subtotal" onChange="calcula_importe('.$d->id_prorrateo.');" value="'.$d->sub_total.'" disabled="true"/></td>
                <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="tipocambio" onChange="calcula_importe('.$d->id_prorrateo.');" value="'.$d->tipo_cambio.'" disabled="true"/></td>
                <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="importedet" value="'.$d->importe.'" disabled="true"/></td>
                <td style="display:flex;">
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar" onClick="editar_adicional('.$d->id_prorrateo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" title="Guardar" onClick="update_adicional('.$d->id_prorrateo.','.$d->id_doc_com.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular" onClick="anular_adicional('.$d->id_prorrateo.','.$d->id_doc_com.');"></i>
                </td>
            </tr>
            ';
            $i++;
        }
        $moneda = DB::table('almacen.guia_com_oc')
        ->select('sis_moneda.simbolo','sis_moneda.descripcion')
        ->join('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','guia_com_oc.id_oc')
        ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','log_ord_compra.id_moneda')
        ->where('id_guia_com',$id)
        ->first();
        return json_encode(['html'=>$html,'total'=>round($total,2,PHP_ROUND_HALF_UP),'moneda'=>$moneda]);
        // return json_encode(['html'=>$html,'total'=>$total]);
    }
    public function listar_guia_detalle_prorrateo($id, $total_comp){
        $data = DB::table('almacen.guia_com_det')
        ->select('guia_com_det.*','alm_prod.codigo','alm_prod.descripcion',
        'alm_und_medida.abreviatura','log_ord_compra.codigo AS cod_orden')
        ->leftjoin('almacen.alm_prod','alm_prod.id_producto','=','guia_com_det.id_producto')
        ->leftjoin('almacen.alm_ubi_posicion','alm_ubi_posicion.id_posicion','=','guia_com_det.id_posicion')
        ->leftjoin('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','guia_com_det.id_unid_med')
        ->leftjoin('logistica.log_det_ord_compra','log_det_ord_compra.id_detalle_orden','=','guia_com_det.id_oc_det')
        ->leftjoin('logistica.log_ord_compra','log_ord_compra.id_orden_compra','=','log_det_ord_compra.id_orden_compra')
        ->leftjoin('administracion.adm_tp_docum','adm_tp_docum.id_tp_documento','=','log_ord_compra.id_tp_documento')
        ->where([['guia_com_det.id_guia_com', '=', $id],
                 ['guia_com_det.estado','=',1]])
            ->get();
        $html = '';
        $suma_total = 0;
        $suma_adicional = 0;
        $suma_costo = 0;

        foreach($data as $det){
            $suma_total += floatval($det->total);
        }
        $valor = $total_comp / $suma_total;

        foreach($data as $det){
            $id_guia_det = $det->id_guia_com_det;
            $oc = $det->cod_orden;
            $codigo = $det->codigo;
            $descripcion = $det->descripcion;
            $cantidad = $det->cantidad;
            $abrev = $det->abreviatura;
            $id_posicion = $det->id_posicion;
            $unitario = $det->unitario;
            $total = floatval($det->total);
            $adicional = round(($valor * $total),2,PHP_ROUND_HALF_UP);
            $costo_total = $total + $adicional;

            $suma_adicional += $adicional;
            $suma_costo += $costo_total;

            $unit = round(($costo_total/$cantidad),2,PHP_ROUND_HALF_UP);

            $html .= 
            '<tr id="det-'.$id_guia_det.'">
                <td>'.$oc.'</td>
                <td>'.$codigo.'</td>
                <td>'.$descripcion.'</td>
                <td style="text-align:right">'.$cantidad.'</td>
                <td>'.$abrev.'</td>
                <td style="text-align:right">'.$total.'</td>
                <td style="text-align:right">'.$adicional.'</td>
                <td style="text-align:right"><input type="text" class="oculto" name="unit" value="'.$unit.'"/>'.$costo_total.'</td>
            </tr>';
        }
        $sumas[] = [
            'suma_total'=>round($suma_total,2,PHP_ROUND_HALF_UP),
            'suma_adicional'=>round($suma_adicional,2,PHP_ROUND_HALF_UP),
            'suma_costo'=>round($suma_costo,2,PHP_ROUND_HALF_UP),
        ];
        return json_encode(['html'=>$html,'sumas'=>$sumas]);
    }
    public function update_doc_prorrateo(Request $request){
        $prorrateo = DB::table('almacen.guia_com_prorrateo')
        ->where('id_prorrateo',$request->id_prorrateo)
        ->update(['importe'=>$request->importe]);

        $doc = DB::table('almacen.doc_com')
        ->where('id_doc_com',$request->id_doc)
        ->update([ 'tipo_cambio'=>$request->tipo_cambio,
                   'sub_total'=>$request->sub_total ]);

        return response()->json($prorrateo);
    }
    public function eliminar_doc_prorrateo($id_prorrateo, $id_doc){
        $data = DB::table('almacen.guia_com_prorrateo')
        ->where('id_prorrateo',$id_prorrateo)
        ->delete();

        $detalle = DB::table('almacen.doc_com_det')->where('id_doc',$id_doc)->get();
        
        if (isset($detalle)){
            DB::table('almacen.doc_com')->where('id_doc_com',$id_doc)
            ->delete();
        }
        
        return response()->json($data);
    }
    public function update_guia_detalle(Request $request){
        $id = explode(',',$request->id_guia_det);
        $unitario = explode(',',$request->unitario);
        $count = count($id);
        $data = '';

        for ($i=0; $i<$count; $i++){
            $id_guia_det = $id[$i];
            $unit = $unitario[$i];

            $data = DB::table('almacen.guia_com_det')
            ->where('id_guia_com_det',$id_guia_det)
            ->update([ 'unitario_adicional'=>$unit ]);
        }
        return response()->json($data);
    }
    public function getTipoCambio($fecha){
        $data = DB::table('contabilidad.cont_tp_cambio')
        ->where('cont_tp_cambio.fecha','<=',$fecha)
        ->orderBy('fecha','desc')
        ->take(1)->get();
        return $data;
    }
    ////////////////////////////////////////
    public function leftZero($lenght, $number){
        $nLen = strlen($number);
        $zeros = '';
        for($i=0; $i<($lenght-$nLen); $i++){
            $zeros = $zeros.'0';
        }
        return $zeros.$number;
    }
    // public function tipo_cambio(Request $request){
    //     $data = file_get_contents('https://api.sunat.cloud/cambio/'.$request->fecha);
        // $info = json_decode($data, true);
        // if ($data === '[]' || $info['fecha_inscripcion'] === '--'){
        //     $datos = array(0 => 'nada');
        // } else {
        //     $datos = array(
        //         0 => $info['compra'],
        //         1 => $info['venta']
        //     );
        // }
    //     return json_encode($data);
    // }
    function encode5t($str){
        for($i=0; $i<5;$i++){
       $str=strrev(base64_encode($str));
        }
        return $str;
    }
   
    function decode5t($str){
        for($i=0; $i<5;$i++){
       $str=base64_decode(strrev($str));
        }
        return $str;
    }
}

        // DB::transaction(function(){
        //     Prueba::whereId('20008')->update('ruta_2','Man-33')->all();
        // });
        // DB::beginTransaction();
        // try {
        //     $post->comments()->save($comment);
        //     $post->last_comment_at = now();
        //     $post->save();
        //     DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     throw $e;
        // } catch (\Throwable $e) {
        //     DB::rollback();
        //     throw $e;

