<?php
	include('../conn/config_db.inc.php');
	//Esta funcion nos permite ver los caracter especiales en las web por ejemplo Ñ
	//toma el juego de caracteres de la base de datos y utuliza
	putenv('NLS_LANG=AMERICAN_AMERICA.WE8ISO8859P1');
	//////////////////////////////////////////////////////////////////////////////////////////////
	//  AQUI INCLUIR CARPETAS ADODB A UTILIZAR......   												//
	//////////////////////////////////////////////////////////////////////////////////////////////
	include_once('../../../adodb/adodb.inc.php');
	include_once('../../../adodb/adodb-pager.inc.php');
	include_once('../../../adodb/tohtml.inc.php');

	//////////////////////////////////////////////////////////////////////////////////////////////
	//																							//
	//////////////////////////////////////////////////////////////////////////////////////////////
	
	$db = ADONewConnection(DRIVER); 
	//
//$db->debug = true;
	$db->Connect(HOST, $_SESSION['usuario'],  $_SESSION['clave'], BASEDATOS);
	

	if (!$db){
		print "No se realizó la conexión";
		exit();
	}
	
?>