<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$saldo_capital=3150;
$tasa_interes=round((20.2623/12)/100,15);
$meses_plazo=6*12;
//$fecha= date_create('2013-09-19');
$valor_cuota= round($saldo_capital*(pow((1+$tasa_interes),$meses_plazo) * $tasa_interes)/(pow((1+$tasa_interes),$meses_plazo) - 1),2);
$fecha = new DateTime('2013-03-01');
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
		  </tr>";
	}else{
		echo "<tr>
			<td>".$i."</td>
			<td>".round($monto_interes,2)."</td>
			<td>".round($amortizacion_capital,2)."</td>
			<td>0</td>
			<td>".round($valor_cuota,2)."</td>
			<td>".round($tasa_interes * 100,2)."</td>
			<td>".$fecha->format('d/m/Y')."</td>
		  </tr>";
	}
	$fecha = new DateTime(date_format($fecha,'Y-m-d'));
	$fecha->modify('+30 day');
	
	
	$monto_interes= $saldo_capital * $tasa_interes;
	$amortizacion_capital= $valor_cuota - $monto_interes;
	$saldo_capital= $saldo_capital - $amortizacion_capital;
}
echo"</table>";
?>
