<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_prestamos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_PRESTAMO; 
    public $REF_PRESTAMO;
    public $COD_BANCO; 
    public $FECHA_APERTURA;
    public $FECHA_VENCIMIENTO; 
	public $PLAZO;
    public $TASA_INTERES; 
    public $MONTO_APROBADO;
    public $VALOR_CUOTA;
    public $VALOR_SEGURO;
    public $COD_ESTADO;
    public $COD_LINEA;
    Public $TIPO_TABLA;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_PRESTAMO','REF_PRESTAMO','COD_BANCO','FECHA_APERTURA','FECHA_VENCIMIENTO','PLAZO','TASA_INTERES','MONTO_APROBADO','VALOR_CUOTA','VALOR_SEGURO','COD_ESTADO','COD_LINEA','TIPO_TABLA');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_ESTADO = PB_ESTADOS.COD_ESTADO 
				 AND ". $this->tableName().".COD_CIA = BANCOS.COD_CIA
				 AND ". $this->tableName().".COD_BANCO = BANCOS.COD_BANCO
				 AND ".$this->tableName().".COD_CIA = PB_LINEASCREDITO.COD_CIA
				 AND ".$this->tableName().".COD_LINEA = PB_LINEASCREDITO.COD_LINEA";
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= 'PB_ESTADOS,BANCOS,PB_LINEASCREDITO';
         return $masx;                
    }

    public function tableName(){
        return 'pb_prestamos';
    }
    
    public function Modulo(){
        return 'prestamos';
    }

    public function llave(){
        return array('COD_CIA','COD_PRESTAMO');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   #Devuelve el Siguente Correlativo de la Secuencia del encabezado del prestamo
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBPRESTAMOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	#Devuelve una lista de Opciones para un select Html, especificamente opction de Prestamos
	public function get_options($COD_BANCO){
		$lstprestamos = $this->get_lsoption($this->tableName(), array("COD_PRESTAMO"=>"","REF_PRESTAMO"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"COD_BANCO"=>"'".$COD_BANCO."'","COD_ESTADO"=>"3"));
		return $lstprestamos;
	}
	
	#Devuelve un array con la tabla de amortizacion teorica del prestamo
	public function lista_tblamorteorica($COD_CIA, $COD_PRESTAMO){
		$this->rows=array();
		$this->query="SELECT   PB_DETALLEPRESTAMOS.NUMERO_CUOTA,
							   PB_PRESTAMOS.REF_PRESTAMO,
							   PB_TIPOS_CREDITOS.DESCRIPCION_TIPOCREDITO,
							   BANCOS.NOM_BANCO,
							   PB_PRESTAMOS.MONTO_APROBADO,
							   PB_PRESTAMOS.PLAZO,
							   PB_PRESTAMOS.FECHA_APERTURA,
							   PB_PRESTAMOS.FECHA_VENCIMIENTO,
							   PB_DETALLEPRESTAMOS.FECHA_PAGO,
							   PB_DETALLEPRESTAMOS.TASA_INTERES,
							   PB_DETALLEPRESTAMOS.VALOR_AMORTIZACION,
							   PB_DETALLEPRESTAMOS.VALOR_INTERES,
							   PB_DETALLEPRESTAMOS.VALOR_CUOTA,
							   PB_DETALLEPRESTAMOS.SALDO_CAPITAL
						FROM   PB_PRESTAMOS
							INNER JOIN
								PB_DETALLEPRESTAMOS
									ON PB_PRESTAMOS.COD_CIA = PB_DETALLEPRESTAMOS.COD_CIA
										AND PB_PRESTAMOS.COD_PRESTAMO = PB_DETALLEPRESTAMOS.COD_PRESTAMO
							INNER JOIN
								PB_LINEASCREDITO
									ON PB_PRESTAMOS.COD_CIA = PB_LINEASCREDITO.COD_CIA
										AND PB_PRESTAMOS.COD_LINEA = PB_LINEASCREDITO.COD_LINEA
							INNER JOIN
								PB_TIPOS_CREDITOS
									ON PB_TIPOS_CREDITOS.COD_CIA = PB_LINEASCREDITO.COD_CIA
										AND PB_TIPOS_CREDITOS.COD_TIPOCREDITO = PB_LINEASCREDITO.COD_TIPOCREDITO
							INNER JOIN
								BANCOS
									ON PB_PRESTAMOS.COD_CIA = BANCOS.COD_CIA
										AND PB_PRESTAMOS.COD_BANCO = BANCOS.COD_BANCO
						WHERE   PB_PRESTAMOS.COD_CIA = ".$COD_CIA." AND PB_PRESTAMOS.COD_PRESTAMO = ".$COD_PRESTAMO."
						ORDER BY   PB_DETALLEPRESTAMOS.NUMERO_CUOTA";
		$this->get_results_from_query();
        return $this->rows;
	}
	
	#Devuelve un array con la tabla de amortizacion real, los pagos realizados
	public function lista_tblamorreal($COD_CIA, $COD_PRESTAMO){
		$this->rows=array();
		$this->query="SELECT   PB_DETALLEPRESTAMOS.NUMERO_CUOTA,
							   PB_PRESTAMOS.REF_PRESTAMO,
							   PB_TIPOS_CREDITOS.DESCRIPCION_TIPOCREDITO,
							   BANCOS.NOM_BANCO,
							   PB_PRESTAMOS.MONTO_APROBADO,
							   PB_PRESTAMOS.PLAZO,
							   PB_PRESTAMOS.FECHA_APERTURA,
							   PB_PRESTAMOS.FECHA_VENCIMIENTO,
							   PB_DETALLEPAGO.FECHA_PAGO,
							   PB_PRESTAMOS.TASA_INTERES,
							   PB_DETALLEPAGO.ABONO_AMORTIZACION,
							   PB_DETALLEPAGO.ABONO_INTERES,
							   PB_PRESTAMOS.VALOR_CUOTA,
							   PB_DETALLEPRESTAMOS.SALDO_CAPITALANT
						FROM   PB_DETALLEPRESTAMOS
							INNER JOIN
								PB_DETALLEPAGO
									ON PB_DETALLEPAGO.COD_CIA = PB_DETALLEPRESTAMOS.COD_CIA
										AND PB_DETALLEPAGO.COD_CUOTA = PB_DETALLEPRESTAMOS.COD_CUOTA
							INNER JOIN
								PB_PRESTAMOS
									ON PB_DETALLEPRESTAMOS.COD_CIA = PB_PRESTAMOS.COD_CIA
										AND PB_DETALLEPRESTAMOS.COD_PRESTAMO =
								PB_PRESTAMOS.COD_PRESTAMO
							INNER JOIN
								PB_LINEASCREDITO
									ON PB_PRESTAMOS.COD_CIA = PB_LINEASCREDITO.COD_CIA
										AND PB_PRESTAMOS.COD_LINEA = PB_LINEASCREDITO.COD_LINEA
							INNER JOIN
								PB_TIPOS_CREDITOS
									ON PB_LINEASCREDITO.COD_CIA = PB_TIPOS_CREDITOS.COD_CIA
										AND PB_LINEASCREDITO.COD_TIPOCREDITO = PB_TIPOS_CREDITOS.COD_TIPOCREDITO
							INNER JOIN
								BANCOS
									ON PB_PRESTAMOS.COD_CIA = BANCOS.COD_CIA
										AND PB_PRESTAMOS.COD_BANCO = BANCOS.COD_BANCO
						WHERE   PB_DETALLEPAGO.COD_CIA = ".$COD_CIA." AND PB_DETALLEPRESTAMOS.COD_PRESTAMO = ".$COD_PRESTAMO."
						ORDER BY PB_DETALLEPAGO.FECHA_PAGO";
		$this->get_results_from_query();
        return $this->rows;
	}
	
	#Devuelve Un Arreglo con los pagos a Realizarse en el periodo de las fechas en el parametro.
	public function proyeccionsaldos($FECHA_INICIAL, $FECHA_FINAL,$BANCO=0){
		if($BANCO != 0){
			$CONDICION=" AND DPRES.FECHA_PAGO BETWEEN '".$FECHA_INICIAL."' AND '".$FECHA_FINAL."'
								AND BAN.COD_BANCO = '".$BANCO ."' ";
		}else{
			$CONDICION=" AND DPRES.FECHA_PAGO BETWEEN '".$FECHA_INICIAL."' AND '".$FECHA_FINAL."' AND  VW_ULPAG.SALDO_CAPACT <> 0 ";
		}
		
		 $this->rows = array();
		 $this->query = " SELECT   TIPCR.DESCRIPCION_TIPOCREDITO,
								   PRES.REF_PRESTAMO,
								   PRES.FECHA_APERTURA,
								   PRES.FECHA_VENCIMIENTO,
								   PRES.PLAZO,
							       PRES.TASA_INTERES,
								   PRES.MONTO_APROBADO,
								   PRES.VALOR_CUOTA,
								   BAN.NOM_BANCO,
								   DPRES.FECHA_PAGO,
								   VW_ULPAG.FECHA_PAGO ULT_PAGO,
								   DPRES.NUMERO_CUOTA,
								   PRES.COD_PRESTAMO,
								   VW_ULPAG.SALDO_CAPACT,
								   BAN.COD_BANCO,
								   TIPCR.COD_TIPOCREDITO,
								   DPRES.VALOR_INTERES
						  FROM  PB_DETALLEPRESTAMOS DPRES
							INNER JOIN
								PB_PRESTAMOS PRES
									ON DPRES.COD_CIA = PRES.COD_CIA
									AND DPRES.COD_PRESTAMO = PRES.COD_PRESTAMO
							INNER JOIN
								BANCOS BAN
									ON PRES.COD_CIA = BAN.COD_CIA
									AND PRES.COD_BANCO = BAN.COD_BANCO
							INNER JOIN
									PB_LINEASCREDITO LNCRE
									ON PRES.COD_CIA = LNCRE.COD_CIA
									AND PRES.COD_LINEA = LNCRE.COD_LINEA
							INNER JOIN
									PB_TIPOS_CREDITOS TIPCR
									ON LNCRE.COD_CIA = TIPCR.COD_CIA
									AND LNCRE.COD_TIPOCREDITO = TIPCR.COD_TIPOCREDITO
							LEFT OUTER JOIN
									VWPB_ULTIMOPAGOPRESTAMOS VW_ULPAG
									ON PRES.COD_CIA = VW_ULPAG.COD_CIA
									AND PRES.COD_PRESTAMO = VW_ULPAG.COD_PRESTAMO
						WHERE   DPRES.COD_CUOTA NOT IN
														(SELECT   DPA.COD_CUOTA
															FROM   PB_DETALLEPAGO DPA
																WHERE   COD_CUOTA = DPRES.COD_CUOTA
																			AND COD_CIA = DPRES.COD_CIA)
									AND (EXTRACT(month FROM DPRES.FECHA_PAGO) < > EXTRACT(month FROM VW_ULPAG.FECHA_PAGO) OR VW_ULPAG.FECHA_PAGO IS NULL)
									AND PRES.FECHA_APERTURA NOT BETWEEN '".$FECHA_INICIAL."' AND '".$FECHA_FINAL."' 
								".$CONDICION."
						ORDER BY   TIPCR.COD_TIPOCREDITO, BAN.COD_BANCO, PRES.REF_PRESTAMO";
            $this->get_results_from_query();
            return $this->rows ;
    }
	
	#Devuelve un Arreglo con los pagos a vencer
	public function pagosxvencer($FECHA_INICIAL, $FECHA_FINAL){
		$this->rows = array();
		 $this->query = "SELECT   REF_PRESTAMO,
									NOM_CORTO,
									DESCRIPCION_TIPOCREDITO,
									NUMERO_CUOTA,
									VALOR_CUOTA,
									VALOR_INTERES,
									VALOR_AMORTIZACION,
									FECHA_PAGO, SALDO_CAPITAL
							FROM   VWSALDO_CUOTAS
								WHERE   FECHA_PAGO between '".$FECHA_INICIAL."' and '".$FECHA_FINAL."'
										AND SALDO_CAPITAL <> 0
							ORDER BY   FECHA_PAGO DESC";
            $this->get_results_from_query();
        return $this->rows ;
	}
}
?>
