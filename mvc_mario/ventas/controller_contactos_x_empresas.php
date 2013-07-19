<?php


error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('model_contactos_x_empresas.php');
require_once('../core/render_view_generic.php');
class controller_contactos_x_empresas extends contactos_x_empresas{
	
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva Contacto de Empresa',
                      'buscar'=>'Buscar Contacto de Empresa',
                      'borrar'=>'Eliminar  Contacto de Empresa',
                      'modificar'=>'Modificar  Contacto de Empresa',
                      'listar'=>'Lista  Contacto de Empresas'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'ventas/index.php?act=set&ctl=controller_contactos_x_empresas',
						'VIEW_GET_USER'=>'ventas/index.php?act=buscar&ctl=controller_contactos_x_empresas',
						'VIEW_EDIT_USER'=>'ventas/index.php?act=modificar&ctl=controller_contactos_x_empresas',
						'VIEW_DELETE_USER'=>'ventas/index.php?act=borrar&ctl=controller_contactos_x_empresas'),
		'form_actions'=>array(
							'SET'=>'../ventas/index.php?act=insert&ctl=controller_contactos_x_empresas',
							'GET'=>'../ventas/index.php&ctl=controller_contactos_x_empresas',
        'DELETE'=>'../ventas/index.php?act=delete&ctl=controller_contactos_x_empresas',
        'EDIT'=>'../ventas/index.php?act=edit&ctl=controller_contactos_x_empresas',
        'GET_ALL'=>'../ventas/index.php?act=get_all&ctl=controller_contactos_x_empresas'
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
			if(array_key_exists('EMPRESA_ID', $_REQUEST)) { 
				$parametros_data['EMPRESA_ID'] = $_REQUEST['EMPRESA_ID']; 
			}
			if(array_key_exists('NOMBRE', $_REQUEST)) { 
				$parametros_data['NOMBRE'] = $_REQUEST['NOMBRE']; 
			}
			if(array_key_exists('APELLIDOS', $_REQUEST)) { 
				$parametros_data['APELLIDOS'] = $_REQUEST['APELLIDOS']; 
			}
			if(array_key_exists('CARGO', $_REQUEST)) { 
				$parametros_data['CARGO'] = $_REQUEST['CARGO']; 
			}
			if(array_key_exists('TELEFONO', $_REQUEST)) { 
				$parametros_data['TELEFONO'] = $_REQUEST['TELEFONO']; 
			}
			if(array_key_exists('CORREO', $_REQUEST)) { 
				$parametros_data['CORREO'] = $_REQUEST['CORREO']; 
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
		$obj = new contactos_x_empresas();
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
		
		$rendertable=$this->date_table($parametros);

		$obvista->html = $obvista->get_template('template',get_class($parametros));		
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}',$this->msg, $obvista->html);
		$obvista->html = str_replace('{error}',$this->error, $obvista->html);
		$obvista->retornar_vista();

	}

	public function date_table(){
		$parametros= $this->set_obj();
		$masx= array($parametros->tableName().'.ID',					 
					 "EMPRESAS.NOMBRE||'|'||EMPRESAS.ID",
					 $parametros->tableName().'.NOMBRES',
					 $parametros->tableName().'.APELLIDOS',
					 $parametros->tableName().'.CARGO');
        			 $masx=implode($masx, ",");
		$links =array("","controller_empresas","","");          			         
		$data = $parametros->lis2(get_class($parametros),'0',$masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),$links);

		return $rendertable;
	} 

}

//$objecon =  new controller_contactos_x_empresas();
//$objecon->handler();





?>
