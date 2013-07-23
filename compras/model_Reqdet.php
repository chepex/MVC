<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class Reqdet extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $NUM_REQ;
    public $ANIO;
    public $COD_PROD;
    public $NOMBRE;
    public $CODIGO_UNIDAD;
    public $CANTIDAD;  
    public $CREATED_AT;
    public $UPDATED_AT;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','NUM_REQ','ANIO','COD_PROD','NOMBRE','CODIGO_UNIDAD','CANTIDAD','CREATED_AT','UPDATED_AT');
        $masx=implode($masx, ",");
        return $masx;
    }
    
    public function relaciones(){
         $masx ="AND ".$this->tableName().".COD_CIA = PRODUCTOS.COD_CIA
                 AND ".$this->tableName().".COD_PROD = PRODUCTOS.COD_PROD";
         return $masx;                
    }

    public function relacione_tablas(){
         $masx= 'PRODUCTOS';
         return $masx;                
    }


        public function tableName()
    {
        return 'Reqdet';
    }
    
    public function Modulo()
    {
        return 'compras';
    }


    public function llave()
    {
        return array('COD_CIA','NUM_REQ','ANIO','COD_PROD');

    }

    ################################# MÉTODOS ##################################
    # Traer datos de un Requisicion
    public function get($ID=0) {
        if($cod_cia != 0) {
            $this->query = "SELECT   R.ID,
									 R.NAME,
									 R.VALUE_ITEM,
									 R.CREATED_AT,
									 R.UPDATED_AT,
									 R.CALCULATED									 
							FROM   PRODUC.PVC_OTHER_COSTS R
							WHERE R.ID=".$ID;
            $this->get_results_from_query();
        }
    }
       


}
?>
