<?php
	# Inicio de session.
	session_start();
	
	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator')):
	
		global $wpdb;
		$cuil= $_SESSION['autorizado'];
		$tr = $wpdb->get_var("select count(*) from galeria g where g.cuil_dipu='".$cuil."' and g.type='video'");
		$tp = $tr / 15;
		if(fmod($tr,15)) $tp = intval($tr / 15) +1;
	?>
		<script>

		/* DEFINIR TOOLBAR */
		var Toolbar = MDIA.attachToolbar();

		Toolbar.setIconsPath('<?= APP_URL ?>/imgs/');
		Toolbar.loadXMLString('<toolbar><item type="button" id="BNuevo" text="Nuevo" img="partenuevo.png" imgdis="partenuevo.png" /><item type="button" id="BVisualizar" text="Visualizar" img="partevisualizar.png" imgdis="partevisualizar.png" /><item type="button" id="BModificar" text="Modificar" img="partemodificar.png" imgdis="partemodificar.png" /><item type="button" id="BEliminar" text="Eliminar" img="parteeliminar.png" imgdis="parteseliminar.png" /><item type="button" id="BPublicar" text="Publicar/Pendiente" img="partepublicar.png" imgdis="partepublicar.png" /><item type="separator" id="separado1" /><item type="button" id="P" text="" img="primero.png" imgdis="primero.png" /><item type="button" id="A" text="" img="anterior.png" imgdis="anterior.png" /><item type="text" id="C" text="Visualizando página 1 de <?= $tp ?> (<?= $tr ?> registros)" /><item type="button" id="S" text="" img="siguiente.png" imgdis="siguiente.png" /><item type="button" id="U" text="" img="ultimo.png" imgdis="ultimo.png" /><item type="separator" id="separador2" /><item type="text" id="B" text="Buscar:" /><item type="buttonInput" id="STR" width="100" value="Esta palabra..." /><item type="text" id="B" text=" en " /><item type="buttonSelect" id="EN" text="el campo" title="" /><item type="button" id="BB" text="" img="buscar.png" imgdis="buscar.png" /><item type="button" id="BC" text="" img="nofiltrar.png" imgdis="nofiltrar.png" /></toolbar>', function(){});

		Toolbar.addListOption('EN','nombre',1,'button','Nombre','encampo.png');
		Toolbar.addListOption('EN','descripcion',2,'button','Descripci&oacute;n','encampo.png');
		Toolbar.addListOption('EN','fecha',3,'button','Fecha','encampo.png');

		Toolbar.attachEvent('onClick',function(name){
			if(name=='BNuevo') FormNuevo();
			if(name=='BVisualizar') FormVisualizar();
			if(name=='BModificar') FormModificar();
			if(name=='BEliminar') FormEliminar();
			if(name=='BPublicar') FormPublicar();
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
		GRID.setHeader(['NOMBRE','DESCRIPCION','FECHA','HORA','ORDEN','ESTADO']);
		GRID.setInitWidths('*,*,60,60,70,70');
		GRID.setColTypes('ro,ro,ro,ro,ro,ro');
		GRID.setColSorting('str,str,date,str,int,str');;
		GRID.setColAlign('left,center,center,center,center,center');
		GRID.enableResizing('true,false,false,false,false,false');
		GRID.enableTooltips('false,false,false,false,false,false');
		GRID.init();
		GRID.load('<?= APP_URL ?>/escritorio/FormVideos.xml.php');

		var windows = new dhtmlXWindows();

		/* FUNCION PARA RECARGAR LA GRID */
		function resetGRID(){MDI.progressOn();GRID.clearAll();GRID.load('<?= APP_URL ?>/escritorio/FormVideos.xml.php',function(){dhtmlx.message('Datos actualizados correctamente...');MDI.progressOff();});}

	
/*
FORM NUEVO
__________
*/
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
			Tabbar.addTab('1','Galer&iacute;a');
			Tabbar.addTab('2','Subir Videos');
			Tabbar.addTab('3','Videos Cargados');
			Tabbar.setTabActive('1');
			Tabbar.disableTab('2');
			Tabbar.disableTab('3');
			Tabbar.hideTab('3');

			/* DEFINIR FGaleria */
			var FGaleria = Tabbar.cells('1').attachForm();
			items = 
			[
				{type:'input', name:'nombre', label:'Nombre:', labelWidth:'120', inputWidth:'600', required:true},
				{type:'calendar', name:'fecha', label:'Fecha:', labelWidth:'120', inputWidth:'600', dateFormat:'%d-%m-%Y', serverDateFormat:'%Y-%m-%d',	value:'<?= date('Y-m-d') ?>', required:true},
				{type:'editor', name:'descripcion', label:'Descripci&oacute;n:', labelWidth:'120', inputWidth:'598', inputHeight:'280',	required: true},
				{type:'button', name:'submit', value:'Siguiente', width:'200'},
				{type:'button',	name:'cancel', value:'Cancelar', width:'200'}
			];
			FGaleria.loadStruct(items,'json');

			/* EVENTOS DE FormG	*/
			FGaleria.attachEvent('onButtonClick',function(btn){

				/* CANCELAR */
				if(btn=='cancel'){resetGRID();Win.close();}

				/* SIGUIENTE */
				if(btn=='submit')
				{
					if(FGaleria.validate())
					{
						dhtmlx.message('Aguarde unos segundos...');
						dhtmlx.message('Enviando Datos al servidor...');
						MDI.progressOn();
						url			= '<?= APP_URL ?>/escritorio/FormVideos.act.php';
						nombre		= FGaleria.getItemValue('nombre');
						fecha		= FGaleria.getItemValue('fecha');
						descripcion	= FGaleria.getItemValue('descripcion');
						prm			= 'exe=insertarGaleria&nombre='+nombre+'&fecha='+fecha+'&descripcion='+descripcion;
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'Error...',text:'Se detect&oacute; un error al ingresar los datos.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true')
							{
								resetGRID();
								FGaleria.hideItem('submit');
								FGaleria.hideItem('cancel');
								Tabbar.enableTab('2');
								Tabbar.setTabActive('2');
								dhtmlx.message('Puede cargar videos...');
								C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getC').xmlDoc.responseText;
								Toolbar.setItemText('C',C);
							} 
						});
					}
					else
					{
						dhtmlx.alert({title:'Cuidado...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});
					}
				}
			});

			/* DEFINIR FCargarVideo */
			var FCargarVideo = Tabbar.cells('2').attachForm();
			items = 
			[
				{type:'upload', name:'videos', label:'Videos:', inputWidth:'740', inputHeight:'290', titleScreen:'false', mode:'html5', url:'<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=insertarVideo'},
				{type:'button',	name:'submit', value:'Finalizar', width:'200'}
			];
			FCargarVideo.loadStruct(items,'json');

			/* EVENTOS DE FormV */
			FCargarVideo.attachEvent('onButtonClick',function(btn){

				/* SIGUIENTE */
				if(btn=='submit'){resetGRID();Win.close();}

			});
			FCargarVideo.attachEvent('onUploadFile',function(n){
				MDI.progressOn();
				dhtmlx.message('Se ha cargado un archivo en el servidor...');
			});
			FCargarVideo.attachEvent('onUploadComplete',function(n){
				Dataview.refresh();
				Tabbar.showTab('3');
				Tabbar.enableTab('3');
				Tabbar.setTabActive('3');
				Dataview.load('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getVideosFromGaleria','json');
				Dataview.refresh();
				MDI.progressOff();
			});


			/* DEFINIR FVideosCargados */
			var FVideosCargados = Tabbar.cells('3').attachForm();
			items = 
				[
					{type:'container', name:'videos', label:'Videos:', labelWidth:'120', inputWidth:'598', inputHeight:'330'},
					{type:'button', name:'image', value:'Eliminar', width:'200'},
					{type:'button', name:'submit', value:'Finalizar', width:'200'}
				];
			FVideosCargados.loadStruct(items,'json');

			/* EVENTOS para FVideosCargados */
			FVideosCargados.attachEvent('onButtonClick',function(btn){
				if(btn=='submit'){resetGRID();Win.close();}
				if(btn=='image')
				{
					dhtmlx.message('Aguarde unos segundos...');
					dhtmlx.message('Eliminando im&aacute;genes en el servidor...');
					MDI.progressOn();
					if(Dataview.getSelected().length >= 1)
					{
						fid = Dataview.getSelected().toString();
						url = '<?= APP_URL ?>/escritorio/FormVideos.act.php';
						prm = 'exe=deleteVideo&fid='+fid;
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERRROR...',text:'Se ha etectado un error al eliminar el video.',type:'alert-error',ok:'Aceptar'});}
							if(rta.xmlDoc.responseText=='true'){Dataview.remove(Dataview.getSelected());dhtmlx.alert({title:'CORRECTO...',text:'Los videos se han eliminado en forma correcta.',type:'alert',ok:'Aceptar'});}
						});
					}
					else
					{dhtmlx.alert({title:'ERROR...',text:'Primero debe seleccionar un video para realizar esta operación.',type:'alert-error',ok:'Aceptar'})}
				}
			});


			/* DEFINIR DATAVIEW */
			var Dataview = new dhtmlXDataView({container:FVideosCargados.getContainer('videos'),
				type:{template:'#video#', width:140, height:80, margin:0, padding:1}	
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
				Tabbar.addTab('1','Galer&iacute;a');
				Tabbar.addTab('2','Videos');
				Tabbar.setTabActive('1');

				/* EVENTOS PARA FormVG */
				var FormVG = Tabbar.cells('1').attachForm();
				
				/* DEFINIR FormVG */
				items = 
				[
					{type:'input',		name:'nombre',		label:'Nombre:',				labelWidth:'120',	inputWidth:'600', required:true, readonly:true},
					{type:'calendar',	name:'fecha',		label:'Fecha:',					labelWidth:'120',	inputWidth:'600', dateFormat:'%d-%m-%Y',	serverDateFormat:'%Y-%m-%d',	required:true, readonly:true},
					{type:'editor',		name:'descripcion',	label:'Descripci&oacute;n:',	labelWidth:'120',	inputWidth:'598', inputHeight:'300',	required: true},
					{type:'button',		name:'submit',		value:'Finalizar',				width:'200'}
				];
				FormVG.loadStruct(items,'json');
				FormVG.attachEvent('onButtonClick',function(btn){
					if(btn=='submit')
					{
						resetGRID();
						Win.close();
					}
				});

				/* CARGAR DATOS en FormVG DESDE AJAX */
				dhtmlxAjax.post(
					'<?= APP_URL ?>/escritorio/FormVideos.act.php',
					'exe=getGaleria&id='+id,
					function(rta)
					{
						$dato = rta.xmlDoc.responseText.split('{%SPLIT%}');
						FormVG.setItemValue('nombre',$dato[0]);
						FormVG.setItemValue('fecha',$dato[1]);
						FormVG.setItemValue('descripcion',$dato[2]);
						MDI.progressOff();
					}
				);

				/* DEFINIR FormVI */
				var FormVI = Tabbar.cells('2').attachForm();
				items = 
					[
						{type:'container',	name:'videos',	label:'Videos:', labelWidth:'120', inputWidth:'598',	inputHeight:'360'},
						{type:'button',		name:'submit',		value:'Finalizar',		width:'200'}
					];
				FormVI.loadStruct(items,'json');

				/* EVENTOS para FormVI*/
				FormVI.attachEvent('onButtonClick',function(btn){
					if(btn=='submit')
					{
						resetGRID();
						Win.close();
					}
				});
				
				/* DEFINIR DATAVIEW */
				var Dataview = new dhtmlXDataView({container:FormVI.getContainer('videos'),
					type:{template:'#video#', width:140, height:80, margin:0, padding:1}	
				});
				Dataview.load('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getVideosFromGaleria&id='+id,'json');
				Dataview.refresh();
				
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
				Tabbar.addTab('1','Galer&iacute;a');
				Tabbar.addTab('2','Videos Cargados');
				Tabbar.addTab('3','Subir Videos');
				Tabbar.setTabActive('1');

				/* DEFINIR FModificarGaleria */
				var FModificarGaleria = Tabbar.cells('1').attachForm();
				items = 
				[
					{type:'input', name:'nombre', label:'Nombre:', labelWidth:'120', inputWidth:'600', required:true},
					{type:'calendar', name:'fecha', label:'Fecha:', labelWidth:'120', inputWidth:'600', dateFormat:'%d-%m-%Y', serverDateFormat:'%Y-%m-%d',	value:'<?= date('Y-m-d') ?>', required:true},
					{type:'editor', name:'descripcion',	label:'Descripci&oacute;n:', labelWidth:'120', inputWidth:'598', inputHeight:'280',	required: true},
					{type:'button', name:'submit', value:'Guardar', width:'200'},
					{type:'button', name:'cancel', value:'Finalizar', width:'200'}
				];
				FModificarGaleria.loadStruct(items,'json');

				/* CARGAR DATOS en FModificarGaleria DESDE AJAX */
				dhtmlxAjax.post(
					'<?= APP_URL ?>/escritorio/FormVideos.act.php',
					'exe=getGaleria&id='+id,
					function(rta)
					{
						$dato = rta.xmlDoc.responseText.split('{%SPLIT%}');
						FModificarGaleria.setItemValue('nombre',$dato[0]);
						FModificarGaleria.setItemValue('fecha',$dato[1]);
						FModificarGaleria.setItemValue('descripcion',$dato[2]);
						MDI.progressOff();
					}
				);
				
				/* EVENTOS PARA FModificarGaleria */
				FModificarGaleria.attachEvent('onButtonClick',function(btn){

					/* EVENTO: cancel */
					if(btn=='cancel'){resetGRID();Win.close();}

					/* EVENTO: submit*/
					if(btn=='submit')
					{
						if(FModificarGaleria.validate())
						{
							dhtmlx.message('Aguarde unos segundos...');
							dhtmlx.message('Enviando datos al servidor...');
							MDI.progressOn();
							url			= '<?= APP_URL ?>/escritorio/FormVideos.act.php';
							nombre		= FModificarGaleria.getItemValue('nombre');
							fecha		= FModificarGaleria.getItemValue('fecha');
							descripcion	= FModificarGaleria.getItemValue('descripcion');
							prm			= 'exe=updateGaleria&nombre='+nombre+'&fecha='+fecha+'&descripcion='+descripcion;
							dhtmlxAjax.post(url,prm,function(rta){
								MDI.progressOff();
								if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERROR...',text:'Se detect&oacute; un error al actualizar los datos en el servidor.',type:'alert-error',ok:'Aceptar'});}
								if(rta.xmlDoc.responseText=='true'){resetGRID();dhtmlx.alert({title:'Correcto...',text:'Los datos se guardaron en forma correcta.',type:'alert',ok:'Aceptar'});Tabbar.setTabActive('2');}
							});
						}
						else
						{dhtmlx.alert({title:'CUIDADO...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});}
					}
				});

				/* DEFINIR FormV */
				var FModificarVideos = Tabbar.cells('2').attachForm();
				items = 
				[
					{type:'container', name:'videos', label:'Videos:', labelWidth:'120', inputWidth:'598', inputHeight:'330'},
					{type:'button', name:'eliminar', value:'Eliminar', width:'200'},
					{type:'button', name:'submit', value:'Finalizar', width:'200'}
				];
				FModificarVideos.loadStruct(items,'json');

				/* EVENTOS PARA FormVI */
				FModificarVideos.attachEvent('onButtonClick',function(btn){

					/* EVENTO: submit */
					if(btn=='submit'){resetGRID();Win.close();}

					/* EVENTO: video */
					if(btn=='eliminar')
					{
						if(Dataview.getSelected().length >= 1)
						{
							dhtmlx.message('Aguarde unos segudos...');
							dhtmlx.message('Eliminando videos...');
							MDI.progressOn();
							fid = Dataview.getSelected().toString();
							url = '<?= APP_URL ?>/escritorio/FormVideos.act.php';
							prm = 'exe=deleteVideo&fid='+fid;
							dhtmlxAjax.post(url,prm,function(rta){
								MDI.progressOff();
								if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERRROR...',text:'Se ha etectado un error al eliminar el video.',type:'alert-error',ok:'Aceptar'});}
								if(rta.xmlDoc.responseText=='true'){Dataview.remove(Dataview.getSelected());dhtmlx.alert({title:'CORRECTO...',text:'Los videos se han eliminado en forma correcta.',type:'alert',ok:'Aceptar'});}
							});
						}
						else
						{dhtmlx.alert({title:'ERROR...',text:'Primero debe seleccionar un video para realizar esta operación.',type:'alert-error',ok:'Aceptar'})}
					}
					
				});

				/* DEFINIR DATAVIEW */
				var Dataview = new dhtmlXDataView({container:FModificarVideos.getContainer('videos'),
					type:{template:'#video#', width:140, height:80, margin:0, padding:1}	
				});
				Dataview.load('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getVideosFromGaleria','json');
				Dataview.refresh();

				
				/* DEFINIR FormVN */
				var FCargarVideo = Tabbar.cells('3').attachForm();
				items = 
				[
					{type:'upload', name:'videos', label:'Videos:', inputWidth:'740', inputHeight:'290', titleScreen:'false', mode:'html5', url:'<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=insertarVideo'},
					{type:'button', name:'submit', value:'Finalizar', width:'200'}
				];
				FCargarVideo.loadStruct(items,'json');

				/* EVENTOS PARA FormVN */
				FCargarVideo.attachEvent('onButtonClick',function(btn){
					if(btn=='submit'){resetGRID();Win.close();}
				});
				FCargarVideo.attachEvent('onUploadFile',function(n){
					dhtmlx.message('Se ha cargado un archivo en el servidor...');
					MDI.progressOn();
				});
				FCargarVideo.attachEvent('onUploadComplete',function(n){
					Tabbar.setTabActive('2');
					Dataview.clearAll();
					Dataview.load('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getVideosFromGaleria','json');
					Dataview.refresh();
					Tabbar.setTabActive('2');
					MDI.progressOff();
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
			if(id==null){dhtmlx.alert({title:'CUIDADO...',text:'Primero debe seleccionar una galer&iacute;a de videos para realizar esta operaci&oacute;n.',type:'alert-warning',ok:'Aceptar'});}
			else
			{
				dhtmlx.confirm({title:'CONFIRMAR...',text:'¿Esta seguro que desea eliminar esta galer&iacute;a de videos?',type:'alert',ok:'Aceptar',cancel:'Cancelar',callback:
					function(x)
					{
						if(x)
						{
							dhtmlx.message('Aguarde unos segundos...');
							dhtmlx.message('Enviando datos al servidor...');
							MDI.progressOn();
							url = '<?= APP_URL ?>/escritorio/FormVideos.act.php';
							prm = 'exe=deleteGaleria&id='+id;
							dhtmlxAjax.post(url,prm,function(rta){
								MDI.progressOff();
								if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERROR...',text:'Se detecto un error al eliminar la galer&iacute;a de videos.',type:'alert-error',ok:'Aceptar'});}
								if(rta.xmlDoc.responseText=='true')
								{
									resetGRID();
									C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getC').xmlDoc.responseText;
									Toolbar.setItemText('C',C);
									dhtmlx.alert({title:'CORRECTO...',text:'Se ha eliminado en forma correcta una galer&iacute;a de videos.',type:'alert',ok:'Aceptar'});
								}
							});
						}
					}
				});
			}
		}


		
/*
FORM PUBLICAR
______________
*/
		function FormPublicar()
		{
			id = GRID.getSelectedId();
			if(id==null){dhtmlx.alert({title:'CUIDADO...',text:'Primero debe seleccionar una galer&iacute;a de videos para realizar esta operaci&oacute;n.',type:'alert-warning',ok:'Aceptar'});}
			else
			{
				dhtmlx.confirm({title:'CONFIRMAR...',text:'¿Esta seguro que desea modificar el estado de esta galer&iacute;a?',type:'alert',ok:'Aceptar',cancel:'Cancelar',callback:
					function(x)
					{
						if(x)
						{
							dhtmlx.message('Aguarde unos segundos...');
							dhtmlx.message('Enviando datos al servidor...');
							MDI.progressOn();
							MDI.progressOn();
							url = '<?= APP_URL ?>/escritorio/FormVideos.act.php';
							std = GRID.cells(id,5).getValue()
							prm = 'exe=publicarGaleria&id='+id+'&estado='+std;
							dhtmlxAjax.post(url,prm,function(rta){
								MDI.progressOff();
								if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERROR...',text:'Se detecto un error al cambiar el estado de la galeria.',type:'alert-error',ok:'Aceptar'});}
								if(rta.xmlDoc.responseText=='true'){resetGRID();dhtmlx.alert({title:'CORRECTO...',text:'El estado de la galer&iacute;a se ha modificado en forma correcta.',type:'alert',ok:'Aceptar'});}
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
			url = '<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=FormP';
			if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
			{
				resetGRID();	
				C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getC').xmlDoc.responseText;
				Toolbar.setItemText('C',C);
			}
		}

		
/*
FORM FormA
______________
*/
		function FormA()
		{
			url = '<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=FormA';
			if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
			{
				resetGRID();	
				C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getC').xmlDoc.responseText;
				Toolbar.setItemText('C',C);
			}
		}


/*
FORM FormS
__________
*/
		function FormS()
		{
			url = '<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=FormS';
			if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
			{
				resetGRID();	
				C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getC').xmlDoc.responseText;
				Toolbar.setItemText('C',C);
			}
		}

		
/*
FORM FormU
______________
*/		
		function FormU()
		{
			url = '<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=FormU';
			if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
			{
				resetGRID();	
				C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getC').xmlDoc.responseText;
				Toolbar.setItemText('C',C);
			}
		}


		
/*
FORM FormBB
______________
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
					url = '<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=FormP';
					if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
					{	
						dhtmlx.message('Enviando busqueda al servidor...');
						MDI.progressOn();
						url = '<?= APP_URL ?>/escritorio/FormVideos.act.php';
						prm = 'exe=FormBB&str='+str+'&campo='+campo;
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detector un error en la busqueda.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true')
							{
								resetGRID();
								C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getC').xmlDoc.responseText;
								Toolbar.setItemText('C',C);
							};
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
						url = '<?= APP_URL ?>/escritorio/FormVideos.act.php';
						prm = 'exe=FormBC';
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detector un error al limpiar los filtros de busqueda.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true')
							{
								resetGRID();
								C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormVideos.act.php?exe=getC').xmlDoc.responseText;
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
