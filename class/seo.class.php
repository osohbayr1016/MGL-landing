<?php
/*
	Хуудасны <head> мета (title / description / canonical / Open Graph /
	Twitter card / JSON-LD) нэг газраас гаргах туслах.

	Ажиллах зарчим:
	  1. site.info.php   — сайтын өгөгдмөл мета ($gloMeta) бүрдүүлнэ.
	  2. Хуудас бүр       — шаардлагатай талбараа $gloMeta дээр дарж бичнэ
	                        (жишээ: widgets/newsmore/sys.php мэдээний
	                        гарчиг, товч, зураг, огноог тавина).
	  3. skin/new/home.php — SeoMeta::render($gloMeta) дуудаж <head>-д
	                        бүх tag-ийг сервер талд шууд бичнэ.

	Бүх утга түүхий (escape хийгээгүй) текст байх ёстой — escape-ийг
	render() өөрөө хийнэ. Тиймээс "&", "<", '"' зэрэг тэмдэгт болон монгол
	кирилл гарчиг HTML-ийг эвдэхгүй.

	PHP 7.2 дээр ажиллана (сервер дээр 7.2.34 байгаа) — mbstring байхгүй
	бол ч ажиллахаар preg /u-г ашигласан.
*/

class SeoMeta
{
	/* ---------------------------------------------------------------
	   Сайтын үндсэн хаяг (canonical / og:url / og:image-д хэрэглэнэ)

	   const.php дотор $gloConstSiteBaseUrl = "https://mglenc.com";
	   гэж заасан бол түүнийг, үгүй бол хүсэлтийн host-оор https
	   хаяг үүсгэнэ. "http://", "www." зэрэг хувилбар бүр дээр ижил
	   canonical гарахын тулд const.php-д заах нь зүйтэй.
	--------------------------------------------------------------- */
	public static function baseUrl()
	{
		global $gloConstSiteBaseUrl;

		if(isset($gloConstSiteBaseUrl) && trim($gloConstSiteBaseUrl)!="")
			return rtrim(trim($gloConstSiteBaseUrl),"/");

		$host = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "mglenc.com";
		$host = preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', $host);

		return "https://".$host;
	}

	/* Харьцангуй зам ("/newsimg/news/1.jpg") -> бүтэн https хаяг.
	   Аль хэдийн бүтэн хаяг бол хэвээр буцаана. */
	public static function absUrl($url)
	{
		$url = trim((string)$url);

		if($url=="")
			return "";

		/* Өөрийн сайтын http:// эсвэл www. хувилбар -> canonical base */
		if(preg_match('#^https?://([^/]+)(/.*)?$#i',$url,$m)){
			$ownHost = strtolower(parse_url(self::baseUrl(), PHP_URL_HOST));
			$urlHost = strtolower($m[1]);

			if($urlHost==$ownHost || $urlHost=="www.".$ownHost || "www.".$urlHost==$ownHost)
				return self::baseUrl().(isset($m[2]) && $m[2]!="" ? $m[2] : "/");

			return $url;
		}

		if(substr($url,0,2)=="//")
			return "https:".$url;

		if(substr($url,0,1)!="/")
			$url = "/".$url;

		return self::baseUrl().$url;
	}

	/* Одоо үзэж буй хуудасны canonical хаяг (query string-гүй). */
	public static function currentUrl()
	{
		$path = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "/";
		$path = parse_url($path, PHP_URL_PATH);

		if($path===null || $path===false || $path=="")
			$path = "/";

		return self::baseUrl().$path;
	}

	/* ---------------------------------------------------------------
	   Текст цэвэрлэх: HTML tag, entity, олон хоосон зай, админаас
	   орж ирдэг "\'" зэрэг үлдэгдлийг арилгаад цэвэр нэг мөр текст
	   буцаана. $max тэмдэгтээс урт бол үгийн зааг дээр таслана.
	--------------------------------------------------------------- */
	public static function plainText($text, $max = 0)
	{
		if(is_array($text) || is_object($text))
			return "";

		$text = (string)$text;

		if($text=="")
			return "";

		/* CKEditor-оос ирдэг мөр таслалт, <br>, </p> -> зай */
		$text = preg_replace('/<\s*br\s*\/?>|<\/p>|<\/div>|<\/li>|<\/h[1-6]>/i', ' ', $text);
		$text = strip_tags($text);

		/* Хоёр удаа хадгалагдсан entity-г бүрэн тайлна (&amp;amp; -> &) */
		$prev = null;
		$loop = 0;
		while($prev!==$text && $loop<3){
			$prev = $text;
			$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, "UTF-8");
			$loop++;
		}

		/* txtSec()/addslashes-ийн үлдэгдэл */
		$text = str_replace(array("\\'", '\\"', "\\\\"), array("'", '"', "\\"), $text);

		/* NBSP болон бүх төрлийн хоосон зайг нэг зай болгоно */
		$clean = preg_replace('/[\x{00A0}\x{200B}\x{FEFF}]/u', ' ', $text);
		$clean = ($clean===null) ? null : preg_replace('/\s+/u', ' ', $clean);

		/* Буруу UTF-8 орж ирвэл /u regex null буцаана — энгийн аргаар цэвэрлэнэ */
		if($clean===null)
			$clean = preg_replace('/\s+/', ' ', $text);

		$text = trim((string)$clean);

		if($max>0)
			$text = self::truncate($text, $max);

		return $text;
	}

	/* UTF-8 тэмдэгтийн тоогоор, үгийн зааг дээр таслана. */
	public static function truncate($text, $max)
	{
		if(self::strLen($text)<=$max)
			return $text;

		$cut = self::subStr($text, 0, $max);

		$space = self::lastSpace($cut);
		if($space>($max*0.6))
			$cut = self::subStr($cut, 0, $space);

		return rtrim($cut, " ,;:.-—–")."…";
	}

	public static function strLen($text)
	{
		if(function_exists("mb_strlen"))
			return mb_strlen($text, "UTF-8");

		return preg_match_all('/./us', $text, $m);
	}

	public static function subStr($text, $start, $len)
	{
		if(function_exists("mb_substr"))
			return mb_substr($text, $start, $len, "UTF-8");

		preg_match_all('/./us', $text, $m);
		return join("", array_slice($m[0], $start, $len));
	}

	private static function lastSpace($text)
	{
		preg_match_all('/./us', $text, $m);
		$chars = $m[0];

		for($i=count($chars)-1; $i>=0; $i--)
			if($chars[$i]==" ")
				return $i;

		return -1;
	}

	/* ---------------------------------------------------------------
	   Зураг: нийтэд харагдах зам ("/newsimg/news/12.jpg") -> серверийн
	   файлын зам. .htaccess-ийн rewrite-тай ижил тааруулна.
	   Олдохгүй бол false.
	--------------------------------------------------------------- */
	public static function localImagePath($publicPath)
	{
		global $gloCdnBase;

		$publicPath = trim((string)$publicPath);

		if($publicPath=="")
			return false;

		/* CDN хаягтай бол зөвхөн замыг нь үлдээнэ */
		if(isset($gloCdnBase) && $gloCdnBase!="" && stripos($publicPath, $gloCdnBase)===0)
			$publicPath = substr($publicPath, strlen(rtrim($gloCdnBase,"/")));

		if(preg_match('#^https?://#i',$publicPath))
			$publicPath = parse_url($publicPath, PHP_URL_PATH);

		$publicPath = "/".ltrim((string)$publicPath, "/");

		$root = self::docRoot();

		$mapArr = array(
			'#^/newsimg/(.+)$#'		=> "cpadmin/postpic/image/$1",
			'#^/newstimg/(.+)$#'	=> "cpadmin/postpic/_thumbs/Images/$1",
			'#^/pics/([A-Za-z]+)/([^/]+)/?$#'	=> "cpadmin/postpic/$1/$2",
		);

		foreach($mapArr as $pattern=>$target){
			if(preg_match($pattern, $publicPath)){
				$rel = preg_replace($pattern, $target, $publicPath);
				$rel = str_replace("..", "", $rel);
				return $root."/".$rel;
			}
		}

		/* Сайтын root дахь энгийн файл (жишээ: /assets/images/og-default.png) */
		$rel = str_replace("..", "", ltrim($publicPath,"/"));
		return $root."/".$rel;
	}

	public static function docRoot()
	{
		return rtrim(str_replace("\\","/",dirname(__DIR__)),"/");
	}

	/* Зургийн бодит хэмжээ, төрөл. Файл олдохгүй бол false. */
	public static function imageInfo($publicPath)
	{
		$file = self::localImagePath($publicPath);

		if($file===false || !is_file($file))
			return false;

		$size = @getimagesize($file);

		if($size===false || $size[0]<1 || $size[1]<1)
			return array("width"=>0,"height"=>0,"type"=>"");

		return array(
			"width"		=> (int)$size[0],
			"height"	=> (int)$size[1],
			"type"		=> isset($size["mime"]) ? $size["mime"] : ""
		);
	}

	/* Сайтын өгөгдмөл share зураг (1200x630, MGLENC лого). */
	public static function defaultImagePath()
	{
		return "/assets/images/og-default.png";
	}

	/* Зураг байгаа эсэхийг шалгаад, байхгүй бол өгөгдмөлийг буцаана.
	   Буцаах: array(url, width, height, type) */
	public static function resolveImage($publicPath)
	{
		$candArr = array();

		if(trim((string)$publicPath)!="")
			$candArr[] = $publicPath;

		$candArr[] = self::defaultImagePath();

		foreach($candArr as $cand){
			$info = self::imageInfo($cand);

			if($info===false)
				continue;

			return array(
				"url"		=> self::absUrl($cand),
				"width"		=> $info["width"],
				"height"	=> $info["height"],
				"type"		=> $info["type"]
			);
		}

		/* Диск дээр юу ч олдохгүй (жишээ нь зөвхөн CDN дээр байгаа) бол
		   өгөгдсөн замыг хэмжээгүйгээр буцаана — хоосон preview гаргахгүй. */
		$last = trim((string)$publicPath)!="" ? $publicPath : self::defaultImagePath();

		return array("url"=>self::absUrl($last), "width"=>0, "height"=>0, "type"=>"");
	}

	/* Сайтын хэл -> og:locale */
	public static function locale($langKey)
	{
		$key = strtolower(trim((string)$langKey));

		$mapArr = array(
			"mn" => "mn_MN",
			"en" => "en_US",
			"ru" => "ru_RU",
			"zh" => "zh_CN",
			"ko" => "ko_KR",
			"ja" => "ja_JP",
		);

		return isset($mapArr[$key]) ? $mapArr[$key] : "mn_MN";
	}

	public static function isoDate($timestamp)
	{
		$timestamp = (int)$timestamp;

		if($timestamp<=0)
			return "";

		return date("c", $timestamp);
	}

	/* ---------------------------------------------------------------
	   Сайтын өгөгдмөл мета. site.info.php энэ утгыг $gloMeta болгоно.
	--------------------------------------------------------------- */
	public static function defaults($siteName, $description, $keywords = "", $langKey = "mn")
	{
		$image = self::resolveImage(self::defaultImagePath());

		return array(
			"title"			=> self::plainText($siteName, 120),
			"description"	=> self::plainText($description, 300),
			"keywords"		=> self::plainText($keywords, 300),
			"siteName"		=> self::plainText($siteName, 120),
			"url"			=> self::currentUrl(),
			"type"			=> "website",
			"locale"		=> self::locale($langKey),
			"image"			=> $image["url"],
			"imageWidth"	=> $image["width"],
			"imageHeight"	=> $image["height"],
			"imageType"		=> $image["type"],
			"imageAlt"		=> self::plainText($siteName, 120),
			"robots"		=> "",
			"article"		=> array(),
			"jsonLd"		=> null,
		);
	}

	/* ---------------------------------------------------------------
	   Мэдээний дэлгэрэнгүй хуудасны мета.

	   $newsObj — db_newsroll + db_newsrollmore нэгдсэн мөр.
	   Талбарын харгалзаа:
	     newsTitle   -> title / og:title / twitter:title / JSON-LD headline
	     newsDesc    -> description (хоосон бол newsBody-с 200 тэмдэгт)
	     newsPic/ID  -> og:image (байхгүй бол өгөгдмөл MGLENC зураг)
	     createDate  -> article:published_time / datePublished
	     updateDate  -> article:modified_time  / dateModified
	     newsCat     -> article:section
	     newsSubCatn -> article:tag
	--------------------------------------------------------------- */
	public static function forNews($base, $newsObj, $canonicalUrl, $imagePath)
	{
		$meta = $base;

		$title = self::plainText(isset($newsObj["newsTitle"]) ? $newsObj["newsTitle"] : "", 200);

		if($title=="")
			$title = $base["title"];

		$desc = self::plainText(isset($newsObj["newsDesc"]) ? $newsObj["newsDesc"] : "", 300);

		if($desc=="")
			$desc = self::plainText(isset($newsObj["newsBody"]) ? $newsObj["newsBody"] : "", 200);

		if($desc=="")
			$desc = $base["description"];

		$image = self::resolveImage($imagePath);

		$meta["title"]			= $title;
		$meta["description"]	= $desc;
		$meta["url"]			= $canonicalUrl;
		$meta["type"]			= "article";
		$meta["image"]			= $image["url"];
		$meta["imageWidth"]		= $image["width"];
		$meta["imageHeight"]	= $image["height"];
		$meta["imageType"]		= $image["type"];
		$meta["imageAlt"]		= $title;

		$published = self::isoDate(isset($newsObj["createDate"]) ? $newsObj["createDate"] : 0);
		$modified  = self::isoDate(isset($newsObj["updateDate"]) ? $newsObj["updateDate"] : 0);

		$article = array();

		if($published!="")
			$article["published_time"] = $published;

		if($modified!="")
			$article["modified_time"] = $modified;

		$section = self::plainText(isset($newsObj["newsCat"]) ? $newsObj["newsCat"] : "", 100);
		if($section!="")
			$article["section"] = $section;

		$tag = self::plainText(isset($newsObj["newsSubCatn"]) ? $newsObj["newsSubCatn"] : "", 100);
		if($tag!="" && $tag!=$section)
			$article["tag"] = array($tag);

		$meta["article"] = $article;

		$jsonLd = array(
			"@context"			=> "https://schema.org",
			"@type"				=> "NewsArticle",
			"headline"			=> $title,
			"description"		=> $desc,
			"image"				=> array($image["url"]),
			"mainEntityOfPage"	=> array("@type"=>"WebPage", "@id"=>$canonicalUrl),
			"url"				=> $canonicalUrl,
			"inLanguage"		=> str_replace("_","-",$base["locale"]),
			"author"			=> array("@type"=>"Organization", "name"=>$base["siteName"], "url"=>self::baseUrl()."/"),
			"publisher"			=> array(
				"@type"	=> "Organization",
				"name"	=> $base["siteName"],
				"url"	=> self::baseUrl()."/",
				"logo"	=> array("@type"=>"ImageObject", "url"=>self::absUrl(self::defaultImagePath()))
			),
		);

		if($published!="")
			$jsonLd["datePublished"] = $published;

		if($modified!="")
			$jsonLd["dateModified"] = $modified;
		elseif($published!="")
			$jsonLd["dateModified"] = $published;

		if($section!="")
			$jsonLd["articleSection"] = $section;

		$meta["jsonLd"] = $jsonLd;

		return $meta;
	}

	/* Олдохгүй хуудас: сайтын мета, гэхдээ индексжүүлэхгүй. */
	public static function forNotFound($base)
	{
		$meta = $base;
		$meta["robots"] = "noindex, follow";
		$meta["article"] = array();
		$meta["jsonLd"] = null;

		return $meta;
	}

	/* ---------------------------------------------------------------
	   HTML гаргах. Бүх утгыг энд escape хийнэ.
	--------------------------------------------------------------- */
	public static function esc($text)
	{
		return htmlspecialchars((string)$text, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
	}

	private static function tag($attr, $name, $content)
	{
		$content = trim((string)$content);

		if($content==="")
			return "";

		return "\t<meta ".$attr."=\"".self::esc($name)."\" content=\"".self::esc($content)."\" />\n";
	}

	public static function render($meta)
	{
		$out = "";

		$title		= isset($meta["title"]) ? trim($meta["title"]) : "";
		$desc		= isset($meta["description"]) ? trim($meta["description"]) : "";
		$url		= isset($meta["url"]) ? trim($meta["url"]) : "";
		$siteName	= isset($meta["siteName"]) ? trim($meta["siteName"]) : "";
		$image		= isset($meta["image"]) ? trim($meta["image"]) : "";
		$type		= isset($meta["type"]) && $meta["type"]!="" ? $meta["type"] : "website";

		$out .= "\t<title>".self::esc($title)."</title>\n";
		$out .= self::tag("name", "description", $desc);

		if(isset($meta["keywords"]) && trim($meta["keywords"])!="")
			$out .= self::tag("name", "keywords", $meta["keywords"]);

		if(isset($meta["robots"]) && trim($meta["robots"])!="")
			$out .= self::tag("name", "robots", $meta["robots"]);

		if($url!="")
			$out .= "\t<link rel=\"canonical\" href=\"".self::esc($url)."\" />\n";

		/* Open Graph */
		$out .= self::tag("property", "og:type", $type);
		$out .= self::tag("property", "og:title", $title);
		$out .= self::tag("property", "og:description", $desc);
		$out .= self::tag("property", "og:url", $url);
		$out .= self::tag("property", "og:site_name", $siteName);
		$out .= self::tag("property", "og:locale", isset($meta["locale"]) ? $meta["locale"] : "");

		if($image!=""){
			$out .= self::tag("property", "og:image", $image);

			if(substr($image,0,8)=="https://")
				$out .= self::tag("property", "og:image:secure_url", $image);

			if(!empty($meta["imageType"]))
				$out .= self::tag("property", "og:image:type", $meta["imageType"]);

			if(!empty($meta["imageWidth"]) && !empty($meta["imageHeight"])){
				$out .= self::tag("property", "og:image:width", $meta["imageWidth"]);
				$out .= self::tag("property", "og:image:height", $meta["imageHeight"]);
			}

			$out .= self::tag("property", "og:image:alt", isset($meta["imageAlt"]) ? $meta["imageAlt"] : $title);
		}

		/* article:* — зөвхөн мэдээний хуудсанд, байгаа өгөгдлөөр */
		if($type=="article" && !empty($meta["article"]) && is_array($meta["article"])){
			foreach($meta["article"] as $key=>$value){
				if(is_array($value)){
					foreach($value as $one)
						$out .= self::tag("property", "article:".$key, $one);
				}
				else
					$out .= self::tag("property", "article:".$key, $value);
			}
		}

		/* Twitter / X */
		$out .= self::tag("name", "twitter:card", $image!="" ? "summary_large_image" : "summary");
		$out .= self::tag("name", "twitter:title", $title);
		$out .= self::tag("name", "twitter:description", $desc);

		if($image!=""){
			$out .= self::tag("name", "twitter:image", $image);
			$out .= self::tag("name", "twitter:image:alt", isset($meta["imageAlt"]) ? $meta["imageAlt"] : $title);
			$out .= self::tag("itemprop", "image", $image);
		}

		/* JSON-LD */
		if(!empty($meta["jsonLd"]) && is_array($meta["jsonLd"])){
			$json = json_encode($meta["jsonLd"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

			if($json!==false){
				/* "</script>" болон "<!--" дотор орж ирвэл HTML-ийг эвдэхгүй */
				$json = str_replace(array("</", "<!--"), array("<\\/", "<\\!--"), $json);
				$out .= "\t<script type=\"application/ld+json\">".$json."</script>\n";
			}
		}

		return $out;
	}
}

?>
