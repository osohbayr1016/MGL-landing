<?php
/*
	Cloudflare R2 storage (S3 compatible, AWS Signature V4).

	Config lives in const.php:
		$gloR2Account, $gloR2Bucket, $gloR2Key, $gloR2Secret

	While any of those is empty every call here is a no-op, so uploads keep
	landing on local disk exactly the way they always have.
*/

function r2Enabled(){
	global $gloR2Account,$gloR2Bucket,$gloR2Key,$gloR2Secret;

	return isset($gloR2Account) && $gloR2Account!="" && isset($gloR2Bucket) && $gloR2Bucket!=""
		&& isset($gloR2Key) && $gloR2Key!="" && isset($gloR2Secret) && $gloR2Secret!="";
}

function r2KeyPath($key){
	$parts = explode("/",trim($key,"/"));

	foreach($parts as $i=>$part)
		$parts[$i] = rawurlencode($part);

	return "/".join("/",$parts);
}

function r2MimeFnc($file){
	$mimeArr = array(
		"jpg"=>"image/jpeg",	"jpeg"=>"image/jpeg",	"png"=>"image/png",
		"gif"=>"image/gif",		"webp"=>"image/webp",	"svg"=>"image/svg+xml",
		"avif"=>"image/avif",	"mp4"=>"video/mp4",		"webm"=>"video/webm",
		"pdf"=>"application/pdf"
	);

	$ext = strtolower(pathinfo($file,PATHINFO_EXTENSION));

	return isset($mimeArr[$ext]) ? $mimeArr[$ext] : "application/octet-stream";
}

function r2Request($method,$key,$payloadHash,$contentType="",$fileHandle=null,$fileSize=0){
	global $gloR2Account,$gloR2Bucket,$gloR2Key,$gloR2Secret;

	$host = $gloR2Account.".r2.cloudflarestorage.com";
	$uri  = "/".rawurlencode($gloR2Bucket).r2KeyPath($key);

	$amzDate   = gmdate("Ymd\THis\Z");
	$dateStamp = gmdate("Ymd");

	$headerArr = array(
		"host"					=> $host,
		"x-amz-content-sha256"	=> $payloadHash,
		"x-amz-date"			=> $amzDate
	);

	if($contentType!="")
		$headerArr["content-type"] = $contentType;

	ksort($headerArr);

	$canonHeaders = "";
	foreach($headerArr as $name=>$value)
		$canonHeaders .= $name.":".$value."\n";

	$signedHeaders = join(";",array_keys($headerArr));

	$canonRequest = $method."\n".$uri."\n\n".$canonHeaders."\n".$signedHeaders."\n".$payloadHash;

	$scope  = $dateStamp."/auto/s3/aws4_request";
	$toSign = "AWS4-HMAC-SHA256\n".$amzDate."\n".$scope."\n".hash("sha256",$canonRequest);

	$kDate     = hash_hmac("sha256",$dateStamp,"AWS4".$gloR2Secret,true);
	$kRegion   = hash_hmac("sha256","auto",$kDate,true);
	$kService  = hash_hmac("sha256","s3",$kRegion,true);
	$kSigning  = hash_hmac("sha256","aws4_request",$kService,true);
	$signature = hash_hmac("sha256",$toSign,$kSigning);

	$sendHeaders = array(
		"Authorization: AWS4-HMAC-SHA256 Credential=".$gloR2Key."/".$scope.", SignedHeaders=".$signedHeaders.", Signature=".$signature,
		"Expect:"
	);

	foreach($headerArr as $name=>$value){
		if($name=="host")
			continue;
		$sendHeaders[] = $name.": ".$value;
	}

	$ch = curl_init("https://".$host.$uri);
	curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$sendHeaders);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,15);
	curl_setopt($ch,CURLOPT_TIMEOUT,180);

	if($fileHandle!==null){
		curl_setopt($ch,CURLOPT_UPLOAD,true);
		curl_setopt($ch,CURLOPT_INFILE,$fileHandle);
		curl_setopt($ch,CURLOPT_INFILESIZE,$fileSize);
	}

	$resBody = curl_exec($ch);
	$resCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	$resErr  = curl_error($ch);
	curl_close($ch);

	$isOk = ($resCode>=200 && $resCode<300);

	if(!$isOk)
		error_log("R2 ".$method." ".$key." failed: HTTP ".$resCode." ".$resErr." ".substr((string)$resBody,0,300));

	return $isOk;
}

/* "postpic/ceo/x.jpg" -> "ceo/x.jpg", the matching key inside the bucket. */
function r2KeyFromPath($localPath){
	if(substr($localPath,0,8)=="postpic/")
		return substr($localPath,8);

	return ltrim($localPath,"/");
}

function r2Put($key,$localFile){
	if(!r2Enabled() || !is_file($localFile))
		return false;

	$fileHandle = fopen($localFile,"rb");

	if(!$fileHandle)
		return false;

	$isOk = r2Request("PUT",$key,hash_file("sha256",$localFile),r2MimeFnc($localFile),$fileHandle,filesize($localFile));

	fclose($fileHandle);

	return $isOk;
}

function r2Delete($key){
	if(!r2Enabled())
		return false;

	return r2Request("DELETE",$key,hash("sha256",""));
}

/*
	Move a freshly uploaded file up to R2 and drop the local copy once the
	upload is confirmed. A failure leaves the local file untouched, so the
	image still shows up through the origin fallback.
*/
function r2Store($key,$localFile){
	if(!r2Put($key,$localFile))
		return false;

	@unlink($localFile);

	return true;
}

/* Drop an image from both R2 and local disk. */
function r2Remove($key,$localFile=""){
	if($localFile!="" && is_file($localFile))
		@unlink($localFile);

	r2Delete($key);
}
?>
