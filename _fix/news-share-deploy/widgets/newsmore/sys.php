<?php 


$newsID = (int)txtSec($_REQUEST["newsID"]);
$cmdNewsID = $newsID;

$newsObj = null;
$newsNotFound = false;

/* Мэдээг нэг л удаа уншина — мета болон хуудасны агуулга хоёулаа
   энэ $newsObj-ийг хэрэглэнэ. (Сайтын хэлийг site.info.php мэдээний
   хэлээр аль хэдийн тохируулсан тул crawler ч зөв хэлээр авна.) */
if($newsID>0){
	$db->join("$db_newsmore B", "B.newsID = A.newsID", "LEFT");
	$db->where("A.newsID", $newsID);
	$newsObj = $db->getOne("$db_newsroll A", null, "A.*,B.*");
}

if(empty($newsObj) || empty($newsObj["newsID"])){

	/* Устгагдсан / байхгүй / буруу дугаартай мэдээ: 404 + сайтын
	   өгөгдмөл мета (индексжүүлэхгүй). Мэдээний og tag гаргахгүй. */
	$newsNotFound = true;
	$newsObj = array("newsID"=>0,"newsTitle"=>"","newsDesc"=>"","newsBody"=>"","newsPic"=>"","createDate"=>0,"newsCatID"=>0,"newsCat"=>"");

	if(!headers_sent())
		http_response_code(404);

	$gloMeta = SeoMeta::forNotFound($gloMeta);
	$nextNewsObj = null;
	$prewNewsObj = null;

}
else{

	$db->orderBy("`newsID`","ASC");
	$db->where ("newsID", $newsID,">");
	$nextNewsObj = $db->getOne($db_newsroll,"newsID, newsTitle"); 

	$db->where ("newsID", $newsID,"<");
	$db->orderBy("`newsID`","DESC");
	$prewNewsObj = $db->getOne($db_newsroll,"newsID, newsTitle"); 

	/* Social share мета — мэдээ бүр өөрийн гарчиг, товч, зураг, огноотой.
	   Canonical хаяг: https://mglenc.com/news/{newsID} (/n/{id} ч ижил
	   хуудас руу очно, гэхдээ og:url/canonical нь үргэлж /news/{id}). */
	$newsCanonicalUrl = SeoMeta::baseUrl()."/news/".$newsObj["newsID"];
	$newsCoverPath    = newsPicFnc($newsObj["newsID"],$newsObj["newsPic"]);

	$gloMeta = SeoMeta::forNews($gloMeta, $newsObj, $newsCanonicalUrl, $newsCoverPath);

	/* Хуучин хувьсагчид — бусад template унших бол ижил утгатай байг */
	$addPageTitle  = $gloMeta["title"];
	$siteInfoDes   = $gloMeta["description"];
	$siteInfoImg   = $gloMeta["image"];
	$siteInfoThisUrl = $gloMeta["url"];

}

$widJsArr["newsview"] = $gloConstWidDir."newsview/view.js.php";

$commNewsID = $newsID;

?>