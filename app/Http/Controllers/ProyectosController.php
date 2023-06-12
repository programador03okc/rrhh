<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

date_default_timezone_set('America/Lima');

class ProyectosController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_sis_contrato(){
        return view('proyectos/sis_contrato');
    }
    function view_tipo_insumo(){
        return view('proyectos/tipo_insumo');
    }
    function view_iu(){
        return view('proyectos/iu');
    }
    function view_insumo(){
        $tipos = $this->mostrar_tipos_insumos_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        $ius = $this->mostrar_ius_cbo();
        return view('proyectos/insumo', compact('tipos','unidades','ius'));
    }
    function view_acu(){
        $unidades = $this->mostrar_unidades_cbo();
        return view('proyectos/acu', compact('unidades'));
    }
    function view_opcion(){
        $clientes = $this->mostrar_clientes_cbo();
        $monedas = $this->mostrar_monedas_cbo();
        $tipos = $this->mostrar_tipos_cbo();
        return view('proyectos/opcion', compact('clientes','monedas','tipos'));
    }
    function view_presint(){
        $monedas = $this->mostrar_monedas_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        return view('proyectos/presupuesto/presint', compact('monedas','sistemas','unidades'));
    }
    function view_propuesta(){
        $monedas = $this->mostrar_monedas_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        return view('proyectos/presupuesto/propuesta', compact('monedas','sistemas','unidades'));
    }
    function view_preseje(){
        $monedas = $this->mostrar_monedas_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $unidades = $this->mostrar_unidades_cbo();
        return view('proyectos/presupuesto/preseje', compact('monedas','sistemas','unidades'));
    }
    function view_proyecto(){
        $clientes = $this->mostrar_clientes_cbo();
        $monedas = $this->mostrar_monedas_cbo();
        $tipos = $this->mostrar_tipos_cbo();
        $sistemas = $this->mostrar_sis_contrato_cbo();
        $modalidades = $this->mostrar_modalidad_cbo();
        $unid_program = $this->mostrar_unid_program_cbo();
        $tipo_contrato = $this->mostrar_tipo_contrato_cbo();
        return view('proyectos/proyecto', compact('clientes','monedas','tipos','sistemas','modalidades','unid_program','tipo_contrato'));
    }
    function view_cronoint(){
        return view('proyectos/cronoint');
    }

    public function tipos_insumos_cbo(){
        $data = DB::table('proyectos.proy_tp_insumo')
        ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.descripcion')
        ->where('estado',1)->get();
        return $data;
    }
    public function mostrar_unidades_cbo(){
        $data = DB::table('almacen.alm_und_medida')
            ->select('alm_und_medida.id_unidad_medida','alm_und_medida.abreviatura','alm_und_medida.descripcion')
            ->where([['alm_und_medida.estado', '=', 1]])
                ->orderBy('descripcion')
                ->get();
        return $data;
    }
    public function mostrar_ius_cbo(){
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.id_iu','proy_iu.descripcion')
            ->where('estado', 1)
            ->orderBy('descripcion')
            ->get();
        return $data;
    }
    public function mostrar_tipos_insumos_cbo(){
        $data = DB::table('proyectos.proy_tp_insumo')
            ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.descripcion')
            ->where('estado',1)
            ->get();
        return $data;
    }
    public function mostrar_clientes_cbo(){
        $data = DB::table('comercial.com_cliente')
            ->select('com_cliente.id_cliente','adm_contri.nro_documento','adm_contri.razon_social')
            ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('com_cliente.estado',1)
            ->get();
        return $data;
    }
    public function mostrar_monedas_cbo()
    {
        $data = DB::table('configuracion.sis_moneda')
            ->select('sis_moneda.id_moneda','sis_moneda.simbolo','sis_moneda.descripcion')
            ->where([['sis_moneda.estado', '=', 1]])
            ->orderBy('sis_moneda.id_moneda')
            ->get();
        return $data;
    }
    public function mostrar_tipos_cbo()
    {
        $data = DB::table('proyectos.proy_tp_proyecto')
            ->select('proy_tp_proyecto.id_tp_proyecto','proy_tp_proyecto.descripcion')
            ->where([['proy_tp_proyecto.estado', '=', 1]])
            ->get();
            return $data;
    }
    public function mostrar_sis_contrato_cbo(){
        $data = DB::table('proyectos.proy_sis_contrato')
            ->select('proy_sis_contrato.id_sis_contrato','proy_sis_contrato.descripcion')
            ->where([['proy_sis_contrato.estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_modalidad_cbo(){
        $data = DB::table('proyectos.proy_modalidad')
            ->select('proy_modalidad.id_modalidad','proy_modalidad.descripcion')
            ->where([['proy_modalidad.estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_unid_program_cbo(){
        $data = DB::table('proyectos.proy_unid_program')
            ->select('proy_unid_program.id_unid_program','proy_unid_program.descripcion')
            ->where([['proy_unid_program.estado', '=', 1]])
            ->get();
        return $data;
    }
    public function mostrar_tipo_contrato_cbo(){
        $data = DB::table('proyectos.proy_tp_contrato')
        ->select('proy_tp_contrato.id_tp_contrato','proy_tp_contrato.descripcion')
        ->where([['proy_tp_contrato.estado','=',1]])
            ->get();
        return $data;
    }
    //modalidad
    public function mostrar_modalidad(){
        $data = DB::table('proyectos.proy_modalidad')
        ->select('proy_modalidad.*')
            ->get();
        // $data = proy_modalidad::all();
        return response()->json($data);
    }
    //tipos de contrato
    public function mostrar_tipos_contrato(){
        $data = DB::table('proyectos.proy_tp_contrato')
        ->select('proy_tp_contrato.*')
            ->get();
        // $data = proy_tp_contrato::all();
        return response()->json($data);
    }
    //tipos de proyecto
    public function mostrar_tipos_proyecto(){
        $data = DB::table('proyectos.proy_tp_proyecto')
        ->select('proy_tp_proyecto.*')
            ->get();
        // $data = proy_tp_proyecto::all();
        return response()->json($data);
    }
    //clientes
    public function mostrar_clientes(){
        $data = DB::table('comercial.com_cliente')
        ->select('com_cliente.*','adm_contri.razon_social','adm_contri.nro_documento')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where([['com_cliente.estado','=',1]])
            ->orderBy('com_cliente.id_cliente')
            ->get();
        return response()->json($data);
    }
    public function mostrar_cliente($id){
        $data = DB::table('comercial.com_cliente')
        ->select('com_cliente.*','adm_contri.razon_social')
        ->join('contabilidad.adm_contri','adm_contri.id_contribuyente','=','com_cliente.id_contribuyente')
            ->where('id_cliente','=',$id)
            ->get();
        return response()->json($data);
    }
    //IGV
    public function mostrar_impuesto($cod,$fecha){
        $data = DB::table('contabilidad.cont_impuesto')
        ->select('cont_impuesto.*')
            ->where([['codigo','=',$cod],['fecha_inicio','<',$fecha]])
            ->orderBy('fecha_inicio','desc')
            ->first();
            // ->get();
        return response()->json($data);
    }
    //moneda
    public function mostrar_moneda(){
        $data = DB::table('configuracion.sis_moneda')
        ->select('sis_moneda.*')
            ->get();
        // $data = moneda::all();
        return response()->json($data);
    }
    //tipos de presupuesto
    public function mostrar_tp_presupuesto(){
        $data = DB::table('proyectos.proy_tp_pres')
        ->select('proy_tp_pres.*')
            ->get();
        // $data = proy_tp_presupuesto::all();
        return response()->json($data);
    }
    //unidad de programacion
    public function mostrar_unid_program(){
        $data = DB::table('proyectos.proy_unid_program')
        ->select('proy_unid_program.*')
            ->get();
        return response()->json($data);
    }
    public function mostrar_unid_programById($id){
        $data = DB::table('proyectos.proy_unid_program')
        ->select('proy_unid_program.*')
        ->where([['id_unid_program', '=', $id]])
            ->get();
        return response()->json($data);
    }
    //iu
    public function mostrar_ius(){
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.*')
            ->orderBy('codigo')
            ->get();        
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_iu($iu)
    {
        $data = DB::table('proyectos.proy_iu')
            ->select('proy_iu.*')
            ->where([['id_iu', '=', $iu]])
            ->get();        
        // $data = proy_iu::where('id_iu', $iu)->first();
        return response()->json($data);
    }
    public function guardar_iu(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_iu = DB::table('proyectos.proy_iu')->insertGetId(
            [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_iu'
            );

        return response()->json($id_iu);
    }
    public function update_iu(Request $request)
    {
        $iu = DB::table('proyectos.proy_iu')
            ->where('id_iu',$request->id_iu)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado
            ]);
        return response()->json($iu);
    }
    public function anular_iu(Request $request,$id_iu)
    {
        $iu = DB::table('proyectos.proy_iu')
            ->where('id_iu',$id_iu)
            ->update([ 'estado' => 2 ]);
        return response()->json($iu);
    }
    public function delete_iu($id)
    {
        DB::table('proyectos.proy_iu')
            ->where('id_iu', '=', $id)
            ->delete();
        // $data = proy_iu::where('id_iu', $id)->delete();
        return response()->json($data);
    }
    // public function delete_iu($id){
    //     $iu = proy_iu::find($id);
    //     $iu->delete();
    //     return response()->json('El IU ha sido eliminado.');
    // }

    //sistemas de contrato
    public function mostrar_sis_contratos()
    {
        $data = DB::table('proyectos.proy_sis_contrato')
        ->select('proy_sis_contrato.*')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_sis_contrato($id)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
        ->select('proy_sis_contrato.*')
            ->where([['id_sis_contrato', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_sis_contrato(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $data = DB::table('proyectos.proy_sis_contrato')->insertGetId(
            [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_sis_contrato'
            );
            
        return response()->json($data);
    }
    public function update_sis_contrato(Request $request)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
            ->where('id_sis_contrato', $request->id_sis_contrato)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion
            ]);

        return response()->json($data);
    }
    public function anular_sis_contrato(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_sis_contrato')
            ->where('id_sis_contrato', $id)
            ->update([ 'estado' => 2 ]);

        return response()->json($data);
    }

    //tipos de insumos
    public function mostrar_tipos_insumos()
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->select('proy_tp_insumo.*')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_tp_insumo($id)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->select('proy_tp_insumo.*')
            ->where([['proy_tp_insumo.id_tp_insumo', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function guardar_tp_insumo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $id_tp_insumo = DB::table('proyectos.proy_tp_insumo')->insertGetId(
            [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'fecha_registro' => $fecha,
                'estado' => 1
            ],
                'id_tp_insumo'
            );
        return response()->json($id_tp_insumo);
    }
    public function update_tp_insumo(Request $request)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->where('id_tp_insumo', $request->id_tp_insumo)
            ->update([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion
            ]);

        return response()->json($data);
    }
    public function anular_tp_insumo(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_tp_insumo')
            ->where('id_tp_insumo', $id)
            ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }
    //Insumos
    public function mostrar_insumos()
    {
        $data = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.*','alm_und_medida.abreviatura',
        'proy_tp_insumo.codigo as cod_tp_insumo',
        'proy_iu.descripcion as iu_descripcion')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->join('proyectos.proy_iu','proy_iu.id_iu','=','proy_insumo.iu')
        ->where([['proy_insumo.estado', '=', 1]])
            ->orderBy('codigo')
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_insumo($id)
    {
        $data = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.*', 'alm_und_medida.abreviatura',
        'proy_tp_insumo.codigo as cod_tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->where([['proy_insumo.id_insumo', '=', $id]])
            ->get();
        return response()->json($data);
    }
    public function next_cod_insumo(){
        $data = DB::table('proyectos.proy_insumo')
        ->orderBy('codigo','desc')
        ->where('estado',1)
        ->first();
        $codigo = ((int)$data->codigo)+1;
        return ((string)$codigo);
    }
    public function guardar_insumo(Request $request)
    {
        $fecha = date('Y-m-d H:i:s');
        $codigo = $this->next_cod_insumo();
        $id_insumo = DB::table('proyectos.proy_insumo')->insertGetId(
            [
                'codigo' => $codigo,
                'descripcion' => $request->descripcion,
                'tp_insumo' => $request->tp_insumo,
                'unid_medida' => $request->unid_medida,
                'precio' => $request->precio,
                'flete' => $request->flete,
                'peso_unitario' => $request->peso_unitario,
                'iu' => $request->iu,
                'fecha_registro' =>  $fecha,
                'estado' => 1,
            ],
                'id_insumo'
            );
        return response()->json($id_insumo);
    }
    public function update_insumo(Request $request)
    {
        $data = DB::table('proyectos.proy_insumo')
        ->where('id_insumo',$request->id_insumo)
        ->update([
                'descripcion' => $request->descripcion,
                'tp_insumo' => $request->tp_insumo,
                'unid_medida' => $request->unid_medida,
                'precio' => $request->precio,
                'flete' => $request->flete,
                'peso_unitario' => $request->peso_unitario,
                'iu' => $request->iu,
            ]);

        return response()->json($data);
    }
    public function anular_insumo(Request $request, $id)
    {
        DB::table('proyectos.proy_insumo')
            ->where('id_insumo', $id)
            ->update([ 'estado' => 2 ]);
        // $insumo = proy_insumo::where('id_insumo', $id_insumo)->first();
        // $insumo->estado = 2;
        // $insumo->save();
        return response()->json($id);
    }
    public function delete_insumo($id)
    {
        DB::table('proyectos.proy_insumo')
                ->where('id_insumo', '=', $id)
                ->delete();
        // $insumo = proy_insumo::find($id);
        // $insumo->delete();
        return response()->json('El insumo ha sido eliminado.');
    }
    public function buscar_iu(Request $request,$id_iu)
    {
        $insumos = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.id_insumo')
            ->where([['proy_insumo.iu', '=', $id_iu]])
            ->get()->count();
        return response()->json($insumos);
    }
    public function buscar_tp_insumo(Request $request,$id)
    {
        $insumos = DB::table('proyectos.proy_insumo')
        ->select('proy_insumo.id_insumo')
            ->where([['proy_insumo.tp_insumo', '=', $id]])
            ->get()->count();
        return response()->json($insumos);
    }

    //Analisis de Costos Unitarios
    public function mostrar_acus()
    {
        $data = DB::table('proyectos.proy_cu')
            ->select('proy_cu.*','alm_und_medida.abreviatura')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu.unid_medida')
            ->where([['proy_cu.estado', '=', 1]])
            ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_acu($id)
    {
        $acu = DB::table('proyectos.proy_cu')
        ->select('proy_cu.*', 'alm_und_medida.abreviatura')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu.unid_medida')
            ->where([['proy_cu.id_cu', '=', $id]])
            ->get();

        return response()->json($acu);

    }
    public function mostrar_acu_todo($id)
    {
        $acu = DB::table('proyectos.proy_cu')
        ->select('proy_cu.*', 'alm_und_medida.abreviatura')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu.unid_medida')
            ->where([['proy_cu.id_cu', '=', $id]])
            ->get();

        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_tp_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu', '=', $id]])
            ->orderBy('codigo')
            ->get();

        $presupuestos = $this->mostrar_presupuestos_acu($id);
        $obs = $this->mostrar_lecciones_acu($id);

        $data = ["acu"=>$acu,"acu_detalle"=>$detalle,"presupuestos"=>$presupuestos,"obs"=>$obs];
        $output['data'] = $data;

        return response()->json($output);

    }
    public function mostrar_acu_completo()
    {
        $acu = DB::table('proyectos.proy_cu')
        ->select('proy_cu.*', 'alm_und_medida.abreviatura')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cu.unid_medida')
            ->get();
            
        $new_acus = [];
        foreach($acu as $cu){
            $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_tp_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
                ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
                ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
                ->where([['proy_cu_detalle.id_cu', '=', $cu->id_cu]])
                ->get();
            $nuevo = array( 'id_cu'=>$cu->id_cu,
                            'codigo'=>$cu->codigo,
                            'descripcion'=>$cu->descripcion,
                            'rendimiento'=>$cu->rendimiento,
                            'abreviatura'=>$cu->abreviatura,
                            'total'=>$cu->total,
                            'estado'=>$cu->estado,
                            'insumos'=>$detalle);
            $new_acus[] = $nuevo;
        }

        return response()->json($new_acus);
    }
    //mostrar acu detalle
    public function mostrar_acu_detalle(Request $request,$id)
    {
        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_tp_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu', '=', $id]])
            ->get();

        return response()->json($detalle);

    }
    public function listar_acu_detalle($id){
        $detalle = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_cu_detalle.*', 'proy_insumo.codigo','proy_insumo.descripcion',
            'proy_insumo.tp_insumo','proy_insumo.codigo as cod_tp_insumo',
            'alm_und_medida.abreviatura','proy_tp_insumo.codigo as cod_tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu', '=', $id]])
            ->get();
        $html = '';
        foreach($detalle as $det){
            $html .='
            <tr id="'.$det->id_cu_detalle.'">
                <td>'.$det->id_insumo.'</td>
                <td>'.$det->codigo.'</td>
                <td>'.$det->descripcion.'</td>
                <td>'.$det->cod_tp_insumo.'</td>
                <td>'.$det->abreviatura.'</td>
                <td>'.$det->cuadrilla.'</td>
                <td>'.$det->cantidad.'</td>
                <td>'.$det->precio_unit.'</td>
                <td>'.$det->precio_total.'</td>
                <td>
                    <button class="btn btn-success boton oculto" onClick="update('.$det->id_insumo.');"><i class="fas fa-save"></i></button>
                    <button class="btn btn-danger boton" onClick="anular('.$det->id_insumo.');"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            ';
        }
        return json_encode($html);
    }
    public function next_cod_acu(){
        $data = DB::table('proyectos.proy_cu')
        ->orderBy('codigo','desc')
        ->where('estado',1)
        ->first();
        $codigo = ((int)$data->codigo)+1;
        return $this->leftZero(4,$codigo);
    }
    public function guardar_acu(Request $request)
    {
        $codigo = $this->next_cod_acu();
        $id_cu = DB::table('proyectos.proy_cu')->insertGetId(
            [
                'codigo' => $codigo,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'total' => $request->total_acu,
                'rendimiento' => $request->rendimiento,
                // 'observacion' => $request->observacion,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
            ],
                'id_cu'
            );

            $ids = explode(',',$request->id_insumo);
            $can = explode(',',$request->cantidad);
            $cua = explode(',',$request->cuadrilla);
            $uni = explode(',',$request->unitario);
            $tot = explode(',',$request->total);
            // $ids = $request->id_insumo;
            $count = count($ids);

            for ($i=0; $i<$count; $i++){
                $id_ins     = $ids[$i];
                $cant       = $can[$i];
                $cuad       = $cua[$i];
                $precio_u   = $uni[$i];
                $precio_t   = $tot[$i];

                DB::table('proyectos.proy_cu_detalle')->insert(
                    [
                        'id_cu' => $id_cu,
                        'id_insumo' => $id_ins,
                        'cantidad' => $cant,
                        'cuadrilla' => $cuad,
                        'precio_unit' => $precio_u,
                        'precio_total' => $precio_t,
                        'fecha_registro' => date('Y-m-d H:i:s'),
                        'estado' => 1
                    ]
                );
            }

        return response()->json($id_cu);
    }

    //update_acu
    public function update_acu(Request $request)
    {
        $data = DB::table('proyectos.proy_cu')->where('id_cu', $request->id_cu)
            ->update([
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'total' => $request->total_acu,
                'rendimiento' => $request->rendimiento,
                // 'observacion' => $request->observacion,
                // 'estado' => 1,
                // 'fecha_registro' => date('Y-m-d H:i:s'),
            ]);

        $id = explode(',',$request->id_det);
        $ids = explode(',',$request->id_insumo);
        $can = explode(',',$request->cantidad);
        $cua = explode(',',$request->cuadrilla);
        $uni = explode(',',$request->unitario);
        $tot = explode(',',$request->total);

        $count = count($ids);

        for ($i=0; $i<$count; $i++){
            $id_det     = $id[$i];
            $id_ins     = $ids[$i];
            // $unid_med   = $unid_medida[$i];
            $cant       = $can[$i];
            $cuad       = $cua[$i];
            $precio_u   = $uni[$i];
            $precio_t   = $tot[$i];

            if ($id_det === '0'){
                DB::table('proyectos.proy_cu_detalle')->insert(
                    [
                        'id_cu' => $request->id_cu,
                        'id_insumo' => $id_ins,
                        'cantidad' => $cant,
                        'cuadrilla' => $cuad,
                        'precio_unit' => $precio_u,
                        'precio_total' => $precio_t,
                        'fecha_registro' => date('Y-m-d H:i:s'),
                        'estado' => 1
                    ]
                );
            }
            else {
                DB::table('proyectos.proy_cu_detalle')
                ->where('id_cu_detalle', $id_det)
                ->update([
                        'id_insumo' => $id_ins,
                        'cantidad' => $cant,
                        'cuadrilla' => $cuad,
                        'precio_unit' => $precio_u,
                        'precio_total' => $precio_t
                    ]
                );
            }
        }
        $elim = explode(',',$request->det_eliminados);
        $count1 = count($elim);
        if (!empty($request->det_eliminados)){
            for ($i=0; $i<$count1; $i++){
                $id_eli = $elim[$i];
                DB::table('proyectos.proy_cu_detalle')
                ->where('id_cu_detalle', $id_eli)
                ->update([ 'estado' => 2 ]);
            }
        }
        return response()->json($data);
    }
    public function anular_acu($id){
        $data = DB::table('proyectos.proy_cu')->where('id_cu', $id)
            ->update([ 'estado' => 2 ]);

        return response()->json($data);
    }
    public function prueba($eli){
        $data = '';
        $elim = explode(',',$eli);
        $count1 = count($elim);
            if (!empty($eli)){
                $data='elimina';
            // for ($i=0; $i<$count1; $i++){
            //     $id_eli = $elim[$i];
                
                // if ($id_eli !== ''){
                //     DB::table('proyectos.proy_cu_detalle')
                //     ->where('id_cu_detalle', $id_eli)
                //     ->update([ 'estado' => 2 ]);
                // }
        
            // }
        }
        return $data;
    }

   //OPCION COMERCIAL
    public function mostrar_opciones()
    {
        $data = DB::table('proyectos.proy_op_com')
            ->select('proy_op_com.*', 'proy_tp_proyecto.descripcion as des_tp_proyecto',
            'sis_moneda.simbolo','adm_contri.razon_social','sis_usua.usuario')
            ->join('proyectos.proy_tp_proyecto','proy_tp_proyecto.id_tp_proyecto','=','proy_op_com.tp_proyecto')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_op_com.moneda')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com.elaborado_por')
                ->where([['proy_op_com.estado', '=', 1]])
                ->orderBy('proy_op_com.codigo','desc')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }

    public function mostrar_opcion($id)
    {
        $data = DB::table('proyectos.proy_op_com')
            ->select('proy_op_com.*', 'adm_contri.razon_social','sis_usua.usuario')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com.elaborado_por')
                ->where([['proy_op_com.id_op_com', '=', $id]])
                ->get();

        $lecciones = DB::table('proyectos.proy_op_com_lec')
            ->select('proy_op_com_lec.*', 'sis_usua.usuario as nombre_usuario')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_op_com_lec.id_proy_op_com')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com_lec.usuario')
                ->where([['proy_op_com_lec.id_proy_op_com', '=', $id]])
                ->get();
        
        return response()->json(['opcion'=>$data,'lecciones'=>$lecciones]);
    }
    public function nextOpcion($id_emp,$fecha)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_emp = '';
        $result = '';

        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', '=', $id_emp)
        ->get();
        foreach ($emp as $rowEmp) {
            $code_emp = $rowEmp->codigo;
        }
        $data = DB::table('proyectos.proy_op_com')
                ->where('id_empresa', '=', $id_emp)
                // ->whereMonth('fecha_emision', '=', $mes)
                ->whereYear('fecha_emision', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = "OP".$code_emp."-".$anio."".$number;

        return $result;
    }
    public function guardar_opcion(Request $request)
    {
        $codigo = $this->nextOpcion($request->id_empresa, $request->fecha_emision);
        $id_op_com = DB::table('proyectos.proy_op_com')->insertGetId(
            [
                'tp_proyecto' => $request->tp_proyecto,
                'id_empresa' => $request->id_empresa,
                'descripcion' => $request->descripcion,
                'cliente' => $request->cliente,
                'elaborado_por' => $request->elaborado_por,
                'moneda' => $request->moneda,
                'importe' => $request->importe,
                'fecha_emision' => $request->fecha_emision,
                'codigo' => $codigo,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_op_com'
            );

            // $lec = $request->lecciones;
            // $lecArray = json_decode($lec, true);
            // $count_lec = count($lecArray);

            // if ($count_lec > 0){
            //     for ($i=0; $i<$count_lec; $i++){
                    
            //         if ($lecArray[$i]['id_leccion']===0){
            //             DB::table('proyectos.proy_op_com_lec')->insert(
            //                 [
            //                     'id_proy_op_com' => $id_op_com,
            //                     'descripcion'    => $lecArray[$i]['descripcion'],
            //                     'usuario'        => $lecArray[$i]['usuario'],
            //                     'estado'         => $lecArray[$i]['estado'],
            //                     'fecha_registro' => $lecArray[$i]['fecha_registro']
            //                 ]
            //             );
            //         }
            //     }
            // }

        return response()->json($id_op_com);
    }

    public function update_opcion(Request $request)
    {
        // $codigo = $this->nextOpcion($request->empresa,$request->fecha_emision);
        $data = DB::table('proyectos.proy_op_com')->where('id_op_com', $request->id_op_com)
            ->update([
                'tp_proyecto' => $request->tp_proyecto,
                'descripcion' => $request->descripcion,
                'cliente' => $request->cliente,
                'moneda' => $request->moneda,
                'importe' => $request->importe,
                'fecha_emision' => $request->fecha_emision
            ]);

            // $lec = $request->lecciones;
            // $lecArray = json_decode($lec, true);
            // $count_lec = count($lecArray);

            // if ($count_lec > 0){
            //     for ($i=0; $i<$count_lec; $i++){
                    
            //         if ($lecArray[$i]['id_leccion']===0){
            //             DB::table('proyectos.proy_op_com_lec')->insert(
            //                 [
            //                     'id_proy_op_com' => $id,
            //                     'descripcion'    => $lecArray[$i]['descripcion'],
            //                     'usuario'        => $lecArray[$i]['usuario'],
            //                     'estado'         => $lecArray[$i]['estado'],
            //                     'fecha_registro' => $lecArray[$i]['fecha_registro']
            //                 ]
            //             );
            //         }
            //     }
            // }

        return response()->json($data);
    }
    public function anular_opcion(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_op_com')
                ->where('id_op_com',$id)
                ->update([ 'estado' => 2 ]);

        // $detalle = DB::table('proyectos.proy_op_com_lec')
        //         ->where('id_proy_op_com',$id)
        //         ->update([ 'estado' => 2 ]);

        return response()->json($data);
    }
    //LECCIONES APRENDIDAS
    public function mostrar_lecciones(Request $request,$id)
    {
        $detalle = DB::table('proyectos.proy_op_com_lec')
                   ->select('proy_op_com_lec.*', 'sis_usua.usuario as nombre_usuario')
                   ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_op_com_lec.id_proy_op_com')
                   ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_op_com_lec.usuario')
                   ->where([['proy_op_com_lec.id_proy_op_com', '=', $id]])
                   ->get();

       return response()->json($detalle);

    }
    public function guardar_leccion(Request $request)
    {
        $data = DB::table('proyectos.proy_op_com_lec')->insertGetId(
            [
                'id_proy_op_com' => $request->id_proy_op_com,
                'descripcion' => $request->descripcion,
                'usuario' => $request->usuario,
                'estado' => $request->estado,
                'fecha_registro' => $request->fecha_registro
            ],
                'id_leccion'
            );
        return response()->json($data);
    }
    public function update_leccion(Request $request, $id)
    {
        $data = DB::table('proyectos.proy_op_com_lec')->where('id_leccion', $id)
            ->update([
                'id_proy_op_com' => $request->id_proy_op_com,
                'descripcion' => $request->descripcion,
                'usuario' => $request->usuario,
                'estado' => $request->estado,
                'fecha_registro' => date('Y-m-d H:i:s')
            ]);
        return response()->json($data);
    }
    //PROYECTO
    public function listar_proyectos()
    {
        $data = DB::table('proyectos.proy_proyecto')
                ->select('proy_proyecto.*', 'adm_contri.razon_social','proy_modalidad.descripcion as nombre_modalidad',
                'proy_tp_proyecto.descripcion as nombre_tp_proyecto','proy_sis_contrato.descripcion as nombre_sis_contrato',
                'sis_moneda.simbolo','sis_usua.usuario','proy_unid_program.descripcion as des_unid_prog')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('proyectos.proy_modalidad','proy_modalidad.id_modalidad','=','proy_proyecto.modalidad')
                ->join('proyectos.proy_tp_proyecto','proy_tp_proyecto.id_tp_proyecto','=','proy_proyecto.tp_proyecto')
                ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_proyecto.sis_contrato')
                ->join('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_proyecto.unid_program')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_proyecto.moneda')
                ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_proyecto.elaborado_por')
                ->where([['proy_proyecto.estado', '=', 1]])
                ->orderBy('id_proyecto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_proyectos_pendientes($emp,$rol)
    {
        //Lista de flujos con el rol en sesion para proyecto
        $flujos = DB::table('administracion.adm_flujo')
            ->select('adm_flujo.*')
            ->where([['adm_flujo.id_rol','=',$rol],
                    ['adm_flujo.estado','=',1],
                    ['adm_flujo.id_operacion','=',6] //Operacion= 6->Proyecto
                    ])
            ->orderBy('orden')
            ->get();

        //Lista de proyectos pendientes
        $pendientes = DB::table('proyectos.proy_proyecto')
            ->select('proy_proyecto.*','adm_documentos_aprob.id_doc_aprob','adm_contri.razon_social',
            'sis_moneda.simbolo')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_proyecto.moneda')
            ->leftjoin('administracion.adm_documentos_aprob','adm_documentos_aprob.codigo_doc','=','proy_proyecto.codigo')
            ->where([['proy_proyecto.estado','=',1],//elaborado
                    ['proy_proyecto.empresa','=',$emp]])
            ->get();
        
        $lista = [];

        //Nro de flujos que necesita para aprobar el proyecto
        $nro_flujo = DB::table('administracion.adm_flujo')
            ->where([['adm_flujo.estado','=',1],//activo->1
                    ['adm_flujo.id_operacion','=',6]])//proyecto->6
            ->count();

        foreach($pendientes as $proy){
            //Nro de aprobacion que necesita
            $nro_ap = DB::table('administracion.adm_aprobacion')
                ->where([['adm_aprobacion.id_doc_aprob','=',$proy->id_doc_aprob],
                        ['adm_aprobacion.id_vobo','=',1]])
                ->count() + 1;
            //Si el nro total de flujos es >= que el nro de aprobaciones
            if ($nro_flujo >= $nro_ap){
                //Recorre los flujos con mi rol
                foreach($flujos as $flujo){
                    //Si el nro de orden de mi flujo es = nro de aprobacion q necesita
                    if ($flujo->orden === $nro_ap){
                        $nuevo_proy = [
                            "id_proyecto"=>$proy->id_proyecto,
                            "empresa"=>$proy->empresa,
                            "descripcion"=>$proy->descripcion,
                            "cliente"=>$proy->cliente,
                            "razon_social"=>$proy->razon_social,
                            "id_doc_aprob"=>$proy->id_doc_aprob,
                            "simbolo"=>$proy->simbolo,
                            "importe"=>$proy->importe,
                            "fecha_inicio"=>$proy->fecha_inicio,
                            "fecha_fin"=>$proy->fecha_fin,
                            "codigo"=>$proy->codigo,
                            "orden"=>$nro_ap,
                            "id_flujo"=>$flujo->id_flujo
                        ];
                        //agrega el proyecto a la lista
                        array_push($lista,$nuevo_proy);
                    }
                }
            }
        }
        // return response()->json(["lista"=>$lista,"flujos"=>$flujos]);
        return response()->json($lista);
    }
    public function aprobacion_completa($id_doc_aprob)
    {
        $rspta = 0;
        //Nro de flujos que necesita para aprobar el proyecto
        $nro_flujo = DB::table('administracion.adm_flujo')
        ->where([['adm_flujo.estado','=',1],//activo->1
                ['adm_flujo.id_operacion','=',6]])//proyecto->6
        ->count();
        //Nro de aprobacion que necesita
        $nro_ap = DB::table('administracion.adm_aprobacion')
        ->where([['adm_aprobacion.id_doc_aprob','=',$id_doc_aprob],
                ['adm_aprobacion.id_vobo','=',1]])
        ->count();
        //Si el nro de aprobaciones es < que el nro total de flujos
        if ($nro_ap >= $nro_flujo){
            $rspta = 1;
        }
        return $rspta;
    }
    public function guardar_aprobacion(Request $request)
    {
        $id_aprobacion = DB::table('administracion.adm_aprobacion')->insertGetId(
            [
                'id_flujo'=>$request->id_flujo, 
                'id_doc_aprob'=>$request->id_doc_aprob, 
                'id_vobo'=>$request->id_vobo, 
                'id_usuario'=>$request->id_usuario, 
                'id_area'=>$request->id_area, 
                'fecha_vobo'=>$request->fecha_vobo, 
                'detalle_observacion'=>$request->detalle_observacion, 
                'id_rol'=>$request->id_rol
            ],
                'id_aprobacion'
            );
        return response()->json($id_aprobacion);
    }
    public function estado_proyecto($id,$estado)
    {
        $data = DB::table('proyectos.proy_proyecto')
        ->where('id_proyecto', $id)
        ->update([ 'estado' => $estado ]);
        return response()->json($data);
    }
    public function mostrar_proyecto($id)
    {
        $data = DB::table('proyectos.proy_proyecto')
            ->select('proy_proyecto.*','adm_contri.razon_social','sis_usua.usuario as nombre_elaborado')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_proyecto.elaborado_por')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->where([['proy_proyecto.id_proyecto', '=', $id]])
            ->get();

        $contratos = DB::table('proyectos.proy_contrato')
            ->select('proy_contrato.*','sis_moneda.simbolo','proy_tp_contrato.descripcion as tipo_contrato')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
            ->join('proyectos.proy_tp_contrato','proy_tp_contrato.id_tp_contrato','=','proy_contrato.id_tp_contrato')
            ->where([['proy_contrato.id_proyecto', '=', $id]])
            ->get();

        $controles = DB::table('proyectos.proy_ctrl_fechas')
            ->select('proy_ctrl_fechas.*')
            ->where([['proy_ctrl_fechas.id_proyecto', '=', $id]])
            ->get();

        $aprobaciones = DB::table('administracion.adm_aprobacion')
            ->select('adm_aprobacion.*','adm_flujo.nombre as nombre_flujo','adm_vobo.descripcion','sis_usua.usuario')
            ->join('administracion.adm_documentos_aprob','adm_documentos_aprob.id_doc_aprob','=','adm_aprobacion.id_doc_aprob')
            ->join('proyectos.proy_proyecto','adm_documentos_aprob.codigo_doc','=','proy_proyecto.codigo')
            ->join('administracion.adm_flujo','adm_flujo.id_flujo','=','adm_aprobacion.id_flujo')
            ->join('administracion.adm_vobo','adm_vobo.id_vobo','=','adm_aprobacion.id_vobo')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','adm_aprobacion.id_usuario')
            ->where([['proy_proyecto.id_proyecto', '=', $id]])
            ->get();

        return response()->json(["proyecto"=>$data,"contratos"=>$contratos,"controles"=>$controles,"aprobaciones"=>$aprobaciones]);
    }
    public function mostrar_proy_contratos()
    {
        $data = DB::table('proyectos.proy_contrato')
                ->select('proy_contrato.*',//'proy_contrato.nro_contrato','proy_contrato.moneda','proy_contrato.importe',
                'proy_proyecto.descripcion','adm_contri.razon_social',//'proy_contrato.fecha_contrato',
                'sis_moneda.simbolo','proy_proyecto.id_op_com','proy_proyecto.empresa')
                ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
                ->where([['proy_contrato.estado', '=', 1]])
                // ->orderBy('nro_contrato')
                ->get();
        return response()->json($data);
    }
    public function mostrar_proyectos_contratos()
    {
        $data = DB::table('proyectos.proy_contrato')
                ->select('proy_contrato.*',
                'proy_proyecto.descripcion','adm_contri.razon_social',
                'sis_moneda.simbolo','proy_proyecto.id_op_com','proy_proyecto.empresa',
                'proy_presup.id_presupuesto','proy_presup.codigo')
                ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
                ->join('proyectos.proy_presup','proy_presup.id_contrato','=','proy_contrato.id_contrato')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
                ->where([['proy_contrato.estado', '=', 1]])
                ->get();
        return response()->json(['data'=>$data]);
    }
    public function mostrar_contrato($id)
    {
        $data = DB::table('proyectos.proy_contrato')
                ->select('proy_contrato.id_contrato','proy_contrato.nro_contrato','proy_contrato.moneda',
                'proy_proyecto.descripcion','adm_contri.razon_social','proy_contrato.fecha_contrato',
                'sis_moneda.simbolo','proy_contrato.importe','proy_proyecto.id_op_com','proy_proyecto.empresa')
                ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
                ->where([['proy_contrato.id_contrato', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function nextProyecto($id_emp,$fecha)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_emp = '';
        $result = '';

        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', '=', $id_emp)
        ->get();
        foreach ($emp as $rowEmp) {
            $code_emp = $rowEmp->codigo;
        }
        $data = DB::table('proyectos.proy_proyecto')
                ->where([['empresa','=',$id_emp]])
                // ->whereMonth('fecha_inicio', '=', $mes)
                ->whereYear('fecha_inicio', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = "PY".$code_emp."-".$anio."".$number;

        return $result;
    }
    public function guardar_proyecto(Request $request)
    {
        $codigo = $this->nextProyecto($request->empresa, $request->fecha_inicio);

        $id_proyecto = DB::table('proyectos.proy_proyecto')->insertGetId(
            [
                'tp_proyecto' => $request->tp_proyecto,
                'empresa' => $request->id_empresa,
                'descripcion' => $request->descripcion,
                'cliente' => $request->cliente,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'elaborado_por' => $request->elaborado_por,
                'codigo' => $codigo,
                'modalidad' => $request->modalidad,
                'sis_contrato' => $request->sis_contrato,
                'moneda' => $request->moneda,
                'plazo_ejecucion' => $request->plazo_ejecucion,
                'unid_program' => $request->unid_program,
                'id_op_com' => $request->id_op_com,
                'importe' => $request->importe,
                'jornal' => $request->jornal,
                'estado' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ],
                'id_proyecto'
            );

        // $ids = $request->c_id_contrato;
        // $count = count($ids);

        // for ($i=0; $i<$count; $i++){
        //     $id_contrato     = $request->c_id_contrato[$i];
        //     $nro_contrato    = $request->c_nro_contrato[$i];
        //     $fecha_contrato  = $request->c_fecha_contrato[$i];
        //     $descripcion     = $request->c_descripcion[$i];
        //     $moneda          = $request->c_moneda[$i];
        //     $importe         = $request->c_importe[$i];
        //     $archivo_adjunto = $request->c_archivo_adjunto[$i];
        //     // $id_proyecto     = $request->c_id_proyecto[$i];
        //     $id_tp_contrato  = $request->c_id_tp_contrato[$i];
        //     $estado          = $request->c_estado[$i];
        //     $fecha_registro  = $request->c_fecha_registro[$i];

        //     DB::table('proyectos.proy_contrato')->insert(
        //         [
        //             'nro_contrato' => $nro_contrato,
        //             'fecha_contrato' => $fecha_contrato,
        //             'descripcion' => $descripcion,
        //             'moneda' => $moneda,
        //             'importe' => $importe,
        //             'archivo_adjunto' => $archivo_adjunto,
        //             'id_proyecto' => $id_proyecto,
        //             'id_tp_contrato' => $id_tp_contrato,
        //             'estado' => $estado,
        //             'fecha_registro' => $fecha_registro
        //         ]
        //     );
        // }

        // $idc = $request->ctl_id_control;
        // $count1 = count($idc);

        // for ($i=0; $i<$count1; $i++){

        //     $id_control     = $request->ctl_id_control[$i];
        //     // $id_proyecto    = $request->ctl_id_proyecto[$i];
        //     $fecha_inicio   = $request->ctl_fecha_inicio[$i];
        //     $fecha_fin      = $request->ctl_fecha_fin[$i];
        //     $descripcion    = $request->ctl_descripcion[$i];
        //     $estado         = $request->ctl_estado[$i];
        //     $fecha_registro = $request->ctl_fecha_registro[$i];

        //     DB::table('proyectos.proy_ctrl_fechas')->insert(
        //         [
        //             'id_proyecto'    => $id_proyecto,
        //             'fecha_inicio'   => $fecha_inicio,
        //             'fecha_fin'      => $fecha_fin,
        //             'descripcion'    => $descripcion,
        //             'estado'         => $estado,
        //             'fecha_registro' => $fecha_registro
        //         ]
        //     );
        // }

        return response()->json($id_proyecto);
    }
    public function actualizar_proyecto(Request $request)
    {
        $data = DB::table('proyectos.proy_proyecto')->where('id_proyecto', $request->id_proyecto)
        ->update([
            'tp_proyecto' => $request->tp_proyecto,
            'descripcion' => $request->descripcion,
            'cliente' => $request->cliente,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'modalidad' => $request->modalidad,
            'sis_contrato' => $request->sis_contrato,
            'moneda' => $request->moneda,
            'plazo_ejecucion' => $request->plazo_ejecucion,
            'unid_program' => $request->unid_program,
            'id_op_com' => $request->id_op_com,
            'importe' => $request->importe
        ]);

        // $ids = $request->c_id_contrato;
        // $count = count($ids);

        // for ($i=0; $i<$count; $i++){
        //     $id_contrato     = $request->c_id_contrato[$i];
        //     $nro_contrato    = $request->c_nro_contrato[$i];
        //     $fecha_contrato  = $request->c_fecha_contrato[$i];
        //     $descripcion     = $request->c_descripcion[$i];
        //     $moneda          = $request->c_moneda[$i];
        //     $importe         = $request->c_importe[$i];
        //     $archivo_adjunto = $request->c_archivo_adjunto[$i];
        //     // $id_proyecto     = $request->c_id_proyecto[$i];
        //     $id_tp_contrato  = $request->c_id_tp_contrato[$i];
        //     $estado          = $request->c_estado[$i];
        //     $fecha_registro  = $request->c_fecha_registro[$i];

        //     if ($id_contrato === 0){
        //         DB::table('proyectos.proy_contrato')->insert(
        //             [
        //                 'id_proyecto' => $id,
        //                 'nro_contrato' => $nro_contrato,
        //                 'fecha_contrato' => $fecha_contrato,
        //                 'descripcion' => $descripcion,
        //                 'moneda' => $moneda,
        //                 'importe' => $importe,
        //                 'archivo_adjunto' => $archivo_adjunto,
        //                 'id_tp_contrato' => $id_tp_contrato,
        //                 'estado' => $estado,
        //                 'fecha_registro' => $fecha_registro
        //             ]
        //         );
        //     }
        //     else {
        //         DB::table('proyectos.proy_contrato')->where('id_contrato', $id_contrato)
        //         ->update([
        //                 'id_proyecto' => $id,
        //                 'nro_contrato' => $nro_contrato,
        //                 'fecha_contrato' => $fecha_contrato,
        //                 'descripcion' => $descripcion,
        //                 'moneda' => $moneda,
        //                 'importe' => $importe,
        //                 'archivo_adjunto' => $archivo_adjunto,
        //                 'id_tp_contrato' => $id_tp_contrato,
        //                 'estado' => $estado,
        //                 // 'fecha_registro' => $fecha_registro
        //             ]
        //         );
        //     }
        // }

        // $idc = $request->ctl_id_control;
        // $count1 = count($idc);

        // for ($i=0; $i<$count1; $i++){

        //     $id_control     = $request->ctl_id_control[$i];
        //     // $id_proyecto    = $request->ctl_id_proyecto[$i];
        //     $fecha_inicio   = $request->ctl_fecha_inicio[$i];
        //     $fecha_fin      = $request->ctl_fecha_fin[$i];
        //     $descripcion    = $request->ctl_descripcion[$i];
        //     $estado         = $request->ctl_estado[$i];
        //     $fecha_registro = $request->ctl_fecha_registro[$i];

        //     if ($id_control === 0){

        //         DB::table('proyectos.proy_ctrl_fechas')->insert(
        //             [
        //                 'id_proyecto'    => $id,
        //                 'fecha_inicio'   => $fecha_inicio,
        //                 'fecha_fin'      => $fecha_fin,
        //                 'descripcion'    => $descripcion,
        //                 'estado'         => $estado,
        //                 'fecha_registro' => $fecha_registro
        //             ]
        //         );
        //     }
        //     else {
        //         DB::table('proyectos.proy_ctrl_fechas')->where('id_control', $id_control)
        //         ->update(
        //             [
        //                 'id_proyecto'    => $id,
        //                 'fecha_inicio'   => $fecha_inicio,
        //                 'fecha_fin'      => $fecha_fin,
        //                 'descripcion'    => $descripcion,
        //                 'estado'         => $estado
        //                 // 'fecha_registro' => $fecha_registro
        //             ]
        //         );
        //     }
        // }

        return response()->json($data);
    }
    public function anular_proyecto(Request $request,$id)
    {
        $data = DB::table('proyectos.proy_proyecto')->where('id_proyecto', $id)
        ->update([ 'estado' => 2 ]);
        return response()->json($data);
    }

    //  PRESUPUESTO INTERNO
    public function mostrar_presupuestos_cabecera()
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_presup.codigo','proy_op_com.descripcion', 
            'adm_contri.razon_social')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_presup.estado', '=', 1]])
                ->orderBy('id_presupuesto')
                ->get();
        return response()->json($data);
    }
    public function mostrar_presupuesto_cabecera($id)
    {
        $data = DB::table('proyectos.proy_presup')
        ->select('proy_presup.codigo','proy_presup.fecha_emision','proy_proyecto.descripcion',
        'sis_moneda.simbolo as moneda','adm_contri.razon_social','proy_presup_importe.*',
        'proy_unid_program.descripcion as des_unid_program','proy_proyecto.plazo_ejecucion')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')

            ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->join('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_proyecto.unid_program')
            
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->get();

        return response()->json($data);
    }
    public function mostrar_presupuesto_cabecera2($id)
    {
        $data = DB::table('proyectos.proy_presup')
        ->select('proy_presup.codigo','proy_presup.fecha_emision',
        'sis_moneda.simbolo as moneda','adm_contri.razon_social','proy_presup_importe.*',
        'proy_proyecto.plazo_ejecucion','proy_op_com.descripcion as nombre_opcion')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')

            // ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
            // ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            // ->join('proyectos.proy_unid_program','proy_unid_program.id_unid_program','=','proy_proyecto.unid_program')
            
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->get();

        return response()->json($data);
    }
    public function mostrar_pres_acu($id)
    {
        $presupuesto = DB::table('proyectos.proy_presup')
            ->select('proy_presup.codigo', 'proy_op_com.descripcion as nombre_opcion', 'proy_presup.fecha_emision',
            'sis_moneda.simbolo as moneda', 'adm_contri.razon_social','proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg')
                ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
                ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                    ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_presup.id_presupuesto', '=', $id]])
                ->first();
        
        $tipos = DB::table('proyectos.proy_tp_insumo')
        ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.codigo','proy_tp_insumo.descripcion')
            ->get();

        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.codigo','proy_cd_partida.descripcion','alm_und_medida.abreviatura',
            'proy_cu.rendimiento','proy_cu.total as cu_total','proy_cu.id_cu')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
                ->where([['proy_cd_partida.id_cd', '=', $presupuesto->id_cd],
                        ['proy_cd_partida.estado','=',1]])
                ->distinct();
                
        $part_ci = DB::table('proyectos.proy_ci_detalle')
        ->select('proy_ci_detalle.codigo','proy_ci_detalle.descripcion','alm_und_medida.abreviatura',
        'proy_cu.rendimiento','proy_cu.total as cu_total','proy_cu.id_cu')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
        ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_ci_detalle.id_cu')
            ->where([['proy_ci_detalle.id_ci', '=', $presupuesto->id_ci],
                    ['proy_ci_detalle.estado','=',1]])
            ->distinct()
            ->unionAll($part_cd);
            
        $part_gg = DB::table('proyectos.proy_gg_detalle')
        ->select('proy_gg_detalle.codigo','proy_gg_detalle.descripcion','alm_und_medida.abreviatura',
        'proy_cu.rendimiento','proy_cu.total as cu_total','proy_cu.id_cu')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
        ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_gg_detalle.id_cu')
            ->where([['proy_gg_detalle.id_gg', '=', $presupuesto->id_gg],
                    ['proy_gg_detalle.estado','=',1]])
            ->distinct()
            ->unionAll($part_ci)
            ->get()
            ->toArray();

        $partidas = [];
        $array = [];

        foreach ($part_gg as $partida){
            $insumos = DB::table('proyectos.proy_cu_detalle')
            ->select('proy_insumo.codigo','proy_insumo.descripcion','alm_und_medida.abreviatura',
            'proy_cu_detalle.cantidad','proy_cu_detalle.cuadrilla','proy_cu_detalle.precio_unit',
            'proy_cu_detalle.precio_total','proy_insumo.tp_insumo')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cu_detalle.id_cu','=',$partida->id_cu]])
            ->get()
            ->toArray();

            $new_tipos = [];

            foreach($tipos as $tipo){
                $sum = 0;
                $array = [];
                foreach ($insumos as $row){
                    if ($tipo->id_tp_insumo == $row->tp_insumo){
                        $sum += $row->precio_total;
                        $array[] = $row;
                    }
                }
                $nuevo = array( 'id_tp_insumo'=>$tipo->id_tp_insumo,
                                'codigo'=>$tipo->codigo,
                                'descripcion'=>$tipo->descripcion,
                                'suma'=>$sum,
                                'insumos'=>$array);
                if ($sum > 0){
                    $new_tipos[] = $nuevo;
                }
            }

            $nuevo = [
                "codigo"=>$partida->codigo,
                "descripcion"=>$partida->descripcion,
                "abreviatura"=>$partida->abreviatura,
                "rendimiento"=>$partida->rendimiento,
                "cu_total"=>$partida->cu_total,
                "id_cu"=>$partida->id_cu,
                "tipos"=>$new_tipos
            ];
            $array = [];
            array_push($partidas,$nuevo);
        }

        return response()->json(["presupuesto"=>$presupuesto,"partidas"=>$partidas]);
    }
    public function mostrar_pres_completo($id_presupuesto)
    {
        $presupuesto = DB::table('proyectos.proy_presup')
            ->select('proy_presup.codigo','proy_presup.fecha_emision',
            'proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg','proy_op_com.descripcion as nombre_opcion',
            'sis_moneda.simbolo as moneda','adm_contri.razon_social','proy_presup_importe.*')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')                
                ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_presup.id_presupuesto', '=', $id_presupuesto]])
                ->first();
                
        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*',//'proy_sis_contrato.descripcion as nombre_sistema',
            'alm_und_medida.abreviatura','proy_cu.rendimiento','proy_cd_pcronog.dias',
            'proy_cd_pcronog.fecha_inicio','proy_cd_pcronog.fecha_fin')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
            ->join('proyectos.proy_cd_pcronog','proy_cd_pcronog.id_partida','=','proy_cd_partida.id_partida')
                ->where([['proy_cd_partida.id_cd','=',$presupuesto->id_cd],
                            ['proy_cd_pcronog.estado','=',1]])
                ->get()
                ->toArray();

        $part_ci = DB::table('proyectos.proy_ci_detalle')
                ->select('proy_ci_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento',
                'proy_ci_pcronog.dias','proy_ci_pcronog.fecha_inicio','proy_ci_pcronog.fecha_fin')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_ci_detalle.id_cu')
                ->join('proyectos.proy_ci_pcronog','proy_ci_pcronog.id_partida','=','proy_ci_detalle.id_ci_detalle')
                ->where([['proy_ci_detalle.id_ci','=',$presupuesto->id_ci],
                         ['proy_ci_pcronog.estado','=',1]])
                    ->get()
                    ->toArray();

        $part_gg = DB::table('proyectos.proy_gg_detalle')
                ->select('proy_gg_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento',
                'proy_gg_pcronog.dias','proy_gg_pcronog.fecha_inicio','proy_gg_pcronog.fecha_fin')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_gg_detalle.id_cu')
                ->join('proyectos.proy_gg_pcronog','proy_gg_pcronog.id_partida','=','proy_gg_detalle.id_gg_detalle')
                ->where([['proy_gg_detalle.id_gg', '=', $presupuesto->id_gg],
                         ['proy_gg_pcronog.estado','=',1]])
                    ->get()
                    ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
                ->where([['proy_cd_compo.id_cd', '=', $presupuesto->id_cd]])
                ->get()->toArray();

        $componentes_cd = [];
        $array = [];

        foreach ($compo_cd as $comp){
            $total = 0;
            foreach($part_cd as $partidax){
                if ($comp->codigo == $partidax->cod_compo){
                    array_push($array, $partidax);
                    $total += $partidax->importe_parcial;
                }
            }

            $nuevo_comp = [
                "id_cd_compo"=>$comp->id_cd_compo,
                "id_cd"=>$comp->id_cd,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];

            $array = [];
            array_push($componentes_cd,$nuevo_comp);
        }
        
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
                ->where([['proy_ci_compo.id_ci', '=', $presupuesto->id_ci]])
                ->get();

        $componentes_ci = [];
        $array = [];

        foreach ($compo_ci as $comp){
            $total = 0;
            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_ci_compo"=>$comp->id_ci_compo,
                "id_ci"=>$comp->id_ci,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_ci,$nuevo_comp);
        }

        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
                ->where([['proy_gg_compo.id_gg', '=', $presupuesto->id_gg]])
                ->get();

        $componentes_gg = [];
        $array = [];

        foreach ($compo_gg as $comp){
            $total = 0;
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_gg_compo"=>$comp->id_gg_compo,
                "id_gg"=>$comp->id_gg,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_gg,$nuevo_comp);
        }

        $cd = ["id_cd"=>$presupuesto->id_cd,"componentes_cd"=>$componentes_cd,"partidas_cd"=>$part_cd];
        $ci = ["id_ci"=>$presupuesto->id_ci,"componentes_ci"=>$componentes_ci,"partidas_ci"=>$part_ci];
        $gg = ["id_gg"=>$presupuesto->id_gg,"componentes_gg"=>$componentes_gg,"partidas_gg"=>$part_gg];

        return response()->json(["presupuesto"=>$presupuesto,"cd"=>$cd,"ci"=>$ci,"gg"=>$gg]);
    }

    public function mostrar_presup_ejecucion()
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_contrato.nro_contrato','proy_proyecto.id_proyecto',
            'proy_proyecto.descripcion','proy_presup.codigo','adm_contri.razon_social')
            ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_presup.estado', '=', 1],['proy_presup.id_tp_presupuesto', '=', 3]])
                ->orderBy('id_presupuesto')
                ->get();
        return response()->json($data);
    }
    public function mostrar_presup_ejecucion_contrato($id_proyecto)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_contrato.nro_contrato','proy_proyecto.id_proyecto',
            'proy_proyecto.descripcion','proy_presup.codigo','adm_contri.razon_social')
            ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_presup.estado', '=', 1],
                        ['proy_presup.id_tp_presupuesto', '=', 3],
                        ['proy_proyecto.id_proyecto','=',$id_proyecto]])
                ->get();
        return response()->json($data);
    }
    public function mostrar_presupuesto($id)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_presup.codigo','proy_presup.fecha_emision',
            'proy_presup.id_tp_presupuesto','proy_op_com.descripcion as nombre_opcion', 'proy_presup.moneda',
            'proy_presup.id_op_com','sis_moneda.simbolo','adm_contri.razon_social','proy_presup.id_empresa',
            'proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg','proy_presup_importe.*')
                ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
                ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
                ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->leftjoin('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->leftjoin('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->leftjoin('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->leftjoin('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_presupuesto', '=', $id]])//Tipo-> Pres.Interno
                ->get();
        return response()->json($data);
    }
    public function mostrar_todo_presint($id_op_com)
    {
        $pres_int = DB::table('proyectos.proy_presup')
            ->select('proy_presup.moneda','proy_presup_importe.*',
            'proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_op_com', '=', $id_op_com],
                         ['proy_presup.id_tp_presupuesto', '=', 1]])//Pres.Interno
                ->orderBy('id_presupuesto','desc')
                ->first();

        $part_cd = DB::table('proyectos.proy_cd_partida')
                ->select('proy_cd_partida.*','proy_sis_contrato.descripcion as nombre_sistema',
                'alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_cd_partida.id_sistema')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
                    ->where([['proy_cd_partida.id_cd', '=', $pres_int->id_cd]])
                    ->get()
                    ->toArray();

        $part_ci = DB::table('proyectos.proy_ci_detalle')
                ->select('proy_ci_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_ci_detalle.id_cu')
                ->where([['proy_ci_detalle.id_ci', '=', $pres_int->id_ci]])
                    ->get()
                    ->toArray();

        $part_gg = DB::table('proyectos.proy_gg_detalle')
                ->select('proy_gg_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_gg_detalle.id_cu')
                ->where([['proy_gg_detalle.id_gg', '=', $pres_int->id_gg]])
                    ->get()
                    ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
                ->where([['proy_cd_compo.id_cd', '=', $pres_int->id_cd]])
                ->get();

        $componentes_cd = [];
        $array = [];

        foreach ($compo_cd as $comp){
            $total = 0;
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }

            $nuevo_comp = [
                "id_cd_compo"=>$comp->id_cd_compo,
                "id_cd"=>$comp->id_cd,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];

            $array = [];
            array_push($componentes_cd,$nuevo_comp);
        }
        
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
                ->where([['proy_ci_compo.id_ci', '=', $pres_int->id_ci]])
                ->get();

        $componentes_ci = [];
        $array = [];

        foreach ($compo_ci as $comp){
            $total = 0;
            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_ci_compo"=>$comp->id_ci_compo,
                "id_ci"=>$comp->id_ci,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_ci,$nuevo_comp);
        }

        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
                ->where([['proy_gg_compo.id_gg', '=', $pres_int->id_gg]])
                ->get();

        $componentes_gg = [];
        $array = [];

        foreach ($compo_gg as $comp){
            $total = 0;
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_gg_compo"=>$comp->id_gg_compo,
                "id_gg"=>$comp->id_gg,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_gg,$nuevo_comp);
        }

        $cd = ["id_cd"=>$pres_int->id_cd,"componentes_cd"=>$componentes_cd,"partidas_cd"=>$part_cd];
        $ci = ["id_ci"=>$pres_int->id_ci,"componentes_ci"=>$componentes_ci,"partidas_ci"=>$part_ci];
        $gg = ["id_gg"=>$pres_int->id_gg,"componentes_gg"=>$componentes_gg,"partidas_gg"=>$part_gg];

        return response()->json(["presupuesto"=>$pres_int,"cd"=>$cd,"ci"=>$ci,"gg"=>$gg]);
    }
    public function mostrar_todo_propuesta($id)
    {
        $propuesta = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*','proy_presup_importe.*','proy_op_com.descripcion as nombre_opcion',
            'proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg','adm_contri.razon_social','sis_moneda.simbolo')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
                ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_presupuesto', '=', $id]])
                ->first();
                
        $pi_importe = DB::table('proyectos.proy_presup')
            ->select('proy_presup_importe.*')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_op_com', '=', $propuesta->id_op_com],
                         ['proy_presup.id_tp_presupuesto','=',1]])//Pres.Interno
                ->orderBy('proy_presup.id_presupuesto','desc')
                ->first();

        $part_cd = DB::table('proyectos.proy_cd_partida')
                ->select('proy_cd_partida.*','proy_sis_contrato.descripcion as nombre_sistema',
                'alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_cd_partida.id_sistema')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
                    ->where([['proy_cd_partida.id_cd', '=', $propuesta->id_cd]])
                    ->get()
                    ->toArray();

        $part_ci = DB::table('proyectos.proy_ci_detalle')
                ->select('proy_ci_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_ci_detalle.id_cu')
                ->where([['proy_ci_detalle.id_ci', '=', $propuesta->id_ci]])
                    ->get()
                    ->toArray();

        $part_gg = DB::table('proyectos.proy_gg_detalle')
                ->select('proy_gg_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_gg_detalle.id_cu')
                ->where([['proy_gg_detalle.id_gg', '=', $propuesta->id_gg]])
                    ->get()
                    ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
                ->where([['proy_cd_compo.id_cd', '=', $propuesta->id_cd]])
                ->get();

        $componentes_cd = [];
        $array = [];

        foreach ($compo_cd as $comp){
            $total = 0;
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }

            $nuevo_comp = [
                "id_cd_compo"=>$comp->id_cd_compo,
                "id_cd"=>$comp->id_cd,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];

            $array = [];
            array_push($componentes_cd,$nuevo_comp);
        }
        
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
                ->where([['proy_ci_compo.id_ci', '=', $propuesta->id_ci]])
                ->get();

        $componentes_ci = [];
        $array = [];

        foreach ($compo_ci as $comp){
            $total = 0;
            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_ci_compo"=>$comp->id_ci_compo,
                "id_ci"=>$comp->id_ci,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_ci,$nuevo_comp);
        }

        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
                ->where([['proy_gg_compo.id_gg', '=', $propuesta->id_gg]])
                ->get();

        $componentes_gg = [];
        $array = [];

        foreach ($compo_gg as $comp){
            $total = 0;
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_gg_compo"=>$comp->id_gg_compo,
                "id_gg"=>$comp->id_gg,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_gg,$nuevo_comp);
        }

        $cd = ["id_cd"=>$propuesta->id_cd,"componentes_cd"=>$componentes_cd,"partidas_cd"=>$part_cd];
        $ci = ["id_ci"=>$propuesta->id_ci,"componentes_ci"=>$componentes_ci,"partidas_ci"=>$part_ci];
        $gg = ["id_gg"=>$propuesta->id_gg,"componentes_gg"=>$componentes_gg,"partidas_gg"=>$part_gg];

        return response()->json(["presupuesto"=>$propuesta,"pi_importe"=>$pi_importe,"cd"=>$cd,"ci"=>$ci,"gg"=>$gg]);
    }

    //Componentes C.D.
    public function getComponentesCDByPresupuesto($id)
    {
        $compo = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
                ->where([['proy_cd_compo.id_cd', '=', $id]])
                ->get();

        return response()->json($compo);
    }

    public function getComponentesCIByPresupuesto($id)
    {
        $data = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
                ->where([['proy_ci_compo.id_ci', '=', $id]])
                ->get()
                ->toArray();
        return response()->json($data);
    }

    public function getComponentesGGByPresupuesto($id)
    {
        $data = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
                ->where([['proy_gg_compo.id_gg', '=', $id]])
                ->get()
                ->toArray();
        return response()->json($data);
    }
    //Partidas C.D.
    public function getPartidasCDByPresupuesto($id)
    {
        $data = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*',
            'proy_sis_contrato.descripcion as nombre_sistema',
            'alm_und_medida.abreviatura','proy_cu.rendimiento')
            ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_cd_partida.id_sistema')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
            // ->join('proyectos.proy_obs','proy_obs.id_cd_partida','=','proy_cd_partida.id_partida')
                ->where([['proy_cd_partida.id_cd', '=', $id]])
                ->orderBy('codigo')
                ->get()
                ->toArray();

        return response()->json($data);
    }

    public function getPartidasCIByPresupuesto($id)
    {
        $data = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_ci_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_ci_detalle.id_cu')
            ->where([['proy_ci_detalle.id_ci', '=', $id]])
            ->orderBy('codigo')
                ->get()
                ->toArray();
        return response()->json($data);
    }

    public function getPartidasGGByPresupuesto($id)
    {
        $data = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_gg_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_gg_detalle.id_cu')
            ->where([['proy_gg_detalle.id_gg', '=', $id]])
            ->orderBy('codigo')
                ->get()
                ->toArray();
        return response()->json($data);
    }
    public function getPartidas_Presupuesto($id_presupuesto)
    {
        $data = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*',
            'proy_sis_contrato.descripcion as nombre_sistema',
            'alm_und_medida.abreviatura','proy_cu.rendimiento')
            ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_cd_partida.id_sistema')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
            ->join('proyectos.proy_cd','proy_cd.id_cd','=','proy_cd_partida.id_cd')
                ->where([['proy_cd.id_presupuesto', '=', $id_presupuesto]])
                ->get()
                ->toArray();
        return response()->json($data);
    }
    public function mostrar_presupuesto_completo($id_presupuesto)
    {
        $presupuesto = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*','proy_presup_importe.*',
            'proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg','proy_contrato.nro_contrato','proy_contrato.fecha_contrato',
            'proy_proyecto.descripcion','sis_moneda.simbolo','proy_contrato.importe','adm_contri.razon_social')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_presup.id_contrato')
                ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
                ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
                ->where([['proy_presup.id_presupuesto', '=', $id_presupuesto]])
                ->first();
                
        $pc_importe = DB::table('proyectos.proy_presup')
                ->select('proy_presup_importe.*')
                    ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                    ->where([['proy_presup.id_op_com', '=', $presupuesto->id_op_com],
                             ['proy_presup.id_tp_presupuesto','=',2]])//Propuesta
                    ->orderBy('proy_presup.id_presupuesto','desc')
                    ->first();

        $part_cd = DB::table('proyectos.proy_cd_partida')
                ->select('proy_cd_partida.*','proy_sis_contrato.descripcion as nombre_sistema',
                'alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_cd_partida.id_sistema')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
                    ->where([['proy_cd_partida.id_cd', '=', $presupuesto->id_cd]])
                    ->get()
                    ->toArray();

        $partidas_cd = array();

        foreach($part_cd as $partida){
            $obs = DB::table('proyectos.proy_obs')
                ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
                ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where('proy_obs.id_cd_partida', $partida->id_partida)
                ->orderBy('proy_obs.fecha_registro')
                ->get()
                ->toArray();

            $nuevo_part = array(
                "id_partida"=>$partida->id_partida,
                "id_cd"=>$partida->id_cd,
                "id_cu"=>$partida->id_cu,
                "codigo"=>$partida->codigo,
                "descripcion"=>$partida->descripcion,
                "unid_medida"=>$partida->unid_medida,
                "cantidad"=>$partida->cantidad,
                "importe_unitario"=>$partida->importe_unitario,
                "importe_parcial"=>$partida->importe_parcial,
                "id_sistema"=>$partida->id_sistema,
                "cod_compo"=>$partida->cod_compo,
                "estado"=>$partida->estado,
                "fecha_registro"=>$partida->fecha_registro,
                "imagen"=>$partida->imagen,
                "nombre_sistema"=>$partida->nombre_sistema,
                "abreviatura"=>$partida->abreviatura,
                "rendimiento"=>$partida->rendimiento,
                "observaciones"=>$obs
            );
            array_push($partidas_cd, $nuevo_part);
        }

        $part_ci = DB::table('proyectos.proy_ci_detalle')
                ->select('proy_ci_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_ci_detalle.id_cu')
                ->where([['proy_ci_detalle.id_ci', '=', $presupuesto->id_ci]])
                    ->get()
                    ->toArray();

        $partidas_ci = [];

        foreach($part_ci as $partida){
            $obs = DB::table('proyectos.proy_obs')
                ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
                ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where('proy_obs.id_ci_detalle', $partida->id_ci_detalle)
                ->orderBy('proy_obs.fecha_registro')
                ->get()
                ->toArray();

            $nuevo_part = [
                "id_ci_detalle"=>$partida->id_ci_detalle,
                "id_ci"=>$partida->id_ci,
                "id_cu"=>$partida->id_cu,
                "codigo"=>$partida->codigo,
                "descripcion"=>$partida->descripcion,
                "unid_medida"=>$partida->unid_medida,
                "cantidad"=>$partida->cantidad,
                "importe_unitario"=>$partida->importe_unitario,
                "importe_parcial"=>$partida->importe_parcial,
                "cod_compo"=>$partida->cod_compo,
                "estado"=>$partida->estado,
                "fecha_registro"=>$partida->fecha_registro,
                "participacion"=>$partida->participacion,
                "tiempo"=>$partida->tiempo,
                "veces"=>$partida->veces,
                "abreviatura"=>$partida->abreviatura,
                "rendimiento"=>$partida->rendimiento,
                "observaciones"=>$obs
            ];
            array_push($partidas_ci, $nuevo_part);
        }

        $part_gg = DB::table('proyectos.proy_gg_detalle')
                ->select('proy_gg_detalle.*','alm_und_medida.abreviatura','proy_cu.rendimiento')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_gg_detalle.id_cu')
                ->where([['proy_gg_detalle.id_gg', '=', $presupuesto->id_gg]])
                    ->get()
                    ->toArray();

        $partidas_gg = [];

        foreach($part_gg as $partida){
            $obs = DB::table('proyectos.proy_obs')
                ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
                ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where('proy_obs.id_gg_detalle', $partida->id_gg_detalle)
                ->orderBy('proy_obs.fecha_registro')
                ->get()
                ->toArray();

            $nuevo_part = [
                "id_gg_detalle"=>$partida->id_gg_detalle,
                "id_gg"=>$partida->id_gg,
                "id_cu"=>$partida->id_cu,
                "codigo"=>$partida->codigo,
                "descripcion"=>$partida->descripcion,
                "unid_medida"=>$partida->unid_medida,
                "cantidad"=>$partida->cantidad,
                "importe_unitario"=>$partida->importe_unitario,
                "importe_parcial"=>$partida->importe_parcial,
                "cod_compo"=>$partida->cod_compo,
                "estado"=>$partida->estado,
                "fecha_registro"=>$partida->fecha_registro,
                "participacion"=>$partida->participacion,
                "tiempo"=>$partida->tiempo,
                "veces"=>$partida->veces,
                "abreviatura"=>$partida->abreviatura,
                "rendimiento"=>$partida->rendimiento,
                "observaciones"=>$obs
            ];
            array_push($partidas_gg, $nuevo_part);
        }

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
                ->where([['proy_cd_compo.id_cd', '=', $presupuesto->id_cd]])
                ->get()->toArray();

        $componentes_cd = [];
        $array = [];

        foreach ($compo_cd as $comp){
            $total = 0;
            foreach($partidas_cd as $partidax){
                if ($comp->codigo == $partidax['cod_compo']){
                    array_push($array, $partidax);
                    $total += $partidax['importe_parcial'];
                }
            }

            $nuevo_comp = [
                "id_cd_compo"=>$comp->id_cd_compo,
                "id_cd"=>$comp->id_cd,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];

            $array = [];
            array_push($componentes_cd,$nuevo_comp);
        }
        
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
                ->where([['proy_ci_compo.id_ci', '=', $presupuesto->id_ci]])
                ->get();

        $componentes_ci = [];
        $array = [];

        foreach ($compo_ci as $comp){
            $total = 0;
            foreach($partidas_ci as $partida){
                if ($comp->codigo == $partida['cod_compo']){
                    array_push($array, $partida);
                    $total += $partida['importe_parcial'];
                }
            }
            $nuevo_comp = [
                "id_ci_compo"=>$comp->id_ci_compo,
                "id_ci"=>$comp->id_ci,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_ci,$nuevo_comp);
        }

        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
                ->where([['proy_gg_compo.id_gg', '=', $presupuesto->id_gg]])
                ->get();

        $componentes_gg = [];
        $array = [];

        foreach ($compo_gg as $comp){
            $total = 0;
            foreach($partidas_gg as $partida){
                if ($comp->codigo == $partida['cod_compo']){
                    array_push($array, $partida);
                    $total += $partida['importe_parcial'];
                }
            }
            $nuevo_comp = [
                "id_gg_compo"=>$comp->id_gg_compo,
                "id_gg"=>$comp->id_gg,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_gg,$nuevo_comp);
        }

        $cd = ["id_cd"=>$presupuesto->id_cd,"componentes_cd"=>$componentes_cd,"partidas_cd"=>$partidas_cd];
        $ci = ["id_ci"=>$presupuesto->id_ci,"componentes_ci"=>$componentes_ci,"partidas_ci"=>$partidas_ci];
        $gg = ["id_gg"=>$presupuesto->id_gg,"componentes_gg"=>$componentes_gg,"partidas_gg"=>$partidas_gg];

        return response()->json(["presupuesto"=>$presupuesto,"pc_importe"=>$pc_importe,
        // "componentes_cd"=>$componentes_cd]);
        "cd"=>$cd,"ci"=>$ci,"gg"=>$gg,"part_gg"=>$part_gg]);
    }

    public function mostrar_seguimiento_cd($codigo,$cd)
    {
        $cd_insumos = DB::table('proyectos.proy_cd_partida')
            ->select('proy_insumo.id_insumo','proy_cd_partida.id_partida','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_cd_partida.cantidad',
            DB::raw('sum(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as total'),
            'alm_und_medida.abreviatura')
            ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_cd_partida.id_cu')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_cd_partida.codigo','=',$codigo],
                     ['proy_cd_partida.id_cd','=',$cd]])
            ->groupBy('proy_insumo.id_insumo','proy_cd_partida.id_partida','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_cd_partida.cantidad','alm_und_medida.abreviatura')
            ->get()
            ->toArray();

        $insumos_cd = [];

        foreach($cd_insumos as $insumo){
            $det = DB::table('proyectos.proy_pdetalle')
                ->select('proy_pdetalle.*','alm_req.codigo as cod_req','alm_item.id_item','alm_prod.codigo','alm_prod.descripcion')
                ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','proy_pdetalle.id_det_req')
                ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                ->join('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
                ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
                ->where([['proy_pdetalle.id_cd_partida','=',$insumo->id_partida],
                        ['proy_pdetalle.id_insumo','=',$insumo->id_insumo]])
                ->get()
                ->toArray();

            $nuevo_part = array(
                "id_insumo"=>$insumo->id_insumo,
                "codigo"=>$insumo->codigo,
                "descripcion"=>$insumo->descripcion,
                "cantidad"=>$insumo->cantidad,
                "total"=>$insumo->total,
                "abreviatura"=>$insumo->abreviatura,
                "detalle"=>$det
            );
            array_push($insumos_cd, $nuevo_part);
        }
        return response()->json($insumos_cd);                          
    }

    public function mostrar_seguimiento_ci($codigo,$ci)
    {
        $ci_insumos = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_insumo.id_insumo','proy_ci_detalle.id_ci_detalle','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_ci_detalle.cantidad','proy_ci_detalle.participacion',
            'proy_ci_detalle.tiempo','proy_ci_detalle.veces',
            DB::raw('sum(proy_cu_detalle.precio_total * proy_ci_detalle.cantidad * proy_ci_detalle.participacion 
             * proy_ci_detalle.tiempo * proy_ci_detalle.veces) as total'),'alm_und_medida.abreviatura')
            ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_ci_detalle.id_cu')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_ci_detalle.codigo','=',$codigo],
                     ['proy_ci_detalle.id_ci','=',$ci]])
            ->groupBy('proy_insumo.id_insumo','proy_ci_detalle.id_ci_detalle','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_ci_detalle.cantidad','proy_ci_detalle.participacion',
            'proy_ci_detalle.tiempo','proy_ci_detalle.veces','alm_und_medida.abreviatura')
            ->get()
            ->toArray();

        $insumos_ci = [];

        foreach($ci_insumos as $insumo){
            $det = DB::table('proyectos.proy_pdetalle')
                ->select('proy_pdetalle.*','alm_req.codigo','alm_item.id_item')
                ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','proy_pdetalle.id_det_req')
                ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                ->join('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
                ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
                ->where([['proy_pdetalle.id_ci_detalle','=', $insumo->id_ci_detalle],
                        ['proy_pdetalle.id_insumo','=', $insumo->id_insumo]])
                ->get()
                ->toArray();

            $nuevo_part = array(
                "id_insumo"=>$insumo->id_insumo,
                "codigo"=>$insumo->codigo,
                "descripcion"=>$insumo->descripcion,
                "cantidad"=>$insumo->cantidad,
                "total"=>$insumo->total,
                "abreviatura"=>$insumo->abreviatura,
                "detalle"=>$det
            );
            array_push($insumos_ci, $nuevo_part);
        }
        return response()->json($insumos_ci);                          
    }

    public function mostrar_seguimiento_gg($codigo,$gg)
    {
        $gg_insumos = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_insumo.id_insumo','proy_gg_detalle.id_gg_detalle','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_gg_detalle.cantidad',
            DB::raw('sum(proy_cu_detalle.precio_total * proy_gg_detalle.cantidad * proy_gg_detalle.participacion 
            * proy_gg_detalle.tiempo * proy_gg_detalle.veces) as total'),
            'alm_und_medida.abreviatura')
            ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_gg_detalle.id_cu')
            ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_gg_detalle.codigo','=',$codigo],
                     ['proy_gg_detalle.id_gg','=',$gg]])
            ->groupBy('proy_insumo.id_insumo','proy_gg_detalle.id_gg_detalle','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_gg_detalle.cantidad','proy_gg_detalle.participacion',
            'proy_gg_detalle.tiempo','proy_gg_detalle.veces','alm_und_medida.abreviatura')
            ->get()
            ->toArray();

        $insumos_gg = [];

        foreach($gg_insumos as $insumo){
            $det = DB::table('proyectos.proy_pdetalle')
                ->select('proy_pdetalle.*','alm_req.codigo','alm_item.id_item')
                ->join('almacen.alm_det_req','alm_det_req.id_detalle_requerimiento','=','proy_pdetalle.id_det_req')
                ->join('almacen.alm_req','alm_req.id_requerimiento','=','alm_det_req.id_requerimiento')
                ->join('almacen.alm_item','alm_item.id_item','=','alm_det_req.id_item')
                ->join('almacen.alm_prod','alm_prod.id_producto','=','alm_item.id_producto')
                ->where([['proy_pdetalle.id_gg_detalle','=',$insumo->id_gg_detalle],
                        ['proy_pdetalle.id_insumo','=',$insumo->id_insumo]])
                ->get()
                ->toArray();

            $nuevo_part = array(
                "id_insumo"=>$insumo->id_insumo,
                "codigo"=>$insumo->codigo,
                "descripcion"=>$insumo->descripcion,
                "cantidad"=>$insumo->cantidad,
                "total"=>$insumo->total,
                "abreviatura"=>$insumo->abreviatura,
                "detalle"=>$det
            );
            array_push($insumos_gg, $nuevo_part);
        }
        return response()->json($insumos_gg);                          
    }

    public function mostrar_presupuestos_acu($id_cu)
    {
        $proy_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo',
                     'proy_op_com.descripcion', 'adm_contri.razon_social','proy_presup.estado')
            ->join('proyectos.proy_cd','proy_cd.id_cd','=','proy_cd_partida.id_cd')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd.id_presupuesto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_cd_partida.id_cu', '=', $id_cu]])
                ->distinct();

        $proy_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo',
                     'proy_op_com.descripcion', 'adm_contri.razon_social','proy_presup.estado')
            ->join('proyectos.proy_ci','proy_ci.id_ci','=','proy_ci_detalle.id_ci')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_ci.id_presupuesto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_ci_detalle.id_cu', '=', $id_cu]])
                ->distinct()
                ->unionAll($proy_cd);

        $proy_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_presup.id_presupuesto', 'proy_presup.codigo',
                     'proy_op_com.descripcion', 'adm_contri.razon_social','proy_presup.estado')
            ->join('proyectos.proy_gg','proy_gg.id_gg','=','proy_gg_detalle.id_gg')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_gg.id_presupuesto')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_gg_detalle.id_cu', '=', $id_cu]])
                ->distinct()
                ->unionAll($proy_ci)
                ->get()
                ->toArray();

        // $resultado = array_map("unserialize", array_unique(array_map("serialize", $proy_gg)));
 
        return $proy_gg;
    }
    public function mostrar_lecciones_acu($id_cu)
    {
        $proy_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_obs.*','proy_cd_partida.id_cu','proy_presup.codigo',
            'sis_usua.usuario as nombre_usuario')
            ->join('proyectos.proy_obs','proy_obs.id_cd_partida','=','proy_cd_partida.id_partida')
            ->join('proyectos.proy_cd','proy_cd.id_cd','=','proy_cd_partida.id_cd')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cd.id_presupuesto')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where([['proy_cd_partida.id_cu', '=', $id_cu]]);

        $proy_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_obs.*','proy_ci_detalle.id_cu','proy_presup.codigo',
            'sis_usua.usuario as nombre_usuario')
            ->join('proyectos.proy_obs','proy_obs.id_ci_detalle','=','proy_ci_detalle.id_ci_detalle')
            ->join('proyectos.proy_ci','proy_ci.id_ci','=','proy_ci_detalle.id_ci')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_ci.id_presupuesto')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where([['proy_ci_detalle.id_cu', '=', $id_cu]])
                ->unionAll($proy_cd);

        $proy_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_obs.*','proy_gg_detalle.id_cu','proy_presup.codigo',
            'sis_usua.usuario as nombre_usuario')
            ->join('proyectos.proy_obs','proy_obs.id_gg_detalle','=','proy_gg_detalle.id_gg_detalle')
            ->join('proyectos.proy_gg','proy_gg.id_gg','=','proy_gg_detalle.id_gg')
            ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_gg.id_presupuesto')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
                ->where([['proy_gg_detalle.id_cu', '=', $id_cu]])
                ->unionAll($proy_ci)
                ->get()
                ->toArray();

        // $resultado = array_map("unserialize", array_unique(array_map("serialize", $proy_gg)));
        // return response()->json($proy_gg);
        return $proy_gg;
    }

    public function obsPartida($id, $origen){

        $data = DB::table('proyectos.proy_obs')
        ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
        ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
        ->where(
            function($query) use ($origen, $id)
            {
                if ($origen === "cd"){
                    $query->where('proy_obs.id_cd_partida', $id);
                }
                else if ($origen === "ci"){
                    $query->where('proy_obs.id_ci_detalle', $id);         
                }
                else if ($origen === "gg"){
                    $query->where('proy_obs.id_gg_detalle', $id);           
                }
            })
        ->orderBy('proy_obs.fecha_registro')
        ->get();

        return response()->json($data);
    }
    public function nextPresupuesto($id,$id_emp,$fecha)
    {
        // $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_tp = '';
        $code_emp = '';
        $result = '';

        $tp = DB::table('proyectos.proy_tp_pres')
        ->select('codigo')
        ->where('id_tp_pres', '=', $id)
        ->get();
        foreach ($tp as $rowTp) {
            $code_tp = $rowTp->codigo;
        }            
        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', '=', $id_emp)
        ->get();
        foreach ($emp as $rowEmp) {
            $code_emp = $rowEmp->codigo;
        }
        $data = DB::table('proyectos.proy_presup')
                ->where([['id_tp_presupuesto', '=', $id],['id_empresa','=',$id_emp]])
                // ->whereMonth('fecha_emision', '=', $mes)
                ->whereYear('fecha_emision', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = $code_tp."".$code_emp."-".$anio."".$number;

        return $result;
    }

    public function guardar_presupuesto(Request $request){

        $cod = $this->nextPresupuesto(
            $request->id_tp_presupuesto,
            $request->id_empresa,
            $request->fecha_emision
        );

        $id_presupuesto = DB::table('proyectos.proy_presup')->insertGetId(
            [
                'id_contrato' => $request->id_contrato,
                'fecha_emision' => $request->fecha_emision,
                'moneda' => $request->moneda,
                'id_tp_presupuesto' => $request->id_tp_presupuesto,
                'elaborado_por' => $request->elaborado_por,
                'observacion' => $request->observacion,
                'estado' => 1,
                'fecha_registro' => $request->fecha_registro,
                'id_unid_ejec' => $request->id_unid_ejec,
                'id_residente' => $request->id_residente,
                'id_op_com' => $request->id_op_com,
                'id_empresa' => $request->id_empresa,
                'codigo' => $cod
            ],
                'id_presupuesto'
        );

        $total_cd = $request->total_costo_directo;

        DB::table('proyectos.proy_presup_importe')->insert(
            [
                'id_presupuesto' => $id_presupuesto,
                'total_costo_directo' => $total_cd,
                'total_ci' => $request->total_ci,
                'porcentaje_ci' => $request->porcentaje_ci,
                'total_gg' => $request->total_gg,
                'porcentaje_gg' => $request->porcentaje_gg,
                'sub_total' => $request->sub_total,
                'porcentaje_utilidad' => $request->porcentaje_utilidad,
                'total_utilidad' => $request->total_utilidad,
                'porcentaje_igv' => $request->porcentaje_igv,
                'total_igv' => $request->total_igv,
                'total_presupuestado' => $request->total_presupuestado
            ]
        );

        $id_cd = DB::table('proyectos.proy_cd')->insertGetId(
            [
                'id_presupuesto' => $id_presupuesto,
                'estado' => 1,
                'fecha_registro' => $request->fecha_registro
            ],
                'id_cd'
        );

        //COSTOS DIRECTOS
        $comp = $request->comp_cd_codigo;
        $count1 = count($comp);

        for ($i=0; $i<$count1; $i++){

            $cod = $request->comp_cd_codigo[$i];
            $des = $request->comp_cd_descripcion[$i];
            $pad = $request->comp_cd_id_padre[$i];
            $por = $request->comp_cd_porcen_utilidad[$i];
            $imp = $request->comp_cd_importe_utilidad[$i];

            $id_cd_compo = DB::table('proyectos.proy_cd_compo')->insertGetId(
                [
                    'id_cd'=> $id_cd,
                    'codigo'=> $cod,
                    'descripcion'=> $des,
                    'cod_padre'=> $pad,
                    'porcen_utilidad'=> $por,
                    'importe_utilidad'=> $imp
                ],
                    'id_cd_compo'
            );
    
        }

        $part = $request->part_cd_codigo;
        $count2 = count($part);

        for ($i=0; $i<$count2; $i++){

            $acu = $request->part_cd_id_cu[$i];
            $cod = $request->part_cd_codigo[$i];
            $des = $request->part_cd_descripcion[$i];
            $um  = $request->part_cd_unid_medida[$i];
            $can = $request->part_cd_cantidad[$i];
            $uni = $request->part_cd_importe_unitario[$i];
            $par = $request->part_cd_importe_parcial[$i];
            $sis = $request->part_cd_id_sistema[$i];
            $com = $request->part_cd_cod_compo[$i];

            $id_partida = DB::table('proyectos.proy_cd_partida')->insertGetId(
                [
                    'id_cd'=> $id_cd,
                    'id_cu'=> $acu,
                    'codigo'=> $cod,
                    'descripcion'=> $des,
                    'unid_medida'=> $um,
                    'cantidad'=> $can,
                    'importe_unitario'=> $uni,
                    'importe_parcial'=> $par,
                    'id_sistema'=> $sis,
                    'cod_compo'=> $com,
                    'estado'=> 1,
                    'fecha_registro'=> $request->fecha_registro
                ],
                    'id_partida'
            );
        }

        $id_ci = DB::table('proyectos.proy_ci')->insertGetId(
            [
                'id_presupuesto' => $id_presupuesto,
                'estado' => 1,
                'fecha_registro' => $request->fecha_registro
            ],
                'id_ci'
        );

        //COSTOS INDIRECTOS
        $comp_ci = $request->comp_ci_codigo;
        $count_ci1 = count($comp_ci);

        for ($i=0; $i<$count_ci1; $i++){

            $cod = $request->comp_ci_codigo[$i];
            $des = $request->comp_ci_descripcion[$i];
            $pad = $request->comp_ci_id_padre[$i];
            $por = $request->comp_ci_porcen_utilidad[$i];
            $imp = $request->comp_ci_importe_utilidad[$i];

            $id_ci_compo = DB::table('proyectos.proy_ci_compo')->insertGetId(
                [
                    'id_ci'=> $id_ci,
                    'codigo'=> $cod,
                    'descripcion'=> $des,
                    'cod_padre'=> $pad,
                    'porcen_utilidad'=> $por,
                    'importe_utilidad'=> $imp,
                    'estado'=>1,
                    'fecha_registro'=>$request->fecha_registro
                ],
                    'id_ci_compo'
            );
    
        }

        $part_ci = $request->part_ci_codigo;
        $countci2 = count($part_ci);

        for ($i=0; $i<$countci2; $i++){

            $acu = $request->part_ci_id_cu[$i];
            $cod = $request->part_ci_codigo[$i];
            $des = $request->part_ci_descripcion[$i];
            $um  = $request->part_ci_unid_medida[$i];
            $can = $request->part_ci_cantidad[$i];
            $uni = $request->part_ci_importe_unitario[$i];
            $part = $request->part_ci_participacion[$i];
            $tiem = $request->part_ci_tiempo[$i];
            $vec = $request->part_ci_veces[$i];
            $par = $request->part_ci_importe_parcial[$i];
            $com = $request->part_ci_cod_compo[$i];

            $id_ci_detalle = DB::table('proyectos.proy_ci_detalle')->insertGetId(
                [
                    'id_ci'=> $id_ci,
                    'id_cu'=> $acu,
                    'codigo'=> $cod,
                    'descripcion'=> $des,
                    'unid_medida'=> $um,
                    'cantidad'=> $can,
                    'importe_unitario'=> $uni,
                    'participacion'=> $part,
                    'tiempo'=> $tiem,
                    'veces'=> $vec,
                    'importe_parcial'=> $par,
                    'cod_compo'=> $com,
                    'estado'=> 1,
                    'fecha_registro'=> $request->fecha_registro
                ],
                    'id_ci_detalle'
            );
        }

        $id_gg = DB::table('proyectos.proy_gg')->insertGetId(
            [
                'id_presupuesto' => $id_presupuesto,
                'estado' => 1,
                'fecha_registro' => $request->fecha_registro
            ],
                'id_gg'
        );

        //GASTOS GENERALES
        $comp_gg = $request->comp_gg_codigo;
        $countgg1 = count($comp_gg);

        for ($i=0; $i<$countgg1; $i++){

            $cod = $request->comp_gg_codigo[$i];
            $des = $request->comp_gg_descripcion[$i];
            $pad = $request->comp_gg_id_padre[$i];
            $por = $request->comp_gg_porcen_utilidad[$i];
            $imp = $request->comp_gg_importe_utilidad[$i];

            $id_gg_compo = DB::table('proyectos.proy_gg_compo')->insertGetId(
                [
                    'id_gg'=> $id_gg,
                    'codigo'=> $cod,
                    'descripcion'=> $des,
                    'cod_padre'=> $pad,
                    'porcen_utilidad'=> $por,
                    'importe_utilidad'=> $imp,
                    'estado'=> 1,
                    'fecha_registro'=> $request->fecha_registro
                ],
                    'id_gg_compo'
            );
    
        }

        $part_gg = $request->part_gg_codigo;
        $countgg2 = count($part_gg);

        for ($i=0; $i<$countgg2; $i++){

            $acu = $request->part_gg_id_cu[$i];
            $cod = $request->part_gg_codigo[$i];
            $des = $request->part_gg_descripcion[$i];
            $um  = $request->part_gg_unid_medida[$i];
            $can = $request->part_gg_cantidad[$i];
            $uni = $request->part_gg_importe_unitario[$i];
            $part = $request->part_gg_participacion[$i];
            $tiem = $request->part_gg_tiempo[$i];
            $vec = $request->part_gg_veces[$i];
            $par = $request->part_gg_importe_parcial[$i];
            $com = $request->part_gg_cod_compo[$i];

            $id_gg_detalle = DB::table('proyectos.proy_gg_detalle')->insertGetId(
                [
                    'id_gg'=> $id_gg,
                    'id_cu'=> $acu,
                    'codigo'=> $cod,
                    'descripcion'=> $des,
                    'unid_medida'=> $um,
                    'cantidad'=> $can,
                    'importe_unitario'=> $uni,
                    'participacion'=> $part,
                    'tiempo'=> $tiem,
                    'veces'=> $vec,
                    'importe_parcial'=> $par,
                    'cod_compo'=> $com,
                    'estado'=> 1,
                    'fecha_registro'=> $request->fecha_registro
                ],
                    'id_gg_detalle'
            );
        }

        $oid = $request->obs_id_obs;
        $counto = count($oid);

        for ($i=0; $i<$counto; $i++){

            $id_obs =         $request->obs_id_obs[$i];
            $id_cd_partida =  $request->obs_id_cd_partida[$i];
            $id_ci_detalle =  $request->obs_id_ci_detalle[$i];
            $id_gg_detalle =  $request->obs_id_gg_detalle[$i];
            $descripcion =    $request->obs_descripcion[$i];
            $usuario =        $request->obs_usuario[$i];

            $id_obs = DB::table('proyectos.proy_obs')->insertGetId(
                [
                    'id_cd_partida'=>  $id_cd_partida,
                    'id_ci_detalle'=>  $id_ci_detalle,
                    'id_gg_detalle'=>  $id_gg_detalle,
                    'descripcion'=>    $descripcion,
                    'usuario'=>        $usuario,
                    'fecha_registro'=> $request->fecha_registro,
                    'estado'=>         1
                ],
                    'id_obs'
            );
        }

        return response()->json($id_presupuesto);
    }

    public function update_presupuesto(Request $request, $id)
    {
        DB::table('proyectos.proy_presup')->where('id_presupuesto', $id)
            ->update([
                'id_contrato' => $request->id_contrato,
                'fecha_emision' => $request->fecha_emision,
                'moneda' => $request->moneda,
                'id_tp_presupuesto' => $request->id_tp_presupuesto,
                'elaborado_por' => $request->elaborado_por,
                'observacion' => $request->observacion,
                'id_unid_ejec' => $request->id_unid_ejec,
                'id_residente' => $request->id_residente,
                'id_op_com' => $request->id_op_com
            ]);
            
        DB::table('proyectos.proy_presup_importe')->where('id_presupuesto', $id)
            ->update([
                'total_costo_directo' => $request->total_costo_directo,
                'total_ci' => $request->total_ci,
                'porcentaje_ci' => $request->porcentaje_ci,
                'total_gg' => $request->total_gg,
                'porcentaje_gg' => $request->porcentaje_gg,
                'sub_total' => $request->sub_total,
                'porcentaje_utilidad' => $request->porcentaje_utilidad,
                'total_utilidad' => $request->total_utilidad,
                'porcentaje_igv' => $request->porcentaje_igv,
                'total_igv' => $request->total_igv,
                'total_presupuestado' => $request->total_presupuestado
            ]);

        //COSTO DIRECTO
        $id_cd = $request->id_cd;

        if ($id_cd === ''){
            $id_cd = DB::table('proyectos.proy_cd')->insertGetId(
                [
                    'id_presupuesto' => $id,
                    'estado' => 1,
                    'fecha_registro' => $request->fecha_registro
                ],
                    'id_cd'
            );
        }

        //COSTO DIRECTO - COMPONENTES
        if (sizeof($request->comp_cd_codigo) > 0){
            $comp = $request->comp_cd_codigo;
            $count1 = count($comp);
    
            for ($i=0; $i<$count1; $i++){
    
                $idc = $request->comp_cd_id_cd_compo[$i];
                $cod = $request->comp_cd_codigo[$i];
                $des = $request->comp_cd_descripcion[$i];
                $pad = $request->comp_cd_cod_padre[$i];
                $por = $request->comp_cd_porcen_utilidad[$i];
                $imp = $request->comp_cd_importe_utilidad[$i];
    
                if ($idc === 0){
                    DB::table('proyectos.proy_cd_compo')->insert(
                        [
                            'id_cd'=> $id_cd,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'cod_padre'=> $pad,
                            'porcen_utilidad'=> $por,
                            'importe_utilidad'=> $imp
                        ]
                    );
                }
                else {
                    DB::table('proyectos.proy_cd_compo')->where('id_cd_compo', $idc)
                    ->update([
                        // 'id_cd'=> $id_cd,
                        'codigo'=> $cod,
                        'descripcion'=> $des,
                        'cod_padre'=> $pad,
                        'porcen_utilidad'=> $por,
                        'importe_utilidad'=> $imp
                    ]);
                }
            }
        }

        //COSTO DIRECTO - PARTIDAS
        if (sizeof($request->part_cd_codigo) > 0){
            $part = $request->part_cd_codigo;
            $count2 = count($part);
    
            for ($i=0; $i<$count2; $i++){
    
                $idp = $request->part_cd_id_partida[$i];
                $acu = $request->part_cd_id_cu[$i];
                $cod = $request->part_cd_codigo[$i];
                $des = $request->part_cd_descripcion[$i];
                $um  = $request->part_cd_unid_medida[$i];
                $can = $request->part_cd_cantidad[$i];
                $uni = $request->part_cd_importe_unitario[$i];
                $par = $request->part_cd_importe_parcial[$i];
                $sis = $request->part_cd_id_sistema[$i];
                $com = $request->part_cd_cod_compo[$i];
    
                if ($idp === 0){
                    DB::table('proyectos.proy_cd_partida')->insert(
                        [
                            'id_cd'=> $id_cd,
                            'id_cu'=> $acu,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'unid_medida'=> $um,
                            'cantidad'=> $can,
                            'importe_unitario'=> $uni,
                            'importe_parcial'=> $par,
                            'id_sistema'=> $sis,
                            'cod_compo'=> $com,
                            'estado'=> $request->estado,
                            'fecha_registro'=> $request->fecha_registro
                        ]
                    );
                }
                else {
                    DB::table('proyectos.proy_cd_partida')->where('id_partida', $idp)
                    ->update([
                            // 'id_cd'=> $id_cd,
                            'id_cu'=> $acu,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'unid_medida'=> $um,
                            'cantidad'=> $can,
                            'importe_unitario'=> $uni,
                            'importe_parcial'=> $par,
                            'id_sistema'=> $sis,
                            'cod_compo'=> $com
                            // 'estado'=> $request->estado,
                            // 'fecha_registro'=> $request->fecha_registro
                        ]);
                }   
            }                
        }

        // COSTO INDIRECTO
        $id_ci = $request->id_ci;

        if ($id_ci == ''){
            $id_ci = DB::table('proyectos.proy_ci')->insertGetId(
                [
                    'id_presupuesto' => $id,
                    'estado' => 1,
                    'fecha_registro' => $request->fecha_registro
                ],
                    'id_ci'
            );
        }

        //COSTO INDIRECTO - COMPONENTES
        if (sizeof($request->comp_ci_codigo) > 0){
            $comp = $request->comp_ci_codigo;
            $count1 = count($comp);
    
            for ($i=0; $i<$count1; $i++){
    
                $idc = $request->comp_ci_id_ci_compo[$i];
                $cod = $request->comp_ci_codigo[$i];
                $des = $request->comp_ci_descripcion[$i];
                $pad = $request->comp_ci_cod_padre[$i];
                $por = $request->comp_ci_porcen_utilidad[$i];
                $imp = $request->comp_ci_importe_utilidad[$i];
    
                if ($idc === 0){
                    DB::table('proyectos.proy_ci_compo')->insert(
                        [
                            'id_ci'=> $id_ci,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'cod_padre'=> $pad,
                            'porcen_utilidad'=> $por,
                            'importe_utilidad'=> $imp
                        ]
                    );
                }
                else {
                    DB::table('proyectos.proy_ci_compo')->where('id_ci_compo', $idc)
                    ->update([
                        // 'id_cd'=> $id_cd,
                        'codigo'=> $cod,
                        'descripcion'=> $des,
                        'cod_padre'=> $pad,
                        'porcen_utilidad'=> $por,
                        'importe_utilidad'=> $imp
                    ]);
                }
            }
        }

        //COSTO INDIRECTO - PARTIDAS
        if (sizeof($request->part_ci_codigo) > 0){
            $part = $request->part_ci_codigo;
            $count2 = count($part);
    
            for ($i=0; $i<$count2; $i++){
    
                $icd = $request->part_ci_id_ci_detalle[$i];
                $acu = $request->part_ci_id_cu[$i];
                $cod = $request->part_ci_codigo[$i];
                $des = $request->part_ci_descripcion[$i];
                $um  = $request->part_ci_unid_medida[$i];
                $can = $request->part_ci_cantidad[$i];
                $uni = $request->part_ci_importe_unitario[$i];
                $part = $request->part_ci_participacion[$i];
                $tiem = $request->part_ci_tiempo[$i];
                $vec = $request->part_ci_veces[$i];
                $par = $request->part_ci_importe_parcial[$i];
                $com = $request->part_ci_cod_compo[$i];
    
                if ($icd === 0){
                    DB::table('proyectos.proy_ci_detalle')->insert(
                        [
                            'id_ci'=> $id_ci,
                            'id_cu'=> $acu,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'unid_medida'=> $um,
                            'cantidad'=> $can,
                            'importe_unitario'=> $uni,
                            'participacion'=> $part,
                            'tiempo'=> $tiem,
                            'veces'=> $vec,
                            'importe_parcial'=> $par,
                            'cod_compo'=> $com,
                            'estado'=> 1,
                            'fecha_registro'=> $request->fecha_registro
                        ]
                    );
                }
                else {
                    DB::table('proyectos.proy_ci_detalle')->where('id_ci_detalle', $icd)
                    ->update([
                            'id_cu'=> $acu,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'unid_medida'=> $um,
                            'cantidad'=> $can,
                            'importe_unitario'=> $uni,
                            'importe_parcial'=> $par,
                            'participacion'=> $part,
                            'tiempo'=> $tiem,
                            'veces'=> $vec,
                            'cod_compo'=> $com
                        ]);
                }   
            }                
        }

        // GASTOS GENERALES
        $id_gg = $request->id_gg;

        if ($id_gg == ''){
            $id_gg = DB::table('proyectos.proy_gg')->insertGetId(
                [
                    'id_presupuesto' => $id,
                    'estado' => 1,
                    'fecha_registro' => $request->fecha_registro
                ],
                    'id_gg'
            );
        }

        //GASTOS GENERALES - COMPONENTES
        if (sizeof($request->comp_gg_codigo) > 0){
            $comp = $request->comp_gg_codigo;
            $count1 = count($comp);
    
            for ($i=0; $i<$count1; $i++){
    
                $idc = $request->comp_gg_id_gg_compo[$i];
                $cod = $request->comp_gg_codigo[$i];
                $des = $request->comp_gg_descripcion[$i];
                $pad = $request->comp_gg_cod_padre[$i];
                $por = $request->comp_gg_porcen_utilidad[$i];
                $imp = $request->comp_gg_importe_utilidad[$i];
    
                if ($idc === 0){
                    DB::table('proyectos.proy_gg_compo')->insert(
                        [
                            'id_gg'=> $id_gg,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'cod_padre'=> $pad,
                            'porcen_utilidad'=> $por,
                            'importe_utilidad'=> $imp
                        ]
                    );
                }
                else {
                    DB::table('proyectos.proy_gg_compo')->where('id_gg_compo', $idc)
                    ->update([
                        // 'id_cd'=> $id_cd,
                        'codigo'=> $cod,
                        'descripcion'=> $des,
                        'cod_padre'=> $pad,
                        'porcen_utilidad'=> $por,
                        'importe_utilidad'=> $imp
                    ]);
                }
            }
        }

        //GASTOS GENERALES - PARTIDAS
        if (sizeof($request->part_gg_codigo) > 0){
            $part = $request->part_gg_codigo;
            $count2 = count($part);
    
            for ($i=0; $i<$count2; $i++){
    
                $icd = $request->part_gg_id_gg_detalle[$i];
                $acu = $request->part_gg_id_cu[$i];
                $cod = $request->part_gg_codigo[$i];
                $des = $request->part_gg_descripcion[$i];
                $um  = $request->part_gg_unid_medida[$i];
                $can = $request->part_gg_cantidad[$i];
                $uni = $request->part_gg_importe_unitario[$i];
                $part = $request->part_gg_participacion[$i];
                $tiem = $request->part_gg_tiempo[$i];
                $vec = $request->part_gg_veces[$i];
                $par = $request->part_gg_importe_parcial[$i];
                $com = $request->part_gg_cod_compo[$i];
    
                if ($icd === 0){
                    DB::table('proyectos.proy_gg_detalle')->insert(
                        [
                            'id_gg'=> $id_gg,
                            'id_cu'=> $acu,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'unid_medida'=> $um,
                            'cantidad'=> $can,
                            'importe_unitario'=> $uni,
                            'participacion'=> $part,
                            'tiempo'=> $tiem,
                            'veces'=> $vec,
                            'importe_parcial'=> $par,
                            'cod_compo'=> $com,
                            'estado'=> 1,
                            'fecha_registro'=> $request->fecha_registro
                        ]
                    );
                }
                else {
                    DB::table('proyectos.proy_gg_detalle')->where('id_gg_detalle', $icd)
                    ->update([
                            'id_cu'=> $acu,
                            'codigo'=> $cod,
                            'descripcion'=> $des,
                            'unid_medida'=> $um,
                            'cantidad'=> $can,
                            'importe_unitario'=> $uni,
                            'participacion'=> $part,
                            'tiempo'=> $tiem,
                            'veces'=> $vec,
                            'importe_parcial'=> $par,
                            'cod_compo'=> $com
                        ]);
                }   
            }                
        }

        $oid = $request->obs_id_obs;
        $counto = count($oid);

        for ($i=0; $i<$counto; $i++){

            $id_obs =         $request->obs_id_obs[$i];
            $id_cd_partida =  $request->obs_id_cd_partida[$i];
            $id_ci_detalle =  $request->obs_id_ci_detalle[$i];
            $id_gg_detalle =  $request->obs_id_gg_detalle[$i];
            $descripcion =    $request->obs_descripcion[$i];
            $usuario =        $request->obs_usuario[$i];

            if ($id_obs === 0){
                DB::table('proyectos.proy_obs')->insertGetId(
                    [
                        'id_cd_partida'=>  $id_cd_partida,
                        'id_ci_detalle'=>  $id_ci_detalle,
                        'id_gg_detalle'=>  $id_gg_detalle,
                        'descripcion'=>    $descripcion,
                        'usuario'=>        $usuario,
                        'fecha_registro'=> $request->fecha_registro,
                        'estado'=>         1
                    ],
                        'id_obs'
                );
            }
            else {
                DB::table('proyectos.proy_obs')->where('id_obs',$id_obs)
                ->update(
                    [
                        'id_cd_partida'=>  $id_cd_partida,
                        'id_ci_detalle'=>  $id_ci_detalle,
                        'id_gg_detalle'=>  $id_gg_detalle,
                        'descripcion'=>    $descripcion,
                        'usuario'=>        $usuario
                        // 'fecha_registro'=> $request->fecha_registro,
                        // 'estado'=>         1
                    ]
                );
            }

        }

        return response()->json($id);
    }

    public function guardar_pres_uti(Request $request){

        if (sizeof($request->id_tp_insumo) > 0){
            $id = $request->id_tp_insumo;
            $count = count($id);
            
            for ($i=0; $i<$count; $i++){

                $id_utilidad = $request->id_utilidad[$i];
                $id_presupuesto = $request->id_presupuesto[$i];
                $id_tp_insumo = $request->id_tp_insumo[$i];
                $suma = $request->suma[$i];
                $porcentaje = $request->porcentaje[$i];
                $utilidad = $request->utilidad[$i];
                $fecha_registro = $request->fecha_registro[$i];

                if ($id_utilidad === 0){

                    DB::table('proyectos.proy_presup_uti')->insert(
                        [
                            'id_presupuesto'=>$id_presupuesto,
                            'id_tp_insumo'=>$id_tp_insumo,
                            'suma'=>$suma,
                            'porcentaje'=>$porcentaje,
                            'utilidad'=>$utilidad,
                            'fecha_registro'=>$fecha_registro
                        ]
                    );
                }
                else {
                    DB::table('proyectos.proy_presup_uti')
                    ->where('id_utilidad', $id_utilidad)
                    ->update([
                            'id_presupuesto'=>$id_presupuesto,
                            'id_tp_insumo'=>$id_tp_insumo,
                            'suma'=>$suma,
                            'porcentaje'=>$porcentaje,
                            'utilidad'=>$utilidad,
                            'fecha_registro'=>$fecha_registro
                        ]
                    );
                }
            }

            $data = DB::table('proyectos.proy_presup_importe')->where('id_presupuesto', $id)
            ->update([
                'total_costo_directo' => $request->total_costo_directo,
                'total_ci' => $request->total_ci,
                'porcentaje_ci' => $request->porcentaje_ci,
                'total_gg' => $request->total_gg,
                'porcentaje_gg' => $request->porcentaje_gg,
                'sub_total' => $request->sub_total,
                'porcentaje_utilidad' => $request->porcentaje_utilidad,
                'total_utilidad' => $request->total_utilidad,
                'porcentaje_igv' => $request->porcentaje_igv,
                'total_igv' => $request->total_igv,
                'total_presupuestado' => $request->total_presupuestado
            ]);

        }
        return 0;
    }
    public function update_pres_uti(Request $request, $id){

        $id_utilidad = DB::table('proyectos.proy_presup_uti')
        ->where('id_utilidad', $id)
        ->update([
                'id_presupuesto'=>$request->id_presupuesto,
                'id_tp_insumo'=>$request->id_tp_insumo,
                'porcentaje'=>$request->porcentaje,
                'utilidad'=>$request->utilidad,
                'fecha_registro'=>$request->fecha_registro
            ]
        );
        return response()->json($id_utilidad);
    }
    
    public function mostrar_uti($id){

        $data = DB::table('proyectos.proy_presup_uti')
        ->select('proy_presup_uti.*','proy_tp_insumo.descripcion')
            ->join('proyectos.proy_tp_insumo','proy_presup_uti.id_tp_insumo','=','proy_tp_insumo.id_tp_insumo')
            ->where([['proy_presup_uti.id_presupuesto', '=', $id]])
            ->get();

        return response()->json($data);
    }

    public function getSumaTipoInsumos($id){

        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo', DB::raw('sum(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as total'))
        ->join('proyectos.proy_cd','proy_presup.id_presupuesto','=','proy_cd.id_presupuesto')
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_cd.id_cd')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_cd_partida.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->groupBy('proy_insumo.tp_insumo');
            // ->get();

        $ci_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo', DB::raw('sum(proy_cu_detalle.precio_total * proy_ci_detalle.cantidad
        * proy_ci_detalle.participacion * proy_ci_detalle.tiempo * proy_ci_detalle.veces) as total'))
        ->join('proyectos.proy_ci','proy_presup.id_presupuesto','=','proy_ci.id_presupuesto')
        ->join('proyectos.proy_ci_detalle','proy_ci_detalle.id_ci','=','proy_ci.id_ci')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_ci_detalle.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->groupBy('proy_insumo.tp_insumo')
        ->unionAll($cd_insumos);
        // ->get();
            
        $gg_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo', DB::raw('sum(proy_cu_detalle.precio_total * proy_gg_detalle.cantidad
        * proy_gg_detalle.participacion * proy_gg_detalle.tiempo * proy_gg_detalle.veces) as total'))
        ->join('proyectos.proy_gg','proy_presup.id_presupuesto','=','proy_gg.id_presupuesto')
        ->join('proyectos.proy_gg_detalle','proy_gg_detalle.id_gg','=','proy_gg.id_gg')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_gg_detalle.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->where([['proy_presup.id_presupuesto', '=', $id]])
        ->groupBy('proy_insumo.tp_insumo')
        ->unionAll($ci_insumos)
        ->get()
        ->toArray();

        $tipos = DB::table('proyectos.proy_tp_insumo')
        ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.codigo','proy_tp_insumo.descripcion')
            ->get();

        $sum = 0;
        $array = [];

        foreach($tipos as $tipo){
            foreach ($gg_insumos as $row){
                if ($tipo->id_tp_insumo == $row->tp_insumo){
                    $sum += $row->total;
                }
            }
            $nuevo = array( 'id_utilidad'=>0,
                            'id_presupuesto'=>$id,
                            'id_tp_insumo'=>$tipo->id_tp_insumo, 
                            'descripcion'=>$tipo->descripcion,
                            'suma'=>$sum,
                            'porcentaje'=>0,
                            'utilidad'=>0,
                            'fecha_registro'=>'');
            $array[] = $nuevo;
            $sum = 0;
        }
        $data = $array;

        // $data = ["cd_insumos"=>$cd_insumos,"ci_insumos"=>$ci_insumos,"gg_insumos"=>$gg_insumos];
        return response()->json($data);
    }
    public function getInsumosByTipo($id){

        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo','proy_tp_insumo.descripcion as tp_descripcion','proy_insumo.codigo',
        'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida',
        'proy_cd_partida.cantidad', DB::raw('sum(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as total'),
        DB::raw("CONCAT('CD') AS tp"),'proy_cd_partida.codigo as cod_part','proy_cd_partida.id_partida')
        ->join('proyectos.proy_cd','proy_presup.id_presupuesto','=','proy_cd.id_presupuesto')
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_cd.id_cd')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_cd_partida.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->groupBy('proy_insumo.tp_insumo','proy_tp_insumo.descripcion','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida',
            'proy_cd_partida.cantidad','proy_cd_partida.codigo','proy_cd_partida.id_partida');
            // ->get();

        $ci_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo','proy_tp_insumo.descripcion as tp_descripcion','proy_insumo.codigo',
        'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida',
        'proy_ci_detalle.cantidad', DB::raw('sum(proy_cu_detalle.precio_total * proy_ci_detalle.cantidad
        * proy_ci_detalle.participacion * proy_ci_detalle.tiempo * proy_ci_detalle.veces) as total'),
        DB::raw("CONCAT('CI') AS tp"),'proy_ci_detalle.codigo as cod_part','proy_ci_detalle.id_ci_detalle as id_partida')
        ->join('proyectos.proy_ci','proy_presup.id_presupuesto','=','proy_ci.id_presupuesto')
        ->join('proyectos.proy_ci_detalle','proy_ci_detalle.id_ci','=','proy_ci.id_ci')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_ci_detalle.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->groupBy('proy_insumo.tp_insumo','proy_tp_insumo.descripcion','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida',
            'proy_ci_detalle.cantidad','proy_ci_detalle.codigo','proy_ci_detalle.id_ci_detalle')
        ->unionAll($cd_insumos);
        // ->get();
            
        $gg_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo','proy_tp_insumo.descripcion as tp_descripcion','proy_insumo.codigo',
        'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida',
        'proy_gg_detalle.cantidad',DB::raw('sum(proy_cu_detalle.precio_total * proy_gg_detalle.cantidad
        * proy_gg_detalle.participacion * proy_gg_detalle.tiempo * proy_gg_detalle.veces) as total'),
        DB::raw("CONCAT('GG') AS tp"),'proy_gg_detalle.codigo as cod_part','proy_gg_detalle.id_gg_detalle as id_partida')
        ->join('proyectos.proy_gg','proy_presup.id_presupuesto','=','proy_gg.id_presupuesto')
        ->join('proyectos.proy_gg_detalle','proy_gg_detalle.id_gg','=','proy_gg.id_gg')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_gg_detalle.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->where([['proy_presup.id_presupuesto', '=', $id]])
        ->groupBy('proy_insumo.tp_insumo','proy_tp_insumo.descripcion','proy_insumo.codigo',
        'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida',
        'proy_gg_detalle.cantidad','proy_gg_detalle.codigo','proy_gg_detalle.id_gg_detalle')
        ->unionAll($ci_insumos)
        // ->orderBy('tp_insumo','ASC')->orderBy('codigo','ASC')
        ->get()
        ->toArray();

        $result = array();
        foreach ($gg_insumos as $t)
        {
            $repeat=false;
            for ($i=0;$i<count($result);$i++)
            {
                if ($result[$i]['id_insumo']===$t->id_insumo)
                {
                    $result[$i]['cantidad']+=$t->cantidad;
                    $result[$i]['total']+=$t->total;
                    $repeat=true;
                    break;
                }
            }
            if ($repeat==false){
                $result[] = array(
                    'id_insumo' => $t->id_insumo, 
                    'tp_insumo' => $t->tp_insumo, 
                    'codigo' => $t->codigo,
                    'descripcion' => $t->descripcion,
                    'abreviatura' => $t->abreviatura,
                    'cantidad' => $t->cantidad,
                    'unitario' => ($t->total / $t->cantidad),
                    'total' => $t->total
                );
            }
        }

        $tipos = DB::table('proyectos.proy_tp_insumo')
        ->select('proy_tp_insumo.id_tp_insumo','proy_tp_insumo.codigo','proy_tp_insumo.descripcion')
            ->get();

        $new_tipos = [];

        foreach($tipos as $tipo){
            $sum = 0;
            $array = [];
            foreach ($result as $row){
                if ($tipo->id_tp_insumo == $row['tp_insumo']){
                    $sum += $row['total'];
                    $array[] = $row;
                }
            }
            $nuevo = array( 'id_tp_insumo'=>$tipo->id_tp_insumo,
                            'codigo'=>$tipo->codigo,
                            'descripcion'=>$tipo->descripcion,
                            'suma'=>$sum,
                            'insumos'=>$array);
            if ($sum > 0){
                $new_tipos[] = $nuevo;
            }
        }

        return response()->json($new_tipos);
    }

    public function getInsumos($id){

        $cd_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo','proy_tp_insumo.descripcion as tp_descripcion','proy_insumo.codigo',
        'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida','proy_cu_detalle.precio_total',
        'proy_cd_partida.cantidad', DB::raw('sum(proy_cu_detalle.precio_total * proy_cd_partida.cantidad) as total'),
        DB::raw("CONCAT('CD') AS tp"),'proy_cd_partida.codigo as cod_part','proy_cd_partida.id_partida')
        ->join('proyectos.proy_cd','proy_presup.id_presupuesto','=','proy_cd.id_presupuesto')
        ->join('proyectos.proy_cd_partida','proy_cd_partida.id_cd','=','proy_cd.id_cd')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_cd_partida.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->groupBy('proy_insumo.tp_insumo','proy_tp_insumo.descripcion','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida','proy_cu_detalle.precio_total',
            'proy_cd_partida.cantidad','proy_cd_partida.codigo','proy_cd_partida.id_partida');
            // ->get();

        $ci_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo','proy_tp_insumo.descripcion as tp_descripcion','proy_insumo.codigo',
        'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida','proy_cu_detalle.precio_total',
        'proy_ci_detalle.cantidad', DB::raw('sum(proy_cu_detalle.precio_total * proy_ci_detalle.cantidad
        * proy_ci_detalle.participacion * proy_ci_detalle.tiempo * proy_ci_detalle.veces) as total'),
        DB::raw("CONCAT('CI') AS tp"),'proy_ci_detalle.codigo as cod_part','proy_ci_detalle.id_ci_detalle as id_partida')
        ->join('proyectos.proy_ci','proy_presup.id_presupuesto','=','proy_ci.id_presupuesto')
        ->join('proyectos.proy_ci_detalle','proy_ci_detalle.id_ci','=','proy_ci.id_ci')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_ci_detalle.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
            ->where([['proy_presup.id_presupuesto', '=', $id]])
            ->groupBy('proy_insumo.tp_insumo','proy_tp_insumo.descripcion','proy_insumo.codigo',
            'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida','proy_cu_detalle.precio_total',
            'proy_ci_detalle.cantidad','proy_ci_detalle.codigo','proy_ci_detalle.id_ci_detalle')
        ->unionAll($cd_insumos);
        // ->get();
            
        $gg_insumos = DB::table('proyectos.proy_presup')
        ->select('proy_insumo.tp_insumo','proy_tp_insumo.descripcion as tp_descripcion','proy_insumo.codigo',
        'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida','proy_cu_detalle.precio_total',
        'proy_gg_detalle.cantidad',DB::raw('sum(proy_cu_detalle.precio_total * proy_gg_detalle.cantidad
        * proy_gg_detalle.participacion * proy_gg_detalle.tiempo * proy_gg_detalle.veces) as total'),
        DB::raw("CONCAT('GG') AS tp"),'proy_gg_detalle.codigo as cod_part','proy_gg_detalle.id_gg_detalle as id_partida')
        ->join('proyectos.proy_gg','proy_presup.id_presupuesto','=','proy_gg.id_presupuesto')
        ->join('proyectos.proy_gg_detalle','proy_gg_detalle.id_gg','=','proy_gg.id_gg')
        ->join('proyectos.proy_cu_detalle','proy_cu_detalle.id_cu','=','proy_gg_detalle.id_cu')
        ->join('proyectos.proy_insumo','proy_insumo.id_insumo','=','proy_cu_detalle.id_insumo')
        ->join('proyectos.proy_tp_insumo','proy_tp_insumo.id_tp_insumo','=','proy_insumo.tp_insumo')
        ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_insumo.unid_medida')
        ->where([['proy_presup.id_presupuesto', '=', $id]])
        ->groupBy('proy_insumo.tp_insumo','proy_tp_insumo.descripcion','proy_insumo.codigo',
        'proy_insumo.descripcion','proy_insumo.id_insumo','alm_und_medida.abreviatura','proy_insumo.unid_medida','proy_cu_detalle.precio_total',
        'proy_gg_detalle.cantidad','proy_gg_detalle.codigo','proy_gg_detalle.id_gg_detalle')
        ->unionAll($ci_insumos)
        ->orderBy('tp_insumo','ASC')->orderBy('codigo','ASC')
        ->get();
        // ->toArray();

        // $data = ["cd_insumos"=>$cd_insumos,"ci_insumos"=>$ci_insumos,"gg_insumos"=>$gg_insumos];
        return response()->json($gg_insumos);
    }

    public function mostrar_cronogramas($tipo)
    {
        $data = DB::table('proyectos.proy_cronog')
        ->select('proy_cronog.*','proy_op_com.descripcion as nombre_opcion',
        'proy_op_com.id_op_com','adm_contri.razon_social')
        ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cronog.id_presupuesto')
        ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
        ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
        ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->where([['proy_cronog.estado', '=', 1],['proy_cronog.id_tp_cronog', '=', $tipo]])
                ->orderBy('codigo')
                ->get();
        return response()->json($data);
    }
    public function mostrar_cronograma($id)
    {
        $data = DB::table('proyectos.proy_cronog')
        ->select('proy_cronog.*','proy_op_com.descripcion as nombre_opcion',
        'proy_op_com.id_op_com','adm_contri.razon_social','proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg')
        ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cronog.id_presupuesto')
        ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
        ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
        ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
            ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_cronog.id_cronog', '=', $id]])
                ->get();
        return response()->json($data);
    }
    public function mostrar_cronograma_completo($id)
    {
        $data = DB::table('proyectos.proy_cronog')
            ->select('proy_cronog.*','proy_op_com.descripcion as nombre_opcion',
                'proy_op_com.id_op_com','adm_contri.razon_social','proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg')
                ->join('proyectos.proy_presup','proy_presup.id_presupuesto','=','proy_cronog.id_presupuesto')
                ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
                ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                    ->where([['proy_cronog.id_cronog', '=', $id]])
                    ->first();

        $part_cd = DB::table('proyectos.proy_cd_pcronog')
                ->select('proy_cd_pcronog.*','alm_und_medida.abreviatura','proy_cd_partida.cantidad',
                'proy_cd_partida.codigo','proy_cd_partida.descripcion','proy_cd_partida.cod_compo')
                ->join('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                    ->where([['proy_cd_pcronog.id_cronog', '=', $id]])
                    ->orderBy('fecha_inicio','asc')
                    ->get()
                    ->toArray();

        $part_ci = DB::table('proyectos.proy_ci_pcronog')
                ->select('proy_ci_pcronog.*','alm_und_medida.abreviatura','proy_ci_detalle.cantidad',
                'proy_ci_detalle.codigo','proy_ci_detalle.descripcion','proy_ci_detalle.cod_compo')
                ->join('proyectos.proy_ci_detalle','proy_ci_detalle.id_ci_detalle','=','proy_ci_pcronog.id_partida')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
                    ->where([['proy_ci_pcronog.id_cronog', '=', $id]])
                    ->orderBy('fecha_inicio','asc')
                    ->get()
                    ->toArray();

        $part_gg = DB::table('proyectos.proy_gg_pcronog')
                ->select('proy_gg_pcronog.*','alm_und_medida.abreviatura','proy_gg_detalle.cantidad',
                'proy_gg_detalle.codigo','proy_gg_detalle.descripcion','proy_gg_detalle.cod_compo')
                ->join('proyectos.proy_gg_detalle','proy_gg_detalle.id_gg_detalle','=','proy_gg_pcronog.id_partida')
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
                    ->where([['proy_gg_pcronog.id_cronog', '=', $id]])
                    ->orderBy('fecha_inicio','asc')
                    ->get()
                    ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
                ->where('proy_cd_compo.id_cd', '=', $data->id_cd)
                ->get();

        $componentes_cd = [];
        $array = [];

        foreach ($compo_cd as $comp){
            $fecha='';
            $duracion=0;
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $duracion += $partida->dias;
                    if ($fecha == ''){
                        $fecha = $partida->fecha_inicio;
                    }
                }
            }
            $nuevo_comp = [
                "id_cd_compo"=>$comp->id_cd_compo,
                "id_cd"=>$comp->id_cd,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "fecha_inicio"=>$fecha,
                "duracion"=>$duracion,
                "partidas"=>$array
            ];

            $array = [];
            array_push($componentes_cd,$nuevo_comp);
        }
        
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
                ->where([['proy_ci_compo.id_ci', '=', $data->id_ci]])
                ->get();

        $componentes_ci = [];
        $array = [];

        foreach ($compo_ci as $comp){
            $fecha='';
            $duracion=0;
            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $duracion += $partida->dias;
                    if ($fecha == ''){
                        $fecha = $partida->fecha_inicio;
                    }
                }
            }
            $nuevo_comp = [
                "id_ci_compo"=>$comp->id_ci_compo,
                "id_ci"=>$comp->id_ci,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "fecha_inicio"=>$fecha,
                "duracion"=>$duracion,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_ci,$nuevo_comp);
        }

        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
                ->where([['proy_gg_compo.id_gg', '=', $data->id_gg]])
                ->get();

        $componentes_gg = [];
        $array = [];

        foreach ($compo_gg as $comp){
            $fecha='';
            $duracion=0;
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $duracion += $partida->dias;
                    if ($fecha == ''){
                        $fecha = $partida->fecha_inicio;
                    }
                }
            }
            $nuevo_comp = [
                "id_gg_compo"=>$comp->id_gg_compo,
                "id_gg"=>$comp->id_gg,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "fecha_inicio"=>$fecha,
                "duracion"=>$duracion,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_gg,$nuevo_comp);
        }

        $cd = ["id_cd"=>$data->id_cd,"componentes_cd"=>$componentes_cd,"partidas_cd"=>$part_cd];
        $ci = ["id_ci"=>$data->id_ci,"componentes_ci"=>$componentes_ci,"partidas_ci"=>$part_ci];
        $gg = ["id_gg"=>$data->id_gg,"componentes_gg"=>$componentes_gg,"partidas_gg"=>$part_gg];

        // return response()->json(["cronograma"=>$data]);
        return response()->json(["cronograma"=>$data,"cd"=>$cd,"ci"=>$ci,"gg"=>$gg]);
    }
    public function getPartidasCDByCronograma($id)
    {
        $data = DB::table('proyectos.proy_cd_pcronog')
            ->select('proy_cd_pcronog.*','alm_und_medida.abreviatura','proy_cd_partida.cantidad',
            'proy_cd_partida.codigo','proy_cd_partida.descripcion')
            ->join('proyectos.proy_cd_partida','proy_cd_partida.id_partida','=','proy_cd_pcronog.id_partida')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                ->where([['proy_cd_pcronog.id_cronog', '=', $id]])
                ->get()
                ->toArray();
        return response()->json($data);
    }
    public function getPartidasCIByCronograma($id)
    {
        $data = DB::table('proyectos.proy_ci_pcronog')
            ->select('proy_ci_pcronog.*','alm_und_medida.abreviatura','proy_ci_detalle.cantidad',
            'proy_ci_detalle.codigo','proy_ci_detalle.descripcion')
            ->join('proyectos.proy_ci_detalle','proy_ci_detalle.id_ci_detalle','=','proy_ci_pcronog.id_partida')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
                ->where([['proy_ci_pcronog.id_cronog', '=', $id]])
                ->get()
                ->toArray();
        return response()->json($data);
    }
    public function getPartidasGGByCronograma($id)
    {
        $data = DB::table('proyectos.proy_gg_pcronog')
            ->select('proy_gg_pcronog.*','alm_und_medida.abreviatura','proy_gg_detalle.cantidad',
            'proy_gg_detalle.codigo','proy_gg_detalle.descripcion')
            ->join('proyectos.proy_gg_detalle','proy_gg_detalle.id_gg_detalle','=','proy_gg_pcronog.id_partida')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
                ->where([['proy_gg_pcronog.id_cronog', '=', $id]])
                ->get()
                ->toArray();
        return response()->json($data);
    }
    public function nextCronograma($id,$emp,$fecha)
    {
        $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_tp = '';
        $code_emp = '';
        $result = '';

        $code_tp = DB::table('proyectos.proy_tp_pres')
        ->select('codigo')
        ->where('id_tp_pres', '=', $id)
        ->first();

        $code_emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', '=', $emp)
        ->first();

        $data = DB::table('proyectos.proy_cronog')
                ->where([['id_tp_cronog', '=', $id],['id_empresa','=',$emp]])
                ->whereMonth('fecha_inicio', '=', $mes)
                ->whereYear('fecha_inicio', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = $code_tp->codigo."/".$code_emp->codigo."-".$anio."".$mes."".$number;
        
        return $result;
    }

    public function guardar_cronograma(Request $request){

        $cod = $this->nextCronograma(
            $request->id_tp_cronog,
            $request->id_empresa,
            $request->fecha_inicio
        );
        $id_cronog = DB::table('proyectos.proy_cronog')->insertGetId(
            [
                'id_presupuesto' => $request->id_presupuesto,
                'unid_program' => $request->unid_program,
                'fecha_inicio' => $request->fecha_inicio,
                'estado' => $request->estado,
                'fecha_registro' => $request->fecha_registro,
                'codigo' => $cod,
                'id_tp_cronog' => $request->id_tp_cronog,
                'id_empresa' => $request->id_empresa
            ],
                'id_cronog'
        );

        $detalle_cd = $request->cd_partidas;
        $detalle_cdArray = json_decode($detalle_cd, true);
        $count_det_cd = count($detalle_cdArray);

        if ($count_det_cd > 0){
            for ($i=0; $i<$count_det_cd; $i++){

                DB::table('proyectos.proy_cd_pcronog')->insert(
                    [
                        'id_partida'=>$detalle_cdArray[$i]['id_partida'],
                        'id_cronog'=>$id_cronog,
                        'fecha_inicio'=>$detalle_cdArray[$i]['fecha_inicio'],
                        'fecha_fin'=>$detalle_cdArray[$i]['fecha_fin'],
                        'predecesora'=>$detalle_cdArray[$i]['predecesora'],
                        'dias'=>$detalle_cdArray[$i]['dias'],
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        
        // $id = $request->cd_id_pcronog;
        // $count = count($id);

        // for ($i=0; $i<$count; $i++){

        //     $id_partida     = $request->cd_id_partida[$i];
        //     $fecha_inicio   = $request->cd_fecha_inicio[$i];
        //     $fecha_fin      = $request->cd_fecha_fin[$i];
        //     $predecesora    = $request->cd_predecesora[$i];
        //     $dias           = $request->cd_dias[$i];

        //     $id_pcronog = DB::table('proyectos.proy_cd_pcronog')->insertGetId(
        //         [
        //             'id_partida'=>$id_partida,
        //             'id_cronog'=>$id_cronog,
        //             'fecha_inicio'=>$fecha_inicio,
        //             'fecha_fin'=>$fecha_fin,
        //             'predecesora'=>$predecesora,
        //             'dias'=>$dias,
        //             'estado'=>$request->estado,
        //             'fecha_registro'=>$request->fecha_registro
        //         ],
        //             'id_pcronog'
        //     );
        // }

        $detalle_ci = $request->ci_partidas;
        $detalle_ciArray = json_decode($detalle_ci, true);
        $count_det_ci = count($detalle_ciArray);

        if ($count_det_ci > 0){
            for ($i=0; $i<$count_det_ci; $i++){

                DB::table('proyectos.proy_ci_pcronog')->insert(
                    [
                        'id_partida'=>$detalle_ciArray[$i]['id_partida'],
                        'id_cronog'=>$id_cronog,
                        'fecha_inicio'=>$detalle_ciArray[$i]['fecha_inicio'],
                        'fecha_fin'=>$detalle_ciArray[$i]['fecha_fin'],
                        'predecesora'=>$detalle_ciArray[$i]['predecesora'],
                        'dias'=>$detalle_ciArray[$i]['dias'],
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }
        // $id = $request->ci_id_pcronog;
        // $count = count($id);

        // for ($i=0; $i<$count; $i++){

        //     $id_partida     = $request->ci_id_partida[$i];
        //     $fecha_inicio   = $request->ci_fecha_inicio[$i];
        //     $fecha_fin      = $request->ci_fecha_fin[$i];
        //     $predecesora    = $request->ci_predecesora[$i];
        //     $dias           = $request->ci_dias[$i];

        //     $id_pcronog = DB::table('proyectos.proy_ci_pcronog')->insertGetId(
        //         [
        //             'id_partida'=>$id_partida,
        //             'id_cronog'=>$id_cronog,
        //             'fecha_inicio'=>$fecha_inicio,
        //             'fecha_fin'=>$fecha_fin,
        //             'predecesora'=>$predecesora,
        //             'dias'=>$dias,
        //             'estado'=>$request->estado,
        //             'fecha_registro'=>$request->fecha_registro
        //         ],
        //             'id_pcronog'
        //     );
        // }

        $detalle_gg = $request->gg_partidas;
        $detalle_ggArray = json_decode($detalle_gg, true);
        $count_det_gg = count($detalle_ggArray);

        if ($count_det_gg > 0){
            for ($i=0; $i<$count_det_gg; $i++){

                DB::table('proyectos.proy_gg_pcronog')->insert(
                    [
                        'id_partida'=>$detalle_ggArray[$i]['id_partida'],
                        'id_cronog'=>$id_cronog,
                        'fecha_inicio'=>$detalle_ggArray[$i]['fecha_inicio'],
                        'fecha_fin'=>$detalle_ggArray[$i]['fecha_fin'],
                        'predecesora'=>$detalle_ggArray[$i]['predecesora'],
                        'dias'=>$detalle_ggArray[$i]['dias'],
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        // $id = $request->gg_id_pcronog;
        // $count = count($id);

        // for ($i=0; $i<$count; $i++){

        //     $id_partida     = $request->gg_id_partida[$i];
        //     $fecha_inicio   = $request->gg_fecha_inicio[$i];
        //     $fecha_fin      = $request->gg_fecha_fin[$i];
        //     $predecesora    = $request->gg_predecesora[$i];
        //     $dias           = $request->gg_dias[$i];

        //     $id_pcronog = DB::table('proyectos.proy_gg_pcronog')->insertGetId(
        //         [
        //             'id_partida'=>$id_partida,
        //             'id_cronog'=>$id_cronog,
        //             'fecha_inicio'=>$fecha_inicio,
        //             'fecha_fin'=>$fecha_fin,
        //             'predecesora'=>$predecesora,
        //             'dias'=>$dias,
        //             'estado'=>$request->estado,
        //             'fecha_registro'=>$request->fecha_registro
        //         ],
        //             'id_pcronog'
        //     );
        // }
        return response()->json($id_cronog);
    }

    public function update_cronograma(Request $request, $id){

        $data = DB::table('proyectos.proy_cronog')
        ->where('id_cronog', $id)
        ->update([
                'id_presupuesto' => $request->id_presupuesto,
                'unid_program' => $request->unid_program,
                'fecha_inicio' => $request->fecha_inicio,
                'id_tp_cronog' => $request->id_tp_cronog,
                'id_empresa' => $request->id_empresa,
                'estado' => $request->estado
            ]
        );

        $idd = $request->cd_id_pcronog;
        $count = count($idd);

        for ($i=0; $i<$count; $i++){

            $id_pcronog     = $request->cd_id_pcronog[$i];
            $id_partida     = $request->cd_id_partida[$i];
            $fecha_inicio   = $request->cd_fecha_inicio[$i];
            $fecha_fin      = $request->cd_fecha_fin[$i];
            $predecesora    = $request->cd_predecesora[$i];
            $dias           = $request->cd_dias[$i];

            if ($id_pcronog === 0){
                DB::table('proyectos.proy_cd_pcronog')->insert(
                    [
                        'id_partida'=>$id_partida,
                        'id_cronog'=>$id,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'predecesora'=>$predecesora,
                        'dias'=>$dias,
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
            else {
                DB::table('proyectos.proy_cd_pcronog')
                ->where('id_pcronog',$id_pcronog)
                ->update([
                        'id_partida'=>$id_partida,
                        // 'id_cronog'=>$id,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'predecesora'=>$predecesora,
                        'dias'=>$dias,
                        'estado'=>$request->estado
                        // 'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        $idi = $request->ci_id_pcronog;
        $count1 = count($idi);

        for ($i=0; $i<$count1; $i++){

            $id_pcronog     = $request->ci_id_pcronog[$i];
            $id_partida     = $request->ci_id_partida[$i];
            $fecha_inicio   = $request->ci_fecha_inicio[$i];
            $fecha_fin      = $request->ci_fecha_fin[$i];
            $predecesora    = $request->ci_predecesora[$i];
            $dias           = $request->ci_dias[$i];

            if ($id_pcronog === 0){
                DB::table('proyectos.proy_ci_pcronog')->insert(
                    [
                        'id_partida'=>$id_partida,
                        'id_cronog'=>$id,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'predecesora'=>$predecesora,
                        'dias'=>$dias,
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
            else {
                DB::table('proyectos.proy_ci_pcronog')
                ->where('id_pcronog',$id_pcronog)
                ->update([
                        'id_partida'=>$id_partida,
                        // 'id_cronog'=>$id_cronog,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'predecesora'=>$predecesora,
                        'dias'=>$dias,
                        'estado'=>$request->estado
                        // 'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        $idg = $request->gg_id_pcronog;
        $count2 = count($idg);

        for ($i=0; $i<$count2; $i++){

            $id_pcronog     = $request->gg_id_pcronog[$i];
            $id_partida     = $request->gg_id_partida[$i];
            $fecha_inicio   = $request->gg_fecha_inicio[$i];
            $fecha_fin      = $request->gg_fecha_fin[$i];
            $predecesora    = $request->gg_predecesora[$i];
            $dias           = $request->gg_dias[$i];

            if ($id_pcronog === 0){
                DB::table('proyectos.proy_gg_pcronog')->insert(
                    [
                        'id_partida'=>$id_partida,
                        'id_cronog'=>$id,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'predecesora'=>$predecesora,
                        'dias'=>$dias,
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
            else {
                DB::table('proyectos.proy_gg_pcronog')
                ->where('id_pcronog',$id_pcronog)
                ->update([
                        'id_partida'=>$id_partida,
                        // 'id_cronog'=>$id_cronog,
                        'fecha_inicio'=>$fecha_inicio,
                        'fecha_fin'=>$fecha_fin,
                        'predecesora'=>$predecesora,
                        'dias'=>$dias,
                        'estado'=>$request->estado
                        // 'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }
        $anucd = $request->anu_cd;
        $cnt = count($anucd);

        for($i=0; $i<$cnt; $i++){
            $idp = $request->anu_cd[$i];

            DB::table('proyectos.proy_cd_pcronog')
            ->where('id_pcronog', '=', $idp)
            ->delete();
        }

        $anuci = $request->anu_ci;
        $cnt1 = count($anuci);

        for($i=0; $i<$cnt1; $i++){
            $idp = $request->anu_ci[$i];

            DB::table('proyectos.proy_ci_pcronog')
            ->where('id_pcronog', '=', $idp)
            ->delete();
        }

        $anugg = $request->anu_gg;
        $cnt2 = count($anugg);

        for($i=0; $i<$cnt2; $i++){
            $idp = $request->anu_gg[$i];

            DB::table('proyectos.proy_gg_pcronog')
            ->where('id_pcronog', '=', $idp)
            ->delete();
        }
        return response()->json($data);
    }
    public function nextDesembolso($emp,$fecha)
    {
        $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_emp = '';
        $result = '';

        $empresa = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa','=',$emp)
        ->get();
        foreach ($empresa as $rowEmp) {
            $code_emp = $rowEmp->codigo;
        }
        $data = DB::table('proyectos.proy_desem')
                ->where('id_empresa','=',$emp)
                ->whereMonth('fecha_inicio', '=', $mes)
                ->whereYear('fecha_inicio', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);//$code_tp."/".
        $result = $code_emp."-".$anio."".$mes."".$number;
        // $result = $emp."-".$mes."-".$yyyy;

        return $result;
    }

    public function guardar_desembolso(Request $request){

        $cod = $this->nextDesembolso(
            $request->id_empresa,
            $request->fecha_inicio
        );
        $id_desem = DB::table('proyectos.proy_desem')->insertGetId(
            [
                'id_presupuesto' => $request->id_presupuesto,
                'unid_program' => $request->unid_program,
                'id_residente' => $request->id_residente,
                'elaborado_por' => $request->elaborado_por,
                'fecha_inicio' => $request->fecha_inicio,
                'estado' => $request->estado,
                'fecha_registro' => $request->fecha_registro,
                'id_empresa' => $request->id_empresa,
                'codigo' => $cod,
            ],
                'id_desem'
        );

        $detalle_cd = $request->cd_partidas;
        $detalle_cdArray = json_decode($detalle_cd, true);
        $count_det = count($detalle_cdArray);

        if ($count_det > 0){
            for ($i=0; $i<$count_det; $i++){
    
                DB::table('proyectos.proy_cd_pdesem')->insert(
                    [
                        'id_desem'=>$id_desem,
                        'id_partida'=>$detalle_cdArray[$i]['id_partida'],
                        'fecha_desem'=>$detalle_cdArray[$i]['fecha_desem'],
                        'porcentaje'=>$detalle_cdArray[$i]['porcentaje'],
                        'importe'=>$detalle_cdArray[$i]['importe'],
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        $detalle_ci = $request->ci_partidas;
        $detalle_ciArray = json_decode($detalle_ci, true);
        $count_det = count($detalle_ciArray);

        if ($count_det > 0){
            for ($i=0; $i<$count_det; $i++){
    
                DB::table('proyectos.proy_ci_pdesem')->insert(
                    [
                        'id_desem'=>$id_desem,
                        'id_partida'=>$detalle_ciArray[$i]['id_partida'],
                        'fecha_desem'=>$detalle_ciArray[$i]['fecha_desem'],
                        'porcentaje'=>$detalle_ciArray[$i]['porcentaje'],
                        'importe'=>$detalle_ciArray[$i]['importe'],
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        $detalle_gg = $request->gg_partidas;
        $detalle_ggArray = json_decode($detalle_gg, true);
        $count_det = count($detalle_ggArray);

        if ($count_det > 0){
            for ($i=0; $i<$count_det; $i++){
    
                DB::table('proyectos.proy_gg_pdesem')->insert(
                    [
                        'id_desem'=>$id_desem,
                        'id_partida'=>$detalle_ggArray[$i]['id_partida'],
                        'fecha_desem'=>$detalle_ggArray[$i]['fecha_desem'],
                        'porcentaje'=>$detalle_ggArray[$i]['porcentaje'],
                        'importe'=>$detalle_ggArray[$i]['importe'],
                        'estado'=>$request->estado,
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        return response()->json($id_desem);
    }
    public function mostrar_desembolsos()
    {
        $data = DB::table('proyectos.proy_desem')
                ->select('proy_desem.*','proy_proyecto.descripcion','proy_presup.codigo as cod_pres')
                ->join('proyectos.proy_presup', 'proy_presup.id_presupuesto', '=', 'proy_desem.id_presupuesto')
                ->join('proyectos.proy_contrato', 'proy_contrato.id_contrato', '=', 'proy_presup.id_contrato')
                ->join('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'proy_contrato.id_proyecto')
                ->where([['proy_desem.estado', '=', 1]])
                ->orderBy('id_desem')
                ->get();
        return response()->json($data);
    }
    public function mostrar_desembolso($id)
    {
        $data = DB::table('proyectos.proy_desem')
                ->select('proy_desem.*','proy_proyecto.descripcion','adm_contri.razon_social',
                'proy_proyecto.plazo_ejecucion','proy_unid_program.descripcion as des_unid_program',
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',
                rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'proy_desem.elaborado_por')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->join('proyectos.proy_presup', 'proy_presup.id_presupuesto', '=', 'proy_desem.id_presupuesto')
                ->join('proyectos.proy_contrato', 'proy_contrato.id_contrato', '=', 'proy_presup.id_contrato')
                ->join('proyectos.proy_proyecto', 'proy_proyecto.id_proyecto', '=', 'proy_presup.id_proyecto')
                ->join('comercial.com_cliente', 'com_cliente.id_cliente', '=', 'proy_proyecto.cliente')
                ->join('contabilidad.adm_contri', 'adm_contri.id_contribuyente', '=', 'com_cliente.id_contribuyente')
                ->join('proyectos.proy_unid_program', 'proy_unid_program.id_unid_program', '=', 'proy_proyecto.unid_program')
                ->where([['proy_desem.id_desem', '=', $id]])
                ->orderBy('id_desem')
                ->get();
        return response()->json($data);
    }
    public function mostrar_residentes()
    {
        $data = DB::table('proyectos.proy_residente')
                ->select('proy_residente.*', 'rrhh_perso.nro_documento', 
                DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'proy_residente.id_trabajador')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->where([['proy_residente.estado', '=', 1]])
                ->orderBy('id_residente')
                ->get();
        return response()->json($data);
    }
    public function mostrar_residente($id)
    {
        $data = DB::table('proyectos.proy_residente')
            ->select('proy_residente.*','rrhh_perso.nro_documento', 'rrhh_postu.telefono',
            DB::raw("CONCAT(rrhh_perso.nombres,' ',rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) 
            AS nombre_trabajador"))
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'proy_residente.id_trabajador')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where([['proy_residente.id_residente', '=', $id]])
            ->get();

        $contratos = DB::table('proyectos.proy_res_con')
            ->select('proy_res_con.*','adm_contri.razon_social','proy_proyecto.descripcion','proy_contrato.nro_contrato',
            'proy_contrato.fecha_contrato', DB::raw("(SELECT proy_presup.codigo FROM proyectos.proy_presup WHERE 
            proy_presup.id_contrato=proy_contrato.id_contrato AND proy_presup.id_tp_presupuesto=3) as cod_presupuesto"))
            ->join('proyectos.proy_contrato','proy_contrato.id_contrato','=','proy_res_con.id_contrato')
            ->join('proyectos.proy_presup','proy_presup.id_contrato','=','proy_contrato.id_contrato')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_contrato.id_proyecto')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->where([['proy_res_con.id_residente', '=', $id]])
            ->get();

        return response()->json(["residente"=>$data,"contratos"=>$contratos]);
    }
    public function guardar_residente(Request $request)
    {
        $id_residente = DB::table('proyectos.proy_residente')->insertGetId(
            [
                'id_trabajador' => $request->id_trabajador,
                'colegiatura' => $request->colegiatura,
                'especialidad' => $request->especialidad,
                'estado' => $request->estado,
                'fecha_registro' => $request->fecha_registro
            ],
                'id_residente'
            );

        $ids = $request->c_id_res_con;
        $count = count($ids);

        for ($i=0; $i<$count; $i++){
            $id_contrato      = $request->c_id_contrato[$i];
            $fecha_inicio     = $request->c_fecha_inicio[$i];
            $fecha_fin        = $request->c_fecha_fin[$i];
            $observacion      = $request->c_observacion[$i];
            $estado           = $request->c_estado[$i];
            $fecha_registro   = $request->c_fecha_registro[$i];
            $usuario_registro = $request->c_usuario_registro[$i];
            
            DB::table('proyectos.proy_res_con')->insert(
                [
                    'id_residente'     => $id_residente,
                    'id_contrato'      => $id_contrato,
                    'fecha_inicio'     => $fecha_inicio,
                    'fecha_fin'        => $fecha_fin,
                    'observacion'      => $observacion,
                    'estado'           => $request->estado,
                    'fecha_registro'   => $request->fecha_registro,
                    'usuario_registro' => $usuario_registro
                ]
            );
        }

        return response()->json($id_residente);
    }
    public function update_residente(Request $request, $id)
    {
        $id_residente = DB::table('proyectos.proy_residente')
        ->where('id_residente', $id)
        ->update([
                'id_trabajador' => $request->id_trabajador,
                'colegiatura' => $request->colegiatura,
                'especialidad' => $request->especialidad,
                'estado' => $request->estado,
                'fecha_registro' => $request->fecha_registro
            ]);

        $ids = $request->c_id_res_con;
        $count = count($ids);

        for ($i=0; $i<$count; $i++){
            $id_res_con       = $request->c_id_res_con[$i];
            $id_contrato      = $request->c_id_contrato[$i];
            $fecha_inicio     = $request->c_fecha_inicio[$i];
            $fecha_fin        = $request->c_fecha_fin[$i];
            $observacion      = $request->c_observacion[$i];
            $estado           = $request->c_estado[$i];
            $fecha_registro   = $request->c_fecha_registro[$i];
            $usuario_registro = $request->c_usuario_registro[$i];
            
            if ($id_res_con === 0){
                DB::table('proyectos.proy_res_con')->insert(
                    [
                        'id_residente'     => $id_residente,
                        'id_contrato'      => $id_contrato,
                        'fecha_inicio'     => $fecha_inicio,
                        'fecha_fin'        => $fecha_fin,
                        'observacion'      => $observacion,
                        'estado'           => $request->estado,
                        'fecha_registro'   => $request->fecha_registro,
                        'usuario_registro' => $usuario_registro
                    ]
                );    
            }
            else {
                DB::table('proyectos.proy_res_con')
                ->where('id_res_con',$id_res_con)
                ->update([
                        // 'id_residente'     => $id_residente,
                        'id_contrato'      => $id_contrato,
                        'fecha_inicio'     => $fecha_inicio,
                        'fecha_fin'        => $fecha_fin,
                        'observacion'      => $observacion,
                        'estado'           => $request->estado,
                        'fecha_registro'   => $request->fecha_registro,
                        'usuario_registro' => $usuario_registro
                    ]);
            }
        }

        return response()->json($id_residente);
    }
    public function anular_residente(Request $request, $id)
    {
        $id_residente = DB::table('proyectos.proy_residente')
                ->where('id_residente',$id)
                ->update([ 'estado' => 2 ]);

        $detalle = DB::table('proyectos.proy_res_con')
                ->where('id_residente',$id)
                ->update([ 'estado' => 2 ]);

        return response()->json($id_residente);
    }
    public function mostrar_portafolios()
    {
        $data = DB::table('proyectos.proy_portafolio')
                ->select('proy_portafolio.*', DB::raw("CONCAT(rrhh_perso.nombres,' ',
                rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_trabajador"))
                ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'proy_portafolio.responsable')
                ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
                ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
                ->where([['proy_portafolio.estado', '=', 1]])
                ->orderBy('id_portafolio')
                ->get();
        return response()->json($data);
    }
    public function mostrar_portafolio($id)
    {
        $data = DB::table('proyectos.proy_portafolio')
            ->select('proy_portafolio.*',DB::raw("CONCAT(rrhh_perso.nombres,' ',
            rrhh_perso.apellido_paterno,' ',rrhh_perso.apellido_materno) AS nombre_trabajador"))
            ->join('rrhh.rrhh_trab', 'rrhh_trab.id_trabajador', '=', 'proy_portafolio.responsable')
            ->join('rrhh.rrhh_postu', 'rrhh_postu.id_postulante', '=', 'rrhh_trab.id_postulante')
            ->join('rrhh.rrhh_perso', 'rrhh_perso.id_persona', '=', 'rrhh_postu.id_persona')
            ->where([['proy_portafolio.id_portafolio', '=', $id]])
            ->get();

        $detalle = DB::table('proyectos.proy_porta_detalle')
            ->select('proy_porta_detalle.*','adm_contri.razon_social',
            'proy_proyecto.descripcion','proy_proyecto.codigo')
            ->join('proyectos.proy_proyecto','proy_proyecto.id_proyecto','=','proy_porta_detalle.id_proyecto')
            ->join('comercial.com_cliente','proy_proyecto.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->where([['proy_porta_detalle.id_portafolio', '=', $id]])
            ->get();

        return response()->json(["portafolio"=>$data,"detalle"=>$detalle]);
    }

    public function nextPortafolio($id_emp,$fecha)
    {
        $mes = date('m',strtotime($fecha));
        $yyyy = date('Y',strtotime($fecha));
        $anio = date('y',strtotime($fecha));
        $code_emp = '';
        $result = '';

        $emp = DB::table('administracion.adm_empresa')
        ->select('codigo')
        ->where('id_empresa', '=', $id_emp)
        ->get();
        foreach ($emp as $rowEmp) {
            $code_emp = $rowEmp->codigo;
        }
        $data = DB::table('proyectos.proy_portafolio')
                ->where('id_empresa', '=', $id_emp)
                ->whereMonth('fecha_emision', '=', $mes)
                ->whereYear('fecha_emision', '=', $yyyy)
                ->count();

        $number = $this->leftZero(3,$data+1);
        $result = "GP/".$code_emp."-".$anio."".$mes."".$number;

        return $result;
    }

    public function guardar_portafolio(Request $request)
    {
        $codigo = $this->nextPortafolio($request->id_empresa,$request->fecha_emision);

        $id_portafolio = DB::table('proyectos.proy_portafolio')->insertGetId(
            [
                'descripcion' => $request->descripcion,
                'fecha_emision' => $request->fecha_emision,
                'responsable' => $request->responsable,
                'fecha_registro' => $request->fecha_registro,
                'usuario_registro' => $request->usuario_registro,
                'estado' => $request->estado,
                'codigo' => $codigo,
                'id_empresa' => $request->id_empresa
            ],
                'id_portafolio'
            );

        $ids = $request->c_id_detalle;
        $count = count($ids);

        for ($i=0; $i<$count; $i++){
            // $id_portafolio  = $request->c_id_portafolio[$i];
            $id_proyecto    = $request->c_id_proyecto[$i];
            $fecha_registro = $request->c_fecha_registro[$i];
            $estado         = $request->c_estado[$i];
            
            DB::table('proyectos.proy_porta_detalle')->insert(
                [
                    // 'id_detalle'     => $id_detalle,
                    'id_portafolio'  => $id_portafolio,
                    'id_proyecto'    => $id_proyecto,
                    'fecha_registro' => $fecha_registro,
                    'estado'         => $estado,
                ]
            );
        }

        return response()->json($id_portafolio);
    }
    public function update_portafolio(Request $request, $id)
    {
        $id_portafolio = DB::table('proyectos.proy_portafolio')
        ->where('id_portafolio',$id)
        ->update([
                'descripcion' => $request->descripcion,
                'fecha_emision' => $request->fecha_emision,
                'responsable' => $request->responsable,
                'fecha_registro' => $request->fecha_registro,
                'usuario_registro' => $request->usuario_registro,
                'estado' => $request->estado
                // 'codigo' => $codigo,
                // 'id_empresa' => $request->id_empresa
            ]);

        $ids = $request->c_id_detalle;
        $count = count($ids);

        for ($i=0; $i<$count; $i++){
            $id_detalle     = $request->c_id_detalle[$i];
            $id_proyecto    = $request->c_id_proyecto[$i];
            $fecha_registro = $request->c_fecha_registro[$i];
            $estado         = $request->c_estado[$i];
            
            if ($id_detalle === 0){
                DB::table('proyectos.proy_porta_detalle')->insert(
                    [
                        'id_portafolio'  => $id,
                        'id_proyecto'    => $id_proyecto,
                        'fecha_registro' => $fecha_registro,
                        'estado'         => $estado,
                    ]
                );
            }
            else {
                DB::table('proyectos.proy_porta_detalle')
                ->where('id_detalle',$id_detalle)
                ->update([
                        // 'id_portafolio'  => $id_portafolio,
                        'id_proyecto'    => $id_proyecto,
                        'fecha_registro' => $fecha_registro,
                        'estado'         => $estado,
                    ]);
            }
        }

        return response()->json($id_portafolio);
    }
    public function anular_portafolio(Request $request, $id)
    {
        $id_portafolio = DB::table('proyectos.proy_portafolio')
                ->where('id_portafolio',$id)
                ->update([ 'estado' => 2 ]);

        $detalle = DB::table('proyectos.proy_porta_detalle')
                ->where('id_portafolio',$id)
                ->update([ 'estado' => 2 ]);

        return response()->json($id_portafolio);
    }
    //construye la valorizacion
    public function mostrar_pres_valorizacion($id_presupuesto)
    {
        $presupuesto = DB::table('proyectos.proy_presup')
            ->select('proy_presup.id_presupuesto','proy_presup.codigo','proy_presup.fecha_emision',
            'proy_cd.id_cd','proy_ci.id_ci','proy_gg.id_gg','proy_op_com.descripcion as nombre_opcion',
            'sis_moneda.simbolo as moneda','adm_contri.razon_social','proy_presup_importe.*')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
                ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')                
                ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
                ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
                ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
                ->where([['proy_presup.id_presupuesto', '=', $id_presupuesto]])
                ->first();
                
        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.id_partida','proy_cd_partida.codigo','proy_cd_partida.descripcion',
            'proy_cd_partida.cantidad','proy_cd_partida.importe_unitario','proy_cd_partida.importe_parcial',
            'proy_cd_partida.cod_compo','alm_und_medida.abreviatura','proy_cu.rendimiento',
            'proy_cd_pcronog.dias','proy_cd_pcronog.fecha_inicio','proy_cd_pcronog.fecha_fin',
                DB::raw('(SELECT SUM(metrado_actual) FROM proyectos.proy_cd_pvalori 
                    WHERE id_partida = proy_cd_partida.id_partida) AS metrado_anterior'),
                DB::raw('(SELECT SUM(porcen_actual) FROM proyectos.proy_cd_pvalori 
                    WHERE id_partida = proy_cd_partida.id_partida) AS porcen_anterior'),
                DB::raw('(SELECT SUM(costo_actual) FROM proyectos.proy_cd_pvalori 
                    WHERE id_partida = proy_cd_partida.id_partida) AS costo_anterior'),
                DB::raw('0 as metrado_actual'), DB::raw('0 as porcen_actual'), DB::raw('0 as costo_actual'),
                DB::raw('0 as metrado_saldo'), DB::raw('0 as porcen_saldo'), DB::raw('0 as costo_saldo'))
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
                ->join('proyectos.proy_cd_pcronog','proy_cd_pcronog.id_partida','=','proy_cd_partida.id_partida')
                ->where([['proy_cd_partida.id_cd','=',$presupuesto->id_cd],
                            ['proy_cd_pcronog.estado','=',1]])
                ->get()
                ->toArray();

        $part_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_ci_detalle.id_ci_detalle as id_partida','proy_ci_detalle.codigo',
            'proy_ci_detalle.descripcion','proy_ci_detalle.cantidad','proy_ci_detalle.importe_unitario',
            'proy_ci_detalle.importe_parcial','proy_ci_detalle.cod_compo','alm_und_medida.abreviatura',
            'proy_cu.rendimiento','proy_ci_pcronog.dias','proy_ci_pcronog.fecha_inicio',
            'proy_ci_pcronog.fecha_fin',
                DB::raw('(SELECT SUM(metrado_actual) FROM proyectos.proy_ci_pvalori 
                    WHERE id_partida = proy_ci_detalle.id_ci_detalle) AS metrado_anterior'),
                DB::raw('(SELECT SUM(porcen_actual) FROM proyectos.proy_ci_pvalori 
                    WHERE id_partida = proy_ci_detalle.id_ci_detalle) AS porcen_anterior'),
                DB::raw('(SELECT SUM(costo_actual) FROM proyectos.proy_ci_pvalori 
                    WHERE id_partida = proy_ci_detalle.id_ci_detalle) AS costo_anterior'),
                DB::raw('0 as metrado_actual'), DB::raw('0 as porcen_actual'), DB::raw('0 as costo_actual'),
                DB::raw('0 as metrado_saldo'), DB::raw('0 as porcen_saldo'), DB::raw('0 as costo_saldo'))
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_ci_detalle.id_cu')
                ->join('proyectos.proy_ci_pcronog','proy_ci_pcronog.id_partida','=','proy_ci_detalle.id_ci_detalle')
                ->where([['proy_ci_detalle.id_ci','=',$presupuesto->id_ci],
                        ['proy_ci_pcronog.estado','=',1]])
                    ->get()
                    ->toArray();

        $part_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_gg_detalle.id_gg_detalle as id_partida','proy_gg_detalle.codigo',
            'proy_gg_detalle.descripcion','proy_gg_detalle.cantidad','proy_gg_detalle.importe_unitario',
            'proy_gg_detalle.importe_parcial','proy_gg_detalle.cod_compo','alm_und_medida.abreviatura',
            'proy_cu.rendimiento','proy_gg_pcronog.dias','proy_gg_pcronog.fecha_inicio',
            'proy_gg_pcronog.fecha_fin',
                DB::raw('(SELECT SUM(metrado_actual) FROM proyectos.proy_gg_pvalori 
                    WHERE id_partida = proy_gg_detalle.id_gg_detalle) AS metrado_anterior'),
                DB::raw('(SELECT SUM(porcen_actual) FROM proyectos.proy_gg_pvalori 
                    WHERE id_partida = proy_gg_detalle.id_gg_detalle) AS porcen_anterior'),
                DB::raw('(SELECT SUM(costo_actual) FROM proyectos.proy_gg_pvalori 
                    WHERE id_partida = proy_gg_detalle.id_gg_detalle) AS costo_anterior'),
                DB::raw('0 as metrado_actual'), DB::raw('0 as porcen_actual'), DB::raw('0 as costo_actual'),
                DB::raw('0 as metrado_saldo'), DB::raw('0 as porcen_saldo'), DB::raw('0 as costo_saldo'))
                ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
                ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_gg_detalle.id_cu')
                ->join('proyectos.proy_gg_pcronog','proy_gg_pcronog.id_partida','=','proy_gg_detalle.id_gg_detalle')
                ->where([['proy_gg_detalle.id_gg', '=', $presupuesto->id_gg],
                         ['proy_gg_pcronog.estado','=',1]])
                    ->get()
                    ->toArray();

        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
                ->where([['proy_cd_compo.id_cd', '=', $presupuesto->id_cd]])
                ->get()->toArray();

        $componentes_cd = [];
        $array = [];

        foreach ($compo_cd as $comp){
            $total = 0;
            foreach($part_cd as $partidax){
                if ($comp->codigo == $partidax->cod_compo){
                    array_push($array, $partidax);
                    $total += $partidax->importe_parcial;
                }
            }

            $nuevo_comp = [
                "id_cd_compo"=>$comp->id_cd_compo,
                "id_cd"=>$comp->id_cd,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];

            $array = [];
            array_push($componentes_cd,$nuevo_comp);
        }
        
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
                ->where([['proy_ci_compo.id_ci', '=', $presupuesto->id_ci]])
                ->get();

        $componentes_ci = [];
        $array = [];

        foreach ($compo_ci as $comp){
            $total = 0;
            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_ci_compo"=>$comp->id_ci_compo,
                "id_ci"=>$comp->id_ci,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_ci,$nuevo_comp);
        }

        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
                ->where([['proy_gg_compo.id_gg', '=', $presupuesto->id_gg]])
                ->get();

        $componentes_gg = [];
        $array = [];

        foreach ($compo_gg as $comp){
            $total = 0;
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    array_push($array, $partida);
                    $total += $partida->importe_parcial;
                }
            }
            $nuevo_comp = [
                "id_gg_compo"=>$comp->id_gg_compo,
                "id_gg"=>$comp->id_gg,
                "codigo"=>$comp->codigo,
                "descripcion"=>$comp->descripcion,
                "cod_padre"=>$comp->cod_padre,
                "total_comp"=>$total,
                "partidas"=>$array
            ];
            
            $array = [];
            array_push($componentes_gg,$nuevo_comp);
        }

        $cd = ["id_cd"=>$presupuesto->id_cd,"componentes_cd"=>$componentes_cd,"partidas_cd"=>$part_cd];
        $ci = ["id_ci"=>$presupuesto->id_ci,"componentes_ci"=>$componentes_ci,"partidas_ci"=>$part_ci];
        $gg = ["id_gg"=>$presupuesto->id_gg,"componentes_gg"=>$componentes_gg,"partidas_gg"=>$part_gg];

        return response()->json(["presupuesto"=>$presupuesto,"cd"=>$cd,"ci"=>$ci,"gg"=>$gg]);
    }
    public function guardar_valorizacion(Request $request){
        // $codigo = $this->nextPortafolio($request->id_empresa,$request->fecha_emision);

        $id_valorizacion = DB::table('proyectos.proy_valori')->insertGetId(
            [
                'id_presupuesto' => $request->id_presupuesto, 
                'fecha_valorizacion' => $request->fecha_valorizacion, 
                'id_residente' => $request->id_residente, 
                'id_unid_ejec' => $request->id_unid_ejec, 
                'estado' => $request->estado, 
                'fecha_registro' => $request->fecha_registro, 
                'id_tp_val' => $request->id_tp_val, 
                'id_empresa' => $request->id_empresa, 
                'codigo' => $request->codigo, 
                'codigo_entero' => $request->codigo_entero,
            ],
                'id_valorizacion'
            );

        $detalle_cd = $request->cd_partidas;
        $detalle_cdArray = json_decode($detalle_cd, true);
        $count_det_cd = count($detalle_cdArray);

        if ($count_det_cd > 0){
            for ($i=0; $i<$count_det_cd; $i++){
    
                DB::table('proyectos.proy_cd_pvalori')->insert(
                    [
                        // 'id_pvalori'=>$detalle_cdArray[$i]['id_pvalori'], 
                        'id_partida'=>$detalle_cdArray[$i]['id_partida'], 
                        'id_valorizacion'=>$id_valorizacion, 
                        'metrado_anterior'=>$detalle_cdArray[$i]['metrado_anterior'], 
                        'metrado_actual'=>$detalle_cdArray[$i]['metrado_actual'], 
                        'metrado_saldo'=>$detalle_cdArray[$i]['metrado_saldo'], 
                        'porcen_anterior'=>$detalle_cdArray[$i]['porcen_anterior'], 
                        'porcen_actual'=>$detalle_cdArray[$i]['porcen_actual'], 
                        'porcen_saldo'=>$detalle_cdArray[$i]['porcen_saldo'], 
                        'costo_anterior'=>$detalle_cdArray[$i]['costo_anterior'], 
                        'costo_actual'=>$detalle_cdArray[$i]['costo_actual'], 
                        'costo_saldo'=>$detalle_cdArray[$i]['costo_saldo'], 
                        'estado'=>$request->estado, 
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        $detalle_ci = $request->ci_partidas;
        $detalle_ciArray = json_decode($detalle_ci, true);
        $count_det_ci = count($detalle_ciArray);

        if ($count_det_ci > 0){
            for ($i=0; $i<$count_det_ci; $i++){
    
                DB::table('proyectos.proy_ci_pvalori')->insert(
                    [
                        // 'id_pvalori'=>$detalle_ciArray[$i]['id_pvalori'], 
                        'id_partida'=>$detalle_ciArray[$i]['id_partida'], 
                        'id_valorizacion'=>$id_valorizacion, 
                        'metrado_anterior'=>$detalle_ciArray[$i]['metrado_anterior'], 
                        'metrado_actual'=>$detalle_ciArray[$i]['metrado_actual'], 
                        'metrado_saldo'=>$detalle_ciArray[$i]['metrado_saldo'], 
                        'porcen_anterior'=>$detalle_ciArray[$i]['porcen_anterior'], 
                        'porcen_actual'=>$detalle_ciArray[$i]['porcen_actual'], 
                        'porcen_saldo'=>$detalle_ciArray[$i]['porcen_saldo'], 
                        'costo_anterior'=>$detalle_ciArray[$i]['costo_anterior'], 
                        'costo_actual'=>$detalle_ciArray[$i]['costo_actual'], 
                        'costo_saldo'=>$detalle_ciArray[$i]['costo_saldo'], 
                        'estado'=>$request->estado, 
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }

        $detalle_gg = $request->gg_partidas;
        $detalle_ggArray = json_decode($detalle_gg, true);
        $count_det_gg = count($detalle_ggArray);

        if ($count_det_gg > 0){
            for ($i=0; $i<$count_det_gg; $i++){
    
                DB::table('proyectos.proy_gg_pvalori')->insert(
                    [
                        // 'id_pvalori'=>$detalle_ggArray[$i]['id_pvalori'], 
                        'id_partida'=>$detalle_ggArray[$i]['id_partida'], 
                        'id_valorizacion'=>$id_valorizacion, 
                        'metrado_anterior'=>$detalle_ggArray[$i]['metrado_anterior'], 
                        'metrado_actual'=>$detalle_ggArray[$i]['metrado_actual'], 
                        'metrado_saldo'=>$detalle_ggArray[$i]['metrado_saldo'], 
                        'porcen_anterior'=>$detalle_ggArray[$i]['porcen_anterior'], 
                        'porcen_actual'=>$detalle_ggArray[$i]['porcen_actual'], 
                        'porcen_saldo'=>$detalle_ggArray[$i]['porcen_saldo'], 
                        'costo_anterior'=>$detalle_ggArray[$i]['costo_anterior'], 
                        'costo_actual'=>$detalle_ggArray[$i]['costo_actual'], 
                        'costo_saldo'=>$detalle_ggArray[$i]['costo_saldo'], 
                        'estado'=>$request->estado, 
                        'fecha_registro'=>$request->fecha_registro
                    ]
                );
            }
        }
    }
    //NUEVO ERP
    public function listar_contratos_proy($id){
        $data = DB::table('proyectos.proy_contrato')
            ->select('proy_contrato.*','sis_moneda.simbolo','proy_tp_contrato.descripcion as tipo_contrato')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_contrato.moneda')
            ->join('proyectos.proy_tp_contrato','proy_tp_contrato.id_tp_contrato','=','proy_contrato.id_tp_contrato')
            ->where([['proy_contrato.id_proyecto', '=', $id],
                    ['proy_contrato.estado', '=', 1]])
            ->get();

        $html = '';
        foreach($data as $d){
            $html .= '
                <tr id="con-'.$d->id_contrato.'">
                    <td>'.$d->tipo_contrato.'</td>
                    <td>'.$d->nro_contrato.'</td>
                    <td>'.$d->descripcion.'</td>
                    <td>'.$d->fecha_contrato.'</td>
                    <td>'.$d->simbolo.'</td>
                    <td>'.$d->importe.'</td>
                    <td><a href="abrir_adjunto/'.$d->archivo_adjunto.'">'.$d->archivo_adjunto.'</a></td>
                    <td style="display:flex;">
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Item" onClick="anular_contrato('.$d->id_contrato.');"></i>
                    </td>
                </tr>';
        }
        return json_encode($html);
    }
    public function guardar_contrato(Request $request){
        $id_contrato = DB::table('proyectos.proy_contrato')->insertGetId(
                [
                    'nro_contrato' => $request->nro_contrato,
                    'fecha_contrato' => $request->fecha_contrato,
                    'descripcion' => $request->descripcion,
                    'moneda' => $request->moneda,
                    'importe' => $request->importe_contrato,
                    // 'archivo_adjunto' => $nombre,
                    'id_proyecto' => $request->id_proyecto,
                    'id_tp_contrato' => $request->id_tp_contrato,
                    'estado' => 1,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ],
                    'id_contrato'
            );
        //obtenemos el campo file definido en el formulario
        $file = $request->file('adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo
            $nombre = $id_contrato.'.'.$request->nro_contrato.'.'.$file->getClientOriginalName();
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('proyectos/contratos/'.$nombre));
            \Storage::disk('archivos')->put('proyectos/contratos/'.$nombre,\File::get($file));
            
            $update = DB::table('proyectos.proy_contrato')
                ->where('id_contrato', $id_contrato)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        return response()->json($id_contrato);
    }
    public function abrir_adjunto($file_name){
        $file_path = public_path('files/proyectos/contratos/'.$file_name);
        // $result = File::exists('files/proyectos/contratos/'.$file_name);
        if (file_exists($file_path)){
            return response()->download($file_path);
        } else {
            return response()->json("No existe dicho archivo!");
        }
    }
    public function anular_contrato($id_contrato){
        $data = DB::table('proyectos.proy_contrato')
            ->where('proy_contrato.id_contrato', $id_contrato)
            ->update(['estado' => 2]);
        return response()->json($data);
    }
    public function mostrar_presupuestos($tp)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_op_com.descripcion', 'proy_presup_importe.total_presupuestado',
                     'sis_moneda.simbolo','adm_contri.razon_social')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.estado', '=', 1],['proy_presup.id_tp_presupuesto', '=', $tp]])
                ->orderBy('proy_presup.id_presupuesto')
                ->get();
        $output['data'] = $data;
        return response()->json($output);
    }
    public function mostrar_presint($id)
    {
        $data = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*', 'proy_tp_pres.descripcion as tipo_descripcion', 
                     'proy_op_com.descripcion', 'proy_presup_importe.total_costo_directo', 
                     'proy_presup_importe.total_ci', 'proy_presup_importe.porcentaje_ci', 
                     'proy_presup_importe.total_gg', 'proy_presup_importe.porcentaje_gg', 
                     'proy_presup_importe.sub_total', 'proy_presup_importe.porcentaje_utilidad', 
                     'proy_presup_importe.total_utilidad', 'proy_presup_importe.porcentaje_igv', 
                     'proy_presup_importe.total_igv', 'proy_presup_importe.total_presupuestado',
                     'sis_moneda.simbolo','adm_contri.razon_social')
            ->join('proyectos.proy_tp_pres','proy_presup.id_tp_presupuesto','=','proy_tp_pres.id_tp_pres')
            ->join('proyectos.proy_op_com','proy_op_com.id_op_com','=','proy_presup.id_op_com')
            ->join('comercial.com_cliente','proy_op_com.cliente','=','com_cliente.id_cliente')
            ->join('contabilidad.adm_contri','com_cliente.id_contribuyente','=','adm_contri.id_contribuyente')
            ->join('configuracion.sis_moneda','sis_moneda.id_moneda','=','proy_presup.moneda')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_presupuesto', '=', $id]])
                ->first();
        
        if ($data->id_tp_presupuesto == 1){
            return response()->json($data);
        } 
        else if ($data->id_tp_presupuesto == 2){
            $importes_pi = DB::table('proyectos.proy_presup')
                ->select('proy_presup_importe.*')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_op_com', '=', $data->id_op_com],
                            ['proy_presup.id_tp_presupuesto','=',1]])//Pres.Interno
                ->orderBy('proy_presup.id_presupuesto','desc')
                ->first();
    
            return response()->json(['propuesta'=>$data,'importes_pi'=>$importes_pi]);
        }
        else if ($data->id_tp_presupuesto == 3){
            $importes_pc = DB::table('proyectos.proy_presup')
                ->select('proy_presup_importe.*')
                ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
                ->where([['proy_presup.id_op_com', '=', $data->id_op_com],
                            ['proy_presup.id_tp_presupuesto','=',2]])//Propuesta
                ->orderBy('proy_presup.id_presupuesto','desc')
                ->first();
    
            return response()->json(['preseje'=>$data,'importes_pc'=>$importes_pc]);
        }
    }
    public function listar_cd($id)
    {
        // $cd = DB::table('proyectos.proy_presup')
        //     ->select('proy_cd.id_cd')
        //     ->join('proyectos.proy_cd','proy_cd.id_presupuesto','=','proy_presup.id_presupuesto')
        //     ->where([['proy_presup.id_presupuesto', '=', $id]])//Pres.Interno
        //     ->first();

        $part_cd = DB::table('proyectos.proy_cd_partida')
            ->select('proy_cd_partida.*','proy_sis_contrato.descripcion as nombre_sistema',
            'alm_und_medida.abreviatura','proy_cu.rendimiento','proy_cu.codigo as cod_acu')
            ->join('proyectos.proy_sis_contrato','proy_sis_contrato.id_sis_contrato','=','proy_cd_partida.id_sistema')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_cd_partida.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_cd_partida.id_cu')
            ->where([['proy_cd_partida.id_cd', '=', $id],
                    ['proy_cd_partida.estado', '=', 1]])
            ->orderBy('proy_cd_partida.codigo')
            ->get()
            ->toArray();
            
        $compo_cd = DB::table('proyectos.proy_cd_compo')
            ->select('proy_cd_compo.*')
            ->where([['proy_cd_compo.id_cd', '=', $id],
                    ['proy_cd_compo.estado', '=', 1]])
            ->orderBy('proy_cd_compo.codigo')
            ->get();
    
        $componentes_cd = [];
        $array = [];
        $html = '';
        $tipo = "'cd'";

        foreach ($compo_cd as $comp){
            $total = 0;
            $codigo = "'".$comp->codigo."'";
            $html .= '
            <tr id="com-'.$comp->id_cd_compo.'">
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$comp->total_comp.'</td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Componente" onClick="agregar_compo_cd('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_cd('.$codigo.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Componente" onClick="editar_compo_cd('.$comp->id_cd_compo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Componente" onClick="update_compo_cd('.$comp->id_cd_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Componente" onClick="anular_compo_cd('.$comp->id_cd_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';
            foreach($part_cd as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $html .= '
                    <tr id="par-'.$partida->id_partida.'">
                        <td id="cu-'.$partida->id_cu.'">'.$partida->codigo.'</td>
                        <td id="ccu-'.$partida->cod_acu.'">'.$partida->descripcion.'</td>
                        <td id="abr-'.$partida->unid_medida.'">'.$partida->abreviatura.'</td>
                        <td>'.$partida->cantidad.'</td>
                        <td>'.$partida->importe_unitario.'</td>
                        <td>'.$partida->importe_parcial.'</td>
                        <td id="sis-'.$partida->id_sistema.'">'.$partida->nombre_sistema.'</td>
                        <td style="display:flex;">
                        <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_cd('.$partida->id_partida.');"></i>
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_cd('.$partida->id_partida.');"></i>
                        <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('.$tipo.','.$partida->id_partida.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        return json_encode($html);
    }
    public function listar_ci($id)
    {
        // $ci = DB::table('proyectos.proy_presup')
        //     ->select('proy_ci.id_ci')
        //     ->join('proyectos.proy_ci','proy_ci.id_presupuesto','=','proy_presup.id_presupuesto')
        //     ->where([['proy_presup.id_presupuesto', '=', $id]])
        //     ->first();

        $part_ci = DB::table('proyectos.proy_ci_detalle')
            ->select('proy_ci_detalle.*',
            'alm_und_medida.abreviatura','proy_cu.rendimiento','proy_cu.codigo as cod_acu')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_ci_detalle.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_ci_detalle.id_cu')
            ->where([['proy_ci_detalle.id_ci', '=', $id],
                    ['proy_ci_detalle.estado', '=', 1]])
            ->orderBy('proy_ci_detalle.codigo')
            ->get()
            ->toArray();
            
        $compo_ci = DB::table('proyectos.proy_ci_compo')
            ->select('proy_ci_compo.*')
            ->where([['proy_ci_compo.id_ci', '=', $id],
                    ['proy_ci_compo.estado', '=', 1]])
            ->orderBy('proy_ci_compo.codigo')
            ->get();
    
        $componentes_ci = [];
        $array = [];
        $html = '';
        $tipo = "'ci'";

        foreach ($compo_ci as $comp){
            $total = 0;
            $codigo = "'".$comp->codigo."'";
            $html .= '
            <tr id="com-'.$comp->id_ci_compo.'">
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$comp->total_comp.'</td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Componente" onClick="agregar_compo_ci('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_ci('.$codigo.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Componente" onClick="editar_compo_ci('.$comp->id_ci_compo.');"></i>
                    <i class="fas fa-save icon-tabla green oculto boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Componente" onClick="update_compo_ci('.$comp->id_ci_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Componente" onClick="anular_compo_ci('.$comp->id_ci_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';
            foreach($part_ci as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $html .= '
                    <tr id="par-'.$partida->id_ci_detalle.'">
                        <td id="cu-'.$partida->id_cu.'">'.$partida->codigo.'</td>
                        <td id="ccu-'.$partida->cod_acu.'">'.$partida->descripcion.'</td>
                        <td id="abr-'.$partida->unid_medida.'">'.$partida->abreviatura.'</td>
                        <td>'.$partida->cantidad.'</td>
                        <td>'.$partida->importe_unitario.'</td>
                        <td>'.$partida->participacion.'</td>
                        <td>'.$partida->tiempo.'</td>
                        <td>'.$partida->veces.'</td>
                        <td>'.$partida->importe_parcial.'</td>
                        <td></td>
                        <td style="display:flex;">
                        <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_ci('.$partida->id_ci_detalle.');"></i>
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_ci('.$partida->id_ci_detalle.');"></i>
                        <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('.$tipo.','.$partida->id_ci_detalle.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        return json_encode($html);
    }
    public function listar_gg($id)
    {
        // $ci = DB::table('proyectos.proy_presup')
        //     ->select('proy_gg.id_gg')
        //     ->join('proyectos.proy_gg','proy_gg.id_presupuesto','=','proy_presup.id_presupuesto')
        //     ->where([['proy_presup.id_presupuesto', '=', $id]])
        //     ->first();

        $part_gg = DB::table('proyectos.proy_gg_detalle')
            ->select('proy_gg_detalle.*',
            'alm_und_medida.abreviatura','proy_cu.rendimiento','proy_cu.codigo as cod_acu')
            ->join('almacen.alm_und_medida','alm_und_medida.id_unidad_medida','=','proy_gg_detalle.unid_medida')
            ->join('proyectos.proy_cu','proy_cu.id_cu','=','proy_gg_detalle.id_cu')
            ->where([['proy_gg_detalle.id_gg', '=', $id],
                    ['proy_gg_detalle.estado', '=', 1]])
            ->orderBy('proy_gg_detalle.codigo')
            ->get()
            ->toArray();
            
        $compo_gg = DB::table('proyectos.proy_gg_compo')
            ->select('proy_gg_compo.*')
            ->where([['proy_gg_compo.id_gg', '=', $id],
                    ['proy_gg_compo.estado', '=', 1]])
            ->orderBy('proy_gg_compo.codigo')
            ->get();
    
        $componentes_gg = [];
        $array = [];
        $html = '';
        $tipo = "'gg'";

        foreach ($compo_gg as $comp){
            $total = 0;
            $codigo = "'".$comp->codigo."'";
            $html .= '
            <tr id="com-'.$comp->id_gg_compo.'">
                <td>'.$comp->codigo.'</td>
                <td>
                    <input type="text" class="input-data" name="descripcion" 
                    value="'.$comp->descripcion.'" disabled="true"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$comp->total_comp.'</td>
                <td style="display:flex;">
                    <i class="fas fa-plus-square icon-tabla green boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Componente" onClick="agregar_compo_gg('.$codigo.')"></i>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Agregar Partida" onClick="agrega_partida_gg('.$codigo.');"></i>
                    <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Editar Componente" onClick="editar_compo_gg('.$comp->id_gg_compo.');"></i>
                    <i class="fas fa-save icon-tabla green boton oculto" data-toggle="tooltip" data-placement="bottom" 
                        title="Guardar Componente" onClick="update_compo_gg('.$comp->id_gg_compo.');"></i>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                        title="Anular Componente" onClick="anular_compo_gg('.$comp->id_gg_compo.','.$codigo.');"></i>
                </td>
                <td hidden>'.$comp->cod_padre.'</td>
            </tr>';
            foreach($part_gg as $partida){
                if ($comp->codigo == $partida->cod_compo){
                    $total += $partida->importe_parcial;
                    $html .= '
                    <tr id="par-'.$partida->id_gg_detalle.'">
                        <td id="cu-'.$partida->id_cu.'">'.$partida->codigo.'</td>
                        <td id="ccu-'.$partida->cod_acu.'">'.$partida->descripcion.'</td>
                        <td id="abr-'.$partida->unid_medida.'">'.$partida->abreviatura.'</td>
                        <td>'.$partida->cantidad.'</td>
                        <td>'.$partida->importe_unitario.'</td>
                        <td>'.$partida->participacion.'</td>
                        <td>'.$partida->tiempo.'</td>
                        <td>'.$partida->veces.'</td>
                        <td>'.$partida->importe_parcial.'</td>
                        <td></td>
                        <td style="display:flex;">
                        <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_partida_gg('.$partida->id_gg_detalle.');"></i>
                        <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_partida_gg('.$partida->id_gg_detalle.');"></i>
                        <i class="fas fa-file-alt icon-tabla orange boton" data-toggle="tooltip" data-placement="bottom" title="Lecciones Aprendidas" onClick="open_presLeccion('.$tipo.','.$partida->id_gg_detalle.');"></i>
                        </td>
                        <td hidden>'.$partida->cod_compo.'</td>
                    </tr>';
                }
            }
        }
        return json_encode($html);
    }

    public function guardar_componente_cd(Request $request)
    {
        $data = DB::table('proyectos.proy_cd_compo')
            ->insertGetId([
                'id_cd' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_cd_compo'
            );
        return response()->json($data);
    }
    public function guardar_componente_ci(Request $request)
    {
        $data = DB::table('proyectos.proy_ci_compo')
            ->insertGetId([
                'id_ci' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_ci_compo'
            );
        return response()->json($data);
    }
    public function guardar_componente_gg(Request $request)
    {
        $data = DB::table('proyectos.proy_gg_compo')
            ->insertGetId([
                'id_gg' => $request->id_pres,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'cod_padre' => $request->cod_compo,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_gg_compo'
            );
        return response()->json($data);
    }
    public function update_componente_cd(Request $request){
        
        $data = DB::table('proyectos.proy_cd_compo')
            ->where('id_cd_compo', $request->id_cd_compo)
            ->update(['descripcion' => $request->descripcion]);

        return response()->json($data);
    }
    public function update_componente_ci(Request $request){
        
        $data = DB::table('proyectos.proy_ci_compo')
            ->where('id_ci_compo', $request->id_ci_compo)
            ->update(['descripcion' => $request->descripcion]);

        return response()->json($data);
    }
    public function update_componente_gg(Request $request){
        
        $data = DB::table('proyectos.proy_gg_compo')
            ->where('id_gg_compo', $request->id_gg_compo)
            ->update(['descripcion' => $request->descripcion]);

        return response()->json($data);
    }
    public function anular_compo_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_compo')
            ->where('proy_cd_compo.id_cd_compo', $request->id_cd_compo)
            ->update(['estado' => 2]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_cd_compo')
                ->where('proy_cd_compo.id_cd_compo', $hijos_com[$i])
                ->update(['estado' => 2]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_cd_partida')
                ->where('proy_cd_partida.id_partida', $hijos_par[$i])
                ->update(['estado' => 2]);
            }
        }

        $this->suma_partidas_cd($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_compo_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_compo')
            ->where('proy_ci_compo.id_ci_compo', $request->id_ci_compo)
            ->update(['estado' => 2]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_ci_compo')
                ->where('proy_ci_compo.id_ci_compo', $hijos_com[$i])
                ->update(['estado' => 2]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_ci_detalle')
                ->where('proy_ci_detalle.id_ci_detalle', $hijos_par[$i])
                ->update(['estado' => 2]);
            }
        }

        $this->suma_partidas_ci($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_compo_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_compo')
            ->where('proy_gg_compo.id_gg_compo', $request->id_gg_compo)
            ->update(['estado' => 2]);

        $hijos_com = explode(',',$request->hijos_com);
        $count1 = count($hijos_com);

        if (!empty($request->hijos_com)){
            for ($i=0; $i<$count1; $i++){
                DB::table('proyectos.proy_gg_compo')
                ->where('proy_gg_compo.id_gg_compo', $hijos_com[$i])
                ->update(['estado' => 2]);
            }
        }

        $hijos_par = explode(',',$request->hijos_par);
        $count2 = count($hijos_par);

        if (!empty($request->hijos_par)){
            for ($i=0; $i<$count2; $i++){
                DB::table('proyectos.proy_gg_detalle')
                ->where('proy_gg_detalle.id_gg_detalle', $hijos_par[$i])
                ->update(['estado' => 2]);
            }
        }

        $this->suma_partidas_gg($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }

    public function guardar_partida_cd(Request $request)
    {
        $data = DB::table('proyectos.proy_cd_partida')
            ->insertGetId([
                'id_cd' => $request->id_cd,
                'id_cu' => $request->id_cu,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'id_sistema' => $request->sis,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_partida'
            );
        $this->suma_partidas_cd($request->comp, $request->id_cd);

        return response()->json($data);
    }
    public function guardar_partida_ci(Request $request)
    {
        $data = DB::table('proyectos.proy_ci_detalle')
            ->insertGetId([
                'id_ci' => $request->id_ci,
                'id_cu' => $request->id_cu,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_ci_detalle'
            );
        $this->suma_partidas_ci($request->comp, $request->id_ci);

        return response()->json($data);
    }
    public function guardar_partida_gg(Request $request)
    {
        $data = DB::table('proyectos.proy_gg_detalle')
            ->insertGetId([
                'id_gg' => $request->id_gg,
                'id_cu' => $request->id_cu,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'estado' => 1
            ],
                'id_gg_detalle'
            );
        $this->suma_partidas_gg($request->comp, $request->id_gg);

        return response()->json($data);
    }
    public function update_partida_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_partida')
            ->where('id_partida', $request->id_partida)
            ->update([
                'id_cu' => $request->id_cu,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'id_sistema' => $request->sis
                ]);

        $this->suma_partidas_cd($request->comp, $request->id_cd);

        return response()->json($data);
    }
    public function update_partida_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_detalle')
            ->where('id_ci_detalle', $request->id_ci_detalle)
            ->update([
                'id_cu' => $request->id_cu,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp
                ]);

        $this->suma_partidas_ci($request->comp, $request->id_ci);

        return response()->json($data);
    }
    public function update_partida_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_detalle')
            ->where('id_gg_detalle', $request->id_gg_detalle)
            ->update([
                'id_cu' => $request->id_cu,
                'descripcion' => $request->descripcion,
                'unid_medida' => $request->unid_medida,
                'cantidad' => $request->cantidad,
                'importe_unitario' => $request->unitario,
                'importe_parcial' => $request->total,
                'participacion' => $request->participacion,
                'tiempo' => $request->tiempo,
                'veces' => $request->veces,
                'cod_compo' => $request->comp
                ]);

        $this->suma_partidas_gg($request->comp, $request->id_gg);

        return response()->json($data);
    }
    public function anular_partida_cd(Request $request){

        $data = DB::table('proyectos.proy_cd_partida')
            ->where('proy_cd_partida.id_partida', $request->id_partida)
            ->update(['estado' => 2]);

        $this->suma_partidas_cd($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_partida_ci(Request $request){

        $data = DB::table('proyectos.proy_ci_detalle')
            ->where('proy_ci_detalle.id_ci_detalle', $request->id_ci_detalle)
            ->update(['estado' => 2]);

        $this->suma_partidas_ci($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function anular_partida_gg(Request $request){

        $data = DB::table('proyectos.proy_gg_detalle')
            ->where('proy_gg_detalle.id_gg_detalle', $request->id_gg_detalle)
            ->update(['estado' => 2]);

        $this->suma_partidas_gg($request->cod_compo, $request->id_pres);

        return response()->json($data);
    }
    public function suma_partidas_cd($padre, $id_cd)
    {
        $part = DB::table('proyectos.proy_cd_partida')
            ->select(DB::raw('SUM(proy_cd_partida.importe_parcial) as suma_partidas'))
            ->where([['proy_cd_partida.cod_compo', '=', $padre],
                    ['proy_cd_partida.id_cd', '=', $id_cd],
                    ['proy_cd_partida.estado', '=', 1]])
            ->first();
        
        $update = DB::table('proyectos.proy_cd_compo')
            ->where([['proy_cd_compo.codigo','=',$padre],
                    ['proy_cd_compo.id_cd', '=', $id_cd]])
            ->update(['total_comp'=>$part->suma_partidas]);

        $this->actualiza_totales($id_cd);

        return response()->json($update);
    }
    public function suma_partidas_ci($padre, $id_ci)
    {
        $part = DB::table('proyectos.proy_ci_detalle')
            ->select(DB::raw('SUM(proy_ci_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_ci_detalle.cod_compo', '=', $padre],
                    ['proy_ci_detalle.id_ci', '=', $id_ci],
                    ['proy_ci_detalle.estado', '=', 1]])
            ->first();
        
        $update = DB::table('proyectos.proy_ci_compo')
            ->where([['proy_ci_compo.codigo','=',$padre],
                    ['proy_ci_compo.id_ci', '=', $id_ci]])
            ->update(['total_comp'=>$part->suma_partidas]);
        
        $this->actualiza_totales($id_ci);

        return response()->json($update);
    }
    public function suma_partidas_gg($padre, $id_gg)
    {
        $part = DB::table('proyectos.proy_gg_detalle')
            ->select(DB::raw('SUM(proy_gg_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_gg_detalle.cod_compo', '=', $padre],
                    ['proy_gg_detalle.id_gg', '=', $id_gg],
                    ['proy_gg_detalle.estado', '=', 1]])
            ->first();
        
        $update = DB::table('proyectos.proy_gg_compo')
            ->where([['proy_gg_compo.codigo','=',$padre],
                    ['proy_gg_compo.id_gg', '=', $id_gg]])
            ->update(['total_comp'=>$part->suma_partidas]);

        $this->actualiza_totales($id_gg);

        return response()->json($update);
    }
    public function actualiza_totales($id_pres)
    {
        $part_cd_todo = DB::table('proyectos.proy_cd_partida')
            ->select(DB::raw('SUM(proy_cd_partida.importe_parcial) as suma_partidas'))
            ->where([['proy_cd_partida.id_cd', '=', $id_pres],
                    ['proy_cd_partida.estado', '=', 1]])
            ->first();

        $part_ci_todo = DB::table('proyectos.proy_ci_detalle')
            ->select(DB::raw('SUM(proy_ci_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_ci_detalle.id_ci', '=', $id_pres],
                    ['proy_ci_detalle.estado', '=', 1]])
            ->first();

        $part_gg_todo = DB::table('proyectos.proy_gg_detalle')
            ->select(DB::raw('SUM(proy_gg_detalle.importe_parcial) as suma_partidas'))
            ->where([['proy_gg_detalle.id_gg', '=', $id_pres],
                    ['proy_gg_detalle.estado', '=', 1]])
            ->first();

        $imp = DB::table('proyectos.proy_presup_importe')
            ->where([['id_presupuesto','=',$id_pres]])
            ->first();
        
        $total_cd = $part_cd_todo->suma_partidas;
        $total_ci = $part_ci_todo->suma_partidas;
        $total_gg = $part_gg_todo->suma_partidas;
        
        if ($total_ci == 0){
            $total_ci = $total_cd * $imp->porcentaje_ci;
        }
        if ($total_gg == 0){
            $total_gg = $total_cd * $imp->porcentaje_gg;
        }
        $subtotal = $total_cd + $total_ci + $total_gg;
        $total_uti = (($subtotal / (1 - ($imp->porcentaje_utilidad / 100))) - $subtotal);
        $total_igv = ($imp->porcentaje_igv / 100) * ($subtotal + $total_uti);
        $total_pres = $subtotal + $total_uti + $total_igv;

        $pres = DB::table('proyectos.proy_presup_importe')
            ->where([['id_presupuesto','=',$id_pres]])
            ->update([
                'total_costo_directo' => $total_cd, 
                'total_ci' => $total_ci,
                'total_gg' => $total_gg,
                'sub_total' => $subtotal, 
                'total_utilidad' => $total_uti, 
                'total_igv' => $total_igv, 
                'total_presupuestado' => $total_pres,
            ]);

        return response()->json($pres);
    }
    public function guardar_presint(Request $request){
        $cod = $this->nextPresupuesto(
            $request->id_tp_presupuesto,
            $request->id_empresa,
            $request->fecha_emision
        );
        $fecha = date('Y-m-d H:i:s');
        $id_presupuesto = DB::table('proyectos.proy_presup')->insertGetId(
            [
                'id_contrato' => $request->id_contrato,
                'fecha_emision' => $request->fecha_emision,
                'moneda' => $request->moneda,
                'id_tp_presupuesto' => $request->id_tp_presupuesto,
                'elaborado_por' => $request->elaborado_por,
                'id_unid_ejec' => $request->id_unid_ejec,
                'id_residente' => $request->id_residente,
                'id_op_com' => $request->id_op_com,
                'observacion' => $request->observacion,
                'estado' => 1,
                'fecha_registro' => $fecha,
                'codigo' => $cod,
                'id_empresa' => $request->id_empresa
            ],
                'id_presupuesto'
        );

        DB::table('proyectos.proy_presup_importe')->insert(
            [
                'id_presupuesto' => $id_presupuesto,
                'total_costo_directo' => 0,
                'total_ci' => 0,
                'porcentaje_ci' => 0,
                'total_gg' => 0,
                'porcentaje_gg' => 0,
                'sub_total' => 0,
                'porcentaje_utilidad' => 0,
                'total_utilidad' => 0,
                'porcentaje_igv' => 0,//jalar igv actual
                'total_igv' => 0,
                'total_presupuestado' => 0
            ]
        );

        $id_cd = DB::table('proyectos.proy_cd')->insertGetId(
            [
                'id_presupuesto' => $id_presupuesto,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_cd'
        );
        $id_ci = DB::table('proyectos.proy_ci')->insertGetId(
            [
                'id_presupuesto' => $id_presupuesto,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_ci'
        );
        $id_gg = DB::table('proyectos.proy_gg')->insertGetId(
            [
                'id_presupuesto' => $id_presupuesto,
                'estado' => 1,
                'fecha_registro' => $fecha
            ],
                'id_gg'
        );

        return response()->json($id_presupuesto);
    }
    public function update_presint(Request $request){
        $data = DB::table('proyectos.proy_presup')
            ->where('id_presupuesto',$request->id_presupuesto)
            ->update([
                'fecha_emision' => $request->fecha_emision,
                'moneda' => $request->moneda,
                'id_unid_ejec' => $request->id_unid_ejec,
                'id_residente' => $request->id_residente,
                'id_op_com' => $request->id_op_com,
                'observacion' => $request->observacion
            ]);
        $imp = DB::table('proyectos.proy_presup_importe')
            ->where('id_presupuesto',$request->id_presupuesto)
            ->update([
                    'total_costo_directo' => $request->total_costo_directo,
                    'total_ci' => $request->total_ci,
                    'porcentaje_ci' => $request->porcentaje_ci,
                    'total_gg' => $request->total_gg,
                    'porcentaje_gg' => $request->porcentaje_gg,
                    'sub_total' => $request->sub_total,
                    'porcentaje_utilidad' => $request->porcentaje_utilidad,
                    'total_utilidad' => $request->total_utilidad,
                    'porcentaje_igv' => $request->porcentaje_igv,
                    'total_igv' => $request->total_igv,
                    'total_presupuestado' => $request->total_presupuestado,
                ]
            );
        return response()->json(['importe'=>$imp,'id_pres'=>$request->id_presupuesto]);
    }
    public function generar_propuesta($id_presint){

        $presint = DB::table('proyectos.proy_presup')
            ->select('proy_presup.*','proy_presup_importe.*')
            ->join('proyectos.proy_presup_importe','proy_presup_importe.id_presupuesto','=','proy_presup.id_presupuesto')
            ->where('proy_presup.id_presupuesto',$id_presint)
            ->first();
        $fecha_emision = date('Y-m-d');
        $fecha_hora = date('Y-m-d H:i:s');

        $cod = $this->nextPresupuesto(
            2,//Propuesta Cliente
            $presint->id_empresa,
            $fecha_emision
        );
        $id_presupuesto = DB::table('proyectos.proy_presup')->insertGetId(
            [
                'id_contrato' => $presint->id_contrato,
                'fecha_emision' => $fecha_emision,
                'moneda' => $presint->moneda,
                'id_tp_presupuesto' => 2,//Propuesta Cliente
                'elaborado_por' => $presint->elaborado_por,
                'id_unid_ejec' => $presint->id_unid_ejec,
                'id_residente' => $presint->id_residente,
                'id_op_com' => $presint->id_op_com,
                'observacion' => $presint->observacion,
                'estado' => 1,
                'fecha_registro' => $fecha_hora,
                'codigo' => $cod,
                'id_empresa' => $presint->id_empresa
            ],
                'id_presupuesto'
        );

        DB::table('proyectos.proy_presup_importe')->insert(
            [
                'id_presupuesto' => $id_presupuesto,
                'total_costo_directo' => $presint->total_costo_directo,
                'total_ci'  => $presint->total_ci,
                'porcentaje_ci' => $presint->porcentaje_ci,
                'total_gg' => $presint->total_gg,
                'porcentaje_gg' => $presint->porcentaje_gg,
                'sub_total' => $presint->sub_total,
                'porcentaje_utilidad' => $presint->porcentaje_utilidad,
                'total_utilidad' => $presint->total_utilidad,
                'porcentaje_igv' => $presint->porcentaje_igv,
                'total_igv' => $presint->total_igv,
                'total_presupuestado' => $presint->total_presupuestado
            ]
        );

        $presint_cd_com = DB::table('proyectos.proy_cd_compo')
            ->where([['id_cd','=',$id_presint],
                     ['estado','=',1]])
            ->get();
        $presint_ci_com = DB::table('proyectos.proy_ci_compo')
            ->where([['id_ci','=',$id_presint],
                     ['estado','=',1]])
            ->get();
        $presint_gg_com = DB::table('proyectos.proy_gg_compo')
            ->where([['id_gg','=',$id_presint],
                     ['estado','=',1]])
            ->get();

        foreach($presint_cd_com as $com)
        {
            DB::table('proyectos.proy_cd_compo')->insertGetId([
                    'id_cd' => $id_presupuesto,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_cd_compo'
                );
        }
        foreach($presint_ci_com as $com)
        {
            DB::table('proyectos.proy_ci_compo')->insertGetId([
                    'id_ci' => $id_presupuesto,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_ci_compo'
                );
        }
        foreach($presint_gg_com as $com)
        {
            DB::table('proyectos.proy_gg_compo')->insertGetId([
                    'id_gg' => $id_presupuesto,
                    'codigo' => $com->codigo,
                    'descripcion' => $com->descripcion,
                    'cod_padre' => $com->cod_padre,
                    'total_comp' => $com->total_comp,
                    'porcen_utilidad' => $com->porcen_utilidad,
                    'importe_utilidad' => $com->importe_utilidad,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_gg_compo'
                );
        }
        $presint_cd_par = DB::table('proyectos.proy_cd_partida')
            ->where('id_cd',$id_presint)
            ->get();
        $presint_ci_par = DB::table('proyectos.proy_ci_detalle')
            ->where('id_ci',$id_presint)
            ->get();
        $presint_gg_par = DB::table('proyectos.proy_gg_detalle')
            ->where('id_gg',$id_presint)
            ->get();

        foreach($presint_cd_par as $par){
            DB::table('proyectos.proy_cd_partida')->insertGetId([
                    'id_cd' => $id_presupuesto,
                    'id_cu' => $par->id_cu,
                    'codigo' => $par->codigo,
                    'descripcion' => $par->descripcion,
                    'unid_medida' => $par->unid_medida,
                    'cantidad' => $par->cantidad,
                    'importe_unitario' => $par->importe_unitario,
                    'importe_parcial' => $par->importe_parcial,
                    'id_sistema' => $par->id_sistema,
                    'cod_compo' => $par->cod_compo,
                    'fecha_registro' => $fecha_hora,
                    'estado' => 1
                ],
                    'id_partida'
                );
        }
        foreach($presint_ci_par as $par){
            DB::table('proyectos.proy_ci_detalle')->insertGetId([
                'id_ci' => $id_presupuesto,
                'id_cu' => $par->id_cu,
                'codigo' => $par->codigo,
                'descripcion' => $par->descripcion,
                'unid_medida' => $par->unid_medida,
                'cantidad' => $par->cantidad,
                'importe_unitario' => $par->importe_unitario,
                'importe_parcial' => $par->importe_parcial,
                'participacion' => $par->participacion,
                'tiempo' => $par->tiempo,
                'veces' => $par->veces,
                'cod_compo' => $par->cod_compo,
                'fecha_registro' => $fecha_hora,
                'estado' => 1
            ],
                'id_ci_detalle'
            );
        }
        foreach($presint_gg_par as $par){
            DB::table('proyectos.proy_gg_detalle')->insertGetId([
                'id_gg' => $id_presupuesto,
                'id_cu' => $par->id_cu,
                'codigo' => $par->codigo,
                'descripcion' => $par->descripcion,
                'unid_medida' => $par->unid_medida,
                'cantidad' => $par->cantidad,
                'importe_unitario' => $par->importe_unitario,
                'importe_parcial' => $par->importe_parcial,
                'participacion' => $par->participacion,
                'tiempo' => $par->tiempo,
                'veces' => $par->veces,
                'cod_compo' => $par->cod_compo,
                'fecha_registro' => $fecha_hora,
                'estado' => 1
            ],
                'id_gg_detalle'
            );
        }
        return response()->json($id_presupuesto);
    }
    public function guardar_adjunto(Request $request)
    {
        $update = false;
        $namefile = "";
        if ($request->id_contrato !== "" && $request->id_contrato !== null){
            $nfile = $request->file('adjunto');
            if (isset($nfile)){
                $namefile = $request->id_contrato.'.'.$nfile->getClientOriginalExtension();
                \File::delete(public_path('proyectos/contratos/'.$namefile));
                Storage::disk('archivos')->put('proyectos/contratos/'.$namefile, \File::get($nfile));
            } else {
                $namefile = null;
            }
            $update = DB::table('proyectos.proy_contrato')
            ->where('id_contrato', $request->id_contrato)
            ->update(['archivo_adjunto' => $namefile]);    
        }

        if ($update){
            $status = 1;
        } else {
            $status = 0;
        }
        $array = array("status"=>$status, "adjunto"=>$namefile);
        return response()->json($array);
    }
    public function listar_obs_cd($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_cd_partida','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        foreach($obs as $o){
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }
    public function listar_obs_ci($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_ci_detalle','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        foreach($obs as $o){
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }
    public function listar_obs_gg($id_partida){
        $obs = DB::table('proyectos.proy_obs')
            ->select('proy_obs.*','sis_usua.usuario as nombre_usuario')
            ->join('configuracion.sis_usua','sis_usua.id_usuario','=','proy_obs.usuario')
            ->where([['proy_obs.id_gg_detalle','=', $id_partida],
                     ['proy_obs.estado','=',1]])
            ->orderBy('proy_obs.fecha_registro')
            ->get();
        $html = '';
        $i = 1;
        foreach($obs as $o){
            $html .= '
            <tr id="obs-'.$o->id_obs.'">
                <td>'.$i.'</td>
                <td>'.$o->descripcion.'</td>
                <td>'.$o->nombre_usuario.'</td>
                <td>'.$o->fecha_registro.'</td>
                <td><a href="abrir_adjunto_partida/'.$o->archivo_adjunto.'">'.$o->archivo_adjunto.'</a></td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                    title="Anular" onClick="anular_obs('.$o->id_obs.');"></i>
                </td>
            </tr>';
            $i++;
        }
        return json_encode($html);
    }
    public function guardar_obs_partida(Request $request){
        $id_obs = DB::table('proyectos.proy_obs')->insertGetId(
                [
                'id_cd_partida'=>$request->id_cd_partida,
                'id_ci_detalle'=>$request->id_ci_detalle,
                'id_gg_detalle'=>$request->id_gg_detalle,
                'descripcion'=>$request->observacion,
                'usuario'=>$request->id_usuario,
                'estado'=>1,
                'fecha_registro'=>date('Y-m-d H:i:s'),
                ],
                'id_obs'
            );
        //obtenemos el campo file definido en el formulario
        $file = $request->file('adjunto');
        if (isset($file)){
            //obtenemos el nombre del archivo
            $nombre = $id_obs.'.'.$file->getClientOriginalName();
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \File::delete(public_path('proyectos/presupuestos/partidas_adjunto/'.$nombre));
            \Storage::disk('archivos')->put('proyectos/presupuestos/partidas_adjunto/'.$nombre,\File::get($file));
            
            $update = DB::table('proyectos.proy_obs')
                ->where('id_obs', $id_obs)
                ->update(['archivo_adjunto' => $nombre]); 
        } else {
            $nombre = null;
        }
        return response()->json($id_obs);
    }
    public function abrir_adjunto_partida($file_name){
        $file_path = public_path('files/proyectos/presupuestos/partidas_adjunto/'.$file_name);
        // $result = File::exists('files/proyectos/contratos/'.$file_name);
        if (file_exists($file_path)){
            return response()->download($file_path);
        } else {
            return response()->json("No existe dicho archivo!");
        }
    }
    public function anular_obs_partida($id_obs){
        $data = DB::table('proyectos.proy_obs')->where('id_obs',$id_obs)
            ->update([ 'estado'=> 2 ]);
        return response()->json($data);
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

}
