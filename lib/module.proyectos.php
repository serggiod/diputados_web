<?php
	class proyectos extends main{

		protected $sql_proy_coo = "select id_expediente id,exp_nro expediente,date_format(exp_fingreso,'%d-%m-%Y') fecha,exp_titulo proyecto,(select fsecu_detalle tipo from formas_secu where id_fsecu=py.id_fsecu) tipo FROM expedientes AS py inner JOIN (SELECT per_apellidos,per_nombres,id_expediente as id_p,personas.per_cuil,expp_tipo FROM exp_personas INNER JOIN personas ON exp_personas.per_cuil=personas.per_cuil) AS p ON id_expediente=id_p WHERE p.per_cuil=':cuil' AND exp_titulo!='Reserva' ORDER BY LPAD(LEFT(py.exp_nro,LOCATE('-',py.exp_nro)-1),9,0) desc ";
		protected $sql_proy_fir	= "select id_expediente id,exp_nro expediente,date_format(exp_fingreso,'%d-%m-%Y') fecha,exp_titulo proyecto,(select fsecu_detalle tipo from formas_secu where id_fsecu=py.id_fsecu) tipo FROM expedientes AS py inner JOIN (SELECT per_apellidos,per_nombres,id_expediente as id_p,personas.per_cuil,expp_tipo FROM exp_personas INNER JOIN personas ON exp_personas.per_cuil=personas.per_cuil) AS p ON id_expediente=id_p WHERE p.per_cuil=':cuil' AND exp_titulo!='Reserva' AND p.expp_tipo='Autor' ORDER BY LPAD(LEFT(py.exp_nro,LOCATE('-',py.exp_nro)-1),9,0) desc ";

		public function __construct(){
			$this->initClass();
		}

		public function cofirmados(){
			$id  = $this->sanitizeGetInt('id');
			$sql = str_replace(':id',$id,$this->sql_diputado_one);
			$qry = $this->db->query($sql) ;
			$dip = $qry->fetch(PDO::FETCH_OBJ);

			$sql = str_replace(':cuil',$dip->cuil,$this->sql_proy_coo);
			$qry = $this->db->query($sql);
			$pry = $qry->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET,POST');
			header('Content-Type: application/json');
			echo json_encode($pry);
		}

		public function firmados(){
			$id  = $this->sanitizeGetInt('id');
			$sql = str_replace(':id',$id,$this->sql_diputado_one);
			$qry = $this->db->query($sql);
			$dip = $qry->fetch(PDO::FETCH_OBJ);

			$sql = str_replace(':cuil',$dip->cuil,$this->sql_proy_fir);
			$qry = $this->db->query($sql);
			$pry = $qry->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET,POST');
			header('Content-Type: application/json');
			echo json_encode($pry);
		}
	}