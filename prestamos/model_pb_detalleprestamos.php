<?php


session_start();
ini_set("display_errors", 0);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_detalleprestamos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_CUOTA; 
    public $COD_PRESTAMO;
    public $VALOR_CUOTA; 
    public $TASA_INTERES;
    public $VALOR_INTERES; 
	public $VALOR_AMORTIZACION;
    public $SALDO_CAPITAL; 
    public $FECHA_PAGO;
    public $NUMERO_CUOTA;
    public $SALDO_CAPITALANT;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_CUOTA','COD_PRESTAMO','VALOR_CUOTA','TASA_INTERES','VALOR_INTERES','VALOR_AMORTIZACION','SALDO_CAPITAL','FECHA_PAGO','NUMERO_CUOTA','SALDO_CAPITALANT');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_CIA = PB_PRESTAMOS.COD_CIA" .
				 $this->tableName().".COD_PRESTAMO = PB_PRESTAMOS.COD_PRESTAMO"; 
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= 'PB_PRESTAMOS';
         return $masx;                
    }

    public function tableName(){
        return 'pb_detalleprestamos';
    }
    
    public function Modulo(){
        return 'prestamos';
    }

    public function llave(){
        return array('COD_CIA','COD_CUOTA');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
   #Recupera el Siguente Correlativo de la Sequencia de Detalle del Prestamo
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBDETPRESTAMOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	#Genera la Tabla de Amortizacion Teorica Para ser comprobada por el Usuario
	public function generar_tablaamortizacion(){
		$tipo_tabla = $_REQUEST['TIPO_TABLA'];
		$saldo_capital=$_REQUEST['MONTO_APROBADO'];
		$tasa_interes=($_REQUEST['TASA_INTERES'])/100;
		$meses_plazo=$_REQUEST['PLAZO'];
		$valor_cuota=round($saldo_capital*(($tasa_interes/12)/(1-(1/pow(1+($tasa_interes/12),$meses_plazo)))),2);
		$fecha_apertura = str_replace('/', '-', $_REQUEST['FECHA_APERTURA']);
		$fecha = new DateTime(date('Y-m-d',strtotime($fecha_apertura)));
		$findesemana=2;//incializacion de variable findesemana
		#0 indica Domingo
		#1 indica Sabado
		#2 indica dia de lunes a viernes
		$html.="<table class='table table-bordered' align='center'>
				<thead>
					<tr>
						<th colspan='6'><center>Tabla de Amortizaci&oacute;n Teorica</center></th>
					</tr>
					<tr>
						<th>No. Cuota</th>
						<th>Monto Interes</th>
						<th>Monto Amortizacion</th>
						<th>Saldo Capital</th>
						<th>Valor Cuota</th>
						<th>Fecha Pago</th>
					</tr>
				</thead>
				<tbody>";
		for($i=0;$i<=$meses_plazo;$i++){
			$valcuota = ($tipo_tabla == 0) ? $valor_cuota :  $monto_interes;
			if($i==0){
				$html.="<tr>
							<td>".$i."</td>
							<td>".number_format(0, 2, '.', ',')."</td>
							<td>".number_format(0, 2, '.', ',')."</td>
							<td>".number_format(round($saldo_capital,2), 2, '.', ',')."</td>
							<td>". number_format(round($valcuota,2), 2, '.', ',') ."</td>
							<td>".$fecha->format('d/m/Y')."</td>
						</tr>";
			}elseif($meses_plazo<>$i){
				$html.="<tr>
							<td>".$i."</td>
							<td>".number_format(round($monto_interes,2), 2, '.', ',')."</td>
							<td>".number_format(round($amortizacion_capital,2), 2, '.', ',')."</td>
							<td>".number_format(round($saldo_capital,2), 2, '.', ',')."</td>
							<td>".number_format(round($valcuota,2), 2, '.', ',') ."</td>
							<td>".$fecha->format('d/m/Y')."</td>
						</tr>";
			}else{
				$valcuota = ($tipo_tabla == 0) ? $valor_cuota :  ($amortizacion_capital + $monto_interes);
				 $html.="<tr>
							<td>".$i."</td>
							<td>".number_format(round($monto_interes,2), 2, '.', ',')."</td>
							<td>".number_format(round($amortizacion_capital,2), 2, '.', ',')."</td>
							<td>".number_format(round($saldo_capital,2), 2, '.', ',')."</td>
							<td>".number_format(round($valcuota,2), 2, '.', ',')."</td>
							<td>".$fecha->format('d/m/Y')."</td>
						</tr>";
			}
			/*Acumulacion de Los Totales de la Tabla*/
			$acuinterese= $acuinterese + $monto_interes;
			$acuamortizacion= $acuamortizacion + $amortizacion_capital ;
			
			/*Calculos Para la Generacion de la Tabla*/
			$fecha_anterior = $fecha->format('d/m/Y');
			if($findesemana==0){
				$fecha->modify('+2 day');
			}elseif($findesemana==1){
				$fecha->modify('+1 day');
			}
			$fecha = new DateTime(date_format($fecha,'Y-m-d'));
			if(date_format($fecha,'m')=="01" or date_format($fecha,'m')=="03" or date_format($fecha,'m')=="05" or date_format($fecha,'m')=="07" or date_format($fecha,'m')=="08" or date_format($fecha,'m')=="10" or date_format($fecha,'m')=="12"){
				$fecha->modify('+31 day');
				$findesemana = $this->es_findesemana($fecha->format('Y-m-d'));
				if($findesemana==0){
					$fecha->modify('-2 day');
				}elseif($findesemana==1){
					$fecha->modify('-1 day');
				}
				
			}else{
				$fecha->modify('+30 day');
				$findesemana = $this->es_findesemana($fecha->format('Y-m-d'));
				if($findesemana==0){
					$fecha->modify('-2 day');
				}elseif($findesemana==1){
					$fecha->modify('-1 day');
				}
			}
	
			//$monto_interes= ($saldo_capital * $tasa_interes * ($this->diferencia_dias($fecha_anterior, $fecha->format('d/m/Y')))) / $this->es_bisiesto($fecha->format('Y'));
			$monto_interes = $this->calculoInteres($saldo_capital, $tasa_interes, $fecha_anterior , $fecha->format('d/m/Y') , $fecha->format('Y'));
			if($tipo_tabla==0){
				if($saldo_capital >= $valor_cuota){
					$amortizacion_capital= $valor_cuota - $monto_interes;
				}else{
					$amortizacion_capital= $saldo_capital;
				}
			}else{
				if(($i+1)==$meses_plazo){
					$amortizacion_capital=$saldo_capital;
				}else{
					$amortizacion_capital=0;
				}
			}
			$saldo_capital= $saldo_capital - $amortizacion_capital;
		}
		$html.="</tbody>
		<tfoot>
						<tr>
							<th>TOTALES:_</th>
							<th>".number_format(round($acuinterese,2), 2, '.', ',')."</th>
							<th>".number_format(round($acuamortizacion,2), 2, '.', ',')."</th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
				</tfoot>
				</table>";
		 $json_array=array("tblamortizacion"=>$html, "fecha_vencimiento"=>$fecha_anterior, "valor_cuota"=>round($valor_cuota,2));
		return json_encode($json_array);
	}
	
	#Guardar la Tabla de Amortizacion una ves examinada por el Usuario, en la tabla pb_detalleprestamos
	public function Guardar_tablaamortizacion(){
		$tipo_tabla = $_REQUEST['TIPO_TABLA'];
		$saldo_capital=$_REQUEST['MONTO_APROBADO'];
		$tasa_interes=($_REQUEST['TASA_INTERES'])/100;
		$meses_plazo=$_REQUEST['PLAZO'];
		$valor_cuota=round($saldo_capital*(($tasa_interes/12)/(1-(1/pow(1+($tasa_interes/12),$meses_plazo)))),2);
		$fecha_apertura = str_replace('/', '-', $_REQUEST['FECHA_APERTURA']);
		$fecha = new DateTime(date('Y-m-d',strtotime($fecha_apertura)));
		$findesemana=2;//incializacion de variable findesemana
		#0 indica Domingo
		#1 indica Sabado
		#2 indica dia de lunes a viernes
		for($i=0;$i<=$meses_plazo;$i++){
			unset($_REQUEST['VALOR_COUTA']);
			$valcuota = ($tipo_tabla == 0) ? $valor_cuota :  $monto_interes;
			if($i==0){
				$_REQUEST['COD_CUOTA'] = $this->nextval_seq();
				$_REQUEST['VALOR_CUOTA'] = round($valcuota,2);
				$_REQUEST['TASA_INTERES'] = round($tasa_interes * 100,2);
				$_REQUEST['VALOR_INTERES'] = 0;
				$_REQUEST['VALOR_AMORTIZACION'] = 0;
				$_REQUEST['SALDO_CAPITAL'] = round($saldo_capital,2);
				$_REQUEST['FECHA_PAGO']= $fecha->format('d/m/Y');
				$_REQUEST['NUMERO_CUOTA']=0;
				$_REQUEST['SALDO_CAPITALANT']=0;
			}elseif($meses_plazo<>$i){
				$_REQUEST['COD_CUOTA'] = $this->nextval_seq();
				$_REQUEST['VALOR_CUOTA'] = round($valcuota,2);
				$_REQUEST['TASA_INTERES'] = round($tasa_interes * 100,2);
				$_REQUEST['VALOR_INTERES'] = round($monto_interes,2);
				$_REQUEST['VALOR_AMORTIZACION'] = round($amortizacion_capital,2);
				$_REQUEST['SALDO_CAPITAL'] = round($saldo_capital,2);
				$_REQUEST['FECHA_PAGO'] = $fecha->format('d/m/Y');
				$_REQUEST['NUMERO_CUOTA'] = $i;
				$_REQUEST['SALDO_CAPITALANT']=$saldo_capitalant;
			}else{
				$valcuota = ($tipo_tabla == 0) ? $valor_cuota :  ($amortizacion_capital + $monto_interes);
				$_REQUEST['COD_CUOTA'] = $this->nextval_seq();
				$_REQUEST['VALOR_CUOTA'] = round($valcuota,2);
				$_REQUEST['TASA_INTERES'] = round($tasa_interes * 100,2);
				$_REQUEST['VALOR_INTERES'] = round($monto_interes,2);
				$_REQUEST['VALOR_AMORTIZACION'] = round($amortizacion_capital,2);
				$_REQUEST['SALDO_CAPITAL'] = 0;
				$_REQUEST['FECHA_PAGO'] = $fecha->format('d/m/Y');
				$_REQUEST['NUMERO_CUOTA'] = $i;
				$_REQUEST['SALDO_CAPITALANT']=$saldo_capitalant;
			}
			
			$this->save(get_class($this));
				
				/*Calculos Para la Generacion de la Tabla*/
				$fecha_anterior = $fecha->format('d/m/Y');
				$saldo_capitalant = $saldo_capital;
				if($findesemana==0){
					$fecha->modify('+2 day');
				}elseif($findesemana==1){
					$fecha->modify('+1 day');
				}

				$fecha = new DateTime(date_format($fecha,'Y-m-d'));
				if(date_format($fecha,'m')=="01" or date_format($fecha,'m')=="03" or date_format($fecha,'m')=="05" or date_format($fecha,'m')=="07" or date_format($fecha,'m')=="08" or date_format($fecha,'m')=="10" or date_format($fecha,'m')=="12"){
					$fecha->modify('+31 day');
					$findesemana = $this->es_findesemana($fecha->format('Y-m-d'));
					if($findesemana==0){
						$fecha->modify('-2 day');
					}elseif($findesemana==1){
						$fecha->modify('-1 day');
					}
					
				}else{
					$fecha->modify('+30 day');
					$findesemana = $this->es_findesemana($fecha->format('Y-m-d'));
					if($findesemana==0){
						$fecha->modify('-2 day');
					}elseif($findesemana==1){
						$fecha->modify('-1 day');
					}
				}
	
				//$monto_interes= ($saldo_capital * $tasa_interes * ($this->diferencia_dias($fecha_anterior, $fecha->format('d/m/Y')))) / $this->es_bisiesto($fecha->format('Y'));
				$monto_interes = $this->calculoInteres($saldo_capital, $tasa_interes, $fecha_anterior , $fecha->format('d/m/Y') , $fecha->format('Y'));
				if($tipo_tabla==0){
					if($saldo_capital >= $valor_cuota){
						$amortizacion_capital= $valor_cuota - $monto_interes;
					}else{
						$amortizacion_capital= $saldo_capital;
					}
				}else{
					if(($i+1)==$meses_plazo){
						$amortizacion_capital=$saldo_capital;
					}else{
						$amortizacion_capital=0;
					}
				}
				$saldo_capital= $saldo_capital - $amortizacion_capital;
		}
	}
	
	#Permite la Actualizacion de la Tasa de Interes para el prestamo
	public function ModificarTasaInteres($COD_PRESTAMO, $COD_CUOTA, $NUEVA_TASA){
		$detprestamos = $this->listar_cuotas($_SESSION['cod_cia'],$COD_PRESTAMO,$COD_CUOTA);
		$fecha = new DateTime(date('Y-m-d',strtotime($detprestamos[0]['FECHA_PAGO'])));
		$saldo_capital = $detprestamos[0]['SALDO_CAPITAL'];
		$i=0;
		foreach ($detprestamos as $mks){
			$i++;
			if($COD_CUOTA != $mks['COD_CUOTA']){
				//$monto_interes= ($saldo_capital * ($NUEVA_TASA/100) * ($this->diferencia_dias($fecha_anterior, $fecha->format('d/m/Y')))) / $this->es_bisiesto($fecha->format('Y'));
				$monto_interes = $this->calculoInteres($saldo_capital, ($NUEVA_TASA/100), $fecha_anterior , $fecha->format('d/m/Y') , $fecha->format('Y'));
				if($mks['TIPO_TABLA'] == 0){
					if($saldo_capital >= $mks['VALOR_CUOTA']){
						$amortizacion_capital= $mks['VALOR_CUOTA'] - $monto_interes;
					}else{
						$amortizacion_capital= $saldo_capital;
					}
				}else{
					if($mks['VALOR_AMORTIZACION'] > 0){
						$amortizacion_capital=$saldo_capital;
					}else{
						$amortizacion_capital=0;
					}
				}
				$saldo_capital= $saldo_capital - $amortizacion_capital;
				
				$this->query="UPDATE pb_prestamos SET
									TASA_INTERES=". $NUEVA_TASA ."
								WHERE COD_PRESTAMO=".$COD_PRESTAMO ."
									AND COD_CIA=". $_SESSION['cod_cia'];
				$this->execute_single_query();
				
				$this->query="UPDATE ".$this->tableName() . " SET
									TASA_INTERES=". $NUEVA_TASA .",
									VALOR_INTERES=". $monto_interes.",
									VALOR_AMORTIZACION=".$amortizacion_capital .",
									SALDO_CAPITAL=". $saldo_capital . "
								WHERE COD_PRESTAMO=".$COD_PRESTAMO ."
									AND COD_CUOTA=".$mks['COD_CUOTA'] . "
									AND COD_CIA=". $_SESSION['cod_cia'];
				$this->execute_single_query();
			}
			$fecha_anterior = str_replace('-', '/', $mks['FECHA_PAGO']);
			$fecha = new DateTime(date('Y-m-d',strtotime($detprestamos[$i]['FECHA_PAGO'])));
			/*if(date_format($fecha,'m')=="01" or date_format($fecha,'m')=="03" or date_format($fecha,'m')=="05" or date_format($fecha,'m')=="07" or date_format($fecha,'m')=="08" or date_format($fecha,'m')=="10" or date_format($fecha,'m')=="12"){
				$fecha->modify('+31 day');
			}else{
				$fecha->modify('+30 day');
			}*/
		}
	}
	
	#Consulta Base para Generar el detalle de las Cuotas Pendientes de Pago
	public function listar_cuotas($FECHA_PAGO_INI, $FECHA_PAGO_FIN){
		 $this->rows = array();
		 $this->query = "SELECT   PB_DETALLEPRESTAMOS.COD_CUOTA,
								  PB_PRESTAMOS.REF_PRESTAMO,
								  BANCOS.NOM_CORTO,
								  PB_LINEASCREDITO.NUM_REFLINEA,
								  PB_TIPOS_CREDITOS.DESCRIPCION_TIPOCREDITO,
								  PB_DETALLEPRESTAMOS.NUMERO_CUOTA,
								  PB_DETALLEPRESTAMOS.VALOR_CUOTA,
								  PB_DETALLEPRESTAMOS.VALOR_CUOTA
								  - NVL (
										SUM (PB_DETALLEPAGO.ABONO_INTERES)
										+ SUM (PB_DETALLEPAGO.ABONO_AMORTIZACION),
											0
										)
										SALDO_CUOTA,
								 PB_DETALLEPRESTAMOS.FECHA_PAGO,
								 PB_DETALLEPRESTAMOS.SALDO_CAPITAlANT,
								 CASE
									WHEN PB_DETALLEPRESTAMOS.VALOR_AMORTIZACION
										- NVL (SUM(PB_DETALLEPAGO.ABONO_AMORTIZACION), 0) <> 0
									THEN
										PB_DETALLEPRESTAMOS.VALOR_AMORTIZACION
										- NVL (SUM (PB_DETALLEPAGO.ABONO_INTERES), 0)
									ELSE
										0
								END VALOR_AMORTIZACION,
								CASE
									WHEN PB_DETALLEPRESTAMOS.VALOR_INTERES
										- NVL (SUM(PB_DETALLEPAGO.ABONO_INTERES), 0) <> 0
									THEN
										PB_DETALLEPRESTAMOS.VALOR_INTERES
										- NVL (SUM (PB_DETALLEPAGO.ABONO_INTERES), 0)
									ELSE
										0
								END	VALOR_INTERES,
								PB_DETALLEPRESTAMOS.SALDO_CAPITAL
						FROM         pb_detalleprestamos
								INNER JOIN
									pb_prestamos
										ON pb_detalleprestamos.cod_cia = pb_prestamos.COD_CIA
											AND PB_DETALLEPRESTAMOS.COD_PRESTAMO =
												pb_prestamos.COD_PRESTAMO
								LEFT OUTER JOIN
									PB_DETALLEPAGO
										ON pb_detalleprestamos.COD_CIA = PB_DETALLEPAGO.COD_CIA
											AND PB_DETALLEPRESTAMOS.COD_CUOTA = PB_DETALLEPAGO.COD_CUOTA
								INNER JOIN
									BANCOS
										ON PB_PRESTAMOS.COD_CIA = BANCOS.COD_CIA
											AND PB_PRESTAMOS.COD_BANCO = BANCOS.COD_BANCO
								INNER JOIN
									PB_LINEASCREDITO
										ON PB_PRESTAMOS.COD_CIA = PB_LINEASCREDITO.COD_CIA
											AND PB_PRESTAMOS.COD_LINEA = PB_LINEASCREDITO.COD_LINEA
								INNER JOIN
									PB_TIPOS_CREDITOS
										ON PB_LINEASCREDITO.COD_CIA = PB_TIPOS_CREDITOS.COD_CIA
											AND PB_LINEASCREDITO.COD_TIPOCREDITO = PB_TIPOS_CREDITOS.COD_TIPOCREDITO
							WHERE   PB_DETALLEPRESTAMOS.NUMERO_CUOTA <> 0 AND PB_DETALLEPRESTAMOS.FECHA_PAGO BETWEEN  '".$FECHA_PAGO_INI."' AND '".$FECHA_PAGO_FIN."'
								GROUP BY   pb_detalleprestamos.cod_prestamo,
											PB_DETALLEPRESTAMOS.COD_CUOTA,
											PB_PRESTAMOS.REF_PRESTAMO,
											PB_DETALLEPRESTAMOS.NUMERO_CUOTA,
											PB_DETALLEPRESTAMOS.VALOR_CUOTA,
											PB_DETALLEPRESTAMOS.FECHA_PAGO,
											PB_DETALLEPRESTAMOS.SALDO_CAPITAlANT,
											PB_DETALLEPRESTAMOS.VALOR_AMORTIZACION,
											PB_DETALLEPRESTAMOS.VALOR_INTERES,
											PB_DETALLEPRESTAMOS.SALDO_CAPITAL,
											BANCOS.NOM_CORTO,
											PB_LINEASCREDITO.NUM_REFLINEA,
											PB_TIPOS_CREDITOS.DESCRIPCION_TIPOCREDITO
							ORDER BY   PB_DETALLEPRESTAMOS.FECHA_PAGO,pb_detalleprestamos.cod_prestamo, pb_detalleprestamos.numero_cuota";
            $this->get_results_from_query();
            return $this->rows ;
    }
    
    #Retorna un arreglo con los saldos de las cuotas a pagar en el rango de fecha seleccionados, por Banco, y Tipo de Credito
	public function resumen_porpagar($FECHA_PAGO_INI, $FECHA_PAGO_FIN){
		 $this->rows = array();
		 $this->query = "SELECT   BANCOS.COD_BANCO,BANCOS.NOM_BANCO,
								  TIPOS_CREDITOS.DESCRIPCION_TIPOCREDITO,
								  SUM (VWSALDOC.SALDO_CUOTA) A_PAGAR
							FROM  VWSALDO_CUOTAS VWSALDOC
								INNER JOIN
									PB_DETALLEPRESTAMOS DETPRES
										ON VWSALDOC.COD_CIA = DETPRES.COD_CIA
											AND VWSALDOC.COD_CUOTA = DETPRES.COD_CUOTA
								INNER JOIN
									PB_PRESTAMOS PRES
									ON DETPRES.COD_CIA = PRES.COD_CIA
										AND DETPRES.COD_PRESTAMO = PRES.COD_PRESTAMO
								INNER JOIN
									PB_LINEASCREDITO LINEA
										ON PRES.COD_CIA = LINEA.COD_CIA
											AND PRES.COD_LINEA = LINEA.COD_LINEA
								INNER JOIN
									PB_TIPOS_CREDITOS TIPOS_CREDITOS
										ON LINEA.COD_CIA = TIPOS_CREDITOS.COD_CIA
											AND LINEA.COD_TIPOCREDITO = TIPOS_CREDITOS.COD_TIPOCREDITO
								INNER JOIN
									BANCOS
										ON PRES.COD_CIA = BANCOS.COD_CIA
											AND PRES.COD_BANCO = BANCOS.COD_BANCO
							WHERE   VWSALDOC.SALDO_CUOTA <> 0
									AND VWSALDOC.FECHA_PAGO BETWEEN '".$FECHA_PAGO_INI."' AND '".$FECHA_PAGO_FIN."'
							GROUP BY   BANCOS.COD_BANCO,BANCOS.NOM_BANCO, TIPOS_CREDITOS.DESCRIPCION_TIPOCREDITO";
            $this->get_results_from_query();
            return $this->rows ;
    }
    
    public function calculoInteres($saldo_capitalx, $tasa_interesx, $fecpagant, $fecnuvpag, $anio){
		$monto_interes = ($saldo_capitalx * ($tasa_interesx) * ($this->diferencia_dias($fecpagant, $fecnuvpag))) / $this->es_bisiesto($anio);
		return $monto_interes;
	}
	
	
}
?>
