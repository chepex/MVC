<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class reqdet extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $NUM_REQ;
    public $ANIO;
    public $COD_PROD;
    public $NOMBRE;
    public $CODIGO_UNIDAD;
    public $CANTIDAD;  
    public $ESPECIFICACIONES;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','NUM_REQ','ANIO','COD_PROD','NOMBRE','CODIGO_UNIDAD','CANTIDAD','ESPECIFICACIONES');
        $masx=implode($masx, ",");
        return $masx;
    }
    
    public function relaciones(){
         $masx ="AND ".$this->tableName().".COD_CIA = PRODUCTOS.COD_CIA
                 AND ".$this->tableName().".COD_PROD = PRODUCTOS.COD_PROD
                 AND ".$this->tableName().".CODIGO_UNIDAD = UNIDADES.CODIGO_UNIDAD";
         return $masx;                
    }

    public function relacione_tablas(){
         $masx= 'PRODUCTOS, UNIDADES';
         return $masx;                
    }


        public function tableName()
    {
        return 'reqdet';
    }
    
    public function Modulo()
    {
        return 'compras';
    }


    public function llave()
    {
        return array('COD_CIA','NUM_REQ','ANIO','COD_PROD');

    }
    public function foreignkey(){
		return array('COD_CIA','NUM_REQ','ANIO');
	}

    ################################# MÉTODOS ##################################
   
   #Consulta Base para Generar el detalle de las Cotizaciones, a partir de los productos requisados
	public function definir_detrequisiciones($COD_CIA, $NUM_REQ, $ANIO){
		 $this->rows = array();
		 $this->query = "   SELECT   RQ.NUM_REQ,
									 PR.COD_PROD,
									 PR.NOMBRE,
									 UNI.DESCRIPCION,
									 RQD.CANTIDAD,
									 RQD.ESPECIFICACIONES
							FROM            REQUISICION RQ
								INNER JOIN
										REQDET RQD
											ON     RQ.COD_CIA = RQD.COD_CIA
												   AND RQ.NUM_REQ = RQD.NUM_REQ
												   AND RQ.ANIO = RQD.ANIO
								INNER JOIN
										PRODUCTOS PR
											ON RQD.COD_PROD = PR.COD_PROD 
											AND RQD.COD_CIA = PR.COD_CIA
								INNER JOIN
										UNIDADES UNI
											ON RQD.CODIGO_UNIDAD = UNI.CODIGO_UNIDAD
							WHERE   RQ.COD_CIA = ".$COD_CIA." 
									AND RQ.NUM_REQ='".$NUM_REQ."' 
									AND RQ.ANIO = ".$ANIO."
							ORDER BY   PR.COD_PROD";
            $this->get_results_from_query();
            return $this->rows ;
    }
    


}
?>
