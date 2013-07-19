<?php
session_start();
class dao{
	var $class;
	var $connexion;


    function __construct() {
        include_once 'config.php';
        $con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
        // selecting database
        mysql_select_db(DB_DATABASE); 
        // return database handler        
        $this->connexion = $con;
        
    }


	function lis($class,$id){
 		$this->connexion;
		$campos =$class->atributos();
		$tabla  =$class->tableName(); 
		$llave	=$class->llave(); 
		$cnombre  =get_class($class); 		
		$i=0;
		$j=0;		
		$sql = "SELECT ".$campos." FROM ".$tabla." WHERE ".$llave." = ".$id;
		//echo $sql;
		$asql=mysql_query($sql);	
		$lista = explode(',', $campos);			
		While ($row2=mysql_fetch_array($asql))
		{		
			for($i=0;$i<=count($lista);$i++){
				$array[$j][$i]=$row2[$i];			
			}
			$j++;		
		} 
		
		return $array;	
	}

	function lista($class){
 		$con=$this->connexion;
 		$j=0;
 		$registros = 3; 
 		$inicial = 0; 
 		$cantidad = 3; 
		$campos =$class->atributos();
		$tabla  =$class->tableName(); 		
		$cnombre  =get_class($class); 		
		$sql = "SELECT ".$campos." FROM ".$tabla;

		
		$asql=mysql_query($sql,$con);
		$total= mysql_num_fields($asql);		
		$lista = explode(',', $campos);
		While ($row2=mysql_fetch_array($asql))
		{		
			for($i=0;$i<=count($lista);$i++){
				$array[$j][$i]=$row2[$i];			
			}
			$j++;		
		}
		return $this->build_table_action($array,$lista,$cnombre);
	}

	function listabyid($class,$id){
 		$this->connexion;
		$campos =$class->atributos();
		$tabla  =$class->tableName(); 
		$llave	=$class->llave(); 
		$cnombre  =get_class($class); 		
		$i=0;
		$j=0;		
		$sql = "SELECT ".$campos." FROM ".$tabla." WHERE ".$llave." = ".$id;
		//echo $sql;
		$asql=mysql_query($sql);	
		$lista = explode(',', $campos);			
		While ($row2=mysql_fetch_array($asql))
		{		
			for($i=0;$i<=count($lista);$i++){
				$array[$j][$i]=$row2[$i];			
			}
			$j++;		
		} 
		
		return $this->build_table_h($array,$lista,$cnombre);		
	}

	function build_table($array,$head,$ntabla){
		$tabla.="<table border= '1'><tr>";
		for($i=0;$i<=count($head);$i++){
			$tabla.= "<td>".$head[$i]."</td>";
		}
		$tabla.="</tr>";

		for($j=0;$j<count($array);$j++){
			$tabla.="<tr>";
			for($i=0;$i<count($head);$i++){
			$tabla.= "<td>".$array[$j][$i]."</td>";	
			}			
			$tabla.="</tr>";
		}
		$tabla.="</table>";
		$this->atras($ntabla);	 
		return $tabla;
	}	

	function build_table_h($array,$head,$ntabla){
		$tabla.="<table border= '1'>";
		/*for($i=0;$i<=count($head);$i++){
			$tabla.= "<td>".$head[$i]."</td>";
		}
		$tabla.="</tr>";
*/
		//for($j=0;$j<count($head);$j++){
			
			for($i=0;$i<count($head);$i++){
				$tabla.="<tr>";
			$tabla.= "<td>".$head[$i]."</td>";
			$tabla.= "<td>".$array[0][$i]."</td>";	
			$tabla.="</tr>";
			}			
			
	//	}
		$tabla.="</table>";
		$this->atras($ntabla);	 
		return $tabla;
	}		
	function build_table_action($array,$head,$ntabla){		
		$tabla.="<table border= '1'><tr>";
		
		for($i=0;$i<=count($head);$i++){
			$tabla.= "<td>".$head[$i]."</td>";
		}
		$tabla.="<td>*</td>";
		$tabla.="</tr>";

		for($j=0;$j<count($array);$j++){
			$tabla.="<tr>";
			for($i=0;$i<=count($head);$i++){
			$tabla.= "<td>".$array[$j][$i]."</td>";	
			}			
			$tabla.="<td>
						<a href= 'page1.php?id=".$array[$j][0]."&act=v&c=".$ntabla."'>V</a>  
						<a href= 'page1.php?id=".$array[$j][0]."&act=e&c=".$ntabla."'>E</a>  
						<a href= 'page1.php?id=".$array[$j][0]."&act=a&c=".$ntabla."'>A</a>  
					 </td>"; 
			$tabla.="</tr>";
		}
		$tabla.="</table>";
		$tabla.="<a href='page1.php?c=".$ntabla."&act=a'>Nuevo</a>";

		$this->atras($ntabla);	 
		return $tabla;
	}

	function delete($class, $id){
		$this->connexion;
		$tabla  =$class->tableName(); 
		$cnombre  =get_class($class); 
		$llave	=$class->llave(); 
		$sql = "DELETE FROM ".$tabla." WHERE ".$llave." = ".$id;
		$asql=mysql_query($sql);
		$this->atras($cnombre);	
		echo "registro eliminado";
	}

	function atras($ntabla){
		echo  "<a href='page1.php?c=$ntabla'>home</a>";
	}

	function form($class,$id){
		$campos =$class->atributos();
		$lista = explode(',', $campos);
		$cnombre  =get_class($class); 
		$llave	=$class->llave(); 
		$nclass=get_class($class);
		$form.="<form method='GET'>";
		
		$array=$class->lis($class,$id);	 
		
		for($i=0;$i<count($lista);$i++){
			
			if ($llave!=$lista[$i]){
				$form.=$lista[$i].":";
				$form.="<input type= 'text' id='".$lista[$i]."'  name='".$lista[$i]."' value = '".$array[0][$i]."'><br>";				
			}
			if ($llave==$lista[$i]){
				$form.="<input type= 'hidden' id='".$lista[$i]."'  name='".$lista[$i]."' value = '".$array[0][$i]."'><br>";				

			}
			
		}
		$form.="<input type= 'hidden' id='c'  name='c' value='$nclass'>";
		if($id>0){
		$form.="<input type= 'hidden' id='act'  name='act' value='u'  >";
		}else{
		$form.="<input type= 'hidden' id='act'  name='act' value='s'  >";	
		}
		
		$form.="<input type='submit' value='guardar'>";
		$this->atras($cnombre);	
		echo $form;
	}

	function save($class,$array){
		$this->connexion;
		$campos =$class->atributos();
		$tabla  =$class->tableName(); 
		$llave	=$class->llave(); 
		$cnombre  =get_class($class); 
		$campos =$class->atributos();
		$lista = explode(',', $campos);
		for($i=0;$i<count($lista);$i++){
			
			$array[$i]="'".$_REQUEST[$lista[$i]]."'";
		}


		$xx=implode(",", $array);


		$sql="INSERT INTO ".$tabla."(".$campos.")VALUES(".$xx.")";
		echo $sql;
		echo "id".$id;
		$asql=mysql_query($sql);
		
		$this->atras($cnombre);	
		echo "registro insertado correctamente";
		
	}

function update($class){
		$this->connexion;
		$campos =$class->atributos();
		$tabla  =$class->tableName(); 
		$llave	=$class->llave(); 
		$cnombre  =get_class($class); 
		$campos =$class->atributos();
		$lista = explode(',', $campos);
		for($i=0;$i<count($lista);$i++){
			
			//$array[$i]="'".$_REQUEST[$lista[$i]]."'";
			if ($llave!=$lista[$i]){				
				$array[$i]=$lista[$i]." = '".$_REQUEST[$lista[$i]]."' ";
				
			}

			if ($llave==$lista[$i]){				
				$yy=$_REQUEST[$lista[$i]];
			}
		}


		$xx=implode(",", $array);


		$sql="UPDATE  ".$tabla." SET ".$xx." WHERE ".$llave." = ".$yy;
		
		
		$asql=mysql_query($sql);
		
		$this->atras($cnombre);	
		echo "registro actualizado correctamente";
		
	}	
}
?>
