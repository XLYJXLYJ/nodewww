<?php
error_reporting(0);
session_start();
header("Content-type: text/html; charset=utf-8");
class LOAD{
	static function loadClass($class_name){
		$filename = "./Lib/".$class_name.".php";
		 if(is_file($filename)) return require($filename);
		$filename = "./Plugin/".$class_name.".php";
		 if(is_file($filename)) return require($filename);
		}
}
if(!file_exists("./install")){
	require("ORG/YouYa.php");
  require("Model/Model.php");
  spl_autoload_register(array('LOAD', 'loadClass'));
  App::run();
}else{
	$status = file_get_contents("./install/status.txt");
	if ($status == "complete") {
	    require("ORG/YouYa.php");
		  require("Model/Model.php");
		  spl_autoload_register(array('LOAD', 'loadClass'));
		  App::run();
	} else {
	    echo '<script>window.location.href="./install";</script>';
	}
}
?>