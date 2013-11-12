<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('controller_pb_detalleprestamos.php');
require_once('controller_pb_ncprestamos.php');
require_once('controller_pb_bancos.php');
require_once('model_pb_prestamos.php');
require_once('model_pb_definicion_ctas_banco.php');
require_once('../core/render_view_generic.php');
#Controlador de pb_prestamos
class controller_pb_prestamos extends pb_prestamos{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nuevo Prestamo',
                      'buscar'=>'Buscar Prestamo',
                      'borrar'=>'Eliminar Prestamo',
                      'modificar'=>'Modificar Prestamo',
                      'listar'=>'Lista de Prestamo'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_prestamos&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_prestamos&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_prestamos&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_prestamos&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_prestamos&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_prestamos',
        'DELETE'=>'../prestamos/?ctl=controller_pb_prestamos&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_prestamos&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_prestamos&act=get_all',
        'PROYEC'=>'../prestamos/?ctl=controller_pb_prestamos&act=viewproyeccion'
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
                        'update','get_all','listar','insert','get_ajax','view','view_detprestamo','proyeccionpago','viewproyeccion');
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
				//$this->get_all($this->msg);
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
			case 'view_detprestamo':
				$this->view_detprestamo();
				break;
			case 'proyeccionpago':
				$this->proyeccionpago();
				break;
			case 'viewproyeccion':
				$this->viewproyeccion();
				break;
		}
	}
	
	#Definicion de una instancia del Modelo del Controlador pb_prestamos
	public function set_obj() {
		$obj = new pb_prestamos();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion del pb_prestamos
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$lstestados = $parametros->get_lsoption("pb_estados", array("COD_ESTADO"=>"","DESCRIPCION_ESTADO"=>""), array("COD_ESTADO >" => "2"));
		$lstbancos = $parametros->get_lsoption("bancos", array("COD_BANCO"=>"","NOM_BANCO"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{COD_PRESTAMO}',$this->nextval_seq(), $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{COD_ESTADO}', $lstestados , $obvista->html);
		$obvista->html = str_replace('{COD_BANCO}', $lstbancos , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
		$lstprestamos =  $parametros->get_options($_REQUEST['COD_BANCO']);
		$lstcuentas = $parametros->get_lsoption("chequeras", array("cod_cuenta"=>"","cod_cuenta"=>""),array("COD_CIA"=>$_SESSION['cod_cia'],"COD_BANCO"=>"'".$_REQUEST['COD_BANCO']."'" , "HABILITADA"=>"'A'"));
		$jsonarray = array("lstprestamos"=> $lstprestamos, "lstcuentas"=> $lstcuentas);
		echo json_encode($jsonarray);
	}
	
	#Método que elimina el detalle de la requisicion, sino tiene detalle
	public function delete(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$parametros->delete(get_class($parametros));
		$this->msg=$parametros->mensaje;
	}
	
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$objlinea = $this->crea_objeto(array("pb_lineascredito linc","bancos ban","pb_estados edos","pb_tipos_creditos tipcre"),
										   array("linc.COD_ESTADO = edos.COD_ESTADO","linc.COD_CIA = ban.COD_CIA","linc.COD_BANCO = ban.COD_BANCO","linc.COD_CIA = tipcre.COD_CIA","linc.COD_TIPOCREDITO = tipcre.COD_TIPOCREDITO AND"),
										   array("linc.COD_CIA=".$_REQUEST['COD_CIA'],"linc.COD_LINEA=".$_REQUEST['COD_LINEA'])
										   );
		$lstestados = $parametros->get_lsoption("pb_estados", array("COD_ESTADO"=>"","DESCRIPCION_ESTADO"=>""),array("COD_ESTADO"=>$objlinea[0]['COD_ESTADO']));
		$lstbancos = $parametros->get_lsoption("bancos", array("COD_BANCO"=>"","NOM_BANCO"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"COD_BANCO"=>$objlinea[0]['COD_BANCO']));
		$lsttipocre = $parametros->get_lsoption("pb_tipos_creditos", array("COD_TIPOCREDITO"=>"","DESCRIPCION_TIPOCREDITO"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"COD_TIPOCREDITO"=>$objlinea[0]['COD_TIPOCREDITO']));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['modificar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('modificar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{COD_LINEA}', $objlinea[0]['COD_LINEA'] , $obvista->html);
		$obvista->html = str_replace('{NUM_REFLINEA}', $objlinea[0]['NUM_REFLINEA'] , $obvista->html);
		$obvista->html = str_replace('{COD_TIPOCREDITO}', $lsttipocre , $obvista->html);
		$obvista->html = str_replace('{TECHO_LINEA}', $objlinea[0]['TECHO_LINEA'], $obvista->html);
		$obvista->html = str_replace('{FECHA_APERTURA}', $objlinea[0]['FECHA_APERTURA'], $obvista->html);
		$obvista->html = str_replace('{FECHA_VENCIMIENTO}', $objlinea[0]['FECHA_VENCIMIENTO'], $obvista->html);
		$obvista->html = str_replace('{DESTINO}', $objlinea[0]['DESTINO'], $obvista->html);
		$obvista->html = str_replace('{DESCRIPCION_FORMA_PAGO}', $objlinea[0]['DESCRIPCION_FORMA_PAGO'], $obvista->html);
		$obvista->html = str_replace('{DESCRIPCION_GARANTIAS}', $objlinea[0]['DESCRIPCION_GARANTIAS'], $obvista->html);
		$obvista->html = str_replace('{MOTIVOS_CADUCIDAD}', $objlinea[0]['MOTIVOS_CADUCIDAD'], $obvista->html);
		$obvista->html = str_replace('{COD_ESTADO}', $lstestados , $obvista->html);
		$obvista->html = str_replace('{COD_BANCO}', $lstbancos , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$parametros->update(get_class($parametros));
		$this->msg=$parametros->mensaje; 
	}
	
	public function insert(){
		$parametros = $this->set_obj();
		$detprestamo = new controller_pb_detalleprestamos();
		$parametros->save(get_class($parametros));
		$detprestamo->Guardar_tablaamortizacion();
		$definicionctas = new pb_definicion_ctas_banco();
		$partidaprestamo = new controller_pb_ncprestamos();
		$lineacredito = $this->crea_objeto(
											array("pb_lineascredito", "pb_tipos_creditos"),
											array("pb_lineascredito.cod_cia = pb_tipos_creditos.cod_cia", "pb_lineascredito.COD_TIPOCREDITO = pb_tipos_creditos.COD_TIPOCREDITO AND"),
											array("pb_lineascredito.COD_CIA=".$_REQUEST['COD_CIA'],"pb_lineascredito.COD_LINEA=".$_REQUEST['COD_LINEA'])
											);
		$banco = $this->crea_objeto(array("BANCOS"),
									"",
									array("COD_CIA=".$_REQUEST['COD_CIA'],"COD_BANCO=".$_REQUEST['COD_BANCO'])
									);
		$ctacorriente = $definicionctas->cuenta_corrientebanco($_REQUEST['COD_CIA'], $_REQUEST['COD_BANCO']);
		$_REQUEST['COD_DETNOTA'] = $partidaprestamo->nextval_seq();
		$_REQUEST['CTA_1'] = $ctacorriente[0]['CTA_1'];
		$_REQUEST['CTA_2'] = $ctacorriente[0]['CTA_2'];
		$_REQUEST['CTA_3'] = $ctacorriente[0]['CTA_3'];
		$_REQUEST['CTA_4'] = $ctacorriente[0]['CTA_4'];
		$_REQUEST['CTA_5'] = $ctacorriente[0]['CTA_5'];
		$_REQUEST['CONCEPTO'] = "CTA. CORRIENTE";
		if($ctacorriente[0]['TIPO_APLICACION'] == 'C'){
			$_REQUEST['CARGO'] = $_REQUEST['MONTO_APROBADO'];
			$_REQUEST['ABONO'] = 0;
		}elseif($ctacorriente[0]['TIPO_APLICACION'] == 'A'){
			$_REQUEST['CARGO'] = 0;
			$_REQUEST['ABONO'] = $_REQUEST['MONTO_APROBADO'];
		}
		$partidaprestamo->insert();
				
		if($lineacredito[0]['DESCRIPCION_TIPOCREDITO']=='DEC'){
			$ctadesembolso = $definicionctas->cuenta_desembolso($_REQUEST['COD_CIA'], $_REQUEST['COD_BANCO'], 7);
			$TIPO_CRED='DEC';
		}elseif($lineacredito[0]['DESCRIPCION_TIPOCREDITO']=='ROT'){
			$ctadesembolso = $definicionctas->cuenta_desembolso($_REQUEST['COD_CIA'], $_REQUEST['COD_BANCO'], 6);
			$TIPO_CRED='ROT';
		}elseif($lineacredito[0]['DESCRIPCION_TIPOCREDITO']=='CC'){
			$ctadesembolso = $definicionctas->cuenta_desembolso($_REQUEST['COD_CIA'], $_REQUEST['COD_BANCO'], 8);
			$TIPO_CRED='CC';
		}else{
			$ctadesembolso = $definicionctas->cuenta_desembolso($_REQUEST['COD_CIA'], $_REQUEST['COD_BANCO'], 6);
		}
		
		$_REQUEST['COD_DETNOTA'] = $partidaprestamo->nextval_seq();
		$_REQUEST['CTA_1'] = $ctadesembolso[0]['CTA_1'];
		$_REQUEST['CTA_2'] = $ctadesembolso[0]['CTA_2'];
		$_REQUEST['CTA_3'] = $ctadesembolso[0]['CTA_3'];
		$_REQUEST['CTA_4'] = $ctadesembolso[0]['CTA_4'];
		$_REQUEST['CTA_5'] = $ctadesembolso[0]['CTA_5'];
		$_REQUEST['CONCEPTO'] = "DESEMB. REF. ".$_REQUEST['REF_PRESTAMO']. " ". $banco[0]['NOM_CORTO'] . " " . $TIPO_CRED;
		if($ctadesembolso[0]['TIPO_APLICACION'] == 'C'){
			$_REQUEST['CARGO'] = $_REQUEST['MONTO_APROBADO'];
			$_REQUEST['ABONO'] = 0;
		}elseif($ctadesembolso[0]['TIPO_APLICACION'] == 'A'){
			$_REQUEST['CARGO'] = 0;
			$_REQUEST['ABONO'] = $_REQUEST['MONTO_APROBADO'];
		}
		$partidaprestamo->insert();
	
		
		echo$this->msg="<h4>Se ha Completado la Transaccion!</h4>";
		
		
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$_REQUEST["filtro"]="NO";
		$mcampos = array($parametros->tableName().".COD_CIA",$parametros->tableName().".COD_PRESTAMO",
						 $parametros->tableName().".REF_PRESTAMO","BANCOS.NOM_BANCO",
						 $parametros->tableName().".FECHA_APERTURA",$parametros->tableName().".FECHA_VENCIMIENTO",
						 $parametros->tableName().".PLAZO",$parametros->tableName().".TASA_INTERES",
						 $parametros->tableName().".MONTO_APROBADO",$parametros->tableName().".VALOR_CUOTA",
						 "PB_ESTADOS.DESCRIPCION_ESTADO","PB_LINEASCREDITO.NUM_REFLINEA");
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 3, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),"",array("delete"=>"style='display:none;'","update"=>"style='display:none;'"));
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
		$obvista = new view_Parametros();
		$datatable = $parametros->lista_tblamorteorica($_REQUEST['COD_CIA'], $_REQUEST['COD_PRESTAMO']);
		$dataamorreal = $parametros->lista_tblamorreal($_REQUEST['COD_CIA'], $_REQUEST['COD_PRESTAMO']);
		$html .="
				<html>
					<head>
							<title>Tablas de Amortizaci&oacute;n Prestamo: ".$datadetrecibos[0]['REF_PRESTAMO']."</title>
							<link rel='stylesheet' type='text/css' href='../site_media/css/bootstrap/css/bootstrap.css'/>
							<script src='../site_media/js/jquery.js'></script>
							<script src='../site_media/js/jquery.PrintArea.js'></script>
					</head>
					<body>
						<center>
							<a class='btn btn-primary' href='?ctl=".$_REQUEST['ctl']."&act=get_all' style='margin-top:1%;'><i class='icon-th-list icon-white'></i>&nbsp;Regresar Lista Prestamos</a>
							<a class='btn btn-primary' id='btpartida' href='#partidadesembolso' style='margin-top:1%;'><i class='icon-eye-open icon-white'></i>&nbsp;Ver Partida de Desembolso</a>
						</center>
						<div id='pb_tbamortizacion' class='PrintArea'>
						<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:10px;margin-top:3%;' align='center'>
							<thead>
								<tr>
									<th colspan='14'><center><h4>Tabla de Amortizaci&oacute;n Te&oacute;rica</h4></center></th>
								</tr>
								<tr>
									<th>Num. Cuota</th>
									<th>Referencia</th>
									<th>Cr&eacute;dito</th>
									<th>Bco.</th>
									<th>Monto</th>
									<th>Plazo</th>
									<th>Apertura</th>
									<th>F.Vcto.</th>
									<th>F.Pago</th>
									<th>%</th>
									<th>Abono K</th>
									<th>Interes N</th>
									<th>Cuota</th>
									<th>Saldo Capital</th>
								</tr>
							</thead><tbody>";
						foreach ($datatable as $mks){
							$html .= "<tr class='tfl'>
										<td>
											".$mks["NUMERO_CUOTA"]."
										</td>
										 <td>
											".$mks["REF_PRESTAMO"]."
										</td>
										<td>
											".$mks["DESCRIPCION_TIPOCREDITO"]."
										</td>
										<td>
											".$mks["NOM_BANCO"]."
										</td>
										<td>
											".number_format($mks["MONTO_APROBADO"],2,'.',',')."
										</td>
										<td>
											".$mks["PLAZO"]."
										</td>
										<td>
											".$mks["FECHA_APERTURA"]."
										</td>	
										 <td>
											".$mks["FECHA_VENCIMIENTO"]."
										</td>
										<td>
											".$mks["FECHA_PAGO"]."
										</td>
										<td>
											".$mks["TASA_INTERES"]."
										</td>
										<td>
											".number_format($mks["VALOR_AMORTIZACION"],2,'.',',')."
										</td>
										<td>
											".number_format($mks["VALOR_INTERES"],2,'.',',')."
										</td>
										<td>
											".number_format($mks["VALOR_CUOTA"],2,'.',',')."
										</td>		
										<td>
											".number_format($mks["SALDO_CAPITAL"],2,'.',',')."
										</td>								
									</tr>";
			
					}	
				$html.="</tbody></table>";
		$html .="	</div>
					<br/>";
		$html .= "<div id='pb_tbamortizacionreal' class='PrintArea'>
						<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:10px;margin-top:3%;' align='center'>
							<thead>
								<tr>
									<th colspan='14'><center><h4>Tabla de Amortizaci&oacute;n Real</h4></center></th>
								</tr>
								<tr>
									<th>Num. Cuota</th>
									<th>Referencia</th>
									<th>Cr&eacute;dito</th>
									<th>Bco.</th>
									<th>Monto</th>
									<th>Plazo</th>
									<th>Apertura</th>
									<th>F.Vcto.</th>
									<th>F.Pago</th>
									<th>%</th>
									<th>Abono K</th>
									<th>Interes N</th>
									<th>Cuota</th>
									<th>Saldo Capital</th>
								</tr>
							</thead><tbody>";
					$acuamortizacion = 0;
					foreach ($dataamorreal as $mks){
							$acuamortizacion = $acuamortizacion + $mks["ABONO_AMORTIZACION"];
							$saldo_capital = $mks["SALDO_CAPITALANT"] - $acuamortizacion;
							$html .= "<tr class='tfl'>
										<td>
											".$mks["NUMERO_CUOTA"]."
										</td>
										 <td>
											".$mks["REF_PRESTAMO"]."
										</td>
										<td>
											".$mks["DESCRIPCION_TIPOCREDITO"]."
										</td>
										<td>
											".$mks["NOM_BANCO"]."
										</td>
										<td>
											".number_format($mks["MONTO_APROBADO"],2,'.',',')."
										</td>
										<td>
											".$mks["PLAZO"]."
										</td>
										<td>
											".$mks["FECHA_APERTURA"]."
										</td>	
										 <td>
											".$mks["FECHA_VENCIMIENTO"]."
										</td>
										<td>
											".$mks["FECHA_PAGO"]."
										</td>
										<td>
											".$mks["TASA_INTERES"]."
										</td>
										<td>
											".number_format($mks["ABONO_AMORTIZACION"],2,'.',',')."
										</td>
										<td>
											".number_format($mks["ABONO_INTERES"],2,'.',',')."
										</td>
										<td>
											".number_format($mks["VALOR_CUOTA"],2,'.',',')."
										</td>		
										<td>
											".number_format($saldo_capital,2,'.',',')."
										</td>								
									</tr>";
			
					}	
				$html.="</tbody></table>";
		$html .="	</div>
					<br/><br/><br/>";
					
					
			$html.="<input type='hidden' id='COD_CIA' name='COD_CIA' value='".$_REQUEST['COD_CIA']."' />
					<input type='hidden' id='COD_PRESTAMO' name='COD_PRESTAMO' value='".$_REQUEST['COD_PRESTAMO']."' />
					<div id='partidadesembolso'></div>
					</body>
				</html>
				<script>
					$('#imprime').click(function (){
						$('div.PrintArea').printArea();
					});
					
					$('#btpartida').click(function (){
						$.ajax({
							url : 'index.php?ctl=controller_pb_ncprestamos&COD_CIA='+ $('#COD_CIA').val() + '&COD_PRESTAMO=' + $('#COD_PRESTAMO').val(),     
							data : { act : 'view' },
							type : 'POST',     
							success : 
								function(resp) {
									var mivar = $.parseJSON(resp);
									$('#partidadesembolso').empty();
									$('#partidadesembolso').fadeOut('slow').fadeIn('slow').html(mivar.tblpartidadese);
								},
							error : 
								function(xhr, status) {
									alert('Disculpe, existió un problema');
								},     
							complete : function(xhr, status) {	
							
							}
						});
					});
				</script>";				
		echo $html;
	}
	
	public function view_detprestamo(){
		$parametros = $this->set_obj();
		$bancos = new controller_pb_bancos();
		$objdetprestamo = new pb_detalleprestamos();
		$arraydetprestamo = $objdetprestamo->listar_cuotas($_REQUEST['FECHA_PAGO_FILTROI'], $_REQUEST['FECHA_PAGO_FILTROF'] );
		$arrayresumenpago = $objdetprestamo->resumen_porpagar($_REQUEST['FECHA_PAGO_FILTROI'], $_REQUEST['FECHA_PAGO_FILTROF']);
		$lstbancos= $bancos->get();
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:8.5px;width:50%;'>
						<tr>
							<th colspan='18'>Compromisos de Pago Bancarios</th>
						</tr>";
			$i=0;
			foreach ($arraydetprestamo as $mks){
				$i++;
				if($mks["SALDO_CUOTA"] > 0){
					if($i%2==0){
						$clasetr="success";

					}else{
						$clasetr="info";	
					}
					$html .= "
					<tr class='warning'>
							<th>*</th>
							<th>Ref. Prestamo</th>
							<th>BCO.</th>
							<th>Ref. Linea</th>
							<th>Tipo Cred.</th>
							<th>No. Cuota</th>
							<th>Fecha Pago</th>
							<th>Saldo Capital Actual</th>
							<th>Amortizacion Cuota</th>
							<th>Interes Cuota</th>
							<th>Sal.Cap. Despues de Pago</th>
							<th>Valor Cuota</th>
							<th>Saldo Cuota</th>
							<th>Abono Cuota</th>
							<th>Pago a traves de Banco:</th>
							<th>Valor Disponible en Banco</th>
							<th>Pen.xCubrir</th>
					</tr>
					<tr class='".$clasetr."'>
									<td>
										<input type='checkbox' class='ckcuota' name='COD_CUOTA_".$i."' value='".$mks["COD_CUOTA"]."' NUMID='".$i."'/>
									</td>
									<td>".$mks["REF_PRESTAMO"]."</td>
									<td>".$mks["NOM_CORTO"]."</td>
									<td>".$mks["NUM_REFLINEA"]."</td>
									<td>".$mks["DESCRIPCION_TIPOCREDITO"]."</td>
									<td>".$mks["NUMERO_CUOTA"]."</td>
									<td>".$mks["FECHA_PAGO"]."</td>
									<td>".number_format($mks["SALDO_CAPITALANT"],2,'.',',')."</td>
									<td>".number_format($mks["VALOR_AMORTIZACION"],2,'.',',')."</td>
									<td>".number_format($mks["VALOR_INTERES"],2,'.',',')."</td>
									<td>".number_format($mks["SALDO_CAPITAL"],2,'.',',')."</td>
									<td>".number_format($mks["VALOR_CUOTA"],2,'.',',')."</td>
									<td>".number_format($mks["SALDO_CUOTA"],2,'.',',')."</td>
									<td>
										<input type='text' class='input-small' required='' id='ABONO_CUOTA_".$i."' name='ABONO_CUOTA_".$i."' value='".number_format($mks["SALDO_CUOTA"],2,'.','')."'  NUMID='".$i."' readonly='readonly'/>
									</td>
									<td>
										<select class='BANCOX chzn-select input-small' id='BANCO_".$i."' name='BANCO_".$i."' NUMID='".$i."'>
											<option>Banco..</option>
											".$lstbancos."
										</select>
										<select class='CUENTAX chzn-select input-small' id='CUENTAX".$i."' name='CUENTAX_".$i."' NUMID='".$i."'>
											<option value='0'>Cuenta..</option>
										</select>
										<select class='PAGOX chzn-select input-small' id='PAGOX_".$i."' name='PAGOX_".$i."' NUMID='".$i."'>
											<option value='0'>Forma Pago</option>
											<option value='NC'>NC-NOTA DE CARGO</option>
											<option value='CH'>CH-CHEQUE</option>
										</select>
										<select class='chzn-select input-small' id='CHEQUERAX_".$i."' name='CHEQUERAX_".$i."' NUMID='".$i."'>
											<option value='0'>Chequera..</option>
										</select>
									</td>
									<td>
										<input type='text' class='input-small' required='' id='DISPONBAN_".$i."' name='DISPONBAN_".$i."' value='0'  />
										<button id='btn_addx_".$i."' class='addx btn btn-primary' title='Agregar Monto' NUMID='".$i."' COD_CUOTA='".$mks["COD_CUOTA"]."' disabled='disabled'><i class='icon-plus-sign icon-white'></i></button>
									</td>
									<td>
										<input type='text' class='input-small' required='' id='XCUBRIR_".$i."' name='XCUBRIR_".$i."' NUMID='".$i."' value='".number_format($mks["SALDO_CUOTA"],2,'.','')."' disabled='disabled' />
									</td>
							  </tr>
							  <tr class='tfl'>
								<td colspan='15'>
										<div id='DESGLOSE_".$mks["COD_CUOTA"]."' NUMID='".$i."' class='DESGLOSEX'></div>
								</td>
							  </tr>";
				}
			}
		$html .= "</table><script>$('.chzn-select').chosen();</script>";
		$html2.="<table class='table table-hover tbl' border='0.5px' bordercolor='#585858' style='font-size:10px;width:100%;'>
						<thead>
						<tr>
							<th colspan='3'>
								<h6>Resumen Acumulado a Pagar<br/>
									Con Fecha de Pago Comprendida entre: ".$_REQUEST['FECHA_PAGO_FILTROI']."
									y el: ".$_REQUEST['FECHA_PAGO_FILTROF']."</h6>
							</th>
						</tr>
						<tr>
							<th>Banco</th>
							<th>Tipo Cred.</th>
							<th>A Pagar</th>
						</tr>
						</thead><tbody>";
			$i=0;
			$acuresumen=0;
			foreach($arrayresumenpago as $resumenpago){
				if($i%2==0){
					$clasetr="success";

				}else{
					$clasetr="info";	
				}

				$html2 .= "
							<tr class='".$clasetr."'>
								<td>".$resumenpago["NOM_BANCO"]."</td>
								<td>".$resumenpago["DESCRIPCION_TIPOCREDITO"]."</td>
								<td>".number_format($resumenpago["A_PAGAR"],2,'.',',')."</td>
							</tr>";
							$i++;
							$acuresumen = $acuresumen + $resumenpago["A_PAGAR"];
			}
			$html2 .= "</tbody><tfoot><tr><th>&nbsp;</th><th>TOTAL A PAGAR:</th><th>".number_format($acuresumen,2,'.',',')."</th></tr></tfoot></table>";
		$data= array("tblcuotas"=>$html,"tblresumen"=>$html2);
		echo json_encode($data);
	}
	
	public function proyeccionpago(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$lstbancos = $parametros->get_lsoption("bancos", array("COD_BANCO"=>"","NOM_BANCO"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('proyeccion',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{COD_BANCO}', $lstbancos , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	public function viewproyeccion(){
		$parametros = $this->set_obj();
		$objproyeccion= $parametros->proyeccionsaldos($_REQUEST['FECHA_INI'],$_REQUEST['FECHA_FIN'],$_REQUEST['COD_BANCO']);	
		$html ="<!DOCTYPE html>
			<head>
					<link rel='stylesheet' type='text/css' href='../site_media/css/bootstrap/css/bootstrap.css'/>
					<meta charset='ISO-8859-15'>
					<style type='text/css'>
						.tbl {border-collapse:collapse}
						.tfl {border:1px solid black}
					</style>
					<title>Saldos Bancarios Por Pagar</title>
			</head>
			<body>";
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:12px;'>
						<tr>
							<th colspan='11'><center><h4>INDUSTRIAS CARICIA, S.A. DE C.V. <br/>SALDOS BANCARIOS POR PAGAR AL: ".$_REQUEST['FECHA_FIN']."</h4></center></th>
						</tr>
						<tr>
							<th>Referencia</th>
							<th>Fecha Ape.</th>
							<th>Fecha Vcto.</th>
							<th>Meses Plazo</th>
							<th>Tasa Int %</th>
							<th>Monto Aprobado</th>
							<th>Cuota</th>
							<th>Prox. Pago</th>
							<th>Ult. Pago</th>
							<th>Int. Normal</th>
							<th>Saldo. Act.</th>
						</tr>
						<tr>
							<td colspan='11'>
								<h6>".$objproyeccion[0]['DESCRIPCION_TIPOCREDITO']."</h6>
							</td>
						</tr>
						<tr>
							<td colspan='11'>
								<h6>".$objproyeccion[0]['NOM_BANCO']."</h6>
							</td>
						</tr>";
			$BANCO = $objproyeccion[0]['COD_BANCO'];
			$TIPOCREDITO = $objproyeccion[0]['COD_TIPOCREDITO'];
			foreach ($objproyeccion as $mks){
				if($TIPOCREDITO != $mks["COD_TIPOCREDITO"]){
						$html.="<tr>
									<td colspan='11'>
										<h6>".$mks['DESCRIPCION_TIPOCREDITO']."</h6>
									</td>
								</tr>";
					}
					if($BANCO != $mks["COD_BANCO"]){
						$html.="<tr>
									<td colspan='11'>
										<h6>".$mks['NOM_BANCO']."</h6>
									</td>
								</tr>";
					}
					$html .= "<tr class='tfl'>
									<td>".$mks["REF_PRESTAMO"]."</td>
									<td>".$mks["FECHA_APERTURA"]."</td>
									<td>".$mks["FECHA_VENCIMIENTO"]."</td>
									<td>".$mks["PLAZO"]."</td>
									<td>".number_format($mks["TASA_INTERES"],2,'.',',')."</td>
									<td>".number_format($mks["MONTO_APROBADO"],2,'.',',')."</td>
									<td>".number_format($mks["VALOR_CUOTA"],2,'.',',')."</td>
									<td>".$mks["FECHA_PAGO"]."</td>
									<td>".$mks["ULT_PAGO"]."</td>
									<td>".number_format($mks["VALOR_INTERES"],2,'.',',')."</td>
									<td>". ((number_format($mks["SALDO_CAPACT"],2,'.',',') <= 0) ? number_format($mks["MONTO_APROBADO"],2,'.',',') : number_format($mks["SALDO_CAPACT"],2,'.',','))."</td>
							  </tr>";
					$BANCO = $mks["COD_BANCO"];
					$TIPOCREDITO = $mks["COD_TIPOCREDITO"];
			}
		$html .= "</table></body></html>";
		echo $html;
	}
	
	public function get_ajax(){
		$parametros = $this->set_obj();
		$detprestamo = new controller_pb_detalleprestamos();
		echo$detprestamo->generar_tablaamortizacion();
	}

}


?>
