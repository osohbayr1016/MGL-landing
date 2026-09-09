<?php
/*
	Харилцагчийн лого байршуулах.

	Файл нь postpic/image/clients/ дотор буух ба R2 тохируулсан бол
	r2Store түүнийг бакет руу зөөгөөд локал хуулбарыг устгана. R2 хоосон
	үед файл локал дискэн дээрээ үлдэж, яг өмнөх шигээ ажиллана.
*/

$clExtArr = array("jpg", "jpeg", "png", "gif", "webp", "svg", "avif");
$clRelDir = "postpic/image/clients/";
$clAbsDir = dirname(dirname(__DIR__)) . "/" . $clRelDir;

if (!isset($_FILES["frmLogo"]) || $_FILES["frmLogo"]["tmp_name"] == "") {
	orderAjaxDone(array("ok" => 0, "error" => "Файл ирсэнгүй."));
}

if ($_FILES["frmLogo"]["error"] !== UPLOAD_ERR_OK) {
	orderAjaxDone(array("ok" => 0, "error" => "Файл хүлээж авахад алдаа гарлаа."));
}

$clExt = strtolower(pathinfo($_FILES["frmLogo"]["name"], PATHINFO_EXTENSION));

if (!in_array($clExt, $clExtArr)) {
	orderAjaxDone(array("ok" => 0, "error" => "Зөвхөн зураг (jpg, png, gif, webp, svg, avif) байршуулна."));
}

if ($_FILES["frmLogo"]["size"] > 5 * 1024 * 1024) {
	orderAjaxDone(array("ok" => 0, "error" => "Зургийн хэмжээ 5MB-аас хэтэрсэн байна."));
}

if (!is_dir($clAbsDir)) {
	@mkdir($clAbsDir, 0775, true);
}

if (!is_dir($clAbsDir)) {
	orderAjaxDone(array("ok" => 0, "error" => "Хадгалах хавтас үүсгэж чадсангүй."));
}

/* Файлын нэрийг латин болгож цэвэрлэнэ — R2 болон Worker-ийн түлхүүр цэвэр байх ёстой. */
$clBase = pathinfo($_FILES["frmLogo"]["name"], PATHINFO_FILENAME);
$clBase = preg_replace("/[^A-Za-z0-9]+/", "-", $clBase);
$clBase = trim($clBase, "-");

if ($clBase == "") {
	$clBase = "logo";
}

$clName = strtolower(substr($clBase, 0, 40)) . "-" . substr(md5(uniqid("", true)), 0, 8) . "." . $clExt;
$clTo   = $clAbsDir . $clName;

if (!move_uploaded_file($_FILES["frmLogo"]["tmp_name"], $clTo)) {
	orderAjaxDone(array("ok" => 0, "error" => "Файлыг хадгалж чадсангүй."));
}

@chmod($clTo, 0644);

r2Store(r2KeyFromPath($clRelDir . $clName), $clTo);

$clPic = "/" . $clRelDir . $clName;

orderAjaxDone(array(
	"pic" => $clPic,
	"url" => newsPicFnc(0, $clPic)
));
