<?php
namespace Controllers;
use Config\View 	as View;
use Model\InstitucionModel as Institucion;
use Model\ReprobadasModel as Reprobadas;


class ReprobadasController{

	public function getReprobadasAction($db){

		$periodo = $_POST['periodo'];		
		$grupo = $_POST['grupo'];
		$area = $_POST['area'];				
		$reprobados = $_POST['reprobados'];	
		$academicas	= $_POST['academicas'];
		$puestoPromedio = array();
		$estudiantesPuestos = array();

		$reprobadas_obj = new Reprobadas($db);
		$informacionGrupo_obj = new Institucion($db);

		$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grupo);		

		if($area=="0"){
			
			$tablaContenido = $reprobadas_obj->getEstudiantesAsiganturasRepro($grupo, $periodo, $academicas);						
			
		}		
		
		if($area=="1")
		{	
			$tablaContenido = $reprobadas_obj->getEstudiantesAareasRepro($grupo, $periodo, $academicas);
		}		
		
			header("Access-Control-Allow-Origin: *");
			$view = new View(
				'reprobadas', 
				'reprobadas', 
				[
				'tablaContenido' => $tablaContenido
				

				]);

			$view->execute();

			$_POST['data']=null;		

		}



		public function getReprobadasFiltroAction($db){

			$periodo = $_POST['periodo'];		
			$grupo = $_POST['grupo'];
			$area = $_POST['area'];				
			$reprobados = $_POST['reprobados'];	
			$academicas	= $_POST['academicas'];
			$numero	= $_POST['numero'];
			$operador	= $_POST['operador'];

			$puestoPromedio = array();
			$estudiantesPuestos = array();
			$estudiantesRepro = array();

			$reprobadas_obj = new Reprobadas($db);
			$informacionGrupo_obj = new Institucion($db);

			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grupo);		

			if($area=="0"){

				$tablaContenido = $reprobadas_obj->getEstudiantesAsiganturasRepro($grupo, $periodo, $academicas);	
				$estudiantesRepro = $reprobadas_obj->getEstudiantesRepro($grupo, $periodo, $academicas, $operador, $numero);

			}		

			if($area=="1")
			{	
				$tablaContenido = $reprobadas_obj->getEstudiantesAareasRepro($grupo, $periodo, $academicas);
				$estudiantesRepro = $reprobadas_obj->getEstudiantesReproA($grupo, $periodo, $academicas, $operador, $numero);
				//var_dump($estudiantesRepro);
			}		

			header("Access-Control-Allow-Origin: *");

			$view = new View(
				'reprobadas', 
				'filtroReprobadas', 
				[
				'tablaContenido' => $tablaContenido,
				'estudiantesRepro' => $estudiantesRepro
				

				]);

			$view->execute();

			$_POST['data']=null;		

		}




	}
	?>