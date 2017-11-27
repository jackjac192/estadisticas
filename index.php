<?php

	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', realpath(dirname(__FILE__)) . DS);
	//define('URL', "http://estadisticas.dev");
	define('URL', "http://agora.net.co/estadisticas");


	require_once "app/Config/Autoload.php";
	session_start();

	Config\Autoload::run();

	if(empty($_GET['url'])){
	    $url = "";
	}else{
	    $url = $_GET['url'];
	}

	$request = new Config\Request($url);
	$request->execute();
?>
