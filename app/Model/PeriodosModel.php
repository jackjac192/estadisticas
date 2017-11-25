<?php

	namespace Model;
	use Config\DataBase as BD;

	class PeriodosModel extends BD{
		 		
		

		function __construct($bd){
			
			$this->database=$bd;
			
		}

		
		public function obtenerGrado($grupo)

		{				
			$this->query = "SELECT id_grado FROM t_grupos 
							WHERE id_grupo = '{$grupo}'					
							";
			$this->execute_single_query();

			if($this->resultado->num_rows > 0){
				$this->get_result_query();

				return array('estado' => true, 'datos' => $this->rows);
			}

			return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
		}

		public function getPeriodos()

		{				
			$this->query = "SELECT periodos, peso FROM periodos											
							";
			$this->execute_single_query();

			if($this->resultado->num_rows > 0){
				$this->get_result_query();

				return array('estado' => true, 'datos' => $this->rows);
			}

			return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
		}

		public function getPeriodosEvaluados()

		{				
			$this->query = "SELECT periodos, peso FROM periodos WHERE apertura is not null										
							";
			$this->execute_single_query();

			if($this->resultado->num_rows > 0){
				$this->get_result_query();

				return array('estado' => true, 'datos' => $this->rows);
			}

			return array('estado'=>false, 'mensaje'=>'Ocurrio un error, vuelve a intertarlo');
		}


		
	}
?>