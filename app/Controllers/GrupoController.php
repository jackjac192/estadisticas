<?php
namespace Controllers;
use Config\View 	as View;
use Model\InstitucionModel as Institucion;
use Model\PuestosModel as Puestos;


class GrupoController{

	public function getIndexAction($db){
		
		$institucion_obj = new Institucion($db);		

		$sedes = $institucion_obj->getSedes();
		

		$view = new View('index', 'seleccionar', ['sedes' => $sedes['datos'], 'urlPrint' =>'', 'urlView' => '/Grupo/getPuestos/']);
		$view->execute();	

	}


	public function getGradosAction($id_sede, $db){

		$institucion_obj = new Institucion($db);
		
		$grados = $institucion_obj->getGrados($id_sede)['datos'];
		header("Access-Control-Allow-Origin: *");
			echo "<option value=0> SELECCIONE UN GRADO</option>";
		foreach ($grados as $grado) {
			echo "<option value='".$grado['id_grado']."' >".$grado['grado']."</option>";
		}
		echo "<option value=-1> TODOS LOS GRADOS </option>";

	}

	public function getGruposAction($id_grado, $db){
		$institucion_obj = new Institucion($db);
		
		
		$grupos = $institucion_obj->getGrupos($id_grado)['datos'];
		header("Access-Control-Allow-Origin: *");
			echo "<option value=0> SELECCIONE UN GRUPO</option>";
		foreach ($grupos as $grupo) {
			echo "<option value='".$grupo['id_grupo']."' >".$grupo['nombre_grupo']."</option>";
		}


	}

	public function getPuestosAction($id_grupo, $db, $area='0', $repro='0'){
		$data = json_decode(stripslashes($_POST['data']));

		$puestos_obj = new Puestos($db);
		
		$puestos = $puestos_obj->obtenerPuestos($data, $db);

		$info = $puestos_obj->obtenerInfoGrupos($data);
		header("Access-Control-Allow-Origin: *");
		$view = new View('grupo', 'grupo', ['info' => $info, 'puestos' => $puestos]);
		$view->execute();

	}

	


}
?>