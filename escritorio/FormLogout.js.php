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
	function FormLogout()
	{
		dhtmlx.confirm({
			title: 'Salir del Sitema',
			text:  '¿Esta seguro que desea salor del sistema?',
			type:  'alert-warning',
			ok:    'Aceptar',
			cancel:'Cancelar',
			callback:function(t)
					{
						if(t)
						{
							dhtmlx.message('Saliendo del sistema...');
							dhtmlx.message('Eliminando datos de sesión...');
							MDI.progressOn();
							window.location='<?= APP_URL ?>/escritorio/index.php';
						}
					}
		});
	}
</script>