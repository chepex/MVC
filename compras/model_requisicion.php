<?php


session_start();
//error_reporting(E_ALL);
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
	public function correo_encargadodocumento($TIPO_DOCTO){
		$this->rows=array();
		$this->query = "SELECT   DISTINCT 
								 c.correo_depto
						FROM   usua_enc_tipo_docto a
							LEFT JOIN
								tipo_documento b
									ON a.cod_cia = b.cod_cia
									AND a.cod_tipo_docto = b.cod_tipo_docto
							LEFT JOIN
								cias_x_usuario c
									ON a.cod_cia = c.cod_cia AND a.usuario = c.usuario
							LEFT JOIN
								vwempleados d
									ON c.cod_cia = d.cod_cia AND c.cod_emp = d.cod_emp
						WHERE   a.cod_cia = ".$_SESSION['cod_cia']." AND a.cod_tipo_docto = '".$TIPO_DOCTO."'";
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
	
	#Verifica si la Requisicion Tiene Detalle
	public function tiene_detalle($cod_cia, $num_req, $anio){
		$respuesta = true;
		$this->rows=array();
		$this->query = "SELECT   COUNT ( * ) as existe_detalle
							FROM   REQDET
								WHERE   
									COD_CIA = ".$cod_cia." AND NUM_REQ = '".$num_req."' AND ANIO =". $anio;
		$this->get_results_from_query();
		if($this->rows[0]['existe_detalle'] == 0){
			$repuesta = false;
		}
        return $respuesta;
	}
	
	#Genera una tabla con el encabezado requisicion para ser enviada por email
	public function create_msghtml_header($objrequisicion){
		$html .="<table class='table table-bordered' border='0.5px'>
						<tr>
							<th colspan='9'>Requisici&oacute;n No.:".$objrequisicion[0]['NUM_REQ']."</th>
						</tr>
						<tr>
							<th>Num<br/>Req.</th>
							<th>Fecha<br/>Req.</th>
							<th>Observaciones</th>
							<th>Tipo<br/>Req.</th>
							<th>Prioridad</th>
							<th>Categoria</th>
							<th>Solicitante</th>
							<th>Departamento</th>
							<th>*</th>
						</tr>";
			foreach ($objrequisicion as $mks){
					$html .= "<tr class='tfl'>
									<td>".$mks["NUM_REQ"]."</td>
									<td>".$mks["FECHA_ING"]."</td>
									<td>".$mks["OBSERVACIONES"]."</td>
									<td>".$mks["TIPO_REQ"]."</td>
									<td>".$mks["DESCRIPCION_PRIORIDAD"]."</td>
									<td>".$mks["COD_CAT"]."||".$mks["NOM_CAT"]."</td>
									<td>".$mks["COD_EMP"]."||".$mks["NOMBRE_ISSS"]."</td>
									<td>".$mks["COD_DEPTO"]."||".$mks["NOM_DEPTO"]."</td>
									<td>*</td>
							  </tr>";
			}
		$html .= "</table>";	
		return $html;
	}
	
	#Genera una tabla con el Detalle requisicion para ser enviada por email
	public function create_msghtml_details($objdetrequisicion){
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858'>
						<tr>
							<th>COD<br/>PRODUCTO</th>
							<th>NOMBRE</th>
							<th>DESCRIPCION</th>
							<th>CANTIDAD</th>
							<th>ESPECIFICACIONES</th>
						</tr>";
			foreach ($objdetrequisicion as $mks){
					$html .= "<tr class='tfl'>
									<td>".$mks["COD_PROD"]."</td>
									<td>".$mks["NOMBRE"]."</td>
									<td>".$mks["DESCRIPCION"]."</td>
									<td>".$mks["CANTIDAD"]."</td>
									<td>".$mks["ESPECIFICACIONES"]."</td>
							  </tr>";
			}
		$html .= "</table>";	
		return $html;
	}
	
	#Disponibilidad de Presupuesto por Categoria
	 public function disponibleporcategoria(){
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
								AND COD_CAT=".$_REQUEST['COD_CAT']."
						ORDER BY   CATEGORIAS.COD_CAT";
            $this->get_results_from_query();
            return $this->rows;
	}
	
	
}
?>
