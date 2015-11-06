<?php
	# Inicio de session.
	session_start();

	# Cambiar directorio.
	chdir('../');

	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
	require_once APP_PATH.'/escritorio/admin_functions.php';

	header ('Content-Type: text/xml');
	echo '<?xml version="1.0"?>';
?>
<menu>
	<item id="archivo" text="Archivo" img="archivo.png" imgdis="archivo.png">
		<item id="formlogout" text="Salir del Sistema" img="logout.png" imgdis="logout.png"/>
		<item id="formpassword" text="Cambiar el Password" img="password.png" imgdis="password.png"/>
<?= require_file_contents(APP_PATH.'/escritorio','menu.archivo.php'); ?>
    </item>
    <item id="herramientas" text="Herramientas" img="herramientas.png" imgdis="herramientas.png">
<?= require_file_contents(APP_PATH.'/escritorio','.menu.herramientas.php'); ?>
    </item>
    <item id="ayuda" text="Ayuda" img="ayuda.png" imgdis="ayuda.png">
		<item id="formabout" text="Acerca de ..." img="about.png" imgdis="about.png"/>
		<item id="formmanual" text="Manual En Linea" img="manual.png" imgdis="manual.png"/>
<?= require_file_contents(APP_PATH.'/escritorio','menu.ayuda.php'); ?>
    </item>
</menu>
