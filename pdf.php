<?php	
include "config.php";

include "site.info.php";

ob_start();

if(isset($_REQUEST["action"])) 
	$_action = txtSec($_REQUEST["action"]);


switch($_action){
	
	case "productmore":
		include "pages/product/more.sys.php";
		include "pages/product/more.pdf.php";
	break;	
}


$content = ob_get_clean();

require_once "assets/pdfinvoice/html2pdf.class.php";
try
{
	$html2pdf = new HTML2PDF('P', 'A5', 'en', true, 'UTF-8', array(15,10,10,0));
	$html2pdf->setDefaultFont('freeserif');
	$html2pdf->writeHTML("", 0);
	$html2pdf->Output('pdf.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e."dd";
	exit;
}

?>