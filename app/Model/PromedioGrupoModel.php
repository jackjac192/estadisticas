<?php

namespace Model;
use Config\DataBase as BD;

class PromedioGrupoModel extends BD{
	public $condicionAcademicas = "AND t_evaluacion.id_asignatura in (select id_asignatura from t_asignaturas where t_asignaturas.tipo_asig = 'A')";


	function __construct($bd){

		$this->database=$bd;

	}


	public function setTextQuery($grupo, $periodo) {
		return "
		sum(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		>= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and 
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior')) as S , 

		sum(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per) 
		>= (SELECT minimo from valoracion WHERE valoracion = 'Alto') and 
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		<= (SELECT maximo from valoracion WHERE valoracion = 'Alto')) as A , 

		sum(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per) 
		>= (SELECT minimo from valoracion WHERE valoracion = 'Basico') and 
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per) 
		<= (SELECT maximo from valoracion WHERE valoracion = 'Basico')) as B , 

		sum(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per) 
		<= (SELECT maximo from valoracion WHERE valoracion = 'Bajo') ) as V , 

		sum(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		>= (SELECT minimo from valoracion where valoracion = 'Bajo')) as TAV,

		ROUND(((SUM(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		)) /
		sum(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		>= (SELECT minimo from valoracion where valoracion = 'Bajo'))),2) as Promedio, 
		ROUND((sum(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		) / SUM(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		>= (SELECT minimo from valoracion where valoracion = 'Bajo'))),2) 
		as pgg, 
		ROUND((sum(
		eval_".$periodo."_per
		) / SUM(
		eval_".$periodo."_per
		>= (SELECT minimo from valoracion where valoracion = 'Bajo'))),2) 
		as pgg_sin_super,
		(SELECT valoracion.val FROM valoracion WHERE ROUND(((SUM(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		)) / sum(
		FUNC_SELECT_VAL(id_estudiante,id_asignatura,{$grupo},{$periodo},eval_".$periodo."_per)
		>= (SELECT minimo from valoracion where valoracion = 'Bajo') )),2) BETWEEN valoracion.minimo AND valoracion.maximo)
		as Desempeno 
		";
	}

	public function getPromedioPuestos($grupo, $periodo, $academica)
	{	
		$stringSql = $academica==1?$this->condicionAcademicas:"";

		$this->query = "
		SELECT id_estudiante, 
		primer_apellido, segundo_apellido, segundo_nombre, primer_nombre, 
		".$this->setTextQuery($grupo,$periodo)."
		FROM 
		(SELECT t_evaluacion.id_estudiante, t_evaluacion.primer_apellido, t_evaluacion.segundo_apellido, t_evaluacion.segundo_nombre, 
		t_evaluacion.primer_nombre, t_evaluacion.eval_".$periodo."_per,t_evaluacion.id_asignatura 
		FROM t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area and 
		t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado 
		and t_evaluacion.id_grupo = '{$grupo}'  and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo')
		".$stringSql." 
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		
		) 
		as t GROUP BY id_estudiante ORDER BY Tav DESC , Promedio DESC ; 		
		";

		$this->execute_single_query();


		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		return false;
		
	}

	public function getPromedioPuestosReprobados($grupo, $periodo, $academica){
		$stringSql = $academica==1?$this->condicionAcademicas:"";

		$this->query = "
		select * from 
		(
		SELECT id_estudiante, 
		primer_apellido, segundo_apellido, segundo_nombre, primer_nombre, 
		".$this->setTextQuery($grupo, $periodo)."		
		FROM 
		(SELECT t_evaluacion.id_estudiante, t_evaluacion.primer_apellido, t_evaluacion.segundo_apellido, t_evaluacion.segundo_nombre, 
		t_evaluacion.primer_nombre, t_evaluacion.eval_".$periodo."_per,t_evaluacion.id_asignatura
		FROM t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area and 
		t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado 
		and t_evaluacion.id_grupo = '{$grupo}'  and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo')
		".$stringSql." 
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		
		) 
		as t GROUP BY id_estudiante ORDER BY Tav DESC , Promedio DESC 
		) as p
		WHERE Promedio <= (SELECT maximo from valoracion where valoracion = 'Bajo'); 
		";

		$this->execute_single_query();


		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		return false;
	}

	public function getPromedioPuestosAreas($grupo, $periodo, $academica){
		$stringSql = $academica==1?$this->condicionAcademicas:"";

		$this->query = "

		SELECT id_estudiante, primer_apellido,segundo_apellido, primer_nombre, 
		sum(Valoracion >= (SELECT minimo from valoracion WHERE valoracion = 'Superior')and valoracion <=(SELECT maximo from valoracion WHERE valoracion = 'Superior'))   as S , 
		sum(Valoracion >=  (SELECT minimo from valoracion WHERE valoracion = 'Alto') and valoracion <= (SELECT maximo from valoracion WHERE valoracion = 'Alto')) as A ,
		sum(valoracion >= (SELECT minimo from valoracion WHERE valoracion = 'Basico') and valoracion <=(SELECT maximo from valoracion WHERE valoracion = 'Basico')) as B , 
		sum(valoracion <= (SELECT maximo from valoracion WHERE valoracion = 'Bajo') ) as V ,  
		segundo_nombre, sum(Valoracion >=(SELECT minimo from valoracion where valoracion = 'Bajo')) TAV,
		round(sum(Valoracion)/ COUNT(id_estudiante),2) as pgg,		
		round(sum(Valoracion)/ COUNT(id_estudiante),2) as Promedio,
		round(sum(Valoracion_sin_super)/ COUNT(id_estudiante),2) as pgg_sin_super,

		(SELECT v.val FROM valoracion v WHERE 
		ROUND(((SUM(t.valoracion)) / count(t.valoracion)),2)
		BETWEEN   v.minimo AND  v.maximo) as Desempeno

		FROM (SELECT id_estudiante, primer_apellido,
		segundo_apellido, primer_nombre, segundo_nombre, id_area as id_asignatura,id_grado,id_grupo, id_as,
		count(id_area), SUM(PesoMay) PesoMa, SUM(PesoIgu) PesoIg, sum(Peso), Area, order_area, SUM(Valoracion) , 
		IF(SUM(Peso)=100, round(sum(valoracion * (Peso/100)),1), ROUND(sum(valoracion)/count(id_area),1)) Valoracion, Valoracion_sin_super FROM 
		( SELECT t_evaluacion.id_estudiante, t_evaluacion.primer_apellido, t_evaluacion.segundo_apellido, t_evaluacion.primer_nombre,
		t_evaluacion.segundo_nombre, t_evaluacion.id_area,t_evaluacion.id_asignatura as id_as, (SELECT t_asignaturas.asignatura 
		FROM t_asignaturas WHERE t_asignaturas.id_asignatura = t_evaluacion.id_asignatura) as Asignatura , t_evaluacion.id_grado, 
		t_evaluacion.id_grupo, t_asignatura_x_area.peso_frente_area>0 as PesoMay, 
		t_asignatura_x_area.peso_frente_area=0 or ISNULL(t_asignatura_x_area.peso_frente_area) as PesoIgu,
		t_asignatura_x_area.peso_frente_area as Peso, (SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Area,
		(SELECT t_area.order_area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as order_area ,
		t_evaluacion.eval_".$periodo."_per as Valoracion_sin_super,
		FUNC_SELECT_VAL(t_evaluacion.id_estudiante,t_evaluacion.id_asignatura,{$grupo},{$periodo},t_evaluacion.eval_".$periodo."_per)				
		as Valoracion	
		FROM t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area 
		and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado 
		and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo')
		".$stringSql." 
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		
		ORDER BY primer_apellido ASC, segundo_apellido ASC, primer_nombre ASC, segundo_nombre Asc, order_area ASC) as t GROUP BY id_area,
		id_estudiante ORDER BY id_estudiante, id_area) as t where Valoracion >= (SELECT minimo from valoracion where valoracion = 'Bajo') 
		GROUP BY id_estudiante ORDER BY Tav DESC , Promedio DESC; 
		";

		$this->execute_single_query();


		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		var_dump($this->rows);
		return false;
	}


	public function getPromedioPuestosAreasReprobados($grupo, $periodo, $academica){
		$stringSql = $academica==1?$this->condicionAcademicas:"";

		$this->query = "
		SELECT * FROM(
		SELECT id_estudiante, primer_apellido,segundo_apellido, primer_nombre, 
		sum(Valoracion >= (SELECT minimo from valoracion WHERE valoracion = 'Superior')and valoracion <=(SELECT maximo from valoracion WHERE valoracion = 'Superior'))   as S , 
		sum(Valoracion >=  (SELECT minimo from valoracion WHERE valoracion = 'Alto') and valoracion <= (SELECT maximo from valoracion WHERE valoracion = 'Alto')) as A ,
		sum(valoracion >= (SELECT minimo from valoracion WHERE valoracion = 'Basico') and valoracion <=(SELECT maximo from valoracion WHERE valoracion = 'Basico')) as B , 
		sum(valoracion <= (SELECT maximo from valoracion WHERE valoracion = 'Bajo') ) as V ,  
		segundo_nombre, sum(Valoracion >=(SELECT minimo from valoracion where valoracion = 'Bajo')) TAV,
		round(sum(Valoracion)/ COUNT(id_estudiante),2) as pgg,
		round(sum(Valoracion)/ COUNT(id_estudiante),2) as Promedio,
		round(sum(Valoracion_sin_super)/ COUNT(id_estudiante),2) as pgg_sin_super,

		(SELECT v.val FROM valoracion v WHERE 
		ROUND(((SUM(t.valoracion)) / count(t.valoracion)),2)
		BETWEEN   v.minimo AND  v.maximo) as Desempeno

		FROM (SELECT id_estudiante, primer_apellido,
		segundo_apellido, primer_nombre, segundo_nombre, id_area as id_asignatura,id_grado,id_grupo, id_as,
		count(id_area), SUM(PesoMay) PesoMa, SUM(PesoIgu) PesoIg, sum(Peso), Area, order_area, SUM(Valoracion) , 
		IF(SUM(Peso)=100, round(sum(valoracion * (Peso/100)),2), ROUND(sum(valoracion)/count(id_area),2)) Valoracion, 
		Valoracion_sin_super FROM 
		( SELECT t_evaluacion.id_estudiante, t_evaluacion.primer_apellido, t_evaluacion.segundo_apellido, t_evaluacion.primer_nombre,
		t_evaluacion.segundo_nombre, t_evaluacion.id_area,t_evaluacion.id_asignatura as id_as, (SELECT t_asignaturas.asignatura 
		FROM t_asignaturas WHERE t_asignaturas.id_asignatura = t_evaluacion.id_asignatura) as Asignatura , t_evaluacion.id_grado, 
		t_evaluacion.id_grupo, t_asignatura_x_area.peso_frente_area>0 as PesoMay, 
		t_asignatura_x_area.peso_frente_area=0 or ISNULL(t_asignatura_x_area.peso_frente_area) as PesoIgu,
		t_asignatura_x_area.peso_frente_area as Peso, (SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Area,
		(SELECT t_area.order_area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as order_area ,
		t_evaluacion.eval_".$periodo."_per as Valoracion_sin_super,
		FUNC_SELECT_VAL(t_evaluacion.id_estudiante,t_evaluacion.id_asignatura,{$grupo},{$periodo},t_evaluacion.eval_".$periodo."_per) as Valoracion FROM 
		t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area 
		and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado 
		and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo')
		".$stringSql." 
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		
		ORDER BY primer_apellido ASC, segundo_apellido ASC, primer_nombre ASC, segundo_nombre Asc, order_area ASC) as t GROUP BY id_area,
		id_estudiante ORDER BY id_estudiante, id_area) as t where Valoracion >= (SELECT minimo from valoracion where valoracion = 'Bajo') 
		GROUP BY id_estudiante ORDER BY primer_apellido ASC, segundo_apellido ASC, primer_nombre ASC, segundo_nombre ASC, Area ASC)
		as s where Promedio <= (SELECT maximo from valoracion where valoracion = 'Bajo')  GROUP BY id_estudiante  ORDER BY Tav DESC , Promedio DESC;
		";

		$this->execute_single_query();


		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		return false;
	}
	



	


}
?>