<?php
	# Inicio de session.
	session_start();

	# Requerir instacia de Wordpress.
	require_once '/home/sdominguez/Desarrollo_Web/diputados_web/public_html/wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator')):
	?>
	<script>
		var ToolBarImportarUsers = MDIA.attachToolbar();
		ToolBarImportarUsers.setIconsPath('<?= APP_URL ?>/imgs/');

		ToolBarImportarUsers.loadXMLString('<toolbar><item type="button" id="BInportarUsersUno" text="Importar un usuario desde SILEJU" img="importar1.png" imgdis="importar1.png" /><item type="button" id="BImportarUsersTodos" text="Importar a todos los usuarios de SILEJU" img="importar2.png" imgdis="importar2.png" /><item type="button" id="BImportarUsersEliminar" text="Eliminar un usuario" img="eliminarimportado.png" imgdis="eliminarimportado.png" /><item type="button" id="BImportarUsersEliminarTodos" text="Eliminar a todos los usuarios" img="eliminarimportado.png" imgdis="eliminarimportado.png" /><item type="separator" id="separado1" /><item type="button" id="P" text="" img="primero.png" imgdis="primero.png" /><item type="button" id="A" text="" img="anterior.png" imgdis="anterior.png" /><item type="text" id="C" text="10/120" /><item type="button" id="S" text="" img="siguiente.png" imgdis="siguiente.png" /><item type="button" id="U" text="" img="ultimo.png" imgdis="ultimo.png" /><item type="separator" id="separador2" /><item type="text" id="B" text="Buscar:" /><item type="buttonInput" id="Bus" width="100" value="" /><item type="button" id="BB" text="" img="buscar.png" imgdis="buscar.png" /></toolbar>', function(){});

		var GRIDImportarUsers = MDIA.attachGrid();
		GRIDImportarUsers.setIconsPath('<?= APP_URL ?>/imgs/');
		
		GRIDImportarUsers.setHeader(["CUIL","APELLIDO","NOMBRE","DOC","NRO","EMAIL","SUPER"]);
		GRIDImportarUsers.setColTypes("rotxt,rotxt,rotxt,rotxt,ro,rotxt,ro");
		
		GRIDImportarUsers.setColAlign('left,center,center,center,center,center,center');
		GRIDImportarUsers.enableResizing('false,true,true,true,false,true,false');
		GRIDImportarUsers.enableTooltips('false,false,false,false,false,false,false');
		GRIDImportarUsers.setColSorting('str,str,str,str,int,str,str');
		GRIDImportarUsers.setColWidth(0, '100');
		GRIDImportarUsers.setColWidth(3, '40');
		GRIDImportarUsers.setColWidth(4, '80');
		GRIDImportarUsers.setColWidth(6, '40');

		GRIDImportarUsers.init();
		GRIDImportarUsers.load('<?= APP_URL ?>/escritorio/FormImportarUsers.xml.php', 'xml');

		ToolBarImportarUsers.attachEvent('onClick',function(name){
			if(name=='BInportarUsersUno') FormImportarUsersUno(GRIDImportarUsers);
			if(name=='BImportarUsersTodos') FormImportarUsersTodos(GRIDImportarUsers);
			if(name=='BImportarUsersEliminar') FormImportarUsersEliminar(GRIDImportarUsers);
			if(name=='BImportarUsersEliminarTodos') FormImportarUsersEliminarTodos(GRIDImportarUsers);
			if(name=='P') FormImportarUsersP(GRIDImportarUsers);
			if(name=='A') FormImportarUsersA(GRIDImportarUsers);
			if(name=='S') FormImportarUsersS(GRIDImportarUsers);
			if(name=='U') FormImportarUsersU(GRIDImportarUsers);
			if(name=='BB') FormImportarUsersBB(GRIDImportarUsers);
		});
	

		function FormImportarUsersUno(GRID)
		{
			<? $sql = "select concat(p.per_apellidos,' ',p.per_nombres) nombre, p.per_cuil cuil from autorizados a inner join personas p on p.per_cuil=a.cuil_persona order by p.per_apellidos asc"; ?>
			var windows = new dhtmlXWindows();
			var WinFomImportarUserUno = windows.createWindow('WinFomImportarUserUno', 0, 0, 380, 170);
			var str = [
				{ type:"select" , name:"FormIUUsuarios", label:"Seleccione un Usuario:", labelWidth:150, inputWidth:140, required:true, offsetLeft:"20", offsetTop:"20", options:[{text:'Seleccione un usuario...',value:'nulo'}<? foreach($wpdb->get_results($sql,OBJECT) as $item): ?>,{text:'<?= $item->nombre ?>',value:'<?= $item->cuil ?>'}<? endforeach; ?>] },
				{ type:"button" , name:"FormIUSubmit", value:"Importar el Usuario", width:"150", inputLeft:170, inputTop:60, position:"absolute"  }
			];
			var FormImportarUserUno = WinFomImportarUserUno.attachForm(str);
			WinFomImportarUserUno.setIcon('<?= APP_URL ?>/imgs/importar1.png');
			WinFomImportarUserUno.setText('Importar un usuario');
			WinFomImportarUserUno.denyResize();
			WinFomImportarUserUno.denyMove();
			WinFomImportarUserUno.setModal(1);
			WinFomImportarUserUno.centerOnScreen();
			WinFomImportarUserUno.button('park').hide();
			WinFomImportarUserUno.button('minmax1').hide();
			
			FormImportarUserUno.attachEvent('onButtonClick',function(name){
				if(name=='FormIUSubmit')
				{
					cuil = FormImportarUserUno.getItemValue('FormIUUsuarios');
					if(cuil=='nulo')
					{
						dhtmlx.alert({title:'Cuidado...',text:'Primero debe seleccionar un usuario.',type:'alert-warning',ok:'Aceptar'});
					}
					else
					{
						dhtmlx.message('Espere un momento que se est&aacute;n importando un usuario.');
						MDIA.progressOn();
						url  = '<?= APP_URL ?>/escritorio/FormImportarUsers.act.php';
						prm  = 'exe=importaruno&cuil='+cuil;
						dhtmlxAjax.post(url,prm,function(rta){
							MDIA.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'Error...',text:'Se egistro un error al importar el usuario.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='exists') dhtmlx.alert({title:'Cuidado...',text:'El usuario ya fue importado anteriormente.',type:'alert-warning',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true') 
							{
								dhtmlx.alert({title:'Correcto...',text:'El usuario fue importado en forma correcta.',type:'alert',ok:'Aceptar',callback:
									function()
									{
										GRID.clearAll();
										GRID.load('<?= APP_URL ?>/escritorio/FormImportarUsers.xml.php', 'xml');
									}
								});
							};
						});
					}
				}
			});
		}

		function FormImportarUsersTodos(GRID)
		{
			dhtmlx.confirm({title:'Confirmar...',text:'Desea importar a todos los usuarios de SILEJU.',type:'alert',ok:'Aceptar',cancel:'Cancelar',callback:
				function(x)
				{
					if(x)
					{
						dhtmlx.message('Espere un momento que se est&aacute;n importando a todos los usuarios.');
						MDIA.progressOn();
						url = '<?= APP_URL ?>/escritorio/FormImportarUsers.act.php';
						prm = 'exe=importartodos';
						dhtmlxAjax.post(url,prm,function(rta){
							MDIA.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'Error...',text:'Se registro un error al importar a los usuarios.',type:'alert-error'});
							if(rta.xmlDoc.responseText=='true') dhtmlx.alert({title:'Correcto...',text:'Los usuarios se han importado en forma correcta.',type:'alert',callback:
								function()
								{
									GRID.clearAll();
									GRID.load('<?= APP_URL ?>/escritorio/FormImportarUsers.xml.php', 'xml');
								}
							});
						});
					}
				}
			});
		}

		function FormImportarUsersEliminar(GRID)
		{
			cuil = GRID.getSelectedId();
			if(cuil==null)
			{
				dhtmlx.alert({title:'Cuidado...',text:'Primero debe seleccionar un usario para realizar esta operaci&oacute;n.',type:'alert-warning',ok:'Aceptar'});
			}
			else
			{
				apellido = GRID.cells(cuil,1).getValue();
				nombre = GRID.cells(cuil,2).getValue();
				dhtmlx.confirm({title:'Confirmar...',text:'¿Esta seguro que desea eliminar al usuario '+apellido+' '+nombre+' ?',callback:
					function(x)
					{
						if(x)
						{
							dhtmlx.message('Se esta eliminando al usuario.');
							MDIA.progressOn();
							url = '<?= APP_URL ?>/escritorio/FormImportarUsers.act.php';
							prm = 'exe=eliminaruno&cuil='+cuil;
							dhtmlxAjax.post(url,prm,function(rta){
								MDIA.progressOff();
								if(rta.xmlDoc.responseText=='false')
								{
									dhtmlx.alert({title:'Error...',text:'Se detect&oacute; un error al tratar de eliminar al usuario.',type:'alert-error',ok:'Aceptar'});
								}
								if(rta.xmlDoc.responseText=='true')
								{
									dhtmlx.alert({title:'Correcto',text:'El usuario se ha eliminado en forma correcta',type:'alert',ok:'Aceptar',callback:
										function(x)
										{
											GRID.clearAll();
											GRID.load('<?= APP_URL ?>/escritorio/FormImportarUsers.xml.php');
										}
									});
								}
							});
						}
					}
				});
			}
		}

		function FormImportarUsersEliminarTodos(GRID)
		{
			dhtmlx.confirm({title:'Confirmar...',text:'¿Esta seguro que desea eliminar a todos los usuarios?',type:'confirm',ok:'Aceptar',cancel:'Cancelar',callback:
				function(x)
				{
					if(x)
					{
						dhtmlx.message('Se estan eliminando a todos los usuarios.');
						MDIA.progressOn();
						url = '<?= APP_URL ?>/escritorio/FormImportarUsers.act.php';
						prm = 'exe=eliminartodos';
						dhtmlxAjax.post(url,prm,
						function(rta)
						{
							MDIA.progressOff();
							if(rta.xmlDoc.responseText=='false')
							{
								dhtmlx.alert({title:'Error...',text:'Se ha detectado un error al aliminar a todos los usuarios.',type:'alert-error',ok:'Aceptar'});
							}
							if(rta.xmlDoc.responseText=='true'){
								dhtmlx.alert({title:'Correcto...',text:'Los usuarios se han eliminado en forma correcta.',type:'alert',ok:'Aceptar',callback:
									function(x)
									{
										GRID.clearAll();
										GRID.load('<?= APP_URL ?>/escritorio/FormImportarUsers.xml.php');
									}
								});
							}
						});
					}
				}
			});
		}
		
		function FormImportarUsersP()
		{
			dhtmlx.alert({title:'Hola...',text:'Desde P.'});
		}

		function FormImportarUsersA()
		{
			dhtmlx.alert({title:'Hola...',text:'Desde A.'});
		}

		function FormImportarUsersS()
		{
			dhtmlx.alert({title:'Hola...',text:'Desde S.'});
		}

		function FormImportarUsersU()
		{
			dhtmlx.alert({title:'Hola...',text:'Desde U.'});
		}
	
		function FormImportarUsersBB()
		{
			dhtmlx.alert({title:'Hola...',text:'Desde BB.'});
		}
		
		</script>
	<?
	else:
		wp_redirect(APP_URL.'/escritorio/index.php');
	endif;
	?>