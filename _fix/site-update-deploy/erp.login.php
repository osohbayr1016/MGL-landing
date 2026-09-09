<?php
/*
	POST /erp.login.php

	Нүүр хуудасны "Нэвтрэх" цонхноос ирсэн мэдээллийг ERP-ийн Worker руу
	дамжуулж шалгуулаад, зөв бол ERP рүү шилжих URL-ыг JSON-оор буцаана.

	Хариу:
		{"ok":true,"redirect":"https://..."}
		{"ok":false,"error":"..."}
*/

/* Алдааны бичвэр JSON хариуг эвдэхээс сэргийлнэ — лог руу л бичигдэнэ */
ini_set("display_errors", "0");

session_start();

include_once "const.php";
include_once "class/erp.auth.class.php";

header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Referrer-Policy: no-referrer");

function erpOut($arr, $status = 200)
{
	http_response_code($status);
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	exit;
}

/*
	Хэрэглэгчийн жинхэнэ IP. Cloudflare-ийн ард байвал REMOTE_ADDR нь
	Cloudflare-ийнх болох тул CF-Connecting-IP-г авна. Гэхдээ уг толгойг
	зөвхөн REMOTE_ADDR нь үнэхээр Cloudflare-ийн хаяг байвал итгэнэ,
	эс бөгөөс хэн ч хуурамчаар илгээж тоолуурыг тойрч болно.
*/
function erpClientIp()
{
	$remote = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "0.0.0.0";

	if(!isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
		return $remote;

	$cf = trim($_SERVER["HTTP_CF_CONNECTING_IP"]);

	if(filter_var($cf, FILTER_VALIDATE_IP)===false)
		return $remote;

	/* https://www.cloudflare.com/ips/ */
	$ranges = array(
		"173.245.48.0/20","103.21.244.0/22","103.22.200.0/22","103.31.4.0/22",
		"141.101.64.0/18","108.162.192.0/18","190.93.240.0/20","188.114.96.0/20",
		"197.234.240.0/22","198.41.128.0/17","162.158.0.0/15","104.16.0.0/13",
		"104.24.0.0/14","172.64.0.0/13","131.0.72.0/22",
		"2400:cb00::/32","2606:4700::/32","2803:f800::/32","2405:b500::/32",
		"2405:8100::/32","2a06:98c0::/29","2c0f:f248::/32",
	);

	foreach($ranges as $cidr){
		if(erpIpInRange($remote, $cidr))
			return $cf;
	}

	return $remote;
}

function erpIpInRange($ip, $cidr)
{
	list($net, $bits) = explode("/", $cidr);

	$ipBin  = @inet_pton($ip);
	$netBin = @inet_pton($net);

	if($ipBin===false || $netBin===false || strlen($ipBin)!==strlen($netBin))
		return false;

	$bytes = intdiv($bits, 8);
	$rest  = $bits % 8;

	if($bytes>0 && strncmp($ipBin, $netBin, $bytes)!==0)
		return false;

	if($rest===0)
		return true;

	$mask = chr(0xff << (8-$rest) & 0xff);

	return ($ipBin[$bytes] & $mask) === ($netBin[$bytes] & $mask);
}

/* ---------------------------------------------------------------- */

if($_SERVER["REQUEST_METHOD"]!=="POST")
	erpOut(array("ok"=>false, "error"=>"Зөвхөн POST хүсэлт хүлээн авна."), 405);

/* Формыг зөвхөн манай хуудаснаас илгээсэн эсэх */
$csrf = isset($_POST["csrf"]) ? (string)$_POST["csrf"] : "";

if(!isset($_SESSION["erpLoginCsrf"]) || $csrf==="" || !hash_equals($_SESSION["erpLoginCsrf"], $csrf))
	erpOut(array("ok"=>false, "error"=>"Хуудас хуучирсан байна. Дахин ачаалаад оролдоно уу."), 419);

$login		= isset($_POST["login"]) ? (string)$_POST["login"] : "";
$password	= isset($_POST["password"]) ? (string)$_POST["password"] : "";

/*
	Ажиллах утгууд нь ErpAuth дотор өгөгдмөлөөр байгаа тул const.php-д
	юу ч нэмээгүй байсан ч ажиллана. const.php дээр хувьсагч зарлавал
	түүнийг давуулна — гэхдээ ХООСОН мөр бол үл тоомсорлоно, эс бөгөөс
	хагас бөглөсөн блок ажиллаж байсныг эвдэнэ.
*/
$erpCfg = array();

$erpMap = array(
	"appUrl"		=> "gloErpAppUrl",
	"loginUrl"		=> "gloErpLoginUrl",
	"userKey"		=> "gloErpLoginUserKey",
	"passKey"		=> "gloErpLoginPassKey",
	"tokenPath"		=> "gloErpTokenPath",
	"redirectPath"	=> "gloErpRedirectPath",
	"tokenMode"		=> "gloErpTokenMode",
	"tokenParam"	=> "gloErpTokenParam",
	"apiKey"		=> "gloErpApiKey",
	"apiKeyHeader"	=> "gloErpApiKeyHeader",
	"timeout"		=> "gloErpTimeout",
	"workerMessage"	=> "gloErpShowWorkerMessage",
	"caBundle"		=> "gloErpCaBundle",
);

foreach($erpMap as $key=>$var){
	if(!isset($GLOBALS[$var]))
		continue;

	$val = $GLOBALS[$var];

	/* Хоосон текст = "тохируулаагүй", өгөгдмөлийг нь үлдээнэ */
	if(is_string($val) && trim($val)==="")
		continue;

	$erpCfg[$key] = $val;
}

$erpAuth = new ErpAuth($erpCfg);

$result = $erpAuth->login($login, $password, erpClientIp());

if($result["ok"]===true){
	/* Формыг дахин илгээхээс сэргийлж token-оо сэлгэнэ */
	$_SESSION["erpLoginCsrf"] = bin2hex(random_bytes(32));

	erpOut(array("ok"=>true, "redirect"=>$result["redirect"]));
}

erpOut(array("ok"=>false, "error"=>$result["error"]), $result["status"]);

?>
