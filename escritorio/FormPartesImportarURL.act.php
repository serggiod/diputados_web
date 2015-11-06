<?php
	# Inicio de session.
	session_start();
	
	ini_set('max_execution_time', 300);

	# Requerir instacia de Wordpress.
	require_once '../wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{

		global $wpdb;
		$html    = null;
		$exe     = null;
		$urlT    = null;

		if($_POST['exe'])     $exe     = sanitize_text_field(trim($_POST['exe']));
		if($_GET['exe'])      $exe     = sanitize_text_field(trim($_GET['exe']));
		if($_POST['urlT'])    $urlT      = sanitize_text_field(trim($_POST['urlT']));

		# Insertar nuevo parte.
		if($exe=='getXPath')
		{
			$urlT = dirname($urlT);
			
			if(strstr($urlT,'https')): $urlT = substr($urlT,8);
			else: $urlT = substr($urlT,7);
			endif;
			
			if(strstr($urlT,'www')): $urlT = substr($urlT,4);
			endif;
			
			global $wpdb;
			$xpath = array();
			$sql = "select xpath,role from m_digital_xpath where domain='".$urlT."';";
			foreach($wpdb->get_results($sql,OBJECT) as $item)
			{
				$xpath[$item->role] = $item->xpath;
			}
			echo json_encode($xpath);die;
		}
	}
	else
	{
		wp_redirect(APP_URL.'/escritorio/index.php');
	}
