<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use DateTime;
use Dompdf\Dompdf;
use PDF;

ini_set('max_execution_time', 3600);
date_default_timezone_set('America/Lima');


class PruebaController extends Controller{

    public function __construct(){
        session_start();
    }

    function view_persona(){
        $doc_identi = $this->mostrar_documentos_idendidad();
        $est_civil = $this->mostrar_estados_civiles();
        return view('rrhh/persona', compact('doc_identi', 'est_civil'));
    }

    function view_postulante(){return view('rrhh/postulante');}
    function view_periodo(){return view('rrhh/periodo');}
    function view_tareo(){return view('rrhh/tareo');}
    function view_horario(){return view('rrhh/variables/horario');}
    function view_tolerancia(){return view('rrhh/variables/tolerancia');}
    function view_est_civil(){return view('rrhh/variables/est_civil');}
    function view_derecho_hab(){return view('rrhh/variables/derecho_hab');}
    function view_niv_estudio(){return view('rrhh/variables/niv_estudios');}
    function view_carrera(){return view('rrhh/variables/carrera');}
    function view_tipo_trabajador(){return view('rrhh/variables/tipo_trabajador');}
    function view_tipo_contrato(){return view('rrhh/variables/tipo_contrato');}
    function view_modalidad(){return view('rrhh/variables/modalidad');}
    function view_concepto_rol(){return view('rrhh/variables/concepto_rol');}
    function view_tipo_planilla(){return view('rrhh/variables/tipo_planilla');}

    public function mostrar_documentos_idendidad(){
        $data = DB::table('contabilidad.sis_identi')
            ->select('sis_identi.id_doc_identidad', 'sis_identi.descripcion')
            ->where('estado', '=', 1)->orderBy('sis_identi.id_doc_identidad', 'asc')->get();
        return $data;
    }
    public function mostrar_estados_civiles(){
        $data = DB::table('rrhh.rrhh_est_civil')->select('rrhh_est_civil.id_estado_civil', 'rrhh_est_civil.descripcion')
            ->orderBy('descripcion', 'asc')->get();
            return $data;
    }
    public function mostrar_estado_civil(){
        $data = DB::table('rrhh.rrhh_est_civil')->select('rrhh_est_civil.id_estado_civil', 'rrhh_est_civil.descripcion')
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_carreras(){
        $data = DB::table('rrhh.rrhh_carrera')->select('rrhh_carrera.id_carrera', 'rrhh_carrera.descripcion')
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_cargo(){
        $data = DB::table('rrhh.rrhh_cargo')->select('rrhh_cargo.id_cargo', 'rrhh_cargo.descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_grupo(){
        $data = DB::table('administracion.adm_grupo')->select('adm_grupo.id_grupo', 'adm_grupo.descripcion')
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_proyecto(){
        $data = DB::table('proyectos.proy_proyecto')->select('proy_proyecto.id_proyecto', 'proy_proyecto.descripcion')
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_condicion_dh(){
        $data = DB::table('rrhh.rrhh_cdn_dhab')->select('rrhh_cdn_dhab.id_condicion_dh', 'rrhh_cdn_dhab.descripcion')
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_persona_table(){
        $data = DB::table('rrhh.rrhh_perso')->select('rrhh_perso.*')
            ->where('estado', '=', 1)->orderBy('id_persona', 'asc')->get();

        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_persona($id){
        $data = DB::table('rrhh.rrhh_perso')->select('rrhh_perso.*')
            ->where([['id_persona', $id], ['estado', '=', 1]])->get();
        return response()->json($data);
    }
    public function mostrar_postulante($id){
        $data = DB::table('rrhh.rrhh_postu')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_postu.id_postulante', 'rrhh_postu.direccion', 'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_postu.brevette', 'rrhh_postu.id_pais', 'rrhh_postu.ubigeo', 'rrhh_perso.id_persona', 'rrhh_perso.nro_documento',
                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_persona"))
            ->where('rrhh_postu.id_postulante', $id)->get();
        return response()->json($data);
    }
    public function mostrar_formacion($id){
        $data = DB::table('rrhh.rrhh_frm_acad')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_frm_acad.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_frm_acad.id_formacion')->where('rrhh_frm_acad.id_postulante', $id)->get();

        if ($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_formacion' => 0, 'id_postulante' => $id];
            return response()->json($data);
        }
    }
    public function mostrar_contrato($id){
        $data = DB::table('rrhh.rrhh_contra')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_contra.id_trabajador')
            ->select('rrhh_contra.id_contrato')->where('rrhh_contra.id_trabajador', $id)->get();

        if ($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_contrato' => 0, 'id_trabajador' => $id];
            return response()->json($data);
        }
    }
    public function mostrar_rol($id){
        $data = DB::table('rrhh.rrhh_rol')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_rol.id_trabajador')
            ->select('rrhh_rol.id_rol')->where('rrhh_rol.id_trabajador', $id)->get();
        if ($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_rol' => 0, 'id_trabajador' => $id];
            return response()->json($data);
        }
    }
    public function mostrar_cuentas($id){
        $data = DB::table('rrhh.rrhh_cta_banc')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_cta_banc.id_trabajador')
            ->select('rrhh_cta_banc.id_cuenta_bancaria')->where('rrhh_cta_banc.id_trabajador', $id)->get();

        if ($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_cuenta_bancaria' => 0, 'id_trabajador' => $id];
            return response()->json($data);
        }
    }
    public function mostrar_formacion_table($id){
        $form = DB::table('rrhh.rrhh_frm_acad')
            ->join('rrhh.rrhh_niv_estud', 'rrhh_niv_estud.id_nivel_estudio', '=', 'rrhh_frm_acad.id_nivel_estudio')
            ->join('rrhh.rrhh_carrera', 'rrhh_carrera.id_carrera', '=', 'rrhh_frm_acad.id_carrera')
            ->select('rrhh_frm_acad.*', 'rrhh_niv_estud.descripcion AS nivel_estudio', 'rrhh_carrera.descripcion AS carrera')
            ->where([
                ['rrhh_frm_acad.id_postulante', '=', $id],
                ['rrhh_frm_acad.estado', '=', 1]
            ])->orderBy('rrhh_frm_acad.fecha_inicio')->get();
        $data = ["postu_formacion"=>$form];
        return response()->json($data);
    }
    public function mostrar_derechohabiente_table($id){
        $dhab = DB::table('rrhh.rrhh_der_hab')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_der_hab.id_persona')
            ->join('rrhh.rrhh_cdn_dhab', 'rrhh_cdn_dhab.id_condicion_dh', '=', 'rrhh_der_hab.id_condicion_dh')
            ->select('rrhh_der_hab.*', 'rrhh_cdn_dhab.descripcion AS condicion', 'rrhh_perso.nro_documento AS dni_persona', 'rrhh_perso.fecha_nacimiento',
                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_persona"))
            ->where([
                ['rrhh_der_hab.id_trabajador', '=', $id],
                ['rrhh_der_hab.estado', '=', 1]
            ])->get();

        foreach ($dhab as $row){
            $id_dha = $row->id_derecho_habiente;
            $id_tra = $row->id_trabajador;
            $id_per = $row->id_persona;
            $id_cdh = $row->id_condicion_dh;
            $estado = $row->estado;
            $fe_reg = $row->fecha_registro;
            $condic = $row->condicion;
            $dni_pe = $row->dni_persona;
            $nom_pe = $row->nombre_persona;
            $fe_nac = date('d-m-Y', strtotime($row->fecha_nacimiento));

            $edad = $this->busca_edad($fe_nac);

            // $sqlDH = DB::table('rrhh.')->get();

            $array[] = array(
                'id_derecho_habiente'   => $id_dha,
                'id_trabajador'         => $id_tra,
                'id_persona'            => $id_per,
                'id_condicion_dh'       => $id_cdh,
                'estado'                => $estado,
                'fecha_registro'        => $fe_reg,
                'condicion'             => $condic,
                'dni_persona'           => $dni_pe,
                'nombre_persona'        => $nom_pe,
                'fecha_nacimiento'      => $fe_nac,
                'edad'                  => $edad
            );
        }
        $data = ["derecho_hab"=>$array];
        return response()->json($data);
    }
    public function mostrar_contrato_table($id){
        $contra = DB::table('rrhh.rrhh_contra')
            ->join('rrhh.rrhh_modali', 'rrhh_modali.id_modalidad', '=', 'rrhh_contra.id_modalidad')
            ->join('rrhh.rrhh_tp_contra', 'rrhh_tp_contra.id_tipo_contrato', '=', 'rrhh_contra.id_tipo_contrato')
            ->select('rrhh_contra.*', 'rrhh_tp_contra.descripcion AS tipo_contrato', 'rrhh_modali.descripcion AS modalidad')
            ->where([
                    ['rrhh_contra.id_trabajador', '=', $id],
                    ['rrhh_contra.estado', '=', 1]
            ])->get();
        $data = ["trab_contrato"=>$contra];
        return response()->json($data);
    }
    public function mostrar_rol_table($id){
        $rol = DB::table('rrhh.rrhh_rol')
            ->join('administracion.adm_area', 'adm_area.id_area', '=', 'rrhh_rol.id_area')
            ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_cargo', '=', 'rrhh_rol.id_cargo')
            ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->select('rrhh_rol.*', 'adm_area.descripcion AS area', 'rrhh_cargo.descripcion AS cargo', 'rrhh_rol_concepto.descripcion AS concepto')
            ->where([
                ['rrhh_rol.id_trabajador', '=', $id],
                ['rrhh_rol.estado', '=', 1]
            ])->get();
        $data = ["trab_rol"=>$rol];
        return response()->json($data);
    }
    public function mostrar_cuentas_table($id){
        $cuenta = DB::table('rrhh.rrhh_cta_banc')
            ->join('contabilidad.adm_tp_cta', 'adm_tp_cta.id_tipo_cuenta', '=', 'rrhh_cta_banc.id_tipo_cuenta')
            ->join('contabilidad.cont_banco', 'cont_banco.id_banco', '=', 'rrhh_cta_banc.id_banco')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
            ->select('rrhh_cta_banc.*', 'adm_tp_cta.descripcion AS tipo_cuenta', 'adm_contri.razon_social AS banco')
            ->where([
                ['rrhh_cta_banc.id_trabajador', '=', $id],
                ['rrhh_cta_banc.estado', '=', 1]
            ])->get();
            
        $data = ["trab_cuenta"=>$cuenta];
        return response()->json($data);
    }
    public function mostrar_experiencia($id){
        $postu = DB::table('rrhh.rrhh_exp_labo')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_exp_labo.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_exp_labo.id_experiencia_laboral')->where('rrhh_exp_labo.id_postulante', $id)->count();

        if ($postu > 0) {
            $data = DB::table('rrhh.rrhh_exp_labo')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_exp_labo.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_exp_labo.*')->where('rrhh_exp_labo.id_postulante', $id)->get();
            return response()->json($data);
        }else{
            $data[0] = ['id_experiencia_laboral' => 0, 'id_postulante' => $id];
            return response()->json($data);
        }
    }
    public function mostrar_experiencia_table($id){
        $exper = DB::table('rrhh.rrhh_exp_labo')
            ->select('rrhh_exp_labo.*')
            ->where([
                ['rrhh_exp_labo.id_postulante', '=', $id],
                ['rrhh_exp_labo.estado', '=', 1]
            ])->orderBy('rrhh_exp_labo.fecha_ingreso')->get();
        $data = ["postu_experiencia"=>$exper];
        return response()->json($data);
    }
    public function mostrar_postulante_id($id){
        $data = DB::table('rrhh.rrhh_postu')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh.rrhh_postu.id_postulante', 'rrhh_perso.nro_documento')
            ->where('rrhh_perso.nro_documento', $id)->get();
        return response()->json($data);
    }
    public function mostrar_trabajador($id){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_trab.id_trabajador', 'rrhh_trab.id_tipo_trabajador', 'rrhh_trab.condicion', 'rrhh_trab.hijos', 'rrhh_trab.id_pension', 'rrhh_trab.seguro', 'rrhh_trab.confianza', 'rrhh_postu.id_postulante',
                    'rrhh_perso.nro_documento AS dni_postulante', 'rrhh_trab.cuspp', 'rrhh_trab.id_categoria_ocupacional', 'rrhh_trab.id_tipo_planilla',
                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_postulante"))
        ->where('rrhh_trab.id_trabajador', $id)->get();
        return response()->json($data);
    }
    public function mostrar_trabajador_id($id){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_trab.id_trabajador', 'rrhh_perso.nro_documento')
            ->where('rrhh_perso.nro_documento', $id)->get();
        return response()->json($data);
    }
    public function mostrar_trabajador_dni($id){
        $trab = 0;
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_trab.id_trabajador')
            ->where('rrhh_perso.nro_documento', $id)->first();
        if ($data){
            $trab = $data->id_trabajador;
        }
        return $trab;
    }
    public function mostrar_postulante_table(){
        $data = DB::table('rrhh.rrhh_postu')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_postu.id_postulante', 'rrhh_postu.direccion', 'rrhh_postu.telefono', 'rrhh_postu.correo', 'rrhh_perso.nombres','rrhh_perso.apellido_paterno','rrhh_perso.apellido_materno', 'rrhh_perso.nro_documento')
            ->where('rrhh_perso.estado', '=', 1)
            ->orderBy('id_postulante','asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_trabajador_table(){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
            ->select('rrhh_trab.id_trabajador', 'rrhh_postu.direccion', 'rrhh_postu.telefono', 'rrhh_perso.nombres','rrhh_perso.apellido_paterno','rrhh_perso.apellido_materno', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_perso.nro_documento')
            ->orderBy('id_trabajador','asc')->get();
        return response()->json($data);
    }
    public function mostrar_postulante_observacion($id){
        $data = DB::table('rrhh.rrhh_obs_postu')
            ->join('configuracion.sis_usua', 'sis_usua.id_usuario', '=', 'rrhh_obs_postu.id_usuario')
            ->select('rrhh_obs_postu.*', 'sis_usua.usuario')
            ->where([['rrhh_obs_postu.id_postulante', '=', $id],['rrhh_obs_postu.estado', '=', 1]])
            ->orderBy('rrhh_obs_postu.id_observacion','asc')->get();
        return response()->json($data);
    }
    public function mostrar_nivel_estudios(){
        $data = DB::table('rrhh.rrhh_niv_estud')->select('id_nivel_estudio', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_categoria_ocupacionl(){
        $data = DB::table('rrhh.rrhh_cat_ocupac')->select('id_categoria_ocupacional', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_trabajador(){
        $data = DB::table('rrhh.rrhh_tp_trab')->select('id_tipo_trabajador', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_fondo_pension(){
        $data = DB::table('rrhh.rrhh_pensi')->select('id_pension', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_modalidad(){
        $data = DB::table('rrhh.rrhh_modali')->select('id_modalidad', 'descripcion')->where('estado', '=', 1)
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_horario(){
        $data = DB::table('rrhh.rrhh_horario')->select('id_horario', 'descripcion')->where('estado', '=', 1)
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_horario_id($id){
        $data = DB::table('rrhh.rrhh_horario')->where('id_horario', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_tolerancia_id($id){
        $data = DB::table('rrhh.rrhh_tolerancia')->where('id_tolerancia', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_est_civil_id($id){
        $data = DB::table('rrhh.rrhh_est_civil')->where('id_estado_civil', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_nivel_estudios_id($id){
        $data = DB::table('rrhh.rrhh_niv_estud')->where('id_nivel_estudio', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_carrera_id($id){
        $data = DB::table('rrhh.rrhh_carrera')->where('id_carrera', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_tipo_trabajador_id($id){
        $data = DB::table('rrhh.rrhh_tp_trab')->where('id_tipo_trabajador', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_tipo_contrato_id($id){
        $data = DB::table('rrhh.rrhh_tp_contra')->where('id_tipo_contrato', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_modalidad_id($id){
        $data = DB::table('rrhh.rrhh_modali')->where('id_modalidad', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_concepto_rol_id($id){
        $data = DB::table('rrhh.rrhh_rol_concepto')->where('id_rol_concepto', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_tipo_planilla_id($id){
        $data = DB::table('rrhh.rrhh_tp_plani')->where('id_tipo_planilla', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_area(){
        $data = DB::table('administracion.adm_area')->select('id_area', 'descripcion')->where('estado', '=', 1)
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_roles(){
        $data = DB::table('rrhh.rrhh_rol_concepto')->select('id_rol_concepto', 'descripcion')->where('estado', '=', 1)
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_contrato(){
        $data = DB::table('rrhh.rrhh_tp_contra')->select('id_tipo_contrato', 'descripcion')->where('estado', '=', 1)
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_cuenta(){
        $data = DB::table('contabilidad.adm_tp_cta')->select('id_tipo_cuenta', 'descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_moneda(){
        $data = DB::table('configuracion.sis_moneda')->select('id_moneda', 'descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_banco(){
        $data = DB::table('contabilidad.cont_banco')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'cont_banco.id_contribuyente')
                ->select('cont_banco.id_banco', 'adm_contri.razon_social AS descripcion')
                ->orderBy('adm_contri.razon_social', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_seleccion_table(){
        $data = DB::table('rrhh.rrhh_selec')
            ->join('administracion.adm_empresa', 'rrhh_selec.id_empresa', '=', 'adm_empresa.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->join('rrhh.rrhh_cargo', 'rrhh_selec.id_cargo', '=', 'rrhh_cargo.id_cargo')
            ->select('rrhh_selec.id_seleccion', 'adm_contri.razon_social', 'rrhh_cargo.descripcion', 'rrhh_selec.requisitos', 'rrhh_selec.perfil', 'rrhh_selec.lugar', 'rrhh_selec.cantidad')
            ->where('rrhh_selec.estado', '=', 1)->orderBy('rrhh_selec.id_seleccion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_seleccion($id){
        $data = DB::table('rrhh.rrhh_selec')
            ->select('rrhh_selec.id_seleccion', 'rrhh_selec.id_cargo', 'rrhh_selec.cantidad', 'rrhh_selec.lugar', 'rrhh_selec.fecha_inicio', 'rrhh_selec.fecha_fin', 'rrhh_selec.perfil', 'rrhh_selec.requisitos')
            ->where('rrhh_selec.id_seleccion', $id)->get();
        return response()->json($data);
    }
    public function mostrar_tipo_asistencia(){
        $data = DB::table('rrhh.rrhh_tp_asist')->select('rrhh_tp_asist.id_tipo_asistencia', 'rrhh_tp_asist.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_trab_asist($ia, $tp){
        $data = array();
        $main = DB::table('rrhh.rrhh_asist')->where('id_asistencia', $ia)->first();
        $ini = $main->fecha_inicio;
        $fin = $main->fecha_fin;

        $sql_verify = DB::table('rrhh.rrhh_pre_calculo')->where('id_asistencia', '=', $ia)->get();

        if ($sql_verify->count() > 0) {
            $sql_trab = DB::table('rrhh.rrhh_pre_calculo')->select(DB::raw('DISTINCT id_trabajador'))
                ->where('id_asistencia', '=', $ia)->orderBy('id_trabajador', 'asc')->get();

            foreach ($sql_trab as $row){
                $trabaj = $row->id_trabajador;

                $sql_name = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->select(DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                    ->where('rrhh_trab.id_trabajador', '=', $trabaj)
                    ->first();
                
                // foreach ($sql_name as $key){
                //     $dt_tra = $key->datos_trabajador;
                // }
                $dt_tra = $sql_name->datos_trabajador;
                $fi = strtotime($ini);
                $ff = strtotime($fin);

                $data_prev = array($trabaj, $dt_tra);
                $cont = 0;

                for ($i = $fi; $i < $ff; $i+=86400){
                    $fecha = date('Y-m-d', $i);

                    $query = DB::table('rrhh.rrhh_pre_calculo')
                        ->select('rrhh_pre_calculo.hora')
                        ->where([['rrhh_pre_calculo.fecha', $fecha], ['rrhh_pre_calculo.id_trabajador', $trabaj], ['rrhh_pre_calculo.id_asistencia', $ia]])
                        ->get();
                
                    if ($query->count() > 0){
                        foreach ($query as $rows){
                            $ht = $rows->hora;
                        }
                        $var = number_format($ht, 2);
                    }else{
                        $var = number_format(0, 2);
                    }
                    $cont += $var;
                    array_push($data_prev, $var);
                }
                $cont = number_format($cont, 2);
                array_push($data_prev, $cont);
                $data[] = $data_prev;
            }
        }else{
            $sql = DB::table('rrhh.rrhh_asi_diaria')
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_asi_diaria.id_trabajador')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->select('rrhh_asi_diaria.id_trabajador', DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                ->where([['rrhh_asi_diaria.id_asistencia', $ia], ['rrhh_asi_diaria.id_tipo_trabajador', $tp]])
                ->groupBy('rrhh_asi_diaria.id_trabajador', 'datos_trabajador')
                ->orderBy('rrhh_asi_diaria.id_trabajador','asc')->get();
    
            foreach ($sql as $row){
                $id_tra = $row->id_trabajador;
                $dt_tra = $row->datos_trabajador;
    
                $fi = strtotime($ini);
                $ff = strtotime($fin);
    
                $data_prev = array($id_tra, $dt_tra);
                $cont = 0;

                for ($i = $fi; $i < $ff; $i+=86400){
                    $fecha = date('Y-m-d', $i);
                    
                    $query = DB::table('rrhh.rrhh_asi_diaria')
                        ->select('rrhh_asi_diaria.hora_trabajada')
                        ->where([['rrhh_asi_diaria.fecha_asistencia', $fecha], ['rrhh_asi_diaria.id_trabajador', $id_tra]])->get();
    
                    if ($query->count() > 0){
                        foreach ($query as $rows){
                            $ht = $rows->hora_trabajada;
                        }
                        $var = number_format($ht, 2);
                    }else{
                        $var = number_format(0, 2);
                    }
                    $cont += $var;
                    array_push($data_prev, $var);
                }
                $cont = number_format($cont, 2);
                array_push($data_prev, $cont);
                $data[] = $data_prev;
            }
        }
        return response()->json($data);
    }
    public function mostrar_trab_tareo($id_asi, $tp_trab, $fe_asi, $tp_ing, $tp_hor){
        $dia = $this->filtrar_dia($fe_asi);

        if ($dia > 0 && $dia < 6){
            /// tipo horario (1 => regular, 2 => almuerzo)
            if ($tp_hor == 1) {
                /// tipo ingreso (1 => entrada, 2 => salida)
                if ($tp_ing == 1){
                    $sql = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_entrada')->get();
                    $info = 1;
                }else{
                    $sql_verify = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_entrada_almuerzo')->get();
                    if ($sql_verify->count() > 0){
                        $info = 1;
                        $sql = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_salida')->get();
                    }else{
                        $info = 0;
                    }
                }
            }else{
                if ($tp_ing == 1){
                    $sql_verify = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_salida_almuerzo')->get();
                    if ($sql_verify->count() > 0){
                        $info = 1;
                        $sql = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_entrada_almuerzo')->get();
                    }else{
                        $info = 0;
                    }
                }else{
                    $sql_verify = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_entrada')->get();
                    if ($sql_verify->count() > 0){
                        $info = 1;
                        $sql = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_salida_almuerzo')->get();
                    }else{
                        $info = 0;
                    }
                }
            }
    
            if ($info > 0){
                if ($sql->count() > 0){
                    if ($tp_hor == 1){
                        if ($tp_ing == 1){
                            $prevData = DB::table('rrhh.rrhh_asi_diaria')
                                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_asi_diaria.id_trabajador')
                                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                                ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                                ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                                ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_asi_diaria.hora_entrada AS horario',
                                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                                ->where([
                                    ['rrhh_asi_diaria.id_asistencia', '=', $id_asi],
                                    ['rrhh_asi_diaria.fecha_asistencia', '=', $fe_asi],
                                    ['rrhh_asi_diaria.id_tipo_trabajador', '=', $tp_trab],
                                    ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                                ])->orderBy('rrhh_trab.id_trabajador','asc')->get();
                        }else{
                            $prevData = DB::table('rrhh.rrhh_asi_diaria')
                                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', 'rrhh_asi_diaria.id_trabajador')
                                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                                ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                                ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                                ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_asi_diaria.hora_salida AS horario',
                                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                                ->where([
                                    ['rrhh_asi_diaria.id_asistencia', '=', $id_asi],
                                    ['rrhh_asi_diaria.fecha_asistencia', '=', $fe_asi],
                                    ['rrhh_asi_diaria.id_tipo_trabajador', '=', $tp_trab],
                                    ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                                ])->orderBy('rrhh_trab.id_trabajador','asc')->get();
                        }
                    }else{
                        if ($tp_ing == 1){
                            $prevData = DB::table('rrhh.rrhh_asi_diaria')
                                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_asi_diaria.id_trabajador')
                                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                                ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                                ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                                ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_asi_diaria.hora_entrada_almuerzo AS horario',
                                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                                ->where([
                                    ['rrhh_asi_diaria.id_asistencia', '=', $id_asi],
                                    ['rrhh_asi_diaria.fecha_asistencia', '=', $fe_asi],
                                    ['rrhh_asi_diaria.id_tipo_trabajador', '=', $tp_trab],
                                    ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                                ])->orderBy('rrhh_trab.id_trabajador','asc')->get();
                        }else{
                            $prevData = DB::table('rrhh.rrhh_asi_diaria')
                                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', 'rrhh_asi_diaria.id_trabajador')
                                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                                ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                                ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                                ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_asi_diaria.hora_salida_almuerzo AS horario',
                                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                                ->where([
                                    ['rrhh_asi_diaria.id_asistencia', '=', $id_asi],
                                    ['rrhh_asi_diaria.fecha_asistencia', '=', $fe_asi],
                                    ['rrhh_asi_diaria.id_tipo_trabajador', '=', $tp_trab],
                                    ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                                ])->orderBy('rrhh_trab.id_trabajador','asc')->get();
                        }
                    }
                    $okc = 1;
                }else{
                    if ($tp_hor == 1){
                        if ($tp_ing == 1) {
                            $prevData = DB::table('rrhh.rrhh_trab')
                                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                                    ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                                    ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                                    ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador',
                                            DB::raw("('08:30') AS horario"),
                                            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                                    ->where([
                                        ['rrhh_trab.id_tipo_trabajador', '=', $tp_trab],
                                        ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                                    ])->orderBy('rrhh_trab.id_trabajador','asc')->get();
                        }else{
                            $prevData = DB::table('rrhh.rrhh_trab')
                                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                                    ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                                    ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                                    ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador',
                                            DB::raw("('18:30') AS horario"),
                                            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                                    ->where([
                                        ['rrhh_trab.id_tipo_trabajador', '=', $tp_trab],
                                        ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                                    ])->orderBy('rrhh_trab.id_trabajador','asc')->get();
                        }
                    }else{
                        if ($tp_ing == 1) {
                            $prevData = DB::table('rrhh.rrhh_trab')
                                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                                    ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                                    ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                                    ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador',
                                            DB::raw("('14:00') AS horario"),
                                            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                                    ->where([
                                        ['rrhh_trab.id_tipo_trabajador', '=', $tp_trab],
                                        ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                                    ])->orderBy('rrhh_trab.id_trabajador','asc')->get();
                        }else{
                            $prevData = DB::table('rrhh.rrhh_trab')
                                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                                    ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                                    ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                                    ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador',
                                            DB::raw("('13:00') AS horario"),
                                            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                                    ->where([
                                        ['rrhh_trab.id_tipo_trabajador', '=', $tp_trab],
                                        ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                                    ])->orderBy('rrhh_trab.id_trabajador','asc')->get();
                        }
                    }
                    $okc = 0;
                }
            }else{
                $prevData = '';
                $okc = 2;
            }
        }else{
            if ($tp_hor == 1){
                /// tipo ingreso
                if ($tp_ing == 1) {
                    $sql = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_entrada')->get();
                }else{
                    $sql = DB::table('rrhh.rrhh_asi_diaria')
                            ->where([['id_asistencia', $id_asi], ['fecha_asistencia', $fe_asi], ['id_tipo_trabajador', $tp_trab]])
                            ->whereNotNull('hora_salida')->get();
                }
                
                if ($sql->count() > 0) {
                    if ($tp_ing == 1) {
                        $prevData = DB::table('rrhh.rrhh_asi_diaria')
                            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', 'rrhh_asi_diaria.id_trabajador')
                            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                            ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                            ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                            ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_asi_diaria.hora_entrada AS horario',
                                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                            ->where([
                                ['rrhh_asi_diaria.id_asistencia', '=', $id_asi],
                                ['rrhh_asi_diaria.fecha_asistencia', '=', $fe_asi],
                                ['rrhh_asi_diaria.id_tipo_trabajador', '=', $tp_trab],
                                ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                            ])->get();
                        $okc = 1;
                    }else{
                        $prevData = DB::table('rrhh.rrhh_asi_diaria')
                            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', 'rrhh_asi_diaria.id_trabajador')
                            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                            ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                            ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                            ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_asi_diaria.hora_salida AS horario',
                                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                            ->where([
                                ['rrhh_asi_diaria.id_asistencia', '=', $id_asi],
                                ['rrhh_asi_diaria.fecha_asistencia', '=', $fe_asi],
                                ['rrhh_asi_diaria.id_tipo_trabajador', '=', $tp_trab],
                                ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                            ])->get();
                        $okc = 1;
                    }
                }else{////// probando
                    if ($tp_ing == 1) {
                        $prevData = DB::table('rrhh.rrhh_trab')
                            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                            ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                            ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                            ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador',
                                    DB::raw("('09:00') AS horario"),
                                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                            ->where([
                                ['rrhh_trab.id_tipo_trabajador', '=', $tp_trab],
                                ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                            ])
                            ->orderBy('id_trabajador','asc')->get();
                    }else{
                        $prevData = DB::table('rrhh.rrhh_trab')
                            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                            ->join('rrhh.rrhh_contra', 'rrhh_contra.id_trabajador', '=', 'rrhh_trab.id_trabajador')
                            ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
                            ->select('rrhh_trab.id_trabajador', 'rrhh_tp_trab.descripcion AS tipo_trabajador',
                                    DB::raw("('12:00') AS horario"),
                                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
                            ->where([
                                ['rrhh_trab.id_tipo_trabajador', '=', $tp_trab],
                                ['rrhh_contra.fecha_inicio', '<=', $fe_asi]
                            ])
                            ->orderBy('id_trabajador','asc')->get();
                    }
                    $okc = 0;
                }
            }else{
                $prevData = '';
                $okc = 3; 
            }
        }

        $data[] = ['data' => $prevData, 'exist' => $okc];
        return response()->json($data);
    }
    public function mostrar_vacaciones($id){
        $vaca = DB::table('rrhh.rrhh_vacac')->where('id_trabajador', $id)->get();
        $data = ["vacaciones"=>$vaca];
        return response()->json($data);
    }
    public function mostrar_prestamo_table($id){
        $presta = DB::table('rrhh.rrhh_presta')
            ->select('rrhh_presta.*')
            ->where([
                ['rrhh_presta.id_trabajador', '=', $id],
                ['rrhh_presta.estado', '=', 1]
            ])
            ->orderBy('id_prestamo', 'asc')->get();
        $data = ["prestamo"=>$presta];
        return response()->json($data);
    }
    public function mostrar_permiso_table($id){
        $permi = DB::table('rrhh.rrhh_permi')
            ->select('rrhh_permi.*')
            ->where([
                ['rrhh_permi.id_trabajador', '=', $id],
                ['rrhh_permi.estado', '=', 1]
            ])
            ->orderBy('id_permiso', 'asc')->get();
        $data = ["permiso"=>$permi];
        return response()->json($data);
    }
    public function mostrar_comision_table($id){
        $comision = DB::table('rrhh.rrhh_com_salida')
            ->select('rrhh_com_salida.*')
            ->where([
                ['rrhh_com_salida.id_trabajador', '=', $id],
                ['rrhh_com_salida.estado', '=', 1]
            ])
            ->orderBy('id_comision_salida', 'asc')->get();
        $data = ["comision"=>$comision];
        return response()->json($data);
    }
    public function mostrar_hextras_table($id){
        $hextra = DB::table('rrhh.rrhh_hrs_extra')
            ->select('rrhh_hrs_extra.*')
            ->where([
                ['rrhh_hrs_extra.id_trabajador', '=', $id],
                ['rrhh_hrs_extra.estado', '=', 1]
            ])
            ->orderBy('id_hora_extra', 'asc')->get();
        $data = ["hextras"=>$hextra];
        return response()->json($data);
    }
    public function mostrar_reintegro_table($id){
        $reinte = DB::table('rrhh.rrhh_reintegro')
            ->select('rrhh_reintegro.*')
            ->where([
                ['rrhh_reintegro.id_trabajador', '=', $id],
                ['rrhh_reintegro.estado', '=', 1]
            ])
            ->orderBy('id_reintegro', 'asc')->get();
        $data = ["reintegro"=>$reinte];
        return response()->json($data);
    }
    public function mostrar_bonificacion_table($id){
        $permi = DB::table('rrhh.rrhh_bonif')
            ->join('rrhh.rrhh_var_bonif', 'rrhh_var_bonif.id_variable_bonificacion', 'rrhh_bonif.id_variable_bonificacion')
            ->select('rrhh_bonif.*', 'rrhh_var_bonif.descripcion AS tipo_bonificacion')
            ->where([
                ['rrhh_bonif.id_trabajador', '=', $id],
                ['rrhh_bonif.estado', '=', 1]
            ])
            ->orderBy('id_bonificacion', 'asc')->get();
        $data = ["bonificacion"=>$permi];
        return response()->json($data);
    }
    public function mostrar_descuento_table($id){
        $permi = DB::table('rrhh.rrhh_dscto')
            ->join('rrhh.rrhh_var_dscto', 'rrhh_var_dscto.id_variable_descuento', 'rrhh_dscto.id_variable_descuento')
            ->select('rrhh_dscto.*', 'rrhh_var_dscto.descripcion AS tipo_descuento')
            ->where([
                ['rrhh_dscto.id_trabajador', '=', $id],
                ['rrhh_dscto.estado', '=', 1]
            ])
            ->orderBy('id_descuento', 'asc')->get();
        $data = ["descuento"=>$permi];
        return response()->json($data);
    }
    public function mostrar_retencion_table($id){
        $permi = DB::table('rrhh.rrhh_retencion')
            ->join('rrhh.rrhh_var_reten', 'rrhh_var_reten.id_variable_retencion', 'rrhh_retencion.id_variable_retencion')
            ->select('rrhh_retencion.*', 'rrhh_var_reten.descripcion AS tipo_retencion')
            ->where([
                ['rrhh_retencion.id_trabajador', '=', $id],
                ['rrhh_retencion.estado', '=', 1]
            ])
            ->orderBy('id_retencion', 'asc')->get();
        $data = ["retencion"=>$permi];
        return response()->json($data);
    }
    public function mostrar_aporte_table(){
        $permi = DB::table('rrhh.rrhh_aport')
            ->join('rrhh.rrhh_var_aport', 'rrhh_var_aport.id_variable_aportacion', 'rrhh_aport.id_variable_aportacion')
            ->select('rrhh_aport.*', 'rrhh_var_aport.descripcion AS tipo_aporte')
            ->where('rrhh_aport.estado', '=', 1)
            ->orderBy('id_aportacion', 'asc')->get();
        $data = ["aporte"=>$permi];
        return response()->json($data);
    }
    public function mostrar_tipo_permiso(){
        $data = DB::table('rrhh.rrhh_tp_permi')->select('rrhh_tp_permi.id_tipo_permiso', 'rrhh_tp_permi.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_comision_salida(){
        $data = DB::table('rrhh.rrhh_var_comision')->select('rrhh_var_comision.id_variable_comision', 'rrhh_var_comision.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_planilla_table(){
        $plani = DB::table('rrhh.rrhh_pag_plani')
            ->join('rrhh.rrhh_tp_plani', 'rrhh_tp_plani.id_tipo_planilla', '=', 'rrhh_pag_plani.id_tipo_planilla')
            ->select('rrhh_pag_plani.*', 'rrhh_tp_plani.descripcion')
            ->orderBy('rrhh_pag_plani.id_pago_planilla', 'asc')->get();
        $data = ["plani"=>$plani];
        return response()->json($data);
    }
    public function mostrar_tipo_planilla(){
        $data = DB::table('rrhh.rrhh_tp_plani')->select('rrhh_tp_plani.id_tipo_planilla', 'rrhh_tp_plani.descripcion')->where('estado', '=', 1)
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_bonificacion(){
        $data = DB::table('rrhh.rrhh_var_bonif')->select('rrhh_var_bonif.id_variable_bonificacion', 'rrhh_var_bonif.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_descuento(){
        $data = DB::table('rrhh.rrhh_var_dscto')->select('rrhh_var_dscto.id_variable_descuento', 'rrhh_var_dscto.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_retencion(){
        $data = DB::table('rrhh.rrhh_var_reten')->select('rrhh_var_reten.id_variable_retencion', 'rrhh_var_reten.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_aporte(){
        $data = DB::table('rrhh.rrhh_var_aport')->select('rrhh_var_aport.id_variable_aportacion', 'rrhh_var_aport.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_sancion_table($id){
        $sanci = DB::table('rrhh.rrhh_sanci')
            ->join('rrhh.rrhh_var_sanci', 'rrhh_var_sanci.id_variable_sancion', '=', 'rrhh_sanci.id_variable_sancion')
            ->select('rrhh_sanci.*', 'rrhh_var_sanci.descripcion')
            ->where([
                ['rrhh_sanci.id_trabajador', '=', $id],
                ['rrhh_sanci.estado', '=', 1]
            ])
            ->orderBy('id_sancion', 'asc')->get();
        $data = ["sancion"=>$sanci];
        return response()->json($data);
    }
    public function mostrar_merito_table($id){
        $merito = DB::table('rrhh.rrhh_merito')
            ->join('rrhh.rrhh_var_merito', 'rrhh_var_merito.id_variable_merito', '=', 'rrhh_merito.id_variable_merito')
            ->select('rrhh_merito.*', 'rrhh_var_merito.descripcion')
            ->where([
                ['rrhh_merito.id_trabajador', '=', $id],
                ['rrhh_merito.estado', '=', 1]
            ])
            ->orderBy('id_merito', 'asc')->get();
        $data = ["merito"=>$merito];
        return response()->json($data);
    }
    public function mostrar_tipo_sancion(){
        $data = DB::table('rrhh.rrhh_var_sanci')->select('rrhh_var_sanci.id_variable_sancion', 'rrhh_var_sanci.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_tipo_merito(){
        $data = DB::table('rrhh.rrhh_var_merito')->select('rrhh_var_merito.id_variable_merito', 'rrhh_var_merito.descripcion')
                ->orderBy('descripcion', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_periodo_table(){
        $data = DB::table('rrhh.rrhh_asist')->select('rrhh_asist.*')
            ->orderBy('id_asistencia', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_periodo($id){
        $data = DB::table('rrhh.rrhh_asist')
                ->where([['id_asistencia', $id], ['estado', '=', 1]])->get();
        return response()->json($data);
    }
    public function mostrar_periodo_semana(){
        $data = DB::table('rrhh.rrhh_asist')->select('rrhh_asist.id_asistencia', 'rrhh_asist.descripcion')
            ->orderBy('id_asistencia', 'asc')->get();
        return response()->json($data);
    }
    public function mostrar_periodo_count($id){
        $periodo = DB::table('rrhh.rrhh_asist')
            ->where('rrhh_asist.id_tipo_asistencia', '=', $id)->count();
        return $periodo + 1;
    }
    public function mostrar_semana(){
        $data = DB::table('rrhh.rrhh_asist')->select('rrhh_asist.*')
            ->where('rrhh_asist.estado', '=', 1)->get();
        return response()->json($data);
    }
    public function mostrar_fechas_semana($id){
        $data = DB::table('rrhh.rrhh_asist')->select('rrhh_asist.fecha_inicio', 'rrhh_asist.fecha_fin')
            ->where('rrhh_asist.id_asistencia', $id)->get();
        return response()->json($data);
    }
    public function mostrar_criterio_table(){
        $crite = DB::table('rrhh.rrhh_criterio')->select('rrhh_criterio.*')
            ->where('rrhh_criterio.estado', 1)
            ->orderBy('id_criterio', 'asc')->get();
        $data = ["criterio"=>$crite];
        return response()->json($data);
    }
    public function mostrar_cargo_table(){
        $cargo = DB::table('rrhh.rrhh_cargo')->select('rrhh_cargo.*')
            ->where('rrhh_cargo.estado', 1)
            ->orderBy('descripcion', 'asc')->get();
        $data = ["cargo"=>$cargo];
        return response()->json($data);
    }
    public function mostrar_modalidad_table(){
        $modali = DB::table('rrhh.rrhh_modali')->where('estado', 1)->orderBy('id_modalidad', 'asc')->get();
        $output['data'] = $modali;
        return response()->json($output);
    }
    public function mostrar_tipo_contrato_table(){
        $tpcon = DB::table('rrhh.rrhh_tp_contra')->where('estado', 1)->orderBy('id_tipo_contrato', 'asc')->get();
        $output['data'] = $tpcon;
        return response()->json($output);
    }
    public function mostrar_traer_longitud($id){
        $data = DB::table('contabilidad.sis_identi')->select('longitud')
                ->where('id_doc_identidad', '=', $id)->get();
        return response()->json($data);
    }
    public function mostrar_estado_civil_table(){
        $civil = DB::table('rrhh.rrhh_est_civil')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $civil;
        return response()->json($output);
    }
    public function mostrar_nivel_estudio_table(){
        $nivel = DB::table('rrhh.rrhh_niv_estud')->where('estado', 1)->orderBy('id_nivel_estudio', 'asc')->get();
        $output['data'] = $nivel;
        return response()->json($output);
    }
    public function mostrar_carrera_table(){
        $carre = DB::table('rrhh.rrhh_carrera')->where('estado', 1)->orderBy('descripcion', 'asc')->get();
        $output['data'] = $carre;
        return response()->json($output);
    }
    public function mostrar_tipo_planilla_table(){
        $tplani = DB::table('rrhh.rrhh_tp_plani')->where('rrhh_tp_plani.estado', 1)->orderBy('id_tipo_planilla', 'asc')->get();
        $output['data'] = $tplani;
        return response()->json($output);
    }
    public function mostrar_tipo_trabajador_table(){
        $tptrab = DB::table('rrhh.rrhh_tp_trab')->select('rrhh_tp_trab.*')
            ->where('rrhh_tp_trab.estado', 1)
            ->orderBy('id_tipo_trabajador', 'asc')->get();
        $output['data']= $tptrab;
        return response()->json($output);
    }
    public function mostrar_categocupacion_table(){
        $categ = DB::table('rrhh.rrhh_cat_ocupac')->select('rrhh_cat_ocupac.*')
            ->where('rrhh_cat_ocupac.estado', 1)
            ->orderBy('id_categoria_ocupacional', 'asc')->get();
        $data = ["categoria_ocup"=>$categ];
        return response()->json($data);
    }
    public function mostrar_pensiones_table(){
        $pensi = DB::table('rrhh.rrhh_pensi')->select('rrhh_pensi.*')
            ->where('rrhh_pensi.estado', 1)
            ->orderBy('id_pension', 'asc')->get();
        $data = ["pensiones"=>$pensi];
        return response()->json($data);
    }
    public function mostrar_horarios_table(){
        $horario = DB::table('rrhh.rrhh_horario')->select('rrhh_horario.*')
            ->where('rrhh_horario.estado', 1)
            ->orderBy('id_horario', 'asc')->get();
            $output['data'] = $horario;
        return response()->json($output);
    }
    public function mostrar_concepto_rol_table(){
        $concepto = DB::table('rrhh.rrhh_rol_concepto')->where('estado', 1)->orderBy('id_rol_concepto', 'asc')->get();
        $output['data'] = $concepto;
        return response()->json($output);
    }
    public function mostrar_condiciondh_table(){
        $condh = DB::table('rrhh.rrhh_cdn_dhab')->where('rrhh_cdn_dhab.estado', 1)->orderBy('id_condicion_dh', 'asc')->get();
        $output['data'] = $condh;
        return response()->json($output);
    }
    public function mostrar_condiciondh_id($id){
        $data = DB::table('rrhh.rrhh_cdn_dhab')->where('id_condicion_dh', $id)->get();
        return response()->json($data);
    }
    public function mostrar_tpmeritos_table(){
        $tpme = DB::table('rrhh.rrhh_var_merito')->select('rrhh_var_merito.*')
            ->where('rrhh_var_merito.estado', 1)
            ->orderBy('id_variable_merito', 'asc')->get();
        $data = ["tipo_meritos"=>$tpme];
        return response()->json($data);
    }
    public function mostrar_tpdemeritos_table(){
        $sanci = DB::table('rrhh.rrhh_var_sanci')->select('rrhh_var_sanci.*')
            ->where('rrhh_var_sanci.estado', 1)
            ->orderBy('id_variable_sancion', 'asc')->get();
        $data = ["tipo_demeritos"=>$sanci];
        return response()->json($data);
    }
    public function mostrar_tpbonif_table(){
        $bonif = DB::table('rrhh.rrhh_var_bonif')->select('rrhh_var_bonif.*')
            ->where('rrhh_var_bonif.estado', 1)
            ->orderBy('id_variable_bonificacion', 'asc')->get();
        $data = ["tipo_bonificacion"=>$bonif];
        return response()->json($data);
    }
    public function mostrar_tpdescuento_table(){
        $dscto = DB::table('rrhh.rrhh_var_dscto')->select('rrhh_var_dscto.*')
            ->where('rrhh_var_dscto.estado', 1)
            ->orderBy('id_variable_descuento', 'asc')->get();
        $data = ["tipo_descuento"=>$dscto];
        return response()->json($data);
    }
    public function mostrar_tpretencion_table(){
        $reten = DB::table('rrhh.rrhh_var_reten')->select('rrhh_var_reten.*')
            ->where('rrhh_var_reten.estado', 1)
            ->orderBy('id_variable_retencion', 'asc')->get();
        $data = ["tipo_retencion"=>$reten];
        return response()->json($data);
    }
    public function mostrar_tpaporte_table(){
        $aport = DB::table('rrhh.rrhh_var_aport')->select('rrhh_var_aport.*')
            ->where('rrhh_var_aport.estado', 1)
            ->orderBy('id_variable_aportacion', 'asc')->get();
        $data = ["tipo_aporte"=>$aport];
        return response()->json($data);
    }
    public function consulta_sueldo($id, $cant){
        $sueldo = DB::table('rrhh.rrhh_cargo')->where('rrhh_cargo.id_cargo', '=', $id)->first();
        
        $min = $sueldo->sueldo_rango_minimo;
        $max = $sueldo->sueldo_rango_maximo;

        if ($cant >= $min && $cant <= $max){
            $data = 'Salario permitido dentro del rango ('.$min.' - '.$max.')';
        }else{
            $data = 'Salario fuera del rango ('.$min.' - '.$max.')';
        }
        return response()->json($data);
    }

    public function cargar_horario_reloj(){
        if (!empty($_FILES['archivo']['name'])){
			$handle = fopen($_FILES['archivo']['tmp_name'], 'r');

			if ($handle){
				while ($data = fgetcsv($handle, 4096, ";")){
					$string[] = array('name' => $data[1], 'fecha' => $data[3], 'dni' => $data[6], 'tipo' => $data[4]);
				}
                fclose($handle);

				foreach ($string as $row) {
                    $fecha = $row['fecha'];
                    $dni = $row['dni'];
                    $tipo = $row['tipo'];
                    $txtIn = '';

                    if ($tipo == 'C/In'){
                        $txtIn = 1;
                    }elseif ($tipo == 'OverTime Out'){
                        $txtIn = 2;
                    }elseif ($tipo == 'OverTime In'){
                        $txtIn = 3;
                    }elseif ($tipo == 'C/Out'){
                        $txtIn = 4;
                    }

                    // buscar ID_TRABAJADOR
                    $id_trab = $this->mostrar_trabajador_dni($dni);

                    if ($id_trab > 0) {
                        $date = substr($fecha, 0, 10);
                        $hour = substr($fecha, 11, 19);
                        $hora = date('H:i:s', strtotime($hour));

                        DB::table('rrhh.rrhh_reloj')->insertGetId(
                            [
                                'id_trabajador' =>$id_trab,
                                'fecha'         =>$date,
                                'horario'       =>$hora,
                                'tipo'          =>$txtIn                                
                            ],
                            'id_horario'
                        );
                    }
                }
                $rpta = 'ok';
			}else{
				$rpta = 'error';
			}
		}else{
			$rpta = 'null';
        }
        $array = array('status' => $rpta);
        echo json_encode($array);
    }

    public function cargar_horario_diario($fecha){
        $myfecha = date('Y-m-d', strtotime($fecha));
        $dia = $this->filtrar_dia($myfecha);

        $txt = '';
		$button = '';
		$hora = [];
		
        $verify = $this->verifyDataHorario($myfecha);
        
        if ($verify > 0){
            $sql = $this->searchPersonalData($myfecha);

			foreach ($sql as $row){
				$id_trab = $row['id_trabajador'];

				$er = $row->hora_ingreso;
				$sa = $row->hora_salida_almuerzo;
				$ea = $row->hora_entrada_almuerzo;
				$sr = $row->hora_salida;
				$ti = $row->minutos_tardanza;
				$ta = $row->minutos_tardanza_alm;

				$txt =
				'<tr>
					<td><input type="text" name="rrhh_persona[]" class="input-name" value="'.$id_trab.'" readonly></td>
					<td><input type="time" name="rrhh_ent_reg[]" value="'.$er.'" onchange="calcularDiario();" disabled></td>
					<td><input type="time" name="rrhh_sal_alm[]" value="'.$sa.'" onchange="calcularDiario();" disabled></td>
					<td><input type="time" name="rrhh_ent_alm[]" value="'.$ea.'" onchange="calcularDiario();" disabled></td>
					<td><input type="time" name="rrhh_sal_reg[]" value="'.$sr.'" onchange="calcularDiario();" disabled></td>
					<td><input type="text" name="rrhh_tar_ing[]" class="input-name" value="'.$ti.'" readonly></td>
					<td><input type="text" name="rrhh_tar_alm[]" class="input-name" value="'.$ta.'" readonly></td>
					<td></td>
				</tr>';
				$hora[$cont] = $txt;
				$cont++;
			}
        }else{
            $cont = 1;
			$sql = $this->mostrar_trabajador_horario();

			foreach ($sql as $row){
                $id_trab = $row->id_trabajador;
                $name = $row->nombres.' '.$row->apellido_paterno;

				$er = '00:00'; //entrada regular
				$sa = '00:00'; //salida al refrigerio
				$ea = '00:00'; //entrada del refrigerio
                $sr = '00:00'; //salida regular
                $hent = '00:00';

                $info = $this->searcHours($id_trab, $myfecha);
                $horaTrab = $this->searchTrabHour($id_trab);
                foreach ($horaTrab as $keyH){
                    $hent = $keyH->hora_ent_reg_sem;
                }
				foreach ($info as $value){
                    $tipo = $value->tipo;
					$hour = date('H:i', strtotime($value->horario));

                    if ($tipo == 1){
                        $er = $hour;
                    }elseif ($tipo == 2){
                        $sa = $hour;
                    }elseif ($tipo == 3){
                        $ea = $hour;
                    }elseif ($tipo == 4){
                        $sr = $hour;
                    }
				}

				$txt =
				'<tr>
					<td><input type="text" name="rrhh_persona[]" class="input-name" value="'.$name.'" readonly></td>
					<td><input type="time" name="rrhh_ent_reg[]" value="'.$er.'" onchange="calcularDiario();"></td>
					<td><input type="time" name="rrhh_sal_alm[]" value="'.$hent.'" onchange="calcularDiario();"></td>
					<td><input type="time" name="rrhh_ent_alm[]" value="'.$ea.'" onchange="calcularDiario();"></td>
					<td><input type="time" name="rrhh_sal_reg[]" value="'.$sr.'" onchange="calcularDiario();"></td>
					<td><input type="text" name="rrhh_tar_ing[]" class="input-name" value="" readonly></td>
					<td><input type="text" name="rrhh_tar_alm[]" class="input-name" value="" readonly></td>
					<td></td>
				</tr>';
				$hora[$cont] = $txt;
				$cont++;
			}
			$button = '<button class="btn btn-warning" onclick="Recargar();">Actualizar Horarios</button>';      
        }
        $myArray = array('hora' => $hora,
						 'dia' => $dia,
						 'button' => $button);
		
		echo json_encode($myArray);
    }

    public function mostrar_trabajador_horario(){
        $data = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', '=', 'rrhh_trab.id_tipo_trabajador')
            ->select('rrhh_trab.id_trabajador', 'rrhh_postu.direccion', 'rrhh_postu.telefono', 'rrhh_perso.nombres','rrhh_perso.apellido_paterno','rrhh_perso.apellido_materno', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_perso.nro_documento')
            ->orderBy('id_trabajador','asc')->get();
        return $data;
    }

    public function mostrar_tolerancias_table(){
        $toler = DB::table('rrhh.rrhh_tolerancia')
            ->where('estado', 1)
            ->orderBy('id_tolerancia', 'asc')->get();
            $output['data'] = $toler;
        return response()->json($output);
    }

    public function verifyDataHorario($fecha){
		$sql = DB::table('rrhh.rrhh_asi_diaria')->where('fecha_asistencia', '=', $fecha)->get();
		return $sql->count();
    }
    
    public function searchPersonalData($fecha){
        $sql = DB::table('rrhh.rrhh_asi_diaria')->where('fecha_asistencia', '=', $fecha)->orderBy('id_trabajador', 'asc')->get();
		return $sql;
    }
    
    function searcHours($id, $fecha){
        $sql = DB::table('rrhh.rrhh_reloj')->where([['id_trabajador', '=', $id],['fecha', '=', $fecha]])->orderBy('id_trabajador', 'asc')->get();
		return $sql;
    }
    
    function searchTrabHour($id){
        $sql = DB::table('rrhh.rrhh_contra')
                ->join('rrhh.rrhh_horario', 'rrhh_horario.id_horario', '=', 'rrhh_contra.id_horario')
                ->where('id_trabajador', '=', $id)
                ->orderBy('id_trabajador', 'asc')->get();
		return $sql;
	}

    // GUARDAR
    public function guardar_persona(Request $request){
        $sql = DB::table('rrhh.rrhh_perso')->where('nro_documento', '=', $request->nro_documento)->get();
        $fecha_registro = date('Y-m-d H:i:s');

        if ($sql->count() > 0){
            $id = 'exist';
        }else{
            $id = DB::table('rrhh.rrhh_perso')->insertGetId(
                [
                    'id_documento_identidad'    => $request->id_documento_identidad,
                    'nro_documento'             => $request->nro_documento,
                    'nombres'                   => strtoupper($request->nombres),
                    'apellido_paterno'          => strtoupper($request->apellido_paterno),
                    'apellido_materno'          => strtoupper($request->apellido_materno),
                    'fecha_nacimiento'          => $request->fecha_nacimiento,
                    'sexo'                      => $request->sexo,
                    'id_estado_civil'           => $request->id_estado_civil,
                    'estado'                    =>1,
                    'fecha_registro'            => $fecha_registro,
                ],
                'id_persona'
            );
        }
        return response()->json($id);
    }
    public function guardar_seleccion(Request $request){
        $id = DB::table('rrhh.rrhh_selec')->insertGetId(
            [
                'id_empresa'       =>$request->id_empresa,
                'id_cargo'         =>$request->id_cargo,
                'requisitos'       =>$request->requisitos,
                'perfil'           =>$request->perfil,
                'lugar'            =>$request->lugar,
                'cantidad'         =>$request->cantidad,
                'fecha_inicio'     =>$request->fecha_inicio,
                'fecha_fin'        =>$request->fecha_fin,
                'estado'           => 1,
                'fecha_registro'    =>$request->fecha_registro,
            ],
            'id_seleccion'
        );
        return response()->json($id);
    }
    public function guardar_alta_trabajador(Request $request){
        $sql = DB::table('rrhh.rrhh_trab')
            ->where('rrhh_trab.id_postulante', '=', $request->id_postulante)->get();

        if ($sql->count() > 0){
            $id = 'exist';
        }else{
            $id = DB::table('rrhh.rrhh_trab')->insertGetId(
                [
                    'id_postulante'             => $request->id_postulante,
                    'id_tipo_planilla'          => $request->id_tipo_planilla,
                    'id_tipo_trabajador'        => $request->id_tipo_trabajador,
                    'id_categoria_ocupacional'  => $request->id_categoria_ocupacional,
                    'condicion'                 => $request->condicion,
                    'hijos'                     => $request->hijos,
                    'id_pension'                => $request->id_pension,
                    'cuspp'                     => $request->cuspp,
                    'seguro'                    => $request->seguro,
                    'confianza'                 => $request->confianza,
                    // 'archivo_adjunto'        => $request->archivo_adjunto,
                    'estado'                    => 1,
                    'fecha_registro'            => $request->fecha_registro
                ],
                'id_trabajador'
            );
        }
        return response()->json($id);
    }
    public function guardar_contrato_trabajador(Request $request){
        $id = DB::table('rrhh.rrhh_contra')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_tipo_contrato'      => $request->id_tipo_contrato,
                'id_modalidad'          => $request->id_modalidad,
                'id_horario'            => $request->id_horario,
                'id_centro_costo'       => $request->id_centro_costo,
                'tipo_centro_costo'     => $request->tipo_centro_costo,
                'fecha_inicio'          => $request->fecha_inicio,
                'fecha_fin'             => $request->fecha_fin,
                'motivo'                => $request->motivo,
                // 'archivo_adjunto'    => $request->archivo_adjunto,
                'estado'                => 1,
                'fecha_registro'        => $request->fecha_registro
            ],
            'id_contrato'
        );
        return response()->json($id);
    }
    public function guardar_rol_trabajador(Request $request){
        $id = DB::table('rrhh.rrhh_rol')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_area'               => $request->id_area,
                'id_cargo'              => $request->id_cargo,
                'id_rol_concepto'       => $request->id_rol_concepto,
                'salario'               => $request->salario,
                'responsabilidad'       => $request->responsabilidad,
                'id_grupo'              => $request->id_grupo,
                'id_proyecto'           => $request->id_proyecto,
                'sctr'                  => $request->sctr,
                'fecha_inicio'          => $request->fecha_ingreso,
                'fecha_fin'             => $request->fecha_cese,
                'estado'                => 1,
                'fecha_registro'        => $request->fecha_registro
            ],
            'id_rol'
        );
        return response()->json($id);
    }
    public function guardar_cuenta_trabajador(Request $request){
        $id = DB::table('rrhh.rrhh_cta_banc')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_banco'              => $request->id_banco,
                'id_tipo_cuenta'        => $request->id_tipo_cuenta,
                'nro_cci'               => $request->nro_cci,
                'nro_cuenta'            => $request->nro_cuenta,
                'id_moneda'             => $request->id_moneda,
                'estado'                => 1,
                'fecha_registro'        => $request->fecha_registro
            ],
            'id_cuenta_bancaria'
        );
        return response()->json($id);
    }
    public function guardar_informacion_postulante(Request $request){
        $sql = DB::table('rrhh.rrhh_postu')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', 'rrhh_postu.id_persona')
                ->where('rrhh_perso.nro_documento', '=', $request->nro_documento)->get();

        if ($sql->count() > 0){
            $id = 'exist';
        }else{
            $id = DB::table('rrhh.rrhh_postu')->insertGetId(
                [
                    'id_persona'        => $request->id_persona,
                    'direccion'         => strtoupper($request->direccion),
                    'telefono'          => $request->telefono,
                    'correo'            => $request->correo,
                    'brevette'          => $request->brevette,
                    'id_pais'           => $request->id_pais,
                    'ubigeo'            => $request->ubigeo,
                    'fecha_registro'    => $request->fecha_registro
                ],
                'id_postulante'
            );
        }
        return response()->json($id);
    }
    public function guardar_formacion_postulante(Request $request){
        $id = DB::table('rrhh.rrhh_frm_acad')->insertGetId(
            [
                'id_postulante'        => $request->id_postulante,
                'id_nivel_estudio'     => $request->id_nivel_estudio,
                'id_carrera'           => $request->id_carrera,
                'fecha_inicio'         => $request->fecha_inicio,
                'fecha_fin'            => $request->fecha_fin,
                'nombre_institucion'   => $request->nombre_institucion,
                'id_pais'              => $request->id_pais,
                'ubigeo'               => $request->ubigeo,
                'estado'               => 1,
                'fecha_registro'       => $request->fecha_registro
            ],
            'id_formacion'
        );
        return response()->json($id);
    }
    public function guardar_dextra_postulante(Request $request){
        $file_foto = $request->file('foto_perfil');
        $file_fapo = $request->file('antecedentes_policiales');
        $file_fape = $request->file('antecedentes_penales');
        $file_fcvs = $request->file('file_cv');

        if(isset($file_foto)){
            $foto = time().$file_foto->getClientOriginalName();
            Storage::disk('archivos')->put('fotos_postulantes/'.$foto, \File::get($file_foto));
        }else{
            $foto = null;
        }
        if(isset($file_fapo)){
            $ant_poli = time().$file_fapo->getClientOriginalName();
            Storage::disk('archivos')->put('antec_policiales/'.$ant_poli, \File::get($file_fapo));
        }else{
            $ant_poli = null;
        }
        if(isset($file_fape)){
            $ant_pena = time().$file_fape->getClientOriginalName();
            Storage::disk('archivos')->put('antec_penales/'.$ant_pena, \File::get($file_fape));
        }else{
            $ant_pena = null;
        }
        if(isset($file_fcvs)){
            $cv = time().$file_fcvs->getClientOriginalName();
            Storage::disk('archivos')->put('cv/'.$cv, \File::get($file_fcvs));
        }else{
            $cv = null;
        }

        $id = DB::table('rrhh.rrhh_dts_extra')->insertGetId(
            [
                'id_postulante'             => $request->id_postulante,
                'foto_perfil'               => $foto,
                'antecedentes_policiales'   => $ant_poli,
                'antecedentes_penales'      => $ant_pena,
                'curriculum_vitae'          => $cv,
                'fecha_registro'            => $request->fecha_registro
            ],
            'id_datos_extras'
        );
        return response()->json($id);
    }
    public function guardar_observacion_postulante(Request $request){
        $id = DB::table('rrhh.rrhh_obs_postu')->insertGetId(
            [
                'id_postulante'             => $request->id_postulante,
                'observacion'               => $request->observacion,
                'id_usuario'                => $request->id_usuario,
                'estado'                    => 1,
                'fecha_registro'            => $request->fecha_registro
            ],
            'id_observacion'
        );
        return response()->json($id);
    }
    public function guardar_experiencia_postulante(Request $request){
        $id = DB::table('rrhh.rrhh_exp_labo')->insertGetId(
            [
                'id_postulante'             => $request->id_postulante,
                'nombre_empresa'            => $request->nombre_empresa,
                'cargo_ocupado'             => $request->cargo_ocupado,
                'datos_contacto'            => $request->datos_contacto,
                'telefono_contacto'         => $request->telefono_contacto,
                'relacion_trab_contacto'    => $request->relacion_trab_contacto,
                'funciones'                 => $request->funciones,
                'fecha_ingreso'             => $request->fecha_ingreso,
                'fecha_cese'                => $request->fecha_cese,
                'estado'                    => 1,
                'fecha_registro'            => $request->fecha_registro
            ],
            'id_experiencia_laboral'
        );
        return response()->json($id);
    }
    public function guardar_derecho_habiente(Request $request){
        $id = DB::table('rrhh.rrhh_der_hab')->insertGetId(
            [
                'id_trabajador'      => $request->id_trabajador,
                'id_persona'         => $request->id_persona,
                'id_condicion_dh'    => $request->id_condicion_dh,
                'estado'             => 1,
                'fecha_registro'     => $request->fecha_registro
            ],
            'id_derecho_habiente'
        );
        return response()->json($id);
    }
    public function guardar_prestamo(Request $request){
        $id = DB::table('rrhh.rrhh_presta')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'concepto'          => $request->concepto,
                'fecha_prestamo'    => $request->fecha_prestamo,
                'nro_cuotas'        => $request->nro_cuotas,
                'monto_prestamo'    => $request->monto_prestamo,
                'porcentaje'        => $request->porcentaje,
                'estado'            => 1,
                'fecha_registro'    => $request->fecha_registro
            ],
            'id_prestamo'
        );
        return response()->json($id);
    }

    public function guardar_periodo(Request $request){
        if ($request->id_tipo_asistencia == 1) {
            $val = 'SEMANA ';
            $num = $this->mostrar_periodo_count($request->id_tipo_asistencia);
            $desc = $val.$num;
            $fecha_registro = date('Y-m-d H:i:s');
        }
        $id = DB::table('rrhh.rrhh_asist')->insertGetId(
            [
                'id_tipo_asistencia'  => $request->id_tipo_asistencia,
                'descripcion'         => $desc,
                'fecha_inicio'        => $request->fecha_inicio,
                'fecha_fin'           => $request->fecha_fin,
                'estado'              => 1,
                'fecha_registro'      => $fecha_registro
            ],
            'id_asistencia'
        );
        return response()->json($id);
    }
    public function guardar_permiso(Request $request){
        $id = DB::table('rrhh.rrhh_permi')->insertGetId(
            [
                'id_trabajador'          => $request->id_trabajador,
                'id_tipo_permiso'        => $request->id_tipo_permiso,
                'motivo'                 => $request->motivo,
                'fecha_inicio_permiso'   => $request->fecha_inicio_permiso,
                'fecha_fin_permiso'      => $request->fecha_fin_permiso,
                'hora_inicio'            => $request->hora_inicio,
                'hora_fin'               => $request->hora_fin,
                'id_trabajador_autoriza' => $request->id_trabajador_autoriza,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'                 => 1,
                'fecha_registro'         => $request->fecha_registro
            ],
            'id_permiso'
        );
        return response()->json($id);
    }
    public function guardar_comision_salida(Request $request){
        $id = DB::table('rrhh.rrhh_com_salida')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_variable_comision'      => $request->id_tipo_comision,
                'motivo'                => $request->motivo,
                'fecha_inicio_comision'  => $request->fecha_inicio_comision,
                'fecha_fin_comision'     => $request->fecha_fin_comision,
                'hora_inicio'           => $request->hora_inicio,
                'hora_fin'              => $request->hora_fin,
                'id_trabajador_autoriza' => $request->id_trabajador_autoriza,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'                => 1,
                'fecha_registro'        => $request->fecha_registro
            ],
            'id_comision_salida'
        );
        return response()->json($id);
    }
    public function guardar_horas_extras(Request $request){
        $id = DB::table('rrhh.rrhh_hrs_extra')->insertGetId(
            [
                'id_trabajador'          => $request->id_trabajador,
                'total_horas'            => $request->total_horas,
                'motivo'                 => $request->motivo,
                'fecha_hora_extra'       => $request->fecha_hora_extra,
                'id_trabajador_autoriza' => $request->id_trabajador_autoriza,
                'estado'                 => 1,
                'fecha_registro'         => $request->fecha_registro
            ],
            'id_hora_extra'
        );
        return response()->json($id);
    }
    public function guardar_reintegro(Request $request){
        $id = DB::table('rrhh.rrhh_reintegro')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'fecha'             => $request->fecha,
                'importe'           => $request->monto,
                'concepto'          => $request->concepto,
                'estado'            => 1,
                'fecha_registro'    => $request->fecha_registro
            ],
            'id_reintegro'
        );
        return response()->json($id);
    }
    public function guardar_tareo(Request $request){
        $id_asistencia = $request->id_asistencia;
        $id_trab = $request->id_trabajador;
        $fecha_asistencia = $request->fecha_asistencia;
        $tipo_ingreso = $request->tipo_ingreso;
        $tipo_horario = $request->tipo_horario;
        $hora_tareo = $request->ht;
        $fecha_registro = $request->fecha_registro;
        $tp_trab = $request->id_tipo_trabajador;

        $count = count($id_trab);
        $dia = $this->filtrar_dia($fecha_asistencia);

        $sqlTolera = DB::table('rrhh.rrhh_var_tolerancia')->where('estado', 1)->first();
        $tolerancia = (float) $sqlTolera->valor;
        
        for ($i = 0; $i < $count; $i++){
            $id_trabajador = $id_trab[$i];
            $hour_reloj = $hora_tareo[$i];

            if ($tipo_ingreso == 1){
                if ($tipo_horario == 1){
                    DB::table('rrhh.rrhh_asi_diaria')->insert(
                        [
                            'id_asistencia'         =>$id_asistencia,
                            'id_tipo_trabajador'    =>$tp_trab,
                            'id_trabajador'         =>$id_trabajador,
                            'fecha_asistencia'      =>$fecha_asistencia,
                            'hora_entrada'          =>$hour_reloj,
                            'fecha_registro'        =>$fecha_registro
                        ]
                    );
                }else{
                    $ent_alm = DB::table('rrhh.rrhh_asi_diaria')->select('id_asistencia_diaria')
                    ->where([['id_asistencia', $id_asistencia], ['id_trabajador', $id_trabajador], ['fecha_asistencia', $fecha_asistencia]])->first();
                    $id_tareo = $ent_alm->id_asistencia_diaria;

                    DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_tareo)->update([
                        'hora_entrada_almuerzo' => $hour_reloj
                    ]);
                }
            }else{
                if ($tipo_horario == 1){
                    $sali_reg = DB::table('rrhh.rrhh_asi_diaria')->select('id_asistencia_diaria', 'hora_entrada', 'hora_salida_almuerzo', 'hora_entrada_almuerzo')
                    ->where([['id_asistencia', $id_asistencia], ['id_trabajador', $id_trabajador], ['fecha_asistencia', $fecha_asistencia]])->first();

                    $id_tareo = $sali_reg->id_asistencia_diaria;
                    $entradaHT = ($sali_reg->hora_entrada != '') ? $sali_reg->hora_entrada : 0 ;
                    $salidaHA = ($sali_reg->hora_salida_almuerzo != '') ? $sali_reg->hora_salida_almuerzo : 0 ;
                    $entradaHA = ($sali_reg->hora_entrada_almuerzo != '') ? $sali_reg->hora_entrada_almuerzo : 0 ;

                    DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_tareo)->update([
                        'hora_salida'   => $hour_reloj
                    ]);

                    $hour_real_almuerzo_sal = '13:00';
                    $hour_real_almuerzo_ent = '14:00';

                    if ($dia == 6){
                        if ($entradaHT > 0) {
                            $hour_real_ingreso = '09:00';
                            $hour_real_salida = '12:00';
                        }
                        
                        $ht1 = $this->restar_horas($hour_real_ingreso, $hour_real_salida);
                        $hora1 = $this->convertHD($ht1);
                        $hora_trabajada_cal = $hora1;

                        $hour_ing = $this->newTime($entradaHT, $tolerancia, 'min');
                        
                        if ($hour_ing > $hour_real_ingreso){
                            $tardanza_entrada = $this->restar_horas($hour_real_ingreso, $entradaHT);
                        }else{
                            $tardanza_entrada = 0;
                        }

                        $minTardanza1 = substr($tardanza_entrada, 3, 2);

                        DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_tareo)
                        ->update([
                            'hora_trabajada'    => $hora_trabajada_cal,
                            'minutos_tardanza'  => $minTardanza1
                        ]);
                    }else{
                        if ($entradaHT > 0 || $salidaHA > 0 || $entradaHA > 0){
                            $hour_real_ingreso = '08:30';
                            $hour_real_salida = '18:30';

                            $ht1 = $this->restar_horas($hour_real_ingreso, $hour_real_almuerzo_sal);
                            $ht2 = $this->restar_horas($hour_real_almuerzo_ent, $hour_real_salida);
                            $hora1 = $this->convertHD($ht1);
                            $hora2 = $this->convertHD($ht2);
                            $hora_trab = $hora1 + $hora2;
                            $hora_trabajada_cal = $hora_trab;
    
                            $hour_ing = $this->newTime($entradaHT, $tolerancia, 'min');
                            $hour_alm = $this->newTime($entradaHA, $tolerancia, 'min');
                            
                            if ($hour_ing > $hour_real_ingreso){
                                $tardanza_entrada = $this->restar_horas($hour_real_ingreso, $entradaHT);
                            }else{
                                $tardanza_entrada = 0;
                            }
    
                            if ($hour_alm > $hour_real_almuerzo_ent) {
                                $ht_alm = $this->restar_horas($salidaHA, $hour_alm);
                                $horaAlm = $this->convertHD($ht_alm);
                                if ($horaAlm > 1) {
                                    $one_hour = '01:00';
                                    $new_hor = $this->sumOneHour($salidaHA, 1, 'hour');
                                    $tardanza_almuerzo = $this->restar_horas($new_hor, $entradaHA);
                                }else{
                                    $tardanza_almuerzo = 0;
                                }
                            }else{
                                $tardanza_almuerzo = 0;
                            }
    
                            $minTardanza1 = substr($tardanza_entrada, 3, 2);
                            $minTardanza2 = substr($tardanza_almuerzo, 3, 2);
    
                            DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_tareo)
                            ->update([
                                'hora_trabajada'        => $hora_trabajada_cal,
                                'minutos_tardanza'      => $minTardanza1,
                                'minutos_tardanza_alm'  => $minTardanza2
                            ]);
                        }
                    }
                }else{
                    $sali_alm = DB::table('rrhh.rrhh_asi_diaria')->select('id_asistencia_diaria')
                    ->where([['id_asistencia', $id_asistencia], ['id_trabajador', $id_trabajador], ['fecha_asistencia', $fecha_asistencia]])
                    ->get();
    
                    foreach($sali_alm as $value){
                        $id_value = $value->id_asistencia_diaria;
                    }
                    $id_tareo = $id_value;
                    DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_tareo)
                    ->update([
                        'hora_salida_almuerzo'  => $hour_reloj
                    ]);
                }
            }
        }
    }
    public function guardar_criterio(Request $request){
        $id = DB::table('rrhh.rrhh_criterio')->insertGetId(
            [
                'descripcion'         => $request->descripcion,
                'tipo_evaluacion'     => $request->tipo_evaluacion,
                'estado'              => 1,
                'fecha_registro'      => $request->fecha_registro
            ],
            'id_criterio'
        );
        return response()->json($id);
    }
    public function guardar_vacaciones(Request $request){
        $nro_dias = $this->numero_dias($request->fecha_inicio, $request->fecha_fin);
        $id = DB::table('rrhh.rrhh_vacac')->insertGetId(
            [
                'id_trabajador'      => $request->id_trabajador,
                'fecha_inicio'       => $request->fecha_inicio,
                'fecha_fin'          => $request->fecha_fin,
                'nro_dias'           => $nro_dias,
                'dias_efectuados'    => $request->nro_dias_efectuados,
                'concepto'           => $request->concepto,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'             => 1,
                'fecha_registro'     => $request->fecha_registro
            ],
            'id_vacaciones'
        );
        return response()->json($id);
    }
    public function guardar_cargo(Request $request){
        $sql = DB::table('rrhh.rrhh_cargo')->get();
        $total = $sql->count() + 1;

        $code = $this->leftZero(5, $total);
        $codigo = 'C'.$code;
        
        $id = DB::table('rrhh.rrhh_cargo')->insertGetId(
            [
                'id_empresa'            => $request->id_empresa,
                'codigo'                => $codigo,
                'descripcion'           => $request->descripcion,
                'sueldo_rango_minimo'   => $request->sueldo_rango_minimo,
                'sueldo_rango_maximo'   => $request->sueldo_rango_maximo,
                'estado'                => 1,
                'fecha_registro'        => $request->fecha_registro
            ],
            'id_cargo'
        );
        return response()->json($id);
    }
    public function guardar_modalidad(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_modali')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'dias_trabajo'      => $request->dias_trabajo,
                'dias_descanso'     => $request->dias_descanso,
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_modalidad'
        );
        return response()->json($id);
    }
    public function guardar_tipo_contrato(Request $request){
        $id = DB::table('rrhh.rrhh_tp_contra')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_tipo_contrato'
        );
        return response()->json($id);
    }
    public function guardar_estado_civil(Request $request){
        $id = DB::table('rrhh.rrhh_est_civil')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_estado_civil'
        );
        return response()->json($id);
    }
    public function guardar_nivel_estudio(Request $request){
        $id = DB::table('rrhh.rrhh_niv_estud')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_nivel_estudio'
        );
        return response()->json($id);
    }
    public function guardar_tipo_planilla(Request $request){
        $id = DB::table('rrhh.rrhh_tp_plani')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_tipo_planilla'
        );
        return response()->json($id);
    }
    public function guardar_tipo_trabajador(Request $request){
        $id = DB::table('rrhh.rrhh_tp_trab')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1
            ],
            'id_tipo_trabajador'
        );
        return response()->json($id);
    }
    public function guardar_categoria_ocupacional(Request $request){
        $id = DB::table('rrhh.rrhh_cat_ocupac')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_categoria_ocupacional'
        );
        return response()->json($id);
    }
    public function guardar_fondo_pension(Request $request){
        $id = DB::table('rrhh.rrhh_pensi')->insertGetId(
            [
                'descripcion'           => $request->descripcion,
                'porcentaje_general'    => $request->porcentaje_general,
                'aporte'                => $request->aporte,
                'prima_seguro'          => $request->prima_seguro,
                'comision'              => $request->comision,
                'estado'                => 1,
                'fecha_registro'        => $request->fecha_registro
            ],
            'id_pension'
        );
        return response()->json($id);
    }
    public function guardar_horario(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_horario')->insertGetId(
            [
                'descripcion'      => strtoupper($request->descripcion),
                'hora_ent_reg_sem' => $request->hora_ini_reg,
                'hora_sal_reg_sem' => $request->hora_fin_reg,
                'hora_sal_alm_sem' => $request->hora_ini_alm,
                'hora_ent_alm_sem' => $request->hora_fin_alm,
                'hora_ent_reg_sab' => $request->hora_ini_sab,
                'hora_sal_reg_sab' => $request->hora_fin_sab,
                'estado'           => 1,
                'fecha_registro'   => $fecha_registro
            ],
            'id_horario'
        );
        return response()->json($id);
    }
    public function guardar_concepto_rol(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $id = DB::table('rrhh.rrhh_rol_concepto')->insertGetId(
            [
                'descripcion'       => strtoupper($request->descripcion),
                'estado'            => 1,
                'fecha_registro'    => $fecha_registro
            ],
            'id_rol_concepto'
        );
        return response()->json($id);
    }
    public function guardar_condicion_dh(Request $request){
        $id = DB::table('rrhh.rrhh_cdn_dhab')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_condicion_dh'
        );
        return response()->json($id);
    }
    public function guardar_tipo_merito(Request $request){
        $id = DB::table('rrhh.rrhh_var_merito')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_variable_merito'
        );
        return response()->json($id);
    }
    public function guardar_tipo_demerito(Request $request){
        $id = DB::table('rrhh.rrhh_var_sanci')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_variable_sancion'
        );
        return response()->json($id);
    }
    public function guardar_tipo_bonificacion(Request $request){
        $id = DB::table('rrhh.rrhh_var_bonif')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_variable_bonificacion'
        );
        return response()->json($id);
    }
    public function guardar_tipo_descuento(Request $request){
        $id = DB::table('rrhh.rrhh_var_dscto')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_variable_descuento'
        );
        return response()->json($id);
    }
    public function guardar_tipo_retencion(Request $request){
        $id = DB::table('rrhh.rrhh_var_reten')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_variable_retencion'
        );
        return response()->json($id);
    }
    public function guardar_tipo_aporte(Request $request){
        $id = DB::table('rrhh.rrhh_var_aport')->insertGetId(
            [
                'descripcion'       => $request->descripcion,
                'estado'            => 1
            ],
            'id_variable_aportacion'
        );
        return response()->json($id);
    }
    public function guardar_tolerancia(Request $request){
        $id = DB::table('rrhh.rrhh_tolerancia')->insertGetId(
            [
                'tiempo'    => $request->tiempo,
                'periodo'   => $request->periodo,
                'estado'    =>1
            ],
            'id_tolerancia'
        );
        return response()->json($id);
    }
    public function guardar_carrera(Request $request){
        $id = DB::table('rrhh.rrhh_carrera')->insertGetId(
            [
                'descripcion'   => strtoupper($request->descripcion),
                'estado'        => 1
            ],
            'id_carrera'
        );
        return response()->json($id);
    }
    public function guardar_sancion(Request $request){
        $id = DB::table('rrhh.rrhh_sanci')->insertGetId(
            [
                'id_trabajador'         => $request->id_trabajador,
                'id_variable_sancion'   => $request->id_variable_sancion,
                'concepto'              => $request->concepto,
                'motivo'                => $request->motivo,
                'fecha_sancion'         => $request->fecha_sancion,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'            => 1,
                'fecha_registro'    => $request->fecha_registro
            ],
            'id_sancion'
        );
        return response()->json($id);
    }
    public function guardar_merito(Request $request){
        $id = DB::table('rrhh.rrhh_merito')->insertGetId(
            [
                'id_trabajador'        => $request->id_trabajador,
                'id_variable_merito'   => $request->id_variable_merito,
                'concepto'             => $request->concepto,
                'motivo'               => $request->motivo,
                'fecha_merito'         => $request->fecha_merito,
                // 'archivo_adjunto'   => $request->archivo_adjunto,
                'estado'            => 1,
                'fecha_registro'    => $request->fecha_registro
            ],
            'id_merito'
        );
        return response()->json($id);
    }
    public function guardar_planilla(Request $request){
        $id = DB::table('rrhh.rrhh_pag_plani')->insertGetId(
            [
                'id_empresa'        => $request->id_empresa,
                'id_asistencia'     => $request->id_asistencia,
                'id_tipo_planilla'  => $request->id_tipo_planilla,
                'estado'            => 1,
                'fecha_registro'    => $request->fecha_registro
            ],
            'id_pago_planilla'
        );
        return response()->json($id);
    }
    public function guardar_precalculo(Request $request){
        $asistencia = $request->id_asistencia;
        $id_trab = $request->id_trabajador;
        $lun = $request->lun;
        $mar = $request->mar;
        $mie = $request->mie;
        $jue = $request->jue;
        $vie = $request->vie;
        $sab = $request->sab;
        $hoy = $request->fecha_registro;
        $asist = DB::table('rrhh.rrhh_asist')->where('id_asistencia', $asistencia)->get();

        foreach ($asist as $row){
            $ini = strtotime($row->fecha_inicio);
            $fin = strtotime($row->fecha_fin);
        }

        $count = count($id_trab);
        
        for ($i = 0; $i < $count; $i++){
            $id_trabajador = $id_trab[$i];
            $cont_dia = 1;
            
            for ($j = $ini; $j < $fin ; $j+= 86400){ 
                $dia = date("Y-m-d", $j);
                
                if ($cont_dia == 1){
                    $hora = $lun[$i];
                }elseif($cont_dia == 2){
                    $hora = $mar[$i];
                }elseif($cont_dia == 3){
                    $hora = $mie[$i];
                }elseif($cont_dia == 4){
                    $hora = $jue[$i];
                }elseif($cont_dia == 5){
                    $hora = $vie[$i];
                }elseif($cont_dia == 6){
                    $hora = $sab[$i];
                }

                DB::table('rrhh.rrhh_pre_calculo')->insert(
                    [
                        'id_asistencia'         =>$asistencia,
                        'id_trabajador'         =>$id_trabajador,
                        'fecha'                 =>$dia,
                        'hora'                  =>$hora,
                        'estado'                => 1,
                        'fecha_registro'        =>$hoy
                    ]
                );
                $cont_dia += 1;
            }
        }
        return 1;
    }
    public function guardar_bonificacion(Request $request){
        $id = DB::table('rrhh.rrhh_bonif')->insertGetId(
            [
                'id_trabajador'             =>$request->id_trabajador,
                'id_variable_bonificacion'  =>$request->id_variable_bonificacion,
                'afecto'                    =>$request->afecto,
                'concepto'                  =>$request->concepto,
                'importe'                   =>$request->importe,
                'fecha_bonificacion'        =>$request->fecha_bonificacion,
                'estado'                    =>1,
                'fecha_registro'            =>$request->fecha_registro,
            ],
            'id_bonificacion'
        );
        return response()->json($id);
    }
    public function guardar_descuento(Request $request){
        $id = DB::table('rrhh.rrhh_dscto')->insertGetId(
            [
                'id_trabajador'             =>$request->id_trabajador,
                'id_variable_descuento'     =>$request->id_variable_descuento,
                'afecto'                    =>$request->afecto,
                'concepto'                  =>$request->concepto,
                'importe'                   =>$request->importe,
                'fecha_descuento'           =>$request->fecha_descuento,
                'estado'                    =>1,
                'fecha_registro'            =>$request->fecha_registro,
            ],
            'id_descuento'
        );
        return response()->json($id);
    }
    public function guardar_retencion(Request $request){
        $id = DB::table('rrhh.rrhh_retencion')->insertGetId(
            [
                'id_trabajador'             =>$request->id_trabajador,
                'id_variable_retencion'     =>$request->id_variable_retencion,
                'afecto'                    =>$request->afecto,
                'concepto'                  =>$request->concepto,
                'importe'                   =>$request->importe,
                'fecha_retencion'           =>$request->fecha_retencion,
                'estado'                    =>1,
                'fecha_registro'            =>$request->fecha_registro,
            ],
            'id_retencion'
        );
        return response()->json($id);
    }
    public function guardar_aporte(Request $request){
        $id = DB::table('rrhh.rrhh_aport')->insertGetId(
            [
                'id_variable_aportacion'    =>$request->id_variable_aportacion,
                'concepto'                  =>$request->concepto,
                'valor'                     =>$request->valor,
                'estado'                    =>1,
                'fecha_registro'            =>$request->fecha_registro,
            ],
            'id_aportacion'
        );
        return response()->json($id);
    }
    


    // BUSCAR
    public function buscar_persona($id){
        $data = DB::table('rrhh.rrhh_perso')
            ->select('rrhh_perso.*')
            ->where('rrhh_perso.nro_documento', $id)->get();
        if($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_persona' => 0];
            return response()->json($data);
        }
    }
    public function buscar_persona_dh($id){
        $data = DB::table('rrhh.rrhh_perso')
                        ->select('rrhh_perso.id_persona',
                                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_persona"))
                        ->where('rrhh_perso.nro_documento', $id)->get();
        if($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_persona' => 0];
            return response()->json($data);
        }
    }
    public function buscar_postulante($id){
        $perso = DB::table('rrhh.rrhh_perso')->select('rrhh_perso.id_persona')->where('rrhh_perso.nro_documento', $id)->get();
        if ($perso->count() > 0){
            foreach($perso as $value){
                $id_persona = $value->id_persona;
            }
            $postu = DB::table('rrhh.rrhh_postu')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->select('rrhh_postu.id_postulante')
                    ->where('rrhh_perso.nro_documento', $id)->get();
            if ($postu->count() > 0) {
                $prevData = DB::table('rrhh.rrhh_postu')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->select('rrhh_postu.*','rrhh_perso.id_persona', 'rrhh_perso.estado',
                                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_persona"))
                    ->where('rrhh_perso.nro_documento', $id)->get();
                $data[0] = ['id_persona' => $id_persona, 'id_postulante' => 1, 'data' => $prevData];
                return response()->json($data);
            }else{
                $prevData = DB::table('rrhh.rrhh_perso')->select('rrhh_perso.id_persona', 'rrhh_perso.estado',
                                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_persona"))
                                ->where('rrhh_perso.nro_documento', $id)->get();
                $data[0] = ['id_persona' => $id_persona, 'id_postulante' => 0, 'data' => $prevData];
                return response()->json($data);
            }
        }else{
            $data[0] = ['id_persona' => 0];
            return response()->json($data);
        }
    }
    public function buscar_postulante_alta($id){
        $postu = DB::table('rrhh.rrhh_perso')
            ->join('rrhh.rrhh_postu', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->select('rrhh_postu.id_postulante')
            ->where('rrhh_perso.nro_documento', $id)->count();
        if($postu > 0){
            $trab = DB::table('rrhh.rrhh_trab')
                        ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                        ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                        ->select('rrhh_trab.id_trabajador')
                        ->where('rrhh_perso.nro_documento', $id)->count();
            if ($trab > 0) {
                $data = DB::table('rrhh.rrhh_trab')
                        ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                        ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                        ->select('rrhh_trab.*','rrhh_perso.id_persona', 'rrhh_perso.nombres','rrhh_perso.apellido_paterno','rrhh_perso.apellido_materno', 'rrhh_perso.estado')
                        ->where('rrhh_perso.nro_documento', $id)->get();
                return response()->json($data);
            }else{
                $data = DB::table('rrhh.rrhh_perso')
                            ->join('rrhh.rrhh_postu', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                            ->select('rrhh_postu.id_postulante', 'rrhh_perso.nombres','rrhh_perso.apellido_paterno','rrhh_perso.apellido_materno', 'rrhh_perso.estado')
                            ->where('rrhh_perso.nro_documento', $id)->get();
                return response()->json($data);
            }
        }else{
            $data[0] = ['id_postulante' => 0];
            return response()->json($data);
        }
    }
    public function buscar_trabajador($id){
        $data = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->select('rrhh_trab.id_trabajador',
                            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_trabajador"))
                    ->where('rrhh_perso.nro_documento', $id)->get();
        if($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_trabajador' => 0];
            return response()->json($data);
        }
    }
    public function buscar_trabajador_generate($id){
        $data = DB::table('rrhh.rrhh_trab')
                    ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                    ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                    ->select('rrhh_trab.id_trabajador',
                            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_trabajador"))
                    ->where('rrhh_perso.nro_documento', $id)->get();
        if($data->count() > 0){
            return response()->json($data);
        }else{
            $data[0] = ['id_trabajador' => 0];
            return response()->json($data);
        }
    }
    public function autocompletar_autorizacion($id){
        $id = strtoupper($id);
        $data = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->select('rrhh_trab.id_trabajador',
                        DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->where('rrhh_perso.nombres', 'like', '%'.$id.'%')->get();
        return response()->json($data);
    }
    public function buscar_trabajador_autoriza($value){
        $data = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->select(DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->where('rrhh_trab.id_trabajador', $value)->get();
        return response()->json($data);
    }
    public function buscar_trab_reporte(){
        $data = DB::table('rrhh.rrhh_trab')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->select('rrhh_trab.id_trabajador', DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->where('rrhh_trab.estado', 1)->get();
        return response()->json($data);
    }

    
    // ACTUALIZAR
    public function actualizar_seleccion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_selec')->where('id_seleccion', $id)
        ->update([
            'id_empresa'     => $request->id_empresa,
            'id_cargo'       => $request->id_cargo,
            'requisitos'     => $request->requisitos,
            'perfil'         => $request->perfil,
            'lugar'          => $request->lugar,
            'cantidad'       => $request->cantidad,
            'fecha_inicio'   => $request->fecha_inicio,
            'fecha_fin'      => $request->fecha_fin,
            'fecha_registro' => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_persona(Request $request){
        $data = DB::table('rrhh.rrhh_perso')->where('id_persona', $request->id_persona)
        ->update([
            'id_documento_identidad'    => $request->id_documento_identidad,
            'nro_documento'             => $request->nro_documento,
            'nombres'                   => $request->nombres,
            'apellido_paterno'          => $request->apellido_paterno,
            'apellido_materno'          => $request->apellido_materno,
            'fecha_nacimiento'          => $request->fecha_nacimiento,
            'sexo'                      => $request->sexo,
            'id_estado_civil'           => $request->id_estado_civil,
            'estado'                    => 1,
            'fecha_registro'            => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_formacion_postulante(Request $request, $id){
        $data = DB::table('rrhh.rrhh_frm_acad')->where('id_formacion', $id)
        ->update([
                'id_postulante'        => $request->id_postulante,
                'id_nivel_estudio'     => $request->id_nivel_estudio,
                'id_carrera'           => $request->id_carrera,
                'fecha_inicio'         => $request->fecha_inicio,
                'fecha_fin'            => $request->fecha_fin,
                'nombre_institucion'   => $request->nombre_institucion,
                'id_pais'              => $request->id_pais,
                'ubigeo'               => $request->ubigeo,
                'estado'               => 1,
                'fecha_registro'       => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_experiencia_postulante(Request $request, $id){
        $data = DB::table('rrhh.rrhh_exp_labo')->where('id_postulante', $id)
        ->update([
            'id_postulante'             => $request->id_postulante,
            'nombre_empresa'            => $request->nombre_empresa,
            'cargo_ocupado'             => $request->cargo_ocupado,
            'datos_contacto'            => $request->datos_contacto,
            'telefono_contacto'         => $request->telefono_contacto,
            'relacion_trab_contacto'    => $request->relacion_trab_contacto,
            'funciones'                 => $request->funciones,
            'fecha_ingreso'             => $request->fecha_ingreso,
            'fecha_cese'                => $request->fecha_cese,
            'estado'                    => 1,
            'fecha_registro'            => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_informacion_postulante(Request $request, $id){
        $data = DB::table('rrhh.rrhh_postu')->where('id_postulante', $id)
        ->update([
            'id_persona'        => $request->id_persona,
            'direccion'         => $request->direccion,
            'telefono'          => $request->telefono,
            'correo'            => $request->correo,
            'brevette'          => $request->brevette,
            'id_pais'           => $request->id_pais,
            'ubigeo'            => $request->ubigeo,
            'fecha_registro'    => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_observacion_postulante(Request $request, $id){
        $data = DB::table('rrhh.rrhh_obs_postu')->where('id_observacion', $id)
        ->update([
            'id_postulante'             => $request->id_postulante,
            'observacion'               => $request->observacion,
            'id_usuario'                => $request->id_usuario,
            'estado'                    => 1,
            'fecha_registro'            => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_alta_trabajador(Request $request, $id){
        $data = DB::table('rrhh.rrhh_trab')->where('id_trabajador', $id)
        ->update([
            'id_postulante'             => $request->id_postulante,
            'id_tipo_planilla'          => $request->id_tipo_planilla,
            'id_tipo_trabajador'        => $request->id_tipo_trabajador,
            'id_categoria_ocupacional'  => $request->id_categoria_ocupacional,
            'condicion'                 => $request->condicion,
            'hijos'                     => $request->hijos,
            'id_pension'                => $request->id_pension,
            'cuspp'                     => $request->cuspp,
            'seguro'                    => $request->seguro,
            'confianza'                 => $request->confianza,
            // 'archivo_adjunto'        => $request->archivo_adjunto,
            'estado'                    => 1,
            'fecha_registro'            => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_contrato_trabajador(Request $request, $id){
        $data = DB::table('rrhh.rrhh_contra')->where('id_contrato', $id)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_tipo_contrato'      => $request->id_tipo_contrato,
            'id_modalidad'          => $request->id_modalidad,
            'id_horario'            => $request->id_horario,
            'id_centro_costo'       => $request->id_centro_costo,
            'tipo_centro_costo'     => $request->tipo_centro_costo,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
            'motivo'                => $request->motivo,
            // 'archivo_adjunto'    => $request->archivo_adjunto,
            'estado'                => 1,
            'fecha_registro'        => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_rol_trabajador(Request $request, $id){
        $data = DB::table('rrhh.rrhh_rol')->where('id_rol', $id)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_area'               => $request->id_area,
            'id_cargo'              => $request->id_cargo,
            'id_rol_concepto'       => $request->id_rol_concepto,
            'salario'               => $request->salario,
            'responsabilidad'       => $request->responsabilidad,
            'id_grupo'              => $request->id_grupo,
            'id_proyecto'           => $request->id_proyecto,
            'sctr'                  => $request->sctr,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
            'estado'                => 1,
            'fecha_registro'        => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_cuenta_trabajador(Request $request, $id){
        $data = DB::table('rrhh.rrhh_cta_banc')->where('id_cuenta_bancaria', $id)
        ->update([
            'id_trabajador'         => $request->id_trabajador,
            'id_banco'              => $request->id_banco,
            'id_tipo_cuenta'        => $request->id_tipo_cuenta,
            'nro_cci'               => $request->nro_cci,
            'nro_cuenta'            => $request->nro_cuenta,
            'id_moneda'             => $request->id_moneda,
            'estado'                => 1,
            'fecha_registro'        => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_derecho_habiente(Request $request, $id){
        $data = DB::table('rrhh.rrhh_der_hab')->where('id_derecho_habiente', $id)
        ->update([
            'id_trabajador'      => $request->id_trabajador,
            'id_persona'         => $request->id_persona,
            'id_condicion_dh'    => $request->id_condicion_dh,
            'estado'             => 1,
            'fecha_registro'     => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_periodo(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_asist')->where('id_asistencia', $request->id_asistencia)
        ->update([
            'id_tipo_asistencia'  => $request->id_tipo_asistencia,
            'descripcion'         => $request->descripcion,
            'fecha_inicio'        => $request->fecha_inicio,
            'fecha_fin'           => $request->fecha_fin,
            'estado'              => 1,
            'fecha_registro'      => $fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_prestamo(Request $request, $id){
        $data = DB::table('rrhh.rrhh_presta')->where('id_prestamo', $id)
        ->update([
            'id_trabajador'      => $request->id_trabajador,
            'concepto'          => $request->concepto,
            'fecha_prestamo'    => $request->fecha_prestamo,
            'nro_cuotas'        => $request->nro_cuotas,
            'monto_prestamo'    => $request->monto_prestamo,
            'porcentaje'        => $request->porcentaje,
            'estado'            => 1,
            'fecha_registro'    => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_permiso(Request $request, $id){
        $data = DB::table('rrhh.rrhh_permi')->where('id_permiso', $id)
        ->update([
            'id_trabajador'          => $request->id_trabajador,
            'id_tipo_permiso'        => $request->id_tipo_permiso,
            'motivo'                 => $request->motivo,
            'fecha_inicio_permiso'   => $request->fecha_inicio_permiso,
            'fecha_fin_permiso'      => $request->fecha_fin_permiso,
            'hora_inicio'            => $request->hora_inicio,
            'hora_fin'               => $request->hora_fin,
            'id_trabajador_autoriza' => $request->id_trabajador_autoriza,
            // 'archivo_adjunto'     => $request->archivo_adjunto,
            'estado'                 => 1,
            'fecha_registro'         => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_comision_salida(Request $request, $id){
        $data = DB::table('rrhh.rrhh_com_salida')->where('id_comision_salida', $id)
        ->update([
            'id_trabajador'          => $request->id_trabajador,
            'id_variable_comision'   => $request->id_tipo_comision,
            'motivo'                 => $request->motivo,
            'fecha_inicio_comision'  => $request->fecha_inicio_comision,
            'fecha_fin_comision'     => $request->fecha_fin_comision,
            'hora_inicio'            => $request->hora_inicio,
            'hora_fin'               => $request->hora_fin,
            'id_trabajador_autoriza' => $request->id_trabajador_autoriza,
            'estado'                 => 1,
            'fecha_registro'         => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_horas_extras(Request $request, $id){
        $data = DB::table('rrhh.rrhh_hrs_extra')->where('id_hora_extra', $id)
        ->update([
            'id_trabajador'          => $request->id_trabajador,
            'total_horas'            => $request->total_horas,
            'motivo'                 => $request->motivo,
            'fecha_hora_extra'       => $request->fecha_hora_extra,
            'id_trabajador_autoriza' => $request->id_trabajador_autoriza,
            'estado'                 => 1,
            'fecha_registro'         => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_reintegro(Request $request, $id){
        $data = DB::table('rrhh.rrhh_reintegro')->where('id_reintegro', $id)
        ->update([
            'id_trabajador'     => $request->id_trabajador,
            'fecha'             => $request->fecha,
            'importe'           => $request->monto,
            'concepto'          => $request->concepto,
            'estado'            => 1,
            'fecha_registro'    => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_criterio(Request $request, $id){
        $data = DB::table('rrhh.rrhh_criterio')->where('id_criterio', $id)
        ->update([
            'descripcion'         => $request->descripcion,
            'tipo_evaluacion'     => $request->tipo_evaluacion,
            'estado'              => 1,
            'fecha_registro'      => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_cargo(Request $request, $id){
        $data = DB::table('rrhh.rrhh_cargo')->where('id_cargo', $id)
        ->update([
            'descripcion'           => $request->descripcion,
            'sueldo_rango_minimo'   => $request->sueldo_rango_minimo,
            'sueldo_rango_maximo'   => $request->sueldo_rango_maximo,
            'estado'                => 1,
            'fecha_registro'        => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_modalidad(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_modali')->where('id_modalidad', $request->id_modalidad)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'dias_trabajo'      => $request->dias_trabajo,
            'dias_descanso'     => $request->dias_descanso,
            'estado'            => 1,
            'fecha_registro'    => $fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_contrato(Request $request){
        $data = DB::table('rrhh.rrhh_tp_contra')->where('id_tipo_contrato', $request->id_tipo_contrato)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_estado_civil(Request $request){
        $data = DB::table('rrhh.rrhh_est_civil')->where('id_estado_civil', $request->id_estado_civil)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_nivel_estudio(Request $request){
        $data = DB::table('rrhh.rrhh_niv_estud')->where('id_nivel_estudio', $request->id_nivel_estudio)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_planilla(Request $request, $id){
        $data = DB::table('rrhh.rrhh_tp_plani')->where('id_tipo_planilla', $id)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_trabajador(Request $request){
        $data = DB::table('rrhh.rrhh_tp_trab')->where('id_tipo_trabajador', $request->id_tipo_trabajador)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_categoria_ocupacional(Request $request, $id){
        $data = DB::table('rrhh.rrhh_cat_ocupac')->where('id_categoria_ocupacional', $id)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_fondo_pension(Request $request, $id){
        $data = DB::table('rrhh.rrhh_pensi')->where('id_pension', $id)
        ->update([
            'descripcion'           => $request->descripcion,
            'porcentaje_general'    => $request->porcentaje_general,
            'aporte'                => $request->aporte,
            'prima_seguro'          => $request->prima_seguro,
            'comision'              => $request->comision,
            'estado'                => 1,
            'fecha_registro'        => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_horario(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_horario')->where('id_horario', $request->id_horario)
        ->update([
            'descripcion'      => strtoupper($request->descripcion),
            'hora_ent_reg_sem' => $request->hora_ini_reg,
            'hora_sal_reg_sem' => $request->hora_fin_reg,
            'hora_sal_alm_sem' => $request->hora_ini_alm,
            'hora_ent_alm_sem' => $request->hora_fin_alm,
            'hora_ent_reg_sab' => $request->hora_ini_sab,
            'hora_sal_reg_sab' => $request->hora_fin_sab,
            'estado'           => 1,
            'fecha_registro'   => $fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_tolerancia(Request $request){
        $fecha_registro = date('Y-m-d H:i:s');
        $data = DB::table('rrhh.rrhh_tolerancia')->where('id_tolerancia', $request->id_tolerancia)
        ->update([
            'tiempo'  => $request->tiempo,
            'periodo' => $request->periodo,
            'estado'  => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_carrera(Request $request){
        $data = DB::table('rrhh.rrhh_carrera')->where('id_carrera', $request->id_carrera)
        ->update([
            'descripcion'   => strtoupper($request->descripcion),
            'estado'        => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_concepto_rol(Request $request){
        $data = DB::table('rrhh.rrhh_rol_concepto')->where('id_rol_concepto', $request->id_rol_concepto)
        ->update([
            'descripcion'       => strtoupper($request->descripcion),
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_condicion_dh(Request $request){
        $data = DB::table('rrhh.rrhh_cdn_dhab')->where('id_condicion_dh', $request->id_condicion_dh)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_merito(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_merito')->where('id_variable_merito', $id)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_demerito(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_sanci')->where('id_variable_sancion', $id)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_bonificacion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_bonif')->where('id_variable_bonificacion', $id)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_descuento(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_dscto')->where('id_variable_descuento', $id)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_retencion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_reten')->where('id_variable_retencion', $id)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tipo_aporte(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_aport')->where('id_variable_aportacion', $id)
        ->update([
            'descripcion'       => $request->descripcion,
            'estado'            => 1
        ]);
        return response()->json($data);
    }
    public function actualizar_tareo(Request $request){
        $id_asistencia = $request->id_asistencia;
        $id_trab = $request->id_trabajador;
        $fecha_asistencia = $request->fecha_asistencia;
        $tipo_ingreso = $request->tipo_ingreso;
        $tipo_horario = $request->tipo_horario;
        $hora_tareo = $request->ht;
        $fecha_registro = $request->fecha_registro;
        $tp_trab = $request->id_tipo_trabajador;

        $count = count($id_trab);
        $dia = $this->filtrar_dia($fecha_asistencia);

        $sqlTolera = DB::table('rrhh.rrhh_var_tolerancia')->where('estado', 1)->first();
        $tolerancia = (float) $sqlTolera->valor;

        for ($i=0; $i < $count; $i++){
            $id_trabajador = $id_trab[$i];
            $hour_reloj = $hora_tareo[$i];
            $sql = DB::table('rrhh.rrhh_asi_diaria')->select('id_asistencia_diaria', 'hora_entrada', 'hora_salida_almuerzo', 'hora_entrada_almuerzo', 'hora_salida')
                ->where([
                        ['id_asistencia', $id_asistencia],
                        ['id_trabajador', $id_trabajador],
                        ['id_tipo_trabajador', $tp_trab],
                        ['fecha_asistencia', $fecha_asistencia]
                    ])->first();

            $id_main = $sql->id_asistencia_diaria;
            $entradaHT = ($sql->hora_entrada != '') ? $sql->hora_entrada : '0';
            $salidaHT = ($sql->hora_salida != '') ? $sql->hora_salida : '0';
            $entradaHA = ($sql->hora_entrada_almuerzo != '') ? $sql->hora_entrada_almuerzo : '0';
            $salidaHA = ($sql->hora_salida_almuerzo != '') ? $sql->hora_salida_almuerzo : '0';
            
                
            if ($tipo_horario == 1){
                if ($tipo_ingreso == 1){
                    $data = DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_main)->update([
                            'hora_entrada'     => $hour_reloj]);
                    if($salidaHT > '0'|| $salidaHA > 0 || $entradaHA > 0){
                        $this->ReCalcTardanza($id_main, $dia, $hour_reloj, $salidaHA, $entradaHA, $salidaHT, $tolerancia);
                    }
                }else{
                    $data = DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_main)->update([
                            'hora_salida'      => $hour_reloj]);
                    if($entradaHT > '0'|| $salidaHA > 0 || $entradaHA > 0){
                        $this->ReCalcTardanza($id_main, $dia, $entradaHT, $salidaHA, $entradaHA, $hour_reloj, $tolerancia);
                    }
                }
            }else{
                if ($tipo_ingreso == 1) {
                    $data = DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_main)->update([
                            'hora_entrada_almuerzo'     => $hour_reloj]);
                    if($entradaHT > '0'|| $salidaHA > 0 || $salidaHT > 0){
                        $this->ReCalcTardanza($id_main, $dia, $entradaHT, $salidaHA, $hour_reloj, $salidaHT, $tolerancia);
                    }
                }else{
                    $data = DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_main)->update([
                            'hora_salida_almuerzo'      => $hour_reloj]);
                    if($entradaHT > '0'|| $entradaHA > 0 || $salidaHT > 0){
                        $this->ReCalcTardanza($id_main, $dia, $entradaHT, $hour_reloj, $entradaHA, $salidaHT, $tolerancia);
                    }
                }
            }
        }
    }
    function ReCalcTardanza($id_tareo, $dia, $hi, $hia, $hsa, $hs, $tolerancia){
        $hour_real_almuerzo_sal = '13:00';
        $hour_real_almuerzo_ent = '14:00';
        if ($dia == 6){
            $hour_real_ingreso = '09:00';
            $hour_real_salida = '12:00';

            $ht1 = $this->restar_horas($hour_real_ingreso, $hour_real_salida);
            $hora1 = $this->convertHD($ht1);
            $hora_trabajada_cal = $hora1;

            $hour_ing = $this->newTime($hi, $tolerancia, 'min');
            
            if ($hour_ing > $hour_real_ingreso){
                $tardanza_entrada = $this->restar_horas($hour_real_ingreso, $hi);
            }else{
                $tardanza_entrada = 0;
            }

            $minTardanza1 = substr($tardanza_entrada, 3, 2);

            DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_tareo)
            ->update([
                'hora_trabajada'    => $hora_trabajada_cal,
                'minutos_tardanza'  => $minTardanza1
            ]);
        }else{
            $hour_real_ingreso = '08:30';
            $hour_real_salida = '18:30';
            
            $ht1 = $this->restar_horas($hour_real_ingreso, $hour_real_almuerzo_sal);
            $ht2 = $this->restar_horas($hour_real_almuerzo_ent, $hour_real_salida);
            $hora1 = $this->convertHD($ht1);
            $hora2 = $this->convertHD($ht2);
            $hora_trab = $hora1 + $hora2;
            $hora_trabajada_cal = $hora_trab;

            $hour_ing = $this->newTime($hi, $tolerancia, 'min');
            $hour_alm = $this->newTime($hsa, $tolerancia, 'min');
            
            if ($hour_ing > $hour_real_ingreso){
                $tardanza_entrada = $this->restar_horas($hour_real_ingreso, $hi);
            }else{
                $tardanza_entrada = 0;
            }

            if ($hour_alm > $hour_real_almuerzo_ent) {
                $ht_alm = $this->restar_horas($hia, $hour_alm);
                $horaAlm = $this->convertHD($ht_alm);
                if ($horaAlm > 1) {
                    $one_hour = '01:00';
                    $new_hor = $this->sumOneHour($hia, 1, 'hour');
                    $tardanza_almuerzo = $this->restar_horas($new_hor, $hsa);
                }else{
                    $tardanza_almuerzo = 0;
                }
            }else{
                $tardanza_almuerzo = 0;
            }

            $minTardanza1 = substr($tardanza_entrada, 3, 2);
            $minTardanza2 = substr($tardanza_almuerzo, 3, 2);

            DB::table('rrhh.rrhh_asi_diaria')->where('id_asistencia_diaria', $id_tareo)
            ->update([
                'hora_trabajada'        => $hora_trabajada_cal,
                'minutos_tardanza'      => $minTardanza1,
                'minutos_tardanza_alm'  => $minTardanza2
            ]);
        }
    }
    public function actualizar_precalculo(Request $request){
        $asistencia = $request->id_asistencia;
        $id_trab = $request->id_trabajador;
        $lun = $request->lun;
        $mar = $request->mar;
        $mie = $request->mie;
        $jue = $request->jue;
        $vie = $request->vie;
        $sab = $request->sab;
        $hoy = $request->fecha_registro;

        $asist = DB::table('rrhh.rrhh_asist')->where('id_asistencia', $asistencia)->get();

        foreach ($asist as $row){
            $ini = strtotime($row->fecha_inicio);
            $fin = strtotime($row->fecha_fin);
        }

        $count = count($id_trab);
        
        for ($i = 0; $i < $count; $i++){
            $id_trabajador = $id_trab[$i];
            $cont_dia = 1;
            $id_calc = 0;
            
            for ($j = $ini; $j < $fin ; $j+= 86400){ 
                $dia = date("Y-m-d", $j);

                $preca = DB::table('rrhh.rrhh_pre_calculo')->select('id_pre_calculo')
                    ->where([
                        ['id_asistencia', $asistencia], ['id_trabajador', $id_trabajador], ['fecha', $dia]
                    ])->get();

                foreach ($preca as $key) {
                    $id_calc = $key->id_pre_calculo;
                }
                
                if ($cont_dia == 1){
                    $hora = $lun[$i];
                }elseif($cont_dia == 2){
                    $hora = $mar[$i];
                }elseif($cont_dia == 3){
                    $hora = $mie[$i];
                }elseif($cont_dia == 4){
                    $hora = $jue[$i];
                }elseif($cont_dia == 5){
                    $hora = $vie[$i];
                }elseif($cont_dia == 6){
                    $hora = $sab[$i];
                }

                $data = DB::table('rrhh.rrhh_pre_calculo')->where('id_pre_calculo', $id_calc)
                    ->update([
                        'hora'      => $hora
                    ]);
                $cont_dia += 1;
            }
        }

        return 1;
    }
    public function actualizar_sancion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_sanci')->where('id_sancion', $id)
        ->update([
            'id_variable_sancion'   => $request->id_variable_sancion,
            'concepto'              => $request->concepto,
            'motivo'                => $request->motivo,
            'fecha_sancion'         => $request->fecha_sancion,
            // 'archivo_adjunto'    => $request->archivo_adjunto,
            'estado'                => 1,
            'fecha_registro'        => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_merito(Request $request, $id){
        $data = DB::table('rrhh.rrhh_merito')->where('id_merito', $id)
        ->update([
            'id_variable_merito'    => $request->id_variable_merito,
            'concepto'              => $request->concepto,
            'motivo'                => $request->motivo,
            'fecha_merito'          => $request->fecha_merito,
            // 'archivo_adjunto'   => $request->archivo_adjunto,
            'estado'                => 1,
            'fecha_registro'        => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_planilla(Request $request, $id){
        $data = DB::table('rrhh.rrhh_pag_plani')->where('id_pago_planilla', $id)
        ->update([
            'id_empresa'        => $request->id_empresa,
            'id_asistencia'     => $request->id_asistencia,
            'id_tipo_planilla'  => $request->id_tipo_planilla,
            'estado'            => 1,
            'fecha_registro'    => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_vacaciones(Request $request, $id){
        $nro_dias = $this->numero_dias($request->fecha_inicio, $request->fecha_fin);
        $data = DB::table('rrhh.rrhh_vacac')->where('id_vacaciones', $id)
        ->update([
            'id_trabajador'      => $request->id_trabajador,
                'fecha_inicio'       => $request->fecha_inicio,
                'fecha_fin'          => $request->fecha_fin,
                'nro_dias'           => $nro_dias,
                'dias_efectuados'    => $request->nro_dias_efectuados,
                'concepto'           => $request->concepto,
                'estado'             => 1,
                'fecha_registro'     => $request->fecha_registro
        ]);
        return response()->json($data);
    }
    public function actualizar_bonificacion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_bonif')->where('id_bonificacion', $id)
        ->update([
            'id_trabajador'             =>$request->id_trabajador,
            'id_variable_bonificacion'  =>$request->id_variable_bonificacion,
            'afecto'                    =>$request->afecto,
            'concepto'                  =>$request->concepto,
            'importe'                   =>$request->importe,
            'fecha_bonificacion'        =>$request->fecha_bonificacion,
            'estado'                    =>1,
            'fecha_registro'            =>$request->fecha_registro,
        ]);
        return response()->json($data);
    }
    public function actualizar_descuento(Request $request, $id){
        $data = DB::table('rrhh.rrhh_dscto')->where('id_descuento', $id)
        ->update([
            'id_trabajador'             =>$request->id_trabajador,
            'id_variable_descuento'     =>$request->id_variable_descuento,
            'afecto'                    =>$request->afecto,
            'concepto'                  =>$request->concepto,
            'importe'                   =>$request->importe,
            'fecha_descuento'           =>$request->fecha_descuento,
            'estado'                    =>1,
            'fecha_registro'            =>$request->fecha_registro,
        ]);
        return response()->json($data);
    }
    public function actualizar_retencion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_retencion')->where('id_retencion', $id)
        ->update([
            'id_trabajador'             =>$request->id_trabajador,
            'id_variable_retencion'     =>$request->id_variable_retencion,
            'afecto'                    =>$request->afecto,
            'concepto'                  =>$request->concepto,
            'importe'                   =>$request->importe,
            'fecha_retencion'           =>$request->fecha_retencion,
            'estado'                    =>1,
            'fecha_registro'            =>$request->fecha_registro,
        ]);
        return response()->json($data);
    }
    public function actualizar_aporte(Request $request, $id){
        $data = DB::table('rrhh.rrhh_aport')->where('id_aportacion', $id)
        ->update([
            'id_trabajador'             =>$request->id_trabajador,
            'id_variable_aportacion'    =>$request->id_variable_aportacion,
            'concepto'                  =>$request->concepto,
            'importe'                   =>$request->importe,
            'estado'                    =>1,
            'fecha_registro'            =>$request->fecha_registro,
        ]);
        return response()->json($data);
    }


    // ANULAR
    public function anular_persona($id){
        $data = DB::table('rrhh.rrhh_perso')->where('id_persona', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }
    public function anular_seleccion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_selec')->where('id_seleccion', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }
    public function anular_formacion_postulante(Request $request, $id){
        $data = DB::table('rrhh.rrhh_frm_acad')->where('id_formacion', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }
    public function anular_experiencia_postulante(Request $request, $id){
        $data = DB::table('rrhh.rrhh_exp_labo')->where('id_experiencia_laboral', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }
    public function anular_datosextra_postulante(Request $request, $id){
        $data = DB::table('rrhh.rrhh_exp_labo')->where('id_experiencia_laboral', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }
    public function anular_observacion_postulante(Request $request, $id){
        $data = DB::table('rrhh.rrhh_obs_postu')->where('id_observacion', $id)
        ->update([
            'estado'    => 2,
        ]);
        return response()->json($data);
    }
    public function anular_contrato_trabajador(Request $request, $id){
        $data = DB::table('rrhh.rrhh_contra')->where('id_contrato', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_rol_trabajador(Request $request, $id){
        $data = DB::table('rrhh.rrhh_rol')->where('id_rol', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_cuenta_trabajador(Request $request, $id){
        $data = DB::table('rrhh.rrhh_cta_banc')->where('id_cuenta_bancaria', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_derecho_habiente(Request $request, $id){
        $data = DB::table('rrhh.rrhh_der_hab')->where('id_derecho_habiente', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_prestamo(Request $request, $id){
        $data = DB::table('rrhh.rrhh_presta')->where('id_prestamo', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_permiso(Request $request, $id){
        $data = DB::table('rrhh.rrhh_permi')->where('id_permiso', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_comision_salida(Request $request, $id){
        $data = DB::table('rrhh.rrhh_com_salida')->where('id_comision_salida', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_horas_extras(Request $request, $id){
        $data = DB::table('rrhh.rrhh_hrs_extra')->where('id_hora_extra', $id)
        ->update([
            'estado'    => 1
        ]);
        return response()->json($data);
    }
    public function anular_reintegro(Request $request, $id){
        $data = DB::table('rrhh.rrhh_reintegro')->where('id_reintegro', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_sancion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_sanci')->where('id_sancion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_merito(Request $request, $id){
        $data = DB::table('rrhh.rrhh_merito')->where('id_merito', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_periodo(Request $request, $id){
        $data = DB::table('rrhh.rrhh_asist')->where('id_asistencia', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }
    public function anular_criterio(Request $request, $id){
        $data = DB::table('rrhh.rrhh_criterio')->where('id_criterio', $id)
        ->update([
            'estado'     => 2
        ]);
        return response()->json($data);
    }
    public function anular_cargo(Request $request, $id){
        $data = DB::table('rrhh.rrhh_cargo')->where('id_cargo', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_modalidad($id){
        $data = DB::table('rrhh.rrhh_modali')->where('id_modalidad', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_contrato(Request $request, $id){
        $data = DB::table('rrhh.rrhh_tp_contra')->where('id_tipo_contrato', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_estado_civil($id){
        $data = DB::table('rrhh.rrhh_est_civil')->where('id_estado_civil', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_nivel_estudio($id){
        $data = DB::table('rrhh.rrhh_niv_estud')->where('id_nivel_estudio', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_planilla(Request $request, $id){
        $data = DB::table('rrhh.rrhh_tp_plani')->where('id_tipo_planilla', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_trabajador($id){
        $data = DB::table('rrhh.rrhh_tp_trab')->where('id_tipo_trabajador', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_categoria_ocupacional(Request $request, $id){
        $data = DB::table('rrhh.rrhh_cat_ocupac')->where('id_categoria_ocupacional', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_fondo_pension(Request $request, $id){
        $data = DB::table('rrhh.rrhh_pensi')->where('id_pension', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_horario($id){
        $data = DB::table('rrhh.rrhh_horario')->where('id_horario', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tolerancia($id){
        $data = DB::table('rrhh.rrhh_tolerancia')->where('id_tolerancia', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_concepto_rol(Request $request, $id){
        $data = DB::table('rrhh.rrhh_rol_concepto')->where('id_rol_concepto', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_condicion_dh($id){
        $data = DB::table('rrhh.rrhh_cdn_dhab')->where('id_condicion_dh', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_merito(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_merito')->where('id_variable_merito', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_demerito(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_sanci')->where('id_variable_sancion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_bonificacion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_bonif')->where('id_variable_bonificacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_descuento(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_dscto')->where('id_variable_descuento', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_retencion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_reten')->where('id_variable_retencion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_tipo_aporte(Request $request, $id){
        $data = DB::table('rrhh.rrhh_var_aport')->where('id_variable_aportacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_planilla(Request $request, $id){
        $data = DB::table('rrhh.rrhh_pag_plani')->where('id_pago_planilla', $id)
        ->update([
            'estado'    => 2,
        ]);
        return response()->json($data);
    }
    public function anular_vacaciones(Request $request, $id){
        $data = DB::table('rrhh.rrhh_vacac')->where('id_vacaciones', $id)
        ->update([
            'estado'    => 2,
        ]);
        return response()->json($data);
    }
    public function anular_bonificacion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_bonif')->where('id_bonificacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_descuento(Request $request, $id){
        $data = DB::table('rrhh.rrhh_dscto')->where('id_descuento', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_retencion(Request $request, $id){
        $data = DB::table('rrhh.rrhh_retencion')->where('id_retencion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    public function anular_aporte(Request $request, $id){
        $data = DB::table('rrhh.rrhh_aport')->where('id_aportacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

    
    // CALCULO DE PLANILLAS
    public function cargar_remuneraciones($plani, $mes, $anio){
        $dmY_primer = $this->primerDia($mes, $anio);
        $dmY_ultimo = $this->ultimoDia($mes, $anio);
        $data = array();

        // PRECALCULO
        $list_trab = DB::table('rrhh.rrhh_pre_calculo')
        ->select(DB::raw('DISTINCT id_trabajador'))->whereBetween('fecha', [$dmY_primer, $dmY_ultimo])
        ->orderBy('id_trabajador', 'asc')->get();

        foreach ($list_trab as $row){
            $ids_trab = $row->id_trabajador;

            // DATOS TRABAJADOR
            $sqlDatos = DB::table('rrhh.rrhh_trab')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->join('rrhh.rrhh_tp_trab', 'rrhh_tp_trab.id_tipo_trabajador', 'rrhh_trab.id_tipo_trabajador')
            ->join('rrhh.rrhh_cat_ocupac', 'rrhh_cat_ocupac.id_categoria_ocupacional', 'rrhh_trab.id_categoria_ocupacional')
            ->join('contabilidad.sis_identi', 'sis_identi.id_doc_identidad', '=', 'rrhh_perso.id_documento_identidad')
            ->join('rrhh.rrhh_pensi', 'rrhh_pensi.id_pension', 'rrhh_trab.id_pension')
            ->select('rrhh_perso.nro_documento AS nro_doc_persona', 'sis_identi.descripcion AS doc_identidad', 'rrhh_trab.cuspp', 'rrhh_trab.hijos',
                    'rrhh_pensi.descripcion AS pension', 'rrhh_pensi.id_pension', 'rrhh_tp_trab.descripcion AS tipo_trabajador', 'rrhh_cat_ocupac.descripcion AS categ_trabajador',
                    DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS datos_trabajador"))
            ->where('rrhh_trab.id_trabajador', '=', $ids_trab)->first();

            // ROL DEL TRABAJADOR
            $sqlRol = DB::table('rrhh.rrhh_rol')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_rol.id_trabajador')
            ->join('rrhh.rrhh_rol_concepto', 'rrhh_rol_concepto.id_rol_concepto', '=', 'rrhh_rol.id_rol_concepto')
            ->select('rrhh_rol_concepto.descripcion AS rol', 'rrhh_rol.id_cargo', 'rrhh_rol.salario')
            ->where('rrhh_trab.id_trabajador', '=', $ids_trab)
            ->limit(1)->orderBy('rrhh_rol.id_rol', 'desc')->first();

            // CONTRATO DEL TRABAJADOR
            $sqlContrato = DB::table('rrhh.rrhh_contra')
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'rrhh_contra.id_trabajador')
            ->join('rrhh.rrhh_tp_contra', 'rrhh_tp_contra.id_tipo_contrato', '=', 'rrhh_contra.id_tipo_contrato')
            ->join('rrhh.rrhh_modali', 'rrhh_modali.id_modalidad', '=', 'rrhh_contra.id_modalidad')
            ->select('rrhh_modali.descripcion AS modalidad', 'rrhh_tp_contra.descripcion AS tipo_contrato',
                    'rrhh_contra.fecha_inicio AS contra_inicio', 'rrhh_contra.fecha_fin AS contra_fin' ,'rrhh_contra.motivo')
            ->where('rrhh_trab.id_trabajador', '=', $ids_trab)
            ->limit(1)->orderBy('rrhh_contra.id_contrato', 'desc')->first();
                $dts_trab = $sqlDatos->datos_trabajador;
                $doc_iden = $sqlDatos->doc_identidad;
                $nro_docu = $sqlDatos->nro_doc_persona;
                $tip_trab = $sqlDatos->tipo_trabajador;
                $cat_trab = $sqlDatos->categ_trabajador;
                $nro_cusp = $sqlDatos->cuspp;
                $ids_pnsi = $sqlDatos->id_pension;
                $fnd_pnsi = $sqlDatos->pension;
                $cnt_hijo = $sqlDatos->hijos;
                $rol_desc = $sqlRol->rol;
                $ids_carg = $sqlRol->id_cargo;
                $sld_trab = (float) $sqlRol->salario;
                $ini_ctts = date('d/m/Y', strtotime($sqlContrato->contra_inicio));
                $fin_ctts = date('d/m/Y', strtotime($sqlContrato->contra_fin));
                $mod_ctts = $sqlContrato->modalidad;
                $tip_ctts = $sqlContrato->tipo_contrato;
                $sld_dias = $sld_trab/30;
                $sld_hora = $sld_trab/30/8;
                $sld_minu = $sld_trab/30/8/60;

            // ASIGNACION FAMILIAR
            if ($cnt_hijo == 1){
                $sqlAsig = DB::table('rrhh.rrhh_asig_familiar')->where('estado', 1)->limit(1)->orderBy('rrhh_asig_familiar.id_asignacion', 'desc')->first();
                    $asg_fami = (float) $sqlAsig->valor;
            }else{
                $asg_fami = 0;
            }

            //DATOS PENSIONES
            $sqlPension = DB::table('rrhh.rrhh_pensi')->where('id_pension', $ids_pnsi)->first();
                $psn_gral = $sqlPension->porcentaje_general;
                $psn_apor = $sqlPension->aporte;
                $psn_prim = $sqlPension->prima_seguro;
                $psn_comi = $sqlPension->comision;
                $psn_desc = ($sqlPension->descripcion == 'ONP') ? 1 : 2;

            // CARGO DEL TRABAJADOR
            $sqlCargo = DB::table('administracion.adm_empresa')
            ->join('rrhh.rrhh_cargo', 'rrhh_cargo.id_empresa', '=', 'adm_empresa.id_empresa')
            ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'adm_empresa.id_contribuyente')
            ->select('rrhh_cargo.descripcion AS cargo', 'adm_contri.nro_documento AS ruc_empresa', 'adm_contri.razon_social AS nombre_empresa')
            ->where('rrhh_cargo.id_cargo', '=', $ids_trab)
            ->first();
                $dsc_carg = $sqlCargo->cargo;
                $ruc_empr = $sqlCargo->ruc_empresa;
                $dts_empr = $sqlCargo->nombre_empresa;

            //PERMISOS
            $total_permiso_con = 0;
            $total_permiso_sin = 0;

            $sqlPermiso = DB::table('rrhh.rrhh_permi')->select('hora_inicio', 'hora_fin', 'id_tipo_permiso', 'fecha_inicio_permiso', 'fecha_fin_permiso')
                ->where([['id_trabajador', $ids_trab],['estado', 1]])->whereBetween('fecha_inicio_permiso', [$dmY_primer, $dmY_ultimo])->get();

            if ($sqlPermiso->count() > 0){
                foreach ($sqlPermiso as $keyperm){
                    $hri_perm = $keyperm->hora_inicio;
                    $hrf_perm = $keyperm->hora_fin;
                    $tip_perm = $keyperm->id_tipo_permiso;
                    $ini_perm = strtotime($keyperm->fecha_inicio_permiso);
                    $fin_perm = strtotime($keyperm->fecha_fin_permiso);
                    $tmp_perm = 0;
                    $hor_perm = 0;
                    $cnt_perm = 1;
                    
                    for ($a = $ini_perm; $a <= $fin_perm; $a+=86400){
                        $fechaPermiso = date('Y-m-d', $a);
                        $dia_perm = $this->filtrar_dia($fechaPermiso);
                        if ($cnt_perm > 1){
                            if ($dia_perm == 6){
                                $hrFinal = '12:00';
                                $tmp_perm = $this->restar_horas($hri_perm, $hrFinal);
                                $hor_perm = $this->convertHD($tmp_perm);
                                if ($hor_perm >= 3){
                                    $hor_final = 3;
                                }else{
                                    $hor_final = $hor_perm;
                                }
                            }else{
                                $hrFinal = '18:30';
                                $tmp_perm = $this->restar_horas($hri_perm, $hrFinal);
                                $hor_perm = $this->convertHD($tmp_perm);
                                if ($hor_perm >= 8){
                                    $hor_final = 9;
                                }else{
                                    $hor_final = $hor_perm;
                                }
                            }
                        }else{
                            if ($dia_perm == 6){
                                $tmp_perm = $this->restar_horas($hri_perm, $hrf_perm);
                                $hor_perm = $this->convertHD($tmp_perm);
                                if ($hor_perm >= 3){
                                    $hor_final = 3;
                                }else{
                                    $hor_final = $hor_perm;
                                }
                            }else{
                                $tmp_perm = $this->restar_horas($hri_perm, $hrf_perm);
                                $hor_perm = $this->convertHD($tmp_perm);
                                if ($hor_perm >= 8){
                                    $hor_final = 9;
                                }else{
                                    $hor_final = $hor_perm;
                                }
                            }
                        }

                        if ($tip_perm == 1){
                            $total_permiso_con += $hor_final;
                        }else{
                            $total_permiso_sin += $hor_final;
                        }
                        $cnt_perm+= 1;
                    }
                }
                $hor_perm = array($total_permiso_con, $total_permiso_sin);
            }else{
                $hor_perm = array(0, 0);
            }

            //BONIFICACIONES
            $con_bonif = 0;
            $sin_bonif = 0;
            $dts_bonif = [];

            $sqlBonif = DB::table('rrhh.rrhh_bonif')->select(DB::raw("SUM(rrhh_bonif.importe) AS importe"), 'rrhh_bonif.afecto', 'rrhh_var_bonif.descripcion AS concepto')
                ->join('rrhh.rrhh_var_bonif', 'rrhh_var_bonif.id_variable_bonificacion', 'rrhh_bonif.id_variable_bonificacion')
                ->where([['rrhh_bonif.id_trabajador', $ids_trab],['rrhh_bonif.estado', 1]])
                ->whereBetween('rrhh_bonif.fecha_bonificacion', [$dmY_primer, $dmY_ultimo])
                ->groupBy('rrhh_bonif.id_variable_bonificacion', 'rrhh_bonif.afecto', 'rrhh_var_bonif.descripcion')->get();
            
            if ($sqlBonif->count() > 0){
                foreach ($sqlBonif as $keybonif){
                    $imp_bonif = $keybonif->importe;
                    $afc_bonif = $keybonif->afecto;
                    $cnc_bonif = $keybonif->concepto;

                    $dts_bonif[] = ['concepto'=> $cnc_bonif, 'importe'=> $imp_bonif];
                    
                    if ($afc_bonif == 'SI'){
                        $con_bonif += $imp_bonif;
                    }else{
                        $sin_bonif += $imp_bonif;
                    }
                }
                $bonif_con = $con_bonif;
                $bonif_sin = $sin_bonif;
                $tot_bonif = $con_bonif + $sin_bonif;
            }else{
                $bonif_con = 0;
                $bonif_sin = 0;
                $tot_bonif = $bonif_con + $bonif_sin;
            }

            //DESCUENTOS
            $con_dsct = 0;
            $sin_dsct = 0;
            $dts_dsct = [];

            $sqlDescu = DB::table('rrhh.rrhh_dscto')->select(DB::raw("SUM(rrhh_dscto.importe) AS importe"), 'rrhh_dscto.afecto', 'rrhh_var_dscto.descripcion AS concepto')
                ->join('rrhh.rrhh_var_dscto', 'rrhh_var_dscto.id_variable_descuento', 'rrhh_dscto.id_variable_descuento')
                ->where([['rrhh_dscto.id_trabajador', $ids_trab],['rrhh_dscto.estado', 1]])
                ->whereBetween('rrhh_dscto.fecha_descuento', [$dmY_primer, $dmY_ultimo])
                ->groupBy('rrhh_dscto.id_variable_descuento', 'rrhh_dscto.afecto', 'rrhh_var_dscto.descripcion')->get();
            
            if ($sqlDescu->count() > 0){
                foreach ($sqlDescu as $keydscto){
                    $imp_dsct = (float) $keydscto->importe;
                    $afc_dscto = $keydscto->afecto;
                    $cnc_dsct = $keydscto->concepto;

                    $dts_dsct[] = ['concepto'=> $cnc_dsct, 'importe'=> $imp_dsct];
                    
                    if ($afc_dscto == 'SI'){
                        $con_dsct += $imp_dsct;
                    }else{
                        $sin_dsct += $imp_dsct;
                    }
                }
                $dscto_con = $con_dsct;
                $dscto_sin = $sin_dsct;
                $tot_dsct = $dscto_con + $dscto_sin;
            }else{
                $dscto_con = 0;
                $dscto_sin = 0;
                $tot_dsct = $dscto_con + $dscto_sin;
            }

            // MINUTOS DE TARDANZA
            $sqlAsist = DB::table('rrhh.rrhh_asi_diaria')
                ->select(DB::raw("SUM(rrhh_asi_diaria.minutos_tardanza) AS tardanza_regular"), DB::raw("SUM(rrhh_asi_diaria.minutos_tardanza_alm) AS tardanza_almuerzo"))
                ->where('rrhh_asi_diaria.id_trabajador', $ids_trab)->first();
            $min_treg = (float) $sqlAsist->tardanza_regular;
            $min_talm = (float) $sqlAsist->tardanza_almuerzo;

            $min_tard = $min_treg + $min_talm;

            /// TOTALES
            $sueldo_asig = $sld_trab + $asg_fami;
            $tot_sueldo = (($sueldo_asig + $bonif_con) - $dscto_con);
            $total_aporte = $tot_sueldo * ($psn_gral / 100);

            if ($psn_desc == 1){
                $tot_pensi = $total_aporte;
                $dts_pensi = array('obligatorio' => $total_aporte, 'prima' => 0, 'comision' => 0, 'total_aportes' => $tot_pensi);
            }else{
                $total_obli = $tot_sueldo * ($psn_apor / 100);
                $total_prim = $tot_sueldo * ($psn_prim / 100);
                $total_comi = $tot_sueldo * ($psn_comi / 100);

                $tot_pensi = $total_obli + $total_prim + $total_comi;
                $dts_pensi = array('obligatorio' => $total_obli, 'prima' => $total_prim, 'comision' => $total_comi, 'total_aportes' => $tot_pensi);
            }

            //APORTES DEL EMPLEADOR
            $dts_apor = [];
            $tot_aport = 0;
            $sqlAporta = DB::table('rrhh.rrhh_aport')->where('estado', '=', 1)->first();
                $apt_valor = $sqlAporta->valor;
                $apt_desc = $sqlAporta->concepto;
                $tot_aport = $tot_sueldo * ($apt_valor / 100);

            $dts_apor[] = ['concepto'=> $apt_desc, 'importe'=> $tot_aport];

            $total_tndza = $sld_minu * $min_tard;
            $total_dscto = $dscto_con + $dscto_sin;
            $total_bonif = $bonif_con + $bonif_sin;
            $pago_final = $sueldo_asig + $total_bonif - $total_dscto - $tot_pensi - $total_tndza;

            $data_prev[] = array(
                'empresa'               => $dts_empr,
                'ruc'                   => $ruc_empr,
                'trabajador'            => $ids_trab,
                'datos_trabajador'      => $dts_trab,
                'tipo_documento'        => $doc_iden,
                'dni_trabajador'        => $nro_docu,
                'fecha_contrato_ini'    => $ini_ctts,
                'modalidad_contrato'    => $mod_ctts,
                'tipo_contrato'         => $tip_ctts,
                'tipo_trabajador'       => $tip_trab,
                'categoria_trabajador'  => $cat_trab,
                'rol_trabajador'        => $rol_desc,
                'numero_cussp'          => $nro_cusp,
                'fondo_pension'         => $fnd_pnsi,
                'sueldo_basico'         => $sld_trab,
                'sueldo_dia'            => $sld_dias,
                'sueldo_hora'           => $sld_hora,
                'sueldo_minuto'         => $sld_minu,
                'minutos_tardanza'      => $min_tard,
                'monto_tardanza'        => $total_tndza,
                'asignacion_familiar'   => $asg_fami,
                'horas_permisos'        => $hor_perm,
                'bonificaciones'        => $dts_bonif,
                'total_bonificacion'    => $tot_bonif,
                'descuentos'            => $dts_dsct,
                'monto_dscto'           => $tot_dsct,
                'total_sueldo'          => $tot_sueldo,
                'aporte_pension'        => $dts_pensi,
                'aporte_empleador'      => $dts_apor,
                'total_pago'            => $pago_final
            );
        }

        //TOTAL DIAS = DIAS COMPUTABLES // TOTAL DOMINGOS
        $dias_compu = 30;
        $total_dias = cal_days_in_month(1, $mes, $anio);
        // $total_domi = $this->totalDomingos($dmY_primer, $dmY_ultimo);
        $dias_rem = array('dias' => $dias_compu, 'total_dias' => $total_dias);

        $data[] = ['dias'=> $dias_rem, 'data' =>$data_prev];

        return $data;
    }

    public function exportPlanilla(){
        $plani = 1;
        $mesPla = 6;
        $aniPla = 2018;

        $data = $this->cargar_remuneraciones($plani, $mesPla, $aniPla);
        $myData = $data[0]['data'];
        $cont = sizeof($myData);
        $mes = $this->hallarMes(6);
        $html = 
        '<html>
            <head>
                <style type="text/css">
                    body{
                        background-color: #fff;
                        font-family: "Arial";
                        font-size: 12px;
                        box-sizing: border-box;
                    }
                    table{
                        border-spacing: 0;
                        border-collapse: collapse;
                        font-size: 12px;
                    }
                    table tr th,
                    table tr td{
                        border: 1px solid #ccc;
                        padding: 5px;
                    }
                    .header-planilla{
                        width: 100%;
                        padding: 5px;
                        border: 1px solid #ccc;
                        margin-bottom: 5px;
                        box-sizing: border-box;
                    }
                    .header-tr{
                        text-align: left;
                    }
                </style>
            </head>
            <body>';

        for ($i = 0; $i < $cont; $i++) { 
            $html .=
            '<div class="header-planilla">
                <p>RUC: '.$myData[$i]['ruc'].'</p>
                <p>Empleador: '.$myData[$i]['empresa'].'</p>
                <p>Periodo: '.$mes.' del '.$aniPla.'</p>
            </div>
            <table width="100%">
                <tr>
                    <th colspan="2">Documento de Identidad</th>
                    <th colspan="2">Nombres y Apellidos</th>
                    <th>Situación</th>
                </tr>
                <tr>
                    <td>'.$myData[$i]['tipo_documento'].'</td>
                    <td>'.$myData[$i]['dni_trabajador'].'</td>
                    <td colspan="2">'.$myData[$i]['datos_trabajador'].'</td>
                    <td>ACTIVO O SUBSIDIADO</td>
                </tr>
                <tr>
                    <th>Fecha Ingreso</th>
                    <th>Tipo Trabajdor</th>
                    <th>Categoría Trabajdor</th>
                    <th>Régimen Pensionario</th>
                    <th>CUSPP</th>
                </tr>
                <tr>
                    <td width="10%">'.$myData[$i]['fecha_contrato'].'</td>
                    <td width="20%">'.$myData[$i]['tipo_trabajador'].'</td>
                    <td width="20%">'.$myData[$i]['categoria_trabajador'].'</td>
                    <td width="30%">'.$myData[$i]['pension'].'</td>
                    <td width="20%">'.$myData[$i]['cussp'].'</td>
                </tr>
            </table>
            <br>
            <table width="100%">
                <thead>
                    <tr>
                        <th colspan="2">Conceptos</th>
                        <th>Ingresos</th>
                        <th>Descuentos</th>
                        <th>Neto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="5" class="header-tr">Ingresos</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>VACACIONES TRUNCAS</td>
                        <td align="right">0.00</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>REMUNERACION VACACIONAL</td>
                        <td align="right">0.00</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>REMUNERACION BASICA</td>
                        <td align="right">'.number_format($myData[$i]['sueldo_basico'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>ASIGNACION FAMILIAR</td>
                        <td align="right">'.number_format($myData[$i]['asig_fam'], 2).'</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>CONDICIONES DE TRABAJO</td>
                        <td align="right">0.00</td>
                        <td></td>
                        <td></td>
                    </tr>';

            $myBoni = $data[0]['data'][$i]['bonificaciones'];
            $contBoni = sizeof($myBoni);

            if ($contBoni > 0){
                for ($j = 0; $j < $contBoni; $j++) { 
                    $html .=
                        '<tr>
                            <td width="10%"></td>
                            <td>'.$myBoni[$j]['concepto'].'</td>
                            <td width="15%" align="right">'.number_format($myBoni[$j]['importe'], 2).'</td>
                            <td width="15%"></td>
                            <td width="15%"></td>
                        </tr>';
                }
            }

            $html .=
                    '<tr><th colspan="5" class="header-tr">Descuentos</th></tr>';
            
            $myDscto = $data[0]['data'][$i]['descuentos'];
            $contDescu = sizeof($myDscto);

            $html .=
                    '<tr>
                        <td width="10%"></td>
                        <td>TARDANZA</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myData[$i]['monto_tardanza'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';

            for ($k = 0; $k < $contDescu; $k++) { 
                $html .=
                    '<tr>
                        <td width="10%"></td>
                        <td>'.$myDscto[$k]['concepto'].'</td>
                        <td width="15%"></td>
                        <td width="15%" align="right">'.number_format($myDscto[$k]['importe'], 2).'</td>
                        <td width="15%"></td>
                    </tr>';
            }

            $myPensi = $data[0]['data'][$i]['aporte_pension'];
            $total_pago = $data[0]['data'][$i]['total'];

            $html .=
                '<tr><th colspan="5" class="header-tr">Aportes del Trabajador</th></tr>
                <tr>
                    <td width="10%"></td>
                    <td>COMISION AFP PORCENTUAL</td>
                    <td width="15%"></td>
                    <td width="15%" align="right">'.number_format($myPensi['comision'], 2).'</td>
                    <td width="15%"></td>
                </tr>
                <tr>
                    <td width="10%"></td>
                    <td>PRIMA SEGURO AFP</td>
                    <td width="15%"></td>
                    <td width="15%" align="right">'.number_format($myPensi['prima'], 2).'</td>
                    <td width="15%"></td>
                </tr>
                <tr>
                    <td width="10%"></td>
                    <td>SPP - APORTACION OBLIGATORIA</td>
                    <td width="15%"></td>
                    <td width="15%" align="right">'.number_format($myPensi['obligatorio'], 2).'</td>
                    <td width="15%"></td>
                </tr>
                <tr>
                    <td width="10%"></td>
                    <td>RETENCIONES - RENTA DE QUINTA CATEGORIA</td>
                    <td width="15%"></td>
                    <td width="15%" align="right">0.00</td>
                    <td width="15%"></td>
                </tr>
                <tr>
                    <th colspan="4" class="header-tr">NETO A PGAR</th>
                    <th>'.number_format($total_pago ,2).'</th>
                </tr>';

            $html .=
                '</tbody>
            </table>
            <br>
            <table width="100%">
                <tr>
                    <th colspan="5" class="header-tr">Aportes del Empleador</th>
                </tr>';
            
            $myAport = $data[0]['data'][$i]['aporte_empleador'];
            $contAport = sizeof($myAport);

            for ($m = 0; $m < $contAport; $m++) { 
                $html .=    
                '<tr>
                    <td colspan="4">'.$myAport[$m]['concepto'].'</td>
                    <td width="15%" align="right">'.number_format($myAport[$m]['importe'], 2).'</td>
                </tr>';
            }

            $html .=
            '</table>
            <br><br><br>';
        }
        $html .=
        '</body>
        </html>';

        return $html;
    }

    public function generar_planilla_pdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->exportPlanilla());
        return $pdf->stream();
        return $pdf->download('reporte.pdf');
    }

    // FUNCIONES GENERALES
    function restar_horas($horaini, $horafin){
        $horai = substr($horaini, 0, 2);
        $mini = substr($horaini, 3, 2);
        $segi = substr($horaini, 6, 2);
        
        $horaf = substr($horafin, 0, 2);
        $minf = substr($horafin, 3, 2);
        $segf = substr($horafin, 6, 2);
        
        $ini = ((($horai * 60) * 60) + ($mini * 60) + $segi);
        $fin = ((($horaf * 60) * 60) + ($minf * 60) + $segf);
        
        $dif = $fin - $ini;
        
        $difh = floor($dif / 3600);
        $difm = floor(($dif - ($difh * 3600)) / 60);
        $difs = $dif - ($difm * 60) - ($difh * 3600);
        $hora = date("H:i", mktime($difh, $difm, $difs));
        return $hora;
    }
    function filtrar_dia($fecha){
        $dia = date("w",strtotime($fecha));
        return $dia;
    }
    function convertHD($hora){
        $hf = explode(":", $hora);
        $h = $hf[0];
        $m = $hf[1];
        $min = (($m * 60) / 3600);
        $horaf = $h + $min;
        return $horaf;
    }
    function numero_dias($inicio, $fin){
        $inicio = strtotime($inicio);
        $fin = strtotime($fin);
        $dif = $fin - $inicio;
        $diasFalt = ((($dif / 60) / 60) / 24);
        return ceil($diasFalt);
    }
    function leftZero($lenght, $number){
		$nLen = strlen($number);
		$zeros = '';
		for($i=0; $i<($lenght-$nLen); $i++){
			$zeros = $zeros.'0';
		}
		return $zeros.$number;
    }
    function busca_edad($fecha_nacimiento){
        $dia = date("d");
        $mes = date("m");
        $anio = date("Y");

        $dianac = date("d",strtotime($fecha_nacimiento));
        $mesnac = date("m",strtotime($fecha_nacimiento));
        $anionac = date("Y",strtotime($fecha_nacimiento));

        if (($mesnac == $mes) && ($dianac > $dia)){
            $anio=($anio - 1);
        }
        if ($mesnac > $mes){
            $anio=($anio - 1);
        }
        $edad = ($anio - $anionac);
        return $edad;
    }

    function ultimoDia($mes, $anio){ 
        $month = $mes;
        $year = $anio;
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    }

    function primerDia($mes, $anio){
        $month = $mes;
        $year = $anio;
        return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    }

    function totalDomingos($ini, $fin){
    	$ini = strtotime($ini);
		$fin = strtotime($fin);
		$total = 0;
		for($i = $ini; $i <= $fin; $i += 86400){
			$dia = date("Y-m-d", $i);
			if (date('w', strtotime($dia)) == 0){
				// echo $dia.'<br>';
				$total += 1;
			}
		}
		return $total;
    }

    function newTime($hour, $time, $type){
		$date = new DateTime($hour);
		$string = '-'.$time.' minute';
		$date->modify($string);
		return $date->format('H:i');
    }
    
    function sumOneHour($hour, $time, $type){
		$date = new DateTime($hour);
		$string = '+'.$time.' hour';
		$date->modify($string);
		return $date->format('H:i');
	}

    function hallarMes($val){
        $meses = array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");
        $fin = $meses[$val];
        return $fin;
    }

}
