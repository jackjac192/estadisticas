<div id="contentConsol" class="content-tabla-grupo">	
	
	<div class="page-header" data-pos="<?php echo $informacionGrupo['id_grupo'] ?>">
		<h4>
			<span>Grupo:</span> <?php echo ($informacionGrupo['nombre_grupo']);?> 
			<input type="hidden" name="gruposId" value="<?php echo $informacionGrupo['id_grupo'] ?>">
			<span>Director Grupo:</span> ... 
		</h4> 
	</div>

	
	<table id="table_id"  data-pos="<?php echo $info['id_grupo'] ?>" class="cell-border">
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
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			
			$id=0;				
			$contador=1;				
			$matriz_notas = [];
			$isPeriodosAcumulados = $isAcumulados;			
			$numero_periodos = count($periodosAll);
			$min_bajo = $valoraciones[1]['minimo'];
			$min_basico = $valoraciones[2]['minimo'];			
			$max_superior = $valoraciones[3]['maximo'];
			$numero_periodo_evaluados = count($periodos_evaluados);	
			$array_estudiantes = array();

			$count_eval=0;

			foreach ($estudiantesPromedios as $key => $estudiantes) {

				if(isset($estudiantes) && $estudiantes != false){
					$count_eval++;
			# estudiantes trae un arreglo con datos personales de los estudiantes por cada periodo			
					foreach ($estudiantes as $_key => $_value) {					
						
					# Combinamos todos los arreglos de estudiantes en un solo contenedor de arreglos de estudiantes
						$array_estudiantes[$_value['id_estudiante']] = array(
							'id_estudiante'		=> 	$_value['id_estudiante'], 
							'primer_apellido'	=>	$_value['primer_apellido'],
							'segundo_apellido'	=>	$_value['segundo_apellido'],
							'primer_nombre'		=>	$_value['primer_nombre'],
							'segundo_nombre'	=>	$_value['segundo_nombre']
						);				
					}	
				}			
			}
			echo $count_eval;
			$numero_periodo_evaluados = $count_eval;
			$numero_periodo_faltantes = $numero_periodos - $numero_periodo_evaluados;
			$peso_periodo_prox = $periodosAll[$numero_periodo_evaluados]['peso'];
			$num_cols_pan = $numero_periodo_evaluados + ($isAcumulados==true?2:0);
			
			if(isset($array_estudiantes) && $array_estudiantes != false){
				
				foreach ($array_estudiantes as $estudiante) {	

					if($id != $estudiante['id_estudiante']  ){
						$array_promedios = [];
						$id = $estudiante['id_estudiante'];
						?>

						<tr class="fila-asignaturas">
							<td width="50px" rowspan="<?=$num_cols_pan;?>" ><?php echo $contador; $contador++; ?> </td>						
							<td data-id="" class="left-align" rowspan="<?=$num_cols_pan;?>">								<?=$estudiante['id_estudiante']?>-<?=$estudiante['primer_apellido']." ".
								$estudiante['segundo_apellido']." ".
								$estudiante['primer_nombre']." ".
								$estudiante['segundo_nombre'];?>
							</td>

							<?php
							$periodo_asig = [];

							foreach ($periodos_evaluados as $key => $valuePeriodo) 
							{
								?>
								<td colspan="" data-id="" class="">
									<?php
									foreach ($puestoPromedio[$key] as $value) {
										if( $value['id'] == $estudiante['id_estudiante'] ){
											echo $value['puesto'];
										}
									}									
									?>
								</td>
								<td class="">
									<?php
									echo $valuePeriodo;									
									?>
								</td>
								
								<td>
									<?php							
									foreach ($puestoPromedio[$key] as $value) {
										if( $value['id'] == $estudiante['id_estudiante'] ){
											echo $value['promedio'];
											$array_promedios[$key] = $value['promedio'];
										}
									}
									?>
								</td>
								<td>
									<?php
									foreach ($puestoPromedio[$key] as $value) {
										if( $value['id'] == $estudiante['id_estudiante'] ){
											echo $value['TAV'];
										}
									}
									?>
								</td>
								<?php
								$class = '';									
								$notas_asig = [];

								if(isset($asignaturasEvaluadas) && $asignaturasEvaluadas != false){
									foreach ($asignaturasEvaluadas as $asignatura) {
										?>
										<td > 
											<?php 

											foreach ($tablaConsolidados[$key] as  $registro) {

												if($id == $registro['id_estudiante'] &&   $asignatura['id_asignatura'] == $registro['id_asignatura']){

													$valoracion_asignatura = $registro['Valoracion']==0?"":$registro['Valoracion'];
													$valoracion_superacion = $registro['Superacion']==""?"":" / ".$registro['Superacion'];
													
													$color_notas = $registro['Valoracion']<= $valoraciones[1]['maximo']?'r-rojo':'notas';
													$valoracion_asignatura =  "<span class=".$color_notas.">".$valoracion_asignatura."</span>";

													//Notas de superaciones
													$color_notas = $registro['Superacion']<= $valoraciones[1]['maximo']?'r-rojo':'notas';
													$valoracion_superacion =  "<span class=".$color_notas.">".$valoracion_superacion."</span>";
													echo $valoracion_asignatura.$valoracion_superacion;

													$nota_asignatura = $registro['Valoracion']>=$registro['Superacion']?$registro['Valoracion']:$registro['Superacion'];
													//Guardamos en un arreglo las valoraciones de cada asignatura
													$notas_asig[$asignatura['id_asignatura']] = $nota_asignatura;
												}
											}
											
											?>
										</td>
										<?php
									}
									$periodo_asig[$key] = $notas_asig;
								}


								?>
							</tr>
							<?php
						}//Fin foreach $key

						$display=array();
						
						if($isPeriodosAcumulados){
							$matriz_notas[$id]= $periodo_asig;
							//Promedio Acumulado
							$promedio = 0;							
							foreach ($periodos_evaluados as $key => $valuePeriodo) {
								if(isset($array_promedios[$key]))										
									$promedio += ($array_promedios[$key] * ($peso_periodos[$key]/100));

							}
							//Generar fila de valoraciones acumuladas
							?>
							<td class="bg-success" colspan="2">VALORACIÓN ACUMULADA: </td>							
							<td class="bg-success">
								<?=round($promedio,1)?>
							</td>
							<td class="bg-success">
								
							</td>
							<?php
							foreach ($asignaturasEvaluadas as $asignatura) {
								$promedio_acumulado_asig=0;
								foreach ($periodos_evaluados as $key => $valuePeriodo) {
									foreach ($tablaConsolidados[$key] as  $registro) {
										if($id == $registro['id_estudiante'] &&   $asignatura['id_asignatura'] == $registro['id_asignatura']){
											
											if($matriz_notas[$id][$key][$asignatura['id_asignatura']] <= $max_superior){
												$display[$key] = $matriz_notas[$id][$key][$asignatura['id_asignatura']]>= $min_basico?true:false;
												$promedio_acumulado_asig += round(($matriz_notas[$id][$key][$asignatura['id_asignatura']] * ($peso_periodos[$key]/100)),1);
											}
											
										}												
									}
								}
								
								foreach ($display as $key => $value) {
									$color_notas = '';
									if (!$value) {
										$color_notas = '';
										break;
									}
									$color_notas = 'notas';
								}

								$promedio_acumulado_asig = $promedio_acumulado_asig==0?"":$promedio_acumulado_asig;
								echo "<td class=bg-success><span class=".$color_notas.">".$promedio_acumulado_asig."</span>"."</td>";
							}							
							?>
						</tr>
						<?php
						
						?>
						<td colspan="2" class="bg-warning">
							<span data-toggle="tooltip" data-placement="top" title="VALORACIÓN MINIMA REQUERIDA PARA EL PRÓXIMO PERIODO">
								VALORACIÓN MIN. REQUERIDA PROX. PER.
							</span>
						</td>						
						<td class=bg-warning> 
							
						</td>
						<td class=bg-warning>
							
						</td>

						<?php

						foreach ($asignaturasEvaluadas as $asignatura) {
							$array_promedios_acumulados=array();

							 //en Posición 1 se guardará el promedio acumulado de la asignatura correspondiente 
							$array_promedios_acumulados[1]=0;
							//en posición 2 se guardará el número de veces donde la asignatura correspondiente fue valorada
							$array_promedios_acumulados[2]=0; 

							foreach ($periodos_evaluados as $key => $valuePeriodo) {
								foreach ($tablaConsolidados[$key] as  $registro) {
									if($id == $registro['id_estudiante'] &&   $asignatura['id_asignatura'] == $registro['id_asignatura']){
										
										if($matriz_notas[$id][$key][$asignatura['id_asignatura']]<= $max_superior){
											$display[$key] = $matriz_notas[$id][$key][$asignatura['id_asignatura']]>= $min_basico?true:false;										
											$array_promedios_acumulados[1] += ($matriz_notas[$id][$key][$asignatura['id_asignatura']] * ($peso_periodos[$key]/100));
											$array_promedios_acumulados[2] += $matriz_notas[$id][$key][$asignatura['id_asignatura']]<1?0:1;
										}
										
									}												
								}
							}
							$valoracion_requerida = round((($min_basico - round($array_promedios_acumulados[1],1))/ $numero_periodo_faltantes) / ($peso_periodo_prox / 100),1);

							if($valoracion_requerida < $min_bajo)
								$valoracion_requerida = "APRO";
							
							elseif($valoracion_requerida > $max_superior)
								$valoracion_requerida = "REP";

							//Si el número de veces que fue valorada la asignatura es menor al número de periodos evaluados,
							//No le calculamos la valoración requerida, porque supera la escala de evaluación. 
							if($array_promedios_acumulados[2]<$numero_periodo_evaluados)
								$valoracion_requerida = "";
							
							foreach ($display as $key => $value) {
								$color_notas = '';
								if (!$value) {
									$color_notas = '';
									break;
								}
								$color_notas = 'notas';
							}

							echo "<td class=bg-warning><span class=".$color_notas.">".$valoracion_requerida."</span>"."</td>";
						}							
						?>
					</tr>
					<?php

					}//Fin if isPeriodosAcumulados

					
				}
			}				

		}
		?>	


	</tbody>
</table>
<br>
<br>







</div>