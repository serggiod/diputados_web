<?php
	# Inicio de session.
	session_start();

	# Requerir instacia de Wordpress.
	require_once '../wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{

		global $wpdb;
		$html		= null;
		$exe		= null;
		$id			= null;
		$nombre		= null;
		$descripcion= null;
		$fecha		= null;
		$orden		= $wpdb->get_var("select max(g.orden) from galeria g where g.cuil_dipu='".$_SESSION['autorizado']."' and g.type='foto'") +1;
		$estado		= 'Pendiente';
		$type 		= 'fotografia';
		$cuil_dipu	= $_SESSION['autorizado'];
		$user_id	= $_SESSION['ID'];

		if($_POST['exe'])			$exe			= sanitize_text_field(trim($_POST['exe']));
		if($_GET['exe'])			$exe			= sanitize_text_field(trim($_GET['exe']));
		if($_POST['id'])			$id				= absint(sanitize_key(trim($_POST['id'])));
		if($_GET['id'])				$id				= absint(sanitize_key(trim($_GET['id'])));
		if($_POST['nombre'])		$nombre			= sanitize_text_field(trim($_POST['nombre']));
		if($_POST['descripcion'])	$descripcion	= sanitize_text_field(trim($_POST['descripcion']));

		if($_POST['fecha'])
		{
			$fechao   = new DateTime(sanitize_text_field(trim($_POST['fecha'])));
			$fecha    = date_format($fechao,'Y-m-d h:i:s'); 
		}
		
		if($_POST['estado'])
		{
			$tmp = sanitize_text_field(trim($_POST['estado']));
			if($tmp=='Pendiente') $estado = 'Publicado';
		}

		if($_POST['orden'])  $orden  = sanitize_text_field(trim($_POST['orden']));

		
		/* insertGallery */
		if($exe=='insertGallery')
		{
			$items = array(
				'nombre'		=> $nombre,
				'descripcion'	=> $descripcion,
				'fecha'			=> $fecha,
				'orden'			=> $orden,
				'estado'		=> $estado,
				'type'			=> $type,
				'cuil_dipu'		=> $cuil_dipu,
				'user_id'		=> $user_id 
			);
			if($wpdb->insert('galeria',$items)): 
				$html = 'true';
				$_SESSION['galeria_last_insert_id'] = $wpdb->get_var("select last_insert_id();");
			else: $html = 'false';
			endif;
		}
		
		/* insertFotografias */
		if($exe=='insertFotografias')
		{
			set_time_limit(43200);
			include APP_PATH.'/lib/class.WideImage/class.WideImage.php';
			if($_FILES)
			{
				$sub = md5($_SESSION['autorizado']);
				$dir  = APP_PATH.'/fotografias/'.$sub;
				$udir = APP_URL.'/fotografias/'.$sub;
				if(!file_exists($dir)) mkdir($dir);
				$realname = $_FILES["file"]["name"];
				$servername = str_replace(array('0.',' '),array('0_','_'),microtime().'_'.$realname);
				$image = WideImage::load($_FILES["file"]["tmp_name"]);
				$tmpimage = $image->resize(500);
				$tmpimage->saveToFile($dir."/".$servername,70);
				if(file_exists($dir."/".$servername))
				{
					$items = array(
							'titulo' => $realname,
							'archivo' => $servername,
							'fecha' => date('Y-m-d'),
							'estado' => 1
					);
					if($wpdb->insert('fotografias',$items))
					{
						$_SESSION['fotografia_last_insert_id'] = $wpdb->get_var("select last_insert_id();");
						$items = array(
								'parte_id' => $_SESSION['galeria_last_insert_id'],
								'media_id' => $_SESSION['fotografia_last_insert_id'],
								'cuil_dipu' => $_SESSION['autorizado'],
								'type' => $type
						);
						if($wpdb->insert('taxonomia',$items))
						{
							$json = '{"state":true,"name":"'.$realname.'"}';
							echo $json;
							die;
						}
					}
				}
			}
		}

		/* getGaleria */
		if($exe=='getGaleria')
		{
			error_log('El id recibido es: '.$id);
			$galeria = $wpdb->get_row("select nombre,date_format(fecha,'%Y-%m-%d') fecha,descripcion from galeria where id=".$id.";");
			$html = $galeria->nombre.'{%SPLIT%}'.$galeria->fecha.'{%SPLIT%}'.$galeria->descripcion;
			error_log('Respuesta enviada al servidor: '.$html);
			$_SESSION['galeria_last_insert_id'] = $id;
			error_log('Id guadada: '.$_SESSION['galeria_last_insert_id']);
		}
		
		/* getFotosFromGaleria */
		if($exe=='getFotosFromGaleria')
		{
			error_log('Id con el que trabaja: '.$_SESSION['galeria_last_insert_id']);
			$id = $_SESSION['galeria_last_insert_id'];
			$sql   = "select f.id id,f.archivo archivo from taxonomia t inner join fotografias f on f.id=t.media_id where t.type='fotografia' and t.parte_id=".$id;
			error_log('Ejecutando SQL: '.$sql);
			$html  = '['."\n";
			foreach($wpdb->get_results($sql) as $fotografia)
			{
				$archivo = '<img src="'.APP_URL.'/fotografias/'.md5($_SESSION['autorizado']).'/'.$fotografia->archivo.'" border="0" width="100%"/>';
				$html .= '{\'id\':\''.$fotografia->id.'\',\'archivo\':\''.$archivo.'\'},'."\n";
			}
			$html .= ']';
			error_log('Respuesta evida al navegador: '.$html);	
		}

		/* getGalleryLastInsertId */
		if($exe=='getGalleryLastInsertId')
		{
			$html = $_SESSION['galeria_last_insert_id'];
		}

		/* updateGallery */
		if($exe=='updateGallery')
		{
			$s = array('nombre'=>$nombre,'fecha'=>$fecha,'descripcion'=>$descripcion);
			$w = array('id'=>$id);
			if($wpdb->update('galeria',$s,$w)): $html='true';
			else: $html='false';
			endif;
		}

		/* deleteFotografia */
		if($exe=='deleteFotografia')
			{
			$ids = explode(',',sanitize_text_field($_POST['fid']));
			$err = array();
			foreach($ids as $id)
			{
				if($wpdb->delete('taxonomia',array('media_id'=>$id,'type'=>'fotografia')))
				{
					$archivo = $wpdb->get_var('select archivo from fotografias where id='.$id);
					if($wpdb->delete('fotografias',array('id'=>$id)))
					{
						shell_exec('rm -r '.APP_PATH.'/fotografias/'.md5($_SESSION['autorizado']).'/'.$archivo);
						if(!file_exists('rm -r '.APP_PATH.'/fotografias/'.md5($_SESSION['autorizado']).'/'.$archivo))
						{
							$err[] = 'true';
						}
					}
				}
				else
				{
					$err[] = 'false';
				}
			}
			if(in_array('false',$err)): $html = 'false';
			else: $html = 'true';
			endif;
		}
		
	/*
 FORM ELIMINAR
______________
*/
		/* deleteGaleria */
		if($exe=='deleteGaleria')
		{
			$sql = "select f.id id, f.archivo archivo from taxonomia t inner join fotografias f on f.id=t.media_id where t.parte_id=".$id." and t.type='fotografia'";
			error_log('SQL: '.$sql);
			
			# Eliminar Archivos #
			foreach($wpdb->get_results($sql) as $fotografia)
			{
				if($wpdb->delete('fotografias',array('id'=>$fotografia->id)))
				{
					shell_exec('rm -r '.APP_PATH.'/fotografia/'.md5($_SESSION['autorizado']).'/'.$fotografia->archivo);
					$wpdb->delete('taxonomia',array('media_id'=>$fotografia->id,'type'=>'fotografia'));
				}
			}

			# Eliminar Galeria #
			if($wpdb->delete('galeria',array('id'=>$id)))
			{
				$html = 'true';
			}
			else
			{
				$html = 'false';
			}
		}
				

	/*
 FORM PUBLICAR
______________
*/
		if($exe=='publicarGaleria')
		{
			if(sanitize_text_field($_POST['estado'])=='Pendiente') $estado = 'Publicado';
			if($wpdb->update('galeria',array('estado'=>$estado),array('id'=>$id))): $html = 'true';
			else: $html = 'false';
			endif;
		}
		
/*
 FORM FormP
______________
*/
		if($exe=='FormP')
		{
			$_SESSION['pc'] = $_SESSION['pP'];
			echo 'true';
			die;
		}

/*
 FORM FormA
______________
*/
		if($exe=='FormA')
		{
			$_SESSION['pc'] = $_SESSION['pA'];
			echo 'true';
			die;
		}

/*
 FORM FormS
__________
*/
		if($exe=='FormS')
		{
			$_SESSION['pc'] = $_SESSION['pS'];
			echo 'true';
			die;
		}


/*
 FORM FormU
______________
*/
		if($exe=='FormU')
		{
			$_SESSION['pc'] = $_SESSION['pU'];
			echo 'true';
			die;;
		}

/*
 FORM getC
______________
*/		
		
		if($_GET['exe']=='getC')
		{
			$html = "Visualizando página ".$_SESSION['pc']." de ".$_SESSION['pU']." (".$_SESSION['pT']." registros)";
		}

/*
 FORM FormBB
______________
*/
		if($exe=='FormBB')
		{
			$_SESSION['buscar'] = true;
			$_SESSION['campo']  = sanitize_text_field(trim($_POST['campo']));
			$_SESSION['str']  = sanitize_text_field(trim($_POST['str']));
			$html = 'true';
		}

/*
 FORM FormBC
___________
*/
		if($exe=='FormBC')
		{
			$_SESSION['buscar'] = false;
			$_SESSION['campo']  = null;
			$_SESSION['str']  = null;
			$html = 'true';
		}

/*
 * Salida Final
 */
		
		echo $html;
		
	}
	else
	{
		wp_redirect(APP_URL.'/escritorio/index.php');
	}
