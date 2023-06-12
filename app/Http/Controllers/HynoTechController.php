<?php

namespace App\Http\Controllers;

use App\Models\Tesoreria\Area;
use App\Models\Tesoreria\Solicitud;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use DiDom\Document;

class HynoTechController extends Controller {
	//
	public static function obtenerDatosDolar($anio, $mes, $dia='') {

		//$mes = $fechaHoy->year
		//$mes = $mes +1;

		$url = "http://www.sunat.gob.pe/cl-at-ittipcam/tcS01Alias?mes={$mes}&anho={$anio}";

		$dataDolar = [];
		$intentos = 0;
		while (true){

			try {
				$req = new Client();
				$req = $req->get($url);
				/*$req = $req->post($url, [
					'form_params'=>[
						'mes'=>'12',
						'anho'=>'2018'
					]
				]);*/

				//$sunat = new Document($url, true);
				$sunat = new Document($req->getBody()->getContents());

				$datos = $sunat->find('table[class^=class]  > tr');

				foreach ($datos as $idx => $data){
					if($idx > 0){
						$hijos = $data->find('td');
						$dividido = array_chunk($hijos, 3);

						foreach ($dividido as $dataDia){
							$dataDolar[] =[
								'dia' => trim($dataDia[0]->text()),
								'compra' => trim($dataDia[1]->text()),
								'venta' => trim($dataDia[2]->text()),
								//'fecha' => Carbon::parse(date_format(trim($dataDia[0]->text()) . '/'. $mes . '/' . $anio,'d/m/Y')),
								'fecha' => Carbon::createFromFormat('d/m/Y',trim($dataDia[0]->text()) . '/'. $mes . '/' . $anio)
							];
						}
						if(count($dataDolar) < 1){
							$mes--;
							$url = "https://e-consulta.sunat.gob.pe/cl-at-ittipcam/tcS01Alias?mes={$mes}&anho={$anio}";
						}
					}
				}

				break;
			} catch (\Exception $e) {
				$intentos++;
				//echo 'Excepción capturada: ',  $e->getMessage(), "\n";
				if ($intentos > 5){
					echo 'Excepción capturada: ',  $e->getMessage(), "\n";
					echo '<br>';
					dd($dataDolar);
					die;
				}
			}
		}

		if($dia != ''){
			foreach ($dataDolar as $data){
				if(($data['dia'] * 1) >= ($dia * 1)){
					return $data;
					break;
				}
			}
		}
		//dd($dataDolar);
		return $dataDolar[count($dataDolar) - 1];
	}

	public static function obtenerNumeracion($clase, $prefijo, $campo='codigo') {

		return $clase::where($campo, 'like', $prefijo . '%')->get()->count() + 1;
	}

	public function consulta_sunat(Request $request){
        // $ruc = $_POST['ruc'];
        $ruc = $request->ruc;
        $data = file_get_contents("https://api.sunat.cloud/ruc/".$ruc);
        $info = json_decode($data, true);
        
        if($data==='[]' || $info['fecha_inscripcion']==='--'){
            $datos = array(0 => 'nada');
            // echo json_encode($datos);
            return response()->json($datos);

        }else{
        $datos = array(
            0 => $info['ruc'], 
            1 => $info['razon_social'],
            2 => date("d/m/Y", strtotime($info['fecha_actividad'])),
            3 => $info['contribuyente_condicion'],
            4 => $info['contribuyente_tipo'],
            5 => $info['contribuyente_estado'],
            6 => date("d/m/Y", strtotime($info['fecha_inscripcion'])),
            7 => $info['domicilio_fiscal'],
            8 => date("d/m/Y", strtotime($info['emision_electronica']))
        );
            // echo json_encode($datos);
            return response()->json($datos);

        }
     }
}
