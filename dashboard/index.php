<?php
// session_start();
date_default_timezone_set('Europe/London');
// var_dump($_SERVER);exit;
require('vendor/autoload.php');
/** @var \Base $f3 */
$f3 = \Base::instance();
$f3->set('CACHE', true);
$f3->session = new Session();

$f3->set('APP_VERSION', '0.3.1');
$f3->BITMASK = ENT_COMPAT|ENT_SUBSTITUTE;

//ini_set('display_errors', 1);
//error_reporting(-1);

$f3->config('app/config/config.ini.php');
// new Session(NULL,'CSRF');


$preErr = Setup::instance()->preflight();
if (!empty($preErr)) {
	header('Content-Type: text;');
	die(implode("\n",$preErr));
}


$token=$f3->get('SESSION.token');
// echo $token;exit;
if (Home::isLoggedIn()) {

	$f3->config('app/routes/routes_admin.ini.php');
	Setup::instance()->load($f3->get('SESSION.account'));
	
	//$f3->reroute("/");
}else{
	$f3->config('app/routes/public.ini.php');
}

// $f3->set('ONERROR',
//     function($f3) {
//         // custom error handler code goes here
//         // use this if you want to display errors in a
//         // format consistent with your site's theme
// 		$code = $f3->get('ERROR.code');
// 		$text =$f3->get('ERROR.text');
// 		// echo $text;exit;
// 		if($code == 403){
// 			echo Template::instance()->render('auth/403.html');die();
// 		}
// 		if($code == 404){
// 		    $url=str_replace('/dashboard','',$_SERVER['REQUEST_URI']);
// 			$token=$f3->get('SESSION.token');
// 			$user=$f3->get('SESSION.account');

// 			if(!$token){
// 				if($user){
				    
// 					if($user=='USER'){
// 						$f3->reroute($f3->get('HOME')); //$f3->get('PROTOCOL').$f3->get('HOST')
// 					}else{
// 						// echo $user;exit;
// 						$f3->reroute($f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE').'/'.strtolower($user));
// 					}
// 				}else{
// 				    $f3->reroute($f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE'));
// 				}
// 			}else{
// 			    echo Template::instance()->render('auth/404.html');die();
// 			}
// 		}

// 		if($code == 500){
// 			echo Template::instance()->render('auth/500.html');die();
// 		}
//         // echo $f3->get('ERROR.code');exit;
//     }
// );

// var_dump($f3->ROUTES);exit;

// $f3->clear('CACHE');

$f3->run();
?>