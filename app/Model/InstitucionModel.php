<?php

namespace Model;
use Config\DataBase as BD;

class InstitucionModel extends BD{



	function __construct($bd){

		$this->database=$bd;

	}


	public function getSedes()
	{
		$this->query = "SELECT * FROM sedes";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}

		return false;
	}


	public function getPeriodos()
	{
		$this->query = "SELECT * FROM periodos";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return array('estado' => true, 'datos' => $this->rows);
		}
		return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
	}

	public function getJornadas()
	{
		$this->query = "SELECT * FROM jornadas";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}
		return false;
	}
	public function getAnosLectivos(){
		$this->query = "SELECT distinct year_matricula FROM matricula";
		$this->execute_single_query();
		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}
		return false;
	}



	public function getGrados()
	{
		$this->query = "SELECT id_grado, grado FROM t_grados ";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return array('estado' => true, 'datos' => $this->rows);
		}
		return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
	}



	public function getGrupos($id_grado)
	{

		$this->query = "SELECT id_grupo, nombre_grupo FROM t_grupos WHERE id_grado = '{$id_grado}' ";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return array('estado' => true, 'datos' => $this->rows);
		}
		return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
	}

	public function getInformacionGrupo($grupo)
	{

		$this->query = "SELECT datos_institucion.nombre_inst, datos_institucion.logo_byte, docentes.primer_apellido,docentes.segundo_apellido, docentes.primer_nombre, docentes.documento, datos_institucion.logo, t_grupos.id_grupo, t_grupos.nombre_grupo, t_grupos.id_director_grupo, sedes.sede FROM t_grupos 
		INNER JOIN datos_institucion
		INNER JOIN sedes ON sedes.id_sede = t_grupos.id_sede
		INNER JOIN docentes ON docentes.id_docente = t_grupos.id_director_grupo
		WHERE id_grupo = '{$grupo}'
		" ;
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}
		return false;
	}




}
?>
