<?php

namespace Helpers;


class GenerarPuestos {

	public function obtenerPromedios($estudiantesPromedios, $db=null){
		
		$contador=0;
		$puestos= array();
		$tavs = array();
		if($estudiantesPromedios != false){
			foreach ($estudiantesPromedios as $key => $value) {
				
				$estudiante = array(
					'id' => $value['id_estudiante'], 
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
					'pgg' => round(($value['pgg']==0?0:(($value['pgg']*$value['TAV'])/$max)),1), 
					'promedio' => $value['pgg'],
					'TAV' => $value['TAV']
				);
				$puestosDef[$value['id']] = $estudiante;
				$contador++;
			}
			$puestosDef = $this->orderMultiDimensionalArray($puestosDef, 'pgg', true);
		//$puestos = $this->orderMultiDimensionalArray($puestos, 'TAV', true);
		//$puestos = $this->orderMultiDimensionalArray($puestos, 'tav', true);
			return $this->generarPuesto($puestosDef);
		}

		
		
	}

	public function obtenerPromediosAsig($estudiantesPromedios, $db=null){
		
		$contador=0;
		$puestos= array();
		$tavs = array();
		foreach ($estudiantesPromedios as $key => $value) {
			$estudiante = array(
				'id' => $value['id_asignatura'], 
				'pgg' => $value['pgg'], 
				'TAV' => $value['TAV'],
				'promedio' => $value['pgg'],
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
			$estudiante = array('id' => $value['id'], 'pgg' => (($value['pgg']*$value['TAV'])/$max) );
			$puestosDef[$contador] = $estudiante;
			$contador++;
		}
		$puestosDef = $this->orderMultiDimensionalArray($puestosDef, 'pgg', true);
		//$puestos = $this->orderMultiDimensionalArray($puestos, 'TAV', true);
		//$puestos = $this->orderMultiDimensionalArray($puestos, 'tav', true);
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
					'pgg' => $value['pgg'], 
					'promedio' => $value['promedio'],
					'TAV' => $value['TAV'] 
				);
				$pggAux = $value['pgg'];
				$puestos[$value['id']]= $estudiantePgg;
				$contadorAux++;
			}
			if($value['pgg']==$pggAux){
				$estudiantePgg = array(
					'id' => $value['id'], 
					'puesto' => $contadorAux-1, 
					'pgg' => $value['pgg'],
					'promedio' => $value['promedio'],
					'TAV' => $value['TAV'] 
				);
				$pggAux=$value['pgg'];
				$puestos[$value['id']] = $estudiantePgg;
			}
			if($value['pgg']<$pggAux){
				$estudiantePgg = array(
					'id' => $value['id'],
					'puesto' => $contadorAux, 
					'pgg' => $value['pgg'],
					'promedio' => $value['promedio'],
					'TAV' => $value['TAV'] 
				);
				$pggAux=$value['pgg'];
				$puestos[$value['id']] = $estudiantePgg;
				$contadorAux++;
			}
			$contador++;
		}

		return $puestos;
	}

	
}
?>