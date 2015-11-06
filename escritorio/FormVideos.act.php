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
		$type 		= 'video';
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

		/* insertarGaleria */
		if($exe=='insertarGaleria')
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


		/* insertarVideo */
		if($exe=='insertarVideo')
		{
			set_time_limit(43200);
			if($_FILES)
			{
				$sub = md5($_SESSION['autorizado']);
				$dir  = APP_PATH.'/videos/'.$sub;
				$udir = APP_URL.'/videos/'.$sub;
				if(!file_exists($dir)) mkdir($dir);
				$realname = $_FILES["file"]["name"];
				$servername = str_replace(array('0.',' '),array('0_','_'),microtime().'_'.$realname);
				if(is_file($_FILES['file']['tmp_name']))
				{
					shell_exec('ffmpeg -i '.$_FILES['file']['tmp_name'].' -y -r 1 -f image2 '.$dir.'/'.$servername.'.jpg &');
					shell_exec('ffmpeg -i '.$_FILES['file']['tmp_name'].' -y '.$dir.'/'.$servername.'.webm &');
					shell_exec('ffmpeg -i '.$_FILES['file']['tmp_name'].' -y '.$dir.'/'.$servername.'.ogv &');
					shell_exec('ffmpeg -i '.$_FILES['file']['tmp_name'].' -y -strict experimental '.$dir.'/'.$servername.'.mp4 &');
					$file = fopen($dir.'/'.$servername.'.html','w');
					fwrite($file,'<video controls poster="'.$udir.'/'.$servername.'.jpg" width="100%" height="auto" controls="controls">');
					fwrite($file,'	<source src="'.$udir.'/'.$servername.'.webm" type="video/webm" />');
					fwrite($file,'	<source src="'.$udir.'/'.$servername.'.mp4" type="video/mp4" />');
					fwrite($file,'	<source src="'.$udir.'/'.$servername.'.ogv" type="video/ogg" />');
					fwrite($file,'</video>');
					fclose($file);
					if(is_file($dir.'/'.$servername.'.html'))
					{
						$items = array(
							'titulo' => $realname,
							'archivo' => $servername,
							'fecha' => date('Y-m-d'),
							'estado' => 1
						);
						if($wpdb->insert('videos',$items))
						{
							$_SESSION['video_last_insert_id'] = $wpdb->get_var("select last_insert_id();");
							$items = array(
								'parte_id' => $_SESSION['galeria_last_insert_id'],
								'media_id' => $_SESSION['video_last_insert_id'],
								'cuil_dipu' => $_SESSION['autorizado'],
								'type' => 'video'
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
		}

		
/*
 * getGaleria
 */		
		if($exe=='getGaleria')
		{
			$_SESSION['galeria_last_insert_id'] = $id;
			$galeria = $wpdb->get_row("select nombre,date_format(fecha,'%Y-%m-%d') fecha,descripcion from galeria where id=".$id.";");
			$html = $galeria->nombre.'{%SPLIT%}'.$galeria->fecha.'{%SPLIT%}'.$galeria->descripcion;
		}

		if($exe=='getVideosFromGaleria')
		{
			$id = $_SESSION['galeria_last_insert_id'];
			$sql   = "select v.id id,v.titulo titulo,v.archivo archivo from taxonomia t inner join videos v on v.id=t.media_id where type='video' and t.parte_id=".$id;
			$html  = '['."\n";
			foreach($wpdb->get_results($sql) as $video)
			{
				$archivo = '<img src="'.APP_URL.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.jpg" border="0" width="100%" align="center"/>';
				$html .= '{\'id\':\''.$video->id.'\',\'video\':\''.$archivo.'\'},'."\n";
			}
			$html .= ']';
		}

		if($exe=='updateGaleria')
		{
			$set = array('nombre' => $nombre,'fecha' => $fecha,'descripcion' => $descripcion);
			$w = array('id' =>$_SESSION['galeria_last_insert_id']);
			if($wpdb->update('galeria',$set,$w)): $html = 'true';
			else: $html = 'false';
			endif;
		}

		if($exe=='deleteVideo')
		{
			$ids = explode(',',sanitize_text_field($_POST['fid']));
			$err = array();
			foreach($ids as $id)
			{
				if($wpdb->delete('taxonomia',array('media_id'=>$id,'type'=>'video')))
				{
					$archivo = $wpdb->get_var('select archivo from videos where id='.$id);
					if($wpdb->delete('videos',array('id'=>$id)))
					{
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$archivo.'.html');
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$archivo.'.jpg');
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$archivo.'.webm');
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$archivo.'.ovg');
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$archivo.'.mp4');
						if(!file_exists(APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$archivo.'.html'))
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
			$sql = "select v.id id, v.archivo archivo from taxonomia t inner join videos v on v.id=t.media_id where t.parte_id=".$id." and t.type='video'";
			
			# Eliminar Archivos #
			foreach($wpdb->get_results($sql) as $video)
			{
				if($wpdb->delete('videos',array('id'=>$video->id)))
				{
					shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.html');
					shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.jpg');
					shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.webm');
					shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.ovg');
					shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.mp4');
					$wpdb->delete('taxonomia',array('media_id'=>$video->id,'type'=>'video'));
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
