<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
require_once('model_cotizacion.php');
require_once('controller_requisicion.php');
require_once('../core/render_view_generic.php');
require_once('../core/html2pdf/html2pdf.class.php');
class controller_cotizacion extends cotizacion{
	
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nueva Cotizacion',
                      'buscar'=>'Buscar Cotizacion',
                      'borrar'=>'Eliminar una Cotizacion',
                      'modificar'=>'Modificar una Cotizacion',
                      'listar'=>'Lista de Cotizacion'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'compras/?ctl=controller_cotizacion&act=set',
						'VIEW_GET_USER'=>'compras/?ctl=controller_cotizacion&act=buscar',
						'VIEW_EDIT_USER'=>'compras/?ctl=controller_cotizacion&act=modificar',
						'VIEW_DELETE_USER'=>'compras/?ctl=controller_cotizacion&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../compras/?ctl=controller_cotizacion&act=insert',
							'GET'=>'../compras/?ctl=controller_cotizacion',
        'DELETE'=>'../compras/?ctl=controller_cotizacion&act=delete',
        'EDIT'=>'../compras/?ctl=controller_cotizacion&act=edit',
        'GET_ALL'=>'../compras/?ctl=controller_cotizacion&act=get_all',
        'VIEW_RPT'=>'../compras/?ctl=controller_cotizacion&act=view_rpt',
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
                        'update','get_all','listar','insert','get_ajax','view','view_rpt');
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
		}
	}
	
	public function set_obj() {
		$obj = new cotizacion();
		return $obj;
	}
	
	public function set_objreq() {
		$obj = new requisicion();
		return $obj;
	}
	

	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$requisicion =  $this->set_objreq();
		$_REQUEST['COD_CIA']= $_SESSION['cod_cia'];
		$mcampos = array($requisicion->tableName().'.COD_CIA',$requisicion->tableName().'.NUM_REQ',$requisicion->tableName().'.CODDEPTO_SOL','DEPARTAMENTOS.NOM_DEPTO',$requisicion->tableName().'.FECHA_ING',$requisicion->tableName().'.FECHA_AUTORIZADO',$requisicion->tableName().'.OBSERVACIONES',$requisicion->tableName().'.PROYECTO',$requisicion->tableName().'.ANIO',$requisicion->tableName().'.COD_CAT',$requisicion->tableName().'.TIPO_REQ','PRIORIDADES.DESCRIPCION_PRIORIDAD');
        $masx=implode($mcampos, ",");
		$data = $requisicion->lis2(get_class($requisicion), 1, $masx);
		$rendertable = $requisicion->render_table_crud(get_class($requisicion));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{formulario_details}', $obvista->get_template('details',get_class($parametros)), $obvista->html); 
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{anio}', date('Y') , $obvista->html);
		$lstrequis = $parametros->get_lsoption("REQUISICION", array("NUM_REQ"=>"","OBSERVACIONES"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "ANIO"=>date('Y')));
		$lstproveedor = $parametros->get_lsoption("PROVEEDORES", array("COD_PROV"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$objunidamedida1 = $parametros->crea_objeto(array("UNIDADES"),"",array("1=1"),array("CODIGO_UNIDAD","DESCRIPCION"));
		$objunidamedida2 = $parametros->crea_objeto(array("UNIDADES u","EQUIVALENCIAS e"),array("u.CODIGO_UNIDAD = e.CODIGO_EQUIVALENCIA"),"",array("e.CODIGO_UNIDAD","u.DESCRIPCION"));
		$obunidadesmedidas= array_merge($objunidamedida1,$objunidamedida2);
		$lstunidades = $parametros->get_htmloptions($obunidadesmedidas);
		$obvista->html = str_replace('{lstrequisicion}', $lstrequis, $obvista->html);
		$obvista->html = str_replace('{lstproveedores}', $lstproveedor, $obvista->html);
		$obvista->html = str_replace('{crud_requisicion}', $obvista->get_template('listar',get_class($requisicion)), $obvista->html);  
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{lstunimedida}', $lstunidades, $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
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
		$obvista = new view_Parametros();
		//$parametros->delete('pvc_other_costs',$parametros_data);
		$this->msg=$parametros->mensaje;
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
		$_REQUEST['ACEPTADA']='N';
		$_REQUEST['USUARIO']='USER';
		$_REQUEST['FECHA_ING']='SYSDATE';
		$parametros->save(get_class($parametros));
		
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$_REQUEST[$parametros->tableName().'.ANIO']= date('Y');
		$_REQUEST[$parametros->tableName().'.COD_CIA']= $_SESSION['cod_cia'];
		$mcampos = array($parametros->tableName().'.COD_CIA',$parametros->tableName().'.NUM_REQ',$parametros->tableName().'.ANIO',$parametros->tableName().'.CORRELATIVO',$parametros->tableName().'.FECHA','PROVEEDORES.COD_PROV','PROVEEDORES.NOMBRE',$parametros->tableName().'.ACEPTADA',$parametros->tableName().'.FECHA_ING');
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 2, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),'',array("delete"=>"style='display:none;'","update"=>"style='display:none;'","set"=>"style='display:none;'"));
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
		$obvista = new view_Parametros();
		$lstrequis = $parametros->get_lsoption("REQUISICION", array("NUM_REQ"=>"","OBSERVACIONES"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "ANIO"=>date('Y')));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('comparativo',get_class($parametros)), $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = str_replace('{formulario_details}', " ", $obvista->html); 
		$obvista->html = str_replace('{lstrequisicion}', $lstrequis, $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{anio}', date('Y') , $obvista->html);
		$obvista->retornar_vista();
		
	}
	
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$table= $parametros->comparativo_precios($_REQUEST['NUM_REQ'], $_REQUEST['ANIO']);
		$html ="<!DOCTYPE html>
			<head>
					<link rel='stylesheet' type='text/css' href='../site_media/css/bootstrap/css/bootstrap.css'/>
					<meta charset='ISO-8859-15'>
					<style type='text/css'>
						.tbl {border-collapse:collapse}
						.tfl {border:1px solid black}
					</style>
					<title>LISTADO COMPARATIVOS DE PRECIOS</title>
			</head>
			<body>";
		$html .="<br/><br/><br/><br/><div id='contenedor_pg' style='height:3000px;'>";
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858'>
						<tr>
							<th colspan='9'>Listado Comparativo de Precios</th>
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
						</tr>";
			foreach ($table as $mks){
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
							  </tr>";
			}
		$html .= "</table></div><!-- Cierre div contenedor_pg -->
				</body></html>";
		//echo $html;
		try{
			$html2pdf = new HTML2PDF('P','letter','es',false,'ISO-8859-15',3);
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($html, isset($_GET['vuehtml']));
			$html2pdf->Output('reporte.pdf');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
		
	}
	
	public function get_ajax(){
		$parametros = $this->set_obj();
		if($_REQUEST['opt']=="NUM_REQ" ){
			$objreq = $parametros->crea_objeto(array("REQUISICION"), "", array("NUM_REQ='".$_REQUEST['data']."'","ANIO=".date('Y')));
			$lstproducto = $parametros->get_lsoption("PRODUCTOS", array("COD_PROD"=>"","NOMBRE"=>""), array("COD_CIA"=>$_SESSION['cod_cia'], "COD_CAT"=>"'".$objreq[0]['COD_CAT']."'"));
			$NUMCORREL = $parametros->get_correl_key('COTIZACION',array("ANIO=".date('Y'),"NUM_REQ=".$_REQUEST['data']),'CORRELATIVO');
			$data= array("lstpro"=>$lstproducto,"correl"=> $NUMCORREL[0][0]);
			echo json_encode($data);
		}
	}

}


?>
