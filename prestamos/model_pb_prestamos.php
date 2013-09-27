<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_prestamos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_PRESTAMO; 
    public $REF_PRESTAMO;
    public $COD_BANCO; 
    public $FECHA_APERTURA;
    public $FECHA_VENCIMIENTO; 
	public $PLAZO;
    public $TASA_INTERES; 
    public $MONTO_APROBADO;
    public $VALOR_CUOTA;
    public $VALOR_SEGURO;
    public $COD_ESTADO;
    public $COD_LINEA;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_PRESTAMO','REF_PRESTAMO','COD_BANCO','FECHA_APERTURA','FECHA_VENCIMIENTO','PLAZO','TASA_INTERES','MONTO_APROBADO','VALOR_CUOTA','VALOR_SEGURO','COD_ESTADO','COD_LINEA');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_ESTADO = PB_ESTADOS.COD_ESTADO 
				 AND ". $this->tableName().".COD_CIA = BANCOS.COD_CIA
				 AND ". $this->tableName().".COD_BANCO = BANCOS.COD_BANCO
				 AND ".$this->tableName().".COD_CIA = PB_LINEASCREDITO.COD_CIA
				 AND ".$this->tableName().".COD_LINEA = PB_LINEASCREDITO.COD_LINEA";
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= 'PB_ESTADOS,BANCOS,PB_LINEASCREDITO';
         return $masx;                
    }


        public function tableName()
    {
        return 'pb_prestamos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_PRESTAMO');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBPRESTAMOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	
}
?>
