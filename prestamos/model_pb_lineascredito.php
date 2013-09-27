<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_lineascredito extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_LINEA; 
    public $NUM_REFLINEA;
    public $COD_BANCO; 
    public $COD_TIPOCREDITO;
    public $COD_ESTADO; 
	public $TECHO_LINEA;
    public $FECHA_APERTURA; 
    public $FECHA_VENCIMIENTO;
    public $DESTINO;
    public $DESCRIPCION_FORMA_PAGO;
    public $DESCRIPCION_GARANTIAS;
    public $MOTIVOS_CADUCIDAD;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_LINEA','NUM_REFLINEA','COD_BANCO','COD_TIPOCREDITO','COD_ESTADO','TECHO_LINEA','FECHA_APERTURA','FECHA_VENCIMIENTO','DESTINO','DESCRIPCION_FORMA_PAGO','DESCRIPCION_GARANTIAS','MOTIVOS_CADUCIDAD');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_ESTADO = PB_ESTADOS.COD_ESTADO 
				 AND ". $this->tableName().".COD_CIA = BANCOS.COD_CIA
				 AND ". $this->tableName().".COD_BANCO = BANCOS.COD_BANCO
				 AND ".$this->tableName().".COD_TIPOCREDITO = PB_TIPOS_CREDITOS.COD_TIPOCREDITO";
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= 'PB_ESTADOS,BANCOS,PB_TIPOS_CREDITOS';
         return $masx;                
    }


        public function tableName()
    {
        return 'Pb_lineascredito';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_LINEA');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBLINEACRED.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function get_options(){
		$lstlineas = $this->get_lsoption($this->tableName(), array("COD_LINEA"=>"","NUM_REFLINEA"=>""), array("COD_CIA"=>$_SESSION['cod_cia'],"COD_BANCO"=>"'".$_REQUEST['COD_BANCO']."'","COD_ESTADO"=>"1"));
		return $lstlineas;
	}
	
	public function validar_disponibilidad($COD_CIA,$COD_BANCO,$COD_LINEA=0,$VALOR_SOLICITUD=0){
		$this->rows=array();
		$this->TECHO_LINEA = 0;
		$this->query="SELECT TECHO_LINEA FROM ". $this->tableName() ." WHERE COD_LINEA=".$COD_LINEA." AND COD_CIA=".$COD_CIA." AND COD_BANCO='".$COD_BANCO."' AND COD_ESTADO=1"  ;
		$this->get_results_from_query();
		$this->TECHO_LINEA = $this->rows[0]['TECHO_LINEA'];
		$this->rows=array();
		$this->query="SELECT nvl(SUM(MONTO_APROBADO),0) TOTAL_PRESTAMOS FROM PB_PRESTAMOS WHERE COD_CIA=".$COD_CIA." AND COD_BANCO='".$COD_BANCO."' AND COD_LINEA=".$COD_LINEA." AND COD_ESTADO=3"  ;
		$this->get_results_from_query();
		$TOTAL_PRESTAMOS = $this->rows[0]['TOTAL_PRESTAMOS'];
		$DISPONIBLE = $this->TECHO_LINEA - $TOTAL_PRESTAMOS;
		if($VALOR_SOLICITUD > $DISPONIBLE){
			$mensaje="El Valor Solicitado \$USD ".number_format($VALOR_SOLICITUD, 2, '.', ',')." es Mayor que el Disponible de la Linea de Credito, el Valor disponible es: \$USD ". number_format($DISPONIBLE, 2, '.', ',');
		}
		return 	$mensaje;	
	}
	
	
}
?>
