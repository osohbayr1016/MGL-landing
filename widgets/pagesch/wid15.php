
<div class="wrapper">
    
    <div class="cusWidget">
		<h1 class="widTitle"><?php echo $selSchBody["title"];?></h1>
        <br />
        <h3 class="widTitle"><?php echo $selSchBody["sub"];?></h3>
		<div id="accordion" class="accordion accordion-vacancies-wrap">
        <?php
		$i = 0;
		if(count($objs["sub"])>0)
		foreach($objs["sub"] as $key=>$obj){
            $subSchBody = json_decode($obj["schNote"],true);
		?>
			<div class="accordion-item">
				<div class="accordion-item-copy">
					<div class="accordion-item-title">
						<span class="accordion-title-name" style="flex-basis:90%"><?php echo $subSchBody["title"];?></span>
						<span class="accordion-title-icon"></span>
					</div>
					<div class="accordion-item-content">
						
						<?php echo $subSchBody["note"];?>
						
					</div>
				</div>
			</div>
        <?php } ?>
			
		</div>
	</div>
    
</div>