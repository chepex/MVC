<?php 
$user = $_REQUEST['name'];
$pass = $_REQUEST['password'];

$connection=mysql_connect("127.0.0.1","root","mario13") or die("No esta el server");
mysql_select_db("utec",$connection) or die("no existe la BD");

/**Verificamos las credenciales del usuario para obtener su rol**/
$res=mysql_query("select * from usuarios where usuario = '$user' and pass = '$pass'");
While ($row=mysql_fetch_array($res))
{
$rol=  $row[4]."<br>";
}
if($rol < 1 ){
echo "<H1> Lo sentimos su usuario no tiene acceso a ningun menu..";
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TemplateWorld.com Template - Web 2.0</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<!--body start -->
<div id="body">
<!--left start -->
<div id="left">
<a href="index.html"><img src="images/logo.gif" alt="import" width="300" height="108" border="0" class="logo" /></a>
<!--member start -->
<div id="member">
<h2>Members Login</h2>
<form name="memberLogin" action="#" method="post">
<label>Enter Name</label>
<input type="text" name="name" class="txtBox" />
<label>Enter Password</label>
<input type="password" name="password" class="txtBox" />
<a href="#">Forget Password?</a>
<input type="submit" name="login" class="login" value="Login" />
<br class="spacer" />
</form>
</div>
<!--member end -->
<!--ser start -->
<div id="ser">
 

<br class="spacer" />
</div>
<!--ser end -->
<!--help start -->
<div id="help">
  

<br class="spacer" />
</div>
<!--help end -->
<!--story start -->
<div id="story">

</div>
<!--story end -->
<br class="spacer" />
</div>
<!--left end -->
<!--right start -->
<div id="right">
<p class="tollFree">tollfree</p>
<p class="num">01-3245-5560</p>
<ul class="nav">
<?php
/**recorremos la tabla de roles por menu con el rol del usuario que ha ingresado**/
$menu2=mysql_query("    
  SELECT descripcion, link FROM roles_x_menus rm , menus m 
WHERE rm.id_menu =  m.id 
AND id_rol= '$rol'");
While ($row2=mysql_fetch_array($menu2))
{
  $mm= $row2[0];
  $link= $row2[1];
  
  
echo "<li><a href='$link'  target='content1' >".$mm."</a></li>";
}  
?>
</ul>





<p class="rightTxt">l<br />
</p>
<!--right1 start -->
<div id="right1">

<ul class="gal">
<li></li>
<li></li>
<li class="noMargin"></li>
<li></li>
<li></li>
<li class="noMargin"></li>
</ul>

<br class="spacer" />
</div>
<!--right1 end -->
<!--right2 start -->
<div id="right2">
<h2>Testimonials</h2>
<h3>&nbsp;</h3>
<a href="#"><img src="images/special_pic.gif" alt="banner" width="258" height="111" border="0" /></a>
<br class="spacer" />
</div>
<!--right2 end -->
<br class="spacer" />
</div>
<!--right end -->
<br class="spacer" />
</div>
<!--body end -->
<!--footer start -->
<div id="footer">
 
</div>
<!--footer end -->
</body>
</html>
