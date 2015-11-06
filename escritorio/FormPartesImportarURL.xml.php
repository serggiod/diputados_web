<?php
	# Inicio de session.
	session_start();

	# Requerir instacia de Wordpress.
	require_once '/home/sdominguez/Desarrollo_Web/diputados_web/public_html/wp-config.php';

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
			$w = "and p.".$_SESSION['campo']." like '%".$_SESSION['str']."%'";
		}

		$_SESSION['pP'] = 1;
		$_SESSION['pA'] = $pc -1;
		$_SESSION['pS'] = $pc +1;
		$_SESSION['pT'] = $wpdb->get_var("select count(*) from taxonomia t inner join partes p on p.id=t.parte_id where t.cuil_dipu='".$cuil."' and t.type='noticia' and p.categoria_id is null ".$w);
		$_SESSION['pU'] = $_SESSION['pT'] / $le;
		if(fmod($_SESSION['pT'],$le)) $_SESSION['pU'] = intval($_SESSION['pT'] / $le) +1;

		if($_SESSION['pA'] < $_SESSION['pP']) $_SESSION['pA'] = 1;
		if($_SESSION['pS'] > $_SESSION['pU']) $_SESSION['pS'] = $_SESSION['pU'];

		
		$sql = "select p.id id,p.titulo titulo,date_format(p.fecha,'%d-%m-%Y') fechaes,date_format(p.estado_in,'%d-%m-%Y') publicado,p.hora hora,estado from taxonomia t inner join partes p on p.id=t.parte_id where t.cuil_dipu='".$cuil."' and t.type='noticia' and p.categoria_id is null ".$w." order by p.fecha desc, p.hora desc limit ".$li.",".$le.";";

		$est = array(0=>'Pendiente',1=>'Publicado');
		foreach($wpdb->get_results($sql,OBJECT) as $item)
		{
			$xml .= '	<row id="'.$item->id.'">'."\n";
			$xml .= '		<cell>'.$item->titulo.'</cell>'."\n";
			$xml .= '		<cell>'.$item->fechaes.'</cell>'."\n";
			$xml .= '		<cell>'.$item->publicado.'</cell>'."\n";
			$xml .= '		<cell>'.$item->hora.'</cell>'."\n";
			$xml .= '		<cell>'.$est[$item->estado].'</cell>'."\n";
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
