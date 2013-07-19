<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class empresas extends DBAbstractModel {

    ############################### PROPIEDADES ################################
    protected $ID;
    public $NOMBRE;
    public $TEL;
    public $DIRECCION;
    public $NUM_REGISTRO;
    public $NIT;
    public $TCONTRIBUYENTE;
    public $GIRO;
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('ID','NOMBRE','TEL','DIRECCION','NUM_REGISTRO','NIT','TCONTRIBUYENTE','GIRO');
        $masx=implode($masx, ",");
        return $masx;
    }



        public function tableName()
    {
        return 'empresas';
    }
    
    public function Modulo()
    {
        return 'ventas';
    }


    public function llave()
    {
        return array('ID');

    }

    public function has_many()
    {
        return array('ID');

    }

}
?>
