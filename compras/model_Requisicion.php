<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class Requisicion extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    protected $NUM_REQ;
    public $CODDEPTO_SOL;
    public $COD_EMP_ELAB;
    public $OBSERVACIONES;
    public $PROYECTO;
    public $AUTORIZADO_POR;
    public $FECHA_AUTORIZADO;
    public $ANIO;
    public $STATUS;
    public $USUARIO;
    public $FECHA_ING;
    public $NO_FORMULARIO;
    public $COD_CAT;
    public $TIPO_REQ;
    public $COMENT_COMPRAS;
    
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','NUM_REQ','CODDEPTO_SOL','EMP_SOL','COD_EMP_ELAB','OBSERVACIONES','PROYECTO','AUTORIZADO_POR','FECHA_AUTORIZADO','ANIO','STATUS','CODIGO_GRUPO','USUARIO','FECHA_ING','NO_FORMULARIO','COD_CAT','TIPO_REQ','COMENT_COMPRAS','CREATED_AT','UPDATED_AT');
        $masx=implode($masx, ",");
        return $masx;
    }


        public function tableName()
    {
        return 'Requisicion';
    }
    
    public function Modulo()
    {
        return 'compras';
    }


    public function llave()
    {
        return array('COD_CIA','NUM_REQ','ANIO');

    }

    public function has_many()
    {
        return array('ID');

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
