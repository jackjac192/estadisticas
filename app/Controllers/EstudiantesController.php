<?php
namespace Controllers;
use Config\View 	as View;
use Model\InstitucionModel as Institucion;
use Model\ReprobadasModel as Reprobadas;
use Model\EstudiantesModel as Estudiantes;


class EstudiantesController{

	public function getListadoGeneroAction($db){

		$periodo = $_POST['periodo'];		
		$grupo = $_POST['grupo'];
		$area = $_POST['area'];				
		$reprobados = $_POST['reprobados'];	
		$academicas	= $_POST['academicas'];
		$jornada	= $_POST['jornada'];
		$ano = $_POST['ano'];

		$informacionGrupo_obj = new Institucion($db);
		$estudiantes_obj = new Estudiantes($db);

		$filasGenero = $estudiantes_obj->getTitulosGenero();
		$generoM = $estudiantes_obj->getGenero($jornada, 'F',$ano);
		$generoH = $estudiantes_obj->getGenero($jornada, 'M',$ano);

		$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grupo);		


		$edades = array();

		for ($i=1; $i < 23 ; $i++) { 
			$edades[$i] = $i;
		}
		if($jornada == "0"){
			$edades= 0;
		}
		header("Access-Control-Allow-Origin: *");
		$view = new View(
			'estudiantes', 
			'Estudiantes', 
			[				
			'filasGenero' => $filasGenero,
			'edades' => $edades,
			'generoM' => $generoM,
			'generoH' => $generoH

			]);

		$view->execute();

		$_POST['data']=null;		

	}



	public function getEficienciaAction($db){

		$periodo = $_POST['periodo'];		
		$grupo = $_POST['grupo'];
		$area = $_POST['area'];				
		$reprobados = $_POST['reprobados'];	
		$academicas	= $_POST['academicas'];
		$numero	= $_POST['numero'];
		$operador	= $_POST['operador'];
		$academicas	= $_POST['academicas'];
		$jornada	= $_POST['jornada'];
		$ano = $_POST['ano'];

		$puestoPromedio = array();
		$estudiantesPuestos = array();
		$estudiantesRepro = array();
		
		$informacionGrupo_obj = new Institucion($db);
		$estudiantes_obj = new Estudiantes($db);

		$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grupo);	

		$matriculaInicialF = $estudiantes_obj->getMatriculadosInicial($jornada, 'F', $ano);
		$matriculaInicialM = $estudiantes_obj->getMatriculadosInicial($jornada, 'M', $ano);

		$matriculaFinalF = $estudiantes_obj->getMatriculadosFinal($jornada, 'F', $ano);
		$matriculaFinalM = $estudiantes_obj->getMatriculadosFinal($jornada, 'M', $ano);

		
		$matriculaReprobadosF = $estudiantes_obj->getMatriculadosNovedades($jornada, 'F', $ano, Estudiantes::$REPRO);
		$matriculaReprobadosM = $estudiantes_obj->getMatriculadosNovedades($jornada, 'M', $ano, Estudiantes::$REPRO);

		$matriculaDesertoresF = $estudiantes_obj->getMatriculadosNovedades($jornada, 'F', $ano, Estudiantes::$DESERT);
		$matriculaDesertoresM = $estudiantes_obj->getMatriculadosNovedades($jornada, 'M', $ano, Estudiantes::$DESERT);

		$matriculaTrasladadosF = $estudiantes_obj->getMatriculadosNovedades($jornada, 'F', $ano, Estudiantes::$TRANSL);
		$matriculaTrasladadosM = $estudiantes_obj->getMatriculadosNovedades($jornada, 'M', $ano, Estudiantes::$TRANSL);

		$matriculaAprobadosF = $estudiantes_obj->getMatriculadosNovedades($jornada, 'F', $ano, Estudiantes::$APROB);
		$matriculaAprobadosM = $estudiantes_obj->getMatriculadosNovedades($jornada, 'M', $ano, Estudiantes::$APROB);

		$matriculaRetiradosF = $estudiantes_obj->getMatriculadosNovedades($jornada, 'F', $ano, Estudiantes::$RETIR);
		$matriculaRetiradosM = $estudiantes_obj->getMatriculadosNovedades($jornada, 'M', $ano, Estudiantes::$RETIR);

		$matriculaGruposF = $estudiantes_obj->getMatriculadosGrupos($jornada, 'F', $ano);
		$matriculaGruposM = $estudiantes_obj->getMatriculadosGrupos($jornada, 'M', $ano);

		$gruposCantidad = $estudiantes_obj->getCantidadGrupos($jornada, $ano);
		$grados = $informacionGrupo_obj->getGrados();
			

		
			//var_dump($matriculaFinalM);
			header("Access-Control-Allow-Origin: *");
			$view = new View(
				'estudiantes', 
				'Eficiencia', 
				[	
					'matriculaInicialF' => $matriculaInicialF,
					'matriculaInicialM' => $matriculaInicialM,
					'matriculaRetiradosF' => $matriculaRetiradosF,
					'matriculaRetiradosM' => $matriculaRetiradosM,
					'matriculaDesertoresM' => $matriculaDesertoresM,
					'matriculaDesertoresF' => $matriculaDesertoresF,
					'matriculaTrasladadosM' => $matriculaTrasladadosM,
					'matriculaTrasladadosF' => $matriculaTrasladadosF,
					'matriculaGruposM' => $matriculaGruposM,
					'matriculaGruposF' => $matriculaGruposF,
					'matriculaFinalF' => $matriculaFinalF,
					'matriculaFinalM' => $matriculaFinalM,
					'matriculaReprobadosF' => $matriculaReprobadosF,
					'matriculaReprobadosM' => $matriculaReprobadosM,
					'matriculaAprobadosM' => $matriculaAprobadosM,
					'matriculaAprobadosF' => $matriculaAprobadosF,
					'grados'	=> $grados['datos'],
					'gruposCantidad' => $gruposCantidad

				]);

			$view->execute();

			$_POST['data']=null;		

		}












	}
	?>