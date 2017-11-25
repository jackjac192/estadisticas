<?php

//var_dump($puestos);

?>


<br>
<div class="content-tabla-grupo">
	<?php
	$cont = 1;

	?>
	<input type="hidden" name="gruposId" value="<?php echo $info['id_grupo'] ?>">
	<table id="table_id" class="cell-border">
		<thead>
			<tr>
				<th>No.</th>
				<th class="left-align" >NOMBRES Y APPELLIDOS</th>
				<th>PUESTOS</th>
				<th>S</th>
				<th>A</th>
				<th>Bs</th>
				<th>Bj</th>
				<th>TAV</th>
				<th>PROMEDIO</th>
				<th>DESEMPEÑO</th>


			</tr>
		</thead>


		<tbody>
			<?php
			//var_dump($tablaPuestos);
			if(isset($tablaPuestos) and $tablaPuestos!= false){
				foreach ($tablaPuestos as $key => $value) {

					?>
					<tr>
						<td width="100px"><?=$cont?></td>
						<td data-id="" class="left-align"><?=$value['primer_apellido']." ".$value['segundo_apellido']." ".$value['primer_nombre']." ".$value['segundo_nombre']?></td>	
						<td width="100px">
							<?php
							foreach ($puestoPromedio as $key => $values) {
								if($values['id']== $value['id_estudiante']){
									echo $values['puesto'];
								}
							}
							?>
						</td>	
						<?php
						$class = '';

						$valoracion = $value['S']==0?"":$value['S'];
						echo "<td > ".$valoracion."</td>";
						$valoracion = $value['A']==0?"":$value['A'];
						echo "<td > ".$valoracion."</td>";
						$valoracion = $value['B']==0?"":$value['B'];
						echo "<td > ".$valoracion."</td>";
						$valoracion = $value['V']==0?"":$value['V'];
						echo "<td > ".$valoracion."</td>";
						echo "<td > ".$value['TAV']."</td>";
						if($value['Promedio']<= $valoraciones[1]['maximo'])
						{
							$class = 'r-rojo';
						}
						else{
							$class = 'dd';
						}

						$promedio_grupo = $value['pgg_sin_super']<$value['Promedio']?$value['pgg_sin_super'].' / ':'';
						
						echo "<td class=".$class."> ".$promedio_grupo.$value['Promedio']."</td>";

						echo "<td > ".$value['Desempeno']."</td>";
						?>
					</tr>
					<?php
					$cont++;
				}

			}
			?>

		</tbody>
	</table>
	
	
</div>