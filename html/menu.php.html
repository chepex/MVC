<?php
session_start();
    require_once 'dao.php';
    

class usuario extends dao{
var $id;
var $usuario;
var $pass;
var $rol;



	public function tableName()
	{
		return 'usuarios';
	}

	public function tipo()
	{
		return 'class';
	}	


	public function llave()
	{
		return 'id';
	}


	public function valor_llave()
	{
		return $this->id;
	}


    public function coneccion2() {
        require_once 'config.php';
        // connecting to mysql
        $con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
        // selecting database
        mysql_select_db(DB_DATABASE);
 
        // return database handler
        return $con;
    }

	public function atributos()
	{
		$masx= array('id','usuario','pass','id_rol');
		$masx=implode($masx, ",");
		return $masx;
	}

	public function get_rol($user,$pass)
	{	
	$this->coneccion2();


		$rs=mysql_query("select count(*) from usuarios WHERE usuario ='$user' AND pass= '$pass'") or die (mysql_error()) ;

		if (!$rs) {
		    echo 'Could not run query: ' . mysql_error();
		    exit;
		}
		$row = mysql_fetch_row($rs);
		$_SESSION['rol'] = $row[0];
		return $row[0];
	}	
}




?>