<?php
namespace Controllers;
use Config\View 	as View;
use Model\InstitucionModel as Institucion;
use Model\PuestosModel as Puestos;


class ReporteController{


	public function getReportesAction($db){
		header("Access-Control-Allow-Origin: *");
		echo "Ya puede imprimir";

	}




}
?>
