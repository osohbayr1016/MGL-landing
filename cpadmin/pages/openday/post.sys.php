<?php
/**
 * Өдөрлөг — хадгалалт. /userPost/openday руу ирсэн POST.
 *
 * Хамгаалалт (бүгд сервер талд):
 *   1. user.sys.posts.php нэвтрээгүй хүнийг энд оруулдаггүй (login руу явуулна)
 *   2. эрхийн бүлэгт "openday_edit" (хадгалах) / "openday_questions" (асуулт) байх ёстой
 *   3. CSRF токен таарах ёстой
 *   4. бүх утгыг OpenDayCore::sanitizePayload шалгаж, хязгаарт шахна
 *   5. revision таарахгүй бол (өөр хүн дундуур нь хадгалсан) татгалзана
 */

include __DIR__ . "/lib.php";

if (!function_exists("openDayJsonOut")) {
	function openDayJsonOut($status, $body)
	{
		if ($status != 200) {
			header("HTTP/1.1 " . $status);
		}
		header("Content-Type: application/json; charset=utf-8");
		header("Cache-Control: no-store");
		echo json_encode($body);
		exit;
	}
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
	openDayJsonOut(405, array("ok" => 0, "error" => "Зөвхөн POST."));
}

$odFrm = isset($_POST["frmPost"]) ? $_POST["frmPost"] : "";

/* ---- Ирсэн асуулт: хариулсан / шинэ болгох / устгах ---- */
if ($odFrm === "openDayQuestion") {
	if (!openDayCan("questions")) {
		openDayJsonOut(403, array("ok" => 0, "error" => "Энэ үйлдлийг хийх эрх танд олгогдоогүй байна."));
	}
	if (!openDayCsrfOk(isset($_POST["csrf"]) ? $_POST["csrf"] : "")) {
		openDayJsonOut(403, array("ok" => 0, "code" => "csrf", "error" => "Хуудасны хугацаа дууссан байна. Хуудсаа дахин ачаална уу."));
	}
	$odOp = isset($_POST["op"]) ? (string)$_POST["op"] : "";
	$odId = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
	if ($odId < 1 || !OpenDayCore::questionOp($db, $odId, $odOp)) {
		openDayJsonOut(400, array("ok" => 0, "error" => "Буруу хүсэлт."));
	}
	openDayJsonOut(200, array("ok" => 1));
}

if ($odFrm !== "openDaySave") {
	openDayJsonOut(400, array("ok" => 0, "error" => "Буруу хүсэлт."));
}

if (!openDayCan("edit")) {
	openDayJsonOut(403, array("ok" => 0, "error" => "Энэ үйлдлийг хийх эрх танд олгогдоогүй байна."));
}

if (!openDayCsrfOk(isset($_POST["csrf"]) ? $_POST["csrf"] : "")) {
	openDayJsonOut(403, array("ok" => 0, "code" => "csrf", "error" => "Хуудасны хугацаа дууссан байна. Хуудсаа дахин ачаалж оролдоно уу."));
}

/* зөвхөн хэмжээ хязгаартай JSON */
$odRaw = isset($_POST["payload"]) && is_string($_POST["payload"]) ? $_POST["payload"] : "";
if ($odRaw === "" || strlen($odRaw) > 600000) {
	openDayJsonOut(400, array("ok" => 0, "error" => "Өгөгдөл хоосон эсвэл хэт том байна."));
}

$odPayload = json_decode($odRaw, true);
if (!is_array($odPayload)) {
	openDayJsonOut(400, array("ok" => 0, "error" => "Өгөгдлийн формат буруу байна."));
}

/* цэг зөвхөн байгаа давхар дээр */
$odFloorKeys = array();
$odPlans = FloorPlanCore::load($db, false);
foreach ($odPlans["floors"] as $odF) {
	$odFloorKeys[] = $odF["plan"]["key"];
}

$odClean = OpenDayCore::sanitizePayload($odPayload, $odFloorKeys);
if (!$odClean["ok"]) {
	openDayJsonOut(422, array("ok" => 0, "code" => "invalid",
		"error" => "Өгөгдөл буруу байна (" . implode(", ", array_unique($odClean["errors"])) . ").",
		"errors" => $odClean["errors"]));
}

$odExpected = isset($_POST["revision"]) && is_numeric($_POST["revision"]) ? (int)$_POST["revision"] : -1;

try {
	$odResult = OpenDayCore::save($db, $odClean, $odExpected);
} catch (Exception $odErr) {
	error_log("CP Admin openday save failed: " . $odErr->getMessage());
	openDayJsonOut(500, array("ok" => 0, "error" => "Өгөгдлийн санд хадгалж чадсангүй. Дахин оролдоно уу."));
}

if (!$odResult["ok"]) {
	openDayJsonOut(409, array("ok" => 0, "code" => "conflict", "error" => "Өөр хэн нэгэн энэ хуудсыг хадгалсан байна. Хуудсаа дахин ачаалж, өөрчлөлтөө давтан оруулна уу."));
}

openDayJsonOut(200, array("ok" => 1, "revision" => $odResult["revision"]));
