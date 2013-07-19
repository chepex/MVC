<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pvc_other_costs extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    protected $ID;
    public $NAME;
    public $VALUE_ITEM;
    public $CREATED_AT;
    public $UPDATED_AT;
    public $CALCULATED;
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('ID','NAME','VALUE_ITEM','CREATED_AT','UPDATED_AT','CALCULATED');
        $masx=implode($masx, ",");
        return $masx;
    }


        public function tableName()
    {
        return 'pvc_other_costs';
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
        
        #Devuelve la lista de Todas las Requisiciones
       /* public function get_all() { 
            $this->query = "SELECT   R.ID,
									 R.NAME,
									 R.VALUE_ITEM,
									 R.CREATED_AT,
									 R.UPDATED_AT,
									 R.CALCULATED									 
							FROM   PRODUC.PVC_OTHER_COSTS R";
            $this->get_results_from_query();
            $this->mensaje = 'Lista de Requisiciones del Mes';
			return $this->rows;
    }*/

    # Crear una nueva Requisicion
    public function set($parametro_data=array()) {
        if(array_key_exists('ID', $parametro_data)) {
            $this->get($parametro_data['ID']);
            if($parametro_data['ID'] != $this->ID) {
                
                
                $this->query = "
                        INSERT INTO PVC_OTHER_COSTS (ID,
												 NAME,
												 VALUE_ITEM,
												 CREATED_AT,
												 UPDATED_AT,
												 CALCULATED)
							VALUES   ( ,									  
									  ".$parametro_data['NAME'].",
									  ".$parametro_data['VALUE_ITEM'].",
									  ".$parametro_data['CREATED_AT'].",
									  ".$parametro_data['UPDATED_AT'].",
									  ".$parametro_data['CALCULATED']."
									  )";
                $this->execute_single_query();
                $this->mensaje = 'El parametro ha sido agregado exitosamente';
            } else {
                $this->mensaje = 'La Requisicion ya existe';
            }
        } 
    }

    # Modificar un usuario
    public function edit($parametro_data=array()) {

        $this->query = "UPDATE   PVC_OTHER_COSTS
							SET   NAME = ".$parametro_data['COD_CIA'].",
								  VALUE_ITEM = '".$parametro_data['NUM_REQ']."',
								  CREATED_AT = ".$parametro_data['CODDEPTO_SOL'].",
								  UPDATED_AT = ".$parametro_data['EMP_SOL'].",
								  CALCULATED = ".$parametro_data['COD_EMP_ELAB'].",								  
						WHERE 	ID=".$parametro_data['ID'];
        $this->execute_single_query();
        $this->mensaje = 'Parametro Modificada';
    }

    # Eliminar un usuario
    /*public function delete($parametro_data='') {
        $this->query = "DELETE 
								FROM PVC_OTHER_COSTS 
						WHERE 	ID=".$parametro_data['ID'];
        $this->execute_single_query();
        $this->mensaje = 'Parametro eliminada';
    }*/



}
?>
