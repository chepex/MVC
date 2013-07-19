<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class recordatorios extends DBAbstractModel {

    ############################### PROPIEDADES ################################
    protected $ID;
    public $TITULO;
    public $DESCRIPCION;
    public $FECHA;
    
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('ID','TITULO','DESCRIPCION','FECHA');
        $masx=implode($masx, ",");
        return $masx;
    }



        public function tableName()
    {
        return 'recordatorios';
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
