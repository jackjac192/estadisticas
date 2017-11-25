<?php

	namespace Model;
	use Config\DataBase as BD;

	class ValoracionModel extends BD{
		 		
		
		

		function __construct($bd){
			$this->database=$bd;
			
		}

		public function obtenerValoraciones(){
			$this->query = "SELECT * FROM valoracion";
			$this->execute_single_query();

			if($this->resultado->num_rows > 0){
				$this->get_result_query();

				return  $this->rows;
			}

			return false;

		}

		
	}
?>