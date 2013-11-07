<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_ncprestamos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_PRESTAMO;
    public $COD_DETNOTA;
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
        $masx= array('COD_CIA','COD_PRESTAMO','COD_DETNOTA','CTA_1','CTA_2','CTA_3','CTA_4','CTA_5','CONCEPTO','CARGO','ABONO');
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
        return 'pb_ncprestamos';
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
		$this->query="SELECT SEQ_PBNCPRESTAMO.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	#Devuelve el Valor de la Secuencia para el siguente correlativo de la tabla pb_detallerecibos
    public function listar_partidadesembolso($COD_CIA, $COD_PRESTAMO){
		$this->rows=array();
		$this->query="SELECT   COD_CIA,
							   COD_PRESTAMO,
							   COD_DETNOTA,
							   CTA_1,
							   CTA_2,
							   CTA_3,
							   CTA_4,
							   CTA_5,
							   CONCEPTO,
							   CARGO,
							   ABONO
					  FROM   PB_NCPRESTAMOS
						WHERE COD_CIA= ".$COD_CIA." AND COD_PRESTAMO=".$COD_PRESTAMO."
						ORDER BY   COD_DETNOTA";
		$this->get_results_from_query();
        return $this->rows;
	}
	
}
?>
