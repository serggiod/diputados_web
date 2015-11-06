<?php
	# Inicio de session.
	session_start();

	# Cambiar directorio.
	# chdir('../');

	# Requerir instacia de Wordpress.
	require_once '/home/sdominguez/Desarrollo_Web/diputados_web/public_html/wp-config.php';

	# Requerir funciones de administración.
if(is_user_logged_in() && current_user_can('administrator')):
?>
<script>
	function FormPassword()
	{
		var windows = new dhtmlXWindows();

		var WinFormPassword = windows.createWindow('WinFormPassword', 0, 0, 390, 150);
		var str = [
			{ type:"password" , name:"pass", label:"Ingrese PASSWORD:",      validate:"ValidAplhaNumeric", labelWidth:150, inputWidth:150, required:true, offsetLeft:"20"  },
			{ type:"password" , name:"repass", label:"Ingrese RE-PASSWORD:", validate:"ValidAplhaNumeric", labelWidth:150, inputWidth:150, required:true, offsetLeft:"20"  },
			{ type:"button" , name:"aceptar",  value:"Aceptar",  width:"150", inputLeft:20, inputTop:60, position:"absolute"  },
			{ type:"button" , name:"cancelar", value:"Cancelar", width:"150", inputLeft:170, inputTop:60, position:"absolute"  }
		];

		var FormPassword = WinFormPassword.attachForm(str);

		WinFormPassword.setIcon('<?= APP_URL ?>/imgs/password.png');
		WinFormPassword.setText('Cambiar el password');
		WinFormPassword.denyMove();
		WinFormPassword.centerOnScreen();
		WinFormPassword.button('park').hide();
		WinFormPassword.button('minmax1').hide();
		WinFormPassword.button('dock').hide();

		// Acciones de botón.
		FormPassword.attachEvent("onButtonClick",function(name)
		{
			if(name=='cancelar') WinFormPassword.close();
			if(name=='aceptar')
		    {
					if(FormPassword.validate())
					{
				    	pass   = FormPassword.getItemValue('pass');
				    	repass = FormPassword.getItemValue('repass');
				    	if(pass==repass)
				    	{
					    	dhtmlx.message('Aguarde unos segundos...');
					    	dhtmlx.message('Enviando datos al servidor...');
					    	WinFormPassword.hide();
				    		MDI.progressOn();
				    		url  = '<?= APP_URL ?>/escritorio/FormPassword.act.php';
				    		prm  = 'exe=update&pass='+pass;
				    		dhtmlxAjax.post(url,prm,function(rta){
				    			MDI.progressOff();
						    	if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'Error...',text:'Los datos que envi&oacute; son incorrectos.',type:'alert-error',callback:function(){WinFormPassword.show();}});
						    	if(rta.xmlDoc.responseText=='true')  dhtmlx.alert({
							    	title:'Correcto...',
							    	text:'El password se ha modificado correctamente.',
							    	type:'alert'
							    });
					    	});
				    	}
				    	else
				    	{
					    	dhtmlx.alert({title:'Error...',text:'Los campos Password y Re-Password deben ser iguales.',type:'alert-error'});
				    	}
					}
					else
					{
						dhtmlx.alert({title:'Cuidado',text:'Debe completar el formulario en forma correcta.',type:'alert-warning'});
					}
			}
		});
	}
</script>
<?php
else:
	wp_redirect(APP_URL.'/escritorio/index.php');
endif;
?>