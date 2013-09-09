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
					'SOLICITANTE','COD_PROV','FORMA_PAGO','VIA','NUM_DIAS',
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
						SOLICITANTE, COD_PROV, 
						FORMA_PAGO, VIA, NUM_DIAS, OBSERVACIONES,
						PROYECTO, ATENDIO, NUM_REQ, 
						ANIO, CODDEPTO_SOL, USUARIO, 
						FECHA_ING, COD_CAT , TIPO_ORDEN) 
					SELECT '".$_REQUEST['NUM_ORDEN']."', '".$_REQUEST['FECHA_ORDEN']."' , COD_CIA, CODIGO_GRUPO, COD_EMP_ELAB, EMP_SOL,
							'".$_REQUEST['COD_PROV']."','".$_REQUEST['FORMA_PAGO']."',
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
   
    
   #Genera una tabla con el encabezado de Orden de compra para ser generada a pdf
   	public function create_msghtml_header($objordencom){
		$html .="<table class='table table-bordered' border='0.5px'>
						<tr>
							<th colspan='9'>Orde de Compra No.:".$objordencom[0]['NUM_ORDEN']."</th>
						</tr>
						<tr>
							<th>Num<br/>Orden.</th>
							<th>Fecha<br/>Orden.</th>
							<th>Observaciones</th>
							<th>Tipo<br/>Orden.</th>
							<th>Prioridad</th>
							<th>Categoria</th>
							<th>Solicitante</th>
							<th>Departamento</th>
							<th>*</th>
						</tr>";
			foreach ($objordencom as $mks){
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
	
	#Genera una tabla con el encabezado de Orden de compra para ser generada a pdf
   	public function rpt_ordencomprabyfecha($fechainicial, $fechafinal){
		$this->rows=array();
		$this->query = "  SELECT   OEC.NUM_ORDEN,
								   OEC.NUM_PEDIDO,
								   PR.NOMBRE,
								   SUM (DOR.VALORREQ) VALOR,
								   OEC.FECHA_ING
						  FROM         ORDENENC OEC
							INNER JOIN
								PROVEEDORES PR
									ON OEC.COD_CIA = PR.COD_CIA AND OEC.COD_PROV = PR.COD_PROV
							INNER JOIN
								DETORDEN DOR
									ON OEC.COD_CIA = DOR.COD_CIA AND OEC.NUM_ORDEN = DOR.NUM_ORDEN
						  WHERE   OEC.FECHA_ING BETWEEN '".$fechainicial."' AND '".$fechafinal."'
						  GROUP BY   OEC.NUM_ORDEN,
									OEC.NUM_PEDIDO,
									PR.NOMBRE,
									OEC.FECHA_ING
						  ORDER BY OEC.FECHA_ING, OEC.NUM_ORDEN";
        $this->get_results_from_query();
        return $this->rows;
	}
	
	
	
}
?>
