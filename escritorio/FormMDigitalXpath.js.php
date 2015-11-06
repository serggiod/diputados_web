<?php
	# Inicio de session.
	session_start();
	
	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator')):
	
		global $wpdb;
		$cuil= $_SESSION['autorizado'];
		$tr = $wpdb->get_var("select count(*) from m_digital_xpath");
		$tp = $tr / 15;
		if(fmod($tr,15)) $tp = intval($tr / 15) +1;
	?>
		<script>

		/* DEFINIR TOOLBAR */
		var Toolbar = MDIA.attachToolbar();

		Toolbar.setIconsPath('<?= APP_URL ?>/imgs/');
		Toolbar.loadXMLString('<toolbar><item type="button" id="BNuevo" text="Nuevo" img="partenuevo.png" imgdis="partenuevo.png" /><item type="button" id="BVisualizar" text="Visualizar" img="partevisualizar.png" imgdis="partevisualizar.png" /><item type="button" id="BModificar" text="Modificar" img="partemodificar.png" imgdis="partemodificar.png" /><item type="button" id="BEliminar" text="Eliminar" img="parteeliminar.png" imgdis="parteseliminar.png" /><item type="separator" id="separado1" /><item type="button" id="P" text="" img="primero.png" imgdis="primero.png" /><item type="button" id="A" text="" img="anterior.png" imgdis="anterior.png" /><item type="text" id="C" text="Visualizando página 1 de <?= $tp ?> (15 de <?= $tr ?> registros)" /><item type="button" id="S" text="" img="siguiente.png" imgdis="siguiente.png" /><item type="button" id="U" text="" img="ultimo.png" imgdis="ultimo.png" /><item type="separator" id="separador2" /><item type="text" id="B" text="Buscar:" /><item type="buttonInput" id="STR" width="100" value="Esta palabra..." /><item type="text" id="B" text=" en " /><item type="buttonSelect" id="EN" text="el campo" title="" /><item type="button" id="BB" text="" img="buscar.png" imgdis="buscar.png" /><item type="button" id="BC" text="" img="nofiltrar.png" imgdis="nofiltrar.png" /></toolbar>', function(){});

		Toolbar.addListOption('EN','domain',1,'button','DOMINIO','encampo.png');
		Toolbar.addListOption('EN','xpath',2,'button','XPATH','encampo.png');
		Toolbar.addListOption('EN','role',3,'button','ROL','encampo.png');

		Toolbar.attachEvent('onClick',function(name){
			if(name=='BNuevo') FormNuevo();
			if(name=='BVisualizar') FormVisualizar();
			if(name=='BModificar') FormModificar();
			if(name=='BEliminar') FormEliminar();
			if(name=='P') FormP(GRID);
			if(name=='A') FormA(GRID);
			if(name=='S') FormS(GRID);
			if(name=='U') FormU(GRID);
			if(name=='BB') FormBB(GRID);
			if(name=='BC') FormBC(GRID);
		});

		/* DEFINICION DE GRID */
		var GRID = MDIA.attachGrid();
		GRID.setIconsPath('<?= APP_URL ?>/imgs/');
		GRID.setHeader(['DOMINIO','XPATH','ROL']);
		GRID.setInitWidths('*,*,*');
		GRID.setColTypes('ro,ro,ro');
		GRID.setColSorting('str,str,str');;
		GRID.setColAlign('center,center,center');
		GRID.enableResizing('true,true,true');
		GRID.enableTooltips('false,false,false');
		GRID.init();
		GRID.load('<?= APP_URL ?>/escritorio/FormMDigitalXpath.xml.php');

		var windows = new dhtmlXWindows();

		/* FUNCION PARA RECARGAR LA GRID */
		function resetGRID(){MDI.progressOn();GRID.clearAll();GRID.load('<?= APP_URL ?>/escritorio/FormMDigitalXpath.xml.php',function(){dhtmlx.message('Datos actualizados correctamente...');MDI.progressOff();});}

	
		/* FORM NUEVO */
		function FormNuevo()
		{
			/* DEFINIR LA VENTANA*/
			var Win = windows.createWindow('Win', 0, 0, 800, 500);
			Win.setText('Nuevo');
			Win.denyResize();
			Win.denyMove();
			Win.setModal(1);
			Win.centerOnScreen();
			Win.button('park').hide();
			Win.button('minmax1').hide();
			Win.button('minmax2').hide();

			/* DEFINIMOS BARRA TAB */
			var Tabbar = Win.attachTabbar();
			Tabbar.setImagePath("<?= APP_URL ?>/imgs/");
			Tabbar.addTab('1','Domain/Xpath');
			Tabbar.setTabActive('1');

			/* DEFINIR FDomainX */
			var FDomainX = Tabbar.cells('1').attachForm();
			items = 
			[
				{type:'input', name:'domain', label:'Dominio:', labelWidth:'120', inputWidth:'600', required:true},
				{type:'input', name:'xpath', label:'Xpath:', labelWidth:'120', inputWidth:'600', required:true},
				{type:'select', name:'role', label:'Rol:', labelWidth:'120', inputWidth:'600', required:true, options:[{text: "Volanta", value: "volanta"},{text: "Titulo", value: "titulo"},{text: "Bajada", value: "bajada"},{text: "Resumen", value: "resumen"},{text: "Contenido", value: "contenido"}]},
				{type:'button', name:'submit', value:'Finalizar', width:'200'},
				{type:'button',	name:'cancel', value:'Cancelar', width:'200'}
			];
			FDomainX.loadStruct(items,'json');

			/* EVENTOS DE FormG	*/
			FDomainX.attachEvent('onButtonClick',function(btn){

				/* CANCELAR */
				if(btn=='cancel'){resetGRID();Win.close();}

				/* SIGUIENTE */
				if(btn=='submit')
				{
					if(FDomainX.validate())
					{
						dhtmlx.message('Aguarde unos segundos...');
						dhtmlx.message('Enviando Datos al servidor...');
						MDI.progressOn();
						url			= '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php';
						domain		= FDomainX.getItemValue('domain');
						role		= FDomainX.getItemValue('role');
						xpath		= FDomainX.getItemValue('xpath');
						prm			= 'exe=insertarDomainX&domain='+domain+'&xpath='+xpath+'&role='+role;
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'Error...',text:'Se detect&oacute; un error al ingresar los datos.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true')
							{
								resetGRID();
								Win.close();
							} 
						});
					}
					else
					{
						dhtmlx.alert({title:'Cuidado...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});
					}
				}
			});
		}



		/*
		FORM VISUALIZAR
		_______________
		*/
		function FormVisualizar()
		{
			id = GRID.getSelectedId();
			if(id==null)
			{
				dhtmlx.alert({title:'CUIDADO...',text:'Primero debe seleccionar un registro para realizar esta operaci&oacute;n.',type:'alert-warning',ok:'Aceptar'});
			}
			else
			{
				/* MENSAJES */
				dhtmlx.message('Aguarde unos segundos...');
				dhtmlx.message('Solicitando datos al servidor...');
				MDI.progressOn();
				
				/* DEFINIMOS LA VENTANA*/
				var Win = windows.createWindow('Win', 0, 0, 800, 500);
				Win.setText('Visualizar');
				Win.denyResize();
				Win.denyMove();
				Win.setModal(1);
				Win.centerOnScreen();
				Win.button('park').hide();
				Win.button('minmax1').hide();
				Win.button('minmax2').hide();

				/* DEFINIMOS LA BARRA TAB */
				var Tabbar = Win.attachTabbar();
				Tabbar.setImagePath("<?= APP_URL ?>/imgs/");
				Tabbar.addTab('1','Domain/Xpath');
				Tabbar.setTabActive('1');

				/* EVENTOS PARA FVisualizarX */
				var FVisualizarX = Tabbar.cells('1').attachForm();
				
				/* DEFINIR FVisualizarX */
				items = 
				[
					{type:'input',name:'domain',label:'Dominio:',labelWidth:'120',inputWidth:'600',required:true,readonly:true},
					{type:'input',name:'xpath',label:'Xpath:',labelWidth:'120',inputWidth:'600',required:true,readonly:true},
					{type:'select', name:'role', label:'Rol:', labelWidth:'120', inputWidth:'600', required:true, options:[{text: "Volanta", value: "volanta"},{text: "Titulo", value: "titulo"},{text: "Bajada", value: "bajada"},{text: "Resumen", value: "resumen"},{text: "Contenido", value: "contenido"}]},
					{type:'button',name:'submit',value:'Finalizar',width:'200'}
				];
				FVisualizarX.loadStruct(items,'json');
				FVisualizarX.attachEvent('onButtonClick',function(btn){
					if(btn=='submit')
					{
						resetGRID();
						Win.close();
					}
				});

				/* CARGAR DATOS en FVisualizarX DESDE AJAX */
				dhtmlxAjax.post(
					'<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php',
					'exe=getDomainX&id='+id,
					function(rta)
					{
						$dato = rta.xmlDoc.responseText.split('{%SPLIT%}');
						FVisualizarX.setItemValue('domain',$dato[0]);
						FVisualizarX.setItemValue('xpath',$dato[1]);
						FVisualizarX.setItemValue('role',$dato[2]);
						MDI.progressOff();
					}
				);
			}
		}


		/*
		FORM MODIFICAR
		______________
		*/
		function FormModificar()
		{
			id = GRID.getSelectedId();
			if(id==null)
			{
				dhtmlx.alert({title:'CUIDADO...',text:'Primero debe seleccionar un registro para realizar esta operaci&oacute;n.',type:'alert-warning',ok:'Aceptar'});
			}
			else
			{
				/* MENSAJES */
				dhtmlx.message('Aguarde unos segundos...');
				dhtmlx.message('Solicitando datos al servidor...');
				MDI.progressOn();
				
				/* DEFINIMOS LA VENTANA*/
				var Win = windows.createWindow('Win', 0, 0, 800, 500);
				Win.setText('Modificar');
				Win.denyResize();
				Win.denyMove();
				Win.setModal(1);
				Win.centerOnScreen();
				Win.button('park').hide();
				Win.button('minmax1').hide();
				Win.button('minmax2').hide();

				/* DEFINIMOS LA BARRA TAB */
				var Tabbar = Win.attachTabbar();
				Tabbar.setImagePath("<?= APP_URL ?>/imgs/");
				Tabbar.addTab('1','Domain/Xpath');
				Tabbar.setTabActive('1');

				/* DEFINIR FModificarX */
				var FModificarX = Tabbar.cells('1').attachForm();
				items = 
				[
					{type:'input',name:'domain',label:'Dominio:',labelWidth:'120',inputWidth:'600',required:true},
					{type:'input',name:'xpath',label:'Xpath:',labelWidth:'120',inputWidth:'600',required:true},
					{type:'select', name:'role', label:'Rol:', labelWidth:'120', inputWidth:'600', required:true, options:[{text: "Volanta", value: "volanta"},{text: "Titulo", value: "titulo"},{text: "Bajada", value: "bajada"},{text: "Resumen", value: "resumen"},{text: "Contenido", value: "contenido"}]},
					{type:'button',name:'submit',value:'Actualizar',width:'200'},
					{type:'button',name:'cancel',value:'Finalizar',width:'200'}
				];
				FModificarX.loadStruct(items,'json');

				/* CARGAR DATOS en FModificarX DESDE AJAX */
				dhtmlxAjax.post(
					'<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php',
					'exe=getDomainX&id='+id,
					function(rta)
					{
						$dato = rta.xmlDoc.responseText.split('{%SPLIT%}');
						FModificarX.setItemValue('domain',$dato[0]);
						FModificarX.setItemValue('xpath',$dato[1]);
						FModificarX.setItemValue('role',$dato[2]);
						MDI.progressOff();
					}
				);
				
				/* EVENTOS */
				FModificarX.attachEvent('onButtonClick',function(btn){

					/* EVENTO: cancel */
					if(btn=='cancel'){resetGRID();Win.close();}

					/* EVENTO: submit*/
					if(btn=='submit')
					{
						if(FModificarX.validate())
						{
							dhtmlx.message('Aguarde unos segundos...');
							dhtmlx.message('Enviando datos al servidor...');
							MDI.progressOn();
							url			= '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php';
							domain		= FModificarX.getItemValue('domain');
							xpath		= FModificarX.getItemValue('xpath');
							role		= FModificarX.getItemValue('role');
							prm			= 'exe=updateDomainX&domain='+domain+'&xpath='+xpath+'&id='+id+'&role='+role;
							dhtmlxAjax.post(url,prm,function(rta){
								MDI.progressOff();
								if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERROR...',text:'Se detect&oacute; un error al actualizar los datos en el servidor.',type:'alert-error',ok:'Aceptar'});}
								if(rta.xmlDoc.responseText=='true'){resetGRID();dhtmlx.alert({title:'Correcto...',text:'Los datos se guardaron en forma correcta.',type:'alert',ok:'Aceptar'});resetGRID;Win.close();}
							});
						}
						else
						{dhtmlx.alert({title:'CUIDADO...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});}
					}
				});
			}
		}


		
		/*
		FORM ELIMINAR
		______________
		*/
		function FormEliminar()
		{
			id = GRID.getSelectedId();
			if(id==null){dhtmlx.alert({title:'CUIDADO...',text:'Primero debe seleccionar un registro para realizar esta operaci&oacute;n.',type:'alert-warning',ok:'Aceptar'});}
			else
			{
				dhtmlx.confirm({title:'CONFIRMAR...',text:'¿Esta seguro que desea eliminar este registro?',type:'alert',ok:'Aceptar',cancel:'Cancelar',callback:
					function(x)
					{
						if(x)
						{
							dhtmlx.message('Aguarde unos segundos...');
							dhtmlx.message('Enviando datos al servidor...');
							MDI.progressOn();
							url = '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php';
							prm = 'exe=deleteDomainX&id='+id;
							dhtmlxAjax.post(url,prm,function(rta){
								if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERROR...',text:'Se detecto un error al eliminar el registro.',type:'alert-error',ok:'Aceptar'});}
								if(rta.xmlDoc.responseText=='true')
								{
									resetGRID();
									C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=getC').xmlDoc.responseText;
									Toolbar.setItemText('C',C);
									dhtmlx.alert({title:'CORRECTO...',text:'Se ha eliminado en forma correcta un registro.',type:'alert',ok:'Aceptar'});
								}
								MDI.progressOff();
							});
						}
					}
				});
			}
		}


		
		/*
		FORM FormP
		__________
		*/
		function FormP()
		{
			url = '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=FormP';
			if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
			{
				resetGRID();	
				C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=getC').xmlDoc.responseText;
				Toolbar.setItemText('C',C);
			}
		}

		
		/*
		FORM FormA
		__________
		*/
		function FormA()
		{
			url = '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=FormA';
			if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
			{
				resetGRID();	
				C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=getC').xmlDoc.responseText;
				Toolbar.setItemText('C',C);
			}
		}


		/*
		FORM FormS
		__________
		*/
		function FormS()
		{
			url = '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=FormS';
			if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
			{
				resetGRID();	
				C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=getC').xmlDoc.responseText;
				Toolbar.setItemText('C',C);
			}
		}

				
		/*
		FORM FormU
		__________
		*/		
		function FormU()
		{
			url = '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=FormU';
			if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
			{
				resetGRID();	
				C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=getC').xmlDoc.responseText;
				Toolbar.setItemText('C',C);
			}
		}


				
		/*
		FORM FormBB
		___________
		*/
		function FormBB()
		{
			str = Toolbar.getValue('STR');
			if(str=='Esta palabra...')
			{
				dhtmlx.alert({title:'CUIDADO...',text:'Primero debe ingresar una palabra para buscar',type:'alert-warning',ok:'Aceptar'});
			}
			else
			{
				campo = Toolbar.getListOptionSelected('EN');
				if(campo==null)
				{
					dhtmlx.alert({title:'CUIDADO...',text:'También debe indicar en que campo se realizar&aacute; la b&uacute;squeda',type:'alert-warning',ok:'Aceptar'});
				}
				else
				{
					url = '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=FormP';
					if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
					{	
						dhtmlx.message('Enviando busqueda al servidor...');
						MDI.progressOn();
						url = '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php';
						prm = 'exe=FormBB&str='+str+'&campo='+campo;
						dhtmlxAjax.post(url,prm,function(rta){
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detector un error en la busqueda.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true')
							{
								resetGRID();
								C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=getC').xmlDoc.responseText;
								Toolbar.setItemText('C',C);
							};
							MDI.progressOff();
						});
					}
				}
			}
		}


		/*
		FORM FormBC
		___________
		*/
		function FormBC(GRID)
		{
			dhtmlx.confirm({title:'CONFIRMAR...',text:'Esta seguro que desea limpiar los filtros de busqueda',type:'alert',ok:'Aceptar',cancel:'Cancelar',callback:
				function(x)
				{
					if(x)
					{
						dhtmlx.message('Limpiando filtros en el servidor...');
						MDI.progressOn();
						url = '<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php';
						prm = 'exe=FormBC';
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detector un error al limpiar los filtros de busqueda.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true')
							{
								resetGRID();
								C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormMDigitalXpath.act.php?exe=getC').xmlDoc.responseText;
								Toolbar.setItemText('C',C);
								Toolbar.setValue('STR','Esta palabra...');
							}
							MDI.progressOff();
						});
					}
				}
			});
		}

		</script>
	<?
	else:
		wp_redirect(APP_URL.'/escritorio/index.php');
	endif;
	?>
