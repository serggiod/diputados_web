<?php
	# Inicio de session.
	session_start();

	# Cambiar directorio.
	chdir('../');

	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
	require_once APP_PATH.'/escritorio/admin_functions.php';

	if(is_user_logged_in() && current_user_can('administrator')):
		echo admin_header();
		require_once APP_PATH.'/escritorio/Menu.js.php';
		if($_GET['jsfile'])
		{
			$jsfile = sanitize_text_field(trim($_GET['jsfile'])).'.js.php';
			if($jsfile=='FormImportarUsers.js.php')
			{
				if($_SESSION['super']=='true')
				{
					require_once APP_PATH.'/escritorio/'.$jsfile;
				}
			}
			else if($jsfile=='FormMDigitalXpath.js.php')
			{
				if($_SESSION['super']=='true')
				{
					require_once APP_PATH.'/escritorio/'.$jsfile;
				}
			}
			else
			{
				require_once APP_PATH.'/escritorio/'.$jsfile;
			}
		}
		require_once APP_PATH.'/escritorio/FormAbout.js.php';
		require_once APP_PATH.'/escritorio/FormLogout.js.php';
		require_once APP_PATH.'/escritorio/FormManual.js.php';
		require_once APP_PATH.'/escritorio/FormPassword.js.php';
		echo admin_footer();
	else:
		wp_redirect(APP_URL.'/escritorio/index.php');
	endif;