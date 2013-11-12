<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_detallepago.php');
require_once('controller_pb_pagos.php');
require_once('../core/render_view_generic.php');
#Controlador de Detalle de pagos
class controller_pb_detallepago extends pb_detallepago{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nuevo Detalle Pago',
                      'buscar'=>'Buscar Detalle Pago',
                      'borrar'=>'Eliminar Detalle Pago',
                      'modificar'=>'Modificar Detalle Pago',
                      'listar'=>'Lista de Detalle Pago'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_detallepago&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_detallepago&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_detallepago&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_detallepago&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_detallepago&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_detallepago',
        'DELETE'=>'../prestamos/?ctl=controller_pb_detallepago&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_detallepago&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_detallepago&act=get_all'
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
	
	#Definicion de una instancia del Modelo del Controlador de detalle de pagos
	public function set_obj() {
		$obj = new pb_detallepago();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion de detalle de pagos, no se utiliza
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
	}
	
	#Método que elimina el detalle de pago, no se utiliza
	public function delete(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico que permite dibujar formulario para actualizacion, no se utiliza
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico, permite la edicion de registros, no se utiliza
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico para insercion de registros a traves del modelo, de detalle de pago
	public function insert(){
		$parametros = $this->set_obj();
		$_REQUEST['COD_DETPAGO'] = $this->nextval_seq();
		$parametros->save(get_class($parametros));
	}
	
	#Método que devuelve una tabla CRUD no se utiliza
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
