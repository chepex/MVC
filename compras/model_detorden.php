<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class detorden extends DBAbstractModel {

    ############################### PROPIEDADES ################################
    public $NUM_ORDEN;
    public $COD_CIA;
    public $COD_PROD;
    public $CODIGO_UNIDAD;
    public $CANTIDAD;  
    public $PRECIOUNI;
    public $VALORREQ;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('NUM_ORDEN','COD_CIA','COD_PROD','CODIGO_UNIDAD','CANTIDAD','PRECIOUNI','VALORREQ');
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
        return 'detorden';
    }
    
    public function Modulo()
    {
        return 'compras';
    }


    public function llave()
    {
        return array('COD_CIA','NUM_ORDEN','COD_PROD');

    }
    public function foreignkey(){
		return array('COD_CIA','NUM_REQ','ANIO');
	}

    ################################# MÉTODOS ##################################
    
       


}
?>
