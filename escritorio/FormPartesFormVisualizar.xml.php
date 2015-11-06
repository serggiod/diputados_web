<?php
	# Inicio de session.
	session_start();

	# Requerir instacia de Wordpress.
	require_once '/home/sdominguez/Desarrollo_Web/diputados_web/public_html/wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{
		
		global $wpdb;
		$exe  = sanitize_text_field($_GET['exe']);
		$id  = absint(sanitize_key($_GET['id']));
		$sql = "select p.volanta volanta,p.titulo titulo,p.bajada bajada,cabeza,cuerpo,p.fecha fechaes,p.hora horaes from partes p where p.id='".$id."' and p.categoria_id is null;";
		$item= $wpdb->get_row($sql,OBJECT);
		$xml = '<items>'."\n";
		$xml .= '	<item type="input"      name="volanta"   label="Volanta:"      labelWidth="150"              labelAlign="center"  inputWidth="150"      offsetLeft="20"      offsetTop="10"    value="'.html_entity_decode($item->volanta).'" />'."\n";
		$xml .= '	<item type="input"      name="titulo"    label="Titulo:"      validate="NotEmpty"           labelWidth="150"     labelAlign="center"  inputWidth="150"     required="true"   offsetLeft="20"                offsetTop="10"         value="'.html_entity_decode($item->titulo).'" />'."\n";
		$xml .= '	<item type="input"      name="bajada"    label="Bajada:"      labelWidth="150"              labelAlign="center"  inputWidth="150"     offsetLeft="20"      offsetTop="10"    value="'.html_entity_decode($item->bajada).'"/>'."\n";
		$xml .= '	<item type="calendar"   name="fecha"     label="Fecha:"       dateFormat="%d-%m-%Y"         labelWidth="150"     labelAlign="center"  inputWidth="150"      required="true"   serverDateFormat="%Y-%m-%d" 	offsetLeft="20"        offsetTop="10"           value="'.$item->fechaes.'" />'."\n";
		$xml .= '	<item type="input"      name="hora"      label="Hora:"        validate="ValidTime"          labelWidth="150"     labelAlign="center"  inputWidth="150"     required="true"   offsetLeft="20"                offsetTop="20"         position="label-left"    value="'.$item->horaes.'" />'."\n";
		$xml .= '	<item type="editor"     name="cabeza"    label="Encabezado:"  validate="NotEmpty"           labelWidth="150"     labelAlign="center"  inputWidth="340"     inputHeight="120" required="true"                offsetLeft="5"         offsetTop="10"           position="label-top" value="'.strip_tags(html_entity_decode($item->cabeza)).'" />'."\n";
		$xml .= '	<item type="editor"     name="cuerpo"    label="Cuerpo:"      labelWidth="150"              labelAlign="center"  inputWidth="340"     inputHeight="120"     offsetLeft="5"    offsetTop="10"                 position="label-top"   value="'.strip_tags(html_entity_decode($item->cuerpo)).'" />'."\n";
		if($exe=='nuevo')
		{
			$xml .= '	<item type="button"    name="aceptar"  value="Aceptar"     width="150" inputLeft="172" inputTop="480" position="absolute"  />'."\n";
			$xml .= '	<item type="button"    name="cancelar" value="Cancelar"    width="150" inputLeft="20" inputTop="480" position="absolute"  />'."\n";
		}
		if($exe=='visualizar')
		{
			$xml .= '	<item type="button"     name="aceptar"   value="Aceptar"      width="150"                   inputLeft="100"      inputTop="480"       position="absolute"  />'."\n";
		}
		if($exe=='modificar')
		{
			$xml .= '	<item type="button"    name="aceptar"  value="Aceptar"     width="150" inputLeft="172" inputTop="480" position="absolute"  />'."\n";
			$xml .= '	<item type="button"    name="cancelar" value="Cancelar"    width="150" inputLeft="20" inputTop="480" position="absolute"  />'."\n";
		}
		$xml .= '</items>';
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo $xml;
	}
	else
	{
		wp_redirect(APP_URL.'/escritorio/index.php');
	}
