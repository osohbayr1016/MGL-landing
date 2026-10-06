<?php
/**
 * Офис схем — хадгалалт. /userPost/floorplan руу ирсэн POST.
 *
 * Хамгаалалт (бүгд сервер талд):
 *   1. user.sys.posts.php нэвтрээгүй хүнийг энд оруулдаггүй (login руу явуулна)
 *   2. эрхийн бүлэгт "floorplan_edit" байх ёстой
 *   3. CSRF токен таарах ёстой
 *   4. бүх утгыг FloorPlanCore::sanitizePayload шалгаж, зөвшөөрөгдөх хязгаарт шахна
 *   5. revision таарахгүй бол (өөр хүн хадгалсан) хадгалахаас татгалзана
 */

include __DIR__ . "/lib.php";

if (!function_exists("floorPlanJsonOut")) {
	function floorPlanJsonOut($status, $body)
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

$fpFrm = isset($_POST["frmPost"]) ? $_POST["frmPost"] : "";

if ($fpFrm !== "floorPlanSave") {
	floorPlanJsonOut(400, array("ok" => 0, "error" => "Буруу хүсэлт."));
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
	floorPlanJsonOut(405, array("ok" => 0, "error" => "Зөвхөн POST."));
}

if (!floorPlanCan()) {
	floorPlanJsonOut(403, array("ok" => 0, "error" => "Энэ үйлдлийг хийх эрх танд олгогдоогүй байна."));
}

if (!floorPlanCsrfOk(isset($_POST["csrf"]) ? $_POST["csrf"] : "")) {
	floorPlanJsonOut(403, array("ok" => 0, "code" => "csrf", "error" => "Хуудасны хугацаа дууссан байна. Хуудсаа дахин ачаалж оролдоно уу."));
}

/* зөвхөн хэмжээ хязгаартай JSON */
$fpRaw = isset($_POST["payload"]) && is_string($_POST["payload"]) ? $_POST["payload"] : "";
if ($fpRaw === "" || strlen($fpRaw) > 400000) {
	floorPlanJsonOut(400, array("ok" => 0, "error" => "Өгөгдөл хоосон эсвэл хэт том байна."));
}

$fpPayload = json_decode($fpRaw, true);
if (!is_array($fpPayload)) {
	floorPlanJsonOut(400, array("ok" => 0, "error" => "Өгөгдлийн формат буруу байна."));
}

$fpClean = FloorPlanCore::sanitizePayload($fpPayload);
if (!$fpClean["ok"]) {
	floorPlanJsonOut(422, array("ok" => 0, "code" => "invalid", "error" => "Өгөгдөл буруу байна.", "errors" => $fpClean["errors"]));
}

/* давхар бүрийн revision: {"office-20f": 3, "office-21f": 1} */
$fpRevisions = isset($_POST["revisions"]) && is_string($_POST["revisions"]) ? json_decode($_POST["revisions"], true) : null;
if (!is_array($fpRevisions)) {
	$fpRevisions = array();
}

try {
	$fpResult = FloorPlanCore::save($db, $fpClean, $fpRevisions);
} catch (Exception $fpErr) {
	error_log("CP Admin floorplan save failed: " . $fpErr->getMessage());
	floorPlanJsonOut(500, array("ok" => 0, "error" => "Өгөгдлийн санд хадгалж чадсангүй. Дахин оролдоно уу."));
}

if (!$fpResult["ok"]) {
	if ($fpResult["code"] === "conflict") {
		floorPlanJsonOut(409, array("ok" => 0, "code" => "conflict", "error" => "Өөр хэн нэгэн энэ схемийг хадгалсан байна. Хуудсаа дахин ачаалж, өөрчлөлтөө давтан оруулна уу."));
	}

	floorPlanJsonOut(500, array("ok" => 0, "error" => "Схем олдсонгүй."));
}

floorPlanJsonOut(200, array("ok" => 1, "revisions" => $fpResult["revisions"]));
