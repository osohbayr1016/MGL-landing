<div class="hero-slider-outer">
	<div class="hero-slider-wrap owl-carousel">
		<?php
		$i = 0;
		if(count($objs["sub"])>0)
		foreach($objs["sub"] as $key=>$obj){

            $subSchBody = json_decode($obj["schNote"],true);

            if($subSchBody["vid"]!="" || $subSchBody["pic"]!=""){

				
			$imgSlide = false;
			
			if($subSchBody["vid"]=="")
				$imgSlide = true;
		?>
			
      
                        
		<div class="hero-slide-item" data-hash="hslide-<?php echo $key?>">
        <?php
        if($imgSlide){
		?>
        	<div class="hero-slide-image hero-banner-image hero-slider-image ">
				<div class="img-overlay "></div>
				<picture>
					<source type="image/jpeg" srcset="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>">
					<img src="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>" alt="<?php echo $subSchBody["title"];?>">
				</picture>
			</div>
         <?php 
		}
		else{
		 ?>
        	<div class="hero-slide-image hero-banner-image hero-slider-video  video-available ">
            	<div class="img-overlay hidden"></div>
				<video class="lozad" autoplay muted loop playsinline>
                    <source src="<?php echo newsPicFnc(0,$subSchBody["vid"]);?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
			</div>
         <?php } ?>   
			
			<a href="<?php echo $subSchBody["link"];?>">
				<div class="hero-slide-title wrapper">
                    <?php echo $subSchBody["title"];?>
				</div>
			</a>
		</div>
		<?php $i++; } } ?>
		
	</div>
	<div class="hero-slider-trigger-wrap">
		<?php
		$i = 0;
		if(count($objs["sub"])>0)
		foreach($objs["sub"] as $key=>$obj){

            $subSchBody = json_decode($obj["schNote"],true);
            if($subSchBody["vid"]!="" || $subSchBody["pic"]!=""){

			$liClass = "";
			if($i<1)
				$liClass = "active";
		?>
		<div class="trigger-item">
			<div class="slide-progress"></div>
			<button class="trigger-link" data-hash="hslide-<?php echo $key?>"></button>
		</div>
		<?php $i++; } } ?>
	</div>
</div>