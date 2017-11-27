var url ="http://estadisticas.dev";
//var url = "http://agora.net.co/estadisticas"

$(function()
{
	var estado=true;
	var baseDatos = $('#databaseDB').val();
	$( ".ocultar-panel" ).hide();

	$('.dropdown-toggle').click(function(ev){
		if(estado)	{
			$('.dropdown-toggle').dropdown();
		}
		estado =false;
		console.log('es: '+estado);

	});




	$('#bs-example-navbar-collapse-1 li[data-accion=click]').each(function(i, element){


		$(element).click(function(ev){
			//console.log(ev);
			estado=true;
			$('#btnpdf').unbind();



			//$('li.dropdown').toggle('');
			$('#bs-example-navbar-collapse-1 li[data-accion=click]').each(function(i, element){
				$(element).removeClass('active');
			});
			$(this).addClass('active')

			$( "#gruposCheckbox input" ).unbind();
			$( "#gruposCheckbox input" ).attr('checked',false);
			$( ".ocultar-panel" ).show();



			var accion = ev.currentTarget.dataset.value;
			var seccion = ev.currentTarget.dataset.v;
			var dataString = 'accion='+accion;
			$.ajax({
				method: "POST",
				url: url+"/Estadisticas/getSeleccionarGrupo/"+baseDatos,
				data: dataString,
				
			})
			.done(function(datos) {
				$('#contenedor-estadisticas').show(100);
				$('#contentGrupos').empty();
				$('#contenedor-estadisticas').empty().append(datos);

				if(seccion=="RF"){
					$('#id_seleccionar').find('.filas').each(function(i,element){
						$(element).removeClass('col-md-4');
						$(element).addClass('col-md-3');
					});
					$('#id_seleccionar').find('.Repro').each(function(i,element){
						$(element).removeClass('hidden');
						//$(element).addClass('col-md-3');
					});
					$('#labelReprobados').hide();

				}
				if(seccion=="RR"){
					$('#labelReprobados').hide();
				}

				if(seccion==""){
					$('#labelReprobados').show();
				}
				if(seccion=="EJ"){
					$('#gruposCheckbox').hide();
					$('#id_seleccionar').find('.filas').each(function(i,element){
						$(element).addClass('hidden');
					});
					$('#id_seleccionar').find('.Jornadas').each(function(i,element){
						$(element).removeClass('hidden');
					});
					$('#selectJornadas').change(getTablaEstadisticas);
					$('#selectLectivo').change(getTablaEstadisticas);

				}else{
					$('#gruposCheckbox').show();
				}


				$('#selectGrados').change(function(ev){
					var idGrado = $(this).val();
					$('#inputGrado').val($('#selectGrados').val());

					$.ajax({
						method: "POST",
						url: url+"/Grupo/getGrupos/"+idGrado+"/"+baseDatos,
						data: '',						
					})
					.done(function(datos) {

						$('#selectGrupos').empty().append(datos);
						$('#btnIF').click(getTablaEstadisticas);
						$('#gruposCheckbox').find('input').on('click',getTablaEstadisticas);
						$('#selectGrupos').change(getTablaEstadisticas);
						$('#selectPeriodos').change(getTablaEstadisticas);
						$('#selectOperador').change(getTablaEstadisticas);
						$('#selectNumero').change(getTablaEstadisticas);

					});



				});

				function getTablaEstadisticas(ev){
					

					var cerrarInforme = false;
					if(ev.currentTarget.id == "btnIF"){
						ev.preventDefault();
						cerrarInforme = true;
						
					}
					
					var controladorAccion = $('#id_accion').val();
					var idGrupo = $('#selectGrupos').val();
					var idPeriodo = $('#selectPeriodos').val();
					var area = $('#idcheckAreas:checked').length;
					var informe =  $('#idInforme:checked').length;
					var reprobados = $('#idReprobados:checked').length;
					var academicas = $('#idAcademicas:checked').length;
					var periodos_acumulados = $('#idAcumulados:checked').length;
					var operador = $('#selectOperador').val();
					var numero = $('#selectNumero').val();
					var jornada = $('#selectJornadas').val();
					var ano = $('#selectLectivo').val();
					var db = $('#db').val();
					var ordering;
					ordering= jornada=="0"?true:false;
					var arreglo = controladorAccion.split('/');

					if(informe){
						$('#btnIF').removeClass('hidden');
					}else{
						$('#btnIF').addClass('hidden');
					}

					$('#idform').attr("action",url+"/PDF/"+arreglo[2]);

					$('#inputGrupo').val(idGrupo);
					$('#inputPeriodo').val(idPeriodo);
					$('#inputAcademica').val(academicas);
					$('#inputArea').val(area);
					$('#inputReprobadas').val(reprobados);
					$('#inputOperador').val(operador);
					$('#inputCantidad').val(numero);
					$('#inputAcumulados').val(periodos_acumulados);
					$('#inputInforme').val(informe);


					var data = 'grupo='+idGrupo+'&periodo='+idPeriodo+'&area='+area+'&reprobados='+
					reprobados+'&academicas='+academicas+'&numero='+numero+'&operador='+operador+'&jornada='+
					jornada+'&ano='+ano+'&per_acumulados='+periodos_acumulados+'&informe='+informe+'&cerrarInforme='+cerrarInforme+'&db='+db;
					console.log(cerrarInforme);
							//console.log(idPeriodo);

							$.ajax({
								method: "POST",
								url: url+controladorAccion+baseDatos,
								data: data,
								beforeSend: function(xhr){
									$('#contentGrupos').empty();
									var $img = $("<img class=loader-img id=img-preoload />");
									$img.attr('src', "http://agora.net.co/estadisticas/web/images/preloader.gif");
									$("#preload").empty().append($img);


								}

							})
							.done(function(datos) {
								$("#preload").empty()
								$('#contentGrupos').empty().append(datos);
								try {
									$('#table_id').DataTable({


										paging: false,
										"rowsGroup": [35],
										"autoWidth": false,
										"bFilter": false,
										"ordering": ordering,


										"oLanguage": {
											"sProcessing":     "Procesando...",
											"sLengthMenu": 'Mostrar <select>'+
											'<option value="10">10</option>'+
											'<option value="20">20</option>'+
											'<option value="30">30</option>'+
											'<option value="40">40</option>'+
											'<option value="50">50</option>'+
											'<option value="-1">All</option>'+
											'</select> registros',
											"sZeroRecords":    "No se encontraron resultados",
											"sEmptyTable":     "Ningún dato disponible en esta tabla",
											"sInfo":           "Mostrando del (_START_ al _END_) de un total de _TOTAL_ registros",
											"sInfoEmpty":      "Mostrando del 0 al 0 de un total de 0 registros",
											"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
											"sInfoPostFix":    "",
											"sSearch":         "Filtrar:",
											"sUrl":            "",
											"sInfoThousands":  ",",
											"sLoadingRecords": "Por favor espere - cargando...",
											"oPaginate": {
												"sFirst":    "Primero",
												"sLast":     "Último",
												"sNext":     "Siguiente",
												"sPrevious": "Anterior"
											},
											"oAria": {
												"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
												"sSortDescending": ": Activar para ordenar la columna de manera descendente"
											},
											"aaSorting": [[ 0, "asc" ]],

										}
									});
								}
								catch (e) {
								   // sentencias para manejar cualquier excepción
								   if(reprobados=='1')
								   {
								   	$('#table_id').find('.notas').empty();
								   }
								}

								if(reprobados=='1')
								{
									$('#table_id').find('.notas').empty();
								}
							});

						}//Fin fnción getTablaEstadisticas






					});



});

});


}); //Fin de  $function
