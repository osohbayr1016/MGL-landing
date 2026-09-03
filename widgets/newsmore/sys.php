<?php 


$newsID = txtSec($_REQUEST["newsID"]);
$cmdNewsID = $newsID;





$db->join("$db_newsmore B", "B.newsID = A.newsID", "LEFT");
$db->where("A.newsID", $newsID);
$newsObj = $db->getOne("$db_newsroll A", null, "A.*,B.*");


$db->orderBy("`newsID`","ASC");
$db->where ("newsID", $newsID,">");
$nextNewsObj = $db->getOne($db_newsroll,"newsID, newsTitle"); 

$db->where ("newsID", $newsID,"<");
$db->orderBy("`newsID`","DESC");
$prewNewsObj = $db->getOne($db_newsroll,"newsID, newsTitle"); 


$addPageTitle = htmlspecialchars($newsObj["newsTitle"]);
$siteInfoDes = preg_replace('/\s+/', ' ',convertPhotoHtml(strip_tags($newsObj["newsDesc"])));	
$siteInfoImg   = absUrl(newsPicFnc($newsObj["newsID"],$newsObj["newsPic"]));
		

$widJsArr["newsview"] = $gloConstWidDir."newsview/view.js.php";

$commNewsID = $newsID;

?>