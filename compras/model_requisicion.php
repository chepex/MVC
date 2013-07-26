<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class requisicion extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    protected $NUM_REQ;
    public $CODDEPTO_SOL;
    public $EMP_SOL;
    public $COD_EMP_ELAB;
    public $OBSERVACIONES;
    public $PROYECTO;
    public $AUTORIZADO_POR;
    public $FECHA_AUTORIZADO;
    public $ANIO;
    public $STATUS;
    public $CODIGO_GRUPO;
    public $USUARIO;
    public $FECHA_ING;
    public $NO_FORMULARIO;
    public $COD_CAT;
    public $TIPO_REQ;
    public $COMENT_COMPRAS;
    public $CREATED_AT;
    public $UPDATED_AT;
    
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','NUM_REQ','CODDEPTO_SOL','EMP_SOL','COD_EMP_ELAB','OBSERVACIONES','PROYECTO','AUTORIZADO_POR','FECHA_AUTORIZADO','ANIO','STATUS','CODIGO_GRUPO','USUARIO','FECHA_ING','NO_FORMULARIO','COD_CAT','TIPO_REQ','COMENT_COMPRAS','CREATED_AT','UPDATED_AT');
        $masx=implode($masx, ",");
        return $masx;
    }


        public function tableName()
    {
        return 'requisicion';
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
    # Traer datos de un requisicion
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
    
    public function get_datagrafico(){
		$this->rows=array();
		$this->query = "  SELECT   CATEGORIAS.COD_CAT,
								   CATEGORIAS.NOM_CAT,
								   NVL (PRESUPUESTOS.VALOR, 0) VAL_PRESUPUESTADO,
								   NVL (PRESUPUESTOS.SALDO, 0) SALDO
						  FROM   PRESUPUESTOS, CATEGORIAS
						  WHERE       PRESUPUESTOS.ANIO = ".date('Y')."
								AND PRESUPUESTOS.MES = ".date('m')."
								AND PRESUPUESTOS.CCOSTO = '".$_SESSION['PROYECTO']."'
								AND PRESUPUESTOS.TIPO = 'P'
								AND CATEGORIAS.COD_CIA = ".$_SESSION['cod_cia']."
								AND CATEGORIAS.PRESUP = 'S'
								AND (    CATEGORIAS.CTA_1 = PRESUPUESTOS.CTA1
										AND CATEGORIAS.CTA_2 = PRESUPUESTOS.CTA2
										AND CATEGORIAS.CTA_3 = PRESUPUESTOS.CTA3
										AND CATEGORIAS.CTA_4 = PRESUPUESTOS.CTA4
										AND CATEGORIAS.CTA_5 = PRESUPUESTOS.CTA5
								OR (    CATEGORIAS.VTA_1 = PRESUPUESTOS.CTA1
										AND CATEGORIAS.VTA_2 = PRESUPUESTOS.CTA2
										AND CATEGORIAS.VTA_3 = PRESUPUESTOS.CTA3
										AND CATEGORIAS.VTA_4 = PRESUPUESTOS.CTA4
										AND CATEGORIAS.VTA_5 = PRESUPUESTOS.CTA5)
								OR (    CATEGORIAS.INV_1 = PRESUPUESTOS.CTA1
										AND CATEGORIAS.INV_2 = PRESUPUESTOS.CTA2
										AND CATEGORIAS.INV_3 = PRESUPUESTOS.CTA3
										AND CATEGORIAS.INV_4 = PRESUPUESTOS.CTA4
										AND CATEGORIAS.INV_5 = PRESUPUESTOS.CTA5))
						ORDER BY   CATEGORIAS.COD_CAT";
            $this->get_results_from_query();
            $i=0;
            foreach($this->rows as $campo => $valor){
				$categorias[]="'".$this->rows[$i]['COD_CAT']."'";
				$saldisponible[]=$this->rows[$i]['SALDO'];
				$i++;
			}
			$datagrafico = array("categorias"=>implode(",",$categorias), "disponible"=>implode(",",$saldisponible));
            return $datagrafico;
	}
}
?>
