<?php
	# Inicio de session.
	session_start();

	# Cambiar directorio.
	chdir('../');

	# Requerir instacia de Wordpress.
	require_once 'wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{

		global $wpdb;
		$cuil= $_SESSION['autorizado'];
		$xml = '<rows>'."\n";
		$w   = '';
		
		$li  = 0;
		$le  = 15;
		$pc  = $_SESSION['pc'];
		
		if(is_null($pc)) $pc = 1;
		
		if($_GET['pc']) $pc = absint($_GET['pc']);
		$li = ($pc -1) * $le;
		
		$_SESSION['pc'] = $pc;
		
		if($_SESSION['buscar'])
		{
			$w = "and ".$_SESSION['campo']." like '%".$_SESSION['str']."%'";
		}

		$_SESSION['pP'] = 1;
		$_SESSION['pA'] = $pc -1;
		$_SESSION['pS'] = $pc +1;
		$_SESSION['pT'] = $wpdb->get_var("select count(*) from m_digital_xpath where 1=1 ".$w);
		$_SESSION['pU'] = $_SESSION['pT'] / $le;
		if(fmod($_SESSION['pT'],$le)) $_SESSION['pU'] = intval($_SESSION['pT'] / $le) +1;

		if($_SESSION['pA'] < $_SESSION['pP']) $_SESSION['pA'] = 1;
		if($_SESSION['pS'] > $_SESSION['pU']) $_SESSION['pS'] = $_SESSION['pU'];

		
		$sql = "select id,domain,xpath,role from m_digital_xpath where 1=1 ".$w." order by id desc limit ".$li.",".$le.";";

		foreach($wpdb->get_results($sql,OBJECT) as $item)
		{
			$xml .= '	<row id="'.$item->id.'">'."\n";
			$xml .= '		<cell>'.$item->domain.'</cell>'."\n";
			$xml .= '		<cell>'.$item->xpath.'</cell>'."\n";
			$xml .= '		<cell>'.$item->role.'</cell>'."\n";
			$xml .= '	</row>'."\n";
		}
		$xml .= '</rows>';
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo $xml;
	}
	else
	{
		wp_redirect(APP_URL.'/escritorio/index.php');
	}
