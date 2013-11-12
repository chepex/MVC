<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_historicotasaicuota.php');
require_once('controller_pb_detalleprestamos.php');
require_once('controller_pb_prestamos.php');
require_once('../core/render_view_generic.php');
#Controlador de pb_historicotasaicuota
class controller_pb_historicotasaicuota extends pb_historicotasaicuota{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nuevo Tipo de Credito',
                      'buscar'=>'Buscar Tipo de Credito',
                      'borrar'=>'Eliminar Tipo de Credito',
                      'modificar'=>'Modificar Tipo de Credito',
                      'listar'=>'Lista de Tipos de De Creditos'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_historicotasaicuota&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_historicotasaicuota&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_historicotasaicuota&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_historicotasaicuota&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_historicotasaicuota&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_historicotasaicuota',
        'DELETE'=>'../prestamos/?ctl=controller_pb_historicotasaicuota&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_historicotasaicuota&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_historicotasaicuota&act=get_all'
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
		$obj = new pb_historicotasaicuota;
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
		$obvista = new view_Parametros();
		$parametros->delete(get_class($parametros));
	}
	
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	public function edit(){
		$parametros = $this->set_obj();
	}
	
	public function insert(){
		$_REQUEST['COD_CIA'] = $_SESSION['cod_cia'];
		$parametros = $this->set_obj();
		$prestamo = new controller_pb_prestamos();
		$detprestamo = new controller_pb_detalleprestamos();
		$this->delete();
		$parametros->save(get_class($parametros));
		$detprestamo->ModificarTasaInteres($_REQUEST['COD_PRESTAMO'],$_REQUEST['COD_CUOTA'],$_REQUEST['TASA_INTERES_NUV']);
		$prestamo->view_detprestamo();
		
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	public function view(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
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
