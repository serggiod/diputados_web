<?php

	session_start();

	define('baseUrl','/diputados_web');
	define('basePath','/var/www/apps/diputados_web');
	define('legUrl','/web/home');
	define('eReporing','E_ALL');
	define('dbDriv','mysql');
	define('dbHost','localhost');
	define('dbName','diputados_web');
	define('dbUser','sdominguez');
	define('dbPass','rjwfthw72x45');
/*
	error_reporting(eReporing);

	function errorHandler($eNro,$eStr,$eFile,$eLine){
		$estr = array(
			'1' 	=>'E_ERROR',
			'2'		=>'E_WARNING',
			'4'		=>'E_PARSE',
			'8'		=>'E_NOTICE',
			'16'	=>'E_CORE_ERROR',
			'32'	=>'E_CORE_WARNING',
			'64'	=>'E_COMPILE_ERROR',
			'128'	=>'E_COMPILE_WARNING',
			'256'	=>'E_USER_ERROR',
			'512'	=>'E_USER_WARNING',
			'1024' 	=>'E_USER_NOTICE',
			'2048' 	=>'E_STRICT',
			'4096' 	=>'E_RECOVERABLE_ERROR',
			'8192' 	=>'E_ALL'
		);

		require_once basePath.'/application/lib/class.html.php';
		$tag   = new html;
		$html  = null;

		if(eReporing=='E_ALL'){
		    $html .= $tag->div('N&uacute;mero de error: '.$eNro.' ('.$estr[$eNro].')','msgError');
		    $html .= $tag->div('Mensaje de error: '.$eStr,'msgError');
		    $html .= $tag->div('Archivo de error: '.$eFile,'msgError');
		    $html .= $tag->div('L&iacute;nea del error: '.$eLine,'msgError');
		}

		echo $html;
		die;
	}

	set_error_handler("errorHandler");
	*/
	$F = scandir(basePath.'/application/lib');
	unset($F[0]);
	unset($F[1]);

	require_once basePath.'/application/lib/module.main.php';

	foreach($F as $f){
		require_once basePath.'/application/lib/'.$f;
	}
