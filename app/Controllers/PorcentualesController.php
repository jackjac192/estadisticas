<?php
namespace Controllers;


use Config\View 	as View;
use Model\InstitucionModel as Institucion;
use Model\ConsolidadosModel as Consolidados;
use Model\PorcentualesModel as Porcentuales;
use Model\ValoracionModel as Valoracion;


class PorcentualesController{

	

	private function generarPuesto($estudiante){
		$contador=1;
		$contadorAux=1;
		$pggAux=0;
		$puestos = array();
		foreach ($estudiante as $key => $value) {

			if($value['pgg']>$pggAux){
				$estudiantePgg = array(
					'id' => $value['id'], 
					'puesto' => $contadorAux , 
					'pgg' => $value['pgg'] 
					);
				$pggAux = $value['pgg'];
				$puestos[$contador]= $estudiantePgg;
				$contadorAux++;
			}
			if($value['pgg']==$pggAux){
				$estudiantePgg = array(
					'id' => $value['id'], 
					'puesto' => $contadorAux-1, 
					'pgg' => $value['pgg']  );
				$pggAux=$value['pgg'];
				$puestos[$contador] = $estudiantePgg;
			}
			if($value['pgg']<$pggAux){
				$estudiantePgg = array(
					'id' => $value['id'], 
					'puesto' => $contadorAux, 
					'pgg' => $value['pgg']  
					);
				$pggAux=$value['pgg'];
				$puestos[$contador] = $estudiantePgg;
				$contadorAux++;
			}
			$contador++;
		}

		return $puestos;
	}

	public function obtenerPromedios($estudiantesPromedios, $db){
		$consolidado_obj = new Consolidados($db);	
		
		$contador=0;
		$puestos= array();
		$tavs = array();
		foreach ($estudiantesPromedios as $key => $value) {
			$estudiante = array(
				'id' => $value['id_asignatura'], 
				'pgg' => $value['pgg'], 
				'TAV' => $value['TAV'] 
				);

			$puestos[$contador] = $estudiante;
			$tavs[$contador]= $value['TAV'];
			$contador++;
		}
		$max = max($tavs);
		$contador=0;
		$puestosDef = array();
		foreach ($puestos as $value) {
			$estudiante = array(
				'id' => $value['id'], 
				'pgg' => (($value['pgg']*$value['TAV'])/$max)
				);
			$puestosDef[$contador] = $estudiante;
			$contador++;
		}
		$puestosDef = $this->orderMultiDimensionalArray($puestosDef, 'pgg', true);		
		return $this->generarPuesto($puestosDef);
	}

	function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {
		$position = array();
		$newRow = array();
		foreach ($toOrderArray as $key => $row) {
			$position[$key]  = $row[$field];
			$newRow[$key] = $row;
		}
		if ($inverse) {
			arsort($position);
		}
		else {
			asort($position);
		}
		$returnArray = array();
		foreach ($position as $key => $pos) {     
			$returnArray[] = $newRow[$key];
		}
		return $returnArray;
	}


	public function getPorcentualesAction($db){

		$periodo = $_POST['periodo'];		
		$grupo = $_POST['grupo'];
		$area = $_POST['area'];				
		$reprobados = $_POST['reprobados'];	
		$academicas	= $_POST['academicas'];
		$puestoPromedio = array();
		$estudiantesPuestos = array();

		$porcentuales_obj = new Porcentuales($db);
		$informacionGrupo_obj = new Institucion($db);

		$valoracion_obj = new Valoracion($db);
		$valoraciones = $valoracion_obj->obtenerValoraciones();
		

		$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grupo);		

		if($area=="0"){

			if ($reprobados=="0") {
				$tablaPuestos = $porcentuales_obj->getAsignaturasPorcentuales($grupo, $periodo, $academicas);	

			}
			if ($reprobados=="1") {
				$tablaPuestos = $porcentuales_obj->getAsignaturasPorcentualesReprobados($grupo, $periodo, $academicas);	
			}	
			$estudiantesPuestos =  $porcentuales_obj->getAsignaturasPorcentuales($grupo, $periodo, $academicas);	
			$puestoPromedio = $this->obtenerPromedios($estudiantesPuestos, $db);			
		}				
		if($area=="1")
		{				
			if ($reprobados=="0") {
				$tablaPuestos = $porcentuales_obj->getAreasPorcentuales($grupo, $periodo, $academicas);
			}
			if($reprobados=="1"){
				$tablaPuestos = $porcentuales_obj->getPromedioPuestosAreasReprobados($grupo, $periodo,$academicas);
			}
			$estudiantesPuestos =  $porcentuales_obj->getAreasPorcentuales($grupo, $periodo, $academicas);	
			$puestoPromedio = $this->obtenerPromedios($estudiantesPuestos, $db);
		}		
		
			header("Access-Control-Allow-Origin: *");
			$view = new View(
				'porcentuales', 
				'porcentuales', 
				[
				'tablaPuestos' => $tablaPuestos,
				'puestoPromedio' => $puestoPromedio,
				'valoraciones' =>$valoraciones
				]);

			$view->execute();

			$_POST['data']=null;

			
		}

		






	}
	?>
