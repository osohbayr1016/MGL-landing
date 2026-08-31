<?php


$db->where ("createDate", "UNIX_TIMESTAMP('".date("Y-m-d 00:00:00")."')", ">");
$newsTodaySum = $db->getValue($db_newsroll,"count(*)");


$db->where ("createDate", "UNIX_TIMESTAMP('".date("Y-m-01 00:00:00")."')", ">");
$newsMonthSum = $db->getValue($db_newsroll,"count(*)");


$db->where ("createDate", "UNIX_TIMESTAMP('".date("Y-01-01 00:00:00")."')", ">");
$newsYearSum = $db->getValue($db_newsroll,"count(*)");


$searchKey= "";
$addWhere = "";


$db->orderBy("A.edate","DESC");
$db->join("$db_newsroll B", "B.newsID = A.feild_id", "INNER");
$db->where("A.table_type", "v");
$commentArr = $db->get("$tbl_comment A", null, "A.*, B.newsTitle");
		
		
		
$widJsArr["home"] = $clkMenuModDir."home.js.php";
$incPageUrl = $clkMenuModDir."home.php";
?>