<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);
require_once('db_abstract_model.php');
class view_Parametros extends DBAbstractModel{
	public $html="";
	
	public function get_template($form='get',  $class='') {
		$file = '../site_media/html/'.$class.'/'.$class.'_'.$form.'.html';    
		$template = file_get_contents($file);
		return $template;
	}
	
	public function render_dinamic_data($html, $data) {
		foreach ($data as $clave=>$valor) {
			$html = str_replace('{'.$clave.'}', $valor, $html);
		}
		return $html;
	}
	
	public function retornar_vista() {

		
		$this->html = str_replace('{menu1}', $this->menu1(), $this->html);
		$this->html = str_replace('{menu2}', $this->menu2(), $this->html);
		print $this->html;
		print "<script>
		$('#home').click(function(){
			$('#menu-modulo').toggle	();
		} )
		</script>";

	}
	
	public function render_html($Etiquetas=array()){
		foreach($Etiquetas as $campo=>$valor){
			$this->html = str_replace($campo, $valor, $this->html); 
		}
		return $this->html;
	}

}




?>
