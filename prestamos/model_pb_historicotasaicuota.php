<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_historicotasaicuota extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_PRESTAMO;
    public $TASA_INTERES_ANT;
    public $VALOR_CUOTA;
    public $COD_CUOTA;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_PRESTAMO','TASA_INTERES_ANT','VALOR_CUOTA','COD_CUOTA');
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
        return 'pb_historicotasaicuota';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_PRESTAMO','COD_CUOTA');

    }

    ################################# MÉTODOS ##################################
  
	
}
?>
