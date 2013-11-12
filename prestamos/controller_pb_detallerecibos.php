<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_detallerecibos.php');
require_once('../core/render_view_generic.php');
#Controlador de pb_detallerecibos
class controller_pb_detallerecibos extends pb_detallerecibos{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Recibo',
                      'buscar'=>'Buscar Recibo',
                      'borrar'=>'Eliminar Recibo',
                      'modificar'=>'Modificar Recibo',
                      'listar'=>'Lista de Recibo'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_detallerecibos&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_detallerecibos&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_detallerecibos&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_detallerecibos&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_detallerecibos&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_detallerecibos',
        'DELETE'=>'../prestamos/?ctl=controller_pb_detallerecibos&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_detallerecibos&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_detallerecibos&act=get_all'
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
		$obj = new pb_detallerecibos();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion, no se utiliza
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
	}
	
	#Método que elimina el detalle de los recibos, no se utiliza
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
		$parametros->save(get_class($parametros));
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
