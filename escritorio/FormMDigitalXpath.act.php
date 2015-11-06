<?php
	# Inicio de session.
	session_start();

	# Requerir instacia de Wordpress.
	require_once '../wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{

		global $wpdb;
		$html		= null;
		$exe		= null;
		$id			= null;
		$domain		= null;
		$xpath      = null;
		$role		= null;
		$cuil_dipu	= $_SESSION['autorizado'];
		$user_id	= $_SESSION['ID'];

		if($_POST['exe'])			$exe			= sanitize_text_field(trim($_POST['exe']));
		if($_GET['exe'])			$exe			= sanitize_text_field(trim($_GET['exe']));
		if($_POST['id'])			$id				= absint(sanitize_key(trim($_POST['id'])));
		if($_GET['id'])				$id				= absint(sanitize_key(trim($_GET['id'])));
		if($_POST['domain'])		$domain			= sanitize_text_field(trim($_POST['domain']));
		if($_POST['xpath'])			$xpath			= trim($_POST['xpath']);
		if($_POST['role'])			$role			= sanitize_text_field(trim($_POST['role']));

		
		/* insertarGaleria */
		if($exe=='insertarDomainX')
		{
			$items = array(
				'domain'		=> $domain,
				'xpath'			=> $xpath,
				'role'			=> $role 
			);
			if($wpdb->insert('m_digital_xpath',$items)): 
				$html = 'true';
			else: $html = 'false';
			endif;
		}


		
		/* getDomainX */		
		if($exe=='getDomainX')
		{
			$dx = $wpdb->get_row("select domain,xpath,role from m_digital_xpath where id=".$id.";");
			$html = $dx->domain.'{%SPLIT%}'.$dx->xpath.'{%SPLIT%}'.$dx->role;
		}

		/* updateDomainX */
		if($exe=='updateDomainX')
		{
			$set = array('domain'=>$domain,'xpath'=>$xpath,'role'=>$role);
			$w   = array('id'=>$id);
			if($wpdb->update('m_digital_xpath',$set,$w)): $html='true';
			else: $html='false';
			endif;
		}

		/* deleteDomainX */
		if($exe=='deleteDomainX')
		{
			if($wpdb->delete('m_digital_xpath',array('id'=>$id))): $html='true';
			else: $html='false';
			endif;
		}

		/*
		 FORM FormP
		______________
		*/
		if($exe=='FormP')
		{
			$_SESSION['pc'] = $_SESSION['pP'];
			echo 'true';
			die;
		}

		/*
		 FORM FormA
		______________
		*/
		if($exe=='FormA')
		{
			$_SESSION['pc'] = $_SESSION['pA'];
			echo 'true';
			die;
		}

		/*
		 FORM FormS
		__________
		*/
		if($exe=='FormS')
		{
			$_SESSION['pc'] = $_SESSION['pS'];
			echo 'true';
			die;
		}


		/*
		 FORM FormU
		______________
		*/
		if($exe=='FormU')
		{
			$_SESSION['pc'] = $_SESSION['pU'];
			echo 'true';
			die;;
		}

		/*
		 FORM getC
		______________
		*/		
		
		if($_GET['exe']=='getC')
		{
			$html = "Visualizando página ".$_SESSION['pc']." de ".$_SESSION['pU']." (".$_SESSION['pT']." registros)";
		}

		/*
		 FORM FormBB
		______________
		*/
		if($exe=='FormBB')
		{
			$_SESSION['buscar'] = true;
			$_SESSION['campo']  = sanitize_text_field(trim($_POST['campo']));
			$_SESSION['str']  = sanitize_text_field(trim($_POST['str']));
			$html = 'true';
		}

		/*
		 FORM FormBC
		___________
		*/
		if($exe=='FormBC')
		{
			$_SESSION['buscar'] = false;
			$_SESSION['campo']  = null;
			$_SESSION['str']  = null;
			$html = 'true';
		}

		/*
		 * Salida Final
		 */
		echo $html;
		
	}
	else
	{
		wp_redirect(APP_URL.'/escritorio/index.php');
	}
