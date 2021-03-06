<?php
	class videos extends main {

		public function __construct(){
			$this->initClass();
		}

		public function videos(){
			$id  = $this->sanitizeGetInt('id');
			$sql = str_replace(':id',$id,$this->sql_diputado_one);
			$qry = $this->db->query($sql);
			$dip = $qry->fetch(PDO::FETCH_OBJ);

			$sql = str_replace(':cuil',$dip->cuil,$this->sql_videos);
			$qry = $this->db->query($sql);
			$vid = $qry->fetchAll(PDO::FETCH_OBJ);

			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($vid);
		}

		public function videos_galeria(){
			$id = $this->sanitizeGetInt('id');
			$sql = str_replace(':id',$id,$this->sql_galeria_videos_one);
			$qry = $this->db->query($sql);
			$jsn = $qry->fetch(PDO::FETCH_ASSOC);

			$sql = str_replace(':id',$id,$this->sql_galeria_videos_archivos);
			$qry = $this->db->query($sql);
			$jsn['archivos'] = $qry->fetchAll(PDO::FETCH_ASSOC);

			header('Access-Control-Alolow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header('content-type: application/json');
			echo json_encode($jsn);
		}
	}