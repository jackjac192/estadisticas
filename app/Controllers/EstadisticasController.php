<?php 

namespace Controllers;
use Config\View 	as View;
use Model\PuestosModel as Puestos;
use Model\InstitucionModel as Institucion;


class EstadisticasController{


	public function setIndexAction($db){
		
		$_SESSION['db'] = $db;
		
		header("Access-Control-Allow-Origin: *");
		$view = new View('index', 'Estadisticas', ['db'=>$db]);
		$view->execute();


	}


	public function getSeleccionarGrupoAction($db){
		
		$institucion_obj = new Institucion($db);	
		$accion = $_POST['accion'];



		$periodos = $institucion_obj->getPeriodos()['datos'];
		$grados = $institucion_obj->getGrados()['datos'];
		$jornadas = $institucion_obj->getJornadas();
		$anos = $institucion_obj->getAnosLectivos();
		
		header("Access-Control-Allow-Origin: *");
		$view = new View('index', 'seleccionar', 
			[
			'grados' => $grados, 
			'periodos' => $periodos, 
			'accion' => $accion, 
			'jornadas' =>$jornadas,
			'anos' =>$anos
			]
			);
		$view->execute();	

	}

	

}
?>