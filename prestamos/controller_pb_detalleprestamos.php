<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_detalleprestamos.php');
require_once('../core/render_view_generic.php');
#Controlador de Requisicion
class controller_pb_detalleprestamos extends pb_detalleprestamos{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nuevo Detalle Prestamo',
                      'buscar'=>'Buscar Detalle Prestamo',
                      'borrar'=>'Eliminar Detalle Prestamo',
                      'modificar'=>'Modificar Detalle Prestamo',
                      'listar'=>'Lista de Detalle Prestamo'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_detalleprestamos&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_detalleprestamos&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_detalleprestamos&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_detalleprestamos&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_detalleprestamos&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_detalleprestamos',
        'DELETE'=>'../prestamos/?ctl=controller_pb_detalleprestamos&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_detalleprestamos&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_detalleprestamos&act=get_all'
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
	
	#Definicion de una instancia del Modelo del Controlador requisicion encabezado de la requisicion
	public function set_obj() {
		$obj = new pb_detalleprestamos();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion del encabezado de la Requisicion
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$lstestados = $parametros->get_lsoption("pb_estados", array("COD_ESTADO"=>"","DESCRIPCION_ESTADO"=>""));
		$lstbancos = $parametros->get_lsoption("pb_bancos", array("COD_BANCO"=>"","NOMBRE_CORTO"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lstlineas = $parametros->get_lsoption("pb_lineascredito", array("COD_LINEA"=>"","NUM_REFLINEA"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{COD_PRESTAMO}',$this->nextval_seq(), $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{COD_ESTADO}', $lstestados , $obvista->html);
		$obvista->html = str_replace('{COD_BANCO}', $lstbancos , $obvista->html);
		$obvista->html = str_replace('{COD_LINEA}', $lstlineas , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
		$objcuota = $parametros->crea_objeto(array($this->tableName()),'',array("COD_CIA=".$_SESSION['cod_cia'],"COD_PRESTAMO=".$_REQUEST['COD_PRESTAMO'],"NUMERO_CUOTA=".$_REQUEST['NUMERO_CUOTA']));
		$dataArray=array("VALOR_CUOTA"=>$objcuota[0]['VALOR_CUOTA'],
				   "TASA_INTERES"=>$objcuota[0]['TASA_INTERES'],
				   "VALOR_INTERES"=>$objcuota[0]['VALOR_INTERES'],
				   "VALOR_AMORTIZACION"=>$objcuota[0]['VALOR_AMORTIZACION'],
				   "SALDO_CAPITAL"=>$objcuota[0]['SALDO_CAPITAL'],
				   "COD_CUOTA"=>$objcuota[0]['COD_CUOTA']
				   );
		/*echo"<pre>";
			print_r($dataArray);
		echo"</pre>";*/
		echo json_encode($dataArray);
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
		$objlinea = $this->crea_objeto(array("pb_lineascredito linc","pb_bancos ban","pb_estados edos","pb_tipos_creditos tipcre"),
										   array("linc.COD_ESTADO = edos.COD_ESTADO","linc.COD_CIA = ban.COD_CIA","linc.COD_BANCO = ban.COD_BANCO","linc.COD_CIA = tipcre.COD_CIA","linc.COD_TIPOCREDITO = tipcre.COD_TIPOCREDITO AND"),
										   array("linc.COD_CIA=".$_REQUEST['COD_CIA'],"linc.COD_LINEA=".$_REQUEST['COD_LINEA'])
										   );
		$lstestados = $parametros->get_lsoption("pb_estados", array("COD_ESTADO"=>"","DESCRIPCION_ESTADO"=>""),array("COD_ESTADO"=>$objlinea[0]['COD_ESTADO']));
		$lstbancos = $parametros->get_lsoption("pb_bancos", array("COD_BANCO"=>"","NOMBRE_CORTO"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"COD_BANCO"=>$objlinea[0]['COD_BANCO']));
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
		$parametros->save(get_class($parametros));
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$_REQUEST["filtro"]="NO";
		$data = $parametros->lis(get_class($parametros), 1, "0");
		$rendertable = $parametros->render_table_crud(get_class($parametros));
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
		/*$detreq = new controller_reqdet();
		//$mcampos = array('COD_CIA','NUM_REQ','CODDEPTO_SOL', 'NOM_DEPTO','FECHA_ING','FECHA_AUTORIZADO','OBSERVACIONES','PROYECTO','ANIO','COD_CAT','TIPO_REQ','DESCRIPCION_PRIORIDAD');
        $mcampos = array($parametros->tableName().'.COD_CIA',
						 $parametros->tableName().'.NUM_REQ',
						 $parametros->tableName().'.CODDEPTO_SOL',
						 'DEPARTAMENTOS.NOM_DEPTO',
						 $parametros->tableName().'.FECHA_ING',
						 $parametros->tableName().'.FECHA_AUTORIZADO',
						 $parametros->tableName().'.OBSERVACIONES',
						 $parametros->tableName().'.PROYECTO',
						 $parametros->tableName().'.ANIO',
						 $parametros->tableName().'.COD_CAT',
						 $parametros->tableName().'.TIPO_REQ',
						 'PRIORIDADES.DESCRIPCION_PRIORIDAD'
						);
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 1, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),'',array("delete"=>"style='display:none;'","update"=>"style='display:none;'","view"=>"style='display:none;'","set"=>"style='display:none;'"));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
		$detreq->get_all();*/
		
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