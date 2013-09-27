<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_tipos_creditos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_TIPOCREDITO;
    public $DESCRIPCION_TIPOCREDITO;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_TIPOCREDITO','DESCRIPCION_TIPOCREDITO');
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
        return 'pb_tipos_creditos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_TIPOCREDITO');

    }

    ################################# MÉTODOS ##################################
   
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBTIPOS_CREDITOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function get_options(){
		$lsttipocredito = $this->get_lsoption($this->tableName(), array("COD_TIPOCREDITO"=>"","DESCRIPCION_TIPOCREDITO"=>""));
		return $lsttipocredito;
	}
	
	
}
?>
