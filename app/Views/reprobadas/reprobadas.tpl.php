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
				<th>#</th>
				<th class="left-align">NOMBRES Y APELLIDOS</th>
				<th class="left-align">Asignaturas</th>				
				<th>FAA</th>
				<th>Val</th>
				<th>Sup</th>
			</tr>
		</thead>


		<tbody>
			<?php
			//var_dump($tablaContenido);
			
			if(isset($tablaContenido) and $tablaContenido!= false){
				$id=0;
				foreach ($tablaContenido as $value) {
					
					if($id != $value['id_estudiante'])
					{	
						$contador=0;
						$id=$value['id_estudiante'];
						

						foreach ($tablaContenido as $tabla) {
							if($id==$tabla['id_estudiante']){
								$contador++;
							}
						}
						$clase=($cont%2)==0?'gris':'';
						echo "<tr class=".$clase.">";
						echo "<td rowspan=".$contador.">".$cont."</td>";
						echo "<td rowspan=".$contador." class=left-align>".$value['primer_apellido']." ".$value['segundo_apellido']." ".$value['primer_nombre']." ".$value['primer_nombre']."</td>";
						foreach ($tablaContenido as $registro) {
							

							if($id == $registro['id_estudiante']){													
								$inasistencia = $registro['Inasistencia']=="0"?"":$registro['Inasistencia'];
								echo "<td class='".$clase." left-align'> ".$registro['Asignatura']."</td>";
								echo "<td class=".$clase."> ".$inasistencia."</td>";
								echo "<td class=".$clase."> ".$registro['valoracion']."</td>";
								echo "<td class=".$clase."></td>";
								echo "</tr>";
							}
							

						}
						
						$cont++;
					}
					

				}
			}
			?>

		</tbody>
	</table>


</div>