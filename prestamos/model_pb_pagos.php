<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_pagos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_PAGO; 
    public $COD_PRESTAMO;
    public $FECHA_PAGO; 

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_PAGO','COD_PRESTAMO','FECHA_PAGO');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_CIA = PB_PRESTAMOS.COD_CIA 
				 AND ". $this->tableName().".COD_PRESTAMO = PB_PRESTAMOS.COD_PRESTAMO
				 AND PB_PRESTAMOS.COD_CIA = BANCOS.COD_CIA
				 AND PB_PRESTAMOS.COD_BANCO = BANCOS.COD_BANCO
				 AND PB_PRESTAMOS.COD_CIA = PB_LINEASCREDITO.COD_CIA
				 AND PB_PRESTAMOS.COD_LINEA = PB_LINEASCREDITO.COD_LINEA";
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= 'PB_PRESTAMOS,PB_LINEASCREDITO,BANCOS';
         return $masx;                
    }


        public function tableName()
    {
        return 'pb_pagos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_PAGO');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
   #Devuelve el correlativo de la secuencia para la tabla pb_pagos
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBPAGOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	
}
?>
