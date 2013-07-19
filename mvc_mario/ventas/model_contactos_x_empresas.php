<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class contactos_x_empresas extends DBAbstractModel {

    ############################### PROPIEDADES ################################
    protected $ID;
    public $EMPRESA_ID;
    public $NOMBRE;
    public $APELLIDOS;
    public $CARGO;
    public $TELEFONO;
    public $CORREO;
    
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array( "ID",
            "EMPRESA_ID",
            "NOMBRES",
            "APELLIDOS",
            "CARGO",
            "TELEFONO",
            "CORREO");
        $masx=implode($masx, ",");
        return $masx;
    }

        public function tableName()
    {
        return 'contactos_x_empresas';
    }
    
    public function Modulo()
    {
        return 'ventas';
    }

    public function relaciones(){
         
         return "AND ".$this->tableName().".empresa_id = empresas.id";          
    }

    public function relacione_tablas(){
         
         return "empresas";
    }

    public function llave()
    {
        return array( "ID");
    }

    

}
?>
