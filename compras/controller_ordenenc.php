<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
require_once('model_requisicion.php');
require_once('model_ordenenc.php');
require_once('model_cotizacion.php');
require_once('model_detorden.php');
require_once('controller_detorden.php');
require_once('../core/render_view_generic.php');
require_once('../core/html2pdf/html2pdf.class.php');
#Controlador de Encabezado de Orden de Compra
class controller_ordenenc extends ordenenc{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva Orden de Compra',
                      'buscar'=>'Buscar Orden de Compra',
                      'borrar'=>'Eliminar una Orden de Compra',
                      'modificar'=>'Modificar una Orden de Compra',
                      'listar'=>'Lista de Orden de Compra'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'compras/?ctl=controller_ordenenc&act=set',
						'VIEW_GET_USER'=>'compras/?ctl=controller_ordenenc&act=buscar',
						'VIEW_EDIT_USER'=>'compras/?ctl=controller_ordenenc&act=modificar',
						'VIEW_DELETE_USER'=>'compras/?ctl=controller_ordenenc&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../compras/?ctl=controller_ordenenc&act=insert',
							'GET'=>'../compras/?ctl=controller_ordenenc',
        'DELETE'=>'../compras/?ctl=controller_ordenenc&act=delete',
        'EDIT'=>'../compras/?ctl=controller_ordenenc&act=edit',
        'GET_ALL'=>'../compras/?ctl=controller_ordenenc&act=get_all',
        'VIEW_RPT'=>'../compras/?ctl=controller_ordenenc&act=render_pdf',
        'RPT_ORDENCOMPRA'=>'../compras/?ctl=controller_ordenenc&act=rpt_ordencompra'
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
			#Peticiones definidas para el controlador Cotizacion
			$peticiones = array('set', 'get', 'delete', 'edit',
                        'agregar', 'buscar', 'borrar', 
                        'update','get_all','listar','insert','get_ajax','view','view_rpt','view_cotizacion','render_pdf', 'set_ocompra','rpt_ordencompra');
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
			case 'view_cotizacion':
				$this->view_cotizacion();
				break;
			case 'render_pdf':
				$this->render_pdf();
				break;
			case 'set_ocompra':
				$this->set_rpt_ordencompra();
				break;
			case 'rpt_ordencompra':
				$this->rpt_ordencompra();
				break;
		}
	}
	
	#Definicion de una instancia del Modelo del Controlador ordenenc encabezado de orden de compra
	public function set_obj() {
		$obj = new ordenenc();
		return $obj;
	}
	
	#Método que dibuja el formulario para insercion del encabezado de la Orden de Compra
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$norden=$parametros->get_correl_key('ordenenc',array("COD_CIA=".$_SESSION['cod_cia']),"num_orden");
		$lstproveedor = $parametros->get_lsoption("PROVEEDORES", array("COD_PROV"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lstrequis = $parametros->get_lsoption("REQUISICION", array("NUM_REQ"=>"","OBSERVACIONES"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "ANIO"=>date('Y')));
		$lstcategorias = $parametros->get_lsoption("CATEGORIAS", array("COD_CAT"=>"","NOM_CAT"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lstdptos = $parametros->get_lsoption("DEPARTAMENTOS", array("COD_DEPTO"=>"","NOM_DEPTO"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lstempelado = $parametros->get_lsoption("VWEMPLEADOS", array("COD_EMP"=>"","NOMBRE_ISSS"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "STATUS"=>"'A'"));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html); 
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{NUM_ORDEN}', $norden[0][0] , $obvista->html);
		$obvista->html = str_replace('{lstproveedores}', $lstproveedor, $obvista->html);
		$obvista->html = str_replace('{lstrequisicion}', $lstrequis, $obvista->html);
		$obvista->html = str_replace('{lstcategorias}', $lstcategorias , $obvista->html);
		$obvista->html = str_replace('{lstemp}', $lstempelado , $obvista->html); 
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{lstdepto}', $lstdptos , $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function delete(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método que inserta en el encabezado de la orden de compra, es algunos campos son tomados de su correspondiente Requisicion
	public function insert(){
		$parametros = $this->set_obj();
		$objciau = $parametros->crea_objeto(array("CIAS_X_USUARIO"), '',array("USUARIO='".$_SESSION['usuario']."'"));
		$_REQUEST['COD_EMP']= $objciau[0]['COD_EMP'];
		if(isset($_REQUEST['NUM_REQ']) && isset($_REQUEST['ckporden'])){
					$this->generar_ordencompra();
		}
	}
	
	#Método que dibuja la tabla Crud para el encanbezado de la Orden de Compra
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$mcampos = array('COD_CIA','NUM_ORDEN','FECHA_ORDEN','SOLICITANTE','COD_PROV','OBSERVACIONES','PROYECTO','AUTORIZADO','FECHAUTORIZADO');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis(get_class($parametros), 0, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html); 
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
	}
	
	#Método que Genera Vista de orden de Compra con su encabezado y detalle
	public function view(){
		$parametros = $this->set_obj();
		$detorden = new controller_detorden();
		$obvista = new view_Parametros();
		$mcampos = array('COD_CIA','NUM_REQ','CODDEPTO_SOL', 'NOM_DEPTO','FECHA_ING','FECHA_AUTORIZADO','OBSERVACIONES','PROYECTO','ANIO','COD_CAT','TIPO_REQ','DESCRIPCION_PRIORIDAD');
        $mcampos = array($parametros->tableName().'.COD_CIA',
						 $parametros->tableName().'.NUM_ORDEN',
						 $parametros->tableName().'.FECHA_ORDEN',
						 $parametros->tableName().'.SOLICITANTE',
						 $parametros->tableName().'.COD_PROV',
						 $parametros->tableName().'.OBSERVACIONES',
						 $parametros->tableName().'.PROYECTO',
						 $parametros->tableName().'.AUTORIZADO',
						 $parametros->tableName().'.FECHAUTORIZADO'
						);
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), "1", $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),'',array("set"=>"style='display:none;'"));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
		$detorden->get_all();
		
	}
	
	#Método que dibuja el formulario para parametrizacion de Reporte de Orden de Compra
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$lstorden = $parametros->get_lsoption("ORDENENC", array("NUM_ORDEN"=>"","OBSERVACIONES"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "ANIO"=>date('Y')));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('ordencompras',get_class($parametros)), $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = str_replace('{formulario_details}', " ", $obvista->html); 
		$obvista->html = str_replace('{lstnumorden}', $lstorden, $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->retornar_vista();
	}
	
	#Método que se encarga de Generar tabla con los datos de la orden de compra para su impresion
	public function render_pdf(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		#Encabezado de Orden de Compra
		$objorden = $parametros->crea_objeto(array("ordenenc ore","vwempleados vwe", "PROVEEDORES PR","DEPARTAMENTOS DEPT","CATEGORIAS CAT","GRUPO_INVENTARIO GINV", "vwempleados vwee"),
										   array("ORE.COD_CIA = VWE.COD_CIA","ORE.SOLICITANTE = VWE.COD_EMP",
												 "ORE.COD_CIA = PR.COD_CIA", "ORE.COD_PROV = PR.COD_PROV", 
												 "ORE.COD_CIA = DEPT.COD_CIA","ORE.CODDEPTO_SOL = DEPT.COD_DEPTO", 
												 "ORE.COD_CIA = CAT.COD_CIA", "ORE.COD_CAT = CAT.COD_CAT",
												 "ORE.COD_CIA = GINV.COD_CIA","ORE.CODIGO_GRUPO = GINV.COD_GRUPO",
												 "ORE.COD_CIA = VWEE.COD_CIA", "ORE.COD_EMP = VWEE.COD_EMP"),
										   array(" AND ORE.NUM_ORDEN='".$_REQUEST['NUM_ORDEN']."'", "ORE.COD_CIA=".$_REQUEST['COD_CIA']),
										   array("ORE.NUM_ORDEN","ORE.FECHA_ORDEN","GINV.COD_GRUPO",
												 "GINV.NOM_GRUPO","PR.COD_PROV","PR.NOMBRE","PR.DIRECCION",
												 "PR.TELEFONO","PR.FAX","ORE.ATENDIO","ORE.NUM_REQ",
												 "DECODE (ORE.FORMA_PAGO,'R','CREDITO',
																		'C',
																			'CONTADO')FORMA_PAGO",
												 "ORE.NUM_DIAS DIAS_CREDITO","DECODE (ORE.VIA,'T','TERRESTRE',
																							  'L','LOCAL',
																							  'M','MARITIMA',
																							  'A','AEREA') VIA",
												"VWE.NOMBRE_ISSS SOLICITANTE", "ORE.OBSERVACIONES", "ORE.ATENDIO",
												"ORE.COD_CAT","CAT.NOM_CAT",
												"DECODE (TIPO_ORDEN,'E','EXTERNA',
																	'G','GLOBAL')TIPO_ORDEN",
												"DEPT.COD_DEPTO","DEPT.NOM_DEPTO","VWEE.NOMBRE_ISSS ELABORADO","ORE.AUTORIZADO","PR.CONTEXTO"
												)
											);
		//$table= $parametros->create_msghtml_header($objorden);
		$html ="<!DOCTYPE html>
			<head>
					<link rel='stylesheet' type='text/css' href='../site_media/css/bootstrap/css/bootstrap.css'/>
					<meta charset='ISO-8859-15'>
					<style type='text/css'>
						.tbl {border-collapse:collapse}
						.tfl {border:1px solid black}
					</style>
					<title>Impresion de Orden de Compra</title>
			</head>
			<body>";
		$html .="<div id='contenedor_pg' style='margin-top:57px;margin-left:57px;margin-right:57px;'>";
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:11px;'>";
			foreach ($objorden as $mks){
					$html .= "<tr>
									<td><strong>INDUSTRIAS CARICIA S.A. DE C.V.</strong></td>
									<td><strong>ORDEN DE COMPRA GCO-FOR-002</strong></td>
									<td>No. ".$mks["NUM_ORDEN"]."</td>
							  </tr>";
					$html .= "<tr>
									<td colspan='2'>
													Direcci&oacute;n: Km. 4 1/2 Blvd. del Ejercito Nacional, Soyapango<br/>
													PBX(503)277-1333, FAX(503)227-1304, email: compras@caricia.com<br/>
													Giro de Negocio: Fabrica de Calzado Registro:351-4 NIT:0614-191071-0017
									</td>
									<td>
										<strong>Fecha:</strong> ".$mks["FECHA_ORDEN"]."<br/><strong>".$mks["NOM_GRUPO"]."</strong>
									</td>
							  </tr>";
					$html .= "<tr>
									<td colspan='2'>
										Proveedor: <strong>".$mks["COD_PROV"]."&nbsp; ".$mks["NOMBRE"]."</strong>
									</td>
									<td>
										Tel. ".$mks["TELEFONO"]."<br/>
										Fax. ".$mks["FAX"]."
									</td>
							  </tr>";
					$html .= "<tr>
									<td colspan='3'>
										<strong>TODA MERCADER&Iacute;A SER&Aacute; RECIBIDA CONTRA PRESENTACI&Oacute;N DE ESTA ORDEN DE COMPRA.</strong>
									</td>
							  </tr>";
					$html .= "<tr>
									<td colspan='2'>
										Contacto: <strong>".$mks["CONTEXTO"]."</strong>
									</td>
									<td>
										Requisici&oacute;n: ".$mks["NUM_REQ"]."
									</td>
							  </tr>";
					$html .= "<tr>
									<td colspan='2'>
										Forma de Pago: ".$mks["FORMA_PAGO"]."
									</td>
									<td>
										Num d&iacute;as: ".$mks["DIAS_CREDITO"]."
									</td>
							  </tr>";
					$html .= "<tr>
									<td colspan='2'>
										Atendi&oacute;: ".$mks["ATENDIO"]."
									</td>
									<td>
										Transporte: ".$mks["VIA"]."
									</td>
							  </tr>";
			}
		$html .= "</table></div><!-- Cierre div contenedor_pg -->";
		
		#Detalle de Orden de Compra
		$objdetorden = $parametros->crea_objeto(array("detorden ode","ordenenc oen", "requisicion rq","productos pro","UNIDADES UM"),
										   array("ODE.COD_CIA = OEN.COD_CIA", "ODE.NUM_ORDEN = OEN.NUM_ORDEN",
												 "OEN.COD_CIA = RQ.COD_CIA","OEN.NUM_REQ = RQ.NUM_REQ",
												 "OEN.ANIO = RQ.ANIO","ODE.COD_CIA = PRO.COD_CIA", "ODE.COD_PROD = PRO.COD_PROD","ODE.CODIGO_UNIDAD = UM.CODIGO_UNIDAD"
												),
										   array(" AND ode.num_orden='".$_REQUEST['NUM_ORDEN']."'", "ode.COD_CIA=".$_REQUEST['COD_CIA']),
										   array("trunc(RQ.FECHA_ING) REQUERIDO", "ODE.COD_PROD", 
												 "ODE.CANTIDAD" ,"UM.DESCRIPCION","PRO.NOMBRE", 
												 "ODE.PRECIOUNI", "ODE.VALORREQ")
											);
		$html .="<div id='detcontenedor_pg' style='margin-left:57px;margin-right:57px;'><table class='table table-bordered' border='0.5px' bordercolor='#585858' style='font-size:11px;'>";
		$html.="<thead>
					<tr>
						<th>Requerido</th>
						<th>C&oacute;digo</th>
						<th>Cantidad</th>
						<th>U/M</th>
						<th>Descripci&oacute;n</th>
						<th>Prec.unit.</th>
						<th>TOTAL</th>
					</tr>
				</thead>";
		foreach ($objdetorden as $mks){
					$html.="<tbody><tr>";
						$html.="<td>".$mks['REQUERIDO']."</td>";
						$html.="<td>".$mks['COD_PROD']."</td>";
						$html.="<td>".$mks['CANTIDAD']."</td>";
						$html.="<td>".$mks['DESCRIPCION']."</td>";
						$html.="<td>".$mks['NOMBRE']."</td>";
						$html.="<td><div style='text-align:right'>".number_format($mks['PRECIOUNI'], 2, '.', ',')."</div></td>";
						$html.="<td><div style='text-align:right'>".number_format($mks['VALORREQ'], 2, '.', '')."</div></td>";
					$html.="</tr></tbody>";
					$TOTAL_ORDEN = $TOTAL_ORDEN + $mks['VALORREQ'];
		}
			$html .= "<tfoot>
						<tr>
							<th colspan='6'><div style='text-align:right'>TOTAL</div></th>
							<th><div style='text-align:right'>$".number_format($TOTAL_ORDEN, 2, '.', '')."</div></th>
						</tr>
						<tr>
							<th colspan='7'>Observaciones: ".$objorden[0]["OBSERVACIONES"]."</th>
						</tr>
						<tr>
							<th colspan='7'>Se utilizar&aacute; en Depto: ".$objorden[0]["NOM_DEPTO"]."</th>
						</tr>
						<tr>
							<th colspan='7'>Elaborado por: ".$objorden[0]["ELABORADO"]."</th>
						</tr>
						<tr>
							<th colspan='7'>Autorizado por: ".$objorden[0]["AUTORIZADO"]."</th>
						</tr>
					</tfoot>";
		$html.="</table></div>";
		$html .= "</body></html>";
		//echo $html;
					try{
$html2pdf = new HTML2PDF('P','letter','es',false,'ISO-8859-15',3);
$html2pdf->pdf->SetDisplayMode('fullpage');
$html2pdf->writeHTML($html, isset($_GET['vuehtml']));
$html2pdf->Output('reporte.pdf');
}
catch(HTML2PDF_exception $e) {
echo $e;
exit;
}
	}
	
	#Método que se encarga de dibujar el detalle de la Orden de Compra, para generarla a partir de los articulos seleccionados
	public function view_cotizacion(){
		$objcotizacion = new cotizacion();
		$arrayCotizacion = $objcotizacion->definir_cotizaciones($_REQUEST['NUM_REQ'], date('Y'), $_REQUEST['COD_PROV']);
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:12px;'>
						<tr>
							<th colspan='10'>Selecci&oacute;n de Articulos Requici&oacute;n No.".$arrayCotizacion[0]["NUM_REQ"]."</th>
						</tr>
						<tr>
							<th>Num<br/>Req.</th>
							<th>Correlativo</th>
							<th>Cod.<br/>Prov</th>
							<th>Nombre</th>
							<th>Cod<br/>Prod.</th>
							<th>Descripci&oacute;n</th>
							<th>Cantidad</th>
							<th>Precio U.</th>
							<th>Valor<br/>Req.</th>
							<th>*</th>
						</tr>";
			foreach ($arrayCotizacion as $mks){
					$html .= "<tr class='tfl'>
									<td>".$mks["NUM_REQ"]."</td>
									<td>".$mks["CORRELATIVO"]."</td>
									<td>".$mks["COD_PROV"]."</td>
									<td>".$mks["NOMBRE"]."</td>
									<td>".$mks["COD_PROD"]."</td>
									<td>".$mks["NOMBRE_PROD"]."</td>
									<td>".number_format($mks["CANTIDAD"], 2, '.', '')."</td>
									<td>$".number_format($mks["PRECIOUNI"], 2, '.', '')."</td>
									<td>$".number_format($mks["VALORREQ"], 2, '.', '')."</td>
									<td><input type='checkbox' name='ckporden[]' id='ckporden-".$mks["CORRELATIVO"]."' value='CTD.CORRELATIVO=".$mks["CORRELATIVO"]."|CTD.COD_PROD=".$mks["COD_PROD"]."|CTD.COD_PROV=".$mks["COD_PROV"]."' req='".$mks["NUM_REQ"]."'></td>
							  </tr>";
			}
		$html .= "</table>";
		echo $html;
	}
	
	#Método invocado via ajax para recupera el contacto registrado en el proveedor
	public function get_ajax(){
		$parametros = $this->set_obj();
		if(isset($_REQUEST['COD_CIA']) && isset($_REQUEST['COD_PROV']) ){
			$objprov = $parametros->crea_objeto(array("PROVEEDORES"), "", array("COD_CIA=".$_REQUEST['COD_CIA'], "COD_PROV='".$_REQUEST['COD_PROV']."'"));
			echo $objprov[0]['CONTEXTO'];
		}
	}
	
	#Método que se encarga de Dibujar el formulario para la parametrizacion del reporte orden de compra por fecha
	public function set_rpt_ordencompra(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('ordenfecha',get_class($parametros)), $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = str_replace('{formulario_details}', " ", $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->retornar_vista();
		
	}
	
	#Método que se encarga de Dibujar el Reporte Orden de Compras por fechas
	public function rpt_ordencompra(){
		$parametros = $this->set_obj();
		$objcomp = $parametros->rpt_ordencomprabyfecha($_REQUEST['fechainicial'],$_REQUEST['fechafinal']);
		$html ="<!DOCTYPE html>
			<head>
					<link rel='stylesheet' type='text/css' href='../site_media/css/bootstrap/css/bootstrap.css'/>
					<meta charset='ISO-8859-15'>
					<style type='text/css'>
						.tbl {border-collapse:collapse}
						.tfl {border:1px solid black}
					</style>
					<title>Impresion de Orden de Compra</title>
			</head>
			<body>";
		$html .="<div id='contenedor_pg' style='margin-top:57px;margin-left:57px;margin-right:57px;'><table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:12px;'>
						<tr>
							<th colspan='5'><center>INDUSTRIAS CARICIAS<br/> Ordenes de Compras por Fecha<br/>Desde:".$_REQUEST['fechainicial']." Hasta: ".$_REQUEST['fechafinal']."</center></th>
						</tr>
						<tr>
							<th>Num<br/>Orden.</th>
							<th>Fecha Orden</th>
							<th>Num Pedido</th>
							<th>Proveedor</th>
							<th>Valor</th>
						</tr>";
			foreach ($objcomp as $mks){
					$html .= "<tr class='tfl'>
									<td>".$mks["NUM_ORDEN"]."</td>
									<td>".$mks["FECHA_ING"]."</td>
									<td>".$mks["NUM_PEDIDO"]."</td>
									<td>".$mks["NOMBRE"]."</td>
									<td>".$mks["VALOR"]."</td>
							  </tr>";
			}
		$html .= "</table>";
		$html .="</div></body></html>";
		echo $html;
	}

}


?>
