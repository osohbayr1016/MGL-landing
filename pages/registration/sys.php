<?php
/**
 * Арга хэмжээний бүртгэлийн хуудас — /registration
 *
 * Навигац, footer цэс, сайтын нийтлэг header ЮУ Ч ЭНД ОРОХГҮЙ.
 * Хуудсыг зөвхөн шууд линк эсвэл QR-аар нээнэ. Хайлтын системд
 * гаргахгүйн тулд noindex/nofollow тавьсан (skin/new/registration.php).
 *
 * Бүх агуулга CP Admin-аас удирдагдана. Түүнээс гадна нэвтэрсэн админ
 * ХУУДСАН ДЭЭРЭЭ ШУУД дарж текстээ засаж, дэвсгэр зураг/видеогоо
 * солих боломжтой (шууд засварлах горим).
 */

include_once __DIR__ . "/../../class/registration.class.php";

RegistrationCore::ensure($db);

$regSet    = RegistrationCore::settings($db);
$regFields = RegistrationCore::fields($db, true);
$regStatus = RegistrationCore::status($db, $regSet);
$regTbl    = RegistrationCore::tables();

/* ------------------------------------------------------------------
   Шууд засварлах горимд орох / гарах
   ------------------------------------------------------------------ */

if (isset($_GET["edit"]) && $_GET["edit"] != "") {
	if ((string)$regSet["liveEdit"] === "1" && RegistrationCore::checkEditToken($db, $_GET["edit"], $regSet)) {
		$_SESSION["regEditor"] = 1;
	}

	header("Location: /registration");
	exit;
}

if (isset($_GET["editexit"])) {
	unset($_SESSION["regEditor"]);
	header("Location: /registration");
	exit;
}

$regEdit = RegistrationCore::canEdit($db, $regSet);

/* Засварлагчийн хүсэлтийг баталгаажуулах нэг удаагийн түлхүүр */
if ($regEdit && empty($_SESSION["regNonce"])) {
	$_SESSION["regNonce"] = function_exists("random_bytes")
		? bin2hex(random_bytes(12))
		: md5(uniqid("n", true));
}
$regNonce = $regEdit ? $_SESSION["regNonce"] : "";

/* ------------------------------------------------------------------
   Засварлагчийн AJAX үйлдлүүд
   ------------------------------------------------------------------ */

/* Хүсэлт post_max_size-аас хэтэрвэл PHP $_POST, $_FILES хоёрыг ХОЁУЛАНГ нь
   хаядаг. Тэр үед энэ хуудас бүхэл HTML-ээ буцаадаг байсан тул хөтөч дээр
   "Сервер хариу буруу буцаалаа" гэж гардаг байв. Одоо ойлгомжтой JSON өгнө. */
if ($_SERVER["REQUEST_METHOD"] == "POST" && count($_POST) < 1 && count($_FILES) < 1
	&& isset($_SERVER["CONTENT_LENGTH"]) && (int)$_SERVER["CONTENT_LENGTH"] > 0) {

	if (!headers_sent()) {
		header("Content-Type: application/json; charset=utf-8");
	}

	echo json_encode(array(
		"ok"    => 0,
		"error" => "Файл хэт том байна (" . RegistrationCore::sizeText((int)$_SERVER["CONTENT_LENGTH"]) . "). "
			. "Энэ сервер дээр дээд тал нь " . RegistrationCore::sizeText(RegistrationCore::uploadMaxBytes()) . " багтана."
	), JSON_UNESCAPED_UNICODE);

	exit;
}

if (isset($_POST["regAction"])) {

	/* Fatal алдаа гарсан ч HTML биш, JSON буцаана */
	RegistrationCore::jsonGuard($regEdit);

	$respond = function ($arr) {
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		exit;
	};

	if (!$regEdit) {
		$respond(array("ok" => 0, "error" => "Засах эрх алга. Дахин нэвтэрнэ үү."));
	}

	$sentNonce = isset($_POST["regNonce"]) ? $_POST["regNonce"] : "";
	$realNonce = isset($_SESSION["regNonce"]) ? $_SESSION["regNonce"] : "";

	if ($sentNonce === "" || $realNonce === "" || $sentNonce !== $realNonce) {
		$respond(array("ok" => 0, "error" => "Хуудсыг дахин ачаална уу."));
	}

	switch ($_POST["regAction"]) {

		/* ---- Текст / утга хадгалах ---- */
		case "save":
			$payload = json_decode(isset($_POST["payload"]) ? $_POST["payload"] : "", true);
			if (!is_array($payload)) {
				$respond(array("ok" => 0, "error" => "Өгөгдөл уншигдсангүй."));
			}

			$saved = 0;

			/* Блокийн талбарууд */
			if (!empty($payload["block"]) && is_array($payload["block"])) {
				foreach ($payload["block"] as $blockID => $values) {
					$blockID = (int)$blockID;
					if ($blockID < 1 || !is_array($values)) {
						continue;
					}

					$row = $db->rawQueryOne(
						"SELECT * FROM `" . $regTbl["block"] . "` WHERE `blockID`=?",
						array($blockID)
					);
					if (!is_array($row) || count($row) < 1) {
						continue;
					}

					$allowed = RegistrationCore::allowedKeys($row["blockType"]);
					$data    = RegistrationCore::decode($row["blockData"]);
					$dirty   = false;

					foreach ($values as $key => $val) {
						if (!isset($allowed[$key])) {
							continue;
						}

						/* true — хуудсан дээрээс ирсэн HTML-ийг чанга шүүнэ */
						$val = RegistrationCore::sanitizeByType($val, $allowed[$key], true);

						if (!isset($data[$key]) || $data[$key] !== $val) {
							$data[$key] = $val;
							$dirty = true;
						}
					}

					if ($dirty) {
						$db->rawQuery(
							"UPDATE `" . $regTbl["block"] . "` SET `blockData`=? WHERE `blockID`=?",
							array(json_encode($data, JSON_UNESCAPED_UNICODE), $blockID)
						);
						$saved++;
					}
				}
			}

			/* Ерөнхий тохиргооны текстүүд */
			if (!empty($payload["setting"]) && is_array($payload["setting"])) {
				$allowedSet = RegistrationCore::editableSettings();
				$setVals = array();

				foreach ($payload["setting"] as $key => $val) {
					if (in_array($key, $allowedSet)) {
						$setVals[$key] = RegistrationCore::sanitizeByType(
							$val, RegistrationCore::settingType($key), true
						);
					}
				}

				if (count($setVals) > 0) {
					RegistrationCore::saveSettings($db, $setVals);
					$saved += count($setVals);
				}
			}

			$respond(array("ok" => 1, "saved" => $saved));
			break;

		/* ---- Зураг / видео байршуулах ---- */
		case "upload":
			if (!isset($_FILES["file"])) {
				$respond(array("ok" => 0, "error" => "Файл сонгоогүй байна."));
			}

			$res = RegistrationCore::mediaStore($db, $_FILES["file"], $regSet);

			if (!$res["ok"]) {
				$respond(array("ok" => 0, "error" => $res["error"]));
			}

			$respond(array(
				"ok"    => 1,
				"url"   => $res["url"],
				"src"   => RegistrationCore::mediaUrl($res["url"]),
				"kind"  => $res["kind"],
				"where" => $res["where"]
			));
			break;

		/* ---- Блокийг зөөх / нуух ---- */
		case "blockop":
			$blockID = (int)(isset($_POST["blockID"]) ? $_POST["blockID"] : 0);
			$op      = isset($_POST["op"]) ? $_POST["op"] : "";

			if ($blockID < 1) {
				$respond(array("ok" => 0, "error" => "Блок олдсонгүй."));
			}

			if ($op == "hide" || $op == "show") {
				$db->rawQuery(
					"UPDATE `" . $regTbl["block"] . "` SET `blockStatus`=? WHERE `blockID`=? AND `parentID`=0",
					array($op == "show" ? 1 : 0, $blockID)
				);
				$respond(array("ok" => 1));
			}

			/* ---- Дэд мөр нэмэх (хөтөлбөрийн цагийн хуваарь г.м) ---- */
			if ($op == "subadd") {
				$parent = $db->rawQueryOne(
					"SELECT * FROM `" . $regTbl["block"] . "` WHERE `blockID`=? AND `parentID`=0",
					array($blockID)
				);

				if (!is_array($parent) || count($parent) < 1
					|| !RegistrationCore::hasSub($parent["blockType"])) {
					$respond(array("ok" => 0, "error" => "Энэ блокт мөр нэмэх боломжгүй."));
				}

				$order = (int)RegistrationCore::scalar($db,
					"SELECT MAX(`blockOrder`) FROM `" . $regTbl["block"] . "` WHERE `parentID`=?",
					array($blockID)
				) + 1;

				$db->insert($regTbl["block"], array(
					"parentID"    => $blockID,
					"blockType"   => $parent["blockType"],
					"blockData"   => json_encode(
						RegistrationCore::blockDefaults($parent["blockType"], true),
						JSON_UNESCAPED_UNICODE
					),
					"blockStatus" => 1,
					"blockOrder"  => $order
				));

				$respond(array("ok" => 1));
			}

			/* ---- Дэд мөр устгах ---- */
			if ($op == "subdel") {
				$row = $db->rawQueryOne(
					"SELECT `blockID` FROM `" . $regTbl["block"] . "` WHERE `blockID`=? AND `parentID`>0",
					array($blockID)
				);

				if (!is_array($row) || count($row) < 1) {
					$respond(array("ok" => 0, "error" => "Мөр олдсонгүй."));
				}

				$db->rawQuery(
					"DELETE FROM `" . $regTbl["block"] . "` WHERE `blockID`=? AND `parentID`>0",
					array($blockID)
				);

				$respond(array("ok" => 1));
			}

			/* ---- Дэд мөрийг дээш/доош ---- */
			if ($op == "subup" || $op == "subdown") {
				$parentID = (int)RegistrationCore::scalar($db,
					"SELECT `parentID` FROM `" . $regTbl["block"] . "` WHERE `blockID`=?",
					array($blockID)
				);

				if ($parentID < 1) {
					$respond(array("ok" => 0, "error" => "Мөр олдсонгүй."));
				}

				$rows = $db->rawQuery(
					"SELECT `blockID` FROM `" . $regTbl["block"] . "` WHERE `parentID`=?"
						. " ORDER BY `blockOrder` ASC, `blockID` ASC",
					array($parentID)
				);

				$ids = array();
				if (is_array($rows)) {
					foreach ($rows as $r) {
						$ids[] = (int)$r["blockID"];
					}
				}

				$pos  = array_search($blockID, $ids, true);
				$swap = $op == "subup" ? $pos - 1 : $pos + 1;

				if ($pos !== false && $swap >= 0 && $swap < count($ids)) {
					$tmp = $ids[$pos];
					$ids[$pos] = $ids[$swap];
					$ids[$swap] = $tmp;

					$order = 1;
					foreach ($ids as $id) {
						$db->rawQuery(
							"UPDATE `" . $regTbl["block"] . "` SET `blockOrder`=? WHERE `blockID`=?",
							array($order, $id)
						);
						$order++;
					}
				}

				$respond(array("ok" => 1));
			}

			if ($op == "up" || $op == "down") {
				$parentID = (int)RegistrationCore::scalar($db,
					"SELECT `parentID` FROM `" . $regTbl["block"] . "` WHERE `blockID`=?",
					array($blockID)
				);

				$rows = $db->rawQuery(
					"SELECT `blockID` FROM `" . $regTbl["block"] . "` WHERE `parentID`=?"
						. " ORDER BY `blockOrder` ASC, `blockID` ASC",
					array($parentID)
				);

				$ids = array();
				if (is_array($rows)) {
					foreach ($rows as $r) {
						$ids[] = (int)$r["blockID"];
					}
				}

				$pos = array_search($blockID, $ids, true);
				$swap = $op == "up" ? $pos - 1 : $pos + 1;

				if ($pos !== false && $swap >= 0 && $swap < count($ids)) {
					$tmp = $ids[$pos];
					$ids[$pos] = $ids[$swap];
					$ids[$swap] = $tmp;

					$order = 1;
					foreach ($ids as $id) {
						$db->rawQuery(
							"UPDATE `" . $regTbl["block"] . "` SET `blockOrder`=? WHERE `blockID`=?",
							array($order, $id)
						);
						$order++;
					}
				}

				$respond(array("ok" => 1));
			}

			$respond(array("ok" => 0, "error" => "Үйлдэл танигдсангүй."));
			break;
	}

	$respond(array("ok" => 0, "error" => "Үйлдэл танигдсангүй."));
}

/* ------------------------------------------------------------------
   Бүртгэлийн форм илгээх
   ------------------------------------------------------------------ */

$regErrors = array();
$regValues = array();
$regDone   = false;

/* PRG — амжилттай бүртгэлийн дараа refresh хийхэд давхар бичихгүй */
if (!empty($_SESSION["regDone"])) {
	$regDone = true;
	unset($_SESSION["regDone"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["frmRegPost"])) {

	/* 1. Robot хамгаалалт — хүн харахгүй талбар дүүрсэн бол хаяна */
	$honey = isset($_POST["regWebsite"]) ? trim($_POST["regWebsite"]) : "";

	/* 2. Хэт хурдан илгээсэн бол (bot) */
	$openedAt = isset($_POST["regTs"]) ? (int)$_POST["regTs"] : 0;
	$tooFast  = ($openedAt > 0 && (time() - $openedAt) < 2);

	if ($honey != "" || $tooFast) {
		$regErrors["_form"] = $regSet["errorText"];
	} elseif (!$regStatus["open"]) {
		$regErrors["_form"] = $regStatus["text"];
	} else {

		/* 3. Нэг IP-ээс цагт 10-аас олон удаа илгээхийг хориглоно */
		$recent = (int)RegistrationCore::scalar($db,
			"SELECT COUNT(*) FROM `" . $regTbl["entry"] . "` WHERE `entryIP`=? AND `entryDate`>DATE_SUB(NOW(), INTERVAL 1 HOUR)",
			array(RegistrationCore::clientIp())
		);

		if ($recent >= 10) {
			$regErrors["_form"] = $regSet["errorText"];
		} else {
			$check = RegistrationCore::validate($db, $regFields, $_POST, $regSet);
			$regValues = $check["values"];

			if ($check["ok"]) {
				RegistrationCore::saveEntry($db, $check["core"], $check["extra"]);

				$_SESSION["regDone"] = 1;
				header("Location: /registration?ok=1#registration-form");
				exit;
			}

			$regErrors = $check["errors"];
		}
	}
}

/* Бүртгэл хаагдсан/дүүрсэн эсэхийг илгээлтийн дараа дахин шалгана */
$regStatus = RegistrationCore::status($db, $regSet);

/* "Хөтөлбөр" хэсэг байхгүй бол мэдээллийн хэсгийн доор нэг удаа нэмнэ */
if (RegistrationCore::ensureAgenda($db, $regSet)) {
	$regSet = RegistrationCore::settings($db);
}

/* Засварлах горимд унтраасан блокуудыг ч харуулж, буцааж асаах боломжтой */
$regBlocks = RegistrationCore::blocks($db, !$regEdit);

/* R2 / CDN тохиргоог идэвхжүүлнэ (зургийн хаяг зөв гарахын тулд) */
RegistrationCore::mediaBoot($db, $regSet);

/* Хуудасны тогтмол дэвсгэр — зураг/видео нэг л удаа, бүх хуудсанд */
$regPageBg = RegistrationCore::pageBg($db, $regSet);
$regSet    = RegistrationCore::settings($db);

/* Формын блок байхгүй бол ч бүртгүүлэх боломжтой байх ёстой */
$regHasForm = false;
foreach ($regBlocks as $regBlockObj) {
	if ($regBlockObj["blockType"] == "form" && (int)$regBlockObj["blockStatus"] == 1) {
		$regHasForm = true;
		break;
	}
}

/* Стандарт layout-ын оронд бие даасан хуудас */
$gloIncHomePage = "registration.php";
$allWidgetArr   = array();
$addPageTitle   = $regSet["metaTitle"];
