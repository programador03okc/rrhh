<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

// use Mail;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Dompdf\Dompdf;
use PDF;

use App\Models\Logistica\Empresa;
use App\Models\Tesoreria\Usuario;
use App\Models\Tesoreria\Grupo;


class LogisticaController extends Controller
{

    function view_lista_requerimientos()
    {
        return view('logistica/requerimientos/lista_requerimientos');
    }

    function view_gestionar_requerimiento()
    {
        $monedas = $this->mostrar_moneda();
        $prioridades = $this->mostrar_prioridad();
        // $tipos = $this->mostrar_tipo();
        $empresas = Empresa::all();
        $areas = $this->mostrar_area();
        $unidades_medida = $this->mostrar_unidad_medida();
        return view('logistica/requerimientos/gestionar_requerimiento', compact('monedas', 'prioridades', 'empresas', 'unidades_medida'));
    }
    function view_gestionar_cotizaciones()
    {
        $tp_contribuyente = $this->select_tp_contribuyente();
        $sis_identidad = $this->select_sis_identidad();
        $empresas = $this->select_mostrar_empresas();
        return view('logistica/cotizaciones/gestionar_cotizaciones', compact('empresas', 'tp_contribuyente', 'sis_identidad'));
    }

    function view_cuadro_comparativo()
    {
        $unidades_medida = $this->mostrar_unidad_medida();
        return view('logistica/cotizaciones/cuadro_comparativo', compact('unidades_medida'));
    }

    function view_generar_orden()
    {
        $condiciones = $this->select_condiciones();
        $tp_doc = $this->select_tp_doc();
        $bancos = $this->select_bancos();
        $cuentas = $this->select_tipos_cuenta();
        $responsables = $this->select_responsables();
        return view('logistica/ordenes/generar_orden', compact('condiciones', 'tp_doc', 'bancos', 'cuentas','responsables'));
    }
    function view_lista_proveedores()
    {
        return view('logistica/proveedores/lista_proveedores');
    }
    
    function listar_proveedores(){
        $output['data']=[];
        $prov = DB::table('contabilidad.adm_contri')
            ->select(
                'adm_contri.*',
                'adm_tp_contri.descripcion as tipo_contribuyente',
                DB::raw("CONCAT(sis_identi.descripcion,' ',adm_contri.nro_documento) as documento"),
                DB::raw("(CASE WHEN adm_contri.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_contri")

            )
            ->leftJoin('contabilidad.adm_tp_contri', 'adm_tp_contri.id_tipo_contribuyente', '=', 'adm_contri.id_tipo_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')

            ->where([['adm_contri.estado', '=', 1]])
            ->orderBy('adm_contri.id_contribuyente', 'asc')->get();

            foreach($prov as $data){
                $id_contribuyente = $data->id_contribuyente;
                $razon_social= $data->razon_social;
                $documento = $data->documento;
                $tipo_contribuyente = $data->tipo_contribuyente;
                $telefono = $data->telefono;
                $direccion_fiscal = $data->direccion_fiscal;
                $estado = $data->estado_contri;
 
                $accion =
                '<center><div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-log bg-default" title="Ver/Editar" onClick="editar_proveedor(' . $id_contribuyente . ');"><i class="fas fa-edit fa-xs"></i></button>
                </div></center>';

                $output['data'][] = array($id_contribuyente, $razon_social, $documento, $tipo_contribuyente, $telefono, $direccion_fiscal, $estado, $accion);


            }    
            return response()->json($output);
    }
    



    public function select_condiciones()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select('log_cdn_pago.id_condicion_pago', 'log_cdn_pago.descripcion')
            ->where('log_cdn_pago.estado', 1)
            ->orderBy('log_cdn_pago.descripcion')
            ->get();
        return $data;
    }
    public function select_tp_doc()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select('cont_tp_doc.id_tp_doc', 'cont_tp_doc.cod_sunat', 'cont_tp_doc.descripcion')
            ->where([['cont_tp_doc.estado', '=', 1]])
            ->orderBy('cont_tp_doc.id_tp_doc')
            ->get();
        return $data;
    }
    public function select_tp_contribuyente()
    {
        $data = DB::table('contabilidad.adm_tp_contri')
            ->select('adm_tp_contri.id_tipo_contribuyente', 'adm_tp_contri.descripcion')
            ->where('adm_tp_contri.estado', '=', 1)
            ->orderBy('adm_tp_contri.descripcion', 'asc')->get();
        return $data;
    }
    public function select_sis_identidad()
    {
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('sis_identi.estado', '=', 1)
            ->orderBy('sis_identi.descripcion', 'asc')->get();
        return $data;
    }
    public function select_mostrar_empresas()
    {
        $data = DB::table('administracion.adm_empresa')
            ->select('adm_empresa.id_empresa', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }
    public function select_bancos()
    {
        $data = DB::table('contabilidad.cont_banco')
            ->select('cont_banco.id_banco', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'cont_banco.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where('cont_banco.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')
            ->get();
        return $data;
    }
    public function select_tipos_cuenta()
    {
        $data = DB::table('contabilidad.adm_tp_cta')
            ->select('adm_tp_cta.id_tipo_cuenta', 'adm_tp_cta.descripcion')
            ->where('adm_tp_cta.estado', '=', 1)
            ->orderBy('adm_tp_cta.descripcion', 'asc')
            ->get();
        return $data;
    }
    public function select_responsables()
    {
        $data = DB::table('configuracion.sis_usua')
            ->select('sis_usua.id_usuario as id_responsable',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_responsable")

            )
            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->orderBy('sis_usua.id_usuario', 'asc')
            ->get();
        return $data;
    }

    public function mostrar_requerimientos()
    {
        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
            ->leftJoin('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->leftJoin('logistica.log_ord_compra', 'log_ord_compra.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            ->leftJoin('almacen.guia_com_oc', 'guia_com_oc.id_oc', '=', 'log_ord_compra.id_orden_compra')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_tp_req.descripcion AS tipo_req_desc',
                'sis_usua.usuario',
                'rrhh_rol.id_area',
                'adm_area.descripcion AS area_desc',
                'rrhh_rol.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_grupo',
                'adm_grupo.descripcion AS adm_grupo_descripcion',
                'alm_req.id_proyecto',
                'proy_proyecto.codigo AS proy_proyecto_codigo',
                'proy_proyecto.descripcion AS proy_proyecto_descripcion',
                'alm_req.concepto AS alm_req_concepto',
                'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                'alm_req.id_prioridad',
                'alm_req.fecha_registro',
                'alm_req.estado',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc"),
                DB::raw("(SELECT  COUNT(log_ord_compra.id_orden_compra) FROM logistica.log_ord_compra
        WHERE log_ord_compra.id_grupo_cotizacion = log_detalle_grupo_cotizacion.id_grupo_cotizacion)::integer as cantidad_orden"),
                DB::raw("(SELECT  COUNT(mov_alm.id_mov_alm) FROM almacen.mov_alm
        WHERE mov_alm.id_guia_com = guia_com_oc.id_guia_com and 
        guia_com_oc.id_oc = log_ord_compra.id_orden_compra)::integer as cantidad_entrada_almacen")

            )
            ->where([
                ['alm_req.estado', '=', 1]
            ])
            ->orderBy('alm_req.id_requerimiento', 'desc')
            ->get();
        return response()->json(["data" => $alm_req]);
    }

    public function mostrar_requerimiento($id, $codigo)
    {
        $requerimiento = $this->get_requerimiento($id, $codigo);
        return response()->json($requerimiento);
    }

    public function get_requerimiento($id, $codigo)
    {
        if ($id > 0) {
            $theWhere = ['alm_req.id_requerimiento', '=', $id];
        } else {

            $theWhere = ['alm_req.codigo', '=', $codigo];
        }
        $alm_req = DB::table('almacen.alm_req')
            ->join('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->join('administracion.adm_grupo', 'adm_grupo.id_grupo', '=', 'alm_req.id_grupo')
            ->join('administracion.sis_sede', 'sis_sede.id_sede', '=', 'adm_grupo.id_sede')
            ->leftJoin('configuracion.sis_usua', 'alm_req.id_usuario', '=', 'sis_usua.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->leftJoin('rrhh.rrhh_rol', 'alm_req.id_rol', '=', 'rrhh_rol.id_rol')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->leftJoin('administracion.adm_area', 'rrhh_rol.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
            ->leftJoin('comercial.com_cliente', 'proy_proyecto.cliente', '=', 'com_cliente.id_cliente')
            ->leftJoin('contabilidad.adm_contri', 'com_cliente.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->leftJoin('proyectos.proy_presup', 'alm_req.id_presupuesto', '=', 'proy_presup.id_presupuesto')
            ->select(
                'alm_req.id_requerimiento',
                'alm_req.codigo',
                'alm_req.concepto',
                'alm_req.id_moneda',
                'alm_req.id_prioridad',
                'alm_req.id_estado_doc',
                'sis_sede.id_empresa',
                'alm_req.id_grupo',
                'adm_grupo.id_sede',
                'alm_req.fecha_requerimiento',
                'alm_req.id_tipo_requerimiento',
                'alm_req.observacion',
                'alm_tp_req.descripcion AS tp_req_descripcion',
                'alm_req.id_usuario',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as persona"),
                'sis_usua.usuario',
                'alm_req.id_rol',
                'rrhh_rol.id_rol_concepto',
                'rrhh_rol_concepto.descripcion AS rrhh_rol_concepto',
                'alm_req.id_area',
                'adm_area.descripcion AS area_descripcion',
                'alm_req.id_proyecto',
                'proy_proyecto.descripcion AS descripcion_proyecto',
                'proy_proyecto.codigo AS codigo_presupuesto',
                'proy_proyecto.importe AS importe_presupuesto',
                'adm_contri.razon_social AS descripcion_cliente',
                'alm_req.id_presupuesto',
                'alm_req.objetivo',
                'alm_req.occ',
                'alm_req.archivo_adjunto',
                'alm_req.fecha_registro',
                'alm_req.estado',
                DB::raw("(CASE WHEN alm_req.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                $theWhere,
                ['alm_req.estado', '=', 1]
            ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();

        if (sizeof($alm_req) <= 0) {
            $alm_req = [];
            return response()->json($alm_req);
        } else {

            foreach ($alm_req as $data) {

                $id_requerimiento = $data->id_requerimiento;

                $requerimiento[] = [
                    'id_requerimiento' => $data->id_requerimiento,
                    'codigo' => $data->codigo,
                    'concepto' => $data->concepto,
                    // 'objetivo' => $data->objetivo, deprecated ( eliminar campo)
                    'id_moneda' => $data->id_moneda,
                    'id_estado_doc' => $data->id_estado_doc,
                    'id_prioridad' => $data->id_prioridad,
                    'occ' => $data->occ,
                    'id_empresa' => $data->id_empresa,
                    'id_grupo' => $data->id_grupo,
                    'id_sede' => $data->id_sede,
                    'fecha_requerimiento' => $data->fecha_requerimiento,
                    'id_tipo_requerimiento' => $data->id_tipo_requerimiento,
                    'tipo_requerimiento' => $data->tp_req_descripcion,
                    'id_usuario' => $data->id_usuario,
                    'persona' => $data->persona,
                    'usuario' => $data->usuario,
                    'id_rol' => $data->id_rol,
                    'id_area' => $data->id_area,
                    'area_descripcion' => $data->area_descripcion,
                    'archivo_adjunto' => $data->archivo_adjunto,
                    'id_proyecto' => $data->id_proyecto,
                    'descripcion_proyecto' => $data->descripcion_proyecto,
                    'codigo_presupuesto' => $data->codigo_presupuesto,
                    'descripcion_cliente' => $data->descripcion_cliente,
                    'importe_presupuesto' => $data->importe_presupuesto,
                    'id_presupuesto' => $data->id_presupuesto,
                    'observacion' => $data->observacion,
                    'fecha_registro' => $data->fecha_registro,
                    'estado' => $data->estado,
                    'estado_desc' => $data->estado_desc
                ];
            };

            $alm_det_req = DB::table('almacen.alm_item')
                ->rightJoin('almacen.alm_det_req', 'alm_item.id_item', '=', 'alm_det_req.id_item')
                ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->leftJoin('almacen.alm_prod', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
                ->leftJoin('logistica.log_servi', 'alm_item.id_servicio', '=', 'log_servi.id_servicio')
                ->leftJoin('logistica.log_tp_servi', 'log_tp_servi.id_tipo_servicio', '=', 'log_servi.id_tipo_servicio')

                ->leftJoin('almacen.alm_und_medida', 'alm_det_req.id_unidad_medida', '=', 'alm_und_medida.id_unidad_medida')
                ->leftJoin('almacen.alm_und_medida as und_medida_det_req', 'alm_det_req.id_unidad_medida', '=', 'und_medida_det_req.id_unidad_medida')
                // ->leftJoin('almacen.alm_clasif', 'alm_clasif.id_clasificacion', '=', 'alm_prod.id_clasif')
                // ->leftJoin('almacen.alm_subcategoria', 'alm_subcategoria.id_subcategoria', '=', 'alm_prod.id_subcategoria')
                // ->leftJoin('almacen.alm_cat_prod', 'alm_cat_prod.id_categoria', '=', 'alm_subcategoria.id_categoria')
                // ->leftJoin('almacen.alm_tp_prod', 'alm_tp_prod.id_tipo_producto', '=', 'alm_cat_prod.id_tipo_producto')
                ->leftJoin('logistica.equipo', 'alm_item.id_equipo', '=', 'equipo.id_equipo')

                ->leftJoin('almacen.alm_req_archivos', 'alm_req_archivos.id_detalle_requerimiento', '=', 'alm_det_req.id_detalle_requerimiento')

                ->leftJoin('finanzas.presup_par', 'presup_par.id_partida', '=', 'alm_det_req.partida')
                ->leftJoin('finanzas.presup_pardet', 'presup_pardet.id_pardet', '=', 'presup_par.id_pardet')

                ->select(
                    'alm_det_req.id_detalle_requerimiento',
                    'alm_req.id_requerimiento',
                    'alm_req.codigo AS codigo_requerimiento',
                    'alm_det_req.id_requerimiento',
                    'alm_det_req.id_item AS id_item_alm_det_req',
                    'alm_det_req.precio_referencial',
                    'alm_det_req.cantidad',
                    'alm_det_req.id_unidad_medida',
                    'und_medida_det_req.descripcion AS unidad_medida',
                    'alm_det_req.obs',
                    'alm_det_req.fecha_registro AS fecha_registro_alm_det_req',
                    'alm_det_req.fecha_entrega',
                    'alm_det_req.lugar_entrega',
                    'alm_det_req.descripcion_adicional',
                    'alm_det_req.id_tipo_item',
                    'alm_det_req.estado',

                    'alm_det_req.partida',
                    'presup_par.codigo AS codigo_partida',
                    'presup_pardet.descripcion AS descripcion_partida',

                    'alm_item.id_item',

                    'alm_item.id_producto',
                    'alm_item.codigo AS codigo_item',
                    'alm_item.fecha_registro AS alm_item_fecha_registro',
                    'alm_prod.codigo AS alm_prod_codigo',
                    'alm_prod.descripcion AS alm_prod_descripcion',

                    // 'alm_prod.id_unidad_medida AS prod_id_unidad_medida',
                    // 'alm_und_medida.abreviatura AS prod_unidad_medida_abreviatura',
                    // 'alm_und_medida.descripcion AS prod_unidad_medida_descripcion',

                    // 'alm_clasif.id_clasificacion',
                    // 'alm_clasif.descripcion AS alm_clasif_descripcion',
                    // 'alm_subcategoria.id_subcategoria',
                    // 'alm_subcategoria.descripcion AS alm_subcategoria_descripcion',
                    // 'alm_subcategoria.codigo AS alm_subcategoria_codigo',
                    // 'alm_cat_prod.id_categoria',
                    // 'alm_cat_prod.descripcion AS alm_cat_prod_descripcion',
                    // 'alm_cat_prod.codigo AS alm_cat_prod_codigo',
                    // 'alm_tp_prod.id_tipo_producto',
                    // 'alm_tp_prod.descripcion AS alm_tp_prod_descripcion',

                    'alm_item.id_servicio',
                    'log_servi.codigo as log_servi_codigo',
                    'log_servi.descripcion as log_servi_descripcion',
                    'log_servi.id_tipo_servicio',
                    'log_tp_servi.descripcion AS log_tp_servi_descripcion',

                    'alm_item.id_equipo',
                    'equipo.descripcion as equipo_descripcion',

                    'alm_req_archivos.id_archivo AS archivo_id_archivo',
                    'alm_req_archivos.archivo AS archivo_archivo',
                    'alm_req_archivos.estado AS archivo_estado',
                    'alm_req_archivos.fecha_registro AS archivo_fecha_registro',
                    'alm_req_archivos.id_detalle_requerimiento AS archivo_id_detalle_requerimiento'
                )
                ->where([
                    ['alm_det_req.id_requerimiento', '=', $requerimiento[0]['id_requerimiento']],
                    ['alm_det_req.estado', '=', 1]
                ])
                ->orderBy('alm_item.id_item', 'asc')
                ->get();

            // archivos adjuntos de items
            if (isset($alm_det_req)) {
                $detalle_requerimiento_adjunto = [];
                foreach ($alm_det_req as $data) {
                    $detalle_requerimiento_adjunto[] = [
                        'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                        'archivo_id_archivo' => $data->archivo_id_archivo,
                        'archivo_archivo' => $data->archivo_archivo,
                        'archivo_id_detalle_requerimiento' => $data->archivo_id_detalle_requerimiento,
                        'archivo_fecha_registro' => $data->archivo_fecha_registro,
                        'archivo_estado' => $data->archivo_estado
                    ];
                }
            } else {
                $detalle_requerimiento_adjunto = [];
            }


            if (isset($alm_det_req)) {
                $lastId = "";
                $detalle_requerimiento = [];
                foreach ($alm_det_req as $data) {
                    if ($data->id_detalle_requerimiento !== $lastId) {
                        $detalle_requerimiento[] = [
                            'id_detalle_requerimiento'  => $data->id_detalle_requerimiento,
                            'id_requerimiento'          => $data->id_requerimiento,
                            'codigo_requerimiento'      => $data->codigo_requerimiento,
                            'id_item'                   => $data->id_item,
                            'cantidad'                  => $data->cantidad,
                            'id_unidad_medida'             => $data->id_unidad_medida,
                            'unidad_medida'             => $data->unidad_medida,
                            'precio_referencial'        => $data->precio_referencial,
                            'descripcion_adicional'     => $data->descripcion_adicional,
                            'fecha_entrega'             => $data->fecha_entrega,
                            'lugar_entrega'             => $data->lugar_entrega,
                            'fecha_registro'            => $data->fecha_registro_alm_det_req,
                            'obs'                       => $data->obs,
                            'estado'                    => $data->estado,

                            'codigo_item'                => $data->codigo_item,
                            'id_tipo_item'                => $data->id_tipo_item,

                            'id_servicio'               => $data->id_servicio,
                            'log_servi_codigo'           => $data->log_servi_codigo,
                            'id_tipo_servicio'           => $data->id_tipo_servicio,
                            'log_tp_servi_descripcion'   => $data->log_tp_servi_descripcion,

                            'id_producto'               => $data->id_producto,
                            'codigo_producto'            => $data->alm_prod_codigo,
                            // 'descripcion'               => $requerimiento[0]["id_tipo_requerimiento"] ==1?$data->alm_prod_descripcion:($requerimiento[0]["id_tipo_requerimiento"] ==2?$data->log_servi_descripcion:''),
                            'descripcion'               => $data->id_tipo_item == 1 ? $data->alm_prod_descripcion : ($data->id_tipo_item == 2 ? $data->log_servi_descripcion : ($data->id_tipo_item == 3 ? $data->equipo_descripcion : $data->descripcion_adicional)),
                            // 'prod_id_unidad_medida'          => $data->prod_id_unidad_medida,
                            // 'prod_unidad_medida_abreviatura'    => $data->prod_unidad_medida_abreviatura,
                            // 'prod_unidad_medida_descripcion'    => $data->prod_unidad_medida_descripcion,
                            'id_equipo'               => $data->id_equipo,
                            // 'id_clasificacion'             => $data->id_clasificacion,
                            // 'alm_clasif_descripcion'       => $data->alm_clasif_descripcion,
                            // 'id_subcategoria'              => $data->id_subcategoria,
                            // 'alm_subcategoria_descripcion' => $data->alm_subcategoria_descripcion,
                            // 'alm_subcategoria_codigo'      => $data->alm_subcategoria_codigo,
                            // 'id_categoria'                 => $data->id_categoria,
                            // 'alm_cat_prod_descripcion'     => $data->alm_cat_prod_descripcion,
                            // 'alm_cat_prod_codigo'          => $data->alm_cat_prod_codigo,
                            // 'id_tipo_producto'             => $data->id_tipo_producto,
                            // 'alm_tp_prod_descripcion'      => $data->alm_tp_prod_descripcion,

                            'id_partida'                    => $data->partida,
                            'codigo_partida'                => $data->codigo_partida,
                            'descripcion_partida'           => $data->descripcion_partida

                        ];
                        $lastId = $data->id_detalle_requerimiento;
                    }
                }

                // insertar adjuntos
                for ($j = 0; $j < sizeof($detalle_requerimiento); $j++) {
                    for ($i = 0; $i < sizeof($detalle_requerimiento_adjunto); $i++) {
                        if ($detalle_requerimiento[$j]['id_detalle_requerimiento'] === $detalle_requerimiento_adjunto[$i]['id_detalle_requerimiento']) {
                            if ($detalle_requerimiento_adjunto[$i]['archivo_estado'] === NUll) {
                                $detalle_requerimiento_adjunto[$i]['archivo_estado'] = 0;
                            }
                            $detalle_requerimiento[$j]['adjunto'][] = $detalle_requerimiento_adjunto[$i];
                        }
                    }
                }
                // end insertar adjuntos

            } else {

                $detalle_requerimiento = [];
            }

            //get cotizaciones que tenga el requerimiento
            $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
                ->select(
                    'log_valorizacion_cotizacion.id_cotizacion'
                )
                ->where(
                    [
                        ['log_valorizacion_cotizacion.id_requerimiento', '=', $id_requerimiento]
                    ]
                )
                ->get();

            $cotizaciones = [];
            foreach ($log_valorizacion_cotizacion as $data) {
                if (in_array($data->id_cotizacion, $cotizaciones) === false) {
                    array_push($cotizaciones, $data->id_cotizacion);
                }
            }
            ////////////////////////////////////////////

            // get detalle grupo cot.
            $log_detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
                ->select(
                    'log_detalle_grupo_cotizacion.id_grupo_cotizacion'
                )
                ->whereIn('log_detalle_grupo_cotizacion.id_cotizacion', $cotizaciones)
                ->get();
        }
        $grupo_cotizacion = [];
        foreach ($log_detalle_grupo_cotizacion as $data) {
            if (in_array($data->id_grupo_cotizacion, $grupo_cotizacion) === false) {
                array_push($grupo_cotizacion, $data->id_grupo_cotizacion);
            }
        }

        $estado_req = $this->consult_estado($id_requerimiento);
        $req_observacion = [];
        // $detalle_req_observacion = '';
        // $descripcion_observacion='';

        if ($estado_req === 3) { // estado observado
            // $id_doc_aprob = $this->consult_doc_aprob($id_requerimiento);
            // $countAprob = $this->consult_aprob($id_doc_aprob);
            // $countObs = $this->consult_obs($id_doc_aprob);
            // $sgt_aprob = ($countAprob + 1);
            // $niv_aprob = $this->next_aprob($sgt_aprob);
            // $na_orden = $niv_aprob['orden'];
            // $na_flujo = $niv_aprob['flujo'];
            // $no_aprob = is_numeric($niv_aprob['rol_aprob'] ) >0 ?$niv_aprob['rol_aprob']:5; //id_rol
            // // $obs = $this->get_observacion($id_requerimiento,$na_flujo,$no_aprob,3);
            $req_observacion = $this->get_header_observacion($id_requerimiento);

        }
        $data = [
            "requerimiento" => $requerimiento,
            "det_req" => $detalle_requerimiento,
            "cotizaciones" => $cotizaciones,
            "grupo_cotizacion" => $grupo_cotizacion,
            // "no_aprob" => $no_aprob,
            "observacion_requerimiento" => $req_observacion ? $req_observacion : []
        ];

        return $data;
    }


    public function imprimir_requerimiento_pdf($id, $codigo)
    {
        $requerimiento = $this->get_requerimiento($id, $codigo);
        $now = new \DateTime();
        $html = '
        <html>
            <head>
            <style type="text/css">
                *{
                    box-sizing: border-box;
                }
                body{
                        background-color: #fff;
                        font-family: "DejaVu Sans";
                        font-size: 11px;
                        box-sizing: border-box;
                        padding:20px;
                }
                
                table{
                width:100%;
                }
                .tablePDF thead{
                    padding:4px;
                    background-color:#e5e5e5;
                }
                .tablePDF,
                .tablePDF tr td{
                    border: 0px solid #ddd;
                }
                .tablePDF tr td{
                    padding: 5px;
                }
                .subtitle{
                    font-weight: bold;
                }
                .bordebox{
                    border: 1px solid #000;
                }
                .verticalTop{
                    vertical-align:top;
                }
                .texttab { 
                    
                    display:block; 
                    margin-left: 20px; 
                    margin-bottom:5px;
                }
                .right{
                    text-align:right;
                }
                .left{
                    text-align:left;
                }
                .justify{
                    text-align: justify;
                }
                .top{
                vertical-align:top;
                }
            </style>
            </head>
            <body>
            <img src="./images/LogoSlogan-80.png" alt="Logo" height="75px">

                <h1><center>REQUERIMIENTO N°' . $requerimiento['requerimiento'][0]['codigo'] . '</center></h1>
                <br><br>
            <table border="0">
            <tr>
                <td class="subtitle">REQ. N°</td>
                <td class="subtitle verticalTop">:</td>
                <td width="40%" class="verticalTop">' . $requerimiento['requerimiento'][0]['codigo'] . '</td>
                <td class="subtitle verticalTop">Fecha</td>
                <td class="subtitle verticalTop">:</td>
                <td>' . $requerimiento['requerimiento'][0]['fecha_requerimiento'] . '</td>
            </tr>
            </tr>  
                <tr>
                    <td class="subtitle">Solicitante</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['persona'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle">Área</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop">' . $requerimiento['requerimiento'][0]['area_descripcion'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle top">Proyecto</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop justify" colspan="4" >' . $requerimiento['requerimiento'][0]['codigo_presupuesto'] . '-' . $requerimiento['requerimiento'][0]['descripcion_proyecto'] . '</td>
                </tr>    
                <tr>
                    <td class="subtitle">Presupuesto</td>
                    <td class="subtitle verticalTop">:</td>
                    <td class="verticalTop"></td>
                </tr>
                </table>
                <br>
                <hr>
                <br>
                <p class="subtitle">1.- DENOMINACIÓN DE LA ADQUISICIÓN</p>
                <div class="texttab">' . $requerimiento['requerimiento'][0]['concepto'] . '</div>';

        $html .=   '</div>
                <p class="subtitle">3.- DESCRIPCIÓN POR ITEM</p>
                <table width="100%" class="tablePDF" border=0>
                <thead>
                    <tr class="subtitle">
                        <td width="3%">#</td>
                        <td width="10%">Item</td>
                        <td width="30%">Descripcion</td>
                        <td width="9%">Fecha Entrega</td>
                        <td width="5%">Und.</td>
                        <td width="5%">Cant.</td>
                        <td width="6%">Precio Ref.</td>
                        <td width="7%">SubTotal</td>
                    </tr>   
                </thead>';
        $total = 0;
        foreach ($requerimiento['det_req'] as $key => $data) {
            $html .= '<tr>';
            $html .= '<td >' . ($key + 1) . '</td>';
            $html .= '<td >' . $data['codigo_item'] . '</td>';
            $html .= '<td >' . ($data['descripcion'] ? $data['descripcion'] : $data['descripcion_adicional']) . '</td>';
            $html .= '<td >' . $data['fecha_entrega'] . '</td>';
            $html .= '<td >' . $data['unidad_medida'] . '</td>';
            $html .= '<td class="right">' . $data['cantidad'] . '</td>';
            $html .= '<td class="right">S/.' . $data['precio_referencial'] . '</td>';
            $html .= '<td class="right">S/.' . $data['cantidad'] * $data['precio_referencial'] . '</td>';
            $html .= '</tr>';
            $total = $total + ($data['cantidad'] * $data['precio_referencial']);
        }
        $html .= '
            <tr>
                <td  class="right" style="font-weight:bold;" colspan="7">TOTAL</td>
                <td class="right">S/.' . $total . '</td>
            </tr>
            </table>
                <br/>
                <br/>
            
                <div class="right">Usuario: ' . $requerimiento['requerimiento'][0]['usuario'] . ' Fecha de Registro:' . $requerimiento['requerimiento'][0]['fecha_registro'] . '</div>
            </body>
            </html>';
        return $html;
    }

    public function generar_requerimiento_pdf($id, $codigo)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->imprimir_requerimiento_pdf($id, $codigo));
        return $pdf->stream();
        return $pdf->download('requerimiento.pdf');
    }

    public function mostrar_adjuntos($id_requerimiento)
    {
        $det_req = DB::table('almacen.alm_req')
            ->select('alm_det_req.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')

            ->where([
                ['alm_req.id_requerimiento', '=', $id_requerimiento],
                ['alm_req.estado', '=', 1]
            ])
            ->get();
        foreach ($det_req as $data) {
            $det_req_list[] = $data->id_detalle_requerimiento;
        }

        $archivos = DB::table('almacen.alm_req_archivos')
            ->select(
                'alm_req_archivos.id_archivo',
                'alm_req_archivos.id_detalle_requerimiento',
                'alm_req_archivos.id_valorizacion_cotizacion',
                'alm_req_archivos.archivo',
                'alm_req_archivos.estado',
                'alm_req_archivos.fecha_registro',
                DB::raw("(CASE WHEN almacen.alm_req_archivos.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->whereIn('alm_req_archivos.id_detalle_requerimiento', $det_req_list)
            ->orderBy('alm_req_archivos.id_archivo', 'asc')
            ->get();

        return response()->json($archivos);
    }

    public function mostrar_archivos_adjuntos($id_detalle_requerimiento)
    {

        $data = DB::table('almacen.alm_req_archivos')
            ->select(
                'alm_req_archivos.id_archivo',
                'alm_req_archivos.id_detalle_requerimiento',
                'alm_req_archivos.id_valorizacion_cotizacion',
                'alm_req_archivos.archivo',
                'alm_req_archivos.estado',
                'alm_req_archivos.fecha_registro',
                'alm_det_req.obs',
                DB::raw("(CASE WHEN almacen.alm_req_archivos.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'alm_req_archivos.id_detalle_requerimiento')

            ->where('alm_req_archivos.id_detalle_requerimiento', $id_detalle_requerimiento)
            ->orderBy('alm_req_archivos.id_archivo', 'asc')
            ->get();

        return response()->json($data);
    }
    public function mostrar_archivos_adjuntos_proveedor($id)
    {

        $data = DB::table('almacen.alm_req_archivos')
            ->select(
                'alm_req_archivos.id_archivo',
                'alm_req_archivos.id_detalle_requerimiento',
                'alm_req_archivos.id_valorizacion_cotizacion',
                'alm_req_archivos.archivo',
                'alm_req_archivos.estado',
                'alm_req_archivos.fecha_registro',
                DB::raw("(CASE WHEN almacen.alm_req_archivos.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where('alm_req_archivos.id_valorizacion_cotizacion', $id)
            ->orderBy('alm_req_archivos.id_archivo', 'asc')
            ->get();

        return response()->json($data);
    }


    public function guardar_archivos_adjuntos_proveedor(Request $request)
    {
        // $archivo_adjunto_length = count($request->only_adjuntos_proveedor);
        $detalle_adjunto = json_decode($request->detalle_adjuntos, true);
        // $detalle_adjuntos_length = count($detalle_adjunto);
        $name_file = '';
        foreach ($request->only_adjuntos_proveedor as $clave => $valor) {
            $file = $request->file('only_adjuntos_proveedor')[$clave];

            if (isset($file)) {
                $name_file = "COT" . time() . $file->getClientOriginalName();
                if ($request->id_valorizacion_cotizacion > 0 || $request->id_valorizacion_cotizacion !== NULL) {

                    $alm_req_archivos = DB::table('almacen.alm_req_archivos')->insertGetId(
                        [
                            // 'id_detalle_requerimiento'  => $request->id_detalle_requerimiento,
                            'id_valorizacion_cotizacion'  => $request->id_valorizacion_cotizacion,
                            'archivo'                   => $name_file,
                            'estado'                    => 1,
                            'fecha_registro'            => date('Y-m-d H:i:s')
                        ],
                        'id_archivo'
                    );
                    Storage::disk('archivos')->put("logistica/cotizacion/" . $name_file, \File::get($file));
                }
            } else {
                $name_file = null;
            }
        }
        return response()->json($alm_req_archivos);
    }

    public function mostrar_archivos_adjuntos_cotizacion($id_cotizacion)
    {

        $data = DB::table('logistica.log_cotizacion')
            ->select(
                'alm_req_archivos.id_archivo',
                'log_valorizacion_cotizacion.id_detalle_requerimiento',
                'alm_req_archivos.archivo',
                'alm_req_archivos.fecha_registro',
                'alm_req_archivos.estado'
            )
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            ->leftJoin('almacen.alm_req_archivos', 'alm_req_archivos.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
            ->where([
                ['log_cotizacion.id_cotizacion', '=', $id_cotizacion],
                ['log_cotizacion.estado', '=', 1],
                ['log_valorizacion_cotizacion.estado', '!=', 7]
                // ['alm_req_archivos.estado','=', 1]
            ])
            ->orderBy('alm_req_archivos.id_archivo', 'asc')
            ->get();


        return response()->json($data);
    }
    public function guardar_archivos_adjuntos(Request $request)
    {
        $archivo_adjunto_length = count($request->only_adjuntos);
        $detalle_adjunto = json_decode($request->detalle_adjuntos, true);
        $detalle_adjuntos_length = count($detalle_adjunto);
        $name_file = '';
        // if (is_array($adjuntos)) {}
        foreach ($request->only_adjuntos as $clave => $valor) {
            $file = $request->file('only_adjuntos')[$clave];

            if (isset($file)) {
                $name_file = "DR" . time() . $file->getClientOriginalName();
                if ($request->id_detalle_requerimiento > 0 || $request->id_detalle_requerimiento !== NULL) {

                    $alm_req_archivos = DB::table('almacen.alm_req_archivos')->insertGetId(
                        [
                            'id_detalle_requerimiento'  => $request->id_detalle_requerimiento,
                            'archivo'                   => $name_file,
                            'estado'                    => 1,
                            'fecha_registro'            => date('Y-m-d H:i:s')
                        ],
                        'id_archivo'
                    );
                    Storage::disk('archivos')->put("logistica/detalle_requerimiento/" . $name_file, \File::get($file));
                }
            } else {
                $name_file = null;
            }
        }
        //     for ($i=0; $i< $detalle_adjuntos_length; $i++){
        //         if($detalle_adjunto[$i]['id_archivo'] === 0 || $detalle_adjunto[$i]['id_archivo'] === null){

        //             $alm_req_archivos = DB::table('almacen.alm_req_archivos')->insertGetId(
        //                 [        
        //                     'id_detalle_requerimiento'  => $detalle_adjunto[$i]['id_detalle_requerimiento'],
        //                     'archivo'                   => $detalle_adjunto[$i]['archivo'],
        //                     'estado'                    => $detalle_adjunto[$i]['estado'],
        //                     'fecha_registro'            => date('Y-m-d H:i:s')
        //                 ],
        //                 'id_archivo'
        //             );
        //         }
        // }
        // if ($alm_req_archivos > 0){
        //     $value = $alm_req_archivos;
        // }else{
        //     $value = 0;
        // }
        return response()->json($alm_req_archivos);
    }

    public function guardar_requerimiento(Request $request)
    {
        // $abreviatura_documento = DB::table('administracion.adm_tp_docum')
        // ->select('adm_tp_docum.*')
        // ->where('adm_tp_docum.id_tp_documento', $request->requerimiento['id_tipo_requerimiento'])
        // ->first();

        $grupo_descripcion = DB::table('administracion.adm_grupo')
            ->select('adm_grupo.descripcion')
            ->where('adm_grupo.id_grupo', $request->requerimiento['id_grupo'])
            ->first();

        //---------------------GENERANDO CODIGO REQUERIMIENTO--------------------------
        // $mes = date('m',strtotime($request->requerimiento['fecha_requerimiento']));
        $mes = date('m', strtotime("now"));
        // $yyyy = date('Y',strtotime($request->requerimiento['fecha_requerimiento']));
        $yy = date('y', strtotime("now"));
        $yyyy = date('Y', strtotime("now"));
        // $anio = date('y',strtotime($request->requerimiento['fecha_requerimiento']));
        // $documento = $abreviatura_documento->abreviatura;
        $documento = 'RQ';
        $grupo = $grupo_descripcion->descripcion[0];
        $num = DB::table('almacen.alm_req')
            ->whereMonth('fecha_registro', '=', $mes)
            ->whereYear('fecha_registro', '=', $yyyy)
            ->count();
        $correlativo = $this->leftZero(4, ($num + 1));
        $codigo = "{$documento}{$grupo}-{$yy}{$correlativo}";
        //----------------------------------------------------------------------------
        $data_req = DB::table('almacen.alm_req')->insertGetId(
            [
                'codigo'                => $codigo,
                'id_tipo_requerimiento' => 1,
                'id_usuario'            => $request->requerimiento['id_usuario'],
                'id_rol'                => $request->requerimiento['id_rol'],
                'fecha_requerimiento'   => $request->requerimiento['fecha_requerimiento'],
                'concepto'              => $request->requerimiento['concepto'],
                'id_moneda'             => $request->requerimiento['id_moneda'],
                'id_grupo'              => $request->requerimiento['id_grupo'],
                'id_area'               => $request->requerimiento['id_area'],
                'id_proyecto'           => $request->requerimiento['id_proyecto'],
                'id_presupuesto'        => $request->requerimiento['id_presupuesto'],
                'id_prioridad'          => $request->requerimiento['id_prioridad'],
                'fecha_registro'        => date('Y-m-d H:i:s'),
                'estado'                => $request->requerimiento['estado'],
                // 'occ'                   => $request->requerimiento['occ'],
                'id_estado_doc'         => $request->requerimiento['id_estado_doc']
            ],
            'id_requerimiento'
        );

        $detalle_reqArray = $request->detalle;
        $count_detalle_req = count($detalle_reqArray);
        if ($count_detalle_req > 0) {
            for ($i = 0; $i < $count_detalle_req; $i++) {
                if ($detalle_reqArray[$i]['estado'] > 0) {
                    $alm_det_req = DB::table('almacen.alm_det_req')->insertGetId(

                        [
                            'id_requerimiento'      => $data_req,
                            'id_item'               => $detalle_reqArray[$i]['id_item'],
                            'precio_referencial'    => $detalle_reqArray[$i]['precio_referencial'],
                            'cantidad'              => $detalle_reqArray[$i]['cantidad'],
                            'fecha_entrega'         => $detalle_reqArray[$i]['fecha_entrega'],
                            'lugar_entrega'         => $detalle_reqArray[$i]['lugar_entrega'],
                            'descripcion_adicional' => $detalle_reqArray[$i]['des_item'],
                            'partida'               => $detalle_reqArray[$i]['id_partida'],
                            'id_unidad_medida'      => is_numeric($detalle_reqArray[$i]['id_unidad_medida']) == 1 ? $detalle_reqArray[$i]['id_unidad_medida'] : null,
                            'id_tipo_item'      => $detalle_reqArray[$i]['id_tipo_item'],
                            'estado'                => $detalle_reqArray[$i]['estado'],
                            'fecha_registro'        => date('Y-m-d H:i:s'),
                            'estado'                => 1
                        ],
                        'id_detalle_requerimiento'
                    );
                    // $count_det_partidas =count($detalle_reqArray[$i]['det_partidas']);
                    // if($count_det_partidas > 0){
                    //     for ($j=0; $j< $count_det_partidas; $j++){ 
                    //         $proy_pdetalle = DB::table('proyectos.proy_pdetalle')->insertGetId(
                    //             [
                    //                 'id_det_req'            => $alm_det_req, 
                    //                 'id_cd_partida'         => $detalle_reqArray[$i]['det_partidas'][$j]['id_cd_partida'],
                    //                 'id_gg_detalle'         => $detalle_reqArray[$i]['det_partidas'][$j]['id_gg_detalle'],
                    //                 'id_ci_detalle'         => $detalle_reqArray[$i]['det_partidas'][$j]['id_ci_detalle'],
                    //                 'cantidad'              => $detalle_reqArray[$i]['det_partidas'][$j]['cantidad'],
                    //                 'cantidad_anulada'      => $detalle_reqArray[$i]['det_partidas'][$j]['cantidad_anulada'],
                    //                 'importe_unitario'      => $detalle_reqArray[$i]['det_partidas'][$j]['importe_unitario'],
                    //                 'id_insumo'             => $detalle_reqArray[$i]['det_partidas'][$j]['id_insumo'],
                    //                 'id_item'               => $detalle_reqArray[$i]['det_partidas'][$j]['id_item'],
                    //                 'importe_parcial'       => $detalle_reqArray[$i]['det_partidas'][$j]['importe_parcial'],
                    //                 'unid_medida'           => $detalle_reqArray[$i]['det_partidas'][$j]['unid_medida'],
                    //                 'cantidad_ejec'         => $detalle_reqArray[$i]['det_partidas'][$j]['cantidad_ejec'],
                    //                 'importe_unitario_ejec' => $detalle_reqArray[$i]['det_partidas'][$j]['importe_unitario_ejec'],
                    //                 'importe_parcial_ejec'  => $detalle_reqArray[$i]['det_partidas'][$j]['importe_parcial_ejec'],
                    //                 'fecha_requerimiento'   => $detalle_reqArray[$i]['det_partidas'][$j]['fecha_requerimiento'],
                    //                 'usuario'               => $detalle_reqArray[$i]['det_partidas'][$j]['usuario'],
                    //                 'fecha_registro'        => $detalle_reqArray[$i]['det_partidas'][$j]['fecha_registro'],
                    //                 'estado'                => $detalle_reqArray[$i]['det_partidas'][$j]['estado'],
                    //             ],
                    //             'id_det_partida'
                    //         );
                    //     }
                    // }
                }
            }
        }

        $requerimiento_guardado = DB::table('almacen.alm_req')
            ->select('alm_req.*')
            ->where([
                ['alm_req.id_requerimiento', '=', $data_req]
            ])
            ->orderBy('alm_req.id_requerimiento', 'asc')
            ->get();
        $req_actual = array();
        foreach ($requerimiento_guardado as $data) {
            array_push(
                $req_actual,
                $data->id_requerimiento,
                $data->codigo,
                1
            );
        }


        $data_doc_aprob = DB::table('administracion.adm_documentos_aprob')->insertGetId(
            [
                'id_tp_documento' => $req_actual[2],
                'codigo_doc'      => $req_actual[1],
                'id_doc'          => $req_actual[0]

            ],
            'id_doc_aprob'
        );


        return response()->json($data_req);
    }


    public function actualizar_requerimiento(Request $request, $id)
    {
        $codigo = $request->requerimiento['codigo'];
        $usuario = $request->requerimiento['id_usuario'];
        $id_rol = $request->requerimiento['id_rol'];
        $fecha_req = $request->requerimiento['fecha_requerimiento'];
        $concepto = $request->requerimiento['concepto'];
        $moneda = $request->requerimiento['id_moneda'];
        $id_area = $request->requerimiento['id_area'];
        $id_proyecto = $request->requerimiento['id_proyecto'];
        $id_presup = $request->requerimiento['id_presupuesto'];
        $id_priori = $request->requerimiento['id_prioridad'];

        if ($id != NULL) {
            $data_requerimiento = DB::table('almacen.alm_req')->where('id_requerimiento', $id)
                ->update([
                    'codigo'                => $codigo,
                    'id_usuario'            => $usuario,
                    'id_rol'                => is_numeric($id_rol) == 1 ? $id_rol : null,
                    'fecha_requerimiento'   => $fecha_req,
                    'concepto'              => $concepto,
                    'id_moneda'             => is_numeric($moneda) == 1 ? $moneda : null,
                    'id_area'               => is_numeric($id_area) == 1 ? $id_area : null,
                    'id_proyecto'           => is_numeric($id_proyecto) == 1 ? $id_proyecto : null,
                    'id_presupuesto'        => is_numeric($id_presup) == 1 ? $id_presup : null,
                    'id_prioridad'          => is_numeric($id_priori) == 1 ? $id_priori : null
                ]);
            $count_detalle = count($request->detalle);
            if ($count_detalle > 0) {
                for ($i = 0; $i < $count_detalle; $i++) {
                    $id_det_req = $request->detalle[$i]['id_detalle_requerimiento'];
                    $id_item = $request->detalle[$i]['id_item'];
                    $precio_ref = $request->detalle[$i]['precio_referencial'];
                    $cantidad = $request->detalle[$i]['cantidad'];
                    $fecha_entrega = $request->detalle[$i]['fecha_entrega'];
                    $lugar_entrega = $request->detalle[$i]['lugar_entrega'];
                    $des_item = $request->detalle[$i]['des_item'];
                    $id_parti = $request->detalle[$i]['id_partida'];
                    $id_unit = $request->detalle[$i]['id_unidad_medida'];
                    $id_tipo_item = $request->detalle[$i]['id_tipo_item'];
                    $estado = $request->detalle[$i]['estado'];

                    if ($id_det_req > 0) {
                        $data_detalle = DB::table('almacen.alm_det_req')
                            ->where('id_detalle_requerimiento', '=', $id_det_req)
                            ->update([
                                'id_requerimiento'      => $id,
                                'id_item'               => is_numeric($id_item) == 1 ? $id_item : null,
                                'precio_referencial'    => $precio_ref,
                                'cantidad'              => $cantidad,
                                'fecha_entrega'         => $fecha_entrega,
                                'lugar_entrega'         => $lugar_entrega,
                                'descripcion_adicional' => $des_item,
                                'partida'               => is_numeric($id_parti) == 1 ? $id_parti : null,
                                'id_unidad_medida'      => is_numeric($id_unit) == 1 ? $id_unit : null,
                                'id_tipo_item'          => is_numeric($id_tipo_item) == 1 ? $id_tipo_item : null,
                                'estado'                => $estado
                            ]);
                    } else {
                        $data_detalle = DB::table('almacen.alm_det_req')->insertGetId(
                            [
                                'id_requerimiento'      => $id,
                                'id_item'               => $id_item,
                                'precio_referencial'    => $precio_ref,
                                'cantidad'              => $cantidad,
                                'fecha_entrega'         => $fecha_entrega,
                                'lugar_entrega'         => $lugar_entrega,
                                'descripcion_adicional' => $des_item,
                                'partida'               => $id_parti,
                                'id_unidad_medida'      => $id_unit,
                                'id_tipo_item'          => $id_tipo_item,
                                'estado'                => $estado,
                                'fecha_registro'        => date('Y-m-d H:i:s'),
                                'estado'                => 1
                            ],
                            'id_detalle_requerimiento'
                        );
                    }
                }
                return response()->json($data_detalle);
            }
            return response()->json($data_requerimiento);
        } else {
            return response(0);
        }
    }

    function mostrar_moneda()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select(
                'sis_moneda.id_moneda',
                'sis_moneda.descripcion',
                'sis_moneda.simbolo',
                'sis_moneda.estado',
                DB::raw("(CASE WHEN configuracion.sis_moneda.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['sis_moneda.estado', '=', 1]
            ])
            ->orderBy('sis_moneda.id_moneda', 'asc')
            ->get();
        return $data;
    }

    function mostrar_prioridad()
    {
        $data = DB::table('administracion.adm_prioridad')
            ->select(
                'adm_prioridad.id_prioridad',
                'adm_prioridad.descripcion'
            )
            ->where([
                ['adm_prioridad.estado', '=', 1]
            ])
            ->orderBy('adm_prioridad.id_prioridad', 'asc')
            ->get();
        return $data;
    }

    // function mostrar_tipo()
    // {
    //     $data = DB::table('almacen.alm_tp_req')
    //         ->select(
    //             'alm_tp_req.id_tipo_requerimiento',
    //             'alm_tp_req.descripcion'
    //         )
    //         ->orderBy('alm_tp_req.id_tipo_requerimiento', 'asc')
    //         ->get();
    //     return $data;
    // }
    public function cargar_estructura_org($id)
    {
        $html = '';
        $sql1 = DB::table('administracion.sis_sede')->where('id_empresa', '=', $id)->get();
        foreach ($sql1 as $row) {
            $id_sede = $row->id_sede;
            $html .= '<ul>';
            $sql2 = DB::table('administracion.adm_grupo')->where('id_sede', '=', $row->id_sede)->get();
            if ($sql2->count() > 0) {
                $html .=
                    '<li class="firstNode" onClick="showEfectOkc(' . $row->id_sede . ');">
                    <h5>+ <b> Sede - ' . $row->descripcion . '</b></h5>
                    <ul class="ul-nivel1" id="detalle-' . $row->id_sede . '">';
                foreach ($sql2 as $key) {
                    $id_grupo = $key->id_grupo;
                    $sql3 = DB::table('administracion.adm_area')->where('id_grupo', '=', $key->id_grupo)->get();
                    if ($sql3->count() > 0) {
                        $html .= '<li><b>Grupo - ' . $key->descripcion . '</b><ul class="ul-nivel2">';
                        foreach ($sql3 as $value) {
                            $id_area = $value->id_area;
                            $area = $value->descripcion;
                            $txtArea = "'" . $area . "'";
                            $html .= '<li id="' . $id_area . '" onClick="areaSelectModal(' . $id_sede . ', ' . $id_grupo . ', ' . $id_area . ', ' . $txtArea . ');"> ' . $area . '</li>';
                        }
                    } else {
                        $html .= '<li> ' . $key->descripcion . '</li>';
                    }
                    $html .= '</li></ul>';
                }
                $html .= '</li></ul>';
            } else {
                $html .= '<li>' . $row->descripcion . '</li>';
            }
            $html .= '</ul>';
        }

        return response()->json($html);
    }

    function mostrar_area()
    {
        $data = DB::table('administracion.adm_area')
            ->select(
                'adm_area.*',
                DB::raw("(CASE WHEN administracion.adm_area.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")

            )
            ->where([
                ['adm_area.estado', '=', 1]
            ])
            ->orderBy('adm_area.id_area', 'asc')
            ->get();
        return $data;
    }

    function mostrar_condicion_pago()
    {
        $data = DB::table('logistica.log_cdn_pago')
            ->select(
                'log_cdn_pago.*',
                DB::raw("(CASE WHEN logistica.log_cdn_pago.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['log_cdn_pago.estado', '=', 1]
            ])
            ->orderBy('log_cdn_pago.id_condicion_pago', 'asc')
            ->get();
        return $data;
    }

    function mostrar_tipo_documento()
    {
        $data = DB::table('contabilidad.cont_tp_doc')
            ->select(
                'cont_tp_doc.*',
                DB::raw("(CASE WHEN contabilidad.cont_tp_doc.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['cont_tp_doc.estado', '=', 1]
            ])
            ->orderBy('cont_tp_doc.id_tp_doc', 'asc')
            ->get();
        return $data;
    }

    function mostrar_unidad_medida()
    {
        $data = DB::table('almacen.alm_und_medida')
            ->select(
                'alm_und_medida.id_unidad_medida',
                'alm_und_medida.descripcion',
                'alm_und_medida.abreviatura',
                'alm_und_medida.estado',
                DB::raw("(CASE WHEN almacen.alm_und_medida.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['alm_und_medida.estado', '=', 1]
            ])
            ->orderBy('alm_und_medida.id_unidad_medida', 'asc')
            ->get();
        return $data;
    }

    function detalle_unidad_medida($id)
    {
        $data = DB::table('almacen.alm_und_medida')
            ->select(
                'alm_und_medida.*',
                DB::raw("(CASE WHEN almacen.alm_und_medida.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS estado_desc")
            )
            ->where([
                ['alm_und_medida.estado', '=', 1],
                ['alm_und_medida.id_unidad_medida', '=', $id]
            ])
            ->orderBy('alm_und_medida.id_unidad_medida', 'asc')
            ->first();
        return response()->json($data);
    }

    public function mostrar_items()
    {
        $data = DB::table('almacen.alm_item')
            ->select(
                'alm_item.id_item',
                'alm_item.codigo',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                DB::raw("(CASE 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                            ELSE 'nulo' END) AS descripcion
                            "),
                DB::raw("(CASE 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.descripcion
                            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'Servicio' 
                            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'Equipo' 
                            ELSE 'nulo' END) AS unidad_medida_descripcion
                            "),

                'alm_prod.id_unidad_medida',
                'alm_prod_ubi.stock'
            )
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            // ->where([
            // ['alm_prod_ubi.stock', '>', 0],
            // ['alm_prod_ubi.estado', '=', 1]
            // ])
            // ->limit(500)
            ->get();
        return response()->json(["data" => $data]);
    }

    public function mostrar_item($id_item)
    {
        $data = DB::table('almacen.alm_item')
            ->leftJoin('almacen.alm_prod', 'alm_item.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_prod_ubi', 'alm_prod_ubi.id_producto', '=', 'alm_prod.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->select(
                'alm_item.id_item',
                'alm_item.codigo',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                DB::raw("(CASE 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                ELSE 'nulo' END) AS descripcion
                "),
                'alm_prod.id_unidad_medida',
                'alm_und_medida.descripcion AS unidad_medida_descripcion',
                'alm_prod_ubi.stock'
            )
            ->where([
                ['alm_item.id_item', '=', $id_item]
            ])
            ->orderBy('alm_item.id_item', 'asc')
            ->get();
        return response()->json($data);
    }


    // proyectos
    public function mostrar_proyectos_contratos()
    {
        $data = DB::table('proyectos.proy_contrato')
            ->select(
                'proy_contrato.*',
                'proy_proyecto.descripcion',
                'adm_contri.razon_social',
                'sis_moneda.simbolo',
                'proy_proyecto.id_op_com',
                'proy_proyecto.empresa',
                'proy_presup.id_presupuesto',
                'proy_presup.codigo'
            )
            ->join('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'proy_contrato.id_proyecto')
            ->join('proyectos.proy_presup', 'proy_presup.id_contrato', '=', 'proy_contrato.id_contrato')
            ->join('comercial.com_cliente', 'proy_proyecto.cliente', '=', 'com_cliente.id_cliente')
            ->join('contabilidad.adm_contri', 'com_cliente.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'proy_contrato.moneda')
            ->where([['proy_contrato.estado', '=', 1]])
            ->get();
        return response()->json(['data' => $data]);
    }
    public function mostrar_proyecto($id)
    {
        $data = DB::table('proyectos.proy_proyecto')
            ->select('proy_proyecto.*', 'adm_contri.razon_social', 'sis_usua.usuario as nombre_elaborado')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'proy_proyecto.elaborado_por')
            ->join('comercial.com_cliente', 'proy_proyecto.cliente', '=', 'com_cliente.id_cliente')
            ->join('contabilidad.adm_contri', 'com_cliente.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->where([['proy_proyecto.id_proyecto', '=', $id]])
            ->get();

        $contratos = DB::table('proyectos.proy_contrato')
            ->select('proy_contrato.*', 'sis_moneda.simbolo', 'proy_tp_contrato.descripcion as tipo_contrato')
            ->join('configuracion.sis_moneda', 'sis_moneda.id_moneda', '=', 'proy_contrato.moneda')
            ->join('proyectos.proy_tp_contrato', 'proy_tp_contrato.id_tp_contrato', '=', 'proy_contrato.id_tp_contrato')
            ->where([['proy_contrato.id_proyecto', '=', $id]])
            ->get();

        $controles = DB::table('proyectos.proy_ctrl_fechas')
            ->select('proy_ctrl_fechas.*')
            ->where([['proy_ctrl_fechas.id_proyecto', '=', $id]])
            ->get();

        $aprobaciones = DB::table('administracion.adm_aprobacion')
            ->select('adm_aprobacion.*', 'adm_flujo.nombre as nombre_flujo', 'adm_vobo.descripcion', 'sis_usua.usuario')
            ->join('administracion.adm_documentos_aprob', 'adm_documentos_aprob.id_doc_aprob', '=', 'adm_aprobacion.id_doc_aprob')
            ->join('proyectos.proy_proyecto', 'adm_documentos_aprob.codigo_doc', '=', 'proy_proyecto.codigo')
            ->join('administracion.adm_flujo', 'adm_flujo.id_flujo', '=', 'adm_aprobacion.id_flujo')
            ->join('administracion.adm_vobo', 'adm_vobo.id_vobo', '=', 'adm_aprobacion.id_vobo')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'adm_aprobacion.id_usuario')
            ->where([['proy_proyecto.id_proyecto', '=', $id]])
            ->get();

        return response()->json(["proyecto" => $data, "contratos" => $contratos, "controles" => $controles, "aprobaciones" => $aprobaciones]);
    }


    

    public function aceptar_sustento(Request $request){
        $id = $request->id;
        $num_doc = $this->consult_doc_aprob($id); 
        $cantidad_aprobados = $this->consult_aprob($num_doc);
        $tamaño_flujo = $this->consult_tamaño_flujo($id); 

        if($cantidad_aprobados == $tamaño_flujo){ //si es la ultima aprobación

            $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id)->update(['id_estado_doc' => 2, 'observacion' => '']); //aprobado
        }else{ // si aun tiene aprobaciones pendientes

            $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id)->update(['id_estado_doc' => 12, 'observacion' => '']); //pendiente aprobacion
        }

        return response()->json($data);
    }

    public function get_current_user(){
        $userRolId = Auth::user()->login_rol;
        $userId = Auth::user()->id_usuario;
        $userName = Auth::user()->usuario;
        $userFullName = Usuario::find($userId)->trabajador->postulante->persona->nombre_completo;

        $user_current=[
            'userRolId'=> $userRolId,
            'userId'=> $userId,
            'userName'=> $userName,
            'userFullName'=> $userFullName
        ];
        return $user_current;
        
    }

    public function get_req_list(){
        $req = DB::table('almacen.alm_req')
        ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
        ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
        ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
        ->leftJoin('administracion.adm_grupo', 'alm_req.id_grupo', '=', 'adm_grupo.id_grupo')
        ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
        ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
        ->select(
            'alm_req.*',
            'adm_estado_doc.estado_doc',
            'alm_tp_req.descripcion AS tipo_requerimiento',
            'adm_prioridad.descripcion AS priori',
            'adm_grupo.descripcion AS grupo',
            'adm_area.descripcion AS area',
            'proy_proyecto.descripcion AS proyecto'
        )
        ->where('alm_req.estado', '=', 1)->orderBy('alm_req.id_requerimiento', 'DESC')
        ->get();



        return $req;
    }
        
    public function listar_requerimiento_v2(){
        // datos del usuario en sesión
        $userRolId= $this->get_current_user()['userRolId'];
        $userId= $this->get_current_user()['userId'];
        $userName= $this->get_current_user()['userName'];
        $userFullName= $this->get_current_user()['userFullName'];
        $output['data']=[];
        
        // datos del requerimiento
        foreach ($this->get_req_list() as $row) {
            $id_req = $row->id_requerimiento;
            $codigo = $row->codigo;
            $priori = $row->priori;
            $tp_req = $row->tipo_requerimiento;
            $id_usu = $row->id_usuario;
            $id_rol = $row->id_rol;
            $fec_rq = date('d/m/Y', strtotime($row->fecha_requerimiento));
            $id_est = $row->id_estado_doc;
            $estado = $row->estado_doc;
            $id_area = $row->id_area;
            $area = $row->area;
            $id_grp = $row->id_grupo;
            $grupo = $row->grupo;
            $proyec = $row->proyecto;
            $observacion_req = $row->observacion;
            $method = '';

            if ($id_area != null) {
                if ($id_area != 5) {
                    $gral = $area;
                } else {
                    $gral = $area . ' - GASTOS ADMINISTRATIVOS';
                }
            } else {
                if ($id_grp == 3) {
                    $gral = $proyec;
                }
            }

            
            if (strtolower($priori) == 'normal') {
                $flag = '<center><i class="fas fa-thermometer-empty green"></i></center>';
            } elseif (strtolower($priori) == 'normaaltal') {
                $flag = '<center><i class="fas fa-thermometer-half orange"></i></center>';
            } else {
                $flag = '<center><i class="fas fa-thermometer-full red"></i></center>';
            }

            $usuario = Usuario::find($id_usu)->trabajador->postulante->persona->nombre_completo;
            $empresa = Grupo::find($id_grp)->sede->empresa->contribuyente->razon_social;

            // numero de documento (adm_documentos_aprob.id_doc)
            $num_doc = $this->consult_doc_aprob($id_req); 

            // nivel de aprobación para conocer si el usuario esta en alguna fase del flujo Function(id_rol,id_prioridad) return id_fñujo, orden , id_rol
            $niv_aprob = $this->consult_nivel_aprob($userRolId);
            $na_orden = $niv_aprob['orden'];
            $na_flujo = $niv_aprob['flujo'];
            $no_aprob = $niv_aprob['rol_aprob'];
            $cantidad_aprobados =0;
            $cantidad_observados =0;
        

        // si el usuario esta dentro del flujo de aprobación
        if ($na_orden > 0) {
            // cantidad de aprobaciones del req
            $cantidad_aprobados = $this->consult_aprob($num_doc); 
            // cantidad de observaciones del req
            $cantidad_observados = $this->consult_obs($num_doc); 
            // consultar rol aprobacion 
            
        }
        
        $cnc_rol = $this->consult_rol_aprob($no_aprob?$no_aprob: $userRolId);

        // buscar ultima aprobacion registrada return id_flujo 
        $last_aprob = $this->last_aprob($num_doc);
        $id_flujo_last_aprob = $last_aprob['id_flujo'];
   

        $ap_apr = "'aprobar'";
        $ap_obs = "'observar'";
        $ap_dng = "'denegar'";
        $ap_sus = "'aprobar_sustento'";
        $status = "";
        $aprobs = "";
        $method='';
       

        $containerOpenBrackets='<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
        $containerCloseBrackets='</div></center>';
        
        $btnEditar='<button type="button" class="btn btn-sm btn-log bg-primary" title="Ver o editar" onClick="editarListaReq(' . $id_req . ');"><i class="fas fa-edit fa-xs"></i></button>';
        $btnDetalleRapido='<button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $num_doc . ');"><i class="fas fa-eye fa-xs"></i></button>';
        $btnSolicitudCotizacion=' <button type="button" class="btn btn-sm btn-log btn-info" title="Crear solicitud de cotización" onClick="crearCoti(' . $id_req . ');"><i class="fas fa-file fa-xs"></i></button>';
        $btnAprobar=' <button type="button" class="btn btn-sm btn-log bg-green" title="Aprobar" onClick="atender_requerimiento(' . $id_req . ', ' . $num_doc . ', ' . $na_flujo . ', ' . $ap_apr . ');"><i class="fas fa-check fa-xs"></i></button>';
        $btnObservar='<button type="button" class="btn btn-sm btn-log bg-yellow" title="Observar" onClick="atender_requerimiento(' . $id_req . ', ' . $num_doc . ', ' . $na_flujo . ', ' . $ap_obs . ');"><i class="fas fa-exclamation-triangle fa-xs"></i><span class="badge badge-light">0</span></button>';
        $btnDenegar='<button type="button" class="btn btn-sm btn-log bg-red" title="Denegar" onClick="atender_requerimiento(' . $id_req . ', ' . $num_doc . ', ' . $na_flujo . ', ' . $ap_dng . ');"><i class="fas fa-ban fa-xs"></i></button>';
        $btnAceptarSustento='<button type="button" class="btn btn-sm btn-log bg-black" title="Aceptar Sustento" onClick="atender_requerimiento(' . $id_req . ', ' . $num_doc . ', ' . $na_flujo . ', ' . $ap_sus . ');"><i class="fas fa-check fa-xs"></i></button>';

        if($cnc_rol =='JEFE DE LOGISTICA' || $cnc_rol =='INSPECTOR DE FASES DE APROBACION'){
            $aprobs .=   $btnObservar;
            if($observacion_req == 'OBSERVADO LOGISTICA'){
                $aprobs .=   $btnAceptarSustento;
            }
        }
        $hasObsDetalleReq= $this->hasObsDetReq($id_req);// cantidad  valores  =t en  alm_det_req


        $descripcion_rol_last_obs_log='';
        if($id_flujo_last_aprob >0){
            // buscar ultima observado por logistica 
            if($cantidad_observados >0){
                $last_obs_log = $this->last_obs_log($num_doc);
                $id_rol_last_obs_log = $last_obs_log['id_rol'];
                $descripcion_rol_last_obs_log = $this->consult_rol_aprob($id_rol_last_obs_log);
                $id_vobo_last_obs_log = $last_obs_log['id_vobo'];
            }

            // buscar orden de la ultima aprobacion  mediante el id_flujo de la ultima aprobacion
            $nro_orden_last_aprob = $this->get_nro_orden_by_flujo($id_flujo_last_aprob);

            // obterner el id_rol de la siguiente aprobacion
            $next_apro = $this->next_aprob($nro_orden_last_aprob+1);
            // $id_flujo_next_aprob = $next_apro['id_flujo'];
            // $nro_orden_next_aprob = $next_apro['orden'];
            $id_rol_next_aprob = $next_apro['rol_aprob']?$next_apro['rol_aprob']:0;
 

            // si al usuario le corresponde una aprobacion.
            if($id_rol_next_aprob == $userRolId){ // le corrresponde aprobacion
                switch ($id_est) {
                    case '12': // si estado dedocumento es pendiente aprobacion
                        $status = '<center><label class="text-black">Pendiente Aprobación</label></center>';
                        $method .= $btnDetalleRapido;
                        $aprobs .=  $btnAprobar.$btnObservar.$btnDenegar;
                        break;

                    case '2': // aprobado
                        $status = '<center><label class="text-black">Aprobado</label></center>';
                        $method .= $btnDetalleRapido;
                        break;
                    case '3': // observado
                        $status = '<center><label class="text-black">Observado</label></center>';
                        $method .= $btnDetalleRapido.$btnEditar;
                        break;

                        case '4': // denegado
                        $status = '<center><label class="text-black">Denegado</label></center>';
                        $method .= $btnDetalleRapido;
                        break;

                    case '5': // atendido
                        $status = '<center><label class="text-black">Atendido</label></center>';
                        $method .= $btnDetalleRapido;
                        break;

                    case '6': // en almacen
                        $status = '<center><label class="text-black">En Almacen</label></center>';
                        $method .= $btnDetalleRapido;
                        break;

                    case '9': // procesado
                        $status = '<center><label class="text-black">Procesado</label></center>';
                        $method .= $btnDetalleRapido;
                        break;
                    case '13': // sustentado
                        $status = '<center><label class="text-black">Sustentado</label></center>';
                        $method .= $btnDetalleRapido;
                        if(($descripcion_rol_last_obs_log =='JEFE DE LOGISTICA' || $descripcion_rol_last_obs_log =='INSPECTOR DE FASES DE APROBACION')  && $id_vobo_last_obs_log ==3 && trim($estado)=="Sustentado"){
                            $aprobs .=  '';

                        }else{

                            $aprobs .=  $btnAprobar.$btnObservar.$btnDenegar;
                        }
                        break;
    

                    default:
                        $method= $id_rol_next_aprob.$userRolId.$id_est;
                        # code...
                        break;
                }
            }else{ // si next_apro_id_rol = 0 
                switch ($id_est) {
                    case '2': // aprobado
                        $status = '<center><label class="text-black">Aprobado</label></center>';
                        $method .= $btnDetalleRapido;

                        if($cnc_rol =='JEFE DE LOGISTICA' || $cnc_rol =='INSPECTOR DE FASES DE APROBACION'){
                            $method .=   $btnSolicitudCotizacion;
                        }
                        break;
                    case '3': // observado
                        $status = '<center><label class="text-black">Observado</label></center>';
                        $method .= $btnDetalleRapido;
                        if($userId == $id_usu){
                            $method .= $btnEditar;
                        }
                        break;

                    case '13': // sustentado    
                        $status = '<center><label class="text-black">Sustentado</label></center>';
                        $method .= $btnDetalleRapido;
                        break;

                    case '5': // atendido
                        $status = '<center><label class="text-black">Atendido</label></center>';
                        $method .= $btnDetalleRapido;
                        break;
                }
            }



        }else{ // no tiene ninguna aprobacion



            switch ($id_est) {

                    case '1': // elaborado
                        $status = '<center><label class="text-black">Elaborado</label></center>';
                        $method .= $btnDetalleRapido;
                        break;
                    case '2': // aprobado
                        $status = '<center><label class="text-black">Aprobado</label></center>';
                        $method .= $btnDetalleRapido;

                        if($cnc_rol =='JEFE DE LOGISTICA' || $cnc_rol =='INSPECTOR DE FASES DE APROBACION'){
                            $method .=   $btnSolicitudCotizacion;
                        }
                        break;
                    case '4': // denegado
                        $status = '<center><label class="text-black">Denegado</label></center>';
                        $method .= $btnDetalleRapido;
                        break;

                    case '5': // atendido
                        $status = '<center><label class="text-black">Atendido</label></center>';
                        $method .= $btnDetalleRapido;
                        break;

                    case '6': // en almacen
                        $status = '<center><label class="text-black">En Almacen</label></center>';
                        $method .= $btnDetalleRapido;
                        break;

                    case '9': // procesado
                        $status = '<center><label class="text-black">Procesado</label></center>';
                        $method .= $btnDetalleRapido;
                        break;

                    case '12': // pendiente aprobacion    
                        $status = '<center><label class="text-black">Pendiente Aprobación</label></center>';
                        $method .= $btnDetalleRapido;

                    break;
                    case '13': // sustentado    
                        $status = '<center><label class="text-black">Sustentado</label></center>';
                        $method .= $btnDetalleRapido;
                        if($hasObsDetalleReq >0){ // aun existe un det_req con obs = t
                            $status = '<center><label class="text-black">Sustentado </br>['.$hasObsDetalleReq.' Obs. Pend.]</label></center>';
                            $method .= $btnEditar;
                        }

                    break;
                    case '3': // observado
                        $status = '<center><label class="text-black">Observado</label></center>';
                        $method .= $btnDetalleRapido;
                        if($userId == $id_usu){
                            $method .= $btnEditar;
                        }
                        break;
                    
                    default:
                        $status ='--';
                        $method .= $btnDetalleRapido;
                        break;
                }

                $cantidad_aprobados = $this->consult_aprob($num_doc); 
                if($cantidad_aprobados == "" || $cantidad_aprobados == null ){
                    // $status = '<center><label class="text-black">AUN NINGUNA APROBACIÓN</label></center>';
                }
                if($cantidad_aprobados <= 0){
                    $id_rol_first_aprob = $this->consulta_primera_aprob($id_req)['id_rol'];
                    if($id_rol_first_aprob == "" || $id_rol_first_aprob == null ){
                        $status = '<center><label class="text-black">Error </label></center>';
                        $aprobs = 'El grupo del requerimiento no existe en el flujo';
                        $method = 'No existe id_operacion en adm_flujo';
                    }
                    if($id_rol_first_aprob == $userRolId){ // le corrresponde la primera aprobacion
                        $aprobs .=  $btnAprobar.$btnObservar.$btnDenegar;
                    }
                }
        }
        $groupMethod = $containerOpenBrackets. $method. $containerCloseBrackets;
        $groupAprob = $containerOpenBrackets. $aprobs. $containerCloseBrackets;
        $action = $groupMethod . $groupAprob;

        $output['data'][] = array($flag, $codigo, $fec_rq, $tp_req, $empresa, $gral, $usuario, $status, $action);
        
    }
        return response()->json($output);
    }

 


    // public function listar_requerimiento()
    // {
    //     $output = array('data' => array());
    //     $miRol = Auth::user()->login_rol;
    //     $req = DB::table('almacen.alm_req')
    //         ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
    //         ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
    //         ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
    //         ->leftJoin('administracion.adm_grupo', 'alm_req.id_grupo', '=', 'adm_grupo.id_grupo')
    //         ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
    //         ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
    //         ->select(
    //             'alm_req.*',
    //             'adm_estado_doc.estado_doc',
    //             'alm_tp_req.descripcion AS tipo_requerimiento',
    //             'adm_prioridad.descripcion AS priori',
    //             'adm_grupo.descripcion AS grupo',
    //             'adm_area.descripcion AS area',
    //             'proy_proyecto.descripcion AS proyecto'
    //         )
    //         ->where('alm_req.estado', '=', 1)->orderBy('alm_req.id_requerimiento', 'DESC')
    //         ->get();

    //     $total_aprob = $this->totalAprobOp(1);

    //     foreach ($req as $row) {
    //         $id_req = $row->id_requerimiento;
    //         $codigo = $row->codigo;
    //         $priori = $row->priori;
    //         $tp_req = $row->tipo_requerimiento;
    //         $id_usu = $row->id_usuario;
    //         $id_rol = $row->id_rol;
    //         $fec_rq = date('d/m/Y', strtotime($row->fecha_requerimiento));
    //         $id_est = $row->id_estado_doc;
    //         $estado = $row->estado_doc;
    //         $id_area = $row->id_area;
    //         $area = $row->area;
    //         $id_grp = $row->id_grupo;
    //         $grupo = $row->grupo;
    //         $proyec = $row->proyecto;
    //         $method = '';

    //         if ($id_area != null) {
    //             if ($id_area != 5) {
    //                 $gral = $area;
    //             } else {
    //                 $gral = $area . ' - GASTOS ADMINISTRATIVOS';
    //             }
    //         } else {
    //             if ($id_grp == 3) {
    //                 $gral = $proyec;
    //             }
    //         }

    //         $usuario = Usuario::find($id_usu)->trabajador->postulante->persona->nombre_completo;
    //         $empresa = Grupo::find($id_grp)->sede->empresa->contribuyente->razon_social;

    //         if (strtolower($priori) == 'normal') {
    //             $flag = '<center><i class="fas fa-thermometer-empty green"></i></center>';
    //         } elseif (strtolower($priori) == 'normaaltal') {
    //             $flag = '<center><i class="fas fa-thermometer-half orange"></i></center>';
    //         } else {
    //             $flag = '<center><i class="fas fa-thermometer-full red"></i></center>';
    //         }

    //         $niv_aprob = $this->consult_nivel_aprob($miRol);
    //         $na_orden = $niv_aprob['orden'];
    //         $na_flujo = $niv_aprob['flujo'];
    //         $no_aprob = $niv_aprob['rol_aprob'];
    //         $doc_aprob = 0;

    //         // aprobacion            
    //         if ($na_orden > 0) {
    //             $doc_aprob = $this->consult_doc_aprob($id_req);
    //             $cnc_rol = $this->consult_rol_aprob($no_aprob);
    //             if ($doc_aprob > 0) {
    //                 $aprobados = $this->consult_aprob($doc_aprob) + 1;
    //                 $ap_apr = "'aprobar'";
    //                 $ap_obs = "'observar'";
    //                 $ap_dng = "'denegar'";
    //                 $ap_sus = "'aprobar_sustento'";
    //                 $status ='';
    //                 $aprobs = '';

    //                 if ($aprobados == $na_orden &&  $cnc_rol != 'INSPECTOR DE FASES DE APROBACION') { // si le corresponde dar aprobacion y no sea del 'rrhh_rol_concepto.descripcion' = INSPECTOR DE FASES DE APROBACION
    //                     if ($id_est == 4) { // si es denegado
    //                         // 
    //                     } else {
                            
    //                             if($id_est !=13){

    //                                 $aprobs =
    //                                 '<div class="btn-group" role="group">
    //                                 <button type="button" class="btn btn-sm btn-log bg-green" title="Aprobar" onClick="atender_requerimiento(' . $id_req . ', ' . $doc_aprob . ', ' . $na_flujo . ', ' . $ap_apr . ');"><i class="fas fa-check fa-xs"></i></button>
    //                                 <button type="button" class="btn btn-sm btn-log bg-yellow" title="Observar" onClick="atender_requerimiento(' . $id_req . ', ' . $doc_aprob . ', ' . $na_flujo . ', ' . $ap_obs . ');"><i class="fas fa-exclamation-triangle fa-xs"></i><span class="badge badge-light">0</span></button>
    //                                 <button type="button" class="btn btn-sm btn-log bg-red" title="Denegar" onClick="atender_requerimiento(' . $id_req . ', ' . $doc_aprob . ', ' . $na_flujo . ', ' . $ap_dng . ');"><i class="fas fa-ban fa-xs"></i></button>
    //                                 </div>';
    //                             }

    //                     }
    //                 } else { // si no pertenece a orden de aprobación
    //                     $aprobs = '';
    //                     if (($cnc_rol == 'INSPECTOR DE FASES DE APROBACION' || $cnc_rol == 'JEFE DE LOGISTICA') && ( $id_est != 13 || $id_est != 4)) {

    //                         $status = '<center><label class="text-black">observado</label></center>';
    //                         $aprobs =  '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                                 <button type="button" class="btn btn-sm btn-log bg-yellow" title="Observar" onClick="atender_requerimiento(' . $id_req . ', ' . $doc_aprob . ', ' . $na_flujo . ', ' . $ap_obs . ');"><i class="fas fa-exclamation-triangle fa-xs"></i><span class="badge badge-light">0</span></button>
    //                                 </div></center>';

    //                                 if ($id_est == 13) { // si es sustentado
    //                                     $status = '<center><label class="text-black">Pendiente de Aceptar Sustento</label></center>';
    //                                     $aprobs =
    //                                     '<div class="btn-group" role="group">
    //                                     <button type="button" class="btn btn-sm btn-log bg-black" title="Aceptar Sustento" onClick="atender_requerimiento(' . $id_req . ', ' . $doc_aprob . ', ' . $na_flujo . ', ' . $ap_sus . ');"><i class="fas fa-check fa-xs"></i></button>
    //                                     <button type="button" class="btn btn-sm btn-log bg-yellow" title="Observar" onClick="atender_requerimiento(' . $id_req . ', ' . $doc_aprob . ', ' . $na_flujo . ', ' . $ap_obs . ');"><i class="fas fa-exclamation-triangle fa-xs"></i><span class="badge badge-light">0</span></button>
    //                                     <button type="button" class="btn btn-sm btn-log bg-red" title="Denegar" onClick="atender_requerimiento(' . $id_req . ', ' . $doc_aprob . ', ' . $na_flujo . ', ' . $ap_dng . ');"><i class="fas fa-ban fa-xs"></i></button>
    //                                     </div>';
    //                             }
    //                     }
    //                 }
    //             } else {
    //                 $aprobs = '';
    //             }
    //         } else {
    //             $aprobs = '';
    //         }

    //         //  estado del documento - opcion informativas
    //         if ($id_est == 1) {
    //             $status = '<center><label class="text-primary">Elaborado</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-primary" title="Ver o editar" onClick="editarListaReq(' . $id_req . ');"><i class="fas fa-edit fa-xs"></i></button>
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 2) {
    //             $status = '<center><label class="text-success">Aprobado</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //                 <button type="button" class="btn btn-sm btn-log btn-info" title="Crear solicitud de cotización" onClick="crearCoti(' . $id_req . ');"><i class="fas fa-file fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 3) {
    //             $status = '<center><label class="text-warning">Observado</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-primary" title="Ver o editar" onClick="editarListaReq(' . $id_req . ');"><i class="fas fa-edit fa-xs"></i></button>
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 13) {
    //             $status = '<center><label class="text-warning">Sustentado</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 4) {
    //             $status = '<center><label class="text-danger">Denegado</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 5) {
    //             $status = '<center><label class="text-success">Atendido</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 6) {
    //             $status = '<center><label class="text-success">En Almacen</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 9) {
    //             $status = '<center><label class="text-danger">Procesado</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 12) {
    //             $status = '<center><label class="text-success">Pendiente de Aprobación</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         } elseif ($id_est == 5) {
    //             $status = '<center><label class="text-success">Atentido</label></center>';
    //             $method =
    //                 '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
    //                 <button type="button" class="btn btn-sm btn-log bg-maroon" title="Ver detalle rápido" onClick="viewFlujo(' . $id_req . ', ' . $doc_aprob . ');"><i class="fas fa-eye fa-xs"></i></button>
    //             </div></center>';
    //         }

    //         $action = $method . $aprobs;

    //         $output['data'][] = array($flag, $codigo, $fec_rq, $tp_req, $empresa, $gral, $usuario, $status, $action);
    //     }
    //     return response()->json($output);
    // }

    function hasObsDetReq($idReq){ 
        $ObsDetReq = DB::table('almacen.alm_det_req')
        ->where([
            ['alm_det_req.obs', '=', 't'],
            ['alm_det_req.id_requerimiento', '=', $idReq]

        ])
        ->count();
        return $ObsDetReq;
    }

    function consulta_prev_rol_obs($idReq){ // solo considera si existe un item obs.. pero no distingue si son mas de un item, caso que se sustente uno y el otro item no, tendra el mismo resultado que solo un item
        $lastAccion = DB::table('almacen.alm_req_obs')
        ->where([
            ['alm_req_obs.id_requerimiento', '=', $idReq]
        ])
        ->orderby('alm_req_obs.fecha_registro', 'desc')
        ->first()->accion;
        if($lastAccion !== ''){

       
         if($lastAccion =='SUSTENTO'){
            $prev = DB::table('almacen.alm_req_obs')
            ->where([
                ['alm_req_obs.id_requerimiento', '=', $idReq],
                ['alm_req_obs.accion', '=', 'OBSERVADO']
            ])
            ->orderby('alm_req_obs.fecha_registro', 'desc')
            ->first();

            $usuario = $prev->id_usuario;

            $rolUsu = DB::table('configuracion.sis_usua')
            ->select(
                'rrhh_rol.id_rol',
                'rrhh_rol_concepto.descripcion')
            ->leftJoin('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol', 'rrhh_trab.id_trabajador', '=', 'rrhh_rol.id_trabajador')
            ->leftJoin('rrhh.rrhh_rol_concepto', 'rrhh_rol.id_rol_concepto', '=', 'rrhh_rol_concepto.id_rol_concepto')
            ->where([
                ['sis_usua.id_usuario', '=', $usuario]
            ])
            ->get();

            
            foreach($rolUsu as $data){
                $output[]=[
                    'id_rol'=>$data->id_rol,
                    'descripcion'=>$data->descripcion,
                ];
            }

            return $output;
    
        }else{
            return 0;
        }
    }
    }

    function mostrar_requerimiento_id($id, $type)
    {
        $sql = DB::table('almacen.alm_req')
            ->leftJoin('administracion.adm_estado_doc', 'alm_req.id_estado_doc', '=', 'adm_estado_doc.id_estado_doc')
            ->leftJoin('almacen.alm_tp_req', 'alm_req.id_tipo_requerimiento', '=', 'alm_tp_req.id_tipo_requerimiento')
            ->leftJoin('administracion.adm_prioridad', 'alm_req.id_prioridad', '=', 'adm_prioridad.id_prioridad')
            ->leftJoin('administracion.adm_grupo', 'alm_req.id_grupo', '=', 'adm_grupo.id_grupo')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->leftJoin('proyectos.proy_proyecto', 'alm_req.id_proyecto', '=', 'proy_proyecto.id_proyecto')
            ->select(
                'alm_req.*',
                'adm_estado_doc.estado_doc',
                'alm_tp_req.descripcion AS tipo_requerimiento',
                'adm_prioridad.descripcion AS priori',
                'adm_grupo.descripcion AS grupo',
                'adm_area.descripcion AS area',
                'proy_proyecto.descripcion AS proyecto'
            )
            ->where('alm_req.id_requerimiento', '=', $id)->get();
        $html = '';

        foreach ($sql as $row) {
            $code = $row->codigo;
            $motivo = $row->concepto;
            $comentario = $row->observacion;
            $id_usu = $row->id_usuario;
            $grupo = $row->id_grupo;
            $area_id = $row->id_area;
            $proy_id = $row->id_proyecto;
            $date = date('d/m/Y', strtotime($row->fecha_requerimiento));
            $moneda = $row->id_moneda;

            if ($grupo == 3) {
                if ($proy_id != null) {
                    $destino = $row->proyecto;
                } else {
                    $destino = $row->area . ' - GASTOS ADMINISTRATIVOS';
                }
            } else {
                if ($area_id != 6) {
                    $destino = $row->area;
                } else {
                    $destino = $row->area . ' - ' . $row->occ;
                }
            }

            $responsable = Usuario::find($id_usu)->trabajador->postulante->persona->nombre_completo;
            $simbol = $this->consult_moneda($moneda);
        }

        $html =
            '<table width="100%">
            <thead>
                <tr>
                    <th width="140">Código:</th>
                    <td>' . $code . '</td>
                </tr>
                <tr>
                    <th width="140">Motivo:</th>
                    <td>' . $motivo . '</td>
                </tr>
                <tr>
                    <th width="140">Comentario:</th>
                    <td>' . $comentario . '</td>
                </tr>
                <tr>
                    <th width="140">Responsable:</th>
                    <td>' . $responsable . '</td>
                </tr>
                <tr>
                    <th>Area o Servicio:</th>
                    <td>' . $destino . '</td>
                </tr>
                <tr>
                    <th>Fecha:</th>
                    <td colspan="2">' . $date . '</td>
                </tr>
                <tr>';
        if ($type == 1) {
            $html .=
                '<th>Moneda:</th>
                    <td>' . $simbol . '</td>';
        } elseif ($type == 2) {
            $html .=
                '<th>Moneda:</th>
                    <td>' . $simbol . '</td>
                    <td width="100" align="right"><button class="btn btn-primary" onClick="imprimirReq(' . $id . ');"><i class="fas fa-print"></i> Imprimir formato</button></td>
                    <td>&nbsp;</td>
                    <td width="100" align="right"><button class="btn btn-info" onClick="verArchivosAdjuntosRequerimiento(' . $id . ');"><i class="fas fa-folder"></i> Archivos Adjuntos</button></td>';
        }
        $html .=
            '</tr>
            </thead>
        </table>
        <br>
        <table class="table table-bordered table-striped table-view-okc" width="100%">';
        if ($type == 1) {
            $html .=
                '<thead style="background-color:#5c5c5c; color:#fff;">
                    <th></th>
                    <th>Descripción del Bien o Servicio</th>
                    <th width="150">Partida</th>
                    <th width="90">Unidad</th>
                    <th width="100">Cantidad</th>
                    <th width="100">Precio Unit.</th>
                    <th width="110">Subtotal</th>
                </thead>
                <tbody>';
        } elseif ($type == 2) {
            $html .=
                '<thead style="background-color:#5c5c5c; color:#fff;">
                    <th width="30">Item</th>
                    <th>Descripción del Bien o Servicio</th>
                    <th width="150">Partida</th>
                    <th width="90">Unidad</th>
                    <th width="100">Cantidad</th>
                    <th width="100">Precio Unit.</th>
                    <th width="110">Subtotal</th>
                </thead>
                <tbody>';
        }

        $cont = 1;
        $total = 0;

        $detail = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*', 'alm_und_medida.descripcion as unidad_medida_descripcion')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->where('id_requerimiento', $id)
            ->get();

        foreach ($detail as $det) {
            $id_det = $det->id_detalle_requerimiento;
            $id_item = $det->id_item;
            $precio = $det->precio_referencial;
            $cant = $det->cantidad;
            $obs = $det->obs;
            $id_part = $det->partida;
            $unit = $det->unidad_medida_descripcion;
            $active = '';

            if (is_numeric($id_part)) {
                $name_part = DB::table('finanzas.presup_par')->select('codigo')->where('id_partida', $id_part)->first();
                $partida = $name_part->codigo;
            } else {
                $partida = ''/*$id_part*/;
            }

            $subtotal = $precio * $cant;
            $total += $subtotal;

            if ($id_item != null) {
                $prod = DB::table('almacen.alm_item')
                    ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                    ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
                    ->select('alm_prod.descripcion AS producto', 'log_servi.descripcion AS servicio', 'alm_item.id_producto', 'alm_item.id_servicio', 'alm_item.id_equipo')
                    ->where('alm_item.id_item', $id_item)->first();
                $name = ($prod->id_producto != null) ? $prod->producto : $prod->servicio;
                $unidad = ($prod->id_servicio > 0) ? 'Servicio' : (($prod->id_equipo > 0) ? 'Equipo' : 'S/N');
            } else {
                $name = $det->descripcion_adicional;
            }

            if ($obs == 't' or $obs == '1' or $obs == 'true') {
                $active = 'checked="checked" disabled';
            }

            if ($type == 1) {
                $html .=
                    '<tr>
                    <td><input type="checkbox" name="check_okc" id="check_okc-' . $id_det . '" data-primary="' . $id . '" data-secundary="' . $id_det . '" onClick="check(this);" ' . $active . '></td>
                    <td>' . $name . '</td>
                    <td>' . $partida . '</td>
                    <td>' . $unit . '</td>
                    <td class="text-right">' . number_format($cant, 3) . '</td>
                    <td class="text-right">' . number_format($precio, 2) . '</td>
                    <td class="text-right">' . number_format($subtotal, 2) . '</td>
                </tr>';
            } elseif ($type == 2) {
                $html .=
                    '<tr>
                    <td>' . $cont . '</td>
                    <td>' . $name . '</td>
                    <td>' . $partida . '</td>
                    <td>' . ($unit ? $unit : $unidad) . '</td>
                    <td class="text-right">' . number_format($cant, 3) . '</td>
                    <td class="text-right">' . number_format($precio, 2) . '</td>
                    <td class="text-right">' . number_format($subtotal, 2) . '</td>
                </tr>';
            }

            $cont++;
        }

        $html .=
            '<tr>
            <th colspan="6" class="text-right">Total:</th>
            <td class="text-right">' . number_format($total, 2) . '</td>
        </tr>
        </tbody></table>';

        // if ($type == 1){
        //     return response()->json($html);
        // }elseif ($type == 2){
        //     return $html;
        // }
        return $html;
    }

    function consult_nivel_aprob($rol)
    {
        $sql = DB::table('administracion.adm_flujo')
            ->where([['id_operacion', '=', 1], ['estado', '=', 1], ['id_rol', '=', $rol]])->get();

        if ($sql->count() > 0) {
            $flujo = $sql->first()->id_flujo;
            $orden = $sql->first()->orden;
            $id_rol = $sql->first()->id_rol;
        } else {
            $flujo = 0;
            $orden = 0;
            $id_rol = '';
        }

        $array = array('orden' => $orden, 'flujo' => $flujo, 'rol_aprob' => $id_rol);
        return $array;
    }

    function consult_doc_aprob($req)
    {
        $sql = DB::table('administracion.adm_documentos_aprob')->where([['id_tp_documento', '=', 1], ['id_doc', '=', $req]])->get();

        if ($sql->count() > 0) {
            $val = $sql->first()->id_doc_aprob;
        } else {
            $val = 0;
        }

        return $val;
    }

    function last_obs_log($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')
        ->select('adm_aprobacion.*')
        ->where([['id_vobo', '=', 3],['id_rol', '=', 5], ['id_doc_aprob', '=', $doc]])
        ->orderby('fecha_vobo', 'desc')
        ->get();

        if ($sql->count() > 0) {
            $id_flujo = $sql->first()->id_flujo;
            $id_vobo = $sql->first()->id_vobo;
            $id_rol = $sql->first()->id_rol;
        } else {
            $id_flujo = 0;
            $id_vobo = 0;
            $id_rol = '';
        }
         $array = array('id_flujo' => $id_flujo, 'id_vobo' => $id_vobo, 'id_rol' => $id_rol);
        return $array;
    }
    function last_aprob($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')
        ->select('adm_aprobacion.*')
        ->where([['id_vobo', '=', 1], ['id_doc_aprob', '=', $doc]])
        ->orderby('fecha_vobo', 'desc')
        ->get();

        if ($sql->count() > 0) {
            $id_flujo = $sql->first()->id_flujo;
            $id_vobo = $sql->first()->id_vobo;
            $id_rol = $sql->first()->id_rol;
        } else {
            $id_flujo = 0;
            $id_vobo = 0;
            $id_rol = '';
        }
         $array = array('id_flujo' => $id_flujo, 'id_vobo' => $id_vobo, 'id_rol' => $id_rol);
        return $array;
    }

    function consult_tamaño_flujo($id_req)
    {

        $req = DB::table('almacen.alm_req')->where([['id_requerimiento', '=', $id_req], ['estado', '=', 1]])->first();
        $id_prioridad = $req->id_prioridad;
        $id_grupo = $req->id_grupo;
        $operacion = DB::table('administracion.adm_operacion')->where([['id_grupo', '=', $id_grupo],['id_prioridad', '=', $id_prioridad], ['estado', '=', 1]])->first();
        $flujo = DB::table('administracion.adm_flujo')->where([['id_operacion', '=', $operacion->id_operacion], ['estado', '=', 1]])->get();
        return $flujo->count();
 
    }
    function consult_aprob($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')->where([['id_vobo', '=', 1], ['id_doc_aprob', '=', $doc]])->get();
        return $sql->count();
    }
    function consult_obs($doc)
    {
        $sql = DB::table('administracion.adm_aprobacion')->where([['id_vobo', '=', 3], ['id_doc_aprob', '=', $doc]])->get();
        return $sql->count();
    }

    function consult_estado($req)
    {
        $sql = DB::table('almacen.alm_req')->select('id_estado_doc')->where('id_requerimiento', $req)->first();
        return $sql->id_estado_doc;
    }

    function consult_usuario_elab($req)
    {
        $sql = DB::table('almacen.alm_req')->select('id_usuario')->where('id_requerimiento', $req)->first();
        return $sql->id_usuario;
    }
    function consulta_primera_aprob($req)
    {
        $sql1 = DB::table('almacen.alm_req')->select('id_grupo')->where('id_requerimiento', $req)->get();
        $sql11 = DB::table('administracion.adm_operacion')->where([['id_grupo', $sql1->first()->id_grupo],['id_tp_documento', 1],['estado', 1]]);
        $sql2 = DB::table('administracion.adm_flujo')->where([['id_operacion', $sql11->first()->id_operacion],['estado', 1]])
        ->orderby('orden', 'asc')
        ->get();

        $nombre = ($sql2->count() > 0) ? $sql2->first()->nombre: '';
        $id_rol = ($sql2->count() > 0) ? $sql2->first()->id_rol: '';

        $array = array('nombre' => $nombre, 'id_rol' => $id_rol);

        return $array;

    }

    function consult_rol_aprob($rol)
    {
        $sql = DB::table('rrhh.rrhh_rol')
            ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->select('rrhh_rol_concepto.descripcion')->where('rrhh_rol.id_rol', '=', $rol)->first();
        return $sql->descripcion;
    }

    function get_nro_orden_by_flujo($id_flujo){
        $sql = DB::table('administracion.adm_flujo')->select('orden')->where([['id_operacion', '=', 1], ['id_flujo', '=', $id_flujo], ['estado', '=', 1]])->first();
        $orden = $sql->orden;

        return $orden;

    }

    function next_aprob($orden)
    {
        $sql = DB::table('administracion.adm_flujo')->select('adm_flujo.*')->where([['adm_flujo.id_operacion', '=', 1], ['adm_flujo.orden', '=', $orden], ['adm_flujo.estado', '=', 1]])->get();
        if ($sql->count() > 0) {
            $flujo = $sql->first()->id_flujo;
            $orden = $sql->first()->orden;
            $id_rol = $sql->first()->id_rol;
        } else {
            $flujo = 0;
            $orden = 0;
            $id_rol = '';
        }
        $array = array('orden' => $orden, 'flujo' => $flujo, 'rol_aprob' => $id_rol);
        return $array;
    }


    function get_header_observacion($id_req){
 
$sql_obs_req = DB::select("SELECT alm_req_obs.id_usuario, CONCAT(rrhh_perso.nombres,' ' ,rrhh_perso.apellido_paterno,' ' ,rrhh_perso.apellido_materno) as nombre_completo, descripcion FROM almacen.alm_req_obs
LEFT JOIN configuracion.sis_usua on sis_usua.id_usuario = alm_req_obs.id_usuario 
LEFT JOIN rrhh.rrhh_trab on rrhh_trab.id_trabajador = sis_usua.id_trabajador 
LEFT JOIN rrhh.rrhh_postu on rrhh_postu.id_postulante = rrhh_trab.id_postulante 
LEFT JOIN rrhh.rrhh_perso on rrhh_perso.id_persona = rrhh_postu.id_persona
where id_observacion in(SELECT MAX(id_observacion) as obs from almacen.alm_req_obs
WHERE accion = 'OBSERVADO' AND id_detalle_requerimiento is null and id_requerimiento=".$id_req."
GROUP BY id_requerimiento, id_usuario ORDER BY obs ASC)");




$id_usu_list=[];
$obs=[];
if(isset($sql_obs_req) && count($sql_obs_req)>0){
    foreach ($sql_obs_req as $key => $value) {
    $id_usu_list[]=$value->id_usuario;
    
    $obs[]=[
        'id_usuario'=> $value->id_usuario, 
        'nombre_completo'=> $value->nombre_completo, 
        'descripcion'=>$value->descripcion,
        'obs_item'=>[]
    ];
    }

 
}

$sql_obs_req_det = DB::select("SELECT alm_req_obs.id_usuario, alm_req_obs.id_detalle_requerimiento, CONCAT(rrhh_perso.nombres,' ' ,rrhh_perso.apellido_paterno,' ' ,rrhh_perso.apellido_materno) as nombre_completo, descripcion FROM almacen.alm_req_obs
LEFT JOIN configuracion.sis_usua on sis_usua.id_usuario = alm_req_obs.id_usuario 
LEFT JOIN rrhh.rrhh_trab on rrhh_trab.id_trabajador = sis_usua.id_trabajador 
LEFT JOIN rrhh.rrhh_postu on rrhh_postu.id_postulante = rrhh_trab.id_postulante 
LEFT JOIN rrhh.rrhh_perso on rrhh_perso.id_persona = rrhh_postu.id_persona
where id_observacion in(SELECT MAX(id_observacion) as obs
FROM almacen.alm_req_obs
WHERE id_requerimiento = 1 AND id_detalle_requerimiento > 0 AND id_usuario IN (".implode(",", $id_usu_list).") AND accion = 'OBSERVADO'
GROUP BY id_requerimiento, id_detalle_requerimiento ORDER BY obs ASC)");

if(isset($sql_obs_req_det) && count($sql_obs_req_det)>0){

    foreach ($sql_obs_req as $value1) {
    foreach ($sql_obs_req_det as $value2) {
        if($value1->id_usuario == $value2->id_usuario){
            $value1->obs_item[] = [
                                'id_detalle_requerimiento'=>$value2->id_detalle_requerimiento,
                                'descripcion'=>$value2->descripcion
                                ];
        }
    }

}
}

// DB::table('almacen.alm_req_obs')
// ->select(['id_usuario as id_usuario','descripcion as descripcion'])
// ->whereIn('id_observacion', function($query) 
//     {
//     $query->select(DB::raw('almacen.alm_req_obs.id_observacion'))
//     ->from('almacen.alm_req_obs')
//     ->where('accion', '=', 'OBSERVADO')
//     ->whereNull('id_detalle_requerimiento')
//     ->where('id_requerimiento', '=', 1)
//     ->groupBy('id_requerimiento', 'id_usuario' )
//     // ->orderBy('almacen.alm_req_obs.id_observacion', 'ASC')
//     ->max('alm_req_obs.id_observacion');
// })
// ->get();
 

return $sql_obs_req;

    }

    function get_observacion($id_req,$idFlujo,$idRol,$idVobo){
        $req_obs = DB::table('almacen.alm_req_obs')
        ->select('alm_req_obs.descripcion')
        ->where([ ['alm_req_obs.id_requerimiento', '=', $id_req], ['alm_req_obs.id_usuario', '=', $idRol], 
        ['alm_req_obs.accion', '=', 'OBSERVADO']])
        ->orderby('fecha_registro', 'desc')
        ->first();

        // $det_obs = DB::table('almacen.alm_req_obs')
        // ->select('alm_req_obs.descripcion')
        // ->where([ ['alm_req_obs.id_requerimiento', '=', $idDoc], ['adm_aprobacion.id_usuario', '=', $idRol], ['adm_aprobacion.id_vobo', '=', $req_obs->id_usuario]])
        // ->orderby('fecha_vobo', 'desc')
        // ->get();
 
        return $req_obs;
    }

    

    function consult_sgt_aprob($orden)
    {
        $sql = DB::table('administracion.adm_flujo')->select('id_rol')->where([['id_operacion', '=', 1], ['orden', '=', $orden], ['estado', '=', 1]])->first();
        $rol = $sql->id_rol;

        $trab = DB::table('rrhh.rrhh_trab')
            ->select('rrhh_perso.nombres', 'rrhh_perso.apellido_paterno', 'rrhh_perso.apellido_materno', 'rrhh_rol_concepto.descripcion AS rol')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('rrhh.rrhh_rol', 'rrhh_rol.id_trabajador', '=', 'rrhh_trab.id_trabajador')
            ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->where('rrhh_rol.id_rol', $rol)->first();
        $nombre = $trab->nombres . ' ' . $trab->apellido_paterno . ' - ' . $trab->rol;
        return $nombre;
    }

    function consult_moneda($id)
    {
        $sql = DB::table('configuracion.sis_moneda')->select('descripcion')->where('id_moneda', '=', $id)->first();
        return $sql->descripcion;
    }

    function totalAprobOp($operacion)
    {
        $sql = DB::table('administracion.adm_flujo')->where([['id_operacion', '=', 1], ['estado', '=', 1]])->get();
        return $sql->count();
    }

    function aprobar_requerimiento(Request $request)
    {
        $usuario = Auth::user();
        $id_req = $request->id_documento;
        $doc_ap = $request->doc_aprobacion;
        $flujos = $request->flujo;
        $idvobo = 1;
        $motivo = $request->motivo;
        $id_usu = $usuario->id_usuario;
        $id_rol = $usuario->login_rol;

        $rolesUsuario = $usuario->trabajador->roles;
        $idarea = 0;

        foreach ($rolesUsuario as $role) {
            $idarea = $role->pivot->id_area;
        }

        $hoy = date('Y-m-d H:i:s');
        $insertar = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'              => $flujos,
                'id_doc_aprob'          => $doc_ap,
                'id_vobo'               => $idvobo,
                'id_usuario'            => $id_usu,
                'id_area'               => $idarea,
                'fecha_vobo'            => $hoy,
                'detalle_observacion'   => $motivo,
                'id_rol'                => $id_rol
            ],
            'id_aprobacion'
        );
        if ($insertar > 0) {
            $totalFlujo = $this->totalAprobOp(1);
            $totalAprob = $this->consult_aprob($doc_ap);

            if ($totalFlujo > $totalAprob) {
                $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 12]);
                if ($data) {
                    $rpta = 'ok';
                } else {
                    $rpta = 'no_actualiza';
                }
            } else {
                $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 2]);
                if ($data) {
                    $rpta = 'ok';
                } else {
                    $rpta = 'no_actualiza';
                }
            }
        } else {
            $rpta = 'no_guarda';
        }

        return response()->json($rpta);
    }

    function observar_requerimiento_vista($req, $doc)
    {
        $html = $this->mostrar_requerimiento_id($req, 1);
        $array = array('view' => $html, 'id_req' => $req);
        return response()->json($array);
    }

    function observar_requerimiento_item(Request $request)
    {
        $id_req = $request->id_requerimiento;
        $id_det = $request->id_detalle_requerimiento;
        $motivo = $request->motivo_obs;
        $action = 'OBSERVADO';
        $usuario = Auth::user();
        $id_usu = $usuario->id_usuario;
        $hoy = date('Y-m-d H:i:s');

        $insertar = DB::table('almacen.alm_req_obs')->insertGetId(
            [
                'id_requerimiento'          => $id_req,
                'id_detalle_requerimiento'  => $id_det,
                'accion'                    => $action,
                'descripcion'               => $motivo,
                'id_usuario'                => $id_usu,
                'fecha_registro'            => $hoy
            ],
            'id_observacion'
        );

        if ($insertar > 0) {
            $data = DB::table('almacen.alm_det_req')->where('id_detalle_requerimiento', $id_det)->update(['obs' => true]);
            if ($data) {
                $rpta = 'ok';
            } else {
                $rpta = 'no_actualiza';
            }
        } else {
            $rpta = 'no_guarda';
        }
        return response()->json($rpta);
    }

    function observar_requerimiento(Request $request)
    {
        $id_req = $request->id_requerimiento;
        $doc_ap = $request->doc_req;
        $flujos = $request->flujo_req;
        $idvobo = 3;
        $motivo = $request->motivo_req;
        $usuario = Auth::user();
        $id_usu = $usuario->id_usuario;
        $id_rol = $usuario->login_rol;
        $hoy = date('Y-m-d H:i:s');

        $rolesUsuario = $usuario->trabajador->roles;
        $idarea = 0;

        $cnc_rol = $this->consult_rol_aprob($id_rol);
        if($cnc_rol =='JEFE DE LOGISTICA' || 'INSPECTOR DE FASES DE APROBACION'){ // es una observación de logistica
            $insertar = DB::table('almacen.alm_req_obs')->insertGetId(
                [
                    'id_requerimiento'        => $id_req,
                    'id_detalle_requerimiento'=> null,
                    'accion'                  => 'OBSERVADO',
                    'descripcion'             => $motivo,
                    'id_usuario'              => $id_usu,
                    'fecha_registro'          => $hoy
                ],
                'id_observacion'
            );

            $req_obs=DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['observacion' => 'OBSERVADO LOGISTICA']);


        }else{ //observacion de un usuario que esta dentro flujo
            foreach ($rolesUsuario as $role) {
                $idarea = $role->pivot->id_area;
            }
    
            $insertar = DB::table('administracion.adm_aprobacion')->insertGetId(
                [
                    'id_flujo'              => $flujos,
                    'id_doc_aprob'          => $doc_ap,
                    'id_vobo'               => $idvobo,
                    'id_usuario'            => $id_usu,
                    'id_area'               => $idarea,
                    'fecha_vobo'            => $hoy,
                    'detalle_observacion'   => $motivo,
                    'id_rol'                => $id_rol
                ],
                'id_aprobacion'
            );

        }
        if ($insertar > 0) {
            $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 3]);
            if ($data) {
                $rpta = 'ok';
            } else {
                $rpta = 'no_actualiza';
            }
        } else {
            $rpta = 'no_guarda';
        }
        
        return response()->json($rpta);
    }

    function denegar_requerimiento(Request $request)
    {
        $usuario = Auth::user();
        $id_req = $request->id_documento;
        $doc_ap = $request->doc_aprobacion;
        $flujos = $request->flujo;
        $idvobo = 2;
        $motivo = $request->motivo;
        $id_usu = $usuario->id_usuario;
        $id_rol = $usuario->login_rol;

        $rolesUsuario = $usuario->trabajador->roles;
        $idarea = 0;

        foreach ($rolesUsuario as $role) {
            $idarea = $role->pivot->id_area;
        }

        $hoy = date('Y-m-d H:i:s');

        $insertar = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo' => $flujos,
                'id_doc_aprob' => $doc_ap,
                'id_vobo' => $idvobo,
                'id_usuario' => $id_usu,
                'id_area' => $idarea,
                'fecha_vobo' => $hoy,
                'detalle_observacion' => $motivo,
                'id_rol' => $id_rol
            ],
            'id_aprobacion'
        );

        if ($insertar > 0) {
            $data = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 4]);
            if ($data) {
                $rpta = 'ok';
            } else {
                $rpta = 'no_actualiza';
            }
        } else {
            $rpta = 'no_guarda';
        }
        return response()->json($rpta);
    }

    function guardar_sustento(Request $request)
    {
        $id_req = $request->id_requerimiento_sustento;
        $id_det = $request->id_detalle_requerimiento_sustento;
        $motivo = $request->motivo_sustento;
        $action = 'SUSTENTO';
        $usuario = Auth::user();
        $id_usu = $usuario->id_usuario;
        $hoy = date('Y-m-d H:i:s');

        if ($id_det > 0) {
            $insertar = DB::table('almacen.alm_req_obs')->insertGetId(
                [
                    'id_requerimiento'          => $id_req,
                    'id_detalle_requerimiento'  => $id_det,
                    'accion'                    => $action,
                    'descripcion'               => $motivo,
                    'id_usuario'                => $id_usu,
                    'fecha_registro'            => $hoy
                ],
                'id_observacion'
            );
            $update_req = DB::table('almacen.alm_req')->where('id_requerimiento', $id_req)->update(['id_estado_doc' => 13]); // estado Sustentado

            if ($insertar > 0) {
                $update_det_req= DB::table('almacen.alm_det_req')->where('id_detalle_requerimiento', $id_det)->update(['obs' => false]);
                if ($update_det_req) {
                    $rpta = 'ok_det';
                } else {
                    $rpta = 'no_actualiza';
                }
            } else {
                $rpta = 'no_guarda';
            }
        } else {
            $insertar = DB::table('almacen.alm_req_obs')->insertGetId(
                [
                    'id_requerimiento'          => $id_req,
                    'id_detalle_requerimiento'  => null,
                    'accion'                    => $action,
                    'descripcion'               => $motivo,
                    'id_usuario'                => $id_usu,
                    'fecha_registro'            => $hoy
                ],
                'id_observacion'
            );

            if ($insertar > 0) {
                $rpta = 'ok_req';
            } else {
                $rpta = 'no_guarda';
            }
        }
        return response()->json($rpta);
    }

    function flujo_aprobacion($req, $doc)
    {
        $sql1 = DB::table('administracion.adm_aprobacion')
            ->join('administracion.adm_vobo', 'adm_vobo.id_vobo', '=', 'adm_aprobacion.id_vobo')
            ->select('adm_aprobacion.*', 'adm_vobo.descripcion AS vobo')
            ->where('adm_aprobacion.id_doc_aprob', '=', $doc)->get();
        $sql2 = DB::table('almacen.alm_req_obs')->where([['id_requerimiento', '=', $req], ['accion', '=', 'SUSTENTO']])->get();
        $sql3 = DB::table('almacen.alm_req')->where('id_requerimiento', '=', $req)->get();
        
        $sql4 = DB::table('almacen.alm_req_obs')
        ->select('alm_req_obs.*')
        ->where([['alm_req_obs.id_requerimiento', '=', $req],['accion', '=', 'OBSERVADO']])->get();

        $html = '';
        $footer = '';
        $cont = 1;
        $verify1 = $sql1->count();
        $verify2 = $sql2->count();
        $verify3 = $sql3->count();
        $verify4 = $sql4->count();
        $dataFinal = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data4 = array();
        $usuarios = array();

        if ($verify1 > 0) {
            foreach ($sql1 as $key) {
                $id_usua = $key->id_usuario;
                $my_vobo = $key->vobo;
                $fechavb = $key->fecha_vobo;
                $det_obs = $key->detalle_observacion;
                $data1[] = array('estado' => $my_vobo, 'usuario' => $id_usua, 'fecha' => $fechavb, 'obs' => $det_obs);
            }
        }

        if ($verify2 > 0) {
            foreach ($sql2 as $row) {
                $id_usu = $row->id_usuario;
                $accion = $row->accion;
                $fechao = $row->fecha_registro;
                $descri = $row->descripcion;
                $data2[] = array('estado' => $accion, 'usuario' => $id_usu, 'fecha' => $fechao, 'obs' => $descri);
            }
        }

        if ($verify3 > 0) {
            foreach ($sql3 as $row) {
                $id_us = $row->id_usuario;
                $fechae = $row->fecha_registro;
                $data3[] = array('estado' => 'ELABORADO', 'usuario' => $id_us, 'fecha' => $fechae, 'obs' => '');
            }
        }
        
        if ($verify4 > 0) {
            foreach ($sql4 as $row) {
                $id_us = $row->id_usuario;
                $fechae = $row->fecha_registro;
                $descripcion = $row->descripcion;
                $id_requerimiento = $row->id_requerimiento;
                $id_detalle_requerimiento = $row->id_detalle_requerimiento;
                // $id_grupo_obs=$row->id_grupo_obs;
                // array_push($grupo_obs,$id_grupo_obs);

                array_push($usuarios,$id_us);
                if($id_requerimiento=! null && $id_detalle_requerimiento == null && $descripcion){ //obs de requerimiento
                $data4[] = array('estado' => 'OBSERVADO', 'usuario' => $id_us, 'fecha' => $fechae, 'obs' => $descripcion, 'id_requerimiento'=> $id_requerimiento, 'id_detalle_requerimiento'=> $id_detalle_requerimiento);
                }
                // $data4[] = array('estado' => 'OBSERVADO', 'usuario' => $id_us, 'fecha' => $fechae, 'obs' => $descripcion, 'id_requerimiento'=> $id_requerimiento, 'id_detalle_requerimiento'=> $id_detalle_requerimiento, 'grupo_obs'=>$id_grupo_obs);
            }
            // $usuarios_unicos=array_values(array_unique($usuarios));
            // $grupo_obs_unicos=array_values(array_unique($grupo_obs));

            // foreach($grupo_obs_unicos as $dataGrupo){
            /*foreach($usuarios_unicos as $dataUser){
                $usuario='';
                $descri_req='';
                $descri_item='';
                foreach ($data4 as $data){
                    // if($dataGrupo == $data['grupo_obs']){
                    if($dataUser == $data['usuario']){
                        if($data['id_requerimiento'] =! null && $data['id_detalle_requerimiento'] == null && $data['obs'] != null){ //obs de requerimiento
                            $descri_req = '[Req] '.$data['obs'].'</br>';
                            $usuario = $data['usuario'];
                            $fecha = $data['fecha'];
                        }elseif($data['id_requerimiento'] =! null && $data['id_detalle_requerimiento'] =! null && $data['obs'] != null){ // obs de item
                            $descri_item = '[Item.] '.$data['obs'].'</br>';
                            $usuario = $data['usuario'];
                            $fecha = $data['fecha'];
                        }
                    }
                }
                        $data44[] = array(
                            'estado' => 'OBSERVADO', 
                            'usuario' => $usuario, 
                            'fecha' => $fecha, 
                            'obs' => $descri_req.$descri_item
                        );

            }*/
            // return $data44;

            
        }

        $dataFinal = array_merge($data4,$data3, $data1, $data2);
        $date = array();
        foreach ($dataFinal as $row) {
            $date[] = $row['fecha'];
        }

        if ($data1 > 0 or $data2 > 0 or $data3 > 0 or $data4 > 0) {
            $alert = '<ul style="list-style: none; padding: 0;">';
            array_multisort($date, SORT_ASC, $dataFinal);
            foreach ($dataFinal as $value => $val) {
                $usu = $val['usuario'];
                $est = $val['estado'];
                $day = $val['fecha'];
                $obs = $val['obs'];

                $usuario = Usuario::find($usu)->trabajador->postulante->persona->nombre_completo;

                if (strtoupper($est) == 'ELABORADO') {
                    $claseObs = 'alert-okc alert-okc-primary';
                } elseif (strtoupper($est) == 'OBSERVADO') {
                    $claseObs = 'alert-okc alert-okc-warning';
                } elseif (strtoupper($est) == 'DENEGADO') {
                    $claseObs = 'alert-okc alert-okc-danger';
                } elseif (strtoupper($est) == 'SUSTENTO') {
                    $claseObs = 'alert-okc alert-okc-info';
                } elseif (strtoupper($est) == 'APROBADO') {
                    $claseObs = 'alert-okc alert-okc-success';
                }

                $alert .=
                    '<li class="' . $claseObs . '" style="padding: 5px; margin-bottom: 8px;">
                    <strong>' . $est . ' - ' . $usuario . '</strong>
                    <small>(' . date('d/m/Y H:i:s', strtotime($day)) . ')</small>
                    <br>' . $obs . '
                </li>';
                $cont++;
            }
            $alert .= '</ul>';
        }

        $estado_req = $this->consult_estado($req); // get id_estado_doc
        $totalFlujo = $this->totalAprobOp(1);
        $totalAprob = $this->consult_aprob($doc); // cantidad aprobaciones


        if ($estado_req == 12) {
            if ($totalAprob > 0) {
                if ($totalFlujo > $totalAprob) {
                    $sgt_aprob = ($totalAprob + 1);
                    $sgt_per = $this->consult_sgt_aprob($sgt_aprob);
                }

                $footer .= '<strong>Próximo en aprobar: </strong>' . $sgt_per;
            }
        } elseif ($estado_req == 3) {
            $usuario_crea = $this->consult_usuario_elab($req);
            $usu_elab = Usuario::find($usuario_crea)->trabajador->postulante->persona->nombre_completo;
            $footer .= '<strong>Por sustentar </strong>' . $usu_elab;
        } elseif ($estado_req == 13) {
            $footer .= '<strong>Por Aceptar Sustento </strong> Logistica' ;
        } elseif($estado_req ==1) {
            $usuPrimeraApro = $this->consulta_primera_aprob($req)['nombre'];
            $footer = '<strong>Pendiente </strong>'. $usuPrimeraApro;
        } else {
            $footer = '';
        }

        $reqs = $this->mostrar_requerimiento_id($req, 2);

        $data = ['flujo' => $alert, 'siguiente' => $footer, 'requerimiento' => $reqs, 'cont' => $cont];
        return response()->json($data);
    }
    /* Rocio */
    public function listar_grupo_cotizaciones()
    {
        $cotizaciones = DB::table('logistica.log_cotizacion')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.adm_contri as contri', 'contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as identi', 'identi.id_doc_identidad', '=', 'contri.id_doc_identidad')
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'log_valorizacion_cotizacion.id_requerimiento')
            ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            ->select(
                'log_grupo_cotizacion.id_grupo_cotizacion',
                'log_grupo_cotizacion.codigo_grupo',
                'log_cotizacion.id_cotizacion',
                'log_cotizacion.codigo_cotizacion',
                'log_cotizacion.id_proveedor',
                'log_cotizacion.estado_envio',
                'log_cotizacion.estado',
                'log_cotizacion.id_empresa',

                'adm_contri.id_contribuyente',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion as nombre_doc_identidad',

                'contri.razon_social as razon_social_empresa',
                'contri.nro_documento as nro_documento_empresa',
                'contri.id_doc_identidad as id_doc_identidad_empresa',
                'identi.descripcion as nombre_doc_idendidad_empresa',
                DB::raw("(SELECT  COUNT(log_valorizacion_cotizacion.id_cotizacion) FROM logistica.log_valorizacion_cotizacion
            WHERE log_valorizacion_cotizacion.id_cotizacion = log_cotizacion.id_cotizacion)::integer as cantidad_items"),
                'alm_req.id_requerimiento',
                'alm_req.codigo AS codigo_requerimiento'

            )
            ->where([
                ['log_cotizacion.estado', '=', 1],
                ['log_valorizacion_cotizacion.estado', '<', 5], // No esten Atentidos (que tengas orden)
            ])
            // ->whereIn('alm_req.id_requerimiento',$auxIdReq)
            ->get();

        $cotizacionAux = [];
        $cotizacionList = [];
        $requerimiento__cotizacion = [];

        foreach ($cotizaciones as $data) {
            $requerimiento__cotizacion[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_requerimiento' => $data->id_requerimiento,
                'codigo_requerimiento' => $data->codigo_requerimiento
            ];
            if (in_array($data->id_cotizacion, $cotizacionAux) === false) {
                $cotizacionAux[] = $data->id_cotizacion;
                $cotizacionList[] = [
                    'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                    'codigo_grupo' => $data->codigo_grupo,
                    'id_cotizacion' => $data->id_cotizacion,
                    'codigo_cotizacion' => $data->codigo_cotizacion,
                    'id_proveedor' => $data->id_proveedor,
                    'id_contribuyente' => $data->id_contribuyente,
                    'estado_envio' => $data->estado_envio,
                    'estado' => $data->estado,
                    'id_empresa' => $data->id_empresa,
                    'razon_social' => $data->razon_social,
                    'nro_documento' => $data->nro_documento,
                    'id_doc_identidad' => $data->id_doc_identidad,
                    'nombre_doc_identidad' => $data->nombre_doc_identidad,
                    'razon_social_empresa' => $data->razon_social_empresa,
                    'nro_documento_empresa' => $data->nro_documento_empresa,
                    'id_doc_identidad_empresa' => $data->id_doc_identidad_empresa,
                    'nombre_doc_idendidad_empresa' => $data->nombre_doc_idendidad_empresa,
                    'cantidad_items' => $data->cantidad_items
                ];
            }
        }

        $aux = [];
        for ($k = 0; $k < sizeof($requerimiento__cotizacion); $k++) {
            if (in_array($requerimiento__cotizacion[$k]['id_cotizacion'] . $requerimiento__cotizacion[$k]['id_requerimiento'], $aux) === false) {
                $aux[] = $requerimiento__cotizacion[$k]['id_cotizacion'] . $requerimiento__cotizacion[$k]['id_requerimiento'];
                $requerimientos_cotiza[] = $requerimiento__cotizacion[$k];
            }
        }

        $aux = [];
        $req = '';
        for ($i = 0; $i < sizeof($cotizacionList); $i++) {
            for ($k = 0; $k < sizeof($requerimientos_cotiza); $k++) {
                if ($cotizacionList[$i]['id_cotizacion'] == $requerimientos_cotiza[$k]['id_cotizacion']) {
                    $cotizacionList[$i]['requerimiento'][] = $requerimientos_cotiza[$k];
                }
            }
        }
        $output['data'] = $cotizacionList;
        return response()->json($output);
    }

    public function listar_requerimientos_pendientes()
    {
        $alm_req = DB::table('almacen.alm_req')
            ->select('alm_req.*', 'adm_area.descripcion as des_area')
            ->leftJoin('administracion.adm_area', 'alm_req.id_area', '=', 'adm_area.id_area')
            ->where([['alm_req.id_estado_doc', '=', 2]]) //Estado Aprobado
            ->orderBy('alm_req.codigo', 'desc')
            ->get();
        $output['data'] = $alm_req;
        return response()->json($output);
    }

    public function detalle_requerimiento($id_req)
    {
        $det = DB::table('almacen.alm_det_req')
            ->select('alm_det_req.*', 'alm_req.codigo as cod_req', 'alm_und_medida.abreviatura')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->where('alm_det_req.id_requerimiento', $id_req)
            ->get();

        $html = '';
        $i = 1;

        foreach ($det as $d) {
            $item = DB::table('almacen.alm_item')
                ->select(
                    'alm_item.*',
                    'alm_prod.codigo as cod_producto',
                    'alm_prod.descripcion as des_producto',
                    'log_servi.codigo as cod_servicio',
                    'log_servi.descripcion as des_servicio',
                    'alm_und_medida.abreviatura'
                )
                ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
                ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
                ->where('id_item', $d->id_item)
                ->first();

            if (isset($item)) { // si existe variable


                if ($item->id_producto !== null || is_numeric($item->id_producto) == 1) {
                    $html .= '
                <tr>
                    <td>
                        <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                        <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>
                        <input type="checkbox"/>
                    </td>
                    <td>' . $d->cod_req . '</td>
                    <td></td>
                    <td>' . $item->cod_producto . '</td>
                    <td>' . $item->des_producto . '</td>
                    <td>' . $item->abreviatura . '</td>
                    <td>' . $d->cantidad . '</td>
                    <td>' . $d->precio_referencial . '</td>
                </tr>
                ';
                } else if ($item->id_servicio !== null || is_numeric($item->id_servicio) == 1) {
                    $html .= '
                <tr>
                    <td>
                        <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                        <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>
                        <input type="checkbox"/>
                    </td>
                    <td>' . $d->cod_req . '</td>
                    <td>' . $item->cod_servicio . '</td>
                    <td>' . $item->des_servicio . '</td>
                    <td>serv</td>
                    <td>' . $d->cantidad . '</td>
                    <td>' . $d->precio_referencial . '</td>
                </tr>
                ';
                }
            } else { // si no existe | no existe id_item

                $html .= '
                <tr>
                    <td>
                        <input class="oculto" value="' . $d->id_requerimiento . '" name="id_requerimiento"/>
                        <input class="oculto" value="' . $d->id_detalle_requerimiento . '" name="id_detalle"/>
                        <input type="checkbox"/>
                    </td>
                    <td>' . $d->cod_req . '</td>
                    <td></td>
                    <td>0</td>
                    <td>' . $d->descripcion_adicional . '</td>
                    <td>' . $d->abreviatura . '</td>
                    <td>' . $d->cantidad . '</td>
                    <td>' . $d->precio_referencial . '</td>
                </tr>
                ';
            }
        }
        return json_encode($html);
    }

    public function guardar_proveedor(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_contribuyente = DB::table('contabilidad.adm_contri')->insertGetId(
            [
                'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
                'id_doc_identidad' => $request->id_doc_identidad,
                'nro_documento' => $request->nro_documento,
                'razon_social' => $request->razon_social,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
            'id_contribuyente'
        );
        $id_proveedor = DB::table('logistica.log_prove')->insertGetId(
            [
                'id_contribuyente' => $id_contribuyente,
                'codigo' => '000',
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
            'id_proveedor'
        );
        $data = DB::table('logistica.log_prove')
            ->select('log_prove.id_proveedor', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([['adm_contri.estado', '=', 1], ['log_prove.estado', '=', 1]])->get();
        $html = '';

        foreach ($data as $d) {
            if ($id_proveedor == $d->id_proveedor) {
                $html .= '<option value="' . $d->id_proveedor . '" selected>' . $d->nro_documento . ' - ' . $d->razon_social . '</option>';
            } else {
                $html .= '<option value="' . $d->id_proveedor . '">' . $d->nro_documento . ' - ' . $d->razon_social . '</option>';
            }
        }
        return json_encode($html);
    }

    public function nextCodigoCotizacion()
    {
        $mes = date('m', strtotime("now"));
        $anio = date('y', strtotime("now"));
        $num = DB::table('logistica.log_cotizacion')->count();
        $correlativo = $this->leftZero(4, ($num + 1));
        $codigo = "CO-{$anio}{$mes}-{$correlativo}";
        return $codigo;
    }

    public function nextCodigoGrupo()
    {
        $mes = date('m', strtotime("now"));
        $anio = date('y', strtotime("now"));
        $num = DB::table('logistica.log_grupo_cotizacion')->count();
        $correlativo = $this->leftZero(4, ($num + 1));
        $codigoGrupo = "CC-{$anio}{$mes}-{$correlativo}";
        return $codigoGrupo;
    }

    public function guardar_cotizacion($items, $id_grupo)
    {
        $codigo = $this->nextCodigoCotizacion();
        $id_cotizacion = DB::table('logistica.log_cotizacion')->insertGetId(
            [
                'codigo_cotizacion' => $codigo,
                'estado_envio' => 0,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
            ],
            'id_cotizacion'
        );

        $items_array = explode(',', $items);
        $count = count($items_array);

        for ($i = 0; $i < $count; $i++) {
            $id = $items_array[$i];
            $detalle = DB::table('almacen.alm_det_req')
                ->where('id_detalle_requerimiento', $id)
                ->first();

            DB::table('logistica.log_valorizacion_cotizacion')->insert(
                [
                    'id_cotizacion' => $id_cotizacion,
                    'id_detalle_requerimiento' => $detalle->id_detalle_requerimiento,
                    'cantidad_cotizada' => $detalle->cantidad,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s'),
                    'id_requerimiento' => $detalle->id_requerimiento
                ]
            );
        }
        //agrega grupo_cotizacion
        if ($id_grupo !== '0') {
            DB::table('logistica.log_detalle_grupo_cotizacion')->insert(
                [
                    'id_grupo_cotizacion' => $id_grupo,
                    'id_cotizacion' => $id_cotizacion,
                    'estado' => 1
                ]
            );
        } else {
            $codigo_grupo = $this->nextCodigoGrupo();
            $id_usuario = Auth::user()->id_usuario;

            $id_grupo = DB::table('logistica.log_grupo_cotizacion')->insertGetId(
                [
                    'codigo_grupo' => $codigo_grupo,
                    'id_usuario' => $id_usuario,
                    'fecha_inicio' => date('Y-m-d'),
                    // 'fecha_fin'=> date('Y-m-d'), 
                    'estado' => 1
                ],
                'id_grupo_cotizacion'
            );

            DB::table('logistica.log_detalle_grupo_cotizacion')->insert(
                [
                    'id_grupo_cotizacion' => $id_grupo,
                    'id_cotizacion' => $id_cotizacion,
                    'estado' => 1
                ]
            );
        }
        return response()->json(['id_cotizacion' => $id_cotizacion, 'id_grupo' => $id_grupo]);
    }

    public function cotizaciones_por_grupo($id_grupo)
    {
        $detalle = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->where('id_grupo_cotizacion', $id_grupo)
            ->get();

        $html = '';
        $i = 1;

        foreach ($detalle as $det) {
            $cotizacion = DB::table('logistica.log_cotizacion')
                ->select(
                    'log_cotizacion.*',
                    'prov.nro_documento',
                    'prov.razon_social',
                    'empresa.razon_social as empresa',
                    'adm_estado_doc.estado_doc'
                )
                ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
                ->leftjoin('contabilidad.adm_contri as prov', 'prov.id_contribuyente', '=', 'log_prove.id_contribuyente')
                ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
                ->leftjoin('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_cotizacion.estado')
                ->where('id_cotizacion', $det->id_cotizacion)
                ->first();

            $nro_items = DB::table('logistica.log_valorizacion_cotizacion')
                ->where([
                    ['id_cotizacion', '=', $det->id_cotizacion],
                    ['estado', '!=', 7] //no mostrar anulados
                ])
                ->count();

            if ($cotizacion->estado !== 7) {
                $codigo = "'" . $cotizacion->codigo_cotizacion . "'";
                $html .= '
                <tr>
                    <td>' . $i . '</td>
                    <td>' . $cotizacion->codigo_cotizacion . '</td>
                    <td>' . $nro_items . ' items</td>
                    <td>' . $cotizacion->nro_documento . ' - ' . $cotizacion->razon_social . '</td>
                    <td><a href="mailto:' . $cotizacion->email_proveedor . '?cc=raulscodes@gmail.com&subject=Solicitud de Cotizacion&body=Señores ' . $cotizacion->razon_social . ', de nuestra consideración tengo el agrado de dirigirme a usted, para saludarle cordialmente en nombre del OK COMPUTER EIRL y le solicitamos cotizar los siguientes productos de acuerdo a los términos que se adjuntan. RICHARD BALTAZAR DORADO BACA - Jefe de Logística">' . $cotizacion->email_proveedor . '</a></td>
                    <td>' . $cotizacion->empresa . '</td>
                    <td>' . $cotizacion->estado_doc . '</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-warning btn-sm" title="Editar" onClick="open_cotizacion(' . $cotizacion->id_cotizacion . ',' . $codigo . ');">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-sm" title="Formato de Solicitud de Cotizacion" onClick="downloadSolicitudCotizacion(' . $cotizacion->id_cotizacion . ');">
                                <i class="fas fa-file-excel"></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" title="Archivos Adjuntos" onClick="ModalArchivosAdjuntosCotizacion(' . $cotizacion->id_cotizacion . ');">
                                <i class="fas fa-folder"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" title="Eliminar" onClick="anular_cotizacion(' . $cotizacion->id_cotizacion . ');">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>';
                $i++;
            }
        }
        return json_encode($html);
    }

    public function items_cotizaciones_por_grupo($id_grupo)
    {
        $detalle = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->where('id_grupo_cotizacion', $id_grupo)
            ->get();

        $html = '';
        $i = 1;
        foreach ($detalle as $det) {
            $cotizacion = DB::table('logistica.log_cotizacion')
                ->select(
                    'log_cotizacion.*',
                    'prov.nro_documento',
                    'prov.razon_social',
                    'empresa.razon_social as empresa',
                    'adm_estado_doc.estado_doc'
                )
                ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
                ->leftjoin('contabilidad.adm_contri as prov', 'prov.id_contribuyente', '=', 'log_prove.id_contribuyente')
                ->leftjoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
                ->leftjoin('contabilidad.adm_contri as empresa', 'empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
                ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_cotizacion.estado')
                ->where('id_cotizacion', $det->id_cotizacion)
                ->first();

            $items = DB::table('logistica.log_valorizacion_cotizacion')
                ->select(
                    'log_valorizacion_cotizacion.*',
                    DB::raw("(CASE 
                        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                        ELSE 'nulo' END) AS descripcion
                        "),
                    DB::raw("(CASE 
                        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                        ELSE 'nulo' END) AS codigo
                        "),
                    DB::raw("(CASE 
                        WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
                        WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                        WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                        ELSE 'nulo' END) AS unidad_medida
                        "),
                    'alm_item.id_producto',
                    'alm_item.id_servicio',
                    'alm_item.id_equipo',
                    'alm_req.id_requerimiento',
                    'alm_req.codigo as cod_req',
                    'alm_det_req.cantidad',
                    'alm_det_req.precio_referencial',
                    'alm_det_req.id_tipo_item',
                    'alm_det_req.descripcion_adicional',
                    'log_cotizacion.codigo_cotizacion'
                )
                ->join('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
                ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
                ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
                ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
                ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
                ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
                ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
                ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
                ->where([
                    ['log_valorizacion_cotizacion.id_cotizacion', '=', $det->id_cotizacion],
                    ['log_valorizacion_cotizacion.estado', '=', 1]
                ])
                ->get();

            foreach ($items as $item) {
                $descripcion = "'" . $item->descripcion . "'";
                $html .= '
                <tr>
                    <td>
                        <input class="oculto" value="' . $item->id_requerimiento . '" name="id_requerimiento"/>
                        <input class="oculto" value="' . $item->id_detalle_requerimiento . '" name="id_detalle"/>
                        ' . $i . '
                    </td>
                    <td>' . $item->cod_req . '</td>
                    <td>' . $item->codigo_cotizacion . '</td>
                    <td>' . ($item->codigo ? $item->codigo : "0") . '</td>
                    <td>' . ($item->descripcion ? $item->descripcion : $item->descripcion_adicional) . '</td>
                    <td>' . $item->unidad_medida . '</td>
                    <td>' . $item->cantidad . '</td>
                    <td>' . $item->precio_referencial . '</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" title="Ver Saldos" 
                        onClick="ver_saldos(' . $item->id_producto . ',' . $item->id_tipo_item . ');">
                        <i class="fas fa-search"></i>
                        </button>
                    </td>
                </tr>
                ';
                $i++;
            }
        }
        return json_encode($html);
    }


    public function mostrar_grupo_cotizacion($id_grupo)
    {
        $data = DB::table('logistica.log_grupo_cotizacion')
            ->where('id_grupo_cotizacion', $id_grupo)
            ->first();
        return response()->json($data);
    }

    public function mostrar_cotizacion($id_cotizacion)
    {
        $data = DB::table('logistica.log_cotizacion')
            ->select('log_cotizacion.*', 'adm_contri.razon_social')
            ->leftjoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftjoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where('id_cotizacion', $id_cotizacion)
            ->first();
        $contacto = $this->listar_contacto_proveedor($data->id_proveedor);
        return response()->json(['cotizacion' => $data, 'contacto' => $contacto]);
    }

    public function mostrar_proveedores()
    {
        $data = DB::table('logistica.log_prove')
            ->select('log_prove.id_proveedor', 'adm_contri.id_contribuyente', 'adm_contri.nro_documento', 'adm_contri.razon_social')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([['log_prove.estado', '=', 1]])
            ->orderBy('adm_contri.nro_documento')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function listar_contacto_proveedor($id_proveedor)
    {
        $data = DB::table('contabilidad.adm_ctb_contac')
            ->join('logistica.log_prove', 'log_prove.id_contribuyente', '=', 'adm_ctb_contac.id_contribuyente')
            ->where([
                ['log_prove.id_proveedor', '=', $id_proveedor],
                ['adm_ctb_contac.estado', '=', 1]
            ])
            ->orderBy('adm_ctb_contac.email', 'asc')
            ->get();
        return $data;
    }
    public function mostrar_email_proveedor($id_proveedor)
    {
        $data = $this->listar_contacto_proveedor($id_proveedor);
        return response()->json($data);
    }

    public function update_cotizacion(Request $request)
    {
        $data = DB::table('logistica.log_cotizacion')
            ->where('id_cotizacion', $request->id_cotizacion)
            ->update([
                'id_proveedor' => $request->id_proveedor,
                'id_empresa' => $request->id_empresa,
                'id_contacto' => $request->id_contacto,
                'email_proveedor' => $request->email_proveedor
            ]);
        return response()->json($data);
    }

    public function duplicate_cotizacion(Request $request)
    {
        $DataGrupo = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->select(
                'log_detalle_grupo_cotizacion.*'
            )
            ->where('id_cotizacion', $request->id_cotizacion)
            ->first();

        $DataCotizacion = DB::table('logistica.log_cotizacion')
            ->select(
                'log_cotizacion.*'
            )
            ->where('id_cotizacion', $request->id_cotizacion)
            ->get();

        $DataValorizacionCotizacion = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.*'
            )
            ->where('id_cotizacion', $request->id_cotizacion)
            ->get();

        if (count($DataCotizacion) > 0) {
            foreach ($DataCotizacion as $data) {
                $codigo = $this->nextCodigoCotizacion();
                $cotizacion = DB::table('logistica.log_cotizacion')->insertGetId(
                    [
                        'codigo_cotizacion' => $codigo,
                        'id_proveedor' => $request->id_proveedor,
                        'id_empresa' => $request->id_empresa,
                        'email_proveedor' => $request->email_proveedor,
                        'id_contacto' => $request->id_contacto,
                        'estado_envio' => 0,
                        'estado' => 1,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                    'id_cotizacion'
                );
                $detalle_grupo = DB::table('logistica.log_detalle_grupo_cotizacion')->insertGetId(
                    [
                        'id_grupo_cotizacion' => $DataGrupo->id_grupo_cotizacion,
                        'id_cotizacion' => $cotizacion,
                        'estado' => 1
                    ],
                    'id_detalle_grupo_cotizacion'
                );
            }


            foreach ($DataValorizacionCotizacion as $data) {
                $valorizacionn = DB::table('logistica.log_valorizacion_cotizacion')->insertGetId(
                    [
                        'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                        'id_cotizacion' => $cotizacion,
                        'id_requerimiento' => $data->id_requerimiento,
                        'estado' => 1,
                        'fecha_registro' => date('Y-m-d H:i:s')
                    ],
                    'id_valorizacion_cotizacion'
                );
            }
        }

        return response()->json($valorizacionn);
    }

    public function guardar_contacto(Request $request)
    {
        $id_datos_contacto = DB::table('contabilidad.adm_ctb_contac')->insertGetId(
            [
                'id_contribuyente' => $request->id_contribuyente,
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'cargo' => $request->cargo,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
            'id_datos_contacto'
        );

        $data = DB::table('contabilidad.adm_ctb_contac')
            ->where([
                ['id_contribuyente', '=', $request->id_contribuyente],
                ['estado', '=', 1]
            ])
            ->get();

        $html = '';
        foreach ($data as $d) {
            if ($id_datos_contacto == $d->id_datos_contacto) {
                $html .= '<option value="' . $d->id_datos_contacto . '" selected>' . $d->nombre . ' - ' . $d->cargo . ' - ' . $d->email . '</option>';
            } else {
                $html .= '<option value="' . $d->id_datos_contacto . '">' . $d->nombre . ' - ' . $d->cargo . ' - ' . $d->email . '</option>';
            }
        }
        return json_encode($html);
    }

    public function get_cotizacion($id_cotizacion)
    {
        $cotizacion = DB::table('logistica.log_cotizacion')
            ->select('log_cotizacion.*')
            ->where([
                ['log_cotizacion.estado', '>', 0],
                ['log_cotizacion.id_cotizacion', '=', $id_cotizacion]
            ])
            ->first();

        $proveedor = DB::table('logistica.log_prove')
            ->select(
                'log_prove.id_proveedor',
                'log_prove.codigo',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion AS nombre_doc_identidad',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'adm_contri.telefono',
                'adm_contri.celular',
                'adm_contri.direccion_fiscal',
                'sis_pais.descripcion AS nombre_pais'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('configuracion.sis_pais', 'sis_pais.id_pais', '=', 'adm_contri.id_pais')
            ->where(
                [
                    ['log_prove.estado', '>', 0],
                    ['log_prove.id_proveedor', '=', $cotizacion->id_proveedor]
                ]
            )
            ->get();

        $empresa = DB::table('administracion.adm_empresa')
            ->select(
                'adm_empresa.id_empresa',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion AS nombre_doc_identidad',
                'adm_contri.nro_documento',
                'adm_contri.razon_social',
                'adm_contri.telefono',
                'adm_contri.celular',
                'adm_contri.direccion_fiscal'
            )
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->where(
                [
                    ['adm_empresa.estado', '>', 0],
                    ['adm_empresa.id_empresa', '=', $cotizacion->id_empresa]
                ]
            )
            ->get();

        $detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->select(
                'log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion',
                'log_detalle_grupo_cotizacion.id_grupo_cotizacion',
                'log_detalle_grupo_cotizacion.id_oc_cliente',
                'log_detalle_grupo_cotizacion.id_cotizacion'
            )
            ->where(
                [
                    ['log_detalle_grupo_cotizacion.estado', '>', 0],
                    ['log_detalle_grupo_cotizacion.id_cotizacion', '=', $cotizacion->id_cotizacion]
                ]
            )
            ->get();

        $valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.id_cotizacion',
                'log_valorizacion_cotizacion.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_detalle_oc_cliente',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.estado AS estado_valorizacion',
                'log_valorizacion_cotizacion.justificacion',
                'log_valorizacion_cotizacion.id_requerimiento',
                'alm_req.codigo as codigo_requerimiento',

                'alm_item.codigo',
                'alm_item.id_producto',
                'alm_prod.descripcion as descripcion_producto',
                'alm_prod.descripcion',
                'alm_prod.id_unidad_medida',
                'alm_prod.estado AS estado_prod',
                'alm_und_medida.descripcion AS unidad_medida_descripcion',

                'alm_item.id_servicio',
                // 'alm_det_req.id_requerimiento',
                'alm_det_req.id_item',
                'alm_det_req.precio_referencial',
                'alm_det_req.cantidad',
                'alm_det_req.fecha_entrega',
                'alm_det_req.descripcion_adicional',
                'alm_det_req.obs',
                'alm_det_req.partida',
                'alm_det_req.unidad_medida',
                'alm_det_req.fecha_registro'
            )
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'log_valorizacion_cotizacion.id_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')
            ->where(
                [
                    ['log_valorizacion_cotizacion.estado', '>', 0],
                    ['log_valorizacion_cotizacion.id_cotizacion', '=', $cotizacion->id_cotizacion]
                ]
            )
            ->get();

        $items = [];
        foreach ($valorizacion_cotizacion as $data) {
            $items[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_requerimiento' => $data->id_requerimiento,
                'codigo_requerimiento' => $data->codigo_requerimiento,
                'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                'codigo' => $data->codigo ? $data->codigo : '0',
                'descripcion' => $data->descripcion ? $data->descripcion : $data->descripcion_adicional,
                'cantidad' => $data->cantidad,
                'id_unidad_medida' => $data->id_unidad_medida,
                'unidad_medida_descripcion' => $data->unidad_medida_descripcion,
                'fecha_registro' => $data->fecha_registro,
                'estado' => $data->estado_prod
            ];
        }

        $cotizacion_item = [
            'id_cotizacion' => $cotizacion->id_cotizacion,
            'id_grupo_cotizacion' => $detalle_grupo_cotizacion[0]->id_grupo_cotizacion,
            'codigo_cotizacion' => $cotizacion->codigo_cotizacion,
            'codigo_proveedor' => $proveedor[0]->codigo,
            'estado_envio' => $cotizacion->estado_envio,
            'estado' => $cotizacion->estado,
            'empresa' => [
                'id_empresa' => $empresa[0]->id_empresa,
                'razon_social' => $empresa[0]->razon_social,
                'nro_documento' => $empresa[0]->nro_documento,
                'nombre_doc_identidad' => $empresa[0]->nombre_doc_identidad
            ],
            'proveedor' => [
                'id_proveedor' => $proveedor[0]->id_proveedor,
                'razon_social' => $proveedor[0]->razon_social,
                'nro_documento' => $proveedor[0]->nro_documento,
                'id_doc_identidad' => $proveedor[0]->id_doc_identidad,
                'nombre_doc_identidad' => $proveedor[0]->nombre_doc_identidad,
                'contacto' => [
                    'email' => $cotizacion->email_proveedor,
                    'telefono' => $proveedor[0]->telefono
                ]
            ],
            'items' => $items
        ];

        return [$cotizacion_item];
    }

    public function imprimir_cotizacion_excel($id_cotizacion)
    {
        $cotizacionArray = $this->get_cotizacion($id_cotizacion);
        $now = new \DateTime();

        $html = '
        <html>
            <head>
            <style type="text/css">
                *{
                    box-sizing: border-box;
                }
                body{
                    background-color: #fff;
                    font-family: "DejaVu Sans";
                    font-size: 12px;
                    box-sizing: border-box;
                }
                .tablePDF,
                .tablePDF tr td{
                    border: 1px solid #ddd;
                }
                .tablePDF tr td{
                    padding: 5px;
                }
                .subtitle{
                    font-weight: bold;
                }
 
            </style>
            </head>
            <body>
                <h1><center>COTIZACIÓN N°' . $cotizacionArray[0]['codigo_cotizacion'] . '</center></h1>
                <br>
            <table border="0">
            </tr>  
                <tr>
                    <td class="subtitle">N° COTIZACIÓN</td>
                    <td width="300">' . $cotizacionArray[0]['codigo_cotizacion'] . '</td>
                    <td></td>
                    <td class="subtitle">FECHA</td>
                    <td>' . $now->format('d-m-Y') . '</td>
                </tr>
                <tr>
                    <td class="subtitle">CLIENTE</td>
                    <td>' . $cotizacionArray[0]['empresa']['razon_social'] . '</td>
                </tr>    
                <tr>
                    <td class="subtitle">PROVEEDOR</td>
                    <td>' . $cotizacionArray[0]['proveedor']['razon_social'] . '</td>
                </tr>
            </table>
            <br>
            <hr>
            <br>
            <table width="100%" class="tablePDF">
                <tr class="subtitle">
                    <td>Req.</td>
                    <td>Item</td>
                    <td>Descripcion</td>
                    <td>Und. Medida</td>
                    <td>Cantidad Solicitada</td>
                    <td>Und. de Medida</td>
                    <td>Cantidad</td>
                    <td>Precio</td>
                    <td>Lugar de Despacho</td>
                    <td>Sub-Total</td>
                    <td>Incluye IGV</td>
                    <td>Plazo Entrega</td>
                    <td>Garantia</td>
                    <td>Observación</td>
                </tr>   ';

        foreach ($cotizacionArray as $row) {
            foreach ($row['items'] as $item) {

                $id_cotizacion = $item['id_cotizacion'];
                $id_detalle_requerimiento = $item['id_detalle_requerimiento'];
                $codigo_requerimiento = $item['codigo_requerimiento'];
                $codigo = $item['codigo'];
                $unidad_medida = $item['unidad_medida_descripcion'];
                $cantidad = $item['cantidad'];
                $descripcion = $item['descripcion'];

                $html .=
                    '<tr>
                            <td>' . $codigo_requerimiento . '</td>
                            <td>' . $codigo . '</td>
                            <td>' . $descripcion . '</td>
                            <td>' . $unidad_medida . '</td>
                            <td>' . $cantidad . '</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    ';
            }
        }

        $html .= '
        </table>
        <br/>
        <table width="100%" class="tablePDF">
        <tr>
            <th>Tipo Comprobante</th>
            <td colspan="13"></td>
        </tr>
        <tr>
            <th>Condicion Compra</th>
            <td colspan="13"></td>
        </tr>
        <tr>
            <th>N° Cuenta Banco Principal</th>
            <td colspan="13"></td>
        </tr>
        <tr>
            <th>N° Cuenta Banco Alternativa</th>
            <td colspan="13"></td>
        </tr>
        <tr>
            <th>N° Cuenta Detracción</th>
            <td colspan="13"></td>
        </tr>
        </table>
        <p>* Adjuntar fichas técnicas</p>
        </body>
        </html>';

        return $html;
    }

    public function solicitud_cotizacion_excel($id_cotizacion)
    {
        $data = $this->imprimir_cotizacion_excel($id_cotizacion);
        return view('logistica/reportes/downloadExcelFormatoSolicitudCotizacion', compact('data'));
    }

    public function anular_cotizacion($id_cotizacion)
    {
        $Cotizacion = DB::table('logistica.log_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update(['estado' => 7]); //Anulado
        $Valorizacion = DB::table('logistica.log_valorizacion_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update(['estado' => 7]); //Anulado
        $Valorizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->update(['estado' => 7]); //Anulado
        return response()->json($Cotizacion);
    }

    public function detalle_cotizacion($id_cotizacion)
    {
        $cotizacion = DB::table('logistica.log_cotizacion')
            ->where('id_cotizacion', $id_cotizacion)
            ->first();

        $detalle = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.*',
                // DB::raw("SUM(log_valorizacion_cotizacion.subtotal) as suma_subtotal"),
                DB::raw("(CASE 
                    WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                    ELSE 'nulo' END) AS descripcion
                    "),
                DB::raw("(CASE 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                    ELSE 'nulo' END) AS codigo
                    "),
                DB::raw("(CASE 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
                    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                    ELSE 'nulo' END) AS unidad_medida
                    "),
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                'alm_det_req.id_item'
            )
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
            ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->where([
                ['log_valorizacion_cotizacion.id_cotizacion', '=', $id_cotizacion],
                ['log_valorizacion_cotizacion.estado', '=', 2]
            ]) //solo los aprobados
            ->get();

        $html = '';
        $i = 1;
        $sum_subtotal = 0;

        foreach ($detalle as $d) {
            $sum_subtotal += floatval($d->subtotal);
            $html .= '
            <tr>
                <td><input class="oculto" name="id_valorizacion_cotizacion" value="' . $d->id_valorizacion_cotizacion . '"/>' . $i . '</td>
                <td><input class="oculto" name="id_item" value="' . $d->id_item . '"/>' . $d->codigo . '</td>
                <td>' . $d->descripcion . '</td>
                <td>' . $d->unidad_medida . '</td>
                <td>' . $d->cantidad_cotizada . '</td>
                <td>' . $d->precio_cotizado . '</td>
                <td>' . $d->monto_descuento . '</td>
                <td>' . $d->subtotal . '</td>
                <td class="oculto"><input class="oculto" name="id_producto" value="' . $d->id_producto . '"/></td>
                <td class="oculto"><input class="oculto" name="id_servicio" value="' . $d->id_servicio . '"/></td>
                <td class="oculto"><input class="oculto" name="id_equipo" value="' . $d->id_equipo . '"/></td>
            </tr>';
            $i++;
        }
        $igv = DB::table('contabilidad.cont_impuesto')
            ->where([['codigo', '=', 'IGV'], ['fecha_inicio', '<', date('Y-m-d')]])
            ->orderBy('fecha_inicio', 'desc')
            ->first();

        return response()->json(['html' => $html, 'sub_total' => $sum_subtotal, 'igv' => $igv->porcentaje]);
    }

    public function nextCodigoOrden($id_tp_docum)
    {
        $mes = date('m', strtotime("now"));
        $anio = date('y', strtotime("now"));

        $num = DB::table('logistica.log_ord_compra')
            ->where('id_tp_documento', $id_tp_docum)->count();

        $correlativo = $this->leftZero(4, ($num + 1));

        if ($id_tp_docum == 2) {
            $codigoOrden = "OC-{$anio}{$mes}-{$correlativo}";
        } else if ($id_tp_docum == 3) {
            $codigoOrden = "OS-{$anio}{$mes}-{$correlativo}";
        } else {
            $codigoOrden = "-{$anio}{$mes}-{$correlativo}";
        }
        return $codigoOrden;
    }

    public function guardar_orden_compra(Request $request)
    {
        $id_tp_documento =  $request->id_tp_documento;
        $usuario = Auth::user()->id_usuario;
        $codigo = $this->nextCodigoOrden($id_tp_documento);
        $id_orden = DB::table('logistica.log_ord_compra')
            ->insertGetId(
                [
                    'id_grupo_cotizacion' => $request->id_grupo_cotizacion,
                    'id_tp_documento' => $id_tp_documento,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => $usuario,
                    'id_moneda' => $request->id_moneda,
                    'id_proveedor' => $request->id_proveedor,
                    'codigo' => $codigo,
                    'monto_subtotal' => $request->monto_subtotal,
                    'igv_porcentaje' => $request->igv_porcentaje,
                    'monto_igv' => $request->monto_igv,
                    'monto_total' => $request->monto_total,
                    'id_condicion' => $request->id_condicion,
                    'plazo_dias' => $request->plazo_dias,
                    'id_cotizacion' => $request->id_cotizacion,
                    'id_cta_principal' => $request->id_cta_principal,
                    'id_cta_alternativa' => $request->id_cta_alternativa,
                    'id_cta_detraccion' => $request->id_cta_detraccion,
                    'personal_responsable' => $request->responsable,
                    'estado' => 1
                ],
                'id_orden_compra'
            );
        $id_val_array = explode(',', $request->id_val);
        $id_item_array = explode(',', $request->id_item);
        $count = count($id_val_array);

        for ($i = 0; $i < $count; $i++) {
            $id_val = $id_val_array[$i];
            $id_item = $id_item_array[$i];

            DB::table('logistica.log_det_ord_compra')->insert([
                'id_orden_compra' => $id_orden,
                'id_item' => ($id_item ? $id_item : null),
                'id_valorizacion_cotizacion' => $id_val,
                'estado' => 1
            ]);
        }

        DB::table('logistica.log_valorizacion_cotizacion')
            ->where('id_valorizacion_cotizacion', $id_val)
            ->update(['estado' => 5]); // estado Atendido ( con orden)

            // buscar id_req por id_cotizacion
        $id_req = $this->get_id_req_by_id_coti($request->id_cotizacion);
        if(isset($id_req) && $id_req > 0){
            DB::table('almacen.alm_req') //requerimiento cambia su estado
                ->where('id_requerimiento', $id_req)
                ->update(['id_estado_doc' => 5]); // estado Atendido ( con orden)            
        }

        return response()->json($id_orden);
    }

    public function get_id_req_by_id_coti($id_cotizacion){
        $output = DB::table('logistica.log_valorizacion_cotizacion')
        ->select(
            'alm_det_req.id_requerimiento'
        )
        ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
        ->join('almacen.alm_req', 'alm_det_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
        ->where([
            ['log_valorizacion_cotizacion.id_cotizacion', '=', $id_cotizacion]
            ])
        ->first();
 
        $id = $output?$output->id_requerimiento:0;
        return $id;
    }

    public function update_orden_compra(Request $request)
    {
        $data = DB::table('logistica.log_ord_compra')
            ->where('id_orden_compra', $request->id_orden_compra)
            ->update(
                [
                    'id_grupo_cotizacion' => $request->id_grupo_cotizacion,
                    // 'id_moneda' => $request->id_moneda,
                    'id_proveedor' => $request->id_proveedor,
                    'monto_subtotal' => $request->monto_subtotal,
                    'igv_porcentaje' => $request->igv_porcentaje,
                    'monto_igv' => $request->monto_igv,
                    'monto_total' => $request->monto_total,
                    'id_condicion' => $request->id_condicion,
                    'plazo_dias' => $request->plazo_dias,
                    'id_cotizacion' => $request->id_cotizacion,
                    'id_cta_principal' => $request->id_cta_principal,
                    'id_cta_alternativa' => $request->id_cta_alternativa,
                    'id_cta_detraccion' => $request->id_cta_detraccion,
                    'personal_responsable' => $request->responsable
                ],
                'id_orden_compra'
            );

        // $detalle = DB::table('logistica.log_det_ord_compra')
        // ->where([['id_orden_compra','=',$request->id_orden_compra],
        //         ['estado','=',1]])
        // ->get();

        // foreach($detalle as $det){
        DB::table('logistica.log_det_ord_compra')
            ->where('id_orden_compra', $request->id_orden_compra)
            ->update(['estado' => 7]);
        // }

        $id_val_array = explode(',', $request->id_val);
        $id_item_array = explode(',', $request->id_item);
        $count = count($id_val_array);

        for ($i = 0; $i < $count; $i++) {
            $id_val = $id_val_array[$i];
            $id_item = $id_item_array[$i];

            DB::table('logistica.log_det_ord_compra')->insert([
                'id_orden_compra' => $request->id_orden_compra,
                'id_item' => $id_item,
                'id_valorizacion_cotizacion' => $id_val,
                'estado' => 1
            ]);
        }
        return response()->json($data);
    }

    public function mostrar_cuentas_bco($id_contribuyente)
    {
        $data = DB::table('contabilidad.adm_cta_contri')
            ->select(
                'adm_cta_contri.*',
                'adm_contri.razon_social as banco',
                'adm_tp_cta.descripcion as tipo_cta'
            )
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->where([
                ['adm_cta_contri.id_contribuyente', '=', $id_contribuyente],
                ['adm_cta_contri.estado', '=', 1]
            ])
            ->get();
        return response()->json($data);
    }

    public function listar_ordenes()
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*',
                'adm_contri.id_contribuyente',
                'adm_contri.razon_social',
                'adm_contri.nro_documento'
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->where([['log_ord_compra.estado', '<>', 7]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_orden($id_orden)
    {
        $orden = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.*',
                'adm_contri.id_contribuyente',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'adm_estado_doc.estado_doc'
            )
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->join('administracion.adm_estado_doc', 'adm_estado_doc.id_estado_doc', '=', 'log_ord_compra.estado')
            ->where([['log_ord_compra.id_orden_compra', '=', $id_orden]])
            ->first();

        $data = DB::table('contabilidad.adm_cta_contri')
            ->select(
                'adm_cta_contri.*',
                'adm_contri.razon_social as banco',
                'adm_tp_cta.descripcion as tipo_cta'
            )
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->where([
                ['adm_cta_contri.id_contribuyente', '=', $orden->id_contribuyente],
                ['adm_cta_contri.estado', '=', 1]
            ])
            ->get();

        $html = '';
        $detra = '';
        foreach ($data as $d) {
            if ($d->id_tipo_cuenta !== 2) {
                $html .= '<option value="' . $d->id_cuenta_contribuyente . '">' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
            } else {
                $detra .= '<option value="' . $d->id_cuenta_contribuyente . '">' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
            }
        }
        return response()->json(['orden' => $orden, 'html' => $html, 'detra' => $detra]);
    }

    public function listar_detalle_orden($id_orden)
    {
        $detalle = DB::table('logistica.log_det_ord_compra')
            ->select(
                'log_det_ord_compra.*',
                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                ELSE 'nulo' END) AS descripcion
                "),
                DB::raw("(CASE 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                ELSE 'nulo' END) AS codigo
                "),
                DB::raw("(CASE 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                ELSE 'nulo' END) AS unidad_medida
                "),
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal'
                // 'alm_det_req.id_item'
            )
            ->join('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->join('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
            ->leftjoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftjoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftjoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->where([
                ['log_det_ord_compra.id_orden_compra', '=', $id_orden],
                ['log_det_ord_compra.estado', '=', 1]
            ])
            ->get();

        $i = 1;
        $html = '';

        foreach ($detalle as $d) {
            $html .= '
            <tr>
                <td><input class="oculto" name="id_valorizacion_cotizacion" value="' . $d->id_valorizacion_cotizacion . '"/>' . $i . '</td>
                <td><input class="oculto" name="id_item" value="' . $d->id_item . '"/>' . $d->codigo . '</td>
                <td>' . $d->descripcion . '</td>
                <td>' . $d->unidad_medida . '</td>
                <td>' . $d->cantidad_cotizada . '</td>
                <td>' . $d->precio_cotizado . '</td>
                <td>' . $d->monto_descuento . '</td>
                <td>' . $d->subtotal . '</td>
            </tr>';
            $i++;
        }
        return response()->json($html);
    }

    public function guardar_cuenta_banco(Request $request)
    {
        $id_cuenta_contribuyente = DB::table('contabilidad.adm_cta_contri')->insertGetId(
            [
                'id_contribuyente' => $request->id_contribuyente,
                'id_banco' => $request->id_banco,
                'id_tipo_cuenta' => $request->id_tipo_cuenta,
                'nro_cuenta' => $request->nro_cuenta,
                'nro_cuenta_interbancaria' => $request->nro_cuenta_interbancaria,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
            'id_cuenta_contribuyente'
        );

        $data = DB::table('contabilidad.adm_cta_contri')
            ->select(
                'adm_cta_contri.*',
                'adm_contri.razon_social as banco',
                'adm_tp_cta.descripcion as tipo_cta'
            )
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'adm_cta_contri.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'adm_cta_contri.id_tipo_cuenta')
            ->where([
                ['adm_cta_contri.id_contribuyente', '=', $request->id_contribuyente],
                ['adm_cta_contri.estado', '=', 1]
            ])
            ->get();

        $html = '';
        $detra = '';

        foreach ($data as $d) {
            if ($d->id_tipo_cuenta == 2) { //   2->cta de detracción 
                if ($d->id_cuenta_contribuyente == $id_cuenta_contribuyente) {
                    $detra .= '<option value="' . $d->id_cuenta_contribuyente . '" selected>' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
                } else {
                    $detra .= '<option value="' . $d->id_cuenta_contribuyente . '">' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
                }
            } else {
                if ($d->id_cuenta_contribuyente == $id_cuenta_contribuyente) {
                    $html .= '<option value="' . $d->id_cuenta_contribuyente . '" selected>' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
                } else {
                    $html .= '<option value="' . $d->id_cuenta_contribuyente . '">' . $d->nro_cuenta . ' - ' . $d->banco . '</option>';
                }
            }
        }
        if ($request->id_tipo_cuenta == 2) {
            return json_encode(['html' => $detra, 'tipo' => $request->id_tipo_cuenta]);
        } else {
            return json_encode(['html' => $html, 'tipo' => $request->id_tipo_cuenta]);
        }
    }

    public function get_orden($id_orden_compra)
    {
        $data = DB::table('logistica.log_ord_compra')
            ->select(
                'log_ord_compra.codigo',
                'log_ord_compra.plazo_dias',
                'log_ord_compra.fecha AS fecha_orden',
                'log_ord_compra.id_usuario',
                DB::raw("CONCAT(pers.nombres,' ',pers.apellido_paterno,' ',pers.apellido_materno) as nombre_usuario"),
                'log_ord_compra.personal_responsable',
                DB::raw("CONCAT(pers_res.nombres,' ',pers_res.apellido_paterno,' ',pers_res.apellido_materno) as nombre_personal_responsable"),
                'adm_tp_docum.descripcion AS tipo_documento',
                'sis_identi.descripcion AS tipo_doc_proveedor',
                'adm_contri.razon_social AS razon_social_proveedor',
                'adm_contri.nro_documento AS nro_documento_proveedor',
                'adm_contri.telefono AS telefono_proveedor',
                'adm_contri.direccion_fiscal AS direccion_fiscal_proveedor',
                'log_cotizacion.id_empresa',
                'contab_sis_identi.descripcion AS tipo_doc_empresa',
                'contab_contri.razon_social AS razon_social_empresa',
                'contab_contri.nro_documento AS nro_documento_empresa',
                'contab_contri.direccion_fiscal AS direccion_fiscal_empresa',
                'alm_req.codigo AS codigo_requerimiento',

                'cont_tp_doc.descripcion AS tipo_doc_contable',
                'log_cdn_pago.descripcion AS condicion_pago',
                'log_cotizacion.condicion_credito_dias',
                'log_cotizacion.nro_cuenta_principal',
                'log_cotizacion.nro_cuenta_alternativa',
                'log_cotizacion.nro_cuenta_detraccion',
                'log_cotizacion.email_proveedor',
                // 'log_det_ord_compra.*',
                'log_valorizacion_cotizacion.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.precio_cotizado',
                'alm_und_medida.descripcion AS unidad_medida_cotizado',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.garantia',
                'log_valorizacion_cotizacion.lugar_despacho',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) as nombre_personal_autorizado"),

                'alm_det_req.descripcion_adicional AS descripcion_requerimiento',
                'alm_det_req.id_item',
                'alm_item.codigo AS codigo_item',
                'alm_prod.descripcion AS descripcion_producto',
                'alm_prod.codigo AS producto_codigo',
                'log_servi.codigo AS servicio_codigo',
                'log_servi.descripcion AS descripcion_servicio'
            )
            ->leftJoin('logistica.log_det_ord_compra', 'log_det_ord_compra.id_orden_compra', '=', 'log_ord_compra.id_orden_compra')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_ord_compra.id_condicion')
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_valorizacion_cotizacion', '=', 'log_det_ord_compra.id_valorizacion_cotizacion')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_ord_compra.id_usuario')
            ->leftJoin('rrhh.rrhh_trab as trab', 'trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post', 'post.id_postulante', '=', 'trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers', 'pers.id_persona', '=', 'post.id_persona')

            ->leftJoin('configuracion.sis_usua as sis_usua_res', 'sis_usua_res.id_usuario', '=', 'log_ord_compra.personal_responsable')
            ->leftJoin('rrhh.rrhh_trab as trab_res', 'trab_res.id_trabajador', '=', 'sis_usua_res.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu as post_res', 'post_res.id_postulante', '=', 'trab_res.id_postulante')
            ->leftJoin('rrhh.rrhh_perso as pers_res', 'pers_res.id_persona', '=', 'post_res.id_persona')

            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'log_valorizacion_cotizacion.personal_autorizado')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_ord_compra.id_proveedor')
            ->Join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->Join('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->Join('administracion.adm_tp_docum', 'adm_tp_docum.id_tp_documento', '=', 'log_ord_compra.id_tp_documento')
            ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_ord_compra.id_cotizacion')
            ->Join('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
            ->Join('contabilidad.adm_contri as contab_contri', 'contab_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->Join('contabilidad.sis_identi as contab_sis_identi', 'contab_sis_identi.id_doc_identidad', '=', 'contab_contri.id_doc_identidad')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'alm_det_req.id_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->where([
                ['log_ord_compra.id_orden_compra', '=', $id_orden_compra],
                ['log_ord_compra.estado', '=', 1]
            ])
            ->get();

        $orden_header_orden = [];
        $orden_header_proveedor = [];
        $orden_header_empresa = [];
        $orden_condiciones = [];
        $valorizacion = [];

        foreach ($data as $data) {
            $orden_header_orden = [
                'codigo' => $data->codigo,
                'tipo_documento' => $data->tipo_documento,
                'fecha_orden' => $data->fecha_orden,
                'nombre_usuario' => $data->nombre_usuario,
                'nombre_personal_responsable' => $data->nombre_personal_responsable,
                'codigo_requerimiento' => $data->codigo_requerimiento
            ];
            $orden_header_proveedor = [
                'razon_social_proveedor' => $data->razon_social_proveedor,
                'tipo_doc_proveedor' => $data->tipo_doc_proveedor,
                'nro_documento_proveedor' => $data->nro_documento_proveedor,
                'telefono_proveedor' => $data->telefono_proveedor,
                'direccion_fiscal_proveedor' => $data->direccion_fiscal_proveedor,
                'email_proveedor' => $data->email_proveedor
            ];
            $orden_header_empresa = [
                'id_empresa' => $data->id_empresa,
                'razon_social_empresa' => $data->razon_social_empresa,
                'tipo_doc_empresa' => $data->tipo_doc_empresa,
                'nro_documento_empresa' => $data->nro_documento_empresa,
                'direccion_fiscal_empresa' => $data->direccion_fiscal_empresa
            ];
            $orden_condiciones = [
                'tipo_doc_contable' => $data->tipo_doc_contable,
                'condicion_pago' => $data->condicion_pago,
                'plazo_dias' => $data->plazo_dias,
                'condicion_credito_dias' => $data->condicion_credito_dias,
                'nro_cuenta_principal' => $data->nro_cuenta_principal,
                'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
                'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion
            ];
            $valorizacion[] = [
                'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                'codigo_item' => $data->codigo_item,
                'descripcion_producto' => $data->descripcion_producto,
                'descripcion_requerimiento' => $data->descripcion_requerimiento,
                'cantidad_cotizada' => $data->cantidad_cotizada,
                'unidad_medida_cotizado' => $data->unidad_medida_cotizado,
                'precio_cotizado' => $data->precio_cotizado,
                'flete' => $data->flete,
                'porcentaje_descuento' => $data->porcentaje_descuento,
                'monto_descuento' => $data->monto_descuento,
                'subtotal' => $data->subtotal,
                'plazo_entrega' => $data->plazo_entrega,
                'incluye_igv' => $data->incluye_igv,
                'garantia' => $data->garantia,
                'lugar_despacho' => $data->lugar_despacho,
                'nombre_personal_autorizado' => $data->nombre_personal_autorizado
            ];
        }
        $result = [
            'header_orden' => $orden_header_orden,
            'header_proveedor' => $orden_header_proveedor,
            'header_empresa' => $orden_header_empresa,
            'condiciones' => $orden_condiciones,
            'valorizacion' => $valorizacion
        ];

        return $result;
    }

    public function imprimir_orden_pdf($id_orden_compra)
    {
        $ordenArray = $this->get_orden($id_orden_compra);
        // $ordenArray = json_decode($orden, true);

        $now = new \DateTime();

        $html = '
        <html>
            <head>
            <style type="text/css">
                *{
                    box-sizing: border-box;
                }
                body{
                    background-color: #fff;
                    font-family: "DejaVu Sans";
                    font-size: 9px;
                    box-sizing: border-box;
                    padding:10px;
                }
                table{
                    width:100%;
                    border-collapse: collapse;
                }
                .tablePDF thead{
                    padding:4px;
                    background-color:#cc352a;
                }
                .tablePDF,
                .tablePDF tr td{
                    border: 1px solid #dbdbdb;
                }
                .tablePDF tr td{
                    padding: 5px;
                }
                h1{
                    text-transform: uppercase;
                }
                .subtitle{
                    font-weight: bold;
                }
                .bordebox{
                    border: 1px solid #000;
                }
                .verticalTop{
                    vertical-align:top;
                }
                .texttab { 
                    display:block; 
                    margin-left: 20px; 
                    margin-bottom:5px;
                }
                .right{
                    text-align:right;
                }
                .left{
                    text-align:left;
                }
                .justify{
                    text-align: justify;
                }
                .top{
                    vertical-align:top;
                }
                hr{
                    color:#cc352a;
                }
                footer {
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                    height: 40px;
                  }
            </style>
            </head>
            <body>
                <img src="./images/LogoSlogan-80.png" alt="Logo" height="75px">
                <br>
                <hr>
                <h1><center>' . $ordenArray['header_orden']['tipo_documento'] . '<br>' . $ordenArray['header_orden']['codigo'] . '</center></h1>
                <table border="0">
                    <tr>
                        <td class="subtitle verticalTop">Sr.(s)</td>
                        <td class="subtitle verticalTop">:</td>
                        <td width="50%" class="verticalTop">' . $ordenArray['header_proveedor']['razon_social_proveedor'] . '</td>
                        <td width="15%" class="subtitle verticalTop">Fecha de Emisión</td>
                        <td class="subtitle verticalTop">:</td>
                        <td>' . substr($ordenArray['header_orden']['fecha_orden'], 0, 11) . '</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Dirección</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">' . $ordenArray['header_proveedor']['direccion_fiscal_proveedor'] . '</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Telefono</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">' . $ordenArray['header_proveedor']['telefono_proveedor'] . '</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Contacto</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">' . $ordenArray['header_proveedor']['email_proveedor'] . '</td>
                    </tr>
                    <tr>
                        <td class="subtitle">Responsable</td>
                        <td class="subtitle verticalTop">:</td>
                        <td class="verticalTop">' . $ordenArray['header_orden']['nombre_personal_responsable'] . '</td>
                    </tr>
                </table>
                <br>

                <table width="80%" class="tablePDF" border=0>
                <thead>
                    <tr class="subtitle">
                        <td width="2%">#</td>
                        <td width="30%">Descripción</td>
                        <td width="5%">Und</td>
                        <td width="5%">Cant.</td>
                        <td width="5%">Precio</td>
                        <td width="5%">IGV</td>
                        <td width="5%">Monto Dscto</td>
                        <td width="5%">Total</td>
                        <td width="5%">Lugar Despacho</td>
                        <td width="5%">Personal Autorizado</td>
                    </tr>   
                </thead>';

        $total = 0;
        foreach ($ordenArray['valorizacion'] as $key => $data) {
            $html .= '<tr>';
            $html .= '<td>' . ($key + 1) . '</td>';
            $html .= '<td>' . ($data['codigo_item'] ? $data['codigo_item'] : '0') . ' - ' . ($data['descripcion_producto'] ? $data['descripcion_producto'] : $data['descripcion_requerimiento']) . '</td>';
            $html .= '<td>' . $data['unidad_medida_cotizado'] . '</td>';
            $html .= '<td class="right">' . $data['cantidad_cotizada'] . '</td>';
            $html .= '<td class="right">' . $data['precio_cotizado'] . '</td>';
            $html .= '<td class="right">0</td>';
            $html .= '<td class="right">' . $data['monto_descuento'] . '</td>';
            $html .= '<td class="right">' . $data['cantidad_cotizada'] * $data['precio_cotizado'] . '</td>';
            $html .= '<td class="right">' . $data['lugar_despacho'] . '</td>';
            $html .= '<td class="right">' . $data['nombre_personal_autorizado'] . '</td>';
            $html .= '</tr>';
            $total = $total + ($data['cantidad_cotizada'] * $data['precio_cotizado']);
        }

        $html .= '
                <tr>
                    <td class="right" style="font-weight:bold;" colspan="7">TOTAL S/.</td>
                    <td class="right">' . $total . '</td>
                    <td colspan="2"></td>
                </tr>
                </table>
                <p class="subtitle">Condición de Compra</p>
                <table border="0">
                    <tr>
                        <td width="20%"class="verticalTop">Forma de Pago</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['condiciones']['condicion_pago'] . '</td>
                    </tr>
                    <tr>
                        <td width="20%" class="verticalTop">Plazo Entrega</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['condiciones']['plazo_dias'] . '';
        if ($ordenArray['condiciones']['plazo_dias'] > 0) {
            $html .= ' días';
        }
        $html .= '</td>
                    </tr>
                    <tr>
                        <td width="20%"class="verticalTop">Req.</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['header_orden']['codigo_requerimiento'] . '</td>
                    </tr>
                    <br>
                </table>
                <p class="subtitle">Datos de Facturación</p>
                <table border="0">
                    <tr>
                        <td width="20%" class="verticalTop">Razon Social</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['razon_social_empresa'] . '</td>
                    </tr>
                    <tr>
                        <td width="20%"class="verticalTop">' . $ordenArray['header_empresa']['tipo_doc_empresa'] . '</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['nro_documento_empresa'] . '</td>
                    </tr>
                    <tr>
                        <td width="20%"class="verticalTop">Dirección</td>
                        <td width="5%" class="verticalTop">:</td>
                        <td width="70%" class="verticalTop">' . $ordenArray['header_empresa']['direccion_fiscal_empresa'] . '</td>
                    </tr>
                </table>

                <br/>
                <br/>
            
                <footer class="right">GENERADO POR: ' . $ordenArray['header_orden']['nombre_usuario'] . '</footer>
            </body>
            
        </html>';
        // <p class="subtitle">Datos para Despacho</p>
        // <table border="0">
        //     <tr>
        //         <td width="20%" class="verticalTop">Destino / Dirección</td>
        //         <td width="5%" class="verticalTop">:</td>
        //         <td width="70%" class="verticalTop"></td>
        //     </tr>
        //     <tr>
        //         <td width="20%"class="verticalTop">Atención / Personal Autorizado</td>
        //         <td width="5%" class="verticalTop">:</td>
        //         <td width="70%" class="verticalTop"></td>
        //     </tr>
        // </table>
        return $html;
    }

    public function generar_orden_pdf($id_orden_compra)
    {
        $pdf = \App::make('dompdf.wrapper');
        $id = $this->decode5t($id_orden_compra);
        $pdf->loadHTML($this->imprimir_orden_pdf($id));
        return $pdf->stream();
        return $pdf->download('orden.pdf');
    }

    public function anular_orden_compra($id_orden)
    {
        $data = DB::table('logistica.log_ord_compra')
            ->where('id_orden_compra', $id_orden)
            ->update(['estado' => 7]);
        return response()->json($data);
    }

    public function leftZero($lenght, $number)
    {
        $nLen = strlen($number);
        $zeros = '';
        for ($i = 0; $i < ($lenght - $nLen); $i++) {
            $zeros = $zeros . '0';
        }
        return $zeros . $number;
    }

    public function verSession()
    {
        $data = Auth::user();
        return $data;
    }


    // cuadro comparativo

    public function grupo_cotizaciones($codigo_cotiazacion, $codigo_cuadro_comparativo, $id_grupo)
    {
        $hasWhere = null;
        if (strlen($codigo_cotiazacion) > 1) {
            $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
                ->select(
                    'log_grupo_cotizacion.id_grupo_cotizacion',
                    'log_detalle_grupo_cotizacion.id_requerimiento'
                )
                ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_grupo_cotizacion.id_grupo_cotizacion')
                ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_cotizacion')
                ->where([['log_cotizacion.codigo_cotizacion', '=', $codigo_cotiazacion]])
                ->first();
            $hasWhere = ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion];
        }

        if (strlen($codigo_cuadro_comparativo) > 1) {

            $hasWhere = ['log_grupo_cotizacion.codigo_grupo', '=', $codigo_cuadro_comparativo];
        }

        if ($id_grupo > 0) {
            $hasWhere = ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $id_grupo];
        }


        $log_cotizacion = DB::table('logistica.log_cotizacion')
            ->select(
                'log_cotizacion.id_cotizacion',
                'log_grupo_cotizacion.id_grupo_cotizacion',
                'log_grupo_cotizacion.codigo_grupo',
                'log_cotizacion.codigo_cotizacion',
                'cont_tp_doc.descripcion as tipo_documento',
                'log_cdn_pago.descripcion AS condicion_pago',
                'log_cotizacion.nro_cuenta_principal',
                'log_cotizacion.nro_cuenta_alternativa',
                'log_cotizacion.nro_cuenta_detraccion',
                'log_prove.id_proveedor',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion as nombre_doc_identidad'
                // 'alm_req.codigo AS codigo_req'
            )
            ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            // ->Join('logistica.log_valorizacion_cotizacion','log_valorizacion_cotizacion.id_cotizacion','=','log_cotizacion.id_cotizacion')
            // ->join('almacen.alm_req','alm_req.id_requerimiento','=','log_valorizacion_cotizacion.id_requerimiento')

            ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_cotizacion.id_condicion_pago')
            ->where(
                [
                    ['log_cotizacion.estado', '>', 0],
                    ['log_cotizacion.estado', '!=', 7], // 7 =anulados
                    $hasWhere
                ]
            )
            ->get();
        if (sizeof($log_cotizacion) > 0) {
            foreach ($log_cotizacion as $data) {
                $id_cotizaciones[] = $data->id_cotizacion;
                $cotizacionArray[] = [

                    'id_cotizacion' => $data->id_cotizacion,
                    'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                    'codigo_grupo' => $data->codigo_grupo,
                    'codigo_cotizacion' => $data->codigo_cotizacion,
                    // 'codigo_requerimiento'=> $data->codigo_req,
                    'tipo_documento' => $data->tipo_documento,
                    'condicion_pago' => $data->condicion_pago,
                    'nro_cuenta_principal' => $data->nro_cuenta_principal,
                    'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
                    'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion,
                    'proveedor' => [
                        "id_proveedor" => $data->id_proveedor,
                        "razon_social" => $data->razon_social,
                        "nro_documento" => $data->nro_documento,
                        "id_doc_identidad" => $data->id_doc_identidad,
                        "nombre_doc_identidad" => $data->nombre_doc_identidad
                    ],
                    'requerimientos' => []
                ];
            }


            $log_valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
                ->select(
                    'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                    'log_valorizacion_cotizacion.id_cotizacion',
                    'log_valorizacion_cotizacion.id_detalle_requerimiento',
                    'log_valorizacion_cotizacion.id_requerimiento',
                    'alm_req.codigo as codigo_requerimiento'
                )
                ->join('almacen.alm_req', 'alm_req.id_requerimiento', '=', 'log_valorizacion_cotizacion.id_requerimiento')
                ->where(
                    [
                        ['log_valorizacion_cotizacion.estado', '>', 0]
                    ]
                )
                ->whereIn('log_valorizacion_cotizacion.id_cotizacion', $id_cotizaciones)
                ->get();

            $idCotizaciones = [];
            $idRequerimientos = [];
            foreach ($log_valorizacion_cotizacion as $data) {
                // if(in_array($data->id_cotizacion, $idCotizaciones)==false && in_array($data->id_requerimiento, $idRequerimientos)==false){
                // array_push($idCotizaciones,$data->id_cotizacion);
                // array_push($idRequerimientos,$data->id_requerimiento);
                $valorizacionArray[] = [
                    // 'id_valorizacion_cotizacion'=> $data->id_valorizacion_cotizacion,
                    'id_cotizacion' => $data->id_cotizacion,
                    // 'id_detalle_requerimiento'=> $data->id_detalle_requerimiento,
                    'id_requerimiento' => $data->id_requerimiento,
                    'codigo_requerimiento' => $data->codigo_requerimiento
                ];
                // }

            }

            // add codigo de requerimiento
            $storageIdRequerimiento = [];
            for ($i = 0; $i < sizeof($cotizacionArray); $i++) {
                for ($j = 0; $j < sizeof($valorizacionArray); $j++) {
                    if ($cotizacionArray[$i]['id_cotizacion'] == $valorizacionArray[$j]['id_cotizacion']) {

                        if (in_array($valorizacionArray[$j]['id_requerimiento'], $storageIdRequerimiento) == false) {
                            array_push($storageIdRequerimiento, $valorizacionArray[$j]['id_requerimiento']);
                            $cotizacionArray[$i]['requerimientos'][] = $valorizacionArray[$j];
                        }
                    }
                }
                $storageIdRequerimiento = [];
            }

            // $grupoVal=[];
            //     for($j=0; $j< sizeof($valorizacionArray);$j++){
            //         if(in_array($valorizacionArray[$j]['id_cotizacion'],$grupoVal)){
            //             $grupoCotizaReqArray[]= $valorizacionArray[$j];
            //         }

            // }






        } else {
            $cotizacionArray = [];
            return response()->json($cotizacionArray);
        }
        return response()->json($cotizacionArray);
    }

    public function mostrar_cuadro_comparativos()
    {
        $log_grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
            // ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_grupo_cotizacion.id_grupo_cotizacion')
            // ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_cotizacion')
            ->select(
                'log_grupo_cotizacion.*'
            )
            ->where([
                ['log_grupo_cotizacion.estado', '=', 1]
            ])
            ->orderBy('log_grupo_cotizacion.id_grupo_cotizacion', 'desc')
            ->get();

        foreach ($log_grupo_cotizacion as $data) {
            $grupo[] = [
                'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                'codigo_grupo' => $data->codigo_grupo,
                'fecha_inicio' => $data->fecha_inicio
            ];
        }

        $log_detalle_grupo_cotizacion = DB::table('logistica.log_detalle_grupo_cotizacion')
            ->select(
                'log_detalle_grupo_cotizacion.*'
            )
            ->where([
                ['log_detalle_grupo_cotizacion.estado', '=', 1]
            ])
            ->orderBy('log_detalle_grupo_cotizacion.id_detalle_grupo_cotizacion', 'desc')
            ->get();

        foreach ($log_detalle_grupo_cotizacion as $data) {
            $detalle_grupo[] = [
                'id_detalle_grupo_cotizacion' => $data->id_detalle_grupo_cotizacion,
                'id_grupo_cotizacion' => $data->id_grupo_cotizacion,
                'id_cotizacion' => $data->id_cotizacion
            ];
        }

        $log_cotizacion = DB::table('logistica.log_cotizacion')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.adm_contri as adm_contri_empresa', 'adm_contri_empresa.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri as adm_contri_prove', 'adm_contri_prove.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->select(
                'log_cotizacion.id_cotizacion',
                'log_cotizacion.codigo_cotizacion',
                'log_cotizacion.id_proveedor',
                'log_cotizacion.id_empresa',
                'adm_contri_empresa.razon_social as razon_social_empresa',
                'adm_contri_prove.razon_social as razon_social_proveedor'
            )
            ->where([
                ['log_cotizacion.estado', '=', 1]
            ])
            ->orderBy('log_cotizacion.id_cotizacion', 'desc')
            ->get();

        foreach ($log_cotizacion as $data) {
            $cotizacion[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'codigo_cotizacion' => $data->codigo_cotizacion
            ];
            $empresa[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_empresa' => $data->id_empresa,
                'razon_social_empresa' => $data->razon_social_empresa

            ];
            $proveedor[] = [
                'id_cotizacion' => $data->id_cotizacion,
                'id_proveedor' => $data->id_proveedor,
                'razon_social_proveedor' => $data->razon_social_proveedor
            ];
        }

        for ($i = 0; $i < sizeof($detalle_grupo); $i++) {
            $detalle_grupo[$i]['cotizacion'] = [];
        }

        $detalle_grupo_cotizacion = $detalle_grupo;
        for ($i = 0; $i < sizeof($detalle_grupo); $i++) {
            for ($j = 0; $j < sizeof($cotizacion); $j++) {
                if ($detalle_grupo[$i]['id_cotizacion'] == $cotizacion[$j]['id_cotizacion']) {
                    $detalle_grupo_cotizacion[$i]['cotizacion'][] = $cotizacion[$j];
                    $detalle_grupo_cotizacion[$i]['empresa'][] = $empresa[$j];
                    $detalle_grupo_cotizacion[$i]['proveedor'][] = $proveedor[$j];
                }
            }
        }

        $grupo_cotizacion = $grupo;
        for ($i = 0; $i < sizeof($grupo); $i++) {
            for ($j = 0; $j < sizeof($detalle_grupo_cotizacion); $j++) {
                if ($grupo[$i]['id_grupo_cotizacion'] == $detalle_grupo_cotizacion[$j]['id_grupo_cotizacion']) {
                    if (count($detalle_grupo_cotizacion[$j]['cotizacion']) > 0) {

                        $grupo_cotizacion[$i]['proveedor'][] = $detalle_grupo_cotizacion[$j]['proveedor'][0]['razon_social_proveedor'];
                        $grupo_cotizacion[$i]['empresa'][] = $detalle_grupo_cotizacion[$j]['empresa'][0]['razon_social_empresa'];
                    }
                    // $grupo_cotizacion[$i]= $grupo[$i];
                }
            }
        }

        // return response()->json($grupo_cotizacion);
        // return response()->json($detalle_grupo_cotizacion);
        return response()->json(["data" => $grupo_cotizacion]);
    }

    public function mostrar_cuadro_comparativo($id)
    {
        $log_grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
            ->select(
                'log_grupo_cotizacion.*'
            )
            ->where([
                ['log_grupo_cotizacion.estado', '=', 1],
                ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $id]
            ])
            ->orderBy('log_grupo_cotizacion.id_grupo_cotizacion', 'desc')
            ->first();
        return response()->json($log_grupo_cotizacion);
    }

    public function listaItemValorizar($id_cotizacion)
    {
        $item_cotizacion = DB::table('logistica.log_cotizacion')
            ->leftJoin('logistica.log_valorizacion_cotizacion', 'log_valorizacion_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_prod.id_unidad_medida')
            ->leftJoin('almacen.alm_und_medida as alm_und_medida_prov', 'alm_und_medida_prov.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')

            ->select(
                'alm_det_req.id_detalle_requerimiento',
                DB::raw("(CASE 
    WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 

    ELSE 'nulo' END) AS descripcion
    "),
                DB::raw("(CASE 
    WHEN alm_item.id_item isNUll THEN 'SIN CODIGO' 
    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
    ELSE 'nulo' END) AS codigo
    "),
                DB::raw("(CASE 
    WHEN alm_item.id_item isNUll THEN '-' 
    WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.abreviatura
    WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
    WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
    ELSE 'nulo' END) AS unidad_medida
    "),
                'alm_item.id_item',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                'alm_det_req.cantidad',
                'alm_det_req.precio_referencial',
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.id_cotizacion',
                'log_valorizacion_cotizacion.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_detalle_oc_cliente',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'alm_und_medida_prov.abreviatura as abrev_unidad_medida_cotizado',
                'log_valorizacion_cotizacion.id_unidad_medida as id_unidad_medida_cotizado',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.estado',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.garantia',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.lugar_despacho',
                'log_valorizacion_cotizacion.detalle'

            )
            ->where([
                ['log_cotizacion.estado', '>', 0],
                ['log_cotizacion.id_cotizacion', '=', $id_cotizacion]
            ])
            ->orderBy('log_cotizacion.id_cotizacion', 'asc')
            ->get();

        return response()->json($item_cotizacion);
    }

    public function update_valorizacion_item(Request $request)
    {
        $id_val_cot = $request->id_valorizacion_cotizacion;
        $id_cot = $request->id_cotizacion;
        $update = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $id_val_cot)
            ->update([
                'precio_cotizado' => $request->precio_valorizacion,
                'cantidad_cotizada' => $request->cantidad_valorizacion,
                'subtotal' => $request->subtotal_valorizacion,
                'flete' => $request->flete_valorizacion,
                'porcentaje_descuento' => $request->porcentaje_descuento_valorizacion,
                'monto_descuento' => $request->monto_descuento_valorizacion,
                'id_unidad_medida' => $request->unidad_medida_valorizacion
            ]);
        if ($update > 0) {
            $val = $id_cot;
        } else {
            $val = 0;
        }
        return response()->json($val);
    }

    public function update_valorizacion_especificacion(Request $request)
    {
        $id_val_cot = $request->id_valorizacion_cotizacion;
        $id_cot = $request->id_cotizacion;
        $update = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $id_val_cot)
            ->update([
                'plazo_entrega' => $request->plazo_entrega,
                'incluye_igv'   => $request->igv,
                'garantia'      => $request->garantia,
                'lugar_despacho' => $request->lugar_entrega,
                'detalle'       => $request->detalle_adicional
            ]);
        if ($update > 0) {
            $val = $id_cot;
        } else {
            $val = 0;
        }

        return response()->json($val);
    }



    //  ****************** imprimir todo el cuadro comparartivo ****************************************

    public function get_cuadro_comparativo($id)
    {
        $grupo_cotizacion = DB::table('logistica.log_grupo_cotizacion')
            ->select(
                'log_grupo_cotizacion.*'
            )
            ->where([['log_grupo_cotizacion.id_grupo_cotizacion', '=', $id]])
            ->first();

        $cotizaciones = DB::table('logistica.log_grupo_cotizacion')
            ->select(
                'log_detalle_grupo_cotizacion.id_cotizacion'
            )
            ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_grupo_cotizacion.id_grupo_cotizacion')
            ->where([['log_detalle_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion]])
            ->get();

        $cotizacioneArray = [];
        foreach ($cotizaciones as $data) {
            $cotizacioneArray[] = $data->id_cotizacion;
        }

        $requIds = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.id_requerimiento'
            )
            ->where(
                [
                    ['log_valorizacion_cotizacion.estado', '=', 1],
                ]
            )
            ->whereIn('log_valorizacion_cotizacion.id_cotizacion', $cotizacioneArray)
            ->get();

        $reqIdArray = [];
        foreach ($requIds as $data) {
            $reqIdArray[] = $data->id_requerimiento;
        }


        $detalle_requerimiento = DB::table('almacen.alm_req')
            ->select(
                'alm_det_req.id_detalle_requerimiento',
                'alm_req.id_requerimiento',
                'alm_req.codigo as codigo_requerimiento',
                'alm_item.id_item',
                'alm_item.id_producto',
                'alm_item.id_servicio',
                'alm_item.id_equipo',
                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN 'S/C.' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
                ELSE 'nulo' END) AS codigo
                "),
                DB::raw("(CASE
                
                WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
                ELSE 'nulo' END) AS descripcion
                "),
                'alm_det_req.cantidad',
                'alm_det_req.fecha_entrega',
                DB::raw("(CASE 
                WHEN alm_item.id_item isNUll THEN alm_und_medida.descripcion 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.descripcion
                WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
                WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
                ELSE 'nulo' END) AS unidad_medida
                "),
                'alm_det_req.precio_referencial'
            )
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_requerimiento', '=', 'alm_req.id_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftjoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftjoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'alm_det_req.id_unidad_medida')

            ->whereIn('alm_req.id_requerimiento', $reqIdArray)
            ->get();

        $log_cotizacion = DB::table('logistica.log_cotizacion')
            ->select(
                'log_cotizacion.id_cotizacion',
                'log_cotizacion.id_empresa',
                'empresa_adm_contri.id_doc_identidad AS empresa_id_doc_identidad',
                'empresa_sis_identi.descripcion AS empresa_nombre_doc_identidad',
                'empresa_adm_contri.nro_documento AS empresa_nro_documento',
                'empresa_adm_contri.razon_social AS empresa_razon_social',
                'empresa_adm_contri.telefono AS empresa_telefono',
                'empresa_adm_contri.celular AS empresa_celular',
                'empresa_adm_contri.direccion_fiscal AS empresa_direccion_fiscal',
                'log_grupo_cotizacion.codigo_grupo',
                'log_grupo_cotizacion.id_usuario',
                'log_grupo_cotizacion.fecha_inicio',
                'log_grupo_cotizacion.fecha_fin',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS full_name"),
                // 'log_cotizacion.codigo_cotizacion',
                'cont_tp_doc.descripcion as tipo_documento',
                'log_cdn_pago.descripcion AS condicion_pago',
                'log_cotizacion.nro_cuenta_principal',
                'log_cotizacion.nro_cuenta_alternativa',
                'log_cotizacion.nro_cuenta_detraccion',
                'log_cotizacion.email_proveedor',
                'log_prove.id_proveedor',
                'adm_contri.razon_social',
                'adm_contri.nro_documento',
                'adm_contri.id_doc_identidad',
                'sis_identi.descripcion as nombre_doc_identidad'
                // 'alm_req.codigo AS codigo_req'
            )
            ->leftJoin('logistica.log_detalle_grupo_cotizacion', 'log_detalle_grupo_cotizacion.id_cotizacion', '=', 'log_cotizacion.id_cotizacion')
            // ->leftJoin('almacen.alm_req','alm_req.id_requerimiento','=','log_detalle_grupo_cotizacion.id_requerimiento')
            ->leftJoin('logistica.log_grupo_cotizacion', 'log_grupo_cotizacion.id_grupo_cotizacion', '=', 'log_detalle_grupo_cotizacion.id_grupo_cotizacion')
            ->leftJoin('logistica.log_prove', 'log_prove.id_proveedor', '=', 'log_cotizacion.id_proveedor')
            ->leftJoin('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'log_prove.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'adm_contri.id_doc_identidad')
            ->leftJoin('contabilidad.cont_tp_doc', 'cont_tp_doc.id_tp_doc', '=', 'log_cotizacion.id_tp_doc')
            ->leftJoin('logistica.log_cdn_pago', 'log_cdn_pago.id_condicion_pago', '=', 'log_cotizacion.id_condicion_pago')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.adm_contri as empresa_adm_contri', 'empresa_adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as empresa_sis_identi', 'empresa_sis_identi.id_doc_identidad', '=', 'empresa_adm_contri.id_doc_identidad')
            ->leftJoin('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'log_grupo_cotizacion.id_usuario')
            ->leftJoin('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'sis_usua.id_trabajador')
            ->leftJoin('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->leftJoin('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where(
                [
                    ['log_cotizacion.estado', '=', 1],
                    ['log_grupo_cotizacion.id_grupo_cotizacion', '=', $grupo_cotizacion->id_grupo_cotizacion]
                ]
            )
            ->get();

        $empresa_cotizacion = [];
        $proveedor_cotizacion = [];
        $head_cuadro = [];
        foreach ($log_cotizacion as $data) {
            $head_cuadro[] = [
                'codigo_grupo' => $data->codigo_grupo,
                'full_name' => $data->full_name,
                'fecha_inicio' => $data->fecha_inicio,
                'fecha_fin' => $data->fecha_fin,
                'empresa_nombre_doc_identidad' => $data->empresa_nombre_doc_identidad,
                'empresa_nro_documento' => $data->empresa_nro_documento,
                'empresa_razon_social' => $data->empresa_razon_social
            ];
            $empresa_cotizacion[] = [
                'id_empresa' => $data->id_empresa,
                'empresa_id_doc_identidad' => $data->empresa_id_doc_identidad,
                'empresa_nombre_doc_identidad' => $data->empresa_nombre_doc_identidad,
                'empresa_nro_documento' => $data->empresa_nro_documento,
                'empresa_razon_social' => $data->empresa_razon_social,
                'empresa_telefono' => $data->empresa_telefono,
                'empresa_celular' => $data->empresa_celular,
                'empresa_direccion_fiscal' => $data->empresa_direccion_fiscal
            ];
            $proveedor_cotizacion[] = [
                'id_proveedor' => $data->id_proveedor,
                'tipo_documento' => $data->tipo_documento,
                'condicion_pago' => $data->condicion_pago,
                'nro_cuenta_principal' => $data->nro_cuenta_principal,
                'nro_cuenta_alternativa' => $data->nro_cuenta_alternativa,
                'nro_cuenta_detraccion' => $data->nro_cuenta_detraccion,
                'email_proveedor' => $data->email_proveedor,
                'razon_social' => $data->razon_social,
                'nro_documento' => $data->nro_documento,
                'id_doc_identidad' => $data->id_doc_identidad,
                'nombre_doc_identidad' => $data->nombre_doc_identidad
            ];
        }
        $det_req = [];
        foreach ($detalle_requerimiento as $data) {
            $det_req[] = [
                'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                'id_requerimiento' => $data->id_requerimiento,
                'codigo_requerimiento' => $data->codigo_requerimiento,
                'codigo' => $data->codigo,
                'descripcion' => $data->descripcion,
                'cantidad' => $data->cantidad,
                'unidad_medida' => $data->unidad_medida,
                'precio_referencial' => $data->precio_referencial,
                'fecha_entrega' => $data->fecha_entrega
            ];
        }

        $valorizacion_cotizacion = DB::table('logistica.log_valorizacion_cotizacion')
            ->select(
                'log_valorizacion_cotizacion.id_valorizacion_cotizacion',
                'log_valorizacion_cotizacion.id_cotizacion',
                'log_cotizacion.id_proveedor',
                'log_cotizacion.id_empresa',
                'empresa_sis_identi.descripcion AS empresa_nombre_doc_identidad',
                'empresa_adm_contri.nro_documento AS empresa_nro_documento',
                'empresa_adm_contri.razon_social AS empresa_razon_social',
                'log_valorizacion_cotizacion.id_detalle_requerimiento',
                'log_valorizacion_cotizacion.id_detalle_oc_cliente',
                'log_valorizacion_cotizacion.precio_cotizado',
                'log_valorizacion_cotizacion.incluye_igv',
                'log_valorizacion_cotizacion.cantidad_cotizada',
                'log_valorizacion_cotizacion.subtotal',
                'log_valorizacion_cotizacion.flete',
                'log_valorizacion_cotizacion.lugar_despacho',
                'log_valorizacion_cotizacion.plazo_entrega',
                'log_valorizacion_cotizacion.fecha_registro',
                'log_valorizacion_cotizacion.porcentaje_descuento',
                'log_valorizacion_cotizacion.monto_descuento',
                'log_valorizacion_cotizacion.estado AS estado_valorizacion',
                'log_valorizacion_cotizacion.justificacion',
                'alm_item.id_item',
                'alm_prod.estado AS estado_prod',
                DB::raw("(CASE 
            WHEN alm_item.id_item isNUll THEN 'SIN CODIGO' 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.codigo 
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.codigo 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.codigo 
            ELSE 'nulo' END) AS codigo
        "),
                DB::raw("(CASE 
            WHEN alm_item.id_item isNUll THEN alm_det_req.descripcion_adicional 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_prod.descripcion 
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN log_servi.descripcion 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN equipo.descripcion 
            ELSE 'nulo' END) AS descripcion_item
        "),
                'log_valorizacion_cotizacion.id_unidad_medida',
                DB::raw("(CASE 
            WHEN alm_item.id_item isNUll THEN '-' 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_equipo isNull THEN alm_und_medida.descripcion
            WHEN alm_item.id_producto isNUll AND alm_item.id_equipo isNull THEN 'serv' 
            WHEN alm_item.id_servicio isNUll AND alm_item.id_producto isNull THEN 'und' 
            ELSE 'nulo' END) AS unidad_medida_descripcion
        ")
            )
            ->leftJoin('almacen.alm_det_req', 'alm_det_req.id_detalle_requerimiento', '=', 'log_valorizacion_cotizacion.id_detalle_requerimiento')
            ->leftJoin('almacen.alm_item', 'alm_item.id_item', '=', 'alm_det_req.id_item')
            ->leftJoin('almacen.alm_prod', 'alm_prod.id_producto', '=', 'alm_item.id_producto')
            ->leftJoin('logistica.log_servi', 'log_servi.id_servicio', '=', 'alm_item.id_servicio')
            ->leftJoin('logistica.equipo', 'equipo.id_equipo', '=', 'alm_item.id_equipo')
            ->leftJoin('almacen.alm_und_medida', 'alm_und_medida.id_unidad_medida', '=', 'log_valorizacion_cotizacion.id_unidad_medida')
            ->leftJoin('logistica.log_cotizacion', 'log_cotizacion.id_cotizacion', '=', 'log_valorizacion_cotizacion.id_cotizacion')
            ->leftJoin('administracion.adm_empresa', 'adm_empresa.id_empresa', '=', 'log_cotizacion.id_empresa')
            ->leftJoin('contabilidad.adm_contri as empresa_adm_contri', 'empresa_adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->leftJoin('contabilidad.sis_identi as empresa_sis_identi', 'empresa_sis_identi.id_doc_identidad', '=', 'empresa_adm_contri.id_doc_identidad')
            ->where(
                [
                    ['log_valorizacion_cotizacion.estado', '>', 0],
                    // ['log_valorizacion_cotizacion.id_cotizacion', '=',  $id_cotizacion]
                ]
            )
            ->whereIn('log_valorizacion_cotizacion.id_cotizacion', $cotizacioneArray)
            ->get();

        $buena_pro = [];
        $valorizacion = [];
        foreach ($valorizacion_cotizacion as $data) {
            if ($data->estado_valorizacion == 2) {
                $buena_pro[] = [
                    'id_valorizacion_cotizacion' => $data->id_valorizacion_cotizacion,
                    'id_cotizacion' => $data->id_cotizacion,
                    'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                    'id_item' => $data->id_item,
                    'codigo_item' => $data->codigo,
                    'descripcion_item' => $data->descripcion_item,
                    'fecha_registro' => $data->fecha_registro,
                    'precio_cotizado' => $data->precio_cotizado,
                    'cantidad_cotizada' => $data->cantidad_cotizada,
                    'id_unidad_medida' => $data->id_unidad_medida,
                    'unidad_medida_cotizada' => $data->unidad_medida_descripcion,
                    'id_proveedor' => $data->id_proveedor,
                    'id_empresa' => $data->id_empresa,
                    'empresa_razon_social' => $data->empresa_razon_social,
                    'empresa_nombre_doc_identidad' => $data->empresa_nombre_doc_identidad,
                    'empresa_nro_documento' => $data->empresa_nro_documento,
                    'justificacion' => $data->justificacion
                ];
            }
            $valorizacion[] = [
                'id_valorizacion_cotizacion' => $data->id_valorizacion_cotizacion,
                'id_cotizacion' => $data->id_cotizacion,
                'id_proveedor' => $data->id_proveedor,
                'id_detalle_requerimiento' => $data->id_detalle_requerimiento,
                'id_item' => $data->id_item,
                'id_detalle_oc_cliente' => $data->id_detalle_oc_cliente,
                'precio_cotizado' => is_numeric($data->precio_cotizado) == 1 ? $data->precio_cotizado : '',
                'incluye_igv' => $data->incluye_igv,
                'cantidad_cotizada' => $data->cantidad_cotizada,
                'id_unidad_medida' => $data->id_unidad_medida,
                'unidad_medida_cotizada' => $data->unidad_medida_descripcion,
                'subtotal' => $data->subtotal,
                'flete' => $data->flete,
                'lugar_despacho' => $data->lugar_despacho,
                'plazo_entrega' => $data->plazo_entrega,
                'porcentaje_descuento' => $data->porcentaje_descuento,
                'monto_descuento' => $data->monto_descuento,
                'justificacion' => $data->justificacion,
                'estado' => $data->estado_valorizacion,
                'id_empresa' => $data->id_empresa
            ];
        }
        //create => new matriz
        $items = $det_req;

        for ($i = 0; $i < sizeof($valorizacion); $i++) {
            for ($k = 0; $k < sizeof($empresa_cotizacion); $k++) {
                if ($valorizacion[$i]['id_empresa'] === $empresa_cotizacion[$k]['id_empresa']) {
                    $valorizacion[$i]['empresa'] = $empresa_cotizacion[$k];
                }
            }
        }

        // agregar todos los proveedores cada item de detaller requerimiento 
        for ($j = 0; $j < sizeof($proveedor_cotizacion); $j++) {
            for ($i = 0; $i < sizeof($items); $i++) {
                $items[$i]['proveedores'][] = $proveedor_cotizacion[$j];
                $items[$i]['proveedores'][$j]['valorizacion'] = json_decode('{}');
            }
        }

        //agregar valorización
        for ($i = 0; $i < sizeof($items); $i++) {
            for ($j = 0; $j < sizeof($valorizacion); $j++) {
                for ($k = 0; $k < sizeof($items[$i]['proveedores']); $k++) {
                    if ($items[$i]['proveedores'][$k]['id_proveedor'] === $valorizacion[$j]['id_proveedor'] && $items[$i]['id_detalle_requerimiento'] === $valorizacion[$j]['id_detalle_requerimiento']) {
                        $items[$i]['proveedores'][$k]['valorizacion'] = $valorizacion[$j];
                    }
                }
            }
        }

        //add => data proveedor a buena_pro
        for ($i = 0; $i < sizeof($buena_pro); $i++) {
            for ($j = 0; $j < sizeof($proveedor_cotizacion); $j++) {
                if ($buena_pro[$i]['id_proveedor'] === $proveedor_cotizacion[$j]['id_proveedor']) {
                    $buena_pro[$i]['razon_social'] = $proveedor_cotizacion[$j]['razon_social'];
                    $buena_pro[$i]['nombre_doc_identidad'] = $proveedor_cotizacion[$j]['nombre_doc_identidad'];
                    $buena_pro[$i]['nro_documento'] = $proveedor_cotizacion[$j]['nro_documento'];
                }
            }
        }

        $result = [
            'head' => $head_cuadro[0],
            'cuadro_comparativo' => $items,
            'proveedores' => $proveedor_cotizacion,
            'buena_pro' => $buena_pro
        ];
        return $result;
    }

    public function mostrar_comparativo($id_cotizacion)
    {
        $cuadro_comparativo = $this->get_cuadro_comparativo($id_cotizacion);
        return response()->json($cuadro_comparativo);
    }

    function encode5t($str)
    {
        for ($i = 0; $i < 5; $i++) {
            $str = strrev(base64_encode($str));
        }
        return $str;
    }

    function decode5t($str)
    {
        for ($i = 0; $i < 5; $i++) {
            $str = base64_decode(strrev($str));
        }
        return $str;
    }


    public function guardar_buenas_pro(Request $request)
    {
        $buenaProList =  json_decode($request->buenasPro, true);
        $tam = count($buenaProList);
        if ($tam > 0) {
            for ($j = 0; $j < $tam; $j++) {
                $data = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $buenaProList[$j]['id_valorizacion_cotizacion'])
                    ->update([
                        'justificacion'   => $buenaProList[$j]['justificacion'],
                        'estado'          => 2
                    ]);
            }
        } else {
            $data = 0;
        }
        return response()->json($data);
    }

    public function eliminar_buena_pro($id_valorizacion)
    {

        $data = DB::table('logistica.log_valorizacion_cotizacion')->where('id_valorizacion_cotizacion', $id_valorizacion)
            ->update([
                'justificacion'   => '',
                'estado'          => 1
            ]);

        return response()->json($data);
    }

    public function exportar_cuadro_comparativo_excel($id_grupo)
    {
        $data = $this->get_cuadro_comparativo($id_grupo);
        $now = new \DateTime();

        $html = '
        <html>
            <head>
            <style type="text/css">
                *{
                    box-sizing: border-box;
                }
                body{
                    background-color: #fff;
                        font-family: "DejaVu Sans";
                        font-size: 12px;
                        box-sizing: border-box;
                }
                .tablePDF,
                .tablePDF tr td{
                    border: 1px solid #ddd;
                }
                .tablePDF tr td{
                    padding: 5px;
                }
                th{
                    background:#ecf0f5;
                }
                .subtitle{
                    font-weight: bold;
                }
                .center{
                    text-align:center;
                }
                .right{
                    text-align:right;
                }
                .left{
                    text-align:left;
                }
                .justify{
                    text-align: justify;
                }
                .top{
                vertical-align:top;
                }

            </style>
            </head>
            <body>
            ';



        // $path  =  '../public/images/logo_okc.png';



        $nameFile = "/images/logo_okc.png";
        // $nameFile = $request->file("/logo_okc.png");
        // $nameFile =  './../../public/images logo_okc.png';
        // $nameFile = 'https://www.okcomputer.com.pe/wp-content/uploads/2014/11/LogoSlogan-Peque.png';

        $html .= '
            <img src="' . $nameFile . '" height="75px" >

                <h1><center>' . $data['head']['codigo_grupo'] . '</center></h1>
                <br><br>
                <table border="0">
            <tr>
                <td class="subtitle">EMPRESA</td>
                <td>' . $data['head']['empresa_razon_social'] . ' - ' . $data['head']['empresa_nombre_doc_identidad'] . ' ' . $data['head']['empresa_nro_documento'] . ' </td>
            </tr>
            </tr>  
                <tr>
                    <td class="subtitle">N° CUADRO COMP.</td>
                    <td width="300">' . $data['head']['codigo_grupo'] . '</td>
                    <td class="subtitle">FECHA INICIO COTIZACIÓN</td>
                    <td>' . $data['head']['fecha_inicio'] . '</td>
                </tr>
                <tr>
                    <td class="subtitle">COTIZADOR</td>
                    <td>' . $data['head']['full_name'] . '</td>
                    <td class="subtitle">FECHA FIN COTIZACIÓN</td>
                    <td>' . $data['head']['fecha_fin'] . '</td>
                </tr>    
                </table>
                <hr>
                <table width="100%" class="tablePDF">
                <tr class="subtitle">
                    <th rowspan="2">Item</th>
                    <th rowspan="2">Descripcion</th>
                    <th rowspan="2">Cantidad</th>
                    <th rowspan="2">Und. Medida</th>
                    <th rowspan="2">Precio Ref.</th>
                ';

        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<th colspan="9" class="center">' . $item['razon_social'] . '<br>' . $item['nombre_doc_identidad'] . ' ' . $item['nro_documento'] . '</th>';
        }
        // }


        $html .= '
                </tr>
                <tr class="subtitle">
                ';


        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['proveedores'] as $item) {
            $html .= '
                    <th width="10%">Unidad</th>
                    <th width="10%">Cantidad</th>
                    <th width="10%">Precio</th>
                    <th width="10%">IGV</th>
                    <th width="10%">% Descuento</th>
                    <th width="10%">Monto Descuento</th>
                    <th width="10%">Sub-total</th>
                    <th width="10%">Plazo Entrega</th>
                    <th width="20%">Despacho</th>
                    ';
        }
        // }

        $html .= '</tr>';

        foreach ($data['cuadro_comparativo'] as $row) {

            $html .= '
                
                    <tr>
                    <td>&nbsp;' . $row['codigo'] . '</td>
                    <td>' . $row['descripcion'] . '</td>
                    <td>' . $row['cantidad'] . '</td>
                    <td>' . $row['unidad_medida'] . '</td>
                    <td>' . $row['precio_referencial'] . '</td>';

            foreach ($row['proveedores'] as $item) {
                if (count((array) $item['valorizacion']) > 0) {
                    $html .=
                        '
                                <td>' . $item['valorizacion']['cantidad_cotizada'] . '</td>
                                <td>' . $item['valorizacion']['unidad_medida_cotizada'] . '</td>
                                <td>' . $item['valorizacion']['precio_cotizado'] . '</td>
                                <td>' . $item['valorizacion']['incluye_igv'] . '</td>
                                <td>' . $item['valorizacion']['porcentaje_descuento'] . '</td>
                                <td>' . $item['valorizacion']['monto_descuento'] . '</td>
                                <td>' . $item['valorizacion']['subtotal'] . '</td>
                                <td>' . $item['valorizacion']['plazo_entrega'] . '</td>
                                <td>' . $item['valorizacion']['lugar_despacho'] . '</td>
                        ';
                } else {
                    $html .=
                        '
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                    ';
                }
            }
            $html .= '  </tr>';
        }

        $html .= '
        </table>
        <br/>

        <table width="100%" class="tablePDF">
        <tr>
            <th  colspan="5" class="right">Tipo Comprobante</th>
        ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">' . $item['tipo_documento'] . '</td>';
        }
        // }
        $html .=  '
        </tr>
        <tr>
            <th  colspan="5" class="right">Condicion de Compra</th>
            ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">' . $item['condicion_pago'] . '</td>';
        }
        // }
        $html .=  '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Principal</th>
        ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">&nbsp;' . $item['nro_cuenta_principal'] . '</td>';
        }
        // }
        $html .=   '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Alternativa</th>
        ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">&nbsp;' . $item['nro_cuenta_alternativa'] . '</td>';
        }
        // }
        $html .= '
        </tr>
        <tr>
            <th  colspan="5" class="right">Número de Cuenta Banco Detracción</th>
        ';
        // foreach ($data['cuadro_comparativo'] as $row){
        foreach ($data['cuadro_comparativo'][0]['proveedores'] as $item) {
            $html .= '<td colspan="9">&nbsp;' . $item['nro_cuenta_detraccion'] . '</td>';
        }
        // }
        $html .= '
        </tr>
        </table>
        
        <br/> 

        <h3 class="subtitle">BUENA PRO</h3>
        <table width="100%" class="tablePDF">';
        foreach ($data['buena_pro'] as $buenaPro) {
            $html .= '<tr><th class="left">Proveedor:</th><td>' . $buenaPro['razon_social'] . ' ' . $buenaPro['nombre_doc_identidad'] . ':' . $buenaPro['nro_documento'] . '</td><th class="left">Item:</th><td>[' . $buenaPro['codigo_item'] . '] ' . $buenaPro['descripcion_item'] . '</td><th class="left"> Cantidad:</th><td>' . $buenaPro['cantidad_cotizada'] . '</td><th class="left"> Precio:</th><td>' . $buenaPro['precio_cotizado'] . '</td></tr>';
            $html .=  '<tr><td colspan="8" rowspan="2">' . $buenaPro['justificacion'] . '</td></tr>';
            $html .=  '<tr></tr>';
        }
        $html .=  '
        </table>
        </body>
        </html>';

        return $html;
    }

    public function solicitud_cuadro_comparativo_excel($id_grupo)
    {
        $data = $this->exportar_cuadro_comparativo_excel($id_grupo);
        return view('logistica/reportes/downloadExcelFormatoCuadroComparativo', compact('data'));
    }
}
