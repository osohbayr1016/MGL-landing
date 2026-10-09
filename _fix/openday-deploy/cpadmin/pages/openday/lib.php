<?php
/**
 * Өдөрлөг (Open Office Day) модулийн сангууд.
 *
 * Цөм код нь сайтын үндсэн хавтас дахь class/openday.class.php — вэб сайт
 * болон CP Admin хоёулаа ЯГ ижил файлыг ашигладаг. Схемийн давхрууд (зураг,
 * хүрээ, шат) нь class/floorplan.class.php-ээс уншигдана.
 */

foreach (array("OpenDayCore" => "openday", "FloorPlanCore" => "floorplan") as $openDayLibClass => $openDayLibName) {
	if (class_exists($openDayLibClass)) {
		continue;
	}
	foreach (array(__DIR__ . "/../../../class/", __DIR__ . "/../../class/") as $openDayLibDir) {
		if (is_file($openDayLibDir . $openDayLibName . ".class.php")) {
			include_once $openDayLibDir . $openDayLibName . ".class.php";
			break;
		}
	}
}

if (!class_exists("OpenDayCore") || !class_exists("FloorPlanCore")) {
	die('<div style="padding:40px;font:15px sans-serif;color:#a00">'
		. 'class/openday.class.php эсвэл class/floorplan.class.php олдсонгүй. Файлуудыг сайтын үндсэн хавтасны class/ дотор байрлуулна уу.'
		. '</div>');
}

/** Админ энэ модулийг засах эрхтэй эсэх (эрхийн бүлгээс). */
if (!function_exists("openDayCan")) {
	function openDayCan()
	{
		global $adminAccessPer;

		return isset($adminAccessPer["openday"]["edit"]);
	}
}

/** CSRF токен: засварын хуудас нээхэд үүсгэж, хадгалахад шалгана. */
if (!function_exists("openDayCsrf")) {
	function openDayCsrf()
	{
		if (empty($_SESSION["openDayCsrf"])) {
			$bytes = function_exists("random_bytes") ? random_bytes(16) : openssl_random_pseudo_bytes(16);
			$_SESSION["openDayCsrf"] = bin2hex($bytes);
		}

		return $_SESSION["openDayCsrf"];
	}
}

if (!function_exists("openDayCsrfOk")) {
	function openDayCsrfOk($token)
	{
		return !empty($_SESSION["openDayCsrf"]) && is_string($token) && hash_equals($_SESSION["openDayCsrf"], $token);
	}
}

/** Сайтын (public_html) үндэс хавтас. */
if (!function_exists("openDaySiteRoot")) {
	function openDaySiteRoot()
	{
		foreach (array(__DIR__ . "/../../../", __DIR__ . "/../../") as $root) {
			if (is_file($root . "assets/js/floorplan/core.js")) {
				return $root;
			}
		}

		return null;
	}
}

/**
 * Засварлагчид хэрэгтэй файлууд — нэрийн жагсаалтаар л олгоно (зам оруулах
 * боломжгүй). "site" = сайтын үндсэнээс, "local" = энэ хавтаснаас.
 */
if (!function_exists("openDayAssetMap")) {
	function openDayAssetMap()
	{
		return array(
			"core.js"       => array("site", "assets/js/floorplan/core.js", "application/javascript; charset=utf-8"),
			"viewer.js"     => array("site", "assets/js/floorplan/viewer.js", "application/javascript; charset=utf-8"),
			"floorplan.css" => array("site", "assets/css/floorplan.css", "text/css; charset=utf-8"),
			"editor.js"     => array("local", "editor.js", "application/javascript; charset=utf-8"),
			"editor.css"    => array("local", "editor.css", "text/css; charset=utf-8")
		);
	}
}

/** Схемийн зургийн файл нэр (office-20f-mid.webp) -> бодит зам, эсвэл null. */
if (!function_exists("openDayImagePath")) {
	function openDayImagePath($name)
	{
		if (!preg_match('/^office-[a-z0-9-]+\.(webp|jpg)$/', (string)$name, $m)) {
			return array(null, null);
		}

		$root = openDaySiteRoot();
		if ($root === null) {
			return array(null, null);
		}

		$file = $root . "assets/images/floorplan/" . $name;

		return array(is_file($file) ? $file : null, $m[1] === "webp" ? "image/webp" : "image/jpeg");
	}
}

/** Нийтийн хуудасны хаяг (урьдчилан харах линк). */
if (!function_exists("openDayPublicUrl")) {
	function openDayPublicUrl()
	{
		return "https://mglenc.com/openday";
	}
}
