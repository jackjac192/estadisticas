<?php

namespace Model;
use Config\DataBase as BD;

class EstudiantesModel extends BD{

	public static $DESERT=9;
	public static $RETIR=47;
	public static $TRANSL=54;
	public static $REPRO=44;
	public static $APROB=38;
	
	function __construct($bd){

		$this->database=$bd;

	}


	public function getTitulosGenero()
	{			

		$this->query = "
		SELECT t_grados.id_grado, CONCAT(LEFT(t_grados.grado,2),'-H') H, CONCAT(LEFT(t_grados.grado,2),'-M') M FROM t_grados;
		";

		$this->execute_single_query();
		if($this->resultado->num_rows > 0){
			$this->get_result_query();
			return $this->rows;
		}
		return false;		
	}


	public function getGenero($jornadas, $sexo, $ano)
	{			
		$this->query = "
		SELECT id_grado, grado, COUNT(genero) cantidad, edad FROM(SELECT DISTINCT students.idstudents, students.primer_apellido, students.primer_nombre, students.genero, TIMESTAMPDIFF(YEAR, students.fecha_nacimiento, CURDATE()) edad,
		(SELECT t_grados.grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) grado,
		(SELECT t_grados.id_grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) id_grado,
		(SELECT jornadas.jornada FROM jornadas WHERE jornadas.id_jornada = students.id_jornada) jornada
		FROM students 
		INNER JOIN matricula ON matricula.idstudents = students.idstudents 
		and matricula.id_jornada = '{$jornadas}' and matricula.year_matricula = '{$ano}') as t WHERE genero = '{$sexo}' AND edad is NOT NULL AND grado IS NOT NULL GROUP BY grado, edad ;
		";

		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		//var_dump($this->rows);
		return false;
		
	}

	public function getMatriculadosInicial($jornadas, $sexo, $ano){
		$this->query = "
		SELECT id_grado, grado, COUNT(genero) cantidad FROM(SELECT DISTINCT students.idstudents, students.primer_apellido, students.primer_nombre, students.genero, TIMESTAMPDIFF(YEAR, students.fecha_nacimiento, CURDATE()) edad,
		(SELECT t_grados.grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) grado,
		(SELECT t_grados.id_grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) id_grado,
		(SELECT jornadas.jornada FROM jornadas WHERE jornadas.id_jornada = students.id_jornada) jornada
		FROM students 
		INNER JOIN matricula ON matricula.idstudents = students.idstudents 
		and matricula.id_jornada = '{$jornadas}' and matricula.year_matricula = '{$ano}') as t WHERE genero = '{$sexo}' AND grado IS NOT NULL GROUP BY grado ORDER BY id_grado ASC;
		";
		$this->execute_single_query();
		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}
		//var_dump($this->rows);
		return false;
	}

	public function getMatriculadosFinal($jornadas, $sexo, $ano){
		$this->query = "
		SELECT id_grado, grado, COUNT(genero) cantidad FROM(SELECT DISTINCT students.idstudents, students.primer_apellido, students.primer_nombre, students.genero, TIMESTAMPDIFF(YEAR, students.fecha_nacimiento, CURDATE()) edad,
		(SELECT t_grados.grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) grado,
		(SELECT t_grados.id_grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) id_grado,
		(SELECT jornadas.jornada FROM jornadas WHERE jornadas.id_jornada = students.id_jornada) jornada
		FROM students 
		INNER JOIN matricula ON matricula.idstudents = students.idstudents and students.idstudents NOT IN (select nov.idstudents 
		from novedades_x_estudiante_fecha as nov 
		WHERE nov.id_novedad = ".self::$RETIR." OR nov.id_novedad = ".self::$DESERT." OR nov.id_novedad = ".self::$TRANSL." )
		and matricula.id_jornada   =  '{$jornadas}' and matricula.year_matricula = '{$ano}') as t WHERE genero = '{$sexo}' AND grado IS NOT NULL GROUP BY grado ORDER BY id_grado ASC;
		";
		$this->execute_single_query();
		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}
		//var_dump($this->rows);
		return false;
	}

	public function getMatriculadosNovedades($jornadas, $sexo, $ano, $novedad){
		$this->query = "
		SELECT id_grado, grado, COUNT(genero) cantidad FROM(SELECT DISTINCT students.idstudents, students.primer_apellido, students.primer_nombre, students.genero, TIMESTAMPDIFF(YEAR, students.fecha_nacimiento, CURDATE()) edad,
		(SELECT t_grados.grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) grado,
		(SELECT t_grados.id_grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) id_grado,
		(SELECT jornadas.jornada FROM jornadas WHERE jornadas.id_jornada = students.id_jornada) jornada
		FROM students 
		INNER JOIN matricula ON matricula.idstudents = students.idstudents and students.idstudents IN (select nov.idstudents from novedades_x_estudiante_fecha as nov WHERE nov.id_novedad = '{$novedad}' )
		and matricula.id_jornada = '{$jornadas}' and matricula.year_matricula = '{$ano}' ) as t WHERE genero = '{$sexo}' and grado IS NOT NULL GROUP BY grado ORDER BY id_grado ASC;
		";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}
		//var_dump($this->rows);
		return false;
	}

	public function getMatriculadosGrupos($jornadas, $sexo, $ano){

		$this->query = "
		SELECT id_grado, grado, COUNT(genero) cantidad FROM(SELECT DISTINCT students.idstudents, students.primer_apellido, students.primer_nombre, students.genero, TIMESTAMPDIFF(YEAR, students.fecha_nacimiento, CURDATE()) edad,
		(SELECT t_grados.grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) grado,
		(SELECT t_grados.id_grado FROM t_grados 
		INNER JOIN t_grupos ON t_grupos.id_grado= t_grados.id_grado WHERE t_grupos.id_grupo = students.id_grupo) id_grado,
		(SELECT jornadas.jornada FROM jornadas WHERE jornadas.id_jornada = students.id_jornada) jornada
		FROM students 
		INNER JOIN t_estudiante_grupo ON t_estudiante_grupo.idstudent = students.idstudents and t_estudiante_grupo.entorno = '{$ano}'
		INNER JOIN matricula ON matricula.idstudents = students.idstudents 
		and matricula.id_jornada = '{$jornadas}' and matricula.year_matricula = '{$ano}') as t WHERE genero = '{$sexo}' AND grado IS 
		NOT NULL GROUP BY grado ORDER BY id_grado ASC
		;
		";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}
		//var_dump($this->rows);
		return false;
	}


	public function getCantidadGrupos($jornadas, $ano){

		$this->query = "
		SELECT t_grados.id_grado, t_grados.grado, count(t_grupos.id_grupo) cantidad FROM t_grados
		INNER JOIN t_grupos ON t_grupos.id_grado = t_grados.id_grado and t_grupos.jornada= '{$jornadas}' 
		and t_grupos.entorno = '{$ano}' GROUP BY t_grados.id_grado
		";
		$this->execute_single_query();

		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;
		}
		//var_dump($this->rows);
		return false;
	}






}