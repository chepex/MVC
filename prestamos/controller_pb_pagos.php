<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_pagos.php');
require_once('controller_pb_prestamos.php');
require_once('controller_pb_detallepago.php');
require_once('controller_pb_desglosepago.php');
require_once('controller_pb_recibos.php');
require_once('../core/render_view_generic.php');
#Controlador de Requisicion
class controller_pb_pagos extends pb_pagos{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nuevo Pago',
                      'buscar'=>'Buscar Pago',
                      'borrar'=>'Eliminar Pago',
                      'modificar'=>'Modificar Pago',
                      'listar'=>'Lista de Pago'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_pagos&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_pagos&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_pagos&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_pagos&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_pagos&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_pagos',
        'DELETE'=>'../prestamos/?ctl=controller_pb_pagos&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_pagos&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_pagos&act=get_all'
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
			case 'view_rpt':
				$this->view_rpt();
				break;
		}
	}
	
	#Definicion de una instancia del Modelo del Controlador requisicion encabezado de la requisicion
	public function set_obj() {
		$obj = new pb_pagos();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion del encabezado de la Requisicion
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$desglosepago = new controller_pb_desglosepago();
		//$lstbancos = $parametros->get_lsoption("bancos", array("COD_BANCO"=>"","NOM_BANCO"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lsttiponota = $parametros->get_lsoption("tipo_nota", array("tipo_nota"=>"","nombre_nota"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{COD_DESGLOSE}',$desglosepago->nextval_seq(), $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		//$obvista->html = str_replace('{COD_BANCO}',$lstbancos, $obvista->html);
		//$obvista->html = str_replace('{TIPO_NOTA}',$lsttiponota, $obvista->html);
		$obvista->html = str_replace('{FPAGO}',date('d/m/Y'), $obvista->html);
		$obvista->html = str_replace('{FPAGOVEN}',date('d/m/Y'), $obvista->html);	
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
		$prestamo = new controller_pb_prestamos();
		$prestamo->view_detprestamo();
	}
	
	#Método que elimina el detalle de la requisicion, sino tiene detalle
	public function delete(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*$parametros->delete(get_class($parametros));
		$this->msg=$parametros->mensaje;*/
	}
	
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*$objlinea = $this->crea_objeto(array("pb_lineascredito linc","bancos ban","pb_estados edos","pb_tipos_creditos tipcre"),
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
		//instancia del modelo pb_pagos
		$parametros = $this->set_obj();
		//instanncia del controlador detallepago
		$detallepago = new controller_pb_detallepago();
		//Instancia de controlador pb_prestatamos
		$prestamo = new controller_pb_prestamos();
		
		$recibos = new controller_pb_recibos();
		/***
			Cuenta de los elementos de $_REQUEST, el cual trae todos los elementos del 
			formulario generado dinamica por controller_pb_prestamos, metodo view_detprestamo.
			se toman solo las cuotas que hayan sido chequeadas para pago, (Tienen que haber sido desglosadas para pago).
		***/
		$cuenta=count($_REQUEST);
		//Se recorren los elementos de $_REQUEST
		for($i=0; $i<=$cuenta;$i++){
			//Se Toman los Checkbox marcados para pago(Deben de haber sido desglosados para pago, para saber cuanto se abonara a la cuota)
			$NOMBRE_CAMPO='COD_CUOTA_'.$i;
			//Se Verifica si se uso El Checkbox, si se marco para pago
			if(isset($_REQUEST[$NOMBRE_CAMPO])){
				//Se Recupera el Codigo de la Cuota del value de los Checkbox a pagar
				$_REQUEST['COD_CUOTA']= $_REQUEST[$NOMBRE_CAMPO];
				//Se Crea Un Objeto con el pb_detalleprestamos, que es la proyeccicon de pago, para saber cual es el valor de interes a pagar de la cuota
				$objcuota = $parametros->crea_objeto(array("pb_detalleprestamos"),"", array("COD_CIA=".$_SESSION['cod_cia'], "COD_CUOTA=".$_REQUEST['COD_CUOTA']));
				//Se Crea Un Objeto con el pb_detallepago, Sumando el valor de interes pagado, para ser comparado con el valor correspondiente a pagar para la cuota
				$objpago = $parametros->crea_objeto(array("pb_detallepago"),"", array("COD_CIA=".$_SESSION['cod_cia'], "COD_CUOTA=".$_REQUEST['COD_CUOTA']), array("nvl(sum(pb_detallepago.ABONO_INTERES),0) VALOR_INTERES"));
				//Se Toma el Valor a Abonar a la Cuota
				$VALOR_PAGO = $_REQUEST['ABONO_CUOTA_'.$i];
				//si no se ha abonado nada de intereses
				if($objpago[0]['VALOR_INTERES'] == 0 ){
					//si el valor abonado es mayor, que el valor de los intereses
					if($VALOR_PAGO > $objcuota[0]['VALOR_INTERES']){
						$VALOR_AMORTIZACION = $VALOR_PAGO - $objcuota[0]['VALOR_INTERES'];
						$_REQUEST['ABONO_AMORTIZACION'] = $VALOR_AMORTIZACION ;
						$_REQUEST['ABONO_INTERES'] = $objcuota[0]['VALOR_INTERES'];
					}else{
						$_REQUEST['ABONO_AMORTIZACION'] = 0;
						$_REQUEST['ABONO_INTERES'] = $VALOR_PAGO;
					}
				//sino, si ya se abono a interes 
				}else{
					//si lo pagado de intereses es igual al valor correspondiente de la cuota(si ya se cubrieron los intereses) se abona todo a la amortizacion
					if($objpago[0]['VALOR_INTERES'] == $objcuota[0]['VALOR_INTERES']){
						$_REQUEST['ABONO_AMORTIZACION'] = $VALOR_PAGO;
						$_REQUEST['ABONO_INTERES'] = 0;
					//sino se abona a intereses lo que resta
					}else{
						$restaxpogarinteres = $objcuota[0]['VALOR_INTERES'] - $objpago[0]['VALOR_INTERES'];
						if($VALOR_PAGO > $restaxpogarinteres){
							$VALOR_AMORTIZACION = $VALOR_PAGO - $restaxpogarinteres;
							$_REQUEST['ABONO_AMORTIZACION'] = $VALOR_AMORTIZACION ;
							$_REQUEST['ABONO_INTERES'] = $restaxpogarinteres;
						}else{
							$_REQUEST['ABONO_AMORTIZACION'] = 0;
							$_REQUEST['ABONO_INTERES'] = $restaxpogarinteres;
						}
					}
				}
				//Se Genera el Detalle de pago, con el valor que corresponde de abono a Interes y a la Amortizacion
				$detallepago->insert();
			}
		}
		$recibos->insert();
		//Se invoca la Lista de Cuotas a pagar, para ver el efecto de lo realizado
		$prestamo->view_detprestamo();
		
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$_REQUEST["filtro"]="NO";
		$mcampos = array($parametros->tableName().".COD_CIA",$parametros->tableName().".COD_PAGO",
						 "PB_PRESTAMOS.REF_PRESTAMO","BANCOS.NOM_BANCO",
						 $parametros->tableName().".FECHA_PAGO","PB_LINEASCREDITO.NUM_REFLINEA");
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
		/*$detprestamo = new controller_pb_detalleprestamos();
		echo$detprestamo->generar_tablaamortizacion();*/
	}

}


?>