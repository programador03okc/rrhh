<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use DateTime;
ini_set('max_execution_time', 3600);
date_default_timezone_set('America/Lima');

class ConfiguracionController extends Controller{
    public $idEmpresa;
    public function __construct(){
        // session_start();
        $this->idEmpresa = session()->get('id_empresa'); /* Empresa en SESSION */
    }
    /* VISTAS */
    function view_modulos(){ return view('configuracion/modulo');}
    function view_aplicaciones(){
        $modulos = $this->select_modulos();
        return view('configuracion/aplicaciones', compact('modulos'));
    }
    function view_usuario(){
        $modulos = $this->select_modulos();
        return view('configuracion/usuarios', compact('modulos'));
    }
    /* COMBOBOX - SELECT */
    public function select_doc_idendidad(){
        $data = DB::table('contabilidad.sis_identi')->select('id_doc_identidad', 'descripcion')->where('estado', '=', 1)
            ->orderBy('id_doc_identidad', 'asc')->get();
        return $data;
    }
    public function select_pais(){
        $data = DB::table('configuracion.sis_pais')->select('id_pais', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_centro_costos(){
        $data = DB::table('administracion.adm_grupo')->select('id_grupo', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_grupo($sede){
        $data = DB::table('administracion.adm_grupo')->select('id_grupo', 'descripcion')->where([['estado', '=', 1], ['id_sede', '=', $sede]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_sede(){
        $data = DB::table('administracion.sis_sede')->select('id_sede', 'descripcion')->where([['estado', '=', 1], ['id_empresa', '=', $this->idEmpresa]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_area($grupo){
        $data = DB::table('administracion.adm_area')->select('id_area', 'descripcion')->where([['estado', '=', 1], ['id_grupo', '=', $grupo]])
            ->orderBy('descripcion', 'asc')->get();
        return $data;
    }
    public function select_empresa(){
        $data = DB::table('administracion.adm_empresa')
            ->join('contabilidad.adm_contri', 'adm_empresa.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select('adm_empresa.id_empresa', 'adm_contri.razon_social')->where('adm_empresa.estado', '=', 1)
            ->orderBy('adm_contri.razon_social', 'asc')->get();
        return $data;
    }
    public function select_modulos(){
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', 0], ['estado', '=', 1]])->orderBy('codigo', 'asc')->get();
        return $data;
    }
    public function select_departamento(){
            $data = DB::table('configuracion.ubi_dpto')->select('id_dpto', 'descripcion')->where('estado', '=', 1)
            ->orderBy('descripcion', 'asc')->get();
            return $data;
    }
    public function select_prov_dep($id){
        $html = '';
        $data = DB::table('configuracion.ubi_prov')->where('id_dpto', '=', $id)->orderBy('descripcion', 'asc')->get();
        foreach ($data as $row){
        $id = $row->id_prov;
        $desc = $row->descripcion;
        $html .= '<option value="'.$id.'">'.$desc.'</option>';
        }
        return response()->json($html);
    }
    public function select_dist_prov($id){
        $html = '';
        $data = DB::table('configuracion.ubi_dis')->where('id_prov', '=', $id)->orderBy('descripcion', 'asc')->get();
        foreach ($data as $row){
        $id = $row->id_dis;
        $desc = $row->descripcion;
        $html .= '<option value="'.$id.'">'.$desc.'</option>';
    }
    return response()->json($html);
    }
    public function traer_ubigeo($id){
        $sql = DB::table('configuracion.ubi_dis')->where('id_dis', '=', $id)->first();
        $ubigeo = $sql->codigo;
    return response()->json($ubigeo);
    }
    public function cargar_estructura_org($id)
    {
        $html = '';
        $sql1 = DB::table('administracion.sis_sede')->where([['id_empresa', '=', $id], ['estado', '=', 1]])->get();
        foreach ($sql1 as $row) {
            $id_sede = $row->id_sede;
            $html .= '<ul>';
            $sql2 = DB::table('administracion.adm_grupo')->where([['id_sede', '=', $row->id_sede], ['estado', '=', 1]])->get();
            if ($sql2->count() > 0) {
                $html .=
                    '<li class="firstNode" onClick="showEfectOkc(' . $row->id_sede . ');">
                    <h5>+ <b> Sede - ' . $row->descripcion . '</b></h5>
                    <ul class="ul-nivel1" id="detalle-' . $row->id_sede . '">';
                foreach ($sql2 as $key) {
                    $id_grupo = $key->id_grupo;
                    $sql3 = DB::table('administracion.adm_area')->where([['id_grupo', '=', $key->id_grupo], ['estado', '=', 1]])->get();
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
    /* PASSWORDS */
    function cambiar_clave(Request $request){
        $p1 = $this->encode5t(addslashes($request->pass_old));
        $p2 = $this->encode5t(addslashes($request->pass_new));
        $user = Session()->get('usuario');
        
        $sql = DB::table('configuracion.sis_usua')->where([['clave', '=', $p1], ['usuario', '=', $user]])->get();
        
        if ($sql->count() > 0) {
            $id_usu = $sql->first()->id_usuario;
            $data = DB::table('configuracion.sis_usua')->where('id_usuario', $id_usu)->update(['clave'  => $p2]);
            $rpta = $data;
        }else{
            $rpta = 0;
        }
        return response()->json($rpta);
    }
    /* MODULO */
    public function mostrar_modulo_table(){
        $data = DB::table('configuracion.sis_modulo')->where('estado', '=', 1)->orderBy('tipo_modulo', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_modulo_id($id){
        $sql = DB::table('configuracion.sis_modulo')->where('id_modulo', $id)->get();
        $myId = $sql->first()->id_padre;
        $opt = $this->mostrar_modulos_edit($myId);
        $data = [0 => $sql, 1 => $opt];
        return response()->json($data);
    }
    public function mostrar_modulos_edit($value){
        $html = '';
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', 0], ['estado', '=', 1]])->orderBy('codigo', 'asc')->get();
        
        foreach ($data as $row){
            $id = $row->id_modulo;
            $desc = $row->descripcion;
            if ($id == $value) {
                $html .= '<option value="'.$id.'" selected>'.$desc.'</option>';
            }else{
                $html .= '<option value="'.$id.'">'.$desc.'</option>';
            }
        }
        return $html;
    }
    public function mostrar_modulos_combo(){
        $html = '';
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', 0], ['estado', '=', 1]])->orderBy('codigo', 'asc')->get();
        
        foreach ($data as $row){
            $id = $row->id_modulo;
            $desc = $row->descripcion;
            $html .= '<option value="'.$id.'">'.$desc.'</option>';
        }
        return response()->json($html);
    }
    public function countModules(){
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', 0],['estado', '=', 1]])->get();
        $num = $data->count();
        return $num;
    }
    public function countSubModules($id){
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', $id],['estado', '=', 1]])->get();
        $num = $data->count();
        return $num;
    }
    public function codeModules($id){
        $data = DB::table('configuracion.sis_modulo')->where('id_modulo', $id)->first();
        $code = $data->codigo;
        return $code;
    }
    public function guardar_modulo(Request $request){
        $tipo = $request->tipo_mod;
        $padre = (empty($request->padre_mod)) ? 0 : $request->padre_mod;
        $id = DB::table('configuracion.sis_modulo')->insertGetId(
            [
                'tipo_modulo'   => $tipo,
                'id_padre'      => $padre,
                'descripcion'   => $request->descripcion,
                'ruta'          => $request->ruta,
                'estado'        => 1
            ],
            'id_modulo'
        );
        if ($id > 0){
            if ($tipo == 1){
                $count = $this->countModules();
                $code = $this->leftZero(2, $count);
            }else{
                $count = $this->countSubModules($padre);
                $code1 = $this->codeModules($padre);
                $code2 = $this->leftZero(2, $count);
                $code = $code1.'.'.$code2;
            }
    
            $data = DB::table('configuracion.sis_modulo')->where('id_modulo', $id)
            ->update([
                'codigo'    => $code
                ]);
        }else{
            $id = 0;
        }
        return response()->json($id);
    }
    public function actualizar_modulo(Request $request){
        $tipo = $request->tipo_mod;
        $padre = (empty($request->padre_mod)) ? 0 : $request->padre_mod;
        
        $data = DB::table('configuracion.sis_modulo')->where('id_modulo', $request->id_modulo)
        ->update([
            'tipo_modulo'   => $tipo,
            'id_padre'      => $padre,
            'descripcion'   => $request->descripcion,
            'ruta'          => $request->ruta
        ]);
        return response()->json($data);
    }
    public function anular_modulo($id){
        $data = DB::table('configuracion.sis_modulo')->where('id_modulo', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }
    /* APLICACIONES */
    public function mostrar_aplicaciones_table(){
        $data = DB::table('configuracion.sis_aplicacion')
            ->join('configuracion.sis_modulo', 'sis_modulo.id_modulo', '=', 'sis_aplicacion.id_sub_modulo')
            ->select('sis_aplicacion.*', 'sis_modulo.descripcion AS modulo')
            ->where('sis_aplicacion.estado', '=', 1)->orderBy('sis_aplicacion.descripcion', 'asc')->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_aplicaciones_id($id){
        $sql = DB::table('configuracion.sis_aplicacion')
            ->join('configuracion.sis_modulo', 'sis_modulo.id_modulo', '=', 'sis_aplicacion.id_sub_modulo')
            ->select('sis_aplicacion.id_aplicacion', 'sis_aplicacion.id_sub_modulo AS submodulo', 'sis_aplicacion.descripcion', 'sis_aplicacion.ruta',
                'sis_modulo.id_padre', 'sis_aplicacion.estado')
            ->where('sis_aplicacion.id_aplicacion', $id)->get();
        $id_sub = $sql->first()->submodulo;
        $option = $this->mostrar_submodulo($id_sub);
        $data = [0 => $sql, 1 => $option];
        return response()->json($data);
    }
    public function mostrar_submodulo_id($id){
        $html = '';
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', $id], ['estado', '=', 1]])->orderBy('descripcion', 'asc')->get();
        foreach ($data as $row){
            $ids = $row->id_modulo;
            $desc = $row->descripcion;
            $html .= '<option value="'.$ids.'">'.$desc.'</option>';
        }
        return response()->json($html);
    }
    public function mostrar_submodulo($id){
        $html = '';
        $sql = DB::table('configuracion.sis_modulo')->where('id_modulo', '=', $id)->first();
        $myId = $sql->id_padre;
        $data = DB::table('configuracion.sis_modulo')->where([['id_padre', '=', $myId], ['estado', '=', 1]])->orderBy('descripcion', 'asc')->get();
        foreach ($data as $row){
            $ids = $row->id_modulo;
            $desc = $row->descripcion;
            if ($id == $ids) {
                $html .= '<option value="'.$ids.'" selected>'.$desc.'</option>';
            }else{
                $html .= '<option value="'.$ids.'">'.$desc.'</option>';
            }
        }
        return $html;
    }
    public function guardar_aplicaciones(Request $request){
        $id = DB::table('configuracion.sis_aplicacion')->insertGetId(
            [
                'id_sub_modulo' => $request->sub_modulo,
                'descripcion'   => $request->descripcion,
                'ruta'          => $request->ruta,
                'estado'        => 1
            ],
            'id_aplicacion'
        );
        return response()->json($id);
    }
    public function actualizar_aplicaciones(Request $request){
        $data = DB::table('configuracion.sis_aplicacion')->where('id_aplicacion', $request->id_aplicacion)
        ->update([
            'id_sub_modulo' => $request->sub_modulo,
            'descripcion'   => $request->descripcion,
            'ruta'          => $request->ruta,
        ]);
        return response()->json($data);
    }
    public function anular_aplicaciones($id){
        $data = DB::table('configuracion.sis_aplicacion')->where('id_aplicacion', $id)
        ->update([
            'estado'    => 2
        ]);
        return response()->json($data);
    }

	public function mostrar_usuarios_table(){
		$data = DB::table('configuracion.sis_usua')
        ->select(
            'sis_usua.id_usuario',
            'sis_usua.nombre_corto',
            'sis_usua.usuario',
            'sis_usua.clave',
            'sis_usua.fecha_registro',
            'sis_usua.estado',
			'rrhh_perso.nro_documento',
            'rrhh_perso.nombres',
			'rrhh_perso.apellido_paterno',
			'rrhh_perso.apellido_materno'
           )
        ->join('rrhh.rrhh_trab', 'sis_usua.id_trabajador', '=', 'rrhh_trab.id_trabajador')
        ->join('rrhh.rrhh_postu', 'rrhh_trab.id_postulante', '=', 'rrhh_postu.id_postulante')
        ->join('rrhh.rrhh_perso', 'rrhh_postu.id_persona', '=', 'rrhh_perso.id_persona')

        ->where([['sis_usua.estado', '!=', 7]])
        ->orderBy('sis_usua.id_usuario', 'asc')
        ->get();
        
        $output=['data'=>$data];
        return $output;
	}
    public function guardar_usuarios(Request $request){
        $data = DB::table('configuracion.sis_usua')->insertGetId(
            [
                'id_trabajador'     => $request->id_trabajador,
                'usuario'           => $request->usuario,
                'clave'             => $this->encode5t($request->clave),
                'estado'            => $request->estado,
                'fecha_registro'    => $request->fecha_registro
                
            ],
            'id_usuario'
        );
        return response()->json($data);
    }
    
    /* FUNCIONES */
    function leftZero($lenght, $number){
		$nLen = strlen($number);
		$zeros = '';
		for($i=0; $i<($lenght-$nLen); $i++){
			$zeros = $zeros.'0';
		}
		return $zeros.$number;
    }
    public static function encode5t($str){
        for($i=0; $i<5;$i++){
            $str=strrev(base64_encode($str));
        }
        return $str;
    }
    
    public function decode5t($str){
        for($i=0; $i<5;$i++){
            $str=base64_decode(strrev($str));
        }
        return $str;
    }
}