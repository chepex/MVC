<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$controlador = $_REQUEST['ctl'];

require_once($controlador.'.php');


$objecon =  new $controlador;

$objecon->handler();

?>
