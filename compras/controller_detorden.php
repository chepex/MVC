<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
require_once('model_detorden.php');
require_once('controller_ordenenc.php');
require_once('../core/render_view_generic.php');
#Controlador Detalle de Orden de Compra
class controller_detorden extends detorden{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva Requisicion',
                      'buscar'=>'Buscar Requisicion',
                      'borrar'=>'Eliminar una Requisicion',
                      'modificar'=>'Modificar una Requisicion',
                      'listar'=>'Lista de Requisicion'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'compras/controller_requisicion.php?act=set',
						'VIEW_GET_USER'=>'compras/controller_requisicion.php?act=buscar',
						'VIEW_EDIT_USER'=>'compras/controller_requisicion.php?act=modificar',
						'VIEW_DELETE_USER'=>'compras/controller_requisicion.php?act=borrar'),
		'form_actions'=>array(
							'SET'=>'../compras/controller_Requisicion.php?act=insert',
							'GET'=>'../compras/controller_requisicion.php',
        'DELETE'=>'../compras/controller_requisicion.php?act=delete',
        'EDIT'=>'../compras/controller_requisicion.php?act=edit',
        'GET_ALL'=>'../compras/controller_requisicion.php?act=get_all'
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
                        'update','get_all','listar','insert');
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
				//$this->set();
				$this->get_all($this->msg);
				break;	
			case 'get_all':
				$this->get_all();
				break;
			case 'get_ajax':
				$this->get_ajax();
				break;
		}
	}
	
	#Definicion de una instancia del Modelo del Controlador detorden
	public function set_obj() {
		$obj = new detorden();
		return $obj;
	}
	
	#Definicion de una Instancia del Modelo de ordenenc encabezado de la orden de compra
	public function set_obj_master() {
		$obj = new ordenenc();
		return $obj;
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function set(){
		$parametros = $this->set_obj();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function delete(){
		$parametros = $this->set_obj();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function update(){
		$parametros = $this->set_obj();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function edit(){
		$parametros = $this->set_obj();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function insert(){
		$parametros = $this->set_obj();
	}
	
	#Método que se encarga de dibujar la tabla crud del detalle de la orden de compra
	public function get_all($mensaje=''){
		$_REQUEST["filtro"]='NO';
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$mcampos = array($parametros->tableName().'.NUM_ORDEN',$parametros->tableName().'.COD_PROD','PRODUCTOS.NOMBRE','UNIDADES.DESCRIPCION', $parametros->tableName().'.CANTIDAD',$parametros->tableName().'.PRECIOUNI',$parametros->tableName().'.VALORREQ');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), "1", $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),'',array("set"=>"style='display:none;'"));
		$obvista->html = $obvista->get_template('listar',get_class($parametros)); 
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
	}
	
	#Método para invocacion via ajax
	public function get_ajax(){
		echo $lstproducto;
	}

}


?>
