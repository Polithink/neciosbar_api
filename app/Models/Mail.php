<?php 

	namespace App\Models;
	use \PDO;
	use \Dotenv;
	
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	// include 'phpmailer.inc.php';
	// include 'smtp.inc.php';
	// include_once 'conf.php';

	class Mail
	{
		
		function __construct()
		{
		}

		public static function sendEmail( $emailAdress, $asunto, $mensaje) {

			$template = file_get_contents('assets/template-email-contrapeso.html');
			$emailTemplate = str_replace( array('{{titulo}}','{{contenido}}'), array( $asunto, $mensaje ), $template);

			// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
			// $dotenv->load();

			$email = new \SendGrid\Mail\Mail(); 
			$email->setFrom(EMAIL_FROM, EMAIL_FROM_CONTACT);
			$email->setSubject($asunto);
			$email->addTo($emailAdress, "Usuario");
			// $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
			$email->addContent("text/html", $emailTemplate);

			// $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
			$sendgrid = new \SendGrid(SENGRID_API_KEY);

			$response = $sendgrid->send($email);
			
			// try {
			// 	$response = $sendgrid->send($email);
			// 	print $response->statusCode() . "\n";
			// 	print_r($response->headers());
			// 	print $response->body() . "\n";
			// } catch (Exception $e) {
			// 	echo 'Caught exception: '. $e->getMessage() ."\n";
			// }
			
			if ($response->statusCode() == 202) {
				$result['data'] = "Correo enviado";
				$result['success'] = true;
			} else {
				$result['data'] = "Correo no enviado";
				$result['success'] = false;
			}
			return $result;
		}

		public static function sendContactForm($data) {

			$name = !$data['name'] ? false : $data['name'];
			$email = !$data['email'] ? false : $data['email'];
			$phone = !$data['phone'] ? false : $data['phone'];
			$message = !isset($data['message']) ? false : $data['message'];
			$aviso = !$data['aviso'] ? false : $data['aviso'];
			
			if ( !$name  ) {
				$result['data'] = "Es necesario proporcionar su nombre";
				return $result;
			}
			if ( !$email  ) {
					$result['data'] = "Es necesario proporcionar su email";
					return $result;
			}
			if ( !$phone  ) {
					$result['data'] = "Es necesario proporcionar su teléfono";
					return $result;
			}
			if ( !$message  ) {
					$result['data'] = "Es necesario proporcionar el mensaje,";
					return $result;
			}
			if ( !$aviso  ) {
					$result['data'] = "Es necesario aceptar el aviso de privacidad ";
					return $result;
			}
			
			$mensaje = " <h1>Nuevo contacto desde el sitio web</h1>
			<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Nombre: $name </p>
			<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Email: $email </p>
			<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Teléfono: $phone </p>
			<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Mensaje: $message </p>";
			
			$emailResult = self::sendEmail( 'contacto@coalicioncontrapeso.org', 'Contacto desde sitio Web', $mensaje);

			if (!$emailResult['success']) {
				$result['data'] = "Error al enviar el correo";
				return $result;
			}

			$result['data'] = "El correo se envió correctamente";
			$result['success'] = true;
			return $result;
		}

		public static function sendContactFormSC($data) {

			$name = !$data['name'] ? false : $data['name'];
			$emailUser = !$data['email'] ? false : $data['email'];
			$phone = !$data['phone'] ? false : $data['phone'];
			$message = !isset($data['message']) ? false : $data['message'];
			$aviso = !$data['aviso'] ? false : $data['aviso'];
			
			if ( !$name  ) {
				$result['data'] = "Es necesario proporcionar su nombre";
				return $result;
			}
			if ( !$emailUser  ) {
					$result['data'] = "Es necesario proporcionar su email";
					return $result;
			}
			if ( !$phone  ) {
					$result['data'] = "Es necesario proporcionar su teléfono";
					return $result;
			}
			if ( !$message  ) {
					$result['data'] = "Es necesario proporcionar el mensaje,";
					return $result;
			}
			if ( !$aviso  ) {
					$result['data'] = "Es necesario aceptar el aviso de privacidad ";
					return $result;
			}
			
			$mensaje = " <h1>Nuevo contacto desde el sitio web</h1>
			<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Nombre: $name </p>
			<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Email: $emailUser </p>
			<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Teléfono: $phone </p>
			<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Mensaje: $message </p>";
			
			$template = file_get_contents('assets/template-email-scritica.html');
			$emailTemplate = str_replace( array('{{titulo}}','{{contenido}}'), array( $message, $mensaje ), $template);

			// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
			// $dotenv->load();

			$email = new \SendGrid\Mail\Mail(); 
			$email->setFrom(EMAIL_FROM, $name);
			$email->setSubject($message);
			$email->addTo(EMAIL_FROM, "Coalición ContraPESO");
			// $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
			$email->addContent("text/html", $emailTemplate);

			// $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
			$sendgrid = new \SendGrid(SENGRID_API_KEY);

			$response = $sendgrid->send($email);
			
			// try {
			// 	$response = $sendgrid->send($email);
			// 	print $response->statusCode() . "\n";
			// 	print_r($response->headers());
			// 	print $response->body() . "\n";
			// } catch (Exception $e) {
			// 	echo 'Caught exception: '. $e->getMessage() ."\n";
			// }
			$result['log'] = $response;
			if ($response->statusCode() == 202) {
				$result['data'] = "Correo enviado";
				$result['success'] = true;
				
			} else {
				$result['data'] = "Correo no enviado";
				$result['success'] = false;
			}
			return $result;
		}

		// public static function sendThankYouEmail( $name ) {

		// 	$mensaje = '<h1>Gracias por ponerte en contacto</h1>
		// 	<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hola *****</p>
		// 	<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Gracias por ponerte en contaco con nosotros.</p>
		// 	<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Mensaje muy chévere</p>
		// 	<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Otro mensaje</p>';

		// }


	}
 ?>

