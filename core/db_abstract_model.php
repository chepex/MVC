<?php

	include_once('../adodb/adodb.inc.php');
	include_once('../adodb/adodb-pager.inc.php');
	include_once('../adodb/tohtml.inc.php');

session_start();
abstract class DBAbstractModel {

	private static $db_host = '192.168.10.235';
	private static $db_user = 'MMIXCO';
	private static $db_pass = 'MARIO13';
	public $debug = true;	
    private static $db_name = 'DESA';
    protected $query;
    protected $rows = array();
    protected $rows_campos = array();

    private $conn;
    public $mensaje ='';
    public $error ='';

   
    
    # los siguientes métodos pueden definirse con exactitud y no son abstractos
	# Conectar a la base de datos
	private function open_connection() {

		if($_SESSION['cod_cia']=="") {
			$this->error= "No se realizó la conexión";
			die('Aun no ha iniciado session');
		}
		$db = ADONewConnection('oci8'); 
		$db->Connect(self::$db_host, self::$db_user,  self::$db_pass,  self::$db_name);
		$db->debug = $this->debug;
		if (!$db){
			$this->error= "No se realizó la conexión";
			die('You cannot call this script directly !');
		}else{
			$this->conn = $db;
		}

		
	}

	# Desconectar la base de datos
	private function close_connection() {
		$this->conn->close();
	}

	# Ejecutar un query simple del tipo INSERT, DELETE, UPDATE
	 protected function execute_single_query() {
	   $this->open_connection();
		

		$result = $this->conn->Execute($this->query);
		if(!$result){
			$this->error= $this->conn->ErrorMsg()."<br> --> ".$this->query;				
		}else{			
			$result->close();
			$this->close_connection();
			}	
		
	}
	
	# Traer resultados de una consulta en un Array
	 protected function get_results_from_query() {
		$this->open_connection();

		$result = $this->conn->Execute($this->query);

		if(!$result){
			$this->error= $this->conn->ErrorMsg()."<br> --> ".$this->query;				
		}else{
			while (!$result->EOF) {
						$this->rows[] = $result->fields;
						$result->MoveNext();
					}
					$result->close();
					$this->close_connection();
			}	

		
	}

	/*Lista especiales donde se depenete de varias tablas*/
public function lis2($class,$id=0,$at="0"){

		$this->open_connection(); 		
 		$cl= new $class;
		
		$campos = $at;
		if( $at =="0"){		
		$campos =$cl->atributos_select();
		}
		
		$tabla  =$cl->tableName(); 
		$llave	=$cl->llave(); 		
		if($id==1){
			$where = " WHERE ". $this->get_key($llave);
		} 
		if($id==2){
			$campos2 = explode(',',$campos);		
			$where = " WHERE ". $this->get_fields($campos2);		
		} 	
		if($_REQUEST["filtro"]=="")$_REQUEST["filtro"]=1;
		if($_REQUEST["filtro"]>0){
			if ($_REQUEST["filtro"]==1){
				$where = "WHERE ".$tabla.".CREATED_AT > add_months(SYSDATE, -3)";
			}
			if ($_REQUEST["filtro"]==2){
				$where = "WHERE ".$tabla.".CREATED_AT > add_months(SYSDATE, -8)";
			}			
			if ($_REQUEST["filtro"]==3){
				$where = "WHERE  ".$tabla.".CREATED_AT > add_months(SYSDATE, -12)";
			}			
		}	
			
		$i=0;
		$j=0;		
		
		
		if( empty ($cl->relacione_tablas) ){
			$sql = "SELECT ".$campos."	 FROM ".$tabla.", ".$cl->relacione_tablas()." ".$where." ".$cl->relaciones();
		
		}else{
			$sql = "SELECT ".$campos."	 FROM ".$tabla ." ".$where;	
		
		} 
		

		$result = $this->conn->Execute($sql);
		if(!$result){
			$this->error= $this->conn->ErrorMsg()."<br> --> ".$sql;	
			
		}else{
			while (!$result->EOF) {



						$this->rows[] =$result->fields;

				
				$result->MoveNext();
			}
			$result->close();
			$this->close_connection();
			$this->mensaje= "";	
			$this->rows_campos[] = $campos;
		}


		

		
		return $this->rows;
	}		

	
	#Ejecutar un select 
	# OPCION 0  SIN FILTROS
	# OPCION 1 FILTRO DE CAMPOS DE LLAVE PRIMARIA
	# OPCION 2 FILTRA POR LOS CAMPOS ENVIADOS	
	public function lis($class,$id=0,$at="0"){

		$this->open_connection(); 		
 		$cl= new $class;
		
		$campos = $at;
		if( $at =="0"){		
		$campos =$cl->atributos();
		}

		
		
		$tabla  =$cl->tableName(); 
		$llave	=$cl->llave(); 		
		if($id==1){
			$where = " WHERE ". $this->get_key($llave);
		} 
		if($id==2){
			$campos2 = explode(',',$campos);		
			$where = " WHERE ". $this->get_fields($campos2);
		
		} 	
		if($_REQUEST["filtro"]=="")$_REQUEST["filtro"]=1;
		if($_REQUEST["filtro"]>0){
			if ($_REQUEST["filtro"]==1){
				$where = "WHERE CREATED_AT > add_months(SYSDATE, -3)";
			}
			if ($_REQUEST["filtro"]==2){
				$where = "WHERE CREATED_AT > add_months(SYSDATE, -8)";
			}			
			if ($_REQUEST["filtro"]==3){
				$where = "WHERE  CREATED_AT > add_months(SYSDATE, -12)";
			}			
		}		
			
		$i=0;
		$j=0;		
		
		

			$sql = "SELECT ".$campos."	 FROM ".$tabla." ".$where;
		

		$result = $this->conn->Execute($sql);
		if(!$result){
			$this->error= $this->conn->ErrorMsg()."<br> --> ".$sql;	
			
		}else{
			while (!$result->EOF) {
				$this->rows[] = $result->fields;				
				$result->MoveNext();
			}
			$result->close();
			$this->close_connection();
			$this->mensaje= "";	
			$this->rows_campos[] = $campos;
		}


		

		
		return $this->rows;
	}

	#Inserta Un nuevo Registro
	protected function save($class){
		$this->connexion;
		$cl = new $class;
		$campos =$cl->atributos();
		$tabla  =$cl->tableName(); 
		$llave	=$cl->llave(); 	
		$campos =$cl->atributos();
		$lista = explode(',', $campos);
		for($i=0;$i<count($lista);$i++){			
			$array[$i]="'".$_REQUEST[$lista[$i]]."'";
		}
		
			$xx=implode(",", $array);
			$this->query="INSERT INTO ".$tabla."(".$campos.")VALUES(".$xx.")";					
			$this->execute_single_query();			
			if($this->error==""){
				$this->mensaje= "registro insertado correctamente";			
			}
			
	}
	
	#Ejecuta una Actualizacion
	public function update($class){
			$this->connexion;
			$cl = new $class;
			$campos =$cl->atributos();
			$tabla  =$cl->tableName(); 
			$llave	=$cl->llave(); 
			
			$campos =$cl->atributos();
			$lista = explode(',', $campos);
			for($i=0;$i<count($lista);$i++){
				if ($llave!=$lista[$i]){				
					$array[$i]=$lista[$i]." = '".$_REQUEST[$lista[$i]]."' ";					
				}
			}
			$where = " WHERE ". $this->get_key($llave);
			$xx=implode(",", $array);
			$this->query="UPDATE  ".$tabla." SET ".$xx." ". $where;
			$this->execute_single_query();				
			if($this->error==""){
				$this->mensaje= "registro insertado correctamente";			
			}				
			
			
	}	
	
	#Elimina un Registro
	public function delete($class, $parametro_data=array()){
		$this->open_connection();
 		
 		$cl= new $class;
		$campos =$cl->atributos();
		$tabla  =$cl->tableName(); 
		$llave	=$cl->llave(); 
		$where = $this->get_key($llave);
		$this->query="DELETE FROM ".$tabla." WHERE ".$where;
		$this->execute_single_query();	
			if($this->error==""){
				$this->mensaje= "registro insertado correctamente";			
			}		
		$this->mensaje = "registro eliminado correctamente";
		
	}
	
	# Renderiza una Tabla Con todos sus Registros y el CRUD
	public function render_table_crud($class='',$links){
		$cl = new $class;
		$llave = $cl->llave();
		$fila=0;	
		foreach($this->rows as $filas=>$valor){
			$columnas = ceil((count($valor)/2) -1);
			$html .= "<tr>";
			for($columna = 0; $columna <= $columnas; $columna++){
				if($links[$columna]!=""){
					$x= explode("|",$this->rows[$fila][$columna]);
					$html .= "<td><a href= '$links[$columna].php?act=update&ID=$x[1]'>".$x[0]."</a></td>";
				}else{
					$html .= "<td>".$this->rows[$fila][$columna]."</td>";	
				}
				
			}	
            $html .= "<td>						
                        <a class= 'btn-del btn btn-small btn-danger' type= 'button' href='../".$cl->Modulo()."/controller_".$class.".php?act=delete&".$this->getkey_str_url($llave, $this->rows, $fila)."'><i title = 'Eliminar' class='icon-trash'></i></a>
                        <a class= 'btn-upd btn btn-small' type= 'button' href='../".$cl->Modulo()."/controller_".$class.".php?act=update&".$this->getkey_str_url($llave, $this->rows, $fila)."'> <i title = 'Actualizar' class='icon-pencil'></i></a>
                        <a class= 'btn-vie btn btn-small' type= 'button' href='../".$cl->Modulo()."/controller_".$class.".php?act=view&".$this->getkey_str_url($llave, $this->rows, $fila)."'> <i title = 'Ver' class='icon-eye-open'></i></a>
                      </td>    
					</tr>";
			$fila++;
		}
		$html.="<tbody></table>";
		$html.="<a class= 'btn btn-primary pull-right btn' type= 'button' href='../".$cl->Modulo()."/controller_".$class.".php?act=set'>Crear</a>";
		return $html;
	} 

	#Devuelve el correlativo de la Tabla Solicitada
	 protected function get_correl_key($table='', $compuestkey=array(), $fieldmax=''){
		$sizearray = count($compuestkey);
		$i=1;
		foreach ($compuestkey as $clave=>$valor){
			if($sizearray != $i){
				$condicion .= $clave . " = " . $valor ." AND ";
			}else{
				$condicion .= $clave . " = " . $valor ;
			}
			$i++;
		}
		$this->query="SELECT 
							nvl(MAX(to_number(".$fieldmax.")),0) + 1  as nexval
					   FROM ". $table ." 
						WHERE " . $condicion;
		$this->get_results_from_query();
		return $this->rows;
	}
	
	#Devuelve Un Arreglo Con Id y Descripcion del Maestro
	 protected function get_lsoption($table='', $fieldsmaster=array(), $condicion=array()){
		$this->rows=array();
		$sizearray = count($fieldsmaster);
		$sizecondicion=count($condicion);
		$i=1;
		foreach ($fieldsmaster as $clave=>$valor){
			if($sizearray != $i){
				$fields .= $clave ." , ";
			}else{
				$fields .= $clave;
			}
			$i++;
		}
		$i=1;
		if($sizecondicion > 0){
			foreach ($condicion as $clave=>$valor){
				if($sizecondicion != $i){
					$condi .= $clave . " = " . $valor ." AND ";
				}else{
					$condi .= $clave . " = " . $valor ;
				}
				$i++;
			}
			$this->query="SELECT ". $fields ." FROM ". $table . " WHERE ". $condi;
		}else{
			$this->query="SELECT ". $fields ." FROM ". $table ;
		}
			
		
		$this->get_results_from_query();
		return $this->get_htmloptions($this->rows);
	}
	
	#Devuelve un String con la lista de options para un select html
	 protected function get_htmloptions($Arraylist=array()){
		foreach($Arraylist as $fila=>$valor){
			$html .= "<option value='".$Arraylist[$fila][0]."'> ".$Arraylist[$fila][0]." | ".$Arraylist[$fila][1]."</option>";
		}
		return $html;
	}
	
	#Crea Objetos de la Entidad Solicitada, devuelve un Arreglo
	 protected function crea_objeto($tableName='' , $condicion=array()){
		$this->rows = array();
		$sizecondicion=count($condicion);
		$i=1;
		if($sizecondicion > 0){
			foreach ($condicion as $clave=>$valor){
				if($sizecondicion != $i){
					$condi .= $clave . " = " . $valor ." AND ";
				}else{
					$condi .= $clave . " = " . $valor ;
				}
				$i++;
			}
			$this->query="SELECT * FROM ". $tableName . " WHERE ". $condi;
		}else{
			$this->query="SELECT * FROM ". $tableName ;
		}
		
		$this->get_results_from_query();
		
		return $this->rows;
	}

	#Devuelve Una Llave para condicionar en una consulta
	protected function get_key($compuestkey){
	 	 foreach ($compuestkey as $clave){
			if(isset($_REQUEST[$clave])){
				$condicion[]=$clave." =".$_REQUEST[$clave];
			}
	 	 }
		 $campos= implode(' AND ',$condicion);	 	
		return $campos;
	}	
	
	#Devuelve Una Llave para condicionar en una consulta
	protected function get_fields($compuestkey){
	
	 	 foreach ($compuestkey as $clave){
	
			if(isset($_REQUEST[$clave])){
	
				$condicion[]=$clave." =".$_REQUEST[$clave];
			}
	 	 }
		 $campos= implode(' AND ',$condicion);	 	
		return $campos;
	}		
	
	#Devuelve Una url con los datos del registro Primario
	protected function getkey_str_url($CompuestKey=array(), $DataArray=array(), $fila=0){
	 	 foreach ($CompuestKey as $clave){
	 	 	$condicion[] = $clave."=".$DataArray[$fila][$clave];
	 	 }
		 $campos= implode('&',$condicion);	 	
		return $campos;
	}
	
	#Devuelve un Arreglo Con los Campos entre {} para ser remplazados en el template de la vista
	 public function render_etiquetas($DataArray=array()){
	 	 foreach ($DataArray as $clave=>$valor){
			 foreach($valor as $campo=>$val){
				$TagData["{".$campo."}"] = $val;
			}
	 	 }
		return $TagData;
	}







public function menu1()
	{


$sql= "select distinct roles_x_modulos.cod_modulo,modulos.descripcion
                from roles_x_modulos
                left join admappli.modulos on
                roles_x_modulos.cod_cia = modulos.cod_cia
                and roles_x_modulos.cod_modulo = modulos.cod_modulo
                where roles_x_modulos.cod_cia =1
                and roles_x_modulos.role in (
                select roles_x_usuario.role
                from roles_x_usuario
                left join listado_roles on
                roles_x_usuario.cod_cia = listado_roles.cod_cia
                and roles_x_usuario.role = listado_roles.role
                where roles_x_usuario.cod_cia = 1
                and roles_x_usuario.usuario = 'MMIXCO'
                and listado_roles.aplicacion = 'S'
                )
                order by roles_x_modulos.cod_modulo";
$this->open_connection();
$rs = $this->conn->Execute($sql);

$padre= 0;
$menu =  "
<br>
<br>
<div class='dropdown ' id= 'menu-modulo' style = 'display:none'> <ul class='dropdown-menu' style = 'display:block'>";
while (!$rs->EOF) 
		{		

				$menu .= "<li class='dropdown-submenu' ><a href= 'page1.php?modulo=".$rs->fields[0]."'><b>".$rs->fields[1] ."</b></a></li>";	

			
			$rs->MoveNext(); 			
		} 
		$menu.="</ul>
		
		</div>";
		return $menu;
		
	}		


	public function menu2()
	{


$sql= "select COD_MODULO,cod_menu,cod_menu_sup,descripcion,LOWER(archivo_destino)
			from menu_x_modulo
			where cod_cia = ".$_SESSION['cod_cia']."
			and cod_modulo = '".$_SESSION['modulo']."'
			and cod_menu in (
			  select cod_menu from opciones_x_role
			  where cod_cia = ".$_SESSION['cod_cia']."
			  and cod_modulo = '".$_SESSION['modulo']."'
			  and role in (
				select role from roles_x_usuario
				where cod_cia = ".$_SESSION['cod_cia']."
				and usuario = '".$_SESSION['usuario']."'
			  )
			)";
$this->open_connection();
$rs = $this->conn->Execute($sql);
$padre= 0;
$menu =  "<div class='navbar navbar-fixed-top'>
			<div class='navbar-inner'>
				<div class='container-fluid'>
					<div class= 'nav-collapse collapse navbar-responsive-collapse'>
<ul class='nav'>
<li>
<a> <i class='icon-align-left' id= 'home'></i>   </a>
</li>
</ul>
			<ul class='nav'>";
while (!$rs->EOF) 
		{		
			$hijo=substr($rs->fields[1], 0, 4);
			if($rs->fields[2]=="01"){
				if($padre>0){
				$menu.="</ul></li>";		
				}
				
				$menu .= "<li class='dropdown' ><a class= 'dropdown-toggle' data-toggle='dropdown' href= '".$rs->fields[4]."'>".$rs->fields[3] ." <b class='caret'></b></a>";	
				$menu .= "<ul class='dropdown-menu' >";
				$padre= $rs->fields[1];
			}
			
				if($padre==$hijo){
					$menu.= "<li ><a padre='$$padre' hijo= '$hijo' href= '".$rs->fields[4]."'>".$rs->fields[3] ."</a></li>";

				}else{
						

				} 			
			$rs->MoveNext(); 			
		} 
		//$menu.="</ul></li><lu class='nav pull-right'><a href='#'>Link</a></lu>";
		
		$menu.="</ul></li>
		    </div>
		  <div class='notices pull-right'>			
			<p class= 'navbar-text'>   ".$_SESSION['usuario']."  </p>		
		  </div>

		
		</div></div></div>";
		return $menu;
		
	}	
}

?>
