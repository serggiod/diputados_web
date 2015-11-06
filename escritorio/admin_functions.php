<?php

	function admin_header()
	{
		global $wpdb;
		$html = null;
		$html .= '<!DOCTYPE html>'."\n";
		$html .= '<html lang="es">'."\n";
		$html .= '<head>'."\n";
		$html .= '	<title>DIPUTADOS WEB | Jujuy</title>'."\n";
		$html .= '	<meta charset="UTF-8"/>'."\n";
		$html .= '	<link rel="stylesheet" href="'.APP_URL.'/dhtmlx.css" type="text/css">'."\n";
		$html .= '	<script src="'.APP_URL.'/dhtmlx.js"></script>'."\n";
		$html .= '	<style>'."\n";
    	$html .= '	html, body {'."\n";
        $html .= '		width: 100%;'."\n";
        $html .= '		height: 100%;'."\n";
        $html .= '		margin: 0px;'."\n";
        $html .= '		padding: 0px;'."\n";
        $html .= '		overflow: hidden;'."\n";
        $html .= '		background-color: #333333;'."\n";
    	$html .= '	}'."\n";
    	$html .= ' #loading{border:0;position:absolute;top:50%;left:50%;margin-top:-25px;margin-left:-121px;}';
		$html .= '	</style>'."\n";
		$html .= '</head>'."\n";
		$html .= '<body id="body" class="body"><img id="loading" src="loading.gif"/>'."\n";
		$html .= '	<script>'."\n";
		$html .= '		dhtmlx.image_path="'.APP_URL.'/imgs/"'."\n";
		$html .= '		var MDI = new dhtmlXLayoutObject(document.body,"1C");'."\n";
		$html .= '		var MDIA = MDI.cells("a");'."\n";
		$html .= '		MDIA.setText("BIENVENIDO '.$_SESSION['nombre'].' '.$_SESSION['apellido'].' '.$_SESSION['cuil'].'");'."\n";
		$html .= '	</script>'."\n";
		return $html;
	}

	function admin_footer()
	{
		global $wpdb;
		$html = null;
		$html .= '</body>'."\n";
		$html .= '</html>'."\n";
		return $html;
		
	}
	function require_file_contents($path,$string)
	{
		$xml = null;
		$dir = scandir($path);
		foreach ($dir as $file)
		{
			if(strpos($file,$string))
			{
				if($file=='FormImportarUsers.menu.herramientas.php')
				{
					if($_SESSION['super']=='true')
					{
						$xml .= file_get_contents(APP_PATH.'/escritorio/'.$file)."\n";
					}
				}
				else if($file=='FormMDigitalXpath.menu.herramientas.php')
				{
					if($_SESSION['super']=='true')
					{
						$xml .= file_get_contents(APP_PATH.'/escritorio/'.$file)."\n";
					}
				}
				else
				{
					$xml .= file_get_contents(APP_PATH.'/escritorio/'.$file)."\n";
				}
			}
		}
		return $xml;
	}
