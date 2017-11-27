<?php 
$getTextformatoValoracionAsignatura = function ($valoracion, $superacion) 
use($min_bajo,$min_basico){	
	
	$valoracion_asignatura = $valoracion<$min_bajo?"":$valoracion;
	$valoracion_superacion = $superacion==""?"":"/".$superacion;

	$span_class_v = $valoracion < $min_basico?'r-rojo':'notas';
	$span_class_s = $superacion< $min_basico?'r-rojo':'notas';

	$span_valoracion = "<span class=".$span_class_v.">".$valoracion_asignatura."</span>";
	$span_superacion = "<span class=".$span_class_s.">".$valoracion_superacion."</span>";
	return $span_valoracion.$span_superacion;
};

$getTextformatoRequereridasAsignatura = function ($valoracion_requerida)use(
	$min_bajo, $min_basico, $max_superior, $cantidad_periodos, $cantidad_periodos_evaluados, $informe
){
	$valoracion_asignatura = $valoracion_requerida<=0?"APRO":$valoracion_requerida;

	if($cantidad_periodos = $cantidad_periodos_evaluados)	{
		$valoracion_asignatura = $valoracion_requerida>$max_superior?"REP":$valoracion_asignatura;		
	}
	if($informe){
		$valoracion_asignatura = $valoracion_requerida>0?"REP":$valoracion_asignatura;
	}

	$span_valoracion = "<span>".$valoracion_asignatura."</span>";	
	return $span_valoracion;
};

?>


<div id="contentConsol" class="content-tabla-grupo">	
	<table id="table_id"  class="cell-border">
		<thead>
			<tr id="fila-asignaturas">
				<th>No.</th>
				<th>Nombres y Apellidos</th>
				<th>Puesto</th>
				<th>Periodo</th>
				<th>PGG</th>
				<th>TAV</th>
				<?php
				if(isset($asignaturasEvaluadas) && $asignaturasEvaluadas != false){
					foreach ($asignaturasEvaluadas as $asignatura) {
						echo '<th>'.$asignatura['n_simpl'].'</th>';
					}
				}?>
			</tr>
		</thead>
		<tbody>
			<?php	
			$isPeriodosAcumulados = $isAcumulados;						
			$nombre_informe = "PROMEDIO ACUMULADO";			
			$nombre_novedad = "VALORACIÓN MIN. REQUERIDA PROX. PER.";
			
			$num_cols_pan = $cantidad_periodos_evaluados  + ($isAcumulados==true?2:0);
			$num_cols_pan = $informe==true?2:$num_cols_pan;
			
			if(isset($array_listado_estudiantes_evaluados) && $array_listado_estudiantes_evaluados != false)
			{				
				$contador=1;
				$id_estudiante=0;
				foreach ($array_listado_estudiantes_evaluados as $array_estudiante_evaluado_) 
				{						
					$id_estudiante = $array_estudiante_evaluado_['id_estudiante'];
					?>
					<tr class="fila-asignaturas">
						<td width="50px" rowspan="<?=$num_cols_pan;?>" ><?php echo $contador; $contador++; ?> </td>						
						<td data-id="" class="left-align" rowspan="<?=$num_cols_pan;?>">										
							<?=$array_estudiante_evaluado_['primer_apellido']." ".
							$array_estudiante_evaluado_['segundo_apellido']." ".
							$array_estudiante_evaluado_['primer_nombre']." ".
							$array_estudiante_evaluado_['segundo_nombre'];?>
						</td>

						<?php
						
						if(!$informe)
						{					
							foreach ($array_periodos_evaluados as $key_periodo_eval => $valuePeriodo) 
							{
								?>
								<td colspan="" data-id="" class=" ">
									<?php echo round($array_listado_estudiantes_promedios_periodos[$id_estudiante][$key_periodo_eval]['puesto'],1);?>
								</td>
								<td class=" ">
									<?php echo ($valuePeriodo+1);?>
								</td>
								<td class=" ">
									<?php echo round($array_listado_estudiantes_promedios_periodos[$id_estudiante][$key_periodo_eval]['promedio'],1);?>
								</td>
								<td class="">
									<?php	echo round($array_listado_estudiantes_promedios_periodos[$id_estudiante][$key_periodo_eval]['TAV'],1);?>
								</td>
								<?php																	
								
								if(isset($asignaturasEvaluadas) && $asignaturasEvaluadas != false){
									foreach ($asignaturasEvaluadas as $asignatura) {
										?>
										<td class=""> 
											<?php 											
											$valoracion = $array_listado_estudiantes_asignatura_periodos[$id_estudiante][$key_periodo_eval][$asignatura['id_asignatura']]['valoracion'];
											$superacion = $array_listado_estudiantes_asignatura_periodos[$id_estudiante][$key_periodo_eval][$asignatura['id_asignatura']]['superacion'];

											echo $getTextformatoValoracionAsignatura($valoracion,$superacion);

											$nota_asignatura = $valoracion>=$superacion?$valoracion:$superacion;
											?>
										</td>
										<?php
									}									
								}
								?>
							</tr>
							<?php
							}//Fin foreach $key_periodo_eval
						}
						else{	

							$nombre_novedad = "NOVEDAD";
							$nombre_informe = "INFORME FINAL";
							foreach ($array_periodos_evaluados as $key_periodo_eval => $valuePeriodo) 
							{																					
								
								if(isset($asignaturasEvaluadas) && $asignaturasEvaluadas != false){
									foreach ($asignaturasEvaluadas as $asignatura) {

										$valoracion = $array_listado_estudiantes_asignatura_periodos[$id_estudiante][$key_periodo_eval][$asignatura['id_asignatura']]['valoracion'];
										$superacion = $array_listado_estudiantes_asignatura_periodos[$id_estudiante][$key_periodo_eval][$asignatura['id_asignatura']]['superacion'];

										$nota_asignatura = $valoracion>=$superacion?$valoracion:$superacion;
									}
									
								}
							}
						}						

						if($isPeriodosAcumulados || $informe){
							?>
							<td class="bg-success" rowspan="2">
								<strong> <?=$array_puesto_promedio_acumulado[$id_estudiante]['puesto'] ?> </strong>
							</td>
							<td class="bg-success">
								<strong><?=$nombre_informe?></strong>
							</td>							
							<td class="bg-success">
								<strong><?=$array_promedios_acumulados[$id_estudiante]['pgg']?></strong>
							</td>
							<td class="bg-success">

							</td>
							<?php							
							foreach ($asignaturasEvaluadas as $asignatura) {
								$promedio_acumulado_asig = $array_estudiantes_acumulados_asignaturas[$id_estudiante][$asignatura['id_asignatura']]['valoracion'];
								$span_class_a = $array_estudiantes_acumulados_asignaturas[$id_estudiante][$asignatura['id_asignatura']]['span_class'];								
								?>								
								<td class=bg-success>
									<span class="<?=$span_class_a?>"><?=$promedio_acumulado_asig?></span>
								</td>
								<?php
							}							
							?>
						</tr>	
						<td  class="bg-warning">
							<span data-toggle="tooltip" data-placement="top" title="VALORACIÓN MINIMA REQUERIDA PARA EL PRÓXIMO PERIODO">
								<strong><?=$nombre_novedad?></strong>
							</span>
						</td>						
						<td class=bg-warning></td>
						<td class=bg-warning></td>
						<?php
						foreach ($asignaturasEvaluadas as $asignatura) {							
							$valoracion_requerida = $array_estudiantes_requeridas_asignaturas[$id_estudiante][$asignatura['id_asignatura']]['valoracion'];
							echo "<td class=bg-warning>".$getTextformatoRequereridasAsignatura($valoracion_requerida)."</td>";
							
						}							
						?>
					</tr>
					<?php
					}//Fin if isPeriodosAcumulados					
				}
			}
			?>	
		</tbody>
	</table>
	<br>
	<br>
</div>

