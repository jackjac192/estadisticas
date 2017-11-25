<?php

namespace Model;
use Config\DataBase as BD;

class CerrarInformeModel extends BD{

	private $array_promedios_estudiantes;
	private $array_promedios_asignaturas;
	private $id_grupo;
	private $ano_lectivo;


	function __construct($bd){	

		$this->database=$bd;
		$this->array_promedios_estudiantes = [];
		$this->array_promedios_asignaturas = [];
		$this->id_grupo = 0;	
		$this->ano_lectivo = date("Y");

	}

	public function setArrayInforme($params){
		extract($params);
		$this->array_promedios_estudiantes = $array_puesto_promedio_acumulado;
		$this->array_promedios_asignaturas = $array_estudiantes_acumulados_asignaturas;
		$this->id_grupo = $id_grupo;


		$this->insertarPromediosEstudiantes();
		$this->insertarPromediosEstudiantesAsignaturas();
	}

	public function insertarPromediosEstudiantes()

	{	

		foreach ($this->array_promedios_estudiantes as $key_id_estudiante => $estudiante_) {
			$this->query = " INSERT INTO informe_final_general ( id_estudiante, id_grupo, promedio, puesto, ano_lectivo, promovido)
			VALUES ('$key_id_estudiante', '$this->id_grupo', '$estudiante_[pgg]', '$estudiante_[puesto]', '$this->ano_lectivo','')
			";
			$this->execute_single_query();
		}		

		
	}

	public function getIdInforme($id_estudiante, $id_grupo)
	{	
		$ano_lectivo = date("Y");
		$this->query = "SELECT id_informe FROM informe_final_general
		WHERE id_estudiante = '{$id_estudiante}' AND id_grupo = '{$id_grupo}'	AND ano_lectivo = '{$ano_lectivo}'				
		";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();
			return $this->rows;
		}
		return false;		
	}

	public function insertarPromediosEstudiantesAsignaturas()
	{				
		foreach ($this->array_promedios_asignaturas as $key_id_estudiante => $asignaturas_estudiante) {
			foreach ($asignaturas_estudiante as $key_id_asignatura => $asignaturas_) {
				$id_informe = $this->getIdInforme($key_id_estudiante, $this->id_grupo)[0];
				
				$this->query = " INSERT INTO informe_final_asignaturas ( id_informe, id_asignatura, valoracion)
				VALUES ('$id_informe[id_informe]', '$key_id_asignatura','$asignaturas_[valoracion]')
				";
				$this->execute_single_query();
			}
			
		}	
	}

	

}
?>