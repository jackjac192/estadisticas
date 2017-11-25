<?php
namespace Helpers;

use Model\ConsolidadosModel as Consolidados;
use Model\PeriodosModel as PeriodosModel;

class Calcular 
{

	private $min_bajo;	
	private $min_basico;
	private $max_superior;
	private $isCalcular;
	private $cantidad_de_periodos;
	private $porcentaje_periodo_prox;		
	private $cantidad_periodos_faltantes;
	private $cantidad_de_periodos_evaluados;
	private $array_periodos;
	private $array_periodo_proximo;
	
	private $obj_generar_puestos;
	private $array_periodos_evaluados;
	private $array_porcentajes_periodos;	
	private $array_promedios_acumulados;
	private $array_datos_estudiantes_periodos;
	private $array_listado_asignaturas_evaluadas;
	private $array_listado_estudiantes_evaluados;
	private $array_estudiantes_requerida_asignaturas;
	private $array_estudiantes_acumulados_asignaturas;
	private $array_datos_estudiantes_promedios_periodos;
	private $array_datos_estudiante_asignaturas_periodos;
	private $array_listado_estudiantes_promedios_periodos;
	private $array_listado_estudiantes_asignaturas_periodos;

	private $obj_periodos;


	public function __construct($db){		
		
		$this->min_bajo = 0;
		$this->min_basico = 0;
		$this->max_superior = 0;
		$this->isCalcular = false;
		$this->array_periodos = [];
		$this->array_periodo_proximo = [];
		$this->cantidad_periodos_faltantes = 0;
		$this->cantidad_de_periodos_evaluados = 0;
		
		$this->array_periodos_evaluados = [];
		$this->array_porcentajes_periodos = [];
		$this->array_promedios_acumulados = [];
		$this->array_listado_estudiantes_evaluados = [];
		$this->array_listado_estudiantes_promedios_periodos = [];
		$this->array_datos_estudiantes_asignaturas_periodos = [];
		$this->array_listado_estudiantes_asignaturas_periodos = [];
		$this->array_listado_asignaturas_evaluadas = [];
		$this->array_estudiantes_acumulados_asignaturas = [];
		$this->array_estudiantes_requerida_asignaturas = [];

		$this->obj_generar_puestos = new GenerarPuestos();
		$this->obj_periodos = new PeriodosModel($db);
		

	}

	
	/**
	*
	*	Write your text
	*
	**/
	public function setArraysCalcular($params){
		extract($params);
		
		$this->min_bajo = $min_bajo;
		$this->min_basico = $min_basico;
		$this->isCalcular = $isCalcular;
		$this->max_superior = $max_superior;
		$this->array_periodos = $array_periodos;
		$this->cantidad_de_periodos = count($array_periodos);
		$this->array_periodos_evaluados = $array_periodos_evaluados;
		$this->cantidad_periodos_faltantes = (count($array_periodos) - count($array_periodos_evaluados));
		$this->cantidad_periodos_faltantes_ = (count($array_periodos) - (count($array_periodos_evaluados)-1));
		$this->array_periodo_proximo = $this->cantidad_periodos_faltantes==0?[]:$array_periodos[count($array_periodos_evaluados)] ;
		$this->porcentaje_periodo_prox = $this->cantidad_periodos_faltantes==0?[]:$array_periodos[count($array_periodos_evaluados)]['peso'];
		$this->porcentaje_periodo_prox_ = $array_periodos[count($array_periodos_evaluados)-1]['peso'];

		print_r($this->array_periodo_proximo);

		$this->array_porcentajes_periodos = $array_porcentajes_periodos;
		$this->array_datos_estudiantes_periodos = $array_datos_estudiantes_periodos;
		$this->array_listado_asignaturas_evaluadas = $array_listado_asignaturas_evaluadas;
		$this->array_datos_estudiantes_promedios_periodos = $array_datos_estudiantes_promedios_periodos;
		$this->array_datos_estudiante_asignaturas_periodos = $array_datos_estudiantes_asignaturas_periodos;

		$this->crearListadoEstudiantes();		
		$this->recorrerListadoEstudiantesEvaluados();
		
	}

	public function getArrayListadoEstudiantesPromediosPeriodos(){
		return $this->array_listado_estudiantes_promedios_periodos;
	}

	public function getArrayListadoEstudiantesAsignaturasPeriodos(){
		$this->array_listado_estudiantes_asignaturas_periodos;
		$array_periodos_asignatura = [];
		$array_asignaturas_valoracion = [];	
		$array_estudiantes_asignaturas_valoracion = [];

		foreach ($this->array_listado_estudiantes_evaluados as $key_id_estudiante => $estudiante_evaluado_) {
			
			foreach ($this->array_datos_estudiante_asignaturas_periodos as $key_periodo_evaluado => $array_estudiantes_asignaturas) {

				foreach ($array_estudiantes_asignaturas as $key_asignatura => $estudiante_asignatura) {
					if($key_id_estudiante == $estudiante_asignatura['id_estudiante']){
						$array_estudiantes_asignaturas_valoracion[$estudiante_asignatura['id_asignatura']]['valoracion'] = $estudiante_asignatura['Valoracion'];	
						$array_estudiantes_asignaturas_valoracion[$estudiante_asignatura['id_asignatura']]['superacion'] = $estudiante_asignatura['Superacion'];		
					}
				}
				$array_periodos_asignatura[$key_periodo_evaluado] = $array_estudiantes_asignaturas_valoracion;				
			}	
			$this->array_listado_estudiantes_asignaturas_periodos[$key_id_estudiante] = $array_periodos_asignatura;
		}
		return $this->array_listado_estudiantes_asignaturas_periodos;		
	}

	public function getArrayListadoEstudiantesAcumuladosAsignaturasPeriodos(){		
		$asignatura_acumulados = [];	
		$asignatura_requeridas = [];
		if($this->isCalcular){
			foreach ($this->array_listado_estudiantes_asignaturas_periodos as $key_id_estudiante => $array_estudiante_periodos_) {
				foreach ($this->array_listado_asignaturas_evaluadas as $asignatura_) {
					$calculo_acumuladas = 0;
					$calculo_requeridas = 0;
					$span_class = "notas";
					foreach ($array_estudiante_periodos_ as $key_periodo_evaluado => $array_estudiantes_asignaturas){

						$valoracion = $array_estudiante_periodos_[$key_periodo_evaluado][$asignatura_['id_asignatura']]['valoracion'];
						$superacion = $array_estudiante_periodos_[$key_periodo_evaluado][$asignatura_['id_asignatura']]['superacion'];
						$nota=$valoracion>$superacion?$valoracion:$superacion;

						$calculo_acumuladas += round(($nota*($this->array_porcentajes_periodos[$key_periodo_evaluado]/100)),1);

						if($nota != "" || $nota >= $this->min_bajo){
							$span_class =$nota<$this->min_basico?"":$span_class;	
						}						
					}
					$asignatura_acumulados[$asignatura_['id_asignatura']]['valoracion'] = $calculo_acumuladas;
					$asignatura_acumulados[$asignatura_['id_asignatura']]['span_class'] = $span_class;

					if($this->cantidad_periodos_faltantes !=0){
						$calculo_requeridas = round((($this->min_basico - round($calculo_acumuladas,1))/ $this->cantidad_periodos_faltantes) / ($this->porcentaje_periodo_prox / 100),1);	
					}else{
						$calculo_requeridas = round((($this->min_basico - round($calculo_acumuladas,1))/ $this->cantidad_periodos_faltantes_) / ($this->porcentaje_periodo_prox_ / 100),1);	
					}

					$asignatura_requeridas[$asignatura_['id_asignatura']]['valoracion'] =  $calculo_requeridas;
					
				}
				$this->array_estudiantes_acumulados_asignaturas[$key_id_estudiante] = $asignatura_acumulados;			
				$this->array_estudiantes_requerida_asignaturas[$key_id_estudiante] = $asignatura_requeridas;
			}		
		}		
		return $this->array_estudiantes_acumulados_asignaturas;
	}

	public function getArrayListadoEstudiantesRequeridasAsignaturasPeriodos(){
		return $this->array_estudiantes_requerida_asignaturas;
	}





	public function getArrayListadoEstudiantesEvaluados(){
		return $this->array_listado_estudiantes_evaluados;
	}

	public function getArrayPorcentajesPeriodos(){
		return $this->array_porcentajes_periodos;
	}


	public function getArrayPuestoPromedioAcumulado(){
		if(isset($this->array_promedios_acumulados)){
			return $this->obj_generar_puestos->obtenerPromedios($this->array_promedios_acumulados);
		}
		return false;
	}


	public function getArrayPromediosAcumulados(){		
		
		if($this->isCalcular){
			foreach($this->array_listado_estudiantes_promedios_periodos as $key_id_estudiante => $estudiante_promedios_periodos_){
				$promedio_acumulado = [];
				$promedio_acumulado['pgg'] = 0;
				$promedio_acumulado['TAV'] = 0;


				foreach($estudiante_promedios_periodos_ as $key_periodo_evaluado => $periodo_evaluado_){
					$promedio_acumulado['pgg'] += $periodo_evaluado_['promedio'] * ($this->array_porcentajes_periodos[$key_periodo_evaluado]/100);
					$promedio_acumulado['TAV'] += $periodo_evaluado_['TAV'];


				}
				$promedio_acumulado['id_estudiante'] = $key_id_estudiante;
				$promedio_acumulado['pgg'] = round($promedio_acumulado['pgg'],1);

				$this->array_promedios_acumulados[$key_id_estudiante] = $promedio_acumulado;

			}
		}
		
		return $this->array_promedios_acumulados; 		
	}



	public function getCantidadPeriodosEvaluados(){
		return $this->cantidad_de_periodos_evaluados;
	}



	public function crearListadoEstudiantes(){
		$count_eval = 0;
		foreach ($this->array_datos_estudiantes_periodos as $key_numero_periodo => $listado_estudiante) {

			if(isset($listado_estudiante) && $listado_estudiante != false){
				$count_eval++;

				# Listado_estudiante trae un arreglo con datos personales de los estudiantes por cada periodo
				# Y Necesitamos crear un listado unico por todos los periodos			
				foreach ($listado_estudiante as $key_numero_estudiante => $estudiante_) {					
					/*
					*	Por cada periodo vamos a guardar un estudiante con su información pero el indice 
					*	será el id del estudiante, lo que asegura que array_listado_estudiantes_evaluados
					*	no tendrá estudiantes repetidos
					*/
					$this->array_listado_estudiantes_evaluados[$estudiante_['id_estudiante']] = array(
						'id_estudiante'		=> 	$estudiante_['id_estudiante'], 
						'primer_apellido'	=>	$estudiante_['primer_apellido'],
						'segundo_apellido'	=>	$estudiante_['segundo_apellido'],
						'primer_nombre'		=>	$estudiante_['primer_nombre'],
						'segundo_nombre'	=>	$estudiante_['segundo_nombre']
					);				
				}	
			}			
		}
		$this->cantidad_de_periodos_evaluados = $count_eval;
	}


	/**	
	*
	* RecorrerArrayEstudianteConsolidados	
	*
	**/
	private function recorrerListadoEstudiantesEvaluados(){
		$identificacion_estudiante = "";

		foreach ($this->array_listado_estudiantes_evaluados as $key_numero_estudiante => $estudiante_evaluado_) {
			if($identificacion_estudiante != $estudiante_evaluado_['id_estudiante']){

				$identificacion_estudiante = $estudiante_evaluado_['id_estudiante'];

				$this->crearListadoPromediosPeriodos($identificacion_estudiante);
				

			}
			
		}
		
		
	}

	public function crearListadoPromediosPeriodos($identificacion_estudiante){
		$array_info_promedio_periodos = [];		
		
		foreach ($this->array_periodos_evaluados as $key_periodo_evaluado => $periodo_evaluado_) {
			
			foreach ($this->array_datos_estudiantes_promedios_periodos[$key_periodo_evaluado] as $array_datos_promedio_estudiante_) {
				if($array_datos_promedio_estudiante_['id'] == $identificacion_estudiante ){
					$array_info_promedio_periodos[$key_periodo_evaluado] = $array_datos_promedio_estudiante_;	
				}				
			}	
		}

		$this->array_listado_estudiantes_promedios_periodos[$identificacion_estudiante] = $array_info_promedio_periodos;

	}


	public function getArrayValoracionesAcumulativas(){

	}

	public function getNovedadesAsignaturas(){

	}


}

?>