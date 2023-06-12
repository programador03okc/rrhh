<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ContabilidadController extends Controller
{
    public function __construct(){
        // session_start();
    }
    function view_cta_contable(){
        return view('contabilidad/cta_contable');
    }
    function mostrar_cuentas_contables(){
        $padres = DB::table('contabilidad.cont_cta_cble')->where('cod_padre', null)->get();
        $array = [];
        
        if (sizeof($padres) > 0){
            foreach ($padres as $row){
                $data = array();
                $data['text'] = $row->codigo." - ".$row->descripcion;
                $data['nodes'] = $this->traer_hijos($row->codigo);
                array_push($array, $data);
            }
        }

        return json_encode(array_values($array));
    }
    function traer_hijos($padre){
        $hijos = DB::table('contabilidad.cont_cta_cble')
        ->where('cod_padre', $padre)
        ->get()->toArray();
        $array = [];

        if (sizeof($hijos) > 0){
            foreach ($hijos as $row){
                $sub_array = array();
                $sub_array['text'] = $row->codigo." - ".$row->descripcion;
                // $sub_array['nodes'] = $this->traer_hijos($row->codigo);
                array_push($array,$sub_array);
            }
        }
        return $array;
    }








    public function mostrar_tipo_cuentas()
    {
      
         $adm_tp_cta = DB::table('contabilidad.adm_tp_cta')
          ->select(
             'adm_tp_cta.id_tipo_cuenta',
             'adm_tp_cta.descripcion',
             'adm_tp_cta.estado',
             DB::raw("(CASE WHEN adm_tp_cta.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS descripcion_estado")

             )
             ->where([
        
                ['adm_tp_cta.estado', '=', 1]
                ])
             ->orderBy('adm_tp_cta.id_tipo_cuenta', 'asc')
         ->get();
         return response()->json($adm_tp_cta);

    }
    public function mostrar_tipo_cuenta($id)
    {
        $adm_tp_cta = DB::table('contabilidad.adm_tp_cta')
        ->select(
            'adm_tp_cta.id_tipo_cuenta',
            'adm_tp_cta.descripcion',
            'adm_tp_cta.estado'
            )
            ->where([
                ['adm_tp_cta.id_tipo_cuenta', '=', $id]
               ])
            ->orderBy('adm_tp_cta.id_tipo_cuenta', 'asc')
        ->get();
        return response()->json(["adm_tp_cta"=>$adm_tp_cta]);
    }
 
    public function guardar_tipo_cuenta(Request $request){
        $data = DB::table('contabilidad.adm_tp_cta')->insertGetId(
            [
            'descripcion'=> $request->descripcion,
            'estado'     => $request->estado
            ],
            'id_tipo_cuenta'
        );
        return response()->json($data);

    }

    public function actualizar_tipo_cuenta(Request $request, $id){
        $data = DB::table('contabilidad.adm_tp_cta')->where('id_tipo_cuenta', $id)
        ->update([
            'descripcion'=> $request->descripcion,
            'estado'     => $request->estado
        ]);
        return response()->json($data);
    }

    public function eliminar_tipo_cuenta($id){
        // $data = DB::table('adm_tp_cta')->where('id_tipo_cuenta', '=', $id)->delete();
        // return response()->json($data);
        $data = DB::table('contabilidad.adm_tp_cta')->where('id_tipo_cuenta', $id)
        ->update([
            'estado'     => 0
        ]);
        return response()->json($data);

    }
    // public function mostrar_tipo_contribuyentes()
    // {
    //     $data = tipo_contribuyente::all();
    //      return response()->json($data);

    // }
    // public function mostrar_tipo_contribuyente($id)
    // {
    //     try {
    //     $data = tipo_contribuyente::where('id_tipo_contribuyente', $id)->first();
    //     return response()->json($data);
    // } catch(QueryException $e) {
    //     // return Response::json(['error' => 'Error msg'], 404); // Status code here
    //     return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    // }

    // }
    // public function guardar_tipo_contribuyente(Request $request){
    //     $data = tipo_contribuyente::create($request->all());
    //       return response()->json($data);

    // }
    // public function eliminar_tipo_contribuyente($id){
    //     $data = tipo_contribuyente::where('id_tipo_contribuyente', $id)->delete();
    //     return response()->json($data);

    // }
    // public function actualizar_tipo_contribuyente(Request $request, $id){
    //     $item = tipo_contribuyente::where('id_tipo_contribuyente', $id)->first();
    //     // $item->id_tipo_contribuyente = $request->id_tipo_contribuyente;
    //     $item->descripcion = $request->descripcion;
    //     $item->estado = $request->estado;
    //     $item->save();
    //     return response()->json($item);
    // }
    //
    public function fill_input_contribuyentes(){

         $adm_tp_contri = DB::table('contabilidad.adm_tp_contri')
                    ->select(
                        'adm_tp_contri.id_tipo_contribuyente',
                        'adm_tp_contri.descripcion AS adm_tp_contri_descripcion'
                    )
                    ->where([
                        ['adm_tp_contri.estado', '=', 1]
                        ])
                    ->get();
         $sis_identi = DB::table('contabilidad.sis_identi')

                    ->select(
                        'sis_identi.id_doc_identidad',
                        'sis_identi.descripcion AS sis_identi_descripcion'
                    )
                    ->where([
                        ['sis_identi.estado', '=', 1]
                        ])
                    ->get();
         $sis_pais = DB::table('configuracion.sis_pais')
                    ->select(
                        'sis_pais.id_pais',
                        'sis_pais.descripcion as sis_pais_descripcion'
                    )
                    ->where([
                        ['sis_pais.estado', '=', 1]
                        ])  
                    ->get();
         $adm_rubro = DB::table('contabilidad.adm_rubro')
                    ->select(
                        'adm_rubro.id_rubro',
                        'adm_rubro.descripcion AS descripcion_rubro'
                     )
                    ->get();
        $data = ["adm_tp_contri"=>$adm_tp_contri,"sis_identi"=>$sis_identi,"sis_pais"=>$sis_pais,"adm_rubro"=>$adm_rubro];   
 
        return response()->json($data);

    }
//     public function fill_input_rubro(){

//         $adm_contri = DB::table('adm_contri')
//                    ->select(
//                        'adm_contri.id_contribuyente',
//                        'adm_contri.razon_social'
//                    )
//                    ->where([
//                        ['adm_contri.estado', '=', 1]
//                        ])
//                    ->get();
//         $adm_rubro = DB::table('adm_rubro')
//                 ->select(
//                     'adm_rubro.id_rubro',
//                     'adm_rubro.descripcion'
//                 )
//                 ->where([
//                     ['adm_rubro.estado', '=', 1]
//                     ])
//                 ->get();
 
//        $data = ["adm_contri"=>$adm_contri,"adm_rubro"=>$adm_rubro];   

//        return response()->json($data);

//    }
    public function fill_input_empresas(){

        $adm_contri = DB::table('contabilidad.adm_contri')
                   ->select(
                       'adm_contri.id_contribuyente',
                       'adm_contri.razon_social'
                   )
                   ->where([
                       ['adm_contri.estado', '=', 1]
                       ])
                   ->get();
 
 
       $data = ["adm_contri"=>$adm_contri];   

       return response()->json($data);

   }
   public function fill_input_cuenta(){
    $adm_contri = DB::table('contabilidad.adm_contri')
                ->select(
                    'adm_contri.id_contribuyente',
                    'adm_contri.razon_social'
                )
                ->where([
                    ['adm_contri.estado', '=', 1]
                    ])
                ->get();
    $adm_tp_cta = DB::table('contabilidad.adm_tp_cta')
               ->select(
                   'adm_tp_cta.id_tipo_cuenta',
                   'adm_tp_cta.descripcion'
               )
               ->get();
            $data = ["adm_tp_cta"=>$adm_tp_cta];   

    $cont_banco = DB::table('contabilidad.cont_banco')
                    ->leftJoin(DB::raw("(SELECT 
                    adm_contri.id_contribuyente,
                    adm_contri.razon_social
                    FROM adm_contri ) as banco"),function($join){
                    $join->on("banco.id_contribuyente","=","cont_banco.id_contribuyente");
                    })
               ->select(
                   'cont_banco.id_banco',
                   'cont_banco.id_contribuyente',
                   'banco.razon_social AS banco_razon_social',
                   'cont_banco.codigo',
                   'cont_banco.estado'
               )
               ->get();
            $data = ["adm_contri"=>$adm_contri,"adm_tp_cta"=>$adm_tp_cta,"cont_banco"=>$cont_banco];   

         return response()->json($data);

}
    public function mostrar_contribuyentes()
        {
         $result = DB::table('contabilidad.adm_contri')
                    ->join('contabilidad.adm_tp_contri', 'adm_contri.id_tipo_contribuyente', '=', 'adm_tp_contri.id_tipo_contribuyente')
                    ->leftJoin('contabilidad.adm_ctb_rubro', 'adm_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
                    ->leftJoin('contabilidad.adm_ctb_contac', 'adm_contri.id_contribuyente', '=', 'adm_ctb_contac.id_contribuyente')
                    ->leftJoin('contabilidad.sis_identi', 'adm_contri.id_doc_identidad', '=', 'sis_identi.id_doc_identidad')
                    ->leftJoin(DB::raw("(SELECT 
                    adm_rubro.id_rubro,
                    adm_rubro.descripcion
                    FROM contabilidad.adm_rubro ) as rubro"),function($join){
                    $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
                    })
                     ->select(
                     'adm_contri.id_contribuyente', 
                     'adm_contri.id_tipo_contribuyente', 
                     'sis_identi.descripcion as sis_identi_descripcion', 
                     'adm_contri.razon_social', 
                     'adm_contri.nro_documento', 
                     'adm_contri.telefono', 
                     'adm_contri.celular', 
                     'adm_contri.direccion_fiscal', 
                     'adm_contri.ubigeo', 
                     'adm_contri.id_pais', 
                     'adm_contri.estado', 
                     'adm_contri.fecha_registro', 
                     'rubro.id_rubro AS id_rubro',
                     'rubro.descripcion AS rubro_descripcion',
                     'adm_ctb_contac.id_contribuyente as adm_ctb_contacto_id_contribuyente',
                     'adm_ctb_contac.nombre', 
                     'adm_ctb_contac.cargo',
                     'adm_ctb_contac.telefono AS adm_ctb_contac_telefono', 
                     'adm_ctb_contac.email', 
                     'adm_ctb_contac.estado AS estado_adm_ctb_contac',
                     'adm_ctb_contac.fecha_registro AS adm_ctb_contac_fecha_registro'
                     )
                     ->where('adm_contri.estado', '=', 1)
                    ->orderBy('adm_contri.id_contribuyente', 'asc')
                     ->get();


                 foreach($result as $data){
                    $contacto[]=[
                        'id_contribuyente'=> $data->adm_ctb_contacto_id_contribuyente,
                        'nombre'=> $data->nombre,
                        'cargo'=> $data->cargo,
                        'telefono'=> $data->adm_ctb_contac_telefono,
                        'email'=> $data->email,
                        'estado'=> $data->estado_adm_ctb_contac,
                        'fecha_registro'=> $data->adm_ctb_contac_fecha_registro              
                    ];      
            };
                
            
            $lastId = "";
                foreach($result as $data){
                    if ($data->id_contribuyente !== $lastId) {
                        $contribuyente[] = [
                            'id_contribuyente'=> $data->id_contribuyente,
                            'id_tipo_contribuyente'=> $data->id_tipo_contribuyente,
                            'razon_social'=> $data->razon_social,
                            'doc_identi'=>$data->sis_identi_descripcion,
                            'nro_documento'=>$data->nro_documento,
                            'id_rubro'=>$data->id_rubro,
                            'rubro_descripcion'=>$data->rubro_descripcion,
                            'telefono'=> $data->telefono,
                            'celular'=> $data->celular,
                            'direccion_fiscal'=> $data->direccion_fiscal,
                            'ubigeo'=> $data->ubigeo,
                            'id_pais'=> $data->id_pais,
                            'estado'=> $data->estado,
                            'fecha_registro'=> $data->fecha_registro,
                        ];  
                        $lastId = $data->id_contribuyente;
                      } 
            };
            
             for($j=0; $j< sizeof($contacto);$j++){
                for($i=0; $i< sizeof($contribuyente);$i++){
                    if($contacto[$j]['id_contribuyente'] === $contribuyente[$i]['id_contribuyente']){
                        $contribuyente[$i]['contacto'][]=$contacto[$j];
                    }

                }

            }
 
        return response()->json($contribuyente);
 
 }

public function mostrar_contribuyente($id)
    {
        try {
            $result = DB::table('contabilidad.adm_contri')
             ->select(
             'adm_contri.id_contribuyente', 
             'adm_contri.razon_social', 
             'adm_contri.estado', 
             'adm_contri.fecha_registro'
             )
            
             ->where([
                 ['adm_contri.id_contribuyente', '=', $id],
                ['adm_contri.estado', '=', 1]
                ])
             ->get();
    return response()->json($result);
    } catch(QueryException $e) {
        // return Response::json(['error' => 'Error msg'], 404); // Status code here
        return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    }
}
public function mostrar_contribuyente_data_contribuyente($id){
        try {
            $result = DB::table('contabilidad.adm_contri')
            ->leftJoin('adm_tp_contri', 'adm_contri.id_tipo_contribuyente', '=', 'adm_tp_contri.id_tipo_contribuyente')
            ->leftJoin('sis_pais', 'adm_contri.id_pais', '=', 'sis_pais.id_pais')
            ->leftJoin('adm_ctb_rubro', 'adm_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
            ->leftJoin('sis_identi', 'adm_contri.id_doc_identidad', '=', 'sis_identi.id_doc_identidad')
            ->leftJoin(DB::raw("(SELECT 
            adm_rubro.id_rubro,
            adm_rubro.descripcion
            FROM adm_rubro ) as rubro"),function($join){
            $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
            })
             ->select(
             'adm_contri.id_contribuyente', 
             'adm_contri.id_tipo_contribuyente', 
             'adm_tp_contri.descripcion AS adm_tip_contri_descripcion',
             'adm_contri.razon_social', 
             'rubro.id_rubro',
             'rubro.descripcion AS descripcion_rubro',
             'adm_contri.nro_documento', 
             'adm_contri.telefono', 
             'adm_contri.celular', 
             'adm_contri.direccion_fiscal', 
             'adm_contri.ubigeo', 
             'adm_contri.id_pais', 
             'sis_pais.descripcion AS sis_pais_descripcion', 
             'adm_contri.estado', 
             'adm_contri.fecha_registro', 
             'sis_identi.id_doc_identidad', 
             'sis_identi.descripcion as sis_identi_descripcion'

 
             )
             ->where([
                ['adm_contri.id_contribuyente', '=', $id],
               ['adm_contri.estado', '=', 1]
               ])
             ->get();

 
    $lastId = "";
        foreach($result as $data){
            if ($data->id_contribuyente !== $lastId) {
                $contribuyente[] = [
                    'id_contribuyente'=> $data->id_contribuyente,
                    'id_tipo_contribuyente'=> $data->id_tipo_contribuyente,
                    'adm_tip_contri_descripcion'=>$data->adm_tip_contri_descripcion,
                    'razon_social'=> $data->razon_social,
                    'id_rubro'=> $data->id_rubro,
                    'descripcion_rubro'=> $data->descripcion_rubro,
                    
                    'id_doc_identidad'=>$data->id_doc_identidad,
                    'doc_identi'=>$data->sis_identi_descripcion,
                    'nro_documento'=>$data->nro_documento,
                    'telefono'=> $data->telefono,
                    'celular'=> $data->celular,
                    'direccion_fiscal'=> $data->direccion_fiscal,
                    'ubigeo'=> $data->ubigeo,
                    'id_pais'=> $data->id_pais,
                    'sis_pais_descripcion'=> $data->sis_pais_descripcion,
                    'fecha_registro'=> $data->fecha_registro,
                    'estado'=> $data->estado
                ];  
                $lastId = $data->id_contribuyente;
              } 
    };
    return response()->json($contribuyente);

    } catch(QueryException $e) {
        // return Response::json(['error' => 'Error msg'], 404); // Status code here
        return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    }
}
// public function mostrar_contribuyente_data_contribuyente_rubro($id){
//         try {
//             $result = DB::table('adm_contri')
//             ->leftJoin('sis_pais', 'adm_contri.id_pais', '=', 'sis_pais.id_pais')
//             ->leftJoin('adm_ctb_rubro', 'adm_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
//                  ->leftJoin(DB::raw("(SELECT 
//                 adm_rubro.id_rubro,
//                 adm_rubro.descripcion
//                 FROM adm_rubro ) as rubro"),function($join){
//                 $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
//                 })
//              ->select(
//              'adm_contri.id_contribuyente', 
//              'adm_contri.razon_social', 
//              'rubro.id_rubro AS id_rubro',
//              'rubro.descripcion',
//              'adm_contri.estado'
//              )
//              ->where([
//                 ['adm_contri.id_contribuyente', '=', $id],
//                ['adm_contri.estado', '=', 1]
//                ])
//              ->get();

 
//     $lastId = "";
//         foreach($result as $data){
//             if ($data->id_contribuyente !== $lastId) {
//                 $contribuyente[] = [
//                     'id_contribuyente'=> $data->id_contribuyente,
//                     'razon_social'=> $data->razon_social,
//                     'id_rubro'=>$data->id_rubro,
//                     'descripcion'=>$data->descripcion,
//                     'estado'=> $data->estado
//                 ];  
//                 $lastId = $data->id_contribuyente;
//               } 
//     };
//     return response()->json($contribuyente);

//     } catch(QueryException $e) {
//         // return Response::json(['error' => 'Error msg'], 404); // Status code here
//         return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
//     }
// }

// public function mostrar_contribuyente_data_contribuyente_contacto($id){
//     try {
//         $result = DB::table('adm_contri')
//         ->leftJoin('sis_pais', 'adm_contri.id_pais', '=', 'sis_pais.id_pais')
//          ->leftJoin('adm_ctb_contac', 'adm_contri.id_contribuyente', '=', 'adm_ctb_contac.id_contribuyente')
//          ->select(
//           'adm_ctb_contac.id_contribuyente',
//          'adm_contri.razon_social', 
//         'adm_ctb_contac.id_datos_contacto', 
//         'adm_ctb_contac.nombre', 
//         'adm_ctb_contac.cargo',
//         'adm_ctb_contac.telefono', 
//         'adm_ctb_contac.email', 
//         'adm_ctb_contac.estado',
//         'adm_ctb_contac.fecha_registro'
//          )
//          ->where([
//             ['adm_ctb_contac.id_contribuyente', '=', $id],
//            ['adm_ctb_contac.estado', '=', 1]
//            ])
//          ->get();


//     $lastId = "";
//     foreach($result as $data){
//         if ($data->id_contribuyente !== $lastId) {
//             $contribuyente[] = [
//                 'id_contribuyente'=> $data->id_contribuyente,
//                 'razon_social'=> $data->razon_social,
//                 'id_datos_contacto'=>$data->id_datos_contacto,
//                 'nombre'=>$data->nombre,
//                 'cargo'=>$data->cargo,
//                 'telefono'=> $data->telefono,
//                 'email'=> $data->email,
//                 'estado'=> $data->estado,
//                 'fecha_registro'=> $data->fecha_registro
//             ];  
//             $lastId = $data->id_contribuyente;
//           } 
//     };
//     return response()->json($contribuyente);

//     } catch(QueryException $e) {
//         // return Response::json(['error' => 'Error msg'], 404); // Status code here
//         return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
//     }
// }

// public function mostrar_contribuyente($id)
//     {
//         try {
//             $result = DB::table('adm_contri')
//             ->leftJoin('adm_tp_contri', 'adm_contri.id_tipo_contribuyente', '=', 'adm_tp_contri.id_tipo_contribuyente')
//             ->leftJoin('sis_pais', 'adm_contri.id_pais', '=', 'sis_pais.id_pais')
//             ->leftJoin('adm_ctb_rubro', 'adm_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
//             ->leftJoin('adm_ctb_contac', 'adm_contri.id_contribuyente', '=', 'adm_ctb_contac.id_contribuyente')
//             ->leftJoin('sis_identi', 'adm_contri.id_doc_identidad', '=', 'sis_identi.id_doc_identidad')
//             ->leftJoin('adm_cta_contri', 'adm_contri.id_contribuyente', '=', 'adm_cta_contri.id_contribuyente')
//             ->leftJoin('adm_tp_cta', 'adm_cta_contri.id_tipo_cuenta', '=', 'adm_tp_cta.id_tipo_cuenta')
//             ->leftJoin('cont_banco', 'adm_cta_contri.id_banco', '=', 'cont_banco.id_banco')
//                  ->leftJoin(DB::raw("(SELECT 
//                 adm_contri.id_contribuyente,
//                 adm_contri.razon_social
//                 FROM adm_contri ) as banco"),function($join){
//                 $join->on("banco.id_contribuyente","=","cont_banco.id_contribuyente");
//                 })
//                  ->leftJoin(DB::raw("(SELECT 
//                 adm_rubro.id_rubro,
//                 adm_rubro.descripcion
//                 FROM adm_rubro ) as rubro"),function($join){
//                 $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
//                 })
//                  ->leftJoin(DB::raw("(SELECT 
//                 adm_tp_cta.id_tipo_cuenta,
//                 adm_tp_cta.descripcion
//                 FROM adm_tp_cta ) as tp_cuenta"),function($join){
//                 $join->on("tp_cuenta.id_tipo_cuenta","=","adm_cta_contri.id_tipo_cuenta");
//                 })
//              ->select(
//              'adm_contri.id_contribuyente', 
//              'adm_contri.id_tipo_contribuyente', 
//              'adm_tp_contri.descripcion AS adm_tip_contri_descripcion',
//              'adm_contri.razon_social', 
//              'adm_contri.nro_documento', 
//              'adm_contri.telefono', 
//              'adm_contri.celular', 
//              'adm_contri.direccion_fiscal', 
//              'adm_contri.ubigeo', 
//              'adm_contri.id_pais', 
//              'sis_pais.descripcion AS sis_pais_descripcion', 
//              'adm_contri.estado', 
//              'adm_contri.fecha_registro', 
//              'sis_identi.id_doc_identidad as sis_identi_id_doc_identidad', 
//              'sis_identi.descripcion as sis_identi_descripcion', 
//             'rubro.id_rubro AS id_rubro',
//              'rubro.descripcion AS rubro_descripcion',
//              'adm_ctb_contac.id_contribuyente as adm_ctb_contacto_id_contribuyente',
//              'adm_ctb_contac.nombre', 
//              'adm_ctb_contac.cargo',
//              'adm_ctb_contac.telefono AS adm_ctb_contac_telefono', 
//              'adm_ctb_contac.email', 
//              'adm_ctb_contac.estado AS estado_adm_ctb_contac',
//              'adm_ctb_contac.fecha_registro AS adm_ctb_contac_fecha_registro',
//              'adm_cta_contri.id_cuenta_contribuyente',
//              'adm_cta_contri.id_contribuyente AS adm_cta_contri_id_contribuyente',
//              'adm_cta_contri.id_banco',
//              'banco.razon_social AS banco_razon_social',
//              'cont_banco.codigo AS cont_banco_codigo',
//              'adm_cta_contri.id_tipo_cuenta',
//              'tp_cuenta.descripcion AS tp_cuenta_descripcion',
//              'adm_tp_cta.descripcion AS adm_tp_cta_descripcion',
//              'adm_cta_contri.nro_cuenta',
//              'adm_cta_contri.nro_cuenta_interbancaria',
//              'adm_cta_contri.fecha_registro AS adm_cta_contri_fecha_registro'
//              )
//              ->where('adm_contri.id_contribuyente', '=', $id)
//              ->get();


//          foreach($result as $data){
//             $contacto[]=[
//                 'id_contribuyente'=> $data->adm_ctb_contacto_id_contribuyente,
//                 'nombre'=> $data->nombre,
//                 'cargo'=> $data->cargo,
//                 'telefono'=> $data->adm_ctb_contac_telefono,
//                 'email'=> $data->email,
//                 'fecha_registro_contacto'=> $data->adm_ctb_contac_fecha_registro,              
//                 'estado'=> $data->estado_adm_ctb_contac
//             ];      
//         };

//         $lastId = "";
//          foreach($result as $data){
//             if ($data->adm_cta_contri_id_contribuyente !== $lastId) {
//             $cuenta[]=[
//                 'id_cuenta_contribuyente'=> $data->id_cuenta_contribuyente,
//                 'id_contribuyente'=> $data->adm_cta_contri_id_contribuyente,
//                 'id_banco'=> $data->id_banco,
//                 'banco_razon_social'=> $data->banco_razon_social,
//                 'cont_banco_codigo'=> $data->cont_banco_codigo,
//                  'id_tipo_cuenta'=> $data->id_tipo_cuenta,
//                  'tp_cuenta_descripcion'=> $data->tp_cuenta_descripcion,
//                 'nro_cuenta'=> $data->nro_cuenta,
//                 'nro_cuenta_interbancaria'=> $data->nro_cuenta_interbancaria,
//                 'adm_cta_contri_fecha_registro'=> $data->adm_cta_contri_fecha_registro             
//              ]; 
//              $lastId = $data->adm_cta_contri_id_contribuyente;

//             }      
//         };
        
    
//     $lastId = "";
//         foreach($result as $data){
//             if ($data->id_contribuyente !== $lastId) {
//                 $contribuyente[] = [
//                     'id_contribuyente'=> $data->id_contribuyente,
//                     'id_tipo_contribuyente'=> $data->id_tipo_contribuyente,
//                     'adm_tip_contri_descripcion'=>$data->adm_tip_contri_descripcion,
//                     'razon_social'=> $data->razon_social,
//                     'sis_identi_id_doc_identidad'=>$data->sis_identi_id_doc_identidad,
//                     'doc_identi'=>$data->sis_identi_descripcion,
//                     'nro_documento'=>$data->nro_documento,
//                     'id_rubro'=>$data->id_rubro,
//                     'rubro_descripcion'=>$data->rubro_descripcion,
//                     'telefono'=> $data->telefono,
//                     'celular'=> $data->celular,
//                     'direccion_fiscal'=> $data->direccion_fiscal,
//                     'ubigeo'=> $data->ubigeo,
//                     'id_pais'=> $data->id_pais,
//                     'sis_pais_descripcion'=> $data->sis_pais_descripcion,
//                     'fecha_registro'=> $data->fecha_registro,
//                     'estado'=> $data->estado
//                 ];  
//                 $lastId = $data->id_contribuyente;
//               } 
//     };
    
//      for($j=0; $j< sizeof($contacto);$j++){
//         for($i=0; $i< sizeof($contribuyente);$i++){
//             if($contacto[$j]['id_contribuyente'] === $contribuyente[$i]['id_contribuyente']){
//                 $contribuyente[$i]['contacto'][]=$contacto[$j];
//             }
//         }
//     }

//     for($j=0; $j< sizeof($cuenta);$j++){
//         for($i=0; $i< sizeof($contribuyente);$i++){
//             if($cuenta[$j]['id_contribuyente'] === $contribuyente[$i]['id_contribuyente']){
//                 $contribuyente[$i]['cuenta'][]=$cuenta[$j];
//             }
//         }
//     }

//     return response()->json($contribuyente);

//     } catch(QueryException $e) {
//         // return Response::json(['error' => 'Error msg'], 404); // Status code here
//         return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
//     }

// }
    public function guardar_contribuyente(Request $request){
        $data = DB::table('contabilidad.adm_contri')->insertGetId(
            [
            'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
            'id_doc_identidad'      => $request->id_doc_identidad,
            'nro_documento'         => $request->nro_documento,
            'razon_social'          => $request->razon_social,
            'telefono'              => $request->telefono,
            'celular'               => $request->celular,
            'direccion_fiscal'      => $request->direccion_fiscal,
            'ubigeo'                => $request->ubigeo,
            'id_pais'               => $request->id_pais,
            'estado'                => $request->estado,
            'fecha_registro'        => $request->fecha_registro
            ],
            'id_contribuyente'
        );
        if($data > 0){
            $data2 = DB::table('contabilidad.adm_ctb_rubro')->insertGetId(
                [
                'id_contribuyente'      => $data,
                'id_rubro'              => $request->id_rubro,
                'fecha_registro'        => $request->fecha_registro
                ],
                'id_rubro_contribuyente'
            );
            return response()->json($data2);
        }
    }
    public function eliminar_contribuyente($id){
        //  $data = DB::table('adm_ctb_rubro')->where('id_contribuyente', '=', $id)->delete();
        // if($data >0 || $data ==""){
        //     $data2 = DB::table('adm_contri')->where('id_contribuyente', '=', $id)->delete();
        //     return response()->json($data2);
        // }
        $data = DB::table('contabilidad.adm_contri')->where('id_contribuyente', $id)
        ->update([
             'estado' => 0
        ]);
        return response()->json($data);

    }
    public function actualizar_contribuyente(Request $request, $id){
        $data = DB::table('contabilidad.adm_contri')->where('id_contribuyente', $id)
        ->update([
            'id_tipo_contribuyente' => $request->id_tipo_contribuyente,
            'id_doc_identidad'      => $request->id_doc_identidad,
            'nro_documento'         => $request->nro_documento,
            'razon_social'          => $request->razon_social,
            'telefono'              => $request->telefono,
            'celular'               => $request->celular,
            'direccion_fiscal'      => $request->direccion_fiscal,
            'ubigeo'                => $request->ubigeo,
            'id_pais'               => $request->id_pais,
            'estado'                => $request->estado,
            'fecha_registro'        => $request->fecha_registro
        ]);
        
        if($data > 0){
            $data2 = DB::table('contabilidad.adm_ctb_rubro')->where('id_contribuyente', $id)
            ->update([
                'id_rubro'              => $request->id_rubro,
                'fecha_registro'        => $request->fecha_registro
            ]); 
        }
        return response()->json($data);
    }
 
 
        public function mostrar_contribuyente_contactos_contribuyente($id)
        {
             $adm_ctb_contac = DB::table('contabilidad.adm_ctb_contac')
            ->join('adm_contri', 'adm_ctb_contac.id_contribuyente', '=', 'adm_contri.id_contribuyente')
            ->select(
                'adm_ctb_contac.id_datos_contacto',
                'adm_ctb_contac.id_contribuyente',
                'adm_contri.razon_social',
                'adm_ctb_contac.nombre',
                'adm_ctb_contac.cargo',
                'adm_ctb_contac.telefono',
                'adm_ctb_contac.email',
                'adm_ctb_contac.estado',
                // 'adm_ctb_contac.estado', 
                DB::raw("(CASE WHEN adm_ctb_contac.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS descripcion_estado")
                
                )
                ->where([
                    ['adm_contri.id_contribuyente', '=', $id],
                   ['adm_ctb_contac.estado', '=', 1]
                   ])
                ->orderBy('adm_ctb_contac.id_datos_contacto', 'asc')
            ->get();


            $data = ["adm_ctb_contac"=>$adm_ctb_contac];   

            return response()->json($data);
        }

 
        public function guardar_contribuyente_contacto(Request $request){
            $data = DB::table('contabilidad.adm_ctb_contac')->insertGetId(
                [
                'id_contribuyente' => $request->id_contribuyente,
                'nombre'      => $request->nombre,
                'telefono'         => $request->telefono,
                'email'          => $request->email,
                'estado'              => $request->estado,
                'fecha_registro'        => $request->fecha_registro,
                'cargo'              => $request->cargo
                
                ],
                'id_datos_contacto'
            );
            return response()->json($data);
        }

        public function eliminar_contribuyente_contacto($id){
            // $data = DB::table('adm_ctb_contac')->where('id_datos_contacto', '=', $id)->delete();
            // return response()->json($data);
            $data = DB::table('adm_ctb_contac')->where('id_datos_contacto', $id)
            ->update([
                'estado' => 0
            ]);
            return response()->json($data);
    
        }
        public function actualizar_contribuyente_contacto(Request $request, $id){
            $data = DB::table('contabilidad.adm_ctb_contac')->where('id_datos_contacto', $id)
            ->update([
                'id_contribuyente' => $request->id_contribuyente,
                'nombre'      => $request->nombre,
                'telefono'         => $request->telefono,
                'email'          => $request->email,
                'estado'                => $request->estado,
                'fecha_registro'        => $request->fecha_registro,
                'cargo'        => $request->cargo
            ]);
            return response()->json($data);
        }

 //
//  public function mostrar_cuenta_contribuyentes()
//  {
//      $data = cuenta_contribuyente::all();
//       return response()->json($data);

//  }
//  public function mostrar_cuenta_contribuyente($id)
//  {
//      try {
//      $data = cuenta_contribuyente::where('id_cuenta_contribuyente', $id)->first();
//      return response()->json($data);
//  } catch(QueryException $e) {
//      // return Response::json(['error' => 'Error msg'], 404); // Status code here
//      return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
//  }

//  }
public function mostrar_contribuyente_data_contribuyente_cuenta($id){
    try {
        $result = DB::table('contabilidad.adm_contri')
        ->join('adm_cta_contri', 'adm_contri.id_contribuyente', '=', 'adm_cta_contri.id_contribuyente')
        ->leftJoin('adm_tp_cta', 'adm_cta_contri.id_tipo_cuenta', '=', 'adm_tp_cta.id_tipo_cuenta')
        ->leftJoin('cont_banco', 'adm_cta_contri.id_banco', '=', 'cont_banco.id_banco')
        ->leftJoin('adm_ctb_rubro', 'adm_cta_contri.id_contribuyente', '=', 'adm_ctb_rubro.id_contribuyente')
                ->leftJoin(DB::raw("(SELECT 
                adm_contri.id_contribuyente,
                adm_contri.razon_social
                FROM adm_contri ) as banco"),function($join){
                $join->on("banco.id_contribuyente","=","cont_banco.id_contribuyente");
                })
                ->leftJoin(DB::raw("(SELECT 
                adm_rubro.id_rubro,
                adm_rubro.descripcion
                FROM adm_rubro ) as rubro"),function($join){
                $join->on("rubro.id_rubro","=","adm_ctb_rubro.id_rubro");
                })
           
           ->select(
        //   'adm_contri.id_contribuyente',
         'adm_contri.razon_social', 
         'adm_cta_contri.id_contribuyente',
         'rubro.id_rubro',
         'rubro.descripcion AS descripcion_rubro',
            //  'adm_cta_contri.id_contribuyente AS id_contribuyente_cuenta',
             'adm_cta_contri.id_cuenta_contribuyente',
             'adm_cta_contri.id_banco',
             'banco.razon_social AS banco_razon_social',
             'cont_banco.codigo AS cont_banco_codigo',
             'adm_cta_contri.id_tipo_cuenta',
              'adm_tp_cta.descripcion',
             'adm_cta_contri.nro_cuenta',
             'adm_cta_contri.nro_cuenta_interbancaria',
             'adm_cta_contri.fecha_registro'
         )
         ->where([
            ['adm_contri.id_contribuyente', '=', $id],
           ['adm_cta_contri.estado', '=', 1]
           ])
         ->get();

     foreach($result as $data){
             $contribuyente[] = [
                'id_contribuyente'=> $data->id_contribuyente,
                // 'razon_social'=> $data->razon_social,
                'id_rubro'=>$data->id_rubro,
                'descripcion_rubro'=>$data->descripcion_rubro,
                'id_cuenta_contribuyente'=> $data->id_cuenta_contribuyente,
                'id_banco'=>$data->id_banco,
                'banco_razon_social'=>$data->banco_razon_social,
                'cont_banco_codigo'=>$data->cont_banco_codigo,
                'id_tipo_cuenta'=> $data->id_tipo_cuenta,
                'descripcion'=> $data->descripcion,
                'nro_cuenta'=> $data->nro_cuenta,
                'nro_cuenta_interbancaria'=> $data->nro_cuenta_interbancaria,
                'fecha_registro'=> $data->fecha_registro
            ];  
         
    };
 
    return response()->json(["adm_contri"=>$result]);

    } catch(QueryException $e) {
        // return Response::json(['error' => 'Error msg'], 404); // Status code here
        return Redirect::to('/login-me')->with('msg', ' Sorry something went worng. Please try again.');
    }
}

 public function guardar_cuenta_contribuyente(Request $request){
    $data = DB::table('contabilidad.adm_cta_contri')->insertGetId(
        [
        'id_contribuyente'  => $request->id_contribuyente,
        'id_banco'          => $request->id_banco,
        'id_tipo_cuenta'    => $request->id_tipo_cuenta,
        'nro_cuenta'        => $request->nro_cuenta,
        'nro_cuenta_interbancaria'  => $request->nro_cuenta_interbancaria,
        'fecha_registro'            => $request->fecha_registro

        ],
        'id_cuenta_contribuyente'
    );
    return response()->json($data);

 }
 public function actualizar_cuenta_contribuyente(Request $request, $id){
    $data = DB::table('contabilidad.adm_cta_contri')->where('id_cuenta_contribuyente', $id)
    ->update([
        'id_contribuyente'  => $request->id_contribuyente,
        'id_banco'          => $request->id_banco,
        'id_tipo_cuenta'    => $request->id_tipo_cuenta,
        'nro_cuenta'        => $request->nro_cuenta,
        'nro_cuenta_interbancaria'  => $request->nro_cuenta_interbancaria,
        'fecha_registro'            => $request->fecha_registro
    ]);
    return response()->json($data);
 }

 public function eliminar_cuenta_contribuyente($id){
    //  $data = DB::table('adm_ctb_contac')->where('id_cuenta_contribuyente', '=', $id)->delete();
    //  return response()->json($data);
    $data = DB::table('contabilidad.adm_cta_contri')->where('id_cuenta_contribuyente', $id)
    ->update([
        'estado'  => 0
    ]);
    return response()->json($data);
 }


 

 public function mostrar_documentos()
 {
    $sis_identi = DB::table('contabilidad.sis_identi')
    ->select(
       'sis_identi.id_doc_identidad',
       'sis_identi.descripcion',
       'sis_identi.longitud',
       'sis_identi.estado',
       DB::raw("(CASE WHEN sis_identi.estado = 1 THEN 'Habilitado' ELSE 'Deshabilitado' END) AS descripcion_estado")

       )
       ->where([
  
          ['sis_identi.estado', '=', 1]
          ])
       ->orderBy('sis_identi.id_doc_identidad', 'asc')
   ->get();
   return response()->json($sis_identi);

 }
 public function mostrar_doc_idendidad($id)
 {
    $sis_identi = DB::table('contabilidad.sis_identi')
    ->select(
        'sis_identi.id_doc_identidad',
        'sis_identi.descripcion',
        'sis_identi.longitud',
        'sis_identi.estado'
        )
        ->where([
            ['sis_identi.id_doc_identidad', '=', $id],
            ['sis_identi.estado', '=', 1]
           ])
        ->orderBy('sis_identi.id_doc_identidad', 'asc')
    ->get();
    return response()->json(["sis_identi"=>$sis_identi]);

 }
 public function guardar_documento(Request $request){
    $data = DB::table('contabilidad.sis_identi')->insertGetId(
        [
        'descripcion'=> $request->descripcion,
        'longitud'   => $request->longitud,
        'estado'     => $request->estado
        ],
        'id_doc_identidad'
    );
    return response()->json($data);

 }
 public function eliminar_documento($id){
    // $data = DB::table('contabilidad.sis_identi')->where('id_doc_identidad', '=', $id)->delete();
    // return response()->json($data);
    $data = DB::table('contabilidad.sis_identi')->where('id_doc_identidad', $id)
    ->update([
  
        'estado'     => 0
    ]);

 }
 public function actualizar_documento(Request $request, $id){
    $data = DB::table('contabilidad.sis_identi')->where('id_doc_identidad', $id)
    ->update([
        'descripcion' => $request->descripcion,
        'longitud'    => $request->longitud,
        'estado'     => $request->estado
    ]);
    return response()->json($data);
 }



}



