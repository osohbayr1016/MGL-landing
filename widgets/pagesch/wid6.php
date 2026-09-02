<div class="home-projects-block">
	<div class="wrapper">
		<div class="section-header">
			<h3><?php echo $selSchBody["title"];?></h3>
		</div>
	</div>
	<div class="home-projects-marquee-wrap">
	<?php
	$projects = is_array($workWidArr[$objs["schID"]]) ? $workWidArr[$objs["schID"]] : array();
	$rowDirs = array("rtl", "ltr", "rtl");

	for ($row = 0; $row < 3; $row++) {
		$rowItems = array_slice($projects, $row * 4, 4);
		if (count($rowItems) === 0) {
			continue;
		}
		$loopItems = array_merge($rowItems, $rowItems);
		$dir = $rowDirs[$row];
	?>
		<div class="home-projects-marquee home-projects-marquee--<?php echo $dir; ?>">
			<div class="home-projects-marquee__track">
			<?php foreach ($loopItems as $obj) {
				include __DIR__ . "/wid6.item.php";
			} ?>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
