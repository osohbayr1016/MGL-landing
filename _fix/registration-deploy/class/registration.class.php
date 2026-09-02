<?php
/**
 * Арга хэмжээний бүртгэлийн модулийн цөм.
 *
 * Вэб сайт (pages/registration/) болон CP Admin (cpadmin/pages/registration/)
 * хоёулаа ЭНЭ нэг файлыг ашиглана. Хүснэгтийн бүтэц, өгөгдмөл утга,
 * блок/талбарын каталогийг нэг газар тодорхойлсноор хоёр тал хэзээ ч зөрөхгүй.
 *
 * Блокийн каталог (blockTypes) нь CP Admin дээрх формыг бүрэн үүсгэдэг —
 * шинэ блок нэмэхийн тулд энд тодорхойлолт нэмээд, pages/registration/blocks/
 * дотор ижил нэртэй render файл үүсгэхэд л хангалттай.
 */

class RegistrationCore
{
	/* ------------------------------------------------------------------
	   Хүснэгтийн нэрс
	   ------------------------------------------------------------------ */

	public static function tables()
	{
		global $db_reg_setting, $db_reg_field, $db_reg_block, $db_reg_entry, $tbl_pref;

		$pref = isset($tbl_pref) && $tbl_pref != "" ? $tbl_pref : "db_";

		return array(
			"setting" => isset($db_reg_setting) && $db_reg_setting != "" ? $db_reg_setting : $pref . "reg_setting",
			"field"   => isset($db_reg_field)   && $db_reg_field   != "" ? $db_reg_field   : $pref . "reg_field",
			"block"   => isset($db_reg_block)   && $db_reg_block   != "" ? $db_reg_block   : $pref . "reg_block",
			"entry"   => isset($db_reg_entry)   && $db_reg_entry   != "" ? $db_reg_entry   : $pref . "reg_entry"
		);
	}

	/* ------------------------------------------------------------------
	   Хүснэгт үүсгэх / өгөгдмөл өгөгдөл суулгах
	   ------------------------------------------------------------------ */

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
			`fieldStatus` tinyint(1) NOT NULL DEFAULT '1',
			`fieldOrder` int(11) NOT NULL DEFAULT '1',
			PRIMARY KEY (`fieldID`),
			UNIQUE KEY `fieldKey` (`fieldKey`),
			KEY `fieldOrder` (`fieldOrder`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["block"] . "` (
			`blockID` int(11) NOT NULL AUTO_INCREMENT,
			`parentID` int(11) NOT NULL DEFAULT '0',
			`blockType` varchar(32) NOT NULL DEFAULT 'text',
			`blockData` longtext,
			`blockStatus` tinyint(1) NOT NULL DEFAULT '1',
			`blockOrder` int(11) NOT NULL DEFAULT '1',
			PRIMARY KEY (`blockID`),
			KEY `parentID` (`parentID`),
			KEY `blockOrder` (`blockOrder`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$db->rawQuery("CREATE TABLE IF NOT EXISTS `" . $t["entry"] . "` (
			`entryID` int(11) NOT NULL AUTO_INCREMENT,
			`entryName` varchar(255) NOT NULL DEFAULT '',
			`entryPhone` varchar(64) NOT NULL DEFAULT '',
			`entryEmail` varchar(255) NOT NULL DEFAULT '',
			`entryData` longtext,
			`entryIP` varchar(64) NOT NULL DEFAULT '',
			`entryDate` datetime DEFAULT NULL,
			`entryStatus` tinyint(1) NOT NULL DEFAULT '1',
			PRIMARY KEY (`entryID`),
			KEY `entryPhone` (`entryPhone`),
			KEY `entryEmail` (`entryEmail`),
			KEY `entryDate` (`entryDate`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		if ($seed) {
			self::seed($db);
		}
	}

	/**
	 * Анх удаа нээхэд ажиллах өгөгдмөл агуулга.
	 * Аль хэдийн мөр байвал юу ч хийхгүй — админы оруулсныг дарж бичихгүй.
	 */
	public static function seed($db)
	{
		$t = self::tables();

		$fieldCount = (int)RegistrationCore::scalar($db, "SELECT COUNT(*) FROM `" . $t["field"] . "`", null);
		if ($fieldCount < 1) {
			$order = 1;
			foreach (self::defaultFields() as $row) {
				$row["fieldOrder"] = $order++;
				$db->insert($t["field"], $row);
			}
		}

		$blockCount = (int)RegistrationCore::scalar($db, "SELECT COUNT(*) FROM `" . $t["block"] . "`", null);
		if ($blockCount < 1) {
			$order = 1;
			foreach (self::defaultBlocks() as $row) {
				$data = isset($row["data"]) ? $row["data"] : array();
				$blockID = $db->insert($t["block"], array(
					"parentID"    => 0,
					"blockType"   => $row["type"],
					"blockData"   => json_encode($data, JSON_UNESCAPED_UNICODE),
					"blockStatus" => 1,
					"blockOrder"  => $order++
				));

				if (!empty($row["sub"]) && $blockID) {
					$subOrder = 1;
					foreach ($row["sub"] as $sub) {
						$db->insert($t["block"], array(
							"parentID"    => (int)$blockID,
							"blockType"   => $row["type"],
							"blockData"   => json_encode($sub, JSON_UNESCAPED_UNICODE),
							"blockStatus" => 1,
							"blockOrder"  => $subOrder++
						));
					}
				}
			}
		}
	}

	/* ------------------------------------------------------------------
	   Тохиргоо
	   ------------------------------------------------------------------ */

	public static function defaultSettings()
	{
		return array(
			/* Арга хэмжээ */
			"eventTitle"     => "Шинэ оффисын нээлтийн өдөрлөг",
			"eventDate"      => "",
			"eventDateText"  => "",
			"eventLocation"  => "",

			/* Бүртгэлийн хяналт */
			"regOpen"        => "1",
			"regOpenFrom"    => "",
			"regOpenTo"      => "",
			"regLimit"       => "0",
			"regDupCheck"    => "0",

			/* Мессежүүд */
			"submitLabel"    => "Бүртгүүлэх",
			"successTitle"   => "Бүртгэл амжилттай",
			"successText"    => "Таны бүртгэлийг хүлээн авлаа. Уулзацгаая!",
			"closedTitle"    => "Бүртгэл хаагдсан",
			"closedText"     => "Бүртгэлийн хугацаа дууссан байна.",
			"fullTitle"      => "Бүртгэл дүүрсэн",
			"fullText"       => "Бүртгэлийн хүрээ дүүрсэн тул шинээр бүртгэл авах боломжгүй боллоо.",
			"errorText"      => "Мэдээллээ шалгаад дахин оролдоно уу.",
			"requiredText"   => "Заавал бөглөнө үү.",
			"dupText"        => "Энэ утас эсвэл и-мэйлээр аль хэдийн бүртгүүлсэн байна.",

			/* Вэб сайтын үндсэн хаяг — QR/линк үүсгэхэд ашиглана.
			   (cpadmin-ын $gloDomainLink нь админы домэйн тул тохирохгүй) */
			"siteBase"       => "https://mglenc.com",

			/* Meta — хайлтын системд гаргахгүй */
			"metaTitle"      => "Бүртгэл | MGL E&C",
			"metaDesc"       => "",
			"favicon"        => "",

			/* Загвар (theme) */
			"themeBg"          => "#0E0E0E",
			"themeSurface"     => "#171717",
			"themeText"        => "#FFFFFF",
			"themeMuted"       => "#A8A8A8",
			"themeBorder"      => "#2A2A2A",
			"themeAccent"      => "#FE5925",
			"themeAccentText"  => "#FFFFFF",
			"themeInputBg"     => "#FFFFFF",
			"themeInputText"   => "#111111",
			"themeMaxWidth"    => "1080",
			"themeRadius"      => "4",
			"themeTitleWeight" => "800",
			"themeBodyWeight"  => "500",
			"themeTitleSize"   => "56",
			"themeUppercase"   => "1",
			"themeLetterSpacing" => "0",

			/* Чөлөөт код */
			"customCss"      => "",
			"customHeadHtml" => "",
			"footerText"     => "© MGL E&C LLC"
		);
	}

	public static function settings($db)
	{
		$t = self::tables();
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
		$t = self::tables();
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
			"consent"  => "Зөвшөөрөл (нэг checkbox)"
		);
	}

	public static function fieldHasOptions($type)
	{
		return in_array($type, array("select", "radio", "checkbox"));
	}

	public static function defaultFields()
	{
		return array(
			array(
				"fieldKey" => "name", "fieldLabel" => "Нэр", "fieldType" => "text",
				"fieldPlaceholder" => "Овог нэр", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "full", "fieldCore" => "name", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "phone", "fieldLabel" => "Утасны дугаар", "fieldType" => "tel",
				"fieldPlaceholder" => "99112233", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "half", "fieldCore" => "phone", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "email", "fieldLabel" => "И-мэйл хаяг", "fieldType" => "email",
				"fieldPlaceholder" => "name@example.com", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 1, "fieldWidth" => "half", "fieldCore" => "email", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "company", "fieldLabel" => "Байгууллага", "fieldType" => "text",
				"fieldPlaceholder" => "", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 0, "fieldWidth" => "half", "fieldCore" => "", "fieldStatus" => 1
			),
			array(
				"fieldKey" => "position", "fieldLabel" => "Албан тушаал", "fieldType" => "text",
				"fieldPlaceholder" => "", "fieldHelp" => "", "fieldOptions" => "",
				"fieldRequired" => 0, "fieldWidth" => "half", "fieldCore" => "", "fieldStatus" => 1
			)
		);
	}

	public static function fields($db, $onlyActive = true)
	{
		$t = self::tables();

		$sql = "SELECT * FROM `" . $t["field"] . "`";
		if ($onlyActive) {
			$sql .= " WHERE `fieldStatus`=1";
		}
		$sql .= " ORDER BY `fieldOrder` ASC, `fieldID` ASC";

		$rows = $db->rawQuery($sql, null);

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
				$val = trim($val);
				$label = trim($label);
			} else {
				$val = $part;
				$label = $part;
			}

			if ($val != "") {
				$out[$val] = $label;
			}
		}

		return $out;
	}

	/* ------------------------------------------------------------------
	   Дизайны блокууд
	   ------------------------------------------------------------------ */

	/**
	 * Блокийн каталог. cols нь CP Admin дээрх формыг үүсгэнэ,
	 * subCols нь давтагдах дэд мөрийн формыг үүсгэнэ.
	 *
	 * Оролтын төрлүүд: text | textarea | editor | file | number | color
	 *                  select | bool | code
	 */
	public static function blockTypes()
	{
		return array(

			"hero" => array(
				"label" => "Hero — үндсэн баннер",
				"icon"  => "fa fa-star",
				"desc"  => "Дэвсгэр зураг/видео дээр гарчиг, огноо, бүртгүүлэх товч.",
				"cols"  => array(
					array("key" => "bgPic",     "name" => "Дэвсгэр зураг",               "type" => "file"),
					array("key" => "bgVideo",   "name" => "Дэвсгэр видео (mp4 линк)",    "type" => "file"),
					array("key" => "overlay",   "name" => "Дэвсгэрийн бараан % (0-100)", "type" => "number", "def" => "45"),
					array("key" => "logo",      "name" => "Лого зураг",                  "type" => "file"),
					array("key" => "logoWidth", "name" => "Логоны өргөн (px)",           "type" => "number", "def" => "160"),
					array("key" => "eyebrow",   "name" => "Дээд жижиг текст",            "type" => "text"),
					array("key" => "title",     "name" => "Гарчиг",                      "type" => "text"),
					array("key" => "subtitle",  "name" => "Дэд гарчиг",                  "type" => "textarea"),
					array("key" => "dateText",  "name" => "Огноо текст",                 "type" => "text"),
					array("key" => "locationText", "name" => "Байршил текст",            "type" => "text"),
					array("key" => "btnText",   "name" => "Товчны текст",                "type" => "text", "def" => "Бүртгүүлэх"),
					array("key" => "align",     "name" => "Хэвтээ байрлал",              "type" => "select", "opt" => "left:Зүүн|center:Төв|right:Баруун", "def" => "center"),
					array("key" => "valign",    "name" => "Босоо байрлал",               "type" => "select", "opt" => "center:Дунд|end:Доод|start:Дээд", "def" => "center"),
					array("key" => "height",    "name" => "Өндөр",                       "type" => "select", "opt" => "auto:Агуулгаараа|60:60vh|75:75vh|90:90vh|100:Дэлгэц дүүрэн", "def" => "90"),
					array("key" => "textColor", "name" => "Текстийн өнгө",               "type" => "color", "def" => "#FFFFFF")
				)
			),

			"info" => array(
				"label" => "Мэдээллийн хэсэг (огноо / цаг / хаяг)",
				"icon"  => "fa fa-info-circle",
				"desc"  => "Дэд мөр бүр нэг мэдээлэл — icon + гарчиг + утга.",
				"cols"  => array(
					array("key" => "title",     "name" => "Хэсгийн гарчиг", "type" => "text"),
					array("key" => "columns",   "name" => "Багана",         "type" => "select", "opt" => "2:2 багана|3:3 багана|4:4 багана", "def" => "3"),
					array("key" => "bg",        "name" => "Дэвсгэр өнгө",   "type" => "color"),
					array("key" => "padTop",    "name" => "Дээд зай (px)",  "type" => "number", "def" => "80"),
					array("key" => "padBottom", "name" => "Доод зай (px)",  "type" => "number", "def" => "80")
				),
				"subLabel" => "Мэдээллийн мөр",
				"subCols"  => array(
					array("key" => "icon",  "name" => "Icon (Font Awesome, ж: fa fa-calendar)", "type" => "text"),
					array("key" => "pic",   "name" => "Эсвэл зураг", "type" => "file"),
					array("key" => "label", "name" => "Гарчиг",      "type" => "text"),
					array("key" => "value", "name" => "Утга",        "type" => "textarea")
				)
			),

			"text" => array(
				"label" => "Текст блок",
				"icon"  => "fa fa-align-left",
				"desc"  => "Чөлөөт текст — editor-оор бичнэ.",
				"cols"  => array(
					array("key" => "title",     "name" => "Гарчиг",               "type" => "text"),
					array("key" => "body",      "name" => "Агуулга",              "type" => "editor"),
					array("key" => "align",     "name" => "Байрлал",              "type" => "select", "opt" => "left:Зүүн|center:Төв", "def" => "left"),
					array("key" => "maxWidth",  "name" => "Хамгийн их өргөн (px)", "type" => "number", "def" => "760"),
					array("key" => "bg",        "name" => "Дэвсгэр өнгө",         "type" => "color"),
					array("key" => "padTop",    "name" => "Дээд зай (px)",        "type" => "number", "def" => "80"),
					array("key" => "padBottom", "name" => "Доод зай (px)",        "type" => "number", "def" => "80")
				)
			),

			"image" => array(
				"label" => "Зураг",
				"icon"  => "fa fa-picture-o",
				"desc"  => "Нэг том зураг, тайлбартай.",
				"cols"  => array(
					array("key" => "pic",       "name" => "Зураг",         "type" => "file"),
					array("key" => "caption",   "name" => "Тайлбар",       "type" => "text"),
					array("key" => "width",     "name" => "Өргөн",         "type" => "select", "opt" => "wide:Дэлгэц дүүрэн|container:Голлон|narrow:Нарийн", "def" => "container"),
					array("key" => "bg",        "name" => "Дэвсгэр өнгө",  "type" => "color"),
					array("key" => "padTop",    "name" => "Дээд зай (px)", "type" => "number", "def" => "0"),
					array("key" => "padBottom", "name" => "Доод зай (px)", "type" => "number", "def" => "0")
				)
			),

			"gallery" => array(
				"label" => "Зургийн цомог",
				"icon"  => "fa fa-th",
				"desc"  => "Дэд мөр бүр нэг зураг.",
				"cols"  => array(
					array("key" => "title",     "name" => "Гарчиг",                   "type" => "text"),
					array("key" => "columns",   "name" => "Багана",                   "type" => "select", "opt" => "2:2|3:3|4:4", "def" => "3"),
					array("key" => "gap",       "name" => "Зураг хоорондын зай (px)", "type" => "number", "def" => "16"),
					array("key" => "bg",        "name" => "Дэвсгэр өнгө",             "type" => "color"),
					array("key" => "padTop",    "name" => "Дээд зай (px)",            "type" => "number", "def" => "80"),
					array("key" => "padBottom", "name" => "Доод зай (px)",            "type" => "number", "def" => "80")
				),
				"subLabel" => "Зураг",
				"subCols"  => array(
					array("key" => "pic",     "name" => "Зураг",   "type" => "file"),
					array("key" => "caption", "name" => "Тайлбар", "type" => "text")
				)
			),

			"countdown" => array(
				"label" => "Тоолуур (countdown)",
				"icon"  => "fa fa-clock-o",
				"desc"  => "Арга хэмжээ хүртэл үлдсэн хугацаа.",
				"cols"  => array(
					array("key" => "title",     "name" => "Гарчиг",        "type" => "text"),
					array("key" => "target",    "name" => "Огноо (ж: 2026-09-25 17:00)", "type" => "text", "help" => "Хоосон бол Тохиргоо дахь арга хэмжээний огноог ашиглана."),
					array("key" => "bg",        "name" => "Дэвсгэр өнгө",  "type" => "color"),
					array("key" => "padTop",    "name" => "Дээд зай (px)", "type" => "number", "def" => "60"),
					array("key" => "padBottom", "name" => "Доод зай (px)", "type" => "number", "def" => "60")
				)
			),

			"form" => array(
				"label" => "Бүртгэлийн форм",
				"icon"  => "fa fa-check-square-o",
				"desc"  => "Бүртгэлийн талбарууд энд гарна. Хуудсанд ЗААВАЛ нэг ширхэг байх ёстой.",
				"cols"  => array(
					array("key" => "title",     "name" => "Гарчиг",                 "type" => "text", "def" => "Бүртгүүлэх"),
					array("key" => "subtitle",  "name" => "Тайлбар",                "type" => "textarea"),
					array("key" => "maxWidth",  "name" => "Формын өргөн (px)",      "type" => "number", "def" => "620"),
					array("key" => "labelShow", "name" => "Талбарын нэр харуулах",  "type" => "bool", "def" => "y"),
					array("key" => "bg",        "name" => "Дэвсгэр өнгө",           "type" => "color"),
					array("key" => "padTop",    "name" => "Дээд зай (px)",          "type" => "number", "def" => "80"),
					array("key" => "padBottom", "name" => "Доод зай (px)",          "type" => "number", "def" => "100")
				)
			),

			"html" => array(
				"label" => "Чөлөөт HTML",
				"icon"  => "fa fa-code",
				"desc"  => "Дизайнер өөрийн HTML-ээ шууд бичнэ (газрын зураг, embed г.м).",
				"cols"  => array(
					array("key" => "body",      "name" => "HTML",          "type" => "code"),
					array("key" => "bg",        "name" => "Дэвсгэр өнгө",  "type" => "color"),
					array("key" => "padTop",    "name" => "Дээд зай (px)", "type" => "number", "def" => "0"),
					array("key" => "padBottom", "name" => "Доод зай (px)", "type" => "number", "def" => "0")
				)
			),

			"spacer" => array(
				"label" => "Хоосон зай",
				"icon"  => "fa fa-arrows-v",
				"desc"  => "Блокуудын хооронд зай / тусгаарлагч.",
				"cols"  => array(
					array("key" => "height", "name" => "Өндөр (px)",        "type" => "number", "def" => "60"),
					array("key" => "bg",     "name" => "Дэвсгэр өнгө",      "type" => "color"),
					array("key" => "line",   "name" => "Тусгаарлах зураас", "type" => "bool")
				)
			)
		);
	}

	public static function blockTypeObj($type)
	{
		$all = self::blockTypes();

		return isset($all[$type]) ? $all[$type] : null;
	}

	public static function hasSub($type)
	{
		$obj = self::blockTypeObj($type);

		return $obj != null && !empty($obj["subCols"]);
	}

	/** Каталогийн def утгуудаас бүрдсэн массив */
	public static function blockDefaults($type, $sub = false)
	{
		$obj = self::blockTypeObj($type);
		$out = array();
		if ($obj == null) {
			return $out;
		}

		$cols = $sub ? (isset($obj["subCols"]) ? $obj["subCols"] : array()) : $obj["cols"];
		foreach ($cols as $col) {
			$out[$col["key"]] = isset($col["def"]) ? $col["def"] : "";
		}

		return $out;
	}

	public static function defaultBlocks()
	{
		return array(
			array(
				"type" => "hero",
				"data" => array(
					"overlay" => "50", "logoWidth" => "160",
					"eyebrow" => "MGL E&C",
					"title" => "Шинэ оффисын нээлтийн өдөрлөг",
					"subtitle" => "Бидний шинэ гэрт хамтдаа тэмдэглэе.",
					"dateText" => "", "locationText" => "",
					"btnText" => "Бүртгүүлэх",
					"align" => "center", "valign" => "center", "height" => "90",
					"textColor" => "#FFFFFF"
				)
			),
			array(
				"type" => "info",
				"data" => array("title" => "Арга хэмжээний мэдээлэл", "columns" => "3", "bg" => "", "padTop" => "80", "padBottom" => "80"),
				"sub"  => array(
					array("icon" => "fa fa-calendar",   "pic" => "", "label" => "Огноо",   "value" => ""),
					array("icon" => "fa fa-clock-o",    "pic" => "", "label" => "Цаг",     "value" => ""),
					array("icon" => "fa fa-map-marker", "pic" => "", "label" => "Байршил", "value" => "")
				)
			),
			array(
				"type" => "form",
				"data" => array(
					"title" => "Бүртгүүлэх",
					"subtitle" => "Доорх мэдээллийг бөглөн бүртгүүлнэ үү.",
					"maxWidth" => "620", "labelShow" => "y",
					"bg" => "", "padTop" => "80", "padBottom" => "100"
				)
			)
		);
	}

	/**
	 * Үндсэн блокуудыг дэд мөрүүдтэй нь хамт буцаана.
	 * Бүтэц: array( array("blockID"=>.., "blockType"=>.., "data"=>array(), "sub"=>array(...)) )
	 */
	public static function blocks($db, $onlyActive = true)
	{
		$t = self::tables();

		$sql = "SELECT * FROM `" . $t["block"] . "`";
		if ($onlyActive) {
			$sql .= " WHERE `blockStatus`=1";
		}
		$sql .= " ORDER BY `blockOrder` ASC, `blockID` ASC";

		$rows = $db->rawQuery($sql, null);
		if (!is_array($rows)) {
			return array();
		}

		$main = array();
		$subs = array();

		foreach ($rows as $row) {
			$row["data"] = self::decode($row["blockData"]);
			$row["sub"] = array();

			if ((int)$row["parentID"] > 0) {
				$subs[(int)$row["parentID"]][] = $row;
			} else {
				$main[(int)$row["blockID"]] = $row;
			}
		}

		foreach ($subs as $parentID => $list) {
			if (isset($main[$parentID])) {
				$main[$parentID]["sub"] = $list;
			}
		}

		return array_values($main);
	}

	public static function decode($json)
	{
		$out = json_decode((string)$json, true);

		return is_array($out) ? $out : array();
	}

	/** Блокийн утга — хоосон бол $fallback */
	public static function val($data, $key, $fallback = "")
	{
		if (isset($data[$key]) && $data[$key] !== "") {
			return $data[$key];
		}

		return $fallback;
	}

	/* ------------------------------------------------------------------
	   Бүртгэлийн төлөв
	   ------------------------------------------------------------------ */

	public static function entryCount($db)
	{
		$t = self::tables();

		return (int)RegistrationCore::scalar($db, "SELECT COUNT(*) FROM `" . $t["entry"] . "` WHERE `entryStatus`=1", null);
	}

	/**
	 * Бүртгэл нээлттэй эсэх.
	 * Буцаах: array("open"=>bool, "state"=>"open|closed|full", "title"=>.., "text"=>..)
	 */
	public static function status($db, $set = null)
	{
		if ($set === null) {
			$set = self::settings($db);
		}

		$closed = array(
			"open" => false, "state" => "closed",
			"title" => $set["closedTitle"], "text" => $set["closedText"]
		);

		if ((string)$set["regOpen"] !== "1") {
			return $closed;
		}

		$now = time();

		if (trim($set["regOpenFrom"]) != "") {
			$from = strtotime(str_replace("T", " ", $set["regOpenFrom"]));
			if ($from && $now < $from) {
				return $closed;
			}
		}

		if (trim($set["regOpenTo"]) != "") {
			$to = strtotime(str_replace("T", " ", $set["regOpenTo"]));
			if ($to && $now > $to) {
				return $closed;
			}
		}

		$limit = (int)$set["regLimit"];
		if ($limit > 0 && self::entryCount($db) >= $limit) {
			return array(
				"open" => false, "state" => "full",
				"title" => $set["fullTitle"], "text" => $set["fullText"]
			);
		}

		return array("open" => true, "state" => "open", "title" => "", "text" => "");
	}

	/* ------------------------------------------------------------------
	   Оролт цэвэрлэх / шалгах
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

	/** Блокийн дэвсгэр өнгө + дээд/доод зайг inline style болгоно */
	public static function sectionStyle($data, $defTop = 0, $defBottom = 0)
	{
		$style = "";

		$bg = self::val($data, "bg");
		if ($bg != "") {
			$style .= "background-color:" . self::esc($bg) . ";";
		}

		$style .= "padding-top:" . (int)self::val($data, "padTop", $defTop) . "px;";
		$style .= "padding-bottom:" . (int)self::val($data, "padBottom", $defBottom) . "px;";

		return $style;
	}

	/**
	 * Нэг утга буцаана.
	 *
	 * MysqliDb::rawQueryValue нь query-ийн төгсгөлд "LIMIT 1" байхгүй бол
	 * МАССИВ буцаадаг тул шууд (int) хөрвүүлэхэд буруу үр дүн гардаг.
	 * Энэ helper түүнийг нэг мөрөөр шийднэ.
	 */
	public static function scalar($db, $sql, $params = null)
	{
		$val = $db->rawQueryValue($sql . " LIMIT 1", $params);

		if (is_array($val)) {
			$val = count($val) > 0 ? reset($val) : null;
		}

		return $val;
	}

	public static function tableExists($db, $table)
	{
		$row = $db->rawQueryOne("SHOW TABLES LIKE '" . str_replace("'", "", $table) . "'", null);

		return is_array($row) && count($row) > 0;
	}

	/** Модуль суусан эсэх — суугаагүй бол суулгана */
	public static function ensure($db)
	{
		$t = self::tables();

		if (!self::tableExists($db, $t["entry"])) {
			self::install($db, true);

			return true;
		}

		return false;
	}

	/**
	 * Илгээсэн формыг шалгана.
	 * Буцаах: array("ok"=>bool, "errors"=>array(key=>msg), "core"=>array(), "extra"=>array(), "values"=>array())
	 */
	public static function validate($db, $fieldArr, $post, $set)
	{
		$errors = array();
		$values = array();
		$core   = array("name" => "", "phone" => "", "email" => "");
		$extra  = array();

		$required = $set["requiredText"] != "" ? $set["requiredText"] : "Заавал бөглөнө үү.";

		foreach ($fieldArr as $field) {
			$key  = $field["fieldKey"];
			$type = $field["fieldType"];
			$raw  = isset($post[$key]) ? $post[$key] : "";

			if ($type == "checkbox") {
				$raw = is_array($raw) ? $raw : ($raw === "" ? array() : array($raw));
				$allowed = self::parseOptions($field["fieldOptions"]);
				$picked = array();

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

			if ($type == "consent") {
				$checked = ($raw !== "" && $raw !== "0");
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

			if ($field["fieldCore"] != "") {
				$core[$field["fieldCore"]] = $val;
			} else {
				$extra[$key] = $val;
			}
		}

		/* Давхардал — Тохиргоо дээр асаасан үед */
		if (count($errors) < 1 && (string)$set["regDupCheck"] === "1") {
			$t = self::tables();
			$dup = false;

			if ($core["phone"] != "") {
				$dup = (int)RegistrationCore::scalar($db, 
					"SELECT COUNT(*) FROM `" . $t["entry"] . "` WHERE `entryStatus`=1 AND `entryPhone`=?",
					array($core["phone"])
				) > 0;
			}

			if (!$dup && $core["email"] != "") {
				$dup = (int)RegistrationCore::scalar($db, 
					"SELECT COUNT(*) FROM `" . $t["entry"] . "` WHERE `entryStatus`=1 AND `entryEmail`=?",
					array($core["email"])
				) > 0;
			}

			if ($dup) {
				$errors["_form"] = $set["dupText"] != "" ? $set["dupText"] : "Аль хэдийн бүртгүүлсэн байна.";
			}
		}

		return array(
			"ok"     => count($errors) < 1,
			"errors" => $errors,
			"core"   => $core,
			"extra"  => $extra,
			"values" => $values
		);
	}

	/** Бүртгэл хадгална, entryID буцаана */
	public static function saveEntry($db, $core, $extra)
	{
		$t = self::tables();

		return $db->insert($t["entry"], array(
			"entryName"   => $core["name"],
			"entryPhone"  => $core["phone"],
			"entryEmail"  => $core["email"],
			"entryData"   => json_encode($extra, JSON_UNESCAPED_UNICODE),
			"entryIP"     => self::clientIp(),
			"entryDate"   => date("Y-m-d H:i:s"),
			"entryStatus" => 1
		));
	}

	public static function clientIp()
	{
		$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";

		return substr((string)$ip, 0, 60);
	}

	/* ------------------------------------------------------------------
	   Туслах
	   ------------------------------------------------------------------ */

	/** Бүртгэлийн хуудасны бүтэн хаяг (QR код үүсгэхэд) */
	public static function pageUrl($set)
	{
		$base = isset($set["siteBase"]) ? trim($set["siteBase"]) : "";

		if ($base == "") {
			$base = "https://mglenc.com";
		}

		return rtrim($base, "/") . "/registration";
	}

	/** Дараагийн эрэмбийн дугаар */
	public static function nextOrder($db, $table, $orderCol, $whereSql = "1=1")
	{
		$max = (int)RegistrationCore::scalar($db, "SELECT MAX(`" . $orderCol . "`) FROM `" . $table . "` WHERE " . $whereSql, null);

		return $max + 1;
	}

	/** Excel-д ашиглах баганын гарчиг */
	public static function exportColumns($fieldArr)
	{
		$cols = array("№");
		foreach ($fieldArr as $field) {
			$cols[] = $field["fieldLabel"] != "" ? $field["fieldLabel"] : $field["fieldKey"];
		}
		$cols[] = "Бүртгүүлсэн огноо";

		return $cols;
	}

	/** Нэг бүртгэлийг талбарын дарааллаар эгнээ болгоно */
	public static function exportRow($entry, $fieldArr, $index)
	{
		$extra = self::decode($entry["entryData"]);
		$row = array($index);

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
				default:
					$val = isset($extra[$field["fieldKey"]]) ? $extra[$field["fieldKey"]] : "";
					$row[] = is_array($val) ? implode(", ", $val) : $val;
					break;
			}
		}

		$row[] = $entry["entryDate"];

		return $row;
	}
}
