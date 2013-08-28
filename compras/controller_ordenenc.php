<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
require_once('model_requisicion.php');
require_once('model_ordenenc.php');
require_once('model_cotizacion.php');
require_once('../core/render_view_generic.php');
class controller_ordenenc extends ordenenc{
	
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva Orden de Compra',
                      'buscar'=>'Buscar Orden de Compra',
                      'borrar'=>'Eliminar una Orden de Compra',
                      'modificar'=>'Modificar una Orden de Compra',
                      'listar'=>'Lista de Orden de Compra'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'compras/?ctl=controller_ordenenc&act=set',
						'VIEW_GET_USER'=>'compras/?ctl=controller_ordenenc&act=buscar',
						'VIEW_EDIT_USER'=>'compras/?ctl=controller_ordenenc&act=modificar',
						'VIEW_DELETE_USER'=>'compras/?ctl=controller_ordenenc&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../compras/?ctl=controller_ordenenc&act=insert',
							'GET'=>'../compras/?ctl=controller_ordenenc',
        'DELETE'=>'../compras/?ctl=controller_ordenenc&act=delete',
        'EDIT'=>'../compras/?ctl=controller_ordenenc&act=edit',
        'GET_ALL'=>'../compras/?ctl=controller_ordenenc&act=get_all'
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
                        'update','get_all','listar','insert','get_ajax','view','view_rpt','view_cotizacion');
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
			case 'view_cotizacion':
				$this->view_cotizacion();
				break;
		}
	}
	
	public function set_obj() {
		$obj = new ordenenc();
		return $obj;
	}
	
	/*public function set_obj_details() {
		$obj = new reqdet();
		return $obj;
	}*/
	
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$norden=$parametros->get_correl_key('ordenenc',array("COD_CIA=".$_SESSION['cod_cia']),"num_orden");
		$lstproveedor = $parametros->get_lsoption("PROVEEDORES", array("COD_PROV"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lstrequis = $parametros->get_lsoption("REQUISICION", array("NUM_REQ"=>"","OBSERVACIONES"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "ANIO"=>date('Y')));
		$lstcategorias = $parametros->get_lsoption("CATEGORIAS", array("COD_CAT"=>"","NOM_CAT"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lstdptos = $parametros->get_lsoption("DEPARTAMENTOS", array("COD_DEPTO"=>"","NOM_DEPTO"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lstempelado = $parametros->get_lsoption("VWEMPLEADOS", array("COD_EMP"=>"","NOMBRE_ISSS"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "STATUS"=>"'A'"));
		/*$objciau = $parametros->crea_objeto(array("CIAS_X_USUARIO"), "", array("USUARIO='".$_SESSION['usuario']."'"));
		$objemp = $parametros->crea_objeto(array("VWEMPLEADOS"), "",array("COD_EMP=". $objciau[0]["COD_EMP"]));
        $objdepto = $parametros->crea_objeto(array("DEPARTAMENTOS"), "",array("COD_CIA=".$_SESSION['cod_cia'],"COD_DEPTO=". $objemp[0]["COD_DEPTO"]));
        $objproy = $parametros->crea_objeto(array("PROYECTO"), "",array("COD_CIA=". $_SESSION['cod_cia'],"PROYECTO='".$objdepto[0]['PROYECTO']."'"));
        $_SESSION['CUENTA_PROYECTO']= $objproy[0]['ENCARGADO'];
        $_SESSION['PROYECTO']=$objproy[0]['PROYECTO'];
		$objunidamedida1 = $parametros->crea_objeto(array("UNIDADES"),"",array("1=1"),array("CODIGO_UNIDAD","DESCRIPCION"));
		$objunidamedida2 = $parametros->crea_objeto(array("UNIDADES u","EQUIVALENCIAS e"),array("u.CODIGO_UNIDAD = e.CODIGO_EQUIVALENCIA"),"",array("e.CODIGO_UNIDAD","u.DESCRIPCION"));
		$obunidadesmedidas= array_merge($objunidamedida1,$objunidamedida2);
		$lstdptos = $parametros->get_lsoption("DEPARTAMENTOS", array("COD_DEPTO"=>"","NOM_DEPTO"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "COD_DEPTO"=>$objemp[0]['COD_DEPTO']));
        $lstempelado = $parametros->get_lsoption("VWEMPLEADOS", array("COD_EMP"=>"","NOMBRE_ISSS"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "STATUS"=>"'A'", "COD_DEPTO"=>$objemp[0]['COD_DEPTO']));
        $lstcategorias = $parametros->get_lsoption("CATEGORIAS", array("COD_CAT"=>"","NOM_CAT"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
        $lstproyectos =  $parametros->get_lsoption("PROYECTO", array("PROYECTO"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"PROYECTO"=> "'".$objdepto[0]['PROYECTO']."'"));
		$lstunidades = $parametros->get_htmloptions($obunidadesmedidas);*/
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html); 
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{NUM_ORDEN}', $norden[0][0] , $obvista->html);
		$obvista->html = str_replace('{lstproveedores}', $lstproveedor, $obvista->html);
		$obvista->html = str_replace('{lstrequisicion}', $lstrequis, $obvista->html);
		$obvista->html = str_replace('{lstcategorias}', $lstcategorias , $obvista->html);
		$obvista->html = str_replace('{lstemp}', $lstempelado , $obvista->html); 
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{lstdepto}', $lstdptos , $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
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
		/*$tiene_detalle =  $parametros->tiene_detalle($_REQUEST['COD_CIA'],$_REQUEST['NUM_REQ'],$_REQUEST['ANIO']);
		if(!$tiene_detalle){
			$parametros->delete(get_class($parametros));
			$this->msg=$parametros->mensaje;
		}else{
			$this->msg="No es Posible eliminar Requisicion, Tiene Detalles Asociados!!";
		}*/
	}
	
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		
		/*$data = $parametros->lis(get_class($parametros),1);
		$tagreplace = $parametros->render_etiquetas($data);
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['modificar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('modificar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->html = str_replace('{NUM_REQ}', $_REQUEST['NUM_REQ'], $obvista->html);
		$obvista->html = str_replace('{COD_CIA}', $_REQUEST['COD_CIA'], $obvista->html);
		$obvista->html = str_replace('{ANIO}', $_REQUEST['ANIO'], $obvista->html);
		$obvista->render_html($tagreplace);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();*/
	}
	
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*if(isset($_REQUEST['CKAUTORIZADO'])){
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
			$bodymsg="La Requisicion No: ". $_REQUEST['NUM_REQ'] . " de Tipo " . $tipo_requisicion . " ha sido Procesada, y su estado es: ". $estado;
			$parametros->sendemail('ingresorequisiciones@caricia.com', $destinatario, $asunto, $bodymsg);
		}
		$parametros->update(get_class($parametros));
		$this->msg=$parametros->mensaje; */
	}
	
	public function insert(){
		$parametros = $this->set_obj();
		$objciau = $parametros->crea_objeto(array("CIAS_X_USUARIO"), '',array("USUARIO='".$_SESSION['usuario']."'"));
		$_REQUEST['COD_EMP']= $objciau[0]['COD_EMP'];
		if(isset($_REQUEST['NUM_REQ']) && isset($_REQUEST['ckporden'])){
					$this->generar_ordencompra();
		}
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*$objciau = $parametros->crea_objeto(array("CIAS_X_USUARIO"), "", array("USUARIO='".$_SESSION['usuario']."'"));
		$objemp = $parametros->crea_objeto(array("VWEMPLEADOS"), "",array("COD_EMP=". $objciau[0]["COD_EMP"]));
		$_REQUEST[$parametros->tableName().".COD_CIA"] = $_SESSION['cod_cia']; 
		$_REQUEST[$parametros->tableName().".ANIO"] = date('Y');//2012;
		$_REQUEST[$parametros->tableName().".CODDEPTO_SOL"] = $objemp[0]["COD_DEPTO"];*/
		$mcampos = array('COD_CIA','NUM_ORDEN','FECHA_ORDEN','SOLICITANTE','COD_PROV','OBSERVACIONES','PROYECTO','AUTORIZADO','FECHAUTORIZADO');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis(get_class($parametros), 0, $masx);
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
		/*$detreq = new controller_reqdet();
		$obvista = new view_Parametros();
		//$mcampos = array('COD_CIA','NUM_REQ','CODDEPTO_SOL', 'NOM_DEPTO','FECHA_ING','FECHA_AUTORIZADO','OBSERVACIONES','PROYECTO','ANIO','COD_CAT','TIPO_REQ','DESCRIPCION_PRIORIDAD');
        $mcampos = array($parametros->tableName().'.COD_CIA',$parametros->tableName().'.NUM_REQ',$parametros->tableName().'.CODDEPTO_SOL','DEPARTAMENTOS.NOM_DEPTO',$parametros->tableName().'.FECHA_ING',$parametros->tableName().'.FECHA_AUTORIZADO',$parametros->tableName().'.OBSERVACIONES',$parametros->tableName().'.PROYECTO',$parametros->tableName().'.ANIO',$parametros->tableName().'.COD_CAT',$parametros->tableName().'.TIPO_REQ','PRIORIDADES.DESCRIPCION_PRIORIDAD');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 1, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html);  
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{formulario_details}', '', $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
		$detreq->get_all();*/
		
	}
	
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		/*$_REQUEST[$parametros->tableName().".COD_CIA"] = $_SESSION['cod_cia']; 
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
		$obvista->retornar_vista();*/
	}
	
	public function view_cotizacion(){
		$objcotizacion = new cotizacion();
		$arrayCotizacion = $objcotizacion->definir_cotizaciones($_REQUEST['NUM_REQ'], date('Y'));
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:12px;'>
						<tr>
							<th colspan='10'>Selecci&oacute;n de Articulos Requici&oacute;n No.".$arrayCotizacion[0]["NUM_REQ"]."</th>
						</tr>
						<tr>
							<th>Num<br/>Req.</th>
							<th>Correlativo</th>
							<th>Cod.<br/>Prov</th>
							<th>Nombre</th>
							<th>Cod<br/>Prod.</th>
							<th>Descripci&oacute;n</th>
							<th>Cantidad</th>
							<th>Precio U.</th>
							<th>Valor<br/>Req.</th>
							<th>*</th>
						</tr>";
			foreach ($arrayCotizacion as $mks){
					$html .= "<tr class='tfl'>
									<td>".$mks["NUM_REQ"]."</td>
									<td>".$mks["CORRELATIVO"]."</td>
									<td>".$mks["COD_PROV"]."</td>
									<td style='width:17%'>".$mks["NOMBRE"]."</td>
									<td>".$mks["COD_PROD"]."</td>
									<td style='width:17%'>".$mks["NOMBRE_PROD"]."</td>
									<td>".number_format($mks["CANTIDAD"], 2, '.', '')."</td>
									<td>$".number_format($mks["PRECIOUNI"], 2, '.', '')."</td>
									<td>$".number_format($mks["VALORREQ"], 2, '.', '')."</td>
									<td><input type='checkbox' name='ckporden[]' id='ckporden-".$mks["CORRELATIVO"]."' value='CTD.CORRELATIVO=".$mks["CORRELATIVO"]."|CTD.COD_PROD=".$mks["COD_PROD"]."|CTD.COD_PROV=".$mks["COD_PROV"]."' req='".$mks["NUM_REQ"]."'></td>
							  </tr>";
			}
		$html .= "</table>";
		echo $html;
	}
	
	public function get_ajax(){
		$parametros = $this->set_obj();
		if($_REQUEST['opt']=="CODDEPTO_SOL"){
			$objdepto = $parametros->crea_objeto(array("DEPARTAMENTOS"), "", array("COD_CIA=".$_SESSION['cod_cia'], "COD_DEPTO=".$_REQUEST['data'].""));
			$lstproyectos =  $parametros->get_lsoption("PROYECTO", array("PROYECTO"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"PROYECTO"=> "'".$objdepto[0]['PROYECTO']."'"));
			echo $lstproyectos;
		}
	}

}


?>
