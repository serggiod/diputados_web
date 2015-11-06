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
	function FormManual()
	{
		/* Definir window. */
		var windows = new dhtmlXWindows();
		windows.setImagePath('<?= APP_URL ?>/imgs/');
		
		/* DEFINIR LA VENTANA*/
		var win = windows.createWindow('Win', 0, 0, 800, 500);
		win.setText('Manual de Usuario');
		win.denyResize();
		win.denyMove();
		win.setModal(1);
		win.centerOnScreen();
		win.button('park').hide();
		win.button('minmax1').hide();
		win.button('minmax2').hide();
		win.attachURL("<?= APP_URL ?>/docs/index.html");
	}
</script>