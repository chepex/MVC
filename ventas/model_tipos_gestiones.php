<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class tipos_gestiones extends DBAbstractModel {

    ############################### PROPIEDADES ################################
    protected $ID;
    public $NOMBRE;
    public $DESCRIPCION;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('ID',
                'NOMBRE',
                'DESCRIPCION'
                );
        $masx=implode($masx, ",");
        return $masx;
    }

    public function relaciones(){
         
         return "";                
    }

    public function relacione_tablas(){
         $masx= '';
         return $masx;                
    }

        public function tableName()
    {
        return 'TIPOS_GESTIONES';
    }
    
    public function Modulo()
    {
        return 'ventas';
    }


    public function llave()
    {
        return array('ID');

    }



}
?>
