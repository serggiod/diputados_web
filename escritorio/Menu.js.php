<?php
	# Inicio de session.
	session_start();

	# Cambiar directorio.
	//chdir('../');

	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
	require_once APP_PATH.'/escritorio/admin_functions.php';

?>
<script>
	var Menu = MDIA.attachMenu();
	Menu.setIconsPath('<?= APP_URL ?>/imgs/');
	Menu.loadXML('<?= APP_URL ?>/escritorio/Menu.xml.php');
	Menu.attachEvent("onClick", function(name){
		dhtmlx.message('Aguarde unos segundos...');
		dhtmlx.message('Solicitando datos al servidor...');
	<? foreach(scandir(APP_PATH.'/escritorio') as $file): ?>
		<? if(strpos($file,'.menu.')): ?>
			<? $str    = explode('.',$file); ?>
			<? $name   = strtolower($str[0]); ?>
			<? $action = 'window.location.href="'.APP_URL.'/escritorio/FormMdi.js.php?jsfile='.$str[0].'"';?>
			if(name=='<?= $name ?>') { MDI.progressOn(); <?= $action ?>;}
		<? endif; ?>
	<? endforeach; ?>
			if(name=='formlogout') FormLogout();
			if(name=='formpassword') FormPassword();
			if(name=='formabout') FormAbout();
			if(name=='formmanual') FormManual(); 
	});
</script>