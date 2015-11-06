<?php
	# Inicio de session.
	session_start();

	# Cambiar directorio.
	chdir('../');

	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
	require_once APP_PATH.'/escritorio/admin_functions.php';

	global $wpdb;
	$html = null;

	$exe = sanitize_text_field(trim($_POST['exe']));

	if($exe=='select')
	{
		# Definición.
		$wp_user = null;
		
		# Optener variables.
		$user = sanitize_text_field(trim($_POST['user']));
		$pass = md5(sanitize_text_field(trim($_POST['pass'])));
		
		# Preparar sql.
		$sql = "select ID,per_nombres nombre,per_apellidos apellido,user_login cuil,user_pass pass,user_email email,doctipo_detalle doc,per_docnro nro,(select meta_value from wp_usermeta where user_id=u.ID and meta_key='wp_super_user') super from wp_users u inner join personas p on p.per_cuil=u.user_login inner join doctipo d on d.id_doctipo=p.id_doctipo where u.user_login='".$user."' and u.user_pass='".$pass."' and per_esdipu=0";
		if(strpos($user,'@')) $sql = "select ID,per_nombres nombre,per_apellidos apellido,user_login cuil,user_pass pass,user_email email,doctipo_detalle doc,per_docnro nro,(select meta_value from wp_usermeta where user_id=u.ID and meta_key='wp_super_user') super from wp_users u inner join personas p on p.per_cuil=u.user_login inner join doctipo d on d.id_doctipo=p.id_doctipo where u.user_email='".$user."' and u.user_pass='".$pass."' and per_esdipu=0";

		# realizar consulta.
		$wp_user = $wpdb->get_row($sql,OBJECT);
		
		# Comprobar si el usuario exite
		if($wp_user->ID):
		
			# Autenticar al usuario.
			wp_set_auth_cookie($wp_user->ID);
			
			# Guardar sesion.
			$_SESSION['ID'] = $wp_user->ID;
			$_SESSION['nombre'] = $wp_user->nombre;
			$_SESSION['apellido'] = $wp_user->apellido;
			$_SESSION['super']	= $wp_user->super;
			$_SESSION['cuil']	= $wp_user->cuil;
			$_SESSION['status']	= true;
			$_SESSION['autorizado']	= $wpdb->get_var("select cuil_dipu from autorizados where cuil_persona='".$wp_user->cuil."';");
			$html = 'true';
		else:
			$html = 'false';
		endif;
	}
	
	echo $html;