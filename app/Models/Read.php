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
	
    class Read {
        function __construct()
		{	
        }

        static function testRead() {
            $result['success'] = true;
			$result['data'] = "Read test OK";
			return $result;
		}
    }