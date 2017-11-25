<?php
namespace Model;
use Libs\FPDF as FPDF;

class FPDFModel extends FPDF
{

    private $info= array();
    private $periodo =1;
    private $header = array();
    private $title = '';

    public function Header()
    {
    // Select Arial bold 15
        if($this->info['logo_byte'] != NULL)
        {
            $pic = 'data:image/png;base64,'.base64_encode($this->info["logo_byte"]);
            //$info = getimagesize($pic);

            // Logo
            $this->Image($pic, 30, 12, 15, 15, 'png');
        }

        $this->SetFont('Arial','B',11);
        // Move to the right
        $this->SetX(7);
        $widthPage = $this->GetPageWidth()-14;
        // Framed title
        $this->Cell($widthPage,6,utf8_decode($this->info['nombre_inst']),'LTR',1,'C');

        $this->SetFont('Arial','B',10);
        $this->SetX(7);
        $this->Cell($widthPage,6,strtoupper(utf8_decode("SEDE: ".$this->info['sede'])),'LR',1,'C');


        $this->SetFont('Arial','',10);
        $this->SetX(7);
        $this->Cell($widthPage,6,strtoupper(utf8_decode($this->title)),'LBR',1,'C');

        //Grupo
        $this->SetX(7);
        $this->Cell(40,5,strtoupper(utf8_decode("GRUPO: ".$this->info['nombre_grupo'])),'LB',0,'L');
        $this->SetX(7);
        $this->Cell($widthPage,5,strtoupper(utf8_decode("")),'BR',1,'C');

        //Director Grupo
        $partWidth = $widthPage/3;
        $this->SetX(7);
        $this->Cell($partWidth,5,strtoupper(utf8_decode("director grupo: ".$this->info['primer_apellido']." ".$this->info['primer_nombre'])),'LBR',0,'L');

        $this->Cell($partWidth,5,strtoupper(utf8_decode("Periodo:  ".$this->periodo)),'LBR',0,'C');
        $this->Cell($partWidth,5,strtoupper(utf8_decode("Fecha: ".date("Y-m-d"))),'LBR',0,'C');



    // Line break
        $this->Ln(5);

        if(isset($this->header)){
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0);
            $this->SetDrawColor(0,0,0);
            $this->SetLineWidth(.1);
            $this->SetFont('','B',9);
    // Cabecera
            $this->SetX(7);
            $widthPage = $this->GetPageWidth()-19;

            $part=0;
            $w = array();

            $fullpart = ($widthPage - 116)/ (count($this->header)-6);
            $this->Cell(10,5,"No.",1,0,'C',true);
            $this->Cell(77,5,"NOMBRES Y APELLIDOS",1,0,'C',true);
            $this->Cell(8,5,"PTO.",1,0,'C',true);
            $this->Cell(10,5,"PER",1,0,'C',true);
            $this->Cell(8,5,"PGG",1,0,'C',true);
            $this->Cell(8,5,"TAV",1,0,'C',true);
            $w[0]=10;
            $w[1]=77;
            $w[2]=8;
            $w[3]=10;
            $w[4]=8;
            $w[5]=8;
            $this->SetFont('','B',9);
            for($i=6;$i<count($this->header);$i++){

                $this->Cell($fullpart,5,utf8_decode($this->header[$i]),1,0,'C',true);

            }

            $this->Ln();
        }
    }


    public function Footer()
    {
    // Go to 1.5 cm from bottom
        $this->SetY(-15);
    // Select Arial italic 8
        $this->SetFont('Arial','I',8);
    // Print centered page number
        $this->Cell(0,4,utf8_decode('Ágora - Página '.$this->PageNo()),0,0,'C');
    }


    function TableHeader($info,$periodo, $header=null, $title_head=null)
    {
        $this->info = $info[0];
        $this->periodo = $periodo;
        $this->header = $header;
        $this->title = $title_head;

    }

    function ReportesAcademico(){
      
    }
// Tabla coloreada
    public function FancyTable($header, $data, $puestos)
    {
    // Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B',9);
    // Cabecera
        $this->SetX(7);
        $w = array(10, 97, 8, 8, 8, 8,  8, 10,  19, 20);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,utf8_decode($header[$i]),1,0,'C',true);

        $this->Ln();
    // Restauración de colores y fuentes
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('','',9);
    // Datos
        $fill = false;
        $cont=1;
        foreach($data as $row)
    {   //var_dump($row);


        foreach ($puestos as $value) {
            if($row['id_estudiante']== $value['id']){
               $this->SetX(7);
               $this->Cell($w[0],5,$cont,'LR',0,'C',$fill);
               $this->Cell($w[1],5,utf8_decode($row['primer_apellido']." ".$row['segundo_apellido']." ".$row['primer_nombre']."".$row['segundo_nombre']),'LR',0,'L',$fill);
               $this->Cell($w[2],5,utf8_decode($value['puesto']),'LR',0,'C',$fill);
               $this->Cell($w[3],5,($row['S']=="0"?"":$row['S']),'LR',0,'C',$fill);
               $this->Cell($w[4],5,($row['A']=="0"?"":$row['A']),'LR',0,'C',$fill);
               $this->Cell($w[5],5,($row['B']=="0"?"":$row['B']),'LR',0,'C',$fill);
               $this->Cell($w[6],5,($row['V']=="0"?"":$row['V']),'LR',0,'C',$fill);
               $this->Cell($w[7],5,($row['TAV']=="0"?"":$row['TAV']),'LR',0,'C',$fill);
               $this->Cell($w[8],5,($row['Promedio']=="0"?"":$row['Promedio']),'LR',0,'C',$fill);
               $this->Cell($w[9],5,utf8_decode($row['Desempeno']),'LR',0,'C',$fill);
           }
       }



       $this->Ln();
       $fill = !$fill;
       $cont++;
   }
    // Línea de cierre
   $this->SetX(7);
   $this->Cell(array_sum($w),0,'','T');
}


public function PorcentualesTable($header, $data, $puestos)
{
    // Colores, ancho de línea y fuente en negrita
    $this->SetFillColor(255,255,260);
    $this->SetTextColor(0);
    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B',9);
    // Cabecera
    $this->SetX(7);
    $widthPage = $this->GetPageWidth()-156;
    $fp = $widthPage/ (count($header)-5);


    $w = array(10, 97, 8,8,20);
    $this->Cell($w[0],7,"No.",1,0,'C',true);
    $this->Cell($w[1],7,"Asignatura",1,0,'C',true);
    $this->Cell($w[2],7,"Pto.",1,0,'C',true);

    for($i=3;$i<count($header)-2;$i++)
        $this->Cell($fp,7,utf8_decode($header[$i]),1,0,'C',true);

    $this->Cell($w[3],7,"PGG",1,0,'C',true);
    $this->Cell($w[4],7,"Desempeno",1,0,'C',true);

    $this->Ln();
    // Restauración de colores y fuentes
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('','',9);
    // Datos
    $fill = false;
    $cont=1;
    foreach($data as $row)
    {   //var_dump($row);


        foreach ($puestos as $value) {
            if($row['id_asignatura']== $value['id']){
               $this->SetX(7);
               $this->Cell($w[0],5,$cont,'LR',0,'C',$fill);
               $this->Cell($w[1],5,utf8_decode($row['Asignatura']),'LR',0,'L',$fill);
               $this->Cell($w[2],5,utf8_decode($value['puesto']),'LR',0,'C',$fill);
               $this->Cell($fp,5,($row['S']=="0"?"":$row['S']),'LR',0,'C',$fill);
               $this->Cell($fp,5,($row['S%']=="0"?"":$row['S%'].'%'),'LR',0,'C',$fill);

               $this->Cell($fp,5,($row['A']=="0"?"":$row['A']),'LR',0,'C',$fill);
               $this->Cell($fp,5,($row['A%']=="0"?"":$row['A%'].'%'),'LR',0,'C',$fill);

               $this->Cell($fp,5,($row['B']=="0"?"":$row['B']),'LR',0,'C',$fill);
               $this->Cell($fp,5,($row['B%']=="0"?"":$row['B%'].'%'),'LR',0,'C',$fill);

               $this->Cell($fp,5,($row['V']=="0"?"":$row['V']),'LR',0,'C',$fill);
               $this->Cell($fp,5,($row['V%']=="0"?"":$row['V%'].'%'),'LR',0,'C',$fill);

               $this->Cell($fp,5,($row['TAV']=="0"?"":$row['TAV']),'LR',0,'C',$fill);
               $this->Cell($w[3],5,($row['Promedio']=="0"?"":$row['Promedio']),'LR',0,'C',$fill);
               $this->Cell($w[4],5,utf8_decode($row['Desempeno']),'LR',0,'C',$fill);
           }
       }



       $this->Ln();
       $fill = !$fill;
       $cont++;
   }
    // Línea de cierre
   $this->SetX(7);
   $this->Cell(array_sum($w)+($fp*(count($header)-5)),0,'','T');
}

public function HeaderConsolidate(){

}


public function ConsolidadoTable(
    $header,
    $estudiantes,
    $puestos=0,
    $asignaturasEvaluadas,
    $tablaConsolidados,
    $min_basico,
    $valoracionesAll,
    $periodos_evaluados,
    $peso_periodos,
    $is_acumulados,
    $periodosAll,
    $is_repro=false)
{
    // Colores, ancho de línea y fuente en negrita
    $this->SetFillColor(255,255,255);
    $this->SetTextColor(0);
    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(.1);
    $this->SetFont('','B',9);
    // Cabecera
    $this->SetX(7);
    $widthPage = $this->GetPageWidth()-19;

    $part=0;
    $w = array();

    $fullpart = ($widthPage - 116)/ (count($header)-6);
    $w[0]=10;
    $w[1]=77;
    $w[2]=8;
    $w[3]=10;
    $w[4]=8;
    $w[5]=8;

    // Restauración de colores y fuentes
    $this->SetTextColor(0);
    $this->SetFont('','',9);


    $cont=1;
    $fill = false;
    $id_estudiante = 0;
    $matriz_notas = [];
    $array_estudiantes = array();
    $max_superior = $valoracionesAll[3]['maximo'];
    $cols_row = ($is_acumulados?2:0) + count($periodos_evaluados);

    $numero_periodos = count($periodosAll);
    $min_bajo = $valoracionesAll[1]['minimo'];
    $min_basico = $valoracionesAll[2]['minimo'];

    $numero_periodo_evaluados = count($periodos_evaluados);
    $numero_periodo_faltantes = $numero_periodos - $numero_periodo_evaluados;
    

    foreach ($estudiantes as $key => $estudiante) {
            # estudiantes trae un arreglo con datos personales de los estudiantes por cada periodo
        foreach ($estudiante as $_key => $_value) {

                    # Combinamos todos los arreglos de estudiantes en un solo contenedor de arreglos de estudiantes
            $array_estudiantes[$_value['id_estudiante']] = array(
                'id_estudiante'     =>  $_value['id_estudiante'],
                'primer_apellido'   =>  $_value['primer_apellido'],
                'segundo_apellido'  =>  $_value['segundo_apellido'],
                'primer_nombre'     =>  $_value['primer_nombre'],
                'segundo_nombre'    =>  $_value['segundo_nombre']
            );
        }
    }

    foreach($array_estudiantes as $estudiante)
    {
        $this->SetFillColor(246,246,246);
        if($id_estudiante != $estudiante['id_estudiante'])
        {
            $this->SetX(7);
            $id_estudiante = $estudiante['id_estudiante'];
            $array_promedios = [];
            $periodo_asig = [];

            $this->Cell($w[0],4*$cols_row,$cont,1,0,'C',$fill);
            $this->Cell($w[1],4*$cols_row,utf8_decode(
                $estudiante['primer_apellido']." "
                .$estudiante['segundo_apellido']." "
                .$estudiante['primer_nombre']." "
                .$estudiante['segundo_nombre']),
            1,0,'L',$fill);


            foreach ($periodos_evaluados as $key => $valuePeriodo)
            {
                $_puesto=0;
                $_promedio=0;
                $_tav=0;
                foreach ($puestos[$key] as $value) {
                    if( $value['id'] == $estudiante['id_estudiante'] ){
                        $_puesto = $value['puesto'];
                        $_promedio = $value['promedio'];
                        $_tav = $value['TAV'];
                        $array_promedios[$key] = $value['promedio'];
                    }

                }


                if($key != 0)
                {
                    $this->Ln();
                    $this->SetX(7);
                    $this->Cell($w[0],4,"",0,0,'C');
                    $this->Cell($w[1],4,"",0,0,'LR');
                }

                $this->Cell($w[2],4,utf8_decode($_puesto),1,0,'C',$fill);
                $this->Cell($w[3],4,$valuePeriodo,1,0,'C',$fill);
                $this->Cell($w[4],4,utf8_decode(round($_promedio,1)),1,0,'C',$fill);
                $this->Cell($w[5],4,$_tav,1,0,'C',$fill);

                $notas_asig = [];

                # Muestra la valoraciones de cada asignatura
                foreach ($asignaturasEvaluadas as $asignatura)
                {
                    $tiene_nota=false;
                    foreach ($tablaConsolidados[$key] as $registro)
                    {
                        if($estudiante['id_estudiante'] == $registro['id_estudiante'] &&  $asignatura['id_asignatura'] == $registro['id_asignatura'])
                        {
                            if($registro['Valoracion']< $min_basico && $registro['Superacion'] < $min_basico)
                            {
                                $this->SetTextColor(255,0,0);
                            }
                            else
                            {
                                $this->SetTextColor(0);
                            }
                            $valoracion_asignatura = $registro['Valoracion']==0?"":$registro['Valoracion'];
                            $valoracion_superacion = $registro['Superacion']==""?"":" / ".$registro['Superacion'];

                            $nota_asignatura = $registro['Valoracion']>=$registro['Superacion']?$registro['Valoracion']:$registro['Superacion'];
                            $notas_asig[$asignatura['id_asignatura']] = $nota_asignatura;

                            $tiene_nota=true;
                            if($is_repro==true && ($valoracion_asignatura >= $min_basico || $valoracion_superacion >= $min_basico ) == true){
                                $this->Cell($fullpart,4,"",1,0,'C',$fill);
                                $this->SetTextColor(0);
                            }else{
                                $this->Cell($fullpart,4,$valoracion_asignatura.$valoracion_superacion,1,0,'C',$fill);
                                $this->SetTextColor(0);
                            }

                        }
                    }
                    if(!$tiene_nota){
                        $this->Cell($fullpart,4,"",1,0,'C',$fill);
                    }
                }
                $periodo_asig[$key] = $notas_asig;

            }

            $display=array();

            # Muestra la valoracion acumulada de cada asignatura
            if($is_acumulados){

                $matriz_notas[$id_estudiante]= $periodo_asig;
                $promedio = 0;
                foreach ($periodos_evaluados as $_key => $valuePeriodo)
                {
                    $promedio += ($array_promedios[$_key] * ($peso_periodos[$_key]/100));
                }
                $this->SetFillColor(224,235,255);
                $this->Ln();
                $this->SetX(7);
                $this->Cell($w[0],4,"",0,0,'C');
                $this->Cell($w[1],4,"",0,0,'LR');
                $this->Cell(18,4,"ACUM.",1,0,'C',true);
                $this->Cell($w[4],4,round($promedio,1),1,0,'C',true);
                $this->Cell($w[5],4,"",1,0,'C',true);
                foreach ($asignaturasEvaluadas as $asignatura)
                {
                    $promedio_acumulado_asig=0;
                    foreach ($periodos_evaluados as $_key => $valuePeriodo)
                    {
                        foreach ($tablaConsolidados[$_key] as  $registro)
                        {
                            if($estudiante['id_estudiante'] == $registro['id_estudiante'] &&  $asignatura['id_asignatura'] == $registro['id_asignatura']){
                                if($matriz_notas[$id_estudiante][$_key][$asignatura['id_asignatura']] <= $max_superior){
                                    $display[$_key] = $matriz_notas[$id_estudiante][$_key][$asignatura['id_asignatura']]>= $min_basico?true:false;
                                    $promedio_acumulado_asig += round(($matriz_notas[$id_estudiante][$_key][$asignatura['id_asignatura']] * ($peso_periodos[$_key]/100)),1);
                                }

                            }

                        }

                    }

                   if($is_repro){
                        $display_result = true;
                        foreach ($display as $key => $value) {
                            if (!$value) {
                                $display_result = false;
                            }
                        }
                        if(!$display_result){
                            $promedio_acumulado_asig = $promedio_acumulado_asig==0?"":$promedio_acumulado_asig;
                            $this->Cell($fullpart,4,$promedio_acumulado_asig,1,0,'C',true);
                        }else{
                            $this->Cell($fullpart,4,"",1,0,'C',true);
                        }
                    }else{
                        $promedio_acumulado_asig = $promedio_acumulado_asig==0?"":$promedio_acumulado_asig;
                        $this->Cell($fullpart,4,$promedio_acumulado_asig,1,0,'C',true);
                    }



                }

            }

            $display=array();
            # Muestras las valoración requerida del próximo periodo
            if($is_acumulados){
                $this->SetFillColor(224,235,255);
                $this->Ln();
                $this->SetX(7);
                $this->Cell($w[0],4,"",0,0,'C');
                $this->Cell($w[1],4,"",0,0,'LR');
                $this->Cell(18,4,"VAL REQ.",1,0,'C',true);
                $this->Cell($w[4],4,"",1,0,'C',true);
                $this->Cell($w[5],4,"",1,0,'C',true);

                foreach ($asignaturasEvaluadas as $asignatura)
                {
                    $array_promedios_acumulados=array();
                    //en Posición 1 se guardará el promedio acumulado de la asignatura correspondiente
                    $array_promedios_acumulados[1]=0;
                            //en posición 2 se guardará el número de veces donde la asignatura correspondiente fue valorada
                    $array_promedios_acumulados[2]=0;
                    foreach ($periodos_evaluados as $_key => $valuePeriodo)
                    {
                        foreach ($tablaConsolidados[$_key] as  $registro)
                        {
                            if($id_estudiante == $registro['id_estudiante'] &&   $asignatura['id_asignatura'] == $registro['id_asignatura']){

                                if($matriz_notas[$id_estudiante][$_key][$asignatura['id_asignatura']]<= $max_superior){
                                    $display[$_key] = $matriz_notas[$id_estudiante][$_key][$asignatura['id_asignatura']]>= $min_bajo?true:false;
                                    $array_promedios_acumulados[1] += round(($matriz_notas[$id_estudiante][$_key][$asignatura['id_asignatura']] * ($peso_periodos[$_key]/100)),1);
                                    $array_promedios_acumulados[2] += $matriz_notas[$id_estudiante][$_key][$asignatura['id_asignatura']]<1?0:1;
                                }

                            }
                        }
                    }
                    
                    if(!$numero_periodo_faltantes == 0){
                        $peso_periodo_prox = $periodosAll[count($periodos_evaluados)]['peso'];
                        $valoracion_requerida = round((($min_basico - round($array_promedios_acumulados[1],1))/ $numero_periodo_faltantes) / ($peso_periodo_prox / 100),1);
                    }else{
                        $peso_periodo_prox = $periodosAll[(count($periodos_evaluados)-1)]['peso'];
                         $valoracion_requerida = round((($min_basico - round($array_promedios_acumulados[1],1))/ ($numero_periodo_faltantes+1)) / ($peso_periodo_prox / 100),1);
                    }
                    
                        if($valoracion_requerida > $max_superior){
                                $valoracion_requerida = "";
                            }
                            
                            if( $array_promedios_acumulados[2] <= $numero_periodos && $array_promedios_acumulados[1] >= $min_basico){
                                $valoracion_requerida = "APRO";
                            }
                                
                    
                    if($numero_periodo_faltantes == 0 && $array_promedios_acumulados[1] < $min_basico && $array_promedios_acumulados[2] == $numero_periodos){
                    $valoracion_requerida = "REP";
                    }
                        

                            //Si el número de veces que fue valorada la asignatura es menor al número de periodos evaluados,
                            //No le calculamos la valoración requerida, porque supera la escala de evaluación.
                    if($array_promedios_acumulados[2]<($numero_periodo_evaluados-1)){
                        $valoracion_requerida = "";
                    }
                        
                        

                    if($is_repro){
                        $display_result = true;
                        foreach ($display as $key => $value) {
                            if (!$value) {
                                $display_result = false;
                            }
                        }
                        if(!$display_result){
                            $promedio_acumulado_asig = $promedio_acumulado_asig==0?"":$promedio_acumulado_asig;
                            $this->Cell($fullpart,4,$valoracion_requerida,1,0,'C',true);
                        }else{
                            $this->Cell($fullpart,4,"",1,0,'C',true);
                        }
                    }else{
                        $this->Cell($fullpart,4,$valoracion_requerida,1,0,'C',true);
                    }


                }
            }






        }



        $this->Ln();
        $fill = !$fill;
        $cont++;
    }

    // Línea de cierre

    $this->Cell(array_sum($header),0,'','T');
}


}
