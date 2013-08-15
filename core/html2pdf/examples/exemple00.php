<?php
/**
 * HTML2PDF Librairy - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @author      Laurent MINGUET <webmaster@html2pdf.fr>
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 */
$content.= 	"<table><tr><td>id</td><td>descripcion</td></tr>";
    // get the HTML
for($i=0;$i<800;$i++){
    
	$content.= 	"<tr><td>$i</td><td>descripcion$i</td>
	<td>$i</td><td>descripcion$i</td>

	</tr> ";
	
	}
$content.="</table>";
    // convert in PDF
    require_once(dirname(__FILE__).'/../html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF();
//      $html2pdf->setModeDebug();
        $html2pdf->setDefaultFont('Arial');
        $html2pdf->writeHTML($content);
        $html2pdf->Output('exemple00.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
