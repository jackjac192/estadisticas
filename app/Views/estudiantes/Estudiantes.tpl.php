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
				echo "<th  width=20px class=fila-asignaturas>Edad</th>";
				foreach ($filasGenero as $key => $value) {
					echo "<th class=fila-asignaturas>".$value['H']."</th>";
					echo "<th class=fila-asignaturas>".$value['M']."</th>";
				}
				echo "<th  class=fila-asignaturas>Total</th>";
				?>				
			</tr>
		</thead>


		<tbody>
			<?php
			$sumaTotal=0;
			//var_dump($tablaPuestos);
			$resultados= array();
			if(isset($edades) and $edades!= false){
				
				$cont=1;
				
				foreach ($edades as $value) {
					$clase=($cont%2)==0?'gris':'';
					
					echo "<tr class=".$clase.">";
					echo '<td>'.$value.'</td>';
					$suma=0;
					
					foreach ($filasGenero as $filas) {
						
						?>
						<td>							
							<?php

							if (isset($generoH) && $generoH != false) 
							{	
								foreach ($generoH as $h) 
								{	


									if($filas['id_grado'] == $h['id_grado'] and $h['edad'] == $value){
										echo ($h['cantidad']==0?"":$h['cantidad']);
										$suma += $h['cantidad'];
									}
								}

							}
							
							?>
						</td>
						<td class="r-rojo">
							<?php
							if (isset($generoM) && $generoM != false) 
							{
								foreach ($generoM as $m) 
								{
									if($filas['id_grado'] == $m['id_grado'] and $m['edad'] == $value){
										echo $m['cantidad'];
										$suma += $m['cantidad'];
									}
								}
							}							
							?>
						</td>
						<?php
					}
					?>
					<td>
						<?php
						
						echo ($suma==0?"":$suma);					
						?>
					</td>
					<?php
					$cont++;
					echo '</tr>';
				}	
				//;
			}	
			

			//echo "suma es:".$sumarColumn;		
			?>

			<thead id="footer-data">
				<tr>
					<td>Total</td>
					<?php


					foreach ($filasGenero as $key => $filass) {
						?>
						<td>

							<?php

							if (isset($generoH) && $generoH != false) 
								{	$suma=0;

									foreach ($generoH as $h) 
									{	

										if($filass['id_grado'] == $h['id_grado']){
											$suma += $h['cantidad'];
										}
									}
									$sumaTotal += $suma;
									echo ($suma==0?"":$suma);
								}

								?>
							</td>
							<td class="r-rojo">

								<?php


								if (isset($generoM) && $generoM != false) 
								{
									$suma=0;
									foreach ($generoM as $m) 
									{
										if($filass['id_grado'] == $m['id_grado']){
											$suma += $m['cantidad'];
										}
									}
									$sumaTotal += $suma;
									echo ($suma==0?"":$suma);
									
								}

								?>
							</td>


							<?php					


						}
						?>

						<td>
							<?php echo ($sumaTotal==0?"":$sumaTotal);?>
						</td>

					</tr>
				</thead>


				<?php

				?>
			</tbody>

		</table>
