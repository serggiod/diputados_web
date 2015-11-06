<?php
	# Inicio de session.
	session_start();
	
	ini_set('max_execution_time', 300);

	# Requerir instacia de Wordpress.
	require_once '../wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{

		global $wpdb;
		$html    = null;
		$exe     = null;
		$id      = null;
		$volanta = null;
		$titulo  = null;
		$bajada  = null;
		$fecha	 = null;
		$hora    = null;
		$cabeza  = null;
		$cuerpo  = null;
		$estado  = '0';

		if($_POST['exe'])     $exe     = sanitize_text_field(trim($_POST['exe']));
		if($_GET['exe'])      $exe     = sanitize_text_field(trim($_GET['exe']));
		if($_POST['id'])      $id      = absint(sanitize_key(trim($_POST['id'])));
		if($_POST['volanta']) $volanta = sanitize_text_field(trim($_POST['volanta']));
		if($_POST['titulo'])  $titulo  = sanitize_text_field(trim($_POST['titulo']));
		if($_POST['bajada'])  $bajada  = sanitize_text_field(trim($_POST['bajada']));
		if($_POST['fecha'])
		{
			$fechao   = new DateTime(sanitize_text_field(trim($_POST['fecha'])));
			$fecha    = date_format($fechao,'Y-m-d'); 
		}
		if($_POST['hora'])    $hora    = sanitize_text_field(trim($_POST['hora']));
		if($_POST['cabeza'])  $cabeza  = sanitize_text_field(trim($_POST['cabeza']));
		if($_POST['cuerpo'])  $cuerpo  = sanitize_text_field(trim($_POST['cuerpo']));
		if($_POST['estado'])
		{
			if(sanitize_text_field(trim($_POST['estado']))=='Publicado') $estado = '0';
			if(sanitize_text_field(trim($_POST['estado']))=='Pendiente') $estado = '1';
		}

		# Insertar nuevo parte.
		if($exe=='insertTitulos')
		{
			$arr = array(
				'volanta' => $volanta,
				'titulo' => $titulo,
				'bajada' => $bajada,
				'fecha' => $fecha,
				'hora' => $hora,
				'estado_in' => '0000-00-00',
				'estado' => '0',
				'orden' => '0'
			);
			if($wpdb->insert('partes',$arr))
			{
				$_SESSION['parte_last_insert_id'] = $wpdb->get_var("select last_insert_id();");
				if($wpdb->insert('taxonomia',array('parte_id'=>$_SESSION['parte_last_insert_id'],'cuil_dipu'=>$_SESSION['autorizado'],'type'=>'noticia'))): $html = 'true';
				else: $html = 'false';
				endif;
			}
			else { $html = 'false'; }
		}

		# Actualizar titulos.
		if($exe=='updateTitulos')
		{
			$set = array(
					'volanta' => $volanta,
					'titulo' => $titulo,
					'bajada' => $bajada,
					'fecha' => $fecha,
					'hora' => $hora
			);
			$where = array(
					'id' => $_SESSION['parte_last_insert_id']
			);
			if($wpdb->update('partes',$set,$where)):$html='true';
			else:$html='false';
			endif;
		}
		
		# Actualizar el contenido del parte.
		if($exe=='updateContenido')
		{
			$set   = array(
					'cabeza'       => $cabeza,
					'cuerpo'       => $cuerpo
			);
			$where = array(
					'id'      => $_SESSION['parte_last_insert_id']
			);
			if($wpdb->update('partes',$set,$where)): $html = 'true';
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
				error_log($dir."/".$servername);
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
								'parte_id' => $_SESSION['parte_last_insert_id'],
								'media_id' => $_SESSION['fotografia_last_insert_id'],
								'cuil_dipu' => $_SESSION['autorizado'],
								'type' => 'fotografia'
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
					set_time_limit (3000);
					shell_exec('ffmpeg -i '.$_FILES['file']['tmp_name'].' -y -r 1 -f image2 '.$dir.'/'.$servername.'.jpg &');
					set_time_limit (3000);
					shell_exec('ffmpeg -i '.$_FILES['file']['tmp_name'].' -y '.$dir.'/'.$servername.'.webm &');
					set_time_limit (3000);
					shell_exec('ffmpeg -i '.$_FILES['file']['tmp_name'].' -y '.$dir.'/'.$servername.'.ogv &');
					set_time_limit (3000);
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
									'parte_id' => $_SESSION['parte_last_insert_id'],
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
					
		/* getFotosFromPartes */
		if($exe=='getFotosFromPartes')
		{
			$id = $_SESSION['parte_last_insert_id'];
			$sql   = "select f.id id,f.archivo archivo from taxonomia t inner join fotografias f on f.id=t.media_id where t.type='fotografia' and t.parte_id=".$id;
			$html  = '['."\n";
			foreach($wpdb->get_results($sql) as $fotografia)
			{
				$archivo = '<img src="'.APP_URL.'/fotografias/'.md5($_SESSION['autorizado']).'/'.$fotografia->archivo.'" border="0" width="100%"/>';
				$html .= '{\'id\':\''.$fotografia->id.'\',\'archivo\':\''.$archivo.'\'},'."\n";
			}
			$html .= ']';
			error_log($sql);
		}

		if($exe=='getVideosFromPartes')
		{
			$id = $_SESSION['parte_last_insert_id'];
			$sql   = "select v.id id,v.titulo titulo,v.archivo archivo from taxonomia t inner join videos v on v.id=t.media_id where type='video' and t.parte_id=".$id;
			$html  = '['."\n";
			foreach($wpdb->get_results($sql) as $video)
			{
				$archivo = '<img src="'.APP_URL.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.jpg" border="0" width="100%" align="center"/>';
				$html .= '{\'id\':\''.$video->id.'\',\'video\':\''.$archivo.'\'},'."\n";
			}
			$html .= ']';
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
						shell_exec('rm -r '.APP_PATH.'/fotografias/'.md5($_SESSION['autorizado']).'/'.$archivo.'.jpg');
						if(!file_exists('rm -r '.APP_PATH.'/fotografias/'.md5($_SESSION['autorizado']).'/'.$archivo.'.jpg'))
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
		
		/* deleteParte */
		if($exe=='deleteParte')
		{
			/* Eliminar Fotografias */
			$sql = "select f.id id, f.archivo archivo from taxonomia t inner join fotografias f on f.id=t.media_id where t.parte_id=".$id." and type='fotografia'";
			foreach($wpdb->get_results($id) as $fotografia)
			{
				if($wpdb->delete('taxonomia',array('media_id'=>$fotografia->id,'type'=>'fotografia')))
				{
					if($wpdb->delete('fotografias',array('id'=>$fotografia->id)))
					{
						shell_exec('rm -r '.APP_PATH.'/fotografias/'.md5($_SESSION['autorizado']).'/'.$fotografia->archivo);
					}
				}
			}
			
			/* Eliminar Videos */
			$sql = "select v.id id, v.archivo archivo from taxonomia t inner join videos v on v.id=t.media_id where t.parte_id=".$id." and type='video'";
			foreach($wpdb->get_results($sql) as $video)
			{
				if($wpdb->delete('taxonomia',array('media_id'=>$video->id,'type'=>'video')))
				{
					if($wpdb->delete('videos',array('id'=>$video->id)))
					{
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.html');
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.jpg');
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.webm');
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.ovg');
						shell_exec('rm -r '.APP_PATH.'/videos/'.md5($_SESSION['autorizado']).'/'.$video->archivo.'.mp4');
					}
				}

			}
			
			/* Eliminar Parte */
			if($wpdb->delete('taxonomia',array('parte_id'=>$id,'type'=>'noticia')))
			{
				if($wpdb->delete('partes',array('id'=>$id))){$html='true';}else{$html='false';}
			}
			else {$html='false';}
		}
		
		/* getParte */
		if($exe=='getParte')
		{
			error_log('El id recibido es: '.$id);
			$parte = $wpdb->get_row("select volanta,titulo,bajada,fecha,hora,cabeza,cuerpo from partes where id=".$id.";");
			$html = $parte->volanta.'{%SPLIT%}'.$parte->titulo.'{%SPLIT%}'.$parte->bajada.'{%SPLIT%}'.$parte->fecha.'{%SPLIT%}'.$parte->hora.'{%SPLIT%}'.$parte->cabeza.'{%SPLIT%}'.$parte->cuerpo;
			error_log('Respuesta enviada al servidor: '.$html);
			$_SESSION['parte_last_insert_id'] = $id;
			error_log('Id guadada: '.$_SESSION['parte_last_insert_id']);
		}
/*
 FORM PUBLICAR
______________
*/
		if($exe=='publicarParte')
		{

			if($wpdb->update('partes',array('estado'=>$estado),array('id'=>$id))): $html = 'true';
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
