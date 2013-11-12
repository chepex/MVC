<?php


error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
require_once('model_pb_definicion_ctas_banco.php');
require_once('../core/render_view_generic.php');
#Controlador de definicion de cuentas de banco
class controller_pb_definicion_ctas_banco extends pb_definicion_ctas_banco{
	
	#Definicion de Titulos de Objetos Html
	protected $diccionario = array(
		'subtitle'=>array('agregar'=>'Crear Nuevo Definicion CTA',
                      'buscar'=>'Buscar Definicion CTA',
                      'borrar'=>'Eliminar Definicion CTA',
                      'modificar'=>'Modificar Definicion CTA',
                      'listar'=>'Lista de Definicion CTA'),
		'links_menu'=>array(
						'VIEW_SET_USER'=>'prestamos/?ctl=controller_pb_definicion_ctas_banco&act=set',
						'VIEW_GET_USER'=>'prestamos/?ctl=controller_pb_definicion_ctas_banco&act=buscar',
						'VIEW_EDIT_USER'=>'prestamos/?ctl=controller_pb_definicion_ctas_banco&act=modificar',
						'VIEW_DELETE_USER'=>'prestamos/?ctl=controller_pb_definicion_ctas_banco&act=borrar'),
		'form_actions'=>array(
							'SET'=>'../prestamos/?ctl=controller_pb_definicion_ctas_banco&act=insert',
							'GET'=>'../prestamos/?ctl=controller_pb_definicion_ctas_banco',
        'DELETE'=>'../prestamos/?ctl=controller_pb_definicion_ctas_banco&act=delete',
        'EDIT'=>'../prestamos/?ctl=controller_pb_definicion_ctas_banco&act=edit',
        'GET_ALL'=>'../prestamos/?ctl=controller_pb_definicion_ctas_banco&act=get_all'
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
			#Peticiones definidas para el controlador Definicion de Cuentas de Banco
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
	
	#Definicion de una instancia del Modelo del Controlador Definicion de Cuentas de Banco
	public function set_obj() {
		$obj = new pb_definicion_ctas_banco();
		return $obj;
	}
	
	
	#Método que dibuja el formulario para la insercion de Definicion de Cuentas de Banco
	public function set(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$lstestados = $parametros->get_lsoption("pb_estados", array("COD_ESTADO"=>"","DESCRIPCION_ESTADO"=>""));
		$lstbancos = $parametros->get_lsoption("bancos", array("COD_BANCO"=>"","NOM_BANCO"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		$lstdestinosapli = $parametros->get_lsoption("pb_destinoaplicacion", array("COD_DESTINOAPLICACION"=>"","DESCRIPCION_DESTINO"=>""));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['agregar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('agregar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{COD_DEFINICION}',$this->nextval_seq(), $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{COD_ESTADO}', $lstestados , $obvista->html);
		$obvista->html = str_replace('{COD_BANCO}', $lstbancos , $obvista->html);
		$obvista->html = str_replace('{COD_DESTINOAPLICACION}', $lstdestinosapli , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	#Método que imprime la lista de option html provistas por el modelo, para ser recuperadas via Ajax
	public function get(){
		$parametros = $this->set_obj();
		echo $parametros->get_options();
	}
	
	#Método que elimina la definicion de la cuenta de bancos
	public function delete(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$parametros->delete(get_class($parametros));
		$this->msg=$parametros->mensaje;
	}
	
	#Método que Dibuja el Formulario de Actualizacion de Definicion de Cuentas de Banco
	public function update(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$objdefctaban = $this->crea_objeto(array("pb_definicion_ctas_banco dfcta","bancos ban","pb_estados edos"),
										   array("dfcta.COD_ESTADO = edos.COD_ESTADO","dfcta.COD_CIA = ban.COD_CIA","dfcta.COD_BANCO = ban.COD_BANCO AND"),
										   array("dfcta.COD_DEFINICION=".$_REQUEST['COD_DEFINICION'])
										   );
		$lstestados = $parametros->get_lsoption("pb_estados", array("COD_ESTADO"=>"","DESCRIPCION_ESTADO"=>""),array("COD_ESTADO"=>$objdefctaban[0]['COD_ESTADO']));
		$lstbancos = $parametros->get_lsoption("bancos", array("COD_BANCO"=>"","NOM_BANCO"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"COD_BANCO"=>$objdefctaban[0]['COD_BANCO']));
		$lstdestinosapli = $parametros->get_lsoption("pb_destinoaplicacion", array("COD_DESTINOAPLICACION"=>"","DESCRIPCION_DESTINO"=>""), array("COD_DESTINOAPLICACION"=>$objdefctaban[0]['COD_DESTINOAPLICACION']));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['modificar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('modificar',get_class($parametros)), $obvista->html);
		$obvista->html = str_replace('{mensaje}', ' ', $obvista->html);
		$obvista->html = str_replace('{codcia}', $_SESSION['cod_cia'] , $obvista->html);
		$obvista->html = str_replace('{descia}', $_SESSION['nom_cia'] , $obvista->html);
		$obvista->html = str_replace('{COD_DEFINICION}', $objdefctaban[0]['COD_DEFINICION'] , $obvista->html);
		$obvista->html = str_replace('{DESCRIPCION_DEFINICION}', $objdefctaban[0]['DESCRIPCION_DEFINICION'], $obvista->html);
		$obvista->html = str_replace('{CTA_1}', $objdefctaban[0]['CTA_1'], $obvista->html);
		$obvista->html = str_replace('{CTA_2}', $objdefctaban[0]['CTA_2'], $obvista->html);
		$obvista->html = str_replace('{CTA_3}', $objdefctaban[0]['CTA_3'], $obvista->html);
		$obvista->html = str_replace('{CTA_4}', $objdefctaban[0]['CTA_4'], $obvista->html);
		$obvista->html = str_replace('{CTA_5}', $objdefctaban[0]['CTA_5'], $obvista->html);
		$obvista->html = str_replace('{COD_ESTADO}', $lstestados , $obvista->html);
		$obvista->html = str_replace('{COD_BANCO}', $lstbancos , $obvista->html);
		$obvista->html = str_replace('{COD_DESTINOAPLICACION}', $lstdestinosapli , $obvista->html);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->retornar_vista();
	}
	
	#Método que Actualiza los Cambios realizados para la definicion de cuentas de banco
	public function edit(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$parametros->update(get_class($parametros));
		$this->msg=$parametros->mensaje; 
	}
	
	#Método que Guarda la definicion de las cuentas de banco
	public function insert(){
		$parametros = $this->set_obj();
		$parametros->save(get_class($parametros));
	}
	
	#Metodo que devuelve las lista CRUD de la definicion de cuentas de Banco
	public function get_all($mensaje=''){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
		$_REQUEST["filtro"]="NO";
		$mcampos = array($parametros->tableName().".COD_DEFINICION",$parametros->tableName().".DESCRIPCION_DEFINICION",
						 "DECODE(".$parametros->tableName().".TIPO_APLICACION,'C','CARGO','A','ABONO') ",$parametros->tableName().".CTA_1",
						 $parametros->tableName().".CTA_2",$parametros->tableName().".CTA_3",
						 $parametros->tableName().".CTA_4",$parametros->tableName().".CTA_5",
						 "BANCOS.NOM_BANCO","PB_ESTADOS.DESCRIPCION_ESTADO","PB_DESTINOAPLICACION.DESCRIPCION_DESTINO");
        $masx=implode($mcampos, ",");
		$data = $parametros->lis2(get_class($parametros), 3, $masx);
		$rendertable = $parametros->render_table_crud(get_class($parametros),"", array("view"=>"style='display:none;'"));
		$obvista->html = $obvista->get_template('template',get_class($parametros));
		$obvista->html = str_replace('{subtitulo}', $this->diccionario['subtitle']['listar'], $obvista->html);
		$obvista->html = str_replace('{formulario}', $obvista->get_template('listar',get_class($parametros)), $obvista->html); 
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['form_actions']);
		$obvista->html = $obvista->render_dinamic_data($obvista->html, $this->diccionario['links_menu']);
		$obvista->html = str_replace('{Detalle}', $rendertable, $obvista->html);
		$obvista->html = str_replace('{mensaje}', $mensaje, $obvista->html);
		$obvista->retornar_vista();
	}
	
	#Método generico no se esta utilizando
	public function view(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();		
	}
	
	#Método Generico para generar reportes, no se esta utilizando
	public function view_rpt(){
		$parametros = $this->set_obj();
		$obvista = new view_Parametros();
	}
	#Método generico que devuelve peticiones via ajax 
	public function get_ajax(){
		$parametros = $this->set_obj();		
	}

}


?>
