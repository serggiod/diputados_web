<?php
	# Inicio de session.
	session_start();

	# Requerir instacia de Wordpress.
	require_once '/home/sdominguez/Desarrollo_Web/diputados_web/public_html/wp-config.php';

	# Requerir funciones de administración.
if(is_user_logged_in() && current_user_can('administrator')):
	global $wpdb;
	$html = 'false';

	function update()
	{
		global $wpdb;
		$rtn = 'false';
		$pass = sanitize_text_field(trim($_POST['pass']));
		$repass = sanitize_text_field(trim($_POST['pass']));
		if($pass == $repass):
			$ID = $_SESSION["ID"];
			$userpass = md5($pass);
			if($wpdb->update('wp_users',array('user_pass'=>$userpass),array('ID'=>$ID))):
				$rtn = 'true';
			endif; 
		endif;
		return $rtn;
	}
	
	$exe = sanitize_text_field(trim($_POST['exe']));
	switch($exe):
		case 'update':
			$html = update();
		break;
	endswitch;
	
	echo $html;
	 
else:
	wp_redirect(APP_URL.'/escritorio/index.php');
endif;