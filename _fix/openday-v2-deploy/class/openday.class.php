<?php
/**
 * Open Office Day (өдөрлөг) — зочдын тур хуудас (/openday).
 *
 * Вэб сайт (pages/openday, skin/new/openday.php) болон CP Admin
 * (cpadmin/pages/openday) хоёулаа ЯГ ЭНЭ файлыг ашиглана — хүснэгтийн бүтэц,
 * шалгалтын дүрэм хэзээ ч зөрөхгүй.
 *
 * Хоёр хүснэгт:
 *   db_openday_setting — өдөрлөгийн ерөнхий мэдээлэл (гарчиг, огноо, цаг, байршил ...)
 *   db_openday_item    — хуудасны бүх мөр, `kind`-аар ялгана:
 *       agenda   — хөтөлбөр (цаг, гарчиг, тайлбар, байршил)
 *       spot     — схем дээрх цэг (давхар, x/y, ангилал)
 *       activity — хийж болох зүйлс / тур маршрут (дарааллаараа)
 *       info     — практик мэдээлэл (WiFi, хувцасны өлгүүр ...)
 *       faq      — түгээмэл асуулт
 *
 * Бүх текст монгол (…Mn) + англи (…En) хоёр талбартай. Англи нь хоосон бол
 * хуудас монголыг нь харуулна.
 *
 * Хүснэгт байхгүй бол анх хандахад өөрөө үүсгэж, жишээ агуулгаар нэг удаа
 * дүүргэнэ (seed). PHP 7.2 дээр ажиллана.
 */

class OpenDayCore
{
	const MAX_ITEMS = 60;      /* нэг төрлийн мөрийн дээд тоо */
	const TITLE_MAX = 160;
	const BODY_MAX  = 1200;

	/* ------------------------------------------------------------------
	   Тогтмолууд
	   ------------------------------------------------------------------ */

	public static function kinds()
	{
		return array("agenda", "spot", "activity", "info", "faq");
	}

	/**
	 * Icon / ангилал. Схемийн цэг болон практик мэдээллийн карт хоёулаа
	 * эндээс сонгоно. Утга = Font Awesome 4.3 класс, админы нэр.
	 */
	public static function icons()
	{
		return array(
			"pin"          => array("fa-map-marker",      "Байршил"),
			"registration" => array("fa-pencil-square-o", "Бүртгэл"),
			"stage"        => array("fa-microphone",      "Тайз / илтгэл"),
			"exhibit"      => array("fa-picture-o",       "Үзэсгэлэн"),
			"demo"         => array("fa-desktop",         "Танилцуулга / демо"),
			"team"         => array("fa-users",           "Баг / алба"),
			"coffee"       => array("fa-coffee",          "Кофе, зууш"),
			"food"         => array("fa-cutlery",         "Хоол"),
			"wardrobe"     => array("fa-suitcase",        "Хувцасны өлгүүр"),
			"restroom"     => array("fa-venus-mars",      "Ариун цэврийн өрөө"),
			"photo"        => array("fa-camera",          "Зураг авах"),
			"stairs"       => array("fa-level-up",        "Шат / лифт"),
			"exit"         => array("fa-sign-out",        "Гарц"),
			"wifi"         => array("fa-wifi",            "WiFi"),
			"phone"        => array("fa-phone",           "Утас / холбоо барих"),
			"help"         => array("fa-life-ring",       "Тусламж"),
			"info"         => array("fa-info-circle",     "Мэдээлэл"),
			"clock"        => array("fa-clock-o",         "Цаг"),
			"accessible"   => array("fa-wheelchair",      "Хүртээмж"),
			"gift"         => array("fa-gift",            "Бэлэг")
		);
	}

	public static function iconClass($key)
	{
		$all = self::icons();

		return isset($all[$key]) ? "fa " . $all[$key][0] : "fa " . $all["pin"][0];
	}

	/** Ерөнхий тохиргооны түлхүүрүүд ба өгөгдмөл утга. */
	public static function defaultSettings()
	{
		return array(
			"eyebrowMn"  => "CITY OF TOMORROW",
			"eyebrowEn"  => "CITY OF TOMORROW",
			"titleMn"    => "OPEN OFFICE DAY 2026",
			"titleEn"    => "OPEN OFFICE DAY 2026",
			"introMn"    => "MGL E&C-ийн шинэ оффист тавтай морил! Энэ хуудаснаас өдөрлөгийн хөтөлбөр, хаана юу болж байгаа, юу үзэж, хийж болох, хэрэгтэй бүх мэдээллээ нэг дороос харна уу.",
			"introEn"    => "Welcome to the new MGL E&C office! This page has everything for the day in one place: the programme, where things are, what to see and do, and practical information.",
			"eventDate"  => "2026-10-17",
			"startTime"  => "11:00",
			"endTime"    => "14:30",
			"locationMn" => "MN Tower, 20F/2008",
			"locationEn" => "MN Tower, 20F/2008",
			"footerMn"   => "© MGL E&C LLC",
			"footerEn"   => "© MGL E&C LLC",

			/* хэсгийн гарчиг, тайлбар мөр */
			"schedTitleMn" => "Хөтөлбөр",
			"schedTitleEn" => "Programme",
			"mapTitleMn"   => "Хаана юу байна",
			"mapTitleEn"   => "Where is what",
			"mapLeadMn"    => "Цэг дээр дарж дэлгэрэнгүйг харна. Давхар солихдоо дээд талын давхрын нэр эсвэл шатны тэмдэг дээр дарна.",
			"mapLeadEn"    => "Tap a point to see what is there. To change floors, tap the floor name at the top or the staircase marker.",
			"todoTitleMn"  => "Хийж болох зүйлс",
			"todoTitleEn"  => "Things to do",
			"todoLeadMn"   => "Санал болгох тур маршрут. Дарааллыг заавал дагах шаардлагагүй.",
			"todoLeadEn"   => "A suggested route through the office. Feel free to go in any order.",
			"infoTitleMn"  => "Практик мэдээлэл",
			"infoTitleEn"  => "Good to know",
			"faqTitleMn"   => "Түгээмэл асуулт",
			"faqTitleEn"   => "FAQ",

			/* асуулт асуух хэсэг (Асуулт хуудас) */
			"askOn"        => "1",
			"askTitleMn"   => "Асуултаа асуух",
			"askTitleEn"   => "Ask a question",
			"askLeadMn"    => "Энд хариулт олдоогүй бол асуултаа бичээрэй — манай баг шууд хүлээн авна.",
			"askLeadEn"    => "Can't find the answer here? Write your question and our team will get it right away."
		);
	}

	/** Хэл тус бүрийн, текст хэлбэрийн тохиргоо (урт хязгаар). */
	public static function settingLimits()
	{
		return array(
			"eyebrowMn" => 80, "eyebrowEn" => 80,
			"titleMn" => 120, "titleEn" => 120,
			"introMn" => 1200, "introEn" => 1200,
			"locationMn" => 120, "locationEn" => 120,
			"footerMn" => 160, "footerEn" => 160,
			"schedTitleMn" => 80, "schedTitleEn" => 80,
			"mapTitleMn" => 80, "mapTitleEn" => 80,
			"mapLeadMn" => 400, "mapLeadEn" => 400,
			"todoTitleMn" => 80, "todoTitleEn" => 80,
			"todoLeadMn" => 400, "todoLeadEn" => 400,
			"infoTitleMn" => 80, "infoTitleEn" => 80,
			"faqTitleMn" => 80, "faqTitleEn" => 80,
			"askTitleMn" => 80, "askTitleEn" => 80,
			"askLeadMn" => 400, "askLeadEn" => 400
		);
	}

	/* ------------------------------------------------------------------
	   Хүснэгтүүд
	   ------------------------------------------------------------------ */

	public static function tables()
	{
		global $db_openday_setting, $db_openday_item, $db_openday_question, $tbl_pref;

		$pref = isset($tbl_pref) && $tbl_pref != "" ? $tbl_pref : "db_";

		return array(
			"setting"  => isset($db_openday_setting) && $db_openday_setting != "" ? $db_openday_setting : $pref . "openday_setting",
			"item"     => isset($db_openday_item) && $db_openday_item != "" ? $db_openday_item : $pref . "openday_item",
			"question" => isset($db_openday_question) && $db_openday_question != "" ? $db_openday_question : $pref . "openday_question"
		);
	}

	public static function createTables($db)
	{
		$t = self::tables();

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["setting"] . "` (
			`setKey` varchar(64) NOT NULL,
			`setValue` text,
			PRIMARY KEY (`setKey`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["item"] . "` (
			`itemID` int(11) NOT NULL AUTO_INCREMENT,
			`kind` varchar(16) NOT NULL DEFAULT '',
			`sortOrder` int(11) NOT NULL DEFAULT '0',
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`slug` varchar(48) NOT NULL DEFAULT '',
			`titleMn` varchar(200) NOT NULL DEFAULT '',
			`titleEn` varchar(200) NOT NULL DEFAULT '',
			`bodyMn` text,
			`bodyEn` text,
			`timeStart` varchar(5) NOT NULL DEFAULT '',
			`timeEnd` varchar(5) NOT NULL DEFAULT '',
			`spot` varchar(48) NOT NULL DEFAULT '',
			`floor` varchar(32) NOT NULL DEFAULT '',
			`x` double DEFAULT NULL,
			`y` double DEFAULT NULL,
			`icon` varchar(24) NOT NULL DEFAULT '',
			`createdAt` datetime DEFAULT NULL,
			`updatedAt` datetime DEFAULT NULL,
			PRIMARY KEY (`itemID`),
			KEY `kindOrder` (`kind`,`sortOrder`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		/* зочдын асуулт (Асуулт хуудасны форм -> CP Admin -> Ирсэн асуулт) */
		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["question"] . "` (
			`qID` int(11) NOT NULL AUTO_INCREMENT,
			`question` text,
			`name` varchar(80) NOT NULL DEFAULT '',
			`lang` varchar(2) NOT NULL DEFAULT 'mn',
			`status` tinyint(1) NOT NULL DEFAULT '0',
			`ip` varchar(45) NOT NULL DEFAULT '',
			`createdAt` datetime DEFAULT NULL,
			`updatedAt` datetime DEFAULT NULL,
			PRIMARY KEY (`qID`),
			KEY `ipTime` (`ip`,`createdAt`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}

	public static function tableExists($db, $table)
	{
		$row = $db->rawQueryOne("SHOW TABLES LIKE '" . str_replace("'", "", $table) . "'", null);

		return is_array($row) && count($row) > 0;
	}

	public static function scalar($db, $sql, $params = null)
	{
		$val = $db->rawQueryValue($sql . " LIMIT 1", $params);

		if (is_array($val)) {
			$val = count($val) > 0 ? reset($val) : null;
		}

		return $val;
	}

	/** Модуль суусан эсэх — суугаагүй бол суулгаж, нэг удаа жишээгээр дүүргэнэ. */
	public static function ensure($db)
	{
		if (!empty($GLOBALS["openDayModuleReady"])) {
			return;
		}

		$t = self::tables();

		if (!self::tableExists($db, $t["setting"]) || !self::tableExists($db, $t["item"]) || !self::tableExists($db, $t["question"])) {
			self::createTables($db);
		}

		$seeded = self::scalar($db, "SELECT `setValue` FROM `" . $t["setting"] . "` WHERE `setKey`='seeded'", null);
		if ((string)$seeded !== "1") {
			self::seed($db);
		}

		$GLOBALS["openDayModuleReady"] = true;
	}

	/** Жишээ агуулга. Хүснэгтэд мөр байгаа бол ХЭЗЭЭ Ч дарж бичихгүй. */
	public static function seed($db)
	{
		$t = self::tables();
		$now = date("Y-m-d H:i:s");

		$hasItems = (int)self::scalar($db, "SELECT COUNT(*) FROM `" . $t["item"] . "`", null) > 0;

		if (!$hasItems) {
			$order = array();
			foreach (self::sampleItems() as $it) {
				$k = $it["kind"];
				$order[$k] = isset($order[$k]) ? $order[$k] + 1 : 1;
				$row = self::toRow($it, $order[$k]);
				$row["createdAt"] = $now;
				$row["updatedAt"] = $now;
				$db->insert($t["item"], $row);
			}
		}

		$values = array();
		foreach (self::defaultSettings() as $k => $v) {
			$values[$k] = $v;
		}
		self::writeSettings($db, $values, false);
		self::writeSettings($db, array("seeded" => "1"), true);
	}

	/**
	 * Тохиргоо бичнэ. $overwrite=false бол байгаа утгыг хөндөхгүй
	 * (seed-ийн үед админы оруулсныг дарахгүй).
	 */
	public static function writeSettings($db, $values, $overwrite = true)
	{
		$t = self::tables();

		foreach ($values as $k => $v) {
			if ($overwrite) {
				$db->rawQuery(
					"INSERT INTO `" . $t["setting"] . "` (`setKey`, `setValue`) VALUES (?, ?)"
						. " ON DUPLICATE KEY UPDATE `setValue`=VALUES(`setValue`)",
					array($k, (string)$v)
				);
			} else {
				$db->rawQuery(
					"INSERT IGNORE INTO `" . $t["setting"] . "` (`setKey`, `setValue`) VALUES (?, ?)",
					array($k, (string)$v)
				);
			}
		}
	}

	/* ------------------------------------------------------------------
	   Унших
	   ------------------------------------------------------------------ */

	/**
	 * array(
	 *   "settings" => array(...),
	 *   "items"    => array("agenda" => array(...), "spot" => ..., ...),
	 *   "revision" => int,
	 *   "fallback" => bool   — өгөгдлийн сан уншиж чадаагүй үед жишээ агуулга
	 * )
	 * $enabledOnly=true бол зөвхөн идэвхтэй мөрүүд (нийтийн хуудас).
	 */
	public static function load($db, $enabledOnly = false)
	{
		try {
			if ($db === null) {
				throw new Exception("no db");
			}

			self::ensure($db);
			$t = self::tables();

			$settings = self::defaultSettings();
			$revision = 0;

			$rows = $db->rawQuery("SELECT `setKey`, `setValue` FROM `" . $t["setting"] . "`", null);
			if (!is_array($rows)) {
				throw new Exception("setting query failed");
			}
			foreach ($rows as $r) {
				if ($r["setKey"] === "revision") {
					$revision = (int)$r["setValue"];
				} elseif (array_key_exists($r["setKey"], $settings)) {
					$settings[$r["setKey"]] = (string)$r["setValue"];
				}
			}

			$rows = $db->rawQuery("SELECT * FROM `" . $t["item"] . "` ORDER BY `kind` ASC, `sortOrder` ASC, `itemID` ASC", null);
			if (!is_array($rows)) {
				throw new Exception("item query failed");
			}

			$items = self::emptyGroups();
			foreach ($rows as $r) {
				$it = self::fromRow($r);
				if ($it !== null && (!$enabledOnly || $it["enabled"])) {
					$items[$it["kind"]][] = $it;
				}
			}

			return array("settings" => $settings, "items" => $items, "revision" => $revision, "fallback" => false);
		} catch (Exception $e) {
			return self::fallback($enabledOnly);
		} catch (Throwable $e) {
			return self::fallback($enabledOnly);
		}
	}

	private static function fallback($enabledOnly)
	{
		$items = self::emptyGroups();
		foreach (self::sampleItems() as $it) {
			$clean = self::sanitizeItem($it);
			if ($clean !== null && (!$enabledOnly || $clean["enabled"])) {
				$items[$clean["kind"]][] = $clean;
			}
		}

		return array("settings" => self::defaultSettings(), "items" => $items, "revision" => 0, "fallback" => true);
	}

	public static function emptyGroups()
	{
		$out = array();
		foreach (self::kinds() as $k) {
			$out[$k] = array();
		}

		return $out;
	}

	/** DB мөр -> цэвэр бүтэц (буруу мөрийг null). */
	public static function fromRow($r)
	{
		return self::sanitizeItem(array(
			"kind"    => isset($r["kind"]) ? $r["kind"] : "",
			"enabled" => isset($r["enabled"]) ? (int)$r["enabled"] === 1 : true,
			"slug"    => isset($r["slug"]) ? $r["slug"] : "",
			"titleMn" => isset($r["titleMn"]) ? $r["titleMn"] : "",
			"titleEn" => isset($r["titleEn"]) ? $r["titleEn"] : "",
			"bodyMn"  => isset($r["bodyMn"]) ? $r["bodyMn"] : "",
			"bodyEn"  => isset($r["bodyEn"]) ? $r["bodyEn"] : "",
			"start"   => isset($r["timeStart"]) ? $r["timeStart"] : "",
			"end"     => isset($r["timeEnd"]) ? $r["timeEnd"] : "",
			"spot"    => isset($r["spot"]) ? $r["spot"] : "",
			"floor"   => isset($r["floor"]) ? $r["floor"] : "",
			"x"       => isset($r["x"]) ? $r["x"] : null,
			"y"       => isset($r["y"]) ? $r["y"] : null,
			"icon"    => isset($r["icon"]) ? $r["icon"] : ""
		));
	}

	/** Цэвэр бүтэц -> DB мөр. */
	public static function toRow($it, $order)
	{
		$it = self::sanitizeItem($it);

		return array(
			"kind"      => $it["kind"],
			"sortOrder" => (int)$order,
			"enabled"   => $it["enabled"] ? 1 : 0,
			"slug"      => $it["slug"],
			"titleMn"   => $it["titleMn"],
			"titleEn"   => $it["titleEn"],
			"bodyMn"    => $it["bodyMn"],
			"bodyEn"    => $it["bodyEn"],
			"timeStart" => $it["start"],
			"timeEnd"   => $it["end"],
			"spot"      => $it["spot"],
			"floor"     => $it["floor"],
			"x"         => $it["x"],
			"y"         => $it["y"],
			"icon"      => $it["icon"]
		);
	}

	/* ------------------------------------------------------------------
	   Шалгалт (сервер талд)
	   ------------------------------------------------------------------ */

	private static function numeric($v)
	{
		if (is_int($v) || is_float($v)) {
			return is_finite((float)$v);
		}

		return is_string($v) && trim($v) !== "" && is_numeric($v) && is_finite((float)$v);
	}

	public static function cleanText($v, $max)
	{
		if ($v === null || is_array($v) || is_object($v) || is_bool($v)) {
			return "";
		}

		/* мөр шилжилтийг \n болгож, удирдлагын бусад тэмдэгтийг хасна */
		$s = str_replace(array("\r\n", "\r"), "\n", (string)$v);
		$s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', "", $s);

		/* UTF-8 биш байт json_encode-ийг бүхэлд нь эвдэнэ — хаяна */
		if ($s === null || !preg_match('//u', $s)) {
			return "";
		}

		$s = trim($s);

		if (function_exists("mb_substr")) {
			return mb_substr($s, 0, $max, "UTF-8");
		}

		return preg_match('/^.{0,' . (int)$max . '}/us', $s, $m) ? $m[0] : "";
	}

	/** Нэг мөрт текст (гарчиг) — мөр шилжилтгүй. */
	public static function cleanLine($v, $max)
	{
		return self::cleanText(preg_replace('/\s*\n\s*/', " ", (string)(is_scalar($v) ? $v : "")), $max);
	}

	public static function cleanTime($v)
	{
		$s = is_string($v) ? trim($v) : "";

		/* "9:30" -> "09:30" */
		if (preg_match('/^(\d{1,2}):(\d{2})$/', $s, $m) && (int)$m[1] <= 23 && (int)$m[2] <= 59) {
			return sprintf("%02d:%02d", (int)$m[1], (int)$m[2]);
		}

		return "";
	}

	public static function cleanDate($v)
	{
		$s = is_string($v) ? trim($v) : "";

		if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $s, $m) && checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
			return $s;
		}

		return "";
	}

	public static function cleanSlug($v)
	{
		$s = strtolower(self::cleanLine($v, 48));

		return trim(preg_replace('/-+/', "-", preg_replace('/[^a-z0-9-]/', "-", $s)), "-");
	}

	/**
	 * Нэг мөрийг цэвэрлэнэ. Төрөл танигдахгүй, эсвэл схемийн цэгийн
	 * координат тоо биш бол null. Бусдыг зөвшөөрөгдөх хязгаарт шахна.
	 */
	public static function sanitizeItem($raw)
	{
		if (!is_array($raw)) {
			return null;
		}

		$kind = isset($raw["kind"]) && is_string($raw["kind"]) ? $raw["kind"] : "";
		if (!in_array($kind, self::kinds(), true)) {
			return null;
		}

		$get = function ($k) use ($raw) {
			return isset($raw[$k]) ? $raw[$k] : null;
		};

		$enabled = $get("enabled");
		$icons = self::icons();
		$icon = is_string($get("icon")) && isset($icons[$get("icon")]) ? $get("icon") : "";

		$it = array(
			"kind"    => $kind,
			"enabled" => !($enabled === false || $enabled === 0 || $enabled === "0"),
			"slug"    => "",
			"titleMn" => self::cleanLine($get("titleMn"), self::TITLE_MAX),
			"titleEn" => self::cleanLine($get("titleEn"), self::TITLE_MAX),
			"bodyMn"  => self::cleanText($get("bodyMn"), self::BODY_MAX),
			"bodyEn"  => self::cleanText($get("bodyEn"), self::BODY_MAX),
			"start"   => "",
			"end"     => "",
			"spot"    => "",
			"floor"   => "",
			"x"       => null,
			"y"       => null,
			"icon"    => ""
		);

		if ($kind === "agenda" || $kind === "activity") {
			$it["start"] = self::cleanTime($get("start"));
			$it["end"]   = self::cleanTime($get("end"));
		}

		if ($kind === "agenda" || $kind === "activity" || $kind === "info") {
			$it["spot"] = self::cleanSlug($get("spot"));
		}

		if ($kind === "info") {
			$it["icon"] = $icon !== "" ? $icon : "info";
		}

		if ($kind === "spot") {
			$x = $get("x");
			$y = $get("y");
			if (!self::numeric($x) || !self::numeric($y)) {
				return null;
			}
			$it["slug"]  = self::cleanSlug($get("slug"));
			$it["floor"] = preg_replace('/[^a-z0-9-]/', "", strtolower(self::cleanLine($get("floor"), 32)));
			$it["x"]     = round(min(1, max(0, (float)$x)), 5);
			$it["y"]     = round(min(1, max(0, (float)$y)), 5);
			$it["icon"]  = $icon !== "" ? $icon : "pin";
			if ($it["slug"] === "" || $it["floor"] === "") {
				return null;
			}
		}

		return $it;
	}

	/**
	 * Засварлагчаас ирсэн бүх өгөгдлийг шалгана:
	 *   {"settings": {...}, "items": {"agenda": [...], "spot": [...], ...}}
	 * Схемийн цэгийн slug давхардвал / хоосон бол шинээр өгнө, бусад мөрийн
	 * байршлын холбоос (spot) байхгүй цэг рүү заасан бол хоосолно.
	 *
	 * @return array("ok"=>bool, "errors"=>array, "settings"=>array, "items"=>array)
	 */
	public static function sanitizePayload($payload, $floorKeys = null)
	{
		$errors = array();

		if (!is_array($payload)) {
			return array("ok" => false, "errors" => array("payload"), "settings" => array(), "items" => array());
		}

		/* ---- ерөнхий тохиргоо ---- */
		$inSet = isset($payload["settings"]) && is_array($payload["settings"]) ? $payload["settings"] : array();
		$settings = array();

		foreach (self::settingLimits() as $k => $max) {
			$v = isset($inSet[$k]) ? $inSet[$k] : "";
			$settings[$k] = ($k === "introMn" || $k === "introEn") ? self::cleanText($v, $max) : self::cleanLine($v, $max);
		}

		$settings["askOn"] = (isset($inSet["askOn"]) && (string)$inSet["askOn"] === "0") ? "0" : "1";
		$settings["eventDate"] = self::cleanDate(isset($inSet["eventDate"]) ? $inSet["eventDate"] : "");
		$settings["startTime"] = self::cleanTime(isset($inSet["startTime"]) ? $inSet["startTime"] : "");
		$settings["endTime"]   = self::cleanTime(isset($inSet["endTime"]) ? $inSet["endTime"] : "");

		if ($settings["titleMn"] === "" && $settings["titleEn"] === "") {
			$errors[] = "settings.title";
		}

		/* ---- мөрүүд ---- */
		$inItems = isset($payload["items"]) && is_array($payload["items"]) ? $payload["items"] : array();
		$items = self::emptyGroups();

		foreach (self::kinds() as $kind) {
			$list = isset($inItems[$kind]) && is_array($inItems[$kind]) ? array_values($inItems[$kind]) : array();

			if (count($list) > self::MAX_ITEMS) {
				$errors[] = $kind . ".count";
				$list = array_slice($list, 0, self::MAX_ITEMS);
			}

			foreach ($list as $raw) {
				if (is_array($raw)) {
					$raw["kind"] = $kind;
				}
				$it = self::sanitizeItem($raw);
				if ($it === null) {
					if ($kind === "spot") {
						$errors[] = "spot.invalid";
					}
					continue;
				}
				$items[$kind][] = $it;
			}
		}

		/* схемийн цэг: slug цорын ганц, давхар нь бодит давхар байх ёстой */
		$seen = array();
		foreach ($items["spot"] as $i => $s) {
			$slug = $s["slug"];
			if ($slug === "" || isset($seen[$slug])) {
				$n = 2;
				$base = $slug !== "" ? $slug : "spot";
				while (isset($seen[$base . "-" . $n])) {
					$n++;
				}
				$slug = $base . "-" . $n;
				$items["spot"][$i]["slug"] = $slug;
			}
			$seen[$slug] = true;

			if (is_array($floorKeys) && count($floorKeys) > 0 && !in_array($s["floor"], $floorKeys, true)) {
				$errors[] = "spot.floor";
			}
		}

		foreach (array("agenda", "activity", "info") as $kind) {
			foreach ($items[$kind] as $i => $it) {
				if ($it["spot"] !== "" && !isset($seen[$it["spot"]])) {
					$items[$kind][$i]["spot"] = "";
				}
			}
		}

		return array("ok" => count($errors) === 0, "errors" => $errors, "settings" => $settings, "items" => $items);
	}

	/* ------------------------------------------------------------------
	   Хадгалах
	   ------------------------------------------------------------------ */

	/**
	 * Бүх агуулгыг нэг transaction-д солино. $expected нь засварлагч нээх үеийн
	 * revision — хэн нэгэн дундуур нь хадгалсан бол "conflict" буцаана.
	 *
	 * @return array("ok"=>bool, "code"=>string, "revision"=>int)
	 */
	public static function save($db, $clean, $expected)
	{
		self::ensure($db);
		$t   = self::tables();
		$now = date("Y-m-d H:i:s");

		/* мөр байхгүй бол FOR UPDATE түгжих зүйлгүй — эхлээд үүсгэнэ */
		$db->rawQuery("INSERT IGNORE INTO `" . $t["setting"] . "` (`setKey`, `setValue`) VALUES ('revision', '0')", null);

		$db->startTransaction();

		try {
			$cur = $db->rawQueryOne("SELECT `setValue` FROM `" . $t["setting"] . "` WHERE `setKey`='revision' FOR UPDATE", null);
			$current = is_array($cur) && isset($cur["setValue"]) ? (int)$cur["setValue"] : 0;

			if ((int)$expected !== $current) {
				$db->rollback();
				return array("ok" => false, "code" => "conflict", "revision" => $current);
			}

			$db->rawQuery("DELETE FROM `" . $t["item"] . "`", null);

			foreach ($clean["items"] as $kind => $list) {
				$order = 0;
				foreach ($list as $it) {
					$order++;
					$row = self::toRow($it, $order);
					$row["createdAt"] = $now;
					$row["updatedAt"] = $now;
					if ($db->insert($t["item"], $row) === false) {
						throw new Exception("item write failed: " . $db->getLastError());
					}
				}
			}

			$values = $clean["settings"];
			$values["revision"] = (string)($current + 1);
			self::writeSettings($db, $values, true);

			$db->commit();

			return array("ok" => true, "code" => "saved", "revision" => $current + 1);
		} catch (Exception $e) {
			$db->rollback();
			throw $e;
		}
	}

	/* ------------------------------------------------------------------
	   Зочдын асуулт
	   ------------------------------------------------------------------ */

	const QUESTION_MAX   = 1000;
	const QUESTION_LIMIT = 5;      /* нэг IP-ээс ... */
	const QUESTION_SPAN  = 600;    /* ... ийм секундэд (10 минут) */

	/**
	 * Зочны асуултыг хадгална.
	 * @return array("ok"=>bool, "code"=>"saved"|"empty"|"rate"|"closed", "id"=>int)
	 */
	public static function askQuestion($db, $text, $name, $lang, $ip)
	{
		self::ensure($db);
		$t = self::tables();

		$text = self::cleanText($text, self::QUESTION_MAX);
		$name = self::cleanLine($name, 80);
		$lang = $lang === "en" ? "en" : "mn";
		$ip   = substr(preg_replace('/[^0-9a-fA-F:.]/', "", (string)$ip), 0, 45);

		if (function_exists("mb_strlen") ? mb_strlen($text, "UTF-8") < 3 : strlen($text) < 3) {
			return array("ok" => false, "code" => "empty", "id" => 0);
		}

		$on = self::scalar($db, "SELECT `setValue` FROM `" . $t["setting"] . "` WHERE `setKey`='askOn'", null);
		if ((string)$on === "0") {
			return array("ok" => false, "code" => "closed", "id" => 0);
		}

		$since = date("Y-m-d H:i:s", time() - self::QUESTION_SPAN);
		$recent = (int)self::scalar($db, "SELECT COUNT(*) FROM `" . $t["question"] . "` WHERE `ip`=? AND `createdAt`>=?", array($ip, $since));
		if ($recent >= self::QUESTION_LIMIT) {
			return array("ok" => false, "code" => "rate", "id" => 0);
		}

		$now = date("Y-m-d H:i:s");
		$id = $db->insert($t["question"], array(
			"question"  => $text,
			"name"      => $name,
			"lang"      => $lang,
			"status"    => 0,
			"ip"        => $ip,
			"createdAt" => $now,
			"updatedAt" => $now
		));

		return array("ok" => $id !== false, "code" => $id !== false ? "saved" : "error", "id" => (int)$id);
	}

	/** Асуултууд (шинэ нь эхэндээ). $afterId > 0 бол зөвхөн түүнээс хойших. */
	public static function questions($db, $afterId = 0, $limit = 500)
	{
		self::ensure($db);
		$t = self::tables();

		$rows = $db->rawQuery(
			"SELECT `qID`, `question`, `name`, `lang`, `status`, `createdAt` FROM `" . $t["question"] . "`"
				. " WHERE `qID`>? ORDER BY `qID` DESC LIMIT " . max(1, min(2000, (int)$limit)),
			array((int)$afterId)
		);

		$out = array();
		if (is_array($rows)) {
			foreach ($rows as $r) {
				$out[] = array(
					"id"     => (int)$r["qID"],
					"text"   => (string)$r["question"],
					"name"   => (string)$r["name"],
					"lang"   => (string)$r["lang"],
					"status" => (int)$r["status"],
					"at"     => (string)$r["createdAt"]
				);
			}
		}

		return $out;
	}

	/** "done" — хариулсан, "new" — шинэ болгох, "delete" — устгах. */
	public static function questionOp($db, $id, $op)
	{
		self::ensure($db);
		$t = self::tables();
		$id = (int)$id;

		if ($op === "delete") {
			$db->rawQuery("DELETE FROM `" . $t["question"] . "` WHERE `qID`=?", array($id));
			return true;
		}
		if ($op === "done" || $op === "new") {
			$db->rawQuery(
				"UPDATE `" . $t["question"] . "` SET `status`=?, `updatedAt`=? WHERE `qID`=?",
				array($op === "done" ? 1 : 0, date("Y-m-d H:i:s"), $id)
			);
			return true;
		}

		return false;
	}

	/* ------------------------------------------------------------------
	   Хэвлэх туслахууд
	   ------------------------------------------------------------------ */

	public static function esc($value)
	{
		return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
	}

	/** Олон мөрт текст -> HTML (escape + <br>). */
	public static function para($value)
	{
		return nl2br(self::esc($value), false);
	}

	/**
	 * Хоёр хэлтэй текст: монгол, англи хоёуланг нь гаргаж CSS-ээр нэгийг нь
	 * нууна (хэл солиход хуудас дахин ачаалагдахгүй). Англи хоосон бол
	 * монголыг нь харуулна.
	 */
	public static function bi($mn, $en, $multiline = false)
	{
		$mn = (string)$mn;
		$en = (string)$en;

		if ($mn === "") {
			$mn = $en;
		}
		if ($en === "") {
			$en = $mn;
		}

		$mnHtml = $multiline ? self::para($mn) : self::esc($mn);

		if ($mn === $en) {
			return $mnHtml;
		}

		$enHtml = $multiline ? self::para($en) : self::esc($en);

		return '<span class="od-mn">' . $mnHtml . '</span><span class="od-en" lang="en">' . $enHtml . '</span>';
	}

	/**
	 * Хуудсан дээр засах горим (CP Admin -> Өдөрлөг): хоёр хэлийг ҮРГЭЛЖ
	 * тусад нь гаргаж, span бүрт засах талбарын замыг (data-ode-f) өгнө.
	 * $edit=false бол bi()-тэй яг ижил (нийтийн хуудас өөрчлөгдөхгүй).
	 *
	 *   $base = "s.title" -> s.titleMn / s.titleEn
	 *           "agenda.3.body" -> agenda.3.bodyMn / agenda.3.bodyEn
	 */
	public static function field($edit, $base, $mn, $en, $multiline = false, $ph = "")
	{
		if (!$edit) {
			return self::bi($mn, $en, $multiline);
		}

		$attr = function ($lang, $ph) use ($base, $multiline) {
			return ' data-ode-f="' . self::esc($base . $lang) . '"'
				. ($multiline ? ' data-ode-multi="1"' : "")
				. ' data-ph="' . self::esc($ph) . '"';
		};

		/* засах үед мөр шилжилтийг текстээр нь үлдээнэ (CSS: white-space:pre-wrap) */
		return '<span class="od-mn"' . $attr("Mn", $ph !== "" ? $ph : "…") . '>' . self::esc($mn) . '</span>'
			. '<span class="od-en" lang="en"' . $attr("En", "EN: " . ($mn !== "" ? $mn : $ph)) . '>' . self::esc($en) . '</span>';
	}

	public static function jsonForHtml($data)
	{
		$flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
		if (defined("JSON_PARTIAL_OUTPUT_ON_ERROR")) {
			$flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
		}

		$json = json_encode($data, $flags);

		/* U+2028/2029 нь <script> доторх JS-д мөр таслагч */
		return str_replace(array("\xE2\x80\xA8", "\xE2\x80\xA9"), array("\\u2028", "\\u2029"), (string)$json);
	}

	/** "11:00", "11:40" -> "11:00 – 11:40"; аль нэг нь хоосон бол нөгөөг нь. */
	public static function timeRange($start, $end)
	{
		if ($start !== "" && $end !== "") {
			return $start . " – " . $end;
		}

		return $start !== "" ? $start : $end;
	}

	/* ------------------------------------------------------------------
	   Жишээ агуулга. CP Admin -> Өдөрлөг хэсгээс бодит мэдээллээр солино.
	   Цэгийн байршил нь Офис схемийн (class/floorplan.class.php) өрөөнүүдийн
	   координат — 20F = office-20f, 21F = office-21f.
	   ------------------------------------------------------------------ */

	public static function sampleItems()
	{
		$out = array();

		/* slug, давхар, x, y, icon, нэр MN, нэр EN, тайлбар MN, тайлбар EN */
		$spots = array(
			array("registration", "office-20f", 0.21720, 0.33584, "registration",
				"Бүртгэл, угтах ширээ", "Registration desk",
				"Гол орцны дэргэд. Нэрээ хэлж бүртгүүлээд энгэрийн тэмдэг, хөтөч авна.",
				"Next to the main entrance. Check in with your name to get your badge and guide."),
			array("cloakroom", "office-20f", 0.09600, 0.42054, "wardrobe",
				"Хувцасны өлгүүр", "Cloakroom",
				"Гадуур хувцас, цүнхээ энд үлдээнэ.",
				"Leave your coat and bags here."),
			array("exhibition", "office-20f", 0.35698, 0.28347, "exhibit",
				"Төслийн үзэсгэлэн", "Project exhibition",
				"Хэрэгжүүлсэн болон хэрэгжиж буй төслүүдийн зураг, загвар.",
				"Drawings and models of our completed and ongoing projects."),
			array("lounge", "office-20f", 0.44419, 0.36591, "stage",
				"Лаунж — гол тайз", "Lounge — main stage",
				"Нээлтийн үг, асуулт хариултын хэсэг энд болно.",
				"Opening remarks and the Q&A session take place here."),
			array("bim-demo", "office-20f", 0.81012, 0.33556, "demo",
				"BIM, DMS танилцуулга", "BIM & DMS demo",
				"BIM технологи болон Design Management System-ийн шууд танилцуулга.",
				"Live demo of our BIM workflow and the Design Management System."),
			array("interior-team", "office-20f", 0.17903, 0.51301, "team",
				"Интерьер дизайны алба", "Interior Design Team",
				"Интерьер дизайнеруудтай уулзаж, ажлын явцтай танилцана.",
				"Meet the interior designers and see work in progress."),
			array("civil-team", "office-20f", 0.82703, 0.51894, "team",
				"Барилга бүтээцийн алба", "Civil Engineering Team",
				"Бүтээцийн инженерүүдтэй уулзана.",
				"Meet the structural engineers."),
			array("mep-team", "office-20f", 0.70751, 0.68340, "team",
				"Инженерийн шугам сүлжээний алба", "MEP Team",
				"Халаалт, агааржуулалт, цахилгаан, ус хангамжийн инженерүүд.",
				"Mechanical, electrical and plumbing engineers."),
			array("photo-spot", "office-20f", 0.91876, 0.57216, "photo",
				"Дурсгалын зураг", "Photo spot",
				"Террас дээр дурсгалын зураг авахуулаарай.",
				"Take a souvenir photo on the terrace."),
			array("coffee", "office-21f", 0.15774, 0.56893, "coffee",
				"Кофе, зууш", "Coffee & snacks",
				"21 давхрын гал тогоонд кофе, цай, зууш бэлэн.",
				"Coffee, tea and snacks in the 21st-floor kitchen."),
			array("architecture-team", "office-21f", 0.63875, 0.58621, "team",
				"Барилга архитектурын алба", "Architecture Team",
				"Архитекторуудтай уулзаж, төслийн үйл явцын талаар ярилцана.",
				"Meet the architects and talk about how a project comes together.")
		);

		foreach ($spots as $s) {
			$out[] = array("kind" => "spot", "enabled" => true, "slug" => $s[0], "floor" => $s[1], "x" => $s[2], "y" => $s[3], "icon" => $s[4],
				"titleMn" => $s[5], "titleEn" => $s[6], "bodyMn" => $s[7], "bodyEn" => $s[8]);
		}

		/* эхлэх, дуусах, байршил, гарчиг MN, EN, тайлбар MN, EN */
		$agenda = array(
			array("11:00", "11:20", "registration", "Бүртгэл, угтах", "Registration & welcome",
				"Бүртгүүлээд, хувцсаа өлгүүрт үлдээж, кофе ууж амсхийнэ үү.",
				"Check in, leave your coat at the cloakroom and grab a coffee."),
			array("11:20", "11:40", "lounge", "Нээлтийн үг", "Opening remarks",
				"Удирдлагын мэндчилгээ, шинэ оффисын тухай.",
				"A welcome from the management and the story of the new office."),
			array("11:40", "12:30", "exhibition", "Оффисын аялал", "Office tour",
				"Багуудаар орж, ажлын орчинтой танилцана. Доорх \"Хийж болох зүйлс\" маршрутыг дагаарай.",
				"Visit the teams and see where the work happens. Follow the route under \"Things to do\"."),
			array("12:30", "13:00", "bim-demo", "BIM, Design Management System танилцуулга", "BIM & Design Management System demo",
				"Зураг төслийн үйл явцыг нэгдсэн байдлаар хэрхэн удирддагийг үзүүлнэ.",
				"How we run the whole design process in one connected system."),
			array("13:00", "13:45", "coffee", "Инженер, архитекторуудтай чөлөөт яриа", "Meet the engineers & architects",
				"Кофе ууж, туршлага солилцох чөлөөт цаг.",
				"Free time over coffee to talk and share experience."),
			array("13:45", "14:15", "lounge", "Асуулт хариулт", "Q&A",
				"Ажлын байр, дадлага, төслүүдийн талаар асуух боломж.",
				"Ask about jobs, internships and our projects."),
			array("14:15", "14:30", "photo-spot", "Хаалт, дурсгалын зураг", "Closing & group photo",
				"", "")
		);

		foreach ($agenda as $a) {
			$out[] = array("kind" => "agenda", "enabled" => true, "start" => $a[0], "end" => $a[1], "spot" => $a[2],
				"titleMn" => $a[3], "titleEn" => $a[4], "bodyMn" => $a[5], "bodyEn" => $a[6]);
		}

		/* тур маршрут — дарааллаараа */
		$activities = array(
			array("", "", "registration", "Бүртгүүлж, хөтөч авах", "Check in and get your guide",
				"Энгэрийн тэмдгээ зүүгээд аяллаа эхлүүлээрэй.",
				"Put on your badge and start the tour."),
			array("", "", "exhibition", "Төслийн үзэсгэлэн үзэх", "See the project exhibition",
				"Төсөл бүрийн дэргэд товч тайлбар байгаа.",
				"Each project has a short description next to it."),
			array("", "", "interior-team", "Интерьер дизайны баг", "Interior Design Team",
				"Материалын дээж, интерьерийн шийдлүүдийг үзнэ.",
				"See material samples and interior concepts."),
			array("", "", "civil-team", "Барилга бүтээцийн баг", "Civil Engineering Team",
				"Бүтээцийн тооцоо, загварчлал хэрхэн хийгддэгийг сонирхоно.",
				"Find out how structures are calculated and modelled."),
			array("", "", "mep-team", "Инженерийн шугам сүлжээний баг", "MEP Team",
				"Инженерийн системүүдийг BIM загвар дээр харна.",
				"See the building services in the BIM model."),
			array("12:30", "13:00", "bim-demo", "BIM, DMS-ийн шууд танилцуулга", "Live BIM & DMS demo",
				"Хурлын өрөөнд. Суудал хязгаартай тул эрт ирээрэй.",
				"In the meeting room. Seats are limited, so come early."),
			array("", "", "architecture-team", "21 давхарт гарч архитекторуудтай уулзах", "Go up to 21F and meet the architects",
				"Оффис доторх эргэлдэх шатаар 21 давхарт гарна.",
				"Take the spiral staircase inside the office up to the 21st floor."),
			array("13:00", "13:45", "coffee", "Кофе ууж, чөлөөтэй ярилцах", "Coffee and conversation",
				"", ""),
			array("14:15", "14:30", "photo-spot", "Террас дээр дурсгалын зураг", "Souvenir photo on the terrace",
				"", "")
		);

		foreach ($activities as $a) {
			$out[] = array("kind" => "activity", "enabled" => true, "start" => $a[0], "end" => $a[1], "spot" => $a[2],
				"titleMn" => $a[3], "titleEn" => $a[4], "bodyMn" => $a[5], "bodyEn" => $a[6]);
		}

		/* icon, байршил, гарчиг MN, EN, агуулга MN, EN */
		$info = array(
			array("wifi", "", "WiFi", "WiFi",
				"Сүлжээ: [нэр]\nНууц үг: [нууц үг]",
				"Network: [name]\nPassword: [password]"),
			array("wardrobe", "cloakroom", "Хувцасны өлгүүр", "Cloakroom",
				"Гадуур хувцас, цүнхээ 20 давхрын хувцасны өрөөнд үлдээнэ.",
				"Leave coats and bags in the 20th-floor locker room."),
			array("stairs", "", "Давхар хооронд", "Between floors",
				"20, 21 давхрыг оффис доторх эргэлдэх шат холбоно.",
				"The 20th and 21st floors are connected by a spiral staircase inside the office."),
			array("restroom", "", "Ариун цэврийн өрөө", "Restrooms",
				"Давхар бүрт байрлана. Хаана байгааг ажилтнаас асууж болно.",
				"On every floor. Any staff member can point the way."),
			array("photo", "", "Зураг авах", "Photos",
				"Зураг авахыг зөвшөөрнө. Ажлын дэлгэц, баримт бичгийн зургийг авахгүй байхыг хүсье.",
				"Photos are welcome. Please do not photograph screens or documents."),
			array("help", "registration", "Тусламж", "Need help?",
				"Бүртгэлийн ширээнд хандана уу.\nУтас: [утасны дугаар]",
				"Ask at the registration desk.\nPhone: [phone number]")
		);

		foreach ($info as $a) {
			$out[] = array("kind" => "info", "enabled" => true, "icon" => $a[0], "spot" => $a[1],
				"titleMn" => $a[2], "titleEn" => $a[3], "bodyMn" => $a[4], "bodyEn" => $a[5]);
		}

		$faq = array(
			array("Хөтөлбөрийн дарааллыг заавал дагах уу?", "Do I have to follow the programme in order?",
				"Үгүй. Өөрийн сонирхсон цэгээр чөлөөтэй явж болно. Одоо юу болж байгааг энэ хуудасны дээд хэсэгт харуулна.",
				"No. Visit whatever interests you. The top of this page always shows what is happening now."),
			array("Багуудтай хэрхэн уулзах вэ?", "How do I meet the teams?",
				"Схем дээрх баг бүрийн цэг дээр тухайн албаны ажилтнууд хүлээж байна. Чөлөөтэй асуулт асуугаарай.",
				"Team members wait at each team's spot on the map. Feel free to ask them anything."),
			array("Ажлын байр, дадлагад яаж өргөдөл гаргах вэ?", "How do I apply for a job or internship?",
				"mglenc.com/career (ажлын байр) болон mglenc.com/internship (дадлага) хуудсаар онлайнаар өргөдлөө илгээнэ.",
				"Apply online at mglenc.com/career (jobs) or mglenc.com/internship (internships)."),
			array("Энд байхгүй асуулт гарвал?", "What if my question is not answered here?",
				"Бүртгэлийн ширээнд эсвэл энгэрийн тэмдэгтэй ажилтанд хандана уу.",
				"Ask at the registration desk or any staff member with a badge.")
		);

		foreach ($faq as $a) {
			$out[] = array("kind" => "faq", "enabled" => true, "titleMn" => $a[0], "titleEn" => $a[1], "bodyMn" => $a[2], "bodyEn" => $a[3]);
		}

		return $out;
	}
}
