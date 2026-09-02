<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rawurldecode($uri);
$root = __DIR__;

function localMime($path)
{
	$map = array(
		"css" => "text/css; charset=utf-8",
		"js" => "application/javascript; charset=utf-8",
		"json" => "application/json; charset=utf-8",
		"svg" => "image/svg+xml",
		"jpg" => "image/jpeg",
		"jpeg" => "image/jpeg",
		"png" => "image/png",
		"gif" => "image/gif",
		"webp" => "image/webp",
		"woff" => "font/woff",
		"woff2" => "font/woff2",
		"ico" => "image/x-icon",
		"mp4" => "video/mp4",
	);
	$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
	return isset($map[$ext]) ? $map[$ext] : "application/octet-stream";
}

function localServeFile($path)
{
	if (!is_file($path)) {
		http_response_code(404);
		echo "Not found";
		return true;
	}
	header("Content-Type: " . localMime($path));
readfile($path);
	return true;
}

function localRoute($pattern, $uri, &$matches)
{
	return (bool)preg_match($pattern, $uri, $matches);
}

if ($uri !== "/" && is_file($root . $uri)) {
	return false;
}

if (localRoute('#^/newsimg/(.+)$#', $uri, $m)) {
	return localServeFile($root . "/postpic/image/" . $m[1]);
}

if (localRoute('#^/newstimg/(.+)$#', $uri, $m)) {
	return localServeFile($root . "/postpic/_thumbs/Images/" . $m[1]);
}

if (localRoute('#^/pics/([A-Za-z]+)/(.+)$#', $uri, $m)) {
	return localServeFile($root . "/postpic/" . $m[1] . "/" . $m[2]);
}

if (localRoute('#^/userPost/([^/]+)/?$#', $uri, $m)) {
	$_GET["mod"] = $m[1];
	require $root . "/user.sys.posts.php";
	return true;
}

if (localRoute('#^/insert/([^/]+)/([0-9]+)/?$#', $uri, $m)) {
	$_GET["incPageType"] = "insert";
	$_GET["subPage"] = $m[1];
	$_GET["objID"] = $m[2];
	require $root . "/index.php";
	return true;
}

if (localRoute('#^/insert/([^/]+)/?$#', $uri, $m)) {
	$_GET["incPageType"] = "insert";
	$_GET["subPage"] = $m[1];
	require $root . "/index.php";
	return true;
}

$modules = array("settings", "access", "info", "users", "home", "registration");
foreach ($modules as $mod) {
	if (localRoute('#^/' . $mod . '/([^/]+)/([0-9]+)/?$#', $uri, $m)) {
		$_GET["incPageType"] = $mod;
		$_GET["subPage"] = $m[1];
		$_GET["objID"] = $m[2];
		require $root . "/index.php";
		return true;
	}
	if (localRoute('#^/' . $mod . '/([^/]+)/?$#', $uri, $m)) {
		$_GET["incPageType"] = $mod;
		$_GET["subPage"] = $m[1];
		require $root . "/index.php";
		return true;
	}
}

if ($uri === "/" || $uri === "") {
	require $root . "/index.php";
	return true;
}

if (localRoute('#^/([^/]+)/?$#', $uri, $m)) {
	$_GET["incPageType"] = $m[1];
	require $root . "/index.php";
	return true;
}

http_response_code(404);
echo "Not found: " . htmlspecialchars($uri, ENT_QUOTES, "UTF-8");
return true;
