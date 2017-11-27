<?php
namespace Controllers;
use Config\View 	as View;
use Model\FPDIModel as FPDI;
use Model\FPDFModel as FPDF;
use Helpers\Calcular as Calcular;
use Model\ReporteModel as ReporteModel;
use Model\PeriodosModel as Periodos;
use Model\ValoracionModel as Valoracion;
use Model\ReprobadasModel as Reprobadas;
use Model\InstitucionModel as Institucion;
use Model\ConsolidadosModel as Consolidados;
use Model\PorcentualesModel as Porcentuales;
use Model\PromedioGrupoModel as PromedioGrupo;
use Helpers\GenerarPuestos as PuestosPromedios;
use ModelPDF\DissaproveStudentPDF as DissaproveStudentPDF;



class PDFController{

	function __construct(){
		$this->puestosPromedios_obj = new PuestosPromedios();
	}

	public function getReportesAction(){

		$db = $_POST['db'];
		$arrayGrupos = array();
		$grado = $_POST['grado'];
		$grupo = $_POST['grupo'];
		$informacionGrupo_obj = new Institucion($db);
		$obj_reporte = new ReporteModel($db);


		$path= array();
		/* Verificados si PDF por grado fue seleccionado, si lo es,
		generamos un array de todos los grupos del grado actual	*/
		if(isset($_POST['isGrado'])){

			# Obtenemos todos los grupos del grado seleccionado
			$result = $informacionGrupo_obj->getGrupos($grado)['datos'];
			foreach ($result as $value) {
				# Creamos un arreglo con todos los id de los grupos del grado seleccionado
				array_push($arrayGrupos, $value['id_grupo']);
			}
		}else{
			# Creamos un arreglo con él único id del grupo seleccionado
			array_push($arrayGrupos, $grupo);
		}
		$contador = 0;
		foreach ($arrayGrupos as $grup) {

			$dir = "template-pdf";
			if (!file_exists($dir)) {
				mkdir($dir, 777);
			}


			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grup);
			$infoStudents = $obj_reporte->getInfoStudents($grup);

			$pdfiStudents = new FPDI();
			$pdfiStudents->generarReporte($infoStudents, $informacionGrupo);
			$path[$contador] = $dir."/".$grup.".pdf";
			$pdfiStudents->Output($path[$contador], "F");
			$contador++;
			ob_clean();

		}

		$pdfi = new FPDI();
		$pdfi->setFiles($path);
		$pdfi->concat();
		ob_clean();
		$pdfi->Output('ficha_academica.pdf', 'D');


	}

	public function getConsolidadoAction(){


		$arrayGrupos = array();
		$tablaPuestos =array();
		$puestoPromedio = array();
		$tablaConsolidados = array();
		$estudiantesPuestos = array();
		$estudiantesPromedios = array();
		$asignaturasEvaluadas = array();

		$db =$_POST['db'];
		$area = $_POST['area'];
		$grado = $_POST['grado'];
		$grupo = $_POST['grupo'];
		$periodo = $_POST['periodo'];
		$reprobados = $_POST['reprobados'];
		$academicas	= $_POST['academicas'];
		$informe = $_POST['informe']=="1"?true:false;
		$periodos_acumulados = $_POST['per_acumulados']=="1"?true:false;
		

		$periodos_obj = new Periodos($_POST['db']);
		$valoracion_obj = new Valoracion($_POST['db']);
		$consolidado_obj = new Consolidados($_POST['db']);
		$informacionGrupo_obj = new Institucion($_POST['db']);
		$_calcular = new Calcular($db);

		$periodosAll = $periodos_obj->getPeriodos()['datos'];
		$valoraciones = $valoracion_obj->obtenerValoraciones();
		$result_per = $periodos_obj->getPeriodosEvaluados()['datos'];


		$num_periodos = [];
		$peso_periodos = [];
		$periodos_evaluados = [];
		$isCalcular = false;

		# Si periodos acumulado ha sido seleccionado
		if (!$periodos_acumulados  && !$informe) {
			$num_periodos[0] = $periodo;

		}else{
			# $result_per contiene todos los periodos evaluados
			foreach ($result_per as $key => $value) {
				# Guardamos en el arreglo $num_periodos cada uno de los periodos evaluados
				$num_periodos[$key] = $value['periodos'];
				$peso_periodos[$key] = $value['peso'];
			}
			$isCalcular = true;			
		}

		# Se guardará la ruta de cada pdf por grupos
		$path= array();

		/* Verificados si PDF por grado fue seleccionado, si lo es,
		generamos un array de todos los grupos del grado actual	*/
		if(isset($_POST['isGrado'])){

			# Obtenemos todos los grupos del grado seleccionado
			$result = $informacionGrupo_obj->getGrupos($grado)['datos'];
			foreach ($result as $value) {
				# Creamos un arreglo con todos los id de los grupos del grado seleccionado
				array_push($arrayGrupos, $value['id_grupo']);
			}
		}else{
			# Creamos un arreglo con él único id del grupo seleccionado
			array_push($arrayGrupos, $grupo);
		}


		# Se usará como indice: $path[$cont] = ruta_de_pdf_por_grupo
		$cont=0;

		/*
		Recorremos cada unos de los id_grupos y generamos para cada grupo su concolidado en pdf,
		para luego unir todos los pdf con merge
		*/
		foreach ($arrayGrupos as $grup) {

			$dir = "doc/$grado";
			if (!file_exists($dir)) {
				mkdir($dir, 777);
			}

			$contador = 0;

			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grup);

			foreach($num_periodos as $key => $_periodo){
				# Valoraciones para asignaturas
				if($consolidado_obj->getPromediosAsiganturas($grup, $_periodo,$academicas) != false){

					$periodos_evaluados[$contador] = $_periodo;

					if($area=="0"){
						$tablaConsolidados[$contador] = $consolidado_obj->getPromediosAsiganturas($grup, $_periodo,$academicas);
						$asignaturasEvaluadas = $consolidado_obj->getAsignaturasEvaludadas($grup, $_periodo,$academicas,$reprobados);

						if($reprobados == "0")
							$estudiantesPromedios[$contador] = $consolidado_obj->getEstudiantesPromedios($grup, $_periodo,$academicas,$reprobados);

						if($reprobados == "1")
							$estudiantesPromedios[$contador] = $consolidado_obj->getEstudiantesPromediosReprobados($grup, $_periodo,$academicas,$reprobados);

						$estudiantesPuestos[$contador] = $consolidado_obj->getEstudiantesPromedios($grup, $_periodo, $academicas);
						$puestoPromedio[$contador] = $this->puestosPromedios_obj->obtenerPromedios($estudiantesPuestos[$contador], $db);
					}

					# Valoraciones para areas
					if($area=="1"){
						$tablaConsolidados[$contador] = $consolidado_obj->getPromediosAreas($grup, $_periodo,$academicas);
						$asignaturasEvaluadas = $consolidado_obj->getAreasEvaluadas($grup, $_periodo,$academicas,$reprobados);

						if($reprobados == "0" || $reprobados == "")
							$estudiantesPromedios[$contador] = $consolidado_obj->getEstudiantesPromediosAreas($grup, $_periodo,$academicas,$reprobados);

						if($reprobados == "1")
							$estudiantesPromedios[$contador] = $consolidado_obj->getEstudiantesAreasReprobadas($grup, $_periodo,$academicas,$reprobados);

						$estudiantesPuestos[$contador] = $consolidado_obj->getEstudiantesPromediosAreas($grup, $_periodo,$academicas);
						$puestoPromedio[$contador] = $this->puestosPromedios_obj->obtenerPromedios($estudiantesPuestos[$contador], $db);

					}
					$contador++;
				}
			}


			$titulosFijo = array('No.', 'NOMBRES Y APELLIDOS', 'Pto.','PGG','Periodo','Tav');

			foreach ($asignaturasEvaluadas as $asignaturas) {
				array_push($titulosFijo, $asignaturas['n_simpl']);
			}

			# Se guarda la ruta del pdf a generar
			$path[$cont] = $dir."/".$grup.".pdf";
			$periodo_pdf = $periodos_acumulados?'PERIODOS ACUMULADOS':$periodo;


			$header = array_merge($titulosFijo);
			$pdf = new FPDF();
			$pdf->TableHeader($informacionGrupo, $periodo_pdf, $header, 'LISTADO DE PERIODOS ACUMULADOS');
			$pdf->AddPage('L','Legal');
			$pdf->SetFont('Arial','B',8);

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



			$pdf->ConsolidadoTable(
				$header,
				$estudiantesPromedios,
				$puestoPromedio,
				$asignaturasEvaluadas,
				$tablaConsolidados,
				$valoraciones[1]['maximo'],
				$valoraciones,
				$periodos_evaluados,
				$peso_periodos,
				$periodos_acumulados,
				$periodosAll,
				($reprobados=="1"?true:false),
				$informe
			);
			ob_clean();
			$pdf->Output($path[$cont], "F");
			$cont++;

		}

		
		$pdfi = new FPDI();
		$pdfi->setFiles($path);
		$pdfi->concat();
		ob_clean();
		$pdfi->Output($grado.'.pdf', 'D');


	}


	public function getPromedioGrupoAction(){

		$grupo = $_POST['grupo'];
		$periodo = $_POST['periodo'];
		$db =$_POST['db'];
		$reprobados = $_POST['reprobados'];
		$academicas	= $_POST['academicas'];
		$area = $_POST['area'];
		$tablaPuestos =array();
		$grado = $_POST['grado'];

		$arrayGrupos = array();
		$tablaPuestos =array();

		$estudiantesPuestos = array();


		$promedioGrupo_obj = new PromedioGrupo($db);
		$informacionGrupo_obj = new Institucion($db);
		$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grupo);
		$estudiantesPuestos = array();
		$puestoPromedio = array();

		$path= array();

		if(isset($_POST['isGrado'])){
			$isGrado=1;

			$result = $informacionGrupo_obj->getGrupos($grado)['datos'];
			foreach ($result as $value) {
				array_push($arrayGrupos, $value['id_grupo']);
			}
		}else{
			$isGrado = 0;
			array_push($arrayGrupos, $grupo);
		}

		$cont=0;

		foreach ($arrayGrupos as $grup) {


			$dir = "doc/$grado";

			if (!file_exists($dir)) {
				mkdir($dir, 777);
			}

			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grup);

			if ($area == "0") {
				if ($reprobados == "0") {
					$tablaPuestos = $promedioGrupo_obj->getPromedioPuestos($grup, $periodo, $academicas);
				}
				if ($reprobados == "1") {
					$tablaPuestos = $promedioGrupo_obj->getPromedioPuestosReprobados($grup, $periodo, $academicas);
				}
				$estudiantesPuestos = $promedioGrupo_obj->getPromedioPuestos($grup, $periodo, $academicas);
				$puestoPromedio = $this->puestosPromedios_obj->obtenerPromedios($estudiantesPuestos, $db);
			}
			if ($area == "1") {

				if ($reprobados == "0") {
					$tablaPuestos = $promedioGrupo_obj->getPromedioPuestosAreas($grup, $periodo, $academicas);
				}
				if ($reprobados == "1") {
					$tablaPuestos = $promedioGrupo_obj->getPromedioPuestosAreasReprobados($grup, $periodo, $academicas);
				}
				$estudiantesPuestos = $promedioGrupo_obj->getPromedioPuestosAreas($grup, $periodo, $academicas);
				$puestoPromedio = $this->puestosPromedios_obj->obtenerPromedios($estudiantesPuestos, $db);

			}

			if($tablaPuestos){
				$path[$cont] = $dir."/".$grup.".pdf";


				$header = array('No.', 'NOMBRES Y APELLIDOS', 'Pto.', 'S', "A", "Bs", "Bj", "TAV", "PROMEDIO", "DESEMPEÑO");
				$pdf = new FPDF();
				$pdf->TableHeader($informacionGrupo, $periodo);
				$pdf->AddPage();
				$pdf->SetFont('Arial', 'B', 8);
				$pdf->FancyTable($header, $tablaPuestos, $puestoPromedio);
				ob_clean();
				$pdf->Output($path[$cont], "F");

				$cont++;
			}
		}

		$pdfi = new FPDI();
		$pdfi->setFiles($path);
		$pdfi->concat();
		ob_clean();
		$pdfi->Output($grado.'.pdf', 'D');

	}


	public function getPorcentualesAction(){
		$grupo = $_POST['grupo'];
		$periodo = $_POST['periodo'];
		$db =$_POST['db'];
		$reprobados = $_POST['reprobados'];
		$academicas	= $_POST['academicas'];
		$area = $_POST['area'];
		$tablaPuestos =array();
		$grado = $_POST['grado'];

		$arrayGrupos = array();
		$tablaPuestos =array();

		$estudiantesPuestos = array();


		$promedioGrupo_obj = new Porcentuales($db);
		$informacionGrupo_obj = new Institucion($db);
		$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grupo);
		$estudiantesPuestos = array();
		$puestoPromedio = array();

		$path= array();

		if(isset($_POST['isGrado'])){
			$isGrado=1;

			$result = $informacionGrupo_obj->getGrupos($grado)['datos'];
			foreach ($result as $value) {
				array_push($arrayGrupos, $value['id_grupo']);
			}
		}else{
			$isGrado = 0;
			array_push($arrayGrupos, $grupo);
		}

		$cont=0;

		foreach ($arrayGrupos as $grup) {


			$dir = "doc/$grado";

			if (!file_exists($dir)) {
				mkdir($dir, 777);
			}

			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grup);

			if ($area == "0") {
				if ($reprobados == "0") {
					$tablaPuestos = $promedioGrupo_obj->getAsignaturasPorcentuales($grup, $periodo, $academicas);
				}
				if ($reprobados == "1") {
					$tablaPuestos = $promedioGrupo_obj->getAsignaturasPorcentualesReprobados($grup, $periodo, $academicas);
				}
				$estudiantesPuestos = $promedioGrupo_obj->getAsignaturasPorcentuales($grup, $periodo, $academicas);
				$puestoPromedio = $this->puestosPromedios_obj->obtenerPromediosAsig($estudiantesPuestos, $db);
			}
			if ($area == "1") {

				if ($reprobados == "0") {
					$tablaPuestos = $promedioGrupo_obj->getAsignaturasPorcentuales($grup, $periodo, $academicas);
				}
				if ($reprobados == "1") {
					$tablaPuestos = $promedioGrupo_obj->getAsignaturasPorcentualesReprobados($grup, $periodo, $academicas);
				}
				$estudiantesPuestos = $promedioGrupo_obj->getAsignaturasPorcentuales($grup, $periodo, $academicas);
				$puestoPromedio = $this->puestosPromedios_obj->obtenerPromediosAsig($estudiantesPuestos, $db);

			}

			if($tablaPuestos){
				$path[$cont] = $dir."/".$grup.".pdf";


				$header = array('No.', 'NOMBRE ASIGNATURAS', 'Pto.', 'S', '%', "A",'%', "Bs",'%', "Bj",'%', "TAV", "PROMEDIO", "DESEMPEÑO");
				$pdf = new FPDF();
				$pdf->TableHeader($informacionGrupo, $periodo);
				$pdf->AddPage('L');
				$pdf->SetFont('Arial', 'B', 8);
				$pdf->PorcentualesTable($header, $tablaPuestos, $puestoPromedio);
				ob_clean();
				$pdf->Output($path[$cont], "F");

				$cont++;
			}
		}

		$pdfi = new FPDI();
		$pdfi->setFiles($path);
		$pdfi->concat();
		ob_clean();
		$pdfi->Output($grado.'.pdf', 'D');
	}





	public function getReprobadasFiltroAction(){
		$grado = $_POST['grado'];

		$path = array();

		if(!empty($_POST)):
			$path = $this->resolveDataFiltro($_POST);
		endif;

		$pdfi = new FPDI();
		$pdfi->setFiles($path);
		$pdfi->concat();
		ob_clean();
		$pdfi->Output($grado.'.pdf', 'I');
	}


	public function getReprobadasAction(){
		$grado = $_POST['grado'];


		$path = array();

		if(!empty($_POST)):
			$path = $this->resolveData($_POST);
		endif;

		$pdfi = new FPDI();
		$pdfi->setFiles($path);
		$pdfi->concat();
		ob_clean();
		$pdfi->Output($grado.'.pdf', 'I');
	}

	//
	private function resolveData($data=array()){

		$periodo = $data['periodo'];
		$grupo = $data['grupo'];
		$area = $data['area'];
		$grado = $data['grado'];
		$reprobados = $data['reprobados'];
		$academicas	= $data['academicas'];
		$puestoPromedio = array();
		$estudiantesPuestos = array();
		$arrayGrupos = array();

		$reprobadas_obj = new Reprobadas($data['db']);
		$informacionGrupo_obj = new Institucion($_POST['db']);



		if(isset($data['isGrado'])){
			$isGrado=1;

			$result = $informacionGrupo_obj->getGrupos($grado)['datos'];
			foreach ($result as $value) {
				array_push($arrayGrupos, $value['id_grupo']);
			}


		}else{
			$isGrado = 0;
			array_push($arrayGrupos, $grupo);
		}

		$cont=0;
		foreach ($arrayGrupos as $grup) {


			$dir = "doc/$grado";

			if (!file_exists($dir)) {
				mkdir($dir, 777);
			}

			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grup);

			if($area=="0"){

				$tablaPuestos = $reprobadas_obj->getEstudiantesAsiganturasRepro($grup, $periodo, $academicas);

			}


			if($area=="1")
			{
				$tablaPuestos = $reprobadas_obj->getEstudiantesAareasRepro($grup, $periodo, $academicas);
			}

			$id_student = 0;
			$resp = array();

			foreach($tablaPuestos as $key => $value){

				if($id_student != $value['id_estudiante']):

					$id_student = $value['id_estudiante'];
					$studentData = array(
						'id_estudiante'	=>	$value['id_estudiante'],
						'nombre'		=>	$value['primer_apellido']." ".$value['segundo_apellido']." ".$value['primer_nombre']." ".$value['segundo_nombre'],
						'asignaturas'	=>	array()

					);
					foreach ($tablaPuestos as $key => $data):

						if($data['id_estudiante'] == $value['id_estudiante']):
							array_push($studentData['asignaturas'],
								array(
									'id_asignatura'	=>	$data['id_asignatura'],
									'asignatura'	=>	$data['Asignatura'],
									'inasistencia'	=>	$data['Inasistencia'],
									'order'			=>	$data['order_area'],
									'valoracion'	=>	$data['valoracion']
								)
							);
						endif;

					endforeach;

					array_push($resp, $studentData);
				endif;

			}

			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grup);
			$header = array('No.', 'NOMBRES Y APELLIDOS', 'ASIGNATURAS', 'FAA', 'VAL', 'SUP');

			$path[$cont] = $dir."/".$grup.".pdf";

			$pdf = new DissaproveStudentPDF('P', $unit='mm', 'Letter');
			$pdf->TableHeader($informacionGrupo,$periodo);
			$pdf->AddPage();
			$pdf->FancyTable($header, $resp);
			ob_clean();
			$pdf->Output($path[$cont], "F");

			$cont++;
		}

		return $path;
	}


	private function resolveDataFiltro($data=array()){

		$periodo = $data['periodo'];
		$grupo = $data['grupo'];
		$area = $data['area'];
		$grado = $data['grado'];
		$reprobados = $data['reprobados'];
		$academicas	= $data['academicas'];

		$numero	= $_POST['cantidad'];
		$operador	= $_POST['operador'];

		$puestoPromedio = array();
		$estudiantesPuestos = array();
		$arrayGrupos = array();

		$reprobadas_obj = new Reprobadas($data['db']);
		$informacionGrupo_obj = new Institucion($_POST['db']);



		if(isset($data['isGrado'])){
			$isGrado=1;

			$result = $informacionGrupo_obj->getGrupos($grado)['datos'];
			foreach ($result as $value) {
				array_push($arrayGrupos, $value['id_grupo']);
			}


		}else{
			$isGrado = 0;
			array_push($arrayGrupos, $grupo);
		}

		$cont=0;
		foreach ($arrayGrupos as $grup) {


			$dir = "doc/$grado";

			if (!file_exists($dir)) {
				mkdir($dir, 777);
			}

			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grup);

			if($area=="0"){

				$tablaPuestos = $reprobadas_obj->getEstudiantesAsiganturasRepro($grup, $periodo, $academicas);
				$estudiantesRepro = $reprobadas_obj->getEstudiantesRepro($grup, $periodo, $academicas, $operador, $numero);

			}


			if($area=="1")
			{
				$tablaPuestos = $reprobadas_obj->getEstudiantesAareasRepro($grup, $periodo, $academicas);
				$estudiantesRepro = $reprobadas_obj->getEstudiantesReproA($grup, $periodo, $academicas, $operador, $numero);
			}

			$id_student = 0;
			$resp = array();



			foreach($estudiantesRepro as $key => $value){


				if($id_student != $value['id_estudiante']):

					$id_student = $value['id_estudiante'];
					$studentData = array(
						'id_estudiante'	=>	$value['id_estudiante'],
						'nombre'		=>	$value['primer_apellido']." ".$value['segundo_apellido']." ".$value['primer_nombre']." ".$value['segundo_nombre'],
						'asignaturas'	=>	array()

					);
					foreach ($tablaPuestos as $key => $data):

						if($data['id_estudiante'] == $value['id_estudiante']):
							array_push($studentData['asignaturas'],
								array(
									'id_asignatura'	=>	$data['id_asignatura'],
									'asignatura'	=>	$data['Asignatura'],
									'inasistencia'	=>	$data['Inasistencia'],
									'order'			=>	$data['order_area'],
									'valoracion'	=>	$data['valoracion']
								)
							);
						endif;

					endforeach;

					array_push($resp, $studentData);
				endif;

			}

			$informacionGrupo = $informacionGrupo_obj->getInformacionGrupo($grup);
			$header = array('No.', 'NOMBRES Y APELLIDOS', 'ASIGNATURAS', 'FAA', 'VAL', 'SUP');

			$path[$cont] = $dir."/".$grup.".pdf";

			$pdf = new DissaproveStudentPDF('P', $unit='mm', 'Letter');
			$pdf->TableHeader($informacionGrupo,$periodo);
			$pdf->AddPage();
			$pdf->FancyTable($header, $resp);
			ob_clean();
			$pdf->Output($path[$cont], "D");

			$cont++;
		}

		return $path;
	}




}
?>
