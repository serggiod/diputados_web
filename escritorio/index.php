<?php
	# Inicio de session.
	session_start();
	
	# Cambiar directorio.
	chdir('../');
	
	# Requerir instacia de Wordpress.
	require_once './wp-config.php';
	
	# Requerir funciones de administración.
	require_once APP_PATH.'/escritorio/admin_functions.php';
	
	session_unset();
	session_destroy();
	wp_logout();
	
	echo admin_header();
	require_once APP_PATH.'/escritorio/FormLogin.js.php';	
	echo admin_footer();
	