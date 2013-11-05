<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_detallepago extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_DETPAGO; 
    public $COD_DESGLOSE; 
    public $COD_CUOTA; 
    public $ABONO_INTERES; 
	public $ABONO_AMORTIZACION;


    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_DETPAGO','COD_DESGLOSE','COD_CUOTA','ABONO_INTERES','ABONO_AMORTIZACION');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_CIA = PB_DETALLEPRESTAMOS.COD_CIA
				 AND ". $this->tableName().".COD_CUOTA = PB_DETALLEPRESTAMOS.COD_CUOTA"; 
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= 'PB_DETALLEPRESTAMOS';
         return $masx;                
    }


        public function tableName()
    {
        return 'pb_detallepago';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_DETPAGO');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
   #Devuelve el correlativo de la secuecia para la tabla de Detalle de Pagos
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBDETPAGO.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	
}
?>
