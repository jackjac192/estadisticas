<?php
namespace Model;
use Libs\FPDF as FPDF;
use Libs\FPDI as FPDI;

class FPDIModel extends FPDI
{
    public $files = array();

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function generarReporte($infoStudents, $informacionGrupo){

      $informacionGrupo = $informacionGrupo[0];

      foreach ($infoStudents as $estudiante) {

      $this->setSourceFile('template-pdf/ficha.pdf');
  		$pageId = $this->importPage(1);
  		$this->addPage('P','Letter');
  		$this->useTemplate($pageId,null, null, null, null, true);

      $this->SetFillColor(255,255,255);
      $this->SetTextColor(0);
      $this->SetDrawColor(0,0,0);
      $this->SetLineWidth(.1);
  		$this->SetFont('Arial','B', 12);
   		$this->SetTextColor(0,0,0);
      # Logo y nombre de la institución
      if($informacionGrupo['logo_byte'] != NULL){
          $pic = 'data:image/png;base64,'.base64_encode($informacionGrupo["logo_byte"]);
          $this->Image($pic, 194, 5, 15, 15, 'png');
      }
      $this->Cell(180,10,strtoupper(utf8_decode($informacionGrupo["nombre_inst"])),0,1,'C');
      $lineas = 0;
      $this->SetFont('Arial','', 6);
      $this->setY(35);
      date_default_timezone_set('UTC');

      # Sesion formato de matricula de estudiante
   		$this->Cell(119.5,8,"",$lineas,0,'C');
      if(1==0){
        //Nuevo
        $this->Cell(39.5,8,"X",$lineas,0,'C');
        $this->Cell(39.5,8,"",$lineas,1,'C');
      }else{
        //Continuidad
        $this->Cell(39.5,8,"",$lineas,0,'C');
        $this->Cell(40,8,"X",$lineas,1,'C');
      }
      $this->ln(4);
      # Fecha Matricula
      $this->Cell(99.5,4,"",$lineas,0,'C');
      $this->Cell(20,4,$estudiante['grado_siguiente'],$lineas,0,'C');
      $this->Cell(19.5,4,"",$lineas,0,'C');
      $this->Cell(20,4,"",$lineas,0,'C');
      $this->Cell(19.5,4,"",$lineas,0,'C');
      $this->Cell(20,4,date("Y")+1,$lineas,0,'C');

      $this->ln(12);
      #Datos de la institución
      $this->Cell(79.5,4,strtoupper(utf8_decode($informacionGrupo["nombre_inst"])),$lineas,0,'C');
      $this->Cell(59.5,4,strtoupper(utf8_decode($informacionGrupo["sede"])),$lineas,0,'C');
      $this->Cell(59.5,4,strtoupper(utf8_decode(".Buenaventura")),$lineas,0,'C');
      $this->ln(4);
      $this->Cell(19,4,"",$lineas,0,'C');
      $this->Cell(40.7,4,strtoupper(utf8_decode($informacionGrupo['documento'])),$lineas,0,'');
      $this->Cell(19,4,"",$lineas,0,'C');
      $this->Cell(60.5,4,strtoupper(utf8_decode($informacionGrupo['primer_apellido']." ".$informacionGrupo['primer_apellido']." ".$informacionGrupo['primer_nombre'])),$lineas,0,'');
      $this->Cell(19,4,"",$lineas,0,'C');
      $this->Cell(40.7,4,"",$lineas,0,'C');
      $this->ln(16);

      #Datos de identificación
      $tipo_doc = $estudiante['tipo_identificacion'];
      $this->Cell(4.7,3.6,"",$lineas,0,'C'); $this->Cell(4.9,3.6,($tipo_doc=="CC"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C'); $this->Cell(5,3.6,($tipo_doc=="NUIP"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C'); $this->Cell(5,3.6,($tipo_doc=="TI"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C'); $this->Cell(5,3.6,($tipo_doc=="CE"?"X":""),$lineas,0,'C');

      $this->Cell(39.7,3.6,$estudiante['numero_documento'],$lineas,0,'C');
      $this->Cell(19.8,3.6,$estudiante['anos_cumplidos'],$lineas,0,'C');
      $this->Cell(29.8,3.6,strtoupper(utf8_decode($estudiante['depto_expedicion'])),$lineas,0,'C');
      $this->Cell(29.8,3.6,strtoupper(utf8_decode($estudiante['mun_expedicion'])),$lineas,0,'C');

      #Marcar con x Masculino o Femenino
      $tipo_sexo = $estudiante['genero'];
      $this->Cell(14.9,3.6,"",$lineas,0,'C'); $this->Cell(5,3.6,($tipo_sexo=="M"?"X":""),$lineas,0,'C');
      $this->Cell(14.9,3.6,"",$lineas,0,'C'); $this->Cell(5,3.6,($tipo_sexo=="F"?"X":""),$lineas,0,'C');

      $this->ln(12);
      $this->Cell(29.8,3.6,strtoupper(utf8_decode($estudiante['primer_apellido'])),$lineas,0,'C');
      $this->Cell(29.8,3.6,strtoupper(utf8_decode($estudiante['segundo_apellido'])),$lineas,0,'C');
      $this->Cell(29.8,3.6,strtoupper(utf8_decode($estudiante['primer_nombre'])),$lineas,0,'C');
      $this->Cell(29.8,3.6,strtoupper(utf8_decode($estudiante['segundo_nombre'])),$lineas,0,'C');

      $this->Cell(24.8,3.6,"",$lineas,0,'C');//Depar nacimiento
      $this->Cell(24.8,3.6,"",$lineas,0,'C');//Municipio nacimiento





      if(!isset($estudiante['fecha_nacimiento'])){
        $fecha_nacimiento_est = array(0 =>"", 1 => "", 2 => "" );;
      }else {
        $fecha_nacimiento_est = explode("-",$estudiante['fecha_nacimiento']);
      }

      #fecha nacimiento
      $this->Cell(9.9,3.6,$fecha_nacimiento_est[2],$lineas,0,'C'); #Día
      $this->Cell(9.9,3.6,$fecha_nacimiento_est[1],$lineas,0,'C'); #Mes
      $this->Cell(9.9,3.6,$fecha_nacimiento_est[0],$lineas,0,'C'); #Año

      $this->ln(12);
      $this->Cell(39.9,3.6,strtoupper(utf8_decode($estudiante['direccion'])),$lineas,0,'C');
      $this->Cell(39.9,3.6,strtoupper(utf8_decode($estudiante['barrio'])),$lineas,0,'C');

      $tipo_zona = strtoupper($estudiante['zona']);
      $this->Cell(9.9,3.6,($tipo_zona!="rural"?"X":""),$lineas,0,'C');
      $this->Cell(9.9,3.6,($tipo_zona=="rural"?"X":""),$lineas,0,'C');
      $this->Cell(29.8,3.6,"",$lineas,0,'C'); #departamento Residencia
      $this->Cell(29.8,3.6,"",$lineas,0,'C'); #Municipio Residencia
      $this->Cell(39.7,3.6,$estudiante['telefono'],$lineas,0,'C');

      #Marcar con x
      if($estudiante['grado_siguiente'] <= 0)
          $nivel_escolar = "PREESCOLAR";
      elseif($estudiante['grado_siguiente'] > 0 && $estudiante['grado_siguiente'] <= 5)
          $nivel_escolar = "BASICA";
      elseif($estudiante['grado_siguiente']> 5 || $estudiante['grado_siguiente'] == "EGRESADO")
              $nivel_escolar = "SECUNDARIA";
      else
        $nivel_escolar = "PREESCOLAR";



      $grado_ingresa = $estudiante['grado_siguiente'];
      $this->ln(8);
      $this->Cell(188.9,3.6,"",$lineas,0,'C'); $this->Cell(9.9,3.6,($nivel_escolar=="PREESCOLAR"?"X":""),$lineas,0,'C');
      $this->ln(4);
      $this->Cell(188.9,3.6,"",$lineas,0,'C'); $this->Cell(9.9,3.6,($nivel_escolar=="BASICA"?"X":""),$lineas,0,'C');
      $this->ln(4);
      $this->Cell(99.4,3.6,"",$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="0"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="1"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="2"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="3"?"X":""),$lineas,0,'C');
      $this->Cell(5.2,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="4"?"X":""),$lineas,0,'C');

      $this->Cell(39.8,3.6,"",$lineas,0,'C'); $this->Cell(9.9,3.6,($nivel_escolar=="SECUNDARIA"?"X":""),$lineas,0,'C');

      $this->ln(4);
      ## Información Académica
      $this->Cell(9.9,3.6,strtoupper(utf8_decode($estudiante['grado'])),$lineas,0,'C');
      $this->Cell(9.9,3.6,"2017",$lineas,0,'C');
      $this->Cell(49.7,3.6,"I. E. NORMAL SUPERIOR JUAN LADRILLEROS",$lineas,0,'C');
      $this->Cell(9.9,3.6,"",$lineas,0,'C');
      $this->Cell(9.9,3.6,"",$lineas,0,'C');
      $this->Cell(9.9,3.6,"",$lineas,0,'C');

      $this->Cell(5,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="5"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="6"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="7"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="8"?"X":""),$lineas,0,'C');
      $this->Cell(5.2,3.6,"",$lineas,0,'C');$this->Cell(4.9,3.6,($grado_ingresa=="9"?"X":""),$lineas,0,'C');

      $this->ln(12);

      $subsidiado = strtoupper(utf8_decode($estudiante['subsidado']));
      $interno = strtoupper(utf8_decode($estudiante['interno']));
      $otro_modelo = strtoupper(utf8_decode($estudiante['otro_modelo']));
      $caracter = strtoupper(utf8_decode($estudiante['caracter']));
      $especialidad = strtoupper(utf8_decode($estudiante['especialidad']));

      $this->Cell(9.9,3.6,($subsidiado=="S"?"X":""),$lineas,0,'C');
      $this->Cell(9.9,3.6,($subsidiado=="N"?"X":""),$lineas,0,'C');
      $this->Cell(9.9,3.6,($interno=="S"?"X":""),$lineas,0,'C');
      $this->Cell(9.9,3.6,($interno=="N"?"X":""),$lineas,0,'C');

      $this->Cell(9.9,3.6,($otro_modelo=="N1"?"X":""),$lineas,0,'C');
      $this->Cell(9.9,3.6,($otro_modelo=="N2"?"X":""),$lineas,0,'C');
      $this->Cell(19.9,3.6,($otro_modelo=="A"?"X":""),$lineas,0,'C');

      $this->Cell(9.9,3.6,($grado_ingresa=="10"?"X":""),$lineas,0,'C');
      $this->Cell(10,3.6,($grado_ingresa=="11"?"X":""),$lineas,0,'C');

      $this->Cell(9.9,3.6,($caracter=="A"?"X":""),$lineas,0,'C');
      $this->Cell(10,3.6,($caracter=="T"?"X":""),$lineas,0,'C');

      $this->Cell(19.87,3.6,($especialidad=="C"?"X":""),$lineas,0,'C');
      $this->Cell(19.87,3.6,($especialidad=="A"?"X":""),$lineas,0,'C');
      $this->Cell(20,3.6,($especialidad=="T"?"X":""),$lineas,0,'C');
      $this->Cell(19.87,3.6,($especialidad=="N"?"X":""),$lineas,0,'C');

      #Sistema de salud
      $this->ln(12);
      $this->Cell(49.7,3.6,strtoupper(utf8_decode($estudiante['eps'])),$lineas,0,'C');
      $this->Cell(49.7,3.6,strtoupper(utf8_decode($estudiante['ips'])),$lineas,0,'C');
      $this->Cell(49.7,3.6,strtoupper(utf8_decode($estudiante['tipo_sangre'])),$lineas,0,'C');
      $this->Cell(49.7,3.6,strtoupper(utf8_decode($estudiante['ars'])),$lineas,0,'C');

      #Programas Especiales
      $victima_conflicto = $estudiante['victima_conflicto'];
      $this->ln(8);
      $this->Cell(39.78,3.6,"",$lineas,0,'C'); $this->Cell(19.89,3.6,($victima_conflicto=="1"?"X":""),$lineas,0,'C');
      $this->Cell(79.56,3.6,"",$lineas,0,'C');
      $this->Cell(39.78,3.6,"",$lineas,0,'C');
      $this->Cell(19.89,3.6,"",$lineas,0,'C');

      $this->ln(4);
      $this->Cell(39.78,3.6,"",$lineas,0,'C'); $this->Cell(19.89,3.6,($victima_conflicto=="2"?"X":""),$lineas,0,'C');
      $this->Cell(79.56,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(19.89,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');

      $this->ln(4);


      $this->Cell(39.78,3.6,"",$lineas,0,'C'); $this->Cell(19.89,3.6,($victima_conflicto=="3"?"X":""),$lineas,0,'C');
      $this->Cell(39.3,8,strtoupper(utf8_decode($estudiante['depto_expulsor'])),$lineas,0,'C');
      $this->Cell(39.9,8,strtoupper(utf8_decode($estudiante['mun_expulsor'])),$lineas,0,'C');



      if(!isset($estudiante['fecha_expulsor'])){
        $fecha_expulsor = array(0 =>"", 1 => "", 2 => "" );;
      }else {
        $fecha_expulsor = explode("-",$estudiante['fecha_expulsor']);
      }

      $this->Cell(10,8,$fecha_expulsor[2],$lineas,0,'C');
      $this->Cell(10,8,$fecha_expulsor[1],$lineas,0,'C');
      $this->Cell(19.89,8,$fecha_expulsor[0],$lineas,0,'C');
      #certificado
      $certificado = strtoupper(utf8_decode($estudiante['certificado']));
      $this->Cell(10,8,($certificado=="S"?"X":""),$lineas,0,'C');
      $this->Cell(10,8,($certificado=="N"?"X":""),$lineas,0,'C');

      $this->ln(4);
      $this->Cell(39.78,3.6,"",$lineas,0,'C'); $this->Cell(19.89,3.6,($victima_conflicto=="NULL"?"X":""),$lineas,0,'C');

      $this->ln(8);


      #Situación Socioeconomica**********************

      $fuente_recurso = strtoupper(utf8_decode($estudiante['fuente_recurso']));
      $opcion = strtoupper(utf8_decode($estudiante['opcion']));

      $this->Cell(141.2,3.6,"",$lineas,0,'C');
      $this->Cell(9.94,3.6,($fuente_recurso=="1"?"X":""),$lineas,0,'C'); #FNR
      $this->ln(4);
      $this->Cell(141.2,3.6,"",$lineas,0,'C');
      $this->Cell(9.94,3.6,($fuente_recurso=="2"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(8,3.6,($opcion=="1"?"X":""),$lineas,0,'C');
      $this->ln(4);
      $this->Cell(141.2,3.6,"",$lineas,0,'C');
      $this->Cell(9.94,3.6,($fuente_recurso=="3"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(8,3.6,($opcion=="2"?"X":""),$lineas,0,'C');

      $this->ln(4);
      $this->Cell(29.83,8,strtoupper(utf8_decode($estudiante['numero_carne_sisben'])),$lineas,0,'C');
      $this->Cell(29.83,8,strtoupper(utf8_decode($estudiante['nivel_sisben'])),$lineas,0,'C');

      $estrato = strtoupper(utf8_decode($estudiante['estrato']));
      $this->Cell(6,8,($estrato=="1"?"X":""),$lineas,0,'C');
      $this->Cell(6,8,($estrato=="2"?"X":""),$lineas,0,'C');
      $this->Cell(6,8,($estrato=="3"?"X":""),$lineas,0,'C');
      $this->Cell(6,8,($estrato=="4"?"X":""),$lineas,0,'C');
      $this->Cell(6,8,($estrato=="5"?"X":""),$lineas,0,'C');
      $this->Cell(6,8,($estrato=="6"?"X":""),$lineas,0,'C');
      $this->Cell(6,8,($estrato=="OTRO"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(9.94,3.6,($fuente_recurso=="4"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(8,3.6,($opcion=="3"?"X":""),$lineas,0,'C');

      $this->ln(4);
      $this->Cell(101.5,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(9.94,3.6,($fuente_recurso=="5"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(8,3.6,($opcion=="4"?"X":""),$lineas,0,'C');



      #Territorialidad*******************

      $this->ln(16);
      $this->Cell(69.6,3.6,strtoupper(utf8_decode($estudiante['resguardo'])),$lineas,0,'C');
      $negritudes = strtoupper(utf8_decode($estudiante['negritudes']));
      $this->Cell(19.9,3.6,($negritudes=="S"?"X":""),$lineas,0,'C');
      $this->Cell(19.9,3.6,($negritudes=="N"?"X":""),$lineas,0,'C');
      $this->Cell(69.6,3.6,strtoupper(utf8_decode($estudiante['etnia'])),$lineas,0,'C');
      $this->Cell(19.9,3.6,"",$lineas,0,'C');


      #Discpacidades ****************************************************


      $this->ln(12);
      $discapacidades = strtoupper(utf8_decode($estudiante['discapacidades']));
      $capacidades = strtoupper(utf8_decode($estudiante['capacidades']));

      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,($discapacidades=="3"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,($discapacidades=="4"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,($capacidades=="1"?"X":""),$lineas,0,'C');

      $this->ln(4);
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,($capacidades=="7"?"X":""),$lineas,0,'C');

      $this->ln(4);
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,($discapacidades=="1"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,($capacidades=="5"?"X":""),$lineas,0,'C');

      $this->ln(4);
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,($discapacidades=="10"?"X":""),$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');
      $this->Cell(39.8,3.6,"",$lineas,0,'C');
      $this->Cell(10,3.6,"",$lineas,0,'C');

      #Informacion Familiar
      $this->ln(16);
      $tipo_doc_familiar = "";
      $this->Cell(5,3.6,($tipo_doc_familiar=="CC"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,($tipo_doc_familiar=="RC"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,($tipo_doc_familiar=="TI"?"X":""),$lineas,0,'C');
      $this->Cell(5,3.6,($tipo_doc_familiar=="CE"?"X":""),$lineas,0,'C');

      $this->Cell(19.9,3.6,"",$lineas,0,'C'); # documento
      $this->Cell(19.9,3.6,"",$lineas,0,'C'); # departamento Exped
      $this->Cell(19.9,3.6,"",$lineas,0,'C');
      $this->Cell(119.3,3.6,"",$lineas,0,'C');

      $this->ln(8);
      $parentesco = "4";
      $this->Cell(89.5,3.6," ",$lineas,0,'C');
      $this->Cell(14.9,3.6," ",$lineas,0,'C');
      $this->Cell(5,3.6,($parentesco==""?"X":""),$lineas,0,'C');
      $this->Cell(14.9,3.6," ",$lineas,0,'C');
      $this->Cell(5,3.6,($parentesco==""?"X":""),$lineas,0,'C');
      $this->Cell(14.9,3.6," ",$lineas,0,'C');
      $this->Cell(5,3.6,($parentesco==""?"X":""),$lineas,0,'C');
      $this->Cell(14.9,3.6," ",$lineas,0,'C');
      $this->Cell(5,3.6,($parentesco==""?"X":""),$lineas,0,'C');

      $this->ln(4);
      $this->Cell(29.8,3.6," ",$lineas,0,'C'); #Direccion
      $this->Cell(29.8,3.6,"",$lineas,0,'C');
      $this->Cell(29.8,3.6," ",$lineas,0,'C');
      $this->Cell(14.9,3.6," ",$lineas,0,'C');
      $this->Cell(5,3.6,($parentesco==""?"X":""),$lineas,0,'C');
      $this->Cell(14.9,3.6," ",$lineas,0,'C');
      $this->Cell(5,3.6,($parentesco==""?"X":""),$lineas,0,'C');
      $this->Cell(14.9,3.6," ",$lineas,0,'C');
      $this->Cell(24.8,3.6,"",$lineas,0,'C');

      $acudiente = "";
      $this->Cell(14.9,3.6,($acudiente=="S"?"X":""),$lineas,0,'C');
      $this->Cell(14.9,3.6,($acudiente=="N"?"X":""),$lineas,0,'C');

      }



    }

    public function concat()
    {
        foreach($this->files AS $file) {
            $pageCount = $this->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $this->ImportPage($pageNo);
                $s = $this->getTemplatesize($tplIdx);
                $this->AddPage($s['w'] > $s['h'] ? 'L' : 'P', array($s['w'], $s['h']));
                $this->useTemplate($tplIdx);
            }
        }
    }
}
