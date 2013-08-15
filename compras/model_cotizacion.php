<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class cotizacion extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $NUM_REQ;
    public $ANIO;
    public $CORRELATIVO;
    public $FECHA;
    public $COD_PROV;
    public $ACEPTADA;
    public $USUARIO;
    public $FECHA_ING;
    

    
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','NUM_REQ','ANIO','CORRELATIVO','FECHA','COD_PROV','ACEPTADA','USUARIO','FECHA_ING');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
         $masx ="AND ".$this->tableName().".NUM_REQ = REQUISICION.NUM_REQ
				 AND ".$this->tableName().".COD_CIA = REQUISICION.COD_CIA
				 AND ".$this->tableName().".COD_CIA = PROVEEDORES.COD_CIA
				 AND ". $this->tableName().".COD_PROV = PROVEEDORES.COD_PROV";
         return $masx;                
    }

    public function relacione_tablas(){
         $masx= 'requisicion, PROVEEDORES';
         return $masx;                
    }


        public function tableName()
    {
        return 'cotizacion';
    }
    
    public function Modulo()
    {
        return 'compras';
    }


    public function llave()
    {
        return array('COD_CIA','NUM_REQ','ANIO','COD_PROV','CORRELATIVO');

    }

    public function has_many()
    {
        return array('ID');

    }


    ################################# MÉTODOS ##################################
    
    public function comparativo_precios($NUM_REQ,$ANIO){
		 $this->rows = array();
		 $this->query = "  SELECT   CT.COD_CIA,
									CT.NUM_REQ,
									CT.ANIO,
									CT.CORRELATIVO,
									PROV.COD_PROV,
									PROV.NOMBRE,
									PR.COD_PROD,
									PR.NOMBRE AS NOMBRE_PROD,
									CTD.CANTIDAD,
									CTD.PRECIOUNI,
									CTD.VALORREQ
							FROM            COTIZACION CT
								INNER JOIN
									COTIZADET CTD
										ON     CT.COD_CIA = CTD.COD_CIA
												AND CT.NUM_REQ = CTD.NUM_REQ
												AND CT.ANIO = CTD.ANIO
												AND CT.CORRELATIVO = CTD.CORRELATIVO
								INNER JOIN
									PRODUCTOS PR
										ON PR.COD_CIA = CTD.COD_CIA 
												AND PR.COD_PROD = CTD.COD_PROD
								INNER JOIN
									PROVEEDORES PROV
										ON CTD.COD_CIA = PROV.COD_CIA 
												AND CTD.COD_PROV = PROV.COD_PROV
							WHERE   CT.NUM_REQ = '".$NUM_REQ."' AND CT.ANIO = ".$ANIO."
								ORDER BY   CTD.COD_PROD";
            $this->get_results_from_query();
            return $this->rows ;
    }
  
	
	
}
?>
