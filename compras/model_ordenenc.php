<?php


session_start();
error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class ordenenc extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $NUM_ORDEN;
    public $FECHA_ORDEN;
    public $COD_CIA;
    public $CODIGO_GRUPO;
    public $COD_EMP;
    public $SOLICITANTE;
    public $NUM_PEDIDO;
    public $COD_PROV;
    public $FORMA_PAGO;
    public $VIA;
    public $NUM_DIAS;
    public $OBSERVACIONES;
    public $PROYECTO;
    public $STATUS;
    public $AUTORIZADO;
    public $FECHAUTORIZADO;
    public $ATENDIO;
    public $ANULADO;
    public $FECHAANULADO;
    public $NUM_REQ;
    public $ANIO;
    public $CODDEPTO_SOL;
    public $USUARIO;
    public $FECHA_ING;
    public $AUTORIZADA;
    public $COD_CAT;
    public $TIPO_ORDEN;
    public $ZAPATERIA;	
    public $PLANTA;
    

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('NUM_ORDEN','FECHA_ORDEN','COD_CIA','CODIGO_GRUPO','COD_EMP',
					'SOLICITANTE','NUM_PEDIDO','COD_PROV','FORMA_PAGO','VIA','NUM_DIAS',
					'OBSERVACIONES','PROYECTO','FECHA_ING','STATUS','AUTORIZADO',
					'FECHAUTORIZADO','ATENDIO','ANULADO','FECHAANULADO','NUM_REQ','ANIO','CODDEPTO_SOL',
					'USUARIO','FECHA_ING','AUTORIZADA','COD_CAT',
					'TIPO_ORDEN','ZAPATERIA','PLANTA');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
         $masx ="";
         return $masx;                
    }

    public function relacione_tablas(){
         $masx= 'dual';
         return $masx;                
    }


        public function tableName()
    {
        return 'ordenenc';
    }
    
    public function Modulo()
    {
        return 'compras';
    }


    public function llave()
    {
        return array('COD_CIA','NUM_ORDEN');

    }

    public function has_many()
    {
        return array('ID');

    }


    ################################# MÉTODOS ##################################
   
   #Procedimiento que se encarga de Crear la Orden de Compra
   public function generar_ordencompra(){
	#Se Genera el Encabezado a partir de la Tabla de Requisiciones
	  $this->query="INSERT INTO ORDENENC
						(NUM_ORDEN, FECHA_ORDEN, COD_CIA, CODIGO_GRUPO, COD_EMP, 
						SOLICITANTE, NUM_PEDIDO, COD_PROV, 
						FORMA_PAGO, VIA, NUM_DIAS, OBSERVACIONES,
						PROYECTO, ATENDIO, NUM_REQ, 
						ANIO, CODDEPTO_SOL, USUARIO, 
						FECHA_ING, COD_CAT , TIPO_ORDEN) 
					SELECT '".$_REQUEST['NUM_ORDEN']."', '".$_REQUEST['FECHA_ORDEN']."' , COD_CIA, CODIGO_GRUPO, COD_EMP_ELAB, EMP_SOL,
							'".$_REQUEST['NUM_PEDIDO']."','120','".$_REQUEST['FORMA_PAGO']."',
							'".$_REQUEST['VIA']."',".$_REQUEST['NUM_DIAS'].", OBSERVACIONES, 
							PROYECTO, '".$_REQUEST['ATENDIO']."', NUM_REQ, ANIO, 
							CODDEPTO_SOL, USUARIO,SYSDATE, COD_CAT,TIPO_REQ
					FROM REQUISICION WHERE ANIO=". date('Y') . " AND NUM_REQ='".$_REQUEST['NUM_REQ']."' AND COD_CIA=".$_REQUEST['COD_CIA'];
		$this->execute_single_query();
		$COTIZACION = $_REQUEST['ckporden'];
		#Se Genera el Detalle a partir de los detalles de la cotizacion seleccionados
		foreach($COTIZACION as $lis){
			$condicion= implode(' AND ', explode("|",$lis));
			$condicion ."<br/>";
			$this->query="INSERT INTO DETORDEN
									(NUM_ORDEN,COD_CIA,COD_PROD,
									CODIGO_UNIDAD,CANTIDAD,
									PRECIOUNI,VALORREQ)
								SELECT  '".$_REQUEST['NUM_ORDEN']."',CTD.COD_CIA,
									CTD.COD_PROD,
									CTD.CODIGO_UNIDAD,
									CTD.CANTIDAD,
									CTD.PRECIOUNI,
									CTD.VALORREQ
							FROM      COTIZADET CTD
								INNER JOIN
									COTIZACION CT
										ON     CTD.COD_CIA = CT.COD_CIA
												AND CTD.NUM_REQ = CT.NUM_REQ
												AND CTD.ANIO = CT.ANIO
												AND CTD.CORRELATIVO = CT.CORRELATIVO
							WHERE   CTD.NUM_REQ = '".$_REQUEST['NUM_REQ']."' AND ".$condicion;
			$this->execute_single_query();
			$this->query="UPDATE COTIZADET CTD SET CTD.ACEPTADA='S' WHERE CTD.NUM_REQ = '".$_REQUEST['NUM_REQ']."' AND ". $condicion;
			$this->execute_single_query();
			
		}
	}
   
    
    #Define Un Arreglo con las Axis 'X' y Axis 'Y' para un grafico
    /*
     * Este Metodo debe ser definido segun la necesidad del Grafico
     * */
    /*public function get_datagrafico(){
		$this->rows=array();
		$this->query = "  SELECT   CATEGORIAS.COD_CAT,
								   CATEGORIAS.NOM_CAT,
								   NVL (PRESUPUESTOS.VALOR, 0) VAL_PRESUPUESTADO,
								   NVL (PRESUPUESTOS.SALDO, 0) SA
						  WHERE       PRESUPUESTOS.ANIO = ".date('Y')."LDO
						  FROM   PRESUPUESTOS, CATEGORIAS
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
	}*/
	
}
?>
