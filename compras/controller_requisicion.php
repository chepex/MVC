<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_requisicion.php');
require_once('model_reqdet.php');
require_once('controller_reqdet.php');
require_once('../core/render_view_generic.php');
class controller_requisicion extends requisicion{
	
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva requisicion',
                      'buscar'=>'Buscar requisicion',
                      'borrar'=>'Eliminar una requisicion',
                      'modificar'=>'Modificar una requisicion',
                      'listar'=>'Lista de requisicion'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'compras/?ctl=controller_requisicion&act=set',
						'VIEW_GET_USER'=>'compras/?ctl=controller_requisicion&act=buscar',
						'VIEW_EDIT_USER'=>'compras/?ctl=controller_requisicion&act=modificar',
						'VIEW_DELETE_USER'=>'compras/?ctl=controller_requisicion&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../compras/?ctl=controller_requisicion&act=insert',
							'GET'=>'../compras/?ctl=controller_requisicion',
        'DELETE'=>'../compras/?ctl=controller_requisicion&act=delete',
        'EDIT'=>'../compras/?ctl=controller_requisicion&act=edit',
        'GET_ALL'=>'../compras/?ctl=controller_requisicion&act=get_all'
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
                        'update','get_all','listar','insert','get_ajax','view','view_rpt','finalizar_req');
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
				//$this->set();
				//$this->get_all($this->msg);
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
			case 'finalizar_req':
				$this->finalizar_req();
		}
	}
	
	public function set_obj() {
		$obj = new requisicion();
		return $obj;
	}
	
	public function set_obj_details() {
		$obj = new reqdet();
		return $obj;
	}
	
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$nre=$parametros->get_correl_key('requisicion',array("COD_CIA=".$_SESSION['cod_cia'],"ANIO=".date('Y')),"NUM_REQ");
		$objciau = $parametros->crea_objeto(array("CIAS_X_USUARIO"), "", array("USUARIO='".$_SESSION['usuario']."'"));
		$objemp = $parametros->crea_objeto(array("VWEMPLEADOS"), "",array("COD_EMP=". $objciau[0]["COD_EMP"]));
        $objdepto = $parametros->crea_objeto(array("DEPARTAMENTOS"), "",array("COD_CIA=".$_SESSION['cod_cia'],"COD_DEPTO=". $objemp[0]["COD_DEPTO"]));
        $objproy = $parametros->crea_objeto(array("PROYECTO"), "",array("COD_CIA=". $_SESSION['cod_cia'],"PROYECTO='".$objdepto[0]['PROYECTO']."'"));
        $objgruposinv = $parametros->crea_objeto(array("user_role_privs u", "inven.roles_inventario r", "inven.grupo_inventario g"),array("u.granted_role = r.rol" , "g.cod_cia = r.cod_cia" , "g.cod_grupo = r.codigo_grupo"),"",array("CODIGO_GRUPO, NOM_GRUPO || '-' || GRANTED_ROLE NOM_GRUPO"));
        $_SESSION['CUENTA_PROYECTO']= $objproy[0]['ENCARGADO'];
        $_SESSION['PROYECTO']=$objproy[0]['PROYECTO'];
		$objunidamedida1 = $parametros->crea_objeto(array("UNIDADES"),"",array("1=1"),array("CODIGO_UNIDAD","DESCRIPCION"));
		$objunidamedida2 = $parametros->crea_objeto(array("UNIDADES u","EQUIVALENCIAS e"),array("u.CODIGO_UNIDAD = e.CODIGO_EQUIVALENCIA"),"",array("e.CODIGO_UNIDAD","u.DESCRIPCION"));
		$obunidadesmedidas= array_merge($objunidamedida1,$objunidamedida2);
		$lstdptos = $parametros->get_lsoption("DEPARTAMENTOS", array("COD_DEPTO"=>"","NOM_DEPTO"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "COD_DEPTO"=>$objemp[0]['COD_DEPTO']));
        $lstempelado = $parametros->get_lsoption("VWEMPLEADOS", array("COD_EMP"=>"","NOMBRE_ISSS"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "STATUS"=>"'A'", "COD_DEPTO"=>$objemp[0]['COD_DEPTO']));
        $lstcategorias = $parametros->get_lsoption("CATEGORIAS", array("COD_CAT"=>"","NOM_CAT"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
        $lstproyectos =  $parametros->get_lsoption("PROYECTO", array("PROYECTO"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"PROYECTO"=> "'".$objdepto[0]['PROYECTO']."'"));
		$lstproyectos =  $parametros->get_lsoption("PROYECTO", array("PROYECTO"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"PROYECTO"=> "'".$objdepto[0]['PROYECTO']."'"));
		$lstunidades = $parametros->get_htmloptions($obunidadesmedidas);
		$lstgruposinv = $parametros->get_htmloptions($objgruposinv);
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{formulario_details}', $obvista->get_template('details',get_class($parametros)), $obvista->html); 
		$obvista->html = str_replace('{lstemp}', $lstempelado , $obvista->html); 
		$obvista->html = str_replace('{lstdepto}', $lstdptos , $obvista->html);
		$obvista->html = str_replace('{lstcategorias}', $lstcategorias , $obvista->html);
		$obvista->html = str_replace('{lstproyecto}',$lstproyectos , $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{NUM_REQ}', $nre[0][0] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{anio}', date('Y') , $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->html = str_replace('{lstunimedida}', $lstunidades , $obvista->html);
		$obvista->html = str_replace('{lstinvgrup}', $lstgruposinv , $obvista->html);
		$obvista->html = str_replace('{grafico}', $obvista->get_grafico('barras'), $obvista->html);
		$datagrafi= $parametros->get_datagrafico();
		$obvista->html = str_replace('{titulo_grafico}', "Presupuesto Disponible por Categoria", $obvista->html);
		$obvista->html = str_replace('{AxisY}', "Valores Expresados en USD($)", $obvista->html);
		$obvista->html = str_replace('{AxisX}', "Codigo de Categorias de Productos", $obvista->html);
		$obvista->html = str_replace('{popup-labelx}', "Categoria ", $obvista->html);
		$obvista->html = str_replace('{popup-labely}', "Presupuesto Disponible USD($) ", $obvista->html);
		$obvista->html = str_replace('{series_x}', $datagrafi['categorias'], $obvista->html);
		$obvista->html = str_replace('{series_y}', $datagrafi['disponible'], $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	public function get(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	public function delete(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$tiene_detalle =  $parametros->tiene_detalle($_REQUEST['COD_CIA'],$_REQUEST['NUM_REQ'],$_REQUEST['ANIO']);
		if(!$tiene_detalle){
			$parametros->delete(get_class($parametros));
			$this->msg=$parametros->mensaje;
		}else{
			$this->msg="No es Posible eliminar Requisicion, Tiene Detalles Asociados!!";
		}
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
		$obvista->html = str_replace('{NUM_REQ}', $_REQUEST['NUM_REQ'], $obvista->html);
		$obvista->html = str_replace('{COD_CIA}', $_REQUEST['COD_CIA'], $obvista->html);
		$obvista->html = str_replace('{ANIO}', $_REQUEST['ANIO'], $obvista->html);
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);
		$obvista->render_html($tagreplace);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		if(isset($_REQUEST['CKAUTORIZADO'])){
			if($_REQUEST['CKAUTORIZADO']=="1"){
				$estado='AUTORIZADA';
				$objciau = $parametros->crea_objeto(array("CIAS_X_USUARIO"), "", array("USUARIO='".$_SESSION['usuario']."'"));
				$_REQUEST['FECHA_AUTORIZADO']="SYSDATE";
				$_REQUEST['AUTORIZADO_POR']=$objciau[0]["COD_EMP"];
			}else{
				$estado='DECLINADA';
			}
			$listaemail = $parametros->correo_solicitante();
			$destinatario = $listaemail[0]['CORREO_USUARIO'];
			$asunto = 'La Requisicion No.'. $_REQUEST['NUM_REQ'];
			$tipo_requisicion= $_REQUEST['TIPO_REQ']=='G' ? 'GLOBAL' : 'EXTERNA';
			$bodymsg="Estimado Usuario: <br/>La Requisicion No: <strong>". $_REQUEST['NUM_REQ'] . "</strong>
					  <br/>Tipo " . $tipo_requisicion . " ha sido Procesada<br/>Su Estado es: ". $estado ."<br/>
					  Comentario de Compras:".$_REQUEST['COMENT_COMPRAS'] ."
					  <br/>Atte.MODULO DE COMPRAS<br/>Departamento de Informática.";
			$parametros->sendemail('ingresorequisiciones@caricia.com', $destinatario, $asunto, $bodymsg);
		}
		$parametros->update(get_class($parametros));
		$this->msg=$parametros->mensaje; 
	}
	
	public function insert(){
		$parametros = $this->set_obj();
		$detailsReq = $this->set_obj_details();
		$objciasxu = $parametros->crea_objeto(array("CIAS_X_USUARIO"),"",array("USUARIO='".$_SESSION['usuario']."'"));
		$_REQUEST['COD_EMP_ELAB']= $objciasxu[0]["COD_EMP"];
		$_REQUEST['AUTORIZADO_POR']= 'NULL';
		$_REQUEST['FECHA_AUTORIZADO']= 'NULL';
		$_REQUEST['STATUS']= 'G';
		$_REQUEST['USUARIO']= $_SESSION['usuario'];
		$_REQUEST['FECHA_ING']= 'SYSDATE';
		$_REQUEST['NO_FORMULARIO']= 'NULL';
		$_REQUEST['COMENT_COMPRAS']= 'NULL';
		$parametros->save(get_class($parametros));
		$this->msg=$detailsReq->mensaje;
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$objciau = $parametros->crea_objeto(array("CIAS_X_USUARIO"), "", array("USUARIO='".$_SESSION['usuario']."'"));
		$objemp = $parametros->crea_objeto(array("VWEMPLEADOS"), "",array("COD_EMP=". $objciau[0]["COD_EMP"]));
		$_REQUEST[$parametros->tableName().".COD_CIA"] = $_SESSION['cod_cia']; 
		$_REQUEST[$parametros->tableName().".ANIO"] = date('Y');//2012;
		$_REQUEST[$parametros->tableName().".CODDEPTO_SOL"] = $objemp[0]["COD_DEPTO"];
		$mcampos = array($parametros->tableName().'.COD_CIA',$parametros->tableName().'.NUM_REQ',$parametros->tableName().'.CODDEPTO_SOL','DEPARTAMENTOS.NOM_DEPTO',$parametros->tableName().'.FECHA_ING',$parametros->tableName().'.FECHA_AUTORIZADO',$parametros->tableName().'.OBSERVACIONES',$parametros->tableName().'.PROYECTO',$parametros->tableName().'.ANIO',$parametros->tableName().'.COD_CAT',$parametros->tableName().'.TIPO_REQ','PRIORIDADES.DESCRIPCION_PRIORIDAD');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 2, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html); 
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
	}
	
	public function view(){
		$parametros = $this->set_obj();
		$detreq = new controller_reqdet();
		$obvista = new view_Parametros();
		//$mcampos = array('COD_CIA','NUM_REQ','CODDEPTO_SOL', 'NOM_DEPTO','FECHA_ING','FECHA_AUTORIZADO','OBSERVACIONES','PROYECTO','ANIO','COD_CAT','TIPO_REQ','DESCRIPCION_PRIORIDAD');
        $mcampos = array($parametros->tableName().'.COD_CIA',
						 $parametros->tableName().'.NUM_REQ',
						 $parametros->tableName().'.CODDEPTO_SOL',
						 'DEPARTAMENTOS.NOM_DEPTO',
						 $parametros->tableName().'.FECHA_ING',
						 $parametros->tableName().'.FECHA_AUTORIZADO',
						 $parametros->tableName().'.OBSERVACIONES',
						 $parametros->tableName().'.PROYECTO',
						 $parametros->tableName().'.ANIO',
						 $parametros->tableName().'.COD_CAT',
						 $parametros->tableName().'.TIPO_REQ',
						 'PRIORIDADES.DESCRIPCION_PRIORIDAD'
						);
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 1, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),'',array("delete"=>"style='display:none;'","update"=>"style='display:none;'","view"=>"style='display:none;'","set"=>"style='display:none;'"));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
		$detreq->get_all();
		
	}
	
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$_REQUEST[$parametros->tableName().".COD_CIA"] = $_SESSION['cod_cia']; 
		$_REQUEST[$parametros->tableName().".ANIO"] = date('Y');//2012;
		//$mcampos = array('COD_CIA','NUM_REQ','CODDEPTO_SOL','NOM_DEPTO','FECHA_ING','FECHA_AUTORIZADO','OBSERVACIONES','PROYECTO','ANIO','COD_CAT','TIPO_REQ','DESCRIPCION_PRIORIDAD');
        $mcampos = array($parametros->tableName().'.COD_CIA',$parametros->tableName().'.NUM_REQ',$parametros->tableName().'.CODDEPTO_SOL','DEPARTAMENTOS.NOM_DEPTO',$parametros->tableName().'.FECHA_ING',$parametros->tableName().'.FECHA_AUTORIZADO',$parametros->tableName().'.OBSERVACIONES',$parametros->tableName().'.PROYECTO',$parametros->tableName().'.ANIO',$parametros->tableName().'.COD_CAT',$parametros->tableName().'.TIPO_REQ','PRIORIDADES.DESCRIPCION_PRIORIDAD');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 2, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html); 
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
	}
	
	public function finalizar_req(){
		$parametros = $this->set_obj();
		$objreq = $parametros->crea_objeto(array("REQUISICION RQ","CATEGORIAS CAT", "PRIORIDADES PRI","VWEMPLEADOS VWE","DEPARTAMENTOS DPTO"),
										   array("RQ.COD_CIA = CAT.COD_CIA", "RQ.COD_CAT = CAT.COD_CAT", "RQ.COD_PRIORIDAD = PRI.COD_PRIORIDAD", "RQ.COD_CIA = VWE.COD_CIA","RQ.EMP_SOL = VWE.COD_EMP","RQ.COD_CIA = DPTO.COD_CIA","RQ.CODDEPTO_SOL = DPTO.COD_DEPTO"),
										   array(" AND RQ.NUM_REQ='".$_REQUEST['NUM_REQ']."'","RQ.ANIO=".$_REQUEST['ANIO'], "RQ.COD_CIA=".$_REQUEST['COD_CIA']),
										   array("RQ.NUM_REQ","RQ.FECHA_ING","RQ.OBSERVACIONES","DECODE (RQ.TIPO_REQ,'E','EXTERNA','G','GLOBAL') TIPO_REQ","RQ.COD_PRIORIDAD","PRI.DESCRIPCION_PRIORIDAD",
												  "CAT.COD_CAT","CAT.NOM_CAT","VWE.COD_EMP","VWE.NOMBRE_ISSS","DPTO.COD_DEPTO","DPTO.NOM_DEPTO")
											);
		$objdetreq = $parametros->crea_objeto(array("REQDET RQD","PRODUCTOS PRO","UNIDADES UNI"),
											  array("RQD.COD_CIA = PRO.COD_CIA","RQD.COD_PROD = PRO.COD_PROD","RQD.CODIGO_UNIDAD = UNI.CODIGO_UNIDAD"),
											  array("AND RQD.NUM_REQ='".$_REQUEST['NUM_REQ']."'","RQD.ANIO=".$_REQUEST['ANIO'], "RQD.COD_CIA=".$_REQUEST['COD_CIA']),
											  array("RQD.NUM_REQ","RQD.COD_PROD","PRO.NOMBRE","UNI.DESCRIPCION","RQD.CANTIDAD","RQD.ESPECIFICACIONES")
											 );
		$html ="<!DOCTYPE html>
			<head>
					<link rel='stylesheet' type='text/css' href='http://192.168.10.235/webcaricia/caricia/181x/mvc_daniel/MVC/site_media/css/bootstrap/css/bootstrap.css'/>
					<meta charset='ISO-8859-15'>
			</head>
			<body>";
		$html.="<h5>Se ha ingresado una Nueva Requisicion de Compra<br/>
				  De tipo: <strong>". $objreq[0]['TIPO_REQ']. "</strong></h5>";
		$html.=$parametros->create_msghtml_header($objreq);
		$html.=$parametros->create_msghtml_details($objdetreq);
		$html.="<h4>Desea Autorizar esta Requisici&oacute;n?</h4><a href='http://192.168.10.235/webcaricia/caricia/181x/mvc_daniel/MVC/compras/?ctl=controller_requisicion&act=update&COD_CIA=".$_REQUEST['COD_CIA']."&NUM_REQ=".$_REQUEST['NUM_REQ']."&ANIO=".$_REQUEST['ANIO']."' class='btn'>SI</a>";
		$html.="</body></html>";
		$tipo_orden = $objreq[0]['TIPO_REQ']=='GLOBAL' ? 'OCG' : 'OCL';
		$listaemail = $parametros->correo_encargadodocumento($tipo_orden);
		$destinatario = $listaemail[0]['CORREO_USUARIO'];
		$asunto = 'Ingreso de Requisicion No.'. $_REQUEST['NUM_REQ'] . " <Correo Generado Automaticamente>";
		$parametros->sendemail('ingresorequisiciones@caricia.com', $destinatario, $asunto, $html);
	}
	
	public function get_ajax(){
		$parametros = $this->set_obj();
		if($_REQUEST['opt']=="COD_CAT"){
				$lstproducto = $parametros->get_lsoption("PRODUCTOS", array("COD_PROD"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "COD_CAT"=>"'".$_REQUEST['data']."'"));
		}
		if($_REQUEST['opt']=="COD_PROD"){
				$lstproducto = $parametros->get_lsoption("PRODUCTOS", array("COD_PROD"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "COD_CAT"=>"'".$_REQUEST['data']."'"));
		}
		echo $lstproducto;
	}

}


?>
