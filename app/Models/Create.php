<?php

	namespace App\Models;
	use \PDO;
	use \DateTime;
	use \DateTimeZone;
	use \Illuminate\Database\Capsule\Manager as Capsule;
	use \DrewM\MailChimp\MailChimp;

	// $capsule = new Capsule;

	// $capsule->addConnection([
	// 	'driver'    => 'mysql',
	// 	'host'      => HOST,
	// 	'database'  => NAME,
	// 	'username'  => USER,
	// 	'password'  => PASS,
	// 	'charset'   => 'utf8',
	// 	'collation' => 'utf8_unicode_ci',
	// 	'prefix'    => '',
	// ]);

	// $capsule->setAsGlobal();
	// $capsule->bootEloquent();

	class Create{

		
		function __construct()
		{
			
		}

		protected $table = 'user';
		protected $fillable = ['email','password'];

		static function test(){
			
			// $mail = new mail();
			// $result = $mail->welcomeMail('Andrea', 'alejandro@wemobile.com.mx', '123456');

			$result['success'] = true;
			$result['data'] = "true test functions.php";
			return $result;

		}

		static function addEmailToNewsletter($data) {

			$email = !$data["email"] ? false : trim($data["email"]);

			$email = strtolower($email);

			if ( !filter_var( $email , FILTER_VALIDATE_EMAIL ) ) {

				$result['data'] = 'El formato del email es inválido';
				return $result;
				
			}
			
			$MailChimp = new MailChimp('c98701ff31866a4df581d5f9e4a19749-us20');

			$list_id = '11d4130f83';

			$resultAdd = $MailChimp->post(
							"lists/$list_id/members", 
							[
								'email_address' => $email,
								'status'        => 'subscribed',
							]);

			if ($MailChimp->success()) {
				
				$result['data'] = "Su dirección fue agregada exitosamente";
				$result['success'] = true;
				return $result;

			} else {
				// var_dump( $MailChimp->getLastError() );
				$result['data'] = $MailChimp->getLastError();

			}

			return $result;

		}
			

	}
 ?>