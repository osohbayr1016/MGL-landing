<?php

$ceoID = (int)txtSec($_POST["ceoID"]);
$pic = isset($_POST["frmPic"]) ? txtSec($_POST["frmPic"]) : "";

if ($ceoID < 1 || $pic === "") {
	orderAjaxDone(array("ok" => 0));
}

$db->where("ceoID", $ceoID);
$db->update($db_ceo, array("ceoPic" => $pic));
orderAjaxDone(array("ceoID" => $ceoID));
