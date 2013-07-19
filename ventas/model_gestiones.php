<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class gestiones extends DBAbstractModel {

    ############################### PROPIEDADES ################################
    protected $ID;
    public $ID_CLIENTE;
    public $ID_TIPO_GESTION;
    public $FECHA;
    public $DESCRIPCION;
    public $ID_RECORDATORIO;
    public $COD_CIA;
    public $COD_EMP;
    public $ESTILO;
    public $PRECIO;
    public $CANTIDAD;
    public $OBSERVACIONES;
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('ID',
                'ID_CONTACTO_X_EMPRESA',
                'ID_TIPO_GESTION',
                'FECHA',
                'DESCRIPCION',
                'ID_RECORDATORIO',
                'COD_CIA','COD_EMP',
                'ESTILO',
                'PRECIO',
                'CANTIDAD',
                'OBSERVACIONES');
        $masx=implode($masx, ",");
        return $masx;
    }

    public function relaciones(){
         $masx ="AND ".$this->tableName().".ID_CONTACTO_X_EMPRESA = contactos_x_empresas.id
                 AND ".$this->tableName().".ID_TIPO_GESTION = TIPOS_GESTIONES.id
                 AND ".$this->tableName().".ID_RECORDATORIO = recordatorios.id(+)";
         return $masx;                
    }

    public function relacione_tablas(){
         $masx= 'contactos_x_empresas , TIPOS_GESTIONES, recordatorios  ';
         return $masx;                
    }

        public function tableName()
    {
        return 'gestiones';
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
