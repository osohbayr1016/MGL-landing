<?php
/**
 * Интерактив оффисын схем (Офис хуудасны hero) — 20, 21-р давхар.
 *
 * Вэб сайт (widgets/pagesch/floorplan.hero.php) болон CP Admin
 * (cpadmin/pages/floorplan) хоёулаа ЯГ ЭНЭ файлыг ашиглана — хүснэгтийн
 * бүтэц, шалгалтын дүрэм хэзээ ч зөрөхгүй.
 *
 * - Давхар бүр өөрийн зурагтай (assets/images/floorplan/office-20f-*.webp,
 *   office-21f-*.webp) "plan" мөр; цэгүүд нь тухайн давхрын зургийн харьцаанд
 *   НОРМАЛЧИЛСАН 0..1 координаттай (пиксель хадгалахгүй).
 * - Давхар бүр шатны цэгтэй (linkX, linkY) — тэнд дарахад нөгөө давхар руу шилжинэ.
 * - Хүснэгтүүд эхний хандалт дээр автоматаар үүснэ (ensure); const.php-г
 *   гараар засах шаардлагагүй.
 * - Өгөгдлийн сан бэлэн биш/алдаатай үед load() нь суулгацын өгөгдмөлийг
 *   буцаана — тиймээс Офис хуудас хэзээ ч унахгүй.
 * - PHP 7.2-той тохирно (production).
 */

class FloorPlanCore
{
	const MAX_SPOTS  = 80;
	const ZOOM_MIN   = 1.2;
	const ZOOM_MAX   = 16.0;
	const OFFSET_MAX = 0.4;

	/** Зөвшөөрөгдөх төрөл. Олон нийтийн дизайнд нөлөөлөхгүй, зөвхөн мэдээлэл. */
	public static function kinds()
	{
		return array("room", "team", "entrance", "terrace", "other");
	}

	/* ------------------------------------------------------------------
	   Хүснэгтүүд
	   ------------------------------------------------------------------ */

	public static function tables()
	{
		global $db_floorplan, $db_floorplan_hotspot, $tbl_pref;

		$pref = isset($tbl_pref) && $tbl_pref != "" ? $tbl_pref : "db_";

		return array(
			"plan"    => isset($db_floorplan) && $db_floorplan != "" ? $db_floorplan : $pref . "floorplan",
			"hotspot" => isset($db_floorplan_hotspot) && $db_floorplan_hotspot != "" ? $db_floorplan_hotspot : $pref . "floorplan_hotspot"
		);
	}

	/** Хүснэгтүүдийг үүсгэж (байхгүй бол), шинэчилж, өгөгдмөлөөр дүүргэнэ. */
	public static function install($db)
	{
		self::createTables($db);
		self::migrate($db);
		self::seed($db);
	}

	public static function createTables($db)
	{
		$t = self::tables();

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["plan"] . "` (
			`planKey` varchar(32) NOT NULL,
			`sortOrder` int(11) NOT NULL DEFAULT '0',
			`floorTitle` varchar(120) NOT NULL DEFAULT '',
			`imageUrl` varchar(255) NOT NULL DEFAULT '',
			`imageMidUrl` varchar(255) NOT NULL DEFAULT '',
			`imageFullUrl` varchar(255) NOT NULL DEFAULT '',
			`imageWidth` int(11) NOT NULL DEFAULT '0',
			`imageHeight` int(11) NOT NULL DEFAULT '0',
			`boundX0` double NOT NULL DEFAULT '0',
			`boundY0` double NOT NULL DEFAULT '0',
			`boundX1` double NOT NULL DEFAULT '1',
			`boundY1` double NOT NULL DEFAULT '1',
			`linkX` double DEFAULT NULL,
			`linkY` double DEFAULT NULL,
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`revision` int(11) NOT NULL DEFAULT '1',
			`createdAt` datetime DEFAULT NULL,
			`updatedAt` datetime DEFAULT NULL,
			PRIMARY KEY (`planKey`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["hotspot"] . "` (
			`hotspotID` int(11) NOT NULL AUTO_INCREMENT,
			`planKey` varchar(32) NOT NULL,
			`slug` varchar(48) NOT NULL,
			`sortOrder` int(11) NOT NULL DEFAULT '0',
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`kind` varchar(16) NOT NULL DEFAULT 'other',
			`titleEn` varchar(80) NOT NULL DEFAULT '',
			`titleMn` varchar(120) NOT NULL DEFAULT '',
			`descriptionMn` varchar(300) NOT NULL DEFAULT '',
			`descriptionEn` varchar(300) NOT NULL DEFAULT '',
			`x` double NOT NULL DEFAULT '0',
			`y` double NOT NULL DEFAULT '0',
			`desktopZoom` double NOT NULL DEFAULT '4.5',
			`desktopFocusX` double DEFAULT NULL,
			`desktopFocusY` double DEFAULT NULL,
			`desktopOffsetX` double NOT NULL DEFAULT '0',
			`desktopOffsetY` double NOT NULL DEFAULT '0',
			`mobileZoom` double NOT NULL DEFAULT '7.5',
			`mobileFocusX` double DEFAULT NULL,
			`mobileFocusY` double DEFAULT NULL,
			`mobileOffsetX` double NOT NULL DEFAULT '0',
			`mobileOffsetY` double NOT NULL DEFAULT '0',
			`createdAt` datetime DEFAULT NULL,
			`updatedAt` datetime DEFAULT NULL,
			PRIMARY KEY (`hotspotID`),
			UNIQUE KEY `planSlug` (`planKey`,`slug`),
			KEY `planOrder` (`planKey`,`sortOrder`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}

	/**
	 * Нэг давхартай эхний хувилбарын хүснэгтээс шинэчилнэ (тэр хувилбар live-д
	 * гараагүй ч локал/туршилтын сан байж болно):
	 *   - дутуу багануудыг нэмнэ
	 *   - бүтэн зураг (8852 өргөн) дээрх хуучин координатууд шинэ давхрын зурагт
	 *     таарахгүй тул тэр өгөгдлийг устгаж, seed() дахин дүүргэнэ.
	 */
	public static function migrate($db)
	{
		$t = self::tables();

		$col = $db->rawQueryOne("SHOW COLUMNS FROM `" . $t["plan"] . "` LIKE 'linkX'", null);
		if (!is_array($col) || count($col) == 0) {
			$db->rawQuery("ALTER TABLE `" . $t["plan"] . "`
				ADD `sortOrder` int(11) NOT NULL DEFAULT '0',
				ADD `boundX0` double NOT NULL DEFAULT '0',
				ADD `boundY0` double NOT NULL DEFAULT '0',
				ADD `boundX1` double NOT NULL DEFAULT '1',
				ADD `boundY1` double NOT NULL DEFAULT '1',
				ADD `linkX` double DEFAULT NULL,
				ADD `linkY` double DEFAULT NULL");
		}

		/* англи тайлбар (2026-10) */
		$col = $db->rawQueryOne("SHOW COLUMNS FROM `" . $t["hotspot"] . "` LIKE 'descriptionEn'", null);
		if (!is_array($col) || count($col) == 0) {
			$db->rawQuery("ALTER TABLE `" . $t["hotspot"] . "` ADD `descriptionEn` varchar(300) NOT NULL DEFAULT '' AFTER `descriptionMn`");
		}

		$old = self::scalar($db, "SELECT COUNT(*) FROM `" . $t["plan"] . "` WHERE `imageWidth`=8852", null);
		if ((int)$old > 0) {
			$db->rawQuery("DELETE FROM `" . $t["hotspot"] . "`", null);
			$db->rawQuery("DELETE FROM `" . $t["plan"] . "`", null);
		}
	}

	/** Байхгүй давхар бүрийг өгөгдмөл цэгүүдтэй нь нэмнэ. Байгаа давхрыг хөндөхгүй. */
	public static function seed($db)
	{
		$t   = self::tables();
		$now = date("Y-m-d H:i:s");

		foreach (self::defaults() as $floor) {
			$plan = $floor["plan"];

			$has = self::scalar($db, "SELECT COUNT(*) FROM `" . $t["plan"] . "` WHERE `planKey`=?", array($plan["key"]));
			if ((int)$has > 0) {
				continue;
			}

			$db->insert($t["plan"], array(
				"planKey"      => $plan["key"],
				"sortOrder"    => $plan["sortOrder"],
				"floorTitle"   => $plan["floorTitle"],
				"imageUrl"     => $plan["imageUrl"],
				"imageMidUrl"  => $plan["imageMidUrl"],
				"imageFullUrl" => $plan["imageFullUrl"],
				"imageWidth"   => $plan["imageWidth"],
				"imageHeight"  => $plan["imageHeight"],
				"boundX0"      => $plan["bounds"][0],
				"boundY0"      => $plan["bounds"][1],
				"boundX1"      => $plan["bounds"][2],
				"boundY1"      => $plan["bounds"][3],
				"linkX"        => $plan["link"]["x"],
				"linkY"        => $plan["link"]["y"],
				"enabled"      => 1,
				"revision"     => 1,
				"createdAt"    => $now,
				"updatedAt"    => $now
			));

			/* цэгүүд үлдсэн байвал (зөвхөн plan хүснэгт алга болсон) давхардуулахгүй */
			$spots = self::scalar($db, "SELECT COUNT(*) FROM `" . $t["hotspot"] . "` WHERE `planKey`=?", array($plan["key"]));
			if ((int)$spots > 0) {
				continue;
			}

			foreach ($floor["hotspots"] as $h) {
				$row = self::toRow($h);
				$row["planKey"]   = $plan["key"];
				$row["createdAt"] = $now;
				$row["updatedAt"] = $now;
				$db->insert($t["hotspot"], $row);
			}
		}
	}

	public static function tableExists($db, $table)
	{
		$row = $db->rawQueryOne("SHOW TABLES LIKE '" . str_replace("'", "", $table) . "'", null);

		return is_array($row) && count($row) > 0;
	}

	/** Модуль суусан эсэх — суугаагүй бол суулгана. Хүсэлтэд нэг удаа. */
	public static function ensure($db)
	{
		if (!empty($GLOBALS["floorPlanModuleReady"])) {
			return;
		}

		$t = self::tables();

		if (!self::tableExists($db, $t["plan"]) || !self::tableExists($db, $t["hotspot"])) {
			self::createTables($db);
		}
		self::migrate($db);

		/* бүх давхар байвал seed-ийн шалгалтыг алгасна (Офис хуудас бүрт нэмэлт query гаргахгүй) */
		$plans = self::scalar($db, "SELECT COUNT(*) FROM `" . $t["plan"] . "`", null);
		if ((int)$plans < count(self::defaults())) {
			self::seed($db);
		}

		$GLOBALS["floorPlanModuleReady"] = true;
	}

	public static function scalar($db, $sql, $params = null)
	{
		$val = $db->rawQueryValue($sql . " LIMIT 1", $params);

		if (is_array($val)) {
			$val = count($val) > 0 ? reset($val) : null;
		}

		return $val;
	}

	/* ------------------------------------------------------------------
	   Унших
	   ------------------------------------------------------------------ */

	/**
	 * Давхрууд (доороос дээш) + тус бүрийн цэгүүд:
	 *   array(
	 *     "floors"    => array(array("plan" => array(...), "hotspots" => array(...)), ...),
	 *     "revisions" => array("office-20f" => 3, ...),
	 *     "fallback"  => bool
	 *   )
	 *
	 * $enabledOnly=true бол зөвхөн идэвхтэй давхар, идэвхтэй цэгүүд (нийтийн хуудас).
	 * Өгөгдлийн сангийн алдаа гарвал суулгацын өгөгдмөл (fallback=true).
	 */
	public static function load($db, $enabledOnly = false)
	{
		try {
			if ($db === null) {
				throw new Exception("no db");
			}

			self::ensure($db);
			$t = self::tables();

			$plans = $db->rawQuery("SELECT * FROM `" . $t["plan"] . "` ORDER BY `sortOrder` ASC, `planKey` ASC", null);
			if (!is_array($plans) || count($plans) == 0) {
				throw new Exception("plan rows missing");
			}

			$rows = $db->rawQuery("SELECT * FROM `" . $t["hotspot"] . "` ORDER BY `sortOrder` ASC, `hotspotID` ASC", null);
			if (!is_array($rows)) {
				throw new Exception("hotspot query failed");
			}

			$byPlan = array();
			foreach ($rows as $row) {
				$h = self::fromRow($row);
				if ($h !== null && (!$enabledOnly || $h["enabled"])) {
					$byPlan[$row["planKey"]][] = $h;
				}
			}

			$floors = array();
			$revisions = array();
			foreach ($plans as $p) {
				$plan = self::planFromRow($p);
				$revisions[$plan["key"]] = (int)$p["revision"];
				if ($enabledOnly && !$plan["enabled"]) {
					continue;
				}
				$floors[] = array(
					"plan"     => $plan,
					"hotspots" => isset($byPlan[$plan["key"]]) ? $byPlan[$plan["key"]] : array()
				);
			}

			return array("floors" => $floors, "revisions" => $revisions, "fallback" => false);
		} catch (Exception $e) {
			return self::fallback($enabledOnly);
		} catch (Throwable $e) {
			return self::fallback($enabledOnly);
		}
	}

	private static function fallback($enabledOnly)
	{
		$floors = array();
		$revisions = array();

		foreach (self::defaults() as $floor) {
			$spots = array();
			foreach ($floor["hotspots"] as $h) {
				if (!$enabledOnly || $h["enabled"]) {
					$spots[] = $h;
				}
			}
			$floors[] = array("plan" => $floor["plan"], "hotspots" => $spots);
			$revisions[$floor["plan"]["key"]] = 0;
		}

		return array("floors" => $floors, "revisions" => $revisions, "fallback" => true);
	}

	public static function planFromRow($p)
	{
		$num = function ($v, $d) {
			return is_numeric($v) && is_finite((float)$v) ? (float)$v : $d;
		};

		$x0 = self::clamp($num(isset($p["boundX0"]) ? $p["boundX0"] : null, 0.0), 0, 1);
		$y0 = self::clamp($num(isset($p["boundY0"]) ? $p["boundY0"] : null, 0.0), 0, 1);
		$x1 = self::clamp($num(isset($p["boundX1"]) ? $p["boundX1"] : null, 1.0), 0, 1);
		$y1 = self::clamp($num(isset($p["boundY1"]) ? $p["boundY1"] : null, 1.0), 0, 1);
		if (!($x1 > $x0 && $y1 > $y0)) {
			$x0 = 0.0; $y0 = 0.0; $x1 = 1.0; $y1 = 1.0;
		}

		$lx = isset($p["linkX"]) ? $num($p["linkX"], null) : null;
		$ly = isset($p["linkY"]) ? $num($p["linkY"], null) : null;

		return array(
			"key"          => (string)$p["planKey"],
			"sortOrder"    => isset($p["sortOrder"]) ? (int)$p["sortOrder"] : 0,
			"floorTitle"   => (string)$p["floorTitle"],
			"imageUrl"     => (string)$p["imageUrl"],
			"imageMidUrl"  => (string)$p["imageMidUrl"],
			"imageFullUrl" => (string)$p["imageFullUrl"],
			"imageWidth"   => (int)$p["imageWidth"],
			"imageHeight"  => (int)$p["imageHeight"],
			"bounds"       => array($x0, $y0, $x1, $y1),
			"link"         => ($lx === null || $ly === null) ? null : array("x" => self::clamp($lx, 0, 1), "y" => self::clamp($ly, 0, 1)),
			"enabled"      => (int)$p["enabled"] === 1
		);
	}

	/** DB мөр -> JS бүтэц. Хэрэглэх боломжгүй мөрийг null болгоно. */
	public static function fromRow($row)
	{
		return self::sanitizeHotspot(array(
			"slug"          => isset($row["slug"]) ? $row["slug"] : "",
			"order"         => isset($row["sortOrder"]) ? $row["sortOrder"] : 0,
			"enabled"       => isset($row["enabled"]) ? (int)$row["enabled"] === 1 : true,
			"kind"          => isset($row["kind"]) ? $row["kind"] : "other",
			"titleEn"       => isset($row["titleEn"]) ? $row["titleEn"] : "",
			"titleMn"       => isset($row["titleMn"]) ? $row["titleMn"] : "",
			"descriptionMn" => isset($row["descriptionMn"]) ? $row["descriptionMn"] : "",
			"descriptionEn" => isset($row["descriptionEn"]) ? $row["descriptionEn"] : "",
			"x"             => isset($row["x"]) ? $row["x"] : null,
			"y"             => isset($row["y"]) ? $row["y"] : null,
			"desktop" => array(
				"zoom"    => isset($row["desktopZoom"]) ? $row["desktopZoom"] : null,
				"focusX"  => isset($row["desktopFocusX"]) ? $row["desktopFocusX"] : null,
				"focusY"  => isset($row["desktopFocusY"]) ? $row["desktopFocusY"] : null,
				"offsetX" => isset($row["desktopOffsetX"]) ? $row["desktopOffsetX"] : 0,
				"offsetY" => isset($row["desktopOffsetY"]) ? $row["desktopOffsetY"] : 0
			),
			"mobile" => array(
				"zoom"    => isset($row["mobileZoom"]) ? $row["mobileZoom"] : null,
				"focusX"  => isset($row["mobileFocusX"]) ? $row["mobileFocusX"] : null,
				"focusY"  => isset($row["mobileFocusY"]) ? $row["mobileFocusY"] : null,
				"offsetX" => isset($row["mobileOffsetX"]) ? $row["mobileOffsetX"] : 0,
				"offsetY" => isset($row["mobileOffsetY"]) ? $row["mobileOffsetY"] : 0
			)
		), 0);
	}

	/** Цэвэр JS бүтэц -> DB мөр. */
	public static function toRow($h)
	{
		return array(
			"slug"           => $h["slug"],
			"sortOrder"      => (int)$h["order"],
			"enabled"        => $h["enabled"] ? 1 : 0,
			"kind"           => $h["kind"],
			"titleEn"        => $h["titleEn"],
			"titleMn"        => $h["titleMn"],
			"descriptionMn"  => $h["descriptionMn"],
			"descriptionEn"  => $h["descriptionEn"],
			"x"              => $h["x"],
			"y"              => $h["y"],
			"desktopZoom"    => $h["desktop"]["zoom"],
			"desktopFocusX"  => $h["desktop"]["focusX"],
			"desktopFocusY"  => $h["desktop"]["focusY"],
			"desktopOffsetX" => $h["desktop"]["offsetX"],
			"desktopOffsetY" => $h["desktop"]["offsetY"],
			"mobileZoom"     => $h["mobile"]["zoom"],
			"mobileFocusX"   => $h["mobile"]["focusX"],
			"mobileFocusY"   => $h["mobile"]["focusY"],
			"mobileOffsetX"  => $h["mobile"]["offsetX"],
			"mobileOffsetY"  => $h["mobile"]["offsetY"]
		);
	}

	/* ------------------------------------------------------------------
	   Шалгалт (сервер талд — JS-ийн sanitizeHotspot-той ижил дүрэм)
	   ------------------------------------------------------------------ */

	public static function clamp($v, $min, $max)
	{
		return min($max, max($min, $v));
	}

	/** Тоо эсэх (NaN/INF/хоосон тэмдэгт биш). */
	private static function numeric($v)
	{
		if (is_int($v) || is_float($v)) {
			return is_finite((float)$v);
		}

		return is_string($v) && trim($v) !== "" && is_numeric($v) && is_finite((float)$v);
	}

	public static function cleanText($v, $max)
	{
		if ($v === null || is_array($v) || is_object($v)) {
			return "";
		}

		/* удирдлагын тэмдэгтүүд (таб, мөр шилжилтээс бусад) хасна */
		$s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', "", (string)$v);

		/* UTF-8 биш байт json_encode-ийг бүхэлд нь эвдэнэ — хаяна */
		if ($s === null || !preg_match('//u', $s)) {
			return "";
		}

		$s = trim($s);

		if (function_exists("mb_substr")) {
			return mb_substr($s, 0, $max, "UTF-8");
		}

		/* mbstring байхгүй үед ч тэмдэгтийн зааг дээр таслана (кирилл тэмдэгтийг хагалахгүй) */
		return preg_match('/^.{0,' . (int)$max . '}/us', $s, $m) ? $m[0] : "";
	}

	private static function cleanSet($s, $defZoom)
	{
		$s = is_array($s) ? $s : array();

		$zoom = isset($s["zoom"]) && self::numeric($s["zoom"]) ? (float)$s["zoom"] : $defZoom;

		$fx = isset($s["focusX"]) && self::numeric($s["focusX"]) ? self::clamp((float)$s["focusX"], 0, 1) : null;
		$fy = isset($s["focusY"]) && self::numeric($s["focusY"]) ? self::clamp((float)$s["focusY"], 0, 1) : null;

		$ox = isset($s["offsetX"]) && self::numeric($s["offsetX"]) ? (float)$s["offsetX"] : 0.0;
		$oy = isset($s["offsetY"]) && self::numeric($s["offsetY"]) ? (float)$s["offsetY"] : 0.0;

		return array(
			"zoom"    => round(self::clamp($zoom, self::ZOOM_MIN, self::ZOOM_MAX), 3),
			"focusX"  => $fx === null ? null : round($fx, 5),
			"focusY"  => $fy === null ? null : round($fy, 5),
			"offsetX" => round(self::clamp($ox, -self::OFFSET_MAX, self::OFFSET_MAX), 4),
			"offsetY" => round(self::clamp($oy, -self::OFFSET_MAX, self::OFFSET_MAX), 4)
		);
	}

	public static function cleanSlug($v)
	{
		$s = strtolower(self::cleanText($v, 48));

		return preg_replace('/[^a-z0-9-]/', "", $s);
	}

	/**
	 * Нэг цэгийг цэвэрлэнэ. Slug байхгүй эсвэл координат тоо биш бол null.
	 * Бусад утгыг зөвшөөрөгдөх хязгаарт шахна (zoom=100 -> 16).
	 */
	public static function sanitizeHotspot($raw, $index)
	{
		if (!is_array($raw)) {
			return null;
		}

		$slug = self::cleanSlug(isset($raw["slug"]) ? $raw["slug"] : "");
		if ($slug === "" || !isset($raw["x"]) || !isset($raw["y"]) || !self::numeric($raw["x"]) || !self::numeric($raw["y"])) {
			return null;
		}

		$kind = isset($raw["kind"]) && in_array($raw["kind"], self::kinds(), true) ? $raw["kind"] : "other";

		$enabled = true;
		if (isset($raw["enabled"])) {
			$enabled = !($raw["enabled"] === false || $raw["enabled"] === 0 || $raw["enabled"] === "0");
		}

		return array(
			"slug"          => $slug,
			"order"         => isset($raw["order"]) && self::numeric($raw["order"]) ? (int)$raw["order"] : (int)$index,
			"enabled"       => $enabled,
			"kind"          => $kind,
			"titleEn"       => self::cleanText(isset($raw["titleEn"]) ? $raw["titleEn"] : "", 80),
			"titleMn"       => self::cleanText(isset($raw["titleMn"]) ? $raw["titleMn"] : "", 120),
			"descriptionMn" => self::cleanText(isset($raw["descriptionMn"]) ? $raw["descriptionMn"] : "", 300),
			"descriptionEn" => self::cleanText(isset($raw["descriptionEn"]) ? $raw["descriptionEn"] : "", 300),
			"x"             => round(self::clamp((float)$raw["x"], 0, 1), 5),
			"y"             => round(self::clamp((float)$raw["y"], 0, 1), 5),
			"desktop"       => self::cleanSet(isset($raw["desktop"]) ? $raw["desktop"] : null, 4.5),
			"mobile"        => self::cleanSet(isset($raw["mobile"]) ? $raw["mobile"] : null, 7.5)
		);
	}

	/**
	 * Хадгалах хүсэлтийн бие:
	 *   array("floors" => array(array("key"=>"office-20f", "floorTitle"=>..., "enabled"=>..., "link"=>array("x","y"), "hotspots"=>array(...)), ...))
	 * Давхрын түлхүүр нь өгөгдмөл давхруудын нэг байх ёстой (давхар нэмэх/устгах
	 * засварлагчаас боломжгүй). Slug бүх давхарт давтагдахгүй (?area= линк).
	 * Цэгийн дараалал нь массивын эрэмбээр (1,2,3 ...) тогтоогдоно.
	 *
	 * @return array("ok"=>bool, "errors"=>array, "floors"=>array)
	 */
	public static function sanitizePayload($payload)
	{
		$errors = array();
		$known = array();
		foreach (self::defaults() as $f) {
			$known[$f["plan"]["key"]] = $f["plan"]["floorTitle"];
		}

		if (!is_array($payload) || !isset($payload["floors"]) || !is_array($payload["floors"]) || count($payload["floors"]) == 0) {
			return array("ok" => false, "errors" => array("Хүсэлтийн бүтэц буруу байна."), "floors" => array());
		}

		$floors = array();
		$seenSlug = array();
		$seenKey = array();
		$total = 0;

		foreach ($payload["floors"] as $rawFloor) {
			$rawFloor = is_array($rawFloor) ? $rawFloor : array();
			$key = isset($rawFloor["key"]) && is_string($rawFloor["key"]) ? $rawFloor["key"] : "";

			if (!isset($known[$key]) || isset($seenKey[$key])) {
				$errors[] = "Давхрын түлхүүр буруу эсвэл давхардсан: " . self::cleanText($key, 32);
				continue;
			}
			$seenKey[$key] = true;

			$title = self::cleanText(isset($rawFloor["floorTitle"]) ? $rawFloor["floorTitle"] : "", 60);
			if ($title === "") {
				$errors[] = $known[$key] . ": давхрын гарчиг хоосон байж болохгүй.";
			}

			$enabled = !(isset($rawFloor["enabled"]) && ($rawFloor["enabled"] === false || $rawFloor["enabled"] === 0 || $rawFloor["enabled"] === "0"));

			$link = null;
			if (isset($rawFloor["link"]) && is_array($rawFloor["link"])) {
				$lx = isset($rawFloor["link"]["x"]) ? $rawFloor["link"]["x"] : null;
				$ly = isset($rawFloor["link"]["y"]) ? $rawFloor["link"]["y"] : null;
				if (self::numeric($lx) && self::numeric($ly)) {
					$link = array("x" => round(self::clamp((float)$lx, 0, 1), 5), "y" => round(self::clamp((float)$ly, 0, 1), 5));
				}
			}

			$rawSpots = isset($rawFloor["hotspots"]) && is_array($rawFloor["hotspots"]) ? $rawFloor["hotspots"] : array();
			$total += count($rawSpots);

			$spots = array();
			$i = 0;
			foreach (array_slice($rawSpots, 0, self::MAX_SPOTS) as $raw) {
				$i++;
				$raw = is_array($raw) ? $raw : array();
				$raw["order"] = $i;

				$h = self::sanitizeHotspot($raw, $i);
				if ($h === null) {
					$errors[] = $title . " #" . $i . " цэгийн slug эсвэл байршил (x, y) буруу байна.";
					continue;
				}

				if (isset($seenSlug[$h["slug"]])) {
					$errors[] = "Slug давхардсан: " . $h["slug"];
					continue;
				}

				if ($h["titleEn"] === "" && $h["titleMn"] === "") {
					$errors[] = "\"" . $h["slug"] . "\" цэгт англи эсвэл монгол нэр заавал байх ёстой.";
					continue;
				}

				$seenSlug[$h["slug"]] = true;
				$spots[] = $h;
			}

			$floors[] = array("key" => $key, "title" => $title, "enabled" => $enabled, "link" => $link, "hotspots" => $spots);
		}

		if ($total > self::MAX_SPOTS) {
			$errors[] = "Нийт хамгийн ихдээ " . self::MAX_SPOTS . " цэг байж болно.";
		}

		return array("ok" => count($errors) == 0, "errors" => $errors, "floors" => $floors);
	}

	/* ------------------------------------------------------------------
	   Хадгалах
	   ------------------------------------------------------------------ */

	/**
	 * Бүх давхрыг нэг transaction-д хадгална. $expected = array(planKey => revision)
	 * нь засварлагч нээх үеийн revision; хэн нэгэн дундуур хадгалсан бол
	 * "conflict" буцаана (хуучин хуудаснаас шинэ өөрчлөлтийг дарж бичихээс сэргийлнэ).
	 *
	 * @return array("ok"=>bool, "code"=>string, "revisions"=>array)
	 */
	public static function save($db, $clean, $expected)
	{
		self::ensure($db);
		$t   = self::tables();
		$now = date("Y-m-d H:i:s");
		$expected = is_array($expected) ? $expected : array();

		$db->startTransaction();

		try {
			$current = array();
			foreach ($clean["floors"] as $f) {
				$cur = $db->rawQueryOne(
					"SELECT `revision` FROM `" . $t["plan"] . "` WHERE `planKey`=? FOR UPDATE",
					array($f["key"])
				);

				if (!is_array($cur) || count($cur) == 0) {
					$db->rollback();
					return array("ok" => false, "code" => "missing", "revisions" => array());
				}

				$current[$f["key"]] = (int)$cur["revision"];
				if (!isset($expected[$f["key"]]) || (int)$expected[$f["key"]] !== (int)$cur["revision"]) {
					$db->rollback();
					return array("ok" => false, "code" => "conflict", "revisions" => $current);
				}
			}

			$revisions = array();

			foreach ($clean["floors"] as $f) {
				$key = $f["key"];

				$db->rawQuery(
					"UPDATE `" . $t["plan"] . "` SET `floorTitle`=?, `enabled`=?, `linkX`=?, `linkY`=?, `revision`=`revision`+1, `updatedAt`=? WHERE `planKey`=?",
					array($f["title"], $f["enabled"] ? 1 : 0,
						$f["link"] === null ? null : $f["link"]["x"],
						$f["link"] === null ? null : $f["link"]["y"],
						$now, $key)
				);
				$revisions[$key] = $current[$key] + 1;

				$existing = array();
				$rows = $db->rawQuery("SELECT `slug` FROM `" . $t["hotspot"] . "` WHERE `planKey`=?", array($key));
				if (is_array($rows)) {
					foreach ($rows as $r) {
						$existing[$r["slug"]] = true;
					}
				}

				/* цэг нэг давхраас нөгөөд шилжсэн бол хуучин давхраас нь эхлээд хасна */
				$keep = array();
				foreach ($f["hotspots"] as $h) {
					$keep[] = $h["slug"];
				}
				if (count($keep) > 0) {
					$marks = implode(",", array_fill(0, count($keep), "?"));
					$db->rawQuery(
						"DELETE FROM `" . $t["hotspot"] . "` WHERE `planKey`<>? AND `slug` IN (" . $marks . ")",
						array_merge(array($key), $keep)
					);
				}

				foreach ($f["hotspots"] as $h) {
					$row = self::toRow($h);

					if (isset($existing[$h["slug"]])) {
						$row["updatedAt"] = $now;
						$db->where("planKey", $key);
						$db->where("slug", $h["slug"]);
						$ok = $db->update($t["hotspot"], $row);
					} else {
						$row["planKey"]   = $key;
						$row["createdAt"] = $now;
						$row["updatedAt"] = $now;
						$ok = $db->insert($t["hotspot"], $row);
					}

					if ($ok === false) {
						throw new Exception("hotspot write failed: " . $db->getLastError());
					}
				}

				if (count($keep) > 0) {
					$marks = implode(",", array_fill(0, count($keep), "?"));
					$db->rawQuery(
						"DELETE FROM `" . $t["hotspot"] . "` WHERE `planKey`=? AND `slug` NOT IN (" . $marks . ")",
						array_merge(array($key), $keep)
					);
				} else {
					$db->rawQuery("DELETE FROM `" . $t["hotspot"] . "` WHERE `planKey`=?", array($key));
				}
			}

			$db->commit();

			return array("ok" => true, "code" => "saved", "revisions" => $revisions);
		} catch (Exception $e) {
			$db->rollback();
			throw $e;
		}
	}

	/* ------------------------------------------------------------------
	   HTML рүү аюулгүй JSON
	   ------------------------------------------------------------------ */

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

	public static function esc($value)
	{
		return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
	}

	/* ------------------------------------------------------------------
	   Суулгацын өгөгдөл.
	   Эх зураг office.zuraglal.tsulgui.png (8852x4252) нь хоёр барилгатай:
	   зүүн = 20-р давхар, баруун = 21-р давхар. Тус бүрийг цагаан захтай
	   тусдаа зураг болгон таслав. Цэгийн байршлыг байршил_тайлбар.jpg дээрх
	   улаан/ногоон цэгүүдээс алгоритмаар илрүүлж, тухайн давхрын зургийн
	   координат руу шилжүүлсэн. Шатны байршил (link) нь хоёр давхрыг холбосон
	   эргэлдэх шат.
	   ------------------------------------------------------------------ */

	public static function defaults()
	{
		$base = "/assets/images/floorplan/";

		$floorDefs = array(
			array(
				"key" => "office-20f", "title" => "20-Р ДАВХАР", "sortOrder" => 20, "w" => 5288, "h" => 3012,
				"bounds" => array(0.03707, 0.06507, 0.96293, 0.93493), "link" => array("x" => 0.59284, "y" => 0.75765),
				/* slug, kind, EN, MN, тайлбар, x, y, desktop zoom, mobile zoom, англи тайлбар */
				"spots" => array(
					array("locker-room",            "room",     "Locker Room",            "Хувцас солих өрөө",               "Хувцас солих болон хадгалах зориулалт бүхий өрөө",      0.09600, 0.42054, 2.8, 4.5, "Changing room with personal storage"),
					array("main-entrance",          "entrance", "Main Entrance",          "Гол орц",                         "Оффис руу нэвтрэх гол хаалга",                           0.21720, 0.33584, 2.8, 4.5, "The main door into the office"),
					array("showroom",               "room",     "Showroom",               "Үзэсгэлэнгийн танхим",            "Материал, загвараа танилцуулах үзэсгэлэнгийн талбай",    0.35698, 0.28347, 2.8, 4.5, "Showroom for materials and design samples"),
					array("lounge",                 "room",     "Lounge",                 "Лаунж",                           "Амрах, чөлөөтэй уулзалт хийх зөөлөн тавилгатай талбай",  0.44419, 0.36591, 2.8, 4.5, "Soft seating for breaks and informal meetings"),
					array("interior-design-team",   "team",     "Interior Design Team",   "Интерьер дизайны алба",           "Интерьер дизайны багийн ажлын байр",                     0.17903, 0.51301, 2.8, 4.5, "Workspace of the interior design team"),
					array("management-team",        "team",     "Management Team",        "Санхүү, менежментийн алба",       "Санхүү, менежментийн багийн ажлын байр",                 0.29705, 0.64331, 2.8, 4.5, "Workspace of the finance and management team"),
					array("recreation-room",        "room",     "Recreation Room",        "Амралтын өрөө",                   "Ажилтнуудын амрах, чөлөөт цагаа өнгөрүүлэх өрөө",         0.47801, 0.65065, 2.8, 4.5, "A room to rest and spend free time"),
					array("mep-team",               "team",     "MEP Team",               "Инженерийн шугам сүлжээний алба", "Инженерийн шугам сүлжээний багийн ажлын байр",           0.70751, 0.68340, 2.5, 4.2, "Workspace of the mechanical, electrical and plumbing team"),
					array("civil-engineering-team", "team",     "Civil Engineering Team", "Барилга бүтээцийн алба",          "Барилга бүтээцийн багийн ажлын байр",                    0.82703, 0.51894, 2.8, 4.5, "Workspace of the structural engineering team"),
					array("meeting-room",           "room",     "Meeting Room",           "Хурлын өрөө",                     "Багийн хурал, уулзалт хийх өрөө",                        0.81012, 0.33556, 2.8, 4.5, "Room for team meetings and discussions"),
					array("terrace-west",           "terrace",  "Terrace",                "Террас",                          "Гадаа гарч амрах нээлттэй террас",                       0.91876, 0.57216, 2.8, 4.5, "Open terrace for a breath of fresh air")
				)
			),
			array(
				"key" => "office-21f", "title" => "21-Р ДАВХАР", "sortOrder" => 21, "w" => 3296, "h" => 2584,
				"bounds" => array(0.03701, 0.04721, 0.96299, 0.95279), "link" => array("x" => 0.36320, "y" => 0.81839),
				"spots" => array(
					array("kitchen",            "room",    "Kitchen",           "Гал тогоо",                 "Хоол, цай бэлтгэх болон хамт хооллох гал тогоо", 0.15774, 0.56893, 2.4, 2.8, "Kitchen for cooking, tea and shared meals"),
					array("walk-in-closet",     "room",    "Walk-in Closet",    "Хувцасны өрөө",             "Хувцас, эд хогшил хадгалах өрөө",                0.54126, 0.34958, 2.4, 2.8, "Storage for clothes and belongings"),
					array("ceo-office",         "room",    "CEO Office",        "Захирлын өрөө",             "Захирлын ажлын өрөө",                            0.63741, 0.36044, 2.4, 2.8, "Office of the CEO"),
					array("terrace-east",       "terrace", "Terrace",           "Террас",                    "Гадаа гарч амрах нээлттэй террас",               0.70106, 0.24904, 2.4, 2.8, "Open terrace for a breath of fresh air"),
					array("architecture-team",  "team",    "Architecture Team", "Барилга архитектурын алба", "Барилга архитектурын багийн ажлын байр",         0.63875, 0.58621, 2.2, 2.6, "Workspace of the architecture team")
				)
			)
		);

		$floors = array();

		foreach ($floorDefs as $fd) {
			$spots = array();
			$i = 0;
			foreach ($fd["spots"] as $s) {
				$i++;
				$spots[] = array(
					"slug"          => $s[0],
					"order"         => $i,
					"enabled"       => true,
					"kind"          => $s[1],
					"titleEn"       => $s[2],
					"titleMn"       => $s[3],
					"descriptionMn" => $s[4],
					"descriptionEn" => $s[9],
					"x"             => $s[5],
					"y"             => $s[6],
					"desktop"       => array("zoom" => $s[7], "focusX" => null, "focusY" => null, "offsetX" => 0, "offsetY" => 0),
					"mobile"        => array("zoom" => $s[8], "focusX" => null, "focusY" => null, "offsetX" => 0, "offsetY" => 0)
				);
			}

			$tag = substr($fd["key"], 7); /* "20f" */

			$floors[] = array(
				"plan" => array(
					"key"          => $fd["key"],
					"sortOrder"    => $fd["sortOrder"],
					"floorTitle"   => $fd["title"],
					"imageUrl"     => $base . "office-" . $tag . "-small.webp",
					"imageMidUrl"  => $base . "office-" . $tag . "-mid.webp",
					"imageFullUrl" => $base . "office-" . $tag . "-full.webp",
					"imageWidth"   => $fd["w"],
					"imageHeight"  => $fd["h"],
					"bounds"       => $fd["bounds"],
					"link"         => $fd["link"],
					"enabled"      => true
				),
				"hotspots" => $spots
			);
		}

		return $floors;
	}
}
