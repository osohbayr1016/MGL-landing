<?php	
include "config.php";

$sysReturnLink = "/";
$isOrderAjax = !empty($_POST["ajaxOrder"]);

include "user.info.php";
	
if($gloUserOnline){
		
	$sysModule = txtSec($_REQUEST["mod"]);

	/* A fatal error inside a save handler used to surface as a blank
	   "HTTP ERROR 500" page. Catch it here instead: log the real cause to
	   error_log and tell the admin what went wrong, with a way back. */
	try{
		if(is_file($gloConstModuleDir.$sysModule."/post.sys.php"))
			include $gloConstModuleDir.$sysModule."/post.sys.php";
	}
	catch(Throwable $sysErr){
		adminPostFailed($sysErr, $isOrderAjax);
	}

	if ($isOrderAjax) {
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(array("ok" => 0, "error" => "order_handler_missing"));
		exit;
	}

}
else{
	
	$sysModule = "users";
	$_POST["frmPost"] = "login";
	
	if(is_file($gloConstModuleDir.$sysModule."/post.sys.php"))
		include $gloConstModuleDir.$sysModule."/post.sys.php";

	if ($isOrderAjax) {
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(array("ok" => 0, "error" => "auth_required"));
		exit;
	}

}

header("location: $sysReturnLink");

function adminPostFailed($err, $isAjax){

	$msg = get_class($err).": ".$err->getMessage()." in ".$err->getFile().":".$err->getLine();

	error_log("CP Admin save failed [".txtSec($_REQUEST["mod"])."/".(isset($_POST["frmPost"]) ? $_POST["frmPost"] : "")."] ".$msg);

	if($isAjax){
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(array("ok" => 0, "error" => $err->getMessage()));
		exit;
	}

	header("HTTP/1.1 500 Internal Server Error");
	header("Content-Type: text/html; charset=utf-8");
	?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="utf-8">
<title>Хадгалах үед алдаа гарлаа</title>
<style>
	body{font:14px/1.6 Arial,sans-serif;color:#333;background:#f3f3f4;margin:0;padding:40px 16px}
	.box{max-width:640px;margin:0 auto;background:#fff;border:1px solid #e7eaec;border-radius:4px;padding:24px 28px}
	h1{font-size:18px;margin:0 0 12px;color:#ed5565}
	pre{white-space:pre-wrap;word-break:break-all;background:#f8f8f9;border:1px solid #e7eaec;padding:12px;font-size:12px}
	a{color:#1ab394}
</style>
</head>
<body>
<div class="box">
	<h1>Хадгалах үед алдаа гарлаа</h1>
	<p>Мэдээлэл бүрэн хадгалагдаагүй байж болзошгүй. Доорх алдааг хөгжүүлэгчид дамжуулна уу.</p>
	<pre><?php echo htmlspecialchars($msg, ENT_QUOTES, "UTF-8"); ?></pre>
	<p><a href="javascript:history.back()">&larr; Буцах</a> &nbsp;|&nbsp; <a href="/">Нүүр хуудас</a></p>
</div>
</body>
</html>
	<?php
	exit;
}
?>
