<?php
/*
	ERP нэвтрэлтийн цонхны локал preview.

		php -S localhost:8000
		http://localhost:8000/preview-login.php

	Сайтын жинхэнэ CSS/JS болон skin/new/erp-login.php-г ашиглана.
	const.php доторх $gloErpLoginUrl тохируулагдсан бол жинхэнэ Worker руу
	хүсэлт явна; хоосон бол "тохируулагдаагүй" гэсэн алдаа харагдана.
*/

session_start();
?>
<!DOCTYPE html>
<html lang="mn">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>MGL E&amp;C — Login preview</title>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
	<link href="/assets/css/erp-login.css" rel="stylesheet">
	<style>
		* { box-sizing: border-box; }
		body { margin: 0; font-family: Montserrat, system-ui, sans-serif; color: #1d1d1d; }

		.pv-bar {
			padding: 12px 16px; background: #1d1d1d; color: #fff;
			font-size: 11px; font-weight: 600; letter-spacing: .08em;
		}

		/* Нүүр хуудасны хар зурагтай толгойг дуурайлгасан хэсэг */
		.pv-hero {
			position: relative; min-height: 70vh; padding: 26px 40px;
			background: linear-gradient(140deg, #0e1417, #2a3a42);
		}
		.pv-hero .mainNavHeader { position: relative; z-index: 2; }
		.pv-hero #logo { height: 26px; }
		.pv-hero .header-logo-div { float: left; }
		.pv-hero .pv-logo { color: #fff; font-weight: 700; letter-spacing: .1em; font-size: 15px; }
		.pv-hero .menu-icons-rhs { display: flex; align-items: center; justify-content: flex-end; padding: 0; }
		.pv-hero .menu-icons-rhs li { list-style: none; }
		.pv-hero .pv-caption {
			position: absolute; left: 40px; bottom: 40px; max-width: 520px;
			color: rgba(255,255,255,.75); font-size: 13px; line-height: 1.7;
		}

		/* Цагаан дэвсгэртэй хуудасны хувилбар */
		.pv-white { padding: 26px 40px 70px; background: #fff; }
		.pv-white .menu-icons-rhs { display: flex; align-items: center; justify-content: flex-end; padding: 0; }
		.pv-white .menu-icons-rhs li { list-style: none; }
		.pv-white p { color: #6d6d6d; font-size: 13px; }
	</style>
</head>
<body class="home">
	<div class="pv-bar">PREVIEW — толгойн товч ба нэвтрэх цонх. Жинхэнэ CSS/JS/partial ашиглаж байна.</div>

	<!-- 1. Ил тод (нүүр/төслийн) толгой -->
	<section class="pv-hero">
		<div class="mainNavHeader">
			<div class="header-logo-div"><span class="pv-logo">MGL E&amp;C</span></div>
			<ul class="menu-icons-rhs">
				<li class="erp-login-li">
					<a class="erp-login-btn" href="#erpLogin" data-erp-open>Нэвтрэх</a>
				</li>
			</ul>
		</div>
		<p class="pv-caption">Ил тод толгойтой хуудсууд (нүүр, төсөл) дээрх харагдац — цагаан хүрээтэй, hover үед цагаан дүүрэн.</p>
	</section>

	<!-- 2. Цагаан толгой -->
	<section class="pv-white whitehdr">
		<div class="mainNavHeader">
			<ul class="menu-icons-rhs">
				<li class="erp-login-li">
					<a class="erp-login-btn" href="#erpLogin" data-erp-open>Нэвтрэх</a>
				</li>
			</ul>
		</div>
		<p>Цагаан толгойтой хуудсууд (мэдээ, тухай) дээрх харагдац — бараан хүрээтэй.</p>
	</section>

	<?php include "skin/new/erp-login.php"; ?>
	<script src="/assets/js/erp-login.js"></script>
</body>
</html>
