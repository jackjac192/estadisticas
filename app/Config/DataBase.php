<?php 
namespace Config;


class DataBase{
  
    
private $user       ='agoranet';
private $password   ='Richard_111';
private $host       ='localhost';
protected  $database = '';
    
/*

private $user       ='root';
private $password   ='';
private $host       ='localhost';
protected  $database = '';



*/
private $conexion;

protected $query;
protected $rows  = array();
protected $resultado;

  // Abre la coneccion a la BD
private function open_connect(){    

    $this->conexion = new \mysqli($this->host, $this->user, $this->password, $this->database);
    $this->conexion->set_charset("utf8");
    if($this->conexion->connect_errno){
       echo "Error: Fallo al conectarse a MySQL debido a: \n";
       echo "Errno: " . $this->conexion->connect_errno . "\n";
       echo "Error: " . $this->conexion->connect_error . "\n";
   }
}



    // Cierra la conexion a la BD
private function close_connect(){

    $this->conexion->close();
}

    // Ejecuta un query sencillo
protected function execute_single_query(){
    //$this->database = static::$bd;
    $this->open_connect();
    $this->resultado = $this->conexion->query($this->query);
    return $this->database;
}

    // Obtiene los resultados de un query
protected function get_result_query(){
    //$this->database = static::$bd;
    $this->rows = array();

    $this->open_connect();
    $result = $this->conexion->query($this->query);

    if($this->conexion->connect_errno){
       echo "Error: Fallo al conectarse a MySQL debido a: \n";
       echo "Errno: " . $this->conexion->connect_errno . "\n";
       echo "Error: " . $this->conexion->connect_error . "\n";
   }else{

     while($this->rows[] = $result->fetch_assoc());

     $result->close();
     $this->close_connect();
     array_pop($this->rows);
 }
}
}
