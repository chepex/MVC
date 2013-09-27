<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_estados extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_ESTADO;
    public $DESCRIPCION_ESTADO;   

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_ESTADO','DESCRIPCION_ESTADO');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
         return $masx;                
    }

    public function relacione_tablas(){
         return $masx;                
    }


        public function tableName()
    {
        return 'pb_estados';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_ESTADO');

    }

    ################################# MÉTODOS ##################################
   
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBESTADOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function get_options(){
		$lstestados = $this->get_lsoption($this->tableName(), array("COD_ESTADO"=>"","DESCRIPCION_ESTADO"=>""));
		return $lstestados;
	}
	
}
?>
