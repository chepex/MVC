<?php
session_start();
if(isset($_REQUEST['modulo'])){
	$_SESSION['modulo'] = $_REQUEST['modulo'];
	header("Location:index.php?ctl=controller_pb_bancos&act=get_all");
}
?>
