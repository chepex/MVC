<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
require_once('model_reqdet.php');
require_once('controller_requisicion.php');
require_once('../core/render_view_generic.php');
#Controlador de Detalle de Orden de Compra
class controller_reqdet extends reqdet{
	
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
							'SET'=>'../compras/controller_requisicion.php?act=insert',
							'GET'=>'../compras/controller_requisicion.php',
        'DELETE'=>'../compras/controller_requisicion.php?act=delete',
        'EDIT'=>'../compras/?ctl=controller_reqdet&act=edit',
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
		}
	}

	#Definicion de una instancia del Modelo del Controlador reqdet detalle de la requisicion
	public function set_obj() {
		$obj = new reqdet();
		return $obj;
	}
	
	#Definicion de una Instancia del Modelo de requisicion encabezado de la orden de rquisicion
	public function set_obj_master() {
		$obj = new requisicion();
		return $obj;
	}
	
	#Método que dibuja el formulario para insercion del detalle de la Requisicion
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
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		
		$parametros->get($parametros_data);
		$data = array('ID'=>$parametros->ID);            
		$obvista->retornar_vista('buscar', $data);
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function delete(){
		$parametros = $this->set_obj();
		$xrequisicion = $this->set_obj_master();
		$obvista = new view_Parametros();
		$parametros->delete(get_class($parametros));
		$this->msg=$parametros->mensaje;
		$xrequisicion->view();
		
	}
	
	#Método que dibuja el formulario para la modificacion de detalle de Requisicion
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$objdetreq=$parametros->crea_objeto(array('reqdet'),'',
											array('COD_CIA='.$_REQUEST['COD_CIA'],
												  'NUM_REQ='.$_REQUEST['NUM_REQ'],
												  'ANIO='.$_REQUEST['ANIO'],
												  'COD_PROD='.$_REQUEST['COD_PROD']
												  ));
												  
		$lstproducto=$parametros->get_lsoption('PRODUCTOS',
											array('COD_PROD'=>"",'NOMBRE'=>""),
											array('COD_CIA'=>$_REQUEST['COD_CIA'],
												  'COD_PROD'=>$_REQUEST['COD_PROD']
												  ));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['modificar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('modificar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{NUM_REQ}', $objdetreq[0]['NUM_REQ'], $obvista->html);
		$obvista->html = str_replace('{COD_CIA}', $objdetreq[0]['COD_CIA'], $obvista->html);
		$obvista->html = str_replace('{ANIO}', $objdetreq[0]['ANIO'], $obvista->html);
		$obvista->html = str_replace('{CANTIDAD}', $objdetreq[0]['CANTIDAD'], $obvista->html);
		$obvista->html = str_replace('{lstproducto}', $lstproducto, $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	#Método que modifica el detalle de la Requisicion, notifica a compras de que la Requisicion sufrio cambios
	public function edit(){
		$parametros = $this->set_obj();
		$requisicion = new requisicion();
		$xrequisicion = new controller_requisicion();
		$obvista = new view_Parametros();
		$parametros->update(get_class($parametros));
		$this->msg=$parametros->mensaje; 
		$listaemail = $requisicion->correo_solicitante();
		$destinatario = $listaemail[0]['CORREO_USUARIO'];
		$asunto = 'Modificacion de Requisicion No.'. $_REQUEST['NUM_REQ'];
		$bodymsg="Estimado Usuario: <br/>La Requisicion No: <strong>". $_REQUEST['NUM_REQ'] . "</strong>
					  <br/>ha sido Modificada por el solicitante, verificar para su revisi&oacute;n<br/>";
		$parametros->sendemail('ingresorequisiciones@caricia.com', $destinatario, $asunto, $bodymsg);
		$xrequisicion->view();
	}
	
	#Método que se encarga del la insercion del detalle de la requisicion
	public function insert(){
		$parametros = $this->set_obj();
		$parametros->save(get_class($parametros));
		$this->msg = $parametros->mensaje;
	
	}
	
	#Método que dibuja la tabla crud del detalle de la Requisicion
	public function get_all($mensaje=''){
		$_REQUEST["filtro"]='NO';
		$parametros = $this->set_obj();
		$objordencompra = $parametros->crea_objeto(array("ORDENENC"), "", array("NUM_REQ='".$_REQUEST['NUM_REQ']."'","ANIO=".$_REQUEST['ANIO'],"COD_CIA=".$_REQUEST['COD_CIA']));
		if(count($objordencompra) > 0){
			$propiedadbtn="style='display:none;'";
		}else{
			$propiedadbtn="style='display:inline;'";
		}
		$obvista = new view_Parametros();
		$mcampos = array($parametros->tableName().'.COD_CIA',$parametros->tableName().'.NUM_REQ',$parametros->tableName().'.COD_PROD', 'PRODUCTOS.NOMBRE',$parametros->tableName().'.CANTIDAD', 'UNIDADES.DESCRIPCION',$parametros->tableName().'.ESPECIFICACIONES',$parametros->tableName().'.ANIO');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 3, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),'',array("update"=>$propiedadbtn,"delete"=>"style='display:none;'", "view"=>"style='display:none;'", "set"=>"style='display:none;'"));
		$obvista->html = $obvista->get_template('listar',get_class($parametros)); 
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
	}
	
	#Método para invocaciones via Ajax
	public function get_ajax(){
		echo $lstproducto;
	}

}


?>
