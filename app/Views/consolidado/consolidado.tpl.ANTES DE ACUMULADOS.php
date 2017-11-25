<?php

//var_dump($puestos);

?>


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
				<th width="70px">Puesto</th>
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
			$puesto=1;
			$pggAux=0;
			$contador=1;
			//var_dump($tablaConsolidados);

			if(isset($estudiantesPromedios) && $estudiantesPromedios != false){
				foreach ($estudiantesPromedios as $estudiante) {

					if($id != $estudiante['id_estudiante']  ){
						$id = $estudiante['id_estudiante'];



						?>

						<tr class="fila-asignaturas">
							<td width="50px"><?php echo $contador; $contador++; ?></td>						
							<td data-id="" class="left-align" >
								<?=$estudiante['primer_apellido']." ".
								$estudiante['segundo_apellido']." ".
								$estudiante['primer_nombre']." ".
								$estudiante['segundo_nombre'];?>
							</td>
							<td data-id="" >
								<?php
								foreach ($puestoPromedio as $value) {
									if( $value['id'] == $estudiante['id_estudiante'] ){
										echo $value['puesto'];
									}
								}
								?>
							</td>
							<td data-id="" >
								<?=$estudiante['pgg']?>
							</td>
							<td>
								<?=$estudiante['TAV']?>
							</td>
							<?php
							$class = '';
							if(isset($asignaturasEvaluadas) && $asignaturasEvaluadas != false){
								foreach ($asignaturasEvaluadas as $asignatura) {
									?>
									<td> 
										<?php
										foreach ($tablaConsolidados as  $registro) {

											if($id == $registro['id_estudiante'] &&   $asignatura['id_asignatura'] == $registro['id_asignatura']){
												
												if($registro['Valoracion']<= $valoraciones[1]['maximo'])
												{
													$class = 'r-rojo';
												}
												if($registro['Valoracion']>$valoraciones[1]['maximo'])
												{
													$class = 'notas';
												}
												$valoracionF='';

												$valoracionF = $registro['Valoracion']==0?"":$registro['Valoracion'];
												echo "<span class=".$class.">".$valoracionF."</span>";
											}
										}
										



									?>

								</td>

								<?php
							}
						}
						?>


					</tr>

					<?php
				}
			}

		}
		?>	


	</tbody>
</table>





</div>
