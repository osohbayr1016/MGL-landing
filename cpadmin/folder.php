<?php
ini_set("display_errors","1");

define('folder1',		'postpic/banner/');
define('folder2',		'postpic/blogpic/');
define('folder3',		'postpic/cache/');
define('folder4',		'postpic/ceo/');
define('folder5',		'postpic/files/');
define('folder6',		'postpic/image/');
define('folder7',		'postpic/news/');
define('folder8',		'postpic/pic/');
define('folder9',		'postpic/poll/');
define('folder10',		'postpic/type/');


if (!file_exists(folder1))
	mkdir(folder1, 0777);

if (!file_exists(folder2))
	mkdir(folder2, 0777);

if (!file_exists(folder3))
	mkdir(folder3, 0777);
	
	
if (!file_exists(folder4))
	mkdir(folder4, 0777);

if (!file_exists(folder5))
	mkdir(folder5, 0777);

if (!file_exists(folder6))
	mkdir(folder6, 0777);
	

if (!file_exists(folder7))
	mkdir(folder7, 0777);

if (!file_exists(folder8))
	mkdir(folder8, 0777);

if (!file_exists(folder9))
	mkdir(folder9, 0777);
	
if (!file_exists(folder10))
	mkdir(folder10, 0777);
?>