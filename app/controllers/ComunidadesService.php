<?php

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;

class ComunidadesService
{
	public function returndata($str,$start,$end){
      $pattern = sprintf(
          '/%s(.+?)%s/ims',
          preg_quote($start, '/'), preg_quote($end, '/')
      );

      if (preg_match($pattern, $str, $matches)) {
          list(, $match) = $matches;
          return $match;
      }
      return 'Vacio';
    }

    function parse($coord)
	{
	    $strings = explode(' ',$coord);
	    $ret['latitud'] = $this->degree2decimal($strings[0]);
	    $ret['longitud'] = $this->degree2decimal($strings[1]);
	    return $ret;
	}
	function degree2decimal($deg_coord="")
	{
		$dms = [];
	    preg_match("/(.*)°(.*)'(.*)\"/", $deg_coord, $dms);
	    return $dms[1]+((($dms[2]*60)+($dms[3]))/3600);
	}

	public function lista(){
		$client = new Client([
		    // Base URI is used with relative requests
		    'base_uri' => 'http://www.ibcperu.org/mapas/',
		    // You can set any number of default request options.
		    'timeout'  => 20.0,
		]);

		$promise = $client->requestAsync('POST','sicna-resultados',[
			'form_params' => [
				'nombre' => '',
				'codigo' => '',	
				'tipo' =>	'0',
				'sector' =>	'0',
				'cuenca' =>	'0',
				'sub-cuenca' =>	'0',
				'departamento' =>	'0',
				'provincia' =>	'0',
				'distrito' =>	'0',
			],
			'query' => ['pagenum' => '1']
		]);

		$promise->then(
		    function (ResponseInterface $res) {
		        $resultado = $res->getBody();
		        
				$htmlComunidades = str_get_html($resultado);
				$nombreComunidad = $htmlComunidades->find('div.comunidad')[0]->children(0)->innertext;
				$comunidades = explode('<br/>', $htmlComunidades->find('div.comunidad')[0]->innertext);
				

				$resultComunidad = [];
				$resultComunidad['nombre'] = $nombreComunidad;
				foreach ($comunidades as $index => $comunidad) {
					$comunidad.='<final>';
					$key =  $this->returndata($comunidad,'<b>',': </b>');
					$data =  $this->returndata($comunidad,'</b>','<final>');
					
					switch ($key) {
						case 'Grupo Etnico ':
							$key = 'grupo_etnico';
							$resultComunidad[$key] = $data;
							break;
						case 'Población Total':
							$key = 'poblacion';
							$resultComunidad[$key] = $data;
							break;
						case 'Numero de familias':
							$key = 'num_familias';
							$resultComunidad[$key] = $data;
							break;
						case 'Numero de escolares ':
							$key = 'num_escolares';
							$resultComunidad[$key] = $data;
							break;
						case '"Río ':
							$key = 'rio';
							$resultComunidad[$key] = $data;
							break;
						case 'Ubicación':
							$key = 'ubicacion';
							$resultComunidad[$key] = $data;
							break;
						case 'Coordenadas':
							$key = 'coordenadas';
							$resultComunidad  = array_merge($resultComunidad, $this->parse($data));
							break;
						case 'Escuela Primaria':
							$key = 'esc_primaria';
							$resultComunidad[$key] = $data;
							break;
						case 'Escuela Secundaria':
							$key = 'esc_secundaria';
							$resultComunidad[$key] = $data;
							break;
						case 'Escuela Bilingüe':
							$key = 'esc_bilingue';
							$resultComunidad[$key] = $data;
							break;
						case 'Puesto de salud':
							$key = 'puesto_salud';
							$resultComunidad[$key] = $data;
							break;
						case 'Productos en venta':
							$key = 'puesto_venta';
							$resultComunidad[$key] = $data;
							break;
					}
				}

				Debug::varDump($resultComunidad);
		    },
		    function (RequestException $e) {
		        echo $e->getMessage() . "\n";
		        echo $e->getRequest()->getMethod();
		    }
		);

		$promise->wait();
	}
}