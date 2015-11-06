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
		$sql = "select f.titulo titulo,f.texto epigrafe,f.fecha fecha,f.autor autor from fotografias f where f.id='".$id."' and f.categoria_id is null;";
		$item= $wpdb->get_row($sql,OBJECT);
		$xml = '<items>'."\n";
		$xml .= '	<item type="input"      name="titulo"    label="Titulo:"      validate="NotEmpty"           labelWidth="150"     labelAlign="center"  inputWidth="150"     required="true"   offsetLeft="20"                offsetTop="10"         value="'.html_entity_decode($item->titulo).'" />'."\n";
		$xml .= '	<item type="editor"     name="texto"     label="Epigrafe:"    validate="NotEmpty"           labelWidth="150"     labelAlign="center"  inputWidth="340"     inputHeight="120" required="true"                offsetLeft="5"         offsetTop="10"           position="label-top" value="'.strip_tags(html_entity_decode($item->texto)).'" />'."\n";
		$xml .= '	<item type="calendar"   name="fecha"     label="Fecha:"       dateFormat="%d-%m-%Y"         labelWidth="150"     labelAlign="center"  inputWidth="150"      required="true"   serverDateFormat="%Y-%m-%d" 	offsetLeft="20"        offsetTop="10"           value="'.$item->fechaes.'" />'."\n";
		$xml .= '	<item type="input"      name="autor"     label="Autor:"      validate="NotEmpty"           labelWidth="150"     labelAlign="center"  inputWidth="150"     required="true"   offsetLeft="20"                offsetTop="10"          value="'.html_entity_decode($item->autor).'" />'."\n";
		$xml .= '	<item type="file"       name="archivo"     label="Archivo:"      validate=""           labelWidth="300"     labelAlign="center"      inputWidth="300"     required="true"    />'."\n";		
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
