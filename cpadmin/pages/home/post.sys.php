<?php

$sysReturnLink =  "/home";

switch($_POST["frmPost"]){
	
	case "cmdDel":
		
		$data				=	"";
		$data["isDel"]		=	"y";

		$db->where ('id', txtSec($_POST["frmDelID"]));
		$db->update ($tbl_comment, $data);

	
	break;
	case "cmdRes":
		
		
		$data				=	"";
		$data["isDel"]		=	"n";

		$db->where ('id', txtSec($_POST["frmDelID"]));
		$db->update ($tbl_comment, $data);
	
	break;
	
}


?>