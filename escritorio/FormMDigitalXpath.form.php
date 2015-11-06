<?php
	# Inicio de session.
	session_start();

	# Cambiar directorio.
	chdir('../');

	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{
		
		global $wpdb;
		$exe  = sanitize_text_field($_GET['exe']);
		$id  = absint(sanitize_key($_GET['id']));
		$sql = "select id,domain,xpath from m_digital_xpath";
		$item= $wpdb->get_row($sql,OBJECT);
		$xml = '<items>'."\n";
		$xml .= '	<item type="input"      name="domain"    label="Dominio:"      validate="NotEmpty"           labelWidth="150"     labelAlign="center"  inputWidth="150"     required="true"   offsetLeft="20"                offsetTop="10"         value="'.$item->domain.'" />'."\n";
		$xml .= '	<item type="input"      name="xpath"     label="Xpath:"        validate="NotEmpty"           labelWidth="150"     labelAlign="center"  inputWidth="150"     required="true"   offsetLeft="20"                offsetTop="10"         value="'.$item->xpath.'" />'."\n";		
		if($exe=='nuevo')
		{
			$xml .= '	<item type="button"    name="aceptar"  value="Aceptar"     width="150" inputLeft="172" inputTop="320" position="absolute"  />'."\n";
			$xml .= '	<item type="button"    name="cancelar" value="Cancelar"    width="150" inputLeft="20" inputTop="320" position="absolute"  />'."\n";
		}
		if($exe=='visualizar')
		{
			$xml .= '	<item type="button"     name="aceptar"   value="Aceptar"      width="1500"                   inputLeft="100"      inputTop="32"       position="absolute"  />'."\n";
		}
		if($exe=='modificar')
		{
			$xml .= '	<item type="button"    name="aceptar"  value="Aceptar"     width="150" inputLeft="172" inputTop="320" position="absolute"  />'."\n";
			$xml .= '	<item type="button"    name="cancelar" value="Cancelar"    width="150" inputLeft="20" inputTop="320" position="absolute"  />'."\n";
		}
		$xml .= '	<item type="newcolumn"/>'."\n";
		$xml .= '</items>';
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo $xml;
	}
	else
	{
		wp_redirect(APP_URL.'/escritorio/index.php');
	}
