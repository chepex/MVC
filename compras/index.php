<?php
$controlador = $_REQUEST['ctl'];

require_once($controlador.'.php');


$objecon =  new $controlador;

$objecon->handler();

?>