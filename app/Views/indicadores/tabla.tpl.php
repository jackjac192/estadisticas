<table id="table_id" class="cell-border" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>Acciones</th>
			<th>Periodo</th>
			<th>Codigo</th>
			<th>Descripción</th>


		</tr>
	</thead>


	<tbody>
		<?php
		$cont = 1;
		foreach ($indicadores  as $clave => $row) {

			?>
			<tr>
				<td width="200px">					
					<button data-id="<?=$row['codigo']?>" data-fun="seleccionar" type="button" class="btn btn-warning "  >
						Seleccionar 
					</button>						
					
					<a href="#" id="<?=$row['codigo']?>" class="btn btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i></a>
										

				</td>
				<td data-id="<?=$row['codigo']?>"><?=$row['periodos']?></td>			
				<td data-id="<?=$row['codigo']?>"> <?=$row['codigo']?></td>
				<td data-id="<?=$row['codigo']?>"><?=$row['superior']?></td>


			</tr>
			<?php
		}
		?>
	</tbody>
</table>








