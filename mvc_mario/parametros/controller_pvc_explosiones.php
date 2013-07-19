<?php

/*
error_reporting(E_ALL);
ini_set("display_errors", 1);
*/
require_once('model_pvc_explosiones.php');
require_once('../core/render_view_generic.php');
class controller_pvc_explosiones extends pvc_explosiones{
	
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva Explosion',
                      'buscar'=>'Buscar Explosion',
                      'borrar'=>'Eliminar  Explosion',
                      'modificar'=>'Modificar  Explosion',
                      'listar'=>'Lista  Explosiones'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'parametros/controller_pvc_explosiones.php?act=set',
						'VIEW_GET_USER'=>'parametros/controller_pvc_explosiones.php?act=buscar',
						'VIEW_EDIT_USER'=>'parametros/controller_pvc_explosiones.php?act=modificar',
						'VIEW_DELETE_USER'=>'parametros/controller_pvc_explosiones.php?act=borrar'),
		'form_actions'=>array(
							'SET'=>'../parametros/controller_pvc_explosiones.php?act=insert',
							'GET'=>'../parametros/controller_pvc_explosiones.php',
        'DELETE'=>'../parametros/controller_pvc_explosiones.php?act=delete',
        'EDIT'=>'../parametros/controller_pvc_explosiones.php?act=edit',
        'GET_ALL'=>'../parametros/controller_pvc_explosiones.php?act=get_all'
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
			if(array_key_exists('NAME', $_REQUEST)) { 
				$parametros_data['NAME'] = $_REQUEST['NAME']; 
			}
			if(array_key_exists('VALUE_ITEM', $_REQUEST)) { 
				$parametros_data['VALUE_ITEM'] = $_REQUEST['VALUE_ITEM']; 
			}
			if(array_key_exists('CREATED_AT', $_REQUEST)) { 
				$parametros_data['CREATED_AT'] = $_REQUEST['CREATED_AT']; 
			}
			if(array_key_exists('UPDATED_AT', $_REQUEST)) { 
				$parametros_data['UPDATED_AT'] = $_REQUEST['UPDATED_AT']; 
			}
			if(array_key_exists('CALCULATED', $_REQUEST)) { 
				$parametros_data['CALCULATED'] = $_REQUEST['CALCULATED']; 
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
		$obj = new pvc_explosiones();
		return $obj;
	}
	$lstempelado = $requisicion->lsoptions("VWEMPLEADOS", 
		array("COD_EMP"=>"","NOMBRE_ISSS"=>""),
		 array("COD_CIA"=>$_SESSION['cod_cia'], "STATUS"=>"'A'", "COD_DEPTO"=>$objemp[0]['COD_DEPTO']));
	
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
		$parametros->delete('pvc_explosiones',$parametros_data);
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
		$masx= array('ID','TYPE_PLAN','PLAN','EXTRA','CREATED_AT');
        $masx=implode($masx, ",");
        if($_REQUEST["filtro"]=="")$_REQUEST["filtro"]=1;
		$data = $parametros->lis(get_class($parametros),'0',$masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros));
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

}

$objecon =  new controller_pvc_explosiones();
$objecon->handler();





?>
