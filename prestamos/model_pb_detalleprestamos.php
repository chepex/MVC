<?php


session_start();
//error_reporting(E_ALL);
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

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_CUOTA','COD_PRESTAMO','VALOR_CUOTA','TASA_INTERES','VALOR_INTERES','VALOR_AMORTIZACION','SALDO_CAPITAL','FECHA_PAGO','NUMERO_CUOTA');
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


        public function tableName()
    {
        return 'pb_detalleprestamos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_CUOTA');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBDETPRESTAMOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function generar_tablaamortizacion(){
		$saldo_capital=$_REQUEST['MONTO_APROBADO'];
		$tasa_interes=($_REQUEST['TASA_INTERES'])/100;
		$meses_plazo=$_REQUEST['PLAZO'];
		$valor_cuota= round($saldo_capital*(pow((1+$tasa_interes),$meses_plazo) * $tasa_interes)/(pow((1+$tasa_interes),$meses_plazo) - 1),2);
		$fecha = new DateTime(date_format($_REQUEST['FECHA_APERTURA'],'Y-m-d'));
		$html.="<table class='table table-bordered' align='center'>
				<thead>
					<tr>
						<th colspan='7'><center>Tabla de Amortizac&oacute;n Teorica</center></th>
					</tr>
					<tr>
						<th>No. Cuota</th>
						<th>Monto Interes</th>
						<th>Monto Amortizacion</th>
						<th>Saldo Capital</th>
						<th>Valor Couta</th>
						<th>Tasa Interes Mensual</th>
						<th>Fecha Pago</th>
					</tr>
				</thead>
				<tbody>";
		for($i=0;$i<=$meses_plazo;$i++){
			if($i==0){
				$html.="<tr>
							<td>".$i."</td>
							<td>0</td>
							<td>0</td>
							<td>".round($saldo_capital,2)."</td>
							<td>".round($valor_cuota,2)."</td>
							<td>".round($tasa_interes * 100,2)."</td>
							<td>_</td>
						</tr>";
			}elseif($meses_plazo<>$i){
				$html.="<tr>
							<td>".$i."</td>
							<td>".round($monto_interes,2)."</td>
							<td>".round($amortizacion_capital,2)."</td>
							<td>".round($saldo_capital,2)."</td>
							<td>".round($valor_cuota,2)."</td>
							<td>".round($tasa_interes * 100,2)."</td>
							<td>".$fecha->format('d/m/Y')."</td>
						</tr>";
			}else{
				 $html.="<tr>
							<td>".$i."</td>
							<td>".round($monto_interes,2)."</td>
							<td>".round($amortizacion_capital,2)."</td>
							<td>0</td>
							<td>".round($valor_cuota,2)."</td>
							<td>".round($tasa_interes * 100,2)."</td>
							<td>".$fecha->format('d/m/Y')."</td>
						</tr>";
				}
				$acuinterese= $acuinterese + $monto_interes;
				$acuamoritzacion= $acuamoritzacion + $amortizacion_capital ;
				$acusaldocapital= $acusaldocapital + $saldo_capital;
				$fecha_vencimiento=$fecha->format('d/m/Y');
				$fecha = new DateTime(date_format($fecha,'Y-m-d'));
				$fecha->modify('+30 day');
				$monto_interes= $saldo_capital * $tasa_interes;
				$amortizacion_capital= $valor_cuota - $monto_interes;
				$saldo_capital= $saldo_capital - $amortizacion_capital;
		}
		$html.="</tbody>
		<tfoot>
						<tr>
							<th>TOTALES:_</th>
							<th>".round($acuinterese,2)."</th>
							<th>".round($acuamoritzacion,2)."</th>
							<th>".round($acusaldocapital,2)."</th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
				</tfoot>
				</table>";
		 
		 $json_array=array("tblamortizacion"=>$html, "fecha_vencimiento"=>$fecha_vencimiento, "valor_cuota"=>round($valor_cuota,2));
		return json_encode($json_array);
	}
	
	public function Guardar_tablaamortizacion(){
		$saldo_capital=$_REQUEST['MONTO_APROBADO'];
		$tasa_interes=($_REQUEST['TASA_INTERES'])/100;
		$meses_plazo=$_REQUEST['PLAZO'];
		$valor_cuota= round($saldo_capital*(pow((1+$tasa_interes),$meses_plazo) * $tasa_interes)/(pow((1+$tasa_interes),$meses_plazo) - 1),2);
		$fecha = new DateTime(date_format($_REQUEST['FECHA_APERTURA'],'Y-m-d'));
		for($i=0;$i<=$meses_plazo;$i++){
			if($i==0){
				$_REQUEST['COD_CUOTA'] = $this->nextval_seq();
				$_REQUEST['VALOR_COUTA'] = round($valor_cuota,2);
				$_REQUEST['TASA_INTERES'] = round($tasa_interes * 100,2);
				$_REQUEST['VALOR_INTERES'] = 0;
				$_REQUEST['VALOR_AMORTIZACION'] = 0;
				$_REQUEST['SALDO_CAPITAL'] = round($saldo_capital,2);
				$_REQUEST['FECHA_PAGO']='NULL';
				$_REQUEST['NUMERO_CUOTA']=0;
			}elseif($meses_plazo<>$i){
				$_REQUEST['COD_CUOTA'] = $this->nextval_seq();
				$_REQUEST['VALOR_COUTA'] = round($valor_cuota,2);
				$_REQUEST['TASA_INTERES'] = round($tasa_interes * 100,2);
				$_REQUEST['VALOR_INTERES'] = round($monto_interes,2);
				$_REQUEST['VALOR_AMORTIZACION'] = round($amortizacion_capital,2);
				$_REQUEST['SALDO_CAPITAL'] = round($saldo_capital,2);
				$_REQUEST['FECHA_PAGO'] = $fecha->format('d/m/Y');
				$_REQUEST['NUMERO_CUOTA'] = $i;
			}else{
				$_REQUEST['COD_CUOTA'] = $this->nextval_seq();
				$_REQUEST['VALOR_COUTA'] = round($valor_cuota,2);
				$_REQUEST['TASA_INTERES'] = round($tasa_interes * 100,2);
				$_REQUEST['VALOR_INTERES'] = round($monto_interes,2);
				$_REQUEST['VALOR_AMORTIZACION'] = round($amortizacion_capital,2);
				$_REQUEST['SALDO_CAPITAL'] = 0;
				$_REQUEST['FECHA_PAGO'] = $fecha->format('d/m/Y');
				$_REQUEST['NUMERO_CUOTA'] = $i;
			}
			
			$this->save(get_class($this));
				
				$acuinterese= $acuinterese + $monto_interes;
				$acuamoritzacion= $acuamoritzacion + $amortizacion_capital ;
				$acusaldocapital= $acusaldocapital + $saldo_capital;
				$fecha_vencimiento=$fecha->format('d/m/Y');
				$fecha = new DateTime(date_format($fecha,'Y-m-d'));
				$fecha->modify('+30 day');
				$monto_interes= $saldo_capital * $tasa_interes;
				$amortizacion_capital= $valor_cuota - $monto_interes;
				$saldo_capital= $saldo_capital - $amortizacion_capital;
		}
	}
	
	
}
?>
