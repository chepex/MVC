<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_bancos extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_CIA;
    public $COD_BANCO; 
    public $NOM_BANCO;
    public $DIRECCION; 
    public $TELEFONO1;
    public $TELEFONO2; 
	public $CONTACTO;
    public $OBSERVACIONES; 
    public $DIAS_MAX_NOTAS;
    public $DIAS_MAX_CHEQUES; 
	public $NOM_CORTO;
    public $ORDEN_PAGO; 


    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_CIA','COD_BANCO','NOM_BANCO','DIRECCION','TELEFONO1','TELEFONO2','CONTACTO','OBSERVACIONES','DIAS_MAX_NOTAS','DIAS_MAX_CHEQUES','NOM_CORTO','ORDEN_PAGO');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  "";
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= '';
         return $masx;                
    }


        public function tableName()
    {
        return 'bancos';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_CIA','COD_BANCO');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
  
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBBANCOS.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function get_options(){
		$lstbancos = $this->get_lsoption($this->tableName(), array("COD_BANCO"=>"","NOM_BANCO"=>""), array("COD_CIA"=>$_SESSION['cod_cia']));
		return $lstbancos;
	}
	
	public function get_optionscuentas($COD_CIA, $COD_BANCO){
		$objcuenta = $this->crea_objeto(array("Bco_Cuentas a","Bancos b"),array("b.Cod_Cia = a.Cod_Cia","b.Cod_Banco = a.Cod_Banco"),array("and a.Cod_Cia =".$COD_CIA ,"b.Cod_banco = '".$COD_BANCO."'","a.Estado = 'A'"),array("a.Cod_Cuenta","'CUENTA BCO.'"));
		$lstcuentas = $this->get_htmloptions($objcuenta);
		return $lstcuentas;
	}
	
	public function get_optionschequeras($COD_CIA, $COD_BANCO, $COD_CUENTA){
		$objchequeras = $this->crea_objeto(array("chequeras a","bco_cuentas b"),array("b.cod_cia = a.cod_cia","b.cod_cuenta = a.cod_cuenta","b.cod_banco = a.cod_banco"),array("and a.Cod_Cia =".$COD_CIA ,"a.Cod_banco = '".$COD_BANCO."'","a.cod_cuenta='".$COD_CUENTA."'","a.habilitada = 'A'", "b.estado = 'A'"),array("a.secuencia","'CHEQUERA'"));
		$lstchequeras= $this->get_htmloptions($objchequeras);
		return $lstchequeras;
	}
	
	
}
?>
