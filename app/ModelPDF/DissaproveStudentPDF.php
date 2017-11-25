<?php
namespace ModelPDF;
use Libs\FPDF as FPDF;

/**
* 
*/
class DissaproveStudentPDF extends FPDF
{
	
	private $info= array();
    private  $periodo =1;

    private $_width_NFVS = 10; //Ancho de la celda para las columnas N°, FAA, VAL, SUP
    private $_width_student = 70; //Ancho de la celda para la columna estudiante
    private $_asignature = 92 ;// Ancho de la celda para la celda asignatura


    function TableHeader($info,$periodo)
    {
        $this->info = $info[0];
        $this->periodo = $periodo;

    }

    public function Header()
    {
    	// Select Arial bold 15
        if($this->info['logo_byte'] != NULL)
        {
            $pic = 'data:image/png;base64,'.base64_encode($this->info["logo_byte"]);
            //$info = getimagesize($pic);

            // Logo
            $this->Image($pic, 12, 12, 15, 15, 'png');
        }

        $this->SetFont('Arial','B',11);
    // Move to the righ
        $widthPage = $this->GetPageWidth()-14;
    // Framed title
        $this->Cell($widthPage,8,utf8_decode($this->info['nombre_inst']),'LTR',1,'C');

        $this->SetFont('Arial','B',10);
        $this->Cell($widthPage,5,strtoupper(utf8_decode("SEDE: ".$this->info['sede'])),'LR',1,'C');
        

        $this->SetFont('Arial','',8);        
        $this->Cell($widthPage,7,strtoupper(utf8_decode("listado de estudiantes reprobados")),'LBR',1,'C');       

        //Grupo
        $this->Cell(40,5,strtoupper(utf8_decode("GRUPO: ".$this->info['nombre_grupo'])),'LB',0,'L');
        $this->Cell(($widthPage-40),5,strtoupper(utf8_decode("")),'BR',1,'C');

        //Director Grupo
        $partWidth = $widthPage/3;
        $this->Cell($partWidth,5,strtoupper(utf8_decode("director grupo: ".$this->info['primer_apellido']." ".$this->info['primer_nombre'])),'LBR',0,'L');
        
        $this->Cell($partWidth,5,strtoupper(utf8_decode("Periodo:  ".$this->periodo)),'LBR',0,'C');
        
        $this->Cell($partWidth,5,strtoupper(utf8_decode("Fecha: ".date("Y-m-d"))),'LBR',0,'C');   



    // Line break
        $this->Ln(8);
    }

    public function FancyTable($header=array(), $data=array(), $puestos='')
    {
    	// Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetDrawColor(0,0,0);
        // $this->SetLineWidth(.3);
        $this->SetFont('','B');

        // Cabecera
        $w = array(
        	$this->_width_NFVS, 
        	$this->_width_student, 
        	$this->_asignature, 
        	$this->_width_NFVS, 
        	$this->_width_NFVS, 
        	$this->_width_NFVS
        );
        
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,utf8_decode($header[$i]),1,0,'C',true);

        $this->Ln();

        $this->SetFont('Arial','',8);
        $fill = false;
        foreach ($data as $key => $student) {

			// 
			$this->Cell($this->_width_NFVS,
				(7 * count($student['asignaturas'])), 
				($key+1), 
				1,0,'C', $fill
			);

			$this->Cell(
				$this->_width_student,
				(7 * count($student['asignaturas'])), 
				utf8_decode($student['nombre']), 
				1,0,'C', $fill
			);

			foreach ($student['asignaturas'] as $keyA => $asignatura) {
				$inasistencia = ($asignatura['inasistencia'] == 0)? '' : $asignatura['inasistencia'];
				if($keyA == 0):
					$this->Cell(
						$this->_asignature,
						7, 
						utf8_decode($asignatura['asignatura']), 1,0,'C', $fill
					);
					$this->Cell(
						$this->_width_NFVS,
						7, 
						$inasistencia, 1,0,'C', $fill
					);
					$this->Cell(
						$this->_width_NFVS,
						7, 
						$asignatura['valoracion'], 1,0,'C', $fill
					);
					$this->Cell(
						$this->_width_NFVS,
						7, 
						'', 1,0,'C', $fill
					);
				else:
					$this->Cell(
						$this->_width_NFVS,
						7, '', 0,0,'C');
					$this->Cell(
						$this->_width_student,
						7, '', 0,0,'C');
					$this->Cell(
						$this->_asignature,
						7, utf8_decode($asignatura['asignatura']), 1,0,'C', $fill);	
						$this->Cell(
							$this->_width_NFVS,
							7, 
							$inasistencia, 1,0,'C', $fill
					);
					$this->Cell(
						$this->_width_NFVS,
						7, $asignatura['valoracion'], 1,0,'C', $fill
					);
					$this->Cell(
						$this->_width_NFVS,7, '', 1,0,'C', $fill);
				endif;
				$this->Ln();				
			}

			$fill = !$fill;
		}
    }

    public function Footer()
    {
    // Go to 1.5 cm from bottom
        $this->SetY(-18);
    // Select Arial italic 8
        $this->SetFont('Arial','I',8);
    // Print centered page number
        $this->Cell(0,10,utf8_decode('Ágora - Página '.$this->PageNo()),0,0,'C');
    }
}
?>