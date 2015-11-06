<?php
	# Inicio de session.
	session_start();

	# Requerir instacia de Wordpress.
	require_once '/home/sdominguez/Desarrollo_Web/diputados_web/public_html/wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{

		global $wpdb;
		$xml = '<rows>';
		$sql = "select user_login cuil,per_nombres nombre,per_apellidos apellido,doctipo_detalle doc,per_docnro nro, per_email email,(select meta_value from wp_usermeta where user_id=ID and meta_key='wp_super_user') super from wp_users u inner join personas p on u.user_login=p.per_cuil inner join doctipo d on p.id_doctipo=d.id_doctipo order by p.per_apellidos asc;";
		foreach($wpdb->get_results($sql,OBJECT) as $item)
		{
			$xml .= '	<row id="'.$item->cuil.'">'."\n";
			$xml .= '		<cell>'.$item->cuil.'</cell>'."\n";
			$xml .= '		<cell>'.$item->apellido.'</cell>'."\n";
			$xml .= '		<cell>'.$item->nombre.'</cell>'."\n";
			$xml .= '		<cell>'.$item->doc.'</cell>'."\n";
			$xml .= '		<cell>'.$item->nro.'</cell>'."\n";
			$xml .= '		<cell>'.$item->email.'</cell>'."\n";
			$xml .= '		<cell>'.$item->super.'</cell>'."\n";
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
