<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class cotizadet extends DBAbstractModel {



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
        $masx= array('COD_CIA','NUM_REQ','ANIO','CORRELATIVO','COD_PROV','COD_PROD','CODIGO_UNIDAD','CANTIDAD','PRECIOUNI','VALORREQ','ACEPTADA');
        $masx=implode($masx, ",");
        return $masx;
    }
    
    public function relaciones(){
         $masx ="AND ".$this->tableName().".COD_CIA = PROVEEDORES.COD_CIA
                 AND ".$this->tableName().".COD_PROV = PROVEEDORES.COD_PROV
                 AND ".$this->tableName().".COD_CIA = PRODUCTOS.COD_CIA
                 AND ".$this->tableName().".COD_PROD = PRODUCTOS.COD_PROD";
         return $masx;                
    }

    public function relacione_tablas(){
         $masx= 'PROVEEDORES, PRODUCTOS';
         return $masx;                
    }


        public function tableName()
    {
        return 'cotizadet';
    }
    
    public function Modulo()
    {
        return 'compras';
    }


    public function llave()
    {
        return array('COD_CIA','NUM_REQ','ANIO','CORRELATIVO','COD_PROV','COD_PROD');

    }
    public function foreignkey(){
		return array('COD_CIA','NUM_REQ','ANIO');
	}

    ################################# MÉTODOS ##################################
   
       


}
?>
