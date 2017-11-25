<?php 
	
	namespace Config;

	class Autoload{ //Cargar las clases a utliziar

		public static function run(){
			spl_autoload_register(function($clase){
				$path = str_replace("\\", "/", $clase);
				
				require_once 'app/'.$path.'.php';
			});
		}
	}
?>