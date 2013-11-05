<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$saldo_capital=282500;
$tasa_interes=(3.75)/100;
$meses_plazo=3;
//$fecha= date_create('2013-09-19');
//$valor_cuota= $saldo_capital * (pow(1 + $tasa_interes,$meses_plazo) * $tasa_interes / pow(1 + $tasa_interes,$meses_plazo) - 1);
//$valor_cuota = $saldo_capital * ($tasa_interes / 1-(1 / pow(1 + $tasa_interes,$meses_plazo)));
//$valor_cuota =  $saldo_capital * (($tasa_interes * pow(1+$tasa_interes,$meses_plazo))/(pow(1+$tasa_interes,$meses_plazo)-1));
echo"<h3>continental Decreciente ref.99-420-61236</h3>";
echo"Saldo incial: ".$saldo_capital."<br>";
echo"Tasa interes anual: ".$tasa_interes."<br>";
echo"meses plazo: ".$meses_plazo."<br>";
$valor_cuota=round($saldo_capital*(($tasa_interes/12)/(1-(1/pow(1+($tasa_interes/12),$meses_plazo)))),2);
$fecha = new DateTime('2013-09-18');
$tipo_pago=1;
echo"Formula de Intereses:(\$saldo_capital * \$tasa_interes) / (es_bisiesto(\$fecha->format('Y'))) * (diferencia_dias(\$fecha_anterior, \$fecha->format('d/m/Y')))<br/>";
echo"formula de cuota:round(\$saldo_capital*(\$tasa_interes/(1-(1/pow(1+\$tasa_interes,\$meses_plazo)))),2);<br/>";
echo"formula de amorticacion:\$valor_cuota - \$monto_interes<br/>";
echo"<table border='1'>
<tr>
			<th>No. Cuota</th>
			<th>Monto Interes</th>
			<th>Monto Amortizacion</th>
			<th>Saldo Capital</th>
			<th>Valor Couta</th>
			<th>Tasa Interes Mensual</th>
			<th>Fecha Pago</th>
</tr>";
//for($i=0;$i<=$meses_plazo;$i++){
	$i=0;
for($i=0;$i<=$meses_plazo;$i++){
	if($i==0){
		echo "<tr>
			<td>".$i."</td>
			<td>0</td>
			<td>0</td>
			<td>".round($saldo_capital,2)."</td>
			<td>".round($valor_cuota,2)."</td>
			<td>".round($tasa_interes * 100,2)."</td>
			<td>_</td>
		  </tr>";
	}elseif($meses_plazo<>$i){
		echo "<tr>
			<td>".$i."</td>
			<td>".round($monto_interes,2)."</td>
			<td>".round($amortizacion_capital,2)."</td>
			<td>".round($saldo_capital,2)."</td>
			<td>".round($valor_cuota,2)."</td>
			<td>".round($tasa_interes * 100,2)."</td>
			<td>".$fecha->format('d/m/Y')."</td>
			<td>".($saldo_capital ." * ". $tasa_interes ." * ". (diferencia_dias($fecha_anterior, $fecha->format('d/m/Y')))) ."//". es_bisiesto($fecha->format('Y'))."</td>
		  </tr>";
	}else{
		echo "<tr>
			<td>".$i."</td>
			<td>".round($monto_interes,2)."</td>
			<td>".round($amortizacion_capital,2)."</td>
			<td>".$saldo_capital."</td>
			<td>".round($valor_cuota,2)."</td>
			<td>".round($tasa_interes * 100,2)."</td>
			<td>".$fecha->format('d/m/Y')."</td>
		  </tr>";
	}
	$fecha_anterior = $fecha->format('d/m/Y');
	$fecha = new DateTime(date_format($fecha,'Y-m-d'));
	if(date_format($fecha,'m')=="01" or date_format($fecha,'m')=="03" or date_format($fecha,'m')=="05" or date_format($fecha,'m')=="07" or date_format($fecha,'m')=="08" or date_format($fecha,'m')=="10" or date_format($fecha,'m')=="12"){
		$fecha->modify('+31 day');
	}else{
		$fecha->modify('+30 day');
	}
	
	
	
	//$monto_interes= ($saldo_capital * $tasa_interes) / (es_bisiesto($fecha->format('Y'))) * (diferencia_dias($fecha_anterior, $fecha->format('d/m/Y')));
	$monto_interes= ($saldo_capital * $tasa_interes * (diferencia_dias($fecha_anterior, $fecha->format('d/m/Y')))) / es_bisiesto($fecha->format('Y'));
	if($tipo_pago==0){
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

echo"</table>";
function diferencia_dias($fecha1, $fecha2){
	//defino fecha 1
	$explode_fecha1= explode("/",$fecha1);
	$ano1 = $explode_fecha1[2];
	$mes1 = $explode_fecha1[1];
	$dia1 = $explode_fecha1[0];

	//defino fecha 2
	$explode_fecha2= explode("/",$fecha2);
	$ano2 = $explode_fecha2[2];
	$mes2 = $explode_fecha2[1];
	$dia2 = $explode_fecha2[0];

	//calculo timestam de las dos fechas
	$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
	$timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

	//resto a una fecha la otra
	$segundos_diferencia = $timestamp1 - $timestamp2;
	//echo $segundos_diferencia;

	//convierto segundos en días
	$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

	//obtengo el valor absoulto de los días (quito el posible signo negativo)
	$dias_diferencia = abs($dias_diferencia);

	//quito los decimales a los días de diferencia
	$dias_diferencia = floor($dias_diferencia);

	return $dias_diferencia; 
}
function es_bisiesto($anio){
	if (($anio % 4 == 0) && (($anio % 100 != 0) || ($anio % 400 == 0))){
		$dias_anio = 366;
	}else{
		$dias_anio = 365;
	}
	return $dias_anio;
}
?>
