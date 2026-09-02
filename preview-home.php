<?php

$previewProjects = array(
	array("Олон зориулалттай өндөр барилга", "MITIC%20profile.jpg"),
	array("Encanto Trade Center", "v7%201720x1080px.jpg"),
	array("Encanto Centro I", "Main%20render%201720x1080px.jpg"),
	array("Wisdom Tower оффисын барилга", "wisdom-nuur.jpg"),
	array("44 давхар олон зориулалттай барилга", "44FF_nuur.png"),
	array("Vision Complex Building", "Vision_new_profile.jpg"),
	array("Stella Vista", "2021-11-04%20%20MIDTOWN-night%20render-2%201720x1080px.jpg"),
	array("Gerlug Vista", "10%201720x1080px%20(3).jpg"),
	array("Амины орон сууцны хотхон", "RENDER%201%201720x1080px.jpg"),
	array("Теннисний ордон", "TC-nuur-shine.jpg"),
	array("IC Mall", "IC%20Mall%20nuur.jpg"),
	array("TOYOTA 8 ДАВХАР ҮЙЛЧИЛГЭЭНИЙ БАРИЛГА", "toyota-nuur.jpg"),
);

$previewImgBase = "https://cp.mglenc.com/postpic/image/content/";
$previewRowDirs = array("rtl", "ltr", "rtl");

function previewProjectCard($title, $file, $imgBase, $index)
{
	$src = $imgBase . $file;
	?>
	<div class="image-teaser-content">
		<a class="image-teaser-inner" href="#p<?php echo (int)$index; ?>">
			<picture>
				<img src="<?php echo htmlspecialchars($src, ENT_QUOTES, "UTF-8"); ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, "UTF-8"); ?>">
			</picture>
			<div class="image-teaser-copy">
				<h4><?php echo htmlspecialchars($title, ENT_QUOTES, "UTF-8"); ?></h4>
				<span class="image-teaser-subtext"></span>
			</div>
		</a>
	</div>
	<?php
}

?>
<!DOCTYPE html>
<html lang="mn">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
	<title>MGL E&amp;C — Home Projects Preview</title>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
	<link href="/assets/css/home-projects.css" rel="stylesheet">
	<link href="/assets/css/home-projects-marquee.css" rel="stylesheet">
	<style>
		* { box-sizing: border-box; }
		body { margin: 0; background: #fff; color: #0e1417; font-family: Montserrat, sans-serif; }
		.preview-banner {
			padding: 12px 16px; background: #003d59; color: #fff;
			font: 600 12px/1.4 Montserrat, system-ui, sans-serif; letter-spacing: .06em;
		}
		.preview-banner a { color: #fff; }
	</style>
</head>
<body class="home">
	<div class="preview-banner">
		LOCAL PREVIEW — Infinite marquee (MySQL шаардлагагүй). Бүтэн PHP сайт: MySQL асаасны дараа index.php ажиллана.
	</div>
	<div class="home-projects-block">
		<div class="wrapper">
			<div class="section-header"><h3>Төслүүд</h3></div>
		</div>
		<div class="home-projects-marquee-wrap">
		<?php for ($row = 0; $row < 3; $row++) {
			$rowItems = array_slice($previewProjects, $row * 4, 4);
			if (count($rowItems) === 0) {
				continue;
			}
			$loopItems = array_merge($rowItems, $rowItems);
			$dir = $previewRowDirs[$row];
		?>
			<div class="home-projects-marquee home-projects-marquee--<?php echo $dir; ?>">
				<div class="home-projects-marquee__track">
				<?php foreach ($loopItems as $idx => $item) {
					previewProjectCard($item[0], $item[1], $previewImgBase, $row * 4 + $idx);
				} ?>
				</div>
			</div>
		<?php } ?>
		</div>
	</div>
</body>
</html>
