<?php
	
	session_start();
	error_reporting(E_ALL);

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	
	header('Content-Type: application/json', true);
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

	use App\Models\{Conexion, Create, Delete, LoginUser, Mail, Read, Tools, Update};
	
	require_once 'config.php';
	require_once 'vendor/autoload.php';

	if( !empty( $_POST ) ){

		$postData = $_POST;

	} else {

		$rawPostBody = file_get_contents( 'php://input' );
		$postData = json_decode( $rawPostBody, true );
		// var_dump($postData);

	}

	$response=['success' => false,'data' => null];
	$method = $_SERVER['REQUEST_METHOD'];
	// var_dump($method);

	switch ($method) {

		case 'OPTIONS':
		break;

		## GET

		case 'GET':

			if( !isset( $_GET['action'] ) ) {

				$response = ['success'=>false,'data'=>"GET: Parámetro de acción no disponible"];
				
			} else {
				
				switch ( $_GET['action'] ) {

					case 'test':
						$response['success'] = true;
						$response['data'] = "test get ok";						
						break;
						
					default:
						$response="No se encontró el método o no se elijió ninguno en GET";
						http_response_code(200);						
						break;

				}
			}

		break;
		
		## POST

		case 'POST':
		
			if ( !isset( $postData['postData']) ){

				$response['data'] = 'POST: Parámetro Post no definido: Undefined';
				$response['success'] = false;

			}
			elseif( empty($postData) ){

				$response['data'] = 'Parámetro Post no disponible: Empty';
				$response['success'] = false;

			}else{

				switch ($postData['postData']) {

					case 'test':
						// $response = Create::test('contacto@Createagencia.com');
						$response['success'] = true;
						$response['data'] = "Test Post";
						break;
					
					case 'addEmailToNewsletter':
						$response = Create::addEmailToNewsletter($postData);
					break;
					
					default:
						$response['data'] = 'Método POST no disponible: 3050';
						$response['success'] = false;
						break;
				}
			}


		break;

		default:
			$response['success'] = false;
			$response['data'] = 'Método no disponible Get o Post';
		break;
	}

	echo json_encode($response);
	http_response_code(202);

 ?>