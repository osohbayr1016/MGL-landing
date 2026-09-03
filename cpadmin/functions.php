<?php
function convert_html($text){

	
	return $text;
}

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

/**********************/

function myupload($file,$path,$filename){   
	
	if(trim($file)!="") {
    $to=getcwd().$path.$filename;
    $from=$_FILES[$file]['tmp_name'];

    if(move_uploaded_file($from,$to)) return 1;

    else return 0;

    }
	
	return 0;

}
function isStrrpos( $str, $len ) {	
		if ( strlen($str) <= $len ) return 1;
		else return 0;
}


function random_str($len)

	{ $i=1;

		while ( $i <= $len )
		{
		if ( rand(0,1) == 1 ) { $p = $p."".chr ( rand ( 48, 57 ) ); } 	

		else { $p = $p."".chr ( rand ( 65, 90 ) ); }
		$i++;
		}
		return $p;
	}


function pageNavPages($allImg_count,$_pp,$_p){
	
		$pageCount = $allImg_count/$_pp;
		$pageCount_floor = floor($pageCount);
		if($pageCount_floor!=$pageCount) $pageCount=$pageCount_floor+1;
		
		
		if($pageCount<$_p)
			$_p = $pageCount;
			
		if($_p <= 1 ){
			$ss = 0;
		}
		else{
			$ss = $_pp*($_p - 1);
		}
		$return_arr=array("$ss ,$_pp",$pageCount);
		return $return_arr;
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
function strtodates($str){
	//return $edate;
	$dateArr = explode(" ",trim($str));
	$day = $dateArr[0];
	$mon = $dateArr[1];
	$yea = $dateArr[2];
	
	switch($mon){
		case "Jan":
			$mon = 1;
		break;
		case "Feb":
			$mon = 2;
		break;
		case "Mar":
			$mon = 3;
		break;
		case "Apr":
			$mon = 4;
		break;
		case "May":
			$mon = 5;
		break;
		case "Jun":
			$mon = 6;
		break;
		case "Jul":
			$mon = 7;
		break;
		case "Aug":
			$mon = 8;
		break;
		case "Sep":
			$mon = 9;
		break;
		case "Oct":
			$mon = 10;
		break;
		case "Nov":
			$mon = 11;
		break;
		case "Dec":
			$mon = 12;
		break;
	}
	
	$date = $yea."-".$mon."-".$day;
	return $date;
}
function txtSec($str){
	$str = str_replace(  "/*", "" , $str);
	$str = username_p($str);
	$str = convert_html($str );
	return $str;
}


function ipEditRe($ip){
	$ipArr = explode(".",$ip);
	$hideIPstr = $ipArr[0].".".$ipArr[1].".".$ipArr[2].".xxx";
	return $hideIPstr;
}

function newsrollTextImg($newsTxt){
	
	$newsTxt = str_replace('font-family:', '', $newsTxt); 
	$newsTxt = str_replace('color:', '', $newsTxt); 
	$newsTxt = str_replace('line-height:', '', $newsTxt); 
	$newsTxt = str_replace('font-size:', '', $newsTxt); 
	$newsTxt = str_replace('text-align:', '', $newsTxt); 
	
	$newsTxt = preg_replace('/(\<img[^>]+)(style\=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $newsTxt );
	
	$newsTxt = preg_replace('/(<iframe[^>]*>)(.*?)(<\/iframe>)/i', '<div class="embed-responsive embed-responsive-16by9">$1$3</div>', $newsTxt);
	
	
	
	return $newsTxt;
	
}

function checkValues($value)
{
	$value = trim($value);
	if (get_magic_quotes_gpc()) 
	{
		$value = stripslashes($value);
	}
	$value = strtr($value, array_flip(get_html_translation_table(HTML_ENTITIES)));
	$value = strip_tags($value);
	$value = htmlspecialchars($value);
	return $value;
}	

function menuLinkFunc($menuObj)
{
	$gloConstSiteURL	=	"/";
	
	switch($menuObj['pageType']){
		case "page":
		case "map":
			$menuLink = $gloConstSiteURL.$menuObj['pageType']."/".$menuObj['id']."/";
		break;
		case "home":
			$menuLink = $gloConstSiteURL;
		break;
		default:
			$menuLink = $gloConstSiteURL.$menuObj['pageType'];
		break;				
	}
	
	if($menuObj['newsType']!="")
		$menuLink = $gloConstSiteURL."c/".$menuObj['newsType']."/";
	
	if($menuObj['static_link']!="")
		$menuLink = $menuObj['static_link'];
	
	if($menuObj['staticType']!="")
		$menuLink = $gloConstSiteURL.$menuObj['staticType'];
				
	
	return $menuLink;
}
function bannerSwfRep($newsTxt){
	$newsTxt = str_replace('/postpic/', '', $newsTxt); 
	return $newsTxt;
	
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
function userProImgLink($userID,$imgWith=32){
	
	return "/profilePic/".$imgWith."/".random_str(10).md5($userID);
}
function time_stamp($session_time) 
{ 
 
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
		
	echo "$seconds секунтын өмнө"; 
}
else if($minutes <=60)
{
   if($minutes==1 || $minutes==0)
   {
     echo"нэг минутын өмнө"; 
    }
   else
   {
   echo"$minutes минутын өмнө"; 
   }
}
else if($hours <=24)
{
   if($hours==1)
   {
   echo"нэг цагийн өмнө";
   }
  else
  {
  echo"$hours цагийн өмнө";
  }
}
else if($days <=7)
{
  if($days==1)
   {
   echo"нэг өдрийн өмнө";
   }
  else
  {
  echo"$days өдрийн өмнө";
  }


  
}
else if($weeks <=4)
{
  if($weeks==1)
   {
   echo"нэг долоо хонгийн өмнө";
   }
  else
  {
  echo"$weeks долоо хонгийн өмнө";
  }
 }
else if($months <=12)
{
   if($months==1)
   {
   echo"нэг сарын өмнө";
   }
  else
  {
  echo"$months сарын өмнө";
  }
 
   
}

else
{
if($years==1)
   {
   echo"нэг жилийн өмнө";
   }
  else
  {
  echo"$years жилийн өмнө";
  }


}
 


} 
function widPicFnc($imgPath){
	$rootPath = "/newsimg/";
	
	if(substr($imgPath,0,7)=="/image/")
		$imgPath = substr($imgPath,7,400);
		
	if(substr($imgPath,0,9)=="/postpic/")
		$imgPath = substr($imgPath,9,400);
			
	if(substr($imgPath,0,6)=="image/")
		$imgPath = substr($imgPath,6,400);
	
	
	return cdnUrl($rootPath.$imgPath);
}
function newsPicFnc($newsID,$imgPath="",$thumb="n"){
	
	$rootPath = "/newsimg/";
	
	if($thumb=="y")
		$rootPath = "/newstimg/";
		
	if($imgPath!=""){
		$oldPath = $imgPath;
		
		if(substr($imgPath,0,7)=="/image/")
			$imgPath = substr($imgPath,7,400);
			
		if(substr($imgPath,0,9)=="/postpic/")
			$imgPath = substr($imgPath,9,400);
				
		if(substr($imgPath,0,6)=="image/")
			$imgPath = substr($imgPath,6,400);

		if($imgPath!=$oldPath)
			$imgPath = $rootPath.$imgPath;
	}
	else{
		
		$rootPath = "/newsimg/news/".$newsID.".jpg";
		
	}
	
	return cdnUrl($imgPath);
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

function picUrl($folder,$file){
	if($file=="")
		return "";

	return cdnUrl("/pics/".$folder."/".$file);
}

/* Same thing for the admin panel, whose document root is cpadmin/. */
function admPicUrl($folder,$file){
	global $gloCdnBase;

	if($file=="")
		return "";

	if(isset($gloCdnBase) && $gloCdnBase!="")
		return rtrim($gloCdnBase,"/")."/pics/".$folder."/".$file;

	return "/postpic/".$folder."/".$file;
}

?>