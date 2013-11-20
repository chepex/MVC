<?php

	include_once('../adodb/adodb.inc.php');
	include_once('../adodb/adodb-pager.inc.php');
	include_once('../adodb/tohtml.inc.php');
	include_once('class.SendEmail.php');

session_start();
abstract class DBAbstractModel {

	private static $db_host = '192.168.10.235';
	private static $db_user = 'DROMERO';
	private static $db_pass = 'DROMERO';
	public $debug = false;	
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
		$usuario= $_SESSION['usuario'];
		$clave =  $_SESSION['clave'];
		$db = ADONewConnection('oci8'); 
		$db->Connect(self::$db_host, $usuario,  $clave,  self::$db_name);
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
	
	private function iniciar_transaccion(){
		$this->open_connection();
		$this->conn->StartTrans();
	}
	
	protected function execute_transaccion_query($query){
		 $this->conn->Execute($this->query);
	}
	
	private function finalizar_transaccion(){
		$this->conn->CompleteTrans();
		$this->close_connection();
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

	/*Lista especiales donde se DEPENDE de varias tablas*/
	public function lis2($class,$id=0,$at="0"){

		$this->rows = array();
		$this->open_connection(); 		
 		$cl= new $class;
		
		$campos = $at;
		if( $at =="0"){		
		$campos =$cl->atributos_select();
		}
		
		$tabla  =$cl->tableName(); 
		$llave	=$cl->llave(); 	
		
		if($_REQUEST["filtro"]=="")$_REQUEST["filtro"]=1;
		if($_REQUEST["filtro"]>0){
			$conjuncion = " AND ";
			if ($_REQUEST["filtro"]==1){
				$where = "WHERE ".$tabla.".CREATED_AT > add_months(SYSDATE, -3)";
			}
			if ($_REQUEST["filtro"]==2){
				$where = "WHERE ".$tabla.".CREATED_AT > add_months(SYSDATE, -8)";
			}			
			if ($_REQUEST["filtro"]==3){
				$where = "WHERE  ".$tabla.".CREATED_AT > add_months(SYSDATE, -12)";
			}			
		}else{
			$conjuncion = " WHERE ";
		}	
		if($id==1){ 
			$where .= $conjuncion. $this->get_key($llave, $tabla);
		} 
		if($id==2){
			$campos2 = explode(',',$campos);	
			$where .= $conjuncion. $this->get_fields($campos2);	
		}
		if($id==3){
			$foreignkey = $cl->foreignkey();
			$where .= $conjuncion. $this->get_key($foreignkey,$tabla);
		}	 	
		
			
		$i=0;
		$j=0;		
		
		
		if( empty($cl->relacione_tablas) ){
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
	# OPCION 3 FILTRA POR LOS CAMPOS FORANEOS DE LA TABLA	
	public function lis($class,$id=0,$at="0"){

		$this->rows = array();
		$this->open_connection(); 		
 		$cl= new $class;
		
		$campos = $at;
		if( $at =="0"){		
		$campos =$cl->atributos();
		}

		
		
		$tabla  =$cl->tableName(); 
		$llave	=$cl->llave(); 	
		
		if($_REQUEST["filtro"]=="")$_REQUEST["filtro"]=1;
		if($_REQUEST["filtro"]>0){
			$conjuncion = " AND ";
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
		else{
			$conjuncion = " WHERE ";
		}
			
		if($id==1){
			$where .= $conjuncion. $this->get_key($llave);
		} 
		if($id==2){
			$campos2 = explode(',',$campos);		
			$where .= $conjuncion. $this->get_fields($campos2);
		
		} 
		if($id==3){
			$foreignkey = $cl->foreignkey();
			$where .= $conjuncion. $this->get_key($foreignkey);
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
			$array[$i]= $_REQUEST[$lista[$i]] == 'SYSDATE' || $_REQUEST[$lista[$i]] == 'NULL' || $_REQUEST[$lista[$i]] == 'USER' ? $_REQUEST[$lista[$i]] : "'".$_REQUEST[$lista[$i]]."'" ;
		}
		
			$xx=implode(",", $array);
			$this->query="INSERT INTO ".$tabla."(".$campos.")VALUES(".$xx.")";					
			$this->execute_single_query();			
			if($this->error==""){
				$this->mensaje= "Registro Insertado Correctamente";			
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
					if(isset($_REQUEST[$lista[$i]])){
						//$array[$i]=$lista[$i]." =". $_REQUEST[$lista[$i]] == 'SYSDATE' || $_REQUEST[$lista[$i]] == 'NULL' ? $_REQUEST[$lista[$i]] : "'".$_REQUEST[$lista[$i]]."'";
						$valor = $_REQUEST[$lista[$i]] == 'SYSDATE' || $_REQUEST[$lista[$i]] == 'NULL' ? $_REQUEST[$lista[$i]] : "'".$_REQUEST[$lista[$i]]."' ";
						$array[$i]=$lista[$i]." =". $valor;
					}								
				}
			}
			$where = " WHERE ". $this->get_key($llave);
			$xx=implode(",", $array);
			$this->query="UPDATE  ".$tabla." SET ".$xx." ". $where;
			$this->execute_single_query();				
			if($this->error==""){
				$this->mensaje= "Registro Actualizado Correctamente";			
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
		$this->mensaje = "Registro Eliminado Correctamente";
		
	}
	
	#Inserta Un nuevo Registro
	protected function save_insert_trans($class){
		$this->connexion;
		$cl = new $class;
		$campos =$cl->atributos();
		$tabla  =$cl->tableName(); 
		$llave	=$cl->llave(); 	
		$campos =$cl->atributos();
		$lista = explode(',', $campos);
		for($i=0;$i<count($lista);$i++){			
			$array[$i]= $_REQUEST[$lista[$i]] == 'SYSDATE' || $_REQUEST[$lista[$i]] == 'NULL' || $_REQUEST[$lista[$i]] == 'USER' ? $_REQUEST[$lista[$i]] : "'".$_REQUEST[$lista[$i]]."'" ;
		}
		
			$xx=implode(",", $array);
			$this->query="INSERT INTO ".$tabla."(".$campos.")VALUES(".$xx.")";					
			/*$this->execute_single_query();			
			if($this->error==""){
				$this->mensaje= "Registro Insertado Correctamente";			
			}*/
		return 	$this->query;
	}
	
	# Renderiza una Tabla Con todos sus Registros y el CRUD
	public function render_table_crud($class='',$links,$bnts=array()){
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
                        <a ".$bnts['delete']." class= 'btn-del btn btn-small btn-danger' type= 'button' href='../".$cl->Modulo()."/?ctl=controller_".$class."&act=delete&".$this->getkey_str_url($llave, $this->rows, $fila)."'><i title = 'Eliminar' class='icon-trash'></i></a>
                        <a ".$bnts['update']." class= 'btn-upd btn btn-small' type= 'button' href='../".$cl->Modulo()."/?ctl=controller_".$class."&act=update&".$this->getkey_str_url($llave, $this->rows, $fila)."'> <i title = 'Actualizar' class='icon-pencil'></i></a>
                        <a ".$bnts['view']." class= 'btn-vie btn btn-small' type= 'button' href='../".$cl->Modulo()."/?ctl=controller_".$class."&act=view&".$this->getkey_str_url($llave, $this->rows, $fila)."'> <i title = 'Ver' class='icon-eye-open'></i></a>
                      </td>    
					</tr>";
			$fila++;
		}
		$html.="<tbody></table>";
		$html.="<a ".$bnts['set']." class= 'btn btn-primary pull-right btn' type= 'button' href='../".$cl->Modulo()."/?ctl=controller_".$class."&act=set'>Crear</a>";
		return $html;
	} 

	#Devuelve el correlativo de la Tabla Solicitada
	 protected function get_correl_key($table='', $compuestkey=array(), $fieldmax=''){
		$this->rows = array();
		$sizearray = count($compuestkey);
		$i=1;
		$condicion= implode(" AND ", $compuestkey);
		/*foreach ($compuestkey as $clave=>$valor){
			if($sizearray != $i){
				$condicion .= $clave . " = " . $valor ." AND ";
			}else{
				$condicion .= $clave . " = " . $valor ;
			}
			$i++;
		}*/
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
	 public function get_htmloptions($Arraylist=array()){
		 //$html .= "<option selected='selected'>Seleccione una Opcion</option>";
		foreach($Arraylist as $fila=>$valor){
			$html .= "<option value='".$Arraylist[$fila][0]."'> ".$Arraylist[$fila][0]." | ".$Arraylist[$fila][1]."</option>";//
		}
		return $html;
	}
	
	#Crea Objetos Devuelve Un Arreglo de la Tablas o Tablas Solicitadas
	#Fecha: 22/07/2013 Añadio: Daniel Romero
	#Funcion creada con objetivo Retornar Registro en caso de no existir Modelo
	#@parametros
	#$tablesNames array() : Nombre de Tabla o Tablas Requeridas
	#$joinstables array() : Si se envia Mas de Una Tabla debe enviarse las uniones
	#$condicion array() : Condiciones para la obtencion del Registro, vacio los devuelve todos
	#$campos array(): Lista de Campos solicitados, si esta vacio los devuelve todos
	 public function crea_objeto($tablesNames=array() , $joinstables=array(), $condicion=array(), $campos=array()){
		$this->rows = array();
		if(count($campos) > 0){
			$fields = implode(', ',$campos);
		}else{
			$fields=" * ";
		}
		if(count($joinstables) > 0){
			$tablesjoins =  implode(' AND ',$joinstables); 
		}
		if(count($tablesNames) > 1){
			if(count($condicion) > 0){
				$criterios = implode(' AND ', $condicion);
				$this->query="SELECT ".$fields." FROM ". implode(',',$tablesNames) . " WHERE ". $tablesjoins ."  " . $criterios;
			}else{
				$this->query="SELECT ".$fields." FROM ". implode(',',$tablesNames) . " WHERE ". $tablesjoins ;
			}
		}else{
			if(count($condicion) > 0){
				$criterios = implode(' AND ', $condicion);
				$this->query="SELECT ".$fields." FROM ". implode(',',$tablesNames) . " WHERE ".$criterios;
			}else{
				$this->query="SELECT ".$fields." FROM ". implode(',',$tablesNames) ;
			}
		}
		
		$this->get_results_from_query();
		
		return $this->rows;
	}

	#Devuelve Una Llave para condicionar en una consulta
	protected function get_key($compuestkey,$tablename=''){
	 	 foreach ($compuestkey as $clave){
			if(isset($_REQUEST[$clave])){
				if(empty($tablename)){
					$condicion[]=$clave." =".$_REQUEST[$clave];
				}else{
					$condicion[]=$tablename.".".$clave." =".$_REQUEST[$clave];
				}
			}
	 	 }
		 $campos= implode(' AND ',$condicion);	 	
		return $campos;
	}	
	
	#Devuelve Una Llave para condicionar en una consulta
	protected function get_fields($compuestkey,$tablename=''){
	
	 	 foreach ($compuestkey as $clave){
	
			if(isset($_REQUEST[$clave])){
				if(empty($tablename)){
					$condicion[]=$clave." =".$_REQUEST[$clave];
				}else{
					$condicion[]=$tablename.".".$clave." =".$_REQUEST[$clave];
				}
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

	#comentario
	public function menu1(){
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
                where roles_x_usuario.cod_cia = ".$_SESSION['cod_cia']."
                and roles_x_usuario.usuario = '".$_SESSION['usuario']."'
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
		while (!$rs->EOF){		
			$menu .= "<li class='dropdown-submenu' ><a href= 'page1.php?modulo=".$rs->fields[0]."'><b>".$rs->fields[1] ."</b></a></li>";	
			$rs->MoveNext(); 			
		} 
		$menu.="</ul>
					</div>";
		return $menu;
		
	}		

	#comentario
	public function menu2(){   
		$sql_padres= "select COD_MODULO,cod_menu,cod_menu_sup,descripcion,LOWER(archivo_destino)
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
													) 
									AND cod_menu IN(
													SELECT COD_MENU_SUP FROM menu_x_modulo 
													where cod_cia = ".$_SESSION['cod_cia']."
													and cod_modulo = '".$_SESSION['modulo']."'
													)";
		$this->open_connection();
		$rs = $this->conn->Execute($sql_padres);

		$menu =  "<div class='navbar navbar-fixed-top'>
					<div class='navbar-inner'>
						<div class='container-fluid'>
							<div class= 'nav-collapse collapse navbar-responsive-collapse'>
								<ul class='nav'>
									<li>
										<a> 
											<i class='icon-align-left' id= 'home'></i>
										</a>
									</li>";
		while (!$rs->EOF){	
			$menu.="<li class='dropdown '>
						<a class='dropdown-toggle aa' data-toggle='dropdown' href='#'>
							".$rs->fields[3] ."
							<b class='caret'></b>
						</a>
						<ul class='dropdown-menu ' style ='' >";
	
			$sql_hijos= "select COD_MODULO,cod_menu,cod_menu_sup,descripcion,LOWER(archivo_destino)
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
													) 
									AND COD_MENU <> '".$rs->fields[2]."' AND COD_MENU_SUP='".$rs->fields[2]."'";
			$rs_hijo = $this->conn->Execute($sql_hijos);
			while(!$rs_hijo->EOF){
				$menu .= "<li class='dropdown' ><a  href= '".$rs_hijo->fields[4]."'>".$rs_hijo->fields[3] ."</a>";				
				$menu.="</li>";	
				$rs_hijo->MoveNext(); 
			} 
			$menu.="</ul></li>";			
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
	
	#Envio de Correo Electronico
	/*
	 * @parametros
	 * @remitente: cuenta de correo desde la cual se envia el email
	 * @destinatario: cuenta de correo a la que se desea enviar el email
	 * @asunto: Asunto del correo
	 * @mensajebody : El cuerpo del mensaje, este puede contener etiquetas html
	 * Informacion:
	 * #Si se utiliza en el cuerpo una referencia a un archivo css, el cliente de correo debera
	 * tener habilitada que muestre este estilo, la segunda opcion es poner en un <style></style> el css.
	 * #
	 * */
	public function sendemail($remitente, $destinatario, $asunto, $mensajebody){
		$clscorreo = New SendEmail();
		$clscorreo->remitente = $remitente;
		$clscorreo->destinatario = $destinatario;
		$clscorreo->asunto = $asunto;
		$clscorreo->msgboby = $mensajebody;
		$clscorreo->EnviarByMailSMTP();
	}
	
	#Numero de Dias Entre dos Fechas
	/*
	 * @Parametros
	 * @fecha1 Fecha inicial desde donde se cuenta la diferencia de dias, este parametro
	 * debe ser completado con una fecha con formato dd/mm/YYY.
	 * @fecha2 Fecha Final desde donde se cuenta la diferencia de dias, este parametro
	 * debe ser completado con una fecha con formato dd/mm/YYY.
	 * 
	 * */
	public function diferencia_dias($fecha1, $fecha2){
		//defino fecha 1
		$explode_fecha1= explode("/",$fecha1);
		$ano1 = $explode_fecha1[2];
		$mes1 = $explode_fecha1[1];
		$dia1 = $explode_fecha1[0];

		//defino fecha 2
		$explode_fecha2= explode("/",$fecha2);
		$ano2 = $explode_fecha2[2];
		$mes2 = $explode_fecha2[1];
		$dia2 = $explode_fecha2[0];

		//calculo timestam de las dos fechas
		$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
		$timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

		//resto a una fecha la otra
		$segundos_diferencia = $timestamp1 - $timestamp2;
		//echo $segundos_diferencia;

		//convierto segundos en días
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

		//obtengo el valor absoulto de los días (quito el posible signo negativo)
		$dias_diferencia = abs($dias_diferencia);

		//quito los decimales a los días de diferencia
		$dias_diferencia = floor($dias_diferencia);

		return $dias_diferencia; 
	}
	
	#Numero de Dias Correspondiente a un Año
	/*
	 * @Parametros
	 * @anio: Año del que se desea saber los dias, se calcula en base a si es bisiesto o no,
	 * si es bisiesto tiene 366 dias, sucede cada cuatro años, y si no es bisiesto tiene 365 dias.
	 * 
	 * */
	public function es_bisiesto($anio){
		if (($anio % 4 == 0) && (($anio % 100 != 0) || ($anio % 400 == 0))){
			$dias_anio = 366;
		}else{
			$dias_anio = 365;
		}
		return $dias_anio;
	}
	
	#Es Fin de Semana
	/*
	 * @Parametros
	 * @fecha: Recibe una Fecha en formato Y-m-d, para saber si el dia de la Fecha es fin de semana
	 * 
	 * */
	public function es_findesemana($fecha){
		//Divide la Fecha es dia Mes Año
		$fecha = explode('-',$fecha);
		// Convierte la Fecha en segundos para operarlos
		$fecha = mktime('1', '1', '1', $fecha[1],$fecha[2],$fecha[0]);
		//Devuelve un Arreglo con la Informacion de la Fecha, Nombre del dia, mes entre otros...
		$DataDate = getdate($fecha);
		//Verifica si es Domingo
		if($DataDate['wday']==0){
			$findesemana=0;
		//Verifica si es Sabado
		}elseif($DataDate['wday']==6){
			$findesemana=1;
		}else{
			$findesemana=2;
		}
		return $findesemana;
	}

}

?>
