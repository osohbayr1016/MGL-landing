<?php


function convertPhotoHtml($text){
	$text = preg_replace('/\r/', '', $text);
	$text = preg_replace('/\n/', '', $text);
	$noword = array(  "/",
					"\"",
					"'");
	
	$nulls   = array(   "\/",
					 "&ldquo;",
					 "`");
	
	$text = str_replace($noword, $nulls, $text);
	
	return $text;

}
function user_func($str, $charlist = false)

{
	if ($charlist === false)
	{
		return rtrim($str);

	}


		$str = rtrim($str, $charlist);

	



	return $str;

}

function username_p($username)

{

	$username = substr(htmlspecialchars(str_replace("\'", "'", trim($username))), 0, 5555);
	$username = user_func($username, "\\");
	$username = str_replace("'", "\'", $username);

	return $username;

}

function emailOK($str) {
	if(empty($str)) return false;
	if(!ereg("@",$str)) return false;
	if(!ereg("\.",$str)) return false;
	list($user, $host) = explode("@", $str);
	if((empty($user)) || (empty($host))) return false;
	
	$badChars = "[ ]+| |\+|=|[|]|{|}|`|\(|\)|,|;|:|!|<|>|%|\*|/|'|\"|~|\?|#|\\$|\\&|\\^|www[.]";
	return !eregi($badChars, $str);
}

function txtSec($str){
	$str = str_replace(  "/*", "" , $str);
	$str = username_p($str);
	return $str;
}

function ipEditRe($ip){
	$ipArr = explode(".",$ip);
	$hideIPstr = $ipArr[0].".".$ipArr[1].".".$ipArr[2].".xxx";
	return $hideIPstr;
}

function newsrollTextImg($newsTxt){
	
	$newsTxt = str_replace('../../postpic/image/', '/newsimg/', $newsTxt); 	
	$newsTxt = str_replace('/postpic/image/', '/newsimg/', $newsTxt); 	
	$newsTxt = str_replace('/image/', '/newsimg/', $newsTxt); 
	
	$newsTxt = str_replace('font-family:', '', $newsTxt); 
	$newsTxt = str_replace('color:', '', $newsTxt); 
	$newsTxt = str_replace('line-height:', '', $newsTxt); 
	$newsTxt = str_replace('font-size:', '', $newsTxt); 
	$newsTxt = str_replace('height=', '', $newsTxt);  
	
	//$newsTxt = preg_replace('/(\<img[^>]+)(style\=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $newsTxt );
	
	//$newsTxt = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $newsTxt);
	$newsTxt = preg_replace('/(<iframe[^>]*>)(.*?)(<\/iframe>)/i', '<div class="embed-responsive embed-responsive-16by9">$1$3</div>', $newsTxt);
	
	
	
	return $newsTxt;
	
}

function newsPicFnc($newsID,$imgPath="",$thumb="n"){
	
	$rootPath = "/newsimg/";
	
	$imgPath = str_replace('https://cpw.nomun.mn', '', $imgPath);  
	$imgPath = str_replace('http://localhost:8088', '', $imgPath);  
	
	if($thumb=="y")
		$rootPath = "/newstimg/";
		
	if($imgPath!=""){
		
		if(substr($imgPath,0,7)=="/image/")
			$imgPath = substr($imgPath,7,400);
			
		if(substr($imgPath,0,9)=="/postpic/"){
			$imgPath = substr($imgPath,9,400);
				
			if(substr($imgPath,0,6)=="image/")
				$imgPath = substr($imgPath,6,400);
			
			$imgPath = $rootPath.$imgPath;
			
		}
		
		
		
	}
	else{
		
		$imgPath = "/newsimg/news/".$newsID.".jpg";
		
	}
	
	return cdnUrl($imgPath);
}

/*
	Харилцагчийн вэб хаягийг цэвэрлэнэ. Админ "mglenc.com" гэж бичсэн ч
	холбоос сайт дотор биш, гадаад хаяг руу зөв очно.
*/
function partnerLinkFnc($link){
	$link = trim($link);

	if($link=="")
		return "";

	if(substr($link,0,1)=="/" || substr($link,0,1)=="#")
		return $link;

	if(preg_match('#^(https?:|mailto:|tel:)#i',$link))
		return $link;

	return "https://".$link;
}

function menuLinkFunc($menuObj)
{
	$gloConstSiteURL	=	"/";
	
	switch($menuObj['pageType']){
		case "page":
		case "video":
			$menuLink = $gloConstSiteURL."page/".$menuObj['id']."/";
		break;
		case "home":
			$menuLink = $gloConstSiteURL;
		break;
		default:			
			$menuLink = $gloConstSiteURL.$menuObj['pageType'];
		break;				
	}
	
	
	if($menuObj['staticlink']!="")
		$menuLink = $menuObj['staticlink'];
				
	
	return $menuLink;
}
function menuNameFunc($menuObj)
{
	$menuNameOverrideArr = array(
		"ОФФИС"	=> "ПРОФАЙЛ",
		"Оффис"	=> "Профайл",
		"OFFICE"	=> "PROFILE",
		"Office"	=> "Profile",
	);

	$menuName = $menuObj['name'];

	if(isset($menuNameOverrideArr[$menuName]))
		$menuName = $menuNameOverrideArr[$menuName];

	return $menuName;
}
function formatTree($tree, $parent, $parentName, $mainIDName){
	
	$tree2 = array();
	if(count($tree)>0)
	foreach($tree as $i => $item){
		if($item[$parentName] == $parent){
			$tree2[$item[$mainIDName]] = $item;
			$tree2[$item[$mainIDName]]['sub'] = formatTree($tree, $item[$mainIDName], $parentName, $mainIDName);
		}
	}

	return $tree2;
	
}

function timeStampFnc($session_time){ 
 
	$time_difference = time() - $session_time ; 
	$seconds = $time_difference ; 
	$minutes = round($time_difference / 60 );
	$hours = round($time_difference / 3600 ); 
	$days = round($time_difference / 86400 ); 
	$weeks = round($time_difference / 604800 ); 
	$months = round($time_difference / 2419200 ); 
	$years = round($time_difference / 29030400 ); 
	
	if($seconds <= 60)
	{
		if($seconds==0)
			$seconds =1;
			
		echo "Яг одоо"; 
	}
	else if($minutes <=60)
	{
	   if($minutes==1 || $minutes==0)
	   {
		 echo "1 минут"; 
		}
	   else
	   {
	   echo "$minutes минут"; 
	   }
	}
	else if($hours <=24)
	{
	   if($hours==1)
	   {
	   echo "1 цаг";
	   }
	  else
	  {
	  echo"$hours цаг";
	  }
	}
	else if($days <=7)
	{
	  if($days==1)
	   {
	   echo "1 өдөр";
	   }
	  else
	  {
	  echo "$days өдөр";
	  }
	
	
	  
	}
	else
	{
	
	echo date('Y/m/d', $session_time);
	
	}

} 
 

/*
	Image delivery. With $gloCdnBase empty these return the same local paths
	the site has always used; once it points at the Cloudflare Worker the
	same images are served from R2, falling back to this server for anything
	not uploaded to R2 yet.
*/
function cdnUrl($path){
	global $gloCdnBase;

	if(!isset($gloCdnBase) || $gloCdnBase=="" || $path=="" || substr($path,0,1)!="/")
		return $path;

	return rtrim($gloCdnBase,"/").$path;
}

function absUrl($url){
	if($url=="" || substr($url,0,1)!="/")
		return $url;

	/* Канон https хаяг (class/seo.class.php) */
	if(class_exists("SeoMeta"))
		return SeoMeta::absUrl($url);

	return "http://".$_SERVER['HTTP_HOST'].$url;
}

function picUrl($folder,$file){
	if($file=="")
		return "";

	return cdnUrl("/pics/".$folder."/".$file);
}

?>