<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_provisioninteres extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_PRESTAMO;
    public $COD_CUOTA;
    public $NUMERO_CUOTA_AFECTA;
    public $VALOR_PROVISION; 
    public $MES_PROVISION;
    public $ANIO_PROVISION; 
    public $PROVISION_CERRADA;
	

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_PRESTAMO','COD_CUOTA','NUMERO_CUOTA_PROVISIONADA','VALOR_PROVISION','MES_PROVISION','ANIO_PROVISION','PROVISION_CERRADA');
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
        return 'pb_provisioninteres';
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
   
   #Devuelve el Valor de la Secuencia para el siguente correlativo de la tabla pb_desglosepago
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBDESGLOSE.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function listar_paraprovision($COD_CIA, $MES, $ANIO){
		$this->rows=array();
		$this->query="SELECT   PB_DETALLEPRESTAMOS.COD_CIA,
							   PB_DETALLEPRESTAMOS.COD_CUOTA,
							   PB_DETALLEPRESTAMOS.COD_PRESTAMO,
							   PB_DETALLEPRESTAMOS.TASA_INTERES,
							   PB_DETALLEPRESTAMOS.VALOR_INTERES,
							   PB_DETALLEPRESTAMOS.SALDO_CAPITAL,
							   PB_DETALLEPRESTAMOS.FECHA_PAGO,
							   PB_DETALLEPRESTAMOS.NUMERO_CUOTA,
							   TRUNC (LAST_DAY (FECHA_PAGO)) ultimo_dia_del_mes,
							   EXTRACT (YEAR FROM FECHA_PAGO) ANIO
						FROM   PB_DETALLEPRESTAMOS
						WHERE   COD_CIA = ".$COD_CIA." 
								AND EXTRACT (MONTH FROM FECHA_PAGO) = '".$MES."'
								AND EXTRACT (YEAR FROM FECHA_PAGO) = '".$ANIO."'";
		$this->get_results_from_query();
        return $this->rows;
	}
	
	public function verificarprovision($COD_CIA, $MES_PROVISION, $ANIO_PROVISION){
		$this->rows=array();
		$this->query="SELECT   COUNT ( * ), PROVISION_CERRADA
						FROM   PB_PROVISIONINTERES
							WHERE   MES_PROVISION = '".$MES_PROVISION."' 
									AND ANIO_PROVISION = '".$ANIO_PROVISION."'
									AND COD_CIA = ". $COD_CIA ."
						GROUP BY PROVISION_CERRADA";
		$this->get_results_from_query();
        return $this->rows;
	}
		
	
}
?>
