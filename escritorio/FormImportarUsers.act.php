<?php
	# Inicio de session.
	session_start();

	# Requerir instacia de Wordpress.
	require_once '/home/sdominguez/Desarrollo_Web/diputados_web/public_html/wp-config.php';

	# Requerir funciones de administración.
	if(is_user_logged_in() && current_user_can('administrator'))
	{

		global $wpdb;
		$html = 'false';
		$exe  = null;

		if($_POST['exe'])
		{
			$exe = sanitize_text_field(trim($_POST['exe']));
		}
		
				
		if($exe=='importaruno')
		{
			$cuil = sanitize_text_field(trim($_POST['cuil']));
			$check = $wpdb->get_var("select concat('true') from wp_users where user_login='".$cuil."'");
			
			if($check=='true')
			{
				$html = 'exists';
			}
			else
			{
				
				$sql  = "select p.per_nombres nombre,p.per_apellidos apellido,p.per_cuil user_login,usu_pass user_pass,p.per_cuil user_nicename,per_email user_email,concat(per_nombres,' ',per_apellidos) display_name from personas p inner join usuarios_jujuy uj on uj.per_cuil=p.per_cuil where p.per_cuil='".$cuil."'";
				$p    = $wpdb->get_row($sql,OBJECT);
				$arr  =	array(
					'ID' => null,
					'user_login' => $p->user_login,
					'user_pass' => $p->user_pass,
					'user_email' => $p->user_email,
					'user_nicename' => $p->user_nicename,
					'user_url' => null,
					'user_registered' => null,
					'user_activation_key' => null,
					'user_status' => 1,
					'display_name' => $p->display_name
				);

				$r = $wpdb->insert('wp_users',$arr);

				if($r)
				{
					$ID = $wpdb->get_var("select last_insert_id();");
					$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'wp_capabilities','meta_value'=>'a:1:{s:13:"administrator";b:1;}'));
					$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'first_name','meta_value'=>$p->nombre));
					$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'last_name','meta_value'=>$p->apellido));
					$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'description','meta_value'=>'Usuario de importado desde SILEJU.'));
					$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'wp_user_level','meta_value'=>'10'));
					$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'wp_super_user','meta_value'=>'false'));
					$html = 'true';
				}
			}
		}


		if($exe=='importartodos')
		{
			$sql = "select p.per_nombres nombre,p.per_apellidos apellido,p.per_cuil user_login,usu_pass user_pass,p.per_cuil user_nicename,per_email user_email,concat(per_nombres,' ',per_apellidos) display_name from personas p inner join usuarios_jujuy uj on uj.per_cuil=p.per_cuil order by p.per_apellidos asc";
			$err = array();

			foreach($wpdb->get_results($sql,OBJECT) as $p)
			{
				if($wpdb->get_var("select ID from wp_users where user_login=='".$p->user_login."'"))
				{}
				else
				{
					$arr  =	array(
						'ID' => null,
						'user_login' => $p->user_login,
						'user_pass' => $p->user_pass,
						'user_email' => $p->user_email,
						'user_nicename' => $p->user_nicename,
						'user_url' => null,
						'user_registered' => null,
						'user_activation_key' => null,
						'user_status' => 1,
						'display_name' => $p->display_name
					);
					$r = $wpdb->insert('wp_users',$arr);
					if($r)
					{
						$ID = $wpdb->get_var("select last_insert_id();");
						$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'wp_capabilities','meta_value'=>'a:1:{s:13:"administrator";b:1;}'));
						$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'first_name','meta_value'=>$p->nombre));
						$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'last_name','meta_value'=>$p->apellido));
						$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'description','meta_value'=>'Usuario de importado desde SILEJU.'));
						$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'wp_user_level','meta_value'=>'10'));
						$wpdb->insert('wp_usermeta',array('umeta_id'=>null,'user_id'=>$ID,'meta_key'=>'wp_super_user','meta_value'=>'false'));
						$err[] = 'true';
					}
					else
					{
						$err[] = 'false';
					}
				}
			}
			
			if(in_array('false',$err)) { $html = 'false'; }
			else { $html = 'true'; }
			
		}

		if($exe=='eliminaruno')
		{
			$user_login = sanitize_text_field(trim($_POST['cuil']));
			$ID   = $wpdb->get_var("select ID from wp_users where user_login='".$user_login."'");
			$chek = $wpdb->get_var("select meta_value from wp_usermeta where user_id='".$ID."' and meta_key='wp_super_user'");
			
			if($chek=='true')
			{
				$html = 'issuper';
			}
			
			if($chek=='false')
			{
				$r = $wpdb->delete('wp_users',array('ID'=>$ID));
				if($r)
				{
					$wpdb->delete('wp_usermeta',array('user_id'=>$ID));
					$html = 'true';
				}
				else
				{
					$html = 'false';
				}
			}
		}

		if($exe=='eliminartodos')
		{
			$err = array();
			$IDs = $wpdb->get_col("select user_id ID from wp_usermeta where meta_key='wp_super_user' and meta_value='false' group by user_id");
			foreach($IDs as $ID)
			{
				$r = $wpdb->delete('wp_users',array('ID'=>$ID));
				if($r)
				{
					$wpdb->delete('wp_usermeta',array('user_id'=>$ID));
					$err[] = 'true';
				}
				else
				{
					$err[] = 'false';
				}
			}
			if(in_array('false',$err))
			{
				$html = 'false';
			}
			else
			{
				$html = 'true';
			}
		}
		
		echo $html;
		
	}
	else
	{
		wp_redirect(APP_URL.'/escritorio/index.php');
	}
