<?php
	# Inicio de session.
	session_start();

	# Cambiar directorio.
	# chdir('../');

	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
?>
<script>
	function FormAbout()
	{
		//dhtmlx.alert({title:'Acerca de...',text:'<img src=../imgs/acerca_de.jpg width=100% />',type:'alert'});
		/* Definir window. */
		var windows = new dhtmlXWindows();
		
		/* DEFINIR LA VENTANA*/
		var win = windows.createWindow('Win', 0, 0, 500, 350);
		win.setText('Acerca de...');
		win.denyResize();
		win.denyMove();
		win.setModal(1);
		win.centerOnScreen();
		win.button('park').hide();
		win.button('minmax1').hide();
		win.button('minmax2').hide();
		win.attachURL("<?= APP_URL ?>/escritorio/about_content.html");
	}
</script>