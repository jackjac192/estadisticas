<?php

//var_dump($puestos);

?>


<br>
<div class="content-tabla-grupo">
	<?php
	$cont = 1;



	?>
	<input type="hidden" name="gruposId" value="<?php echo $info['id_grupo'] ?>">
	<table id="table_id"  class="cell-border" >
		<thead>
			<tr>
				<?php
				echo "<th   rowspan=2 class=fila-asignaturas>Grado</th>";
				echo "<th   rowspan=2  width=20px class=fila-asignaturas>Grupos</th>";
				echo "<th   colspan=3 class=fila-asignaturas>MAT. INICIAL</th>";
				echo "<th   colspan=3 class=fila-asignaturas>RETIRADOS</th>";
				echo "<th   colspan=3 class=fila-asignaturas>DESERTORES</th>";
				echo "<th   colspan=3 class=fila-asignaturas>TRASLADOS</th>";
				echo "<th   colspan=3 class=fila-asignaturas>MAT. FINAL</th>";
				echo "<th   colspan=3 class=fila-asignaturas>MAT. GRUPO</th>";
				echo "<th   colspan=3 class=fila-asignaturas>REPROBADOS</th>";
				echo "<th   colspan=3 class=fila-asignaturas>APROBADOS</th>";
				?>				
			</tr>
			<tr class="border-th">
				<?php

				for ($i=0; $i <8; $i++) { 
					echo "<th   class=fila-asignaturas>HOM</th>";
					echo "<th   class=fila-asignaturas>MUJ</th>";
					echo "<th   class=fila-asignaturas>TOTAL</th>";
				}
				
				?>
			</tr>
		</thead>


		<tbody>

			<?php
			//print_r($grados);
			foreach ($grados as $grado) {
				echo "<tr >";
				echo "<td >".$grado['grado']."</td>";
				$sumaFilas=0;
				?>
				<td>
					<?php
					if (isset($gruposCantidad) && $gruposCantidad != false){
						foreach ($gruposCantidad as  $grupoCant) {
							echo $grado['id_grado']== $grupoCant['id_grado']?$grupoCant['cantidad']:"";

						}
					}
					?>
				</td>

				<!-- MATRICULADOS INICIAL -->
				<td>
					<?php
					if (isset($matriculaInicialM) && $matriculaInicialM != false){
						foreach ($matriculaInicialM as  $inicialH) {
							if($grado['id_grado']== $inicialH['id_grado']){
								echo $inicialH['cantidad'];	
								$sumaFilas += $inicialH['cantidad'];
							}

						}
					}					
					?>
				</td>
				<td>
					<?php
					if (isset($matriculaInicialF) && $matriculaInicialF != false){
						foreach ($matriculaInicialF as  $inicialF) {
							if($grado['id_grado']== $inicialF['id_grado']){
								echo $inicialF['cantidad'];	
								$sumaFilas += $inicialF['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					echo ($sumaFilas==0?"":$sumaFilas);
					$sumaFilas=0;
					?>
				</td>

				<!-- RETIRADOS-->
				<td>
					
					<?php
					if (isset($matriculaRetiradosM) && $matriculaRetiradosM != false){
						foreach ($matriculaRetiradosM as  $retiradosM) {
							if($grado['id_grado']== $retiradosM['id_grado']){
								echo $retiradosM['cantidad'];	
								$sumaFilas += $retiradosM['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					if (isset($matriculaRetiradosF) && $matriculaRetiradosF != false){
						foreach ($matriculaRetiradosF as  $retiradosF) {
							if($grado['id_grado']== $retiradosF['id_grado']){
								echo $retiradosF['cantidad'];	
								$sumaFilas += $retiradosF['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					echo ($sumaFilas==0?"":$sumaFilas);
					$sumaFilas=0;
					?>
				</td>


				<!-- DESERTORES-->
				<td>
					<?php
					if (isset($matriculaDesertoresM) && $matriculaDesertoresM != false){
						foreach ($matriculaDesertoresM as  $desertoresM) {
							if($grado['id_grado']== $desertoresM['id_grado']){
								echo $desertoresM['cantidad'];	
								$sumaFilas += $desertoresM['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					if (isset($matriculaDesertoresF) && $matriculaDesertoresF != false){
						foreach ($matriculaDesertoresF as  $desertoresF) {
							if($grado['id_grado']== $desertoresF['id_grado']){
								echo $desertoresF['cantidad'];	
								$sumaFilas += $desertoresF['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					echo ($sumaFilas==0?"":$sumaFilas);
					$sumaFilas=0;
					?>
				</td>

				<!-- TRASLADOS -->
				<td>
					<?php
					if (isset($matriculaTrasladadosM) && $matriculaTrasladadosM != false){
						foreach ($matriculaTrasladadosM as  $trasladosM) {
							if($grado['id_grado']== $trasladosM['id_grado']){
								echo $trasladosM['cantidad'];	
								$sumaFilas += $trasladosM['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					if (isset($matriculaTrasladadosF) && $matriculaTrasladadosF != false){
						foreach ($matriculaTrasladadosF as  $trasladosF) {
							if($grado['id_grado']== $trasladosF['id_grado']){
								echo $trasladosF['cantidad'];	
								$sumaFilas += $trasladosF['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					echo ($sumaFilas==0?"":$sumaFilas);
					$sumaFilas=0;
					?>
				</td>	
				<!-- MATRICULA FINAL -->			

				<td>
					<?php
					if (isset($matriculaFinalM) && $matriculaFinalM != false){
						foreach ($matriculaFinalM as  $finalM) {
							if($grado['id_grado']== $finalM['id_grado']){
								echo $finalM['cantidad'];	
								$sumaFilas += $finalM['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					if (isset($matriculaFinalF) && $matriculaFinalF != false){
						foreach ($matriculaFinalF as  $finalF) {
							if($grado['id_grado']== $finalF['id_grado']){
								echo $finalF['cantidad'];	
								$sumaFilas += $finalF['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					echo ($sumaFilas==0?"":$sumaFilas);
					$sumaFilas=0;
					?>
				</td>

				<!-- MATRICULA GRUPOS -->		


				<td>
					<?php
					if (isset($matriculaGruposM) && $matriculaGruposM != false){
						foreach ($matriculaGruposM as  $gruposM) {
							if($grado['id_grado']== $gruposM['id_grado']){
								echo $gruposM['cantidad'];	
								$sumaFilas += $gruposM['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					if (isset($matriculaGruposF) && $matriculaGruposF != false){
						foreach ($matriculaGruposF as  $gruposF) {
							if($grado['id_grado']== $gruposF['id_grado']){
								echo $gruposF['cantidad'];	
								$sumaFilas += $gruposF['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					echo ($sumaFilas==0?"":$sumaFilas);
					$sumaFilas=0;
					?>
				</td>

				<!-- REPROBADOS  -->

				<td>
					<?php
					if (isset($matriculaReprobadosM) && $matriculaReprobadosM != false){
						foreach ($matriculaReprobadosM as  $reprobadosM) {
							if($grado['id_grado']== $reprobadosM['id_grado']){
								echo $reprobadosM['cantidad'];	
								$sumaFilas += $reprobadosM['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					if (isset($matriculaReprobadosF) && $matriculaReprobadosF != false){
						foreach ($matriculaReprobadosF as  $reprobadosF) {
							if($grado['id_grado']== $reprobadosF['id_grado']){
								echo $reprobadosF['cantidad'];	
								$sumaFilas += $reprobadosF['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					echo ($sumaFilas==0?"":$sumaFilas);
					$sumaFilas=0;
					?>
				</td>

				<!-- APROBADOS  -->

				<td>
					<?php
					if (isset($matriculaAprobadosM) && $matriculaAprobadosM != false){
						foreach ($matriculaAprobadosM as  $aprobadosM) {
							if($grado['id_grado']== $aprobadosM['id_grado']){
								echo $aprobadosM['cantidad'];	
								$sumaFilas += $aprobadosM['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					if (isset($matriculaAprobadosF) && $matriculaAprobadosF != false){
						foreach ($matriculaAprobadosF as  $aprobadosF) {
							if($grado['id_grado']== $aprobadosF['id_grado']){
								echo $aprobadosF['cantidad'];	
								$sumaFilas += $aprobadosF['cantidad'];
							}
						}
					}					
					?>
				</td>
				<td>
					<?php
					echo ($sumaFilas==0?"":$sumaFilas);
					$sumaFilas=0;
					?>
				</td>




				<?php
				


				echo "</tr>";
			}

			?>
			<!-- PIE DE PÁGINA TOTALES-->
			<thead id="footer-data">
				<tr>
					<?php
					$totalMatIni=0;
					?>
					<td>Total</td>

					<!-- TOTAL DE GRUPOS-->
					<td>
						<?php
						if (isset($gruposCantidad) && $gruposCantidad != false){
							$sumFoot=0;
							foreach ($gruposCantidad as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
						}
						?>
					</td>
					<!-- TOTAL DE MAT. INICIAL-->
					<td>
						<?php
						if (isset($matriculaInicialM) && $matriculaInicialM != false){
							$sumFoot=0;
							foreach ($matriculaInicialM as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						if (isset($matriculaInicialF) && $matriculaInicialF != false){
							$sumFoot=0;
							foreach ($matriculaInicialF as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						echo ($totalMatIni==0?'':$totalMatIni);
						$totalMatIni=0;
						?>
					</td>


					<!-- TOTAL RETIRADOS-->

					<td>
						<?php
						if (isset($matriculaRetiradosM) && $matriculaRetiradosM != false){
							$sumFoot=0;
							foreach ($matriculaRetiradosM as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						if (isset($matriculaRetiradosF) && $matriculaRetiradosF != false){
							$sumFoot=0;
							foreach ($matriculaRetiradosF as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						echo ($totalMatIni==0?'':$totalMatIni);
						$totalMatIni=0;
						?>
					</td>

					<!-- DESERTORES -->

					<td>
						<?php
						if (isset($matriculaDesertoresM) && $matriculaDesertoresM != false){
							$sumFoot=0;
							foreach ($matriculaDesertoresM as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						if (isset($matriculaDesertoresF) && $matriculaDesertoresF != false){
							$sumFoot=0;
							foreach ($matriculaDesertoresF as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						echo ($totalMatIni==0?'':$totalMatIni);
						$totalMatIni=0;
						?>
					</td>

					<!-- TRASLADADOS -->

					<td>
						<?php
						if (isset($matriculaTrasladadosM) && $matriculaTrasladadosM != false){
							$sumFoot=0;
							foreach ($matriculaTrasladadosM as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						if (isset($matriculaTrasladadosF) && $matriculaTrasladadosF != false){
							$sumFoot=0;
							foreach ($matriculaTrasladadosF as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						echo ($totalMatIni==0?'':$totalMatIni);
						$totalMatIni=0;
						?>
					</td>

					<!-- MATRICULA FINAL -->

					<td>
						<?php
						if (isset($matriculaFinalM) && $matriculaFinalM != false){
							$sumFoot=0;
							foreach ($matriculaFinalM as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						if (isset($matriculaFinalF) && $matriculaFinalF != false){
							$sumFoot=0;
							foreach ($matriculaFinalF as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						echo ($totalMatIni==0?'':$totalMatIni);
						$totalMatIni=0;
						?>
					</td>

					<!-- MATRICULA GRUPOS -->

					<td>
						<?php
						if (isset($matriculaGruposM) && $matriculaGruposM != false){
							$sumFoot=0;
							foreach ($matriculaGruposM as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						if (isset($matriculaGruposF) && $matriculaGruposF != false){
							$sumFoot=0;
							foreach ($matriculaGruposF as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						echo ($totalMatIni==0?'':$totalMatIni);
						$totalMatIni=0;
						?>
					</td>

					<!-- MATRICULA REPROBADOS -->

					<td>
						<?php
						if (isset($matriculaReprobadosM) && $matriculaReprobadosM != false){
							$sumFoot=0;
							foreach ($matriculaReprobadosM as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						if (isset($matriculaReprobadosF) && $matriculaReprobadosF != false){
							$sumFoot=0;
							foreach ($matriculaReprobadosF as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						echo ($totalMatIni==0?'':$totalMatIni);
						$totalMatIni=0;
						?>
					</td>

					<!-- MATRICULA APROBADOS -->

					<td>
						<?php
						if (isset($matriculaAprobadosM) && $matriculaAprobadosM != false){
							$sumFoot=0;
							foreach ($matriculaAprobadosM as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						if (isset($matriculaAprobadosF) && $matriculaAprobadosF != false){
							$sumFoot=0;
							foreach ($matriculaAprobadosF as  $value) {							 
								$sumFoot += $value['cantidad'];
							}
							echo $sumFoot;
							$totalMatIni +=$sumFoot;
						}
						?>
					</td>
					<td>
						<?php
						echo ($totalMatIni==0?'':$totalMatIni);
						$totalMatIni=0;
						?>
					</td>


				</tr>
			</thead>

		</tbody>

	</table>


