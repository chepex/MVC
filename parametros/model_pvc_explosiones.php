<?php

/*
session_start();
error_reporting(E_ALL);*/
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pvc_explosiones extends DBAbstractModel {

    ############################### PROPIEDADES ################################
    protected $ID;
    public $TYPE_PLAN;
    public $PLAN;
    public $EXTRA;
    public $CREATED_AT;
    public $UPDATED_AT;
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('ID','TYPE_PLAN','PLAN','EXTRA','CREATED_AT','UPDATED_AT');
        $masx=implode($masx, ",");
        return $masx;
    }



        public function tableName()
    {
        return 'pvc_explosiones';
    }
    
    public function Modulo()
    {
        return 'parametros';
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
