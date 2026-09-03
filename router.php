<?php

$uri = urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));

if ($uri !== "/" && $uri !== "/home" && $uri !== "/registration" && is_file(__DIR__ . $uri)) {
	return false;
}

if ($uri === "/" || $uri === "/home") {
	require __DIR__ . "/preview-home.php";
	return true;
}

if ($uri === "/registration") {
	require __DIR__ . "/preview-registration.php";
	return true;
}

if ($uri === "/preview-home.html") {
	header("Location: /", true, 302);
	exit;
}

http_response_code(404);
header("Content-Type: text/html; charset=utf-8");
echo "<!DOCTYPE html><html><body style=\"font:16px system-ui;padding:40px\">";
echo "<h1>404</h1><p>Not found: " . htmlspecialchars($uri, ENT_QUOTES, "UTF-8") . "</p>";
echo "<p><a href=\"/\">Back to preview</a></p></body></html>";
return true;
