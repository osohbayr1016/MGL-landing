<?php
/**
 * Офис схем модулийн сангууд.
 *
 * Цөм код нь сайтын үндсэн хавтас дахь class/floorplan.class.php — вэб сайт
 * болон CP Admin хоёулаа ЯГ ижил файлыг ашигладаг тул хүснэгтийн бүтэц,
 * шалгалтын дүрэм хэзээ ч зөрөхгүй.
 */

if (!class_exists("FloorPlanCore")) {
	$floorPlanLibCandidates = array(
		__DIR__ . "/../../../class/floorplan.class.php",   /* public_html/class/ */
		__DIR__ . "/../../class/floorplan.class.php"       /* cpadmin/class/ (нөөц) */
	);

	foreach ($floorPlanLibCandidates as $floorPlanLibPath) {
		if (is_file($floorPlanLibPath)) {
			include_once $floorPlanLibPath;
			break;
		}
	}
}

if (!class_exists("FloorPlanCore")) {
	die('<div style="padding:40px;font:15px sans-serif;color:#a00">'
		. 'class/floorplan.class.php олдсонгүй. Файлыг сайтын үндсэн хавтасны class/ дотор байрлуулна уу.'
		. '</div>');
}

/** Админ энэ модулийг засах эрхтэй эсэх (эрхийн бүлгээс). */
if (!function_exists("floorPlanCan")) {
	function floorPlanCan()
	{
		global $adminAccessPer;

		return isset($adminAccessPer["floorplan"]["edit"]);
	}
}

/** CSRF токен: засварын хуудас нээхэд үүсгэж, хадгалахад шалгана. */
if (!function_exists("floorPlanCsrf")) {
	function floorPlanCsrf($renew = false)
	{
		if ($renew || empty($_SESSION["floorPlanCsrf"])) {
			$bytes = function_exists("random_bytes") ? random_bytes(16) : openssl_random_pseudo_bytes(16);
			$_SESSION["floorPlanCsrf"] = bin2hex($bytes);
		}

		return $_SESSION["floorPlanCsrf"];
	}
}

if (!function_exists("floorPlanCsrfOk")) {
	function floorPlanCsrfOk($token)
	{
		return !empty($_SESSION["floorPlanCsrf"]) && is_string($token) && hash_equals($_SESSION["floorPlanCsrf"], $token);
	}
}

/** Сайтын (public_html) үндэс хавтас. */
if (!function_exists("floorPlanSiteRoot")) {
	function floorPlanSiteRoot()
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
 * Засварлагчид хэрэгтэй, сайтын нийтийн файлууд. Нэрийн жагсаалтаар л олгоно
 * (зам оруулах боломжгүй). Эхний тэмдэгт мөр нь сайтын үндсэнээс харьцангуй зам.
 */
if (!function_exists("floorPlanAssetMap")) {
	function floorPlanAssetMap()
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

/** Схемийн зургийн файл нэр (office-20f-5120.webp) -> бодит зам, эсвэл null. */
if (!function_exists("floorPlanImagePath")) {
	function floorPlanImagePath($name)
	{
		if (!preg_match('/^office-[a-z0-9-]+\.(webp|jpg)$/', (string)$name, $m)) {
			return array(null, null);
		}

		$root = floorPlanSiteRoot();
		if ($root === null) {
			return array(null, null);
		}

		$file = $root . "assets/images/floorplan/" . $name;

		return array(is_file($file) ? $file : null, $m[1] === "webp" ? "image/webp" : "image/jpeg");
	}
}
