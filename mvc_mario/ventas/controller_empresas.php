<?php


error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('model_empresas.php');
require_once('controller_contactos_x_empresas.php');
require_once('../core/render_view_generic.php');
class controller_empresas extends empresas{
	
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva Empresa',
                      'buscar'=>'Buscar Empresa',
                      'borrar'=>'Eliminar  Empresa',
                      'modificar'=>'Modificar  Empresa',
                      'listar'=>'Lista  Empresas'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'ventas/index.php?act=set&ctl=controller_empresas',
						'VIEW_GET_USER'=>'ventas/index.php?act=buscar&ctl=controller_empresas',
						'VIEW_EDIT_USER'=>'ventas/index.php?act=modificar&ctl=controller_empresas',
						'VIEW_DELETE_USER'=>'ventas/index.php?act=borrar&ctl=controller_empresas'),
		'form_actions'=>array(
							'SET'=>'../ventas/index.php?act=insert&ctl=controller_empresas',
							'GET'=>'../ventas/index.php&ctl=controller_empresas',
        'DELETE'=>'../ventas/index.php?act=delete&ctl=controller_empresas',
        'EDIT'=>'../ventas/index.php?act=edit&ctl=controller_empresas',
        'GET_ALL'=>'../ventas/index.php?act=get_all&ctl=controller_empresas'
		)
	);
	
//	protected $msg;
	
	public function handler($op='') {
		if(empty($op)){
			$event = 'buscar';
			if(isset($_REQUEST['act'])){
				$uri = $_REQUEST['act'];
			}
			else{
				$uri = "get_all";
			}
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
		
		

		
		switch ($event) {
			case 'set':
				$this->set();
				break;
			case 'get':        
				$this->get();
				break;
			case 'delete':
				$this->delete();
				$this->get_all();
				break;
			case 'update':
				$this->update();
				break;
			case 'edit':
				$this->edit();
				$this->get_all();
				break;
			case 'insert':
				$this->insert();

				$this->get_all();
				break;	
			case 'get_all':

				$this->get_all();

				break;
		}
	}
	
	public function helper_parametros_data() {
		$parametros_data = array();
		if(isset($_REQUEST)) {
			if(array_key_exists('ID', $_REQUEST)) { 
				$parametros_data['ID'] = $_REQUEST['ID']; 
			}
			if(array_key_exists('NOMBRE', $_REQUEST)) { 
				$parametros_data['NOMBRE'] = $_REQUEST['NOMBRE']; 
			}
			if(array_key_exists('TEL', $_REQUEST)) { 
				$parametros_data['TEL'] = $_REQUEST['TEL']; 
			}
			if(array_key_exists('DIRECCION', $_REQUEST)) { 
				$parametros_data['DIRECCION'] = $_REQUEST['DIRECCION']; 
			}
			if(array_key_exists('NUM_REGISTRO', $_REQUEST)) { 
				$parametros_data['NUM_REGISTRO'] = $_REQUEST['NUM_REGISTRO']; 
			}
			if(array_key_exists('NIT', $_REQUEST)) { 
				$parametros_data['NIT'] = $_REQUEST['NIT']; 
			}
			if(array_key_exists('TCONTRIBUYENTE', $_REQUEST)) { 
				$parametros_data['TCONTRIBUYENTE'] = $_REQUEST['TCONTRIBUYENTE']; 
			}
			if(array_key_exists('GIRO', $_REQUEST)) { 
				$parametros_data['GIRO'] = $_REQUEST['GIRO']; 
			}							
		} else if($_REQUEST) {
			if(array_key_exists('ID', $_REQUEST)) {
				$parametros_data = $_REQUEST['ID'];
			}
		}
		// print_r( $requisicion_data);
		return $parametros_data;
	}

	public function set_obj() {
		$obj = new empresas();
		return $obj;
	}
	
	public function set(){
		$parametros_data = $this->helper_parametros_data();
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html); 
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);				
		$obvista->html = str_replace('{error}', ' ', $obvista->html);	
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	public function get(){
		$parametros_data = $this->helper_parametros_data();
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		
		$parametros->get($parametros_data);
		$data = array('ID'=>$parametros->ID);            
		$obvista->retornar_vista('buscar', $data);
	}
	
	public function delete(){
		$parametros_data = $this->helper_parametros_data();
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$parametros->delete('empresas',$parametros_data);
		$this->msg=$parametros->mensaje;
		$this->error=$parametros->error;

	}
	
	public function update(){
		$parametros_data = $this->helper_parametros_data();
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		
		$data = $parametros->lis(get_class($parametros),1);
		$tagreplace = $parametros->render_etiquetas($data);
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['modificar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('modificar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->html = str_replace('{error}',' ', $obvista->html);
		$obvista->render_html($tagreplace);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();

	}
	
	public function edit(){
		$parametros_data = $this->helper_parametros_data();
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$parametros->update(get_class($parametros));
		$this->msg=$parametros->mensaje; 
		$this->error=$parametros->error;
	}
	
	public function insert(){
		echo "aqui1";
		print_r($parametros);
		$parametros_data = $this->helper_parametros_data();
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		
		$parametros->save(get_class($parametros));
		$this->msg=$parametros->mensaje;
		$this->error=$parametros->error;
		
	
	}
	
	public function get_all(){
		
		$parametros_data = $this->helper_parametros_data();
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$masx= array('ID','NOMBRE','NIT','TEL','GIRO');
        $masx=implode($masx, ",");
        if($_REQUEST["filtro"]=="")$_REQUEST["filtro"]=1;

		$data = $parametros->lis(get_class($parametros),'0',$masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros));
		$contactos = new controller_contactos_x_empresas();
		$tabla_contactos = $contactos->date_table();


	

		$obvista->html = $obvista->get_template('template',get_class($parametros));		
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);

		$x = $obvista->get_template('listar',"contactos_x_empresas");
		
		$x = str_replace('{Detalle}', $tabla_contactos, $x);	

		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{Detalle2}', $x, $obvista->html);
		
		$obvista->html = str_replace('{mensaje}',$this->msg, $obvista->html);
		$obvista->html = str_replace('{error}',$this->error, $obvista->html);
		$obvista->retornar_vista();

	}

}






?>
