<?php

namespace Model;
use Config\DataBase as BD;

class PorcentualesModel extends BD{
	public $condicionAcademicas = "AND t_evaluacion.id_asignatura in (select id_asignatura from t_asignaturas where t_asignaturas.tipo_asig = 'A')";


	function __construct($bd){

		$this->database=$bd;

	}		
		 

	public function getAsignaturasPorcentuales($grupo, $periodo, $academica)
	{	
		$stringSql = $academica==1?$this->condicionAcademicas:"";

		$this->query = "
		SELECT 
		sum(eval_".$periodo."_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and eval_".$periodo."_per
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior')) as S , 
		round(( 
		(
		sum(eval_".$periodo."_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and eval_".$periodo."_per
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'S%',
		sum(eval_".$periodo."_per >=
		(SELECT minimo from valoracion WHERE valoracion = 'Alto') and eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Alto')) as A , 
		
		round(( 
		(
		sum(eval_".$periodo."_per >=
		(SELECT minimo from valoracion WHERE valoracion = 'Alto') and eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Alto'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'A%',
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion WHERE valoracion = 'Basico') and eval_".$periodo."_per <=
		(SELECT maximo from valoracion WHERE valoracion = 'Basico')) as B , 
		
		round(( 
		(
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion WHERE valoracion = 'Basico') and eval_".$periodo."_per <=
		(SELECT maximo from valoracion WHERE valoracion = 'Basico'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'B%',
		
		sum(eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Bajo') ) as V , 

		round(( 
		(
		sum(eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Bajo') )
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'V%',
		
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo')) as TAV,
		 t_evaluacion.id_area,t_evaluacion.id_asignatura, (SELECT t_asignaturas.asignatura FROM t_asignaturas 
		WHERE t_asignaturas.id_asignatura = t_evaluacion.id_asignatura) as Asignatura , t_evaluacion.id_grado, t_evaluacion.id_grupo, 
		t_asignatura_x_area.peso_frente_area as Peso, (SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Area, 
		(SELECT t_area.order_area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as order_area , 
		round(sum(t_evaluacion.eval_".$periodo."_per) / sum(t_evaluacion.eval_".$periodo."_per>0),1) 
		as Promedio,
		round(sum(t_evaluacion.eval_".$periodo."_per) / sum(t_evaluacion.eval_".$periodo."_per>0),1) 
		as pgg,
		(SELECT valoracion.val FROM valoracion WHERE ROUND(((SUM(eval_".$periodo."_per)) / sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo') )),1) BETWEEN valoracion.minimo AND valoracion.maximo)
		as Desempeno
		FROM t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area 
		and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado 
		and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo') 
		".$stringSql."
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		and t_evaluacion.id_subgrupo is NULL
		GROUP BY id_asignatura
		ORDER BY Promedio DESC, 
		order_area ASC; 
				
		";

		$this->execute_single_query();

		//print_r($this->query);
		if($this->resultado->num_rows > 0){
			$this->get_result_query();

			return $this->rows;

		}
		return false;
		
	}

	public function getAsignaturasPorcentualesReprobados($grupo, $periodo, $academica){
		$stringSql = $academica==1?$this->condicionAcademicas:"";

		$this->query = "
		select * from 
		(
		SELECT 
		sum(eval_".$periodo."_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and eval_".$periodo."_per
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior')) as S , 
		round(( 
		(
		sum(eval_".$periodo."_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and eval_".$periodo."_per
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'S%',
		sum(eval_".$periodo."_per >=
		(SELECT minimo from valoracion WHERE valoracion = 'Alto') and eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Alto')) as A , 
		
		round(( 
		(
		sum(eval_".$periodo."_per >=
		(SELECT minimo from valoracion WHERE valoracion = 'Alto') and eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Alto'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'A%',
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion WHERE valoracion = 'Basico') and eval_".$periodo."_per <=
		(SELECT maximo from valoracion WHERE valoracion = 'Basico')) as B , 
		
		round(( 
		(
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion WHERE valoracion = 'Basico') and eval_".$periodo."_per <=
		(SELECT maximo from valoracion WHERE valoracion = 'Basico'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'B%',
		
		sum(eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Bajo') ) as V , 

		round(( 
		(
		sum(eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Bajo') )
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'V%',
		
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo')) as TAV,
		 t_evaluacion.id_area,t_evaluacion.id_asignatura, (SELECT t_asignaturas.asignatura FROM t_asignaturas 
		WHERE t_asignaturas.id_asignatura = t_evaluacion.id_asignatura) as Asignatura , t_evaluacion.id_grado, t_evaluacion.id_grupo, 
		t_asignatura_x_area.peso_frente_area as Peso, (SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Area, 
		(SELECT t_area.order_area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as order_area , 
		round(sum(t_evaluacion.eval_".$periodo."_per) / sum(t_evaluacion.eval_".$periodo."_per>0),1) 
		as Promedio,
		round(sum(t_evaluacion.eval_".$periodo."_per) / sum(t_evaluacion.eval_".$periodo."_per>0),1) 
		as pgg,
		(SELECT valoracion.val FROM valoracion WHERE ROUND(((SUM(eval_".$periodo."_per)) / sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo') )),1) BETWEEN valoracion.minimo AND valoracion.maximo)
		as Desempeno
		FROM t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area 
		and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado 
		and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo') 
		".$stringSql."
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		and t_evaluacion.id_subgrupo is NULL
		GROUP BY id_asignatura
		ORDER BY Promedio DESC, 
		order_area ASC
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

	public function getAreasPorcentuales($grupo, $periodo, $academica){
		$stringSql = $academica==1?$this->condicionAcademicas:"";

		$this->query = "		
		SELECT 
		sum(eval_".$periodo."_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and eval_".$periodo."_per
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior')) as S , 
		round(( 
		(
		sum(eval_".$periodo."_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and eval_".$periodo."_per
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'S%',
		sum(eval_".$periodo."_per >=
		(SELECT minimo from valoracion WHERE valoracion = 'Alto') and eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Alto')) as A , 
		
		round(( 
		(
		sum(eval_".$periodo."_per >=
		(SELECT minimo from valoracion WHERE valoracion = 'Alto') and eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Alto'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'A%',
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion WHERE valoracion = 'Basico') and eval_".$periodo."_per <=
		(SELECT maximo from valoracion WHERE valoracion = 'Basico')) as B , 
		
		round(( 
		(
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion WHERE valoracion = 'Basico') and eval_".$periodo."_per <=
		(SELECT maximo from valoracion WHERE valoracion = 'Basico'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'B%',
		
		sum(eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Bajo') ) as V , 

		round(( 
		(
		sum(eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Bajo') )
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'V%',
		
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo')) as TAV,
		 t_evaluacion.id_area,t_evaluacion.id_asignatura, (SELECT t_asignaturas.asignatura FROM t_asignaturas 
		WHERE t_asignaturas.id_asignatura = t_evaluacion.id_asignatura) as Asignatura , t_evaluacion.id_grado, t_evaluacion.id_grupo, 
		t_asignatura_x_area.peso_frente_area as Peso, (SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Area, 
		(SELECT t_area.order_area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as order_area , 
		round(sum(t_evaluacion.eval_".$periodo."_per) / sum(t_evaluacion.eval_".$periodo."_per>0),1) 
		as Promedio,
		round(sum(t_evaluacion.eval_".$periodo."_per) / sum(t_evaluacion.eval_".$periodo."_per>0),1) 
		as pgg,
		(SELECT valoracion.val FROM valoracion WHERE ROUND(((SUM(eval_".$periodo."_per)) / sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo') )),1) BETWEEN valoracion.minimo AND valoracion.maximo)
		as Desempeno
		FROM t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area 
		and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado 
		and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo') 
		".$stringSql."
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		and t_evaluacion.id_subgrupo is NULL
		GROUP BY id_asignatura
		ORDER BY Promedio DESC, 
		order_area ASC; 
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
		SELECT 
		sum(eval_".$periodo."_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and eval_".$periodo."_per
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior')) as S , 
		round(( 
		(
		sum(eval_".$periodo."_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and eval_".$periodo."_per
		<=(SELECT maximo from valoracion WHERE valoracion = 'Superior'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'S%',
		sum(eval_".$periodo."_per >=
		(SELECT minimo from valoracion WHERE valoracion = 'Alto') and eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Alto')) as A , 
		
		round(( 
		(
		sum(eval_".$periodo."_per >=
		(SELECT minimo from valoracion WHERE valoracion = 'Alto') and eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Alto'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'A%',
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion WHERE valoracion = 'Basico') and eval_".$periodo."_per <=
		(SELECT maximo from valoracion WHERE valoracion = 'Basico')) as B , 
		
		round(( 
		(
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion WHERE valoracion = 'Basico') and eval_".$periodo."_per <=
		(SELECT maximo from valoracion WHERE valoracion = 'Basico'))
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'B%',
		
		sum(eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Bajo') ) as V , 

		round(( 
		(
		sum(eval_".$periodo."_per <= 
		(SELECT maximo from valoracion WHERE valoracion = 'Bajo') )
		/ sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo'))
		) * 100
		),0) as 'V%',
		
		sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo')) as TAV,
		 t_evaluacion.id_area,t_evaluacion.id_asignatura, (SELECT t_asignaturas.asignatura FROM t_asignaturas 
		WHERE t_asignaturas.id_asignatura = t_evaluacion.id_asignatura) as Asignatura , t_evaluacion.id_grado, t_evaluacion.id_grupo, 
		t_asignatura_x_area.peso_frente_area as Peso, (SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Area, 
		(SELECT t_area.order_area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as order_area , 
		round(sum(t_evaluacion.eval_".$periodo."_per) / sum(t_evaluacion.eval_".$periodo."_per>0),1) 
		as Promedio,
		round(sum(t_evaluacion.eval_".$periodo."_per) / sum(t_evaluacion.eval_".$periodo."_per>0),1) 
		as pgg,
		(SELECT valoracion.val FROM valoracion WHERE ROUND(((SUM(eval_".$periodo."_per)) / sum(eval_".$periodo."_per >= 
		(SELECT minimo from valoracion where valoracion = 'Bajo') )),1) BETWEEN valoracion.minimo AND valoracion.maximo)
		as Desempeno
		FROM t_evaluacion INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area = t_evaluacion.id_area 
		and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND t_evaluacion.id_grado = t_asignatura_x_area.id_grado 
		and t_evaluacion.id_grupo = '{$grupo}' and t_evaluacion.eval_".$periodo."_per >= (SELECT minimo from valoracion where valoracion = 'Bajo') 
		".$stringSql."
		and (t_evaluacion.novedad NOT LIKE 'Ret' OR t_evaluacion.novedad IS NULL)
		and t_evaluacion.id_subgrupo is NULL
		GROUP BY id_asignatura
		ORDER BY Promedio DESC, 
		order_area ASC; 
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