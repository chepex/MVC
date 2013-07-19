<link href='style.css' rel='stylesheet' type='text/css' />

<?php
session_start();
require_once 'usuario.php';
require_once 'home.php';
require_once 'menu.php';
require_once 'role.php';
require_once 'roles_x_menus.php';

class page{
	var $items;  
	function body() {
		echo "<div id='body'>";
	}
	function left() {
      
		echo "
		<div id='left'>
    <a href='index.html'><img src='images/logo.gif' alt='import' width='300' height='108' border='0' class='logo' /></a>
	
	<div id='member'>
	<h2>Members Login</h2>
	<form name='memberLogin' action='#' method='post'>
	<label>Enter Name</label>
	<input type='text' name='user' id= 'user' class='txtBox' />
	<label>Enter Password</label>
	<input type='password' name='pass' id= 'pass' class='txtBox' />
	<a href='#'>Forget Password?</a>
	<input type='submit' name='login' id = 'login' class='login' value='Login' />
	<br class='spacer' />
	</form>
	</div>
	</div>
	
	";
    }

    function ser(){
    	echo "<div id='ser'>
	<br class='spacer' />
	</div>";

    }

    function help(){
    echo "<!--help start -->
	<div id='help'>
	<br class='spacer' />
	</div>"	;
    }

    function story(){
    	echo "<div id='story'>	</div>	";    	
    }
    

	function right() {
        echo "<div id='right'>
				<p class='tollFree'>tollfree</p>
				<p class='num'>01-3245-5560</p>";
    }    

    function nav($rol){
	$this->coneccion;


    	echo "<ul class='nav'>";
    	if(isset($rol)){
				$menu2=mysql_query("    
				SELECT descripcion, link,clase FROM roles_x_menus rm , menus m 
				WHERE rm.id_menu =  m.id 
				AND id_rol= '$rol'") ;
				While ($row2=mysql_fetch_array($menu2))
				{
				  $mm= $row2[0];
				  $link= $row2[1];
				  $clase= $row2[2];
				echo "<li><a href='$link?c=$clase'>$mm</a></li>";
				}  
		  
		  		echo "</div>";

		}

    }
	
       

    function footer(){
echo "
<br class='spacer' />
</div>
<br class='spacer' />
</div>
<div id='footer'>
 </div>";
    }  

 

    public function coneccion() {
        require_once 'config.php';
        // connecting to mysql
        $con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
        // selecting database
        mysql_select_db(DB_DATABASE);
 
        // return database handler
        return $con;
    }

	function right1($class,$act,$id){			
			echo "<div id='right1'>";
			$x="";
			$tipo  =$class->tipo(); 


			if(isset($class)){
				if($tipo=="class") {
					if($act=="v")	$x= $class->listabyid($class,$id);
					if($act=="e")	$x= $class->delete($class,$id);
					if($act=="a")	$x= $class->form($class,$id);
					if($act=="")	$x= $class->lista($class);
					if($act=="s")	$x= $class->save($class);	
					if($act=="u")	$x= $class->update($class,$id);	
				}	
				if($tipo=="page") {	
					if($act=="page")	$x= $class->page();			
				}
			}
			echo $x;
			echo "</div>";
	}
	function right2(){

			echo "<div id='right2'>
<h2>Testimonials</h2>
<h3>&nbsp;</h3>
<a href='#'>
<img src='images/special_pic.gif' alt='banner' width='258' height='111' border='0' /></a>
<br class='spacer' />
</div>";
	}
}
$act = $_REQUEST['act'];
$c = $_REQUEST['c'];
$id= $_REQUEST['id'];
$login= $_REQUEST['login'];
/*Si se logea*/
if($login=="Login"){
$user1= $_REQUEST['user'];
$pass= $_REQUEST['pass'];
$user = new usuario;	
$rol= $user->get_rol($user1,$pass);
}
$rol =$_SESSION['rol'];
$x = strlen($c);
if($x>1){
$cl = new $c;		
}else{
$cl = new home;	
$act="page";
}
$page = new page;
$page->body();
$page->left();
$page->right();
$page->nav($rol);
if($rol){
$page->right1($cl,$act,$id);	
} 
$page->right2();
$page->footer();
?>
