<?php
	class prensa extends main {

		public function __construct(){
			$this->initClass();
		}

		public function partes(){
			$id  = $this->sanitizeGetInt('id');
			$sql = str_replace(':id',$id,$this->sql_diputado_one);
			$qry = $this->db->query($sql);
			$dip = $qry->fetch(PDO::FETCH_OBJ);

			$sql = str_replace(':cuil',$dip->cuil,$this->sql_partes);
			$qry = $this->db->query($sql);
			$prt = $qry->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($prt);
		}
		
	}