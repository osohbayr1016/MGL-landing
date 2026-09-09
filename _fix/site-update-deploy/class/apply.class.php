<?php
/**
 * Дадлага / ажлын байрны өргөдлийн цөм.
 *
 * Вэб сайт (pages/apply) болон CP Admin (cpadmin/pages/internship) хоёулаа
 * ЯГ ЭНЭ файлыг ашиглана — хүснэгтийн бүтэц, талбарын каталог хэзээ ч зөрөхгүй.
 *
 * Хүснэгтүүд нь эхний хандалт дээр автоматаар үүснэ (ensure), тул const.php-г
 * серверт гараар засах шаардлагагүй.
 */

class ApplyCore
{
	/** Хавсралт хадгалах хавтас (сайтын үндсэн хавтаснаас) */
	const FILE_FOLDER = "cpadmin/postpic/apply";

	/* ------------------------------------------------------------------
	   Хүснэгтүүд
	   ------------------------------------------------------------------ */

	public static function tables()
	{
		global $db_apply_setting, $db_apply_field, $db_apply_entry, $tbl_pref;

		$pref = isset($tbl_pref) && $tbl_pref != "" ? $tbl_pref : "db_";

		return array(
			"setting" => isset($db_apply_setting) && $db_apply_setting != "" ? $db_apply_setting : $pref . "apply_setting",
			"field"   => isset($db_apply_field)   && $db_apply_field   != "" ? $db_apply_field   : $pref . "apply_field",
			"entry"   => isset($db_apply_entry)   && $db_apply_entry   != "" ? $db_apply_entry   : $pref . "apply_entry"
		);
	}

	public static function install($db, $seed = true)
	{
		$t = self::tables();

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["setting"] . "` (
			`setKey` varchar(64) NOT NULL DEFAULT '',
			`setVal` longtext,
			PRIMARY KEY (`setKey`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["field"] . "` (
			`fieldID` int(11) NOT NULL AUTO_INCREMENT,
			`fieldKey` varchar(64) NOT NULL DEFAULT '',
			`fieldLabel` varchar(255) NOT NULL DEFAULT '',
			`fieldType` varchar(32) NOT NULL DEFAULT 'text',
			`fieldPlaceholder` varchar(255) NOT NULL DEFAULT '',
			`fieldHelp` varchar(255) NOT NULL DEFAULT '',
			`fieldOptions` text,
			`fieldRequired` tinyint(1) NOT NULL DEFAULT '0',
			`fieldWidth` varchar(16) NOT NULL DEFAULT 'full',
			`fieldCore` varchar(16) NOT NULL DEFAULT '',
			`fieldFor` varchar(16) NOT NULL DEFAULT 'both',
			`fieldStatus` tinyint(1) NOT NULL DEFAULT '1',
			`fieldOrder` int(11) NOT NULL DEFAULT '1',
			PRIMARY KEY (`fieldID`),
			UNIQUE KEY `fieldKey` (`fieldKey`),
			KEY `fieldOrder` (`fieldOrder`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["entry"] . "` (
			`entryID` int(11) NOT NULL AUTO_INCREMENT,
			`entryType` varchar(16) NOT NULL DEFAULT 'intern',
			`entryName` varchar(255) NOT NULL DEFAULT '',
			`entryPhone` varchar(64) NOT NULL DEFAULT '',
			`entryEmail` varchar(255) NOT NULL DEFAULT '',
			`entryPosition` varchar(255) NOT NULL DEFAULT '',
			`entryData` longtext,
			`entryFiles` longtext,
			`entryIP` varchar(64) NOT NULL DEFAULT '',
			`entryDate` datetime DEFAULT NULL,
			`entryState` varchar(16) NOT NULL DEFAULT 'new',
			`entryNote` text,
			`entryStatus` tinyint(1) NOT NULL DEFAULT '1',
			PRIMARY KEY (`entryID`),
			KEY `entryType` (`entryType`),
			KEY `entryState` (`entryState`),
			KEY `entryDate` (`entryDate`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		if ($seed) {
			self::seed($db);
		}
	}

	/** Анх нээхэд ажиллах өгөгдмөл талбарууд. Мөр байвал юу ч хийхгүй. */
	public static function seed($db)
	{
		$t = self::tables();

		$count = (int)self::scalar($db, "SELECT COUNT(*) FROM `" . $t["field"] . "`", null);
		if ($count > 0) {
			return;
		}

		$order = 1;
		foreach (self::defaultFields() as $row) {
			$row["fieldOrder"] = $order++;
			$db->insert($t["field"], $row);
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
		if (!empty($GLOBALS["applyModuleReady"])) {
			return false;
		}

		$t = self::tables();
		$made = false;

		if (!self::tableExists($db, $t["entry"])) {
			self::install($db, true);
			$made = true;
		}

		$GLOBALS["applyModuleReady"] = true;

		return $made;
	}

	/* ------------------------------------------------------------------
	   Тохиргоо
	   ------------------------------------------------------------------ */

	public static function defaultSettings()
	{
		return array(
			/* Дадлагын хуудас */
			"internTitle"   => "Дадлага хийх хүсэлт",
			"internText"    => "Хавар, намрын улиралд 1-3 сарын хугацаанд дадлагын ажилтан сонгон авдаг. "
				. "Анкет, портфолиогоо доорх маягтаар илгээнэ үү.",

			/* Ажлын байрны хуудас */
			"jobTitle"      => "Ажлын байрны өргөдөл",
			"jobText"       => "Хүссэн ажлын байраа сонгож, танилцуулга болон портфолиогоо хавсаргана уу.",

			/* Ерөнхий */
			"submitLabel"   => "Хүсэлт илгээх",
			"successTitle"  => "Хүсэлтийг хүлээн авлаа",
			"successText"   => "Таны хүсэлт бүртгэгдлээ. Хүсэлтийг 2 долоо хоног тутам хянаж, "
				. "шалгаруулалтын дүнг и-мэйлээр мэдэгдэнэ.",
			"closedTitle"   => "Хүсэлт хүлээн авахыг түр зогсоолоо",
			"closedText"    => "Одоогоор шинэ хүсэлт хүлээн авахгүй байна. Дараа дахин оролдоно уу.",
			"errorText"     => "Мэдээллээ шалгаад дахин оролдоно уу.",
			"requiredText"  => "Заавал бөглөнө үү.",
			"dupText"       => "Энэ и-мэйл хаягаар сүүлийн 30 хоногт хүсэлт илгээсэн байна.",

			/* Хяналт */
			"internOpen"    => "1",
			"jobOpen"       => "1",
			"dupCheck"      => "0",

			/* Хавсралт */
			"fileMaxMb"     => "10",
			"fileTypes"     => "pdf,jpg,jpeg,png,doc,docx",

			/* Формыг автоматаар нэмэх хуудсууд (/page/N).
			   Таслалаар тусгаарлана. Хоосон бол хаана ч нэмэхгүй. */
			"embedIntern"   => "7",
			"embedJob"      => "6"
		);
	}

	public static function settings($db)
	{
		$t   = self::tables();
		$out = self::defaultSettings();

		$rows = $db->rawQuery("SELECT `setKey`,`setVal` FROM `" . $t["setting"] . "`", null);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$out[$row["setKey"]] = $row["setVal"];
			}
		}

		return $out;
	}

	public static function saveSettings($db, $values)
	{
		$t       = self::tables();
		$allowed = self::defaultSettings();

		foreach ($values as $key => $val) {
			if (!array_key_exists($key, $allowed)) {
				continue;
			}

			$db->rawQuery(
				"REPLACE INTO `" . $t["setting"] . "` (`setKey`,`setVal`) VALUES (?, ?)",
				array($key, (string)$val)
			);
		}
	}

	/* ------------------------------------------------------------------
	   Хүсэлтийн төрөл ба төлөв
	   ------------------------------------------------------------------ */

	public static function types()
	{
		return array(
			"intern" => "Дадлага",
			"job"    => "Ажлын байр"
		);
	}

	public static function typeName($type)
	{
		$all = self::types();

		return isset($all[$type]) ? $all[$type] : $type;
	}

	/** Хүсэлтийн төрлийг зөвтгөнө (үл мэдэгдэх бол дадлага) */
	public static function typeKey($type)
	{
		$all  = self::types();
		$type = (string)$type;

		return isset($all[$type]) ? $type : "intern";
	}

	public static function states()
	{
		return array(
			"new"       => "Шинэ",
			"read"      => "Үзсэн",
			"interview" => "Ярилцлага",
			"accepted"  => "Зөвшөөрсөн",
			"rejected"  => "Татгалзсан"
		);
	}

	/** Төлөв бүрийн өнгө (CP Admin дээрх шошго) */
	public static function stateClass($state)
	{
		$map = array(
			"new"       => "label-primary",
			"read"      => "label-info",
			"interview" => "label-warning",
			"accepted"  => "label-success",
			"rejected"  => "label-danger"
		);

		return isset($map[$state]) ? $map[$state] : "label-default";
	}

	public static function stateName($state)
	{
		$all = self::states();

		return isset($all[$state]) ? $all[$state] : $state;
	}

	/**
	 * Тухайн төрлөөр хүсэлт хүлээн авах эсэх.
	 * Буцаах: array("open"=>bool, "title"=>.., "text"=>..)
	 */
	public static function status($set, $type)
	{
		$key = self::typeKey($type) == "job" ? "jobOpen" : "internOpen";

		if ((string)$set[$key] === "1") {
			return array("open" => true, "title" => "", "text" => "");
		}

		return array(
			"open"  => false,
			"title" => $set["closedTitle"],
			"text"  => $set["closedText"]
		);
	}

	/** Хуудасны гарчиг / тайлбар */
	public static function pageTitle($set, $type)
	{
		return self::typeKey($type) == "job" ? $set["jobTitle"] : $set["internTitle"];
	}

	public static function pageText($set, $type)
	{
		return self::typeKey($type) == "job" ? $set["jobText"] : $set["internText"];
	}

	/**
	 * "Формыг автоматаар нэмэх" хуудсууд.
	 * Буцаах: array(pageID => "intern"|"job")
	 */
	public static function embedPages($set)
	{
		$out = array();

		$map = array("intern" => "embedIntern", "job" => "embedJob");

		foreach ($map as $type => $setKey) {
			$raw = isset($set[$setKey]) ? (string)$set[$setKey] : "";

			foreach (preg_split('/[^0-9]+/', $raw) as $one) {
				$one = (int)$one;
				if ($one > 0 && !isset($out[$one])) {
					$out[$one] = $type;
				}
			}
		}

		return $out;
	}

	/* ------------------------------------------------------------------
	   Формын талбарууд
	   ------------------------------------------------------------------ */

	public static function fieldTypes()
	{
		return array(
			"text"     => "Текст",
			"tel"      => "Утасны дугаар",
			"email"    => "И-мэйл хаяг",
			"number"   => "Тоо",
			"date"     => "Огноо",
			"textarea" => "Урт текст",
			"select"   => "Сонголт (dropdown)",
			"radio"    => "Сонголт (radio)",
			"checkbox" => "Олон сонголт (checkbox)",
			"consent"  => "Зөвшөөрөл (нэг checkbox)",
			"file"     => "Файл хавсаргах"
		);
	}

	public static function fieldHasOptions($type)
	{
		return in_array($type, array("select", "radio", "checkbox"));
	}

	/** Талбар аль хуудсанд гарах */
	public static function fieldForTypes()
	{
		return array(
			"both"   => "Хоёуланд нь",
			"intern" => "Зөвхөн дадлага",
			"job"    => "Зөвхөн ажлын байр"
		);
	}

	public static function defaultFields()
	{
		return array(
			array(
				"fieldKey" => "name", "fieldLabel" => "Овог нэр", "fieldType" => "text",
				"fieldPlaceholder" => "Овог нэр", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "full", "fieldCore" => "name",
				"fieldFor" => "both", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "phone", "fieldLabel" => "Утасны дугаар", "fieldType" => "tel",
				"fieldPlaceholder" => "99112233", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "half", "fieldCore" => "phone",
				"fieldFor" => "both", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "email", "fieldLabel" => "И-мэйл хаяг", "fieldType" => "email",
				"fieldPlaceholder" => "name@example.com", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "half", "fieldCore" => "email",
				"fieldFor" => "both", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "position", "fieldLabel" => "Хүсэж буй ажлын байр", "fieldType" => "text",
				"fieldPlaceholder" => "ж: Junior Architect", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "full", "fieldCore" => "position",
				"fieldFor" => "job", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "school", "fieldLabel" => "Сургууль", "fieldType" => "text",
				"fieldPlaceholder" => "", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "half", "fieldCore" => "",
				"fieldFor" => "intern", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "major", "fieldLabel" => "Мэргэжил", "fieldType" => "text",
				"fieldPlaceholder" => "", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "half", "fieldCore" => "",
				"fieldFor" => "intern", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "course", "fieldLabel" => "Курс", "fieldType" => "select",
				"fieldPlaceholder" => "", "fieldHelp" => "", "fieldOptions" => "1-р курс\n2-р курс\n3-р курс\n4-р курс\nМагистр\nТөгссөн",
				"fieldRequired" => 0, "fieldWidth" => "half", "fieldCore" => "",
				"fieldFor" => "intern", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "period", "fieldLabel" => "Дадлага хийх хугацаа", "fieldType" => "text",
				"fieldPlaceholder" => "ж: 2026 оны 2-4 сар", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 0, "fieldWidth" => "half", "fieldCore" => "",
				"fieldFor" => "intern", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "portfolio", "fieldLabel" => "Портфолио / CV", "fieldType" => "file",
				"fieldPlaceholder" => "", "fieldHelp" => "PDF хэлбэрээр, 10 MB-аас ихгүй.", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "full", "fieldCore" => "",
				"fieldFor" => "both", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "link", "fieldLabel" => "Цахим холбоос (Behance, вэб хуудас)", "fieldType" => "text",
				"fieldPlaceholder" => "https://", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 0, "fieldWidth" => "full", "fieldCore" => "",
				"fieldFor" => "both", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "about", "fieldLabel" => "Өөрийнхөө тухай товч", "fieldType" => "textarea",
				"fieldPlaceholder" => "", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 0, "fieldWidth" => "full", "fieldCore" => "",
				"fieldFor" => "both", "fieldStatus" => 1
			)
		);
	}

	/**
	 * Талбаруудыг унших.
	 *
	 * $type != "" бол зөвхөн тухайн хуудсанд харагдах талбарууд ("both" + өөрийнх).
	 */
	public static function fields($db, $type = "", $onlyActive = true)
	{
		$t = self::tables();

		$sql   = "SELECT * FROM `" . $t["field"] . "`";
		$where = array();
		$params = array();

		if ($onlyActive) {
			$where[] = "`fieldStatus`=1";
		}

		if ($type != "") {
			$where[] = "(`fieldFor`='both' OR `fieldFor`=?)";
			$params[] = self::typeKey($type);
		}

		if (count($where) > 0) {
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		$sql .= " ORDER BY `fieldOrder` ASC, `fieldID` ASC";

		$rows = $db->rawQuery($sql, count($params) > 0 ? $params : null);

		return is_array($rows) ? $rows : array();
	}

	/** "a:Сонголт А|b:Сонголт Б" эсвэл мөр тус бүрээр -> array(value => label) */
	public static function parseOptions($raw)
	{
		$out = array();

		if (trim((string)$raw) == "") {
			return $out;
		}

		$parts = preg_split('/[\r\n\|]+/', $raw);

		foreach ($parts as $part) {
			$part = trim($part);
			if ($part == "") {
				continue;
			}

			if (strpos($part, ":") !== false) {
				list($val, $label) = explode(":", $part, 2);
				$val   = trim($val);
				$label = trim($label);
			} else {
				$val   = $part;
				$label = $part;
			}

			if ($val != "") {
				$out[$val] = $label;
			}
		}

		return $out;
	}

	/* ------------------------------------------------------------------
	   Туслахууд
	   ------------------------------------------------------------------ */

	/**
	 * Оролтын цэвэрлэгээ. HTML entity болгохгүй — Excel-д цэвэр текст
	 * очиж, дэлгэцэн дээр гаргахдаа esc()-ээр хамгаална.
	 */
	public static function clean($value, $max = 255)
	{
		if (is_array($value)) {
			$out = array();
			foreach ($value as $v) {
				$out[] = self::clean($v, $max);
			}

			return $out;
		}

		$value = (string)$value;
		$value = str_replace(array("\0", "\r"), "", $value);
		$value = strip_tags($value);
		$value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', "", $value);
		if ($value === null) {
			$value = "";
		}
		$value = trim($value);

		if ($max > 0) {
			$value = function_exists("mb_substr")
				? mb_substr($value, 0, $max, "UTF-8")
				: substr($value, 0, $max);
		}

		return $value;
	}

	public static function esc($value)
	{
		return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
	}

	public static function decode($json)
	{
		$out = json_decode((string)$json, true);

		return is_array($out) ? $out : array();
	}

	/**
	 * Нэг утга буцаана.
	 *
	 * MysqliDb::rawQueryValue нь query-ийн төгсгөлд "LIMIT 1" байхгүй бол
	 * МАССИВ буцаадаг тул шууд (int) хөрвүүлэхэд буруу үр дүн гардаг.
	 */
	public static function scalar($db, $sql, $params = null)
	{
		$val = $db->rawQueryValue($sql . " LIMIT 1", $params);

		if (is_array($val)) {
			$val = count($val) > 0 ? reset($val) : null;
		}

		return $val;
	}

	public static function nextOrder($db, $table, $orderCol)
	{
		$max = (int)self::scalar($db, "SELECT MAX(`" . $orderCol . "`) FROM `" . $table . "`", null);

		return $max + 1;
	}

	public static function clientIp()
	{
		$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";

		return substr((string)$ip, 0, 60);
	}

	public static function entryCount($db, $type = "", $state = "")
	{
		$t = self::tables();

		$sql    = "SELECT COUNT(*) FROM `" . $t["entry"] . "` WHERE `entryStatus`=1";
		$params = array();

		if ($type != "") {
			$sql .= " AND `entryType`=?";
			$params[] = self::typeKey($type);
		}

		if ($state != "") {
			$sql .= " AND `entryState`=?";
			$params[] = $state;
		}

		return (int)self::scalar($db, $sql, count($params) > 0 ? $params : null);
	}

	/* ------------------------------------------------------------------
	   Хавсралт
	   ------------------------------------------------------------------ */

	/** Сайтын үндсэн хавтас (энэ файл class/ дотор байдаг) */
	public static function sitePath($rel = "")
	{
		$base = dirname(__DIR__);

		return $rel == "" ? $base : $base . "/" . ltrim($rel, "/");
	}

	/**
	 * Хавсралтын хавтас. Байхгүй бол үүсгээд, гаднаас татаж авахыг
	 * хориглосон .htaccess-ийг нь бичнэ (файлыг зөвхөн админ PHP-ээр уншина).
	 */
	public static function uploadDir()
	{
		$dir = self::sitePath(self::FILE_FOLDER);

		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}

		$guard = $dir . "/.htaccess";

		if (is_dir($dir) && !is_file($guard)) {
			@file_put_contents($guard,
				"# Хавсралтыг шууд татахыг хориглоно — зөвхөн CP Admin-аар дамжина.\n"
				. "<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n"
				. "<IfModule !mod_authz_core.c>\n\tOrder allow,deny\n\tDeny from all\n</IfModule>\n"
			);
		}

		return $dir;
	}

	/** Зөвшөөрөх өргөтгөлүүд */
	public static function allowedExt($set)
	{
		$out = array();

		foreach (preg_split('/[^a-zA-Z0-9]+/', (string)$set["fileTypes"]) as $one) {
			$one = strtolower(trim($one));
			if ($one != "") {
				$out[$one] = $one;
			}
		}

		if (count($out) < 1) {
			$out = array("pdf" => "pdf");
		}

		return $out;
	}

	/** Тохиргоо ба серверийн хязгаарын аль багыг нь буцаана (байт) */
	public static function maxFileBytes($set)
	{
		$mb  = (int)$set["fileMaxMb"];
		$own = $mb > 0 ? $mb * 1024 * 1024 : 0;
		$srv = self::uploadMaxBytes();

		if ($own < 1) {
			return $srv;
		}

		return $srv > 0 && $srv < $own ? $srv : $own;
	}

	public static function iniBytes($val)
	{
		$val = trim((string)$val);

		if ($val === "") {
			return 0;
		}

		$num  = (float)$val;
		$last = strtolower(substr($val, -1));

		if ($last == "g") {
			$num *= 1024 * 1024 * 1024;
		} elseif ($last == "m") {
			$num *= 1024 * 1024;
		} elseif ($last == "k") {
			$num *= 1024;
		}

		return (int)$num;
	}

	/** Энэ сервер дээр нэг удаад байршуулж болох дээд хэмжээ (байт) */
	public static function uploadMaxBytes()
	{
		$limits = array();

		$up = self::iniBytes(@ini_get("upload_max_filesize"));
		if ($up > 0) {
			$limits[] = $up;
		}

		/* POST-д формын бусад талбар ч багтдаг тул бага зэрэг зай үлдээнэ */
		$post = self::iniBytes(@ini_get("post_max_size"));
		if ($post > 0) {
			$limits[] = $post - 65536;
		}

		if (count($limits) < 1) {
			return 8 * 1024 * 1024;
		}

		$min = min($limits);

		return $min > 0 ? $min : 0;
	}

	public static function sizeText($bytes)
	{
		$bytes = (float)$bytes;

		if ($bytes >= 1073741824) {
			return round($bytes / 1073741824, 1) . " GB";
		}
		if ($bytes >= 1048576) {
			return round($bytes / 1048576) . " MB";
		}
		if ($bytes >= 1024) {
			return round($bytes / 1024) . " KB";
		}

		return (int)$bytes . " B";
	}

	public static function uploadErrorText($code)
	{
		switch ((int)$code) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return "Файл хэт том байна.";
			case UPLOAD_ERR_PARTIAL:
				return "Файл бүтэн хуулагдсангүй. Дахин оролдоно уу.";
			case UPLOAD_ERR_NO_TMP_DIR:
			case UPLOAD_ERR_CANT_WRITE:
			case UPLOAD_ERR_EXTENSION:
				return "Сервер дээр файл хадгалж чадсангүй.";
		}

		return "Файл хуулахад алдаа гарлаа.";
	}

	/**
	 * Нэг хавсралтыг хадгална.
	 * Буцаах: array("ok"=>bool, "error"=>"", "file"=>array(stored,name,size))
	 */
	public static function storeFile($file, $set)
	{
		$fail = function ($msg) {
			return array("ok" => false, "error" => $msg, "file" => null);
		};

		if (!is_array($file) || !isset($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])) {
			return $fail("Файл хуулагдсангүй.");
		}

		$max = self::maxFileBytes($set);
		if ($max > 0 && (int)$file["size"] > $max) {
			return $fail("Файл хэт том байна. Дээд тал нь " . self::sizeText($max) . ".");
		}

		$name = self::clean(isset($file["name"]) ? $file["name"] : "", 190);
		$ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

		$allowed = self::allowedExt($set);
		if ($ext == "" || !isset($allowed[$ext])) {
			return $fail("Зөвшөөрөгдөх төрөл: " . implode(", ", array_keys($allowed)) . ".");
		}

		$dir = self::uploadDir();
		if (!is_dir($dir) || !is_writable($dir)) {
			return $fail("Сервер дээр файл хадгалж чадсангүй.");
		}

		$rand = function_exists("random_bytes")
			? bin2hex(random_bytes(10))
			: md5(uniqid("a", true));

		$stored = date("Ymd") . "-" . $rand . "." . $ext;

		if (!@move_uploaded_file($file["tmp_name"], $dir . "/" . $stored)) {
			return $fail("Сервер дээр файл хадгалж чадсангүй.");
		}

		@chmod($dir . "/" . $stored, 0644);

		return array(
			"ok"    => true,
			"error" => "",
			"file"  => array(
				"stored" => $stored,
				"name"   => $name != "" ? $name : $stored,
				"size"   => (int)$file["size"]
			)
		);
	}

	/** Хадгалсан файлын бүтэн зам (нэр нь эвдэрсэн бол хоосон) */
	public static function filePath($stored)
	{
		$stored = (string)$stored;

		if ($stored == "" || !preg_match('/^[0-9A-Za-z._-]+$/', $stored) || strpos($stored, "..") !== false) {
			return "";
		}

		$path = self::sitePath(self::FILE_FOLDER) . "/" . $stored;

		return is_file($path) ? $path : "";
	}

	/** Хүсэлтэд хавсаргасан файлууд */
	public static function files($entry)
	{
		$out = array();

		foreach (self::decode(isset($entry["entryFiles"]) ? $entry["entryFiles"] : "") as $one) {
			if (is_array($one) && !empty($one["stored"])) {
				$out[] = $one;
			}
		}

		return $out;
	}

	/* ------------------------------------------------------------------
	   Илгээсэн формыг шалгах / хадгалах
	   ------------------------------------------------------------------ */

	/**
	 * Буцаах: array("ok"=>bool, "errors"=>array(key=>msg), "core"=>array(),
	 *               "extra"=>array(), "files"=>array(), "values"=>array())
	 */
	public static function validate($db, $fieldArr, $post, $files, $set)
	{
		$errors = array();
		$values = array();
		$core   = array("name" => "", "phone" => "", "email" => "", "position" => "");
		$extra  = array();
		$saved  = array();

		$required = $set["requiredText"] != "" ? $set["requiredText"] : "Заавал бөглөнө үү.";

		foreach ($fieldArr as $field) {
			$key  = $field["fieldKey"];
			$type = $field["fieldType"];
			$raw  = isset($post[$key]) ? $post[$key] : "";

			/* ---- Хавсралт ---- */
			if ($type == "file") {
				$one = isset($files[$key]) ? $files[$key] : null;
				$has = is_array($one) && isset($one["error"]) && (int)$one["error"] !== UPLOAD_ERR_NO_FILE;

				if (!$has) {
					if ((int)$field["fieldRequired"] == 1) {
						$errors[$key] = $required;
					}
					continue;
				}

				if ((int)$one["error"] !== UPLOAD_ERR_OK) {
					$errors[$key] = self::uploadErrorText($one["error"]);
					continue;
				}

				$res = self::storeFile($one, $set);

				if (!$res["ok"]) {
					$errors[$key] = $res["error"];
					continue;
				}

				$res["file"]["key"]   = $key;
				$res["file"]["label"] = $field["fieldLabel"];
				$saved[] = $res["file"];
				continue;
			}

			/* ---- Олон сонголт ---- */
			if ($type == "checkbox") {
				$raw     = is_array($raw) ? $raw : ($raw === "" ? array() : array($raw));
				$allowed = self::parseOptions($field["fieldOptions"]);
				$picked  = array();

				foreach ($raw as $one) {
					$one = self::clean($one, 190);
					if ($one !== "" && (count($allowed) < 1 || isset($allowed[$one]))) {
						$picked[] = isset($allowed[$one]) ? $allowed[$one] : $one;
					}
				}

				$values[$key] = $picked;

				if ((int)$field["fieldRequired"] == 1 && count($picked) < 1) {
					$errors[$key] = $required;
				}

				$extra[$key] = $picked;
				continue;
			}

			/* ---- Зөвшөөрөл ---- */
			if ($type == "consent") {
				$checked      = ($raw !== "" && $raw !== "0");
				$values[$key] = $checked ? "y" : "";

				if ((int)$field["fieldRequired"] == 1 && !$checked) {
					$errors[$key] = $required;
				}

				$extra[$key] = $checked ? "Тийм" : "Үгүй";
				continue;
			}

			$max = ($type == "textarea") ? 2000 : 190;
			$val = self::clean($raw, $max);
			$values[$key] = $val;

			if ($val === "") {
				if ((int)$field["fieldRequired"] == 1) {
					$errors[$key] = $required;
				}
			} else {
				if (self::fieldHasOptions($type)) {
					$allowed = self::parseOptions($field["fieldOptions"]);
					if (count($allowed) > 0 && !isset($allowed[$val])) {
						$errors[$key] = "Сонголт буруу байна.";
					} else {
						$val = isset($allowed[$val]) ? $allowed[$val] : $val;
					}
				}

				if ($type == "email" && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
					$errors[$key] = "И-мэйл хаяг буруу байна.";
				}

				if ($type == "tel") {
					$digits = preg_replace('/[^0-9]/', "", $val);
					if (strlen($digits) < 6) {
						$errors[$key] = "Утасны дугаар буруу байна.";
					}
				}

				if ($type == "number" && !is_numeric(str_replace(" ", "", $val))) {
					$errors[$key] = "Тоо оруулна уу.";
				}
			}

			if ($field["fieldCore"] != "" && isset($core[$field["fieldCore"]])) {
				$core[$field["fieldCore"]] = $val;
			} else {
				$extra[$key] = $val;
			}
		}

		/* Давхардал — Тохиргоо дээр асаасан үед (сүүлийн 30 хоног) */
		if (count($errors) < 1 && (string)$set["dupCheck"] === "1" && $core["email"] != "") {
			$t = self::tables();

			$dup = (int)self::scalar($db,
				"SELECT COUNT(*) FROM `" . $t["entry"] . "` WHERE `entryStatus`=1 AND `entryEmail`=?"
					. " AND `entryDate`>DATE_SUB(NOW(), INTERVAL 30 DAY)",
				array($core["email"])
			);

			if ($dup > 0) {
				$errors["_form"] = $set["dupText"] != "" ? $set["dupText"] : "Аль хэдийн хүсэлт илгээсэн байна.";
			}
		}

		/* Шалгалт унасан бол хадгалсан файлуудыг үлдээхгүй */
		if (count($errors) > 0 && count($saved) > 0) {
			foreach ($saved as $one) {
				$path = self::filePath($one["stored"]);
				if ($path != "") {
					@unlink($path);
				}
			}
			$saved = array();
		}

		return array(
			"ok"     => count($errors) < 1,
			"errors" => $errors,
			"core"   => $core,
			"extra"  => $extra,
			"files"  => $saved,
			"values" => $values
		);
	}

	/** Хүсэлтийг хадгална, entryID буцаана */
	public static function saveEntry($db, $type, $core, $extra, $files)
	{
		$t = self::tables();

		return $db->insert($t["entry"], array(
			"entryType"     => self::typeKey($type),
			"entryName"     => $core["name"],
			"entryPhone"    => $core["phone"],
			"entryEmail"    => $core["email"],
			"entryPosition" => isset($core["position"]) ? $core["position"] : "",
			"entryData"     => json_encode($extra, JSON_UNESCAPED_UNICODE),
			"entryFiles"    => json_encode($files, JSON_UNESCAPED_UNICODE),
			"entryIP"       => self::clientIp(),
			"entryDate"     => date("Y-m-d H:i:s"),
			"entryState"    => "new",
			"entryNote"     => "",
			"entryStatus"   => 1
		));
	}

	/** Хүсэлт болон түүний хавсралтуудыг устгана */
	public static function deleteEntry($db, $entryID)
	{
		$t       = self::tables();
		$entryID = (int)$entryID;

		$row = $db->rawQueryOne("SELECT * FROM `" . $t["entry"] . "` WHERE `entryID`=?", array($entryID));

		if (!is_array($row) || count($row) < 1) {
			return false;
		}

		foreach (self::files($row) as $one) {
			$path = self::filePath($one["stored"]);
			if ($path != "") {
				@unlink($path);
			}
		}

		$db->rawQuery("DELETE FROM `" . $t["entry"] . "` WHERE `entryID`=?", array($entryID));

		return true;
	}

	/* ------------------------------------------------------------------
	   Excel / CSV
	   ------------------------------------------------------------------ */

	public static function exportColumns($fieldArr)
	{
		$cols = array("№", "Төрөл");

		foreach ($fieldArr as $field) {
			$cols[] = $field["fieldLabel"] != "" ? $field["fieldLabel"] : $field["fieldKey"];
		}

		$cols[] = "Төлөв";
		$cols[] = "Тэмдэглэл";
		$cols[] = "Илгээсэн огноо";

		return $cols;
	}

	public static function exportRow($entry, $fieldArr, $index)
	{
		$extra = self::decode($entry["entryData"]);
		$files = self::files($entry);

		$row = array($index, self::typeName($entry["entryType"]));

		foreach ($fieldArr as $field) {
			switch ($field["fieldCore"]) {
				case "name":
					$row[] = $entry["entryName"];
					break;
				case "phone":
					$row[] = $entry["entryPhone"];
					break;
				case "email":
					$row[] = $entry["entryEmail"];
					break;
				case "position":
					$row[] = $entry["entryPosition"];
					break;
				default:
					if ($field["fieldType"] == "file") {
						$names = array();
						foreach ($files as $one) {
							if (isset($one["key"]) && $one["key"] == $field["fieldKey"]) {
								$names[] = $one["name"];
							}
						}
						$row[] = implode(", ", $names);
						break;
					}

					$val = isset($extra[$field["fieldKey"]]) ? $extra[$field["fieldKey"]] : "";
					$row[] = is_array($val) ? implode(", ", $val) : $val;
					break;
			}
		}

		$row[] = self::stateName($entry["entryState"]);
		$row[] = (string)$entry["entryNote"];
		$row[] = $entry["entryDate"];

		return $row;
	}
}
