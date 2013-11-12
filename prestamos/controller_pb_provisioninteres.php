<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_provisioninteres.php');
require_once('model_pb_detalleprestamos.php');
require_once('../core/render_view_generic.php');
#Controlador de pb_provisioninteres
class controller_pb_provisioninteres extends pb_provisioninteres{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Provision de Interes',
                      'buscar'=>'Buscar Provision de Interes',
                      'borrar'=>'Eliminar Provision de Interes',
                      'modificar'=>'Modificar Provision de Interes',
                      'listar'=>'Lista Provision de Interes'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_provisioninteres&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_provisioninteres&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_provisioninteres&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_provisioninteres&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_provisioninteres&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_provisioninteres',
        'DELETE'=>'../prestamos/?ctl=controller_pb_provisioninteres&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_provisioninteres&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_provisioninteres&act=get_all'
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
				$this->get_all($this->msg);
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
	
	#Definicion de una instancia del Modelo del Controlador pb_provisioninteres
	public function set_obj() {
		$obj = new pb_provisioninteres();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion del pb_provisioninteres
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{ANIO_PROVISION}', date('Y') , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
	}
	
	#Método que elimina el detalle de la requisicion, sino tiene detalle
	public function delete(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*$parametros->delete(get_class($parametros));
		$this->msg=$parametros->mensaje;
		$this->view();*/
	}
	
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*$objlinea = $this->crea_objeto(array("pb_provisioninteres linc","bancos ban","pb_estados edos","pb_tipos_creditos tipcre"),
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
		$obvista->retornar_vista();*/
	}
	
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*$parametros->update(get_class($parametros));
		$this->msg=$parametros->mensaje; */
	}
	
	public function insert(){
		$parametros = $this->set_obj();
		$detalleprestamo =  new pb_detalleprestamos();
		$cuotasProvisionar = $parametros->listar_paraprovision($_REQUEST['COD_CIA'], $_REQUEST['MES_PROVISION'], $_REQUEST['ANIO_PROVISION']);
		foreach($cuotasProvisionar as $cuota){
			$_REQUEST['COD_PRESTAMO'] = $cuota['COD_PRESTAMO'];
			$_REQUEST['COD_CUOTA'] = $cuota['COD_CUOTA'] + 1;
			$_REQUEST['NUMERO_CUOTA_PROVISIONADA'] = $cuota['NUMERO_CUOTA'] ;
			$_REQUEST['PROVISION_CERRADA'] = 0;
			$_REQUEST['VALOR_PROVISION'] = $detalleprestamo->calculoInteres($cuota['SALDO_CAPITAL'], ($cuota['TASA_INTERES']/100), str_replace('-','/',$cuota['FECHA_PAGO']) , str_replace('-','/',$cuota['ULTIMO_DIA_DEL_MES']) , $cuota['ANIO']);
			$parametros->save(get_class($parametros));
		}
		$this->set();
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		/*$obvista = new view_Parametros();
		$_REQUEST["filtro"]="NO";
		$mcampos = array($parametros->tableName().".COD_CIA",$parametros->tableName().".COD_LINEA",
						 $parametros->tableName().".NUM_REFLINEA","BANCOS.NOM_BANCO",
						 "PB_TIPOS_CREDITOS.DESCRIPCION_TIPOCREDITO",$parametros->tableName().".TECHO_LINEA","PB_ESTADOS.DESCRIPCION_ESTADO",
						 $parametros->tableName().".FECHA_APERTURA",$parametros->tableName().".FECHA_VENCIMIENTO",
						 $parametros->tableName().".DESTINO",$parametros->tableName().".DESCRIPCION_FORMA_PAGO",
						 $parametros->tableName().".DESCRIPCION_GARANTIAS",$parametros->tableName().".MOTIVOS_CADUCIDAD");
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 3, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),"", array("view"=>"style='display:none;'"));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html); 
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();*/
	}
	
	public function view(){
		$parametros = $this->set_obj();
		/*$Desglosepago = $this->listar_desglosepago($_REQUEST['COD_CIA'], $_REQUEST['COD_CUOTA'],$_REQUEST['COD_DESGLOSE']);
		$acuabono=0;
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:10px;width:60%;'>
						<thead>
						<tr>
							<th colspan='5'>Couta se Pagar&aacute; as&iacute;:</th>
						</tr>
						<tr>
							<th>Banco</th>
							<th>Pago A trav&eacute;s de</th>
							<th>Cuenta</th>
							<th>Chequera</th>
							<th>Por el valor</th>
							<th>*</th>
						</tr></thead><tbody>";
			foreach ($Desglosepago as $mks){
					$html .= "<tr class='tfl'>
									<td>
										".$mks["NOM_BANCO"]."
									</td>
									<td>
										".$mks["PAGO_ATRAVES"]."
									</td>
									<td>
										".$mks["COD_CUENTA"]."
									</td>
									<td>
										".$mks["SECUENCIA"]."
									</td>
									<td>
										".number_format($mks["VALOR_ABONO"],2,'.',',')."
									</td>
									<td>
										<a href='#' id='deldesglose' title='Eliminar Desglose' class='BTNDESGLOSEX btn btn-danger' COD_DETDESGLOSE='".$mks["COD_DETDESGLOSE"]."' COD_CUOTA='".$mks["COD_CUOTA"]."' VALOR_ABONO='".$mks["VALOR_ABONO"]."' NUMID='".$_REQUEST['NUMID']."'><i class=' icon-trash icon-white'></i></a>
									</td>									
							  </tr>";
							  $acuabono = number_format($acuabono + $mks["VALOR_ABONO"],2,'.','');
			}
		$html .= "</tbody><tfoot><tr><th></th><th></th><th></th><th>TOTAL A PAGAR:</th><th>$".number_format($acuabono,2,'.',',')."</th></tr></tfoot></table>";
		$data= array("tbldeglosepago"=>$html,"totalabono"=>$acuabono);
		echo json_encode($data);*/
	}
	
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*$_REQUEST[$parametros->tableName().".COD_CIA"] = $_SESSION['cod_cia']; 
		$_REQUEST[$parametros->tableName().".ANIO"] = date('Y');//2012;
		//$mcampos = array('COD_CIA','NUM_REQ','CODDEPTO_SOL','NOM_DEPTO','FECHA_ING','FECHA_AUTORIZADO','OBSERVACIONES','PROYECTO','ANIO','COD_CAT','TIPO_REQ','DESCRIPCION_PRIORIDAD');
        $mcampos = array($parametros->tableName().'.COD_CIA',$parametros->tableName().'.NUM_REQ',$parametros->tableName().'.CODDEPTO_SOL','DEPARTAMENTOS.NOM_DEPTO',$parametros->tableName().'.FECHA_ING',$parametros->tableName().'.FECHA_AUTORIZADO',$parametros->tableName().'.OBSERVACIONES',$parametros->tableName().'.PROYECTO',$parametros->tableName().'.ANIO',$parametros->tableName().'.COD_CAT',$parametros->tableName().'.TIPO_REQ','PRIORIDADES.DESCRIPCION_PRIORIDAD');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 2, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html); 
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();*/
	}
	
	public function get_ajax(){
		$parametros = $this->set_obj();
		/*if(isset($_REQUEST['COD_CAT']) && isset($_REQUEST['PROYECTO'])){
			$lstproducto = $parametros->get_lsoption("PRODUCTOS", array("COD_PROD"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "COD_CAT"=>"'".$_REQUEST['COD_CAT']."'"));
			$presupuestoxcategoria= $parametros->disponibleporcategoria();
			if($presupuestoxcategoria[0]['SALDO'] > 0){
				$msjpresupuesto="";
			}else{
				$msjpresupuesto="Para la categoria Seleccionada, no dispone de Presupuesto! Categoria No.".$_REQUEST['COD_CAT'] ." saldo: " . $presupuestoxcategoria[0]['SALDO'];
			}
			$json_array=array("lstproducto"=>$lstproducto ,"msjpresupuesto"=>$msjpresupuesto,"valorsaldo"=>$presupuestoxcategoria[0]['SALDO']);
			echo json_encode($json_array);	
		}*/		
	}

}


?>
