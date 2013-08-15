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
    public $COD_PRIORIDAD;
    
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','NUM_REQ','CODDEPTO_SOL','EMP_SOL','COD_EMP_ELAB','OBSERVACIONES','PROYECTO','AUTORIZADO_POR','FECHA_AUTORIZADO','ANIO','STATUS','CODIGO_GRUPO','USUARIO','FECHA_ING','NO_FORMULARIO','COD_CAT','TIPO_REQ','COMENT_COMPRAS','COD_PRIORIDAD');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
         $masx ="AND ".$this->tableName().".COD_PRIORIDAD = PRIORIDADES.COD_PRIORIDAD
				 AND ".$this->tableName().".CODDEPTO_SOL = DEPARTAMENTOS.COD_DEPTO
				 AND ".$this->tableName().".COD_CIA = DEPARTAMENTOS.COD_CIA";
         return $masx;                
    }

    public function relacione_tablas(){
         $masx= 'PRIORIDADES, DEPARTAMENTOS';
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
   
    
    #Define Un Arreglo con las Axis 'X' y Axis 'Y' para un grafico
    /*
     * Este Metodo debe ser definido segun la necesidad del Grafico
     * */
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
	
	#Correos de Compras
	public function correos_compras(){
		$this->rows=array();
		$this->query = "SELECT   
								LTRIM (RTRIM (B.CORREO_DEPTO)) correo_usuario,
								a.usuario
						FROM   
								FIRMASXTIPO_DOCTO A, CIAS_X_USUARIO B
							WHERE       A.COD_CIA = ".$_SESSION['cod_cia']."
								AND A.PRIORIDAD = 1
								AND A.FIRMA_ACTIVO = 'S'
								AND A.COD_TIPO_DOCTO = 'OCL'           
								AND B.COD_CIA = A.COD_CIA
								AND B.USUARIO = A.USUARIO
								AND A.COD_DEPTO = 55";
		$this->get_results_from_query();
        return $this->rows;
	}
	
	#Devuelve el Correo del Usuario Solicitante de la Requisicion
	public function correo_solicitante(){
		$this->rows=array();
		$this->query = "SELECT   R.NUM_REQ, CXU.CORREO_USUARIO
							FROM      REQUISICION R
										INNER JOIN
											CIAS_X_USUARIO CXU
												ON R.COD_CIA = CXU.COD_CIA 
												AND R.EMP_SOL = CXU.COD_EMP
							WHERE   
								R.NUM_REQ = ".$_REQUEST['NUM_REQ']." 
								AND R.ANIO = ".$_REQUEST['ANIO']." 
								AND R.COD_CIA = ". $_SESSION['cod_cia'];
		$this->get_results_from_query();
        return $this->rows;
	}
	
	#Devuelve un array con las requisiciones del usuario que esta dentro
	public function requisiciones_usuario($emp_sol, $anio , $deptosol){
		$this->rows=array();
		$this->query = "SELECT   RQ.NUM_REQ,
								 PRI.DESCRIPCION_PRIORIDAD,
								 RQ.FECHA_AUTORIZADO,
								 VWE.NOMBRE_ISSS
						FROM         REQUISICION RQ
										INNER JOIN
											PRIORIDADES PRI
												ON RQ.COD_PRIORIDAD = PRI.COD_PRIORIDAD
										INNER JOIN
											VWEMPLEADOS VWE
												ON RQ.COD_CIA = VWE.COD_CIA AND RQ.AUTORIZADO_POR = VWE.COD_EMP
						WHERE       RQ.EMP_SOL = ".$emp_sol."
							AND RQ.ANIO = ".$anio."
							AND RQ.COD_CIA = ".$_SESSION['cod_cia']."
							AND RQ.CODDEPTO_SOL = ".$deptosol."
							AND VWE.STATUS = 'A'";
		$this->get_results_from_query();
        return $this->rows;
	}
	
}
?>
