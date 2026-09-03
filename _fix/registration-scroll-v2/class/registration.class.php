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

			/* Хуудасны дэвсгэр — БҮХ хуудсанд тогтмол зогсоно (fixed).
			   Текст нь дээгүүр нь гүйнэ, зураг/видео нь хөдлөхгүй. */
			"pageBgPic"      => "",
			"pageBgVideo"    => "",
			"pageBgOverlay"  => "55",
			"pageBgBlur"     => "0",
			"pageBgPos"      => "center",
			"pageBgMoved"    => "",
			"agendaAdded"    => "",

			/* Гүйлт (scroll) */
			"scrollSnap"     => "1",
			"scrollDots"     => "1",

			/* Чөлөөт код */
			"customCss"      => "",
			"customHeadHtml" => "",
			"footerText"     => "© MGL E&C LLC",

			/* Шууд засварлах горим (хуудсан дээрээ дарж засах) */
			"liveEdit"       => "1",
			"editToken"      => "",
			"editTokenExp"   => "0",

			/* Медиа хадгалалт — Cloudflare R2.
			   Хоосон бол const.php доторх $gloR2* утгуудыг ашиглана,
			   тэр ч хоосон бол зураг сервер дээрээ хадгалагдана. */
			"r2Account"      => "",
			"r2Bucket"       => "",
			"r2Key"          => "",
			"r2Secret"       => "",
			"mediaCdn"       => ""
		);
	}

	/** Нууц утгууд — админд бүтнээр нь харуулахгүй */
	public static function secretSettings()
	{
		return array("r2Secret", "editToken");
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

			"agenda" => array(
				"label" => "Хөтөлбөр (agenda)",
				"icon"  => "fa fa-list-alt",
				"desc"  => "Дээр нь цагийн хуваарь (timeline), доор нь чөлөөтэй засварлах \"Хөтөлбөр\" хэсэг.",
				"cols"  => array(
					array("key" => "title",     "name" => "Хэсгийн гарчиг", "type" => "text"),

					/* Доод талын чөлөөт хэсэг */
					array("key" => "programTitle",   "name" => "Доод хэсгийн гарчиг",        "type" => "text", "def" => "Хөтөлбөр"),
					array("key" => "program",        "name" => "Хөтөлбөрийн агуулга",        "type" => "editor",
						"help" => "Хуудсан дээрээ шууд засахад үсгийн хэмжээ, өнгө, байрлал өөрчлөх багаж гарч ирнэ."),
					array("key" => "programAlign",   "name" => "Текстийн байрлал",           "type" => "select", "opt" => "left:Зүүн|center:Төв|right:Баруун", "def" => "left"),
					array("key" => "programWidth",   "name" => "Хайрцгийн өргөн (px)",       "type" => "number", "def" => "760"),
					array("key" => "programPos",     "name" => "Хайрцгийн байрлал",          "type" => "select", "opt" => "left:Зүүн|center:Голд|right:Баруун", "def" => "center"),
					array("key" => "programBg",      "name" => "Хайрцгийн дэвсгэр өнгө",     "type" => "color"),
					array("key" => "programOpacity", "name" => "Дэвсгэрийн тунгалаг (0-100)", "type" => "number", "def" => "70"),
					array("key" => "programPad",     "name" => "Дотоод зай (px)",            "type" => "number", "def" => "32"),

					array("key" => "bg",        "name" => "Дэвсгэр өнгө",   "type" => "color"),
					array("key" => "padTop",    "name" => "Дээд зай (px)",  "type" => "number", "def" => "80"),
					array("key" => "padBottom", "name" => "Доод зай (px)",  "type" => "number", "def" => "80")
				),
				"subLabel" => "Цагийн хуваарийн мөр",
				"subCols"  => array(
					array("key" => "time",     "name" => "Цаг",      "type" => "text"),
					array("key" => "date",     "name" => "Огноо",    "type" => "text"),
					array("key" => "location", "name" => "Байршил",  "type" => "text"),
					array("key" => "body",     "name" => "Агуулга",  "type" => "editor")
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
					"logoWidth" => "160",
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
				"type" => "agenda",
				"data" => array(
					"title" => "Арга хэмжээний хөтөлбөр",
					"programTitle" => "Хөтөлбөр",
					"program" => "<p>Өдөрлөгийн дэлгэрэнгүй хөтөлбөрөө энд бичнэ. Хуудсан дээрээ шууд дарж үсгийн хэмжээ, өнгө, байрлалаа өөрчилнө.</p>",
					"programAlign" => "left", "programWidth" => "760", "programPos" => "center",
					"programBg" => "", "programOpacity" => "70", "programPad" => "32",
					"bg" => "", "padTop" => "80", "padBottom" => "80"
				),
				"sub"  => array(
					array("date" => "2026.09.25", "time" => "09:00", "location" => "Үндсэн танхим", "body" => "<p><strong>Бүртгэл</strong> — зочдын угтах</p>"),
					array("date" => "2026.09.25", "time" => "10:00", "location" => "Үндсэн танхим", "body" => "<p><strong>Нээлтийн ёслол</strong></p>"),
					array("date" => "2026.09.25", "time" => "12:00", "location" => "Ресторан", "body" => "<p><strong>Өдрийн хоол</strong></p>")
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

	/**
	 * #RRGGBB + тунгалаг хувь -> rgba(). Хоосон өнгө бол хоосон буцаана,
	 * ингэснээр хайрцаг ил тод үлдэж хуудасны дэвсгэр харагдана.
	 */
	public static function rgba($hex, $pct)
	{
		$hex = trim((string)$hex);
		$pct = max(0, min(100, (int)$pct));

		if ($hex === "" || $pct === 0) {
			return "";
		}

		$hex = ltrim($hex, "#");

		if (strlen($hex) === 3) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if (!preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
			return "";
		}

		return "rgba(" . hexdec(substr($hex, 0, 2)) . "," . hexdec(substr($hex, 2, 2))
			. "," . hexdec(substr($hex, 4, 2)) . "," . round($pct / 100, 3) . ")";
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
	   Шууд засварлах горим (хуудсан дээрээ дарж засах)
	   ------------------------------------------------------------------ */

	/** Нэвтэрсэн админ мөн эсэх (нүүр сайт болон CP Admin session хуваалцдаг) */
	public static function isAdminSession($db)
	{
		if (empty($_SESSION["umail"]) || empty($_SESSION["upass"])) {
			return false;
		}

		global $tbl_prefadmin, $tbl_pref;
		$tbl = isset($tbl_prefadmin) && $tbl_prefadmin != ""
			? $tbl_prefadmin
			: (isset($tbl_pref) ? $tbl_pref : "db_") . "admin";

		if (!self::tableExists($db, $tbl)) {
			return false;
		}

		$n = (int)self::scalar($db,
			"SELECT COUNT(*) FROM `" . $tbl . "` WHERE `aname`=? AND `apass`=?",
			array($_SESSION["umail"], $_SESSION["upass"])
		);

		return $n > 0;
	}

	/** CP Admin-аас "шууд засах" линк үүсгэх (өөр домэйнээс ч ажиллана) */
	public static function makeEditToken($db)
	{
		$token = "";
		if (function_exists("random_bytes")) {
			$token = bin2hex(random_bytes(16));
		} else {
			$token = md5(uniqid("reg", true) . microtime());
		}

		self::saveSettings($db, array(
			"editToken"    => $token,
			"editTokenExp" => (string)(time() + 86400)
		));

		return $token;
	}

	public static function checkEditToken($db, $token, $set = null)
	{
		$token = trim((string)$token);
		if ($token === "" || strlen($token) > 64) {
			return false;
		}

		if ($set === null) {
			$set = self::settings($db);
		}

		if ($set["editToken"] === "" || (int)$set["editTokenExp"] < time()) {
			return false;
		}

		/* Хугацаа-тогтмол харьцуулалт */
		if (function_exists("hash_equals")) {
			return hash_equals((string)$set["editToken"], $token);
		}

		return (string)$set["editToken"] === $token;
	}

	/**
	 * Энэ хүсэлт хуудсыг засах эрхтэй юу?
	 * 1) Тохиргоонд liveEdit асаалттай
	 * 2) Нэвтэрсэн админ ЭСВЭЛ session дотор зөвшөөрөгдсөн тэмдэг
	 */
	public static function canEdit($db, $set)
	{
		if ((string)$set["liveEdit"] !== "1") {
			return false;
		}

		if (!empty($_SESSION["regEditor"])) {
			return true;
		}

		return self::isAdminSession($db);
	}

	/**
	 * Засварлах боломжтой элементэд тавих атрибутууд.
	 *   $scope  block | setting
	 *   $id     блокийн ID (setting үед хэрэггүй)
	 *   $key    талбарын түлхүүр
	 *   $mode   text | html
	 */
	public static function editAttr($on, $scope, $id, $key, $mode = "text")
	{
		if (!$on) {
			return "";
		}

		return ' data-reg-edit="' . self::esc($scope . ":" . $id . ":" . $key . ":" . $mode) . '"';
	}

	/** Медиа (зураг/видео) солих боломжтой элемент */
	public static function mediaAttr($on, $scope, $id, $key, $accept = "image")
	{
		if (!$on) {
			return "";
		}

		return ' data-reg-media="' . self::esc($scope . ":" . $id . ":" . $key . ":" . $accept) . '"';
	}

	/** Блокийн төрөлд зөвшөөрөгдсөн талбарын түлхүүрүүд (аюулгүйн шүүлт) */
	public static function allowedKeys($blockType)
	{
		$obj = self::blockTypeObj($blockType);
		$keys = array();

		if ($obj == null) {
			return $keys;
		}

		foreach ($obj["cols"] as $col) {
			$keys[$col["key"]] = isset($col["type"]) ? $col["type"] : "text";
		}

		if (!empty($obj["subCols"])) {
			foreach ($obj["subCols"] as $col) {
				$keys[$col["key"]] = isset($col["type"]) ? $col["type"] : "text";
			}
		}

		return $keys;
	}

	/**
	 * Талбарын төрлөөр нь цэвэрлэнэ.
	 * editor / code нь админы бичсэн HTML тул хэвээр үлдэнэ (CP Admin-тай ижил),
	 * бусад нь энгийн текст болно.
	 */
	public static function sanitizeByType($value, $type)
	{
		if (is_array($value)) {
			$value = "";
		}

		$value = (string)$value;

		switch ($type) {

			case "editor":
			case "code":
				$value = str_replace("\0", "", $value);

				/* $strict — хуудсан дээрээс ирсэн HTML. CP Admin-ы TinyMCE-ээс
				   ирсэн агуулгыг хөндөхгүй, зөвхөн live edit-ийг шүүнэ. */
				if ($strict) {
					$value = self::sanitizeHtml($value);
				}

				return function_exists("mb_substr")
					? mb_substr($value, 0, 60000, "UTF-8")
					: substr($value, 0, 60000);

			case "file":
				$value = trim(strip_tags($value));
				if ($value === "") {
					return "";
				}
				/* Зөвхөн танил хэлбэрийн зам */
				if (preg_match('#^(/pics/|/postpic/|/newsimg/|/image/|https?://)#i', $value)) {
					return substr($value, 0, 500);
				}
				return "";

			case "number":
				return preg_match('/^-?\d{1,9}$/', trim($value)) ? trim($value) : "";

			case "color":
				$value = trim($value);
				return preg_match('/^#[0-9A-Fa-f]{3,8}$/', $value) ? $value : "";

			case "bool":
				return ($value === "y" || $value === "1" || $value === "true") ? "y" : "";

			case "textarea":
				return self::clean($value, 2000);

			default:
				return self::clean($value, 500);
		}
	}

	/** Шууд засахад өөрчилж болох тохиргоонууд */
	public static function editableSettings()
	{
		return array("footerText", "successTitle", "successText", "submitLabel",
			"closedTitle", "closedText", "fullTitle", "fullText", "metaTitle",
			"pageBgPic", "pageBgVideo", "pageBgOverlay", "pageBgBlur", "pageBgPos");
	}

	/** Тохиргооны утгыг ямар төрлөөр цэвэрлэхийг заана */
	public static function settingType($key)
	{
		$map = array(
			"pageBgPic"     => "file",
			"pageBgVideo"   => "file",
			"favicon"       => "file",
			"pageBgOverlay" => "number",
			"pageBgBlur"    => "number"
		);

		return isset($map[$key]) ? $map[$key] : "text";
	}

	/**
	 * Хуудсан дээрээс ирсэн HTML-ийг цэвэрлэнэ.
	 *
	 * Загварын style, өнгө, үсгийн хэмжээг ҮЛДЭЭНЭ — тэр нь яг засварлагчийн
	 * зорилго. Зөвхөн гүйцэтгэгддэг (script, event, javascript:) зүйлсийг хаяна.
	 */
	public static function sanitizeHtml($html)
	{
		$html = (string)$html;
		$bad  = "script|style|iframe|object|embed|form|input|button|select|textarea|link|meta|base";

		$rules = array(
			/* Хос таг — доторх агуулгатай нь хамт */
			'#<\s*(' . $bad . ')\b[^>]*>.*?<\s*/\s*\1\s*>#isu'  => "",
			/* Үлдсэн ганц таг */
			'#<\s*/?\s*(' . $bad . ')\b[^>]*>#isu'                 => "",
			/* onclick, onerror ... event атрибутууд */
			'#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#isu' => ""
		);

		foreach ($rules as $re => $to) {
			$out = preg_replace($re, $to, $html);
			if ($out !== null) {
				$html = $out;
			}
		}

		/* javascript: / vbscript: хаяг */
		$out = preg_replace(
			'#(href|src)\s*=\s*("|\')\s*(javascript|vbscript)\s*:[^"\']*\2#isu',
			'$1="#"',
			$html
		);
		if ($out !== null) {
			$html = $out;
		}

		return $html;
	}

	/**
	 * Хуудасны тогтмол дэвсгэр (зураг эсвэл видео).
	 *
	 * Өмнө нь дэвсгэр зураг Hero блок дотор байсан бөгөөд хуудастайгаа хамт
	 * доошоо гүйдэг байв. Одоо бүх хуудсанд НЭГ дэвсгэр байж, дэлгэцэн дээр
	 * тогтмол зогсоно. Хуучин Hero зургийг нэг удаа автоматаар шилжүүлж авна —
	 * админ дахин юу ч хийх шаардлагагүй.
	 */
	/**
	 * "Хөтөлбөр" хэсэг хуудсан дээр БАЙХ ёстой.
	 *
	 * Мэдээллийн хэсгийн ЯГ ДООР нь agenda блок нэмнэ — тэр блокийн дотор
	 * өдөрлөгийн хөтөлбөрөө бичих чөлөөт талбар байна. Зөвхөн НЭГ УДАА
	 * ажиллана (agendaAdded тэмдэглэгээ), тиймээс дараа нь админ блокоо
	 * устгасан бол дахин ургахгүй.
	 */
	public static function ensureAgenda($db, $set)
	{
		if ((string)self::val($set, "agendaAdded") === "1") {
			return false;
		}

		$t = self::tables();

		$have = (int)self::scalar($db,
			"SELECT COUNT(*) FROM `" . $t["block"] . "` WHERE `blockType`='agenda' AND `parentID`=0",
			null
		);

		if ($have > 0) {
			self::saveSettings($db, array("agendaAdded" => "1"));
			return false;
		}

		/* Мэдээллийн (info) блокийн байрлалыг олно — түүний яг ард орно */
		$after = (int)self::scalar($db,
			"SELECT `blockOrder` FROM `" . $t["block"] . "` WHERE `blockType`='info' AND `parentID`=0"
				. " ORDER BY `blockOrder` ASC",
			null
		);

		if ($after < 1) {
			$after = (int)self::scalar($db,
				"SELECT MAX(`blockOrder`) FROM `" . $t["block"] . "` WHERE `parentID`=0 AND `blockType`<>'form'",
				null
			);
		}

		/* Ард нь байгаа блокуудыг нэг доош түлхэнэ */
		$db->rawQuery(
			"UPDATE `" . $t["block"] . "` SET `blockOrder`=`blockOrder`+1"
				. " WHERE `parentID`=0 AND `blockOrder`>?",
			array($after)
		);

		$data = self::blockDefaults("agenda");
		$data["title"]        = "Өдөрлөгийн хөтөлбөр";
		$data["programTitle"] = "Хөтөлбөр";
		$data["program"]      = "<p>Өдөрлөгийн хөтөлбөрөө энд бичнэ үү.</p>";

		$blockID = $db->insert($t["block"], array(
			"parentID"    => 0,
			"blockType"   => "agenda",
			"blockData"   => json_encode($data, JSON_UNESCAPED_UNICODE),
			"blockStatus" => 1,
			"blockOrder"  => $after + 1
		));

		/* Цагийн хуваарийн эхний хоёр мөр — админ засаад л болно */
		if ($blockID) {
			$rows = array(
				array("time" => "09:00", "date" => "", "location" => "", "body" => "<p>Бүртгэл</p>"),
				array("time" => "10:00", "date" => "", "location" => "", "body" => "<p>Нээлт</p>")
			);

			$order = 1;
			foreach ($rows as $row) {
				$db->insert($t["block"], array(
					"parentID"    => (int)$blockID,
					"blockType"   => "agenda",
					"blockData"   => json_encode($row, JSON_UNESCAPED_UNICODE),
					"blockStatus" => 1,
					"blockOrder"  => $order++
				));
			}
		}

		self::saveSettings($db, array("agendaAdded" => "1"));

		return true;
	}

	public static function pageBg($db, $set)
	{
		$pic   = trim(self::val($set, "pageBgPic"));
		$video = trim(self::val($set, "pageBgVideo"));

		if ($pic == "" && $video == "" && (string)self::val($set, "pageBgMoved") !== "1") {

			$t = self::tables();
			$rows = $db->rawQuery(
				"SELECT `blockData` FROM `" . $t["block"] . "` WHERE `blockType`='hero'"
					. " ORDER BY `blockOrder` ASC, `blockID` ASC",
				null
			);

			if (is_array($rows)) {
				foreach ($rows as $row) {
					$d = self::decode($row["blockData"]);

					if (self::val($d, "bgPic") != "" || self::val($d, "bgVideo") != "") {
						$pic   = self::val($d, "bgPic");
						$video = self::val($d, "bgVideo");
						break;
					}
				}
			}

			self::saveSettings($db, array(
				"pageBgPic"   => $pic,
				"pageBgVideo" => $video,
				"pageBgMoved" => "1"
			));
		}

		$pos = self::val($set, "pageBgPos", "center");
		if (!in_array($pos, array("center", "top", "bottom"))) {
			$pos = "center";
		}

		return array(
			"pic"     => $pic,
			"video"   => $video,
			"overlay" => max(0, min(100, (int)self::val($set, "pageBgOverlay", "55"))),
			"blur"    => max(0, min(30, (int)self::val($set, "pageBgBlur", "0"))),
			"pos"     => $pos
		);
	}

	/* ------------------------------------------------------------------
	   Медиа — Cloudflare R2 (буцах зам: сервер дээрээ хадгална)
	   ------------------------------------------------------------------ */

	/** postpic доторх дэд хавтас — /pics/reg/... болж үйлчилнэ */
	const MEDIA_FOLDER = "reg";

	/**
	 * R2-ийн тохиргоог бэлдэнэ.
	 * Эрэмбэ: const.php доторх $gloR2* -> админд оруулсан утгууд.
	 * Буцаах: array("ready"=>bool, "bucket"=>.., "cdn"=>..)
	 */
	public static function mediaBoot($db, $set = null)
	{
		if ($set === null) {
			$set = self::settings($db);
		}

		$map = array(
			"gloR2Account" => "r2Account",
			"gloR2Bucket"  => "r2Bucket",
			"gloR2Key"     => "r2Key",
			"gloR2Secret"  => "r2Secret"
		);

		foreach ($map as $glo => $key) {
			$val = isset($set[$key]) ? trim((string)$set[$key]) : "";

			if (empty($GLOBALS[$glo]) && $val !== "") {
				$GLOBALS[$glo] = $val;
			}
		}

		/* Зөвхөн бүртгэлийн хуудсанд CDN-ээр үйлчилнэ (сайтын бусад хэсэгт хөндөхгүй) */
		$cdn = isset($set["mediaCdn"]) ? trim((string)$set["mediaCdn"]) : "";

		if (empty($GLOBALS["gloCdnBase"]) && $cdn !== "") {
			$GLOBALS["gloCdnBase"] = rtrim($cdn, "/");
		}

		$r2Path = self::sitePath("cpadmin/r2.php");
		if (!function_exists("r2Enabled") && $r2Path !== "" && is_file($r2Path)) {
			include_once $r2Path;
		}

		return array(
			"ready"  => function_exists("r2Enabled") && r2Enabled(),
			"bucket" => isset($GLOBALS["gloR2Bucket"]) ? $GLOBALS["gloR2Bucket"] : "",
			"cdn"    => isset($GLOBALS["gloCdnBase"]) ? $GLOBALS["gloCdnBase"] : ""
		);
	}

	/** Сайтын үндсэн хавтаснаас эхлэсэн зам */
	public static function sitePath($rel = "")
	{
		/* class/registration.class.php -> сайтын үндэс */
		$root = dirname(__DIR__);

		return $rel === "" ? $root : $root . "/" . ltrim($rel, "/");
	}

	/**
	 * Медиагийн хаяг. CDN тохируулсан бол түүгээр, эс бөгөөс сервер дээрээс.
	 * (Глобал cdnUrl() функцээс хараат бус — модуль дангаараа ажиллана.)
	 */
	public static function mediaUrl($path)
	{
		$path = trim((string)$path);

		if ($path === "") {
			return "";
		}

		/* Бүтэн хаяг бол хэвээр нь */
		if (preg_match('#^(https?:)?//#i', $path)) {
			return $path;
		}

		if (substr($path, 0, 1) !== "/") {
			$path = "/" . $path;
		}

		$base = isset($GLOBALS["gloCdnBase"]) ? rtrim((string)$GLOBALS["gloCdnBase"], "/") : "";

		return $base === "" ? $path : $base . $path;
	}

	/** "8M", "512K", "1G" -> байт */
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

	/** Байтыг хүн уншихаар */
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

	/** PHP-ийн байршуулалтын алдааны кодыг ойлгомжтой текст болгоно */
	public static function uploadErrorText($code)
	{
		$max = self::sizeText(self::uploadMaxBytes());

		switch ((int)$code) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return "Файл хэт том байна. Энэ сервер дээр дээд тал нь " . $max . " багтана.";
			case UPLOAD_ERR_PARTIAL:
				return "Файл бүтнээрээ ирсэнгүй. Дахин оролдоно уу.";
			case UPLOAD_ERR_NO_FILE:
				return "Файл сонгоогүй байна.";
			case UPLOAD_ERR_NO_TMP_DIR:
				return "Серверийн түр хавтас алга (upload_tmp_dir). Хостоо мэдэгдэнэ үү.";
			case UPLOAD_ERR_CANT_WRITE:
				return "Сервер дискэн дээрээ бичиж чадсангүй (диск дүүрсэн байж болно).";
			case UPLOAD_ERR_EXTENSION:
				return "PHP-ийн өргөтгөл байршуулалтыг зогсоолоо.";
		}

		return "Файл байршуулж чадсангүй (алдаа " . (int)$code . ").";
	}

	/* ------------------------------------------------------------------
	   AJAX — ямар ч тохиолдолд JSON буцаана
	   ------------------------------------------------------------------ */

	/** ob_start-ын түвшин — jsonShutdown зөвхөн өөрийнхөө буферийг цэвэрлэнэ */
	protected static $jsonLevel = 0;

	/** Нарийн алдааг хариунд бичих эсэх (зөвхөн нэвтэрсэн засварлагчид) */
	protected static $jsonDetail = false;

	/**
	 * AJAX хариуг хамгаална.
	 *
	 * PHP fatal алдаа гарвал вэб сервер HTML алдаа буцааж, хөтөч дээр
	 * "Сервер хариу буруу буцаалаа" гэж гардаг. Энэ нь тэр тохиолдолд ч
	 * JSON буцаана — админ жинхэнэ шалтгааныг хуудсан дээрээ хардаг.
	 */
	public static function jsonGuard($showDetail = false)
	{
		self::$jsonDetail = $showDetail ? true : false;

		@ini_set("display_errors", "0");
		@ini_set("html_errors", "0");

		if (!headers_sent()) {
			header("Content-Type: application/json; charset=utf-8");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("X-Content-Type-Options: nosniff");
		}

		ob_start();
		self::$jsonLevel = ob_get_level();

		register_shutdown_function(array("RegistrationCore", "jsonShutdown"));
	}

	public static function jsonShutdown()
	{
		$err   = error_get_last();
		$fatal = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING,
			E_COMPILE_ERROR, E_COMPILE_WARNING, E_USER_ERROR);

		$isFatal = is_array($err) && in_array($err["type"], $fatal);

		/* PHP fatal дээр 500 тавьчихсан байдаг — хөтөч JSON-оо уншихын
		   тулд буферыг цэвэрлэхээс ӨМНӨ 200 болгож буцаана */
		if ($isFatal && !headers_sent() && function_exists("http_response_code")) {
			http_response_code(200);
		}

		$body = "";

		while (ob_get_level() >= self::$jsonLevel && ob_get_level() > 0) {
			$chunk = ob_get_clean();
			$body  = ($chunk === false ? "" : $chunk) . $body;
		}

		if ($isFatal) {
			$where = basename(isset($err["file"]) ? $err["file"] : "") . ":" . (isset($err["line"]) ? $err["line"] : 0);

			error_log("registration ajax fatal: " . $err["message"] . " @ " . $where);

			$text = "Серверт алдаа гарлаа.";

			if (self::$jsonDetail) {
				/* Зөвхөн эхний мөр — stack trace, серверийн бүтэн замыг харуулахгүй */
				$msg = (string)$err["message"];
				$cut = strpos($msg, "Stack trace");

				if ($cut !== false) {
					$msg = substr($msg, 0, $cut);
				}

				$msg = str_replace(array(self::sitePath(), "\\"), array("", "/"), $msg);
				$msg = trim(preg_replace('/\s+/', " ", $msg));

				if (strlen($msg) > 220) {
					/* UTF-8 тэмдэгтийг дундуур нь таслахгүй — эс бөгөөс json_encode унана */
					$msg = function_exists("mb_substr")
						? mb_substr($msg, 0, 220, "UTF-8")
						: preg_replace('/[\x80-\xFF]+$/', "", substr($msg, 0, 220));

					$msg .= "...";
				}

				$text .= " " . $msg . " (" . $where . ")";
			}

			if (!headers_sent()) {
				header("Content-Type: application/json; charset=utf-8");
			}

			$out = json_encode(array("ok" => 0, "error" => $text), JSON_UNESCAPED_UNICODE);

			echo $out === false ? '{"ok":0,"error":"Server error"}' : $out;
			return;
		}

		echo $body;
	}

	public static function mediaTypes()
	{
		return array(
			"jpg" => "image", "jpeg" => "image", "png" => "image", "gif" => "image",
			"webp" => "image", "avif" => "image", "svg" => "image",
			"mp4" => "video", "webm" => "video", "ogv" => "video", "mov" => "video"
		);
	}

	/**
	 * Байршуулсан файлыг хадгална.
	 *
	 * 1. cpadmin/postpic/reg/ дотор бичнэ
	 * 2. R2 тохируулагдсан бол тийш хуулна
	 * 3. Зөвхөн CDN-ээр үйлчилж байгаа үед л локал хуулбарыг устгана —
	 *    эс бөгөөс зураг эвдэрнэ
	 *
	 * Буцаах: array("ok"=>bool, "url"=>"/pics/reg/x.jpg", "kind"=>"image|video", "error"=>"")
	 */
	/**
	 * Байршуулсан файлыг шалгана (диск рүү бичихээс өмнө).
	 * Буцаах: array("ok"=>bool, "ext"=>.., "kind"=>"image|video", "error"=>"")
	 */
	public static function mediaValidate($file)
	{
		$fail = array("ok" => false, "ext" => "", "kind" => "", "error" => "");

		if (!is_array($file) || !isset($file["tmp_name"]) || !isset($file["error"])) {
			$fail["error"] = "Файл ирсэнгүй.";
			return $fail;
		}

		if ((int)$file["error"] !== UPLOAD_ERR_OK) {
			$fail["error"] = self::uploadErrorText($file["error"]);
			return $fail;
		}

		if (!isset($file["size"]) || (int)$file["size"] < 1) {
			$fail["error"] = "Файл хоосон байна.";
			return $fail;
		}

		$max = self::uploadMaxBytes();
		if ($max > 0 && $file["size"] > $max) {
			$fail["error"] = "Файл хэт том (" . self::sizeText($file["size"]) . "). Дээд хэмжээ " . self::sizeText($max) . ".";
			return $fail;
		}

		if ($file["size"] > 200 * 1024 * 1024) {
			$fail["error"] = "Файл хэт том (200 MB-аас их).";
			return $fail;
		}

		$name = isset($file["name"]) ? $file["name"] : "";

		/* Давхар өргөтгөл (a.php.jpg) болон дүрсний зам хаана */
		$name = str_replace(array("\0", "/", "\\"), "", $name);
		$ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

		$types = self::mediaTypes();
		if ($ext === "" || !isset($types[$ext])) {
			$fail["error"] = "Зөвхөн зураг (jpg, png, webp, gif, svg) эсвэл видео (mp4, webm) байршуулна.";
			return $fail;
		}

		/* Нэрэнд гүйцэтгэгдэх өргөтгөл нуугдсан эсэх */
		if (preg_match('/\.(php\d?|phtml|phar|cgi|pl|py|sh|htaccess|js|html?)\./i', $name)) {
			$fail["error"] = "Файлын нэр зөвшөөрөгдөхгүй.";
			return $fail;
		}

		$kind = $types[$ext];

		/* Зургийн хувьд агуулгыг нь бас шалгана */
		if ($kind == "image" && $ext != "svg" && function_exists("getimagesize")
			&& is_file($file["tmp_name"])) {
			if (@getimagesize($file["tmp_name"]) === false) {
				$fail["error"] = "Зураг уншигдахгүй байна.";
				return $fail;
			}
		}

		return array("ok" => true, "ext" => $ext, "kind" => $kind, "error" => "");
	}

	/**
	 * Байршуулсан медиагийн хавтсыг хамгаална.
	 *
	 * ЧУХАЛ: "php_flag" нь зөвхөн mod_php дээр ойлгогддог. PHP-FPM / CGI дээр
	 * .htaccess дотор ил бичвэл ТЭР ХАВТАС БҮХЭЛДЭЭ 500 өгдөг — тиймээс
	 * IfModule дотор хийж, бусад сервер дээр өөр аргаар хаалаа.
	 */
	public static function mediaGuard($dir)
	{
		$body = "# Байршуулсан медиа — энд ямар ч скрипт ажиллуулахгүй
"
			. "# php_flag зөвхөн mod_php дээр ажиллана — бусад тохиолдолд 500 өгдөг
"
			. "<IfModule mod_php.c>
	php_flag engine off
</IfModule>
"
			. "<IfModule mod_php7.c>
	php_flag engine off
</IfModule>
"
			. "<IfModule mod_php5.c>
	php_flag engine off
</IfModule>
"
			. "
"
			. "<IfModule mod_mime.c>
"
			. "	RemoveHandler .php .php3 .php4 .php5 .php7 .php8 .phtml .phar .pl .py .cgi .sh
"
			. "	AddType text/plain .php .php3 .php4 .php5 .php7 .php8 .phtml .phar .pl .py .cgi .sh
"
			. "</IfModule>
"
			. "
"
			. "<FilesMatch \"\.(php\d?|phtml|phar|pl|py|cgi|sh)$\">
"
			. "	<IfModule mod_authz_core.c>
		Require all denied
	</IfModule>
"
			. "	<IfModule !mod_authz_core.c>
		Order allow,deny
		Deny from all
	</IfModule>
"
			. "</FilesMatch>
";

		$guard = rtrim($dir, "/\\\\") . "/.htaccess";

		/* Хуучин — 500 өгдөг — хувилбарыг ч дарж бичнэ */
		if (!is_file($guard) || @file_get_contents($guard) !== $body) {
			@file_put_contents($guard, $body);
		}
	}

	public static function mediaStore($db, $file, $set = null)
	{
		$fail = array("ok" => false, "url" => "", "kind" => "", "error" => "");

		$check = self::mediaValidate($file);
		if (!$check["ok"]) {
			$fail["error"] = $check["error"];
			return $fail;
		}

		if (!is_uploaded_file($file["tmp_name"])) {
			$fail["error"] = "Файл буруу.";
			return $fail;
		}

		$ext  = $check["ext"];
		$kind = $check["kind"];

		$dir = self::sitePath("cpadmin/postpic/" . self::MEDIA_FOLDER);
		if (!is_dir($dir)) {
			@mkdir($dir, 0775, true);
		}
		if (!is_dir($dir) || !is_writable($dir)) {
			$fail["error"] = "cpadmin/postpic/" . self::MEDIA_FOLDER . " хавтас бичигдэхгүй байна.";
			return $fail;
		}

		/* Гүнзгий хамгаалалт: энэ хавтсанд ямар ч скрипт ажиллуулахгүй */
		self::mediaGuard($dir);

		$base = pathinfo($file["name"], PATHINFO_FILENAME);
		$base = preg_replace('/[^A-Za-z0-9_-]+/', "-", $base);
		$base = trim(substr($base, 0, 40), "-");
		if ($base === "") {
			$base = "media";
		}

		$name = $base . "-" . date("YmdHis") . "-" . substr(md5(uniqid("", true)), 0, 6) . "." . $ext;
		$dest = $dir . "/" . $name;

		if (!@move_uploaded_file($file["tmp_name"], $dest)) {
			$free = @disk_free_space($dir);

			$fail["error"] = "Файлыг хадгалж чадсангүй"
				. ($free !== false && $free < $file["size"] ? " — диск дүүрсэн байна." : " (хавтасны эрх шалгана уу).");

			error_log("registration upload: move_uploaded_file failed -> " . $dest);

			return $fail;
		}
		@chmod($dest, 0644);

		$media = self::mediaBoot($db, $set);
		$stored = "local";

		if ($media["ready"] && function_exists("r2Put")) {
			$key = self::MEDIA_FOLDER . "/" . $name;

			if (r2Put($key, $dest)) {
				$stored = "r2";

				/* CDN-ээр үйлчилж байж л локал хуулбарыг хаяна */
				if ($media["cdn"] !== "") {
					@unlink($dest);
					$stored = "r2-only";
				}
			}
		}

		return array(
			"ok"    => true,
			"url"   => "/pics/" . self::MEDIA_FOLDER . "/" . $name,
			"kind"  => $kind,
			"where" => $stored,
			"error" => ""
		);
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

/*
	Аюулгүйн шат: сайтын functions.php-д cdnUrl() ирээгүй байхад бүртгэлийн
	модуль дангаараа ажиллах ёстой. Байгаа бол дахин тодорхойлохгүй.
*/
if (!function_exists("cdnUrl")) {

	function cdnUrl($path)
	{
		return RegistrationCore::mediaUrl($path);
	}
}
