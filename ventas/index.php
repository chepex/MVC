<?php
$controlador = $_REQUEST['ctl'];
echo "a";
require_once($controlador.'.php');


$objecon =  new $controlador;

$objecon->handler();

?>