<?php

namespace Model;
use Config\DataBase as BD;

class AreasModel extends BD{



	function __construct($db){
		$this->database = $db;
	}

	public function set($atributo, $valor){
		$this->$atributo = $valor;
	}

	public function get($atributo){
		return $this->$atributo;
	}

	public function getAsignaturas(){

		$this->query = "SELECT id_asignatura, asignatura 
		FROM t_asignaturas ORDER BY asignatura ASC";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return array('estado' => true, 'datos' => $this->rows);
		}

		return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
	}

	public function getAsignaturaById($id){

		$this->query = "SELECT * 
		FROM t_asignaturas
		WHERE id_asignatura='$id'";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return array('estado' => true, 'datos' => $this->rows);
		}

		return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
	}



	public function getAreaPorGrados($id_grados){

		$this->query = "SELECT DISTINCT a.id_area, a.area
		FROM t_asignatura_x_area t
		INNER JOIN t_area a ON t.id_area = a.id_area  AND t.id_grado = '$id_grados' ORDER BY a.area ASC
		";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

				//var_dump($this->rows);
			return array('estado' => true, 'datos' => $this->rows);
		}

		return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
	}


	public function getAsignaturaGrados($id_grados,$area){

		$this->query = "SELECT DISTINCT ta.id_asignatura, ta.asignatura  
		FROM  t_asignatura_x_area t 
		INNER JOIN t_asignaturas ta  ON t.id_asignatura = ta.id_asignatura  
		WHERE t.id_area='$area' and t.id_grado = '$id_grados' ORDER BY ta.asignatura ASC
		";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

				//var_dump($this->rows);
			return array('estado' => true, 'datos' => $this->rows);
		}

		return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
	}

	







}
?>