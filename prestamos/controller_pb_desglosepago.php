<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_desglosepago.php');
require_once('../core/render_view_generic.php');
#Controlador de de Desglose de Pago
class controller_pb_desglosepago extends pb_desglosepago{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Desglose de pago',
                      'buscar'=>'Buscar Desglose de pago',
                      'borrar'=>'Eliminar Desglose de pago',
                      'modificar'=>'Modificar Desglose de pago',
                      'listar'=>'Lista Desglose de pago'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_desglosepago&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_desglosepago&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_desglosepago&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_desglosepago&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_desglosepago&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_desglosepago',
        'DELETE'=>'../prestamos/?ctl=controller_pb_desglosepago&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_desglosepago&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_desglosepago&act=get_all'
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
			#Peticiones definidas para el controlador desglose de pago
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
				$this->get_all($this->msg);
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
	
	#Definicion de una instancia del Modelo del Controlador de desglose de pago
	public function set_obj() {
		$obj = new pb_desglosepago();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para insercion, no se utiliza
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
	}
	
	#Método que elimina el detalle de desglose de pago
	public function delete(){
		$_REQUEST['COD_CIA'] = $_SESSION['cod_cia'];
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$parametros->delete(get_class($parametros));
		$this->msg=$parametros->mensaje;
		$this->view();
	}
	
	#Método generico que dibuja el formulario para modificacion, no se utiliza
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico que procesa la actualizacion de la informacion. no se utiliza
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico, que permite la insercion de la indormacion en la tabla de modelo
	public function insert(){
		$parametros = $this->set_obj();
		$_REQUEST['COD_CIA'] = $_SESSION['cod_cia'];
		$_REQUEST['COD_DETDESGLOSE'] = $this->nextval_seqdet();
		
		$objcuota = $parametros->crea_objeto(array("pb_detalleprestamos"),"", array("COD_CIA=".$_SESSION['cod_cia'], "COD_CUOTA=".$_REQUEST['COD_CUOTA']));
		$objprovision = $parametros->crea_objeto(array("pb_provisioninteres"),"", array("COD_CIA=".$_SESSION['cod_cia'], "COD_CUOTA=".$_REQUEST['COD_CUOTA']), array("pb_provisioninteres.VALOR_PROVISION"));
		$objpagado = $parametros->crea_objeto(array("pb_desglosepago"),"", array("COD_CIA=".$_SESSION['cod_cia'], "COD_CUOTA=".$_REQUEST['COD_CUOTA']), array("nvl(sum(pb_desglosepago.VALOR_PROVISION),0) ABONOS_PROVISION", "nvl(sum(pb_desglosepago.VALOR_GASTO),0) ABONOS_GASTO", "nvl(sum(pb_desglosepago.VALOR_CAPITAL),0) ABONOS_CAPITAL"));
		$tinterespagado = $objpagado[0]['ABONOS_PROVISION'] + $objpagado[0]['ABONOS_GASTO'];
		//Si en ningun desglose de pago se ha pagado interes, o no se ha realizado pagos para cuota
		if($tinterespagado == 0){
			//Si el lo abonado es mayor que el Valor de interes de la cuota
			if($_REQUEST['VALOR_ABONO'] > $objcuota[0]['VALOR_INTERES']){
				$_REQUEST['VALOR_CAPITAL'] =  $_REQUEST['VALOR_ABONO'] - $objcuota[0]['VALOR_INTERES'];
				$_REQUEST['VALOR_GASTO'] = $objcuota[0]['VALOR_INTERES'] - $objprovision[0]['VALOR_PROVISION']; 
				$_REQUEST['VALOR_PROVISION'] = $objprovision[0]['VALOR_PROVISION']; 
			//Si el valor a abonar es menor que el valor de los intereses, no se abona nada a capital
			}else{
				$_REQUEST['VALOR_CAPITAL'] = 0;
				//si el abono es mayor que el valor del gasto se paga toda la provision, y el resto al gasto
				if($_REQUEST['VALOR_ABONO'] > $objprovision[0]['VALOR_PROVISION']){
					$_REQUEST['VALOR_PROVISION'] = $objprovision[0]['VALOR_PROVISION'];
					$_REQUEST['VALOR_GASTO'] = $_REQUEST['VALOR_ABONO'] - $objprovision[0]['VALOR_PROVISION'];
				//si el valor del abono no cubre el gasto se abona todo a la provision
				}else{
					$_REQUEST['VALOR_GASTO'] = 0;
					$_REQUEST['VALOR_PROVISION'] = $_REQUEST['VALOR_ABONO'];
				}
			}
		}else{
			if($tinterespagado == $objcuota[0]['VALOR_INTERES']){
				$_REQUEST['VALOR_CAPITAL'] = $_REQUEST['VALOR_ABONO'];
				$_REQUEST['VALOR_GASTO'] = 0;
				$_REQUEST['VALOR_PROVISION'] = 0;
			}else{
				$restaxpogarinteres = $objcuota[0]['VALOR_INTERES'] - $tinterespagado;
				if($_REQUEST['VALOR_ABONO'] > $restaxpogarinteres){
					$_REQUEST['VALOR_CAPITAL'] = $_REQUEST['VALOR_ABONO'] - $restaxpogarinteres ;
					if($objpagado[0]['ABONOS_PROVISION'] == $objprovision[0]['VALOR_PROVISION']){
						$_REQUEST['VALOR_GASTO'] = $restaxpogarinteres;
						$_REQUEST['VALOR_PROVISION'] = 0;
					}else{
						$resporpagarprovision = $objprovision[0]['VALOR_PROVISION'] - $objpagado[0]['ABONOS_PROVISION'];
						if($restaxpogarinteres > $resporpagarprovision){
							$_REQUEST['VALOR_GASTO'] = $restaxpogarinteres - $resporpagarprovision;
							$_REQUEST['VALOR_PROVISION'] = $resporpagarprovision;
						}else{
							$_REQUEST['VALOR_GASTO'] = 0;
							$_REQUEST['VALOR_PROVISION'] = $restaxpogarinteres;
						}
					}					
				}else{
						$_REQUEST['VALOR_CAPITAL'] = 0;
						if($objpagado[0]['ABONOS_PROVISION'] == $objprovision[0]['VALOR_PROVISION']){
							$_REQUEST['VALOR_GASTO'] = $restaxpogarinteres;
							$_REQUEST['VALOR_PROVISION'] = 0;
						}else{
							$resporpagarprovision = $objprovision[0]['VALOR_PROVISION'] - $objpagado[0]['ABONOS_PROVISION'];
							if($restaxpogarinteres > $resporpagarprovision){
								$_REQUEST['VALOR_GASTO'] = $restaxpogarinteres - $resporpagarprovision;
								$_REQUEST['VALOR_PROVISION'] = $resporpagarprovision;
							}else{
								$_REQUEST['VALOR_GASTO'] = 0;
								$_REQUEST['VALOR_PROVISION'] = $restaxpogarinteres;
							}
						}		
				}
			}
		}
		$parametros->save(get_class($parametros));
		$this->view();
	}
	
	#Método Generico que devuelve una tbla CRUD del modelo, no se utiliza
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
	}
	
	#Método que permite visualizar informacion, Lista el desglose de pago, que puede tener una cuota
	public function view(){
		$parametros = $this->set_obj();
		$Desglosepago = $this->listar_desglosepago($_REQUEST['COD_CIA'], $_REQUEST['COD_CUOTA'],$_REQUEST['COD_DESGLOSE']);
		$acuabono=0;
		$html .="<table class='table table-striped tbl' border='0.5px' bordercolor='#585858' style='font-size:10px;width:60%;'>
						<thead>
						<tr>
							<th colspan='5'>Couta se Pagar&aacute; as&iacute;:</th>
						</tr>
						<tr>
							<th>Banco</th>
							<th>Pago A trav&eacute;s de</th>
							<th>Cuenta</th>
							<th>Chequera</th>
							<th>Por el valor</th>
							<th>*</th>
						</tr></thead><tbody>";
			foreach ($Desglosepago as $mks){
					$html .= "<tr class='tfl'>
									<td>
										".$mks["NOM_BANCO"]."
									</td>
									<td>
										".$mks["PAGO_ATRAVES"]."
									</td>
									<td>
										".$mks["COD_CUENTA"]."
									</td>
									<td>
										".$mks["SECUENCIA"]."
									</td>
									<td>
										".number_format($mks["VALOR_ABONO"],2,'.',',')."
									</td>
									<td>
										<a href='#' id='deldesglose' title='Eliminar Desglose' class='BTNDESGLOSEX btn btn-danger' COD_DETDESGLOSE='".$mks["COD_DETDESGLOSE"]."' COD_CUOTA='".$mks["COD_CUOTA"]."' VALOR_ABONO='".$mks["VALOR_ABONO"]."' NUMID='".$_REQUEST['NUMID']."'><i class=' icon-trash icon-white'></i></a>
									</td>									
							  </tr>";
							  $acuabono = number_format($acuabono + $mks["VALOR_ABONO"],2,'.','');
			}
		$html .= "</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th>TOTAL A PAGAR:</th>
								<th>$".number_format($acuabono,2,'.',',')."</th>
							</tr>
						</tfoot>
					</table>";
		$data= array("tbldeglosepago"=>$html,"totalabono"=>$acuabono);
		echo json_encode($data);
	}
	
	#Método generico que permite dibujar formulario para reportes, no se utiliza
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método Generico que permite realizar peticiones especificas via ajax, no se utiliza
	public function get_ajax(){
		$parametros = $this->set_obj();	
	}

}


?>
