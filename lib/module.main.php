<?php
	class main {

		protected $db   = null;
		protected $html = null;
		protected $url  = null;
		protected $leg  = null;
		protected $path = null;
		protected $sql_index        = "select d.id,d.nombre,d.apellido from diputado d where d.estado=1 order by d.apellido asc limit 48;";
		protected $sql_diputado_one = "select d.nombre,d.apellido,d.archivo,year(d.desde) desde,year(d.hasta) hasta,d.telefono,d.paginaweb,d.facebook,d.twitter,d.youtube,b.archivo escudo,b.nombre bloque,d.email,d.cuil,md5(d.cuil) usrdir from diputado d inner join bloque b on b.id=d.bloque_id where d.id=:id and d.estado=1 limit 1;";
		protected $sql_comision		= "select c.nombre nombre, ca.cargo from comision_autoridades ca inner join comision c on c.id=ca.comision_id where ca.diputado_id=:id and ca.estado=1 order by ca.cargo desc;";
		protected $sql_partes		= "select p.id id,p.titulo titulo, p.volanta volanta, p.bajada bajada, p.cabeza cabeza, date_format(fecha,'%d-%m-%Y') fecha,hora, estado, (select f.archivo from taxonomia ta inner join fotografias f on f.id=ta.media_id where ta.parte_id=p.id limit 1) fotografia from taxonomia t inner join partes p on p.id=t.parte_id where t.media_id is null and t.cuil_dipu=':cuil' and t.type='noticia' and p.estado=1 order by p.fecha desc, p.hora desc ";
		protected $sql_partes_one 	= "select p.id id,p.titulo titulo, p.volanta volanta, p.bajada bajada, p.cabeza cabeza, p.cuerpo cuerpo, date_format(p.fecha,'%d-%m-%Y') fecha,hora from partes p where p.id=:id and p.estado=1;";
		protected $sql_partes_fotos = "select f.archivo from taxonomia t inner join fotografias f on f.id=t.media_id where t.parte_id=:id and t.type='fotografia';";
		protected $sql_partes_videos= "select v.archivo from taxonomia t inner join videos v on v.id=t.media_id where t.parte_id=:id and t.type='video';";
		protected $sql_fotos		= "select g.id id,g.nombre nombre,g.descripcion descripcion,date_format(g.fecha,'%d-%m-%Y') fecha from galeria g where g.type='fotografia' and g.estado='Publicado' and cuil_dipu=':cuil' order by g.fecha desc";
		protected $sql_videos		= "select g.id id,g.nombre nombre,g.descripcion descripcion,date_format(g.fecha,'%d-%m-%Y') fecha from galeria g where g.type='video' and g.estado='Publicado' and cuil_dipu=':cuil' order by g.fecha desc";
		protected $sql_galeria_foto_one 	 = "select g.nombre nombre,g.descripcion descripcion, date_format(g.fecha,'%d-%m-%Y') fecha from galeria g where id=:id;";
		protected $sql_galeria_foto_archivos = "select f.archivo archivo from taxonomia t inner join fotografias f on f.id=t.media_id where t.parte_id=:id and t.type='fotografia';";
		protected $sql_galeria_videos_one 	 = "select g.nombre nombre,g.descripcion descripcion, date_format(g.fecha,'%d-%m-%Y') fecha from galeria g where id=:id;";
		protected $sql_galeria_videos_archivos = "select v.archivo archivo from taxonomia t inner join videos v on v.id=t.media_id where t.parte_id=:id and t.type='video';";

		protected function initClass(){
			$this->url  = baseUrl;
			$this->leg  = legUrl;
			$this->path = basePath;
			$this->html = new html;
			$this->db   = new PDO(dbDriv.':host='.dbHost.';dbname='.dbName.';charset=utf8',dbUser,dbPass);
		}

		public function sanitizeGetInt($key=null){
			$_GET[$key] = filter_var($_GET[$key],FILTER_SANITIZE_NUMBER_INT);
			if(!filter_var($_GET[$key],FILTER_VALIDATE_INT)){
				$_GET[$key] = null;
			}
			return $_GET[$key];
		}

		public function sanitizeGetString($key=null){
			$_GET[$key] = filter_var($_GET[$key],FILTER_SANITIZE_STRING);
			if(!filter_var($_GET[$key],FILTER_VALIDATE_REGEXP,array('options'=>array('regexp'=>'/^[0-9a-zA-Z@áéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ\ \s\,\.\-\_]+$/')))){
				$_GET[$key] = null;
			}
			return utf8_encode($_GET[$key]);
		}

		public function sanitizePostInt($key=null){
			$_POST[$key] = filter_var($_POST[$key],FILTER_SANITIZE_NUMBER_INT);
			if(!filter_var($_POST[$key],FILTER_VALIDATE_INT)){
				$_POST[$key] = null;
			}
			return $_POST[$key];
		}

		public function sanitizePostString($key=null){
			$_POST[$key] = filter_var($_POST[$key],FILTER_SANITIZE_STRING);
			if(!filter_var($_POST[$key],FILTER_VALIDATE_REGEXP,array('options'=>array('regexp'=>'/^[0-9a-zA-ZáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ\ \s\,\.]+$/')))){
				$_POST[$key] = null;
			}
			return utf8_encode($_POST[$key]);
		}

		public function sanitizePostDate($key=null){
			$_POST[$key] = filter_var($_POST[$key],FILTER_SANITIZE_STRING);
			if(!filter_var($_POST[$key],FILTER_VALIDATE_REGEXP,array('options'=>array('regexp'=>'/^(0[1-9]|1[0-9]|2[0-9]|3[0-1])-(0[1-9]|1[0-2])-(19[7-9][0-9]|20[0-2][0-9])$/')))){
				$_POST[$key] = null;
			}
			return $_POST[$key];
		}

		public function msgError($msg=null){
			$html  = $this->html->div($msg,'msgError');
			return $html;
		}

		public function msgInfo($msg=null){
			$html  = $this->html->div($msg,'msgInfo');
			return $html;
		}

		public function msgSuccess($msg=null){
			$html = $this->html->div($msg,'msgSuccess');
			return $html;
		}

		public function msgAlert($msg=null){
			$html  = $this->html->div($msg,'msgAlert');
			return $html;
		}

		public function diputado_data(){
			$this-> initClass();
			$id   = $this->sanitizeGetInt('id');
			$sql  = str_replace(':id',$id,$this->sql_diputado_one);
			$query= $this->db->query($sql);
			$dip  = $query->fetch(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($dip);
		}

		public function diputado_comisiones(){
			$this-> initClass();
			$id   = $this->sanitizeGetInt('id');
			$sql  = str_replace(':id',$id,$this->sql_comision);
			$query=$this->db->query("select c.nombre nombre, ca.cargo from comision_autoridades ca inner join comision c on c.id=ca.comision_id where ca.diputado_id=61 and ca.estado=1 order by ca.cargo desc;");
			$com  = $query->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($com);
		}

		public function diputado_tres_noticias(){
			$this-> initClass();
			$id   = $this->sanitizeGetInt('id');
			$sql  = str_replace(':id',$id,$this->sql_diputado_one);
			$query= $this->db->query($sql);
			$dip  = $query->fetch(PDO::FETCH_OBJ);
			
			$sql  = str_replace(':cuil',$dip->cuil,$this->sql_partes);
			$query= $this->db->query($sql.' limit 3;');
			$not  = $query->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($not);
		}

		public function noticia_leerMas(){
			$this->initClass();
			$id 	= $this->sanitizeGetInt('id');
			$sql 	= str_replace(':id',$id,$this->sql_partes_one);
			$query	= $this->db->query($sql);
			$not  	= $query->fetch(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($not);	
		}

		public function noticia_leerMas_fotos(){
			$this->initClass();
			$id 	= $this->sanitizegetInt('id');
			$sql 	= str_replace(':id',$id,$this->sql_partes_fotos);
			$query 	= $this->db->query($sql);
			$fot 	= $query->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($fot);
		}

		public function noticia_leerMas_videos(){
			$this->initClass();
			$id 	= $this->sanitizeGetInt('id');
			$sql 	= str_replace(':id',$id,$this->sql_partes_videos);
			$query 	= $this->db->query($sql);
			$vid 	= $query->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($vid);
		}

		public function index(){
			$this->initClass();
			$sql 	= $this->sql_index;
			$query 	= $this->db->query($sql);
			$dip 	= $query->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($dip);
		}

		public function email(){
			$this->initClass();
			$json 	= array();
			$id 	= $this->sanitizeGetInt('id');
			$nombre = $this->sanitizeGetString('nombre');
			$email  = $this->sanitizeGetString('email');
			$asunto = $this->sanitizeGetString('asunto');
			$mensaje= $this->sanitizeGetString('mensaje');

			$sql  = str_replace(':id',$id,$this->sql_diputado_one);
			$query= $this->db->query($sql);
			$dip  = $query->fetch(PDO::FETCH_OBJ);
			$para = $dip->email; 

			if(mail($dip->email,$asunto,$mensaje,"From: ".$email."\r\n"."Reply-To: ".$email. "\r\n"."X-Mailer: PHP/".phpversion())){
				if(mail($email,'Consulta enviada.','Su consulta fue enviada correctamente, en breve le enviaran una respuesta.',"From: ".$dip->email."\r\n"."Reply-To: ".$dip->email. "\r\n"."X-Mailer: PHP/".phpversion())){
					$json['event'] = true;
					$json['msg']   = 'El mensaje se envio en forma correcta.';
				} else {
					$json['event'] = false;
					$json['msg']   = 'Su cuenta de correo no fue comprobada.';
				}
			} else {
				$json['event'] = false;
				$json['msg']   = 'El mensaje no pudo ser enviado.';
			}

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($json);
		}
	}