<?php


error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_ncprestamos.php');
require_once('../core/render_view_generic.php');
#Controlador de pb_ncprestamos
class controller_pb_ncprestamos extends pb_ncprestamos{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Recibo',
                      'buscar'=>'Buscar Recibo',
                      'borrar'=>'Eliminar Recibo',
                      'modificar'=>'Modificar Recibo',
                      'listar'=>'Lista de Recibo'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_ncprestamos&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_ncprestamos&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_ncprestamos&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_ncprestamos&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_ncprestamos&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_ncprestamos',
        'DELETE'=>'../prestamos/?ctl=controller_pb_ncprestamos&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_ncprestamos&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_ncprestamos&act=get_all'
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
			#Peticiones definidas para el controlador Requisicion
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
	
	#Definicion de una instancia del Modelo del Controlador requisicion encabezado de la requisicion
	public function set_obj() {
		$obj = new pb_ncprestamos();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion del encabezado de la Requisicion
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	#Método generico definido en el controlador, no se utiliza
	public function get(){
		$parametros = $this->set_obj();
	}
	
	#Método que elimina el detalle de la requisicion, sino tiene detalle
	public function delete(){
		$parametros = $this->set_obj();
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
		$parametros->save(get_class($parametros));
	}
	
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
	}
	
	public function view(){
		$parametros = $this->set_obj();
		$partidadesembolso = $this->listar_partidadesembolso($_REQUEST['COD_CIA'], $_REQUEST['COD_PRESTAMO']);
		$acucargo=0;
		$acuabono=0;
		$html .="<table class='table table table-bordered' border='0.5px' bordercolor='#585858' style='font-size:10px;' align='center'>
						<thead>
						<tr>
							<th colspan='8'>Partida de Desembolso</th>
						</tr>
						<tr>
							<th>CTA_1</th>
							<th>CTA_2</th>
							<th>CTA_3</th>
							<th>CTA_4</th>
							<th>CTA_5</th>
							<th>CONCEPTO</th>
							<th>CARGO</th>
							<th>ABONO</th>
						</tr></thead><tbody>";
			foreach ($partidadesembolso as $mks){
					$html .= "<tr class='tfl'>
									<td>
										".$mks["CTA_1"]."
									</td>
									<td>
										".$mks["CTA_2"]."
									</td>
									<td>
										".$mks["CTA_3"]."
									</td>
									<td>
										".$mks["CTA_4"]."
									</td>
									<td>
										".$mks["CTA_5"]."
									</td>
									<td>
										".$mks["CONCEPTO"]."
									</td>
									<td>
										".number_format($mks["CARGO"],2,'.',',')."
									</td>
									<td>
										".number_format($mks["ABONO"],2,'.',',')."
									</td>									
							  </tr>";
							  $acucargo = number_format($acucargo + $mks["CARGO"],2,'.','');
							  $acuabono = number_format($acuabono + $mks["ABONO"],2,'.','');
			}
		$html .= "</tbody>
				  <tfoot>
						<tr>
							<th colspan='6'>
								<p style='float:right'>TOTAL:</p>
							</th>
							<th>".number_format($acucargo,2,'.',',')."</th>
							<th>".number_format($acuabono,2,'.',',')."</th>
						</tr>
					</tfoot>
				</table>";
		$data= array("tblpartidadese"=>$html);
		echo json_encode($data);
	}
	
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	
	public function get_ajax(){
		$parametros = $this->set_obj();
	}

}


?>
