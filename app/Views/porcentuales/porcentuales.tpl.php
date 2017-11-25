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
				<th class="left-align" >NOMBRE ASIGNATURA</th>
				<th>PUESTOS</th>
				<th>S</th>
				<th>%</th>
				<th>A</th>
				<th>%</th>
				<th>Bs</th>
				<th>%</th>
				<th>Bj</th>
				<th>%</th>
				<th>TEV</th>
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
						<td data-id="" class="left-align"><?=$value['Asignatura']?></td>	
						<td width="100px">
							<?php
							foreach ($puestoPromedio as $key => $values) {
								if($values['id']== $value['id_asignatura']){
									echo $values['puesto'];
								}
							}
							?>
						</td>	
						<?php
						$class = '';

						$valoracion = $value['S']==0?"":$value['S'];
						echo "<td width=40px> ".$valoracion."</td>";
						$valoracion = $value['S%']==0?"":$value['S%'].'%';
						echo "<td width=40px> ".$valoracion."</td>";

						$valoracion = $value['A']==0?"":$value['A'];
						echo "<td width=40px> ".$valoracion."</td>";
						$valoracion = $value['A%']==0?"":$value['A%'].'%';
						echo "<td width=40px> ".$valoracion."</td>";

						$valoracion = $value['B']==0?"":$value['B'];
						echo "<td width=40px> ".$valoracion."</td>";
						$valoracion = $value['B%']==0?"":$value['B%'].'%';
						echo "<td width=40px> ".$valoracion."</td>";

						$valoracion = $value['V']==0?"":$value['V'];
						echo "<td width=40px> ".$valoracion."</td>";
						$valoracion = $value['V%']==0?"":$value['V%'].'%';
						echo "<td width=40px> ".$valoracion."</td>";

						echo "<td width=40px> ".$value['TAV']."</td>";
						if($value['Promedio']<= $valoraciones[1]['maximo'])
						{
							$class = 'r-rojo';
						}
						else{
							$class = 'dd';
						}
						echo "<td class=".$class."> ".$value['Promedio']."</td>";
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