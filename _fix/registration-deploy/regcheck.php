<?php
/**
 * ОНОШЛОГООНЫ ФАЙЛ — бүртгэлийн модуль яагаад ажиллахгүй байгааг олно.
 *
 * Хэрэглэх:
 *   1) Энэ файлыг public_html дотор (index.php-ийн хажууд) байрлуулна
 *   2) https://mglenc.com/regcheck.php хаягаар нээнэ
 *   3) Гарсан текстийг бүхэлд нь илгээнэ
 *   4) ДУУССАНЫ ДАРАА ЭНЭ ФАЙЛЫГ УСТГАНА
 */

header("Content-Type: text/plain; charset=utf-8");
@ini_set("display_errors", "1");
@ini_set("log_errors", "0");
error_reporting(E_ALL);

function h($t) { echo "\n" . str_repeat("=", 68) . "\n" . $t . "\n" . str_repeat("=", 68) . "\n"; }
function line($ok, $label, $note = "") {
	echo ($ok === true ? "  [OK]      " : ($ok === false ? "  [АЛГА]    " : "  [   ]     "))
		. str_pad($label, 46) . ($note !== "" ? " " . $note : "") . "\n";
}

$root = __DIR__;

/* ------------------------------------------------------------------ */
h("1. ОРЧИН");

echo "  PHP хувилбар        : " . PHP_VERSION . "\n";
echo "  Энэ файлын хавтас   : " . $root . "\n";
echo "  DOCUMENT_ROOT       : " . (isset($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : "?") . "\n";
echo "  HTTP_HOST           : " . (isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "?") . "\n";
echo "  index.php байгаа эсэх: " . (is_file($root . "/index.php") ? "тийм" : "ҮГҮЙ — буруу хавтас!") . "\n";
echo "  mod_rewrite         : " . (function_exists("apache_get_modules")
	? (in_array("mod_rewrite", apache_get_modules()) ? "асаалттай" : "УНТРААЛТАЙ")
	: "шалгах боломжгүй (LiteSpeed/FastCGI)") . "\n";
echo "  ZipArchive          : " . (class_exists("ZipArchive") ? "бий" : "алга (хэрэггүй — өөрийн бичигч ашигладаг)") . "\n";
echo "  zlib (gzdeflate)    : " . (function_exists("gzdeflate") ? "бий" : "АЛГА — xlsx шахагдахгүй, гэхдээ ажиллана") . "\n";
echo "  mbstring            : " . (function_exists("mb_substr") ? "бий" : "алга") . "\n";

/* ------------------------------------------------------------------ */
h("2. ФАЙЛУУД БАЙРЛАСАН ЭСЭХ");

$need = array(
	"class/registration.class.php",
	"class/xlsx.writer.class.php",
	"pages/registration/sys.php",
	"pages/registration/blocks/hero.php",
	"pages/registration/blocks/form.php",
	"pages/registration/blocks/info.php",
	"skin/new/registration.php",
	"assets/css/registration.css",
	"assets/js/registration.js",
	"cpadmin/user.info.php",
	"cpadmin/pages/registration/sys.php",
	"cpadmin/pages/registration/lib.php",
	"cpadmin/pages/registration/list.sys.php",
	"cpadmin/pages/registration/list.php",
	"cpadmin/pages/registration/post.sys.php",
	"cpadmin/pages/registration/export.php"
);

$missing = 0;
foreach ($need as $f) {
	$ok = is_file($root . "/" . $f);
	if (!$ok) { $missing++; }
	line($ok, $f, $ok ? "(" . filesize($root . "/" . $f) . " b)" : "");
}

/* Zip-ийг буруу задалсан эсэх */
$wrongPlaces = array(
	"registration-deploy",
	"_fix/registration-deploy",
	"public_html",
	"cpadmin/registration-deploy"
);
echo "\n  Санамсаргүй үүссэн хавтас шалгах:\n";
foreach ($wrongPlaces as $w) {
	if (is_dir($root . "/" . $w)) {
		echo "  !!! ОЛДЛОО: " . $w . "  <-- файлууд ЭНД байна, буруу байрлал\n";
	}
}
if ($missing > 0) {
	echo "\n  >>> " . $missing . " файл алга. Zip-ийн ДОТООД агуулгыг public_html дотор\n";
	echo "  >>> хуулах ёстой (registration-deploy хавтсыг биш).\n";
}

/* ------------------------------------------------------------------ */
h("3. .htaccess ДҮРМҮҮД");

$ht = is_file($root . "/.htaccess") ? file_get_contents($root . "/.htaccess") : "";
line($ht !== "", ".htaccess файл байгаа");
line(strpos($ht, "incPageType=registration") !== false, "/registration дүрэм нэмэгдсэн");
line(strpos($ht, "RewriteEngine") !== false, "RewriteEngine on");

$aht = is_file($root . "/cpadmin/.htaccess") ? file_get_contents($root . "/cpadmin/.htaccess") : "";
line($aht !== "", "cpadmin/.htaccess файл байгаа");
line(strpos($aht, "incPageType=registration") !== false, "админы registration дүрэм нэмэгдсэн");

if ($aht !== "" && preg_match('/AddHandler\s+(\S+)/', $aht, $m)) {
	echo "  cpadmin PHP handler : " . $m[1] . "\n";
}
if ($ht !== "" && preg_match('/AddHandler\s+(\S+)/', $ht, $m)) {
	echo "  сайтын PHP handler  : " . $m[1] . "\n";
}

/* ------------------------------------------------------------------ */
h("4. CP ADMIN ЦЭС");

$ui = is_file($root . "/cpadmin/user.info.php") ? file_get_contents($root . "/cpadmin/user.info.php") : "";
line($ui !== "", "cpadmin/user.info.php байгаа");
line(strpos($ui, 'gloMenuArr["registration"]') !== false, "цэсний код нэмэгдсэн (ШИНЭ хувилбар мөн үү)");
line(strpos($ui, 'adminAccessPer["registration"]') !== false, "эрхийн код нэмэгдсэн");

if ($ui !== "" && strpos($ui, 'gloMenuArr["registration"]') === false) {
	echo "\n  >>> cpadmin/user.info.php ХУУЧИН хувилбар байна — дарж бичигдээгүй.\n";
	echo "  >>> Иймд admin цэсэнд 'Арга хэмжээний бүртгэл' гарахгүй.\n";
}

$cn = is_file($root . "/cpadmin/const.php") ? file_get_contents($root . "/cpadmin/const.php") : "";
line($cn !== "", "cpadmin/const.php байгаа");
if ($cn !== "" && preg_match('/\$gloDomainLink\s*=\s*"([^"]+)"\s*;\s*$/m', $cn, $m)) {
	echo "  gloDomainLink       : " . $m[1] . "\n";
}

/* ------------------------------------------------------------------ */
h("5. ӨГӨГДЛИЙН САН");

if (!is_file($root . "/config.php")) {
	echo "  config.php алга — цааш шалгах боломжгүй.\n";
} else {
	if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
	include_once $root . "/config.php";
	@ini_set("display_errors", "1");
	error_reporting(E_ALL);

	if (!isset($db) || !is_object($db)) {
		echo "  $db үүсээгүй — DB холболт амжилтгүй.\n";
	} else {
		echo "  DB холбогдсон       : тийм (" . (isset($sysDbName) ? $sysDbName : "?") . ")\n";
		echo "  $tbl_pref           : " . (isset($tbl_pref) ? $tbl_pref : "тодорхойлогдоогүй") . "\n";

		$tabs = array("reg_setting", "reg_field", "reg_block", "reg_entry");
		$pref = isset($tbl_pref) && $tbl_pref != "" ? $tbl_pref : "db_";

		foreach ($tabs as $t) {
			$name = $pref . $t;
			$r = $db->rawQuery("SHOW TABLES LIKE '" . $name . "'", null);
			$exists = is_array($r) && count($r) > 0;
			$cnt = "";
			if ($exists) {
				$c = $db->rawQuery("SELECT COUNT(*) c FROM `" . $name . "` LIMIT 1", null);
				$cnt = "мөр: " . (is_array($c) && isset($c[0]["c"]) ? $c[0]["c"] : "?");
			}
			line($exists, $name, $cnt);
		}

		/* CREATE эрхтэй эсэх */
		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $pref . "reg_probe` (`i` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8", null);
		$r = $db->rawQuery("SHOW TABLES LIKE '" . $pref . "reg_probe'", null);
		$canCreate = is_array($r) && count($r) > 0;
		line($canCreate, "DB хэрэглэгч CREATE TABLE хийж чадах эсэх");
		if ($canCreate) {
			$db->rawQuery("DROP TABLE `" . $pref . "reg_probe`", null);
		} else {
			echo "  >>> CREATE эрх алга. _sql/registration.sql-ийг phpMyAdmin дээр\n";
			echo "  >>> ГАРААР ажиллуулна уу.\n";
		}
	}
}

/* ------------------------------------------------------------------ */
h("5b. АДМИНЫ ЭРХИЙН БҮЛЭГ (цэс харагдахад шаардлагатай)");

if (!isset($db) || !is_object($db)) {
	echo "  DB холбогдоогүй тул алгасав.\n";
} else {
	$pref = isset($tbl_pref) && $tbl_pref != "" ? $tbl_pref : "db_";
	$groups = $db->rawQuery("SELECT * FROM `" . $pref . "admingroup`", null);

	if (!is_array($groups) || count($groups) < 1) {
		echo "  Эрхийн бүлэг олдсонгүй.\n";
	} else {
		foreach ($groups as $g) {
			$act = isset($g["adminGroupAction"]) ? $g["adminGroupAction"] : "";
			$name = isset($g["adminGroupName"]) ? $g["adminGroupName"] : (isset($g["name"]) ? $g["name"] : "?");
			$hasInsert = strpos($act, "insert_") !== false;
			$hasReg    = strpos($act, "registration_") !== false;

			echo "\n  Бүлэг: " . $name . " (id " . (isset($g["adminGroupID"]) ? $g["adminGroupID"] : "?") . ")\n";
			echo "    insert_* эрх     : " . ($hasInsert ? "БИЙ" : "АЛГА  <-- цэс гарахгүй шалтгаан") . "\n";
			echo "    registration_* эрх: " . ($hasReg ? "БИЙ" : "алга (автоматаар олгогдоно)") . "\n";
			echo "    түүхий утга      : " . substr($act, 0, 200) . "\n";
		}
	}
}

/* ------------------------------------------------------------------ */
h("6. /registration ХУУДСЫГ ЖИНХЭНЭЭР АЧААЛЖ ҮЗЭХ");

if (!is_file($root . "/pages/registration/sys.php")) {
	echo "  pages/registration/sys.php алга — тиймээс хуудас хоосон буцаж байна.\n";
} elseif (!isset($db) || !is_object($db)) {
	echo "  DB холбогдоогүй тул алгасав.\n";
} else {
	$err = "";
	set_error_handler(function ($no, $str, $file, $lineNo) use (&$err) {
		$err .= "  PHP: " . $str . "\n       " . $file . ":" . $lineNo . "\n";
		return true;
	});

	try {
		$gloConstSkinDir = "skin/new/";
		$gloIncHomePage  = "home.php";
		$allWidgetArr    = array();
		$_REQUEST["incPageType"] = "registration";

		include $root . "/site.info.php";
		include $root . "/pages/registration/sys.php";

		echo "  sys.php ачаалагдлаа : тийм\n";
		echo "  gloIncHomePage      : " . $gloIncHomePage . "\n";
		echo "  Блокийн тоо         : " . (isset($regBlocks) ? count($regBlocks) : "?") . "\n";
		echo "  Талбарын тоо        : " . (isset($regFields) ? count($regFields) : "?") . "\n";
		echo "  Бүртгэл нээлттэй эсэх: " . (isset($regStatus) && $regStatus["open"] ? "тийм" : "үгүй") . "\n";

		ob_start();
		include $root . "/skin/new/" . $gloIncHomePage;
		$html = ob_get_clean();

		echo "  Хуудасны хэмжээ     : " . strlen($html) . " байт\n";
		echo "  <form> байгаа эсэх  : " . (strpos($html, "registration-form") !== false ? "тийм" : "ҮГҮЙ") . "\n";

	} catch (Throwable $e) {
		while (ob_get_level() > 0) { ob_end_clean(); }
		echo "  !!! FATAL: " . get_class($e) . ": " . $e->getMessage() . "\n";
		echo "      " . $e->getFile() . ":" . $e->getLine() . "\n";
	} catch (Exception $e) {
		while (ob_get_level() > 0) { ob_end_clean(); }
		echo "  !!! EXCEPTION: " . $e->getMessage() . "\n";
	}

	restore_error_handler();

	if ($err !== "") {
		echo "\n  Гарсан анхааруулгууд:\n" . $err;
	}
}

h("ДУУСЛАА — энэ файлыг устгахаа мартуузай");
