<?php
    
session_start();
class home {



	public function tableName()
	{
		return "home";
	}


	public function llave()
	{
		return 0;
	}

	public function tipo()
	{
		return 'page';
	}	

	public function valor_llave()
	{
		return 0;
	}


    

	public function atributos()
	{
		
		return 0;
	}

	public function page()
	{
		$html= "<H1>BIENVENIDO</H1>";
		echo $html;
	}
	
}




?>