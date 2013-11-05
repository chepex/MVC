<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_recibos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_RECIBO;
    public $COD_DESGLOSE;
    public $COD_BANCO;
    public $TIPO_DOCUMENTO; 
    public $COD_CUENTA;
    public $SECUENCIA; 
    public $FECHA_RECIBO; 
    public $VALOR_RECIBO;
	

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_RECIBO','COD_DESGLOSE','COD_BANCO','TIPO_DOCUMENTO','COD_CUENTA','SECUENCIA','FECHA_RECIBO','VALOR_RECIBO');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_CIA = BANCOS.COD_CIA
				 AND ". $this->tableName().".COD_BANCO = BANCOS.COD_BANCO";
        return $masx;                  
    }

    public function relacione_tablas(){
		 $masx= 'BANCOS';
         return $masx;                
    }


        public function tableName()
    {
        return 'pb_recibos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_RECIBO');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
   #Devuelve el Valor de la Secuencia para el siguente correlativo de la tabla pb_recibos
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBRECIBOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	
}
?>
