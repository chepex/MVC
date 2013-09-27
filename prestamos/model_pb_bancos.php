<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_bancos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_BANCO; 
    public $NOM_BANCO;
    public $DIRECCION; 
    public $TELEFONO1;
    public $TELEFONO2; 
	public $CONTACTO;
    public $OBSERVACIONES; 
    public $DIAS_MAX_NOTAS;
    public $DIAS_MAX_CHEQUES; 
	public $NOM_CORTO;
    public $ORDEN_PAGO; 


    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_BANCO','NOM_BANCO','DIRECCION','TELEFONO1','TELEFONO2','CONTACTO','OBSERVACIONES','DIAS_MAX_NOTAS','DIAS_MAX_CHEQUES','NOM_CORTO','ORDEN_PAGO');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  "";
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= '';
         return $masx;                
    }


        public function tableName()
    {
        return 'bancos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_BANCO');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBBANCOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function get_options(){
		$lstbancos = $this->get_lsoption($this->tableName(), array("COD_BANCO"=>"","NOMBRE_CORTO"=>""));
		return $lstbancos;
	}
	
	
}
?>
