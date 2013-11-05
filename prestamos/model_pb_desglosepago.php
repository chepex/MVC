<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_desglosepago extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_DESGLOSE;
    public $COD_DETDESGLOSE;
    public $COD_CUOTA; 
    public $COD_BANCO;
    public $TIPO_DOCUMENTO; 
    public $COD_CUENTA;
    public $SECUENCIA; 
    public $VALOR_ABONO;
    public $VALOR_PROVISION;
    public $VALOR_GASTO;
    public $VALOR_CAPITAL;
    public $FECHA_PAGO;
	

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_DESGLOSE','COD_DETDESGLOSE','COD_CUOTA','COD_BANCO','TIPO_DOCUMENTO','COD_CUENTA','SECUENCIA','VALOR_ABONO','VALOR_PROVISION','VALOR_GASTO','VALOR_CAPITAL','FECHA_PAGO');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_CIA = PB_DETALLEPRESTAMOS.COD_CIA 
				 AND ". $this->tableName().".COD_CUOTA = PB_DETALLEPRESTAMOS.COD_CUOTA
				 AND ". $this->tableName().".COD_BANCO = BANCOS.COD_BANCO
				 AND ".$this->tableName().".COD_CIA = BANCOS.COD_CIA";
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= 'PB_DETALLEPRESTAMOS,BANCOS';
         return $masx;                
    }


        public function tableName()
    {
        return 'Pb_desglosepago';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_DESGLOSE','COD_DETDESGLOSE');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
   #Devuelve el Valor de la Secuencia para el siguente correlativo de la tabla pb_desglosepago
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBDESGLOSE.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	#Devuelve el Valor de la Secuencia para el siguente correlativo de la tabla pb_desglosepago, el codigo unico del desglose
    public function nextval_seqdet(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBDETDESGLOSE.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
   
   
   public function listar_desglosepago($COD_CIA, $COD_CUOTA, $COD_DESGLOSE){
		 $this->rows = array();
		 $this->query = "SELECT   PB_DESGLOSEPAGO.COD_CUOTA,
								  PB_DESGLOSEPAGO.COD_DESGLOSE,
								  PB_DESGLOSEPAGO.COD_DETDESGLOSE,
								  BANCOS.COD_BANCO,
								  BANCOS.NOM_BANCO,
								  DECODE (PB_DESGLOSEPAGO.TIPO_DOCUMENTO,
								  'NC',
								  'NOTA DE CARGO',
								  'CH',
								  'CHEQUE')
								   PAGO_ATRAVES,
								   PB_DESGLOSEPAGO.COD_CUENTA,
								   PB_DESGLOSEPAGO.SECUENCIA,
								   PB_DESGLOSEPAGO.VALOR_ABONO
							FROM      PB_DESGLOSEPAGO
								INNER JOIN
									BANCOS
										ON PB_DESGLOSEPAGO.COD_BANCO = BANCOS.COD_BANCO
											AND PB_DESGLOSEPAGO.COD_CIA = BANCOS.COD_CIA
							WHERE   PB_DESGLOSEPAGO.COD_CIA = ".$COD_CIA." AND PB_DESGLOSEPAGO.COD_CUOTA =".$COD_CUOTA ." AND PB_DESGLOSEPAGO.COD_DESGLOSE='".$COD_DESGLOSE."'
							ORDER BY PB_DESGLOSEPAGO.COD_DETDESGLOSE";
            $this->get_results_from_query();
            return $this->rows ;
	}
	
	public function listar_degloserecibo($COD_DESGLOSE){
		$this->rows = array();
		$this->query = "SELECT   COD_CIA,
								 COD_DESGLOSE,
								 COD_BANCO,
								 TIPO_DOCUMENTO,
								 COD_CUENTA,
								 SECUENCIA,
								 SUM (VALOR_ABONO) VALOR_RECIBO,
								 FECHA_PAGO
						FROM   PB_DESGLOSEPAGO
							WHERE   COD_DESGLOSE = ".$COD_DESGLOSE."
						GROUP BY   	COD_CIA,
									COD_DESGLOSE,
									COD_BANCO,
									TIPO_DOCUMENTO,
									COD_CUENTA,
									SECUENCIA,
									FECHA_PAGO";
            $this->get_results_from_query();
            return $this->rows ;
	
	}
	
	public function listar_detdegloserecibo($COD_DESGLOSE, $COD_BANCO, $TIPODOC, $COD_CUENTA){
		$this->rows = array();
		$this->query = "SELECT
								PB_DESGLOSEPAGO.COD_CUOTA,
								PB_DESGLOSEPAGO.VALOR_ABONO,
								PB_DESGLOSEPAGO.VALOR_PROVISION,
								PB_DESGLOSEPAGO.VALOR_GASTO,
								PB_DESGLOSEPAGO.VALOR_CAPITAL
						FROM  PB_DESGLOSEPAGO
							WHERE PB_DESGLOSEPAGO.COD_DESGLOSE = ".$COD_DESGLOSE."
								AND PB_DESGLOSEPAGO.COD_BANCO = '".$COD_BANCO."'
								AND PB_DESGLOSEPAGO.TIPO_DOCUMENTO = '".$TIPODOC."'
								AND PB_DESGLOSEPAGO.COD_CUENTA = '".$COD_CUENTA."'";
            $this->get_results_from_query();
            return $this->rows ;
	
	}
	
	
	
	
}
?>
