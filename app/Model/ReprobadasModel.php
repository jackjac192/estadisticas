<?php

namespace Model;
use Config\DataBase as BD;

class ReprobadasModel extends BD{
	public $condicionAcademicas = "AND t_evaluacion.id_asignatura in (select id_asignatura from t_asignaturas where t_asignaturas.tipo_asig = 'A')";

	function __construct($bd){

		$this->database=$bd;

	}


	public function getEstudiantesAsiganturasRepro($grupo, $periodo, $academica)
	{	
		$stringSql = $academica==1?$this->condicionAcademicas:"";

		$this->query = "
		SELECT t_evaluacion.id_estudiante, t_evaluacion.primer_apellido, t_evaluacion.segundo_apellido, 
		t_evaluacion.primer_nombre, t_evaluacion.segundo_nombre,t_evaluacion.inasistencia_p".$periodo." as Inasistencia,
		t_evaluacion.id_asignatura, 
		(SELECT t_asignaturas.asignatura FROM t_asignaturas WHERE t_asignaturas.id_asignatura = t_evaluacion.id_asignatura) as Asignatura , 		
		(SELECT t_area.order_area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area)as order_area ,
		round(t_evaluacion.eval_".$periodo."_per,1) as valoracion FROM t_evaluacion INNER JOIN t_asignatura_x_area ON 
		t_asignatura_x_area.id_area = t_evaluacion.id_area and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND
		t_evaluacion.id_grado = t_asignatura_x_area.id_grado and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per <= 
		(SELECT maximo from valoracion where valoracion = 'Bajo')
		".$stringSql."
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		and t_evaluacion.id_subgrupo is NULL
		and t_evaluacion.eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo')
		ORDER BY primer_apellido ASC, segundo_apellido ASC, primer_nombre ASC, segundo_nombre Asc, order_area ASC; 
		";

		$this->execute_single_query();


		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		return false;
		
	}

	public function getEstudiantesAareasRepro($grupo, $periodo, $academica)
	{	
		$stringSql = $academica==1?$this->condicionAcademicas:"";	
		//Asignatura = Area
		$this->query = "

		SELECT id_estudiante, primer_apellido, segundo_apellido, primer_nombre, Asignatura,
		segundo_nombre, SUM(Inasistencia) as Inasistencia,  id_area as 'id_asignatura',id_as, count(id_area), SUM(PesoMay) PesoMa, 
		SUM(PesoIgu) PesoIg, sum(Peso), Area, order_area , IF(SUM(Peso)=100, round(sum(valoracion * (Peso/100)),1),
		ROUND(sum(valoracion)/count(id_area),1)) valoracion FROM ( SELECT t_evaluacion.id_estudiante, t_evaluacion.primer_apellido, 
		t_evaluacion.segundo_apellido, t_evaluacion.primer_nombre, t_evaluacion.inasistencia_p".$periodo." as Inasistencia, t_evaluacion.segundo_nombre, t_evaluacion.id_area,t_evaluacion.id_asignatura 
		as id_as, 
		(SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Asignatura
		, t_evaluacion.id_grado, t_evaluacion.id_grupo, t_asignatura_x_area.peso_frente_area>0 as 
		PesoMay, t_asignatura_x_area.peso_frente_area=0 or ISNULL(t_asignatura_x_area.peso_frente_area) as 
		PesoIgu, t_asignatura_x_area.peso_frente_area as Peso, (SELECT t_area.area FROM t_area WHERE 
		t_area.id_area = t_evaluacion.id_area) as Area, (SELECT t_area.order_area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as 
		order_area ,t_evaluacion.eval_".$periodo."_per as Valoracion FROM t_evaluacion INNER JOIN t_asignatura_x_area ON 
		t_asignatura_x_area.id_area = t_evaluacion.id_area and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND 
		t_evaluacion.id_grado = t_asignatura_x_area.id_grado and t_evaluacion.id_grupo = '{$grupo}'
		and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo')
		".$stringSql."
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		and t_evaluacion.id_subgrupo is NULL
		ORDER BY primer_apellido ASC, segundo_apellido ASC, primer_nombre ASC, segundo_nombre Asc, order_area ASC) as 
		t where Valoracion <= (SELECT maximo from valoracion where valoracion = 'Bajo')
		GROUP BY id_area, id_estudiante ORDER BY id_estudiante, id_area; 
		";

		$this->execute_single_query();

		//print_r($this->query);
		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		
		return false;
		
	}


	public function getEstudiantesRepro($grupo, $periodo, $academica, $operador, $num)
	{	
		$stringSql = $academica==1?$this->condicionAcademicas:"";
		$op = $operador=="1"?">=":($operador=="0"?"=":"<=");
		
		$this->query = "
		SELECT * FROM		(SELECT 
		t_evaluacion.id_estudiante, t_evaluacion.primer_apellido, t_evaluacion.segundo_apellido, t_evaluacion.primer_nombre, 
		t_evaluacion.segundo_nombre,
		count(t_evaluacion.id_asignatura) as numeroAsignaturas		
		FROM t_evaluacion	INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area  = t_evaluacion.id_area
		and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND
		t_evaluacion.id_grado = t_asignatura_x_area.id_grado and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per>=(SELECT minimo from valoracion where valoracion = 'Bajo')
		".$stringSql."		
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		and t_evaluacion.id_subgrupo is NULL
		and t_evaluacion.eval_".$periodo."_per <= (SELECT maximo from valoracion where valoracion = 'Bajo')
		GROUP BY id_estudiante ORDER BY primer_apellido, id_estudiante ASC ) as d WHERE numeroAsignaturas $op '{$num}';
		";

		$this->execute_single_query();


		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		return false;
		
	}	

	public function getEstudiantesReproA($grupo, $periodo, $academica, $operador, $num)
	{	
		$stringSql = $academica==1?$this->condicionAcademicas:"";
		$op = $operador=="1"?">=":($operador=="0"?"=":"<=");
		
		$this->query = "
		SELECT * from (select id_estudiante, primer_apellido, segundo_apellido, primer_nombre, segundo_nombre, Inasistencia,id_asignatura, COUNT(id_estudiante) numAreas FROM 
		(SELECT id_estudiante, primer_apellido, segundo_apellido, primer_nombre, Asignatura, segundo_nombre, 
		SUM(Inasistencia) as Inasistencia, id_area as 'id_asignatura',  Area, order_area , IF(SUM(Peso)=100, round(sum(valoracion * (Peso/100)),1), 
		ROUND(sum(valoracion)/count(id_area),1)) valoracion FROM ( SELECT t_evaluacion.id_estudiante, 
		t_evaluacion.primer_apellido, t_evaluacion.segundo_apellido, t_evaluacion.primer_nombre, t_evaluacion.inasistencia_p1 as
		Inasistencia, t_evaluacion.segundo_nombre, t_evaluacion.id_area,t_evaluacion.id_asignatura as id_as, 
		(SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Asignatura , t_evaluacion.id_grado, 
		t_evaluacion.id_grupo, t_asignatura_x_area.peso_frente_area>0 as PesoMay, t_asignatura_x_area.peso_frente_area=0 or 
		ISNULL(t_asignatura_x_area.peso_frente_area) as PesoIgu, t_asignatura_x_area.peso_frente_area as Peso, 
		(SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Area, (SELECT t_area.order_area 
		FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as order_area ,t_evaluacion.eval_".$periodo."_per as Valoracion FROM 
		t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area and 
		t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado
		and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo') 
		".$stringSql."	
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		and t_evaluacion.id_subgrupo is NULL
		ORDER BY primer_apellido ASC, segundo_apellido ASC, primer_nombre ASC, segundo_nombre Asc, order_area ASC) as t 
		where Valoracion <= (SELECT maximo from valoracion where valoracion = 'Bajo') GROUP BY id_area, id_estudiante 
		ORDER BY id_estudiante, id_area) as t GROUP BY id_estudiante)as t where numAreas $op '{$num}'		

		";

		$this->execute_single_query();
		//print_r($this->query);
		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		//var_dump($this->rows);
		return false;		
	}
}




?>