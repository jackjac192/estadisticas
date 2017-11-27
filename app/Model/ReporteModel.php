<?php

namespace Model;
use Config\DataBase as BD;

class ReporteModel extends BD{

	function __construct($bd){

		$this->database=$bd;

	}


	public function getInfoStudents($id_grupo){

		$this->query = "SELECT students.idstudents, students.primer_apellido, students.segundo_apellido, students.primer_nombre, students.segundo_nombre,
        (SELECT t_grupos.nombre_grupo from t_grupos WHERE t_grupos.id_grupo = students.id_grupo) as grupo,
        (SELECT t_grados.grado from t_grados WHERE t_grados.id_grado =
        (SELECT t_grupos.id_grado from t_grupos WHERE t_grupos.id_grupo = students.id_grupo)
        ) as grado,
        (SELECT t_grados.grado from t_grados WHERE t_grados.id_grado =
        (SELECT t_grupos.id_grado from t_grupos WHERE t_grupos.id_grupo = students.id_grupo)+1
        ) as grado_siguiente,
        students.tipo_identificacion,
        students.numero_documento,
        TIMESTAMPDIFF(YEAR,students.fecha_nacimiento, ('18-01-30') ) anos_cumplidos,
        (SELECT departamento.nombre from departamento WHERE departamento.iddepartamento = students.departanebti_expedicion)
        as depto_expedicion,
        (SELECT municipio.nombreMunicipio from municipio WHERE municipio.idmunicipio = students.municipio_expedicion)
        as mun_expedicion,
        students.genero,
        students.fecha_nacimiento,
        students.address as direccion,
        students.barrio,
        students.zona,
        students.telefono,
        students.nombre_ult_plantel,
        students.subsidado,
        students.interno,
        students.otro_modelo,
        students.grado_anterior,
        students.caracter,
        students.especialidad,
        (SELECT eps.eps_nombre from eps WHERE eps.id_eps = students.eps ) as eps,
        students.ips,
        students.tipo_sangre,
        students.ars,
        students.depto_expulsor,
        (SELECT departamento.nombre from departamento WHERE departamento.iddepartamento = students.depto_expulsor)
        as depto_expulsor,
        (SELECT municipio.nombreMunicipio from municipio WHERE municipio.idmunicipio = students.municipio_expulsor)
        as mun_expulsor,
        students.certificado,
        students.victima_conflicto,
        students.numero_carne_sisben,
        students.nivel_sisben,
        students.estrato,
        students.fuente_recurso,
        students.opcion,
        students.resguardo,
        students.negritudes,
        students.etnia,
        students.discapacidades,
        students.capacidades
        from students where students.id_grupo = {$id_grupo}
        ORDER BY students.primer_apellido, students.segundo_apellido, students.primer_nombre, students.segundo_nombre;
        ";	
        

        $this->execute_single_query();       

        if($this->resultado->num_rows > 0){
            $this->get_result_query();
            
            return $this->rows;

        }
        var_dump($this->database);
        return false;
    }


}
?>
