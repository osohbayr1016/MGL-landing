
<div class="wrapper">
	<div class="pageHeader" id="widhas<?php echo $objs["schID"]?>">
		<div class="header-bt wrapper">
			<h1><?php echo $selSchBody["title"];?></h1>
			 
		</div>
		<picture>
		<source type="image/jpeg" srcset="<?php echo newsPicFnc(0,$selSchBody["pic"]);?>">
		<img src="<?php echo newsPicFnc(0,$selSchBody["pic"]);?>">
		</picture>
	</div>
    <?php echo $selSchBody["note"];?>
    
    <div class="media-text-wrap media-featured  aos-init aos-animate" data-aos="fade-left" data-aos-duration="1500">
    <?php
    $i = 0;
    if(count($objs["sub"])>0)
    foreach($objs["sub"] as $key=>$obj){

        $subSchBody = json_decode($obj["schNote"],true);

		$mediaCls = "media-left";
		if($i%2!=0)
		$mediaCls = "media-right";
    ?>
        <div class="media-text-inner  <?php echo $mediaCls;?> ">
            <div class="media-text-media">
                <picture>
                    <source type="image/jpeg" srcset="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>">
                    <img src="<?php echo newsPicFnc(0,$subSchBody["pic"]);?>">
                </picture>
            </div>
            <div class="media-text-copy wrapper">
                <div class="media-text-title">
                    <?php echo $subSchBody["title"];?>
                </div>
                <div class="media-text-subtext">
                    <?php echo $subSchBody["note"];?>
                </div>
                <?php 
					if($subSchBody["btn"]!=""){
					?>
                    <br /><br />
                    <a class="bbtn" href="<?php echo $subSchBody["pdf"];?>"><?php echo $subSchBody["btn"];?></a>
                    <?php } ?>
            </div>
        </div>
    <?php $i++; } ?>
        
    </div>
    
</div>