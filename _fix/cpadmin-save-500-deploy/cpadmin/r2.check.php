<?php
/*
	One-off connectivity check for the R2 setup. Run it from the cPanel
	terminal or as a cron job:

		php /home/mglencmo/public_html/cpadmin/r2.check.php

	It writes a small test object, reads it back and deletes it again, so a
	clean run proves outbound HTTPS, the credentials and the bucket all work.
	Delete this file once the setup is confirmed.
*/

if(php_sapi_name()!="cli"){
	header("HTTP/1.1 403 Forbidden");
	die("CLI only");
}

chdir(__DIR__);

include_once "const.php";
include_once "r2.php";

function say($ok,$label,$note=""){
	echo ($ok ? "  OK   " : "  FAIL ").$label.($note!="" ? "  -- ".$note : "")."\n";
	return $ok;
}

echo "\nR2 check\n--------\n";

if(!say(function_exists("curl_init"),"curl available"))
	exit(1);

if(!say(r2Enabled(),"credentials set in const.php","fill in \$gloR2Account / \$gloR2Bucket / \$gloR2Key / \$gloR2Secret"))
	exit(1);

/* outbound HTTPS at all - the thing shared hosting most often blocks */
$ch = curl_init("https://api.cloudflare.com/client/v4/ips");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_TIMEOUT,20);
curl_exec($ch);
$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if(!say($httpCode>0,"outbound HTTPS allowed",$curlErr!="" ? $curlErr : "HTTP ".$httpCode))
	exit(1);

/* full write / read / delete round trip */
$testKey  = "_healthcheck/r2-check.txt";
$testFile = tempnam(sys_get_temp_dir(),"r2");
file_put_contents($testFile,"r2 check ".gmdate("c")."\n");

$putOk = r2Put($testKey,$testFile);
@unlink($testFile);

if(!say($putOk,"upload to bucket","see error_log for the HTTP status"))
	exit(1);

global $gloCdnBase;
if($gloCdnBase!=""){
	$ch = curl_init(rtrim($gloCdnBase,"/")."/pics/_healthcheck/r2-check.txt");
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,20);
	curl_exec($ch);
	$readCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	curl_close($ch);

	say($readCode==200,"worker serves it back","HTTP ".$readCode.($readCode==200 ? "" : " -- check the worker route and R2 binding"));
}
else{
	echo "  SKIP worker read-back  -- \$gloCdnBase still empty\n";
}

say(r2Delete($testKey),"delete from bucket");

echo "\nDone. Remove this file when you are finished.\n\n";
?>
