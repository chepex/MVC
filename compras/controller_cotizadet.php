<?php


error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once('model_cotizadet.php');
require_once('controller_cotizacion.php');
require_once('../core/render_view_generic.php');
class controller_cotizadet extends reqdet{
	
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva Cotizacion',
                      'buscar'=>'Buscar Cotizacion',
                      'borrar'=>'Eliminar una Cotizacion',
                      'modificar'=>'Modificar una Cotizacion',
                      'listar'=>'Lista de Cotizacion'),
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

	public function set_obj() {
		$obj = new cotizadet();
		return $obj;
	}
	
	public function set_obj_master() {
		$obj = new cotizacion();
		return $obj;
	}
	
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$objciau = $parametros->crea_objeto(array("CIAS_X_USUARIO"), "", array("USUARIO='".$_SESSION['usuario']."'"));
		$objemp = $parametros->crea_objeto(array("VWEMPLEADOS"), "",array("COD_EMP=". $objciau[0]["COD_EMP"]));
        $objdepto = $parametros->crea_objeto(array("DEPARTAMENTOS"), "",array("COD_DEPTO=". $objemp[0]["COD_DEPTO"]));
		$objunidamedida1 = $parametros->crea_objeto(array("UNIDADES"),"",array("1=1"),array("CODIGO_UNIDAD","DESCRIPCION"));
		$objunidamedida2 = $parametros->crea_objeto(array("UNIDADES u","EQUIVALENCIAS e"),array("u.CODIGO_UNIDAD = e.CODIGO_EQUIVALENCIA"),"",array("e.CODIGO_UNIDAD","u.DESCRIPCION"));
		$obunidadesmedidas= array_merge($objunidamedida1,$objunidamedida2);
		$lstdptos = $parametros->get_lsoption("DEPARTAMENTOS", array("COD_DEPTO"=>"","NOM_DEPTO"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "COD_DEPTO"=>$objemp[0]['COD_DEPTO']));
        $lstempelado = $parametros->get_lsoption("VWEMPLEADOS", array("COD_EMP"=>"","NOMBRE_ISSS"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "STATUS"=>"'A'", "COD_DEPTO"=>$objemp[0]['COD_DEPTO']));
        $lstcategorias = $parametros->get_lsoption("CATEGORIAS", array("COD_CAT"=>"","NOM_CAT"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
        $lstproyectos =  $parametros->get_lsoption("PROYECTO", array("PROYECTO"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"PROYECTO"=> "'".$objdepto[0]['PROYECTO']."'"));
		$lstunidades = $parametros->get_htmloptions($obunidadesmedidas);
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		//$obvista->html = str_replace('{formulario_details}', $obvista->get_template('details',get_class($parametros)), $obvista->html); 
		$obvista->html = str_replace('{lstemp}', $lstempelado , $obvista->html); 
		$obvista->html = str_replace('{lstdepto}', $lstdptos , $obvista->html);
		$obvista->html = str_replace('{lstcategorias}', $lstcategorias , $obvista->html);
		$obvista->html = str_replace('{lstproyecto}',$lstproyectos , $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{anio}', date('Y') , $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->html = str_replace('{lstunimedida}', $lstunidades , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	public function get(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		
		$parametros->get($parametros_data);
		$data = array('ID'=>$parametros->ID);            
		$obvista->retornar_vista('buscar', $data);
	}
	
	public function delete(){
		$parametros = $this->set_obj();
		$xrequisicion = $this->set_obj_master();
		$obvista = new view_Parametros();
		$parametros->delete(get_class($parametros));
		$this->msg=$parametros->mensaje;
		$xrequisicion->view();
		
	}
	
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$data = $parametros->lis(get_class($parametros),1);
		$tagreplace = $parametros->render_etiquetas($data);
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['modificar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('modificar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->render_html($tagreplace);  
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
		$_REQUEST['ACEPTADA']='N';
		$_REQUEST['VALORREQ']= $_REQUEST['CANTIDAD'] * $_REQUEST['PRECIOUNI'];
		$parametros->save(get_class($parametros));
		$this->msg = $parametros->mensaje;
	
	}
	
	public function get_all($mensaje=''){
		$_REQUEST["filtro"]='NO';
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$mcampos = array($parametros->tableName().'.COD_CIA',$parametros->tableName().'.NUM_REQ',$parametros->tableName().'.COD_PROD', 'PRODUCTOS.NOMBRE',$parametros->tableName().'.CANTIDAD',$parametros->tableName().'.ANIO',$parametros->tableName().'.CORRELATIVO');
        $masx=implode($mcampos, ",");
        /*echo"<pre>";
			print_r($_REQUEST);
        echo"</pre>";*/
		$data = $parametros->lis2(get_class($parametros), 1, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros));
		$obvista->html = $obvista->get_template('listar',get_class($parametros)); 
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
	}
	
	public function get_ajax(){
		echo $lstproducto;
	}

}

/*$objecon =  new controller_reqdet();
	$objecon->handler();*/







?>
