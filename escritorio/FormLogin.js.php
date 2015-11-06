<?php
	# Inicio de session.
	session_start();
	
	# Requerir instacia de Wordpress.
	require_once './wp-config.php';
	
	# Requerir funciones de administración.
	require_once APP_PATH.'/escritorio/admin_functions.php';
	
?>
<script>
	var windows = new dhtmlXWindows();
	windows.setImagePath('<?= APP_URL ?>/imgs/');

	var WinLogin = windows.createWindow('WinLogin', 0, 0, 350, 140);
	var str = [
		{ type:"input" , name:"user", label:"Ingresar CUIL o EMAIL:", validate:"NotEmpty", labelWidth:150, inputWidth:120, required:true, offsetLeft:"15"  },
		{ type:"password" , name:"pass", label:"Ingresar PASSWORD:", validate:"ValidAplhaNumeric", labelWidth:150, inputWidth:120, required:true, offsetLeft:"15"  },
		{ type:"button" , name:"ingresar", value:"Ingresar", width:"120", inputLeft:80, inputTop:55, position:"absolute"  }
	];

	var FormLogin = WinLogin.attachForm(str);
	FormLogin.enableLiveValidation(true);

	WinLogin.setText('Ingrese sus datos');
	WinLogin.denyResize();
	WinLogin.denyMove();
	WinLogin.centerOnScreen();
	WinLogin.button('park').hide();
	WinLogin.button('minmax1').hide();
	WinLogin.attachEvent("onClose", function(name){
        window.location.reload();
    });
	
    FormLogin.attachEvent('onButtonClick',function(name){
        if(FormLogin.validate())
        {    
        	user = FormLogin.getItemValue('user');
        	pass = FormLogin.getItemValue('pass');
        	url='<?= APP_URL ?>/escritorio/FormLogin.act.php';
        	prm='exe=select&user='+user+'&pass='+pass;
        	dhtmlx.message('Aguarde unos segundos...');
        	dhtmlx.message('Enviando datos al servidor...');
        	WinLogin.hide();
        	MDI.progressOn();
        	dhtmlxAjax.post(url,prm,function(rta){
        		MDI.progressOff();
            	if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'Error',text:'Los datos que envio no son correctos',type:'alert-error',ok:'Aceptar',callback:function(){WinLogin.show();}});
            	if(rta.xmlDoc.responseText=='true') location.href='<?= APP_URL ?>/escritorio/FormMdi.js.php';
        	});
        }
        else
        {
            dhtmlx.alert({title:'Cuidado',text:'Debe completar los campos en forma correcta.',type:'alert-warning',ok:'Aceptar'});
        }
    });
</script>