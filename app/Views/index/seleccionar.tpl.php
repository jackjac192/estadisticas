<!DOCTYPE html>
<html lang="es">
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>



	<input type="hidden" id="id_base" value="<?php echo $db;?>"/>
	<input type="hidden" id="id_accion" value="<?php echo $accion;?>"/>
	<div class="container" id="id_seleccionar">
		<div class="row">
			<div class="col-md-4 hidden Jornadas">
				<div class="form-group">
					<label for="">JORNADA</label>
					<select name="sede" id="selectJornadas" class="form-control">
						<option value="0">SELECCIONE UNA JORNADA</option>						
						<?php							
							foreach ($jornadas as $value) {
								echo "<option value='".$value['id_jornada']."' >".$value['jornada']."</option>";
							}
						
						?>
					</select>
				</div>
			</div>
			<div class="col-md-4 hidden Jornadas">
				<div class="form-group">
					<label for="">AÑO LECTIVO</label>
					<select name="sede" id="selectLectivo" class="form-control">
						
						<?php							
							foreach ($anos as $value) {
								echo "<option value='".$value['year_matricula']."' >".$value['year_matricula']."</option>";
							}
						
						?>
					</select>
				</div>
			</div>
			<div class="col-md-4 filas">
				<div class="form-group">
					<label for="">Grado</label>
					<select name="sede" id="selectGrados" class="form-control">
						<option value="">SELECCIONE UN GRADO</option>
						<?php							
						foreach ($grados as $grado) {
							echo "<option value='".$grado['id_grado']."' >".$grado['grado']."</option>";
						}
						?>
					</select>
				</div>
			</div>
			<div class="col-md-4 filas">
				<div class="form-group">
					<label for="">Grupo</label>										
					<select name="grupo" id="selectGrupos" class="form-control">
						
					</select>
				</div>
			</div>
			<div class="col-md-4 filas">
				<div class="form-group">
					<label for="">Periodo</label>										
					<select name="grupo" id="selectPeriodos" class="form-control">
						<?php
						foreach ($periodos as $periodo) {
							echo "<option value='".$periodo['periodos']."' > PERIODO ".$periodo['periodos']."</option>";
						}
						?>
					</select>
				</div>
			</div>	
			<div class="col-md-1 hidden Repro">
				<div class="form-group">
					<label for="">Cantidad</label>										
					<select name="grupo" id="selectNumero" class="form-control">
						<option value="1"> 1 </option>
						<option value="2"> 2 </option>
						<option value="3"> 3 </option>
						<option value="4"> 4 </option>
						<option value="4"> 4 </option>
						<option value="5"> 5 </option>
						<option value="6"> 6 </option>
					</select>
				</div>
			</div>	
			<div class="col-md-2 hidden Repro">
				<div class="form-group">
					<label for="">Condición</label>										
					<select name="grupo" id="selectOperador" class="form-control">
						<option value="1"> >= </option>
						<option value="0"> = </option>
						<option value="-1"> <= </option>						
					</select>
				</div>
			</div>
						
		</div>
	</div>



<script src="<?=URL?>/web/js/jquery-1.12.4.js"></script>   
<script src="<?=URL?>/web/js/bootstrap.min.js"></script>
<script src="<?=URL?>/web/js/jquery.dataTables.min.js"></script>
<script src="<?=URL?>/web/js/multiselect.js"></script>


</body>
</html>