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
		$obvista->html = str_replace('{error}', $this->msg , $obvista->html);
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
	}
	
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['modificar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('modificar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{ANIO_PROVISION}', date('Y') , $obvista->html);
		$obvista->html = str_replace('{error}', $this->msg , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$dataprovisiones = $this->verificarprovision($_REQUEST['COD_CIA'], $_REQUEST['MES_PROVISION'], $_REQUEST['ANIO_PROVISION']);
		if(count($dataprovisiones) > 0){
			if($dataprovisiones[0]['PROVISION_CERRADA'] == 0){
				$this->query="UPDATE ". $this->tableName() . 
						" SET PROVISION_CERRADA = 1
						WHERE ANIO_PROVISION=". $_REQUEST['ANIO_PROVISION'] ."
								AND MES_PROVISION = " . $_REQUEST['MES_PROVISION'] ."
								AND PROVISION_CERRADA = 0" ;
				$this->execute_single_query();
				$this->msg="Se ha completado el cierre de Provisi&oacute;n para el mes: ". $_REQUEST['MES_PROVISION'] . " y a&ntilde;o ". $_REQUEST['ANIO_PROVISION'];
			}else{
				$this->msg="La Provisi&oacute;n para el mes: ".$_REQUEST['MES_PROVISION']." y a&ntilde;o: ".$_REQUEST['ANIO_PROVISION']." ya fu&eacute; Cerrada anteriormente!";
			}
		}else{
			$this->msg="No Existe una provisi&oacute;n para el mes: ". $_REQUEST['MES_PROVISION'] ." y a&ntilde;o ". $_REQUEST['ANIO_PROVISION'] ;
		}
		$this->update();
	}
	
	public function insert(){
		$parametros = $this->set_obj();
		$detalleprestamo =  new pb_detalleprestamos();
		$dataprovisiones = $this->verificarprovision($_REQUEST['COD_CIA'], $_REQUEST['MES_PROVISION'], $_REQUEST['ANIO_PROVISION']);
		if(count($dataprovisiones) == 0){
			$cuotasProvisionar = $parametros->listar_paraprovision($_REQUEST['COD_CIA'], $_REQUEST['MES_PROVISION'], $_REQUEST['ANIO_PROVISION']);
			foreach($cuotasProvisionar as $cuota){
				$_REQUEST['COD_PRESTAMO'] = $cuota['COD_PRESTAMO'];
				$_REQUEST['COD_CUOTA'] = $cuota['COD_CUOTA'] + 1;
				$_REQUEST['NUMERO_CUOTA_PROVISIONADA'] = $cuota['NUMERO_CUOTA'] ;
				$_REQUEST['PROVISION_CERRADA'] = 0;
				$_REQUEST['VALOR_PROVISION'] = $detalleprestamo->calculoInteres($cuota['SALDO_CAPITAL'], ($cuota['TASA_INTERES']/100), str_replace('-','/',$cuota['FECHA_PAGO']) , str_replace('-','/',$cuota['ULTIMO_DIA_DEL_MES']) , $cuota['ANIO']);
				$parametros->save(get_class($parametros));
			}
			$this->msg = "Se ha Completado el Calculo de Provisi&oacute;n para el mes: ". $_REQUEST['MES_PROVISION'] . " y a&ntilde;o: ". $_REQUEST['ANIO_PROVISION'] ;
		}else{
			if($dataprovisiones[0]['PROVISION_CERRADA'] == 0){
				$this->query="DELETE FROM ". $this->tableName() . 
						" WHERE ANIO_PROVISION=". $_REQUEST['ANIO_PROVISION'] ."
								AND MES_PROVISION = " . $_REQUEST['MES_PROVISION'] ."
								AND PROVISION_CERRADA = 0" ;
				$this->execute_single_query();
				
				$cuotasProvisionar = $parametros->listar_paraprovision($_REQUEST['COD_CIA'], $_REQUEST['MES_PROVISION'], $_REQUEST['ANIO_PROVISION']);
				foreach($cuotasProvisionar as $cuota){
					$_REQUEST['COD_PRESTAMO'] = $cuota['COD_PRESTAMO'];
					$_REQUEST['COD_CUOTA'] = $cuota['COD_CUOTA'] + 1;
					$_REQUEST['NUMERO_CUOTA_PROVISIONADA'] = $cuota['NUMERO_CUOTA'] ;
					$_REQUEST['PROVISION_CERRADA'] = 0;
					$_REQUEST['VALOR_PROVISION'] = $detalleprestamo->calculoInteres($cuota['SALDO_CAPITAL'], ($cuota['TASA_INTERES']/100), str_replace('-','/',$cuota['FECHA_PAGO']) , str_replace('-','/',$cuota['ULTIMO_DIA_DEL_MES']) , $cuota['ANIO']);
					$parametros->save(get_class($parametros));
				}
				$this->msg = "Se ha Completado el Calculo de Provisi&oacute;n para el mes: ". $_REQUEST['MES_PROVISION'] . " y a&ntilde;o: ". $_REQUEST['ANIO_PROVISION'] ." <br/> Se Revirti&oacute; la Provisi&oacute;n que estaba sin cerrar " ;
			}else{
				$this->msg = "No es posible la reversi&oacute;n de la Provisi&oacute;n para el mes: ".$_REQUEST['MES_PROVISION'] . " y a&ntilde;o: " . $_REQUEST['ANIO_PROVISION'] . " Esta ya fue cerrada" ;
			}
		}
		$this->set();
		
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
	}
	
	public function view(){
		$parametros = $this->set_obj();
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
