<?php
namespace Controllers;
use Config\View 	as View;
use Model\InstitucionModel as Institucion;
use Model\ConsolidadosModel as Consolidados;
use Model\ValoracionModel as Valoracion;
use Model\PeriodosModel as Periodos;
use Helpers\GenerarPuestos as PuestosPromedios;
use Helpers\Calcular as Calcular;
use Model\CerrarInformeModel as CerrarInforme;


class ConsolidadoController{
	private $puestosPromedios_obj;	

	function __construct(){		
		$this->puestosPromedios_obj = new PuestosPromedios();		
	}


	public function getConsolidadoAction($db){		
		
		$db = $_POST['db'];
		$_calcular = new Calcular($db);		
		$area = $_POST['area'];	
		$cerrar = $_POST['cerrarInforme'];
		$grupo = $_POST['grupo'];
		$puestoPromedio = array();
		$periodo = $_POST['periodo'];		
		$informe = $_POST['informe']=="1"?true:false;
		$estudiantesPuestos = array();
		$reprobados = $_POST['reprobados'];	
		$academicas	= $_POST['academicas'];		
		$periodos_acumulados = $_POST['per_acumulados']=="1"?true:false;

		$valoracion_obj = new Valoracion($db);		
		$valoraciones = $valoracion_obj->obtenerValoraciones();
		
		$consolidado_obj = new Consolidados($db);
		$informacionGrupo_obj = new Institucion($db);

		$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grupo);

		$periodos_obj = new Periodos($db);
		$result_per = $periodos_obj->getPeriodosEvaluados()['datos'];
		$periodosAll = $periodos_obj->getPeriodos()['datos'];		


		$tablaConsolidados = [];
		$asignaturasEvaluadas = [];
		$estudiantesPromedios = [];
		$estudiantesPuestos = [];
		$puestoPromedio = [];
		

		$num_periodos = [];
		$peso_periodos = [];
		$isCalcular = false;


		if (!$periodos_acumulados && !$informe) {
			$num_periodos[0] = $periodo;			
		}else{
			foreach ($result_per as $key => $value) {
				$num_periodos[$key] = $value['periodos'];
				$peso_periodos[$key] = $value['peso'];

			}
			$isCalcular = true;
		}

		
		$contador = 0;
		$periodos_evaluados = [];

		foreach($num_periodos as $key => $_periodo){			

			if($consolidado_obj->getAreasEvaluadas($grupo, $_periodo,$academicas,$reprobados) != false){

				$periodos_evaluados[$contador] = $_periodo;
				if($area=="0"){

					$tablaConsolidados[$contador] = $consolidado_obj->getPromediosAsiganturas($grupo, $_periodo,$academicas);
					$asignaturasEvaluadas = $consolidado_obj->getAsignaturasEvaludadas($grupo, $_periodo,$academicas,$reprobados);

					if($reprobados == "0"){
						$estudiantesPromedios[$contador] = $consolidado_obj->getEstudiantesPromedios($grupo, $_periodo,$academicas);
					}
					if($reprobados == "1"){

						$estudiantesPromedios[$contador] = $consolidado_obj->getEstudiantesPromediosReprobados($grupo, $_periodo,$academicas);	
					}
					$estudiantesPuestos[$contador] = $consolidado_obj->getEstudiantesPromedios($grupo, $_periodo, $academicas);
					$puestoPromedio[$contador] = $this->puestosPromedios_obj->obtenerPromedios($estudiantesPuestos[$contador], $db);

				}		

				if($area=="1")
				{
					$tablaConsolidados[$contador] = $consolidado_obj->getPromediosAreas($grupo, $_periodo,$academicas);
					$asignaturasEvaluadas = $consolidado_obj->getAreasEvaluadas($grupo, $_periodo,$academicas,$reprobados);


					if($reprobados == "0"){
						$estudiantesPromedios[$contador] = $consolidado_obj->getEstudiantesPromediosAreas($grupo, $_periodo,$academicas);
					}
					if($reprobados == "1"){

						$estudiantesPromedios[$contador] = $consolidado_obj->getEstudiantesAreasReprobadas($grupo, 
							$_periodo , $academicas);

					}
					$estudiantesPuestos[$contador] = $consolidado_obj->getEstudiantesPromediosAreas($grupo,$_periodo,$academicas);
					$puestoPromedio[$contador] = $this->puestosPromedios_obj->obtenerPromedios($estudiantesPuestos[$contador], $db);

				}
				$contador++;
			}

		}		

		

		
		$_calcular->setArraysCalcular(
			[
				'array_datos_estudiantes_periodos' =>	$estudiantesPromedios,
				'array_datos_estudiantes_promedios_periodos' =>	$puestoPromedio,
				'array_periodos_evaluados' => $periodos_evaluados,
				'array_porcentajes_periodos' => $peso_periodos,
				'array_datos_estudiantes_asignaturas_periodos' => $tablaConsolidados,
				'array_listado_asignaturas_evaluadas' => $asignaturasEvaluadas,
				'min_bajo' => $valoraciones[1]['minimo'],
				'min_basico' => $valoraciones[2]['minimo'],
				'max_superior' => $valoraciones[3]['maximo'],
				'array_periodos' => $periodosAll,
				'isCalcular' => $isCalcular

			]
		);
		
		//Se debe conservar el orden de ejecución de cada método
		$array_listado_estudiantes_promedios_periodos = $_calcular->getArrayListadoEstudiantesPromediosPeriodos();
		$array_listado_estudiantes_evaluados = $_calcular->getArrayListadoEstudiantesEvaluados();
		$cantidad_periodos_evaluados = $_calcular->getCantidadPeriodosEvaluados();
		$array_promedios_acumulados = $_calcular->getArrayPromediosAcumulados();
		$array_puesto_promedio_acumulado = $_calcular->getArrayPuestoPromedioAcumulado();
		$array_listado_estudiantes_asignatura_periodos = $_calcular->getArrayListadoEstudiantesAsignaturasPeriodos();
		$array_estudiantes_acumulados_asignaturas = $_calcular->getArrayListadoEstudiantesAcumuladosAsignaturasPeriodos();
		$array_estudiantes_requeridas_asignaturas = $_calcular->getArrayListadoEstudiantesRequeridasAsignaturasPeriodos();
		$array_periodos_evaluados = $_calcular->getArrayPeriodosEvaluados();
		

		if($cerrar == "true" && $area=="0" && $reprobados == "0" && $academicas == "0"){
			
			$cerrarInforme_obj = new CerrarInforme(
				[	
					'db' => $db,
					'array_estudiantes_acumulados_asignaturas' =>$array_estudiantes_acumulados_asignaturas,
					'array_puesto_promedio_acumulado' =>$array_puesto_promedio_acumulado, 
					'id_grupo' => $grupo
				]);			
			
			
			
		}else{
			echo "No se ha creado el informe final, solo es posible por asignaturas";
		}
		
		header("Access-Control-Allow-Origin: *");
		
		$view = new View(
			'consolidado', 
			'consolidado', 
			[
				'informe' => $informe,
				'isAcumulados' => $periodos_acumulados,
				'min_bajo' => $valoraciones[1]['minimo'],				 
				'min_basico' => $valoraciones[2]['minimo'],
				'informacionGrupo' => $informacionGrupo[0], 
				'cantidad_periodos' => count($periodosAll),
				'max_superior' => $valoraciones[3]['maximo'],
				'estudiantesPromedios'=>$estudiantesPromedios,
				'asignaturasEvaluadas' => $asignaturasEvaluadas,
				'array_periodos_evaluados' => $array_periodos_evaluados,
				'array_promedios_acumulados' => $array_promedios_acumulados,
				'cantidad_periodos_evaluados' => $cantidad_periodos_evaluados,
				'array_puesto_promedio_acumulado' => $array_puesto_promedio_acumulado,
				'array_listado_estudiantes_evaluados' => $array_listado_estudiantes_evaluados,
				'array_estudiantes_requeridas_asignaturas' => $array_estudiantes_requeridas_asignaturas, 
				'array_estudiantes_acumulados_asignaturas' => $array_estudiantes_acumulados_asignaturas,
				'array_listado_estudiantes_promedios_periodos' => $array_listado_estudiantes_promedios_periodos,
				'array_listado_estudiantes_asignatura_periodos' => $array_listado_estudiantes_asignatura_periodos
			]);

		$view->execute();		

	}



	


}
?>