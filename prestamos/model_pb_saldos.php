<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_saldos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_PRESTAMO;   
    public $FECHA_ULTPAGO;
    public $SALDO_CAPITAL;  
    public $TASA_INTERES;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_PRESTAMO','FECHA_ULTPAGO','SALDO_CAPITAL','TASA_INTERES');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
         return $masx;                
    }

    public function relacione_tablas(){
         return $masx;                
    }


        public function tableName()
    {
        return 'pb_saldos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return $masx;

    }
}
?>
