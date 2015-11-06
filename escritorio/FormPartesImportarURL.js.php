<?php
	# Inicio de session.
	session_start();
	
	# Requerir instacia de Wordpress.
	require_once './wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator')):
		global $wpdb;
		$cuil = $_SESSION['autorizado'];
		$tr   = $wpdb->get_var("select count(*) from taxonomia t inner join partes p on p.id=t.parte_id where t.cuil_dipu='".$cuil."' and t.type='noticia' and p.categoria_id is null ");
		$tp   = $tr / 15;
		if(fmod($tr,15)) $tp = intval($tr / 15) +1;
	?>
		<script>

		/* Definir window. */
		var windows = new dhtmlXWindows();
		windows.setImagePath('<?= APP_URL ?>/imgs/');
		
		/* Definir Toolbar. */
		var Toolbar = MDIA.attachToolbar();
		
		Toolbar.setIconsPath('<?= APP_URL ?>/imgs/');
		Toolbar.loadXMLString('<toolbar><item type="button" id="BNuevo" text="Nuevo" img="partenuevo.png" imgdis="partenuevo.png" /><item type="button" id="BVisualizar" text="Visualizar" img="partevisualizar.png" imgdis="partevisualizar.png" /><item type="button" id="BModificar" text="Modificar" img="partemodificar.png" imgdis="partemodificar.png" /><item type="button" id="BEliminar" text="Eliminar" img="parteeliminar.png" imgdis="parteseliminar.png" /><item type="button" id="BPublicar" text="Publicar/Pendiente" img="partepublicar.png" imgdis="partepublicar.png" /><item type="separator" id="separado1" /><item type="button" id="P" text="" img="primero.png" imgdis="primero.png" /><item type="button" id="A" text="" img="anterior.png" imgdis="anterior.png" /><item type="text" id="C" text="Visualizando página 1 de <?= $tp ?> (<?= $tr ?> registros)" /><item type="button" id="S" text="" img="siguiente.png" imgdis="siguiente.png" /><item type="button" id="U" text="" img="ultimo.png" imgdis="ultimo.png" /><item type="separator" id="separador2" /><item type="text" id="B" text="Buscar:" /><item type="buttonInput" id="STR" width="100" value="Esta palabra..." /><item type="text" id="B" text=" en " /><item type="buttonSelect" id="EN" text="el campo" title="" /><item type="button" id="BB" text="" img="buscar.png" imgdis="buscar.png" /><item type="button" id="BC" text="" img="nofiltrar.png" imgdis="nofiltrar.png" /></toolbar>', function(){});
		Toolbar.addListOption('EN','volanta',1,'button','Volanta','encampo.png');
		Toolbar.addListOption('EN','titulo',2,'button','Titulo','encampo.png');
		Toolbar.addListOption('EN','bajada',3,'button','Bajada','encampo.png');
		Toolbar.addListOption('EN','fecha',4,'button','Fecha','encampo.png');
		Toolbar.addListOption('EN','hora',5,'button','Hora','encampo.png');
		Toolbar.addListOption('EN','cabeza',6,'button','Encabezado','encampo.png');
		Toolbar.addListOption('EN','cuerpo',7,'button','Cuerpo','encampo.png');
		Toolbar.attachEvent('onClick',function(name){
			if(name=='BNuevo') FormNuevo();
			if(name=='BVisualizar') FormVisualizar();
			if(name=='BModificar') FormModificar();
			if(name=='BEliminar') FormEliminar();
			if(name=='BPublicar') FormPublicar();
			if(name=='P') FormP();
			if(name=='A') FormA();
			if(name=='S') FormS();
			if(name=='U') FormU();
			if(name=='BB') FormBB();
			if(name=='BC') FormBC();
		});

		/* Definir grid. */
		var GRID = MDIA.attachGrid();
		GRID.setIconsPath('<?= APP_URL ?>/imgs/');
		GRID.setHeader(["TITULO","FECHA","PUBLICADO","HORA","ESTADO"]);
		GRID.setInitWidths("*,80,90,55,70");
		GRID.setColTypes("rotxt,rotxt,rotxt,rotxt,rotxt");
		GRID.setColAlign('left,center,center,center,center');
		GRID.enableResizing('false,true,true,true,true');
		GRID.enableTooltips('false,false,false,false,false');
		GRID.setColSorting('str,str,str,str,str');
		GRID.init();
		GRID.load('<?= APP_URL ?>/escritorio/FormPartes.xml.php');

		/* Resetear GRID */
		function resetGRID()
		{
			MDI.progressOn();
			GRID.clearAll();
			GRID.load('<?= APP_URL ?>/escritorio/FormPartes.xml.php',function(){dhtmlx.message('Datos actualizados correctamente...');MDI.progressOff();});
		}
		
/*
* Nuevo
*/
		function FormNuevo()
		{
			var Win = windows.createWindow('Win', 0, 0, 980, 500);
			Win.setIcon('partenuevo.png');
			Win.setText('Nuevo');
			Win.denyResize();
			Win.denyMove();
			Win.setModal(1);
			Win.centerOnScreen();
			Win.button('park').hide();
			Win.button('minmax1').hide();
			
			var TabBar = Win.attachTabbar();
			TabBar.setImagePath("<?= APP_URL ?>/imgs/");
			TabBar.addTab('1','Url');
			TabBar.addTab('2','Titulos');
			TabBar.addTab('3','Contenido');
			TabBar.addTab('4','Cargar Fotos');
			TabBar.addTab('5','Fotos Cargadas');
			TabBar.addTab('6','Cargar Videos');
			TabBar.addTab('7','Videos Cargados');
			TabBar.setTabActive('1');
			TabBar.disableTab('2');
			TabBar.disableTab('3');
			TabBar.disableTab('4');
			TabBar.disableTab('5');
			TabBar.disableTab('6');
			TabBar.disableTab('7');
			TabBar.hideTab('5');
			TabBar.hideTab('7');

			/* Inicio cargar url. */
			var FormUrl = TabBar.cells('1').attachForm();
			items = [
						{type:'input', name:'urlT', label:'Url:', labelWidth:120, inputWidth:710, required:true},
						{type:'button', name:'submit', value:'Siguiente', width:200},
						{type:'button', name:'cancel', value:'Finalizar', width:200}	
					];
			FormUrl.loadStruct(items,'json');
			FormUrl.attachEvent('onButtonClick',function(name){
				if(name=='cancel')
				{
					resetGRID();
					dhtmlx.message('Aguarde unos segundos...');
					dhtmlx.message('Solicitando datos al servidor...');
					Win.close();
				}
				if(name=='submit')
				{
					if(FormUrl.validate())
					{
						dhtmlx.message('Aguarde unos segundos...');
						dhtmlx.message('Enviando datos al servidor...');
						MDI.progressOn();
						urlT = FormUrl.getItemValue('urlT');

						ajax = new XMLHttpRequest();
						r = 'http://query.yahooapis.com/v1/public/yql?q=';
         				q   = encodeURIComponent('select * from html where url="'+urlT+'" and xpath="/html"');
						ajax.onreadystatechange = function()
						{
							if (ajax.readyState === 4)
							{
								if (ajax.status === 200)
								{
									xml = ajax.responseXML;
			                     	titulo = xml.getElementsByTagName("title")[0].childNodes[0];
			                     	tituloText = new XMLSerializer().serializeToString(titulo);
			                     	resumenText  = null;
			                     	contenidoText = null;
			                     	P = xml.getElementsByTagName('p');
			                     	for(i in P)
			                     	{
				                     	if(P[i].innerText)
				                     	{
			                     			contenidoText += P[i].innerText;
				                     	}
			                     	}
			                     	if(contenidoText)
			                     	{
				                     	txt = contenidoText.replace('null','');
				                     	resumenText = txt.split('.');
				                     	FormTitulos.setItemValue('titulo',tituloText);
				                     	FContenido.setItemValue('texto',txt);
				                     	FContenido.setItemValue('cabeza',resumenText[0]+'.');
				                     	MDI.progressOff();
			                     	}
			                     	else
			                     	{
				                     	MDI.progressOff();
										dhtmlx.alert({title:'ERROR...',text:'El sistema no puede importar esta noticia. Consulte con su administrador',type:'alert-error',ok:'Aceptar',callback:function(){Win.close();}});
			                     	}
								}
			            	} 
			        	};
				    	ajax.open("POST",r+q,true);
				        ajax.send(null);
						        
							TabBar.enableTab('2');
							TabBar.setTabActive('2');
							TabBar.disableTab('1');
							TabBar.hideTab('1');
					}
					else
					{
						dhtmlx.alert({title:'CUIDADO...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});
					}
				}
			});
			

			/* Inicio formulario para cargar titulos. */
			var FormTitulos = TabBar.cells('2').attachForm();
			items = 
			[
				{type:'input', name:'volanta', label:'Volanta:', labelWidth:120, inputWidth:710},
				{type:'input', name:'titulo', label:'Titulo:', labelWidth:120, inputWidth:710, required:true},
				{type:'input', name:'bajada', label:'Bajada:', labelWidth:120, inputWidth:710	},
				{type:'calendar', name:'fecha', label:'Fecha:', labelWidth:120, inputWidth:710, dateFormat:'%d-%m-%Y', serverDateFormat:'%Y-%m-%d',	value:'<?= date('Y-m-d'); ?>', required:true},
				{type:'input', name:'hora', label:'Hora:', labelWidth:120, inputWidth:710, value:'<?= date('h:i:s'); ?>'},
				{type:'button', name:'submit', value:'Siguiente', width:200},
				{type:'button', name:'cancel', value:'Cancelar', width:200}
			];
			FormTitulos.loadStruct(items,'json');
			FormTitulos.attachEvent('onButtonClick',function(name){
				if(name=='cancel')
				{
					resetGRID();
					dhtmlx.message('Aguarde unos segundos...');
					dhtmlx.message('Solicitando datos al servidor...');
					Win.close();
				}
				if(name=='submit')
				{
					if(FormTitulos.validate())
					{
						dhtmlx.message('Aguarde unos segundos...');
						dhtmlx.message('Enviando datos al servidor...');
						MDI.progressOn();
						volanta = FormTitulos.getItemValue('volanta');
						titulo  = FormTitulos.getItemValue('titulo');
						bajada  = FormTitulos.getItemValue('bajada');
						fecha   = FormTitulos.getItemValue('fecha');
						hora    = FormTitulos.getItemValue('hora');
						url		= '<?= APP_URL ?>/escritorio/FormPartes.act.php';
						prm     = 'exe=insertTitulos'
								  +'&volanta='+volanta
								  +'&titulo='+titulo
								  +'&bajada='+bajada
								  +'&fecha='+fecha
								  +'&hora='+hora;
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detecto un error al ingresar los datos al servidor.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true')
							{
								resetGRID();
								FormTitulos.lock();
								TabBar.enableTab('3');
								TabBar.setTabActive('3');
								dhtmlx.message('Puede cargar el contenido...');
								C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getC').xmlDoc.responseText;
								Toolbar.setItemText('C',C);
							}
						});
					}
					else
					{
						dhtmlx.alert({title:'CUIDADO...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});
					}
				}
			});
			
			/* Fin formulario para cargar titulos. */

			/* Inicio de formulario Contenido. */
			var FContenido = TabBar.cells('3').attachForm();
			items = [
				{type:'input', name:'cabeza', label:'Resumen:', labelWidth:120, inputWidth:710, required:true},
				{type:'editor', name:'texto', label:'Noticia:', labelWidth:120, inputWidth:710, inputHeight:300},
				{type:'button', name:'submit', value:'Siguiente', width:200},
				{type:'button', name:'cancel', value:'Finalizar', width:200}
			];
			FContenido.loadStruct(items,'json');
			FContenido.attachEvent('onButtonClick',function(name){
				if(name=='cancel')
				{
					resetGRID();
					Win.close();
				}
				if(name=='submit')
				{
					if(FContenido.validate())
					{
						dhtmlx.message('Aguarde unos segundos...');
						dhtmlx.message('Enviando datos al servidor...');
						MDI.progressOn();
						cabeza  = FContenido.getItemValue('cabeza');
						cuerpo  = FContenido.getItemValue('texto');
						url     = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
						prm     = 'exe=updateContenido'
								+ '&cabeza='+cabeza
								+ '&cuerpo='+cuerpo;
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detecto un error al ingresar los datos al servidor.',type:'alert-error',ok:'Aceptar'});
							if(rta.xmlDoc.responseText=='true')
							{
								FContenido.lock();
								TabBar.enableTab('4');
								TabBar.setTabActive('4');
								dhtmlx.message('Puede cargar fotos y videos...');
							}
						});
					}
					else
					{
						dhtmlx.alert({title:'CUIDADO...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});
					}
				} 
			});
			/* Fin de formulario contenido. */

			/* Inicio de formulario para cargar imagenes. */
			var CargarImg = TabBar.cells('4').attachForm();
			items = [
						{type:'upload',  name:'fotografia', label:'Fotografias:', titleScreen:false,  labelWidth:120, inputWidth:710, inputHeight:280, mode:'html5', url:'<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=insertFotografias'},
						{type:'button',  name:'submit',  value:'Siguiente', width:200},
						{type:'button',  name:'cancel',  value:'Finalizar',  width:200}
					];
			CargarImg.loadStruct(items,'json');
			CargarImg.attachEvent('onButtonClick',function(name){
				if(name=='cancel')
				{
					resetGRID();
					Win.close();
				}
				if(name=='submit')
				{
					TabBar.enableTab('6');
					TabBar.setTabActive('6');
				}
			});
			CargarImg.attachEvent('onUploadComplete',function(n){
				Dataview1.load('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getFotosFromPartes','json');
				Dataview1.refresh();
				TabBar.showTab('5');
				TabBar.enableTab('5');
				TabBar.setTabActive('5');
				MDI.progressOff();
			});
			CargarImg.attachEvent('onUploadFile',function(n){
				MDI.progressOn();
				dhtmlx.message('Se ha enviado una fotografia al servidor.');
			});
			/* Fin de formulario para cargar imagenes. */
			
			/* Inicio de formulario para visualizar imagenes. */
			var ImgCargadas = TabBar.cells('5').attachForm();
			items = 
			[
				{type:'container', name:'fotografias', label:'Fotograf&iacute;as:', labelWidth:'120', inputWidth:'598',	inputHeight:'300'},
				{type:'button', name:'imagen', value:'Eliminar', width:'200'},
				{type:'button', name:'siguiente', value:'Siguiente', width:'200'},
				{type:'button', name:'submit', value:'Finalizar', width:'200'}
			];
			ImgCargadas.loadStruct(items,'json');
			
			/* EVENTOS */
			ImgCargadas.attachEvent('onButtonClick',function(btn){
				if(btn=='submit')
				{
					resetGRID();
					Win.close();
				}
				if(btn=='siguiente')
				{
					TabBar.enableTab('6');
					TabBar.setTabActive('6');
				}
				if(btn=='imagen')
				{
					if(Dataview1.getSelected().length >= 1)
					{
						dhtmlx.message('Aguarde unos segundos...');
						dhtmlx.message('Eliminando archivo en el servidor...');
						MDI.progressOn();
						fid = Dataview1.getSelected().toString();
						url = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
						prm = 'exe=deleteFotografia&fid='+fid;
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERRROR...',text:'Se ha etectado un error al eliminar las fotografias.',type:'alert-error',ok:'Aceptar'});}
							if(rta.xmlDoc.responseText=='true'){Dataview1.remove(Dataview1.getSelected());dhtmlx.alert({title:'CORRECTO...',text:'Las fotografías se han eliminado en forma correcta.',type:'alert',ok:'Aceptar'});}
						});
					}
					else
					{dhtmlx.alert({title:'ERROR...',text:'Primero debe seleccionar una fotografia para realizar esta operación.',type:'alert-error',ok:'Aceptar'})}
				}
			});
			/* DEFINIR DATAVIEW1 */
			var Dataview1 = new dhtmlXDataView({container:ImgCargadas.getContainer('fotografias'),
				type:{template:'#archivo#', width:140, height:80, margin:0, padding:1}	
			});
			/* Fin de formulario para visualizar imagenes. */
		
			/* Inicio de formulario para cargar videos. */
			var CargarVd = TabBar.cells('6').attachForm();
			items = [
						{type:'upload',   name:'video', label:'Videos:', titleScreen:false,  labelWidth:120, inputWidth:710, inputHeight:280, mode:'html5', url:'<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=insertarVideo'},
						{type:'button',  name:'submit',  value:'Finalizar', width:200}
					];
			CargarVd.loadStruct(items,'json');
			CargarVd.attachEvent('onButtonClick',function(name){
				if(name=='submit') { resetGRID(); Win.close(); }
			});
			CargarVd.attachEvent('onUploadFile',function(n){
				MDI.progressOn();
				dhtmlx.message('Se ha cargado un archivo en el servidor...');
			});
			CargarVd.attachEvent('onUploadComplete',function(n){
				Dataview2.load('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getVideosFromPartes','json');
				Dataview2.refresh();
				TabBar.showTab('7');
				TabBar.enableTab('7');
				TabBar.setTabActive('7');
				MDI.progressOff();
			});
			/* Fin de formulario para cargar videos. */

			/* Inicio de formulario para visualizar videos. */
			var FVideosCargados = TabBar.cells('7').attachForm();
			items = 
				[
					{type:'container', name:'videos', label:'Videos:', labelWidth:'120', inputWidth:'598', inputHeight:'330'},
					{type:'button', name:'image', value:'Eliminar', width:'200'},
					{type:'button', name:'submit', value:'Finalizar', width:'200'}
				];
			FVideosCargados.loadStruct(items,'json');
			FVideosCargados.attachEvent('onButtonClick',function(btn){
				if(btn=='submit'){resetGRID();Win.close();}
				if(btn=='image')
				{
					if(Dataview2.getSelected().length >= 1)
					{
						dhtmlx.message('Aguarde unos segundos...');
						dhtmlx.message('Eliminando archivo en el servidor...');
						MDI.progressOn();
						fid = Dataview2.getSelected().toString();
						url = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
						prm = 'exe=deleteVideo&fid='+fid;
						dhtmlxAjax.post(url,prm,function(rta){
							MDI.progressOff();
							if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERRROR...',text:'Se ha etectado un error al eliminar el video.',type:'alert-error',ok:'Aceptar'});}
							if(rta.xmlDoc.responseText=='true'){Dataview2.remove(Dataview2.getSelected());dhtmlx.alert({title:'CORRECTO...',text:'Los videos se han eliminado en forma correcta.',type:'alert',ok:'Aceptar'});}
						});
					}
					else
					{dhtmlx.alert({title:'ERROR...',text:'Primero debe seleccionar un video para realizar esta operación.',type:'alert-error',ok:'Aceptar'})}
				}
			});

			/* DEFINIR DATAVIEW */
			var Dataview2 = new dhtmlXDataView({container:FVideosCargados.getContainer('videos'),
				type:{template:'#video#', width:140, height:80, margin:0, padding:1}	
			});
			/* Fin de formulario para visualizar videos. */
		}

		
/*
 * VISUALIZAR
 */
		function FormVisualizar()
		{
			id = GRID.getSelectedId();
			if(id==null)
			{
				dhtmlx.alert({title:'CUIDADO...',text:'Para realizar esta operaci&oacute;n primero debe seleccionar una noticia.',type:'alert-warning',ok:'Aceptar'});
			}
			else
			{
				/* MENSAJES */
				dhtmlx.message('Aguarde unos segundos...');
				dhtmlx.message('Solicitando datos al servidor...');
				MDI.progressOn();
				
				var Win = windows.createWindow('Win', 0, 0, 980, 500);
				Win.setIcon('partevisualizar.png');
				Win.setText('Visualizar');
				Win.denyResize();
				Win.denyMove();
				Win.setModal(1);
				Win.centerOnScreen();
				Win.button('park').hide();
				Win.button('minmax1').hide();
				
				var TabBar = Win.attachTabbar();
				TabBar.setImagePath("<?= APP_URL ?>/imgs/");
				TabBar.addTab('1','Titulos');
				TabBar.addTab('2','Contenido');
				TabBar.addTab('3','Fotos Cargadas');
				TabBar.addTab('4','Videos Cargados');
				TabBar.setTabActive('1');
				/* Inicio formulario para cargar titulos. */
				var FormTitulos = TabBar.cells('1').attachForm();
				items = 
				[
					{type:'input', name:'volanta', label:'Volanta:', labelWidth:120, inputWidth:710},
					{type:'input', name:'titulo', label:'Titulo:', labelWidth:120, inputWidth:710, required:true},
					{type:'input', name:'bajada', label:'Bajada:', labelWidth:120, inputWidth:710	},
					{type:'calendar', name:'fecha', label:'Fecha:', labelWidth:120, inputWidth:710, dateFormat:'%d-%m-%Y', serverDateFormat:'%Y-%m-%d',	value:'<?= date('Y-m-d'); ?>', required:true},
					{type:'input', name:'hora', label:'Hora:', labelWidth:120, inputWidth:710, value:'<?= date('h:i:s'); ?>'},
					{type:'button', name:'cancel', value:'Finalizar', width:200}
				];

				FormTitulos.loadStruct(items,'json');
				FormTitulos.attachEvent('onButtonClick',function(name){
					if(name=='cancel')
					{
						resetGRID();
						Win.close();
					}
				});
				/* Fin formulario para cargar titulos. */

				/* Inicio de formulario Contenido. */
				var FContenido = TabBar.cells('2').attachForm();
				items = [
					{type:'input', name:'cabeza', label:'Resumen:', labelWidth:120, inputWidth:710, required:true},
					{type:'editor', name:'texto', label:'Noticia:', labelWidth:120, inputWidth:710, inputHeight:300},
					{type:'button', name:'cancel', value:'Finalizar', width:200}
				];
				FContenido.loadStruct(items,'json');
				FContenido.attachEvent('onButtonClick',function(name){
					if(name=='cancel')
					{
						resetGRID();
						Win.close();
					}
				});
				/* Fin de formulario contenido. */
				
				/* Inicio cargar contenido */
				dhtmlxAjax.post(
					'<?= APP_URL ?>/escritorio/FormPartes.act.php',
					'exe=getParte&id='+id,
					function(rta)
					{
						console.log(rta.xmlDoc.responseText);
						$dato = rta.xmlDoc.responseText.split('{%SPLIT%}');
						FormTitulos.setItemValue('volanta',$dato[0]);
						FormTitulos.setItemValue('titulo',$dato[1]);
						FormTitulos.setItemValue('bajada',$dato[2]);
						FormTitulos.setItemValue('fecha',$dato[3]);
						FormTitulos.setItemValue('hora',$dato[4]);
						FContenido.setItemValue('cabeza',$dato[5]);
						FContenido.setItemValue('texto',$dato[6]);
						MDI.progressOff();
					}
				);
				/* Fin cargar contenido */

				/* Inicio de formulario para visualizar imagenes. */
				var ImgCargadas = TabBar.cells('3').attachForm();
				items = 
				[
					{type:'container', name:'fotografias', label:'Fotograf&iacute;as:', labelWidth:'120', inputWidth:'598',	inputHeight:'300'},
					{type:'button', name:'submit', value:'Finalizar', width:'200'}
				];
				ImgCargadas.loadStruct(items,'json');
				ImgCargadas.attachEvent('onButtonClick',function(btn){
					if(btn=='submit')
					{
						resetGRID();
						Win.close();
					}
				});
				/* DEFINIR DATAVIEW1 */
				var Dataview1 = new dhtmlXDataView({container:ImgCargadas.getContainer('fotografias'),
					type:{template:'#archivo#', width:140, height:80, margin:0, padding:1}	
				});
				Dataview1.load('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getFotosFromPartes','json');
				Dataview1.refresh();
				/* Fin de formulario para visualizar imagenes. */

				/* Inicio de formulario para visualizar videos. */
				var FVideosCargados = TabBar.cells('4').attachForm();
				items = 
					[
						{type:'container', name:'videos', label:'Videos:', labelWidth:'120', inputWidth:'598', inputHeight:'330'},
						{type:'button', name:'submit', value:'Finalizar', width:'200'}
					];
				FVideosCargados.loadStruct(items,'json');
				FVideosCargados.attachEvent('onButtonClick',function(btn){
					if(btn=='submit'){resetGRID();Win.close();}
				});
				/* DEFINIR DATAVIEW */
				var Dataview2 = new dhtmlXDataView({container:FVideosCargados.getContainer('videos'),
					type:{template:'#video#', width:140, height:80, margin:0, padding:1}	
				});
				Dataview2.load('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getVideosFromPartes','json');
				Dataview2.refresh();
				/* Fin de formulario para visualizar videos. */
			}
		}


		
/*
 * MODIFICAR
 */
		function FormModificar()
		{
			id = GRID.getSelectedId();
			if(id==null)
			{
				dhtmlx.alert({title:'CUIDADO...',text:'Para realizar esta operaci&oacute;n primero debe seleccionar una noticia.',type:'alert-warning',ok:'Aceptar'});
			}
			else
			{
				dhtmlx.confirm({title:'CONFIRMAR...',text:'&iquest;Desea modificar esta noticia &#63;',type:'alert',ok:'Aceptar',cancel:'Cancelar',callback:
					function(x)
					{
						if(x)
						{
							/* MENSAJES */
							dhtmlx.message('Aguarde unos segundos...');
							dhtmlx.message('Solicitando datos al servidor...');
							MDI.progressOn();
							
							var Win = windows.createWindow('Win', 0, 0, 980, 500);
							Win.setIcon('partemodificar.png');
							Win.setText('Modificar');
							Win.denyResize();
							Win.denyMove();
							Win.setModal(1);
							Win.centerOnScreen();
							Win.button('park').hide();
							Win.button('minmax1').hide();
							
							var TabBar = Win.attachTabbar();
							TabBar.setImagePath("<?= APP_URL ?>/imgs/");
							TabBar.addTab('1','Titulos');
							TabBar.addTab('2','Contenido');
							TabBar.addTab('3','Cargar Fotos');
							TabBar.addTab('4','Fotos Cargadas');
							TabBar.addTab('5','Cargar Videos');
							TabBar.addTab('6','Videos Cargados');
							TabBar.setTabActive('1');

							/* Inicio formulario para cargar titulos. */
							var FormTitulos = TabBar.cells('1').attachForm();
							items = 
							[
								{type:'input', name:'volanta', label:'Volanta:', labelWidth:120, inputWidth:710},
								{type:'input', name:'titulo', label:'Titulo:', labelWidth:120, inputWidth:710, required:true},
								{type:'input', name:'bajada', label:'Bajada:', labelWidth:120, inputWidth:710	},
								{type:'calendar', name:'fecha', label:'Fecha:', labelWidth:120, inputWidth:710, dateFormat:'%d-%m-%Y', serverDateFormat:'%Y-%m-%d',	required:true},
								{type:'input', name:'hora', label:'Hora:', labelWidth:120, inputWidth:710, value:'<?= date('h:i:s'); ?>'},
								{type:'button', name:'submit', value:'Actualizar', width:200},
								{type:'button', name:'cancel', value:'Finalizar', width:200}
							];

							FormTitulos.loadStruct(items,'json');
							FormTitulos.attachEvent('onButtonClick',function(name){
								if(name=='cancel')
								{
									resetGRID();
									Win.close();
								}
								if(name=='submit')
								{
									if(FormTitulos.validate())
									{
										dhtmlx.message('Aguarde unos segundos...');
										dhtmlx.message('Enviando datos al servidor...');
										MDI.progressOn();
										volanta = FormTitulos.getItemValue('volanta');
										titulo  = FormTitulos.getItemValue('titulo');
										bajada  = FormTitulos.getItemValue('bajada');
										fecha   = FormTitulos.getItemValue('fecha');
										hora    = FormTitulos.getItemValue('hora');
										url		= '<?= APP_URL ?>/escritorio/FormPartes.act.php';
										prm     = 'exe=updateTitulos'
												  +'&volanta='+volanta
												  +'&titulo='+titulo
												  +'&bajada='+bajada
												  +'&fecha='+fecha
												  +'&hora='+hora;
										dhtmlxAjax.post(url,prm,function(rta){
											MDI.progressOff();
											if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERROR...',text:'Se detecto un error al ingresar los datos al servidor.',type:'alert-error',ok:'Aceptar'});}
											if(rta.xmlDoc.responseText=='true'){dhtmlx.alert({title:'CORRECTO...',text:'Los titulos se actualizaron en forma correcta.',type:'alert',ok:'Aceptar'});resetGRID();}
										});
									}
									else
									{
										dhtmlx.alert({title:'CUIDADO...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});
									}
								}
							});
							/* Fin formulario para cargar titulos. */

							/* Inicio de formulario Contenido. */
							var FContenido = TabBar.cells('2').attachForm();
							items = [
								{type:'input', name:'cabeza', label:'Resumen:', labelWidth:120, inputWidth:710, required:true},
								{type:'editor', name:'texto', label:'Noticia:', labelWidth:120, inputWidth:710, inputHeight:300},
								{type:'button', name:'submit', value:'Actualizar', width:200},
								{type:'button', name:'cancel', value:'Finalizar', width:200}
							];
							FContenido.loadStruct(items,'json');
							FContenido.attachEvent('onButtonClick',function(name){
								if(name=='cancel')
								{
									resetGRID();
									Win.close();
								}
								if(name=='submit')
								{
									if(FContenido.validate())
									{
										dhtmlx.message('Aguarde unos segundos...');
										dhtmlx.message('Enviando datos al servidor...');
										MDI.progressOn();
										cabeza  = FContenido.getItemValue('cabeza');
										cuerpo  = FContenido.getItemValue('texto');
										url     = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
										prm     = 'exe=updateContenido'
												+ '&cabeza='+cabeza
												+ '&cuerpo='+cuerpo;
										dhtmlxAjax.post(url,prm,function(rta){
											MDI.progressOff();
											if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detecto un error al ingresar los datos al servidor.',type:'alert-error',ok:'Aceptar'});
											if(rta.xmlDoc.responseText=='true') dhtmlx.alert({title:'CORRECTO...',text:'El cintenido se actualizó en forma correcta.',type:'alert',ok:'Aceptar'});
										});
									}
									else
									{
										dhtmlx.alert({title:'CUIDADO...',text:'Debe completar el formulario en forma correcta.',type:'alert-warning',ok:'Aceptar'});
									}
								} 
							});
							/* Fin de formulario contenido. */
							
							/* Inicio cargar contenido */
							dhtmlxAjax.post(
								'<?= APP_URL ?>/escritorio/FormPartes.act.php',
								'exe=getParte&id='+id,
								function(rta)
								{
									$dato = rta.xmlDoc.responseText.split('{%SPLIT%}');
									FormTitulos.setItemValue('volanta',$dato[0]);
									FormTitulos.setItemValue('titulo',$dato[1]);
									FormTitulos.setItemValue('bajada',$dato[2]);
									FormTitulos.setItemValue('fecha',$dato[3]);
									FormTitulos.setItemValue('hora',$dato[4]);
									FContenido.setItemValue('cabeza',$dato[5]);
									FContenido.setItemValue('texto',$dato[6]);
									MDI.progressOff();
								}
							);
							/* Fin cargar contenido */
							
							/* Inicio de formulario para cargar imagenes. */
							var CargarImg = TabBar.cells('3').attachForm();
							items = [
										{type:'upload',  name:'fotografia', label:'Fotografias:', titleScreen:false,  labelWidth:120, inputWidth:710, inputHeight:280, mode:'html5', url:'<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=insertFotografias'},
										{type:'button',  name:'submit',  value:'Siguiente', width:200},
										{type:'button',  name:'cancel',  value:'Finalizar',  width:200}
									];
							CargarImg.loadStruct(items,'json');
							CargarImg.attachEvent('onButtonClick',function(name){
								if(name=='cancel')
								{
									resetGRID();
									Win.close();
								}
								if(name=='submit')
								{
									TabBar.setTabActive('4');
								}
							});
							CargarImg.attachEvent('onUploadComplete',function(n){
								Dataview1.load('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getFotosFromPartes','json');
								Dataview1.refresh();
								TabBar.setTabActive('4');
								MDI.progressOff();
							});
							CargarImg.attachEvent('onUploadFile',function(n){
								dhtmlx.message('Se ha enviado una fotografia al servidor.');
								MDI.progressOn();
							});
							/* Fin de formulario para cargar imagenes. */
			
							/* Inicio de formulario para visualizar imagenes. */
							var ImgCargadas = TabBar.cells('4').attachForm();
							items = 
							[
								{type:'container', name:'fotografias', label:'Fotograf&iacute;as:', labelWidth:'120', inputWidth:'598',	inputHeight:'300'},
								{type:'button', name:'imagen', value:'Eliminar', width:'200'},
								{type:'button', name:'siguiente', value:'Siguiente', width:'200'},
								{type:'button', name:'submit', value:'Finalizar', width:'200'}
							];
							ImgCargadas.loadStruct(items,'json');
							ImgCargadas.attachEvent('onButtonClick',function(btn){
								if(btn=='submit')
								{
									resetGRID();
									Win.close();
								}
								if(btn=='siguiente')
								{
									TabBar.setTabActive('5');
								}
								if(btn=='imagen')
								{
									if(Dataview1.getSelected().length >= 1)
									{
										dhtmlx.message('Aguarde unos segundos...');
										dhtmlx.message('Eliminando datos en el servidor');
										MDI.progressOn();
										fid = Dataview1.getSelected().toString();
										url = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
										prm = 'exe=deleteFotografia&fid='+fid;
										dhtmlxAjax.post(url,prm,function(rta){
											MDI.progressOff();
											if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERRROR...',text:'Se ha etectado un error al eliminar las fotografias.',type:'alert-error',ok:'Aceptar'});}
											if(rta.xmlDoc.responseText=='true'){Dataview1.remove(Dataview1.getSelected());dhtmlx.alert({title:'CORRECTO...',text:'Las fotografías se han eliminado en forma correcta.',type:'alert',ok:'Aceptar'});}
										});
									}
									else
									{dhtmlx.alert({title:'ERROR...',text:'Primero debe seleccionar una fotografia para realizar esta operación.',type:'alert-error',ok:'Aceptar'})}
								}
							});
							/* DEFINIR DATAVIEW1 */
							var Dataview1 = new dhtmlXDataView({container:ImgCargadas.getContainer('fotografias'),
								type:{template:'#archivo#', width:140, height:80, margin:0, padding:1}	
							});
							Dataview1.load('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getFotosFromPartes','json');
							Dataview1.refresh();
							/* Fin de formulario para visualizar imagenes. */

							/* Inicio de formulario para cargar videos. */
							var CargarVd = TabBar.cells('5').attachForm();
							items = [
										{type:'upload',   name:'video', label:'Videos:', titleScreen:false,  labelWidth:120, inputWidth:710, inputHeight:280, mode:'html5', url:'<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=insertarVideo'},
										{type:'button', name:'siguiente', value:'Siguiente', width:'200'},
										{type:'button',  name:'submit',  value:'Finalizar', width:'200'}
									];
							CargarVd.loadStruct(items,'json');
							CargarVd.attachEvent('onButtonClick',function(name){
								if(name=='submit') { resetGRID(); Win.close(); }
								if(name=='siguiente')
								{
									TabBar.setTabActive('6');
								}
							});
							CargarVd.attachEvent('onUploadComplete',function(n){
								Dataview2.load('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getVideosFromPartes','json');
								Dataview2.refresh();
								TabBar.setTabActive('6');
							});
							/* Fin de formulario para cargar videos. */
			
							/* Inicio de formulario para visualizar videos. */
							var FVideosCargados = TabBar.cells('6').attachForm();
							items = 
								[
									{type:'container', name:'videos', label:'Videos:', labelWidth:'120', inputWidth:'598', inputHeight:'330'},
									{type:'button', name:'image', value:'Eliminar', width:'200'},
									{type:'button', name:'submit', value:'Finalizar', width:'200'}
								];
							FVideosCargados.loadStruct(items,'json');
							FVideosCargados.attachEvent('onButtonClick',function(btn){
								if(btn=='submit'){resetGRID();Win.close();}
								if(btn=='image')
								{
									if(Dataview2.getSelected().length >= 1)
									{
										dhtmlx.message('Aguarde unos segudos...');
										dhtmlx.message('Eliminando datos en el servidor...');
										MDI.progressOn();
										fid = Dataview2.getSelected().toString();
										url = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
										prm = 'exe=deleteVideo&fid='+fid;
										dhtmlxAjax.post(url,prm,function(rta){
											MDI.progressOff();
											if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERRROR...',text:'Se ha etectado un error al eliminar el video.',type:'alert-error',ok:'Aceptar'});}
											if(rta.xmlDoc.responseText=='true'){Dataview2.remove(Dataview2.getSelected());dhtmlx.alert({title:'CORRECTO...',text:'Los videos se han eliminado en forma correcta.',type:'alert',ok:'Aceptar'});}
										});
									}
									else
									{dhtmlx.alert({title:'ERROR...',text:'Primero debe seleccionar un video para realizar esta operación.',type:'alert-error',ok:'Aceptar'})}
								}
							});
							/* DEFINIR DATAVIEW */
							var Dataview2 = new dhtmlXDataView({container:FVideosCargados.getContainer('videos'),
								type:{template:'#video#', width:140, height:80, margin:0, padding:1}	
							});
							Dataview2.load('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getVideosFromPartes','json');
							Dataview2.refresh();
							/* Fin de formulario para visualizar videos. */
						}
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
			if(id==null){dhtmlx.alert({title:'CUIDADO...',text:'Primero debe seleccionar una galer&iacute;a de videos para realizar esta operaci&oacute;n.',type:'alert-warning',ok:'Aceptar'});}
			else
			{
				dhtmlx.confirm({title:'CONFIRMAR...',text:'¿Esta seguro que desea eliminar esta noticia?',type:'alert',ok:'Aceptar',cancel:'Cancelar',callback:
					function(x)
					{
						if(x)
						{
							dhtmlx.message('Aguarde unos segundos...');
							dhtmlx.message('Eliminando datos en el servidor');
							MDI.progressOn();
							url = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
							prm = 'exe=deleteParte&id='+id;
							dhtmlxAjax.post(url,prm,function(rta){
								MDI.progressOff();
								if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERROR...',text:'Se detecto un error al eliminar el Parte.',type:'alert-error',ok:'Aceptar'});}
								if(rta.xmlDoc.responseText=='true')
								{
									resetGRID();
									C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getC').xmlDoc.responseText;
									Toolbar.setItemText('C',C);
									dhtmlx.alert({title:'CORRECTO...',text:'Se ha eliminado en forma correcta una noticia.',type:'alert',ok:'Aceptar'});
								}
							});
						}
					}
				});
			}
		}

/*
FORM FormPublicar
__________
*/
		function FormPublicar()
		{
			id = GRID.getSelectedId();
			if(id==null){dhtmlx.alert({title:'CUIDADO...',text:'Primero debe seleccionar una galer&iacute;a de videos para realizar esta operaci&oacute;n.',type:'alert-warning',ok:'Aceptar'});}
			else
			{
				dhtmlx.confirm({title:'CONFIRMAR...',text:'&iquest;Desea cambiar el estado de esta noticia &#63;',type:'alert',ok:'Aceptar',cancel:'Cancelar',callback:
					function(x)
					{
						if(x)
						{
							dhtmlx.message('Aguarde unos segundos');
							dhtmlx.message('Enviando datos al servidor');
							MDI.progressOn();
							estado = GRID.cells(id,4).getValue();
							url = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
							prm = 'exe=publicarParte&id='+id+'&estado='+estado;
							dhtmlxAjax.post(url,prm,function(rta){
								MDI.progressOff();
								if(rta.xmlDoc.responseText=='false'){dhtmlx.alert({title:'ERROR...',text:'Se ha detectado un error al cambiar el estado del usuario.',type:'alert-error',ok:'Aceptar'});}
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
					url = '<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=FormP';
					if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
					{
						resetGRID();	
						C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getC').xmlDoc.responseText;
						Toolbar.setItemText('C',C);
					}
				}

				
		/*
		FORM FormA
		______________
		*/
				function FormA()
				{
					url = '<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=FormA';
					if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
					{
						resetGRID();	
						C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getC').xmlDoc.responseText;
						Toolbar.setItemText('C',C);
					}
				}


		/*
		FORM FormS
		__________
		*/
				function FormS()
				{
					url = '<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=FormS';
					if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
					{
						resetGRID();	
						C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getC').xmlDoc.responseText;
						Toolbar.setItemText('C',C);
					}
				}

				
		/*
		FORM FormU
		______________
		*/		
				function FormU()
				{
					url = '<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=FormU';
					if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
					{
						resetGRID();	
						C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getC').xmlDoc.responseText;
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
							url = '<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=FormP';
							if(dhtmlxAjax.getSync(url).xmlDoc.responseText=='true')
							{	
								dhtmlx.message('Enviando busqueda al servidor...');
								MDI.progressOn();
								url = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
								prm = 'exe=FormBB&str='+str+'&campo='+campo;
								dhtmlxAjax.post(url,prm,function(rta){
									MDI.progressOff();
									if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detector un error en la busqueda.',type:'alert-error',ok:'Aceptar'});
									if(rta.xmlDoc.responseText=='true')
									{
										resetGRID();
										C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getC').xmlDoc.responseText;
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
								url = '<?= APP_URL ?>/escritorio/FormPartes.act.php';
								prm = 'exe=FormBC';
								dhtmlxAjax.post(url,prm,function(rta){
									MDI.progressOff();
									if(rta.xmlDoc.responseText=='false') dhtmlx.alert({title:'ERROR...',text:'Se detector un error al limpiar los filtros de busqueda.',type:'alert-error',ok:'Aceptar'});
									if(rta.xmlDoc.responseText=='true')
									{
										resetGRID();
										C = dhtmlxAjax.getSync('<?= APP_URL ?>/escritorio/FormPartes.act.php?exe=getC').xmlDoc.responseText;
										Toolbar.setItemText('C',C);
										Toolbar.setValue('STR','Esta palabra...');
									}
								});
							}
						}
					});
				}

				function progressOff(MDI)
				{
					MDI.progressOff();
				}
		</script>
	<?
	else:
		wp_redirect(APP_URL.'/escritorio/index.php');
	endif;
	?>