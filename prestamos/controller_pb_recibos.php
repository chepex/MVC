<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_recibos.php');
require_once('model_pb_detallerecibos.php');
require_once('model_pb_desglosepago.php');
require_once('model_pb_definicion_ctas_banco.php');
require_once('controller_pb_detallerecibos.php');
require_once('../core/render_view_generic.php');
#Controlador de pb_recibos
class controller_pb_recibos extends pb_recibos{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Recibo',
                      'buscar'=>'Buscar Recibo',
                      'borrar'=>'Eliminar Recibo',
                      'modificar'=>'Modificar Recibo',
                      'listar'=>'Lista de Recibo'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_recibos&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_recibos&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_recibos&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_recibos&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_recibos&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_recibos',
        'DELETE'=>'../prestamos/?ctl=controller_pb_recibos&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_recibos&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_recibos&act=get_all'
		)
	);
	
	protected $msg;
	
	#Manejador de Peticiones en base a accion solicitada
	public function handler($op='') {
		if(empty($op)){
			$event = 'buscar';
			if(isset($_REQUEST['act'])){
				$uri = $_REQUEST['act'];
			}
			else{
				$uri = "get_all";
			}
			#Peticiones definidas para el controlador Requisicion
			$peticiones = array('set', 'get', 'delete', 'edit',
                        'agregar', 'buscar', 'borrar', 
                        'update','get_all','listar','insert','get_ajax','view','view_rpt','finalizar_req');
			foreach ($peticiones as $peticion) {
				if( $uri == $peticion)  {
					$event = $peticion;
				}
			}
		}else{
			$event=$op;
		}
		
		#Selector de Acciones, Llamada de las mismas.
		switch ($event) {
			case 'set':
				$this->set();
				break;
			case 'get':        
				$this->get();
				break;
			case 'delete':
				$this->delete();
				$this->get_all($this->msg);
				break;
			case 'update':
				$this->update();
				break;
			case 'edit':
				$this->edit();
				$this->get_all($this->msg);
				break;
			case 'insert':
				$this->insert();
				break;	
			case 'get_all':
				$this->get_all();
				break;
			case 'get_ajax':
				$this->get_ajax();
				break;
			case 'view':
				$this->view();
				break;
			case 'view_rpt':
				$this->view_rpt();
				break;
		}
	}
	
	#Definicion de una instancia del Modelo del Controlador requisicion encabezado de la requisicion
	public function set_obj() {
		$obj = new pb_recibos();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion del encabezado de la Requisicion
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
	}
	
	#Método que elimina el detalle de la requisicion, sino tiene detalle
	public function delete(){
		$parametros = $this->set_obj();
	}
	
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	public function insert(){
		$parametros = $this->set_obj();
		$desglosepago =  new pb_desglosepago();
		$detrecibo = new controller_pb_detallerecibos();
		$datadesglose = $desglosepago->listar_degloserecibo($_REQUEST['COD_DESGLOSE']);
		$definicionctas = new pb_definicion_ctas_banco();
		foreach($datadesglose as $desglose){
			$_REQUEST['COD_CIA'] = $desglose['COD_CIA'];
			$_REQUEST['COD_RECIBO'] = $this->nextval_seq();
			$_REQUEST['COD_DESGLOSE'] = $desglose['COD_DESGLOSE'];
			$_REQUEST['COD_BANCO'] = $desglose['COD_BANCO'];
			$_REQUEST['TIPO_DOCUMENTO'] = $desglose['TIPO_DOCUMENTO'];
			$_REQUEST['COD_CUENTA'] = $desglose['COD_CUENTA'];
			$_REQUEST['SECUENCIA'] = $desglose['SECUENCIA'];
			$_REQUEST['FECHA_RECIBO'] = $desglose['FECHA_PAGO'];
			$_REQUEST['VALOR_RECIBO'] = $desglose['VALOR_RECIBO'];
			$parametros->save(get_class($parametros));
			$datadetdesglose = array();
			$ctacorriente = $definicionctas->cuenta_corrientebanco($desglose['COD_CIA'], $desglose['COD_BANCO']);
			$_REQUEST['COD_DETRECIBO'] = $detrecibo->nextval_seq();
			//$_REQUEST['COD_CUOTA'] = $detdesglose['COD_CUOTA'];
			$_REQUEST['CTA_1'] = $ctacorriente[0]['CTA_1'];
			$_REQUEST['CTA_2'] = $ctacorriente[0]['CTA_2'];
			$_REQUEST['CTA_3'] = $ctacorriente[0]['CTA_3'];
			$_REQUEST['CTA_4'] = $ctacorriente[0]['CTA_4'];
			$_REQUEST['CTA_5'] = $ctacorriente[0]['CTA_5'];
			$_REQUEST['CONCEPTO'] = "CTA. CORRIENTE ". $desglose['COD_CUENTA'];
			if($ctacorriente[0]['TIPO_APLICACION'] == "C"){
				$_REQUEST['CARGO'] = $desglose['VALOR_RECIBO'];
				$_REQUEST['ABONO'] = 0;
			}elseif($ctacorriente[0]['TIPO_APLICACION'] == "A"){
				$_REQUEST['CARGO'] = 0;
				$_REQUEST['ABONO'] = $desglose['VALOR_RECIBO'];
			}
			$detrecibo->insert();
			$datadetdesglose = $desglosepago->listar_detdegloserecibo($desglose['COD_DESGLOSE'], $desglose['COD_BANCO'], $desglose['TIPO_DOCUMENTO'] , $desglose['COD_CUENTA']);
			foreach($datadetdesglose as $detdesglose){
				if($detdesglose['VALOR_PROVISION'] > 0){
					$obdefinicion = $definicionctas->view_cta($desglose['COD_CIA'], $detdesglose['COD_CUOTA'], 3);
					$_REQUEST['COD_DETRECIBO'] = $detrecibo->nextval_seq();
					$_REQUEST['COD_CUOTA'] = $detdesglose['COD_CUOTA'];
					$_REQUEST['CTA_1'] = $obdefinicion[0]['CTA_1'];
					$_REQUEST['CTA_2'] = $obdefinicion[0]['CTA_2'];
					$_REQUEST['CTA_3'] = $obdefinicion[0]['CTA_3'];
					$_REQUEST['CTA_4'] = $obdefinicion[0]['CTA_4'];
					$_REQUEST['CTA_5'] = $obdefinicion[0]['CTA_5'];
					$_REQUEST['CONCEPTO'] = $obdefinicion[0]['CONCEPTO'];
					if($obdefinicion[0]['TIPO_APLICACION'] == "C"){
						$_REQUEST['CARGO'] = $detdesglose['VALOR_PROVISION'];
						$_REQUEST['ABONO'] = 0;
					}elseif($obdefinicion[0]['TIPO_APLICACION'] == "A"){
						$_REQUEST['CARGO'] = 0;
						$_REQUEST['ABONO'] = $detdesglose['VALOR_PROVISION'];
					}
					$detrecibo->insert();
				}
				if($detdesglose['VALOR_GASTO'] > 0){
					$obdefinicion = $definicionctas->view_cta($desglose['COD_CIA'], $detdesglose['COD_CUOTA'], 4);
					$_REQUEST['COD_DETRECIBO'] = $detrecibo->nextval_seq();
					$_REQUEST['COD_CUOTA'] = $detdesglose['COD_CUOTA'];
					$_REQUEST['CTA_1'] = $obdefinicion[0]['CTA_1'];
					$_REQUEST['CTA_2'] = $obdefinicion[0]['CTA_2'];
					$_REQUEST['CTA_3'] = $obdefinicion[0]['CTA_3'];
					$_REQUEST['CTA_4'] = $obdefinicion[0]['CTA_4'];
					$_REQUEST['CTA_5'] = $obdefinicion[0]['CTA_5'];
					$_REQUEST['CONCEPTO'] = $obdefinicion[0]['CONCEPTO'];
					if($obdefinicion[0]['TIPO_APLICACION'] == "C"){
						$_REQUEST['CARGO'] = $detdesglose['VALOR_GASTO'];
						$_REQUEST['ABONO'] = 0;
					}elseif($obdefinicion[0]['TIPO_APLICACION'] == "A"){
						$_REQUEST['CARGO'] = 0;
						$_REQUEST['ABONO'] = $detdesglose['VALOR_GASTO'];
					}
					$detrecibo->insert();
				}
				if($detdesglose['VALOR_CAPITAL'] > 0){
					$obdefinicion = $definicionctas->view_cta($desglose['COD_CIA'], $detdesglose['COD_CUOTA'], 2);
					$_REQUEST['COD_DETRECIBO'] = $detrecibo->nextval_seq();
					$_REQUEST['COD_CUOTA'] = $detdesglose['COD_CUOTA'];
					$_REQUEST['CTA_1'] = $obdefinicion[0]['CTA_1'];
					$_REQUEST['CTA_2'] = $obdefinicion[0]['CTA_2'];
					$_REQUEST['CTA_3'] = $obdefinicion[0]['CTA_3'];
					$_REQUEST['CTA_4'] = $obdefinicion[0]['CTA_4'];
					$_REQUEST['CTA_5'] = $obdefinicion[0]['CTA_5'];
					$_REQUEST['CONCEPTO'] = $obdefinicion[0]['CONCEPTO'];
					if($obdefinicion[0]['TIPO_APLICACION'] == "C"){
						$_REQUEST['CARGO'] = $detdesglose['VALOR_CAPITAL'];
						$_REQUEST['ABONO'] = 0;
					}elseif($obdefinicion[0]['TIPO_APLICACION'] == "A"){
						$_REQUEST['CARGO'] = 0;
						$_REQUEST['ABONO'] = $detdesglose['VALOR_CAPITAL'];
					}
					$detrecibo->insert();
				}
				
			}
		}
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$_REQUEST["filtro"]="NO";
		$mcampos = array($parametros->tableName().".COD_CIA",$parametros->tableName().".COD_RECIBO",
						 $parametros->tableName().".COD_DESGLOSE","BANCOS.NOM_BANCO",
						 $parametros->tableName().".TIPO_DOCUMENTO",
						 $parametros->tableName().".COD_CUENTA",$parametros->tableName().".SECUENCIA",
						 $parametros->tableName().".FECHA_RECIBO",$parametros->tableName().".VALOR_RECIBO");
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 3, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),"", array("update"=>"style='display:none;'", "delete"=>"style='display:none;'", "set"=>"style='display:none;'"));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html); 
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
	}
	
	public function view(){
		$parametros = $this->set_obj();
		$detrecibo = new pb_detallerecibos();
		$datadetrecibos = $detrecibo->listar_detrecibos($_REQUEST['COD_CIA'],$_REQUEST['COD_RECIBO']);
		$html .="
				<html>
					<head>
							<title>Recibo de Prestamos Bancarion No. ".$datadetrecibos[0]['COD_RECIBO']."</title>
							<link rel='stylesheet' type='text/css' href='../site_media/css/bootstrap/css/bootstrap.css'/>
							<script src='../site_media/js/jquery.js'></script>
							<script src='../site_media/js/jquery.PrintArea.js'></script>
					</head>
					<body>
						<center>
							<a class='btn btn-primary' href='#' id='imprime' style='margin-top:1%;'><i class='icon-print icon-white'></i>&nbsp;Imprimir Recibo</a>
							<a class='btn btn-primary' href='?ctl=".$_REQUEST['ctl']."&act=get_all' style='margin-top:1%;'><i class='icon-th-list icon-white'></i>&nbsp;Regresar Lista Recibos</a>
						</center>
						<div id='pb_recibo' class='PrintArea'>
						<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:10px; width:40%; margin-top:3%;' align='center'>
							<tr>
								<th colspan='4'><center>RECIBO DE PAGO</center></th>
							</tr>
							<tr>
								<th>Recibo No.: ".$datadetrecibos[0]['COD_RECIBO']."</th>
								<th>Banco: ".$datadetrecibos[0]['NOM_BANCO']."</th>
								<th>Tipo Doc.: ".$datadetrecibos[0]['TIPO_DOCUMENTO']."</th>
								<th>Fecha: ".$datadetrecibos[0]['FECHA_RECIBO']."</th>
							</tr>
							<tr>
								<th>Cuenta: ".$datadetrecibos[0]['COD_CUENTA']."</th>
								<th>Chequera: ".$datadetrecibos[0]['SECUENCIA']." </th>
								<th colspan='2'>Valor: ".number_format($datadetrecibos[0]['VALOR_RECIBO'],2,'.',',')."</th>
							</tr>
						</table>";
			$html .="<table class='table table table-bordered' border='0.5px' bordercolor='#585858' style='font-size:10px;' align='center'>
						<thead>
						<tr>
							<th>CTA 1</th>
							<th>CTA 2</th>
							<th>CTA 3</th>
							<th>CTA 4</th>
							<th>CTA 5</th>
							<th>CONCEPTO</th>
							<th>CARGO</th>
							<th>ABONO</th>
						</tr>
						</thead>
						<tbody>";
			$acutotalc = 0;
			$acutotala = 0;
			foreach ($datadetrecibos as $mks){
					$html .= "<tr class='tfl'>
									<td>
										".$mks["CTA_1"]."
									</td>
									<td>
										".$mks["CTA_2"]."
									</td>
									<td>
										".$mks["CTA_3"]."
									</td>
									<td>
										".$mks["CTA_4"]."
									</td>
									<td>
										".$mks["CTA_5"]."
									</td>
									<td>
										".$mks["CONCEPTO"]."
									</td>	
									<td>
										<p style='float:right;'>".number_format($mks["CARGO"],2,'.',',')."</p>
									</td>	
									<td>
										<p style='float:right;'>".number_format($mks["ABONO"],2,'.',',')."</p>
									</td>									
							  </tr>";
							  $acutotalc = $acutotalc + $mks["CARGO"];
							  $acutotala = $acutotala + $mks["ABONO"];
			}
		$html .= "</tbody>
				<tfoot>
					<th colspan='6'><p style='float:right;'>TOTAL</p></th>
					<th><p style='float:right;'>".number_format($acutotalc,2,'.',',')."</p></th>
					<th><p style='float:right;'>".number_format($acutotala,2,'.',',')."</p></th>
				</tfoot>
				</table>";
		$html .="	</div></body>
				</html>
				<script>
					$('#imprime').click(function (){
						$('div.PrintArea').printArea();
	}				);
				</script>";				
		echo $html;
	}
	
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	public function get_ajax(){
		$parametros = $this->set_obj();
	}

}


?>
