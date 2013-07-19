<?php
session_start();
    require_once 'dao.php';
    

class roles_x_menus extends dao{
var $id;
var $descripcion;



	public function tableName()
	{
		return 'roles';
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
		$masx= array('id','descripcion');
		$masx=implode($masx, ",");
		return $masx;
	}


}




?>