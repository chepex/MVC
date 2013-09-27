<?php


session_start();
//error_reporting(E_ALL);
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class pb_definicion_ctas_banco extends DBAbstractModel {



    ############################### PROPIEDADES ################################
    public $COD_DEFINICION;
    public $DESCRIPCION_DEFINICION; 
    public $TIPO_APLICACION;
    public $CTA_1; 
    public $CTA_2;
    public $CTA_3; 
	public $CTA_4;
    public $CTA_5; 
    public $COD_CIA;
    public $COD_BANCO;
    public $COD_ESTADO;
    public $COD_DESTINOAPLICACION;

    ############################### ATRIBUTOS ################################
        public function atributos()
    {
        $masx= array('COD_DEFINICION','DESCRIPCION_DEFINICION','TIPO_APLICACION','CTA_1','CTA_2','CTA_3','CTA_4','CTA_5','COD_CIA','COD_BANCO','COD_ESTADO','COD_DESTINOAPLICACION');
        $masx=implode($masx, ",");
        return $masx;
    }
    
     
    public function relaciones(){
		$masx =  $this->tableName().".COD_ESTADO = PB_ESTADOS.COD_ESTADO 
				 AND ". $this->tableName().".COD_CIA = BANCOS.COD_CIA
				 AND ". $this->tableName().".COD_BANCO = BANCOS.COD_BANCO
				 AND ". $this->tableName().".COD_DESTINOAPLICACION = PB_DESTINOAPLICACION.COD_DESTINOAPLICACION";
        return $masx;                
    }

    public function relacione_tablas(){
		 $masx= 'PB_ESTADOS,BANCOS,PB_DESTINOAPLICACION';
         return $masx;                
    }


        public function tableName()
    {
        return 'pb_definicion_ctas_banco';
    }
    
    public function Modulo()
    {
        return 'prestamos';
    }


    public function llave()
    {
        return array('COD_DEFINICION');

    }
    
    public function foreignkey(){
		return "";
	}

    ################################# MÉTODOS ##################################
   
    public function nextval_seq(){
		$this->rows=array();
		$this->query="SELECT SEQ_PBDEFICTASBANCO.NEXTVAL FROM DUAL";
		$this->get_results_from_query();
        return $this->rows[0]['NEXTVAL'];
	}
	
	public function get_options(){
		$lstdestinoapli = $this->get_lsoption("pb_destinoaplicacion", array("COD_DESTINOAPLICACION"=>"","DESCRIPCION_DESTINO"=>""));
		return $lstdestinoapli;
	}
	
	
}
?>
