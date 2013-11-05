<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_detallerecibos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_DETRECIBO;
    public $COD_RECIBO;
    public $COD_CUOTA;
    public $CTA_1; 
    public $CTA_2;
    public $CTA_3; 
    public $CTA_4; 
    public $CTA_5;
	public $CONCEPTO;
	public $CARGO;
	public $ABONO;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_DETRECIBO','COD_RECIBO','COD_CUOTA','CTA_1','CTA_2','CTA_3','CTA_4','CTA_5','CONCEPTO','CARGO','ABONO');
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
        return 'pb_detallerecibos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
   #Devuelve el Valor de la Secuencia para el siguente correlativo de la tabla pb_detallerecibos
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBDETRECIBOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function listar_detrecibos($COD_CIA, $COD_RECIBO){
		$this->rows=array();
		$this->query="SELECT   DETRECI.CTA_1,
							   DETRECI.CTA_2,
							   DETRECI.CTA_3,
							   DETRECI.CTA_4,
							   DETRECI.CTA_5,
							   DETRECI.CONCEPTO,
							   DETRECI.CARGO,
							   DETRECI.ABONO,
							   BANCOS.NOM_BANCO,
							   PB_RECIBOS.COD_DESGLOSE,
							   PB_RECIBOS.COD_RECIBO,
							   PB_RECIBOS.COD_CUENTA,
							   PB_RECIBOS.SECUENCIA,
							   PB_RECIBOS.TIPO_DOCUMENTO,
							   PB_RECIBOS.VALOR_RECIBO
						FROM	PB_DETALLERECIBOS DETRECI
							INNER JOIN
								PB_RECIBOS
									ON DETRECI.COD_CIA = PB_RECIBOS.COD_CIA
										AND DETRECI.COD_RECIBO = PB_RECIBOS.COD_RECIBO
							INNER JOIN
								BANCOS
									ON PB_RECIBOS.COD_CIA = BANCOS.COD_CIA
										AND PB_RECIBOS.COD_BANCO = BANCOS.COD_BANCO
						WHERE   DETRECI.COD_CIA = ".$COD_CIA." AND DETRECI.COD_RECIBO = ". $COD_RECIBO;
		$this->get_results_from_query();
        return $this->rows;
	}
	
	
}
?>
