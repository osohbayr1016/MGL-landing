<?php
/**
 * Router for `php -S` that serves ONLY the CP Admin "Офис схем" module, with
 * a real database (MysqliDb) and a pretend logged-in admin — so the editor
 * (editor.js) and the save endpoint (post.sys.php) can be exercised end to
 * end without the rest of CP Admin (login, other modules).
 *
 *   FP_DB_PORT=3399 FP_DB_PASS=... [FP_NO_PERM=1] php -S 127.0.0.1:8098 -t cpadmin tests/floorplan/e2e/editor-router.php
 */
$root   = realpath(__DIR__ . "/../../..");
$cp     = $root . "/cpadmin";
$uri    = rawurldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));

/* static files of CP Admin (bootstrap, font-awesome ...) */
if ($uri !== "/" && strpos($uri, "/floorplan/") !== 0 && strpos($uri, "/userPost/") !== 0 && strpos($uri, "/__") !== 0) {
	return false;
}

ini_set("display_errors", "0"); /* like config.db.php on the real site: the old MysqliDb emits PHP 8 notices */
session_start();

require $root . "/class/main.class.php";

$gloConstModuleDir = "pages/";
$tbl_pref = "fptest_";
$db = new MysqliDb(
	getenv("FP_DB_HOST") ?: "127.0.0.1",
	getenv("FP_DB_USER") ?: "root",
	getenv("FP_DB_PASS") ?: "",
	getenv("FP_DB_NAME") ?: "fp_test",
	(int)(getenv("FP_DB_PORT") ?: 3306)
);

function txtSec($s) { return preg_replace('/[^\w\-\.]/u', "", (string)$s); }

/* what cpadmin/user.info.php provides to a logged-in admin */
$adminAccessPer = array();
if (!getenv("FP_NO_PERM")) {
	$adminAccessPer["floorplan"] = array("edit" => "edit");
}

chdir($cp);

if ($uri === "/__dump") {
	require $root . "/class/floorplan.class.php";
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode(FloorPlanCore::load($db, false));
	return true;
}

if ($uri === "/__reset") {
	foreach (array("fptest_floorplan", "fptest_floorplan_hotspot") as $t) {
		$db->rawQuery("DROP TABLE IF EXISTS `" . $t . "`");
	}
	echo "reset";
	return true;
}

if ($uri === "/userPost/floorplan") {
	$sysModule = "floorplan";
	include $cp . "/pages/floorplan/post.sys.php";
	return true;
}

/* assets: /?incPageType=floorplan&subPage=asset&asset=<name> (what the editor uses) */
if ($uri === "/" && isset($_GET["subPage"]) && $_GET["subPage"] === "asset") {
	$_REQUEST["subPage"] = "asset";
	$_REQUEST["asset"] = isset($_GET["asset"]) ? $_GET["asset"] : "";
	include $cp . "/pages/floorplan/sys.php";
	return true;
}

if ($uri === "/floorplan/edit" || $uri === "/floorplan" || $uri === "/") {
	$_REQUEST["subPage"] = "edit";
	include $cp . "/pages/floorplan/sys.php";
	?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="/assets/css/style.css" rel="stylesheet"></head>
<body><div id="wrapper"><div id="page-wrapper" class="gray-bg" style="margin:0"><?php include $incPageUrl; ?></div></div></body></html>
	<?php
	return true;
}

if (preg_match('#^/floorplan/asset/([^/]+)/?$#', $uri, $m)) {
	$_REQUEST["subPage"] = "asset";
	$_REQUEST["asset"] = $m[1];
	include $cp . "/pages/floorplan/sys.php";
	return true;
}

http_response_code(404);
echo "not found";
return true;
