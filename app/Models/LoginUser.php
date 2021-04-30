<?php

	namespace App\Models;
	use \PDO;
	use \DateTime;
	use \DateTimeZone;
	use \Illuminate\Database\Capsule\Manager as Capsule;

	$capsule = new Capsule;

	$capsule->addConnection([
		'driver'    => 'mysql',
		'host'      => HOST,
		'database'  => NAME,
		'username'  => USER,
		'password'  => PASS,
		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => '',
	]);

	$capsule->setAsGlobal();
	$capsule->bootEloquent();

	class LoginUser {

		static function registry(array $data){

			$result['success'] = false;

			// $response = Read::login($postData);
			// var_dump($data);
			// exit();
							
			
			$nombre     = !$data["name"] ? false : $data["name"];
            $email      = !$data["email"] ? false : $data["email"];
            $telMovil   = !$data["telMovil"] ? false : $data["telMovil"];
			$password   = !$data["password"] ? false : $data["password"];
			$privacy	= !isset($data["privacy"]) ? false : $data["privacy"];

			$registroSucursal = !isset($data["registro_sucursal"]) ? false : $data["registro_sucursal"];
			$sucursal = !isset($data['sucursal']) ? false : $data['sucursal'];
			$dia_cita = empty($data['dia_cita']) ? false : $data['dia_cita'];
			$horas_cita = !isset($data['horas_cita']) ? false : $data['horas_cita'];
		
			if ( !$nombre ) {

				$result['data'] = "Es necesario introducir su nombre";
				return $result;
				
			} 
			if ( !$email ) {

				$result['data'] = 'Es necesario escribir su email';
				return $result;

			}                       
			if ( !filter_var( $email , FILTER_VALIDATE_EMAIL ) ) {

				$result['data'] = 'El formato del email es inválido';
				return $result;

            }
            if ( !$telMovil ) {
				
				$result['data'] = "Es necesario introducir su teléfono móvil";
				return $result;

			} 
			
			if ( !$password ) {

				$result['data'] = 'Escriba una contraseña';
				return $result;

			}

			if ($registroSucursal === "on") {

				if ( $sucursal === false ) {
					$result['data'] = 'Seleccione una sucursal';
					return $result;
				}
				if ( $dia_cita === false ) {
					$result['data'] = 'Seleccione un día';
					return $result;
				}
				if ( $horas_cita === false ) {
					$result['data'] = 'Seleccione una hora';
					return $result;
				}

			}

			if ( !$privacy ) {
				$result['data'] = 'Debe aceptar el aviso de privacidad y los términos y codiciones del servicio';
				return $result;
			}
			

			$validateEmailResult = self::validateEmail($email);

			if ($validateEmailResult['success'] === true) {
				$result['data'] = $validateEmailResult['data'];
				return $result;
			}

			$password_hash = password_hash($password, PASSWORD_DEFAULT);

			$db = new Conexion();
			$conexion = $db->get_conexion();

			$sql = "INSERT 
					INTO usuariosPro(
						nombre,
						email,
						password
					) 
					VALUES (
						:nombre,
						:email,
						:password
					)";
			$query = $conexion->prepare($sql);
            $query_result = $query->execute([
					'nombre' => $nombre,
					'email' => $email,
					'password' => $password_hash
                ]);
			
            if ( !$query_result ) {

				$result['data'] = "Error al registrar el usuario";
				return $result;

			} else {

				$lastId = $conexion->lastInsertId();

				$addUserInDbAllDbResult = Tools::addUserInAllDb( $lastId );

				$verificacionResult = Create::addVerificacionEmail( $lastId );

				if ($verificacionResult['success'] === false) {
					# code...
				} else {

					$dataValidacionEmail = [
						'token' => $verificacionResult['data'],
						'email_registro' => $email,
						'subject' => "Pro Conductores: Verificación de correo electrónico"
					];

					$emailVerificationResult = Mail::notificacion_validacion_email($dataValidacionEmail);

				}
				
				// var_dump($datosPersonalesResult);

				$savePhoneResult = Update::updatePhone($lastId,$telMovil);
				if ($registroSucursal === "on") {
					$saveCitaResult = Create::addCita([
						'sucursal' => $sucursal,
						'dia_cita' => $dia_cita,
						'horas_cita' => $horas_cita,
						'clave_tramite' => '1',
						'idUsuario' => $lastId,
						'tramite' => 'Registro en sucursal'
					]);
					if ( !$saveCitaResult['success'] ) {
					
					}
				}

				$loginResult = self::login($email, $password);

				$result['data'] = 'Registro exitoso';
				$result['success'] = true;
				
				return $result;
			}

			$result['data'] = 'ok datos';
			$result['success'] = true;
			return $result;
		}
		
		public static function login( $email, $password, $captcha ){

			$result['success'] = false;

			if ( !$email ) {

				$result['data'] = "Ingresa el email";
				return $result;                
			}

			if ( !$password ) {

				$result['data'] = "Ingresa la contraseña";
				return $result;                
			}
			if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {

				$result['data'] = 'El formato del email es inválido';
				return $result;
			}

			// if (!$captcha) {
			// 	$result['data'] = 'Error captcha';
			// 	return $result;
			// }

			$recaptcha = new \ReCaptcha\ReCaptcha(GOOGLE_RECAPTCHA_KEY);
			$resp = $recaptcha->setExpectedHostname(EXPECTED_HOSTNAME)
							->verify($captcha, $_SERVER['REMOTE_ADDR']);
			if ($resp->isSuccess()) {
				// Verified!
			} else {

				$errors = $resp->getErrorCodes();

				$result['data'] = "Error. Captcha incorrecto";
				$result['success'] = false;

			}

			$user = Capsule::table('users')->select('*')->where('email','=',$email)->get();

			// var_dump($user);

			if (!$user) {
				
				$result['data'] = "Error. Verifique que los datos sean correctos.";
				$result['success'] = false;

				return $result;
			}			

			if (password_verify($password, $user['0']->password)){

				$_SESSION['active'] = true;
				// $_SESSION['data'] = $user['idUsuario'];
				// $_SESSION['data']['cuenta'] = $user['cuenta'];
				$_SESSION['data'] = [
					'idUser' => $user['0']->idUser,
					'email' => $user['0']->email
				];

				$result['data'] = "Datos correctos";
				$result['success'] = true;

				return $result;

			} else {

				$result['data'] = "Error. Verifique que los datos sean correctos.";
				$result['success'] = false;
				return $result;
				
			}
			

			return $result;

			$result['data'] = "login ok";
			$result['success'] = true;
			return $result;
		}
		public static function validateEmail($email){

			$db = new Conexion();
			$conexion = $db->get_conexion();

			$queryEmail = "SELECT * FROM usuariosPro WHERE email = :email";
			$prepared = $conexion->prepare($queryEmail);
			$prepared->execute([
				'email' => $email
			]);

			$user = $prepared->fetch(PDO::FETCH_ASSOC);

			// var_dump($user);

			if (!$user) {
				$result['success'] = false;
				$result['data'] = "No existe";
				return $result;
			}
				
			if ( $user['email'] == $email ) {

				$result['data'] = "El correo que ingresó ya está registrado.";
				$result['success'] = true;
				return $result;
			}
		}
	}

 ?>